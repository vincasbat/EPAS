<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    exit();
}

include "dbstuff.inc";

($con = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$sql =
    "SELECT dok_id, dok_formos_kodas, mokestis, dok_kelias, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pareiskejas,  status_dabar, DATE(status_dabar_date) AS dab_statuso_data, ip, pastabos FROM dokai INNER JOIN naudotojai ON naudotojai.naud_email = dokai.naud_email WHERE status_dabar = 'Gautas'  ORDER BY status_dabar_date";

($result = mysqli_query($con, $sql)) or die("Error: " . mysqli_error($con));

if (!$result) {
    http_response_code(404);
    die(mysqli_error($con));
}

echo "[";
for ($i = 0; $i < mysqli_num_rows($result); $i++) {
    echo ($i > 0 ? "," : "") . json_encode(mysqli_fetch_object($result));
}
echo "]";

$con->close();

?>

