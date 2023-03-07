<?php

session_start();

if (@$_POST["Button"] == "Prisijungti") {
    include "dbstuff.inc";
    ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
        die("Query died: connect");

    $naud_mail = $_POST[naud_email];
    $naud_passw = $_POST[naud_passw];

    $sql = "SELECT naud_email FROM naudotojai WHERE naud_email='$naud_mail'";
    ($result = mysqli_query($cxn, $sql)) or die("Query died: naud_email");
    $num = mysqli_num_rows($result);

    if (strlen($_POST[naud_passw]) < 6) {
        $message_1 = "Neteisingas slaptažodis! Bandykite dar kartą.";
        include "login_form.inc";
        exit();
    }

    if ($num > 0) {
        $sql = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas, naud_grupe FROM naudotojai WHERE naud_email='$naud_mail' AND naud_passw=md5('$naud_passw')";
        ($result2 = mysqli_query($cxn, $sql)) or die("Query died: password");
        $num2 = mysqli_num_rows($result2);

        //echo md5('$_POST[naud_passw]');

        if ($num2 > 0) {
            //password matches
            $row = mysqli_fetch_assoc($result2);
            $pareiskejas = $row["pareiskejas"];
            $naud_email = $row["naud_email"];
            $grupe = $row["naud_grupe"];

            $_SESSION["auth"] = "yes"; //------------------
            $_SESSION["pareiskejas"] = $pareiskejas;
            $_SESSION["naud_email"] = $naud_email;
            $_SESSION["grupe"] = $grupe;

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

        
        
        else {
            $message_1 = "El. pašto adresas  '$_POST[naud_email]' yra, bet jūs įvedėte neteisingą slaptažodį. Bandykite dar kartą.";
            $naud_email = strip_tags(trim($_POST[naud_email])); //   strip tags   ?????
            include "login_form.inc";
        }
    }

    // end if $num > 0
    elseif ($num == 0) {
        // login name not found
        $message_1 = "Tokio el. pašto adreso nėra! Bandykite dar kartą.";
        include "login_form.inc";
    }
}

//if button
//jei nebuvo paspaustas mygtukas Prisijungti
else {
    include "login_form.inc";
}

?>

