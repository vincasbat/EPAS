<?php
session_start();
if (@$_SESSION["auth"] != "yes") {
    header("Location: login.php");
    exit();
}

switch (@$_SESSION["grupe"]) {
    case "admins":
    case "pr":
    case "pz":
    case "is":
    case "ap":
        $naud_el_pastas = $_SESSION["naud_email"];
        $par = $_SESSION["pareiskejas"];
        $mess_pareiskejas = "<span style='color:green;'> $par </span>";
        break;

    default:
        header("Location: nera_teisiu.php");
        exit();
        break;
} //switch

?>


<!DOCTYPE html>
<html>
<head><title>Paraiškų priėmimo ir dokumentų valdymo skyrius</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<script src="./vue.js"></script>

 <script src="daypilot/daypilot-all.min.js" type="text/javascript"></script>
 <script type="text/javascript" src="./jquery-1.11.1.min.js"></script>



<style type="text/css">

table.blueTable {
  border: 0px solid #1C6EA4;
  background-color: #D0E4F5;
  width: 80%;
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







#nav a  {
display: block;
}
</style>



<script>




function main() {
var savaite = dates(new Date());
var localeLT = new DayPilot.Locale(  
"lt-lt",
{
dayNames:["Sekmadienis","Pirmadienis","Antradienis","Trečiadienis","Ketvirtadienis","Penktadienis","Šeštadienis"],
//dayNames: "Sekmadienis_Pirmadienis_Antradienis_Trečiadienis_Ketvirtadienis_Penktadienis_Šeštadienis".split("_"),
dayNamesShort: "Sk_Pr_An_Tr_Kt_Pn_Št".split("_"),
monthNames: "Sausis_Vasaris_Kovas_Balandis_Gegužė_Birželis_Liepa_Rupgjūtis_Rugsėjis_Spalis_Lapkritis_Gruodis".split("_"),
monthNamesShort: "Sau_Vas_Kov_Bal_Geg_Bir_Lie_Rgp_Rgs_Spa_Lap_Gru".split("_"),
timePattern: "hh:mm:tt",
datePattern: "yyyy-MM-dd",
dateTimePattern:  "yyyy-MM-dd HH:mm",
timeFormat: "Clock24Hours",
weekStarts: 1
}
);

DayPilot.Locale.register(localeLT); 



var nav = new DayPilot.Navigator("nav2");
  nav.showMonths = 2;
  nav.skipMonths = 2;
  nav.selectMode = "week";
  //nav.orientation  = "horizontal";
nav.locale =  "lt-lt";
  nav.init();

nav.onTimeRangeSelected = function(args) {        // last_year();        https://api.daypilot.org/daypilot-navigator-onvisiblerangechanged/
var data = args.day.toString().split("T")[0]; 
var savaite = dates(new Date(data)); 
//getIsduotus(savaite[0], savaite[6], "#week");
loadData('get_registras.php?start=' + savaite[0] + '&end=' + savaite[6],  document.getElementById("can_gav_sav"), document.getElementById("can_isd_sav"));
var ln = "get_ataskaitos.php?start=" + savaite[0] + "&end=" + savaite[6];
$.get(ln, function(data, status){
 var ob = JSON.parse(data);
$("#sav_data").text("Savaitė " + savaite[0] +":::" + savaite[6]);
$("#sav_count").text("Per savaitę išduota: " + ob[0].isduota);
});


var men = month_first_last(new Date(data));
//getIsduotus(men[0], men[1], "#month");
loadData('get_registras.php?start=' + men[0] + '&end=' + men[1],  document.getElementById("can_gav_men"), document.getElementById("can_isd_men"));
ln = "get_ataskaitos.php?start=" + men[0] + "&end=" + men[1];
$.get(ln, function(data, status){
 var ob = JSON.parse(data);
$("#men_data").text("Mėnuo " + men[0] +":::" + men[1]);
$("#men_count").text("Per mėnesį išduota: " + ob[0].isduota);
});
 

  };

var savaite = dates(new Date()); 		//ši savaitė
//getIsduotus(savaite[0], savaite[6], "#week");
loadData('get_registras.php?start=' + savaite[0] + '&end=' + savaite[6],  document.getElementById("can_gav_sav"), document.getElementById("can_isd_sav"));
var ln = "get_ataskaitos.php?start=" + savaite[0] + "&end=" + savaite[6];
$.get(ln, function(data, status){
 var ob = JSON.parse(data);
$("#sav_data").text("Savaitė " + savaite[0] +":::" + savaite[6]);
$("#sav_count").text("Per savaitę išduota: " + ob[0].isduota);
});



var men = month_first_last(new Date());   //šis mėnuo
//getIsduotus(men[0], men[1], "#month");
loadData('get_registras.php?start=' + men[0] + '&end=' + men[1],  document.getElementById("can_gav_men"), document.getElementById("can_isd_men"));
ln = "get_ataskaitos.php?start=" + men[0] + "&end=" + men[1];
$.get(ln, function(data, status){
 var ob = JSON.parse(data);
$("#men_data").text("Mėnuo " + men[0] +":::" + men[1]);
$("#men_count").text("Per mėnesį išduota: " + ob[0].isduota);
});




getIsduotus(last_year()[0], last_year()[1], "#last");   //praėjusiais metais išduoti dokumentai

var data = new Date();
var year = data.getFullYear();
var first = new Date(year, 0, 2).toISOString().slice(0, 10);  
var now = data.toISOString().slice(0, 10);  //alert(now);
getIsduotus(first, now, "#year");			//šiais metais išduoti dokumentai
}//main


//main end


function getIsduotus (start, end, el) {
var ln = "get_ataskaitos.php?start=" + start + "&end=" + end;
$.get(ln, function(data, status){
  var ob = JSON.parse(data);
$(el).text("Nuo " + start + " iki " + end + " išduota dokumentų: " + ob[0].isduota);
});
}//end func




function month_first_last(date) {
   var first_last = new Array();
var year = date.getFullYear();
var month = date.getMonth();
var firstDay = new Date(year, month, 2);
var lastDay = new Date(year, month + 1, 1);
first_last.push(new Date(firstDay).toISOString().slice(0, 10));
first_last.push(new Date(lastDay).toISOString().slice(0, 10));
return first_last;
}//month first last

function last_year() {
  var last = new Array();
  var date = new Date();
  var last_yr = date.getFullYear() - 1;
  var firstd = new Date(last_yr, 0, 2);
  var lastd = new Date(last_yr, 11, 32);
  last.push(new Date(firstd).toISOString().slice(0, 10));
   last.push(new Date(lastd).toISOString().slice(0, 10));
//alert(last[0]); alert(last[1]);
   return last;
}


function dates(current) {
    var week= new Array(); 
    var savd = current.getDay();
 
    if (savd==0) savd = 7;
  
    current.setDate((current.getDate() - savd +1));
    for (var i = 0; i < 7; i++) {
        week.push(
            new Date(current).toISOString().slice(0, 10)
        ); 
        current.setDate(current.getDate() +1);
    }
    return week; 
}//dates

var isd;

function openWin( windowURL, windowName, windowFeatures ) { 
		return window.open( windowURL, windowName, windowFeatures ) ; 
	} 


</script>



</head>
<body onload='main()'>



<div id="container">
   <div id="header">

<?php      
include("header.inc");
echo "</div>\n";     


//išdėstymas
echo "<div id='nav'>\n";


switch (@$_SESSION['grupe'])
{
case "admins":
echo "<a href='gauti.php'>Priėmimo skyrius</a>\n";
echo "<a href='adm.php'>Administravimas</a>\n";
break;
case "pr":
echo "<a href='gauti.php'>Priėmimo skyrius</a>\n";  //meniu

break;
case "pz":
echo "<a href='pz.php'>Prekių ženklų skyrius</a>\n";  //meniu

break;
case "is":
echo "<a href='is.php'>Išradimų skyrius</a>\n";  //meniu

break;
case "ap":
echo "<a href='ap.php'>Apeliacinis skyrius</a>\n";  //meniu

break;
default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch


echo "<a href='ataskaitos.php'>Ataskaitos</a>\n";
echo "<a href='registras.php'>Registras </a>\n";
echo "<a href='regprasyma.php'>Registruoti prašymą </a>\n";
echo "<a href='isduoti.php'>Išduoti dokumentą</a>\n";
echo "<a href='isduoti2.php'>Be parašo</a>\n";


echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";


if(isset($mess_pareiskejas))
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";


?>

</div>
<div id='content'>
<h4>Ataskaitos. Išduoti dokumentai</h4><br> 



  



<div style="float:left; width: 160px;">
    <div id="nav2"></div>
  
</div>

<div style="margin-left: 160px;">
  <div id="rez"> </div>
</div>







<table class="blueTable">
<thead>
  <tr>
    <th ></th>
    <th  id="sav_data">Savaitė</th>
    <th  id="men_data">Mėnuo</th>
  </tr>
</thead>
  <tr>
    <td >      </td>
    <td >
						<canvas id="can_isd_sav" width="280" height="160">
							Your browser does not support HTML5 Canvas.
						</canvas>
    </td>
    <td >
						<canvas id="can_isd_men" width="280" height="160">
							Your browser does not support HTML5 Canvas.
						</canvas>

    </td>
  </tr>
 
  <tr>
    <td>      </td>
    <td>
						<canvas id="can_gav_sav" width="280" height="160">
							Your browser does not support HTML5 Canvas.
						</canvas>
    </td>
    <td>
						<canvas id="can_gav_men" width="280" height="160">
							Your browser does not support HTML5 Canvas.
						</canvas>
    </td>
  </tr>
<tr>
    <td></td>
    <td  id="sav_count">Viso:</td>
    <td  id="men_count">Viso:</td>
  </tr>
</table>
<br>
<p id="year">  </p>
  <p id="last">  </p>
 
<h4>Gauti prašymai</h4>



 <script>

})   
</script>

<?php


echo "<p><a href=".'"'."JavaScript: newWindow = openWin('./visi_mokejimai.php', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()".'"'."> Naujausi mokėjimai</a> | \n";

echo "<a href=".'"'."JavaScript: newWindow = openWin('./sm.php', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()".'"'.">Šis mėnuo</a> | \n";

echo " <a href=".'"'."JavaScript: newWindow = openWin('./pm.php', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()".'"'.">Praėjęs mėnuo</a> | \n";

echo " <a href=".'"'."JavaScript: newWindow = openWin('./pm3.php', 'didelis', 'width=750,height=600,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1'); newWindow.focus()".'"'.">Praėję 3 mėnesiai</a></p>\n";





echo "</div><div id='footer'>\n";
include("footer.inc");


echo "</div>";

if ($_GET['mailerr'])
{
echo "<script type='text/javascript'>alert('Nepavyko išsiųsti el. laiško');</script>";
}
?>

<script type="text/javascript">

	function drawPieChart (canvas, chartData, centerX, centerY, pieRadius) {
			var ctx;  // The context of canvas
			var previousStop = 0;  // The end position of the slice
			var totalDonors = 0;
			
			var totalCities = chartData.length;
			
           
			for (var i = 0; i < totalCities; i++) {
					totalDonors += chartData[i].donors;
			}

			ctx = canvas.getContext("2d");
			ctx.clearRect(0, 0, canvas.width, canvas.height);

		    var colorScheme = ["#2F69BF", "#A2BF2F", "#BF5A2F", 
		                       "#BFA22F", "#772FBF", "#2F94BF", "#c3d4db"];

							   for (var i = 0; i < totalCities; i++) {
				
				//draw the sector
				ctx.fillStyle = colorScheme[i];
				ctx.beginPath();
				ctx.moveTo(centerX, centerY);
				ctx.arc(centerX, centerY, pieRadius, previousStop, previousStop + 
					(Math.PI * 2 * (chartData[i].donors / totalDonors)), false);
				ctx.lineTo(centerX, centerY);
				ctx.fill();
				
				// label's bullet
				var labelY = 20 * i + 10;
				var labelX = pieRadius*2 + 20;
				
				ctx.rect(labelX, labelY, 10, 10);
				ctx.fillStyle = colorScheme[i];
        		ctx.fill();
        		
        		// label's text
				ctx.font = "italic 12px sans-serif";
				ctx.fillStyle = "#222";
				var txt = chartData[i].location + " | " + chartData[i].donors;
				ctx.fillText (txt, labelX + 18, labelY + 8);
				
				previousStop += Math.PI * 2 * (chartData[i].donors / totalDonors);
			}
		}
			
		function loadData(dataUrl, canvas, canvas2) {
			var xhr = new XMLHttpRequest();
			xhr.open('GET', dataUrl, true);

			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4) {
                   if ((xhr.status >= 200 && xhr.status < 300) || 
                                             xhr.status === 304) {
						var jsonData = xhr.responseText;

						var chartData = JSON.parse(jsonData).ChartData;

						drawPieChart(canvas,chartData.gavejai, 50, 50, 49);
						drawPieChart(canvas2,chartData.isdavejai, 50, 50, 49);
						
					} else {
						console.log(xhr.statusText);
						tempContainer.innerHTML += '<p class="error">Error getting ' + 
                                      target.name + ": "+ xhr.statusText + 
                                      ",code: "+ xhr.status + "</p>";
					}
				}
			}
			xhr.send();
		}
	
		 document.getElementById("can_isd_sav"));
</script>

</body></html>
 
