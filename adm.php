<?php

session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: login.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    //Palikti galutiniam variante tik adminus
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
<head><title>Administravimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">

<style type="text/css"  media="screen">
  
#nav a  {
display: block;
}
</style>  

</head>
<body>
<div id="container">
   <div id="header">

<?php include "header.inc"; ?>

</div>

<div id="nav">

<a href='adm.php'>Administravimas</a>
<a href='ataskaitos.php'>Ataskaitos</a>


<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>
<?php if (isset($mess_pareiskejas)) {
    echo "$mess_pareiskejas<br />";
    echo "<a href='naud_atsijungimas.php'>Atsijungti</a>\n";
} ?>


</div> <!-- nav --> 

<div id="content">
<h4 style='color: grey'>El. paslaugų administravimas</h4>
<a href='naud_adm.php'>Naudotojų administravimas</a>
<br />
<a href='db_adm.php'>Duomenų bazės administravimas</a>
<br />
<a href='mokejimai.php'>Mokėjimai</a>




</div> <!-- content --> 


<div id='footer'>
<?php include "footer.inc"; ?>

</div>  <!-- footer -->
</div>  <!-- container --></body></html>
 
