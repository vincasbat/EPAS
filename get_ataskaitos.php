<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    exit();
}

include "dbstuff.inc";

($con = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $start_week = $_GET["start"];
        $end_week = $_GET["end"];

        $sql = "SELECT COUNT(*) AS isduota FROM siunc_registras WHERE data >= '$start_week' AND data <= '$end_week' ";
        break;
    case "POST":
        break;
}

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

