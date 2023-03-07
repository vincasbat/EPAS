<!DOCTYPE html>
<html>
<head><title>Registracija</title>
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




echo "<br />\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p><br />\n";
echo "</div><div id='content'>\n";   //išdėstymas
 
 
 


//Registravimo formos laukai 
 $fields =array("naud_vardas"=> "Vardas",   
"naud_pavarde" => "Pavardė",   
"naud_ak" => "Asmens kodas",   
"naud_email"=> "El. paštas",
"naud_telef" => "Telefonas",
"naud_adr" => "Adresas",
"naud_org" => "Organizacija",
 
);
 
 ?>
 

 
 <h4>Valstybinio patentų biuro elektroninės paslaugos</h4> 

<p>Sveiki, Jūs patekote į Valstybinio patentų biuro elektroninių paslaugų  sistemą. Prašom pateikti informaciją apie save ir registruotis. Pirmiausia susipažinkite su naudojimosi Valstybinio patentų biuro elektronių paslaugų sistema  <a href='http://www3.lrs.lt/pls/inter3/dokpaieska.showdoc_l?p_id=483462&p_tr2=2' target='_blank' title='Atsidarys naujame lange/kortelėje'>  taisyklėmis. </a> </p>

<form action=<?php echo $_SERVER['PHP_SELF']; ?>   method="POST">
<fieldset >

 <legend>Registracija</legend>
<p></p>
 
<?php

$token = uniqid();                                     

$_SESSION['token'] = $token;
echo  "<input type='hidden' name='token' value='".$token."'/>\n";
// echo $token, "\n";
//echo '<input type="hidden" name="csrf_token" value="' . $csrf_token . '"/>';   
 
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

	if ($field=="naud_vardas" or $field=="naud_ak" or $field=="naud_pavarde")
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



 echo  "<p>Spausdamas (-a) mygtuką <i>Registruotis</i> patvirtinu, kad susipažinau su naudojimosi <a href='http://www3.lrs.lt/pls/inter3/dokpaieska.showdoc_l?p_id=483462&p_tr2=2' target='_blank' title='Atsidarys naujame lange/kortelėje'> taisyklėmis </a> ir įsipareigoju jų laikytis.</p>\n";
 
 if(isset($pranesimas_naudotojui))
echo  "<input type='submit' name='Button' style='margin-left: 16em; margin-bottom: .5em' value='Registruotis' disabled />\n";
else
echo  "<input type='submit' name='Button' style='margin-left: 16em; margin-bottom: .5em' value='Registruotis'  />\n";

if(isset($pranesimas_naudotojui)) echo $pranesimas_naudotojui; 

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

