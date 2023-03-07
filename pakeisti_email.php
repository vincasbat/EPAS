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
<head><title>El. pašto adreso keitimas</title>
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
<h4 style='color: grey'>El. pašto adreso keitimas</h4>



<?php



if(isset($_POST['Upl']))
{
try {
   
$senas = $_POST['senas'];			
$naujas = $_POST['naujas'];
include("dbstuff.inc");
$cxn = mysqli_connect($host,$user,$passwd,$dbname)
			     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");

		
$query = "UPDATE atsiliepimai SET naud_email='$naujas' WHERE naud_email='$senas'";    
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));

$query = "UPDATE dokai SET naud_email='$naujas' WHERE naud_email='$senas'";    
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));

$query = "UPDATE dok_statusai SET naud_email='$naujas' WHERE naud_email='$senas'";    
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));

$query = "UPDATE mokejimai SET naud_email='$naujas' WHERE naud_email='$senas'";    
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));

$query = "UPDATE naudotojai SET naud_email='$naujas' WHERE naud_email='$senas'";    
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));


$query = "UPDATE siunc_registras SET adresatas='$naujas' WHERE adresatas='$senas'";    
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));


echo "<p style='color:green'>Naudotojo el. paštas pakeistas</p>\n";

mysqli_close($cxn);

} catch (Exception $e) {
    echo 'Klaida: ',  $e->getMessage(), "\n";
}




}
 
else
{
echo "<form  action='{$_SERVER['PHP_SELF']}'     method='POST'>\n";

echo "<label for='senas'>Senas el. paštas </label> \n";
echo "<input type='text' name='senas' size='50' value=''   /><br /> \n";
echo "<label for='naujas'>Naujas el. paštas  </label> \n";
echo "<input type='text' name='naujas' size='50' value=''   /> \n";

echo " <input type='submit' name='Upl'  value='Pakeisti'  /></form>\n";
}


?>

</div> <!-- content --> 


<div id='footer'>
<?php
include("footer.inc");
?>

</div>  <!-- footer -->
</div><!-- container --></body></html>
 
