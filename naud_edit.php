<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: login.php");
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
<head><title>Naudotojo duomenų keitimas</title>
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

<a href='ataskaitos.php'>Ataskaitos</a><br />
<a href='adm.php'>Administravimas</a><br />

<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>
<?php
if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}
?>
<br /><br />

</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>Naudotojo duomenų keitimas</h4>


<?php

include("dbstuff.inc");
include("stuff.inc");

if (isset($_GET['n_email']))
$n_email = $_GET['n_email'];

if (!isset($_POST['submitted']))
	{


$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$query = "SELECT naud_vardas, naud_pavarde, naud_sukurimo_data, naud_telef, naud_adr, naud_ak, naud_grupe, naud_org, grupe FROM naudotojai WHERE naud_email = '$n_email'";
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$rw = mysqli_fetch_assoc($result);   //tikrinti ar vienas įrašas
$naud_vardas = $rw['naud_vardas'];
$naud_pavarde = $rw['naud_pavarde'];
$naud_passw = $rw['naud_passw'];       
$naud_sukurimo_data = $rw['naud_sukurimo_data'];   
$naud_telef = $rw['naud_telef'];
$naud_adr = $rw['naud_adr'];
$naud_ak = $rw['naud_ak'];      
$naud_grupe = $rw['naud_grupe'];
$naud_org = $rw['naud_org'];

$grupe = $rw['grupe'];
?>



<form action=<?php echo $_SERVER['PHP_SELF']?> 
 method="POST">
<fieldset style='border: 2px solid #000000; border-color: grey;'>
<legend>Naudotojo duomenų keitimas</legend>

<div id="field">
<input type="hidden" name="submitted" value="true" />
<input type="hidden" name="n_email" value="<?php echo $n_email; ?>" />
</div>

<div id="field">
<label for = 'naud_vardas'>Vardas</label>
<input type="text" name="naud_vardas" value="<?php echo $naud_vardas; ?>" size="40" maxlength="65" />
</div>

<div id="field">
<label for = 'naud_pavarde'>Pavardė</label>
<input type="text" name="naud_pavarde" value="<?php echo $naud_pavarde; ?>" size="40" maxlength="65" />
</div>

<div id="field">
<label for = 'naud_passw'>Slaptažodis</label>
<input type="password" name="naud_passw" value="" size="40" maxlength="65" />
</div>

<div id="field">
<label for = 'naud_telef'>Telefonas</label>
<input type="text" name="naud_telef" value="<?php echo $naud_telef; ?>" size="40" maxlength="65" />
</div>

<div id="field">
<label for = 'naud_adr'>Adresas</label>
<input type="text" name="naud_adr" value="<?php echo $naud_adr; ?>" size="40" maxlength="65" />
</div>

<div id="field">
<label for = 'naud_org'>Organizacija</label>
<input type="text" name="naud_org" value="<?php echo $naud_org; ?>" size="40" maxlength="65" />
</div>

<div id="field">
 <label for="naud_grupe">Grupė</label>
      <select name='naud_grupe'>";
    
<option value="par"  <?php if ($naud_grupe=="par") echo " selected"; ?> >PAR </option>
<option value="pz" <?php if ($naud_grupe=="pz") echo " selected"; ?> >PZ </option>
<option value="pr" <?php if ($naud_grupe=="pr") echo " selected"; ?> >PR </option>
<option value="is" <?php if ($naud_grupe=="is") echo " selected"; ?> >IS </option>
<option value="ap" <?php if ($naud_grupe=="ap") echo " selected"; ?> >AP </option>
<option value="admins" <?php if ($naud_grupe=="admins") echo " selected"; ?>  >ADMINS </option>
         </select>
</div>

<?php
$qry="select grupe from grupes";
$grs = mysqli_query($cxn, $qry) or die ("Error: ".mysqli_error($cxn));
$n=1;
while($rw = mysqli_fetch_assoc($grs)) 
{
     $grupes[$n]=$rw['grupe'];
    $n++;
}  

$gr_count = sizeof($grupes);

echo "<p><label for='grupe'>Grupė 2</label> <select  name='grupe' size='1' maxlength='30' id='grs'>";
echo "<option value=''></option>\n";
for ($b=1;$b<=$gr_count;$b++)
{
echo "<option value='$grupes[$b]'";
if ($grupes[$b]==$grupe) echo " selected ";
echo ">$grupes[$b]</option>\n";  
 }
echo "</select></p>";
mysqli_close($cxn);
?>


<input type="submit" name="Button"
style='margin-left: 45%; margin-bottom: .5em'
value="Keisti">
<hr />
Slaptažodį turi sudaryti nuo 6 iki 10 simbolių.
</fieldset>
</form>

<?php
}  // if (!isset($_POST['submitted']))     -----------------------------------------------------
else
{

$naud_vardas = trim($_POST['naud_vardas']);
$naud_pavarde = trim($_POST['naud_pavarde']);
$naud_passw = trim($_POST['naud_passw']);       
$naud_telef = trim($_POST['naud_telef']);
$naud_adr = trim($_POST['naud_adr']);
$naud_org = trim($_POST['naud_org']);
$naud_grupe = trim($_POST['naud_grupe']);
$grupe = trim($_POST['grupe']);
$n_email = trim($_POST['n_email']);



	


 if (!preg_match("/^(.+){1,50}$/",$naud_vardas))  
 {
 $errors[] = "Toks vardas  negalimas. ";
 }
  if (!preg_match("/^(.+){1,50}$/",$naud_pavarde))  
 {
 $errors[] = "Tokia pavardė  negalima. ";
 }

 
 if(!preg_match("/^(.+){1,100}$/", $naud_adr))
 {
 $errors[] = "Toks adresas negalimas. ";
 }
 
if(!preg_match("/^(.+){1,100}$/", $naud_org))
 {
 $errors[] = "Tokia organizacija negalima. ";
 }


 if(!preg_match("/^[0-9)( -+]{5,20}$/", $naud_telef))
 {
$errors[] = "Neteisingas telefono numeris. ";
 }


 if (!preg_match("/^(.+){6,10}$/",$naud_passw))    //???????? passw
 {
 $errors[] = "Slaptažodį turi sudaryti nuo 6 iki 10 simbolių. ";
 }
 





// $field = strip_tags(trim($_POST['smth']));      //-------pritaikyti ir kitose formose
 

 if(@is_array($errors)) 
 {
 $message = ""; 
 foreach($errors as $value)
 {
 $message .= $value." Bandykite dar kartą.<br />";
 }

echo "<p class='errors'>$message</p>\n";
echo "<hr /><a href='naud_adm.php'>Atgal</a><br />\n";
echo "</div><div id='footer'>";
include("footer.inc");
echo "</div> </div></body></html>";
exit();
}

	


$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");

 $sql = "UPDATE naudotojai SET  naud_vardas='$naud_vardas', naud_pavarde='$naud_pavarde', naud_passw=md5('$naud_passw'),  naud_telef='$naud_telef', naud_adr='$naud_adr', naud_grupe='$naud_grupe', naud_org='$naud_org', grupe='$grupe' WHERE naud_email='$n_email' LIMIT 1";
 mysqli_query($cxn,$sql);
 

$ok = mysqli_affected_rows($cxn);
if ($ok==0)
echo "<p class='errors'>Klaida įrašant duomenis!</p>\n"; 
else
echo "<p style='font-weight:bold'>Naudotojo $naud_vardas $naud_pavarde duomenys sėkmingai pakeisti.</p>\n";    

mysqli_close($cxn);
} 
?>


<a href='naud_adm.php'>Atgal</a><br />
<hr />
par - pareiškėjai<br />
admins - administratoriai<br />
pz, pr, is, ap - VPB darbuotojai<br />

</div> <!-- content --> 


<div id="footer">
<?php
include("footer.inc");
?>

</div>  <!-- footer -->
</div>  <!-- container --></body></html>
