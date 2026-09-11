<?php
	include_once("top.php");
	include_once("header.php");

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Förväntad inkommande godsvolym</h1>\n";
	echo "<div class='top10'>";
	?>
	<style>
	.filter-card {
		background: #fff;
		border: 1px solid #e5e7eb;
		border-radius: 8px;
		box-shadow: 0 1px 2px rgba(0,0,0,0.04);
		padding: 16px 20px;
		max-width: 640px;
	}
	.filter-row {
		display: flex;
		align-items: center;
		gap: 10px;
		margin-bottom: 10px;
	}
	.filter-row:last-child { margin-bottom: 0; }
	.filter-row .field-label {
		width: 110px;
		flex: 0 0 auto;
		font-weight: bold;
	}
	.filter-card input[type="date"] {
		border: 1px solid #d1d5db;
		border-radius: 4px;
		padding: 4px 8px;
		font-family: Verdana, sans-serif;
		font-size: 13px;
	}
	.filter-card input[type="date"]:focus {
		outline: none;
		border-color: #2dd4bf;
		box-shadow: 0 0 0 2px rgba(45,212,191,0.25);
	}
	.filter-check {
		display: flex;
		align-items: center;
		gap: 6px;
		font-size: 13px;
		cursor: pointer;
	}
	.filter-check input[type="checkbox"] {
		width: 16px;
		height: 16px;
		accent-color: #0d9488;
		cursor: pointer;
	}
	.btn-submit-teal {
		background: #0d9488;
		color: #fff;
		border: none;
		border-radius: 5px;
		padding: 6px 18px;
		font-size: 13px;
		cursor: pointer;
	}
	.btn-submit-teal:hover { background: #0f766e; }
	.today-link { font-size: 12px; }
	</style>
	<div class="filter-card">
	<form name="sampleform" method="POST">
	<input type="hidden" name="supID" value="<?php echo $supID; ?>">
	<div class="filter-row">
		<label class="field-label" for="firstinput">Datum</label>
		<input type="date" id="firstinput" name="firstinput" value="<?php echo $dagensdatum; ?>" onchange="submit()">
		<?php if ($ref_dagensdatum != $dagensdatum) { ?>
		<a class="today-link" href="<?php echo $_SERVER['PHP_SELF']; ?>">Idag</a>
		<?php } ?>
	</div>
	<div class="filter-row">
		<label class="filter-check">
			<input type="checkbox" onclick="submit()" name="not_only_today" value="yes"<?php if ($not_only_today == "yes") echo " checked"; ?>>
			Visa även leveranser med icke exakt datum specificerat
		</label>
	</div>
	<div class="filter-row">
		<label class="filter-check">
			<input type="checkbox" onclick="submit()" name="economy" value="yes"<?php if ($economy == "yes") echo " checked"; ?>>
			Visa även nettosumman (tar längre tid)
		</label>
	</div>
	<div class="filter-row">
		<button type="submit" class="btn-submit-teal">Rapport</button>
	</div>
	</form>
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

	include_once("footer.php");
?>
