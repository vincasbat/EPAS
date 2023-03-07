<!DOCTYPE html>
<html>
<head><title>Naujo naudotojo registracija</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">
 </head>
 <body>
<div id="container">
   <div id="header">
<?php
include("header.inc");
echo "</div><div id='nav'>\n";



echo "<a href='adm.php'>Administravimas</a><br />\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}


echo "</div><div id='content'>\n";   //išdėstymas
 
 
 
//Registravimo formos laukai 
 $fields =array("naud_vardas"=> "Vardas",
"naud_pavarde" => "Pavardė",
"naud_email"=> "El. paštas",
"naud_passw"=> "Slaptažodis",
"conf_passw"=> "Pakartoti", 
"naud_telef" => "Telefonas",
"naud_adr" => "Adresas",
 "naud_org" => "Organizacija",
"naud_grupe" => "Grupė",
);
 
 ?>
 

 
 <h4>Naujo naudotojo registracija</h4> 
<form action=<?php echo $_SERVER['PHP_SELF']?>

method="POST">
<fieldset style='border: 1px solid #000000; border-color: grey;'>
 <legend>Registracija</legend>

 
<?php
 
 
 if(isset($message_2))
 {
 echo "<p class='errors'>$message_2</p>\n";
 }

 foreach($fields as $field => $value)
 {

if($field == "naud_grupe")
  {
    echo "<div id='field'>  <label for='$field'>$value</label>
      <select name='naud_grupe' id='grupe'>";
    
echo "<option value='par' >PAR</option>\n";
echo "<option value='pz'>PZ</option>\n";
echo "<option value='pr'>PR</option>\n";
echo "<option value='is'>IS</option>\n";
echo "<option value='ap'>AP</option>\n";
echo "<option value='admins'>ADMINS</option>\n";
        echo "</select></div>";
  }
  else
  {


 if(preg_match("/pass/i",$field))
 $type = "password";
 else
 $type = "text";
echo "<div id='field'>
 <label for='$field'>$value</label>
 <input id='$field' name='$field' type='$type' value='".@$$field."' size='40' maxlength='65' /></div>\n";
}
 } 

 ?>




 <input type="submit" name="Button"
style='margin-left: 45%; margin-bottom: .5em'
value="Registruoti">
<hr />
Slaptažodį turi sudaryti nuo 6 iki 10 simbolių.
</fieldset>
 </form>

<a href='naud_adm.php'>Atgal</a><br />
<hr />
par - pareiškėjai<br />
admins - administratoriai<br />
pz, pr, is, ap - VPB darbuotojai<br />

 <?php
echo "</div>  <div id='footer'>\n";
include("footer.inc");
?>
 </div>
</div>
</body></html>

