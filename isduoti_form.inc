<!DOCTYPE html>
<html>
<head><title>Dokumento siuntimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<script type="text/javascript">
function showProgress(){
      document.getElementById('progress').style.visibility = 'visible';
      return true;
}
function clearForm(oForm)
{  
 document.getElementById('progress').style.visibility = 'hidden';

var elements = oForm.elements;
oForm.reset();
var selektas = document.getElementsByName('pareiskejai')[0];
selektas.value = "0";

var selektas2 = document.getElementsByName('tipas')[0];
selektas2.value = "nera";

  for(i=0; i<elements.length; i++) {
     
  field_type = elements[i].type.toLowerCase();
 
  switch(field_type) {
 
    case "text":
    case "textarea":
    elements[i].value = "";
      break;
    
      default:
      break;
  }//switch
    }//for
document.getElementById("ann").value = 1;
}//func



//document.getElementById('realtxt').onkeyup = searchSel;
function searchSel() 
    {
      var input = document.getElementById('realtxt').value.toLowerCase();
       
          len = input.length;
          output = document.getElementById('realitems').options;
      for(var i=0; i<output.length; i++)
         
            if (output[i].text.toLowerCase().startsWith(input))
          {
          output[i].selected = true;
              break;
          } else output[0].selected = true;
      if (input == '')
        output[0].selected = true;
    }


function setFocus(){
    document.getElementById("realtxt").focus();
}

function runScript(e) {
    if (e.keyCode == 13) {
       document.getElementById("pno").focus();
        return false;
    }
}


function wpno(){
var fname = "";
if(document.getElementById("pno").value.length < 1) {
fname = document.getElementById("failas").value.replace(/[^0-9]/g,''); //lieka tik skaitmenys
document.getElementById("pno").value = fname;
}
}



 function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;
        return true;
    }  
</script>

<style>
#nav a  {
display: block;
}
</style>

</head >

<body  onload='setFocus();'>
<div id="container">
   <div id="header">
<?php
include "header.inc";
echo "</div><div id='nav'>\n";

switch (@$_SESSION["grupe"]) {
    case "admins":
        break;
    case "pr":
        echo "<a href='gauti.php'>Priėmimo skyrius</a>\n"; //meniu

        break;
    case "pz":
        echo "<a href='pz.php'>Prekių ženklų skyrius</a>\n"; //meniu

        break;
    case "is":
        echo "<a href='is.php'>Išradimų skyrius</a>\n"; //meniu

        break;
    case "ap":
        echo "<a href='ap.php'>Apeliacinis skyrius</a>\n"; //meniu

        break;
    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
}

echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<a href='registras.php'>Registras </a>\n";
echo "<a href='regprasyma.php'>Registruoti prašymą </a>\n";
echo "<a href='isduoti.php'>Išduoti dokumentą</a>\n";
echo "<a href='isduoti2.php'>Be parašo</a>\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";
if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />\n";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div><div id='content'>\n"; //išdėstymas
echo "<h4 style='color: grey;'>Dokumento išsiuntimas pareiškėjui</h4>\n";
?>

<div style='text-align: left'>
<form enctype="multipart/form-data"
        action="<?php echo $_SERVER["PHP_SELF"]; ?>"
        method="POST" onsubmit="showProgress();">
<?php
if (isset($mess)) {
    echo "<p class='errors'> $mess </p>\n";
}
if (isset($mess_ok)) {
    echo "<p style='color:green;'><b>$mess_ok </b> </p>\n";
}

//+++++++++++++++++++++++++++++

$qry =
    "select naud_vardas, naud_pavarde, naud_email from naudotojai order by naud_pavarde";
($rslt = mysqli_query($cxn, $qry)) or die("Error: " . mysqli_error($cxn));
$n = 1;
while ($rw = mysqli_fetch_assoc($rslt)) {
    $pareiskejai[$n] = $rw["naud_pavarde"] . " " . $rw["naud_vardas"];
    $paremail[$n] = $rw["naud_email"];
    $n++;
}
$n = 1;
$k = sizeof($pareiskejai);

$pasirinktaspar = "0";
if (isset($_POST["pareiskejai"])) {
    $pasirinktaspar = $_POST["pareiskejai"];
}

echo "<p><label for='pareiskejai'> <span style='color:red;'>*</span></label> <select id='realitems' name='pareiskejai' size='1' maxlength='30' >";
echo "<option value='0'>Pasirinkite pareiškėją...</option>\n";
for ($b = 1; $b <= $k; $b++) {
    echo "<option value='$paremail[$b]'";
    if ($paremail[$b] == $pasirinktaspar) {
        echo " selected ";
    }
    echo ">$pareiskejai[$b]</option>\n";
}
echo "</select>  <img style='vertical-align:middle;' src='imgs/srch.png'/> <input autocomplete='off' type='text' id='realtxt' name='srch' onkeyup='javascript:searchSel();'  onkeypress='return runScript(event)'/>   </p>";

//echo $file;
?>

 
 <input type="hidden" name="MAX_FILE_SIZE" value="20971520" />

<p><label for='ip'>PNO Nr. <span style='color:red;'>*</span> </label> 

<input id='pno' type="text"  autocomplete="off" name="ip" size="10" value="<?php if (
    isset($_POST["ip"])
) {
    echo $_POST["ip"];
} ?>" />&nbsp;Pvz., 6154.</p>


 
<p><label for='tipas'><span style='color:red;'>*</span> </label>
<select name='tipas'>
<option value='nera'> Nurodykite dokumento tipą... </option>
<option value='ISR' <?php if (isset($_POST["tipas"]) && $tipas == "ISR") {
    echo " selected ";
} ?> > Išrašas </option>
   <option value='LIU' <?php if (isset($_POST["tipas"]) && $tipas == "LIU") {
       echo " selected ";
   } ?>> Liudijimas </option>
   <option value='PAZ' <?php if (isset($_POST["tipas"]) && $tipas == "PAZ") {
       echo " selected ";
   } ?>> Pažyma </option>
<option value='SPR' <?php if (isset($_POST["tipas"]) && $tipas == "SPR") {
    echo " selected ";
} ?>> Sprendimas </option>
<option value='PRA' <?php if (isset($_POST["tipas"]) && $tipas == "PRA") {
    echo " selected ";
} ?>> Pranešimas </option>
<option value='KIT' <?php if (isset($_POST["tipas"]) && $tipas == "KIT") {
    echo " selected ";
} ?>> Kita </option>"
</select>
</p>

<p><label for='annot'>El. p. vieta </label> 
<input id='ann' type="text" name="annot" size="3" autocomplete="off" title="Puslapio, kuriame bus el. parašas, numeris" onkeypress="javascript:return isNumber(event)" value="<?php if (
    isset($_POST["annot"])
) {
    echo $_POST["annot"];
} else {
    echo 1;
} ?>" />&nbsp; &nbsp; &nbsp; &nbsp; 
  <input type="checkbox" name="elpformatas" value="pdf"  <?php if (
      isset($_POST["elpformatas"]) &&
      $elpformatas == "pdf"
  ) {
      echo " checked ";
  } ?> disabled  >PDF<br></p>



 <p><label for='dokai'>Failas <span style='color:red;'>*</span></label> 
<input id="failas" type="file" name="dokai" size="60" onchange="wpno();" /></p>
 
 <input type="hidden" name="myform_key" value="<?php echo md5("labas"); ?>" />

<?php
echo "<p style='text-align: center;'><input type='submit' name='Upload'  value='Įkelti'  />&nbsp\n";
echo "<img src='imgs/loading.gif' id='progress' style='visibility:hidden;' />";
echo "&nbsp;<input type='button' name='clear' value='Išvalyti' onclick='clearForm(this.form);' /></p>\n";

if (isset($mess_ok)) {
    echo "<script language=javascript>clearForm(document.forms[0])</script>\n";
}
?>
</form></div>

<p><span style='color:red;'>*</span> &#8211; privalomi laukai.</p>

<?php
echo "</div>  <div id='footer'>\n";
include "footer.inc";

//mysqli_close($cxn);
?>

</div>
</div>
</body></html>
 


