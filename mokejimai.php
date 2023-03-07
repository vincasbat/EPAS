<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: login.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    //Palikti galutiniam variante tik adminus
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
<head><title>Mokėjimai</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="stylesheet" type="text/css" href="./style/pag.css">
<link rel="shortcut icon" href="favicon.ico">

<script>
function confirmDelete(delUrl) {
  if (confirm("Ar tikrai ištrinti?")) {
    document.location = delUrl;
  }
}
</script>

</head>
<body>
<div id="container">
   <div id="header">

<?php
include "header.inc";
include "paginator.class.php";
echo "</div><div id='nav'>\n";

include "stuff.inc";

echo "<a href='adm.php'>Administravimas</a><br />\n";
echo "<a href='ataskaitos.php'>Ataskaitos</a><br />\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";
if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />\n";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}

echo "</div><div id='content'>\n";

$naud = "";
$email = "";
// Parodome mokesčių lentelę:

include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Nepavyko prisijungti prie duomenų bazės");

$table_heads = [
    "mok_id" => "ID",
    "dok_id" => "Prašymo Nr.",
    "suma" => "Suma, Lt",
    "paskirtis" => "Mokėjimo paskirtis",
    "banko_pranesimas" => "Banko pranešimas",
    "mok_data" => "Data",
    "trinti" => "Trinti",
];

if (!$_POST["submit"]) {
    $query =
        "SELECT mok_id, dok_id, suma, paskirtis, banko_pranesimas, LEFT(mokejimo_data, 10) AS mok_data, moketojas, naud_email FROM mokejimai  ORDER BY mok_id desc";

    //-----------
    $where = "";
    $naud = trim($_GET["naud"]);
    $email = trim($_GET["email"]);
    if (!empty($naud)) {
        $where = " WHERE moketojas  LIKE '%$naud%' ";
        if (!empty($email)) {
            $where .= " AND naud_email  LIKE '%$email%' ";
        }
    } else {
        if (!empty($email)) {
            $where = " WHERE naud_email  LIKE '%$email%' ";
        }
    }

    $query = "SELECT mok_id FROM mokejimai {$where} ORDER BY mok_id desc";

    //------------------
} else {
    $where = "";
    $naud = trim($_POST["naud"]);
    $email = trim($_POST["email"]);
    if (!empty($naud)) {
        $where = " WHERE moketojas  LIKE '%$naud%' ";
        if (!empty($email)) {
            $where .= " AND naud_email  LIKE '%$email%' ";
        }
    } else {
        if (!empty($email)) {
            $where = " WHERE naud_email  LIKE '%$email%' ";
        }
    }

    $query = "SELECT mok_id FROM mokejimai {$where} ORDER BY mok_id desc";
}

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result);

if ($c < 1) {
    echo "Mokėjimų nėra.</div><div id='footer'>\n";
    include "footer.inc";
    echo "</div></body></html>\n";
    exit();
}

$pages = new Paginator();
$pages->items_total = $c;
$pages->mid_range = 9;
$pages->paginate();

//$pages->limit
$query =
    "SELECT mok_id, dok_id, suma, paskirtis, banko_pranesimas, LEFT(mokejimo_data, 10) AS mok_data, moketojas, naud_email FROM mokejimai {$where} ORDER BY mok_id desc " .
    $pages->limit;

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));

echo "<h4 style='color: grey;'><b>Mokėjimai per Elektroninius valdžios vartus ($c)</b></h4>\n";
?>

<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
<lablel for='naud'>Pavardė</label><input type='text' name='naud' size='25' value='<?= $naud ?>'>
<lablel for='email'>El. p.</label><input type='text' name='email' size='25' value='<?= $email ?>'>
<input type='submit' name='submit' value='Atrinkti' class='btn'>
</form>



<?php
echo "<p>Mokėtojo (ne prašytojo) vardą ir pavardę bei prašytojo el. pašto adresą galima pamatyti užvedus pelę ant eilutės.</p>\n";

echo $pages->display_pages();

echo "<table border='0' cellpadding='2' class='fixedwidth' style='margin-top:7px; margin-bottom:7px' >\n";
echo "<tr>\n";
foreach ($table_heads as $heading) {
    echo "<th>$heading</th>";
}
echo "</tr>\n"; //   edit, delete

$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}
$n_dokai = sizeof($dokai);
for ($i = 1; $i <= $n_dokai; $i++) {
    $moketojas = $dokai[$i]["moketojas"];
    $elpastas = $dokai[$i]["naud_email"];
    $title = $moketojas . ", " . $elpastas;
    if ($i % 2 == 0) {
        echo "<tr title='$title'>\n";
    } else {
        echo "<tr class='odd' title='$title'>\n";
    } //odd rows
    echo "<td style='text-align: right; padding-right: 3px;'>{$dokai[$i]["mok_id"]}</td>\n";

    echo "<td style='text-align: right; padding-right: 10px;'>{$dokai[$i]["dok_id"]}</td>\n";
    echo "<td style='text-align: right; padding-right: 6px;'>{$dokai[$i]["suma"]} </td>\n";
    echo "<td>{$dokai[$i]["paskirtis"]} </td>\n";
    //echo "<td>{$dokai[$i]['banko_pranesimas']} </td>\n";
    $pran = $dokai[$i]["banko_pranesimas"];
    if ($pran == "1901" || $pran == "3101") {
        echo "<td style='text-align: center;' ><span style='color: red;'>" .
            $pran .
            "</span></td>\n";
    } else {
        echo "<td style='text-align: center;'>$pran</td>\n";
    }
    echo "<td>{$dokai[$i]["mok_data"]} </td>\n";
    $mok_id = $dokai[$i]["mok_id"];
    //echo "<td style='color:red; text-align:center;'> <a style='color:red;' href='trinti_mok.php?mok_id=$mok_id' > i </a> </td></tr>\n";

    echo "<td style='color:red; text-align:center;'> <a style='color:red;' href=" .
        '"' .
        "javascript:confirmDelete('trinti_mok.php?mok_id=$mok_id')" .
        '"' .
        " > x </a> </td></tr>\n";
} //for
echo "</table>\n"; //baigias mokėjimų lentelė
mysqli_close($cxn);
echo $pages->display_pages();
?>

<br /><br />
<table>
<tr style="background: white;"><td>1101&nbsp;</td><td>Banko pranešimas apie  sėkmingą paslaugos apmokėjimo operacijos įvykdymą</td></tr>
<tr style="background: white;"><td>1201</td><td>Banko pranešimas apie sėkmingai priimtą, bet dar nepatvirtintą paslaugos apmokėjimo operaciją</td></tr>
<tr style="background: white;"><td style='color: red;'>1901</td><td>Banko pranešimas apie nutrauktą paslaugos apmokėjimą</td></tr>
<tr style="background: white;"><td style='color: red;'>3101</td><td>Užklausos parašas neteisingas</td></tr>
</table>

<?php
echo "</div>  <div id='footer'>\n";
include "footer.inc";
?>

</div>
</div>
</body></html>
 
