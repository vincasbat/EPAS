<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: login.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "admins":
    case "par":
        $naud_el_pastas = $_SESSION["naud_email"];
        $par = $_SESSION["pareiskejas"];
        $mess_pareiskejas = "<span style='color:green;'> $par </span>";
        break;

    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
} //switch

$json = file_get_contents("php://input");
$data = json_decode($json);
$msg = "";
$suma = 0;
$sarasas = "\r\n";
$metai = "";
foreach ($data as $prates) {
    $suma += $prates->suma;
    $sum = number_format($prates->suma, 2, ".", "");
    $sarasas .= "$prates->patnr\t\t$prates->metai\t$sum  EUR \r\n";
}
$suma = number_format($suma, 2, ".", "");

include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$dok_id = "";
$ip = count($data) . " CSV. $suma  EUR.";

$from_ip = $_SERVER["REMOTE_ADDR"];
$query = "INSERT INTO dokai (dok_kelias,pastabos, status_dabar, status_dabar_date, naud_email, dok_formos_kodas, from_ip, ip) VALUES ('-','$json', 'Gautas', NOW(), '$naud_el_pastas', 'Nenurodyta', '$from_ip', '$ip')";

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$dok_id = mysqli_insert_id($cxn);

$query = "INSERT INTO dok_statusai (dok_id,statusID,status_date, naud_email) VALUES ($dok_id,'Gautas', NOW(), '$naud_el_pastas')";
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));

if ($result) {
    $msg = "Prašymas sėkmingai priimtas. Prašymo numeris $dok_id.";

    $v_pav = explode(" ", $par);
    $pare = "";
    foreach ($v_pav as $value) {
        $pare .= " " . sauksm($value);
    }
    $pare = trim($pare);

    $emess = "Gerb. $pare,\r\n\r\n";
    $emess .= "Jūsų prašymas dėl pramoninės nuosavybės objektų sąrašo CSV formatu priimtas Valstybinio patentų biuro (VPB) elektroninių paslaugų sistemoje EPAS. Prašymo Nr. $dok_id. \n";
    $emess .= "Mokėtina suma $suma EUR. Pateiktų pratęsimų sąrašas:\r\n";
    $emess .= "$sarasas \r\n";
    $emess .=
        "Šis laiškas sukurtas automatiškai todėl į jį neatsakykite. \r\n\r\n";
    $emess .=
        "Pagarbiai\r\n\r\nValstybinio patentų biuro elektroninių paslaugų sistema EPAS\r\n\r\n";
    $subj = "Gautas dokumentas";

    $extra_header_str = "MIME-Version: 1.0" . "\r\n";
    $extra_header_str .= "From: www@epaslaugos.vpb.lt" . "\r\n";
    $extra_header_str .=
        "Content-type: text/plain; " . "  charset=UTF-8" . "\r\n";
    $extra_header_str .= "CC: vid@vpb.gov.lt" . "\r\n";
    $extra_header_str .= "BCC: cius@vpb.gov.lt" . "\r\n";
    $mailsend = mail("$naud_el_pastas", "$subj", "$emess", $extra_header_str);

    $logfile = "./log.txt";
    $logdetails = $emess . "\r\n";
    ($fp = fopen($logfile, "a")) or die("Unable to open log.txt!");
    fwrite($fp, $logdetails);
    fclose($fp);
}
// if $result
else {
    $msg = "Nepavyko!";
}

mysqli_close($cxn);

function sauksm($str)
{
    $sauks = $str;
    if (preg_match("/as$/", $str)) {
        $sauks = preg_replace("/as$/", "ai", $str);
    }
    if (preg_match("/AS$/", $str)) {
        $sauks = preg_replace("/AS$/", "AI", $str);
    }

    if (preg_match("/is$/", $str)) {
        $sauks = preg_replace("/is$/", "i", $str);
    }
    if (preg_match("/IS$/", $str)) {
        $sauks = preg_replace("/IS$/", "I", $str);
    }

    if (preg_match("/ys$/", $str)) {
        $sauks = preg_replace("/ys$/", "y", $str);
    }
    if (preg_match("/YS$/", $str)) {
        $sauks = preg_replace("/YS$/", "Y", $str);
    }

    if (preg_match("/us$/", $str)) {
        $sauks = preg_replace("/us$/", "au", $str);
    }
    if (preg_match("/US$/", $str)) {
        $sauks = preg_replace("/US$/", "AU", $str);
    }

    if (preg_match("/ė$/", $str)) {
        $sauks = preg_replace("/ė$/", "e", $str);
    }
    if (preg_match("/Ė$/", $str)) {
        $sauks = preg_replace("/Ė$/", "E", $str);
    }

    return $sauks;
}

$atsakymas = ["rez" => "OK", "msg" => $msg];
header("Content-Type: application/json");
echo json_encode($atsakymas);

?>

