<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
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
}

//switch
?>




<!DOCTYPE html>
<html>
<head><title>Įrašas ištrintas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">
<style type="text/css"  media="screen">
  <!--


-->   </style>

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
    echo "$mess_pareiskejas<br />";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
} ?>
<br /><br />

</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>Įrašas ištrintas</h4>



<?php
include "dbstuff.inc";

include "stuff.inc";

$dok_id = $_GET["dok_id"];

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

//Čia iš bazės nuskaityti kelią į failą:
$query = "SELECT dok_kelias FROM dokai WHERE dok_id = $dok_id"; //$pv tb kabutese
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$rw = mysqli_fetch_assoc($result);
$dok_kelias = $rw["dok_kelias"];
$visas_kelias = $dest . $dok_kelias;
echo $visas_kelias . "<br />Kiti failai:<br />"; // der

$query = "DELETE FROM dokai WHERE dok_id = $dok_id";
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));

$query = "DELETE FROM dok_statusai WHERE dok_id = $dok_id";
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));

($fh = fopen($visas_kelias, "w")) or die("Negalima atidaryti failo");
fclose($fh);
unlink("$visas_kelias");

// kiti failai:    ---------------------------------------------------------------------------------------------------- pr
//Į masyvą įrašome trinamų failų id ir kelia:
$query = "SELECT file_id, dok_kelias FROM kiti_failai WHERE dok_id = $dok_id";

($result = mysqli_query($cxn, $query)) or die("Klaida: " . mysqli_error($cxn));
$c = mysqli_num_rows($result); // irasu skaicius
$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $trinami_dokai[$n]["fid"] = $row["file_id"];
    $trinami_dokai[$n]["dkelias"] = $row["dok_kelias"];
    echo "Failo Nr. ", $trinami_dokai[$n]["fid"], "<br />";
    $n++;
}

$t_dokai = sizeof($trinami_dokai);
for ($i = 1; $i <= $t_dokai; $i++) {
    $file_id = $trinami_dokai[$i]["fid"];

    $dok_kelias = $trinami_dokai[$i]["dkelias"];
    $visas_kelias = $dest . $dok_kelias;
    echo $visas_kelias . " <br />"; // der
    ($fh = fopen($visas_kelias, "w")) or die("Negalima atidaryti failo");
    fclose($fh);
    unlink("$visas_kelias");
} // for trinami visi dokai ir failai

$query = "DELETE FROM kiti_failai WHERE dok_id = $dok_id";
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
// kiti failai:    ---------------------------------------------------------------------------------------------------- pab

echo "<b>Prašymas Nr. $dok_id sėkmingai ištrintas.</b><br />\n";

mysqli_close($cxn);
?>
<a href='db_adm.php'>Atgal</a><br />
<hr />
par - pareiškėjai<br />
admins - administratoriai<br />
pz, pr, is - VPB darbuotojai<br />

</div> <!-- content --> 


<div id='footer'>
<?php include "footer.inc"; ?>

</div>  <!-- footer -->
</div><!-- container --></body></html>
 
