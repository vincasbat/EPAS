<?php
session_start();
if (isset($_COOKIE[session_name()])) {
  setcookie(session_name(), '', time()-86400, '/');
  }

session_destroy();
?>
<!DOCTYPE html>
<html>
<head><title>Atsijungimas</title>
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

<?php
include("header.inc");
?>

</div>
<!--
<div id="nav">




<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p><br />
<br /><br />

</div>  nav --> 

<div id="content">
<p>Jūs atsijungėte nuo Valstybinio patentų biuro elektroninių paslaugų sistemos. Kad būtų užtikrintas saugumas, uždarykite naršyklės langą.</p>



</div> <!-- content --> 


<div id='footer'>
<?php
include("footer.inc");
?>

</div>  <!-- footer -->
</div>  <!-- container -->
</body></html>
 
