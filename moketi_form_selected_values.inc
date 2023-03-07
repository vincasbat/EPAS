<!DOCTYPE html>
<html>
<head><title>Mokėjimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
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
echo "<a href='upload.php'>Pateikti prašymą</a><br />\n";
echo "<a href='mano_prasymai.php'>Mano prašymai</a><br />\n";
echo "<a href='salygos.php'>Taisyklės</a><br />";
       echo  "<a href='naud_instr.php'>Instrukcija</a><br />\n";
echo  "<a href='moketi.php'>Mokėti</a><br />\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div><div id='content'>\n";   //išdėstymas



echo "<h4 style='color: grey;'>Mokesčių mokėjimo tvarka</h4>\n";
?>
 <p> Prašymai vykdomi tik tada, kai yra sumokėti Mokesčių už pramoninės nuosavybės objektų registravimą įstatyme nurodyti <a href='http://www3.lrs.lt/pls/inter2/dokpaieska.showdoc_l?p_id=298250' target='_blanc'>mokesčiai.</a> Mokesčius galima sumokėti:</p>

<ol>

<li>Per Elektroninius valdžios vartus. Mokėjimas bus užskaitytas iš karto po jo patvirtinimo pasirinktoje elektroninės bankininkystės sistemoje.<br />
<b>Pastaba:</b>  Mokant per Elektroninius valdžios vartus iš mokėtojo imamas papildomas nuo paslaugos teikėjo
nepriklausantis komisinis mokestis.
</li>

<br />



<form action="moketi2.php" method="POST"> 
<fieldset>

<legend>Mokėjimas per Elektroninius vadžios vartus</legend>

        
<?php

$mokesciai = array(
"0"  => "Nurodykite sumą",
"0.01" => "0.01 LTL",
"40.00" => "40.00 LTL",
"80.00" => "80.00 LTL",
"100.00" => "100.00 LTL",
"120.00" => "120.00 LTL",
"240.00" => "240.00 LTL",
"300.00" => "300.00 LTL",
"320.00" => "320.00 LTL",
"400.00" => "400.00 LTL",
"800.00" => "800.00 LTL",
"1200.00" => "1200.00 LTL",
"1600.00" => "1600.00 LTL",
"2000.00" => "2000.00 LTL",
);

if (isset($vk_amount)) $sel = $vk_amount; else $sel = "0";

echo "<div id='field'><label for='VK_AMOUNT'> Suma, LTL </label><select  name='VK_AMOUNT' size='1' maxlength='20' >\n>";
foreach($mokesciai as $sum => $info)
{
  
  echo "<option value= '$sum'";
  if ($sum == $sel)
     echo " selected";
  echo "> $info </option>\n";	//$dfk
}
echo "</select>\n";

?>
Mokėtina suma nurodyta <a href='http://www3.lrs.lt/pls/inter2/dokpaieska.showdoc_l?p_id=298250' target='_blank' title='Atsidarys naujame lange/kortelėje'>įstatyme</a>
</div>

<div id='field'><label for='DOK_ID'> Prašymo Nr. </label>
  <input type="text" name="DOK_ID" size="20" value="<?php echo $upl_dok_id; ?>" />  
Prašymo Nr. galite rasti skyrelyje <a href="mano_prasymai.php" target="_blank" title="Atsidarys naujame lange/kortelėje">Mano prašymai</a> 
</div>
<div id='field'>
<label for='VK_MSG'>Paskirtis </label>
<input type="text" name="VK_MSG" size="60"  value="<?php echo $vk_msg; ?>" /> 
 
<div id="pvz">Pvz., už patento Nr. 4562 galiojimo 5-uosius metus </div></div>


  <input type="submit" name="Pay"  value="Mokėti" style='margin-left: 17em; margin-bottom: .5em; margin-top: .5em' />



</fieldset>
</form>
<br />
<li>Grynaisiais pinigais, mokėjimo pavedimu (nurodymu) ir pašto perlaida mokesčiai už pramoninės nuosavybės objektų registravimą mokami <b>Valstybinei mokesčių inspekcijai prie Lietuvos Respublikos finansų ministerijos (VMI prie FM)</b> (įmonės kodas 188659752) 
į  banko „Swedbank“, AB sąskaitą 
Nr. LT24 7300 0101 1239 4300, 
įmokos kodas 5310. Sumokėjus šiuo būdu mokėjimo duomenis reikia pateikti kartu su prašymu pridedant mokėjimo dokumento kopiją PDF formatu, nurodant mokėjimo duomenis prašyme arba pastabų laukelyje.</li></ol>


<?php
// Į mokesčių lentelę įrašyti iš banko gautus duomenis apie mokesčio sumokėjimą.


//echo "alert('I am sorry we need: First Name, Last Name and E-mail.');";


echo "</div>  <div id='footer'>\n";
include("footer.inc");
?>


</div>
</div>
</body></html>
 


