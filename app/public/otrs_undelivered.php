<?php
	include_once("top.php");
	include_once("header.php");

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"otrs_undelivered.css?ver=ad" . date("ynjGi") . "\">\n";

	// Znuny lagrar svensk lokal tid: "nu" och fönstrets start räknas här, aldrig i databasen.
	$tz = new DateTimeZone('Europe/Stockholm');
	$now = new DateTime('now', $tz);
	$defSince = (clone $now)->modify('yesterday');
	$since = $defSince;
	if (isset($_GET['since']) && is_string($_GET['since'])) {
		$d = DateTime::createFromFormat('Y-m-d\TH:i', $_GET['since'], $tz);
		if ($d && $d->format('Y-m-d\TH:i') === $_GET['since'] && $d <= $now) $since = $d;
	}
	$maxDays = 31; $clamped = false;
	if ($since < (clone $now)->modify("-$maxDays days")) { $since = (clone $now)->modify("-$maxDays days")->setTime(0, 0); $clamped = true; }

	$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
	// "12:05" för idag, annars "12 nov 12:05"
	$mon = array(1 => 'jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec');
	$when = function ($t) use ($now, $mon) {
		if ($t === null || $t === '') return '–';
		$ts = is_int($t) ? $t : strtotime($t);
		return (date('Y-m-d', $ts) === $now->format('Y-m-d') ? '' : date('j', $ts) . ' ' . $mon[(int)date('n', $ts)] . ' ') . date('H:i', $ts);
	};
	$ticket = function ($r) use ($h) { return "<a href=\"" . $h(COtrsUndelivered::ticketUrl($r)) . "\" target=\"_blank\" rel=\"noopener\">" . $h($r['tn']) . "</a>"; };
	$to = function ($r) use ($h) { return $h($r['addr'] ? implode(', ', $r['addr']) : $r['a_to']); };

	echo "<h1>Otrs - ärenden som behöver hanteras</h1>\n";
	echo "<p class=\"ou-intro\">Svar som skrivits i ärendesystemet (Znuny/OTRS) men aldrig nått kunden. Znuny sparar inte mejl som en mejlserver nekar, "
	   . "så svaret måste skickas igen för hand: öppna ärendet via länken och välj <b>Skicka igen</b> på det markerade svaret. Sidan ändrar ingenting själv. Autosvar ingår inte.</p>\n";

	echo "<form method=\"GET\" class=\"ou-form\">\n<div class=\"filter-bar\">\n";
	echo "<label>Misslyckade sedan <input type=\"datetime-local\" name=\"since\" value=\"" . $since->format('Y-m-d\TH:i') . "\" max=\"" . $now->format('Y-m-d\TH:i') . "\"></label>\n";
	echo "<button type=\"submit\" class=\"ou-btn\">Visa</button>\n";
	$q = function ($d) { return $_SERVER['PHP_SELF'] . "?since=" . rawurlencode($d->format('Y-m-d\TH:i')); };
	echo "<span class=\"ou-quick\">Snabbval: <a href=\"" . $q((clone $now)->setTime(0, 0)) . "\">idag</a> · <a href=\"" . $h($_SERVER['PHP_SELF']) . "\">sedan igår</a> · <a href=\"" . $q((clone $now)->modify('-7 days')->setTime(0, 0)) . "\">senaste veckan</a></span>\n";
	echo "<span class=\"ou-updated\">Uppdaterad " . $now->format('H:i:s') . ", laddas om varje minut</span>\n";
	echo "</div>\n</form>\n";
	if ($clamped) echo "<p class=\"ou-note\">Perioden är begränsad till $maxDays dagar; visar från " . $since->format('Y-m-d H:i') . ".</p>\n";

	$ou = new COtrsUndelivered();
	$limit = $ou->maxAttempts();
	$f = $ou->error ? null : $ou->failed($since->format('Y-m-d H:i:s'));
	$queue = $ou->error ? array() : $ou->queued($limit, $now->getTimestamp());

	if ($ou->error) {
		echo "<p class=\"ou-note\">Kunde inte läsa från Znuny: " . $h($ou->error) . "</p>\n";
	} else {
		$todo = $f['todo']; $resent = $f['resent'];
		$drop = count(array_filter($queue, function ($r) { return $r['will_drop']; }));

		echo "<div class=\"ou-kpis\">\n";
		echo "<div class=\"ou-kpi " . (count($todo) ? "alert" : "ok") . "\"><div class=\"v\">" . count($todo) . "</div><div class=\"l\">svar behöver skickas om</div></div>\n";
		echo "<div class=\"ou-kpi" . ($drop ? " alert" : "") . "\"><div class=\"v\">" . count($queue) . "</div><div class=\"l\">svar väntar i Znunys utkö" . ($drop ? ", varav $drop kommer att tas bort" : "") . "</div></div>\n";
		echo "<div class=\"ou-kpi\"><div class=\"v\">" . count($resent) . "</div><div class=\"l\">misslyckade svar är redan omskickade</div></div>\n";
		echo "</div>\n";

		// 1. Behöver skickas om
		echo "<h2 class=\"ou-h2\">Behöver skickas om (" . count($todo) . ")</h2>\n";
		if (!$todo) {
			echo "<p class=\"ou-sub\">Inga svar har misslyckats sedan " . $since->format('Y-m-d H:i') . " utan att ha skickats om.</p>\n";
		} else {
			$by = array();
			foreach ($todo as $r) { $s = COtrsUndelivered::sender($r); $by[$s] = (isset($by[$s]) ? $by[$s] : 0) + 1; }
			arsort($by);
			$parts = array();
			foreach ($by as $s => $n) $parts[] = $h($s) . " " . $n;
			echo "<p class=\"ou-by\">Per avsändare: " . implode(" · ", $parts) . "</p>\n";
			echo "<div class=\"ou-wrap\"><table class=\"table-list\"><thead><tr><th>Ärende</th><th>Kö</th><th>Skickat av</th><th>Skrivet</th><th>Mottagare</th><th>Misslyckades</th><th>Orsak</th></tr></thead><tbody>\n";
			foreach ($todo as $r) {
				echo "<tr><td class=\"nowrap\">" . $ticket($r) . "</td><td>" . $h($r['queue']) . "</td><td>" . $h(COtrsUndelivered::sender($r)) . "</td><td class=\"nowrap\">" . $when($r['written_at']) . "</td><td>" . $to($r) . "</td><td class=\"nowrap\">" . $when($r['failed_at']) . "</td>"
				   . "<td><span class=\"ou-reason\" title=\"" . $h(trim((string)$r['reason_text']) !== '' ? $r['reason_text'] : 'Znuny har inte sparat något felmeddelande.') . "\">" . $h($r['reason']) . "</span>"
				   . ($r['pointless'] ? "<br><span class=\"ou-warn\">Skicka inte om till samma adress, kontrollera adressen först.</span>" : "")
				   . ($r['tries'] > 1 ? "<br><span class=\"muted\">" . $r['tries'] . " försök har misslyckats, det senaste visas.</span>" : "") . "</td></tr>\n";
			}
			echo "</tbody></table></div>\n";
		}
		echo "<p class=\"ou-sub\">Ett svar räknas som omskickat när ett senare mejl till samma adress har gått iväg i samma ärende. Skickas svaret om till en annan adress ligger det kvar i listan.</p>\n";

		// 2. Väntar i utkön
		echo "<h2 class=\"ou-h2\">Väntar i Znunys utkö (" . count($queue) . ")</h2>\n";
		if (!$queue) {
			echo "<p class=\"ou-sub\">Utkön är tom: inga svar väntar på att skickas.</p>\n";
		} else {
			echo "<p class=\"ou-sub\">Znuny försöker skicka de här själv. Efter $limit körningar av utkön (ungefär en per minut) tas mejlet bort och hamnar i listan ovan. Tiderna är de tidigaste möjliga; när Znuny har mycket att göra går det långsammare.</p>\n";
			echo "<div class=\"ou-wrap\"><table class=\"table-list\"><thead><tr><th>Ärende</th><th>Kö</th><th>Skickat av</th><th>Skrivet</th><th>Mottagare</th><th class=\"num\">Körningar</th><th>Senaste svar</th><th>Vad händer nu</th></tr></thead><tbody>\n";
			foreach ($queue as $r) {
				if ($r['next_try'] === null) $next = "Första försöket görs inom någon minut";
				elseif ($r['will_drop']) $next = "<span class=\"ou-warn\">Tas bort tidigast " . $when($r['gives_up']) . " utan nytt försök. Behöver då skickas om.</span>";
				else $next = "Nästa försök " . $when($r['next_try']) . ", ges upp tidigast " . $when($r['gives_up']);
				echo "<tr><td class=\"nowrap\">" . $ticket($r) . "</td><td>" . $h($r['queue']) . "</td><td>" . $h(COtrsUndelivered::sender($r)) . "</td><td class=\"nowrap\">" . $when($r['written_at']) . "</td><td>" . $to($r) . "</td>"
				   . "<td class=\"num\">" . (int)$r['attempts'] . " / $limit</td><td>" . $h($r['last_smtp_code'] ?: '–') . "</td><td>" . $next . "</td></tr>\n";
			}
			echo "</tbody></table></div>\n";
		}
		if ($f['waiting']) echo "<p class=\"ou-sub\">" . $f['waiting'] . " misslyckade svar har ett omskick som ligger i utkön och visas därför inte under \"Behöver skickas om\".</p>\n";

		// 3. Redan omskickade
		echo "<details class=\"ou-details\"><summary>Redan omskickade (" . count($resent) . ")</summary>\n";
		if (!$resent) {
			echo "<p class=\"ou-sub\">Inga.</p>\n";
		} else {
			echo "<div class=\"ou-wrap\"><table class=\"table-list\"><thead><tr><th>Ärende</th><th>Kö</th><th>Skickat av</th><th>Skrivet</th><th>Mottagare</th><th>Orsak</th><th>Omskickat</th></tr></thead><tbody>\n";
			foreach ($resent as $r) {
				list($reason) = COtrsUndelivered::reason($r['reason_text']);
				echo "<tr><td class=\"nowrap\">" . $ticket($r) . "</td><td>" . $h($r['queue']) . "</td><td>" . $h(COtrsUndelivered::sender($r)) . "</td><td class=\"nowrap\">" . $when($r['written_at']) . "</td><td>" . $to($r) . "</td>"
				   . "<td><span class=\"ou-reason\" title=\"" . $h($r['reason_text']) . "\">" . $h($reason) . "</span></td><td class=\"nowrap\">" . $when($r['resent_at']) . "</td></tr>\n";
			}
			echo "</tbody></table></div>\n";
		}
		echo "</details>\n";
	}

	// Ladda om varje minut, men inte medan någon håller på att ändra starttiden.
	echo "<script>setInterval(function () { var a = document.activeElement; if (a && a.name === 'since') return; location.reload(); }, 60000);</script>\n";

	include_once("footer.php");
?>
