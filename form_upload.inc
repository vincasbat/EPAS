<!DOCTYPE html>
<html>
<head><title>Prašymų pateikimas</title>
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

<script type="text/javascript" src="./jquery-1.11.1.min.js"></script>
<script type="text/javascript">

function getExtention(fileName) {
  dots = fileName.split(".")
return dots[dots.length-1];
}

function showProgress(){
//validation:
var countfile = ($('form input:file').length+1);
var input;
var filesize = 0;
var file;


var ip = document.forms["myForm"]["ip"].value;
var numRegex =  /^([0-9a-zA-Z,; ]){4,254}$/;    
    if(!numRegex.test(ip))
 {
        alert('Reikia nurodyti PNO Nr.');
        document.getElementById('ip1').focus();
        return false;
        } 

 
var fileid="";        

for(i=1; i<countfile; i++) {

input = null;
file = null;

 
fileid = 'failas_' + i;
 

if(document.getElementById(fileid).value == "") {
   alert('Reikia nurodyti failą');
        document.getElementById(fileid).focus();
        return false;
        } 


input = document.getElementById(fileid);
if(input.files[0]) {
file = input.files[0];


if(file.name.length > 38){
   alert('Failo vardas neturi viršyti 30 ženklų!');
           document.getElementById(fileid).focus();
          return false;
}




filesize += file.size;
ext = getExtention(file.name);
    if(!(ext=='pdf' ||  ext=='doc' ||  ext=='docx' || ext=='PDF' ||  ext=='DOC' ||  ext=='DOCX' |  ext=='zip' |  ext=='ZIP' )) {
    alert('Turi būti pasirinktas PDF, ZIP, DOC arba DOCX failas!');
            document.getElementById(fileid).focus();
            return false;
    }




if((file.size/1024/1024) > 10){
    alert('Failas turi būti mažesnis kaip 10 MB!');
            document.getElementById(fileid).focus();
            return false;
    }





}

}//for

var fsize = filesize/1024/1024;
fsize = fsize.toFixed(2); 			

if (filesize/1024/1024>100)
{
alert('Failai per dideli (' + fsize + ' MB). Visų failų dydis neturi viršyti 100 MB.');
return false;
}


      document.getElementById('progress').style.visibility = 'visible';
      return true;
}//enf of function showProgress






function clearForm(oForm)
{  
var elements = oForm.elements;

   
  oForm.reset();


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

var countfile = ($('form input:file').length); 
for (var i = 2; i < (countfile+1); i++) { 
var curp = document.getElementById("p_"+i); 
curp.remove();
}

}


function addField(){

var countfile = ($('form input:file').length)+1;   // alert (countfile);
if(countfile>20) {alert("Daugiau pridėti failų negalima!"); return;}
var newp = document.createElement('p');
var ih = "<label for='failas_"+ countfile +"'>"+ countfile +" <span style='color:red;'>*</span></label><input type='file'  name='file_" + countfile +  "' size='60'  id='failas_" + countfile + "' />&nbsp;&nbsp;&nbsp;<img src='./imgs/d2.png'   id='img_" + countfile + "' onclick='javascript:deleteRow(this);'  onmouseover='mouseOn(this)' onmouseout='mouseOut(this)' title='Trinti' />";

newp.setAttribute("id", "p_"+countfile); 
newp.innerHTML = ih;
ap_id = "#" + "p_" + (countfile - 1);  
$(newp).insertAfter(ap_id);

}


function mouseOn(x) {x.src = 'imgs/d1.png';}
function mouseOut(x) {x.src = 'imgs/d2.png';}


function deleteRow(r) {  
var rowsid = r.parentNode.getAttribute("id"); 
var row = parseInt(rowsid.substring(2)); 
r.parentNode.remove();
var countfile = ($('form input:file').length); 
for (var i = row+1; i < (countfile+2); i++) { 
var curp = document.getElementById("p_"+i); 

curp.setAttribute("id", "p_"+(i-1)); 
    var c = curp.children;
    for (var b = 0; b < c.length; b++) {
     if(c[b].tagName=="LABEL") {
       c[b].innerHTML = (i-1) +" <span style='color:red;'>*</span>";
       c[b].setAttribute("for", "failas_"+(i-1));
       }
     if(c[b].tagName=="INPUT") {
       c[b].setAttribute("id", "failas_"+(i-1));
       c[b].setAttribute("name", "file_"+(i-1));
     }
     if(c[b].tagName=="IMG") {c[b].setAttribute("id", "img_"+(i-1));}

    }//for b
}//for
}// end function

</script>

</head>
<body>
<div id="container">
   <div id="header">
<?php
include "header.inc";
echo "</div><div id='nav'>\n";

echo "<a href='index.php'>Į pradžią</a>\n";
echo "<a href='upload.php'>Pateikti prašymą</a>\n";
echo "<a href='mano_prasymai.php'>Mano prašymai</a>\n";
echo "<a href='moketi.php'>Mokėti</a>\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />\n";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div><div id='content'>\n"; //išdėstymas

echo "<h4 style='color: grey;' >Prašymų pateikimas</h4>\n";

//#FF0000
?>

<p style="border:1px; border-style:solid; border-color:orange; padding: 1em;">
Jei norite pateikti prašymus patentų arba papildomos apsaugos liudijimų galiojimui pratęsti <a href="upload2.php">spauskite čia.</a></p>

<p>Prašymą atlikti teisiniams veiksmams, susijusiems su jau pateiktomis paraiškomis ar įregistruotais pramoninės nuosavybės objektais siųskite tokia tvarka:</p>
<ol>  
<li>Nurodykite pramoninės nuosavybės objekto (PNO) paraiškos, registracijos ar patento numerį (-ius) <i>PNO Nr.</i> laukelyje. Jeigu nurodote keletą numerių, juos atskirkite kableliais, pavyzdžiui, 2011 2222, 2012 3333.</li>   
<li>Nurodykite prašymo failą 1-ajame laukelyje paspaudę mygtuką <i>Parinkti (Browse)</i>. Jeigu reikia pateikti daugiau failų, spauskite mygtuką <i>Pridėti failą</i> </li>
<li>Jei reikia, į pastabų laukelį įrašykite papildomą informaciją.</li>
    <li>Spauskite mygtuką <i>Siųsti</i>.</li>
</ol>
<div style='text-align: left'>
<form enctype="multipart/form-data"
        action="<?php echo $_SERVER["PHP_SELF"]; ?>"
        method="POST"
	 name="myForm" 
	style="display: inline;"
	onsubmit="return showProgress();" >

<?php
if (isset($mess)) {
    echo "<p class='errors'> $mess </p>\n";
}
if (isset($mess_ok)) {
    echo "<p style='color:green;'><b>$mess_ok  Už paslaugą galite sumokėti per <a href='moketi.php'>Elektroninius valdžios vartus.</a> </b> </p>\n";
}
?>
 <input type="hidden" name="MAX_FILE_SIZE" value="25485760" />

<p><label for='ip'>PNO Nr. <span style='color:red;'>*</span> </label> 
<!--
<input type="text" name="ip" size="10" value="<?php if (isset($_POST["ip"])) {
    echo $_POST["ip"];
} ?>" />&nbsp;Pavyzdžiui, 2011 2222.</p>
-->
<textarea rows="2" cols="60" name='ip' id='ip1'><?php if (isset($_POST["ip"])) {
    echo $_POST["ip"];
} ?></textarea></p>

<p><label for='pastabos'>Pastabos </label>
<textarea rows="3" cols="60" name='pastabos'><?php if (
    isset($_POST["pastabos"])
) {
    echo $_POST["pastabos"];
} ?></textarea></p>


 <p id="p_1"><label for='failas_1'>1 <span style='color:red;'>*</span></label> <input type="file" name="file_1" id="failas_1" size="60"  /></p>
 

 <input type="hidden" name="myform_key" value="<?php echo md5("labas"); ?>" />
<label for"prideti">&nbsp;</label><input type='submit' name='Upload'  value='Siųsti' />
<img src='imgs/loading.gif' id='progress' style='visibility:hidden;' />
<input type='button' name='prideti' value='Pridėti failą' onclick='addField();' />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if (isset($mess_ok)) {
    echo "<script language=javascript>clearForm(document.forms[0])</script>\n";
} ?>

</form>
<input type='button' name='valyti' value='Išvalyti' onclick='clearForm(document.forms[0]);' />
</div>





<p><span style='color:red;'>*</span> &#8211; privalomi laukai.</p>

<p><b>Jeigu už paslaugą mokate ne per Elektroninius valdžios vartus, būtinai nurodykite mokėjimo duomenis prašymuose arba pastabų laukelyje, pavyzdžiui, pavedimo Nr. 58; mokėjimo data 2010-11-15; mokėtojas Vardenis Pavardenis (arba
UAB „Pavadinimas“); mokėjimo paskirtis: už prekių ženklo Nr. 55555 išrašo išdavimą. Atsiimant dokumentus  Valstybiniam patentų biurui būtina pateikti mokamąjį pavedimą su banko žymomis arba kvitą.</b> </p>

<p> Kiekvieno failo dydis neturi viršyti <b> 10 MB</b>, o vardas turi būti ne ilgesnis kaip 20 ženklų. Priimami formatai yra .doc, .docx, .zip ir .pdf.  </p>



<p >Mokėjimo duomenis taip pat galite nurodyti  pridėdami mokėjimo dokumento kopiją .pdf formatu.</p>




<?php
echo "</div>  <div id='footer'>\n";
include "footer.inc";
?>


</div>
</div>
</body></html>
 


