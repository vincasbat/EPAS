<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "admins":
    case "pr":
    case "pz":
    case "is":
    case "ap":
        $naud_el_pastas = $_SESSION["naud_email"];
        $par = $_SESSION["pareiskejas"];
        $mess_pareiskejas = "<span style='color:green;'> $par </span>";
        break;

    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
} //switch

if ($_GET["dok_id"]) {
    $dok_id = $_GET["dok_id"];
} else {
    $dok_id = $_POST["dok_id"];
}

$_SESSION["dok_id"] = $dok_id;
include "details_form_ataskaitos.inc";

function getStatus()
{
    $formCode = [
        1 => "Gautas",
        "PZ",
        "IS",
        "PZ OK",
        "IS OK",
        "PZ atmestas",
        "IS atmestas",
        "OK",
        "Atmestas",
        "Visi",
    ];
    return $formCode;
}
?>

