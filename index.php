<?php

session_start();
if (@$_SESSION["auth"] == "yes") {
    $naud_el_pastas = $_SESSION["naud_email"];
    $grupe = $_SESSION["grupe"];
    $par = $_SESSION["pareiskejas"];
    $mess_pareiskejas = "<span style='color:green;'> $par </span>";
}

if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "pz":
        header("Location: pz.php");
        break;
    case "is":
        header("Location: is.php");
        break;

    case "pr":
        header("Location: gauti.php");
        break;

    case "ap":
        header("Location: apel.php");
        break;

    default:
    case "admins":
        break;
}

//switch
?>






<!DOCTYPE html>
<html>
<head><title>Valstybinio patentų biuro elektroninės paslaugos</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="stylesheet" type="text/css" href="./style/pag.css">
<link rel="shortcut icon" href="favicon.ico">

<style>

table.blueTable {
  border: 0px solid #1C6EA4;
  background-color: #D0E4F5;
  width: 100%;
  text-align: left;
  border-collapse: collapse;
}
table.blueTable td, table.blueTable th {
  border: 0px solid #FFFFFF;
  padding: 3px 2px;
}
table.blueTable tbody td {
  font-size: 13px;
}
table.blueTable tr:nth-child(even) {
  background: #E6F1F5;
}


table.blueTable tbody tr {
   background: #D0E4F5;
}



table.blueTable thead {
  background: #1C6EA4;
  background: -moz-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  background: -webkit-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  background: linear-gradient(to bottom, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  border-bottom: 2px solid white;
}
table.blueTable thead th {
  font-size: 15px;
  font-weight: bold;
  color: #FFFFFF;
  
}
table.blueTable thead th:first-child {
  border-left: none;
}





#nav a  {
display: block;
}
</style>

<script type="text/javascript">
<!--
	function openWin( windowURL, windowName, windowFeatures ) { 
		return window.open( windowURL, windowName, windowFeatures ) ; 
	} 
 -->
</script>


</head>
<body>
<div id="container">
   <div id="header">

<?php include "header.inc"; ?>

</div>

<div id="nav">


<?php if (isset($grupe)) {
    if ($grupe == "par") {
        echo "<a href='index.php'>Į pradžią</a>\n";
    }
    echo "<a href='upload.php'>Pateikti prašymą</a>";
    echo "<a href='mano_prasymai.php'>Mano prašymai</a>\n";

    echo "<a href='moketi.php'>Mokėti</a>\n";
} else {
    echo "<a href='login.php'>Prisijungti</a><br />\n";
} ?>


<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>
<?php
if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas\n";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}

if (isset($grupe)) {
    if ($grupe == "par") {
        echo "<a href='keisti_duomenis.php'>Keisti duomenis</a><br />\n";
    }
}
?>

<br /><br />

</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>Valstybinio patentų biuro elektroninės paslaugos</h4>

<p>Prieš pradėdami naudotis Valstybinio patentų biuro paslaugomis susipažinkite su naudojimosi Valstybinio patentų biuro elektroninėmis paslaugomis <a href='http://www3.lrs.lt/pls/inter3/dokpaieska.showdoc_l?p_id=483462&p_tr2=2' target="_blank" title="Atsidarys naujame lange/kortelėje"> taisyklėmis.</a></p>

<p>Prašymų, kuriuos galima teikti elektroniniu būdu, sąrašas pateiktas   <a href='http://www.vpb.gov.lt/index.php?n=540&l=lt' target="_blank" title="Atsidarys naujame lange/kortelėje"> Valstybinio patentų biuro tinklalapyje.</a></p>

<p>Pastabas, atsiliepimus, skundus ar pranešimus ir pasiūlymus dėl Valstybinio patentų biuro  paslaugų  galite pateikti    <a href='./atsiliepimai.php'  > čia.</a></p>
<br />

<?php
$table_heads = [
    "reg_nr" => "Reg. Nr.",
    "data" => "Data",
    "adresatas" => "Adresatas",
    "dokumentas" => "Dok. tipas",
    "kelias" => "Failas",
    "dok_id" => "Praš. Nr.",
    "pno" => "PNO Nr.",
    "isdave" => "Išdavė",
];
include "dbstuff.inc";
include "paginator.class.php";

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");
$qr = "SELECT grupe FROM naudotojai WHERE naud_email = '$naud_el_pastas'";
($res = mysqli_query($cxn, $qr)) or die("Error: " . mysqli_error($cxn));
$gr = "";
while ($rw = mysqli_fetch_assoc($res)) {
    $gr = $rw["grupe"];
}

$nauds = "";
$where = "";
$qre = "SELECT naud_email, naud_vardas, naud_pavarde FROM naudotojai WHERE grupe = '$gr'";
($resu = mysqli_query($cxn, $qre)) or die("Error: " . mysqli_error($cxn));
while ($rwe = mysqli_fetch_assoc($resu)) {
    $ne = $rwe["naud_email"];
    $where .= "adresatas = '$ne' or ";
    $nauds .= $rwe["naud_vardas"] . " " . $rwe["naud_pavarde"] . ", ";
}

$where = rtrim($where, "or ");
$nauds = rtrim($nauds, ", ");
//echo $where;
//echo "<br>", $naud_emails;

if (strlen($gr) < 2) {
    $query = "SELECT reg_nr FROM siunc_registras WHERE adresatas = '$naud_el_pastas'";
} else {
    $query = "SELECT reg_nr FROM siunc_registras WHERE $where ";
}
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result);

if ($c < 1) {
    echo "</div><div id='footer'>\n";
    include "footer.inc";
    echo "</div></body></html>\n";
    exit();
}

$pages = new Paginator();
$pages->items_total = $c;
$pages->mid_range = 9;
$pages->paginate();

echo "<p style='color: grey'><b>Išduoti elektroniniai dokumentai ($c)</b></p>\n";

if (strlen($gr) > 2) {
    echo "<p> Grupė $gr: $nauds. </p>\n";
}

echo $pages->display_pages();
echo "<br /><br />\n";

echo "<table class='blueTable'><thead><tr>\n"; //pagr. lentelė

foreach ($table_heads as $heading) {
    echo "<th>$heading</th>";
}
echo "</tr></thead>\n"; //

if (strlen($gr) < 2) {
    $query =
        "SELECT reg_nr, data, adresatas, dokumentas, kelias, dok_id, pno, isdave FROM siunc_registras  WHERE adresatas = '$naud_el_pastas' order by reg_ai desc" .
        $pages->limit;
} else {
    $query =
        "SELECT reg_nr, data, adresatas, dokumentas, kelias, dok_id, pno, isdave FROM siunc_registras  WHERE $where order by reg_ai desc" .
        $pages->limit;
}

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result);

$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}
$n_dokai = sizeof($dokai);
for ($i = 1; $i <= $n_dokai; $i++) {
    echo "<tr>\n";
    $dok_id = $dokai[$i]["dok_id"];
    echo "<td>   {$dokai[$i]["reg_nr"]}  </td>\n";
    echo "<td>{$dokai[$i]["data"]}</td>\n";

    $email = $dokai[$i]["adresatas"];

    $query_vykdytojas = "SELECT  CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS prasytojas FROM naudotojai WHERE naud_email = '$email'";
    ($res = mysqli_query($cxn, $query_vykdytojas)) or
        die("Error: " . mysqli_error($cxn));
    $row = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas
    $pras = $row["prasytojas"];

    echo "<td><a href='mailto:$email'>$pras</a></td>\n";
    echo "<td>{$dokai[$i]["dokumentas"]}</td>\n";

    $failas = $dokai[$i]["kelias"];

    $kelias = "./pazymos/" . $failas;

    if (file_exists($kelias)) {
        $src = "src='./imgs/doc.gif'";
        $onclick = "";
    } else {
        $src = "src='./imgs/stop.png'";
        $onclick = "onclick='return false'";
    }
    $kelias = base64_encode($failas);
    echo "<td style='text-align: center;'><a href='view_file.php?f=$kelias' target='_blanc' $onclick ><img $src title='$failas'/></a></td>\n";

    $dok_id = $dokai[$i]["dok_id"];
    if ($dok_id != 0) {
        echo "<td ><form style='margin-bottom: 0' name='dok_nr' action='details_mano_prasymai.php' method='post' target='did'  onsubmit=" .
            '"' .
            "JavaScript: newWindow = openWin('', 'did', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()" .
            '"' .
            " >  <input type='hidden' name='dok_id' value='$dok_id'/> <input type='submit' value='$dok_id' class='btn' title='Atsidarys naujame lange' /> </form></td>\n";
    } else {
        echo "<td></td>\n";
    }
    echo "<td >{$dokai[$i]["pno"]}</td>\n";

    $isdavemail = $dokai[$i]["isdave"];
    $query_isdave = "SELECT  CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS isd FROM naudotojai WHERE naud_email = '$isdavemail'";
    ($rs = mysqli_query($cxn, $query_isdave)) or
        die("Error: " . mysqli_error($cxn));
    $rw = mysqli_fetch_assoc($rs);
    $isdave = $rw["isd"];
    echo "<td >$isdave</td>\n";
} //for
echo "</tr></table><br />\n";
echo $pages->display_pages();

mysqli_close($cxn);
?>




 
</div> <!-- content --> 


<div id='footer'>
<?php include "footer.inc"; ?>

</div>  <!-- footer -->
</div>  <!-- container -->
</body></html>
 
