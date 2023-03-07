<?php 
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    //tik pareiškėjai gali matyti savo prašymus
    case "par":
    case "admins":
        $naud_el_pastas = $_SESSION["naud_email"];
        $par = $_SESSION["pareiskejas"];
        $mess_pareiskejas = "<span style='color:green;'> $par </span>";
        break;

    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
} //switch

$dok_id = $_POST["dok_id"];

$_SESSION["dok_id"] = $dok_id;
include "details_form_mano_prasymai.inc";

function getStatus()
{
    $formCode = [
        1 => "Gautas",
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
        "Visi",
    ];
    return $formCode;
}
?>

