<?php   
 

session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
case "par":   //gali matyti tik pareiskejai ir adminai
case "admins":
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;

default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch


		
if (isset($_POST['Atgal']))
{
$vk_msg = $_POST['VK_MSGa'];
$vk_amount  = $_POST['VK_AMOUNTa'];
$upl_dok_id = $_POST['DOK_IDa'];
}

else
{
$upl_dok_id = $_SESSION['upl_dok_id'];

}



 

include("moketi_form.inc");


?>
