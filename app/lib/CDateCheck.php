<?php

function datebetweenInpris($datum) {
	$date = strtotime($datum); 
	$secs = time() - $date; 
	$days = $secs / 60 / 60 / 24; 
	$varde = ceil($days);
	echo $varde . " dagar";
	if ($varde > 182) { ?>
	<a onMouseOver="return escape('<b>Varningsklocka!</b><br><br>Inpriset på denna produkt är mer än ett halvår gammalt.<br><br>Bör kollas upp omgående!')">
	<?php
	echo "&nbsp;<b><font color='red'>** Varning! **</font></b></a>";
	}
} 

function datebetweenInprisMarkera($datum, $katID) {
	$date = strtotime($datum); 
	$secs = time() - $date; 
	$days = $secs / 60 / 60 / 24; 
	$varde = ceil($days);
	if ($_SERVER['REMOTE_ADDR'] == "81.8.240.115" && $ej == "nu") {
		if ($katID == 392 || $katID == 393 || $katID == 394 || $katID == 395) {
			if ($varde > 90) { ?>
			<a onMouseOver="this.T_WIDTH=250;this.T_BGCOLOR='FF0000';this.T_FONTCOLOR='FFFFFF';return escape('Inpriset på denna produkt är mer än ett tre månader gammalt.<br><br>Vänligen uppdatera!')">
			<?php
			echo "<b><font color='red'>";
			}
		} else {
			if ($varde > 180) { ?>
			<a onMouseOver="this.T_WIDTH=250;this.T_BGCOLOR='FF0000';this.T_FONTCOLOR='FFFFFF';return escape('Inpriset på denna produkt är mer än ett halvår gammalt.<br><br>Vänligen uppdatera!')">
			<?php
			echo "<b><font color='red'>";
			}
		}
	}
} 

?>