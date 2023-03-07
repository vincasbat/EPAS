<!DOCTYPE html>
<html>
<head><title>Prašymo statusai</title>
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
ini_set("display_errors", 1);
error_reporting(E_ALL);

echo "</div>\n";
include "dbstuff.inc";

echo "<div id='pop'>\n";
echo "<h4 style='color: grey'>Prašymo statusai</h4>\n";

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$dok_id = $_SESSION["dok_id"];

$query = "SELECT dok_kelias, naud_email, from_ip, pastabos, ip, dok_formos_kodas FROM dokai WHERE dok_id = $dok_id";
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);
$kelias = $row["dok_kelias"];
//$kelias = "uploaded_files/".$kelias;
$naud_email = $row["naud_email"];
$from_ip = $row["from_ip"];
$pastabos = $row["pastabos"];
$ip = $row["ip"];
$dok_formos_kodas = $row["dok_formos_kodas"];

echo "<p>Prašymo Nr. <b>$dok_id</b>. &nbsp; &nbsp; &nbsp;  PNO Nr. <b>$ip</b>. &nbsp; &nbsp; &nbsp;  Formos kodas <b>$dok_formos_kodas</b>.</p>\n";
$dokoid = $dok_id;

$query_naudotojai = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas, CONCAT(' ', naud_adr, '; tel. ', naud_telef, '; ', naud_org) AS par_duom FROM naudotojai WHERE naud_email = '$naud_email'";
($res = mysqli_query($cxn, $query_naudotojai)) or
    die("Error: " . mysqli_error($cxn));
$rw = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas
$naud = $rw["pareiskejas"];
$naud_duom = $rw["par_duom"];
$naud_email_ref = "<a href= 'mailto:$naud_email' > $naud </a>";
echo "<p>", $naud_email_ref, $naud_duom, "</p>\n";

if (strlen($kelias) > 5) {
    $kelias = base64_encode($kelias);
    echo "<p>Rinkmena (-os) <a href='view_file.php?id=$kelias' target='_blanc' ><img src='./imgs/doc.gif'/></a>\n";

    // Kiti failai:----------------------------------------- pr
    $query = "SELECT file_id, dok_kelias FROM kiti_failai WHERE dok_id = $dok_id";

    ($res = mysqli_query($cxn, $query)) or die("Klaida: " . mysqli_error($cxn));
    $c = mysqli_num_rows($res); // irasu skaicius
    $trinami_dokai = null;
    $n = 1;
    while ($r = mysqli_fetch_assoc($res)) {
        $trinami_dokai[$n]["fid"] = $r["file_id"];
        $trinami_dokai[$n]["dkelias"] = $r["dok_kelias"];
        //echo "Failo Nr. ", $trinami_dokai[$n]['fid'], "<br />";
        $n++;
    }

    $t_dokai = sizeof($trinami_dokai);
    for ($i = 1; $i <= $t_dokai; $i++) {
        $file_id = $trinami_dokai[$i]["fid"];

        $dok_kelias = $trinami_dokai[$i]["dkelias"];
        $visas_kelias = "uploaded_files/" . $dok_kelias;
        $dok_kelias = base64_encode($dok_kelias);
        //view_file.php?id=$kelias
        //echo "<a href='$visas_kelias' target='_blanc' ><img src='./imgs/doc.gif'/></a>\n";
        echo "<a href='view_file.php?id=$dok_kelias' target='_blanc' ><img src='./imgs/doc.gif'/></a>\n";
    }
    // Kiti failai:----------------------------------------- pab

    echo "Gauta iš adreso $from_ip </p>\n";
}

// if strlen
else {
    echo "<p>&nbsp;Gauta iš adreso $from_ip </p>\n";
}

//DATE(status_date) AS statuso_data
//$query = "SELECT dok_id, statusID, status_date, naud_email FROM dok_statusai  WHERE dok_id = $dok_id ORDER BY status_date ";

$query = "SELECT dok_id, statusID, status_date, CONCAT(naudotojai.naud_vardas, ' ', naudotojai.naud_pavarde) AS vykdytojas
 FROM dok_statusai INNER JOIN naudotojai ON dok_statusai.naud_email = naudotojai.naud_email  WHERE dok_id = $dok_id ORDER BY status_date ";

//

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$n = 1;
$dokai = null;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}
$n_dokai = sizeof($dokai);

if ($pastabos) {
    echo "<table cellpadding='2'><tr><th>Statusas</th><th>Data</th><th>Pateikė</th><th>Pastabos</th></tr>";
} else {
    echo "<table cellpadding='2'><tr><th>Statusas</th><th>Data</th><th>Pateikė</th></tr>";
}

for ($i = 1; $i <= $n_dokai; $i++) {
    //echo "<tr>\n";
    if ($i % 2 == 0) {
        echo "<tr>\n";
    } else {
        echo "<tr class='odd'>\n";
    }
    $sd = $dokai[$i]["statusID"];
    if (($sd == "Atmestas") | ($sd == "PZ atmestas") | ($sd == "IS atmestas")) {
        echo "<td style='color: red'>$sd</td>\n";
    } else {
        echo "<td>$sd</td>\n";
    }

    echo "<td>{$dokai[$i]["status_date"]}</td>\n";
    echo "<td>{$dokai[$i]["vykdytojas"]}</td>\n"; //Čia įrašyti vykdytoją
    if ($pastabos) {
        if ($i == 1) {
            echo "<td rowspan=$n_dokai style='vertical-align:middle;'> $pastabos</td>\n";
        }
    }
    echo "</tr>\n";
} //for
echo "</table>\n";

// Parodome mokėjimus per Elektroninės valdžios vartus:
unset($dokai);
unset($table_heads);
$dokai = null;
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

echo "<p>Prašytojo (-os) mokėjimas per Elektroninius valdžios vartus </p>\n";
$title = "";
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

    $title = $dokai[$i]["moketojas"];

    $suma = $dokai[$i]["suma"];

    if ($dokai[$i]["mok_data"] < "2015.01.01") {
        $suma .= "&nbsp;LT";
    } else {
        $suma .= "&nbsp;EUR";
    }

    echo "<td style='text-align: right; padding-right: 6px;' title='$title'>$suma </td>\n";

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

if (substr_count($pastabos, ":") > 1) {
    $pratesimai = explode(" ", $pastabos);

    $dvitaskiai = substr_count($pratesimai[0], ":"); //**********
    if ($dvitaskiai == 2) {
        $span = 2;
    } else {
        $span = 3;
    } //**********

    echo "<p><b>Patentų galiojimo pratęsimai</b>  <a href='word.php?dokid=$dokoid'><img src='./imgs/doc__.gif'/></a> </p>";
    echo "<table border='0' cellpadding='2' class='fixedwidth' >\n";
    echo "<tr><td></td><td>Prašymo Nr.</td><td colspan='$span'> $dokoid </td></tr>";
    echo "<tr><td></td><td>Pareiškėjas</td><td colspan='$span'> $naud </td></tr>";
    echo "<tr><td></td><td>Mokėtojas</td><td colspan='$span'> $title </td></tr>";
    if ($dvitaskiai == 2) {
        //  ************
        echo "<tr><th></th><th>Patento Nr.</th><th>Metai</th><th>Suma</th></tr>";
    } else {
        echo "<tr><th></th><th>Patento Nr.</th><th>Metai</th><th>Suma</th><th>Info</th></tr>";
    }
    for ($i = 0; $i < count($pratesimai); $i++) {
        $eil = explode(":", $pratesimai[$i]);
        if ($dvitaskiai == 2) {
            //  *******************
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
?>



<table>

<tr style="background: white;"><td>1101&nbsp;</td><td>Banko pranešimas apie  sėkmingą paslaugos apmokėjimo operacijos įvykdymą</td></tr>
<tr style="background: white;"><td>1201</td><td>Banko pranešimas apie sėkmingai priimtą, bet dar nepatvirtintą paslaugos apmokėjimo operaciją</td></tr>
<tr style="background: white;"><td style='color: red;'>1901</td><td>Banko pranešimas apie nutrauktą paslaugos apmokėjimą</td></tr>
<tr style="background: white;"><td style='color: red;'>3101</td><td>Užklausos parašas neteisingas</td></tr>
</table>
<?php //-----------------------

mysqli_close($cxn);
//echo "</div>  <div id='footer'>\n";
//include("footer.inc");
?>
<p>Užvedus pelę ant sumos parodomas mokėtojo (ne pareiškėjo) vardas.</p>

</div>
</body></html>
