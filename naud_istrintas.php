<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: login.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
   				//Palikti galutiniam variante tik adminus
case "admins":
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;

default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch
?>




<!DOCTYPE html>
<html>
<head><title>Naudotojas ištrintas</title>
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

<?php
include("header.inc");
?>

</div>

<div id="nav">


<a href='adm.php'>Administravimas</a><br />
<a href='ataskaitos.php'>Ataskaitos</a><br />
<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>
<?php
if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
?>
<br /><br />

</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>Naudotojas ištrintas</h4>



<?php


include("dbstuff.inc");

include("stuff.inc");

$n_email = $_GET['n_email'];

$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$query = "DELETE FROM naudotojai WHERE naud_email = '$n_email'"; 
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));

echo "<b>Naudotojas, kurio el. paštas yra $n_email, sėkmingai ištrintas.</b><br />\n";


?>
<a href='naud_adm.php'>Atgal</a><br />
<hr />
par - pareiškėjai<br />
admins - administratoriai<br />
pz, pr, is - VPB darbuotojai<br />

</div> <!-- content --> 


<div id='footer'>
<?php
include("footer.inc");
?>

</div>  <!-- footer -->
</div>  <!-- container -->
</body></html>
 
