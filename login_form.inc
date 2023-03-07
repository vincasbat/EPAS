<!DOCTYPE html>
<html>
<head><title>Prisijungimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<script>
function setFocus(){
    document.getElementById("naud_email").focus();
}
</script>


 </head>
<body  onload='setFocus();'>
<div id="container">
   <div id="header">
<?php
include "header.inc";
echo "</div><div id='nav'>\n";

//echo "<a href='index.php'>Į pradžią</a><br />\n";
echo "<br />\n";
echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p><br />\n";
echo "</div><div id='content'>\n"; //išdėstymas

$fields_1 = ["naud_email" => "El. paštas", "naud_passw" => "Slaptažodis"];
?>
 


<h4>Valstybinio patentų biuro elektroninės paslaugos</h4> 


<p>Prisijunkite naudodamiesi savo el. pašto adresu ir slaptažodžiu.</p>
 
<form action=<?php echo $_SERVER["PHP_SELF"]; ?>

method="POST">
<fieldset style='border: 1px solid #000000; border-color: grey;'>
 <legend>Prisijungimas</legend>

<br />
<?php
if (isset($message_1)) {
    echo "<p class='errors'>$message_1</p>\n";
}
foreach ($fields_1 as $field => $value) {
    if (preg_match("/pass/i", $field)) {
        $type = "password";
    } else {
        $type = "text";
    }
    echo "<div id='field'>
    <label for='$field'>$value</label>
    <input id='$field' name='$field' type='$type'
    value='" .
        @$$field .
        "' size='30' maxlength='50' />
    </div>\n";
}
?> 

 <p><input type="submit" name="Button"
style='margin-left: 18em;' 
value="Prisijungti"></p>

</fieldset>
 </form>


<p>Iškilus problemoms prašom kreiptis į VPB IT skyriaus darbuotojus. </p>


<?php
echo "</div>  <div id='footer'>\n";
include "footer.inc";
?>
 </div>
</div>
</body></html>

