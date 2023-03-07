<?php
///ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "par": //gali matyti tik pareiskejai ir adminai
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

if (!isset($_POST["Upload"])) {
    include "form_upload.inc";
    exit();
}

$types = [
    "application/pdf",
    "application/force-download",
    "application/msword",
    "application/vnd.ms-word",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "application/download",
];

$allowedExtensions = ["pdf", "doc", "docx", "zip"];

$ip = $_POST["ip"];
$ip = filter_var($ip, FILTER_SANITIZE_STRING);

$ip = trim($ip);

$ip = str_replace(",", ", ", $ip);

/*	 
if(!preg_match("/^[0-9a-zA-Z,;(). ]{4,254}$/", $ip))	  
	{
	$mess .= "Reikia nurodyti pramoninės nuosavybės objekto numerį! <br />";
	include("form_upload.inc");
	       exit();
	}
*/

include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

//***************************
$dok_id = "";
$file_count = count($_FILES);
for ($i = 1; $i <= $file_count; $i++) {
    $file_name = "file_" . $i;

    //$tipas = $_POST[$tipas_name];

    //*******************************

    if ($_FILES[$file_name]["tmp_name"] == "none") {
        
        $mess .= "Įkelti failų nepavyko. <br />";
        include "form_upload.inc";
        exit();
    }

    if (isset($_FILES[$file_name])) {
        if (strlen(trim($_FILES[$file_name]["name"])) == 0) {
            //trim
            $mess .= "Reikia nurodyti prašymo failą 1-ajame laukelyje!<br />";
            include "form_upload.inc";
            exit();
        }

        $mime = trim($_FILES[$file_name]["type"], "'\"");
      
        $arr = explode(".", strtolower($_FILES[$file_name]["name"]));
        $ext = end($arr);
        if (!in_array($ext, $allowedExtensions)) {
            $mess .=
                "Nepavyko. Galima siųsti tik .pdf, .zip, .doc arba .docx formatų failus!<br />";
            include "form_upload.inc";
            exit();
        }

        if (strlen($_FILES[$file_name]["name"]) > 40) {
            $mess .= "Failo vardas turi būti ne ilgesnis kaip 30 ženklų!<br />";
            include "form_upload.inc";
            exit();
        }

        //Tikrinti failo dydį:

        $sizeOK = false;
        //echo $_FILES[$file_name]['name']; echo $_FILES[$file_name]['size']; die;

        if (
            $_FILES[$file_name]["size"] > 0 &&
            $_FILES[$file_name]["size"] <= 15485760
        ) {
            //2097152 = 2 MB
            $sizeOK = true;
        }
        if (!$sizeOK) {
            $mess .= "$i  failo dydis neturi viršyti 10 MB! <br />";
            include "form_upload.inc";
            exit();
        }

        //===================================================================================== pradzia

        if ($_FILES[$file_name]["error"] == 0) {
            if (is_uploaded_file($_FILES[$file_name]["tmp_name"])) {
                include "stuff.inc";
                $dir_metai_men = date("YM");
                if (!is_dir($dest . $dir_metai_men)) {
                    
                    mkdir($dest . $dir_metai_men);
                }
                $ad_day_time = date("dHis");
                $file = str_replace(" ", "_", $_FILES[$file_name]["name"]);
                $file = strtolower(preg_replace("[^A-Za-z0-9.]", "", $file));
                $path = $dir_metai_men . "/" . $ad_day_time . $file;
                $destination = $dest . $path;
                $temp_file = $_FILES[$file_name]["tmp_name"];
                $result = move_uploaded_file($temp_file, $destination);
                if ($result) {
                    // ($result == 1)
                    // if file_1 else....

                    if ($i == 1) {
                        $pastabos = $_POST["pastabos"];
                        $pastabos = filter_var(
                            $pastabos,
                            FILTER_SANITIZE_STRING
                        );
                        $pastabos = mysqli_real_escape_string($cxn, $pastabos);
                        $from_ip = $_SERVER["REMOTE_ADDR"];
                        $query = "INSERT INTO dokai (dok_kelias,pastabos, status_dabar, status_dabar_date, naud_email, dok_formos_kodas, from_ip, ip) VALUES ('$path','$pastabos', 'Gautas', NOW(), '$naud_el_pastas', 'Nenurodyta', '$from_ip', '$ip')";
                        ($result = mysqli_query($cxn, $query)) or
                            die("Error: " . mysqli_error($cxn));
                        $dok_id = mysqli_insert_id($cxn);

                        $query = "INSERT INTO dok_statusai (dok_id,statusID,status_date, naud_email) VALUES ($dok_id,'Gautas', NOW(), '$naud_el_pastas')";
                        ($result = mysqli_query($cxn, $query)) or
                            die("Error: " . mysqli_error($cxn));

                        $v_pav = explode(" ", $par);
                        $pare = "";
                        foreach ($v_pav as $value) {
                            $pare .= " " . sauksm($value);
                        }
                        $pare = trim($pare);

                        $emess = "Gerb. $pare,\r\n\r\n";
                        $emess .= "Jūsų prašymas dėl pramoninės nuosavybės objekto, kurio paraiškos, registracijos ar patento Nr. $ip, priimtas Valstybinio patentų biuro (VPB) elektroninių paslaugų sistemoje EPAS. Prašymo Nr. $dok_id. \n";
                        $emess .=
                            "Prašymo vykdymo eigą galite sekti prisijungę prie VPB e.paslaugų sistemos EPAS skyrelyje „Mano prašymai“. ";
                        $emess .= "Jei turite klausimų,";
                        $emess .=
                            " rašykite adresu vida.mikutiene@vpb.gov.lt arba skambinkite telefonu (8 5) 2780286.\r\n\r\n"; //?????
                        $emess .=
                            "Pagarbiai\r\n\r\nValstybinio patentų biuro elektroninių paslaugų sistema EPAS\r\n\r\n";
                        $emess .=
                            "Šis laiškas sukurtas automatiškai todėl į jį neatsakykite.";
                        $subj = "Gautas dokumentas";

                        $extra_header_str = "MIME-Version: 1.0" . "\r\n";
                        $extra_header_str .=
                            "From: www@epaslaugos.vpb.lt" . "\r\n";
                        $extra_header_str .=
                            "Content-type: text/plain; " .
                            "  charset=UTF-8" .
                            "\r\n";
                        $extra_header_str .= "CC: vida.@vpb.gov.lt" . "\r\n";
                        $extra_header_str .= "BCC: vin@vpb.gov.lt" . "\r\n";
                        $mailsend = mail(
                            "$naud_el_pastas",
                            "$subj",
                            "$emess",
                            $extra_header_str
                        );
                    } else {
                        $query = "INSERT INTO kiti_failai (dok_kelias, dok_id) VALUES ('$path', $dok_id)";
                        ($result = mysqli_query($cxn, $query)) or
                            die("Error: " . mysqli_error($cxn));
                    }

                    $failo_dydis = $_FILES[$file_name]["size"];
                    if ($failo_dydis > 1048575) {
                        $failo_dydis = round($failo_dydis / 1048576, 1) . " MB";
                    } else {
                        $failo_dydis = round($failo_dydis / 1024, 1) . " KB";
                    }

                    $mess_ok .= "$i rinkmena sėkmingai įkelta: {$_FILES[$file_name]["name"]} ($failo_dydis). Prašymo numeris $dok_id.<br />";
                }
                // if $result
                else {
                    $mess .= "Įrašyti į duomenų bazę nepavyko!<br />";
                    include "form_upload.inc";
                    exit();
                }
            }
            //if (is_uploaded_file($_FILES[$file_name]['tmp_name']))
            else {
                $mess .= "Įkelti prašymo rinkmenos nepavyko!<br />";
                include "form_upload.inc";
                exit();
            } //if (is_uploaded_file($_FILES[$file_name]['tmp_name'])) po else
        }
        // error  (prasymo failo)
        else {
            $mess .= "Klaida įkeliant prašymo rinkmeną!<br />";
            include "form_upload.inc";
            exit();
        } // error else
    }
    // if (isset($_FILES[$file_name]))
    else {
        $mess .= "Įkelti prašymo rinkmenos nepavyko!<br />";
        include "form_upload.inc";
        exit();
    } //// if (isset($_FILES[$file_name])) else
} //for $file_count

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

$_SESSION["upl_dok_id"] = $dok_id;

include "form_upload.inc";

?>

