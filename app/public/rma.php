<?php 
	include_once("top.php");
	include_once("header.php");
	
	// echo $rma_year . "<br>";
	// echo $rma_month . "<br>";
	// echo date("Y");
	if ($doa) {
		echo "<h1>Registrerade Retur DOA</h1>\n";
	} elseif ($return_rma) {
		echo "<h1>Registrerade Retur öppet köp</h1>\n";
	} else {
		echo "<h1>Registrerade reparationer</h1>\n";
	}
	echo "<form method=\"GET\">\n";
	echo "<input type=\"hidden\" name=\"rma_year\" value=\"$rma_year\">\n";
	echo "<input type=\"hidden\" name=\"rma_month\" value=\"$rma_month\">\n";
	echo "<input type=\"hidden\" name=\"rma_days\" value=\"$rma_days\">\n";
	echo "<input type=\"hidden\" name=\"rma_cat\" value=\"$rma_cat\">\n";
	echo "<input type=\"hidden\" name=\"rma_artnr\" value=\"$rma_artnr\">\n";
	include("rma_year.php");
	if ($rma_year > 0) {
		include("rma_month.php");
	}
	if ($rma_month > 0) {
		include("rma_days.php");
	}
	echo "</form>\n";
	if ($rma_cat > 0 && $rma_artnr == "") {
		echo "<h2>Detaljer för kategori</h2>\n";
		$rma->displayRMACategoryDetail($rma_cat);
	} elseif ($rma_artnr != "") {
		echo "<h2>Detaljer för produkt</h2>\n";
		$rma->displayRMAProductDetail($rma_artnr);
		echo "<h2>Total summering för produkt ovan</h2>\n";
		$rma->summaryRMAProduct($rma_artnr);
	} else {
		echo "<h2>Per kategori</h2>\n";
		$rma->displayRMACategory();
	}
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
	}
	
	include_once("footer.php");
?>