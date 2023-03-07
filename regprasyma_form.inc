<!DOCTYPE html>
<html>
<head><title>Prašymų pateikimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<script type="text/javascript">
function clearForm(oForm)
{  
var elements = oForm.elements;
oForm.reset();
var selektas = document.getElementsByName('pareiskejai')[0];
selektas.value = "0";

  for(i=0; i<elements.length; i++) {
     
  field_type = elements[i].type.toLowerCase();
 
  switch(field_type) {
 
    case "text":
    case "textarea":
    elements[i].value = "";
      break;
    
      default:
      break;
  }
    }
}
</script>

</head>
<body>
<div id="container">
   <div id="header">
<?php
include("header.inc");
echo "</div><div id='nav'>\n";



switch (@$_SESSION['grupe'])
{
case "admins":

echo "<a href='adm.php'>Administravimas</a><br />\n";
break;
case "pr":
echo "<a href='gauti.php'>Priėmimo skyrius</a><br />\n";  //meniu

break;
case "pz":
echo "<a href='pz.php'>Prekių ženklų skyrius</a><br />\n";  //meniu

break;
case "is":
echo "<a href='is.php'>Išradimų skyrius</a><br />\n";  //meniu

break;
case "ap":
echo "<a href='ap.php'>Apeliacinis skyrius</a><br />\n";  //meniu

break;
default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch


echo "<a href='ataskaitos.php'>Ataskaitos</a><br />\n";
echo "<a href='registras.php'>Registras </a><br />\n";
echo "<a href='regprasyma.php'>Registruoti prašymą </a><br />\n";
echo "<a href='isduoti.php'>Išduoti dokumentą</a><br />\n";
echo "<a href='isduoti2.php'>Be parašo</a><br />\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";
if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div><div id='content'>\n";   //išdėstymas
echo "<h4 style='color: grey;'>Ne per sistemą pateikto prašymo įkėlimas</h4>\n";
?>

<ol>  
<li>Nurodykite pareiškėją.</li> 
<li>Nurodykite pramoninės nuosavybės objekto (PNO) paraiškos, registracijos ar patento numerį  <i>PNO Nr.</i> laukelyje.</li>   
<li>Nurodykite prašymo rinkmeną paspaudę mygtuką <i>Parinkti (Browse)</i>.</li>
<li>Jei reikia, į pastabų laukelį įrašykite papildomą informaciją.</li>
    <li>Spauskite mygtuką <i>Įkelti</i>.</li>
</ol>
<div style='text-align: left'>
<form enctype="multipart/form-data"
        action="<?php echo $_SERVER['PHP_SELF'] ?>"
        method="POST">
<?php
if (isset($mess)) echo "<p class='errors'> $mess </p>\n" ;
if (isset($mess_ok)) 
echo "<p style='color:green;'><b>$mess_ok </b> </p>\n" ;

//+++++++++++++++++++++++++++++


$qry="select naud_vardas, naud_pavarde, naud_email from naudotojai order by naud_pavarde";
$rslt = mysqli_query($cxn, $qry) or die ("Error: ".mysqli_error($cxn));
$n=1;
while($rw = mysqli_fetch_assoc($rslt)) 
{
     $pareiskejai[$n]=$rw['naud_pavarde'].' '.$rw['naud_vardas'];
$paremail[$n]=$rw['naud_email'];
 	  $n++;
}  
$n=1;
$k = sizeof($pareiskejai);

$pasirinktaspar = '0';
if(isset($_POST['pareiskejai'])) { 
$pasirinktaspar = $_POST['pareiskejai']; 
}

echo "<p><label for='pareiskejai'>Pareiškėjas <span style='color:red;'>*</span></label> <select  name='pareiskejai' size='1' maxlength='30' >";
echo "<option value='0'>Pasirinkite pareiškėją...</option>\n";
for ($b=1;$b<=$k;$b++)
{
echo "<option value='$paremail[$b]'";
if ($paremail[$b]==$pasirinktaspar) echo " selected ";
echo ">$pareiskejai[$b]</option>\n";	
 }
echo "</select></p>";

?>
 <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />

<p><label for='ip'>PNO Nr. <span style='color:red;'>*</span> </label> 

<input type="text" name="ip" size="10" value="<?php if(isset($_POST['ip']))  echo $_POST['ip']; ?>" />&nbsp;Pavyzdžiui, 2011 2222.</p>

 <p><label for='dokai'>Failas <span style='color:red;'>*</span></label> <input type="file" name="dokai" size="60"  /></p>
 
 <input type="hidden" name="myform_key" value="<?php echo md5('labas'); ?>" />

<label for='pastabos'>Pastabos </label>

<textarea rows="3" cols="60" name='pastabos'><?php if(isset($_POST['pastabos']))  echo $_POST['pastabos']; ?></textarea>

<?php

   echo  "<p style='text-align: center;'><input type='submit' name='Upload'  value='Įkelti'  />&nbsp;&nbsp;<input type='button' name='clear' value='Išvalyti' onclick='clearForm(this.form);' /></p>\n";


if (isset($mess_ok)) echo "<script language=javascript>clearForm(document.forms[0])</script>\n";

?>
</form></div>

<p><span style='color:red;'>*</span> &#8211; privalomi laukai.</p>

<?php
echo "</div>  <div id='footer'>\n";
include("footer.inc");
mysqli_close($cxn);
?>

</div>
</div>
</body></html>
 


