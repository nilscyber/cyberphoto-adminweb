<?php 
include_once("top.php");
include_once("header.php");

echo "<h1>Fördelning av betalsätt samt nettovärde på dessa</h1>\n";

echo "<form>\n";
echo "<div>\n";
echo "<div style=\"float: left; width: 180px;\">\n";
echo "<select name=\"history\" onchange=\"this.form.submit();\">\n";
if ($history == "day") {
	echo "<option value=\"day\" selected>Senaste dygnet</option>\n";
} else {
	echo "<option value=\"day\">Senaste dygnet</option>\n";
}
if ($history == "week") {
	echo "<option value=\"week\" selected>Senaste veckan</option>\n";
} else {
	echo "<option value=\"week\">Senaste veckan</option>\n";
}
if ($history == "month" || $history == "") {
	echo "<option value=\"month\" selected>Senaste månaden</option>\n";
} else {
	echo "<option value=\"month\">Senaste månaden</option>\n";
}
if ($history == "month3") {
	echo "<option value=\"month3\" selected>Senaste 3 månaderna</option>\n";
} else {
	echo "<option value=\"month3\">Senaste 3 månaderna</option>\n";
}
if ($history == "month6") {
	echo "<option value=\"month6\" selected>Senaste 6 månaderna</option>\n";
} else {
	echo "<option value=\"month6\">Senaste 6 månaderna</option>\n";
}
echo "</select>\n";
echo "</div>\n";
echo "<div style=\"float: left; width: 180px;\">\n";
if ($netto_noll == "no") {
	echo "Exkludera 0 kr ordrar<input type=\"checkbox\" name=\"netto_noll\" value=\"no\" onClick=\"submit()\" checked>\n";
} else {
	echo "Exkludera 0 kr ordrar<input type=\"checkbox\" name=\"netto_noll\" value=\"no\" onClick=\"submit()\">\n";
}
echo "</div>\n";
echo "<div style=\"float: left; width: 180px;\">\n";
echo "<select name=\"group\" onchange=\"this.form.submit();\">\n";
if ($group == "") {
	echo "<option value=\"\" selected>Alla kunder</option>\n";
} else {
	echo "<option value=\"\">Alla kunder</option>\n";
}
if ($group == "company") {
	echo "<option value=\"company\" selected>Företag</option>\n";
} else {
	echo "<option value=\"company\">Företag</option>\n";
}
if ($group == "privat") {
	echo "<option value=\"privat\" selected>Privatpersoner</option>\n";
} else {
	echo "<option value=\"privat\">Privatpersoner</option>\n";
}
echo "</select>\n";
echo "</div>\n";
echo "</div>\n";
echo "</form>\n";

echo "<div class='top10 clear'>";
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
	var data = google.visualization.arrayToDataTable([
			['Betalsätt', 'Antal per månad'],
			<?php $statistics->getLatestPayMethods(true,false,false); ?>
			]);

	var options = {
		title: 'Sverige',
		is3D: true,
	};

	var chart = new google.visualization.PieChart(document.getElementById('piechart_3d_se'));
	chart.draw(data, options);
}
</script>
<div id="piechart_3d_se" style="width: 1200px; height: 500px;"></div>
<?php
echo "</div>\n";


include_once("footer.php");
?>