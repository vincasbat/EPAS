<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
session_start();
if (@$_SESSION["auth"] == "yes") {
    $naud_el_pastas = $_SESSION["naud_email"];
    $grupe = $_SESSION["grupe"];
    $par = $_SESSION["pareiskejas"];
    $mess_pareiskejas = "<span style='color:green;'> $par </span>";
}

if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
}

switch (@$_SESSION["grupe"]) {
    case "admins":
        break;
    case "pz":
        header("Location: pz.php");
        break;
    case "is":
        header("Location: is.php");
        break;

    case "pr":
        header("Location: gauti.php");
        break;

    case "ap":
        header("Location: ap.php");
        break;

    default:
        header("Location: nera_teisiu.php");
        break;
}

//switch
?>






<!DOCTYPE html>
<html>
<head><title>Grupės</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">
<style type="text/css"  media="screen">
</style>



</head>
<body>
<div id="container">
   <div id="header">

<?php include "header.inc"; ?>

</div>

<div id="nav">


<a href='adm.php'>Administravimas</a><br />
<a href='ataskaitos.php'>Ataskaitos</a><br />
<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>
<?php if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />\n";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
} ?>

<br /><br />

</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>Grupės 2</h4>


<?php
include "dbstuff.inc";
$mess = "";
if ($_POST) {
    $grupe2 = $_POST["grupe2"];
    $grupe2 = filter_var($grupe2, FILTER_SANITIZE_STRING);
    $grupe2 = trim($grupe2);
    if (strlen($grupe2) < 3) {
        $mess .= "Reikia nurodyti grupę! <br />";
    }
    ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
        die("Klaida! Nepavyko prisijungti prie duomenų bazės");
    $qre = "SELECT grupe  FROM grupes";
    ($rs = mysqli_query($cxn, $qre)) or die("Error: " . mysqli_error($cxn));
    $grps = [];
    while ($rwe = mysqli_fetch_assoc($rs)) {
        array_push($grps, $rwe["grupe"]);
    }
    if (in_array($grupe2, $grps)) {
        $mess .= "Tokia grupė jau yra! <br />";
    }
    mysqli_close($cxn);
}
// if post
else {
}
?>


<div style='text-align: left'>
<form   action="<?php echo $_SERVER["PHP_SELF"]; ?>"    method="POST">
<fieldset>
<legend>Nauja grupė</legend>
<?php
if (isset($mess)) {
    echo "<p class='errors'> $mess </p>\n";
}

if (strlen($mess) == 0 && $_POST) {
    ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
        die("Klaida! Nepavyko prisijungti prie duomenų bazės");

    $grupe2 = mysqli_real_escape_string($cxn, $grupe2);
    $query = "INSERT INTO grupes (grupe) VALUES ('$grupe2')";
    ($result = mysqli_query($cxn, $query)) or
        die("Error: " . mysqli_error($cxn));
    mysqli_close($cxn);
    echo "<p style='color:green;'><b> Grupė įrašyta </b> </p>\n";
}
?>

<div id='field'>
<label for='grupe2'>Grupė <span style='color:red;'>*</span> </label> 

<input type="text" name="grupe2" size="10" value="<?php if (
    isset($_POST["grupe2"])
) {
    echo $_POST["grupe2"];
} ?>"   /> 


<?php
if (strlen($mess) == 0 && $_POST) {
    echo "<input type='submit' name='Teikti'  value='Įrašyti' disabled  />";
} else {
    echo "<input type='submit' name='Teikti'  value='Įrašyti'  />";
}
echo "</div><br>";
?>

  </fieldset> </form>

</div>
<br />



<?php
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$query = "SELECT grupe  FROM grupes";

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));

$c = mysqli_num_rows($result);

if ($c > 0) {
    echo "<p><b>Visos grupės ($c)</b></p>\n";

    echo "<table>\n";
    while ($row = mysqli_fetch_assoc($result)) {
        $gr = $row["grupe"];
        echo "<tr><td><b>", $gr, "</b></td><td>";
        $qr = "SELECT *  FROM naudotojai where grupe='$gr'";
        ($res = mysqli_query($cxn, $qr)) or die("Error: " . mysqli_error($cxn));
        while ($rw = mysqli_fetch_assoc($res)) {
            echo $rw["naud_vardas"], " ", $rw["naud_pavarde"], "<br>";
        }
        echo "</td></tr>";
    }
    echo "</table>\n";
}

mysqli_close($cxn);
?>


<br />



</div> <!-- content --> 


<div id='footer'>
<?php include "footer.inc"; ?>

</div>  <!-- footer -->
</div>  <!-- container -->
</body></html>
 
