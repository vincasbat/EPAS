<?php

session_start();

$signature = $_POST["SIGNATURE"];

$data =
    $_POST["SRC"] .
    $_POST["TIME"] .
    $_POST["PERSON_CODE"] .
    $_POST["PERSON_FNAME"] .
    $_POST["PERSON_LNAME"] .
    $_POST["COMPANY_CODE"] .
    $_POST["COMPANY_NAME"];

$fp = fopen("./crt/epaslaugos_ident.crt", "r");
$cert = fread($fp, 8192);
fclose($fp);
$pubkeyid = openssl_get_publickey($cert);
$ok1 = openssl_verify($data, base64_decode($signature), $pubkeyid);

$ok2 = 0;
$ok = 0;
if (
    isset($_SESSION["token"], $_POST["token"]) &&
    $_SESSION["token"] == $_POST["token"]
) {
    $ok2 = 1;
}

if ($ok1 == 1 || $ok2 == 1) {
    $ok = 1;
} else {
    $ok = 0;
}

openssl_free_key($pubkeyid);
if ($ok != 1) {
    header("Location: nera_teisiu.php");
    exit();
}

include "dbstuff.inc";

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Nepavyko prisijungti prie duomenų bazės");
$sql = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas, naud_grupe FROM naudotojai WHERE naud_ak='$_POST[PERSON_CODE]' ";
($result2 = mysqli_query($cxn, $sql)) or die("Asmens kodo užklausa nepavyko");
$num2 = mysqli_num_rows($result2);

if ($num2 > 0) {
    $row = mysqli_fetch_assoc($result2);
    $pareiskejas = $row["pareiskejas"];
    $naud_email = $row["naud_email"];
    $grupe = $row["naud_grupe"];
    $naud_ak = $row["naud_ak"];

    mysqli_close($cxn); //nebūtina

    $_SESSION["auth"] = "yes";
    $_SESSION["pareiskejas"] = $pareiskejas;
    $_SESSION["naud_email"] = $naud_email;
    $_SESSION["grupe"] = $grupe;
    $_SESSION["naud_ak"] = $naud_ak;

    switch ($grupe) {
        case "par": //gali matyti tik pareiskejai ir adminai
            header("Location: index.php");
            break;
        case "admins":
            header("Location: adm.php");
            break;
        case "is":
            header("Location: is.php");
            break;
        case "pz":
            header("Location: pz.php");
            break;
        case "pr":
            header("Location: gauti.php");
            break;
        case "ap":
            header("Location: ap.php");
            break;

        default:
            header("Location: nera_teisiu.php");
            exit();
            break;
    } //switch
}

// nera tokio asmens kodo    -
else {
   
    $fields = [
        "naud_vardas" => "vardas",
        "naud_pavarde" => "pavardė",
        "naud_ak" => "asmens kodas",
        "naud_email" => "el. paštas",
        "naud_telef" => "telefonas",
        "naud_adr" => "adresas",
        "naud_org" => "organizacija",
    ];

    session_start();

    if (@$_POST["Button"] == "Registruotis") {
        foreach ($_POST as $field => $value) {
            if ($value == "") {
                $blanks[] = $fields[$field];
            } else {
                $good_data[$field] = strip_tags(trim($value));
            }
        } // end foreach POST

        if (isset($blanks)) {
            $message_2 = "Turi būti užpildyti šie laukai: ";
            foreach ($blanks as $value) {
                $message_2 .= "$value, ";
            }
            extract($good_data);
            include "register_form.inc";
            exit();
        }

        /* validate data */
        foreach ($_POST as $field => $value) {
            if (!empty($value)) {
                if (preg_match("/email/i", $field)) {
                    if (!preg_match("/^.+@.+\\..+$/", $value)) {
                        $errors[] = "$value neteisingas el. pašto adresas. ";
                    }
                }

                if (preg_match("/telef/i", $field)) {
                    if (!preg_match("/^[0-9)( -+]{5,20}$/", $value)) {
                        $errors[] = "$value neteisingas telefono numeris. ";
                    }
                }

                if (preg_match("/naud_passw/i", $field)) {
                    if (!preg_match("/^(.+){6,10}$/", $value)) {
                        $errors[] = "Slaptažodis $value negalimas. Jį turi sudaryti nuo 6 iki 10 simbolių. ";
                    }
                }
            } // end if not empty1
        } // end foreach POST

        foreach ($_POST as $field => $value) {
            $$field = strip_tags(trim($value));
        }

        if (@is_array($errors)) {
            $message_2 = "";
            foreach ($errors as $value) {
                $message_2 .= $value . " Bandykite dar kartą.<br />";
            }
            include "register_form.inc";
            exit();
        } // end if errors are found 1

        /* check to see if user name already exists */
        include "dbstuff.inc";
        ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
            die("Nepavyko prisijungti prie duomenų bazės");

        //--------------

        $sql = "SELECT naud_email FROM naudotojai WHERE naud_email='$naud_email'";
        ($result = mysqli_query($cxn, $sql)) or die("Query died: user_name.");
        $num = mysqli_num_rows($result);

        if ($num > 0) {
            $message_2 = "$naud_email jau panaudotas. Prašom pasirinkti kitą el. pašto adresą.";
            include "register_form.inc";
            exit();
        }
        // end if user name already exists
        else {
            $today = date("Y-m-d");
            $sql = "INSERT INTO naudotojai (naud_vardas, naud_pavarde, naud_passw, naud_sukurimo_data, naud_email, naud_telef, naud_adr, naud_org, naud_ak) VALUES
('$naud_vardas','$naud_pavarde',md5('$naud_passw'), '$today', '$naud_email','$naud_telef','$naud_adr','$naud_org','$naud_ak')";
            mysqli_query($cxn, $sql);

            $pareiskejas = $_POST["naud_vardas"] . " " . $_POST["naud_pavarde"]; //trim ir t.t.

            $_SESSION["pareiskejas"] = $pareiskejas;
            $_SESSION["auth"] = "yes";
            $_SESSION["naud_email"] = $naud_email;
            $_SESSION["grupe"] = "par";

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

            $v_pav = explode(" ", $pareiskejas);
            $pareiskejas_s = sauksm($v_pav[0]) . " " . sauksm($v_pav[1]);

            /* send email to new Customer */
            $emess = "Gerb. $pareiskejas_s,\n\n";
            $emess .=
                "Jūs sėkmingai užsiregistravote Valstybinio patentų biuro elektroninių paslaugų sistemoje EPAS. ";
            $emess .=
                "Jei turite klausimų, skambinkite tel. 8 (5) 278 0286 arba";
            $emess .= " rašykite adresu vida.mikutiene@vpb.gov.lt.\r\n\r\n"; //?????
            $emess .=
                "Pagarbiai\r\n\r\nValstybinio patentų biuro elektroninių paslaugų sistema EPAS.\r\n\r\n";
            $emess .=
                "Šis laiškas sukurtas automatiškai todėl į jį neatsakykite.";
            $subj =
                "Naudotojo registracija VPB elektroninių paslaugų sistemoje EPAS";

            $extra_header_str = "MIME-Version: 1.0" . "\r\n";
            $extra_header_str .= "From: www@vvv.vpb.lt" . "\r\n";
            $extra_header_str .=
                "Content-type: text/plain; " . "  charset=UTF-8" . "\r\n";
            $extra_header_str .= "BCC: vin@vpb.gov.lt" . "\r\n";
            $mailsend = mail(
                "$naud_email",
                "$subj",
                "$emess",
                $extra_header_str
            );

            $pranesimas_naudotojui =
                "<p style='color:green'><b>Jūsų duomenys sėkmingai įrašyti. Prašom pereiti į pradinį Valstybinio patentų biuro elektroninių paslaugų <a href='index.php' > puslapį. </a></b></p>";
            include "register_form.inc";
            exit();
        }
    }

    // else nera tokio el. pašto
    else {
        $naud_ak = $_POST["PERSON_CODE"];
    
    $naud_vardas = $_POST["PERSON_FNAME"];
    $naud_pavarde = $_POST["PERSON_LNAME"];
}
    include "register_form.inc";
}


?>



 ?>
