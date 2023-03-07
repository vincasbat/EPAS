<?php		// neturi būti jokio tarpo prieš <?php
session_start();
 if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }

?>


<!DOCTYPE html>
<html>
<head><title>Šio mėnesio statistika</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
 <link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">

<style type="text/css">
body {
	background: white;
}
#container {  
width: 100%;
}
</style>



</head>
<body>
<div id="container">
   <div id="header">

<?php


echo "</div>\n";
include("dbstuff.inc");


echo "<div id='pop'>\n";

$cxn = mysqli_connect($host,$user,$passwd,$dbname)
     or die("Klaida! Nepavyko prisijungti prie duomenų bazės");
$query = "SELECT dokai.dok_id, dokai.dok_formos_kodas, mokestis, dokai.dok_kelias, dokai.naud_email, dokai.status_dabar, DATE(status_dabar_date) AS dab_statuso_data, pastabos, ip, dok_statusai.statusid,  DATE(dok_statusai.status_date) as gautu_data FROM dokai INNER JOIN naudotojai ON dokai.naud_email = naudotojai.naud_email INNER JOIN dok_statusai ON dok_statusai.dok_id = dokai.dok_id WHERE dok_statusai.statusid = 'Gautas' AND dok_statusai.status_date >= (SELECT DATE_FORMAT(NOW() ,'%Y-%m-01'))  ORDER BY dokai.naud_email, status_dabar_date ";     // date > 1 men.d.


$result = mysqli_query($cxn, $query) or die ("Error: ".mysqli_error($cxn));
$c = mysqli_num_rows($result);   // irasu skaicius
$n=1;
while($row = mysqli_fetch_assoc($result)) 
{
  foreach($row as $field => $value)
  {
    $dokai[$n][$field]=$value;
  }
  $n++;
}

$table_heads = array("dok_id" => "Nr.",
                     "ip" => "PNO Nr.",
			
                     "mokestis" => "Suma, LTL/EUR",
                     "dok_kelias"        => "Failų/pratęsimų sk.",
 			"naud_email"          => "Prašytojas",
"gautu_data"          => "Gavimo data",
                     );

echo "<p style='color: grey;'><b>Šį mėnesį gauti prašymai ($c)</b></p>\n";

?>

<div id="charts-container">
						<canvas id="canvas" width="280" height="160">
							Your browser does not support HTML5 Canvas.
						</canvas>
						
					</div>



<?php
$pateikta_dokumentu = 0;
$pratesimu_sk = 0;
$sumoketa = 0;

echo "<table border='0' cellpadding='2'  >\n";   //pagr. lentelė
//cellpadding='3' cellspacing='3' 
echo "<tr>\n";
foreach($table_heads as $heading)
{
   echo "<th>$heading</th>";
}

echo "</tr>\n"; //



$n_dokai = sizeof($dokai);
for ($i=1;$i<=$n_dokai;$i++)
{
$dok_id = $dokai[$i]['dok_id']; 
   
if ($i%2==0) echo "<tr>\n"; else echo "<tr class='odd'>\n";
echo "<td style='text-align: right;'>$dok_id</td>\n";  // padding-right: .5in'

$ip = $dokai[$i]['ip'];
if(strlen($ip)> 25) {
$ipshort = substr($ip, 0, 25)." ...";
echo "<td title='$ip'>$ipshort</td>\n";
} else {echo "<td>$ip</td>\n";}

// mokejimas per EPAS
$mok = 0; $moktes = "";
 $mokdokai = null; 
$quer = "SELECT dok_id, suma, paskirtis, banko_pranesimas, LEFT(mokejimo_data, 10) AS mok_data, moketojas FROM mokejimai WHERE  dok_id = $dok_id  ORDER BY mok_id desc  LIMIT 0, 100 ";
$res = mysqli_query($cxn, $quer) or die ("Error: ".mysqli_error($cxn));
$count = mysqli_num_rows($res); 
$b = 1;
while($row = mysqli_fetch_assoc($res)) 
{
  foreach($row as $field => $value)
  {
    $mokdokai[$b][$field]=$value;  
  }
 $b++; 
}
$m_dokai = sizeof($mokdokai);  //echo $m_dokai;
for ($a=1;$a<=$m_dokai;$a++)
{
$moktes = $mokdokai[$a]['suma'];
}
$mok = (float)$moktes;
$sumoketa += $mok; 
echo "<td style='text-align:right;'>$moktes</td>\n";

$q = "SELECT COUNT(*) AS nr FROM kiti_failai WHERE dok_id = '$dok_id' ";
$rezultatas = mysqli_query($cxn, $q);
$eil = mysqli_fetch_array($rezultatas, MYSQLI_ASSOC);

if(strpos($ip,'EUR) ',0)) { 
   $k = 0;
   $pieces = sizeof(explode(",", $ip));
   $pratesimu_sk += $pieces;
   echo "<td>Pratęsimų: $pieces</td>\n";
} else {$k = $eil['nr']+1;
echo "<td>Failų: $k</td>\n";
}
$pateikta_dokumentu += $k;

$naud_email = $dokai[$i]['naud_email'];
$query_naudotojai = "SELECT naud_email, CONCAT(LEFT(naud_vardas, 1), '. ', naud_pavarde) AS pareiskejas FROM naudotojai WHERE naud_email = '$naud_email'";
$res = mysqli_query($cxn, $query_naudotojai) or die ("Error: ".mysqli_error($cxn));
$row = mysqli_fetch_assoc($res);   //tikrinti ar vienas įrašas
$naud = $row['pareiskejas'];
$naud = "<a href='mailto:$naud_email'>$naud</a>";
echo "<td>$naud</td>\n";


echo "<td>{$dokai[$i]['gautu_data']}</td>\n";

echo "</tr>\n";

}   //for
echo "</table>\n";  //baigias gautų lentelė
$suma = number_format((float)$sumoketa, 2, '.', '');
echo "<p>Gauta  prašymų: $c, failų: $pateikta_dokumentu, pratęsimų: $pratesimu_sk, sumokėta per EPAS $suma EUR.</p>";

mysqli_close($cxn);

?>
<br /><br />

</div>

<script type="text/javascript">

		function drawPieChart (canvas, chartData, centerX, centerY, pieRadius) {
			var ctx;  // The context of canvas
			var previousStop = 0;  // The end position of the slice
			var totalDonors = 0;
			
			var totalCities = chartData.items.length;
			
            // Count total donors
			for (var i = 0; i < totalCities; i++) {
					totalDonors += chartData.items[i].donors;
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
					(Math.PI * 2 * (chartData.items[i].donors / totalDonors)), false);
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
				var txt = chartData.items[i].location + " | " + chartData.items[i].donors;
				ctx.fillText (txt, labelX + 18, labelY + 8);
				
				previousStop += Math.PI * 2 * (chartData.items[i].donors / totalDonors);
			}
		}
		
			
		function loadData(dataUrl, canvas) {
			var xhr = new XMLHttpRequest();
			xhr.open('GET', dataUrl, true);

			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4) {
                   if ((xhr.status >= 200 && xhr.status < 300) || 
                                             xhr.status === 304) {
						var jsonData = xhr.responseText;

						var chartData = JSON.parse(jsonData).ChartData;

						drawPieChart(canvas,chartData, 50, 50, 49);
						
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
	
		loadData('manojson.php?per=sm', document.getElementById("canvas"));



</script>

</body></html>



