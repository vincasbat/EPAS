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
    include "regprasyma_form.inc";
    exit();
}

$pasirinktas = $_POST["pareiskejai"];
if ($pasirinktas == "0") {
    $mess .= "Reikia nurodyti pareiškėją! <br />";
    include "regprasyma_form.inc";
    exit();
}

$ip = $_POST["ip"];
$ip = filter_var($ip, FILTER_SANITIZE_STRING);
$ip = trim($ip);

if (!preg_match("/^[0-9a-zA-Z,; ]{4,254}$/", $ip)) {
    $mess .= "Reikia nurodyti pramoninės nuosavybės objekto numerį! <br />";
    include "regprasyma_form.inc";
    exit();
}

if ($_FILES["dokai"]["tmp_name"] == "none") {
    $mess .= "Įkelti failo nepavyko. <br />";
    include "regprasyma_form.inc";
    exit();
}

$allowedExtensions = ["pdf", "doc", "docx"];

if (isset($_FILES["dokai"])) {
    if (strlen(trim($_FILES["dokai"]["name"])) == 0) {
        //trim
        $mess .= "Reikia nurodyti prašymo rinkmeną!<br />";
        include "regprasyma_form.inc";
        exit();
    }

    $arr = explode(".", strtolower($_FILES["dokai"]["name"]));
    $ext = end($arr);
    if (!in_array($ext, $allowedExtensions)) {
        $mess .=
            "Nepavyko. Galima siųsti tik .pdf, .doc arba .docx formatų rinkmenas!<br />";
        include "regprasyma_form.inc";
        exit();
    }

    if (strlen($_FILES["dokai"]["name"]) > 20) {
        $mess .= "Rinkmenos vardas turi būti ne ilgesnis kaip 20 ženklų!<br />";
        include "regprasyma_form.inc";
        exit();
    }

    //Tikrinti failo dydį:

    $sizeOK = false;
    if ($_FILES["dokai"]["size"] > 0 && $_FILES["dokai"]["size"] <= 2097152) {
        //2097152 = 2 MB
        $sizeOK = true;
    }

    if (!$sizeOK) {
        $mess .= "Prašymo rinkmena turi būti mažesnė kaip 2 MB! <br />";
        include "regprasyma_form.inc";
        exit();
    }

    if ($_FILES["dokai"]["error"] == 0) {
        if (is_uploaded_file($_FILES["dokai"]["tmp_name"])) {
            include "stuff.inc";
            $dir_metai_men = date("YM");
            if (!is_dir($dest . $dir_metai_men)) {
                //?????; jei nedaromi men katalogai, uzkomentuoti
                mkdir($dest . $dir_metai_men);
            }
            $ad_day_time = date("dHis");
            $file = str_replace(" ", "_", $_FILES["dokai"]["name"]);
            $file = strtolower(ereg_replace("[^A-Za-z0-9.]", "", $file));
            $path = $dir_metai_men . "/" . $ad_day_time . $file;
            $destination = $dest . $path;
            $temp_file = $_FILES["dokai"]["tmp_name"];
            $result = move_uploaded_file($temp_file, $destination);
            if ($result) {
                // ($result == 1)
                $pastabos = $_POST["pastabos"];

                $pastabos = filter_var($pastabos, FILTER_SANITIZE_STRING);

                $pastabos = mysqli_real_escape_string($cxn, $pastabos);

                $statusas = "";
                switch (@$_SESSION["grupe"]) {
                    case "ap":
                        $statusas = "AP";
                        break;
                    case "pz":
                        $statusas = "PZ";
                        break;
                    case "is":
                        $statusas = "IS";
                        break;
                    case "admins":
                        break;

                    default:
                        break;
                }

                $from_ip = $_SERVER["REMOTE_ADDR"];
                $query = "INSERT INTO dokai (dok_kelias,pastabos, status_dabar, status_dabar_date, naud_email, dok_formos_kodas, from_ip, ip) VALUES ('$path','$pastabos', '$statusas', NOW(), '$pasirinktas', 'Nenurodyta', '$from_ip', '$ip')";
                ($result = mysqli_query($cxn, $query)) or
                    die("Error: " . mysqli_error($cxn));

                //Identity:
                $dok_id = mysqli_insert_id($cxn);

                $failo_dydis = $_FILES["dokai"]["size"];
                if ($failo_dydis > 1048575) {
                    $failo_dydis = round($failo_dydis / 1048576, 1) . " MB";
                } else {
                    $failo_dydis = round($failo_dydis / 1024, 1) . " KB";
                }

                $mess_ok .= "Failas sėkmingai įkeltas: {$_FILES["dokai"]["name"]} ($failo_dydis). Prašymo numeris $dok_id.<br />";
            }
            // if $result
            else {
                $mess .= "Įrašyti į duomenų bazę nepavyko!<br />";
                include "regprasyma_form.inc";
                exit();
            }

            $query = "INSERT INTO dok_statusai (dok_id,statusID,status_date, naud_email) VALUES ($dok_id,'$statusas', NOW(), '$naud_el_pastas')";
            ($result = mysqli_query($cxn, $query)) or
                die("Error: " . mysqli_error($cxn));
        }
        //if (is_uploaded_file($_FILES['dokai']['tmp_name']))
        else {
            $mess .= "Įkelti prašymo rinkmenos nepavyko!<br />";
            include "regprasyma_form.inc";
            exit();
        }

        $_SESSION["upl_dok_id"] = $dok_id;
    }
    // error  (prasymo failo)
    else {
        $mess .= "Klaida įkeliant prašymo rinkmeną!<br />";
        include "regprasyma_form.inc";
        exit();
    } // error else
}
// if (isset($_FILES['dokai']))
else {
    $mess .= "Įkelti prašymo rinkmenos nepavyko!<br />";
    include "regprasyma_form.inc";
    exit();
}
include "regprasyma_form.inc";
?>

