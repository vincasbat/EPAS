<?php

session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

$logfile = "./log.txt";
$IP = $_SERVER["REMOTE_ADDR"];
$logdetails = date("F j, Y, g:i a") . ": " . $_SERVER["REMOTE_ADDR"] . "\r\n";
($fp = fopen($logfile, "a")) or die("Unable to open log.txt!");
fwrite($fp, $logdetails);
fclose($fp);

$dok_id = $_GET["dokid"];
$file_names = [];

$failai = glob("./dnl/*");
foreach ($failai as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

include "dbstuff.inc";

($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Klaida! Nepavyko prisijungti prie duomenų bazės");

$query = "SELECT dok_kelias, naud_email, from_ip, pastabos, ip, DATE(status_dabar_date) AS dab_statuso_data FROM dokai WHERE dok_id = $dok_id";
($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$row = mysqli_fetch_assoc($result);
$kelias = $row["dok_kelias"];
$dab_statuso_data = $row["dab_statuso_data"];

$pratesimai = true;
if (strlen($kelias) > 5) {
    $pratesimai = false;
}

$kelias = "uploaded_files/" . $kelias;

array_push($file_names, $kelias);

$naud_email = $row["naud_email"];
$pastabos = $row["pastabos"];
$ip = $row["ip"];

$qr = "SELECT MAX(dok_id) as maks FROM dokai";
($rslt = mysqli_query($cxn, $qr)) or die("Error: " . mysqli_error($cxn));
$rowas = mysqli_fetch_assoc($rslt);
$maxid = $rowas["maks"];

$query_naudotojai = "SELECT naud_email, CONCAT(naud_vardas, ' ', naud_pavarde) AS pareiskejas, CONCAT(' ', naud_adr, '; tel. ', naud_telef, '; ', naud_org) AS par_duom FROM naudotojai WHERE naud_email = '$naud_email'";
($res = mysqli_query($cxn, $query_naudotojai)) or
    die("Error: " . mysqli_error($cxn));
$rw = mysqli_fetch_assoc($res); //tikrinti ar vienas įrašas
$naud = $rw["pareiskejas"];
$naud_duom = $rw["par_duom"];

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
    array_push($file_names, $visas_kelias);
}
// Kiti failai:----------------------------------------- pab

//mokesciai:
$dokai = null;
$mok = "";
unset($query);
$query = "SELECT dok_id, suma, paskirtis, banko_pranesimas, LEFT(mokejimo_data, 10) AS mok_data, moketojas FROM mokejimai WHERE naud_email='$naud_email' AND (dok_id = $dok_id) ORDER BY mok_id desc  LIMIT 0, 100 ";

($result = mysqli_query($cxn, $query)) or die("Error: " . mysqli_error($cxn));
$c = mysqli_num_rows($result);
//echo $c; die;

if ($c > 0) {
    $title = "";

    $n = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        foreach ($row as $field => $value) {
            $dokai[$n][$field] = $value;
        }
        $n++;
    }
    $n_dokai = sizeof($dokai);

    $mkdata = "";

    for ($i = 1; $i <= $n_dokai; $i++) {
        $mkdata = $dokai[$i]["mok_data"];
        $title = $dokai[$i]["moketojas"];
        $suma = $dokai[$i]["suma"];
        if ($dokai[$i]["mok_data"] < "2015.01.01") {
            $suma .= " LT";
        } else {
            $suma .= " EUR";
        }
        $mokejimai = "Mokėtojas: $title, suma: $suma\r\n";
        $mok_paskirtis =
            "Mokėjimo paskirtis: " . $dokai[$i]["paskirtis"] . "\r\n";
        $mok_data = "Mokėjimo data: " . $dokai[$i]["mok_data"] . "\r\n\r\n";
    } //for

    $mok = $mokejimai . $mok_paskirtis . $mok_data;
} //if c>0

//end mokesciai

if ($pratesimai) {
    $prasymai = [];
    $pratesimai = [];
    $pratesimai = explode(" ", $pastabos);
    $pratesimas = [];
    foreach ($pratesimai as $value) {
        $prat = [];
        $pratesimas = [];
        $prat = explode(":", $value);
        $pratesimas["pno"] = $prat[0];
        $pratesimas["metai"] = $prat[1];
        $pratesimas["suma"] = $prat[2];
        $pratesimas["info"] = $prat[3];
        array_push($prasymai, $pratesimas);
        unset($pratesimas);
        unset($prat);
    }

    $dokai = [];
    $dokai["pratesimai"] = $prasymai;

    $pareiskejas = [];
    $pareiskejas["name"] = $naud;
    $pareiskejas["email"] = $naud_email;
    $pareiskejas["address"] = $naud_duom;

    $dokai["pareiskejas"] = $pareiskejas;

    $dokid = [];
    $dokid["dokid"] = $dok_id;
    $dokid["maxdokid"] = $maxid;
    $dokid["data"] = $dab_statuso_data;

    $dokai["epas"] = $dokid;

    $mokejimas = [];
    $mokejimas["moketojas"] = $title;
    $mokejimas["suma"] = $suma;
    $mokejimas["data"] = str_replace(".", "-", $mkdata);

    $dokai["mokejimas"] = $mokejimas;

    $prasymas = [];
    $prasymas["prasymas"] = $dokai;

    mysqli_close($cxn);

    header("Content-type: application/json; charset=utf-8");

    //echo json_encode($prasymas, JSON_PRETTY_PRINT);
    echo json_encode($prasymas);
} else {
    // jei ne $pratesimai

    //pastabos:
    ($myfile = fopen("./dnl/pastabos.txt", "w")) or die("Unable to open file!");
    fwrite($myfile, $ip . "\r\n\r\n");
    fwrite($myfile, $naud . "      ");
    fwrite($myfile, $naud_duom . "\r\n\r\n");
    fwrite($myfile, $mok);
    fwrite($myfile, $pastabos);
    fclose($myfile);

    array_push($file_names, "./dnl/pastabos.txt");

    $archive_file_name = $dok_id . ".zip";

    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if (
        $zip->open("./dnl/" . $archive_file_name, ZIPARCHIVE::CREATE) !== true
    ) {
        exit("cannot open <$archive_file_name>\n");
    }

    foreach ($file_names as $files) {
        $zip->addFile("./" . $files, basename($files));
    }
    $zip->close();

    mysqli_close($cxn);

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: application/octet-stream");
    header(
        "Content-Disposition: attachment; filename=\"" .
            $archive_file_name .
            "\""
    );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . filesize("./dnl/" . $archive_file_name));
    ob_end_flush();
    @readfile("./dnl/" . $archive_file_name);
}

//else if $pratesimai
?>

 ?>
