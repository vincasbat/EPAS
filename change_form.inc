<!DOCTYPE html>
<html>
<head><title>Duomenų keitimas</title>
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



echo "<a href='index.php'>Į pradžią</a><br />\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
echo "</div><div id='content'>\n";  
 
 


//Registravimo formos laukai 
 $fields =array("naud_vardas"=> "Vardas",   //read only field
"naud_pavarde" => "Pavardė",   //read only field
"naud_ak" => "Asmens kodas",    //read only field
"naud_email"=> "El. paštas",
"naud_telef" => "Telefonas",
"naud_adr" => "Adresas",
"naud_org" => "Organizacija",
 
);
 
 ?>
 

 
 <h4>Valstybinio patentų biuro elektroninės paslaugos</h4> 

<form action=<?php echo $_SERVER['PHP_SELF']?>   method="POST">
<fieldset>
 <legend>Mano duomenų keitimas</legend>
<p></p>
 
<?php
 
 
 if(isset($message_2))
 {
 echo "<p class='errors'>$message_2</p>\n";
 }

 foreach($fields as $field => $value)
 {

 if(preg_match("/pass/i",$field))
 $type = "password";
 else
 $type = "text";

// padaryti ak, vardo ir pavardes laukus readonly
// echo $field;  echo "$field"; echo '$field';

	if ($field=="naud_vardas" or $field=="naud_ak" or $field=="naud_pavarde" or  $field=="naud_email")
	{
	echo "<div id='field'>        
	 <label for='$field'>$value</label>
	<input id='$field' name='$field' type='$type' value='".@$$field."' size='40' maxlength='65' readonly /></div>\n";
	}
	else
	{
	echo "<div id='field'>        
	 <label for='$field'>$value</label>
	<input id='$field' name='$field' type='$type' value='".@$$field."' size='40' maxlength='65' /></div>\n";
	}

 } // end foreach field


 ?>
 
 <input type="submit" name="Button"
style='margin-left: 10em; margin-bottom: .5em'
value="Keisti">
<?php
if(isset($pranesimas_naudotojui))
echo "<p style='color:blue'>$pranesimas_naudotojui</p>"; 

?>


</fieldset>
 </form>



 <?php
echo "</div>  <div id='footer'>\n";
include("footer.inc");
?>
 </div>
</div>
</body></html>

