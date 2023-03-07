<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    exit();
}

include "dbstuff.inc";

($con = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$sql =
    "SELECT CONCAT(naud_vardas, ' ', naud_pavarde) AS naudotojas, DATE(naud_sukurimo_data) AS data, naud_email,  naud_telef, naud_adr, naud_ak, naud_grupe FROM naudotojai ORDER BY naud_pavarde ";

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

