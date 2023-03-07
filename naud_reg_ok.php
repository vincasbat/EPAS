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
$mess_pareiskejas = "<p style='color:green;'> $par </p>";
break;

default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch
?>





<!DOCTYPE html>
<html>
<head><title>Registracija sėkminga</title>
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

<a href='index.php'>Į pradžią</a><br />
<a href='upload.php'>Prašymų pateikimas</a><br />

<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p><br />
<?php
if(isset($mess_pareiskejas))
{
echo "<p>$mess_pareiskejas</p>";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}
?>
<br /><br />

</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>Registracija sėkminga</h4>

<b><a href='upload.php'>Pateikti prašymą</a><br /></b><br />

<?php







?>

</div> <!-- content --> 


<div id='footer'>
<?php
include("footer.inc");
?>

</div>  <!-- footer -->
</div>  <!-- container --></body></html>
 
