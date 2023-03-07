<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
case "pz":   //gali matyti tik prekiu zenklu skyrius ir adminai
case "admins":
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
<head><title>Prekių ženklų ir dizaino skyrius</title>
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
include("stuff.inc");

$table_heads = array("dok_id" => "Nr.",
 	"ip" => "PNO Nr.",
                     "dok_formos_kodas" => "Formos kodas",
                     "mokestis" => "Suma, Lt",
                     "dok_kelias"        => "Rinkmena",
 			"naud_email"          => "Prašytojas",
			"status_dabar"          => "Statusas",
			"status_dabar_date"          => "Data",
                     );






//echo "<tr><td valign='top' style='background: white; width: 180px;'>\n";  

switch (@$_SESSION['grupe'])
{
case "admins":
echo "<a href='ataskaitos.php'>Ataskaitos</a><br />\n";
//                       ?????????
break;
case "pz":
echo "<a href='pz.php'>Prekių ženklų skyrius</a><br />\n";  //meniu
echo "<a href='ataskaitos.php'>Ataskaitos</a><br />\n";
echo "<a href='registras.php'>Registras </a><br />\n";
echo "<a href='regprasyma.php'>Registruoti prašymą </a><br />\n";
echo "<a href='isduoti.php'>Išduoti dokumentą</a><br />\n";
break;

default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch

//echo "<a href='elparasas.php'>ADOC</a><br /><br />\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}
echo "<br /><br /><br /><br /><br />\n";


include("dbstuff.inc");


echo "</div><div id='content'>\n";

$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");
// iš asoc masyvo eina blogai koduotos raidės─ gal reikės traukti vardus iš db
$query = "SELECT dok_id, dok_formos_kodas, mokestis, dok_kelias, naud_email,  status_dabar, DATE(status_dabar_date) AS dab_statuso_data, pastabos, ip FROM dokai WHERE status_dabar = 'PZ' ORDER BY dok_id desc ";     // where status_dabar = Gautas"
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));

$c = mysqli_num_rows($result);
echo "<h4 style='color: grey'>Prekių ženklų ir dizaino skyrius ($c)</h4>\n";


echo "<table border='0' cellpadding='2'  >\n";   //pagr. lentelė
//cellpadding='3' cellspacing='3' 
echo "<tr>\n";
foreach($table_heads as $heading)
{
   echo "<th>$heading</th>";
}
echo "</tr>\n"; //

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
 if ($i%2==0) echo "<tr>\n"; else echo "<tr class='odd'>\n";   
$dok_id = $dokai[$i]['dok_id'];
//echo "<td align=center><a href='details_pz.php?dok_id=$dok_id'>Pasirinkti</a></td></tr>\n";
echo "<td style='text-align: right; padding-right:3px;'>
     <a href='details.php?dok_id=$dok_id&st=pz'> {$dokai[$i]['dok_id']}</a></td>\n";
echo "<td>{$dokai[$i]['ip']}</td>\n";
echo "<td>{$dokai[$i]['dok_formos_kodas']}</td>\n";
echo "<td style='text-align: right;'>{$dokai[$i]['mokestis']}</td>\n";
$kelias = $dokai[$i]['dok_kelias'];
//$kelias = "uploaded_files/".$kelias;
$kelias = base64_encode($kelias);
$pastabos = $dokai[$i]['pastabos'];
echo "<td align=center><a href='view_file.php?id=$kelias' ><img src='./imgs/doc.gif' title='$pastabos'/></a></td>\n";
$naud_email = $dokai[$i]['naud_email'];
$query_naudotojai = "SELECT naud_email, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pareiskejas FROM naudotojai WHERE naud_email = '$naud_email'";
$res = mysqli_query($cxn, $query_naudotojai) or die ("Error: ".mysqli_error($cxn));
$row = mysqli_fetch_assoc($res);   //tikrinti ar vienas įrašas
$naud = $row['pareiskejas'];
echo "<td>$naud</td>\n";
echo "<td>{$dokai[$i]['status_dabar'] }</td>\n";	
echo "<td>{$dokai[$i]['dab_statuso_data']}</td>\n";
}   //for
echo "</tr></table><br />\n";  //baigias gautų lentelė




mysqli_close($cxn); 



echo "</div><div id='footer'>\n";
include("footer.inc");


echo "</div></div>";

if ($_GET['mailerr'])
{
echo "<script type='text/javascript'>alert('Nepavyko išsiųsti el. laiško');</script>";
}


echo "</body></html>";
 ?>
 
