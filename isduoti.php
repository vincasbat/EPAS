<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
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

include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

if (!isset($_POST["Upload"])) {
    include "isduoti_form.inc";
    exit();
}

$tipas = $_POST["tipas"];
$annot = $_POST["annot"];
$elpformatas = $_POST["elpformatas"];
$pasirinktas = $_POST["pareiskejai"];
if ($pasirinktas == "0") {
    $mess .= "Reikia nurodyti pareiškėją! <br />";
    include "isduoti_form.inc";
    exit();
}

$ip = $_POST["ip"];
$ip = filter_var($ip, FILTER_SANITIZE_STRING);
$ip = trim($ip);

if (!preg_match("/^[0-9a-zA-Z,; ]{4,254}$/", $ip)) {
    $mess .= "Reikia nurodyti pramoninės nuosavybės objekto numerį! <br />";
    include "isduoti_form.inc";
    exit();
}

if ($tipas == "nera") {
    $mess .= "Reikia nurodyti dokumento tipą! <br />";
    include "isduoti_form.inc";
    exit();
}

if ($_FILES["dokai"]["tmp_name"] == "none") {
    $mess .= "Įkelti failo nepavyko. <br />";
    include "isduoti_form.inc";
    exit();
}

$allowedExtensions = ["pdf"]; //, 'doc', 'docx'

if (isset($_FILES["dokai"])) {
    if (strlen(trim($_FILES["dokai"]["name"])) == 0) {
        //trim
        $mess .= "Reikia nurodyti prašymo rinkmeną!<br />";
        include "isduoti_form.inc";
        exit();
    }

    $arr = explode(".", strtolower($_FILES["dokai"]["name"]));
    $ext = end($arr);
    if (!in_array($ext, $allowedExtensions)) {
        $mess .= "Nepavyko. Galima siųsti tik .pdf rinkmenas!<br />";
        include "isduoti_form.inc";
        exit();
    }

    if (strlen($_FILES["dokai"]["name"]) > 40) {
        $mess .= "Rinkmenos vardas turi būti ne ilgesnis kaip 40 ženklų!<br />";
        include "isduoti_form.inc";
        exit();
    }

    //Tikrinti failo dydį:

    $sizeOK = false;
    if ($_FILES["dokai"]["size"] > 0 && $_FILES["dokai"]["size"] <= 20971520) {
        //2097152 = 2 MB
        $sizeOK = true;
    }

    if (!$sizeOK) {
        $mess .= "Rinkmena turi būti mažesnė kaip 20 MB! <br />";
        include "isduoti_form.inc";
        exit();
    }

    if ($_FILES["dokai"]["error"] == 0) {
        if (is_uploaded_file($_FILES["dokai"]["tmp_name"])) {
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
            $file =
                $pasirinktas .
                "_" .
                $ip_trimmed .
                "_" .
                $data .
                "_" .
                $tipas .
                "_" .
                $skyrius .
                ".pdf";

            $destination = "./dtbs/" . $file;
            $temp_file = $_FILES["dokai"]["tmp_name"];
            $result = move_uploaded_file($temp_file, $destination);
            if ($result) {
                $failo_dydis = $_FILES["dokai"]["size"];
                if ($failo_dydis > 1048575) {
                    $failo_dydis = round($failo_dydis / 1048576, 1) . " MB";
                } else {
                    $failo_dydis = round($failo_dydis / 1024, 1) . " KB";
                }

                if (
                    isset($_POST["elpformatas"]) &&
                    $_POST["elpformatas"] == "pdf"
                ) {
                    header(
                        "Location: sign_gateway.php?file_name=$file&vpbinic=taip&annot=$annot&formatas=pdf"
                    );
                } else {
                    header(
                        "Location: sign_gateway.php?file_name=$file&vpbinic=taip&annot=$annot&formatas=pdflt"
                    );
                }
            }
            // if $result
            else {
                $mess .= "Nepavyko!<br />";
                include "isduoti_form.inc";
                exit();
            }
        }
        //if (is_uploaded_file($_FILES['dokai']['tmp_name']))
        else {
            $mess .= "Įkelti rinkmenos nepavyko!<br />";
            include "isduoti_form.inc";
            exit();
        }
    }
    // error  (prasymo failo)
    else {
        $mess .= "Klaida įkeliant rinkmeną!<br />";
        include "isduoti_form.inc";
        exit();
    } // error else
}
// if (isset($_FILES['dokai']))
else {
    $mess .= "Įkelti rinkmenos nepavyko!<br />";
    include "isduoti_form.inc";
    exit();
}
include "isduoti_form.inc";
?>

