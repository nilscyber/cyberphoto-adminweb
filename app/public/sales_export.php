<?php
	include_once("top.php");

	// Period: standard = föregående kalenderdag (dagens datum ingår inte, dagen är inte komplett)
	$today = new DateTime('today');
	$yesterday = (clone $today)->modify('-1 day')->format('Y-m-d');
	$validDate = function ($s) { return is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) && strtotime($s) !== false; };
	$from  = (isset($_GET['from']) && $validDate($_GET['from'])) ? $_GET['from'] : $yesterday;
	$to    = (isset($_GET['to'])   && $validDate($_GET['to']))   ? $_GET['to']   : $yesterday;
	if ($from > $to) { $tmp = $from; $from = $to; $to = $tmp; }
	$basis = (isset($_GET['basis']) && in_array($_GET['basis'], array('order', 'done', 'open'), true)) ? $_GET['basis'] : 'order';
	$format = (isset($_GET['format']) && $_GET['format'] === 'excel') ? 'excel' : 'standard';
	$maxDays = 1100; $clamped = false;
	if ($basis !== 'open' && (strtotime($to) - strtotime($from)) / 86400 > $maxDays - 1) {
		$from = date('Y-m-d', strtotime($to . ' -' . ($maxDays - 1) . ' days')); $clamped = true;
	}
	$basisName = array('order' => 'Orderdatum', 'done' => 'Slutfört datum', 'open' => 'Öppen orderstock');

	$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
	$nf = function ($n, $d = 0) { return number_format((float)$n, $d, ',', ' '); };

	// ---------- CSV-nedladdning (ingen HTML runt omkring) ----------
	if (isset($_GET['download'])) {
		$exp = new CSalesExport();
		@set_time_limit(1800);
		$chunks = CSalesExport::chunks($from, $to, $basis);
		// Första biten körs innan några headers skickas, så att ett databasfel kan ge ett riktigt felmeddelande
		$res = $exp->query($chunks[0][0], $chunks[0][1], $basis);
		if (!$res) {
			header('Content-Type: text/plain; charset=utf-8', true, 500);
			echo "Kunde inte läsa från ADempiere: " . $exp->error;
			exit;
		}
		while (ob_get_level() > 0) { ob_end_clean(); }
		$delim = ($format === 'excel') ? ';' : ',';
		if ($basis === 'open') {
			$fname = "forsaljning_oppen_orderstock_" . date('Y-m-d_Hi');
		} else {
			$fname = "forsaljning_" . $from . "_" . $to . "_" . ($basis === 'done' ? 'slutfort' : 'order');
		}
		$fname .= ($format === 'excel' ? '_excel' : '') . ".csv";
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $fname . '"');
		header('Cache-Control: no-store');
		$out = fopen('php://output', 'w');
		if ($format === 'excel') { fwrite($out, "\xEF\xBB\xBF"); }
		$cols = CSalesExport::columns($res);
		$isNum = array();
		foreach ($cols as $i => $c) { $isNum[$i] = in_array($c, CSalesExport::$numericColumns, true); }
		fputcsv($out, $cols, $delim, '"', '');
		flush();
		$n = 0;
		foreach ($chunks as $ci => $ch) {
			if ($ci > 0) {
				if (connection_aborted()) break;
				$res = $exp->query($ch[0], $ch[1], $basis);
				if (!$res) { fputcsv($out, array("FEL: avbruten, " . $exp->error), $delim, '"', ''); break; }
			}
			while ($row = pg_fetch_row($res)) {
				if ($format === 'excel') {
					foreach ($row as $i => $v) { if ($isNum[$i] && $v !== null) { $row[$i] = str_replace('.', ',', $v); } }
				}
				fputcsv($out, $row, $delim, '"', '');
				if ((++$n % 2000) == 0) { flush(); }
			}
			pg_free_result($res);
			flush();
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
	echo "<p class=\"se-intro\">Kundorder som CSV för analys och uppföljning, en rad per orderrad. Samma ordernummer finns på alla rader i ordern så att både antal unika ordrar och försäljning per kategori kan räknas. "
	   . "Tre rapporter med samma kolumner: <b>orderingång</b> (ordrar skapade i perioden), <b>slutförd försäljning</b> (ordrar slutförda i perioden) och <b>öppen orderstock</b> (allt som inte är färdiglevererat just nu). "
	   . "Standardperioden är föregående kalenderdag; dagens datum är inte komplett.</p>\n";

	echo "<form method=\"GET\" class=\"se-form\" id=\"se-form\">\n";
	echo "<div class=\"filter-bar\">\n";
	echo "<label>Rapport <select name=\"basis\" id=\"se-basis\">"
	   . "<option value=\"order\"" . ($basis == 'order' ? ' selected' : '') . ">Orderingång, period = orderdatum (skapad)</option>"
	   . "<option value=\"done\"" . ($basis == 'done' ? ' selected' : '') . ">Slutförd försäljning, period = slutfört datum</option>"
	   . "<option value=\"open\"" . ($basis == 'open' ? ' selected' : '') . ">Öppen orderstock just nu (ingen period)</option>"
	   . "</select></label>\n";
	echo "<label class=\"se-dates\">Från <input type=\"date\" name=\"from\" value=\"" . $h($from) . "\"></label>\n";
	echo "<label class=\"se-dates\">Till <input type=\"date\" name=\"to\" value=\"" . $h($to) . "\"></label>\n";
	echo "<label>Format <select name=\"format\"><option value=\"standard\"" . ($format == 'standard' ? ' selected' : '') . ">Standard (komma, decimalpunkt)</option><option value=\"excel\"" . ($format == 'excel' ? ' selected' : '') . ">Svenskt Excel (semikolon, decimalkomma)</option></select></label>\n";
	echo "<button type=\"submit\" class=\"se-btn se-btn-secondary\">Förhandsgranska</button>\n";
	echo "<button type=\"submit\" name=\"download\" value=\"1\" class=\"se-btn\">Ladda ner CSV</button>\n";
	echo "</div>\n";
	echo "<div class=\"se-quick\">Snabbval: <a href=\"" . $h($qs($yesterday, $yesterday, 'order')) . "\">igår</a> · <a href=\"" . $h($qs($lastMonday->format('Y-m-d'), $lastSunday->format('Y-m-d'), 'order')) . "\">förra veckan</a> · "
	   . "<a href=\"" . $h($qs('2025-01-01', '2026-10-13', 'order')) . "\">baslinje 2025-01-01 – 2026-10-13</a> · <a href=\"" . $h($qs($yesterday, $yesterday, 'done')) . "\">slutfört igår</a> · <a href=\"" . $h($qs($from, $to, 'open')) . "\">öppen orderstock</a></div>\n";
	echo "</form>\n";
	echo "<script>(function(){var b=document.getElementById('se-basis');function t(){var o=b.value==='open';document.querySelectorAll('.se-dates').forEach(function(e){e.style.opacity=o?'0.45':'1';});}b.addEventListener('change',t);t();})();</script>\n";
	if ($clamped) echo "<p class=\"se-note\">Perioden är begränsad till $maxDays dagar; visar " . $h($from) . " – " . $h($to) . ".</p>\n";

	// ---------- Förhandsgranskning ----------
	if (isset($_GET['from']) || isset($_GET['to']) || isset($_GET['basis'])) {
		$exp = new CSalesExport();
		@set_time_limit(1800);
		$cols = null; $err = "";
		$orders = array(); $lines = 0; $value = 0.0; $cost = 0.0; $remain = 0.0; $remQty = 0.0; $credited = 0.0; $preview = array(); $chan = array(); $reason = array();
		foreach (CSalesExport::chunks($from, $to, $basis) as $ch) {
			// håller anslutningen vid liv genom proxyn (60 s tystnad ger 504) medan månaderna läses
			echo "<!-- läser " . $h($ch[0]) . " -->\n"; flush();
			$res = $exp->query($ch[0], $ch[1], $basis);
			if (!$res) { $err = $exp->error; break; }
			if ($cols === null) $cols = CSalesExport::columns($res);
			while ($row = pg_fetch_assoc($res)) {
				$lines++;
				if (!isset($orders[$row['Ordernummer']])) { $orders[$row['Ordernummer']] = 1; $k = $row['Kanal']; $chan[$k] = ($chan[$k] ?? 0) + 1; }
				$value += (float)$row['Värde exkl moms']; $cost += (float)$row['Varukostnad']; $remain += (float)$row['Kvarstående värde']; $remQty += (float)$row['Kvarstående antal']; $credited += (float)$row['Krediterat belopp'];
				if ($basis === 'open' && (float)$row['Kvarstående antal'] > 0) { $k = $row['Restorsak']; $reason[$k] = ($reason[$k] ?? 0) + (float)$row['Kvarstående värde']; }
				if ($lines <= 25) $preview[] = $row;
			}
			pg_free_result($res);
		}
		if ($err !== "") {
			echo "<p class=\"se-note\">Kunde inte läsa från ADempiere: " . $h($err) . "</p>\n";
		} else {
			$nOrders = count($orders);
			if ($basis === 'open') {
				echo "<p class=\"se-period\">Öppen orderstock, ögonblicksbild per " . date('Y-m-d H:i') . ". Alla ordrar med minst en varurad som inte är fullt levererad, oavsett orderdatum.</p>\n";
			} else {
				echo "<p class=\"se-period\">" . $basisName[$basis] . " " . $h($from) . " – " . $h($to) . ". Status per " . date('Y-m-d H:i') . ".</p>\n";
			}
			if ($lines == 0) {
				echo "<p class=\"se-note\">Inga ordrar i urvalet.</p>\n";
			} else {
				echo "<div class=\"se-kpis se-kpis-6\">\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($nOrders) . "</div><div class=\"l\">unika ordrar</div></div>\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($lines) . "</div><div class=\"l\">orderrader</div></div>\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($value) . "</div><div class=\"l\">värde exkl moms, kr</div></div>\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($cost) . "</div><div class=\"l\">varukostnad, kr</div></div>\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($remain) . "</div><div class=\"l\">kvarstående värde, kr (" . $nf($remQty) . " enheter)</div></div>\n";
				echo "<div class=\"se-kpi\"><div class=\"v\">" . $nf($credited) . "</div><div class=\"l\">krediterat belopp, kr</div></div>\n";
				echo "</div>\n";
				$parts = array(); arsort($chan);
				foreach ($chan as $k => $v) { $parts[] = $h($k) . " " . $nf($v); }
				echo "<p class=\"se-period\">Ordrar per kanal: " . implode(" · ", $parts) . "</p>\n";
				if ($basis === 'open' && count($reason)) {
					$parts = array(); arsort($reason);
					foreach ($reason as $k => $v) { $parts[] = $h($k) . " " . $nf($v) . " kr"; }
					echo "<p class=\"se-period\">Kvarstående värde per restorsak: " . implode(" · ", $parts) . "</p>\n";
				}
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
		array('Urval', 'Kundorder av typen Standard Order med status Slutförd, Under bearbetning eller annan aktiv status. Utkast, annullerade och återkallade ordrar ingår inte. Annullerade ordrar har kvantitet 0 på alla rader i ADempiere och kan därför inte visas med värde. Orderrader med antal 0 (annullerade rader) tas inte med. I öppen orderstock ligger även ett tjugotal gamla ordrar (2012–2024) som aldrig stängts; sortera eller filtrera på orderdatum.'),
		array('Ordernummer', 'Ordernumret. Samma nummer på alla rader i ordern.'),
		array('Orderdatum', 'Datum då ordern skapades i ADempiere (fältet Created, som aldrig ändras). Det manuellt ändringsbara fältet Orderdatum används inte.'),
		array('Slutfört datum', 'Datum då ordern senast slutfördes i ADempiere (ProcessedOn). För 11 % av ordrarna är det en annan dag än orderdatum. Tomt för ordrar som inte är slutförda.'),
		array('Orderstatus', 'Slutförd (CO), Under bearbetning (IP) eller Avslutad (CL). Sedan 2025 är i praktiken alla ordrar Slutförd; Under bearbetning är ordrar som är registrerade men inte slutförda.'),
		array('Levererad / Dellevererad', 'Orderns leveransflaggor i ADempiere. Levererad = Ja när alla rader är fullt levererade. Dellevererad = Ja när ordern har levererats i flera omgångar.'),
		array('Kanal', 'Webb = säljare "Weborder". Telefon / E-post / Fax = ordermetod som säljaren angett på ordern (fylld på ca 5 % av de manuella ordrarna). Butik = manuell order som hämtas i butiken. Övrig manuell = resten av de manuella ordrarna, där kanalen inte är angiven.'),
		array('Kundtyp', 'Företag = kunden ligger i affärspartnergruppen Företagskund. Allt annat är Privat (även anställda, leverantörer, återförsäljare). Kundgrupp visar den exakta gruppen.'),
		array('Kund-ID', 'Kundnumret (affärspartnerns nummer i ADempiere). Samma kund har samma nummer över tid.'),
		array('Huvudkategori / Överkategori / Kategori', 'Produktkategoriträdet i ADempiere. Huvudkategori = trädets översta nivå, Överkategori = nivån under den, Kategori = produktens egen kategori. Kategorisökväg visar hela kedjan. Rader utan produkt (avgifter) har "(ingen kategori)". Kategorin "** DOLD **" innehåller gratis friförsäkring och rabattrader.'),
		array('Artikelnummer / Produktnamn', 'Produktens artikelnummer och namn. Avgiftsrader saknar artikelnummer.'),
		array('Radtyp', 'Vara, Tjänst (t.ex. frakt och friförsäkring), Resurs eller Avgift. Filtrera på Vara för ren varuförsäljning. Orderrabatter ligger som egna rader (artikel rab, "Övrig rabatt") med negativt värde.'),
		array('Antal rader i ordern', 'Antal orderrader i hela ordern (samma värde på alla rader i ordern). Summera inte kolumnen.'),
		array('Antal enheter / Levererat antal / Kvarstående antal', 'Beställt antal på raden, hur mycket av det som är levererat, och resten.'),
		array('Värde exkl moms', 'Radens nettobelopp i kronor exklusive moms, efter rabatt. Ordrar i andra valutor är omräknade.'),
		array('Kvarstående värde', 'Den del av radens värde som inte är levererad ännu (värde × kvarstående antal / beställt antal).'),
		array('Varukostnad', 'Bokförd kostnad för sålda varor (konto 4010) på radens leveranser. Det som inte är levererat än, och rader som saknar bokning, värderas med radens inköpspris. Storkameror från de stora varumärkena visar ofta negativt täckningsbidrag eftersom leverantörernas kampanjstöd inte ligger i produktkostnaden.'),
		array('Krediterat antal / Krediterat belopp', 'Returer och krediteringar som hör till raden: kreditfakturor kopplade via returärende (RMA) till radens leverans. Beloppet är exkl moms och positivt. Dra av det från värdet för nettoförsäljning efter returer. Krediteringar utan returärende (ca 1 MSEK per år, ofta avgifter) kan inte knytas till en orderrad och syns inte här.'),
		array('Lagersaldo / Tillgängligt i lager / Beställt hos leverantör', 'Bara i öppen orderstock. Produktens saldo i Umeå (Standard) just nu, saldo minus reserverat, och antal på öppna inköpsorder.'),
		array('Leverantör', 'Bara i öppen orderstock. Produktens nuvarande leverantör.'),
		array('Beräknat leveransdatum / precision', 'Bara i öppen orderstock. Orderradens utlovade datum och hur exakt det är (på dagen, samma vecka, del av månad, samma månad, okänt).'),
		array('Restorsak', 'Bara i öppen orderstock, härledd: Finns i lager, ej skickad (saldot täcker raden) · Delvis i lager · Väntar på inleverans (beställd hos leverantör) · Saknas i lager, ej beställd · Tjänst/avgift · Levererad (raden är klar men ordern har andra öppna rader).'),
	);
	foreach ($defs as $d) { echo "<tr><td><b>" . $h($d[0]) . "</b></td><td>" . $h($d[1]) . "</td></tr>\n"; }
	echo "</tbody></table>\n";
	echo "<p class=\"se-note2\">Siffror för tidigare dagar kan ändras om en order senare annulleras, levereras eller krediteras. "
	   . "Format: Standard har komma som kolumnavgränsare, punkt som decimaltecken och UTF-8; Svenskt Excel har semikolon, decimalkomma och UTF-8 med BOM.</p>\n";
	echo "</details></div>\n";

	include_once("footer.php");
?>
