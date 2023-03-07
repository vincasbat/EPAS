<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: login.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "is":
    case "ap":
    case "pz":
    case "pr":
    case "admins":
        $naud_el_pastas = $_SESSION["naud_email"];
        $par = $_SESSION["pareiskejas"];
        $mess_pareiskejas = "<span style='color:green;'> $par </span>";
        break;

    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
}

//switch
?>





<!DOCTYPE html>
<html>
<head><title>Siunčiamų dokumentų registras</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="stylesheet" type="text/css" href="./style/pag.css">



<link rel="shortcut icon" href="favicon.ico">
<script type="text/javascript">

	function openWin( windowURL, windowName, windowFeatures ) { 
		return window.open( windowURL, windowName, windowFeatures ) ; 
	} 

function confirmDelete(delUrl) {
  if (confirm("Ar tikrai ištrinti?")) {
    document.location = delUrl;
  }
}




// -->
</script>

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

table.blueTable tfoot td {
  font-size: 14px;
}
table.blueTable tfoot .links {
  text-align: right;
}
table.blueTable tfoot .links a{
  display: inline-block;
  background: #1C6EA4;
  color: #FFFFFF;
  padding: 2px 8px;
  border-radius: 5px;
}







#nav a  {
display: block;
}
</style>

</head>
<body>
<div id="container">
   <div id="header">

<?php
include "header.inc";
include "paginator.class.php";
echo "</div><div id='nav'>\n";

include "stuff.inc";

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

if ($_SESSION["grupe"] == "admins") {
    $table_heads += ["x" => "Trinti"];
}

switch (@$_SESSION["grupe"]) {
    case "admins":
        echo "<a href='adm.php'>Administravimas</a>\n";
        break;
    case "pr":
        echo "<a href='gauti.php'>Priėmimo skyrius</a>\n"; //meniu

        break;
    case "pz":
        echo "<a href='pz.php'>Prekių ženklų skyrius</a>\n"; //meniu

        break;
    case "is":
        echo "<a href='is.php'>Išradimų skyrius</a>\n"; //meniu

        break;
    case "ap":
        echo "<a href='ap.php'>Apeliacinis skyrius</a>\n"; //meniu

        break;
    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
} //switch

echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<a href='registras.php'>Registras </a>\n";
echo "<a href='regprasyma.php'>Registruoti prašymą </a>\n";
echo "<a href='isduoti.php'>Išduoti dokumentą</a>\n";
echo "<a href='isduoti2.php'>Be parašo</a>\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />\n";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}

echo "<br /><br /><br /><br /><br />\n";

echo "</div><div id='content'>\n";
echo "<h4 style='color: grey'>Siunčiamų dokumentų registras </h4>\n";

include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

if ($_POST["submit"]) {<?php
    //post:
    ?>

<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">

<?php
$where = "";
$jauwhere = 0;

$wherepno = "";
$pno = $_POST["pno"];
if (strlen($pno) > 2) {
    if ($jauwhere) {
        $wherepno = " AND pno = '$pno' ";
    } else {
        $wherepno = " pno = '$pno' ";
    }
    $jauwhere = 1;
}
echo "<p><label for='pno'>PNO Nr.</label><input type='text' name='pno' size='16' value='$pno'></p>";

$wheredoktipas = "";
$doktipas = $_POST["doktipas"];
if (strlen($doktipas) > 2) {
    if ($jauwhere) {
        $wheredoktipas = " AND dokumentas = '$doktipas' ";
    } else {
        $wheredoktipas = " dokumentas = '$doktipas' ";
    }
    $jauwhere = 1;
}

    //echo "<p><label for='adresatas'>Adresatas</label>
    ?>

<p><label for='doktipas'>Dok. tipas</label>
<select name='doktipas' >
<option value=''>Bet koks</option>
<option value='ISR' <?php if ($doktipas == "ISR") {
    echo " selected ";
} ?>   > Išrašas </option>
<option value='LIU' <?php if ($doktipas == "LIU") {
    echo " selected ";
} ?>   > Liudijimas </option>
<option value='PAZ' <?php if ($doktipas == "PAZ") {
    echo " selected ";
} ?>   > Pažyma </option>
<option value='SPR' <?php if ($doktipas == "SPR") {
    echo " selected ";
} ?>   > Sprendimas </option>
<option value='PRA' <?php if ($doktipas == "PRA") {
    echo " selected ";
} ?>   > Pranešimas </option>
<option value='KIT' <?php if ($doktipas == "KIT") {
    echo " selected ";
} ?>   > Kita </option>"
</select></p>


<?php
$whereregnr = "";
$regnr = $_POST["regnr"];
if (strlen($regnr) > 2) {
    if ($jauwhere) {
        $whereregnr = " AND reg_nr = '$regnr' ";
    } else {
        $whereregnr = " reg_nr = '$regnr' ";
    }
    $jauwhere = 1;
}
echo "<p><label for='regnr'>Reg. Nr.</label><input type='text' name='regnr' size='16' value='$regnr'></p>";

$whererenuo = "";
$nuo = $_POST["nuo"];
if (strlen($nuo) > 2) {
    if ($jauwhere) {
        $wherenuo = " AND data >= '$nuo' ";
    } else {
        $wherenuo = " data >= '$nuo' ";
    }
    $jauwhere = 1;
}
echo "<p><label for='nuo'>Nuo</label><input type='text' name='nuo' size='16' value='$nuo'> Pvz.: 2000-03-04</p>";

$wherereiki = "";
$iki = $_POST["iki"];
if (strlen($iki) > 2) {
    if ($jauwhere) {
        $whereiki = " AND data <= '$iki' ";
    } else {
        $whereiki = " data <= '$iki' ";
    }
    $jauwhere = 1;
}
echo "<p><label for='iki'>Iki</label><input type='text' name='iki' size='16' value='$iki'> Pvz.: 2016-11-03</p>";

//===== isdaveju pavardes

$wherereisdave = "";
$isdave = $_POST["isdave"];
if (strlen($isdave) > 2) {
    if ($jauwhere) {
        $whereisdave = " AND isdave = '$isdave' ";
    } else {
        $whereisdave = " isdave = '$isdave' ";
    }
    $jauwhere = 1;
}

$qry =
    "select naud_vardas, naud_pavarde, naud_email from naudotojai WHERE naud_grupe <> 'par' order by naud_pavarde";
($rslt = mysqli_query($cxn, $qry)) or die("Error: " . mysqli_error($cxn));
$n = 1;
while ($rw = mysqli_fetch_assoc($rslt)) {
    $pareiskejai[$n] = $rw["naud_pavarde"] . " " . $rw["naud_vardas"];
    $paremail[$n] = $rw["naud_email"];
    $n++;
}
$n = 1;
$k = sizeof($pareiskejai);

echo "<p><label for='pareiskejai'>Išdavė</label> <select  name='isdave' size='1' maxlength='30' id='pars'>";
echo "<option value='0'>Visi</option>\n";
for ($b = 1; $b <= $k; $b++) {
    echo "<option value='$paremail[$b]'";
    if ($paremail[$b] == $isdave) {
        echo " selected ";
    }
    echo ">$pareiskejai[$b]</option>\n";
}
echo "</select></p>";

//adresatas:
$whereadresatas = "";
$adresatas = $_POST["adresatas"];
if (strlen($adresatas) > 2) {
    if ($jauwhere) {
        $whereadresatas = " AND adresatas = '$adresatas' ";
    } else {
        $whereadresatas = " adresatas = '$adresatas' ";
    }
    $jauwhere = 1;
}

unset($pareiskejai);
unset($qry);
unset($rslt);
$qry =
    "select naud_vardas, naud_pavarde, naud_email from naudotojai order by naud_pavarde";
($rslt = mysqli_query($cxn, $qry)) or die("Error: " . mysqli_error($cxn));
$n = 1;
while ($rw = mysqli_fetch_assoc($rslt)) {
    $pareiskejai[$n] = $rw["naud_pavarde"] . " " . $rw["naud_vardas"];
    $paremail[$n] = $rw["naud_email"];
    $n++;
}
$n = 1;
$k = sizeof($pareiskejai);

echo "<p><label for='adresatas'>Adresatas</label> <select  name='adresatas' size='1' maxlength='30' >";
echo "<option value='0'>Visi</option>\n";
for ($b = 1; $b <= $k; $b++) {
    echo "<option value='$paremail[$b]'";
    if ($paremail[$b] == $adresatas) {
        echo " selected ";
    }
    echo ">$pareiskejai[$b]</option>\n";
}
echo "</select></p>";

    //======
    ?>

<p style='margin-left: 9.5em;';><input type='submit' name='submit' value='Atrinkti' class='btn'> <a href='registras.php' class='btn'>Išvalyti</a></p>
</form>  

<?php
if ($jauwhere) {
    $where =
        " WHERE " .
        $wherepno .
        $wheredoktipas .
        $whereregnr .
        $wherenuo .
        $whereiki .
        $whereisdave .
        $whereadresatas;
}

$query =
    "SELECT reg_ai, reg_nr, data, adresatas, dokumentas, kelias, dok_id, pno, isdave FROM siunc_registras" .
    $where .
    " order by reg_ai desc LIMIT 500";
//echo $query;
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result);
if ($c < 1) {
    echo "Dokumentų nerasta.</div><div id='footer'>\n";
    include "footer.inc";
    echo "</div></body></html>\n";
    exit();
}
} else {<?php
    // "not post";
    ?>
<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">

<p><label for='pno'>PNO Nr.</label><input type='text' name='pno' size='16' value=''></p>


<p><label for='doktipas'>Dok. tipas</label>
<select name='doktipas' >
<option value=''>Bet koks</option>
<option value='ISR'    > Išrašas </option>
<option value='LIU'    > Liudijimas </option>
<option value='PAZ'   > Pažyma </option>
<option value='SPR'   > Sprendimas </option>
<option value='PRA'  > Pranešimas </option>
<option value='KIT'   > Kita </option>"
</select></p>

<p><label for='regnr'>Reg. Nr.</label><input type='text' name='regnr' size='16' value=''></p>
<p><label for='nuo'>Nuo</label><input type='text' name='nuo' size='16' value=''> Pvz.: 2000-03-04</p>
<p><label for='iki'>Iki</label><input type='text' name='iki' size='16' value=''> Pvz.: 2016-11-03</p>

<?php
$qry =
    "select naud_vardas, naud_pavarde, naud_email from naudotojai WHERE naud_grupe <> 'par' order by naud_pavarde";
($rslt = mysqli_query($cxn, $qry)) or die("Error: " . mysqli_error($cxn));
$n = 1;
while ($rw = mysqli_fetch_assoc($rslt)) {
    $pareiskejai[$n] = $rw["naud_pavarde"] . " " . $rw["naud_vardas"];
    $paremail[$n] = $rw["naud_email"];
    $n++;
}
$n = 1;
$k = sizeof($pareiskejai);

echo "<p><label for='pareiskejai'>Išdavė</label> <select  name='isdave' size='1' maxlength='30' id='pars'>";
echo "<option value='0'>Visi</option>\n";
for ($b = 1; $b <= $k; $b++) {
    echo "<option value='$paremail[$b]'";
    echo ">$pareiskejai[$b]</option>\n";
}
echo "</select></p>";

//adresatas:

unset($pareiskejai);
unset($qry);
unset($rslt);
$qry =
    "select naud_vardas, naud_pavarde, naud_email from naudotojai order by naud_pavarde";
($rslt = mysqli_query($cxn, $qry)) or die("Error: " . mysqli_error($cxn));
$n = 1;
while ($rw = mysqli_fetch_assoc($rslt)) {
    $pareiskejai[$n] = $rw["naud_pavarde"] . " " . $rw["naud_vardas"];
    $paremail[$n] = $rw["naud_email"];
    $n++;
}
$n = 1;
$k = sizeof($pareiskejai);

echo "<p><label for='adresatas'>Adresatas</label> <select  name='adresatas' size='1' maxlength='30' >";
echo "<option value='0'>Visi</option>\n";
for ($b = 1; $b <= $k; $b++) {
    echo "<option value='$paremail[$b]'";
    echo ">$pareiskejai[$b]</option>\n";
}
echo "</select></p>";
?>

<p style='margin-left: 9.5em;';><input type='submit' name='submit' value='Atrinkti' class='btn'> <a href='registras.php' class='btn'>Išvalyti</a></p>
</form> 

<?php
$query =
    "SELECT reg_ai, reg_nr, data, adresatas, dokumentas, kelias, dok_id, pno, isdave FROM siunc_registras order by reg_ai desc LIMIT 500";

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result);
if ($c < 1) {
    echo "Dokumentų nerasta.</div><div id='footer'>\n";
    include "footer.inc";
    echo "</div></body></html>\n";
    exit();
}
} //else

if ($c % 10 == 1 && $c != 11) {
    echo "<p>$c dokumentas (max. 500)</p>";
} elseif ($c % 10 == 0 || ($c > 10 && $c < 20)) {
    echo "<p>$c dokumentų (max. 500)</p>";
} else {
    echo "<p>$c dokumentai (max. 500)</p>";
}

echo "<table class='blueTable'  ><thead>\n"; //pagr. lentelė

echo "<tr>\n";
foreach ($table_heads as $heading) {
    echo "<th>$heading</th>";
}
echo "</tr>\n"; //

$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}
$n_dokai = sizeof($dokai);
for ($i = 1; $i <= $n_dokai; $i++) {
    $dok_id = $dokai[$i]["dok_id"];
    echo "</thead><tr><td>   {$dokai[$i]["reg_nr"]}  </td>\n";
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
    //$kelias = "./dtbs/".$failas;  //derinimas
    $kelias = "./pazymos/" . $failas;

    if (file_exists($kelias)) {
        $src = "src='./imgs/doc.gif'";
        $onclick = "";
    } else {
        $src = "src='./imgs/stop.png'"; //doc _inactive.gif
        $onclick = "onclick='return false'";
    }

    $kelias = base64_encode($failas);
    echo "<td style='text-align: center;'><a href='view_file.php?f=$kelias' target='_blanc' $onclick ><img $src title='$failas'/></a></td>\n";

    $dok_id = $dokai[$i]["dok_id"];
    if ($dok_id != 0) {
        echo "<td ><a href=" .
            '"' .
            "JavaScript: newWindow = openWin('./details_ataskaitos.php?dok_id=$dok_id', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()" .
            '"' .
            ">$dok_id</a></td>\n";
    } else {
        echo "<td></td>\n";
    }
    echo "<td >{$dokai[$i]["pno"]}</td>\n";

    $isdavemail = $dokai[$i]["isdave"];
    $query_isdave = "SELECT  CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS isd FROM naudotojai WHERE naud_email = '$isdavemail'";
    ($rs = mysqli_query($cxn, $query_isdave)) or
        die("Error: " . mysqli_error($cxn));
    $rw = mysqli_fetch_assoc($rs); //tikrinti ar vienas įrašas
    $isdave = $rw["isd"];
    echo "<td >$isdave</td>\n";

    if ($_SESSION["grupe"] == "admins") {
        $doknr = $dokai[$i]["reg_ai"];
        echo "<td><a style='color:red;' href=" .
            '"' .
            "javascript:confirmDelete('trinti_rg.php?doknr=$doknr')" .
            '"' .
            " >$doknr</a></td></tr>\n";
    } else {
        echo "</tr>\n";
    }
} //for
echo "</table><br />\n";

echo "<p>Paskutinių savaičių statistika</p>";
date_default_timezone_set("Europe/Vilnius");
$current_week = strtotime("today + 1 day");
$start_week = strtotime("last monday midnight", $current_week);
$end_week = strtotime("next sunday", $start_week);
$start_week = date("Y-m-d", $start_week);
$end_week = date("Y-m-d", $end_week);
$i = 0;
echo "<table class='blueTable'>\n";
while ($i < 6) {
    $start_week = date("Y-m-d", strtotime($start_week . " - 7 days"));
    $end_week = date("Y-m-d", strtotime($end_week . " -  7 days"));
    $qr = "SELECT * FROM siunc_registras WHERE data >= '$start_week' AND data <= '$end_week' ";
    ($rslt = mysqli_query($cxn, $qr)) or die("Error: " . mysqli_error($cxn));
    $c = mysqli_num_rows($rslt);
    echo "<tr><td>$start_week</td><td>$end_week</td><td> &nbsp; $c &nbsp; </td></tr>";
    $i++;
}
echo "</table><br />\n";

mysqli_close($cxn);
echo "</div><div id='footer'>\n";
include "footer.inc";

echo "</div></div>";

echo "</body></html>";

?>

