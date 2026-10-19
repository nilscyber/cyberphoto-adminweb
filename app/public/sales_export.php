<?php
	include_once("top.php");

	// Period: standard = föregående kalenderdag (dagens datum ingår inte, dagen är inte komplett)
	$today = new DateTime('today');
	$yesterday = (clone $today)->modify('-1 day')->format('Y-m-d');
	$validDate = function ($s) { return is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) && strtotime($s) !== false; };
	$from  = (isset($_GET['from']) && $validDate($_GET['from'])) ? $_GET['from'] : $yesterday;
	$to    = (isset($_GET['to'])   && $validDate($_GET['to']))   ? $_GET['to']   : $yesterday;
	if ($from > $to) { $tmp = $from; $from = $to; $to = $tmp; }
	$basis = (isset($_GET['basis']) && $_GET['basis'] === 'done') ? 'done' : 'order';
	$format = (isset($_GET['format']) && $_GET['format'] === 'excel') ? 'excel' : 'standard';
	$maxDays = 1100; $clamped = false;
	if ((strtotime($to) - strtotime($from)) / 86400 > $maxDays - 1) {
		$from = date('Y-m-d', strtotime($to . ' -' . ($maxDays - 1) . ' days')); $clamped = true;
	}

	$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
	$nf = function ($n, $d = 0) { return number_format((float)$n, $d, ',', ' '); };

	// ---------- CSV-nedladdning (ingen HTML runt omkring) ----------
	if (isset($_GET['download'])) {
		$exp = new CSalesExport();
		@set_time_limit(600);
		$res = $exp->query($from, $to, $basis);
		if (!$res) {
			header('Content-Type: text/plain; charset=utf-8', true, 500);
			echo "Kunde inte läsa från ADempiere: " . $exp->error;
			exit;
		}
		while (ob_get_level() > 0) { ob_end_clean(); }
		$delim = ($format === 'excel') ? ';' : ',';
		$fname = "forsaljning_" . $from . "_" . $to . "_" . ($basis === 'done' ? 'slutfort' : 'order') . ($format === 'excel' ? '_excel' : '') . ".csv";
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $fname . '"');
		header('Cache-Control: no-store');
		$out = fopen('php://output', 'w');
		if ($format === 'excel') { fwrite($out, "\xEF\xBB\xBF"); }
		$cols = CSalesExport::columns($res);
		$isNum = array();
		foreach ($cols as $i => $c) { $isNum[$i] = in_array($c, CSalesExport::$numericColumns, true); }
		fputcsv($out, $cols, $delim, '"', '');
		$n = 0;
		while ($row = pg_fetch_row($res)) {
			if ($format === 'excel') {
				foreach ($row as $i => $v) { if ($isNum[$i] && $v !== null) { $row[$i] = str_replace('.', ',', $v); } }
			}
			fputcsv($out, $row, $delim, '"', '');
			if ((++$n % 2000) == 0) { flush(); }
		}
		fclose($out);
		exit;
	}

	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"sales_export.css?ver=ad" . date("ynjGi") . "\">\n";

	$self = $_SERVER['PHP_SELF'];
	$qs = function ($f, $t, $b = null, $fm = null) use ($self, $basis, $format) {
		return $self . "?from=$f&to=$t&basis=" . ($b ?: $basis) . "&format=" . ($fm ?: $format);
	};
	$lastMonday = (clone $today)->modify('monday this week')->modify('-7 days');
	$lastSunday = (clone $lastMonday)->modify('+6 days');

	echo "<h1>Försäljningsexport</h1>\n";
	echo "<p class=\"se-intro\">Slutförda kundorder, en rad per orderrad, som CSV för analys och uppföljning. Samma ordernummer finns på alla rader i ordern så att både antal unika ordrar och försäljning per kategori kan räknas. "
	   . "Standard är föregående kalenderdag; dagens datum är inte komplett.</p>\n";

	echo "<form method=\"GET\" class=\"se-form\" id=\"se-form\">\n";
	echo "<div class=\"filter-bar\">\n";
	echo "<label>Från <input type=\"date\" name=\"from\" value=\"" . $h($from) . "\"></label>\n";
	echo "<label>Till <input type=\"date\" name=\"to\" value=\"" . $h($to) . "\"></label>\n";
	echo "<label>Perioden avser <select name=\"basis\"><option value=\"order\"" . ($basis == 'order' ? ' selected' : '') . ">Orderdatum</option><option value=\"done\"" . ($basis == 'done' ? ' selected' : '') . ">Slutfört datum</option></select></label>\n";
	echo "<label>Format <select name=\"format\"><option value=\"standard\"" . ($format == 'standard' ? ' selected' : '') . ">Standard (komma, decimalpunkt)</option><option value=\"excel\"" . ($format == 'excel' ? ' selected' : '') . ">Svenskt Excel (semikolon, decimalkomma)</option></select></label>\n";
	echo "<button type=\"submit\" class=\"se-btn se-btn-secondary\">Förhandsgranska</button>\n";
	echo "<button type=\"submit\" name=\"download\" value=\"1\" class=\"se-btn\">Ladda ner CSV</button>\n";
	echo "</div>\n";
	echo "<div class=\"se-quick\">Snabbval: <a href=\"" . $h($qs($yesterday, $yesterday)) . "\">igår</a> · <a href=\"" . $h($qs($lastMonday->format('Y-m-d'), $lastSunday->format('Y-m-d'))) . "\">förra veckan</a> · "
	   . "<a href=\"" . $h($qs('2025-01-01', '2026-10-13')) . "\">baslinje 2025-01-01 – 2026-10-13</a></div>\n";
	echo "</form>\n";
	if ($clamped) echo "<p class=\"se-note\">Perioden är begränsad till $maxDays dagar; visar " . $h($from) . " – " . $h($to) . ".</p>\n";

	// ---------- Förhandsgranskning ----------
	if (isset($_GET['from']) || isset($_GET['to'])) {
		$exp = new CSalesExport();
		@set_time_limit(600);
		$res = $exp->query($from, $to, $basis);
		if (!$res) {
			echo "<p class=\"se-note\">Kunde inte läsa från ADempiere: " . $h($exp->error) . "</p>\n";
		} else {
			$cols = CSalesExport::columns($res);
			$orders = array(); $lines = 0; $units = 0.0; $value = 0.0; $cost = 0.0; $preview = array(); $chan = array();
			while ($row = pg_fetch_assoc($res)) {
				$lines++;
				if (!isset($orders[$row['Ordernummer']])) { $orders[$row['Ordernummer']] = 1; $k = $row['Kanal']; $chan[$k] = ($chan[$k] ?? 0) + 1; }
				$units += (float)$row['Antal enheter']; $value += (float)$row['Värde exkl moms']; $cost += (float)$row['Varukostnad'];
				if ($lines <= 25) $preview[] = $row;
			}
			$nOrders = count($orders);
			echo "<p class=\"se-period\">" . ($basis == 'done' ? "Slutfört" : "Orderdatum") . " " . $h($from) . " – " . $h($to) . ". Status per " . date('Y-m-d H:i') . ".</p>\n";
			if ($lines == 0) {
				echo "<p class=\"se-note\">Inga slutförda ordrar i perioden.</p>\n";
			} else {
				echo "<div class=\"se-kpis\">\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($nOrders) . "</div><div class=\"l\">unika ordrar</div></div>\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($lines) . "</div><div class=\"l\">orderrader</div></div>\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($value) . "</div><div class=\"l\">värde exkl moms, kr</div></div>\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($cost) . "</div><div class=\"l\">varukostnad, kr</div></div>\n";
				echo "</div>\n";
				$parts = array(); arsort($chan);
				foreach ($chan as $k => $v) { $parts[] = $h($k) . " " . $nf($v); }
				echo "<p class=\"se-period\">Ordrar per kanal: " . implode(" · ", $parts) . "</p>\n";
				echo "<h2 class=\"se-h2\">Förhandsgranskning, första " . count($preview) . " raderna</h2>\n";
				echo "<div class=\"se-scroll\"><table class=\"table-list\"><thead><tr>";
				foreach ($cols as $c) { echo "<th" . (in_array($c, CSalesExport::$numericColumns, true) ? " class=\"n\"" : "") . ">" . $h($c) . "</th>"; }
				echo "</tr></thead><tbody>\n";
				foreach ($preview as $r) {
					echo "<tr>";
					foreach ($cols as $c) { echo "<td" . (in_array($c, CSalesExport::$numericColumns, true) ? " class=\"n\"" : "") . ">" . $h($r[$c]) . "</td>"; }
					echo "</tr>\n";
				}
				echo "</tbody></table></div>\n";
			}
		}
	}

	// ---------- Definitioner ----------
	echo "<div class=\"se-method\"><details><summary>Kolumner och definitioner</summary>\n";
	echo "<table class=\"table-list\"><thead><tr><th>Kolumn</th><th>Innehåll</th></tr></thead><tbody>\n";
	$defs = array(
		array('Ordernummer', 'Ordernumret. Samma nummer på alla rader i ordern.'),
		array('Orderdatum', 'Datum då ordern lades.'),
		array('Slutfört datum', 'Datum då ordern senast slutfördes i ADempiere (ProcessedOn). För 11 % av ordrarna är det en annan dag än orderdatum. En order som återöppnas och slutförs igen får det senaste datumet.'),
		array('Orderstatus', 'Slutförd (CO) eller Stängd (CL). Endast dessa ingår; makulerade, pågående och utkast ingår inte.'),
		array('Kanal', 'Webb = säljare "Weborder". Telefon / E-post / Fax = ordermetod som säljaren angett på ordern (fylld på ca 5 % av de manuella ordrarna). Butik = manuell order som hämtas i butiken. Övrig manuell = resten av de manuella ordrarna, där kanalen inte är angiven.'),
		array('Kundtyp', 'Företag = kunden ligger i affärspartnergruppen Företagskund. Allt annat är Privat (även anställda, leverantörer, återförsäljare). Kundgrupp visar den exakta gruppen.'),
		array('Kund-ID', 'Kundnumret (affärspartnerns nummer i ADempiere). Samma kund har samma nummer över tid.'),
		array('Huvudkategori / Överkategori / Kategori', 'Produktkategoriträdet i ADempiere. Huvudkategori = trädets översta nivå, Överkategori = nivån under den, Kategori = produktens egen kategori. Kategorisökväg visar hela kedjan. Rader utan produkt (avgifter) har "(ingen kategori)". Kategorin "** DOLD **" innehåller gratis friförsäkring och rabattrader.'),
		array('Artikelnummer / Produktnamn', 'Produktens artikelnummer och namn. Avgiftsrader saknar artikelnummer.'),
		array('Radtyp', 'Vara, Tjänst (t.ex. frakt och friförsäkring), Resurs eller Avgift. Filtrera på Vara för ren varuförsäljning. Orderrabatter ligger som egna rader (artikel rab, "Övrig rabatt") med negativt värde.'),
		array('Antal rader i ordern', 'Antal orderrader i hela ordern (samma värde på alla rader i ordern). Summera inte kolumnen.'),
		array('Antal enheter', 'Beställt antal på raden.'),
		array('Värde exkl moms', 'Radens nettobelopp i kronor exklusive moms, efter rabatt. Ordrar i andra valutor är omräknade.'),
		array('Varukostnad', 'Bokförd kostnad för sålda varor (konto 4010) på radens leveranser. Det som inte är levererat än, och rader som saknar bokning, värderas med radens inköpspris. Storkameror från de stora varumärkena visar ofta negativt täckningsbidrag eftersom leverantörernas kampanjstöd inte ligger i produktkostnaden.'),
	);
	foreach ($defs as $d) { echo "<tr><td><b>" . $h($d[0]) . "</b></td><td>" . $h($d[1]) . "</td></tr>\n"; }
	echo "</tbody></table>\n";
	echo "<p class=\"se-note2\">Orderrader med antal 0 (annullerade rader) tas inte med, inte heller ordrar där alla rader är annullerade. Returer och krediteringar är inte avräknade. "
	   . "Siffror för tidigare dagar kan ändras om en order senare makuleras. Format: Standard har komma som kolumnavgränsare, punkt som decimaltecken och UTF-8; Svenskt Excel har semikolon, decimalkomma och UTF-8 med BOM.</p>\n";
	echo "</details></div>\n";

	include_once("footer.php");
?>
