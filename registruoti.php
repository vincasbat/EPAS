<?php
require_once  './dbstuff.inc';

function registruoti($filename) {


$fv = explode ('_', $filename); 
$prasymas = $fv[0];
$mas = explode ('.', $fv[4]);
$skyrius=$mas[0];
$extention=$mas[1];   //pdf, adoc,....
$pno = $fv[1];
$data = $fv[2];
$doktipas = $fv[3];  //PAZ, KIT, LIUD, ISR



//Rasyti i DB:


//Suzinome prasytojo el. pasta:
 $cxn = mysqli_connect($host,$user,$passwd,$dbname)
 or die("Nepavyko prisijungti prie duomenų bazės");
$sql = "SELECT naud_vardas, naud_pavarde, naudotojai.naud_email FROM naudotojai, dokai WHERE dok_id = '$prasymas' and naudotojai.naud_email = dokai.naud_email";
$result = mysqli_query($cxn, $sql);
if (mysqli_num_rows($result) > 0) {
     while($row = mysqli_fetch_assoc($result)) {
$email = $row["naud_email"];
 }
} else {
return false;
}
 
$reg_nr = getRegnr ($skyrius);

 $sql = "INSERT INTO `siunc_registras`(`reg_nr`, `data`, `adresatas`, `dokumentas`, `kelias`, `dok_id`, `pno`, `isdave`) VALUES ('$reg_nr','$data','$email','$doktipas','$filename','$prasymas','$pno', '$naud_el_pastas')";
//echo $sql;

mysqli_query($cxn,$sql) or die ("Error: ".mysqli_error($cxn));
mysqli_close($cxn);

}//end function

function getRegnr ($skyrius){
   $cxn = mysqli_connect($host,$user,$passwd,$dbname)
 or die("Nepavyko prisijungti prie duomenų bazės");  
  $sql = "SELECT MAX(reg_ai) AS maks FROM `siunc_registras`";
$result = mysqli_query($cxn, $sql);
$max = 0;
if (mysqli_num_rows($result) > 0) {
     while($row = mysqli_fetch_assoc($result)) { $max = $row["maks"];    }
} else {
return false;
}

mysqli_close($cxn);

$max++;
if ($max < 100000) $maxz = "".$max;
if ($max < 10000) $maxz = "0".$max;
if ($max < 1000) $maxz = "00".$max;
if ($max < 100) $maxz = "000".$max;
if ($max < 10) $maxz = "0000".$max;

return "EPAS-".$skyrius."-".$maxz;
 
}//end function 2




?>
