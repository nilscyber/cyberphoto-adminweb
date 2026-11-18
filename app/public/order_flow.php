<?php
	include_once("top.php");
	include_once("header.php");

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"goods_inflow.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"order_flow.css?ver=ad" . date("ynjGi") . "\">\n";

	// Period: standard = senaste 7 hela dagarna (igår och en vecka bakåt, dagens datum ingår inte)
	$today = new DateTime('today');
	$defTo = (clone $today)->modify('-1 day')->format('Y-m-d');
	$defFrom = (clone $today)->modify('-7 days')->format('Y-m-d');
	$validDate = function ($s) { return is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) && strtotime($s) !== false; };
	$from = (isset($_GET['from']) && $validDate($_GET['from'])) ? $_GET['from'] : $defFrom;
	$to   = (isset($_GET['to'])   && $validDate($_GET['to']))   ? $_GET['to']   : $defTo;
	if ($from > $to) { $tmp = $from; $from = $to; $to = $tmp; }
	if ($to > $today->format('Y-m-d')) { $to = $today->format('Y-m-d'); if ($from > $to) { $from = $to; } }
	$maxDays = COrderFlow::MAX_DAYS; $clamped = false; $tooOld = false;
	// frågan läser alla lagerhändelser från periodens start till nu, så startdatumet får inte ligga för långt bak
	$minFrom = (clone $today)->modify(COrderFlow::MAX_BACK)->format('Y-m-d');
	if ($from < $minFrom) { $from = $minFrom; $tooOld = true; if ($to < $from) { $to = date('Y-m-d', strtotime($from . ' +6 days')); } }
	if (round((strtotime($to) - strtotime($from)) / 86400) > $maxDays - 1) {	// round: dygnet då klockan ställs om är inte 24 timmar
		$from = date('Y-m-d', strtotime($to . ' -' . ($maxDays - 1) . ' days')); $clamped = true;
	}

	// Filter
	$filters = array(
		'channel'  => array('label' => 'Kanal', 'opts' => array('all' => 'Alla ordrar', 'web' => 'Webbordrar', 'manual' => 'Manuella ordrar'),
		                    'text' => array('all' => '', 'web' => 'bara webbordrar', 'manual' => 'bara manuella ordrar')),
		'dropship' => array('label' => 'Direktleverans', 'opts' => array('all' => 'Ta med', 'exclude' => 'Uteslut', 'only' => 'Bara direktleverans'),
		                    'text' => array('all' => '', 'exclude' => 'direktleveransrader uteslutna', 'only' => 'bara direktleveransrader')),
		'tradein'  => array('label' => 'Inbytesvaror', 'opts' => array('all' => 'Ta med', 'exclude' => 'Uteslut', 'only' => 'Bara inbyten'),
		                    'text' => array('all' => '', 'exclude' => 'inbytesvaror uteslutna', 'only' => 'bara inbytesvaror')),
		'preorder' => array('label' => 'Förbeställningar', 'opts' => array('exclude' => 'Uteslut', 'include' => 'Ta med'),
		                    'text' => array('exclude' => '', 'include' => 'förbeställningar medräknade')),
	);
	$opt = array(); $optText = array(); $optQuery = "";
	foreach ($filters as $k => $f) {
		$def = key($f['opts']);
		$opt[$k] = COrderFlow::opt(isset($_GET[$k]) ? $_GET[$k] : $def, array_keys($f['opts']), $def);
		if ($f['text'][$opt[$k]] !== '') { $optText[] = $f['text'][$opt[$k]]; }
		if ($opt[$k] !== $def) { $optQuery .= "&amp;$k=" . $opt[$k]; }
	}
	$optLine = $optText ? ", " . implode(", ", $optText) : "";

	$nf = function ($n) { return number_format((float)$n, 0, ',', ' '); };
	$pct = function ($a, $b, $dec = 0) { return $b > 0 ? number_format(100 * $a / $b, $dec, ',', ' ') . ' %' : '–'; };
	$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };

	echo "<h1>Orderflöde</h1>\n";
	echo "<p class=\"gi-intro\">Hur många kundordrar som kommer in per dag, hur stor del av dem som är kompletta direkt vid köp – allt på ordern fanns fritt i lager – "
	   . "och hur fort de går ut, räknat i arbetsdagar. Standardperioden är de senaste sju hela dagarna; dagens datum ingår inte eftersom dagen inte är komplett.</p>\n";

	echo "<form method=\"GET\" class=\"gi-form\">\n";
	echo "<div class=\"filter-bar\">\n";
	echo "<label>Från <input type=\"date\" name=\"from\" value=\"" . $h($from) . "\" max=\"" . $today->format('Y-m-d') . "\"></label>\n";
	echo "<label>Till <input type=\"date\" name=\"to\" value=\"" . $h($to) . "\" max=\"" . $today->format('Y-m-d') . "\"></label>\n";
	foreach ($filters as $k => $f) {
		echo "<label>" . $f['label'] . " <select name=\"$k\">";
		foreach ($f['opts'] as $v => $lab) { echo "<option value=\"$v\"" . ($opt[$k] === $v ? " selected" : "") . ">$lab</option>"; }
		echo "</select></label>\n";
	}
	echo "<button type=\"submit\" class=\"gi-btn\">Visa</button>\n";
	$q = function ($f, $t) use ($optQuery) { return $_SERVER['PHP_SELF'] . "?from=$f&amp;to=$t" . $optQuery; };
	$m1 = (clone $today)->modify('first day of last month')->format('Y-m-d'); $m2 = (clone $today)->modify('last day of last month')->format('Y-m-d');
	echo "<span class=\"gi-quick\">Snabbval: <a href=\"" . $q($defFrom, $defTo) . "\">senaste veckan</a> · <a href=\"" . $q((clone $today)->modify('-30 days')->format('Y-m-d'), $defTo) . "\">senaste 30 dagarna</a> · <a href=\"" . $q($m1, $m2) . "\">förra månaden</a></span>\n";
	echo "</div>\n";
	echo "</form>\n";
	if ($tooOld) echo "<p class=\"gi-note\">Perioden kan börja tidigast " . $h($minFrom) . " (två år bakåt); visar " . $h($from) . " – " . $h($to) . ".</p>\n";
	elseif ($clamped) echo "<p class=\"gi-note\">Perioden är begränsad till $maxDays dagar; visar " . $h($from) . " – " . $h($to) . ".</p>\n";

	@set_time_limit(60);
	$of = new COrderFlow();
	$res = $of->fetch($from, $to);
	$R = ($res && !$of->error) ? $of->build($res, $from, $to, $opt) : null;

	if ($of->error) {
		echo "<p class=\"gi-note\">Kunde inte läsa från ADempiere: " . $h($of->error) . "</p>\n";
	} elseif ($R['ord']['all']['n'] == 0) {
		echo "<p class=\"gi-note\">Inga ordrar " . $h($from) . " – " . $h($to) . $optLine . ".</p>\n";
	} else {
		$TO = $R['ord']['all']; $TL = $R['lin']['all'];
		$BK = array('Samma arbetsdag', '1 arbetsdag', '2–3 arbetsdagar', '4–5 arbetsdagar', '6–10 arbetsdagar', 'Mer än 10 arbetsdagar', 'Ej levererad ännu');
		$legend = "<div class=\"gi-legend\"><span><i class=\"sw of-S\"></i>Fritt i lager vid köp</span><span><i class=\"sw of-P\"></i>I lager men lovat till tidigare ordrar</span>"
		        . "<span><i class=\"sw of-N\"></i>Saknades i lager</span>" . ($TL['D'] > 0 ? "<span><i class=\"sw of-D\"></i>Direktleverans från leverantör</span>" : "") . "</div>\n";
		// länk som öppnar listan bakom en siffra: data-attributen är filtret (se order_flow.js)
		$pick = function ($text, $kind, $attrs, $title) use ($h) {
			$s = ""; foreach ($attrs as $k => $v) { $s .= " data-$k=\"" . $h($v) . "\""; }
			return "<a href=\"#\" class=\"of-pick\" data-kind=\"$kind\"$s data-title=\"" . $h($title) . "\">$text</a>";
		};

		$exclNote = array();
		if ($R['excl']['pre'] > 0) { $exclNote[] = $nf($R['excl']['pre']) . " " . ($R['excl']['pre'] == 1 ? "order med förbeställd vara är borttagen" : "ordrar med förbeställda varor är borttagna"); }
		if ($R['excl']['redated'] > 0) { $exclNote[] = $nf($R['excl']['redated']) . " " . ($R['excl']['redated'] == 1 ? "order levererad före sitt orderdatum är borttagen" : "ordrar levererade före sitt orderdatum är borttagna"); }
		echo "<p class=\"gi-period\">Period " . $h($from) . " – " . $h($to) . $optLine . ". " . ($exclNote ? implode(", ", $exclNote) . ". " : "") . "Status per " . date('Y-m-d H:i') . ".</p>\n";

		// KPI
		$split = ($R['ord']['web']['n'] > 0 && $R['ord']['man']['n'] > 0);
		echo "<div class=\"gi-kpis\">\n";
		echo "<div class=\"gi-kpi\"><div class=\"v\">" . $nf($TO['n']) . "</div><div class=\"l\">kundordrar i perioden</div><div class=\"d\">"
		   . ($split ? $nf($R['ord']['web']['n']) . " webb, " . $nf($R['ord']['man']['n']) . " manuella · " : "") . $nf($TL['n']) . " orderrader</div></div>\n";
		echo "<div class=\"gi-kpi\"><div class=\"v\">" . $pct($TO['S'], $TO['n']) . "</div><div class=\"l\">av ordrarna var kompletta vid köp</div><div class=\"d\">" . $nf($TO['S']) . " ordrar hade allt fritt i lager"
		   . ($split ? " · webb " . $pct($R['ord']['web']['S'], $R['ord']['web']['n']) . ", manuella " . $pct($R['ord']['man']['S'], $R['ord']['man']['n']) : "") . "</div></div>\n";
		echo "<div class=\"gi-kpi\"><div class=\"v\">" . $pct($TO['same'], $TO['n']) . "</div><div class=\"l\">av ordrarna gick ut samma arbetsdag</div><div class=\"d\">" . $nf($TO['same']) . " ordrar · "
		   . $pct($TO['w3'], $TO['n']) . " inom 3 arbetsdagar</div></div>\n";
		echo "<div class=\"gi-kpi\"><div class=\"v\">" . $pct($TL['S'], $TL['n']) . "</div><div class=\"l\">av orderraderna fanns fritt i lager vid köp</div><div class=\"d\">saknades: " . $nf($TL['N']) . " rader (" . $pct($TL['N'], $TL['n'], 1) . ")"
		   . " · lovat till tidigare ordrar: " . $nf($TL['P']) . "</div></div>\n";
		echo "</div>\n";

		// ---------- per dag
		echo "<h2 class=\"gi-h2\">Ordrar per dag</h2>\n";
		echo "<p class=\"gi-sub\">Dagens inkomna ordrar, uppdelade efter hur lagret såg ut i köpögonblicket. En order räknas efter sin sämsta rad: saknades en vara räknas hela ordern som ”saknades”. "
		   . "Linjerna under visar hur stor andel av dagens ordrar som var kompletta vid köp och hur stor andel som gick ut samma arbetsdag.</p>\n";
		echo "<div class=\"gi-card\">\n" . $legend;
		echo "<div class=\"gi-scroll\" id=\"of-day\"></div>\n";
		echo "<div class=\"gi-legend small of-linelegend\"><span><i class=\"sw of-line of-S\"></i>Andel kompletta vid köp</span><span><i class=\"sw of-line of-same\"></i>Andel ute samma arbetsdag</span></div>\n";
		echo "<div class=\"gi-scroll\" id=\"of-daypct\"></div>\n";
		echo "<p class=\"gi-hint\">Klicka på en stapel för att se ordrarna bakom den.</p>\n";
		echo "<details><summary>Visa som tabell</summary>\n";
		echo "<div class=\"gi-card-actions\"><button type=\"button\" class=\"gi-export\" data-target=\"of-daytable\" data-name=\"per-dag\">Exportera CSV</button></div>\n";
		echo "<div class=\"gi-tablewrap\"><table class=\"table-list\" id=\"of-daytable\"><thead><tr><th>Datum</th><th>Dag</th><th class=\"n\">Ordrar</th><th class=\"n\">Kompletta vid köp</th><th class=\"n\">Andel kompletta</th><th class=\"n\">Lovat</th>"
		   . "<th class=\"n\">Saknades</th><th class=\"n\">Direktleverans</th><th class=\"n\">Ut samma arbetsdag</th><th class=\"n\">Andel samma dag</th><th class=\"n\">Orderrader</th><th class=\"n\">Fritt i lager</th><th class=\"n\">Andel fritt</th></tr></thead><tbody>\n";
		$wk = array(1 => 'mån', 'tis', 'ons', 'tor', 'fre', 'lör', 'sön');
		$stTitle = array('S' => 'kompletta vid köp', 'P' => 'i lager men lovat till tidigare ordrar', 'N' => 'något saknades i lager', 'D' => 'direktleverans från leverantör');
		foreach ($R['days'] as $d => $g) {
			$num = function ($v, $st) use ($nf, $pick, $d, $stTitle) { return $v > 0 ? $pick($nf($v), 'o', array('day' => $d, 'st' => $st), "Ordrar $d: " . $stTitle[$st]) : '–'; };
			echo "<tr><td>$d</td><td>" . $wk[(int)date('N', strtotime($d))] . "</td><td class=\"n\">" . ($g['n'] > 0 ? $pick($nf($g['n']), 'o', array('day' => $d), "Ordrar $d") : '–') . "</td>"
			   . "<td class=\"n\">" . $num($g['S'], 'S') . "</td><td class=\"n\">" . $pct($g['S'], $g['n']) . "</td><td class=\"n\">" . $num($g['P'], 'P') . "</td><td class=\"n\">" . $num($g['N'], 'N') . "</td><td class=\"n\">" . $num($g['D'], 'D') . "</td>"
			   . "<td class=\"n\">" . $nf($g['same']) . "</td><td class=\"n\">" . $pct($g['same'], $g['n']) . "</td><td class=\"n\">" . $nf($g['ln']) . "</td><td class=\"n\">" . $nf($g['lS']) . "</td><td class=\"n\">" . $pct($g['lS'], $g['ln']) . "</td></tr>\n";
		}
		echo "</tbody><tfoot><tr class=\"total-row\"><td>Summa</td><td></td><td class=\"n\">" . $nf($TO['n']) . "</td><td class=\"n\">" . $nf($TO['S']) . "</td><td class=\"n\">" . $pct($TO['S'], $TO['n']) . "</td><td class=\"n\">" . $nf($TO['P']) . "</td>"
		   . "<td class=\"n\">" . $nf($TO['N']) . "</td><td class=\"n\">" . $nf($TO['D']) . "</td><td class=\"n\">" . $nf($TO['same']) . "</td><td class=\"n\">" . $pct($TO['same'], $TO['n']) . "</td><td class=\"n\">" . $nf($TL['n']) . "</td><td class=\"n\">" . $nf($TL['S']) . "</td><td class=\"n\">" . $pct($TL['S'], $TL['n']) . "</td></tr></tfoot></table></div>\n";
		echo "</details>\n</div>\n";

		// ---------- tid till utleverans
		echo "<h2 class=\"gi-h2\">Tid till utleverans</h2>\n";
		echo "<p class=\"gi-sub\">Arbetsdagar från orderdagen tills hela ordern, respektive den enskilda raden, var levererad. En order som läggs från kl " . COrderFlow::CUTOFF_HOUR . " eller på en helg räknas från nästa arbetsdag. "
		   . "Färgen visar lagerläget vid köpet, så staplarna till höger visar också vad förseningen berodde på.</p>\n";
		$histTable = function ($kind, $tot, $what) use ($R, $BK, $nf, $pct, $pick) {
			$s = "<details><summary>Visa som tabell</summary><div class=\"gi-tablewrap\"><table class=\"table-list\"><thead><tr><th>Levererad</th><th class=\"n\">$what</th><th class=\"n\">Andel</th><th class=\"n\">Ackumulerat</th>"
			   . "<th class=\"n\">Fritt i lager</th><th class=\"n\">Lovat</th><th class=\"n\">Saknades</th><th class=\"n\">Direktleverans</th></tr></thead><tbody>\n";
			$acc = 0;
			foreach ($R['hist'][$kind] as $b => $g) {
				$n = array_sum($g); $acc += $n;
				$s .= "<tr><td>" . $BK[$b] . "</td><td class=\"n\">" . ($n > 0 ? $pick($nf($n), $kind, array('b' => $b), "$what: " . mb_strtolower($BK[$b])) : '–') . "</td><td class=\"n\">" . $pct($n, $tot, 1) . "</td>"
				    . "<td class=\"n\">" . ($b < COrderFlow::BUCKET_UNDELIVERED ? $pct($acc, $tot, 1) : '–') . "</td>";
				foreach (array('S', 'P', 'N', 'D') as $st) { $s .= "<td class=\"n\">" . ($g[$st] > 0 ? $nf($g[$st]) : '–') . "</td>"; }
				$s .= "</tr>\n";
			}
			return $s . "</tbody></table></div></details>\n";
		};
		echo "<div class=\"gi-grid2\">\n";
		echo "<div class=\"gi-card\"><h3>Ordrar</h3>\n" . str_replace('gi-legend', 'gi-legend small', $legend) . "<div class=\"gi-scroll\" id=\"of-hist-o\"></div>\n" . $histTable('o', $TO['n'], 'Ordrar') . "</div>\n";
		echo "<div class=\"gi-card\"><h3>Orderrader</h3>\n" . str_replace('gi-legend', 'gi-legend small', $legend) . "<div class=\"gi-scroll\" id=\"of-hist-l\"></div>\n" . $histTable('l', $TL['n'], 'Orderrader') . "</div>\n";
		echo "</div>\n";

		// ---------- webb / manuella, skickas / hämtas
		echo "<h2 class=\"gi-h2\">Webbordrar och manuella ordrar</h2>\n";
		echo "<p class=\"gi-sub\">Samma mått uppdelade efter hur ordern kom in och hur den lämnas ut. Manuella ordrar är de som en säljare har lagt; webbordrar kommer från webbutiken. "
		   . "Andelarna ”inom” är ackumulerade: inom 3 arbetsdagar innehåller också de som gick ut samma dag.</p>\n";
		$GL = array('all' => array('Alla ordrar', 'Alla orderrader'), 'web' => array('Webbordrar', 'Rader på webbordrar'), 'man' => array('Manuella ordrar', 'Rader på manuella ordrar'),
		            'ship' => array('Skickas', 'Rader som skickas'), 'pick' => array('Hämtas i butik', 'Rader som hämtas i butik'));
		$groupTable = function ($kind, $id) use ($R, $GL, $nf, $pct, $pick) {
			$o = ($kind === 'o');
			$s = "<div class=\"gi-tablewrap\"><table class=\"table-list\" id=\"$id\"><thead><tr><th>" . ($o ? "Ordrar" : "Orderrader") . "</th><th class=\"n\">Antal</th><th class=\"n\">" . ($o ? "Kompletta vid köp" : "Fritt i lager vid köp") . "</th><th class=\"n\">Andel</th>"
			   . "<th class=\"n\">" . ($o ? "Något saknades" : "Saknades") . "</th><th class=\"n\">Andel</th><th class=\"n\">Lovat</th><th class=\"n\">Samma arbetsdag</th><th class=\"n\">Inom 1</th><th class=\"n\">Inom 3</th><th class=\"n\">Inom 5</th><th class=\"n\">Inom 10</th><th class=\"n\">Ej levererade</th></tr></thead><tbody>\n";
			foreach ($R[$o ? 'ord' : 'lin'] as $g => $a) {
				if ($a['n'] == 0) { continue; }
				$lab = $GL[$g][$o ? 0 : 1];
				$s .= "<tr" . ($g === 'ship' ? " class=\"of-sep\"" : "") . "><td>" . $pick($lab, $kind, array('g' => $g), $lab) . "</td><td class=\"n\">" . $nf($a['n']) . "</td><td class=\"n\">" . $nf($a['S']) . "</td><td class=\"n\"><b>" . $pct($a['S'], $a['n'], 1) . "</b></td>"
				    . "<td class=\"n\">" . ($a['N'] > 0 ? $pick($nf($a['N']), $kind, array('g' => $g, 'st' => 'N'), "$lab: saknades i lager vid köp") : '–') . "</td><td class=\"n\">" . $pct($a['N'], $a['n'], 1) . "</td>"
				    . "<td class=\"n\">" . ($a['P'] > 0 ? $pick($nf($a['P']), $kind, array('g' => $g, 'st' => 'P'), "$lab: i lager men lovat till tidigare ordrar") : '–') . "</td>"
				    . "<td class=\"n\"><b>" . $pct($a['same'], $a['n'], 1) . "</b></td><td class=\"n\">" . $pct($a['w1'], $a['n'], 1) . "</td><td class=\"n\">" . $pct($a['w3'], $a['n'], 1) . "</td><td class=\"n\">" . $pct($a['w5'], $a['n'], 1) . "</td><td class=\"n\">" . $pct($a['w10'], $a['n'], 1) . "</td>"
				    . "<td class=\"n\">" . ($a['undel'] > 0 ? $pick($nf($a['undel']), $kind, array('g' => $g, 'b' => COrderFlow::BUCKET_UNDELIVERED), "$lab: ej levererade ännu") : '–') . "</td></tr>\n";
			}
			return $s . "</tbody></table></div>\n";
		};
		echo "<div class=\"gi-card\">\n";
		echo "<div class=\"gi-card-actions\"><button type=\"button\" class=\"gi-export\" data-target=\"of-grp-o\" data-name=\"ordrar-per-kanal\">Exportera CSV</button></div>\n<h3>Ordrar</h3>\n" . $groupTable('o', 'of-grp-o');
		echo "<div class=\"gi-card-actions\"><button type=\"button\" class=\"gi-export\" data-target=\"of-grp-l\" data-name=\"orderrader-per-kanal\">Exportera CSV</button></div>\n<h3 class=\"of-h3gap\">Orderrader</h3>\n" . $groupTable('l', 'of-grp-l');
		echo "<p class=\"gi-hint\">Klicka på en rubrik eller en siffra för att se ordrarna eller raderna bakom den.</p>\n";
		echo "</div>\n";

		// ---------- varför inte samma arbetsdag
		$lateO = $TO['n'] - $TO['same']; $lateL = array_sum($R['lwhy']);
		echo "<h2 class=\"gi-h2\">Varför gick inte allt ut samma arbetsdag?</h2>\n";
		echo "<p class=\"gi-sub\">" . $nf($lateO) . " av " . $nf($TO['n']) . " ordrar (" . $pct($lateO, $TO['n'], 1) . ") gick inte ut samma arbetsdag. Till vänster vad som stoppade ordern, "
		   . "till höger varför rader som själva fanns fritt i lager ändå fick vänta.</p>\n";
		echo "<div class=\"gi-grid2\">\n";
		echo "<div class=\"gi-card\"><h3>Ordrar som inte gick ut samma arbetsdag</h3>\n<div class=\"gi-scroll\" id=\"of-why-o\"></div>\n</div>\n";
		echo "<div class=\"gi-card\"><h3>Rader som fanns i lager men fick vänta" . ($lateL > 0 ? " (" . $nf($lateL) . " rader)" : "") . "</h3>\n<div class=\"gi-scroll\" id=\"of-why-l\"></div>\n</div>\n";
		echo "</div>\n";
		echo "<p class=\"gi-hint\">Klicka på en stapel för att se ordrarna eller raderna bakom den.</p>\n";

		// ---------- produkter
		echo "<h2 class=\"gi-h2\">Produkter</h2>\n";
		echo "<p class=\"gi-sub\">Varje produkt som har sålts i perioden: hur ofta den såldes och hur ofta den saknades i lager i köpögonblicket. ”Lovat” är rader där varan fanns men redan var reserverad för tidigare ordrar. "
		   . "Jämför ”Sakn. webb” med ”Sakn. man.” för att se om det är säljarna eller webbkunderna som väljer varor vi inte har hemma.</p>\n";
		echo "<div class=\"gi-card\" id=\"of-prod-card\">\n";
		echo "<div class=\"of-toolbar\"><input type=\"search\" id=\"of-prod-q\" placeholder=\"Sök artikel, produkt, varumärke eller kategori\" autocomplete=\"off\">"
		   . "<label><input type=\"checkbox\" id=\"of-prod-miss\"> bara produkter där något saknades</label>"
		   . "<span class=\"gi-detail-count\" id=\"of-prod-count\"></span>"
		   . "<button type=\"button\" class=\"gi-export\" id=\"of-prod-export\">Exportera CSV</button></div>\n";
		echo "<div class=\"gi-det-scroll\"><table class=\"table-list of-sortable\" id=\"of-prod\"><thead><tr>"
		   . "<th data-sort=\"value\">Artikel</th><th data-sort=\"name\">Produkt</th><th data-sort=\"brand\">Varumärke</th>"
		   . "<th class=\"n\" data-sort=\"1\" title=\"Antal orderrader med produkten\">Rader</th><th class=\"n\" data-sort=\"2\" title=\"Beställt antal\">Antal</th><th class=\"n\" data-sort=\"3\" title=\"Rader på manuella ordrar\">Manuella</th>"
		   . "<th class=\"n\" data-sort=\"4\" title=\"Rader där varan saknades i lager vid köpet\">Saknades</th><th class=\"n\" data-sort=\"share\" title=\"Saknades / rader\">Andel</th>"
		   . "<th class=\"n\" data-sort=\"6\" title=\"Saknades, rader på webbordrar\">Sakn. webb</th><th class=\"n\" data-sort=\"7\" title=\"Saknades, rader på manuella ordrar\">Sakn. man.</th>"
		   . "<th class=\"n\" data-sort=\"5\" title=\"I lager men lovat till tidigare ordrar\">Lovat</th><th class=\"n\" data-sort=\"8\" title=\"Median arbetsdagar till utleverans, levererade rader\">Median dagar</th>"
		   . "<th class=\"n\" data-sort=\"9\" title=\"Rader som inte är levererade ännu\">Ej lev.</th><th class=\"n\" data-sort=\"10\" title=\"Lagersaldo just nu\">Lager nu</th></tr></thead><tbody></tbody></table></div>\n";
		echo "<p class=\"gi-hint\"><span id=\"of-prod-moretext\"></span> <a href=\"#\" id=\"of-prod-more\" hidden>Visa fler</a> Klicka på en rubrik för att sortera, på antalet rader för att se orderraderna.</p>\n";
		echo "</div>\n";

		// ---------- varumärken och kategorier
		echo "<h2 class=\"gi-h2\">Varumärken och kategorier</h2>\n";
		echo "<p class=\"gi-sub\">Orderrader per varumärke och per kategori, sorterade efter hur många rader som saknades i lager vid köpet.</p>\n";
		$dimTable = function ($rows, $id, $head, $key, $name) use ($nf, $pct, $pick) {
			$s = "<div class=\"gi-card\"><div class=\"gi-card-actions\"><button type=\"button\" class=\"gi-export\" data-target=\"$id\" data-name=\"$name\">Exportera CSV</button></div><h3>$head</h3>\n"
			   . "<div class=\"gi-det-scroll of-dim\"><table class=\"table-list\" id=\"$id\"><thead><tr><th>$head</th><th class=\"n\">Rader</th><th class=\"n\">Antal</th><th class=\"n\">Saknades</th><th class=\"n\">Andel</th><th class=\"n\">Lovat</th>"
			   . "<th class=\"n\">Samma arbetsdag</th><th class=\"n\">Ej lev.</th></tr></thead><tbody>\n";
			foreach ($rows as $a) {
				$nm = htmlspecialchars($a['name'], ENT_QUOTES, 'UTF-8');
				$s .= "<tr><td>" . $pick($nm, 'l', array($key => $a['i']), $a['name']) . "</td><td class=\"n\">" . $nf($a['n']) . "</td><td class=\"n\">" . $nf($a['q']) . "</td>"
				    . "<td class=\"n\">" . ($a['N'] > 0 ? $pick($nf($a['N']), 'l', array($key => $a['i'], 'st' => 'N'), $a['name'] . ": saknades i lager vid köp") : '–') . "</td><td class=\"n\">" . $pct($a['N'], $a['n']) . "</td>"
				    . "<td class=\"n\">" . ($a['P'] > 0 ? $nf($a['P']) : '–') . "</td><td class=\"n\">" . $pct($a['same'], $a['n']) . "</td><td class=\"n\">" . ($a['undel'] > 0 ? $nf($a['undel']) : '–') . "</td></tr>\n";
			}
			return $s . "</tbody></table></div></div>\n";
		};
		echo "<div class=\"gi-grid2\">\n" . $dimTable($R['brands'], 'of-brands', 'Varumärke', 'bi', 'varumarken') . $dimTable($R['cats'], 'of-cats', 'Kategori', 'ki', 'kategorier') . "</div>\n";

		// ---------- detaljlista (fylls av order_flow.js och flyttas till det man klickade på)
		echo "<div class=\"gi-card gi-detail\" id=\"gi-detail\" hidden>\n";
		echo "<div class=\"gi-detail-head\"><h3 id=\"gi-detail-title\"></h3><span class=\"gi-detail-count\" id=\"gi-detail-count\"></span>"
		   . "<span class=\"gi-detail-actions\"><button type=\"button\" class=\"gi-export\" id=\"of-detail-export\">Exportera CSV</button><button type=\"button\" class=\"gi-detail-close\" id=\"gi-detail-close\">Stäng</button></span></div>\n";
		echo "<div class=\"gi-det-scroll\"><table class=\"table-list\" id=\"of-detail-table\"><thead></thead><tbody></tbody></table></div>\n";
		echo "<p class=\"gi-hint\" id=\"of-detail-more\" hidden></p>\n";
		echo "</div>\n";

		// ---------- metod
		$hol = array();
		foreach (array_unique(array((int)substr($from, 0, 4), (int)substr($to, 0, 4))) as $y) { foreach (COrderFlow::holidays($y) as $hd => $hn) { if ($hd >= $from && $hd <= $to && (int)date('N', strtotime($hd)) < 6) { $hol[] = "$hn $hd"; } } }
		echo "<details class=\"gi-method\"><summary>Så är det räknat</summary><ul>\n";
		echo "<li><b>Ordrar:</b> kundordrar av typen Standard Order som är klara eller under behandling, efter orderdatum. Offerter, utkast, returer och makulerade ordrar ingår inte.</li>\n";
		echo "<li><b>Orderrader:</b> rader med lagerförda artiklar. Frakt, avgifter och tjänster räknas inte.</li>\n";
		echo "<li><b>Lagerläget vid köp</b> är återskapat i efterhand: dagens saldo räknat baklänges genom alla lagerhändelser till det ögonblick då orderraden skapades. "
		   . "<i>Fritt i lager</i> = saldot räckte till raden även efter att tidigare ordrars reservationer var avdragna. <i>Lovat</i> = varan fanns på hyllan men var redan reserverad för tidigare ordrar "
		   . "(den gruppen är en uppskattning, eftersom reservationer som har strukits i efterhand inte går att återskapa exakt). <i>Saknades</i> = saldot var mindre än det beställda antalet.</li>\n";
		echo "<li><b>En order räknas efter sin sämsta rad.</b> ”Komplett vid köp” betyder att alla rader fanns fritt i lager. Väljer du att utesluta inbyten eller direktleverans bedöms ordern på de rader som är kvar.</li>\n";
		echo "<li><b>Arbetsdagar:</b> måndag–fredag utom helgdagar, midsommarafton, julafton och nyårsafton. En order lagd från kl " . COrderFlow::CUTOFF_HOUR . ":00, eller på en dag vi inte arbetar, räknas från nästa arbetsdag. "
		   . "”Samma arbetsdag” = utleveransen har samma datum som den dagen." . ($hol ? " Helgdagar i perioden: " . $h(implode(", ", $hol)) . "." : "") . "</li>\n";
		echo "<li><b>Levererad</b> = datumet på den utleverans som levererade raden färdigt. För ordern gäller den sista raden; en order där någon rad väntar räknas som ej levererad.</li>\n";
		echo "<li><b>Förbeställningar:</b> en order med en vara vars säljstart eller lanseringsdatum ligger efter orderdatumet tas bort helt (om du inte väljer ”Ta med”).</li>\n";
		echo "<li><b>Direktleverans</b> = orderrader som ligger på lagret Direktleverans, det vill säga skickas direkt från leverantören. De har inget eget lager att jämföra med och visas som en egen grupp.</li>\n";
		echo "<li><b>Hämtas i butik:</b> utleveransen görs när kunden hämtar, så de ordrarna säger mer om kunden än om lagret. Titta på ”Skickas” om du vill se lagrets egen takt.</li>\n";
		echo "<li>Ordrar som har fått nytt orderdatum efter att de levererades (leverans före orderdatum) tas bort. De senaste dagarnas ordrar kan fortfarande vara på väg ut – de visas som ”Ej levererad ännu”.</li>\n";
		echo "</ul></details>\n";

		// ---------- data till order_flow.js
		$days = array();
		foreach ($R['days'] as $d => $g) { $days[] = array($d, $g['n'], $g['S'], $g['P'], $g['N'], $g['D'], $g['same'], $g['ln'], $g['lS']); }
		$slug = "";
		foreach ($filters as $k => $f) { if ($opt[$k] !== key($f['opts'])) { $slug .= "_" . $k . "-" . $opt[$k]; } }
		$js = array('from' => $from, 'to' => $to, 'slug' => $slug, 'days' => $days, 'hist' => $R['hist'], 'why' => $R['why'], 'lwhy' => $R['lwhy'],
		            'no' => $TO['n'], 'nl' => $TL['n'], 'bk' => $BK, 'O' => $R['O'], 'L' => $R['L'], 'P' => $R['P'], 'B' => $R['B'], 'K' => $R['K'], 'products' => $R['products']);
		echo "<script>window.OFLOW = " . json_encode($js, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE) . ";</script>\n";
		echo "<div id=\"gi-tip\" hidden></div>\n";
		echo "<script src=\"order_flow.js?ver=ad" . date("ynjGi") . "\"></script>\n";
	}

	include_once("footer.php");
?>
