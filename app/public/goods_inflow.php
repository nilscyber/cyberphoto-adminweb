<?php
	include_once("top.php");
	include_once("header.php");

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"goods_inflow.css?ver=ad" . date("ynjGi") . "\">\n";

	// Period: standard = senaste 7 hela dagarna (igår och en vecka bakåt, dagens datum ingår inte)
	$today = new DateTime('today');
	$defTo = (clone $today)->modify('-1 day')->format('Y-m-d');
	$defFrom = (clone $today)->modify('-7 days')->format('Y-m-d');
	$validDate = function ($s) { return is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) && strtotime($s) !== false; };
	$from = (isset($_GET['from']) && $validDate($_GET['from'])) ? $_GET['from'] : $defFrom;
	$to   = (isset($_GET['to'])   && $validDate($_GET['to']))   ? $_GET['to']   : $defTo;
	if ($from > $to) { $tmp = $from; $from = $to; $to = $tmp; }
	$maxDays = 93; $clamped = false;
	if ((strtotime($to) - strtotime($from)) / 86400 > $maxDays - 1) {
		$from = date('Y-m-d', strtotime($to . ' -' . ($maxDays - 1) . ' days')); $clamped = true;
	}
	$wh = 1000000;
	$tradein = CGoodsInflow::tradeinMode(isset($_GET['tradein']) ? $_GET['tradein'] : 'all');
	$tradeinText = array('all' => '', 'exclude' => ', inbytesvaror uteslutna', 'only' => ', bara inbytesvaror');

	$nf = function ($n) { return number_format((float)$n, 0, ',', ' '); };
	$pct = function ($a, $b, $dec = 0) { return $b > 0 ? number_format(100 * $a / $b, $dec, ',', ' ') . ' %' : '–'; };
	$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
	// produktnamn som länk till produktkortet + märke för inbytesvaror
	$plink = function ($value, $id, $name) use ($h) { return "<a href=\"" . $h(CGoodsInflow::productUrl($value, $id)) . "\" target=\"_blank\" rel=\"noopener\">" . $h($name) . "</a>"; };
	$tbadge = function ($yn) { return $yn ? " <span class=\"badge\">inbyte</span>" : ""; };

	echo "<h1>Inleveransflöde</h1>\n";
	echo "<p class=\"gi-intro\">Hur mycket av det som levereras in till Umeå (Standard) går ut direkt, hur mycket täcker en kundorder som ändå får vänta, och hur mycket läggs på hyllan. "
	   . "Standardperioden är de senaste sju hela dagarna; dagens datum ingår inte eftersom dagen inte är komplett.</p>\n";

	echo "<form method=\"GET\" class=\"gi-form\">\n";
	echo "<div class=\"filter-bar\">\n";
	echo "<label>Från <input type=\"date\" name=\"from\" value=\"" . $h($from) . "\" max=\"" . $today->format('Y-m-d') . "\"></label>\n";
	echo "<label>Till <input type=\"date\" name=\"to\" value=\"" . $h($to) . "\" max=\"" . $today->format('Y-m-d') . "\"></label>\n";
	echo "<label>Inbytesvaror <select name=\"tradein\">";
	foreach (array('all' => 'Ta med', 'exclude' => 'Uteslut', 'only' => 'Bara inbyten') as $k => $lab) {
		echo "<option value=\"$k\"" . ($tradein === $k ? " selected" : "") . ">$lab</option>";
	}
	echo "</select></label>\n";
	echo "<button type=\"submit\" class=\"gi-btn\">Visa</button>\n";
	$q = function ($f, $t) use ($tradein) { return $_SERVER['PHP_SELF'] . "?from=$f&amp;to=$t" . ($tradein !== 'all' ? "&amp;tradein=$tradein" : ""); };
	$m1 = (clone $today)->modify('first day of last month')->format('Y-m-d'); $m2 = (clone $today)->modify('last day of last month')->format('Y-m-d');
	echo "<span class=\"gi-quick\">Snabbval: <a href=\"" . $q($defFrom, $defTo) . "\">senaste veckan</a> · <a href=\"" . $q((clone $today)->modify('-30 days')->format('Y-m-d'), $defTo) . "\">senaste 30 dagarna</a> · <a href=\"" . $q($m1, $m2) . "\">förra månaden</a></span>\n";
	echo "</div>\n";
	echo "</form>\n";
	if ($clamped) echo "<p class=\"gi-note\">Perioden är begränsad till $maxDays dagar; visar " . $h($from) . " – " . $h($to) . ".</p>\n";

	$gi = new CGoodsInflow();
	$rows = $gi->fetchReceipts($from, $to, $wh, $tradein);
	$orders = $gi->error ? array() : $gi->fetchOrders($from, $to, $wh, $tradein);

	if ($gi->error) {
		echo "<p class=\"gi-note\">Kunde inte läsa från ADempiere: " . $h($gi->error) . "</p>\n";
	} elseif (count($rows) == 0) {
		echo "<p class=\"gi-note\">Inga inleveranser " . $h($from) . " – " . $h($to) . $tradeinText[$tradein] . ".</p>\n";
	} else {
		$agg = $gi->aggregate($rows, $orders);
		$T = $agg['tot'];
		$perDay = $agg['ndays'] > 0 ? $T['qty'] / $agg['ndays'] : 0;
		$rowsDay = $agg['ndays'] > 0 ? $T['rows'] / $agg['ndays'] : 0;
		$waitQty = $T['alloc'] - $T['a01'];
		$slut7 = $T['s1'] + $T['s7'];

		echo "<p class=\"gi-period\">Period " . $h($from) . " – " . $h($to) . ", " . $agg['ndays'] . " dagar med inleverans" . $tradeinText[$tradein] . ". Status per " . date('Y-m-d H:i') . ".</p>\n";

		// KPI
		echo "<div class=\"gi-kpis\">\n";
		echo "<div class=\"gi-kpi\"><div class=\"v\">" . $nf($perDay) . "</div><div class=\"l\">enheter in per dag</div><div class=\"d\">" . $nf($T['qty']) . " enheter, " . $nf($T['rows']) . " rader, " . $nf($rowsDay) . " rader per dag</div></div>\n";
		echo "<div class=\"gi-kpi\"><div class=\"v\">" . $pct($T['alloc'], $T['qty']) . "</div><div class=\"l\">täckte en väntande kundorder</div><div class=\"d\">" . $nf($T['alloc']) . " enheter</div></div>\n";
		echo "<div class=\"gi-kpi\"><div class=\"v\">" . $pct($T['a01'], $T['alloc']) . "</div><div class=\"l\">av dem gick ut inom 1 dag</div><div class=\"d\">" . $nf($T['a01']) . " enheter, " . $pct($T['a01'], $T['qty']) . " av allt som kom in</div></div>\n";
		echo "<div class=\"gi-kpi\"><div class=\"v\">" . $pct($slut7, $T['tl']) . "</div><div class=\"l\">av det som gick till lager var slutsålt inom 7 dagar</div><div class=\"d\">" . $nf($slut7) . " av " . $nf($T['tl']) . " enheter till lager</div></div>\n";
		echo "</div>\n";

		// Flöde
		echo "<h2 class=\"gi-h2\">Vart tog varorna vägen?</h2>\n";
		echo "<p class=\"gi-sub\">Vänster: allt som kom in. Mitten: hade varan en väntande kundorder eller inte. Höger: vad som hänt med den fram till nu.</p>\n";
		echo "<div class=\"gi-card\"><div class=\"gi-scroll\" id=\"gi-flow\"></div>\n";
		echo "<div class=\"gi-legend\"><span><i class=\"sw sw-direct\"></i> Täckte order, skickad inom 1 dag</span><span><i class=\"sw sw-wait\"></i> Täckte order men väntade</span><span><i class=\"sw sw-stock\"></i> Till lager (ingen väntande order)</span></div>\n";
		echo "<p class=\"gi-hint\">Klicka på en stapel i flödet, eller i diagrammen längre ner, för att se orderraderna eller inleveranserna bakom den.</p></div>\n";

		// Detaljer bakom en klickad stapel (filtreras av goods_inflow.js)
		echo "<div class=\"gi-card gi-detail\" id=\"gi-detail\" hidden>\n";
		echo "<div class=\"gi-detail-head\"><h3 id=\"gi-detail-title\">Detaljer</h3><span class=\"gi-detail-count\" id=\"gi-detail-count\"></span><span class=\"gi-detail-actions\"><button type=\"button\" class=\"gi-export\" data-target=\"detail\" title=\"Raderna som visas, som CSV för Excel\">Exportera CSV</button><button type=\"button\" class=\"gi-detail-close\" id=\"gi-detail-close\">Stäng</button></span></div>\n";
		echo "<div class=\"gi-det-scroll\" id=\"gi-det-orders\" hidden><table class=\"table-list\"><thead><tr><th>Inlev. datum</th><th>Artikelnr</th><th>Produkt</th><th>Ordernr</th><th>Orderdatum</th><th>Kund</th><th class=\"n\">Antal</th><th>Skickad</th><th class=\"n\">Dagar</th><th>Utfall</th><th hidden>Inleverans</th><th hidden>Inbyte</th></tr></thead><tbody>\n";
		foreach ($orders as $o) {
			$days = ($o['ship_days'] === null || $o['ship_days'] === '') ? null : (int)$o['ship_days'];
			$dkey = $days === null ? 'ej' : (string)min($days, 15);
			$ship = $o['first_ship_after'] ? substr($o['first_ship_after'], 0, 16) : '–';
			$out = CGoodsInflow::outcome($o);
			echo "<tr data-b=\"" . CGoodsInflow::orderBucket($o) . "\" data-d=\"" . $dkey . "\" data-r=\"" . $h($out) . "\" data-q=\"" . (float)$o['alloc_qty'] . "\" hidden>"
			   . "<td>" . $h($o['rdate']) . "</td><td>" . $plink($o['product_value'], $o['m_product_id'], $o['product_value']) . "</td><td>" . $plink($o['product_value'], $o['m_product_id'], $o['product_name']) . $tbadge($o['istradein'] === 'Y') . "</td>"
			   . "<td><a href=\"/search_dispatch.php?mode=order&amp;page=1&amp;q=" . rawurlencode($o['order_no']) . "\" target=\"_blank\" rel=\"noopener\">" . $h($o['order_no']) . "</a></td><td>" . $h($o['dateordered']) . "</td><td>" . $h($o['customer']) . "</td>"
			   . "<td class=\"n\">" . $nf($o['alloc_qty']) . "</td><td>" . $h($ship) . "</td><td class=\"n\">" . ($days === null ? '–' : $days) . "</td><td>" . $h($out) . "</td>"
			   . "<td hidden>" . $h($o['receipt_no']) . "</td><td hidden>" . ($o['istradein'] === 'Y' ? 'Ja' : 'Nej') . "</td></tr>\n";
		}
		echo "</tbody></table></div>\n";
		echo "<div class=\"gi-det-scroll\" id=\"gi-det-stock\" hidden><table class=\"table-list\"><thead><tr><th>Inlev. datum</th><th>Inleverans</th><th>Artikelnr</th><th>Produkt</th><th>Leverantör</th><th class=\"n\">Antal in</th><th class=\"n\">Till lager</th><th class=\"n\">Lager före</th><th class=\"n\">Sålt sedan</th><th class=\"n\">Dagar till slutsålt</th><th hidden>Inbyte</th></tr></thead><tbody>\n";
		foreach ($rows as $r) {
			$tl = (float)$r['qty'] - (float)$r['alloc_qty'];
			if ($tl <= 0) continue;
			$fd = ($r['fifo_full_days'] === null || $r['fifo_full_days'] === '') ? '–' : (int)$r['fifo_full_days'];
			echo "<tr data-b=\"" . CGoodsInflow::stockBucket($r) . "\" data-q=\"" . $tl . "\" hidden>"
			   . "<td>" . $h($r['rdate']) . "</td><td><a href=\"/search_dispatch.php?mode=inout&amp;q=" . rawurlencode($r['receipt_no']) . "\" target=\"_blank\" rel=\"noopener\">" . $h($r['receipt_no']) . "</a></td><td>" . $plink($r['product_value'], $r['m_product_id'], $r['product_value']) . "</td><td>" . $plink($r['product_value'], $r['m_product_id'], $r['product_name']) . $tbadge($r['istradein'] === 'Y') . "</td><td>" . $h($r['vendor']) . "</td>"
			   . "<td class=\"n\">" . $nf($r['qty']) . "</td><td class=\"n\">" . $nf($tl) . "</td><td class=\"n\">" . $nf($r['onhand_before']) . "</td><td class=\"n\">" . $nf($r['ship_qty_after']) . "</td><td class=\"n\">" . $fd . "</td><td hidden>" . ($r['istradein'] === 'Y' ? 'Ja' : 'Nej') . "</td></tr>\n";
		}
		echo "</tbody></table></div>\n";
		echo "</div>\n";

		// Per dag
		echo "<h2 class=\"gi-h2\">Per dag</h2>\n";
		echo "<div class=\"gi-card\">\n";
		echo "<div class=\"gi-legend\"><span><i class=\"sw sw-direct\"></i> Täckte order, ut inom 1 dag</span><span><i class=\"sw sw-wait\"></i> Täckte order men väntade</span><span><i class=\"sw sw-stock\"></i> Till lager</span></div>\n";
		echo "<div class=\"gi-scroll\" id=\"gi-day\"></div>\n";
		echo "<details><summary>Visa som tabell</summary>\n";
		echo "<table class=\"table-list\"><thead><tr><th>Datum</th><th class=\"n\">Rader</th><th class=\"n\">Antal in</th><th class=\"n\">Täckte order</th><th class=\"n\">varav ut inom 1 dag</th><th class=\"n\">varav väntade</th><th class=\"n\">Till lager</th><th class=\"n\">Andel täckte order</th><th class=\"n\">Andel direkt av order</th></tr></thead><tbody>\n";
		foreach ($agg['day'] as $d => $g) {
			echo "<tr><td>" . $h($d) . "</td><td class=\"n\">" . $nf($g['rows']) . "</td><td class=\"n\">" . $nf($g['qty']) . "</td><td class=\"n\">" . $nf($g['alloc']) . "</td><td class=\"n\">" . $nf($g['a01']) . "</td><td class=\"n\">" . $nf($g['alloc'] - $g['a01']) . "</td><td class=\"n\">" . $nf($g['tl']) . "</td><td class=\"n\">" . $pct($g['alloc'], $g['qty']) . "</td><td class=\"n\">" . $pct($g['a01'], $g['alloc']) . "</td></tr>\n";
		}
		echo "</tbody><tfoot><tr class=\"total-row\"><td>Totalt</td><td class=\"n\">" . $nf($T['rows']) . "</td><td class=\"n\">" . $nf($T['qty']) . "</td><td class=\"n\">" . $nf($T['alloc']) . "</td><td class=\"n\">" . $nf($T['a01']) . "</td><td class=\"n\">" . $nf($waitQty) . "</td><td class=\"n\">" . $nf($T['tl']) . "</td><td class=\"n\">" . $pct($T['alloc'], $T['qty']) . "</td><td class=\"n\">" . $pct($T['a01'], $T['alloc']) . "</td></tr></tfoot></table>\n";
		echo "</details></div>\n";

		// Ordersidan
		echo "<h2 class=\"gi-h2\">Det som täckte en väntande order</h2>\n";
		echo "<p class=\"gi-sub\">" . $nf($T['alloc']) . " enheter kom in medan en kund redan väntade på dem. Utleveranser skapas i morgonkörningen kl 06, så dag 0–1 räknas som \"direkt\".</p>\n";
		echo "<div class=\"gi-grid2\">\n";
		echo "<div class=\"gi-card\"><h3>Dagar från inleverans till utleverans</h3><div class=\"gi-scroll\" id=\"gi-days\"></div></div>\n";
		echo "<div class=\"gi-card\"><h3>Varför väntade de som inte gick ut direkt?</h3><p class=\"gi-sub\">" . $nf($waitQty) . " enheter skickades senare än nästa dag, eller inte alls.</p><div class=\"gi-scroll\" id=\"gi-reason\"></div></div>\n";
		echo "</div>\n";

		echo "<p class=\"gi-sub\"><a href=\"#gi-detail\" class=\"gi-pick\" data-table=\"orders\" data-attr=\"b\" data-vals=\"A2,A3,A5\" data-title=\"Orderrader som inte gick ut direkt\">Visa alla orderrader som inte gick ut direkt</a></p>\n";

		// Lagersidan
		echo "<h2 class=\"gi-h2\">Det som gick till lager</h2>\n";
		echo "<p class=\"gi-sub\">" . $nf($T['tl']) . " enheter hade ingen väntande order. Hur länge låg partiet kvar innan det var slutsålt? Räknat först-in-först-ut: det gamla saldot säljs först, sedan det nya partiet. \"Kvar i lager\" = inte slutsålt ännu.</p>\n";
		echo "<div class=\"gi-card\"><div class=\"gi-scroll\" id=\"gi-stock\"></div></div>\n";

		// Kategorier + leverantörer
		echo "<h2 class=\"gi-h2\">Per produktkategori och leverantör</h2>\n";
		echo "<div class=\"gi-grid2\">\n";
		echo "<div class=\"gi-card\"><h3>Tio största kategorierna</h3><div class=\"gi-legend small\"><span><i class=\"sw sw-direct\"></i> Order, ut inom 1 dag</span><span><i class=\"sw sw-wait\"></i> Order, väntade</span><span><i class=\"sw sw-stock\"></i> Till lager</span></div><div class=\"gi-scroll\" id=\"gi-cats\"></div></div>\n";
		echo "<div class=\"gi-card\"><h3>Leverantörer</h3><div class=\"gi-tablewrap\"><table class=\"table-list\"><thead><tr><th>Leverantör</th><th class=\"n\">Rader</th><th class=\"n\">Antal in</th><th class=\"n\">Täckte order</th><th class=\"n\">Ut inom 1 dag</th></tr></thead><tbody>\n";
		foreach (array_slice($agg['vend'], 0, 15) as $v) {
			echo "<tr><td>" . $h($v['name']) . ($v['group'] == 'Privatkund' ? " <span class=\"badge\">inbyte</span>" : "") . "</td><td class=\"n\">" . $nf($v['rows']) . "</td><td class=\"n\">" . $nf($v['qty']) . "</td><td class=\"n\">" . $nf($v['alloc']) . "</td><td class=\"n\">" . $nf($v['a01']) . "</td></tr>\n";
		}
		echo "</tbody></table></div></div>\n";
		echo "</div>\n";

		// Per produkt
		echo "<div class=\"gi-card\"><div class=\"gi-card-actions\"><button type=\"button\" class=\"gi-export\" data-target=\"gi-prod-table\" data-name=\"produkter\" title=\"Alla produkter, som CSV för Excel\">Exportera CSV</button></div>";
		echo "<details><summary>Per produkt (" . count($agg['prod']) . " produkter, sorterat på antal)</summary>\n";
		echo "<table class=\"table-list\" id=\"gi-prod-table\"><thead><tr><th>Artikelnr</th><th>Produkt</th><th>Kategori</th><th class=\"n\">Antal in</th><th class=\"n\">Lager före</th><th class=\"n\">Täckte order</th><th class=\"n\">Ut inom 1 dag</th><th class=\"n\">Väntade</th><th class=\"n\">Till lager</th><th class=\"n\">Dagar till slutsålt</th><th hidden>Inbyte</th></tr></thead><tbody>\n";
		foreach ($agg['prod'] as $p) {
			$fifo = count($p['fifo']) ? max($p['fifo']) : '–';
			echo "<tr><td>" . $plink($p['value'], $p['id'], $p['value']) . "</td><td>" . $plink($p['value'], $p['id'], $p['name']) . $tbadge($p['tradein']) . "</td><td>" . $h($p['cat']) . "</td><td class=\"n\">" . $nf($p['qty']) . "</td><td class=\"n\">" . $nf($p['onhand_before']) . "</td><td class=\"n\">" . $nf($p['alloc']) . "</td><td class=\"n\">" . $nf($p['a01']) . "</td><td class=\"n\">" . $nf($p['alloc'] - $p['a01']) . "</td><td class=\"n\">" . $nf($p['tl']) . "</td><td class=\"n\">" . $h($fifo) . "</td><td hidden>" . ($p['tradein'] ? 'Ja' : 'Nej') . "</td></tr>\n";
		}
		echo "</tbody></table></details></div>\n";

		// Metod
		echo "<div class=\"gi-method\"><details><summary>Så är det räknat</summary><ul>\n";
		echo "<li><b>Underlag:</b> färdigställda leverantörsinleveranser till Umeå (Standard) i perioden, bara varor. Direktleveranser och returlagret ingår inte. Inbytesvaror (produkter markerade som inbyte) ingår som standard och märks \"inbyte\" i listorna; filtret Inbytesvaror tar bort dem eller visar bara dem. I leverantörslistan märks kunder i gruppen Privatkund som \"inbyte\".</li>\n";
		echo "<li><b>Export:</b> knappen Exportera CSV ger raderna som visas, med semikolon som avgränsare, så att filen öppnas direkt i Excel. Exporten har två extra kolumner som inte syns på sidan: inleveransnummer för orderraderna och om varan är ett inbyte.</li>\n";
		echo "<li><b>Täckte en väntande order:</b> i inleveransögonblicket fanns en kundorder på produkten som ännu inte skickats. Bara vanliga kundorder (Standard Order) med status Slutförd eller Under bearbetning räknas; offerter, utkast och returer räknas inte. Inlevererad kvantitet fördelas på de väntande orderraderna, äldst först.</li>\n";
		echo "<li><b>Ut inom 1 dag:</b> orderraden skickades samma dag eller dagen efter inleveransen.</li>\n";
		echo "<li><b>Väntade på annan vara:</b> ordern hade fler öppna rader vid inleveransen och minst en av dem levererades in senare, innan ordern skickades. <b>Andra rader öppna:</b> fler rader var öppna men ingen inleverans registrerades för dem. <b>Komplett men skickad senare:</b> inget annat saknades.</li>\n";
		echo "<li><b>Ej skickad:</b> beskrivs utifrån orderns läge just nu. <b>Ordern väntar på andra varor</b> = minst en annan varurad i ordern är inte fullt levererad. <b>Inget annat saknas i ordern</b> = varan kom in och inget annat i ordern saknas, men raden är ändå inte skickad; de är värda att titta på. <b>Raden ändrad</b> = raden har annullerats eller minskats efter inleveransen.</li>\n";
		echo "<li><b>Till lager, slutsålt inom N dagar:</b> dagar tills utleveranserna efter inleveransen förbrukat först det gamla saldot och sedan hela partiet.</li>\n";
		echo "<li><b>Förbehåll:</b> makulerade order har kvantitet 0 i databasen och syns inte som väntande. Siffror för de senaste dagarna ändras tills orderna hunnit skickas.</li>\n";
		echo "</ul></details></div>\n";

		$js = array('from' => $from, 'to' => $to, 'tradein' => $tradein, 'tot' => $T, 'day' => $agg['day'], 'cat' => $agg['cat'], 'ship_hist' => $agg['ship_hist'], 'reason' => $agg['reason'], 'ndays' => $agg['ndays']);
		echo "<script>window.INFLOW = " . json_encode($js, JSON_UNESCAPED_UNICODE) . ";</script>\n";
		echo "<div id=\"gi-tip\" hidden></div>\n";
		echo "<script src=\"goods_inflow.js?ver=ad" . date("ynjGi") . "\"></script>\n";
	}

	include_once("footer.php");
?>
