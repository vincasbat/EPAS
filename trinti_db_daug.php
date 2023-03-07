<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }
  
   
  // for($i = 0; $i < $selected_count; $i++){}
    if(isset($_POST['delete'])) {
        
        $selected = $_POST['chkbox']; 
        $selected_count = 0;
        $selected_count = sizeof($selected);
        
        include("dbstuff.inc");
        include("stuff.inc");
        $cxn = mysqli_connect($host,$user,$passwd,$dbname)
        or die("Klaida! Nepavyko prisijungti prie duomenų bazės");
       

if($selected_count>0) {
       foreach ($selected as $dok_id) {

   
   $query = "SELECT dok_kelias FROM dokai WHERE dok_id = $dok_id";  //$pv tb kabutese
   $result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
   $rw = mysqli_fetch_assoc($result);
   $dok_kelias = $rw['dok_kelias'];
   $visas_kelias = $dest.$dok_kelias;
 //  echo $visas_kelias."<br />Kiti failai:<br />"; // der
   
   $query = "DELETE FROM dokai WHERE dok_id = $dok_id"; 
   $result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
   
   $query = "DELETE FROM dok_statusai WHERE dok_id = $dok_id"; 
   $result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
   
   $fh = fopen($visas_kelias, 'w') or die("Negalima atidaryti failo");
   fclose($fh);    
   unlink("$visas_kelias");   
   
   
   // kiti failai:    ---------------------------------------------------------------------------------------------------- pr
   //Į masyvą įrašome trinamų failų id ir kelia:
   $query = "SELECT file_id, dok_kelias FROM kiti_failai WHERE dok_id = $dok_id";
   
   $result = mysqli_query($cxn, $query) or die ("Klaida: ".mysqli_error($cxn));
   $c = mysqli_num_rows($result);   // irasu skaicius
   $n=1;
   while($row = mysqli_fetch_assoc($result)) 
   {
   $trinami_dokai[$n]['fid']=$row['file_id'];
   $trinami_dokai[$n]['dkelias']=$row['dok_kelias']; 
  // echo "Failo Nr. ", $trinami_dokai[$n]['fid'], "<br />";
    $n++;
   }
   
   $t_dokai = sizeof($trinami_dokai);
   for ($i=1;$i<=$t_dokai;$i++)
   {
   $file_id = $trinami_dokai[$i]['fid'];
   
   $dok_kelias = $trinami_dokai[$i]['dkelias'];
   $visas_kelias = $dest.$dok_kelias;
   //echo $visas_kelias.' <br />'; // der
   $fh = fopen($visas_kelias, 'w') or die("Negalima atidaryti failo");
   fclose($fh);    
   unlink("$visas_kelias");   
   } // for trinami visi dokai ir failai
   
   $query = "DELETE FROM kiti_failai WHERE dok_id = $dok_id"; 
   $result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
   // kiti failai: --------------------------------------------- pab
}//foreach
}// if count > 0
}//if issset post delete
   mysqli_close($cxn);
   
   
   header("Location: db_adm.php?count=$selected_count");

   ?>   