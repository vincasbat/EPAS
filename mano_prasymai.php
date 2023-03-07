<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    //Palikti galutiniam variante tik adminus
    case "par":
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
<head><title>Mano prašymai</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<link rel="stylesheet" type="text/css" href="./style/styles.css">

<link rel="shortcut icon" href="favicon.ico">

<script type="text/javascript">
<!--
	function openWin( windowURL, windowName, windowFeatures ) { 
		return window.open( windowURL, windowName, windowFeatures ) ; 
	} 
 -->
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


</head>
<body>
<div id="container">
   <div id="header">

<?php
include "header.inc";
echo "</div><div id='nav'>\n";

include "stuff.inc";

$table_heads = [
    "dok_id" => "Prašymo Nr.",
    "ip" => "PNO Nr.",
    "dok_formos_kodas" => "Formos kodas",
    "naud_email" => "Pareiškėjas",
    "status_dabar" => "Statusas",
    "status_dabar_date" => "Data",
];

//echo "<tr><td valign='top' style='background: white; width: 180px;'>\n";

echo "<a href='index.php'>Į pradžią</a>\n";
echo "<a href='upload.php'>Pateikti prašymą</a>";
echo "<a href='mano_prasymai.php'>Mano prašymai</a>\n";

echo "<a href='moketi.php'>Mokėti</a>\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";
if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas";
}
echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";

echo "</div><div id='content'>\n";
echo "<h4 style='color: grey'>Mano prašymai</h4>\n";
include "dbstuff.inc";

function getStatus()
{
    $formCode = [
        1 => "Bet koks",
        "Gautas",
        "PZ",
        "IS",
        "AP",
        "PZ OK",
        "IS OK",
        "AP OK",
        "PZ atmestas",
        "IS atmestas",
        "AP atmestas",
        "OK",
        "Atmestas",
    ];
    return $formCode;
}
$statusai = getStatus(); //tik leistinų statusų funkcija
$k = sizeof($statusai);

if (!$_POST["submit"]) {<?php
   
    ?>
<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
<?php
echo "Statusas: <select  name='statusas' size='1' maxlength='20' \n>";
for ($b = 1; $b <= $k; $b++) {
    $statusas = $statusai[$b];
    echo "<option value='$statusas'";
    if ($statusas == "Bet koks") {
        echo " selected";
    }
    echo ">$statusas </option>\n";
}
echo "</select>\n";
echo "<input type='submit' name='submit' value='Atrinkti'>\n";
echo "</form>\n";

//$where = ""; // nerodome senesnių kaip 30 dienų įvykdytų prašymų (OK):
$where =
    " AND NOT (status_dabar = 'OK' AND DATEDIFF(NOW(),  status_dabar_date) > 30) ";
} else {$sd = $_POST["statusas"]; ?>

<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
<?php
echo "Statusas: <select  name='statusas' size='1' maxlength='20' \n>";
for ($b = 1; $b <= $k; $b++) {
    $statusas = $statusai[$b];
    echo "<option value='$statusas'";
    if ($statusas == $sd) {
        echo " selected";
    }
    echo ">$statusas </option>\n";
}
echo "</select>\n";
echo "<input type='submit' name='submit' value='Atrinkti'>\n";
echo "</form>\n";

if ($sd == "Bet koks") {
    //nerodome senesnių kaip 30 dienų įvykdytų (OK) prašymų:

    $where =
        " AND NOT (status_dabar = 'OK' AND DATEDIFF(NOW(),  status_dabar_date) > 30) ";
} else {
    if ($sd == "OK") {
        $where = " AND status_dabar = '$sd' AND NOT (status_dabar = 'OK' AND DATEDIFF(NOW(),  status_dabar_date) > 30) ";
    } else {
        $where = " AND status_dabar = '$sd' ";
    }
}
}

//===========================

$naud_email = $_SESSION["naud_email"];
//$naud_email = 2;  //veliau pakeisti i tikra naudotojo id

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$query = "SELECT dok_id, dok_formos_kodas, mokestis, dok_kelias, naud_email,  status_dabar, DATE(status_dabar_date) AS dab_statuso_data, ip FROM dokai WHERE naud_email = '$naud_email'  {$where} ORDER BY status_dabar_date desc ";
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}

$c = mysqli_num_rows($result);
echo "<p>Rasta įrašų: $c. &nbsp;&nbsp;&nbsp;&nbsp;";

echo "<a href=" .
    '"' .
    "JavaScript: newWindow = openWin('./mano_mokejimai.php', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()" .
    '"' .
    ">Mano mokėjimai per Elektroninius valdžios vartus</a> </p>\n";

echo "<table class='blueTable'><thead><tr>\n";

foreach ($table_heads as $heading) {
    echo "<th>$heading</th>";
}
echo "<th></th></thead></tr>\n"; //

$n_dokai = sizeof($dokai);
for ($i = 1; $i <= $n_dokai; $i++) {
    // echo "<tr>";
    if ($i % 2 == 0) {
        echo "<tr >\n";
    } else {
        echo "<tr class='odd'>\n";
    }
    echo "<td style='text-align: right; padding-right: 6px;'>
      {$dokai[$i]["dok_id"]}</td>\n";
    echo "<td style='text-align: right; padding-right: 4px;'>{$dokai[$i]["ip"]}</td>\n";
    echo "<td>{$dokai[$i]["dok_formos_kodas"]}</td>\n";

    $naud_email = $dokai[$i]["naud_email"];
    $query_naudotojai = "SELECT naud_email, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pareiskejas FROM naudotojai WHERE naud_email = '$naud_email'";
    ($res = mysqli_query($cxn, $query_naudotojai)) or
        die("Error: " . mysqli_error($cxn));
    $row = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas
    $naud = $row["pareiskejas"];
    echo "<td>$naud</td>\n";
    //atmestus prasymus zymime raudonai:
    $sd = $dokai[$i]["status_dabar"];
    if (
        ($sd == "Atmestas") |
        ($sd == "PZ atmestas") |
        ($sd == "IS atmestas") |
        ($sd == "AP atmestas")
    ) {
        echo "<td style='color: red'>$sd</td>\n";
    } else {
        echo "<td>$sd</td>\n";
    }

    echo "<td>{$dokai[$i]["dab_statuso_data"]}</td>\n";
    $dok_id = $dokai[$i]["dok_id"];

    echo "<td > <form style='margin-bottom: 0' name='dok_nr' action='details_mano_prasymai.php' method='post' target='did'  onsubmit=" .
        '"' .
        "JavaScript: newWindow = openWin('', 'did', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()" .
        '"' .
        " >  <input type='hidden' name='dok_id' value='$dok_id'/> <input type='submit' value='...' class='btn' title='Atsidarys naujame lange' /> </form> </td></tr>\n";
} //for
echo "</table>\n";

//baigias gautų lentelė
?>


<?php
mysqli_close($cxn);

echo "</div>  <div id='footer'>\n";
include "footer.inc";
?>

</div>
</div>
</body></html>
 
