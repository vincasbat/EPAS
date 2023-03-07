<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

include "dbstuff.inc";

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

switch ($_GET["per"]) {
    case "sm":
        $query =
            "SELECT count(*) as prasymai, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pareiskejas   FROM dokai INNER JOIN naudotojai ON dokai.naud_email = naudotojai.naud_email WHERE dokai.status_dabar = 'Gautas' AND dokai.status_dabar_date >= (SELECT DATE_FORMAT(NOW() ,'%Y-%m-01')) GROUP BY pareiskejas  ORDER BY prasymai desc"; // date > 1 men.d.

        break;
    case "pm":
        $query =
            "SELECT count(*) as prasymai, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pareiskejas   FROM dokai INNER JOIN naudotojai ON dokai.naud_email = naudotojai.naud_email WHERE dokai.status_dabar = 'Gautas' AND dokai.status_dabar_date <  DATE_FORMAT(NOW() ,'%Y-%m-01') AND dokai.status_dabar_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m-01')  GROUP BY pareiskejas  ORDER BY prasymai desc"; // date > 1 men.d.
        break;
    case "pm3":
        $query =
            "SELECT count(*) as prasymai, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pareiskejas   FROM dokai INNER JOIN naudotojai ON dokai.naud_email = naudotojai.naud_email WHERE dokai.status_dabar = 'Gautas'  AND dokai.status_dabar_date <  DATE_FORMAT(NOW() ,'%Y-%m-01') AND dokai.status_dabar_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 3 MONTH, '%Y-%m-01') GROUP BY pareiskejas  ORDER BY prasymai desc"; // date > 1 men.d.

        break;
    default:
        $query =
            "SELECT count(*) as prasymai, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pareiskejas   FROM dokai INNER JOIN naudotojai ON dokai.naud_email = naudotojai.naud_email WHERE dokai.status_dabar = 'Gautas' AND dokai.status_dabar_date >= (SELECT DATE_FORMAT(NOW() ,'%Y-%m-01')) GROUP BY pareiskejas  ORDER BY prasymai desc"; // date > 1 men.d.
} // switch

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
        $kiti = $kiti + intval($dokai[$i]["prasymai"]);
    } else {
        $resp[$count] = [
            "donors" => intval($dokai[$i]["prasymai"]),
            "location" => $dokai[$i]["pareiskejas"],
        ];
    }
    $count++;
} //for
if ($count > 5) {
    $resp[6] = ["donors" => $kiti, "location" => "Kiti"];
}

//echo "<pre>", print_r($resp), "</pre>";   die;

mysqli_close($cxn);

$items = [];
$items["items"] = $resp;

$chartdata = [];
$chartdata["ChartData"] = $items;

echo json_encode($chartdata);

?>

