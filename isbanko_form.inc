<!DOCTYPE html>
<html>
<head><title>Banko atsakymas</title>
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
//echo "<a href='salygos.php'>Taisyklės</a><br />";
 //      echo  "<a href='naud_instr.php'>Instrukcija</a><br />\n";
echo  "<a href='moketi.php'>Mokėti</a><br />\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div><div id='content'>\n";   //išdėstymas

function len3 ($str)
{
$sl = strlen($str);
if($sl==0) return "";
 if($sl > 0 && $sl < 10) $ln = "00".$sl;
if($sl > 9 && $sl < 100) $ln = "0".$sl;
if($sl > 99 && $sl < 1000) $ln = "$sl";
return $ln;
}





$vk_service = $_POST['VK_SERVICE'];
$pid = $_POST['PID'];
$vk_snd_id = $_POST['VK_SND_ID'];
$vk_amount = $_POST['VK_AMOUNT'];
$vk_curr = $_POST['VK_CURR'];
$vk_rec_acc = $_POST['VK_REC_ACC'];
$vk_rec_name = $_POST['VK_REC_NAME'];
$vk_snd_name = $_POST['VK_SND_NAME'];
$vk_msg = $_POST['VK_MSG'];
$vk_t_date = $_POST['VK_T_DATE'];
$vk_extra = $_POST['VK_EXTRA'];
$signature = $_POST['VK_MAC'];






$vk_msg_u = iconv("Windows-1257", "UTF-8", $vk_msg);
$vk_snd_name_u = iconv("Windows-1257", "UTF-8", $vk_snd_name);
$vk_rec_name_u = iconv("Windows-1257", "UTF-8", $vk_rec_name);



function testas() {
echo "<p style='color: white;'>pid: ".$_POST['PID'], "<br />";
echo "vk_snd_id: ".$_POST['VK_SND_ID'], "<br />";   //Pinigų gavėjo banko ID numeris
echo "vk_curr: ".$_POST['VK_CURR'], "<br />";
echo "vk_amount: ".$_POST['VK_AMOUNT'], "<br />";  
echo "vk_rec_acc: ".$_POST['VK_REC_ACC'], "<br />";  
echo "vk_rec_name: ".$_POST['VK_REC_NAME'], "<br />";
echo "vk_snd_name: ".$_POST['VK_SND_NAME'], "<br />";
echo "vk_service: ".$_POST['VK_SERVICE'], "<br />";
echo "vk_t_date: ".$_POST['VK_T_DATE'], "<br />";
echo "vk_extra: ".$_POST['VK_EXTRA'], "<br />";
echo "vk_msg: ".$_POST['VK_MSG'], "<br />";
echo "vk_mac: ".$_POST['VK_MAC'], "<br />";
echo "vk_mac2: ".$_POST['VK_MAC2'], "<br />";
echo "vk_body: ".$_POST['VK_BODY'], "</p>";
} 
// Tikriname parašą: _____________


$data = len3($vk_service).$vk_service.
	len3($pid).$pid.
	len3($vk_snd_id).$vk_snd_id.
	len3($vk_amount).$vk_amount.
	len3($vk_curr).$vk_curr.
	len3($vk_rec_acc).$vk_rec_acc.
	len3($vk_rec_name).$vk_rec_name.
	len3($vk_snd_name).$vk_snd_name.
	len3($vk_msg).$vk_msg.
	len3($vk_t_date).$vk_t_date.
	len3($vk_extra).$vk_extra;







  
$fp = fopen("./crt/mok_sert.crt", "r");   //  --------------------live  
//$fp = fopen("./crt_mano/rsa_new.crt", "r");  // ------test
$cert = fread($fp, 8192);
fclose($fp);
$pubkeyid = openssl_get_publickey($cert);
$ok = openssl_verify($data, base64_decode($_POST['VK_MAC2']), $pubkeyid);   // VK_MAC2  ??

openssl_free_key($pubkeyid);




$upl_dok_id = "00";  //negalimas prasymo nr.   
if (isset($_SESSION['upl_dok_id']))
{ $upl_dok_id = $_SESSION['upl_dok_id'];
unset($_SESSION['upl_dok_id']);
}

/*
 include("dbstuff.inc");
$cxn = mysqli_connect($host,$user,$passwd,$dbname)
 or die("Nepavyko prisijungti prie duomenų bazės");
 $sql = "SELECT naud_ak FROM naudotojai WHERE naud_email='$naud_el_pastas'";
$result = mysqli_query($cxn, $sql) or die ("Error: ".mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);
$ak = $row['naud_ak'];


if (!$ak==$vk_extra)        // __ak______ak  if ($upl_dok_id==$vk_extra) 
{
echo "<h4 style='color: red;'>Mokėjimo operacijos klaida</h4>\n";
echo "</div>  <div id='footer'>\n";
include("footer.inc");
echo "</div></div></body></html>\n";
exit();
}

*/
if ($ok) $titlas = "P_T"; else $titlas = "P_N"; 
 

switch($vk_service)   //tb po parašo tikrinimo
{
case "1101":
echo "<h4 style='color: green;' title = '$titlas'>Mokėjimo operacija sėkmingai įvykdyta</h4>\n";
break;

case "1201":
echo "<h4 style='color: green;' title = '$titlas'>Mokėjimo operacija priimta, bet dar nepatvirtinta</h4>\n";
break;

case "1901":
echo "<h4 style='color: red;' title = '$titlas'>Mokėjimo operacija nutrūko</h4>\n";

echo "</div>  <div id='footer'>\n";
include("footer.inc");
echo "</div></div></body></html>\n";
exit();
break;

case "3101":
echo "<h4 style='color: red;' title = '$titlas'>Mokėjimo operacijos parašas neteisingas</h4>\n";

echo "</div>  <div id='footer'>\n";
include("footer.inc");
echo "</div></div></body></html>\n";
exit();
break;

default:
echo "<h4 style='color: red;' title = '$titlas'>Mokėjimo operacija nepavyko</h4>\n";

echo "</div>  <div id='footer'>\n";
include("footer.inc");
echo "</div></div></body></html>\n";
exit();

}

if (!$ok)  { echo "<h4 style='color: red;'>VAIISIS parašas neteisingas</h4>\n"; // test???live
echo "</div>  <div id='footer'>\n";
include("footer.inc");
echo "</div></div></body></html>\n";
exit();  }  




echo "<table><tr><td class='heads'>Gavėjas</td><td>$vk_rec_name_u</td></tr>\n";
echo "<tr><td class='heads'>Sąskaita</td><td>$vk_rec_acc</td></tr>\n";
echo "<tr><td class='heads'>Mokėtojas</td><td>$vk_snd_name_u</td></tr>\n";
echo "<tr><td class='heads'>Suma, LTL</td><td> $vk_amount</td></tr>\n";
echo "<tr><td class='heads'>Mokėjimo paskirtis</td><td> $vk_msg_u</td></tr>\n";
echo "<tr><td class='heads'>Prašymo Nr.</td><td>$upl_dok_id</td></tr>\n"; // _ak____ $vk_extra
echo "<tr><td class='heads'>Banko pranešimas</td><td>\n"; 

 if ($vk_service == '1901' || $vk_service == '3101') echo "<span style='color: red;'>".$vk_service."</span>"; else echo $vk_service; 

echo "</td></tr><tr><td class='heads'>Mokėjimo data</td><td>$vk_t_date</td></tr></table>\n";

echo "<p>Per Elektroninius valdžios vartus sumokėtų mokesčių sąrašą galima rasti skyrelyje <a href='mano_prasymai.php'>Mano prašymai.</a></p>\n";



// Į mokesčių lentelę įrašyti iš banko gautus duomenis apie mokesčio sumokėjimą.
include("dbstuff.inc");
 $cxn = mysqli_connect($host,$user,$passwd,$dbname)
 or die("Nepavyko prisijungti prie duomenų bazės");


 $sql = "INSERT INTO mokejimai (dok_id, suma, paskirtis, moketojas, naud_email, gavejas, banko_pranesimas, mokejimo_data, saskaita) VALUES
('$upl_dok_id','$vk_amount', '$vk_msg_u', '$vk_snd_name_u', '$naud_el_pastas','$vk_rec_name_u','$vk_service','$vk_t_date', '$vk_rec_acc')";
 mysqli_query($cxn,$sql) or die ("Error: ".mysqli_error($cxn));
mysqli_close($cxn);



?>


<table>
<tr style="background: white; color: grey; font-weight: bold"><td>Banko <br /> pranešimas</td><td>Aprašymas</td></tr>
<tr style="background: white;"><td>1101</td><td>Banko pranešimas apie  sėkmingą paslaugos apmokėjimo operacijos įvykdymą</td></tr>
<tr style="background: white;"><td>1201</td><td>Banko pranešimas apie sėkmingai priimtą, bet dar nepatvirtintą paslaugos apmokėjimo operaciją</td></tr>
<tr style="background: white;"><td style='color: red;'>1901</td><td>Banko pranešimas apie nutrauktą paslaugos apmokėjimą</td></tr>
<tr style="background: white;"><td style='color: red;'>3101</td><td>Užklausos parašas neteisingas</td></tr>
</table><br />


<?php
echo "</div>  <div id='footer'>\n";
include("footer.inc");
?>


</div>
</div>
</body></html>
 


