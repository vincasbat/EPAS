<?php   //neturi buti tarpo pries <?php
 

session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
case "par":   //gali matyti tik pareiskejai ir adminai
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



if(isset($_POST['viso']))
  {
$_SESSION['visosuma'] = $_POST['viso'];   
  }
if(isset($_POST['pastabos']))
  {
$_SESSION['pastabos'] = $_POST['pastabos'];    
  }


 if(!isset($_POST['Upload']))
  {
	include("form_upload2.inc");
 exit();
  }





$ip = $_POST['ip'];
$ip = filter_var($ip, FILTER_SANITIZE_STRING); 


$ip = trim($ip);


include("dbstuff.inc");
$cxn = mysqli_connect($host,$user,$passwd,$dbname)
			   or die("Klaida! Nepavyko prisijungti prie duomenų bazės");


//***************************	
$dok_id = "";

	$pastabos = $_POST['pastabos'];
			$pastabos = filter_var($pastabos, FILTER_SANITIZE_STRING);       
			$pastabos = mysqli_real_escape_string($cxn, $pastabos);
			$from_ip = $_SERVER["REMOTE_ADDR"];
			$query = "INSERT INTO dokai (dok_kelias,pastabos, status_dabar, status_dabar_date, naud_email, dok_formos_kodas, from_ip, ip) VALUES ('-','$pastabos', 'Gautas', NOW(), '$naud_el_pastas', 'Nenurodyta', '$from_ip', '$ip')";    
			$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
			$dok_id = mysqli_insert_id($cxn);

			$query = "INSERT INTO dok_statusai (dok_id,statusID,status_date, naud_email) VALUES ($dok_id,'Gautas', NOW(), '$naud_el_pastas')";  
			$result = mysqli_query($cxn,$query) or die ("Error: ".mysqli_error($cxn));
            $mess_ok = "";
if($result) {

    $mess_ok =  "Prašymas sėkmingai priimtas. Prašymo numeris $dok_id.<br />";
			



	$v_pav = explode(" ", $par);
	$pare = "";
	foreach ($v_pav as $value) {
	    $pare .= " ".sauksm($value);
	}
	$pare = trim($pare);


		$emess = "Gerb. $pare,\r\n\r\n";
		 $emess .= "Jūsų prašymas dėl pramoninės nuosavybės objekto, kurio paraiškos, registracijos ar patento Nr. $ip, priimtas Valstybinio patentų biuro (VPB) elektroninių paslaugų sistemoje EPAS. Prašymo Nr. $dok_id. \n";
		 $emess .= "Prašymo vykdymo eigą galite sekti prisijungę prie VPB e.paslaugų sistemos EPAS skyrelyje „Mano prašymai“. ";
		 $emess .= "Jei turite klausimų,";
		 $emess .= " rašykite adresu vida.mikutiene@vpb.gov.lt arba skambinkite telefonu (8 5) 2780286.\r\n\r\n";     //?????
		$emess .= "Pagarbiai\r\n\r\nValstybinio patentų biuro elektroninių paslaugų sistema EPAS\r\n\r\n";
		$emess .= "Šis laiškas sukurtas automatiškai todėl į jį neatsakykite.";
		 $subj = "Gautas dokumentas";

		$extra_header_str  = 'MIME-Version: 1.0' . "\r\n";
		$extra_header_str .= 'From: www@epaslaugos.vpb.lt' . "\r\n"; 
		$extra_header_str .= 'Content-type: text/plain; '  .  '  charset=UTF-8' . "\r\n";
		$extra_header_str .= 'CC: vida.mikutiene@vpb.gov.lt' . "\r\n";
		$extra_header_str .= 'BCC: vincas.batulevicius@vpb.gov.lt' . "\r\n";
		 $mailsend=mail("$naud_el_pastas","$subj","$emess", $extra_header_str);

	

		}  // if $result
			else 
			{
			$mess .= "Įrašyti į duomenų bazę nepavyko!<br />";
			include("form_upload2.inc");
			       exit();
			}






mysqli_close($cxn);






function sauksm ($str)
{
$sauks = $str;
if (preg_match("/as$/", $str))  $sauks = preg_replace("/as$/", "ai", $str);
if (preg_match("/AS$/", $str))  $sauks = preg_replace("/AS$/", "AI", $str);

if (preg_match("/is$/", $str))  $sauks = preg_replace("/is$/", "i", $str);
if (preg_match("/IS$/", $str))  $sauks = preg_replace("/IS$/", "I", $str);

if (preg_match("/ys$/", $str))  $sauks = preg_replace("/ys$/", "y", $str);
if (preg_match("/YS$/", $str))  $sauks = preg_replace("/YS$/", "Y", $str);

if (preg_match("/us$/", $str))  $sauks = preg_replace("/us$/", "au", $str);
if (preg_match("/US$/", $str))  $sauks = preg_replace("/US$/", "AU", $str);

if (preg_match("/ė$/", $str))  $sauks = preg_replace("/ė$/", "e", $str);
if (preg_match("/Ė$/", $str))  $sauks = preg_replace("/Ė$/", "E", $str);

return $sauks;
}



$_SESSION['upl_dok_id'] = $dok_id;


include("form_upload2.inc");

?>
