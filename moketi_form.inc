<!DOCTYPE html>
<html>
<head><title>Mokėjimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<style>
#nav a  {
display: block;
}
</style>



<script type="text/javascript">
function validatePrice()
{  
var forma = document.forms[0];
var suma = forma.elements["VK_AMOUNT"].value;
suma = suma.replace(",", ".");
if (suma.indexOf(".") == -1) suma = suma + ".00";
var validPrice = /^\d+(\.\d{2})?$/;
if (!validPrice.test(suma)) suma = "0.00";
forma.elements["VK_AMOUNT"].value = suma; 
}
</script>

</head>



</head>
<body>
<div id="container">
   <div id="header">
<?php
include("header.inc");
echo "</div><div id='nav'>\n";



echo "<a href='index.php'>Į pradžią</a>\n";
echo "<a href='upload.php'>Pateikti prašymą</a>\n";
echo "<a href='mano_prasymai.php'>Mano prašymai</a>\n";
echo  "<a href='moketi.php'>Mokėti</a>\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div><div id='content'>\n";   //išdėstymas



echo "<h4 style='color: grey;'>Mokesčių mokėjimo tvarka</h4>\n";
?>
 <p> Prašymai vykdomi tik tada, kai yra sumokėti Mokesčių už pramoninės nuosavybės objektų registravimą įstatyme nurodyti <a href='http://www3.lrs.lt/pls/inter3/dokpaieska.showdoc_l?p_id=485459&p_tr2=2' target='_blanc'>mokesčiai.</a> Mokesčius galima sumokėti:</p>

<ol>

<li>Per Elektroninius valdžios vartus. Mokėjimas bus užskaitytas iš karto po jo patvirtinimo pasirinktoje elektroninės bankininkystės sistemoje.<br />
<b>Pastaba.</b>  Mokant per Elektroninius valdžios vartus iš mokėtojo imamas papildomas nuo paslaugos teikėjo
nepriklausantis komisinis mokestis.
</li>

<br />



<form action="moketi2.php" method="POST" name="forma"> 
<fieldset>

<legend>Mokėjimas per Elektroninius vadžios vartus</legend>
<br />
        
<?php

if (isset($vk_amount)) $sel = $vk_amount; else $sel = "0.00";


if (isset($_SESSION['visosuma'])) {$sel = $_SESSION['visosuma'];  unset($_SESSION['visosuma']);}  	
if(!isset($vk_msg)) { if (isset($_SESSION['pastabos'])) 
      {$vk_msg = "EPAS:" . $upl_dok_id . '-'  . $_SESSION['pastabos'];  unset($_SESSION['pastabos']);} }





$date = new DateTime();
$result = $date->format('Y.m.d');
?>

<div id="field"><label for="VK_AMOUNT"> Suma, <?php if($result<'2015.01.01') echo '&nbsp;LTL'; else echo 'EUR'; ?> </label> <input type="text" name="VK_AMOUNT" size="20" value="<?php echo $sel; ?>"  onblur="validatePrice();" />  Mokėtina suma nurodyta <a href="http://www3.lrs.lt/pls/inter3/dokpaieska.showdoc_l?p_id=485459&p_tr2=2" target="_blank" title="Atsidarys naujame lange/kortelėje">įstatyme</a></div>



<div id='field'><label for='DOK_ID'> Prašymo Nr. </label>
  <input type="text" name="DOK_ID" size="20" value="<?php echo $upl_dok_id; ?>" />  
Prašymo Nr. galite rasti skyrelyje <a href="mano_prasymai.php" target="_blank" title="Atsidarys naujame lange/kortelėje">Mano prašymai</a> 
</div>
<div id='field'>
<label for='VK_MSG'>Paskirtis </label>
<input type="text" name="VK_MSG" size="60"  value="<?php echo $vk_msg; ?>" /> 
 
<div id="pvz">Pavyzdžiui, už patento Nr. 4562 galiojimo 5-uosius metus </div></div>


  <input type="submit" name="Pay"  value="Mokėti" style='margin-left: 17em; margin-bottom: .5em; margin-top: .5em' />



</fieldset>
</form>
<br />
<li>Grynaisiais pinigais, mokėjimo pavedimu (nurodymu) ir pašto perlaida mokesčiai už pramoninės nuosavybės objektų registravimą mokami <b>Valstybinei mokesčių inspekcijai prie Lietuvos Respublikos finansų ministerijos (VMI prie FM)</b> (įmonės kodas 188659752) 
į  banko „Swedbank“, AB sąskaitą 
Nr. LT24 7300 0101 1239 4300, 
įmokos kodas 5310. Sumokėjus šiuo būdu mokėjimo duomenis reikia pateikti kartu su prašymu pridedant mokėjimo dokumento kopiją PDF formatu, nurodant mokėjimo duomenis prašyme arba pastabų laukelyje.</li></ol>


<?php

echo "</div>  <div id='footer'>\n";
include("footer.inc");
?>


</div>
</div>
</body></html>
 


