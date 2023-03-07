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
case "par":  //tik pareiškėjai gali parsisiųsti 
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
$dok_id = $_POST['dok_id'];
$data = date("Y-m-d");

$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$query = "SELECT ip, status_date, pastabos, status_dabar, status_dabar_date FROM dokai, dok_statusai WHERE dokai.dok_id=dok_statusai.dok_id and dok_statusai.statusID ='Gautas' and dokai.dok_id = $dok_id ";
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);
$pno=$row['ip'];
$pastabos=$row['pastabos'];
$gauta=substr($row['status_date'], 0, 10); //->format('Y-m-d');
$status_dabar=$row['status_dabar'];
$status_dabar_date=substr($row['status_dabar_date'], 0, 10);
if($status_dabar=='OK')
$prasymas_ivykdytas = "Prašymas įvykdytas $status_dabar_date.";
if($status_dabar=='Atmestas')
$prasymas_ivykdytas = "Prašymas atmestas $status_dabar_date.";


$q = "SELECT COUNT(*) AS nr FROM kiti_failai WHERE dok_id = '$dok_id' ";
$rezultatas = mysqli_query($cxn, $q);
$eil = mysqli_fetch_array($rezultatas, MYSQLI_ASSOC);
$doc_count = $eil['nr']+1;

$q = "SELECT suma, paskirtis, mokejimo_data FROM mokejimai WHERE dok_id = '$dok_id' ";
$rezultat = mysqli_query($cxn, $q) or die ("Error: ".mysqli_error($cxn));
$rw = mysqli_fetch_assoc($rezultat);
$sumoketa = $rw['suma'];
 // else $sumoketa = "0 Lt";
$paskirtis = $rw['paskirtis'];
$mokejimo_data = substr ( $rw['mokejimo_data'], 0, 10);
if($sumoketa > 0) { if($mokejimo_data<'2015.01.01') $sumoketa .= " LTL"; else $sumoketa .= " EUR";}
$mokejimo_data = str_replace(".", "-", $mokejimo_data);


 
//----------------------  PDF generavimas -------------------------if($dokai[$i]['mok_data']<'2015.01.01') $suma .= '&nbsp;LT'; else $suma .= '&nbsp;EUR';

class MYPDF extends TCPDF {
public function Header() {
        // Logo
        $image_file = './imgs/herbas_nukirptas.jpg';
//$this->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        $this->Image($image_file, 25, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('dejavusans', '', 14);
$this->setCellPaddings(2, 2, 2, 2);
$this->SetFillColor(65, 85, 177);
$txt = 'Valstybinis patentų biuras'."\n".'Elektroninių paslaugų sistema EPAS';
$this->MultiCell(0, 0, $txt, 0, 'L', 0, 1,  '42', '', true, 0, false, true, 0, 'S', false);
        // Title
$this->setCellMargins(3, 3, 3, 3);//2
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
$pdf->SetMargins(25, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT); //PDF_MARGIN_LEFT
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 10, '', true);

$pdf->AddPage();





if((substr_count($pastabos, ':')>1) && strpos($pno, 'CSV')) {
$pratesimai = json_decode($pastabos);
$sarasas = "";
$metai = "";
foreach ($pratesimai as $prates) {

$sum =   number_format($prates->suma, 2, ".", "");
 $sarasas .= "$prates->patnr\t\t$prates->metai\t$sum  EUR<br>";
}
$pastabos = $sarasas;
}//if



$html = <<<EOD
<p></p>
<p></p>
<p></p>
<h3>Pažyma apie gautą prašymą</h3>
<p>Pažymime, kad per Valstybinio patentų biuro e.paslaugų sistemą EPAS gautas šis prašymas:</p>
<table   cellpadding="4">
<tr bgcolor="#E6E6E6"><td align="right">Prašymo Nr.</td><td><b>$dok_id</b></td></tr>
<tr><td align="right">&nbsp;&nbsp;&nbsp;Pramoninės nuosavybės objekto Nr.</td><td><b>$pno</b></td></tr>
<tr bgcolor="#E6E6E6"><td align="right">Pareiškėjas (-a)</td><td><b>$par</b></td></tr>
<tr><td align="right">Pareiškėjo el. pašto adresas</td><td><b>$naud_el_pastas</b></td></tr>
<tr bgcolor="#E6E6E6"><td align="right">Pateikta dokumentų</td><td><b>$doc_count</b></td></tr>
<tr><td align="right">Prašymas gautas</td><td><b>$gauta</b></td></tr>
<tr bgcolor="#E6E6E6"><td align="right">Sumokėta per Elektroninius valdžios vartus</td><td><b>$sumoketa</b></td></tr>
<tr><td align="right">Mokėjimo data</td><td><b>$mokejimo_data</b></td></tr>
<tr bgcolor="#E6E6E6"><td align="right">Mokėjimo paskirtis</td><td><b>$paskirtis</b></td></tr>
<tr><td align="right">Pastabos</td><td><b>$pastabos</b></td></tr>
<tr bgcolor="#E6E6E6"><td align="right">Pažyma sukurta</td><td><b>$data</b></td></tr>
</table>
<p>$prasymas_ivykdytas</p>
<p><img src="./imgs/e.jpg">&nbsp;&nbsp;&nbsp;Valstybinio patentų biuro e.paslaugų sistema EPAS</p>
EOD;


$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);




$pdf->Output('VPBpazyma_'.$dok_id.'.pdf', 'I');



//  ----------------------- pabaiga -------------------------------
?>
