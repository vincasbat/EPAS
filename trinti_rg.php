<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
   				
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
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}
?>
<br /><br />

</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>Įrašas ištrintas</h4>



<?php


include("dbstuff.inc");

include("stuff.inc");

$doknr = $_GET['doknr'];

$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");

//Čia iš bazės nuskaityti kelią į failą:
$query = "SELECT kelias FROM siunc_registras WHERE reg_ai = $doknr";  //$pv tb kabutese
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$rw = mysqli_fetch_assoc($result);
$dok_kelias = $rw['kelias'];
$visas_kelias = "./pazymos/".$dok_kelias;


$query = "DELETE FROM siunc_registras WHERE reg_ai = $doknr"; 
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));

/*
$fh = fopen($visas_kelias, 'w') or die("Negalima atidaryti failo");
fclose($fh);    
unlink("$visas_kelias");   
*/


echo "<b>Dokumentas Nr. $doknr sėkmingai ištrintas.</b><br />\n";
echo $visas_kelias."<br />"; // der
/*		Failo trynimo funkcija
function fileDelete($filepath,$filename) {
	$success = FALSE;
	if (file_exists($filepath.$filename)&&$filename!=""&&$filename!="n/a") {
		unlink ($filepath.$filename);
		$success = TRUE;
	}
	return $success;	
}
*/

mysqli_close($cxn);
?>
<a href='registras.php'>Atgal</a><br />
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
</div><!-- container --></body></html>
 
