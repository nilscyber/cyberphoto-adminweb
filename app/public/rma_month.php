<?php
	echo "<div>\n";
	echo "<select class=\"select_frontsection\" name=\"rma_month\" onchange=\"this.form.submit(this.options[this.selectedIndex].value)\">\n";
	echo "<option value=\"0\">Alla månader</option>\n";
	echo "<option value=\"01\"";
	if ($rma_month == "01") {
		echo " selected";
	}
	echo ">Januari</option>\n";
	echo "<option value=\"02\"";
	if ($rma_month == "02") {
		echo " selected";
	}
	echo ">Februari</option>\n";
	echo "<option value=\"03\"";
	if ($rma_month == "03") {
		echo " selected";
	}
	echo ">Mars</option>\n";
	echo "<option value=\"04\"";
	if ($rma_month == "04") {
		echo " selected";
	}
	echo ">April</option>\n";
	echo "<option value=\"05\"";
	if ($rma_month == "05") {
		echo " selected";
	}
	echo ">Maj</option>\n";
	echo "<option value=\"06\"";
	if ($rma_month == "06") {
		echo " selected";
	}
	echo ">Juni</option>\n";
	echo "<option value=\"07\"";
	if ($rma_month == "07") {
		echo " selected";
	}
	echo ">Juli</option>\n";
	echo "<option value=\"08\"";
	if ($rma_month == "08") {
		echo " selected";
	}
	echo ">Augusti</option>\n";
	echo "<option value=\"09\"";
	if ($rma_month == "09") {
		echo " selected";
	}
	echo ">September</option>\n";
	echo "<option value=\"10\"";
	if ($rma_month == "10") {
		echo " selected";
	}
	echo ">Oktober</option>\n";
	echo "<option value=\"11\"";
	if ($rma_month == "11") {
		echo " selected";
	}
	echo ">November</option>\n";
	echo "<option value=\"12\"";
	if ($rma_month == "12") {
		echo " selected";
	}
	echo ">December</option>\n";
	echo "</select>\n";
	echo "</div>\n";
?>