<?php		// neturi būti jokio tarpo prieš <?php
session_start();
 if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
case "admins":
case "pr":
case "is":
case "pz":
case "ap":
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;

default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch
?>


<!DOCTYPE html>
<html>
<head><title>Mokėjimai</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
 <link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="stylesheet" type="text/css" href="./style/pag.css">
<link rel="shortcut icon" href="favicon.ico">

<style type="text/css">
body {
	background: white;
}
#container {  
width: 100%;
}
</style>

</head>
<body>
<div id="container">
   <div id="header">

<?php

include("paginator.class.php");
//echo "</div><div id='nav'>\n";
echo "</div>\n";
include("dbstuff.inc");


echo "<div id='pop'>\n";

$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");

if (isset($_GET['naud']))
{ $get = $_GET['naud'];
$naud = " AND mokejimai.naud_email = '$get' ";
$naud2 = " WHERE naud_email = '$get' ";
}
else {
$naud = "";
$naud2 = "";
}
     
  $query = "SELECT mok_id   FROM mokejimai {$naud2}";
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
   $viso = mysqli_num_rows($result);

if ($viso<1)
{
echo "Mokėjimų nėra.</div></body></html>\n";
exit();
}
 $pages = new Paginator;  
    $pages->items_total   = $viso;
    $pages->mid_range = 3;  
    $pages->paginate();  

echo "<p style='color: grey;'><b>Naujausi mokėjimai per Elektroninius valdžios vartus ($viso)</b></p>\n";

    echo $pages->display_pages();

// Parodome mokėjimus per Elektroninės valdžios vartus:
unset($dokai);
unset($table_heads);

$table_heads = array(
"mok_id" => "Mok.Nr.",
"dok_id" => "Prašymo Nr.",
                       "suma" => "Suma",
                     "paskirtis"        => "Mokėjimo paskirtis",
 			"banko_pranesimas"          => "Banko pranešimas",
			"mok_data"          => "Data",
"pras"          => "Prašytojas",
"moketojas"          => "Mokėtojas",
"gavejas"          => "Gavėjas",
"banko_pranesimas"          => "Banko pr.",
"saskaita"          => "Gavėjo sąskaita"		
                     );



//mok_id, dok_id, suma, paskirtis, moketojas, naud_email, gavejas, banko_pranesimas, mokejimo_data, saskaita  CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pras
$query = "SELECT mok_id, dok_id, suma, paskirtis, banko_pranesimas, LEFT(mokejimo_data, 10) AS mok_data, moketojas, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pras, gavejas, banko_pranesimas, saskaita  FROM mokejimai, naudotojai where mokejimai.naud_email = naudotojai.naud_email {$naud} ORDER BY mok_id desc ".$pages->limit;
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$c = mysqli_num_rows($result);  

function toEur($litai)
{
return round($litai/3.4528, 2);
}


echo "<table border='0' cellpadding='2' class='fixedwidth' style='margin-top:7px; margin-bottom:7px' >\n";   
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

/*$did = $dokai[$i]['dok_id'];
if ($dokoid==$did) echo "<tr style='background: lightgreen'>\n";

else  {
  if ($i%2==0) echo "<tr>\n"; else echo "<tr class='odd'>\n";    //odd rows
}*/


	if ($dokai[$i]['dok_id']==$_GET['dok_id'])
		 echo "<tr style='background: lightgreen'>\n";
else {
  if ($i%2==0) echo "<tr>\n"; else echo "<tr class='odd'>\n";}

echo "<td style='text-align: right; padding-right: 6px;'>{$dokai[$i]['mok_id']}</td>\n";
echo "<td style='text-align: right; padding-right: 6px;'><u>{$dokai[$i]['dok_id']}</u></td>\n";
$suma = $dokai[$i]['suma'];

if($dokai[$i]['mok_data']<'2015.01.01') $suma .= '&nbsp;LT'; else $suma .= '&nbsp;EUR';


echo "<td style='text-align: right; padding-right: 5px;' ><b>$suma </b?</td>\n";


$mok_paskirtis = $dokai[$i]['paskirtis'];
$pos = strpos($mok_paskirtis, $_GET['ip']);
if (!($pos === false)) $mok_paskirtis = str_replace($_GET['ip'], "<span style='background-color:lightgreen;'>{$_GET['ip']}</span>", $mok_paskirtis);
echo "<td>$mok_paskirtis </td>\n";



//echo "<td>{$dokai[$i]['paskirtis']} </td>\n";

$pran = $dokai[$i]['banko_pranesimas'];
if ($pran == '1901' || $pran == '3101') echo "<td><span style='color: red;'>".$pran."</span></td>\n"; else echo "<td>$pran</td>\n";
echo "<td>{$dokai[$i]['mok_data']} </td>\n";
echo "<td>{$dokai[$i]['pras']} </td>\n";
echo "<td>{$dokai[$i]['moketojas']} </td>\n";
echo "<td>{$dokai[$i]['gavejas']} </td>\n";
echo "<td>{$dokai[$i]['saskaita']} </td></tr>\n";

}   //for
echo "</table>\n";  //baigias mokėjimų lentelė

echo $pages->display_pages();

?>

<table>
<br /><br />
<tr style="background: white;"><td>1101&nbsp;</td><td>Banko pranešimas apie  sėkmingą paslaugos apmokėjimo operacijos įvykdymą</td></tr>
<tr style="background: white;"><td>1201</td><td>Banko pranešimas apie sėkmingai priimtą, bet dar nepatvirtintą paslaugos apmokėjimo operaciją</td></tr>
<tr style="background: white;"><td style='color: red;'>1901</td><td>Banko pranešimas apie nutrauktą paslaugos apmokėjimą</td></tr>
<tr style="background: white;"><td style='color: red;'>3101</td><td>Užklausos parašas neteisingas</td></tr>
</table>

<?php
//-----------------------


mysqli_close($cxn);


//echo "</div>  <div id='footer'>\n";
//include("footer.inc");
?>

</div>
</body></html>
