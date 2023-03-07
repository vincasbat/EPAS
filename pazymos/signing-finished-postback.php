<?php
require_once  '../config.php';
/**
 * Read reqeust data, extract params.
 */
$body = file_get_contents('php://input');
$params = json_decode($body, true);


function sauksm ($str)
{
$sauks = $str;
if (preg_match("/as$/", $str))  $sauks = preg_replace("/as$/", "ai", $str);
if (preg_match("/AS$/", $str))  $sauks = preg_replace("/AS$/", "AI", $str);

if (preg_match("/is$/", $str))  $sauks = preg_replace("/is$/", "i", $str);
if (preg_match("/IS$/", $str))  $sauks = preg_replace("/IS$/", "I", $str);

if (preg_match("/ys$/", $str))  $sauks = preg_replace("/ys$/", "y", $str);
if (preg_match("/YS$/", $str))  $sauks = preg_replace("/YS$/", "Y", $str);

if (preg_match("/us$/", $str))  $sauks = preg_replace("/us$/", "au", $str);
if (preg_match("/US$/", $str))  $sauks = preg_replace("/US$/", "AU", $str);

if (preg_match("/ė$/", $str))  $sauks = preg_replace("/ė$/", "e", $str);
if (preg_match("/Ė$/", $str))  $sauks = preg_replace("/Ė$/", "E", $str);

return $sauks;
}




/**
 * Write log helper
 * @param mixed $data
 */
function writeLog($data) {
    $path =  './postback.log';
    if (is_writable($path)) {
        file_put_contents($path, $data . '\n', FILE_APPEND);
    }
}

/**
 * Download signed file helper
 * @param string $url
 */
function downloadFile($url, $token) {
    writeLog("Downloading signed file from " . $url);

    // Using curl to download file
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_SSLVERSION, 3);
curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $error = curl_error($ch);

    // Log errors
    if ($error) {
        writeLog("Error: " . print_r($error, true));
        exit;
    }

    // Save downloaded file
    $name = 'signed_' . mt_rand() . '.pdf';


//Suzinome failo varda:
include("../dbstuff.inc");
 $cxn = mysqli_connect($host,$user,$passwd,$dbname)  or die("Nepavyko prisijungti prie duomenų bazės");
$sql = "SELECT kelias, adresatas, LEFT(naud_vardas, 1) as vardas, naud_pavarde, pno, reg_nr FROM siunc_registras, naudotojai WHERE adresatas = naudotojai.naud_email AND token = '$token'";

//writeLog("sql: ".$sql);
$result = mysqli_query($cxn, $sql);
if (mysqli_num_rows($result) > 0) {
     while($row = mysqli_fetch_assoc($result)) {
$name = $row["kelias"];
$pno = $row["pno"];
$regnr = $row["reg_nr"];

//randame skyrių, dok. tipą:  3124_1636389_20150623_ISR_IS.pdf
$masyvas = explode("_",  $name);
$doktip = $masyvas[3];
$doktipas = "dokumentą";
switch ($doktip) {
    case "ISR":
        $doktipas = "išrašą";
        break;
    case "SPR":
        $doktipas = "sprendimą";
        break;
    case "PAZ":
        $doktipas = "pažymą";
        break;
    case "LIU":
        $doktipas = "liudijimą";
        break;
    case "PRA":
        $doktipas = "pranešimą";
        break;
    case "KIT":
        $doktipas = "dokumentą";
        break;
    default:
         $doktipas = "dokumentą";
}



$skyr = end($masyvas);
$skyri =  explode(".",  $skyr);
$skyriu = $skyri[0];
$skyrius = "skyrius";

switch ($skyriu) {
    case "IS":
        $skyrius = "Išradimų skyrius";
        break;
    case "PZ":
        $skyrius = "Prekių ženklų ir dizaino skyrius";
        break;
    case "AP":
        $skyrius = "Apeliacinis skyrius";
        break;
    case "PR":
        $skyrius = "Priėmimo skyrius";
        break;
   
    default:
         $skyrius = "skyrius";
}

$adresatas = $row["adresatas"];
$vardas = $row["vardas"];
$pavarde = $row["naud_pavarde"];
$sauksm = $vardas . '. ' .  sauksm($pavarde);
 }// while
} //if>1
else {
//delete token is siunc registro
}

mysqli_close($cxn);

//writeLog("name: ".$name);
    $path =  './' . $name;
    writeLog("Saving file to " . $path);
    file_put_contents($path, $data);
    curl_close($ch);


//Siunčiame pranešimą adresatui:
require_once("../PHPMailer/class.phpmailer.php");
$emess = "Gerb. $sauksm,\n\n";
$emess .= "Pranešame, kad  Valstybinio patentų biuro (VPB) $skyrius per elektroninių paslaugų sistemą EPAS (https://www.epaslaugos.lt/portal/external/services/authentication/v1/?service_id=0901acbe8068fede)  Jums išdavė  $doktipas (registracijos Nr. $regnr, pramoninės nuosavybės objekto Nr. $pno). \r\n\r\n";

$emess .= "Išsamią informaciją apie VPB elektronines paslaugas rasite tinklalapyje www.vpb.lt. \r\n\r\n";
$emess .= "Prašome į šį el. laišką neatsakyti. \r\n\r\n";

$emess .= "Pagarbiai\r\nVALSTYBINIS PATENTŲ BIURAS\r\n\r\n";

$subj = "Valstybinis patentų biuras";

$mail = new PHPMailer(); 
 try {
$mail->ContentType = 'text/plain'; 
$mail->IsHTML(false);
$mail->CharSet = 'utf-8';
$mail->SetFrom('www@epaslaugos.vpb.lt', 'VPB');
$mail->AddAddress($adresatas, $vardas.'. '.$pavarde);   
$mail->AddBCC('vincas.batulevicius@vpb.gov.lt');
$mail->Subject    = $subj;

$mail->Body=$emess;


if(!$mail->Send()) {
$email_err = true;
} else {
$mail->ClearAddresses();
$mail->ClearAttachments();
   }

} catch (phpmailerException $e) {
echo $e->errorMessage(); 
$email_err = true;
} catch (Exception $e) {
echo  $e->getMessage(); 
$email_err = true;
}

    
    
}//end function downloadFile 







/**
 * Write log
 */
$log = '[' . date('Y-m-d H:i:s') . '] ' . '\n';
$log .= $body . '\n';
$log .= print_r($params, true);
writeLog($log);
 
if ($params['action'] == 'signer_signed') {
    
    
} elseif ($params['action'] == 'signing_completed') {
   
$url = $params['file'] . '?access_token=' . $accessToken;
$token = $params['token'];
writeLog("tokenas: ".$token);
    
    downloadFile($url, $token);
}

writeLog('End.');
