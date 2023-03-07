<!DOCTYPE html>
<html>
<head><title>Mok&#279;jimas</title>

<meta http-equiv="Content-Type" content="text/html; charset=Windows-1257" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<!--
<meta http-equiv="Content-Type" content="text/html; charset=Windows-1257" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
-->


<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<style>
#nav a  {
display: block;
}
</style>


</head>
<body>
<div id="container">
   <div id="header">
<?php
//include("header.inc");

echo "<table  width='100%'    border='0' ><tr><td width='180' style='text-align: center; padding: 0.5em; background:white;'> <img  alt='Valstybinis patent&#371; biuras' src='imgs/herbas_nukirptas.jpg' /> 
</td>

<td  style='background: url(imgs/hed.jpg) no-repeat; color:  brown; background-color: white; vertical-align:  middle; padding: 0.5em;'> 
<p style=' font-size: 150%; font-weight: bold;'>Elektronin&#279;s paslaugos (EPAS)</p>


<p>
Kalvarij&#371; g. 3, LT-09310 Vilnius <br />
Kodas 188708943<br />
Informacija tel. (8 5) 278 02 90, faksas (8 5) 275 0723<br />
El. pa&#353;tas info@vpb.gov.lt
</p>

</td></tr></table>\n";

//url(imgs/banner_right.gif)



echo "</div><div id='nav'>\n";


echo "<a href='index.php'>&#302; prad&#382;i&#261;</a>\n";
echo "<a href='upload.php'>Pateikti pra&#353;ym&#261;</a>\n";
echo "<a href='mano_prasymai.php'>Mano pra&#353;ymai</a>\n";
//echo "<a href='salygos.php'>Taisykl&#279;s</a>";
 
echo  "<a href='moketi.php'>Mok&#279;ti</a>\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
 $mess_pareiskejas = iconv("UTF-8", "Windows-1257", $mess_pareiskejas);
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}
echo "</div><div id='content'>\n";   //išdėstymas



echo "<h4 style='color: grey;'>Mok&#279;jimo nurodymas</h4>\n";
//$parw = iconv("UTF-8", "Windows-1257", $par);
$date = new DateTime();
$result = $date->format('Y.m.d');
?>
 
<table>
<tr><td class="heads">Gav&#279;jas</td><td><?php echo $vk_name_w; ?></td></tr>
<tr><td class="heads">S&#261;skaita</td><td> LT24 7300 0101 1239 4300,  Swedbank, AB</td></tr>
<tr><td class="heads">&#302;mokos kodas</td><td> 5310</td></tr>
<tr><td class="heads">Suma, <?php if($result<'2015.01.01') echo '&nbsp;LTL'; else echo 'EUR'; ?></td><td> <?php echo $vk_amount; ?></td></tr>
<tr><td class="heads">Mok&#279;jimo paskirtis</td><td> <?php echo $vk_msg_w; ?></td></tr>
<tr><td class="heads">Pra&#353;ymo Nr.</td><td> <?php echo $dok_id; ?></td></tr>
</table>

<?php
  


if(@is_array($errors)) 
 {
 $mess = ""; 
 foreach($errors as $value)
 {

 $mess .= iconv("UTF-8", "Windows-1257", $value)."<br />";
 }
echo "<p class='errors'>$mess</p>\n";
echo "<p>Spauskite <b>Atgal</b> ir  &#303;ra&#353;ykite tr&#363;kstamus duomenis.</p>\n";
}
else
echo "<p> Paspaudus mygtuk&#261; <i>Mok&#279;ti</i> mok&#279;jimo nurodymas bus perduotas Elektronini&#371; vald&#382;ios vart&#371; mok&#279;jimo sistemai.</p><p> Baig&#281; mok&#279;jim&#261; pasirinkto banko elektronin&#279;s bankininkyst&#279;s sistemoje <b>b&#363;tinai</b> paspauskite mygtuk&#261; <span style='color:red;font-weight:bold;'>  <i>Gr&#303;&#382;ti pas pardav&#279;j&#261;.</i></span></p>\n";

?>






<fieldset style="border:  0px;">

<form action="moketi.php" method="POST" style="display: inline;">
<input type="hidden" name="VK_AMOUNTa" value="<?php echo $vk_amount; ?>" /> 
<input type="hidden" name="VK_MSGa" value="<?php echo $vk_msg; ?>"  /> 
<input type="hidden" name="DOK_IDa" value="<?php echo $dok_id; ?>"  /> 
<input type="submit" name="Atgal"  value="Atgal"  />
</form>




<form action="https://www.epaslaugos.lt/PaymentBroker/egovportal/payment.jsp" method="POST" style="display: inline;"> 


<input type="hidden" name="VK_AMOUNT" value="<?php echo $vk_amount_w; ?>" /> 

<input type="hidden" name="VK_MSG" value="<?php echo $vk_msg_w; ?>"  /> 
 
<input type="hidden" name="VK_MAC" value="<?php echo $vk_mac; ?>" />

<input type="hidden" name="PID" value="<?php echo $pid_w; ?>" />

<input type="hidden" name="VK_SND_ID" value="<?php echo $vk_snd_id_w; ?>" />

<input type="hidden" name="VK_RETURN" value="<?php echo $vk_return_w; ?>" />

<input type="hidden" name="VK_NAME" value="<?php echo $vk_name_w; ?>" />

<input type="hidden" name="VK_PCODE" value="<?php echo $vk_pcode_w; ?>" />

<input type="hidden" name="VK_REQ_DATE" value="<?php echo $vk_req_date_w; ?>" />

<input type="hidden" name="VK_VERSION" value="<?php echo $vk_version_w; ?>" />

<input type="hidden" name="VK_EXTRA" value="<?php echo $vk_extra_w; ?>" />

<input type="hidden" name="VK_PAYER_CODE" value="<?php echo $vk_payer_code_w; ?>" />

 <input type="submit" name="Pay"  value="Mok&#279;ti" <?php if(@is_array($errors)) echo "disabled"; ?> />


   </form>

</fieldset>
<?php



echo "</div>  <div id='footer'>\n";
//include("footer.inc");


$yr = date("Y");
echo "<p> &copy; $yr Valstybinis patent&#371; biuras</p>\n"; 


?>


</div>
</div>
</body></html>
 


