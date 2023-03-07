<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
date_default_timezone_set("Europe/Vilnius");
require_once "../tcpdf/tcpdf.php";
require_once "../fpdi/fpdi.php";
session_start();

if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}
switch (@$_SESSION["grupe"]) {
    case "ap":
    case "pr":
    case "pz":
    case "is":
    case "admins":
        $naud_el_pastas = $_SESSION["naud_email"];
        $par = $_SESSION["pareiskejas"];
        $mess_pareiskejas = "<span style='color:green;'> $par </span>";
        break;

    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
} //switch

date_default_timezone_set("Europe/Vilnius");

include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

if (!isset($_POST["Upload"])) {
    include "isduoti_form2.inc";
    exit();
}

// count($_FILES) . " files uploaded";

$file_count = count($_FILES);
$pasirinktas = $_POST["pareiskejai"];
$sql = "SELECT LEFT(naud_vardas, 1) as vardas, naud_pavarde FROM naudotojai WHERE naud_email = '$pasirinktas'";
$result = mysqli_query($cxn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $vardas = $row["vardas"];
        $pavarde = $row["naud_pavarde"];
        $sauksm = $vardas . ". " . sauksm($pavarde);
    }
}

require_once "./PHPMailer/class.phpmailer.php";
$emess = "Gerb. $sauksm,\n\n";

if ($file_count > 1) {
    $emess .=
        "Pranešame, kad per Valstybinio patentų biuro (VPB) elektroninių paslaugų sistemą EPAS (https://www.epaslaugos.lt/portal/external/services/authentication/v1/?service_id=0901acbe8068fede) Jums išduota " .
        $file_count .
        " PDF dokumentai (-ų). \n";
} else {
    $emess .=
        "Pranešame, kad per Valstybinio patentų biuro (VPB) elektroninių paslaugų sistemą EPAS (https://www.epaslaugos.lt/portal/external/services/authentication/v1/?service_id=0901acbe8068fede) Jums išduotas PDF dokumentas. \r\n\r\n";
}

$emess .=
    "Išsamią informaciją apie VPB elektronines paslaugas rasite tinklalapyje www.vpb.lt. \r\n\r\n";
$emess .= "Prašome į šį el. laišką neatsakyti. \r\n\r\n";

$emess .= "Pagarbiai\r\nVALSTYBINIS PATENTŲ BIURAS\r\n\r\n";

$subj = "Valstybinis patentų biuras";

$mail = new PHPMailer(); // defaults to using php "mail()"

$mail->ContentType = "text/plain";
$mail->IsHTML(false);
$mail->CharSet = "utf-8";
$mail->SetFrom("www@epaslaugos.vpb.lt", "VPB");
$mail->AddAddress($pasirinktas, $vardas . ". " . $pavarde);
$mail->AddBCC("vincas.batulevicius@vpb.gov.lt");
$mail->Subject = $subj;

$mail->Body = $emess;


$pno_name = "";
$tipas_name = "";
$file_name = "";

for ($i = 1; $i <= $file_count; $i++) {
    $pno_name = "pno_" . $i;
    $tipas_name = "sel_" . $i;
    $file_name = "file_" . $i;

    $tipas = $_POST[$tipas_name];

    if ($pasirinktas == "0") {
        $mess .= "Reikia nurodyti pareiškėją! <br />";
        include "isduoti_form2.inc";
        exit();
    }

    $ip = $_POST[$pno_name];
    //echo $ip; exit();

    $ip = filter_var($ip, FILTER_SANITIZE_STRING);
    $ip = trim($ip);

    //if(!preg_match("/^[0-9 ]{4,9}$/", $ip))
    if (!preg_match("/^[0-9a-zA-Z,; ]{4,254}$/", $ip)) {
        $mess .= "Reikia nurodyti pramoninės nuosavybės objekto numerį! <br />";
        include "isduoti_form2.inc";
        exit();
    }

    if ($tipas == "nera") {
        $mess .= "Reikia nurodyti dokumento tipą! <br />";
        include "isduoti_form2.inc";
        exit();
    }

    if ($_FILES[$file_name]["tmp_name"] == "none") {
        
        $mess .= "Įkelti failo nepavyko. <br />";
        include "isduoti_form2.inc";
        exit();
    }

    $allowedExtensions = ["pdf"]; //, 'doc', 'docx'

    if (isset($_FILES[$file_name])) {
        if (strlen(trim($_FILES[$file_name]["name"])) == 0) {
            //trim
            $mess .= "Reikia nurodyti prašymo rinkmeną!<br />";
            include "isduoti_form2.inc";
            exit();
        }

        $arr = explode(".", strtolower($_FILES[$file_name]["name"]));
        $ext = end($arr);
        if (!in_array($ext, $allowedExtensions)) {
            $mess .= "Nepavyko. Galima siųsti tik .pdf rinkmenas!<br />";
            include "isduoti_form2.inc";
            exit();
        }

        //Tikrinti failo dydį:

        $sizeOK = false;
        if (
            $_FILES[$file_name]["size"] > 0 &&
            $_FILES[$file_name]["size"] <= 2097152
        ) {
            //2097152 = 2 MB
            $sizeOK = true;
        }

        if (!$sizeOK) {
            $mess .= "Rinkmena turi būti mažesnė kaip 2 MB! <br />";
            include "isduoti_form2.inc";
            exit();
        }

        if ($_FILES[$file_name]["error"] == 0) {
            if (is_uploaded_file($_FILES[$file_name]["tmp_name"])) {
                //	include("stuff.inc");
                //	$data = date("Ymd");
                $data = date("YmdHis");

                $prasytojo_email_mod = str_replace("@", "", $pasirinktas); //   ?????
                $skyrius = "";

                switch (@$_SESSION["grupe"]) {
                    case "pr":
                        $skyrius = "PR";
                        break;
                    case "is":
                        $skyrius = "IS";
                        break;
                    case "pz":
                        $skyrius = "PZ";
                        break;
                    case "ap":
                        $skyrius = "AP";
                        break;
                    case "admins":
                        $skyrius = "ADM";
                        break;

                    default:
                        $skyrius = "";
                        header("Location: nera_teisiu.php");
                        exit();
                        break;
                } //switch

                $ip_trimmed = str_replace(" ", "", $ip);

                $pasirinktas_be_at = str_replace("@", "_", $pasirinktas);

                $file =
                    $pasirinktas_be_at .
                    "_" .
                    $ip_trimmed .
                    "_" .
                    $data .
                    "_" .
                    $tipas .
                    "_" .
                    $skyrius .
                    ".pdf";

                $destination = "pazymos/" . $file;
                $temp_file = $_FILES[$file_name]["tmp_name"];
                $result = move_uploaded_file($temp_file, $destination);
                if ($result) {
                    // ($result == 1)
                    $failo_dydis = $_FILES[$file_name]["size"];
                    if ($failo_dydis > 1048575) {
                        $failo_dydis = round($failo_dydis / 1048576, 1) . " MB";
                    } else {
                        $failo_dydis = round($failo_dydis / 1024, 1) . " KB";
                    }

                    $sql = "SELECT MAX(reg_ai) AS maks FROM `siunc_registras`";
                    $result = mysqli_query($cxn, $sql);
                    $max = 0;
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $max = $row["maks"];
                        }
                    } else {
                        echo "<br /><br />Nerasta maks duomenų bazėje<br /><br />" .
                            '\n';
                        exit();
                    }
                    $max++;
                    if ($max < 100000) {
                        $maxz = "" . $max;
                    }
                    if ($max < 10000) {
                        $maxz = "0" . $max;
                    }
                    if ($max < 1000) {
                        $maxz = "00" . $max;
                    }
                    if ($max < 100) {
                        $maxz = "000" . $max;
                    }
                    if ($max < 10) {
                        $maxz = "0000" . $max;
                    }
                    $reg_nr = "EPAS-" . $skyrius . "-" . $maxz;
                    $sql = "INSERT INTO `siunc_registras`(`reg_nr`, `data`, `adresatas`, `dokumentas`, `kelias`, `dok_id`, `pno`, `isdave`) VALUES ('$reg_nr','$data','$pasirinktas','$tipas','$file',null,'$ip_trimmed', '$naud_el_pastas')";
                    //echo $sql;
                    mysqli_query($cxn, $sql) or
                        die("Error: " . mysqli_error($cxn));

                    //Čia į PDFą įrašyti siunčiamo dokumento numerį:  +++++++++++++++++++++++++++++++++++++++
                    //$version =  pdfVersion(dirname(__FILE__) . '/' . $destination);

                    //echo $version, '<br>';
                    //echo gettype($version);
                    //exit();

                    if ($skyrius != "PR") {
                        $data = date("Y-m-d");
                        $pdf = new FPDI();
                        $pdf->SetPrintHeader(false);
                        $pageCount = $pdf->setSourceFile(
                            dirname(__FILE__) . "/" . $destination
                        );
                        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                            $templateId = $pdf->importPage($pageNo);
                            $size = $pdf->getTemplateSize($templateId);
                            // create a page (landscape or portrait depending on the imported page size)
                            if ($size["w"] > $size["h"]) {
                                $pdf->AddPage("L", [$size["w"], $size["h"]]);
                            } else {
                                $pdf->AddPage("P", [$size["w"], $size["h"]]);
                            }
                            $pdf->useTemplate($templateId);
                            if ($pageNo == 1) {
                                $pdf->SetFont("Dejavusans", "", 6);
                                $pdf->SetXY(30, 5);
                                $pdf->Write(
                                    0,
                                    $data . " " . $reg_nr,
                                    "",
                                    0,
                                    "L",
                                    false,
                                    0,
                                    false,
                                    false,
                                    0
                                );
                            }
                        }
                        $pdf->Output( dirname(__FILE__) . "/" . $destination, "F"  );
                    } //if skyrius
                }
                // if $result
                else {
                    $mess .= "Nepavyko!<br />";
                    include "isduoti_form2.inc";
                    exit();
                }
            }
            //if (is_uploaded_file($_FILES[$file_name]['tmp_name']))
            else {
                $mess .= "Įkelti rinkmenos nepavyko!<br />";
                include "isduoti_form2.inc";
                exit();
            }
        }
        // error  (prasymo failo)
        else {
            $mess .= "Klaida įkeliant rinkmeną!<br />";
            include "isduoti_form2.inc";
            exit();
        } // error else
    }
    // if (isset($_FILES[$file_name]))
    else {
        $mess .= "Įkelti rinkmenos nepavyko!<br />";
        include "isduoti_form2.inc";
        exit();
    }
} // for

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

function pdfVersion($filename)
{
   
    $fp = @fopen($filename, "rb");

    if (!$fp) {
        return 0;
    }

    /* Reset file pointer to the start */
    fseek($fp, 0);

    /* Read 20 bytes from the start of the PDF */
    preg_match("/\d\.\d/", fread($fp, 20), $match);

    fclose($fp);

    if (isset($match[0])) {
        return $match[0];
    } else {
        return 0;
    }
}



if (!$mail->Send()) {
    $mess .= "Išsiųsti dokumentų nepavyko!<br />";
    include "isduoti_form2.inc";
    exit();
} else {
    $mail->ClearAddresses();
    $mail->ClearAttachments();
    if ($file_count > 1) {
        $messok =
            "<p id='mess' style='color:green;background-color:lightgreen;'><b>" .
            $file_count .
            " dokumentai išduoti pareiškėjui</b> </p>\n";
    } else {
        $messok =
            "<p id='mess' style='color:green;background-color:lightgreen;'><b>Dokumentas išduotas pareiškėjui</b> </p>\n";
    }
    include "isduoti_form2.inc";
}
?>

