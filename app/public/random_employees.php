<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Slumpgeneratorn (används vid utlottning bland anställda)</h1>\n";
	echo "<h2>Välj antalet</h2>\n";
	echo "<form id=\"FormName\" method=\"post\" name=\"FormName\">\n";
	echo "<input type=\"text\" name=\"rand_num\" size=\"2\" value=\"\n";
	if ($rand_num) {
		echo $rand_num;
	} else {
		echo 1;
	}
	echo "\">\n";
	echo "<p><input type=\"submit\" value=\"Slumpa fram\" /></p>\n";
	echo "</form>\n";

	if (is_numeric($rand_num)) {
		$employees->listRandomEmployees($rand_num);
	}
	
	include_once("footer.php");
?>