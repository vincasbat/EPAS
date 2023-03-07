<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: login.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
   				
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
<head><title>Naudotojų administravimas</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="./vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>


<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<style type="text/css"  media="screen">

#nav a  {
display: block;
}

th a:visited {
  color: white;
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

<script>
function confirmDelete(delUrl) {
  if (confirm("Ar tikrai ištrinti?")) {
    document.location = delUrl;
  }
}



</script>

</head>
<body>
<div id="container">
   <div id="header">

<?php
include("header.inc");
?>

</div>

<div id="nav">



<a href='adm.php'>Administravimas</a>
<a href='ataskaitos.php'>Ataskaitos</a>
<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>
<?php
if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}
?>
<br /><br />

</div> <!-- nav --> 

<div id="content">









<!-- vue --> 

<div id='vueapp'>

<h4 style='color: grey;'><b>Naudotojai ({{naudotojai.length}})</b></h4>

<p> <a href='register_adm.php'>Naujas naudotojas</a>  | <a href='register_grupe.php'>Nauja grupė</a><br /></p>

<p style='color:red;'>Dėmesio! Paspaudus <span style='font-weight: bold;'>Ištrinti</span> įrašas bus ištrintas iš duomenų bazės ir jo atkurti bus neįmanoma. Prieš ištrinant naudotoją pirmiausia turi būti ištrinti visi jo prašymai.</p>



<form>
  <div id="field">
<label for='naud'></label><input  type='text' v-model='search' autocomplete='off'  name='naud' size='30' value=''><img style='vertical-align:middle;' src='imgs/srch.png'/>
 ({{filteredNaud.length}})</div> 

</form>


 debug: sort={{currentSort}}, dir={{currentSortDir}}, search={{search}},  filtras.length={{filteredNaud.length}} <br>  
   

<table class='blueTable'><thead>
   <tr>
     <th><a href=# @click="sort('naudotojas')">Naudotojas</a></th>
     <th><a href=# @click="sort('data')">Data</a></th>
     <th><a href=# @click="sort('naud_email')">El. paštas</a>     </th>
     <th>Telefonas</th>
     <th><a href=# @click="sort('naud_grupe')">Grupė</a></th>
     <th></th>
     


   </tr></thead>    
    
<!--  <tr v-for='naud in naudotojai'> -->
<tr v-for="naud in sortedNaud"  v-if="naud.naudotojas.toUpperCase().indexOf(search.toUpperCase()) !== -1">
  
     <td> <a v-bind:href="'naud_edit.php?n_email='+naud.naud_email">{{naud.naudotojas }}</a>  </td>

     <td>{{naud.data }}</td>

    <td>{{naud.naud_email}}</td>
     
  <td>{{naud.naud_telef}}  </td>
     <td>{{ naud.naud_grupe }}</td>
     <td> <input style='color:red' type='button' value='Ištrinti' @click='deleteRecord(naud.naud_email)'></td>
    
   </tr>
 </table>
 </br>
</div>

 <script>
var app = new Vue({        
  el: '#vueapp',  		
 


  data: {
      
      naudotojai: [],
   
  currentSort:'naudotojas',
  currentSortDir:'asc',
  search: ''
  },
  mounted: function () {      //created
    this.getNaudotojai();
  },

  methods: {
    getNaudotojai: function(){
        axios.get('get_naudotojai.php')
        .then(function (response) {
            console.log(response.data);
            app.naudotojai = response.data;

        })
        .catch(function (error) {
            console.log(error);
        });
    },
   
deleteRecord: function(email){   if (confirm('Ar tikrai ištrinti?'))    document.location = 'naud_istrintas.php?n_email='+email;  },

sort:function(s) {
    //if s == current sort, reverse
    if(s === this.currentSort) {
      this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
    }
    this.currentSort = s;
  }

  },//methods

computed:{
  sortedNaud:function() {
    return this.naudotojai.sort((a,b) => {
      let modifier = 1;
      if(this.currentSortDir === 'desc') modifier = -1;
      if(a[this.currentSort] < b[this.currentSort]) return -1 * modifier;   
      if(a[this.currentSort] > b[this.currentSort]) return 1 * modifier;    
      return 0;
    });
  },

filteredNaud: function() {
    return this.naudotojai.filter((items) => {   
        if(String(items.naudotojas).toUpperCase().indexOf(this.search.toUpperCase()) !== -1) {
          return true
      }
      return false
    })
  }
 

}//computed




})   
</script>






</div> <!-- content --> 


<div id='footer'>
<?php
include("footer.inc");
?>

</div>  <!-- footer -->
</div>  <!-- container -->
</body></html>
 
