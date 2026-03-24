<?php
session_start();
require_once("CTemp.php");
$temp = new CTemp();

	// echo "<div class=\"align_center clock\">" . date("Y-m-d H:i") . "</div>\n";
	// echo "<div class=\"align_center clock\">" . date("H:i:s") . "</div>\n";
	// echo "<div class=\"align_center clock\">" . date("H:i") . "</div>\n";
	// echo date("H:i") . " | " . $temp->showLastTemp(2) . "&#8451;";
	echo date("H:i") . " | " . $temp->showLastTemp(2) . "&deg;";
?>