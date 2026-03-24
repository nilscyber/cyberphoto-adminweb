<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Förväntad inkommande godsvolym</h1>\n";
	echo "<div class='top10'>";
	?>
	<div>
	<form name="sampleform" method="POST">
	<input type="hidden" name="supID" value="<?php echo $supID; ?>">
	<span class="abbrubrik">Annat datum:</span><br>
	<input type="text" name="firstinput" size=12 value="<?php echo $dagensdatum; ?>" style="font-family: Verdana; font-size: 10px"> <span class="abbrubrik"><a href="javascript:showCal('Calendar1')">Välj datum</a></span> <?php if ($ref_dagensdatum != $dagensdatum) { ?><span class="abbrubrik"><a href="<?php echo $_SERVER['PHP_SELF']; ?>"> Idag</a></span><?php } ?>&nbsp;
	<input type="checkbox" onClick="submit()" name="not_only_today" value="yes"<?php if ($not_only_today == "yes"): echo " checked"; endif; ?>>&nbsp;<?php if ($not_only_today): echo "<b>"; endif; ?>Visa även leveranser med icke exakt datum specifiserat</b>
	<input type="checkbox" onClick="submit()" name="economy" value="yes"<?php if ($economy == "yes"): echo " checked"; endif; ?>>&nbsp;<?php if ($economy): echo "<b>"; endif; ?>Visa även nettosumman (tar längre tid)</b><br>
	<hr noshade color="#C0C0C0" align="left" width="150" size="1">
	<input type="submit" value="Rapport" style="font-family: Verdana; font-size: 10px">
	</div>
	<?php
	echo "</div>\n";
	echo "<div class='top10'>";
	$adintern->goodsExpectation();
	echo "</div>\n";
	if ($supID > 0) {
		echo "<div class='top10'>";
		$adintern->goodsExpectationDetail();
		echo "</div>\n";
	}
	?>
	</form>
	<?php
	
	include_once("footer.php");
?>