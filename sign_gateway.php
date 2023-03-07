<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
require_once "./config.php";
require_once "./lib.php";
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: ./login.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "is":
    case "pz":
    case "ap":
    case "pr":
    case "admins":
        $naud_el_pastas = $_SESSION["naud_email"];
        $par = $_SESSION["pareiskejas"];
        $mess_pareiskejas = "<span style='color:green;'> $par </span>";
        break;

    default:
        header("Location: ../nera_teisiu.php");
        exit();
        break;
}

//switch
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <script type="text/javascript" src="./jquery-1.11.1.min.js"></script>
    <!--<script type="text/javascript" src="./deployJavaNoredirect.js"></script>-->
    <link href="./style/styles.css" rel="stylesheet" type="text/css"/>

<style>
#nav a  {
display: block;
}
</style>


</head>
<body>
<div id="container">
   <div id="header">
<?php
include "./header.inc";
echo "</div><div id='nav'>\n";

$prasymas = explode("_", $_GET["file_name"]);
$prasymas = $prasymas[0];

$annot = intval($_GET["annot"]);
if (is_numeric($annot)) {
    if ($annot < 1) {
        $annot = 1;
    }
    if ($annot > 100) {
        $annot = 1;
    }
} else {
    $annot = 1;
}

$formatas = $_GET["formatas"];

$vpbinic = false;
if (isset($_GET["vpbinic"]) && $_GET["vpbinic"] == "taip") {
    $vpbinic = true;
}

switch (@$_SESSION["grupe"]) {
    case "admins":
        echo "<a href='./ataskaitos.php'>Ataskaitos</a>\n";
        //                       ?????????
        break;
    case "pr":
        echo "<a href='./gauti.php'>Priėmimo skyrius</a><br />\n"; //meniu
        if (!$vpbinic) {
            echo "<a href='./details.php?dok_id=$prasymas&st=gautas'>Prašymas Nr. $prasymas</a>\n";
        }
        break;
    case "pz":
        echo "<a href='./pz.php'>Prekių ženklų skyrius</a><br />\n"; //meniu
        if (!$vpbinic) {
            echo "<a href='./details.php?dok_id=$prasymas&st=pz'>Prašymas Nr. $prasymas</a>\n";
        }
        break;
    case "is":
        echo "<a href='./is.php'>Išradimų skyrius</a><br />\n"; //meniu
        if (!$vpbinic) {
            echo "<a href='./details.php?dok_id=$prasymas&st=is'>Prašymas Nr. $prasymas</a>\n";
        }
        break;
    case "ap":
        echo "<a href='./ap.php'>Apeliacinis skyrius</a><br />\n"; //meniu
        if (!$vpbinic) {
            echo "<a href='./details.php?dok_id=$prasymas&st=ap'>Prašymas Nr. $prasymas</a>\n";
        }
        break;
    default:
        header("Location: ./nera_teisiu.php");
        exit();
        break;
} //switch

if ($vpbinic) {
    echo "<a href='./registras.php'>Registras</a>\n";
}

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />\n";
    echo "<a href='./naud_atsijungimas.php'>Atsijungti</a><br />\n";
}

echo "<br /><br /><br /><br /><br />\n";

echo "</div><div id='content'>\n";

//Pasirašymas:

$file["name"] = $_GET["file_name"];
$path = "./dtbs/" . $_GET["file_name"];
$file["digest"] = sha1_file($path);
$file["url"] = "https://epaslaugos.vpb.lt/dtbs/" . $_GET["file_name"];

$file["name"] = $_GET["file_name"]; 

$array = [
    "file" => $file,
];

$action = "upload";
//$uploadResponse = request(getApiUrlByAction($action), ['file' => $file], REQUEST_POST);
$uploadResponse = request(getApiUrlByAction($action), $array, REQUEST_POST);

if ($uploadResponse["status"] != "ok") {
    echo "File could not be uploaded. Please ensure that file URL is accessible from the internet:<br />";
    echo $file["url"];
    exit();
}
$action = "upload/status/" . $uploadResponse["token"];
$statusResponse = "";

$token_uploadResponse = [
    "token" => $uploadResponse["token"],
];
while ($statusResponse === "" || $statusResponse["status"] == "pending") {
    $statusResponse = request(
        getApiUrlByAction($action),
        $token_uploadResponse,
        REQUEST_GET
    );
    sleep(2);
}

if (empty($statusResponse) || $statusResponse["status"] != "uploaded") {
    echo "Gateway API could not download the file. Please ensure that file URL is accessible from the internet." .
        '\n';
    exit();
}

unset($file);
//echo "creating signing <br>";

//$signers = [];
//$files = [];
$signers = [];
$files = [];

$file["token"] = $uploadResponse["token"];
array_push($files, $file);

$f = explode(".", $_GET["file_name"]);
$signingName = $f[0];

$signerUID = $naud_el_pastas;
$signer["id"] = $signerUID;

$pieces = explode(" ", $par);

//Registravimas:
//file_name=1596_5720_20150404_ISR_IS.pdf
$filename = $_GET["file_name"];
$fv = explode("_", $filename);
$prasymas = $fv[0];
$mas = explode(".", $fv[4]);
$skyrius = $mas[0];
$extention = $mas[1]; //pdf, adoc,....
$pno = $fv[1];
$data = $fv[2];
$doktipas = $fv[3]; //PAZ, KIT, LIUD, ISR
include "dbstuff.inc";
($cxn = mysqli_connect($host, $user, $passwd, $dbname)) or
    die("Nepavyko prisijungti prie duomenų bazės");
$sql = "SELECT MAX(reg_ai) AS maks FROM `siunc_registras`";
$result = mysqli_query($cxn, $sql);
$max = 0;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $max = $row["maks"];
    }
} else {
    echo "<br /><br />Nerasta maks duomenų bazėje<br /><br />" . '\n';
    exit();
}
$max++;
if ($max < 100000) {
    $maxz = "" . $max;
}
if ($max < 10000) {
    $maxz = "0" . $max;
}
if ($max < 1000) {
    $maxz = "00" . $max;
}
if ($max < 100) {
    $maxz = "000" . $max;
}
if ($max < 10) {
    $maxz = "0000" . $max;
}
$reg_nr = "EPAS-" . $skyrius . "-" . $maxz;

//---- cia perkelta end

$action = "signing/create";
//***********************************  PDFLT
$reg_data = date("Y-m-d\TH:i:s");

if ($formatas == "pdflt") {
    $arr = [
        "pdflt" => [
            "level" => "pades-t",

            "annotation" => ["page" => $annot],
        ],
        "postback_url" => $postbackUrl,
        "type" => "pdflt",
        "name" => "Dokumento pasirašymas",
        "files" => [
            [
                "token" => $uploadResponse["token"],
            ],
        ],

        "signers" => [
            [
                "id" => $naud_el_pastas,
                "name" => $pieces[0],

                "surname" => end($pieces),
                "phone" => "",
                "code" => "",
                "position" => "Ekspertas",
                "signing_purpose" => "signature",
                "signing_location" => "Vilnius",
                "pdflt" => [
                    "reason" => "Registracijos Nr. " . $reg_nr,
                    "registration" => [
                        "date" => $reg_data . "Z",
                        "number" => $reg_nr,
                    ],

                    "annotation" =>
                        $annot > 1
                            ? [
                                "text" =>
                                    "Išrašą patvirtino: " .
                                    $par .
                                    '\nData: ' .
                                    $reg_data .
                                    '\nRegistracijos Nr. ' .
                                    $reg_nr,
                                "page" => $annot,
                            ]
                            : null,
                ], //signers[0]
            ], //signers
        ], // pdflt
    ]; //$arr
} else {
    //  ------------- PDF
    $arr = [
        "pdf" => [
            "annotation" => ["page" => $annot],
        ],

        "postback_url" => $postbackUrl,
        "type" => "pdf",
        "name" => "Dokumento pasirašymas",
        "files" => [
            [
                "token" => $uploadResponse["token"],
            ],
        ],
        "signers" => [
            [
                "id" => $naud_el_pastas,
                "name" => $pieces[0],
                "surname" => end($pieces),
                "phone" => "",
                "code" => "",
                "position" => "Ekspertas",
                "signing_purpose" => "signature",
                "signing_location" => "Vilnius",
                "pdf" => [
                    "reason" => "Registracijos Nr. " . $reg_nr,
                ],
            ],
        ],
    ];

    //  -----  END PDF
}

$createResponse = request(getApiUrlByAction($action), $arr, REQUEST_POST);

if ($createResponse["status"] != "ok") {
    echo "Pasirašymas negalimas. <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
    exit();
}

$signingUrl =
    trim($apiUrl, "/") .
    "/signing/" .
    $createResponse["token"] .
    "?access_token=" .
    $createResponse["signers"][$signerUID];
//echo $signingUrl;

//is cia perkelta

$signingToken = $createResponse["token"];
if ($vpbinic) {
    $email = $prasymas;
    $prasymas = "";
} else {
    //kam isduodama:
    $sql = "SELECT naud_vardas, naud_pavarde, naudotojai.naud_email FROM naudotojai, dokai WHERE dok_id = '$prasymas' and naudotojai.naud_email = dokai.naud_email";
    $result = mysqli_query($cxn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $email = $row["naud_email"];
        }
    } else {
        echo "Nerastas prasytojo el pastas." . '\n';
    }
}
$sql = "INSERT INTO `siunc_registras`(`reg_nr`, `data`, `adresatas`, `dokumentas`, `kelias`, `dok_id`, `pno`, `isdave`, `token`) VALUES ('$reg_nr','$data','$email','$doktipas','$filename','$prasymas','$pno', '$naud_el_pastas', '$signingToken')";
//echo $sql;
mysqli_query($cxn, $sql) or die("Error: " . mysqli_error($cxn));
mysqli_close($cxn);

echo "<iframe src='$signingUrl' height='1000' width='800' style='border:0px'>\n";
echo "<p>Your browser does not support iframes.</p>\n";
echo "</iframe>'\n";

echo "</div><div id='footer'>\n";
include "./footer.inc";
echo "</div></div>";
?>
</body>
</html>
