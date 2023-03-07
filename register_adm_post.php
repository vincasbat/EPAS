<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: login.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
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

$fields = [
    "naud_vardas" => "vardas",
    "naud_pavarde" => "pavardė",
    "naud_email" => "el. paštas",
    "naud_passw" => "slaptažodis",
    "conf_passw" => "pakartoti slaptažodį",
    "naud_telef" => "telefonas",
    "naud_adr" => "adresas",
    "naud_org" => "organizacija",
];

session_start(); //????????

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
    include "register_form_adm.inc";
    exit();
} // end if blanks found

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

        //palyginti abu slaptazodzius, registration successful,
    } // end if not empty1
} // end foreach POST

//palyginame ar slaptazodis patvirtintas teisingai:
if (!($_POST["naud_passw"] == $_POST["conf_passw"])) {
    $errors[] = "Neteisingai patvirtintas slaptažodis. ";
}

foreach ($_POST as $field => $value) {
    $$field = strip_tags(trim($value));
}

if (@is_array($errors)) {
    $message_2 = "";
    foreach ($errors as $value) {
        $message_2 .= $value . " Bandykite dar kartą.<br />";
    }
    //include("register_form_adm.inc");
    $atsakymas = ["rez" => "ER", "msg" => $message_2];
    header("Content-Type: application/json");
    echo json_encode($atsakymas);
    exit();

    exit();
} // end if errors are found 1

/* check to see if user name already exists */
include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Nepavyko prisijungti prie duomenų bazės");

//

$sql = "SELECT naud_email FROM naudotojai WHERE naud_email='$naud_email'";
($result = mysqli_query($cxn, $sql)) or die("Query died: user_name.");
$num = mysqli_num_rows($result);

if ($num > 0) {
    $message_2 = "$naud_email jau panaudotas. Prašom pasirinkti kitą el. pašto adresą.";

    $atsakymas = ["rez" => "ER", "msg" => $message_2];
    header("Content-Type: application/json");
    echo json_encode($atsakymas);
    exit();
}
// end if user name already exists
else {
    //	dar irasyti grupe
    $today = date("Y-m-d");
    $sql = "INSERT INTO naudotojai (naud_vardas, naud_pavarde, naud_passw, naud_sukurimo_data, naud_email, naud_telef, naud_adr, naud_org, naud_grupe) VALUES
('$naud_vardas','$naud_pavarde',md5('$naud_passw'), '$today', '$naud_email','$naud_telef','$naud_adr','$naud_org', '$naud_grupe')";
    mysqli_query($cxn, $sql) or die("Error: " . mysqli_error($cxn));

    $emess =
        "Jūs užregistruotas Valstybinio patentų biuro el. paslaugų sistemoje EPAS. ";
    $emess .= "Jūsų el. paštas ir slaptažodis yra: ";
    $emess .= "\r\n\r\n\t$naud_email\r\n\t";
    $emess .= "$naud_passw\r\n\r\n";
    $emess .= "Jei turite klausimų,";
    $emess .= " rašykite vincas.batulevicius@vpb.gov.lt\r\n\r\n";
    $emess .=
        "Pagarbiai\r\n\r\nValstybinio patentų biuro elektroninių paslaugų sistema EPAS.\r\n\r\n";

    $subj = "Naudotojo registracija";

    $extra_header_str = "MIME-Version: 1.0" . "\r\n";
    $extra_header_str .= "From: www@epaslaugos.vpb.lt" . "\r\n";
    $extra_header_str .=
        "Content-type: text/plain; " . "  charset=UTF-8" . "\r\n";
    $extra_header_str .= "CC: ius@vpb.gov.lt" . "\r\n";

    $mailsend = mail("$naud_email", "$subj", "$emess", "$extra_header_str");
   

    $atsakymas = ["rez" => "OK", "msg" => ""];
    header("Content-Type: application/json");
    echo json_encode($atsakymas);
} // end else no errors found

?>

