<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

include "dbstuff.inc";

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$start = $_GET["start"];
$end = $_GET["end"];

//pagal eksperta
$query = "SELECT count(*) as isdokai, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS isdavejas   FROM siunc_registras INNER JOIN naudotojai ON siunc_registras.isdave = naudotojai.naud_email WHERE  data >= '$start' AND data <= '$end' GROUP BY isdavejas  ORDER BY isdokai desc";

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result); // irasu skaicius
$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}

$resp = [];
$count = 0;
$kiti = 0;

$n_dokai = sizeof($dokai);
for ($i = 1; $i <= $n_dokai; $i++) {
    if ($count > 5) {
        $kiti = $kiti + intval($dokai[$i]["isdokai"]);
    } else {
        $resp[$count] = [
            "donors" => intval($dokai[$i]["isdokai"]),
            "location" => $dokai[$i]["isdavejas"],
        ];
    }
    $count++;
} //for
if ($count > 5) {
    $resp[6] = ["donors" => $kiti, "location" => "Kiti"];
}

//pagal adreasata
$query = "SELECT count(*) as isdokai, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS gavejas   FROM siunc_registras INNER JOIN naudotojai ON siunc_registras.adresatas = naudotojai.naud_email WHERE  data >= '$start' AND data <= '$end' GROUP BY gavejas ORDER by isdokai DESC";
unset($dokai);
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result); // irasu skaicius
$n = 1;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $field => $value) {
        $dokai[$n][$field] = $value;
    }
    $n++;
}

$resp2 = [];
$count = 0;
$kiti = 0;

$n_dokai = sizeof($dokai);
for ($i = 1; $i <= $n_dokai; $i++) {
    if ($count > 5) {
        $kiti = $kiti + intval($dokai[$i]["isdokai"]);
    } else {
        $resp2[$count] = [
            "donors" => intval($dokai[$i]["isdokai"]),
            "location" => $dokai[$i]["gavejas"],
        ];
    }
    $count++;
} //for
if ($count > 5) {
    $resp2[6] = ["donors" => $kiti, "location" => "Kiti"];
}

mysqli_close($cxn);

$items = [];
$items["isdavejai"] = $resp;
$items["gavejai"] = $resp2;

$chartdata = [];
$chartdata["ChartData"] = $items;

echo json_encode($chartdata);

?>

