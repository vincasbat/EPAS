<?php

session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "par": //gali matyti tik pareiskejai ir adminai
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

function len3($str)
{
    $sl = strlen($str);
    if ($sl == 0) {
        return "";
    }
    if ($sl > 0 && $sl < 10) {
        $ln = "00" . $sl;
    }
    if ($sl > 9 && $sl < 100) {
        $ln = "0" . $sl;
    }
    if ($sl > 99 && $sl < 1000) {
        $ln = "$sl";
    }
    return $ln;
}

$pid = "jhjhjhjhjhj";
$pid_w = iconv("UTF-8", "Windows-1257", $pid);

$vk_snd_id = "454545454"; // gal vpb kodas?
$vk_snd_id_w = iconv("UTF-8", "Windows-1257", $vk_snd_id);

$vk_return = "https://epaslaugos.vpb.lt/isbanko.php";

$vk_return_w = iconv("UTF-8", "Windows-1257", $vk_return);

$vk_amount = $_POST["VK_AMOUNT"];
$vk_amount_w = iconv("UTF-8", "Windows-1257", $vk_amount);

//$errors =  array();

//if ($vk_amount == '0')
if ($vk_amount < 0.01) {
    $errors[] = "Reikia nurodyti mokėjimo sumą! ";
}

$vk_msg = $_POST["VK_MSG"];
$vk_msg_w = iconv("UTF-8", "Windows-1257", $vk_msg);
//$vk_msg_w = strip_tags($vk_msg_w);

if (strlen($vk_msg) < 7) {
    $errors[] = "Reikia nurodyti mokėjimo paskirtį! ";
}

$dok_id = $_POST["DOK_ID"];
$dok_id_w = iconv("UTF-8", "Windows-1257", $dok_id);

if (!preg_match("/^[0-9 -]{3,6}$/", $dok_id)) {
    $errors[] = "Reikia nurodyti teisingą prašymo numerį! ";
}

$vk_name = "VALSTYBINĖ MOKESČIŲ INSPEKCIJA PRIE LR FM";
$vk_name_w = iconv("UTF-8", "Windows-1257", $vk_name);

$vk_pcode = "5310";
$vk_pcode_w = iconv("UTF-8", "Windows-1257", $vk_pcode);

$vk_req_date = date("Y.m.d H:i:s");
$vk_req_date_w = iconv("UTF-8", "Windows-1257", $vk_req_date);

$vk_version = "001";
$vk_version_w = iconv("UTF-8", "Windows-1257", $vk_version);

$_SESSION["upl_dok_id"] = $dok_id;

include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Nepavyko prisijungti prie duomenų bazės");
$sql = "SELECT naud_ak FROM naudotojai WHERE naud_email='$naud_el_pastas'";
($result = mysqli_query($cxn, $sql)) or die("Error: " . mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);
$ak = $row["naud_ak"];
//$vk_extra = $ak;
$vk_payer_code = $ak;
$vk_payer_code_w = iconv("UTF-8", "Windows-1257", $vk_payer_code);

mysqli_close($cxn);


$vk_extra = ""; 
$vk_extra_w = iconv("UTF-8", "Windows-1257", $vk_extra);

//$vk_mac = len3($pid).$pid.len3($vk_snd_id).$vk_snd_id.len3($vk_return).$vk_return.len3($vk_amount).$vk_amount.len3($vk_msg).$vk_msg.len3($vk_name).$vk_name.len3($vk_pcode).$vk_pcode.len3($vk_req_date).$vk_req_date.len3($vk_version).$vk_version.len3($vk_extra).$vk_extra;

$vk_mac =
    len3($pid_w) .
    $pid_w .
    len3($vk_snd_id_w) .
    $vk_snd_id_w .
    len3($vk_return_w) .
    $vk_return_w .
    len3($vk_amount_w) .
    $vk_amount_w .
    len3($vk_msg_w) .
    $vk_msg_w .
    len3($vk_name_w) .
    $vk_name_w .
    len3($vk_pcode_w) .
    $vk_pcode_w .
    len3($vk_req_date_w) .
    $vk_req_date_w .
    len3($vk_version_w) .
    $vk_version_w .
    len3($vk_extra_w) .
    $vk_extra_w .
    len3($vk_payer_code_w) .
    $vk_payer_code_w;

//echo $vk_mac;   //     test test test

$sh = $vk_mac;

$signature = "";
$fp = fopen("crt_mano/new_private_key.pem", "r");
$priv_key = fread($fp, 8192);
fclose($fp);
$pkeyid = openssl_get_privatekey($priv_key);
openssl_sign($sh, $signature, $pkeyid);
$signature = base64_encode($signature);
openssl_free_key($pkeyid);
$vk_mac = $signature;

include "moketi2_form.inc";

?>

