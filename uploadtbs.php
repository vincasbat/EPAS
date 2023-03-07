<?php
  
   $destination_path = getcwd().DIRECTORY_SEPARATOR."dtbs".DIRECTORY_SEPARATOR;

   $result = 0;
   $failovardas = $_POST['failo_vardas']; // .'.pdf'
  $target_path = $destination_path . $failovardas;

   if(@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
      $result = 1;
   }
   
   sleep(1);
?>
<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo $result; ?>);</script>
