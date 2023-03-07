<!DOCTYPE html>
<html>
<head><title>Pateikti dokumentai</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">

<link rel="shortcut icon" href="favicon.ico">
 <!--
<link rel="icon" type="image/gif" href="animated_favicon1.gif">
 -->

<style type='text/css'>
 
	option.atm {  color: red;}		
	
#nav a  {
display: block;
}
</style>



<script type="text/javascript">
function validatePrice()
{  
var forma = document.forms[0];
var suma = forma.elements["mokestis"].value;
suma = suma.replace(",", ".");
if (suma.indexOf(".") == -1) suma = suma + ".00";
var validPrice = /^\d+(\.\d{2})?$/;
if (!validPrice.test(suma)) suma = "0.00";
forma.elements["mokestis"].value = suma; 
}



function marke(pav) {
pav.style.backgroundColor = "red";
  }

function clearForm(oForm)
{  
oForm.reset();
document.getElementById("txa").style.display = 'none';
}


 function disableIfNotAtmestas()
    {
var cbo = document.getElementById("cbo"); 
var selind =    cbo.options[cbo.selectedIndex].text; 
var txa =     document.getElementById("txa");

if (selind=="Atmestas")
txa.disabled = false; else txa.disabled = true;
           
           
        }

function hideAtmPr(){
if (document.getElementById("cbo").value == 'Atmestas') {
document.getElementById("txa").style.display = 'block';
//document.getElementById("txa").style.visibility = 'visible';
document.getElementById("txar").focus();
}
else
//document.getElementById("txa").style.visibility = 'hidden';
document.getElementById("txa").style.display = 'none';
}

 
</script>


<script type="text/javascript">
<!--
	function openWin( windowURL, windowName, windowFeatures ) { 
		return window.open( windowURL, windowName, windowFeatures ) ; 
	} 
// -->
</script>


</head>
<body onload='hideAtmPr();'>
<div id="container">
   <div id="header">

<?php
include("header.inc");
echo "</div><div id='nav'>\n";

//heading ir galimi tolesni statusai
switch ($st)
{
case "is":
function getStatus()
{// IS atmestas nereikalingas, jei IS duos galutinį atsakymą
  return array(1=> 'IS', 'OK', 'IS atmestas', 'Atmestas');   //Vidos prašymu idedame OK
   }
$antraste =  "<h4 style='color: grey'>Išradimų skyriui pateiktas prašymas</h4>";
break;
case "pz":
function getStatus()
{
  return array(1=> 'PZ', 'OK', 'PZ atmestas', 'Atmestas');   //Vidos prašymu idedame OK
   }
$antraste =  "<h4 style='color: grey'>Prekių ženklų skyriui pateiktas prašymas</h4>";
break;
case "ap":
function getStatus()
{
  return array(1=> 'AP', 'OK','AP atmestas',  'Atmestas');   //Vidos prašymu idedame OK
   }
$antraste =  "<h4 style='color: grey'>Apeliaciniam skyriui pateiktas prašymas</h4>";
break;




case "gautas":
function getStatus()
{
  $formCode = array(1=> 'Gautas', 'PZ', 'IS', 'AP', 'OK', 'Atmestas');   //Vidos prašymu idedame OK
  return $formCode;
}
$antraste =  "<h4 style='color: grey'>Gautas prašymas</h4>";
break;
case "pz_ok":
function getStatus()
{
  $formCode = array(1=> 'PZ OK',  'OK', 'Atmestas');  //??? ar reikia atmesto?, gal reikia grazinti i pz
  return $formCode;
}
$antraste =  "<h4 style='color: grey'>Prekių ženklų ir dizaino skyriuje įvykdytas prašymas</h4>";
break;
case "is_ok":
function getStatus()
{
  $formCode = array(1=>  'IS OK', 'OK', 'Atmestas');
  return $formCode;
}
$antraste =  "<h4 style='color: grey'>Išradimų skyriuje įvykdytas prašymas</h4>";
break;

case "ap_ok":
function getStatus()
{
  $formCode = array(1=>  'AP OK', 'OK', 'Atmestas');
  return $formCode;
}
$antraste =  "<h4 style='color: grey'>Apeliaciniame skyriuje įvykdytas prašymas</h4>";
break;

case "pz_atm":
function getStatus()
{
  $formCode = array(1=>  'PZ atmestas',  'Atmestas', 'PZ', 'IS', 'AP');       //, 'IS', 'AP'
  return $formCode;
}
$antraste =  "<h4 style='color: red'>Prekių ženklų ir dizaino skyriuje atmestas prašymas</h4>";
break;
case "is_atm":
function getStatus()
{
  $formCode = array(1=> 'IS atmestas', 'Atmestas', 'IS', 'PZ', 'AP');     //, 'PZ', 'AP'
  return $formCode;
}
$antraste =  "<h4 style='color: red'>Išradimų skyriuje atmestas prašymas</h4>";
break;

case "ap_atm":
function getStatus()
{
  $formCode = array(1=> 'AP atmestas', 'Atmestas', 'AP', 'IS', 'PZ');   //, 'IS', 'PZ'
  return $formCode;
}
$antraste =  "<h4 style='color: red'>Apeliaciniame skyriuje atmestas prašymas</h4>";
break;

default:
function getStatus()
{
  $formCode = array(1=> 'Gautas', 'PZ', 'IS', 'AP', 'PZ OK', 'IS OK', 'AP OK', 'PZ atmestas', 'IS atmestas', 'AP atmestas', 'OK', 'Atmestas');
  return $formCode;
}
}  //switch



include("dbstuff.inc");
$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$query = "SELECT dok_id, dok_formos_kodas, mokestis, dok_kelias, naud_email, pastabos, status_dabar, status_dabar_date, ip, vykdytojas FROM dokai WHERE dok_id=$dok_id";              			 
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);   //tikrinti ar vienas įrašas

$laukas = $row['dok_id'];   $dokoid = $row['dok_id'];

switch (@$_SESSION['grupe'])
{
case "admins":
case "pr":
echo "<a href='gauti.php'>Priėmimo skyrius</a>\n";  //meniu
echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<a href='registras.php'>Registras </a>\n";
break;
case "pz":
echo "<a href='pz.php'>Prekių ženklų skyrius</a>\n";  //meniu
echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<a href='registras.php'>Registras </a>\n";
break;
case "is":
echo "<a href='is.php'>Išradimų skyrius</a>\n";  //meniu
echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<a href='registras.php'>Registras </a>\n";
break;
case "ap":
echo "<a href='ap.php'>Apeliacinis skyrius</a>\n";  //meniu
echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<a href='registras.php'>Registras </a>\n";
break;
default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch


echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";
if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}
echo "</div><div id='content'>\n";




echo "$antraste\n";
?>

<form enctype="multipart/form-data" action=<?php echo $_SERVER['PHP_SELF']?> method="POST">

<?php                       //----------------------------------------------FORM

$trinami_dokai=null;
$dokai=null;

echo "<table border=0 cellpadding='3'><tr><td class='heads'>&nbsp;&nbsp;&nbsp;&nbsp;Prašymo&nbsp;Nr. </td><td >", $laukas, "</td></tr>\n";

$ip = $row['ip'];
echo "<tr><td class='heads'>PNO Nr. </td><td >", $ip, "</td></tr>\n";


$naud_email = $row['naud_email'];
$query_naudotojai = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas, CONCAT(' ', naud_adr, '; tel. ', naud_telef, '; ', naud_org) AS par_duom FROM naudotojai WHERE naud_email = '$naud_email'";
$res = mysqli_query($cxn, $query_naudotojai) or die ("Error: ".mysqli_error($cxn));
$rw = mysqli_fetch_assoc($res);   //tikrinti ar vienas įrašas
$naud = $rw['pareiskejas'];
$naud_duom = $rw['par_duom'];

$naud_email_ref = "<a href= 'mailto:$naud_email' > $naud </a>";

echo "<tr><td class='heads'>Pareiškėjas </td><td>", $naud_email_ref, $naud_duom, "</td></tr>\n"; 


$kelias = $row['dok_kelias'];

if(strlen($kelias)>5) {

//$kelias = "uploaded_files/".$kelias;
$kelias = base64_encode($kelias);
echo "<tr><td class='heads'>Rinkmenos</td><td > <a href='view_file.php?id=$kelias' target='_blanc' ><img  src='imgs/doc.gif' onclick='marke(this);' oncontextmenu='marke(this);' /></a>\n";

// Kiti failai:----------------------------------------- pr
$query = "SELECT file_id, dok_kelias FROM kiti_failai WHERE dok_id = $dok_id";

$res = mysqli_query($cxn, $query) or die ("Klaida: ".mysqli_error($cxn));
$c = mysqli_num_rows($res);   // irasu skaicius
$n=1;
while($r = mysqli_fetch_assoc($res)) 
{
$trinami_dokai[$n]['fid']=$r['file_id'];
$trinami_dokai[$n]['dkelias']=$r['dok_kelias']; 
//echo "Failo Nr. ", $trinami_dokai[$n]['fid'], "<br />";
 $n++;
}

$t_dokai = sizeof($trinami_dokai);
for ($i=1;$i<=$t_dokai;$i++)
{
$file_id = $trinami_dokai[$i]['fid'];

$dok_kelias = $trinami_dokai[$i]['dkelias'];
$visas_kelias = "uploaded_files/".$dok_kelias;  
$dok_kelias = base64_encode($dok_kelias);    
//$e = $i+1;
 //  stuff-----------------------------------------

echo "<a href='view_file.php?id=$dok_kelias' target='_blanc' ><img src='./imgs/doc.gif'  onclick='marke(this);'  oncontextmenu='marke(this);' /></a>\n";
}
// Kiti failai:----------------------------------------- pab


}//if strlen


echo "</td></tr>\n";


$laukas = $row['status_dabar_date'];
echo "<tr><td class='heads'>Data </td><td>", $laukas, "</td></tr>\n";

//--------------dok_formos_kodas    //redaguojami laukai

if (isset($dok_formos_kodas)) $laukas = $dok_formos_kodas; else    //kkkkkkkkkkkkkkkkkkk
$laukas = $row['dok_formos_kodas'];    
$qry="select  dok_formos_kodas from dok_tipai order by dok_pavadinimas";  //distinct
$rslt = mysqli_query($cxn, $qry) or die ("Error: ".mysqli_error($cxn));
$n=1;
while($rw = mysqli_fetch_assoc($rslt)) 
{
     $dok_formos_kodai[$n]=$rw['dok_formos_kodas'];
 	  $n++;
}
$n=1;
$k = sizeof($dok_formos_kodai);

echo "<tr><td class='heads'> Kodas</td><td><select  name='dok_formos_kodas' size='1' maxlength='10' >";
for ($b=1;$b<=$k;$b++)
{
  $dfk=$dok_formos_kodai[$b];
  echo "<option value='$dfk'";
  if ($dfk == $laukas)
     echo " selected";
  echo ">$dfk </option>\n";	//$dfk
}
echo "</select></td></tr>\n";   //--------------dok_formos_kodas pabaiga

if (isset($mokestis)) $laukas = $mokestis; else    
$laukas = $row['mokestis'];
?>

<tr><td class="heads">Mokestis</td><td ><input type="text" name="mokestis" value="<?php echo $laukas; ?>"   maxlength="10" onblur="validatePrice();" /><?php echo '&nbsp;'.'EUR' ?></td></tr>

<?php
$qry="select naud_vardas, naud_pavarde, naud_email from naudotojai where naud_grupe  = 'admins' or naud_grupe = 'pr' or naud_grupe = 'ap' or naud_grupe = 'pz' or naud_grupe = 'is'  order by naud_pavarde";
$qry="select naud_vardas, naud_pavarde, naud_email from naudotojai where naud_grupe = 'is'  order by naud_pavarde";
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
if(isset($_POST['vykd'])) { 
$pasirinktaspar = $_POST['vykd']; 
} else
$pasirinktaspar = $row['vykdytojas'];


echo "<tr ><td class='heads'>Vykdytojas</td><td><select id='cbv'   name='vykd' size='1' maxlength='10'  \n>";
 echo "<option value='0'>Pasirinkite vykdytoją...</option>\n";
for ($b=1;$b<=$k;$b++)
{
echo "<option value='$paremail[$b]'";
if ($paremail[$b]==$pasirinktaspar) echo " selected ";
echo ">$pareiskejai[$b]</option>\n";	
 }


echo "</select></td></tr>";

if (isset($status_dabar)) $laukas = $status_dabar; else    
$laukas = $row['status_dabar'];					

$statusai = getStatus();    //tik leistinų statusų funkcija
$k = sizeof($statusai);

echo "<tr ><td class='heads'>Statusas</td><td><select id='cbo' onchange='hideAtmPr();'  name='status_dabar' size='1' maxlength='10'  \n>";
for ($b=1;$b<=$k;$b++)
{
  $statusas=$statusai[$b];

  echo "<option value='$statusas'";
  if ($statusas == $laukas)
     echo " selected";
  echo ">$statusas </option>\n";	
}
echo "</select></td></tr>";

echo "</table><br />\n";



//--------------statusas

if (isset($message)) echo "<p class='errors'> $message </p>\n" ;  // jei pvz. nenurodytas formos tipas
//if (isset($pastabos)) $laukas = trim($pastabos); else   {$laukas = $row['pastabos'];          }
//$past = $laukas;
$pastabos = $row['pastabos']; 

if(strpos($ip, 'CSV')) $pastab =  substr($pastabos, 0, 100)."..."; else $pastab = $pastabos;

echo "<label for='pastabos'  style='width: 7em;'>Pastabos</label>\n";
 echo "<textarea rows='2' cols='60' name='pastabos'>$pastab</textarea>\n";    


?>





<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
 
<!--
<label style='width: 7em;' for='dokai'>Prisegti </label> <input style='margin-left: 0em; margin-bottom: .5em' type="file" name="dokai" size="25" title="Prisegtas failas bus išsiųstas prašytojui"  />
<span style='font-size: small;'>(iki 2 MB)</span>
-->


            <p>    <input type="submit" name="Button" title="Keisti prašymo statusą" 
                   style='margin-left: 9em; margin-bottom: .5em;'
                   value="Keisti" disabled> &nbsp;statusą&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='laukti' value='taip'> Išsaugoti tik pastabas ir vykdytoją 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='clear' value='Išvalyti' onclick='clearForm(this.form);' /></p>
            </form>

<br />
<?php


function getFormCodes()     //kol kas nenaudojama
{
$qry="select distinct dok_formos_kodas from dok_tipai";
$rslt = mysqli_query($cxn, $qry) or die ("Error: ".mysqli_error($cxn));
$rw = mysqli_fetch_assoc($rslt);
   return $rw;
}


// Parodome mokėjimus per Elektroninės valdžios vartus:
unset($table_heads);

$table_heads = array("dok_id" => "Prašymo Nr.",
                       "suma" => "Suma",
                     "paskirtis"        => "Mokėjimo paskirtis",
 			"banko_pranesimas"          => "Banko pranešimas",
			"mok_data"          => "Data",
			
                     );


$query = "SELECT dok_id, suma, paskirtis, banko_pranesimas,  LEFT(mokejimo_data, 10) AS mok_data, moketojas FROM mokejimai WHERE naud_email='$naud_email' AND (dok_id =$dok_id or paskirtis LIKE '%$ip%') ORDER BY mok_id desc LIMIT 0, 100";

$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$c = mysqli_num_rows($result);  
//echo "<p>Rasta mokėjimų: $c </p>";
echo "<p>Mokėjimas per EVV  &nbsp;|&nbsp; \n";

echo "<a href=".'"'."JavaScript: newWindow = openWin('./visi_mokejimai.php?naud=$naud_email&dok_id=$dok_id&ip=$ip', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()".'"'.">Kiti prašytojo (-os) mokėjimai</a> &nbsp;|&nbsp; <a href=".'"'."JavaScript: newWindow = openWin('./details_ataskaitos.php?dok_id=$dok_id', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()".'"'.">Statusai</a></p>\n";


echo "<table border='0' cellpadding='2' class='fixedwidth' >\n";   //pagr. lentelė
//cellpadding='3' cellspacing='3' 
echo "<tr>\n";
foreach($table_heads as $heading)
{
   echo "<th>$heading</th>";
}
echo "</tr>\n"; //   edit, delete

$n=1;
while($row = mysqli_fetch_assoc($result)) 
{
  foreach($row as $field => $value)
  {
    $dokai[$n][$field]=$value;
  }
  $n++;
}
$n_dokai = sizeof($dokai);
for ($i=1;$i<=$n_dokai;$i++)
{

$pran = $dokai[$i]['banko_pranesimas']; 
$did = $dokai[$i]['dok_id'];
if ($dokoid==$did ) echo "<tr style='background: lightgreen'>\n";


else  {

  if ($i%2==0) echo "<tr>\n"; else echo "<tr class='odd'>\n";    //odd rows
}
//echo "<td>{$dokai[$i]['dok_id']}</td>\n";




//if ($dokoid==$did) echo "<td style='background: lightgreen; '>$did</td>\n";
//else
echo "<td style='text-align: right; padding-right: 5px;'>$did</td>\n";
$title = $dokai[$i]['moketojas'];


$suma = $dokai[$i]['suma'];

if($dokai[$i]['mok_data']<'2015.01.01') $suma .= '&nbsp;LT'; else $suma .= '&nbsp;EUR';

echo "<td style='text-align: right; padding-right: 5px;' title='$title'>$suma</td>\n";

//$ip  $pos = strpos($mystring, $findme);
$mok_paskirtis = $dokai[$i]['paskirtis'];
$pos = strpos($mok_paskirtis, $ip);
if (!($pos === false)) $mok_paskirtis = str_replace($ip, "<span style='background-color:lightgreen;'>$ip</span>", $mok_paskirtis);

echo "<td>$mok_paskirtis </td>\n";
//echo "<td>{$dokai[$i]['paskirtis']} </td>\n";
//echo "<td>{$dokai[$i]['banko_pranesimas']} </td>\n";
//$pran = $dokai[$i]['banko_pranesimas'];
if ($pran == '1901' || $pran == '3101') echo "<td style='text-align: center;'><span style='color: red;'>".$pran."</span></td>\n"; else echo "<td style='text-align: center;'>$pran</td>\n";
echo "<td>{$dokai[$i]['mok_data']} </td>\n";
}   //for
echo "</table><br />\n";  //baigias mokėjimų lentelė




if((substr_count($pastabos, ':')>1) && !strpos($ip, 'CSV')) {

$pratesimai = explode(" ", $pastabos);
$dvitaskiai =  substr_count($pratesimai[0], ':');				
if($dvitaskiai == 2) $span = 2; else $span = 3;  				
echo "<p><b>Patentų galiojimo pratęsimai</b>   </p>";
echo "<table border='0' cellpadding='2' class='fixedwidth' >\n";
echo "<tr><td></td><td>Prašymo Nr.</td><td colspan='$span'> $dokoid </td></tr>";
echo "<tr><td></td><td>Pareiškėjas</td><td colspan='$span'> $naud </td></tr>";
echo "<tr><td></td><td>Mokėtojas</td><td colspan='$span'> $title </td></tr>";
if($dvitaskiai == 2) 
echo "<tr><th></th><th>Patento Nr.</th><th>Metai</th><th>Suma</th></tr>";
else
echo "<tr><th></th><th>Patento Nr.</th><th>Metai</th><th>Suma</th><th>Info</th></tr>";
for ($i=0; $i<count($pratesimai); $i++)
{
$eil = explode(":", $pratesimai[$i]);
if($dvitaskiai == 2) 
echo "<tr><td>" . ($i+1) . "</td><td>". $eil[0] ."</td><td>". $eil[1] ."</td><td>". $eil[2] ."</td></tr>";
else
echo "<tr><td>" . ($i+1) . "</td><td>". $eil[0] ."</td><td>". $eil[1] ."</td><td>". $eil[2] ."</td><td>". $eil[3] ."</td></tr>";

}
echo "</table><br />\n"; 
}

if((substr_count($pastabos, ':')>1) && strpos($ip, 'CSV')) {
$pratesimai = json_decode($pastabos);
echo "<p><b>Patentų galiojimo pratęsimai</b> </p>";
echo "<table border='0' cellpadding='2' class='fixedwidth' >\n";
echo "<tr><td></td><td>Prašymo Nr.</td><td colspan='3'> $dokoid </td></tr>";
echo "<tr><td></td><td>Pareiškėjas</td><td colspan='3'> $naud </td></tr>";
echo "<tr><td></td><td>Mokėtojas</td><td colspan='3'> $title </td></tr>";

echo "<tr><th></th><th>Patento Nr.</th><th>Metai</th><th>Suma</th><th>Info</th></tr>";
for ($i=0; $i<count($pratesimai); $i++)
{
$eil =  $pratesimai[$i];
if(isset($eil->info)) $info = $eil->info; else  $info = '';
echo "<tr><td>" . ($i+1) . "</td><td>". $eil->patnr ."</td><td>". $eil->metai ."</td><td>". number_format($eil->suma, 2, '.', '') ."</td><td>". $info ."</td></tr>";
}
echo "</table><br />\n"; 

}//if
mysqli_close($cxn);

//echo "<image src='imgs/banner_right.gif' />\n";
?>
<hr />

<table>




<tr style="background: white;"><td>1101&nbsp;</td><td>Banko pranešimas apie  sėkmingą paslaugos apmokėjimo operacijos įvykdymą</td></tr>
<tr style="background: white;"><td>1201</td><td>Banko pranešimas apie sėkmingai priimtą, bet dar nepatvirtintą paslaugos apmokėjimo operaciją</td></tr>
<tr style="background: white;"><td style='color: red;'>1901</td><td>Banko pranešimas apie nutrauktą paslaugos apmokėjimą</td></tr>
<tr style="background: white;"><td style='color: red;'>3101</td><td>Užklausos parašas neteisingas</td></tr>
</table>
<p>Užvedus pelę ant sumos parodomas mokėtojo (ne pareiškėjo) vardas. </p>


<?php
include("sutart_zym.inc");
echo "</div>  <div id='footer'>\n";
include("footer.inc");

?>

</div>
</div>
</body></html>
