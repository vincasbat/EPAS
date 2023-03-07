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
<head><title>Aplanko sukūrimas</title>
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
<h4 style='color: grey'>Aplanko sukūrimas</h4>



<?php



if(isset($_POST['Upl']))
{
$kelias = $_POST['kelias'];


			
			If(!is_dir($kelias))    mkdir($kelias, 0755); 
			
			
			


echo "<p style='color:green'>Aplankas $kelias sukurtas</p>\n";
}
 
else
{
echo "<form  action='{$_SERVER['PHP_SELF']}'     method='POST'>\n";

echo "<label for='kelias'>Kelias </label> \n";
echo "<input type='text' name='kelias' size='50' value=''   /><br /> \n";


echo " <input type='submit' name='Upl'  value='Sukurti aplanką'  /></form>\n";
}
//-----------------------------------

?>

</div> <!-- content --> 


<div id='footer'>
<?php
include("footer.inc");
?>

</div>  <!-- footer -->
</div><!-- container --></body></html>
 
