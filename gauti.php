<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: login.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
case "pr":   //gali matyti tik priemimo skyrius ir adminai
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

?>


<!DOCTYPE html>
<html>
<head><title>Paraiškų priėmimo ir dokumentų valdymo skyrius</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<script src="./vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<!--   -->


<style>
#nav a  {
display: block;
}

 
table.blueTable {
  border: 0px solid #1C6EA4;
  background-color: #D0E4F5;
  width: 100%;
  text-align: left;
  border-collapse: collapse;
}
table.blueTable td, table.blueTable th {
  border: 0px solid #FFFFFF;
  padding: 3px 2px;
}
table.blueTable tbody td {
  font-size: 13px;
}
table.blueTable tr:nth-child(even) {
  background: #E6F1F5;
}


table.blueTable tbody tr {
   background: #D0E4F5;
}



table.blueTable thead {
  background: #1C6EA4;
  background: -moz-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  background: -webkit-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  background: linear-gradient(to bottom, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  border-bottom: 2px solid white;
}
table.blueTable thead th {
  font-size: 15px;
  font-weight: bold;
  color: #FFFFFF;
}
table.blueTable thead th:first-child {
  border-left: none;
}

table.blueTable tfoot td {
  font-size: 14px;
}
table.blueTable tfoot .links {
  text-align: right;
}
table.blueTable tfoot .links a{
  display: inline-block;
  background: #1C6EA4;
  color: #FFFFFF;
  padding: 2px 8px;
  border-radius: 5px;
}


</style>


</head>
<body>



<div id="container">
   <div id="header">

<?php      
include("header.inc");
echo "</div>\n";     


//išdėstymas
echo "<div id='nav'>\n";

echo "<a href='gauti.php'>Priėmimo skyrius</a>\n";
echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<a href='registras.php'>Registras </a>\n";
echo "<a href='regprasyma.php'>Registruoti prašymą </a>\n";
echo "<a href='isduoti.php'>Išduoti dokumentą</a>\n";
echo "<a href='isduoti2.php'>Be parašo</a>\n";
echo "<a href='atsiliepimaiadm.php'>Atsiliepimai</a>\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />";
echo "<a  href='naud_atsijungimas.php'>Atsijungti</a>\n";
}

?>

</div>
<div id='content'>
<h4>Paraiškų priėmimo ir dokumentų valdymo skyrius </h4>

<div id='vueapp'>

<p style='color: grey;'><b>Gauti prašymai ({{dokai.length}})</b></p>
<table class='blueTable'><thead>
   <tr>
     <th>Nr.</th>
     <th>PNO Nr.</th>
     <th>PDF</th>
     <th>ZIP/WORD</th>
     <th>Prašytojas</th>
     <th>Statusas</th>
     <th>Data</th>

   </tr></thead>    
   
   <tr v-for='dokas in dokai'>    
     <td>  <a v-bind:href="'details.php?dok_id=' + dokas.dok_id + '&st=gautas'">{{dokas.dok_id }}</a>  </td>

     <td>{{ dokas.ip }}</td>

    <td><div v-if="dokas.dok_kelias.length < 5"> <a v-bind:href="'pdf2.php?dokid=' + dokas.dok_id" v-bind:target="'_blank'">
        <img v-bind:src="'./imgs/pdf.png'"   v-on:click="marke" v-on:contextmenu="marke"/></a></div>
    </td>
     
  <td><div v-if="dokas.dok_kelias.length < 5"> <a v-bind:href="'word.php?dokid=' + dokas.dok_id">
     <img v-bind:src="'./imgs/word.png'"  v-on:click="marke" v-on:contextmenu="marke"/></a></div>
     <div v-else>
     <a v-bind:href="'dnld.php?dokid=' + dokas.dok_id"><img v-bind:src="'./imgs/zip.png'" 
  v-bind:title="dokas.pastabos" v-on:click="marke" v-on:contextmenu="marke" /></a></div>
  </td>
     <td>{{ dokas.pareiskejas }}</td>
     <td>{{ dokas.status_dabar }}</td>
     <td>{{ dokas.dab_statuso_data }}</td>
   </tr>
 </table>
 </br>
</div>

 <script>
var app = new Vue({  
  el: '#vueapp',  
     
  data: {
      
      dokai: []
  },
  mounted: function () {
    this.getDokai();
  },

  methods: {
    getDokai: function(){
        axios.get('get_dokai.php')
        .then(function (response) {
            console.log(response.data);
            app.dokai = response.data;

        })
        .catch(function (error) {
            console.log(error);
        });
    },
    marke : function (event) {
      event.target.style.backgroundColor = "green";
    }
    


  }//methods
})   
</script>

<?php


echo "</div><div id='footer'>\n";
include("footer.inc");


echo "</div></div>";

if ($_GET['mailerr'])
{
echo "<script type='text/javascript'>alert('Nepavyko išsiųsti el. laiško');</script>";
}


echo "</body></html>";
 ?>
