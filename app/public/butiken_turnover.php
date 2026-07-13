<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Butiken – omsättning &amp; pling</h1>\n";

	$turnover = $butiken->getTurnoverButikenData();
	$pling    = $butiken->getPlingButikenData();

	$months = array_unique(array_merge(array_keys($turnover), array_keys($pling)));
	rsort($months);

	if (count($months) > 0) {

		echo "<table class=\"table-list\">\n";
		echo "\t<thead>\n";
		echo "\t\t<tr>\n";
		echo "\t\t\t<th>Månad</th>\n";
		echo "\t\t\t<th>Ordrar i butik</th>\n";
		echo "\t\t\t<th>Omsättning</th>\n";
		echo "\t\t\t<th>Pling</th>\n";
		echo "\t\t</tr>\n";
		echo "\t</thead>\n";
		echo "\t<tbody>\n";

		foreach ($months as $month) {
			$antal = isset($turnover[$month]) ? $turnover[$month]['antal'] : "";
			$total = isset($turnover[$month]) ? number_format($turnover[$month]['total'], 0, ',', ' ') : "";
			$plingAntal = isset($pling[$month]) ? $pling[$month] : "";

			echo "\t\t<tr>\n";
			echo "\t\t\t<td>" . htmlspecialchars($month) . "</td>\n";
			echo "\t\t\t<td>$antal</td>\n";
			echo "\t\t\t<td>$total</td>\n";
			echo "\t\t\t<td>$plingAntal</td>\n";
			echo "\t\t</tr>\n";
		}

		echo "\t</tbody>\n";
		echo "</table>\n";

	}

	include_once("footer.php");
?>
