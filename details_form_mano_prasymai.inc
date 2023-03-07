<!DOCTYPE html>
<html>
<head><title>Mano prašymo statusai</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<style type="text/css">
body {
	background: white;
}
#container {  
width: 100%;
}
</style>

</head>
<body>
<div id="container">
   <div id="header">


<?php
//include("header.inc");
echo "</div>\n";

include "dbstuff.inc";
//include("stuff.inc");

echo "<div id='pop'>\n";
echo "<h4 style='color: grey'>Mano prašymo statusai</h4>\n";

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

//$naud_el_pastas = $_SESSION['naud_email'];
$dok_id = $_SESSION["dok_id"];

function toEur($litai)
{
    return round($litai / 3.4528, 2);
}

echo "<p>Prašymo Nr. <b>$dok_id</b>. \n";
$dokoid = $dok_id;
$query = "SELECT dok_kelias, naud_email, ip, pastabos FROM dokai WHERE dok_id = $dok_id AND naud_email = '$naud_el_pastas'";
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);
$kelias = $row["dok_kelias"];
$pastabos = $row["pastabos"];
//$kelias = "uploaded_files/".$kelias;     //  stuff------------------------------------------------------- ??????
$naud_email = $row["naud_email"];
$ip = $row["ip"];

echo "PNO Nr.  <b>$ip</b>. \n";
$query_naudotojai = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas FROM naudotojai WHERE naud_email = '$naud_email'";
($res = mysqli_query($cxn, $query_naudotojai)) or
    die("Error: " . mysqli_error($cxn));
$rw = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas LIMIT 1;
$naud = $rw["pareiskejas"];
echo "Pareiškėjas (-a)  $naud.</p>\n";
//<a href='view_file.php?id=$kelias' >

if (strlen($kelias) > 5) {
    $kelias = base64_encode($kelias);
    //echo "<p>Failai <a href='$kelias' target='_blanc' ><img src='./imgs/doc.gif'/></a>\n";
    echo "<p>Rinkmena (-os) <a href='view_file.php?id=$kelias' ><img src='./imgs/doc.gif'/></a>\n";
    // Kiti failai:----------------------------------------- pr
    $query = "SELECT file_id, dok_kelias FROM kiti_failai WHERE dok_id = $dok_id";

    ($result = mysqli_query($cxn, $query)) or
        die("Klaida: " . mysqli_error($cxn));
    $c = mysqli_num_rows($result); // irasu skaicius
    $n = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $trinami_dokai[$n]["fid"] = $row["file_id"];
        $trinami_dokai[$n]["dkelias"] = $row["dok_kelias"];
        //echo "Failo Nr. ", $trinami_dokai[$n]['fid'], "<br />";
        $n++;
    }

    $t_dokai = sizeof($trinami_dokai);
    for ($i = 1; $i <= $t_dokai; $i++) {
        $file_id = $trinami_dokai[$i]["fid"];

        $dok_kelias = $trinami_dokai[$i]["dkelias"];
        $visas_kelias = "uploaded_files/" . $dok_kelias; //  stuff------------------------------------------------------- ??????
        $dok_kelias = base64_encode($dok_kelias);
        //echo "<a href='$visas_kelias' target='_blanc' ><img src='./imgs/doc.gif'/></a>\n";
        echo "<a href='view_file.php?id=$dok_kelias' ><img src='./imgs/doc.gif'/></a>\n";
    }
    // Kiti failai:----------------------------------------- pab
} //if strlen

// Mygtukas pdf'ui generuoti:

echo "<form style='margin-bottom: 0' name='dok_nr' action='pdf.php' method='post'>  <input type='hidden' name='dok_id' value='$dok_id'/> <input type='submit' value='Parsisiųsti pažymą' class='btn' title='Parsisiųsti pažymą PDF formatu' /> </form>\n";

echo "</p>\n";

//DATE(status_date) AS statuso_data    --> 2009-01-01

//$query = "SELECT dok_id, statusID, status_date  FROM dok_statusai WHERE dok_id = $dok_id ORDER BY status_date ";

$query = "SELECT dok_statusai.dok_id, statusID, status_date, naudotojai.naud_email, naudotojai.naud_telef, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas  FROM dok_statusai, naudotojai WHERE dok_statusai.naud_email = naudotojai.naud_email AND dok_statusai.dok_id = $dok_id   ORDER BY status_date ";

// AND dok_statusai.naud_email = '$naud_el_pastas'  reikalingas kad nebūtų galima pažiūrėti kitų dokumentų

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}
$n_dokai = sizeof($dokai);

//echo "<table cellpadding='2'><tr><th>Statusas</th><th>Data</th><th>Pateikė</th></tr>";

if ($pastabos) {
    echo "<table cellpadding='2'><tr><th>Statusas</th><th>Data</th><th>Pateikė</th><th>Pastabos</th></tr>";
} else {
    echo "<table cellpadding='2'><tr><th>Statusas</th><th>Data</th><th>Pateikė</th></tr>";
}

//Čia gal pridėti stulpelį, kuriame būtų nurodyta, kas perkėlė į tą statusą
for ($i = 1; $i <= $n_dokai; $i++) {
    //echo "<tr>\n";
    if ($i % 2 == 0) {
        echo "<tr>\n";
    } else {
        echo "<tr class='odd'>\n";
    }
    $sd = $dokai[$i]["statusID"];
    if (
        ($sd == "Atmestas") |
        ($sd == "PZ atmestas") |
        ($sd == "IS atmestas") |
        ($sd == "AP atmestas")
    ) {
        // čia pataisyti-----

        echo "<td style='color: red'>$sd</td>\n";
    } else {
        echo "<td>$sd</td>\n";
    }

    if (strpos($ip, "CSV")) {
        $pastab = substr($pastabos, 0, 100) . "...";
    } else {
        $pastab = $pastabos;
    }

    echo "<td>{$dokai[$i]["status_date"]}</td><td>{$dokai[$i]["pareiskejas"]}, tel. {$dokai[$i]["naud_telef"]},  <a href= 'mailto:{$dokai[$i]["naud_email"]}'> {$dokai[$i]["naud_email"]} </a></td>\n";
    if ($pastabos) {
        if ($i == 1) {
            echo "<td rowspan=$n_dokai style='vertical-align:middle;'> $pastab</td>\n";
        }
    }
    echo "</tr>\n";
} //for
echo "</table>\n";

// Parodome mokėjimus per Elektroninės valdžios vartus:
unset($dokai);
unset($table_heads);

$table_heads = [
    "dok_id" => "Prašymo Nr.",
    "suma" => "Suma",
    "paskirtis" => "Mokėjimo paskirtis",
    "banko_pranesimas" => "Banko pranešimas",
    "mok_data" => "Data",
];

$query = "SELECT dok_id, suma, paskirtis, banko_pranesimas, LEFT(mokejimo_data, 10) AS mok_data, moketojas FROM mokejimai WHERE naud_email='$naud_email' AND (dok_id = $dok_id or paskirtis LIKE '%$ip%') ORDER BY mok_id desc  LIMIT 0, 100 ";

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result);
//echo "<p>Rasta mokėjimų: $c </p>";
echo "<p>Mokėjimas per Elektroninius valdžios vartus </p>\n";

echo "<table border='0' cellpadding='2' class='fixedwidth' >\n";
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
    $did = $dokai[$i]["dok_id"];
    if ($dokoid == $did) {
        echo "<tr style='background: lightgreen'>\n";
    } else {
        if ($i % 2 == 0) {
            echo "<tr>\n";
        } else {
            echo "<tr class='odd'>\n";
        } //odd rows
    }

    echo "<td style='text-align: right; padding-right: 6px;'>$did</td>\n";
    $suma_litais = $dokai[$i]["suma"];
    $suma_eurais = toEur($suma_litais);
    $title = $dokai[$i]["moketojas"];
    $suma = $dokai[$i]["suma"];

    if ($dokai[$i]["mok_data"] < "2015.01.01") {
        $suma .= "&nbsp;LT";
    } else {
        $suma .= "&nbsp;EUR";
    }
    echo "<td style='text-align: right; padding-right: 6px;' title='$title'>$suma</td>\n";

    $mok_paskirtis = $dokai[$i]["paskirtis"];
    $pos = strpos($mok_paskirtis, $ip);
    if (!($pos === false)) {
        $mok_paskirtis = str_replace(
            $ip,
            "<span style='background-color:lightgreen;'>$ip</span>",
            $mok_paskirtis
        );
    }

    echo "<td>$mok_paskirtis </td>\n";
    //echo "<td>{$dokai[$i]['paskirtis']} </td>\n";
    //echo "<td>{$dokai[$i]['banko_pranesimas']} </td>\n";
    $pran = $dokai[$i]["banko_pranesimas"];
    if ($pran == "1901" || $pran == "3101") {
        echo "<td style='text-align: center;'><span style='color: red;'>" .
            $pran .
            "</span></td>\n";
    } else {
        echo "<td style='text-align: center;'>$pran</td>\n";
    }
    echo "<td>{$dokai[$i]["mok_data"]} </td>\n";
} //for
echo "</table><br />\n"; //baigias mokėjimų lentelė

if (substr_count($pastabos, ":") > 1 && !strpos($ip, "CSV")) {
    $pratesimai = explode(" ", $pastabos);
    $dvitaskiai = substr_count($pratesimai[0], ":");
    if ($dvitaskiai == 2) {
        $span = 2;
    } else {
        $span = 3;
    }
    echo "<p><b>Patentų galiojimo pratęsimai</b> </p>";
    echo "<table border='0' cellpadding='2' class='fixedwidth' >\n";
    echo "<tr><td></td><td>Prašymo Nr.</td><td colspan='$span'> $dokoid </td></tr>";
    echo "<tr><td></td><td>Pareiškėjas</td><td colspan='$span'> $naud </td></tr>";
    echo "<tr><td></td><td>Mokėtojas</td><td colspan='$span'> $title </td></tr>";
    if ($dvitaskiai == 2) {
        echo "<tr><th></th><th>Patento Nr.</th><th>Metai</th><th>Suma</th></tr>";
    } else {
        echo "<tr><th></th><th>Patento Nr.</th><th>Metai</th><th>Suma</th><th>Info</th></tr>";
    }
    for ($i = 0; $i < count($pratesimai); $i++) {
        $eil = explode(":", $pratesimai[$i]);
        if ($dvitaskiai == 2) {
            echo "<tr><td>" .
                ($i + 1) .
                "</td><td>" .
                $eil[0] .
                "</td><td>" .
                $eil[1] .
                "</td><td>" .
                $eil[2] .
                "</td></tr>";
        } else {
            echo "<tr><td>" .
                ($i + 1) .
                "</td><td>" .
                $eil[0] .
                "</td><td>" .
                $eil[1] .
                "</td><td>" .
                $eil[2] .
                "</td><td>" .
                $eil[3] .
                "</td></tr>";
        }
    }
    echo "</table><br />\n";
}

if (substr_count($pastabos, ":") > 1 && strpos($ip, "CSV")) {
    $pratesimai = json_decode($pastabos);
    echo "<p><b>Patentų galiojimo pratęsimai</b> </p>";
    echo "<table border='0' cellpadding='2' class='fixedwidth' >\n";
    echo "<tr><td></td><td>Prašymo Nr.</td><td colspan='3'> $dokoid </td></tr>";
    echo "<tr><td></td><td>Pareiškėjas</td><td colspan='3'> $naud </td></tr>";
    echo "<tr><td></td><td>Mokėtojas</td><td colspan='3'> $title </td></tr>";

    echo "<tr><th></th><th>Patento Nr.</th><th>Metai</th><th>Suma</th><th>Info</th></tr>";
    for ($i = 0; $i < count($pratesimai); $i++) {
        $eil = $pratesimai[$i];
        if (isset($eil->info)) {
            $info = $eil->info;
        } else {
            $info = "";
        }
        echo "<tr><td>" .
            ($i + 1) .
            "</td><td>" .
            $eil->patnr .
            "</td><td>" .
            $eil->metai .
            "</td><td>" .
            number_format($eil->suma, 2, ".", "") .
            "</td><td>" .
            $info .
            "</td></tr>";
    }
    echo "</table><br />\n";
} //if

mysqli_close($cxn);

//include("sutart_zym.inc");
?>

<table>
<tr style="background: white; color: grey; font-weight: bold"><td>Banko <br /> pranešimas</td><td>Aprašymas</td></tr>
<tr style="background: white;"><td>1101</td><td>Banko pranešimas apie  sėkmingą paslaugos apmokėjimo operacijos įvykdymą</td></tr>
<tr style="background: white;"><td>1201</td><td>Banko pranešimas apie sėkmingai priimtą, bet dar nepatvirtintą paslaugos apmokėjimo operaciją</td></tr>
<tr style="background: white;"><td style='color: red;'>1901</td><td>Banko pranešimas apie nutrauktą paslaugos apmokėjimą</td></tr>
<tr style="background: white;"><td style='color: red;'>3101</td><td>Užklausos parašas neteisingas</td></tr>
</table>

</div>
</body></html>
