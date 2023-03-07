<?php // neturi būti jokio tarpo prieš <?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

$naud_el_pastas = $_SESSION["naud_email"]; //išsiųsti reikia ne priėmimo skyriaus darbuotojui, o pareiškėjui
$par = $_SESSION["pareiskejas"];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";

switch (@$_POST["Button"]) {
    case "Keisti":
        $dok_id = $_SESSION["dok_id"]; //Tik pastabu issaugojimas
        if (isset($_POST["laukti"]) && $_POST["laukti"] == "taip") {
            $vykd = $_POST["vykd"];
            $pastabos = $_POST["pastabos"];
            $pastabos = strip_tags($pastabos); //????
            $st = $_SESSION["st"];
            include "dbstuff.inc";
            ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
                die("Klaida! Nepavyko prisijungti prie duomenų bazės");

            $query = "UPDATE dokai SET pastabos='$pastabos', vykdytojas='$vykd' where dok_id=$dok_id";
            ($result = mysqli_query($cxn, $query)) or
                die("Error: " . mysqli_error($cxn));
            mysqli_close($cxn);
            include "details_form.inc"; //extract();  ???????
            exit();
        } // if isset

        $valid = true;
        $atm_pr = $_POST["atmetimo_priezastis"];
        $message = ""; //????

        $dok_formos_kodas = $_POST["dok_formos_kodas"];
        if ($dok_formos_kodas == "Nenurodyta") {
            $valid = false;
            $message .= "Nenurodytas formos kodas!<br />";
        }
        $mokestis = $_POST["mokestis"];
        if (!is_numeric("$mokestis")) {
            
            $valid = false;
            $message .= "Neteisingai įvestas mokestis!<br /> ";
        }
        $status_dabar = $_POST["status_dabar"];
        $st = $_SESSION["st"];
        if (
            ($status_dabar == "IS OK" and $st == "is_ok") ||
            ($status_dabar == "PZ OK" and $st == "pz_ok") ||
            ($status_dabar == "IS atmestas" and $st == "is_atm") ||
            ($status_dabar == "PZ atmestas" and $st == "pz_atm") ||
            ($status_dabar == "Gautas" and $st == "gautas") ||
            ($status_dabar == "AP OK" and $st == "ap_ok") ||
            ($status_dabar == "AP atmestas" and $st == "ap_atm") ||
            ($status_dabar == "IS" and $st == "is") ||
            ($status_dabar == "PZ" and $st == "pz") ||
            ($status_dabar == "AP" and $st == "ap")
        ) {
            //if
            $valid = false;
            $message .= "Nenurodytas tolesnis prašymo statusas!<br />";
        }

        $vykd = $_POST["vykd"];

        $pastabos = $_POST["pastabos"];
        $pastabos = strip_tags($pastabos); //????
        $dok_id = $_SESSION["dok_id"];

        if (!$valid) {
            $_SESSION["st"] = $st;
            include "details_form.inc";
            exit();
        } else {
            include "dbstuff.inc";
            ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
                die("Klaida! Nepavyko prisijungti prie duomenų bazės");

            $query = "UPDATE dokai SET pastabos='$pastabos', status_dabar='$status_dabar', status_dabar_date=NOW(),  dok_formos_kodas='$dok_formos_kodas', mokestis=$mokestis, vykdytojas='$vykd' where dok_id=$dok_id"; // naud_id tb pakeistas, dok_tip_id tb pakeistas

            ($result = mysqli_query($cxn, $query)) or
                die("Error: " . mysqli_error($cxn));

            $query = "INSERT INTO dok_statusai (dok_id,statusID, status_date, naud_email) VALUES ($dok_id,'$status_dabar',NOW(), '$naud_el_pastas')"; //naud_id turi būti pakeistas,
            ($result = mysqli_query($cxn, $query)) or
                die("Error: " . mysqli_error($cxn));

            // Nuskaitome pareiškėjo, o ne priėmimo skyriaus darbuotojo el. pašto adresą iš db pagal dok. nr.:
            $query = "SELECT  naud_email FROM dokai WHERE dok_id=$dok_id";
            ($result = mysqli_query($cxn, $query)) or
                die("Error: " . mysqli_error($cxn));
            $row_cnt = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);

            $naud_email = $row["naud_email"]; //  or die "Klaida nustatant pareiškėjo el. pašto adresą\n";

            $query_naudotojai = "SELECT  CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas  FROM naudotojai WHERE naud_email = '$naud_email'";
            ($res = mysqli_query($cxn, $query_naudotojai)) or
                die("Error: " . mysqli_error($cxn));
            $rw = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas
            $naud = $rw["pareiskejas"];

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

            //$v_pav = explode(" ", $naud);
            //$naud_s = sauksm($v_pav[0])." ".sauksm($v_pav[1]);

            $v_pav = explode(" ", $naud);
            $pare = "";
            foreach ($v_pav as $value) {
                $pare .= " " . sauksm($value);
            }
            $naud_s = trim($pare);

            $email_err = false;
            //kokius kontaktus reikia laiške įrašyti $naud_el_pastas-vykdytojas, $naud_email - prasytojas?
            if ($status_dabar == "OK") {
                require_once "./PHPMailer/class.phpmailer.php";
                $emess = "Gerb. $naud_s,\n\n";
                $emess .= "Jūsų prašymas Nr. $dok_id, kurį pateikėte  Valstybiniam patentų biurui per elektroninių paslaugų sistemą EPAS, įvykdytas. \n";
                $emess .= "Jei turite klausimų, kreipkitės adresu $naud_el_pastas.\r\n\r\n"; //?????$naud_el_pastas?
                $emess .=
                    "Pagarbiai\r\n\r\nValstybinio patentų biuro elektroninių paslaugų sistema EPAS\r\n\r\n";
                $emess .=
                    "Šis laiškas sukurtas automatiškai todėl į jį neatsakykite.";

                $subj = "Valstybinis patentų biuras";

                $mail = new PHPMailer(); // defaults to using php "mail()"
                try {
                    $mail->ContentType = "text/plain";
                    $mail->IsHTML(false);
                    $mail->CharSet = "utf-8";
                    $mail->SetFrom("www@epaslaugos.vpb.lt", "VPB");
                    $mail->AddAddress($naud_email, $naud); // gal prideti varda ir pavarde?
                    $mail->AddBCC("vin@vpb.gov.lt");
                    $mail->Subject = $subj;
                    //$mail->AltBody = $emess; //"To view the message, please use an HTML compatible email viewer!";
                    //$mail->MsgHTML($emess);
                    $mail->Body = $emess;

                    if (
                        isset($_FILES["dokai"]) &&
                        $_FILES["dokai"]["error"] == UPLOAD_ERR_OK
                    ) {
                        $mail->AddAttachment(
                            $_FILES["dokai"]["tmp_name"],
                            $_FILES["dokai"]["name"]
                        );
                    }

 
                    if (!$mail->Send()) {
                        $email_err = true;
                    } else {
                        $mail->ClearAddresses();
                        $mail->ClearAttachments();
                    }
                } catch (phpmailerException $e) {
                    echo $e->errorMessage();
                    $email_err = true;
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $email_err = true;
                }
            } //if ($status_dabar=='OK')

            if ($status_dabar == "Atmestas") {
                $vpbkomentaras = trim($_POST["atmetimo_priezastis"]);
                if (strlen($vpbkomentaras) > 3) {
                    if (substr($vpbkomentaras, -1) == ".") {
                    } else {
                        $vpbkomentaras = $vpbkomentaras . ".";
                    }
                }

                $emess = "Gerb. $naud_s,\n\n";
                $emess .= "Jūsų prašymas Nr. $dok_id, kurį pateikėte Valstybiniam patentų biurui per elektroninių paslaugų sistemą EPAS, atmestas. ";
                $emess .= $vpbkomentaras;
                $emess .= "\r\nNorėdami gauti daugiau informacijos kreipkitės adresu $naud_el_pastas.\r\n\r\n"; //?????
                $emess .=
                    "Pagarbiai\r\n\r\nValstybinio patentų biuro elektroninių paslaugų sistema EPAS\r\n\r\n";
                $emess .=
                    "Šis laiškas sukurtas automatiškai todėl į jį neatsakykite.";
                $subj = "Valstybinis patentų biuras";
                $extra_header_str = "MIME-Version: 1.0" . "\r\n";
                $extra_header_str .= "From: www@epaslaugos.vpb.lt" . "\r\n";
                $extra_header_str .=
                    "Content-type: text/plain; " . "  charset=UTF-8" . "\r\n";
                $extra_header_str .=
                    "BCC: vincas.batulevicius@vpb.gov.lt" . "\r\n";
                $mailsend = mail(
                    "$naud_email",
                    "$subj",
                    "$emess",
                    $extra_header_str
                );
                if ($mailsend) {
                } else {
                    $email_err = true;
                }
            } //if ($status_dabar=='Atmestas')

            switch (@$_SESSION["grupe"]) {
                case "admins":
                case "pr":
                    $location = "gauti.php";
                    break;
                case "pz":
                    $location = "pz.php";
                    break;
                case "is":
                    $location = "is.php";
                    break;
                case "ap":
                    $location = "ap.php";
                    break;
                default:
                    header("Location: nera_teisiu.php");
                    exit();
                    break;
            } //switch

            if ($email_err) {
                header("Location: $location?mailerr=er");
            } else {
                header("Location: $location");
            }
        } //else //case
        break;
    default:
        $st = $_GET["st"];
        $dok_id = $_GET["dok_id"];

        $_SESSION["dok_id"] = $dok_id;
        $_SESSION["st"] = $st;
        include "details_form.inc";
} //switch

?>

