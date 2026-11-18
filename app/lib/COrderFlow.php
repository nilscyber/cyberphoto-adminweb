<?php

require_once("Db.php");

/**
 * Orderflöde: hur stor del av kundordrarna är kompletta direkt vid köp, och hur fort går de ut?
 *
 * För varje lagerförd artikelrad på en Standard Order (klar eller pågående) i perioden:
 *  - lagersaldo och reserverat antal i radens lager i köpögonblicket, återskapat bakåt från
 *    dagens saldo (M_Storage) minus alla lagertransaktioner och orderrader efter köpet
 *  - radens status vid köp: fritt i lager (S), i lager men lovat till tidigare ordrar (P),
 *    saknades (N) eller direktleverans från leverantör (D)
 *  - när raden var fullt levererad, räknat i arbetsdagar från orderdagen
 *
 * Allt hämtas i en fråga (fetch) och summeras i PHP (build). Endast läsning.
 */
class COrderFlow
{
	const WEB_SALESREP = 1652736;   // SalesRep_ID för webbordrar
	const WH_DROPSHIP = 1000003;    // lagret Direktleverans
	const CUTOFF_HOUR = 17;         // order lagd från 17:00 räknas från nästa arbetsdag
	const MAX_DAYS = 93;            // längsta period
	const MAX_BACK = '-2 years';    // tidigaste startdatum (frågan läser allt från periodstart till nu)

	/** Leveransklasser i arbetsdagar: övre gräns per klass, sista klassen = ej levererad. */
	const BUCKETS = array(0, 1, 3, 5, 10, PHP_INT_MAX);
	const BUCKET_UNDELIVERED = 6;

	private $ad_r;
	public $error = "";
	private $idx = array();     // datum => antal arbetsdagar t.o.m. datumet
	private $work = array();    // datum => true om arbetsdag

	public function __construct()
	{
		$this->ad_r = Db::getConnectionAD(false);
		if ($this->ad_r) { @pg_set_client_encoding($this->ad_r, "UTF8"); }
	}

	public static function opt($v, $allowed, $def) { return in_array($v, $allowed, true) ? $v : $def; }
	public static function productUrl($value, $id) { return "/search_dispatch.php?mode=product&q=" . rawurlencode($value) . "&open=product&id=" . (int)$id; }
	public static function orderUrl($docno) { return "/search_dispatch.php?mode=order&page=1&q=" . rawurlencode($docno); }

	/** Dagar utan utleverans ett visst år: svenska helgdagar och aftnar då lagret är stängt. */
	public static function holidays($year)
	{
		$a = $year % 19; $b = intdiv($year, 100); $c = $year % 100;
		$d = intdiv($b, 4); $e = $b % 4; $f = intdiv($b + 8, 25); $g = intdiv($b - $f + 1, 3);
		$h = (19 * $a + $b - $d - $g + 15) % 30;
		$i = intdiv($c, 4); $k = $c % 4;
		$l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
		$m = intdiv($a + 11 * $h + 22 * $l, 451);
		$easter = new DateTime(sprintf('%04d-%02d-%02d', $year, intdiv($h + $l - 7 * $m + 114, 31), (($h + $l - 7 * $m + 114) % 31) + 1));
		$rel = function ($days) use ($easter) { return (clone $easter)->modify(($days >= 0 ? '+' : '') . $days . ' days')->format('Y-m-d'); };
		$out = array(
			"$year-01-01" => 'Nyårsdagen', "$year-01-06" => 'Trettondedag jul',
			$rel(-2) => 'Långfredagen', $rel(1) => 'Annandag påsk',
			"$year-05-01" => 'Första maj', $rel(39) => 'Kristi himmelsfärdsdag', "$year-06-06" => 'Nationaldagen',
			"$year-12-24" => 'Julafton', "$year-12-25" => 'Juldagen', "$year-12-26" => 'Annandag jul', "$year-12-31" => 'Nyårsafton',
		);
		for ($day = 19; $day <= 25; $day++) {   // midsommarafton = fredagen 19–25 juni
			$dt = new DateTime("$year-06-$day");
			if ($dt->format('N') == 5) { $out[$dt->format('Y-m-d')] = 'Midsommarafton'; }
		}
		return $out;
	}

	/** Bygger arbetsdagskalendern för [from, to]. */
	private function calendar($from, $to)
	{
		$this->idx = array(); $this->work = array();
		$hol = array();
		for ($y = (int)substr($from, 0, 4); $y <= (int)substr($to, 0, 4); $y++) { $hol += self::holidays($y); }
		$n = 0;
		for ($dt = new DateTime($from), $end = new DateTime($to); $dt <= $end; $dt->modify('+1 day')) {
			$s = $dt->format('Y-m-d');
			$w = ($dt->format('N') < 6) && !isset($hol[$s]);
			if ($w) { $n++; }
			$this->work[$s] = $w; $this->idx[$s] = $n;
		}
	}

	/** Närmaste arbetsdag från och med (inclusive) eller efter datumet. */
	private function nextWorkday($date, $inclusive)
	{
		$dt = new DateTime($date);
		if (!$inclusive) { $dt->modify('+1 day'); }
		for ($i = 0; $i < 20; $i++, $dt->modify('+1 day')) {
			$s = $dt->format('Y-m-d');
			if (!empty($this->work[$s])) { return $s; }
		}
		return $date;
	}

	public static function bucket($wd)
	{
		if ($wd < 0) { return self::BUCKET_UNDELIVERED; }
		foreach (self::BUCKETS as $i => $max) { if ($wd <= $max) { return $i; } }
		return count(self::BUCKETS) - 1;
	}

	/**
	 * En rad per lagerförd artikelrad på Standard Order (CO/IP) med orderdatum i perioden.
	 * Returnerar pg-resultatet; build() läser det order för order så att en lång period inte fyller PHP-minnet.
	 *
	 * Lager och reservation i köpögonblicket räknas baklänges: en händelseström per produkt + lager
	 * (k=0 lagertransaktion / utleverans mot orderrad, k=1 orderrad, k=2 dagens M_Storage) och
	 * fönsterfunktioner – inga joinar mellan CTE:er, de blir kvadratiska på läsvärden.
	 * Bara dokumenttyp 1000030 reserverar; offerter och utkast gör det inte.
	 */
	public function fetch($from, $to)
	{
		$sql = <<<'SQL'
WITH ord AS MATERIALIZED (
  SELECT o.c_order_id, o.documentno, o.dateordered, o.created, o.salesrep_id, o.deliveryviarule, o.c_bpartner_id,
         (o.docstatus IN ('CO','IP') AND o.dateordered >= $1::date AND o.dateordered < $2::date + 1) AS inperiod
  FROM c_order o
  WHERE o.issotrx = 'Y' AND o.c_doctype_id = 1000030 AND o.docstatus IN ('CO','IP','CL')
    AND (o.dateordered >= $1::date OR o.created >= $1::date)
),
lines AS MATERIALIZED (
  SELECT o.c_order_id, o.documentno, o.dateordered::date AS d, o.created AS ocreated, o.salesrep_id, o.deliveryviarule, o.c_bpartner_id, o.inperiod,
         l.c_orderline_id, l.m_product_id, l.m_warehouse_id, l.qtyordered, greatest(l.created, o.dateordered) AS ts
  FROM ord o
  JOIN c_orderline l ON l.c_order_id = o.c_order_id
  JOIN m_product p ON p.m_product_id = l.m_product_id
  WHERE l.qtyordered > 0 AND p.producttype = 'I' AND p.isstocked = 'Y'
),
prods AS MATERIALIZED (SELECT DISTINCT m_product_id FROM lines WHERE inperiod),
trx AS MATERIALIZED (
  SELECT t.m_product_id, loc.m_warehouse_id, t.created, t.movementqty, t.movementtype, t.m_inoutline_id
  FROM m_transaction t JOIN m_locator loc ON loc.m_locator_id = t.m_locator_id
  WHERE t.m_product_id IN (SELECT m_product_id FROM prods) AND t.created >= $1::date
),
ev AS (
  SELECT m_product_id, m_warehouse_id, created AS t, 0 AS k, movementqty AS dq, 0::numeric AS dres, NULL::numeric AS oh, NULL::numeric AS rs,
         NULL::numeric AS c_orderline_id, NULL::numeric AS c_order_id, NULL::varchar AS documentno, NULL::date AS d, NULL::timestamp AS ocreated,
         NULL::numeric AS salesrep_id, NULL::bpchar AS deliveryviarule, NULL::numeric AS c_bpartner_id, false AS inperiod, NULL::numeric AS qtyordered
  FROM trx
  UNION ALL
  SELECT c.m_product_id, c.m_warehouse_id, x.created, 0, 0, x.movementqty, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false, NULL
  FROM trx x JOIN m_inoutline il ON il.m_inoutline_id = x.m_inoutline_id JOIN c_orderline c ON c.c_orderline_id = il.c_orderline_id
  WHERE x.movementtype = 'C-'
  UNION ALL
  SELECT m_product_id, m_warehouse_id, ts, 1, 0, qtyordered, NULL, NULL, c_orderline_id, c_order_id, documentno, d, ocreated, salesrep_id, deliveryviarule, c_bpartner_id, inperiod, qtyordered
  FROM lines WHERE m_product_id IN (SELECT m_product_id FROM prods)
  UNION ALL
  SELECT s.m_product_id, loc.m_warehouse_id, NULL, 2, 0, 0, sum(s.qtyonhand), sum(s.qtyreserved), NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, false, NULL
  FROM m_storage s JOIN m_locator loc ON loc.m_locator_id = s.m_locator_id
  WHERE s.m_product_id IN (SELECT m_product_id FROM prods) GROUP BY 1, 2
),
run AS (
  SELECT ev.*,
         COALESCE(max(oh) OVER p, 0) - (sum(dq) OVER p - sum(dq) OVER w) AS onhand_then,
         COALESCE(max(rs) OVER p, 0) - (sum(dres) OVER p - COALESCE(sum(dres) OVER (PARTITION BY m_product_id, m_warehouse_id ORDER BY t, k, c_orderline_id ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING), 0)) AS reserved_then,
         COALESCE(max(oh) OVER p, 0) AS onhand_now
  FROM ev
  WINDOW p AS (PARTITION BY m_product_id, m_warehouse_id),
         w AS (PARTITION BY m_product_id, m_warehouse_id ORDER BY t, k, c_orderline_id ROWS UNBOUNDED PRECEDING)
)
SELECT r.c_order_id, r.documentno, r.d, r.ocreated, r.salesrep_id, r.deliveryviarule, bp.name AS customer,
       r.c_orderline_id, r.m_product_id, r.m_warehouse_id, r.qtyordered,
       p.value AS product_value, p.name AS product_name, manu.name AS brand, pc.name AS category,
       p.istradein, p.salestart::date AS salestart, p.launchdate::date AS launchdate,
       r.onhand_now, r.onhand_then, r.reserved_then,
       CASE WHEN sh.qty >= r.qtyordered THEN sh.last_ship END AS ship, sh.ship_no
FROM run r
JOIN m_product p ON p.m_product_id = r.m_product_id
LEFT JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id
LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
LEFT JOIN c_bpartner bp ON bp.c_bpartner_id = r.c_bpartner_id
LEFT JOIN LATERAL (
  SELECT max(io.movementdate)::date AS last_ship, sum(il.movementqty) AS qty, max(io.documentno) AS ship_no
  FROM m_inoutline il JOIN m_inout io ON io.m_inout_id = il.m_inout_id
  WHERE il.c_orderline_id = r.c_orderline_id AND io.issotrx = 'Y' AND io.movementtype = 'C-' AND io.docstatus IN ('CO','CL')
) sh ON true
WHERE r.k = 1 AND r.inperiod
ORDER BY r.d, r.documentno, r.c_order_id, r.c_orderline_id
SQL;
		if (!$this->ad_r) { $this->error = "ingen anslutning"; return false; }
		$res = @pg_query_params($this->ad_r, $sql, array($from, $to));
		if (!$res) { $this->error = pg_last_error($this->ad_r); }
		return $res;
	}

	/** Raderna i resultatet, en order i taget (raderna kommer sorterade per order). */
	private function orders($res)
	{
		pg_result_seek($res, 0);
		$cur = null; $ls = array();
		while ($row = pg_fetch_assoc($res)) {
			if ($cur !== null && $row["c_order_id"] !== $cur) { yield $ls; $ls = array(); }
			$cur = $row["c_order_id"]; $ls[] = $row;
		}
		if ($ls) { yield $ls; }
	}

	/**
	 * Summerar resultatet från fetch().
	 * $opt: channel all|web|manual, dropship all|exclude|only, tradein all|exclude|only, preorder exclude|include.
	 *
	 * Förbeställningar och omdaterade ordrar (levererade före orderdatum) tas bort som hela ordrar.
	 * Direktleverans- och inbytesfiltret tar bort rader; ordern bedöms på de rader som är kvar.
	 */
	public function build($res, $from, $to, $opt)
	{
		$this->calendar($from, date('Y-m-d', strtotime('+21 days')));
		$zero = array('n' => 0, 'S' => 0, 'P' => 0, 'N' => 0, 'D' => 0, 'same' => 0, 'w1' => 0, 'w3' => 0, 'w5' => 0, 'w10' => 0, 'undel' => 0);
		$st4 = array('S' => 0, 'P' => 0, 'N' => 0, 'D' => 0);
		$R = array('ord' => array(), 'lin' => array(), 'days' => array(), 'qty' => 0, 'excl' => array('pre' => 0, 'redated' => 0),
			'hist' => array('o' => array(), 'l' => array()),
			'why' => array('N' => 0, 'P' => 0, 'D' => 0, 'pick' => 0, 'other' => 0), 'lwhy' => array(1 => 0, 2 => 0, 3 => 0));
		foreach (array('all', 'web', 'man', 'ship', 'pick') as $g) { $R['ord'][$g] = $zero; $R['lin'][$g] = $zero; }
		for ($b = 0; $b <= self::BUCKET_UNDELIVERED; $b++) { $R['hist']['o'][$b] = $st4; $R['hist']['l'][$b] = $st4; }
		for ($dt = new DateTime($from), $end = new DateTime($to); $dt <= $end; $dt->modify('+1 day')) {
			$R['days'][$dt->format('Y-m-d')] = array('n' => 0, 'S' => 0, 'P' => 0, 'N' => 0, 'D' => 0, 'same' => 0, 'ln' => 0, 'lS' => 0);
		}
		$rank = array('S' => 0, 'P' => 1, 'D' => 2, 'N' => 3);
		$add = function (&$a, $st, $wd) {
			$a['n']++; $a[$st]++;
			if ($wd < 0) { $a['undel']++; return; }
			if ($wd == 0) { $a['same']++; }
			if ($wd <= 1) { $a['w1']++; }
			if ($wd <= 3) { $a['w3']++; }
			if ($wd <= 5) { $a['w5']++; }
			if ($wd <= 10) { $a['w10']++; }
		};
		$num = function ($v) { $f = (float)$v; return $f == (int)$f ? (int)$f : round($f, 2); };

		$O = array(); $L = array(); $P = array(); $B = array(); $K = array();
		$pix = array(); $bix = array(); $kix = array(); $pa = array(); $ba = array(); $ka = array();

		foreach ($this->orders($res) as $ls) {
			$h = $ls[0]; $d = $h['d'];
			$web = ((int)$h['salesrep_id'] === self::WEB_SALESREP);
			if (($opt['channel'] === 'web' && !$web) || ($opt['channel'] === 'manual' && $web)) { continue; }
			$pick = ($h['deliveryviarule'] === 'P');

			// status vid köp för alla artikelrader på ordern (även de som filtreras bort nedan)
			$pre = false; $redated = false; $busy = array();
			foreach ($ls as $i => $l) {
				$qty = (float)$l['qtyordered']; $oh = (float)$l['onhand_then']; $rs = max(0.0, (float)$l['reserved_then']);
				$ds = ((int)$l['m_warehouse_id'] === self::WH_DROPSHIP);
				$st = $ds ? 'D' : ($oh < $qty ? 'N' : ($oh - $rs >= $qty ? 'S' : 'P'));
				$ls[$i]['st'] = $st; $ls[$i]['ds'] = $ds;
				if ($st === 'N' || $st === 'P') { $busy[$l['m_warehouse_id']] = true; }
				if (($l['salestart'] && $l['salestart'] > $d) || ($l['launchdate'] && $l['launchdate'] > $d)) { $pre = true; }
				if ($l['ship'] && $l['ship'] < $d) { $redated = true; }
			}
			if ($pre && $opt['preorder'] === 'exclude') { $R['excl']['pre']++; continue; }
			if ($redated) { $R['excl']['redated']++; continue; }

			// första arbetsdag som ordern kan plockas
			$cr = (string)$h['ocreated'];
			$late = (substr($cr, 0, 10) === $d && (int)substr($cr, 11, 2) >= self::CUTOFF_HOUR);
			$d0 = $this->nextWorkday($d, !$late);

			$kept = array(); $ost = 'S'; $owd = 0;
			foreach ($ls as $l) {
				$ti = ($l['istradein'] === 'Y');
				if (($opt['dropship'] === 'exclude' && $l['ds']) || ($opt['dropship'] === 'only' && !$l['ds'])) { continue; }
				if (($opt['tradein'] === 'exclude' && $ti) || ($opt['tradein'] === 'only' && !$ti)) { continue; }
				$wd = -1;
				if ($l['ship']) { $wd = max(0, (isset($this->idx[$l['ship']]) ? $this->idx[$l['ship']] : 0) - (isset($this->idx[$d0]) ? $this->idx[$d0] : 0)); }
				// varför en rad som fanns fritt i lager ändå inte gick ut samma arbetsdag
				$l['why'] = 0;
				if ($l['st'] === 'S' && $wd !== 0) { $l['why'] = isset($busy[$l['m_warehouse_id']]) ? 1 : ($pick ? 2 : 3); }
				$l['wd'] = $wd; $l['ti'] = $ti;
				if ($rank[$l['st']] > $rank[$ost]) { $ost = $l['st']; }
				if ($wd < 0) { $owd = -1; } elseif ($owd >= 0 && $wd > $owd) { $owd = $wd; }
				$kept[] = $l;
			}
			if (!$kept) { continue; }

			$gs = array('all', $web ? 'web' : 'man', $pick ? 'pick' : 'ship');
			foreach ($gs as $g) { $add($R['ord'][$g], $ost, $owd); }
			$R['days'][$d]['n']++; $R['days'][$d][$ost]++;
			if ($owd === 0) { $R['days'][$d]['same']++; }
			else { $R['why'][$ost === 'S' ? ($pick ? 'pick' : 'other') : $ost]++; }
			$R['hist']['o'][self::bucket($owd)][$ost]++;
			$oi = count($O);
			$O[] = array($h['documentno'], $d, $web ? 1 : 0, $pick ? 1 : 0, (string)$h['customer'], $ost, $owd, count($kept));

			foreach ($kept as $l) {
				$st = $l['st']; $wd = $l['wd']; $qty = (float)$l['qtyordered'];
				foreach ($gs as $g) { $add($R['lin'][$g], $st, $wd); }
				$R['hist']['l'][self::bucket($wd)][$st]++;
				if ($l['why']) { $R['lwhy'][$l['why']]++; }
				$R['qty'] += $qty;
				$R['days'][$d]['ln']++;
				if ($st === 'S') { $R['days'][$d]['lS']++; }

				// produkt, varumärke, kategori
				$pid = $l['m_product_id'];
				if (!isset($pix[$pid])) {
					$bn = ($l['brand'] === null || $l['brand'] === '') ? '(saknas)' : $l['brand'];
					$kn = ($l['category'] === null || $l['category'] === '') ? '(saknas)' : $l['category'];
					if (!isset($bix[$bn])) { $bix[$bn] = count($B); $B[] = $bn; $ba[] = array('i' => $bix[$bn], 'name' => $bn) + $zero + array('q' => 0); }
					if (!isset($kix[$kn])) { $kix[$kn] = count($K); $K[] = $kn; $ka[] = array('i' => $kix[$kn], 'name' => $kn) + $zero + array('q' => 0); }
					$pix[$pid] = count($P);
					$P[] = array($l['product_value'], (int)$pid, $l['product_name'], $bix[$bn], $kix[$kn], $l['ti'] ? 1 : 0);
					$pa[] = array('i' => $pix[$pid], 'n' => 0, 'q' => 0, 'man' => 0, 'N' => 0, 'P' => 0, 'Nw' => 0, 'Nm' => 0, 'wd' => array(), 'undel' => 0, 'stock' => null);
				}
				$pi = $pix[$pid]; $bi = $P[$pi][3]; $ki = $P[$pi][4];
				$a = &$pa[$pi];
				$a['n']++; $a['q'] += $qty;
				if (!$web) { $a['man']++; }
				if ($st === 'N') { $a['N']++; if ($web) { $a['Nw']++; } else { $a['Nm']++; } }
				if ($st === 'P') { $a['P']++; }
				if ($wd < 0) { $a['undel']++; } else { $a['wd'][] = $wd; }
				if (!$l['ds'] || $a['stock'] === null) { $a['stock'] = $num($l['onhand_now']); }
				unset($a);
				$add($ba[$bi], $st, $wd); $ba[$bi]['q'] += $qty;
				$add($ka[$ki], $st, $wd); $ka[$ki]['q'] += $qty;

				$L[] = array($oi, $pi, $num($qty), $st, $wd, (string)$l['ship_no'], $num($l['onhand_then']), $num(max(0.0, (float)$l['reserved_then'])), $l['why'], $l['ds'] ? 1 : 0);
			}
		}

		// produktlistan: [index i P, rader, antal, manuella rader, saknades, lovat, saknades webb, saknades manuella, median arbetsdagar, ej levererade, lager nu]
		$prod = array();
		foreach ($pa as $a) {
			$w = $a['wd']; sort($w); $c = count($w);
			$med = $c === 0 ? null : ($c % 2 ? $w[intdiv($c, 2)] : ($w[$c / 2 - 1] + $w[$c / 2]) / 2);
			$prod[] = array($a['i'], $a['n'], $num($a['q']), $a['man'], $a['N'], $a['P'], $a['Nw'], $a['Nm'], $med, $a['undel'], $a['stock']);
		}
		usort($prod, function ($x, $y) { return ($y[4] <=> $x[4]) ?: (($y[1] <=> $x[1]) ?: ($x[0] <=> $y[0])); });
		$bySize = function ($x, $y) { return ($y['N'] <=> $x['N']) ?: (($y['n'] <=> $x['n']) ?: strcmp($x['name'], $y['name'])); };
		usort($ba, $bySize); usort($ka, $bySize);

		$R['O'] = $O; $R['L'] = $L; $R['P'] = $P; $R['B'] = $B; $R['K'] = $K;
		$R['products'] = $prod; $R['brands'] = $ba; $R['cats'] = $ka;
		return $R;
	}
}
