<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: nera_teisiu.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "admins":
        $naud_el_pastas = $_SESSION["naud_email"];
        $par = $_SESSION["pareiskejas"];
        $mess_pareiskejas = "<span style='color:green;'> $par </span>";
        break;

    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
}

//switch
?>




<!DOCTYPE html>
<html>
<head><title>Index įkėlimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">
<style type="text/css"  media="screen">
  <!--


-->   </style>

</head>
<body>
<div id="container">
   <div id="header">

<?php include "header.inc"; ?>

</div>

<div id="nav">



<a href='adm.php'>Administravimas</a><br />
<a href='ataskaitos.php'>Ataskaitos</a><br />
<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>
<?php if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
} ?>
<br /><br />

</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>Index failo įkėlimas</h4>



<?php if (isset($_POST["Upl"])) {
    $form_dir = "./uploaded_files";

    $file = $_FILES["forma"]["name"];

    $path = $form_dir . "/" . $file;
    $temp_file = $_FILES["forma"]["tmp_name"];
    $result = move_uploaded_file($temp_file, $path);

    echo "<p style='color:green'>Forma $path įkelta</p>\n";
} else {
    echo "<form enctype='multipart/form-data' action='{$_SERVER["PHP_SELF"]}'     method='POST'>\n";
    echo "<input type='hidden' name='MAX_FILE_SIZE' value='2097152' /> <input type='file' name='forma' size='60'  />\n";
    echo " <input type='submit' name='Upl'  value='Siųsti'  /></form>\n";
}
//-----------------------------------
?>

</div> <!-- content --> 


<div id='footer'>
<?php include "footer.inc"; ?>

</div>  <!-- footer -->
</div><!-- container --></body></html>
 
