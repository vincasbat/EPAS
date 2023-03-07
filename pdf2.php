<?php		
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');
session_start();  //?
 if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }
switch (@$_SESSION['grupe'])
{
case "pr":  //tik priemimas
case "admins":
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
break;
default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch
include("dbstuff.inc");
$dok_id = $_GET['dokid'];
$data = date("Y-m-d");
$out = "";

$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");




$query = "SELECT dok_kelias, naud_email, from_ip, pastabos, ip, dok_formos_kodas FROM dokai WHERE dok_id = $dok_id";
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);
$naud_email=$row['naud_email'];
$pastabos = $row['pastabos'];
$ip = $row['ip'];  

$out .= "<p>Prašymo Nr. <b>$dok_id</b>. &nbsp; &nbsp; &nbsp;  PNO Nr. $ip </p>\n";   


$dokoid =  $dok_id;
$query_naudotojai = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas, CONCAT(' ', naud_adr, '; tel. ', naud_telef, '; ', naud_org) AS par_duom FROM naudotojai WHERE naud_email = '$naud_email'";
$res = mysqli_query($cxn, $query_naudotojai) or die ("Error: ".mysqli_error($cxn));
$rw = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas
$naud = $rw['pareiskejas'];
$naud_duom = $rw['par_duom'];
$naud_email_ref = $naud;
$out .= "<p>$naud_email_ref  $naud_duom </p>\n";





unset($dokai);

$dokai = null;


$query = "SELECT dok_id, suma, paskirtis, banko_pranesimas, LEFT(mokejimo_data, 10) AS mok_data, moketojas FROM mokejimai WHERE naud_email='$naud_email' AND (dok_id = $dok_id) ORDER BY mok_id desc  LIMIT 0, 100 ";

$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$c = mysqli_num_rows($result);  


$title = "";

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

$did = $dokai[$i]['dok_id'];

//echo "Prašymo Nr. $did<br>\n";

$title = $dokai[$i]['moketojas'];

$suma = $dokai[$i]['suma'];

if($dokai[$i]['mok_data']<'2015.01.01') $suma .= '&nbsp;LT'; else $suma .= '&nbsp;EUR';




$mok_paskirtis = $dokai[$i]['paskirtis'];

//echo "Mokėjio paskirtis: $mok_paskirtis <br>\n";

$dt =  $dokai[$i]['mok_data'];
}   //for


//$out .= "Mokėtojas <b>$title</b> <br>Suma <b>$suma</b><br>Data <b>$dt</b> <br><br><br>\n";
$tb = "";
$tb= "<table border='0' cellpadding='2'  >\n";
$tb .= "<tr><td>Mokėtojas</td><td> $title </td><td></td><td></td><td></td></tr>";
$tb .= "<tr><td>Suma</td><td> $suma </td><td></td><td></td><td></td></tr>";
$tb .= "<tr><td>Data</td><td> $dt </td><td></td><td></td><td></td></tr></table><br><br><br>\n";

$out .= $tb; 

if((substr_count($pastabos, ':')>1) && !strpos($ip, 'CSV')) {

$pratesimai = explode(" ", $pastabos);
$dvitaskiai =  substr_count($pratesimai[0], ':');				
	

usort($pratesimai, "cmp");

$table = "";


$table.= "<table border='0' cellpadding='2' class='fixedwidth' >\n";
if($dvitaskiai == 2)
$table.= "<tr><th></th><th><b>Patento Nr.</b></th><th><b>Metai</b></th><th><b>Suma</b></th></tr>";
else
$table.= "<tr><th></th><th><b>Patento Nr.</b></th><th><b>Metai</b></th><th><b>Suma</b></th><th><b>Info</b></th></tr>";
for ($i=0; $i<count($pratesimai); $i++)
{
$eil = explode(":", $pratesimai[$i]);
if($dvitaskiai == 2) 			
$table.= "<tr><td>" . ($i+1) . "</td><td>". $eil[0] ."</td><td>". $eil[1] ."</td><td>". $eil[2] ."</td></tr>";
else
$table.= "<tr><td>" . ($i+1) . "</td><td>". $eil[0] ."</td><td>". $eil[1] ."</td><td>". $eil[2] ."</td><td>". $eil[3] ."</td></tr>";

}
$table.= "</table>\n"; 

}




if((substr_count($pastabos, ':')>1) && strpos($ip, 'CSV')) {
$pratesimai = json_decode($pastabos);
usort($pratesimai, "cmp2");
$table = "";
$table.= "<table border='0' cellpadding='2' class='fixedwidth' >\n";
$table .= "<tr><th></th><th><b>Patento Nr.</b></th><th><b>Metai</b></th><th><b>Suma</b></th><th><b>Info</b></th></tr>";
for ($i=0; $i<count($pratesimai); $i++)
{

$table.= "<tr><td>" . ($i+1) . "</td><td>". $pratesimai[$i]->patnr ."</td><td>".  $pratesimai[$i]->metai ."</td><td>".  number_format($pratesimai[$i]->suma, 2, '.', '') ."</td><td>".  $pratesimai[$i]->info ."</td></tr>";

}
$table.= "</table>\n"; 

}//if 





function cmp($a, $b)
{
$am = explode(":", $a);
$bm = explode(":", $b);

    return $am[1]>$bm[1];
}

function cmp2($a, $b)
{
    return $a->metai > $b->metai;
}



mysqli_close($cxn);


$pdfout = "<p></p><p></p><p></p><p>".$out.$table."<p></p><p></p>";


 
//----------------------  PDF generavimas -------------------------
class MYPDF extends TCPDF {
public function Header() {
        // Logo
        $image_file = './imgs/e.jpg';
//$this->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        $this->Image($image_file, 26, 15, 5, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font $image_file, 25, 10, 15,
        $this->SetFont('dejavusans', '', 11);
$this->setCellPaddings(2, 2, 2, 2);
$this->SetFillColor(65, 85, 177);
$txt = 'Elektroninių paslaugų sistema EPAS';
$this->MultiCell(0, 0, $txt, 0, 'L', 0, 1,  '35', '', true, 0, false, true, 0, 'S', false);
        // Title
$this->setCellMargins(2, 2, 2, 2);
       $this->Cell(0, 0, '', 'T', 1, 'L', 0, '', 0, false, 'T', 'M');
      
    }


    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('dejavusans', '', 8);
        // Page number
$tekstas = 'Kalvarijų g. 3, LT-09310, Vilnius, kodas 188708943, tel. (8 5) 278 02 90, faks. (8 5) 275 0723, el. paštas info@vpb.gov.lt';

        $this->Cell(0, 10, $tekstas, 'T', false, 'C', 0, '', 0, false, 'T', 'M');



    }
}//class




$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Valstybinis patentų biuras');
$pdf->SetTitle('Pažyma apie pateiktą prašymą');
$pdf->SetSubject('VPB e.paslaugos');
$pdf->SetKeywords('VPB, e.paslaugos');


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(25, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT); 
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$pdf->setLanguageArray($l);

// ---------------------------------------------------------


$pdf->setFontSubsetting(true);


$pdf->SetFont('dejavusans', '', 10, '', true);


$pdf->AddPage();



$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $pdfout, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

$txt = "Dokumentas sukurtas $data";
$pdf->Write($h=0, $txt, $link='', $fill=0, $align='R', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);


$pdf->Output($dok_id.'.pdf', 'I');



//  ----------------------- pabaiga -------------------------------
?>
