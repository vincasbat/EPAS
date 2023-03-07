<?php
require_once('./phpword/vendor/autoload.php');
include("dbstuff.inc");
session_start();
 if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }


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


$dok_id=$_GET["dokid"]; 		


$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");


$query = "SELECT dok_kelias, naud_email, from_ip, pastabos, ip, dok_formos_kodas FROM dokai WHERE dok_id = $dok_id";
$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);
$naud_email=$row['naud_email'];
$pastabos = $row['pastabos'];
$ip = $row['ip'];  //pno nr.



//exit();

$query_naudotojai = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas, CONCAT(' ', naud_adr, '; tel. ', naud_telef, '; ', naud_org) AS par_duom FROM naudotojai WHERE naud_email = '$naud_email'";
$res = mysqli_query($cxn, $query_naudotojai) or die ("Error: ".mysqli_error($cxn));
$rw = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas
$naud = $rw['pareiskejas'];
$naud_duom = $rw['par_duom'];
$naud_email_ref = "<a href= 'mailto:$naud_email' > $naud </a>";


// Parodome mokėjimus per Elektroninės valdžios vartus:
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

$title = $dokai[$i]['moketojas'];

$suma = $dokai[$i]['suma'];

if($dokai[$i]['mok_data']<'2015.01.01') $suma .= ' LT'; else $suma .= ' EUR';

$mok_paskirtis = $dokai[$i]['paskirtis'];  $data = $dokai[$i]['mok_data'];


}   //for


mysqli_close($cxn);



//https://php-download.com/package/phpoffice/phpword/example

$phpWord = new \PhpOffice\PhpWord\PhpWord();


	$phpWord->setDefaultParagraphStyle(
    array(
        'align'      => 'left',
        'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(1),
        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(1),
        'spacing'    => 10,
        )
    );
    
    
	$section = $phpWord->addSection();
	$header = $section->addHeader();
	$textrunh = $header->addTextRun();
	$textrunh->addText('  ');
	$textrunh->addImage('./imgs/epas.png', array('wrappingStyle' => 'inline', 'width'=>40, 'height'=>20));
	$textrunh->addText('    Elektroninių paslaugų sistema');
	$section->addLine(['weight' => 1, 'width' => 480, 'height' => 0]);
	
	
	$fontStyle = new \PhpOffice\PhpWord\Style\Font();
	$fontStyle->setBold(false);
	$fontStyle->setName('Arial');
	$fontStyle->setSize(8);
	
	$footer = $section->addFooter();
	
 $textrunf = $footer->addTextRun();
 $textrunf->addText('Kalvarijų g. 3, LT-09310, Vilnius, kodas 188708943, tel. (8 5) 278 02 90, faks. (8 5) 275 0723, el. paštas info@vpb.gov.lt', $fontStyle);
 

	$table = $section->addTable();
	$table->addRow();  $table->addCell(1500)->addText("Prašymo Nr. "); $table->addCell(8000)->addText($dok_id);
	$table->addRow();  $table->addCell(1500)->addText("PNO Nr. "); $table->addCell(8000)->addText($ip, ['bold' => true]);
	$table->addRow();  $table->addCell(1500)->addText("Pareiškėjas"); $table->addCell(8000)->addText($naud);
	$table->addRow();  $table->addCell(1500)->addText("Mokėtojas"); $table->addCell(8000)->addText($title);
	$table->addRow();  $table->addCell(1500)->addText("Suma"); $table->addCell(8000)->addText($suma);
	$table->addRow();  $table->addCell(1500)->addText("Data");  $table->addCell(8000)->addText($data);

	
	 $section->addTextBreak(2);
	 
	 
$section = $phpWord->addSection(['breakType' => 'continuous', 'colsNum' => 2]);







if((substr_count($pastabos, ':')>1) && !strpos($ip, 'CSV')) {
	$pratesimai = explode(" ", $pastabos);
	$dvitaskiai =  substr_count($pratesimai[0], ':');				
	usort($pratesimai, "cmp");

	
	$table = $section->addTable();

	$table->addRow('', array('tblHeader' => true));  
	$table->addCell(800)->addText("Eil. Nr.", ['bold' => true]); $table->addCell(1300)->addText("Patento Nr.", ['bold' => true]); $table->addCell(900)->addText("Metai", ['bold' => true]); $table->addCell(900)->addText("Suma", ['bold' => true]); $table->addCell(800)->addText("Info", ['bold' => true]); 

	for ($i=0; $i<count($pratesimai); $i++)
	{
	 $eil = explode(":", $pratesimai[$i]);

	if (isset($eil[3])) $info = $eil[3]; else $info = "";
	$table->addRow();  
	$table->addCell(500)->addText($i+1); $table->addCell(1500)->addText($eil[0]); $table->addCell(900)->addText($eil[1]); $table->addCell(900)->addText($eil[2]); $table->addCell(900)->addText($info); 
	}//for

	}//if not csv


	if((substr_count($pastabos, ':')>1) && strpos($ip, 'CSV')) {

$pratesimai = json_decode($pastabos);
usort($pratesimai, "cmp2");



	$table = $section->addTable();

	$table->addRow('', array('tblHeader' => true));  
	$table->addCell(800)->addText("Eil. Nr.", ['bold' => true]); $table->addCell(1300)->addText("Patento Nr.", ['bold' => true]); $table->addCell(900)->addText("Metai", ['bold' => true]); $table->addCell(900)->addText("Suma", ['bold' => true]); $table->addCell(800)->addText("Info", ['bold' => true]); 


for ($i=0; $i<count($pratesimai); $i++)
{
$eil =  $pratesimai[$i];
if(isset($eil->info)) $info = $eil->info; else  $info = '';
$table->addRow();  
$table->addCell(800)->addText($i+1); $table->addCell(1300)->addText($eil->patnr); $table->addCell(900)->addText($eil->metai); $table->addCell(900)->addText(number_format($eil->suma, 2, '.', '')); $table->addCell(800)->addText($info); 
}//for
}//if csv





$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

$filename = $dok_id . ".docx";

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document'); 
	header('Content-Disposition: attachment;filename="'.$filename.'"'); 
	header('Cache-Control: max-age=0'); //no cache
	$objWriter->save('php://output');









?>
