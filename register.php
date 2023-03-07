<?php
/* Program: Register.php
		---------------- Dabar nenaudojama ---------------
*/
$fields =array("naud_vardas"=> "vardas",
"naud_pavarde" => "pavardė",
"naud_email"=> "el. paštas",
"naud_passw"=> "slaptažodis",
"conf_passw"=> "pakartoti slaptažodį", 
"naud_telef" => "telefonas",
"naud_adr" => "adresas",
);


session_start();  //????????


if(@$_POST['Button'] =="Registruotis")
{

 /* Check for blanks */
foreach($_POST as $field => $value)
{

 if ($value == "")
 {

$blanks[] = $fields[$field];
 }
 else
 {
 $good_data[$field] = strip_tags(trim($value));   
 }

} // end foreach POST



if(isset($blanks))
{
 $message_2 = "Turi būti užpildyti šie laukai: ";
 foreach($blanks as $value)
 {
 $message_2 .="$value, ";
 }
 extract($good_data);			
 include("register_form.inc");
 exit();
} // end if blanks found




foreach($_POST as $field => $value)
{
 if(!empty($value))
 {



 if(preg_match("/email/i",$field))
 {
 if(!preg_match("/^.+@.+\\..+$/",$value))
 {
 $errors[] = "$value neteisingas el. pašto adresas. ";
 }
 }


 if(preg_match("/telef/i",$field) )
 {
 if(!preg_match("/^[0-9)( -+]{5,20}$/",$value))
 {
$errors[] = "$value neteisingas telefono numeris. ";
 }
 }


if(preg_match("/naud_passw/i",$field) )
 {
 if (!preg_match("/^(.+){6,10}$/",$value))   
 {
 $errors[] = "Slaptažodis $value negalimas. Jį turi sudaryti nuo 6 iki 10 simbolių. ";
 }
 }

//palyginti abu slaptazodzius, registration successful, 

 } // end if not empty1
 } // end foreach POST

//palyginame ar slaptazodis patvirtintas teisingai:
if (!($_POST['naud_passw']==$_POST['conf_passw']))
 {
 $errors[] = "Neteisingai patvirtintas slaptažodis. ";
 }


 foreach($_POST as $field => $value)
 {
 $$field = strip_tags(trim($value));      //-------pritaikyti ir kitose formose
 }

 if(@is_array($errors)) 
 {
 $message_2 = ""; 
 foreach($errors as $value)
 {
 $message_2 .= $value." Bandykite dar kartą.<br />";
 }
 include("register_form.inc"); 
 exit();
 } // end if errors are found 1



/* check to see if user name already exists */
 include("dbstuff.inc");
 $cxn = mysqli_connect($host,$user,$passwd,$dbname)
 or die("Nepavyko prisijungti prie duomenų bazės");

//

 $sql = "SELECT naud_email FROM naudotojai WHERE naud_email='$naud_email'";
 $result = mysqli_query($cxn,$sql)
or die("Query died: user_name.");
 $num = mysqli_num_rows($result); 

 if($num > 0) 
 {
 $message_2 = "$naud_email jau panaudotas. Prašom pasirinkti kitą el. pašto adresą.";
 include("register_form.inc");
 exit();
 
} 
else
{

 $today = date("Y-m-d");
 $sql = "INSERT INTO naudotojai (naud_vardas, naud_pavarde, naud_passw, naud_sukurimo_data, naud_email, naud_telef, naud_adr) VALUES
('$naud_vardas','$naud_pavarde',md5('$naud_passw'), '$today', '$naud_email','$naud_telef','$naud_adr')";
 mysqli_query($cxn,$sql);
 
$pareiskejas = $_POST['naud_vardas'].' '.$_POST['naud_pavarde'];   

 $_SESSION['pareiskejas'] = $pareiskejas;
 $_SESSION['auth']="yes";
$_SESSION['naud_email'] = $naud_email;
$_SESSION['grupe'] = "par";


 header("Location: naud_reg_ok.php");      
} // end else no errors found
 
}

else  

include("register_form.inc");


?>

