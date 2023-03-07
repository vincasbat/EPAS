<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: login.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
case "pr":  
$skyrius = "PR"; 
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;
case "is":
$skyrius = "IS";
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;
case "pz":
$skyrius = "PZ";
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;
case "ap":
$skyrius = "AP"; 
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;
case "admins":
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;

default:
$skyrius = "";
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch

?>

<!DOCTYPE html>
<html>
<head><title>PDF elektroninis parašas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">

<link rel="shortcut icon" href="./favicon.ico">
<script type="text/javascript">

function clearForm(oForm)
{  
oForm.reset();
}

function getName()
{  
var forma = document.forms[0];
var failovardas = baseName(forma.elements["mainDocument"].value);
var fvardas = failovardas.replace(/[^\w]/g,'');
forma.elements["documentTitle"].value = fvardas; 
}

function baseName(str)
{  
   var base = new String(str).substring(str.lastIndexOf('/') + 1); 
if(str.lastIndexOf("\\")!= -1)    base = new String(str).substring(str.lastIndexOf('\\') + 1);
   if(base.lastIndexOf(".") != -1)       
       base = base.substring(0, base.lastIndexOf("."));
   return base;
}

function getFullName()
{  
var forma = document.forms[0]; 
var pno = document.getElementsByName("pno").item(0).value; 
var israsas = document.getElementsByName("isr").item(0).value;
var date = new Date(); 
var men = date.getMonth() + 1;
var menstr = "";
if(men<10) menstr = "0"+men; else menstr = ""+men;
var diena = date.getDate();
var dienastr = "";
if(diena<10) dienastr = "0"+diena; else dienastr = ""+diena;
var time = date.getFullYear()+menstr+dienastr;
var skyrius = document.getElementById("skyrius").innerHTML;
var prnr = document.getElementById("prnr").innerHTML;
var fvardas = prnr  + "_" + pno  + "_" + time + "_" + israsas + "_" + skyrius + ".pdf";
forma.elements["failo_vardas"].value = fvardas;
document.forms[1].elements["file_name"].value = fvardas; 
}
</script>
<script>
function getFullName2()
{  
var forma = document.forms[0]; 
var pno = document.getElementsByName("pno").item(0).value; 
pno = pno.replace(/ /g, "");
var israsas = document.getElementsByName("isr").item(0).value;
var date = new Date(); 
var men = date.getMonth() + 1;
var menstr = "";
if(men<10) menstr = "0"+men; else menstr = ""+men;
var diena = date.getDate();
var dienastr = "";
if(diena<10) dienastr = "0"+diena; else dienastr = ""+diena;
var time = date.getFullYear()+menstr+dienastr;
var skyrius = "ISSS"; //document.getElementById("skyrius").innerHTML;
var fvardas = "Z_" + pno  + "_" + time + "_" + israsas + "_" + skyrius + ".pdf";
forma.elements["failo_vardas"].value = fvardas;
document.forms[1].elements["file_name"].value = fvardas; 
}
</script>

<script language="javascript" type="text/javascript">
<!--
function startUpload(){
if( document.getElementById('selectPno').value=='PNO')
    {
        alert("Reikia nurodyti PNO numerį");
document.getElementById('selectPno').focus();
        return false;
    }
 
 if( document.getElementById('selectTip').value=='TIP')
    {
        alert("Reikia nurodyti dokumento tipą");
document.getElementById('selectTip').focus();
        return false;
    }




input = document.getElementById("file_to_uplaod");
if(input.files[0]) {
file = input.files[0];

filesize = file.size;
fsize = filesize/1024/1024;
fsize = fsize.toFixed(2);

if (filesize/1024/1024>1)
{
alert('Failas per didelis (' + fsize + ' MB).  Failo dydis neturi viršyti 1 MB.');
return false;
}

ext = getExtention(file.name);
    if(ext!='pdf') {
    alert('Turi būti nurodytas PDF failas');
            document.getElementById("file_to_uplaod").focus();
            return false;
    }
}


      document.getElementById('f1_upload_process').style.visibility = 'visible';
      return true;
}

function getExtention(fileName) {
  dots = fileName.split(".")
return dots[dots.length-1];
}

function showProgress(){
      document.getElementById('progress').style.visibility = 'visible';
      return true;
}

function stopUpload(success){
      var result = '';
      if (success == 1){
         result = '<span style="color:green;">Failas sėkmingai įkeltas!<\/span><br/><br/>';
document.forms[1].style.visibility = 'visible';
      }
      else {
         result = '<span class="errors">Įkeliant failą įvyko klaida!<\/span><br/><br/>';
      }
      document.getElementById('f1_upload_process').style.visibility = 'hidden';
      document.getElementById('f1_upload_form').innerHTML = result;
      return true;   
}
//-->
</script>   


<style>
#f1_upload_process { visibility:hidden;}
</style>

</head>

 <body onload='getFullName();'>
<div id="container">
   <div id="header">

<?php       //#9999FF;  <body onload='getFullName();'>
include("./header.inc");
echo "</div>\n";      // cia pataisyti i nav
include("stuff.inc");



//išdėstymas
echo "<div id='nav'>\n";


switch (@$_SESSION['grupe'])
{
case "pr":  
case "admins": 
$padalinys = "Priėmimo skyrius";
echo "<a href='gauti.php'>Priėmimo skyrius</a><br />\n"; break;
case "is":
$padalinys = "Išradimų skyrius";
echo "<a href='is.php'>Išradimų skyrius</a><br />\n"; break;
case "pz":
$padalinys = "Prekių ženklų ir dizaino skyrius";
echo "<a href='pz.php'>Prekių ženklų skyrius</a><br />\n"; break;
case "ap":
$padalinys = "Apeliacinis skyrius";
echo "<a href='ap.php'>Apeliacinis skyrius</a><br />\n"; break;

default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch

//echo "<a href='elparasas.php'>ADOC</a><br />\n";
//$doknuor = $_SERVER['HTTP_REFERER'];


echo "<p align='center'><img  alt='VPB' src='./imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div>\n";


echo "<div id='content'>\n";
include("dbstuff.inc");

echo "<h4>Elektroninis parašas</h4>\n";

/*
if($_GET['dok']){
$dokTitle = $_GET['dok'];
} else {
$dokTitle = "";
}
*/

include("./dbstuff.inc");
$cxn = mysqli_connect($host,$user,$passwd,$dbname)     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");
$dok_id = $_GET['dok'];


echo "<p> Prašymo Nr. <b>". $_GET['dok']. "</b>\n";
$query = "SELECT ip FROM dokai WHERE dok_id=$dok_id";
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);   
$pno = $row['ip']; 
$pnos = explode(',', $pno); 
mysqli_close($cxn);
function trimpno($str) {return trim(str_replace(' ', '', $str));  }
$trimmed=array_map('trimpno',$pnos);
//var_dump($pnos);
//print_r($trimmed);
//echo $pno;
echo "PNO Nr.<select id='selectPno' name='pno' onchange='getFullName();'>";
echo "<option value='PNO'>" . 'Nurodykite PNO numerį...' . "</option>";
for($i=0; $i < count($trimmed); $i++)
   echo "<option value=". $trimmed[$i] .">" . $trimmed[$i] . "</option>";
echo "</select>";

echo " Dok. tipas<select id='selectTip' name='isr' onchange='getFullName();'>";
 echo "<option value='TIP'>" . 'Nurodykite dokumento tipą...' . "</option>";
   echo "<option value='ISR'>" . 'Išrašas' . "</option>";
   echo "<option value='LIU'>" . 'Liudijimas' . "</option>";
   echo "<option value='PAZ'>" . 'Pažyma' . "</option>";
echo "<option value='SPR'>" . 'Sprendimas' . "</option>";
echo "<option value='PRA'>" . 'Prašymas' . "</option>";
echo "<option value='KIT'>" . 'Kita' . "</option>";
echo "</select>
<span id='skyrius' style='visibility:hidden;'>$skyrius</span>
<span id='prnr' style='visibility:hidden;'>{$_GET['dok']}</span></p>";


?>



       
                <form action="uploadtbs.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="return startUpload();" >
<p>Failo vardas <input type='text' name='failo_vardas' size='37' value='' readonly/></p>
                     <p><br /><br />
                     
            PDF failas   <input name="myfile" type="file" size="30" id="file_to_uplaod" />
<img src="imgs/loading.gif" id="f1_upload_process" />
                         <br /><br />
          <input type="submit" name="submitBtn" class="sbtn" value="Įkelti" />
                     <span id="f1_upload_form"></span></p>
                     
                     <iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
                 </form>
            

<form action="./sign_gateway.php" method="GET" style="visibility:hidden;"  onsubmit="return showProgress();">
<input type="hidden" name="file_name" value="" />
<p><input type="submit" value="Toliau" class="btn" />&nbsp;<img src="imgs/loading.gif" id="progress" style="visibility:hidden;" /></p>
</form>





<?php

echo "</div><div id='footer'>\n";
include("./footer.inc");
echo "</div></div></body></html>\n";
?>
