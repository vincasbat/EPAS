<?php

session_start();
if (@$_SESSION["auth"] == "yes") {
    $naud_el_pastas = $_SESSION["naud_email"];
    $grupe = $_SESSION["grupe"];
    $par = $_SESSION["pareiskejas"];
    $mess_pareiskejas = "<span style='color:green;'> $par </span>";
} else {
    header("Location: nera_teisiu.php");
    exit();
}

include "dbstuff.inc";

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Nepavyko prisijungti prie duomenų bazės");
$sql = "SELECT naud_email, naud_vardas, naud_pavarde, naud_grupe, naud_org, naud_adr, naud_telef, naud_ak FROM naudotojai WHERE naud_email='$naud_el_pastas' ";
($result2 = mysqli_query($cxn, $sql)) or die("El. paštas nerastas");
$num2 = mysqli_num_rows($result2);

if ($num2 > 0) {
    // yra toks el. paštas
    $row = mysqli_fetch_assoc($result2);

    $pareiskejas = $par;
    $naud_email = $row["naud_email"];
    $grupe = $row["naud_grupe"];
    $naud_ak = $row["naud_ak"];
    $naud_vardas = $row["naud_vardas"];
    $naud_pavarde = $row["naud_pavarde"];
    $naud_org = $row["naud_org"];
    $naud_telef = $row["naud_telef"];
    $naud_adr = $row["naud_adr"];

    mysqli_close($cxn); //nebūtina

    $_SESSION["auth"] = "yes"; //------------------
    $_SESSION["pareiskejas"] = $pareiskejas;
    $_SESSION["naud_email"] = $naud_email;
    $_SESSION["grupe"] = $grupe;
    $_SESSION["naud_ak"] = $naud_ak;
}

$fields = [
    "naud_vardas" => "vardas", //read only field
    "naud_pavarde" => "pavardė", //read only field
    "naud_ak" => "asmens kodas", //read only field
    "naud_email" => "el. paštas",
    "naud_telef" => "telefonas",
    "naud_adr" => "adresas",
    "naud_org" => "organizacija",
];

session_start(); //????????

if (@$_POST["Button"] == "Keisti") {
    //is register_form.php
    /* Check for blanks */
    foreach ($_POST as $field => $value) {
        if ($value == "") {
            //$blanks[] = $field;
            $blanks[] = $fields[$field];
        } else {
            $good_data[$field] = strip_tags(trim($value)); //________pritaikyti ir kitose formose
        }
    } // end foreach POST

    /*

*/

    if (isset($blanks)) {
        $message_2 = "Turi būti užpildyti šie laukai: ";
        foreach ($blanks as $value) {
            $message_2 .= "$value, ";
        }
        extract($good_data); //-------pritaikyti ir kitose formose
        include "change_form.inc";
        exit();
    } // end if blanks found

    /* db epaslaugos lenteles naudotojai laukai:
naud_vardas
naud_pavarde 
naud_passw 
naud_sukurimo_data     vartotojas taip pat sutinka su sąlygomis
naud_email 
naud_telef 
naud_adr
naud_ak
*/
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
        include "change_form.inc";
        exit();
    } // end if errors are found 1

    /* check to see if user name already exists */
    include "dbstuff.inc";
    ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
        die("Nepavyko prisijungti prie duomenų bazės");

    $today = date("Y-m-d");
    $sql = "UPDATE naudotojai SET   naud_telef='$naud_telef', naud_adr='$naud_adr', naud_org='$naud_org' 
 WHERE naud_email='$naud_el_pastas' "; //  naud_email='$naud_email'
    mysqli_query($cxn, $sql) or die("Nepavyko atnaujinti įrašo");

    //$ok = mysqli_affected_rows($cxn);
    //if ($ok==0)
    //echo "<p class='errors'>Klaida įrašant duomenis!</p>\n";
    //else
    //echo "<p style='font-weight:bold'>Naudotojo $naud_vardas $naud_pavarde duomenys sėkmingai pakeisti.</p>\n";

    $pareiskejas = $_POST["naud_vardas"] . " " . $_POST["naud_pavarde"]; //trim ir t.t.

    $_SESSION["pareiskejas"] = $pareiskejas;
    $_SESSION["auth"] = "yes";
    $_SESSION["naud_email"] = $naud_email;
    $_SESSION["grupe"] = "par";

    $pranesimas_naudotojui = "Jūsų duomenys sėkmingai pakeisti.";
    include "change_form.inc";
    exit();
}

//jei nebuvo paspaustas mygtukas Keisti, atėjo iš index.php  nuorodos Keisti duomenis
// nuskaitome iš posto vardą, pavardę ir asm. kodą. ir if isset(@ak)
else {
    ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
        die("Nepavyko prisijungti prie duomenų bazės");
    $sql = "SELECT naud_ak, naud_adr, naud_org, naud_telef, naud_email, naud_vardas, naud_pavarde, naud_grupe FROM naudotojai WHERE naud_email='$naud_el_pastas' ";
    ($result2 = mysqli_query($cxn, $sql)) or die("El. paštas nerastas");
    $num2 = mysqli_num_rows($result2);

    if ($num2 > 0) {
        // yra toks el. paštas
        $row = mysqli_fetch_assoc($result2);
        $pareiskejas = $par;
        $naud_email = $row["naud_email"];
        $grupe = $row["naud_grupe"];
        $naud_ak = $row["naud_ak"];
        $naud_vardas = $row["naud_vardas"];
        $naud_pavarde = $row["naud_pavarde"];
        $naud_org = $row["naud_org"];
        $naud_telef = $row["naud_telef"];
        $naud_adr = $row["naud_adr"];

        mysqli_close($cxn); //nebūtina

        $_SESSION["auth"] = "yes";
        $_SESSION["pareiskejas"] = $pareiskejas;
        $_SESSION["naud_email"] = $naud_email;
        $_SESSION["grupe"] = $grupe;
        $_SESSION["naud_ak"] = $naud_ak;
    }

    include "change_form.inc";
}

//  else nėra toki el. pašto  else {}
?>



 ?>
