<!DOCTYPE html>
<html>
<head><title>Dokumentai be el. parašo</title>
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

function showProgress(){
//validation:
var countfile = ($('form input:file').length+1);
var input;
var filesize = 0;
var file;


var pars = document.forms["myForm"]["pareiskejai"].value;
if(pars=='0') {
        alert('Reikia nurodyti pareiškėją');
        document.getElementById('pars').focus();
        return false;
        } 



 var pnoid ="";
var selid="";
var fileid="";        

for(i=1; i<countfile; i++) {

  input = null;
file = null;

 pnoid = 'pno_' + i;
 selid = 'sel_' + i;
 fileid = 'file_' + i;

var pno = document.getElementById(pnoid).value;
var numRegex =  /^\d{4,7}$/; //     /^[\d]+$/;     

    if(!numRegex.test(pno)) {
        alert('Reikia nurodyti PNO numerį');
        document.getElementById(pnoid).focus();
        return false;
        }            
 
var sel = document.forms["myForm"][selid].value;
if(sel=='nera') {
        alert('Reikia nurodyti dokumento tipą');
        document.getElementById(selid).focus();
        return false;
        } 

if(document.getElementById(fileid).value == "") {
   alert('Reikia nurodyti failą');
        document.getElementById(fileid).focus();
        return false;
        } 


input = document.getElementById(fileid);
if(input.files[0]) {
file = input.files[0];
filesize += file.size;
ext = getExtention(file.name);
    if(ext!='pdf') {
    alert('Turi būti nurodytas PDF failas');
            document.getElementById(fileid).focus();
            return false;
    }
}


}//for

var fsize = filesize/1024/1024;
fsize = fsize.toFixed(2);

if (filesize/1024/1024>2)
{
alert('Failai per dideli (' + fsize + ' MB). Visų failų dydis neturi viršyti 2 MB.');
return false;
}


      document.getElementById('progress').style.visibility = 'visible';
      return true;
}//enf of function showProgress

function getExtention(fileName) {
  dots = fileName.split(".")
return dots[dots.length-1];
}

function clearForm(oForm)
{  
var element = document.getElementById("mess");
if (element!=null)  element.parentNode.removeChild(element);

 document.getElementById('progress').style.visibility = 'hidden';
document.getElementById('failai').innerHTML = "";
var elements = oForm.elements;
oForm.reset();
document.getElementById('failai').innerHTML = "";
var selektas = document.getElementsByName('pareiskejai')[0];
selektas.value = "0";

var selektas2 = document.getElementsByName('sel_1')[0];
selektas2.value = "nera";

  for(i=0; i<elements.length; i++) {
     
  field_type = elements[i].type.toLowerCase();
 
  switch(field_type) {
 
    case "text":
    case "textarea":
    elements[i].value = "";
      break;
case "file":

   break; 
      default:
      break;
  }
    }

  var table = document.getElementById("lent");
    for(var i = table.rows.length - 1; i > 1; i--)
    {   
    table.deleteRow(i);
    }
}//end valyti


function pno(name){
var arr = name.split("_");
var nr = arr[1];
var fname = "";

if(document.getElementById("pno_" + nr).value.length < 1) {
fname = document.getElementById("file_" + nr).value.replace(/\.[^/.]+$/, ""); //removes file extention
	if (fname.indexOf("fakepath") > -1)
	{
	fname = fname.substring(12);
	}
document.getElementById("pno_" + nr).value = fname;
}
}

function addField(){
var countfile = ($('form input:file').length)+1;
if(countfile>10) {alert("Daugiau pridėti failų negalima!"); return;}
var newp = document.createElement('p');
var ih = "<input type='file' name='file_" + countfile +  "' size='60'  id='file_"+ countfile +"'  onchange='pno(this.name);'/>";
//alert (ih);
//newp.innerHTML = ih;
//document.getElementById('failai').appendChild(newp);
var sel = document.getElementById('sel_1').cloneNode(true);
sel.setAttribute("id", "sel_"+countfile);
sel.setAttribute("name", "sel_"+countfile);

var pno = document.getElementById('pno_1').cloneNode(true);
pno.setAttribute("id", "pno_"+countfile);
pno.setAttribute("name", "pno_"+countfile);
pno.value = "";

var tableRef = document.getElementById('lent').getElementsByTagName('tbody')[0];
var newRow   = tableRef.insertRow(tableRef.rows.length);

var newCell0  = newRow.insertCell(0);
var newCell1  = newRow.insertCell(1);
var newCell2  = newRow.insertCell(2);



newCell0.appendChild(pno);
newCell1.appendChild(sel);
newCell2.innerHTML = ih;

//appendChild(newText2);
 
}

function searchSel() 
    {
      var input = document.getElementById('realtxt').value.toLowerCase();
       
          len = input.length;
          output = document.getElementById('realitems').options;
      for(var i=0; i<output.length; i++)
         // if (output[i].text.toLowerCase().indexOf(input) != -1 )
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
       document.getElementById("pno_1").focus();
        return false;
    }
}

</script>

</head >

<body  onload='setFocus();'>

<div id="container">
   <div id="header">
<?php
include("header.inc");
echo "</div><div id='nav'>\n";



switch (@$_SESSION['grupe'])
{
case "admins":
echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";

break;
case "pr":
echo "<a href='gauti.php'>Priėmimo skyrius</a>\n";  //meniu

break;
case "pz":
echo "<a href='pz.php'>Prekių ženklų skyrius</a>\n";  //meniu

break;
case "is":
echo "<a href='is.php'>Išradimų skyrius</a>\n";  //meniu

break;
case "ap":
echo "<a href='ap.php'>Apeliacinis skyrius</a>\n";  //meniu

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
if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div><div id='content'>\n";   //išdėstymas
echo "<h4 style='color: grey;'>Dokumentai be el. parašo</h4>\n";

?>

<div style='text-align: left'>
<form enctype="multipart/form-data" 
        action="<?php echo $_SERVER['PHP_SELF'] ?>"
        method="POST" 
        onsubmit="return showProgress();"
        name="myForm" 
         style="display: inline;">
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
/*
if(isset($_POST['pareiskejai'])) { 
$pasirinktaspar = $_POST['pareiskejai']; 
}
*/
echo "<p><label for='pareiskejai'> <span style='color:red;'>*</span></label> <select id='realitems'  name='pareiskejai' size='1' maxlength='30' id='pars'>";
echo "<option value='0'>Pasirinkite pareiškėją...</option>\n";
for ($b=1;$b<=$k;$b++)
{
echo "<option value='$paremail[$b]'";
if ($paremail[$b]==$pasirinktaspar) echo " selected ";
echo ">$pareiskejai[$b]</option>\n";	
 }//
echo "</select>   <img src='imgs/srch.png' style='vertical-align:middle;' /> <input type='text' id='realtxt' name='srch' onkeyup='javascript:searchSel();' onkeypress='return runScript(event)'/>  </p>";
if(isset($messok)) echo $messok;
?>
 <input type="hidden" name="MAX_FILE_SIZE" value="9097152" />

<table id="lent"><tr><td>
PNO Nr. <span style='color:red;'>*</span> 
</td><td>Tipas<span style='color:red;'>*</span> </td>
<td>Failas<span style='color:red;'>*</span>
</td></tr>
<tr><td><input type="text" name="pno_1" size="10" id="pno_1"  /></td>
<td>
<select name='sel_1' id='sel_1'>
<option value='nera'> Nurodykite tipą... </option>
<option value='ISR'  > Išrašas </option>
<option value='LIU' > Liudijimas </option>
<option value='PAZ' > Pažyma </option>
<option value='SPR' > Sprendimas </option>
<option value='PRA' > Pranešimas </option>
<option value='KIT' > Kita </option>"
</select>
</td>
<td>
<input type="file" name="file_1" size="60"  id="file_1" onchange="pno(this.name);" />
</td></tr>
</table>





  
 <div id="failai"></div>
 
 <input type="hidden" name="myform_key" value="<?php echo md5('labas'); ?>" />

<?php

   echo  "<br /><input type='submit' name='Upload'  value='Įkelti'  />&nbsp;\n";
echo "<img src='imgs/loading.gif' id='progress' style='visibility:hidden;' />";
echo "&nbsp;<input type='button' name='clear' value='Išvalyti' onclick='clearForm(this.form);' />\n";


if (isset($mess_ok)) echo "<script language=javascript>clearForm(document.forms[0])</script>\n";

?>

</form>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="addField();" >Pridėti failą</button>
</div>

<p><span style='color:red;'>*</span> &#8211; privalomi laukai.</p>

<?php
echo "</div>  <div id='footer'>\n";
include("footer.inc");
//mysqli_close($cxn);
?>

</div>
</div>
</body></html>
 


