<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: login.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
   
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
<head><title>Duomenų bazės administravimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<style type="text/css"  media="screen">
  
#nav a  {
display: block;
}
</style>  

<script type="text/javascript">
<!--
	function openWin( windowURL, windowName, windowFeatures ) { 
		return window.open( windowURL, windowName, windowFeatures ) ; 
	} 


function confirmDelete(delUrl) {
  if (confirm("Ar tikrai ištrinti?")) {
    document.location = delUrl;
  }
}

function ask_delete(form) {
    return confirm('Ar tikrai ištrinti?');
}


// -->
</script>

</head>
<body>
<div id="container">
   <div id="header">

<?php
include "header.inc";
echo "</div><div id='nav'>\n";

include "stuff.inc";

$table_heads = [
    "dok_id" => "Nr.",
    "dok_formos_kodas" => "Formos kodas",
    "mokestis" => "Suma",
    "dok_kelias" => "Failas",
    "naud_email" => "Prašytojas",
    //			"pastabos"          => "Pastabos",
    "status_dabar" => "Statusas",
    "status_dabar_date" => "Data",
];

echo "<a href='adm.php'>Administravimas</a>\n";
echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";
if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />\n";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
}

echo "</div><div id='content'>\n";
echo "<h4 style='color: grey'>Duomenų bazės administravimas</h4>\n";

include "dbstuff.inc";
//echo "<br />------naudotojo psw - el. pašto adresas---Rasta viso irasu: ?-\n";

function get_naud_email($pv)
{
    //nenaudojama
    include "dbstuff.inc"; //????????
    ($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
        die("Klaida! Nepavyko prisijungti prie duomenų bazės");
    $query = "SELECT naud_email FROM naudotojai WHERE naud_pavarde = '$pv'"; //$pv tb kabutese
    ($result = mysqli_query($cxn, $query)) or
        die("Error: " . mysqli_error($cxn));
    $rw = mysqli_fetch_assoc($result);
    $naudid = $rw["naud_email"];
    mysqli_close($cxn);
    return $naudid;
}

/*    irasu skaicius:
$n = mysqli_num_rows($result);
if($n < 1)
{
   echo "User name and password are not valid";
   exit();
}
*/

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
    /*   
    Statusas: <input name="statusas" size="20" maxlength="20">
	Dok. Nr.: <input name="dokid" size="20" maxlength="20">
    <input type="submit" name="submit" value="Atrinkti">
    </form>
*/
    ?>

<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
<?php
echo "<table border='0' ><tr><td>Dok. Nr.  </td><td>  <input type='text' name='dokid' size='5' value=''></td></tr>\n";
echo "<tr><td>Statusas</td><td> <select  name='statusas' size='1' maxlength='20' \n>";
for ($b = 1; $b <= $k; $b++) {
    $statusas = $statusai[$b];
    echo "<option value='$statusas'";
    if ($statusas == "Gautas") {
        //---------------
        echo " selected";
    }
    echo ">$statusas </option>\n";
}
echo "</select></td></tr>\n";

echo "<tr><td>Pareiškėjo pavardė</td><td> <input type='text' name='naud' size='25' value=''></td></tr></table>\n";
echo "<input type='submit' name='submit' value='Atrinkti'>\n";
echo "</form>\n";

$where = " AND status_dabar = 'Gautas' ";
} else {
    $sd = $_POST["statusas"]; //sanitize
    $dok_id = trim($_POST["dokid"]);
    //$nuo = $_POST['nuo'];
    //$iki = $_POST['iki'];
    $pavarde = trim($_POST["naud"]);
    ?>
<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
<?php
echo "<table border='0' ><tr><td>Dok. Nr.  </td><td><input type='text' name='dokid' size='5' value='$dok_id'></td></tr>\n";
echo "<tr><td>Statusas</td><td> <select  name='statusas' size='1' maxlength='20' \n>";
for ($b = 1; $b <= $k; $b++) {
    $statusas = $statusai[$b];
    echo "<option value='$statusas'";
    if ($statusas == $sd) {
        echo " selected";
    }
    echo ">$statusas </option>\n";
}
echo "</select></td></tr>\n";

echo "<tr><td>Pareiškėjo pavardė</td><td> <input type='text' name='naud' size='25' value='$pavarde'></td></tr></table>\n";
echo "<input type='submit' name='submit' value='Atrinkti'>\n";
echo "</form>\n";

if ($sd == "Bet koks") {
    $where = "";
} else {
    $where = " AND status_dabar = '$sd' ";
}

if (is_numeric($dok_id)) {
    $where .= " AND dok_id = '$dok_id' ";
}
//if (is_numeric($dok_id)  and empty($where)  )
//	$where = " WHERE dok_id = '$dok_id' ";

if (!is_numeric($dok_id) and !empty($dok_id)) {
    $ms =
        " <p style='color: red; font-weight: bold;  font-style: italic;'>Klaida įvedant atrankos kriterijus!</p> ";
}

if (!empty($pavarde)) {
    $where .= " AND naud_pavarde LIKE '%$pavarde%' ";
}
} //else pabaiga

echo $ms;

//===========================
if (empty($where)) {
    $limit = " LIMIT 0, 1000 ";
    echo "<p>Atranka apribota 1000 įrašų</p> ";
} else {
    $limit = "";
}

$naud_email = 2; //veliau pakeisti i tikra naudotojo id------------------------>>>>>

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");
// iš asoc masyvo eina blogai koduotos raidės─ gal reikės traukti vardus iš db
$query = "SELECT dok_id, dok_formos_kodas, mokestis, dok_kelias, dokai.naud_email, status_dabar, DATE(status_dabar_date) AS dab_statuso_data FROM dokai, naudotojai WHERE dokai.naud_email = naudotojai.naud_email {$where} ORDER BY status_dabar_date {$limit}"; //LIMIT 1, 10 "
//naud_email = $naud_email
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result); // irasu skaicius
$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}

echo "<p>Rasta įrašų: $c. </p>";

echo "<p style='color:red;'>Dėmesio! Paspaudus \"Trinti\" įrašas bus ištrintas iš duomenų bazės ir jo atkurti bus neįmanoma.</p>";
if (isset($_GET["count"]) && $_GET["count"] > 0) {
    $trinta = $_GET["count"];
    echo "<h4 style='color:red;'>Ištrinta įrašų: $trinta</h4>";
}
echo "<form name='form1' method='post' action='trinti_db_daug.php' onsubmit='return ask_delete(this);' >";
echo "<table border='0' cellpadding='2'  >\n"; //pagr. lentelė
//cellpadding='3' cellspacing='3'
echo "<tr>\n";
foreach ($table_heads as $heading) {
    echo "<th>$heading</th>";
}
echo "<th>Statusai</th>\n"; //
echo "<th>Trinti</th>\n"; //
echo "<th>X</th></tr>\n"; //

$n_dokai = sizeof($dokai);
for ($i = 1; $i <= $n_dokai; $i++) {
    //echo "<tr>";
    if ($i % 2 == 0) {
        echo "<tr>\n";
    } else {
        echo "<tr class='odd'>\n";
    }
    echo "<td style='text-align: right; padding-right: .5in'>
      {$dokai[$i]["dok_id"]}</td>\n";
    echo "<td>{$dokai[$i]["dok_formos_kodas"]}</td>\n";
    echo "<td>{$dokai[$i]["mokestis"]} $currency</td>\n";
    $kelias = $dokai[$i]["dok_kelias"];
    $kelias = "uploaded_files/" . $kelias;
    //$kelias = base64_encode($kelias);
    //echo "<td align=center><a href='view_file.php?id=$kelias' ><img src='./imgs/doc.gif'/></a></td>\n";
    echo "<td align=center><a href='$kelias' ><img src='./imgs/doc.gif'/></a></td>\n";

    $naud_email = $dokai[$i]["naud_email"];
    $query_naudotojai = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas FROM naudotojai WHERE naud_email = '$naud_email'";
    ($res = mysqli_query($cxn, $query_naudotojai)) or
        die("Error: " . mysqli_error($cxn));
    $row = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas
    $naud = $row["pareiskejas"];
    echo "<td>$naud</td>\n";
    //echo "<td>{$dokai[$i]['pastabos']}</td>\n";
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

    //echo "<td>{$dokai[$i]['status_dabar'] }</td>\n";
    echo "<td>{$dokai[$i]["dab_statuso_data"]}</td>\n";
    $dok_id = $dokai[$i]["dok_id"];
    //echo "<td align=center><a href='details_ataskaitos.php?dok_id=$dok_id'>Statusai</a></td>\n";

    echo "<td align=center><a href=" .
        '"' .
        "JavaScript: newWindow = openWin('./details_ataskaitos.php?dok_id=$dok_id', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()" .
        '"' .
        ">Statusai</a></td>\n";
    echo "<td style='text-align: center;'><a style='color:red;' href=" .
        '"' .
        "javascript:confirmDelete('trinti_db.php?dok_id=$dok_id')" .
        '"' .
        " >x</a></td>\n";

    echo "<td style='text-align: center;'><input type='checkbox' name='chkbox[]' value='$dok_id'></td></tr>\n";
} //for
echo "</table><br />\n"; //baigias gautų lentelė
echo "<input type='submit' name='delete' value='Trinti pasirinktus'/></form>";

echo "<p><a href='trinti_db_po_men.php'>Trinti įrašus, po kurių įvydymo ar atmetimo praėjo daugiau kaip 130 dienų.</a> (Mokesčiai netrinami.)</p>\n";
mysqli_close($cxn);

echo "<p><a href='ikelti_forma.php'>Įkelti failą</a></p>\n";
echo "<p><a href='ikelti_index_upl.php'>Įkelti index failą į uploaded_files</a></p>\n";
echo "<p><a href='pakeisti_email.php'>Pakeisti naudotojo el. pašto adresą</a></p>\n";
echo "<p><a href='mkdir.php'>Sukurti aplanką</a></p>\n";
//-------------------------------

echo "</div>  <div id='footer'>\n";
include "footer.inc";
?>

</div>
</div>
</body></html>
 
