<?php

require_once("Db.php");

/**
 * Inleveransflöde: vad händer med det som levereras in?
 *
 * För varje leverantörsinleverans (V+) till ett lager under en period:
 *  - lagersaldo precis före inleveransen
 *  - öppen kundorderkvantitet för produkten i inleveransögonblicket ("restnoterat"),
 *    fördelad på orderraderna äldst först ("allokerat")
 *  - när de allokerade orderraderna faktiskt skickades (dagar från inleveransdatum)
 *  - FIFO: hur många dagar det tog innan partiet var slutsålt (gamla saldot först)
 *  - varför en allokerad orderrad väntade (andra rader på ordern)
 *
 * Allt läses med vanliga SELECT/CTE:er så att det går att köra mot en read-only standby.
 * Samma beräkning som engångsuttaget i adempiereTrunk/.claude/scripts/inflow-report.sql (helår 2025).
 */
class CGoodsInflow {

    private $ad_r;
    public $error = "";

    function __construct() {
        $this->ad_r = Db::getConnectionAD(false);
        if ($this->ad_r) { @pg_set_client_encoding($this->ad_r, "UTF8"); }
    }

    /** Gemensamma CTE:er. Parametrar: $1 = lager, $2 = från (datum), $3 = till (datum, inklusive). */
    private function cte() {
        return <<<'SQL'
WITH rcpt AS (
  SELECT min(l.m_inoutline_id) m_inoutline_id, io.m_inout_id, io.documentno, io.movementdate::date rdate,
         min(t.created) rtime, l.m_product_id, sum(l.movementqty) qty, io.c_bpartner_id vendor_id
  FROM m_inout io
  JOIN m_inoutline l ON l.m_inout_id = io.m_inout_id
  JOIN m_transaction t ON t.m_inoutline_id = l.m_inoutline_id
  JOIN m_product p ON p.m_product_id = l.m_product_id
  WHERE io.movementtype = 'V+' AND io.docstatus IN ('CO','CL') AND io.m_warehouse_id = $1
    AND io.movementdate >= $2::date AND io.movementdate < $3::date + 1
    AND p.producttype = 'I' AND l.movementqty > 0
  GROUP BY io.m_inout_id, io.documentno, io.movementdate::date, l.m_product_id, io.c_bpartner_id
),
prods AS (SELECT DISTINCT m_product_id FROM rcpt),
trx AS (
  SELECT t.m_product_id, t.created, t.movementqty, t.movementtype
  FROM m_transaction t JOIN m_locator loc ON loc.m_locator_id = t.m_locator_id
  WHERE loc.m_warehouse_id = $1 AND t.movementdate >= $2::date AND t.m_product_id IN (SELECT m_product_id FROM prods)
),
cur AS (
  SELECT s.m_product_id, sum(s.qtyonhand) onhand
  FROM m_storage s JOIN m_locator loc ON loc.m_locator_id = s.m_locator_id
  WHERE loc.m_warehouse_id = $1 AND s.m_product_id IN (SELECT m_product_id FROM prods) GROUP BY 1
),
ship AS (
  SELECT m_product_id, created, -movementqty qty,
         sum(-movementqty) OVER (PARTITION BY m_product_id ORDER BY created) cumq
  FROM trx WHERE movementtype = 'C-'
),
sol AS (
  SELECT ol.c_orderline_id, ol.c_order_id, o.documentno order_no, ol.m_product_id, ol.qtyordered,
         ol.created ol_created, o.dateordered::date dateordered, o.c_bpartner_id
  FROM c_orderline ol JOIN c_order o ON o.c_order_id = ol.c_order_id
  WHERE o.issotrx = 'Y' AND o.docstatus IN ('CO','CL','IP') AND ol.qtyordered > 0
    AND o.m_warehouse_id = $1 AND o.dateordered >= $2::date - 365 AND o.dateordered < $3::date + 1
    AND ol.m_product_id IN (SELECT m_product_id FROM prods)
    AND NOT (ol.qtydelivered >= ol.qtyordered AND ol.updated < $2::date)  -- fully shipped before the period: cannot be open at any receipt
),
shipl AS (
  SELECT l.c_orderline_id, t.created, -t.movementqty qty
  FROM m_transaction t JOIN m_inoutline l ON l.m_inoutline_id = t.m_inoutline_id
  WHERE t.movementtype = 'C-' AND l.c_orderline_id IN (SELECT c_orderline_id FROM sol)
),
dem AS (
  SELECT r.m_inoutline_id, r.rtime, r.rdate, r.qty rqty, r.m_product_id, s.c_orderline_id, s.c_order_id, s.order_no, s.dateordered, s.c_bpartner_id,
         s.qtyordered - COALESCE(sum(sh.qty) FILTER (WHERE sh.created < r.rtime), 0) open_qty,
         min(sh.created) FILTER (WHERE sh.created >= r.rtime) first_ship_after
  FROM rcpt r
  JOIN sol s ON s.m_product_id = r.m_product_id AND s.ol_created < r.rtime
  LEFT JOIN shipl sh ON sh.c_orderline_id = s.c_orderline_id
  GROUP BY r.m_inoutline_id, r.rtime, r.rdate, r.qty, r.m_product_id, s.c_orderline_id, s.c_order_id, s.order_no, s.dateordered, s.c_bpartner_id, s.qtyordered
),
dem2 AS (
  SELECT d.*,
    greatest(0, least(open_qty, rqty - COALESCE(sum(open_qty) OVER (PARTITION BY m_inoutline_id ORDER BY dateordered, c_orderline_id ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING), 0))) alloc_qty,
    CASE WHEN first_ship_after IS NULL THEN NULL ELSE first_ship_after::date - rdate END ship_days
  FROM dem d WHERE open_qty > 0
)
SQL;
    }

    /**
     * En rad per inleverans + produkt.
     */
    function fetchReceipts($from, $to, $wh = 1000000) {
        $sql = $this->cte() . <<<'SQL'
, agg AS (
  SELECT m_inoutline_id, sum(open_qty) backorder_qty, sum(alloc_qty) alloc_qty,
         sum(alloc_qty) FILTER (WHERE ship_days <= 1) alloc_0_1d,
         sum(alloc_qty) FILTER (WHERE ship_days BETWEEN 2 AND 7) alloc_2_7d,
         sum(alloc_qty) FILTER (WHERE ship_days > 7) alloc_over7d,
         sum(alloc_qty) FILTER (WHERE ship_days IS NULL) alloc_not_shipped,
         count(DISTINCT c_order_id) FILTER (WHERE alloc_qty > 0) n_orders
  FROM dem2 GROUP BY 1
),
rc2 AS (
  SELECT r.*, COALESCE(c.onhand, 0) - COALESCE((SELECT sum(movementqty) FROM trx x WHERE x.m_product_id = r.m_product_id AND x.created >= r.rtime), 0) onhand_before,
         COALESCE((SELECT max(cumq) FROM ship s WHERE s.m_product_id = r.m_product_id AND s.created < r.rtime), 0) shipped_base
  FROM rcpt r LEFT JOIN cur c ON c.m_product_id = r.m_product_id
)
SELECT r.m_inoutline_id, r.documentno receipt_no, r.rdate, r.rtime, r.m_product_id, p.value product_value,
       CASE WHEN manu.name IS NOT NULL AND manu.name <> '' THEN manu.name || ' ' || p.name ELSE p.name END product_name,
       pc.name product_category, bp.name vendor, bpg.name vendor_group,
       r.qty, r.onhand_before,
       (SELECT min(s.created) FROM ship s WHERE s.m_product_id = r.m_product_id AND s.created >= r.rtime AND s.cumq - r.shipped_base >= greatest(r.onhand_before, 0) + 1)::date - r.rdate fifo_first_days,
       (SELECT min(s.created) FROM ship s WHERE s.m_product_id = r.m_product_id AND s.created >= r.rtime AND s.cumq - r.shipped_base >= greatest(r.onhand_before, 0) + r.qty)::date - r.rdate fifo_full_days,
       (SELECT COALESCE(sum(s.qty), 0) FROM ship s WHERE s.m_product_id = r.m_product_id AND s.created >= r.rtime) ship_qty_after,
       COALESCE(a.backorder_qty, 0) backorder_qty, COALESCE(a.alloc_qty, 0) alloc_qty, COALESCE(a.alloc_0_1d, 0) alloc_0_1d,
       COALESCE(a.alloc_2_7d, 0) alloc_2_7d, COALESCE(a.alloc_over7d, 0) alloc_over7d, COALESCE(a.alloc_not_shipped, 0) alloc_not_shipped,
       COALESCE(a.n_orders, 0) n_orders
FROM rc2 r
JOIN m_product p ON p.m_product_id = r.m_product_id
LEFT JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id
LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
LEFT JOIN c_bpartner bp ON bp.c_bpartner_id = r.vendor_id
LEFT JOIN c_bp_group bpg ON bpg.c_bp_group_id = bp.c_bp_group_id
LEFT JOIN agg a ON a.m_inoutline_id = r.m_inoutline_id
ORDER BY r.rtime
SQL;
        return $this->run($sql, array((int)$wh, $from, $to));
    }

    /**
     * En rad per allokerad kundorderrad, med orsak när den inte gick ut direkt.
     */
    function fetchOrders($from, $to, $wh = 1000000) {
        $sql = $this->cte() . <<<'SQL'
, alloc AS MATERIALIZED (SELECT * FROM dem2 WHERE alloc_qty > 0),
sib AS MATERIALIZED (
  SELECT d.m_inoutline_id, d.c_orderline_id,
    count(*) FILTER (WHERE s2.qtyordered - COALESCE((SELECT sum(-t.movementqty) FROM m_transaction t JOIN m_inoutline l ON l.m_inoutline_id = t.m_inoutline_id
                                                    WHERE t.movementtype = 'C-' AND l.c_orderline_id = s2.c_orderline_id AND t.created < d.rtime), 0) > 0) open_siblings,
    count(*) FILTER (WHERE s2.qtyordered - COALESCE((SELECT sum(-t.movementqty) FROM m_transaction t JOIN m_inoutline l ON l.m_inoutline_id = t.m_inoutline_id
                                                    WHERE t.movementtype = 'C-' AND l.c_orderline_id = s2.c_orderline_id AND t.created < d.rtime), 0) > 0
                     AND EXISTS (SELECT 1 FROM m_transaction t JOIN m_locator loc ON loc.m_locator_id = t.m_locator_id
                                 WHERE t.movementtype = 'V+' AND t.m_product_id = s2.m_product_id AND loc.m_warehouse_id = $1
                                   AND t.movementdate >= $2::date AND t.created > d.rtime AND t.created <= COALESCE(d.first_ship_after, now()))) siblings_received_later
  FROM alloc d
  JOIN c_orderline s2 ON s2.c_order_id = d.c_order_id AND s2.c_orderline_id <> d.c_orderline_id AND s2.m_product_id IS NOT NULL AND s2.qtyordered > 0
  WHERE d.ship_days IS NULL OR d.ship_days > 1
  GROUP BY 1, 2
)
SELECT d.m_inoutline_id, r.documentno receipt_no, d.rdate, p.value product_value,
       CASE WHEN manu.name IS NOT NULL AND manu.name <> '' THEN manu.name || ' ' || p.name ELSE p.name END product_name,
       d.c_order_id, d.order_no, d.dateordered, bp.name customer, d.open_qty, d.alloc_qty, d.first_ship_after, d.ship_days,
       COALESCE(s.open_siblings, 0) open_siblings, COALESCE(s.siblings_received_later, 0) siblings_received_later
FROM alloc d
JOIN rcpt r ON r.m_inoutline_id = d.m_inoutline_id
JOIN m_product p ON p.m_product_id = d.m_product_id
LEFT JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id
LEFT JOIN c_bpartner bp ON bp.c_bpartner_id = d.c_bpartner_id
LEFT JOIN sib s ON s.m_inoutline_id = d.m_inoutline_id AND s.c_orderline_id = d.c_orderline_id
ORDER BY d.rtime, d.dateordered
SQL;
        return $this->run($sql, array((int)$wh, $from, $to));
    }

    private function run($sql, $params) {
        if (!$this->ad_r) { $this->error = "Ingen anslutning till ADempiere."; return array(); }
        $res = @pg_query_params($this->ad_r, $sql, $params);
        if (!$res) { $this->error = pg_last_error($this->ad_r); return array(); }
        $out = array();
        while ($row = pg_fetch_assoc($res)) { $out[] = $row; }
        return $out;
    }

    /** Orsakstext för en allokerad orderrad. */
    static function outcome($o) {
        if ($o['ship_days'] === null || $o['ship_days'] === '') return 'Ej skickad ännu';
        $sd = (int)$o['ship_days'];
        if ($sd <= 1) return 'Skickad direkt (0–1 dag)';
        if ((int)$o['siblings_received_later'] > 0) return 'Väntade på annan vara (inlevererad senare)';
        if ((int)$o['open_siblings'] > 0) return 'Andra rader öppna, ingen inleverans';
        return 'Komplett men skickad senare';
    }

    /**
     * Summeringar för sidan (JSON till diagrammen + tabellunderlag).
     */
    function aggregate($rows, $orders) {
        $z = function () { return array('rows' => 0, 'qty' => 0, 'alloc' => 0, 'a01' => 0, 'a27' => 0, 'o7' => 0, 'ns' => 0, 'tl' => 0,
                                        's1' => 0, 's7' => 0, 's30' => 0, 'sover30' => 0, 'kvar' => 0); };
        $add = function (&$g, $r) {
            $qty = (float)$r['qty']; $alloc = (float)$r['alloc_qty']; $tl = $qty - $alloc;
            $g['rows']++; $g['qty'] += $qty; $g['alloc'] += $alloc; $g['a01'] += (float)$r['alloc_0_1d'];
            $g['a27'] += (float)$r['alloc_2_7d']; $g['o7'] += (float)$r['alloc_over7d']; $g['ns'] += (float)$r['alloc_not_shipped'];
            $g['tl'] += $tl;
            if ($tl > 0) {
                $d = ($r['fifo_full_days'] === null || $r['fifo_full_days'] === '') ? null : (int)$r['fifo_full_days'];
                if ($d === null) $g['kvar'] += $tl;
                elseif ($d <= 1) $g['s1'] += $tl;
                elseif ($d <= 7) $g['s7'] += $tl;
                elseif ($d <= 30) $g['s30'] += $tl;
                else $g['sover30'] += $tl;
            }
        };
        $tot = $z(); $day = array(); $cat = array(); $prod = array(); $vend = array();
        foreach ($rows as $r) {
            $add($tot, $r);
            $k = $r['rdate']; if (!isset($day[$k])) $day[$k] = $z(); $add($day[$k], $r);
            $k = $r['product_category'] ?: '(okänd)'; if (!isset($cat[$k])) { $cat[$k] = $z(); $cat[$k]['name'] = $k; } $add($cat[$k], $r);
            $k = $r['product_value'];
            if (!isset($prod[$k])) { $prod[$k] = $z(); $prod[$k]['value'] = $k; $prod[$k]['name'] = $r['product_name']; $prod[$k]['cat'] = $r['product_category']; $prod[$k]['onhand_before'] = (float)$r['onhand_before']; $prod[$k]['fifo'] = array(); }
            $add($prod[$k], $r);
            if ($r['fifo_full_days'] !== null && $r['fifo_full_days'] !== '') $prod[$k]['fifo'][] = (int)$r['fifo_full_days'];
            $k = $r['vendor'] ?: '(okänd)'; if (!isset($vend[$k])) { $vend[$k] = $z(); $vend[$k]['name'] = $k; $vend[$k]['group'] = $r['vendor_group']; } $add($vend[$k], $r);
        }
        ksort($day);
        uasort($cat, function ($a, $b) { return $b['qty'] <=> $a['qty']; });
        uasort($prod, function ($a, $b) { return $b['qty'] <=> $a['qty']; });
        uasort($vend, function ($a, $b) { return $b['qty'] <=> $a['qty']; });

        $hist = array(); $reason = array();
        foreach ($orders as $o) {
            $a = (float)$o['alloc_qty'];
            $k = ($o['ship_days'] === null || $o['ship_days'] === '') ? 'ej' : (string)min((int)$o['ship_days'], 15);
            $hist[$k] = ($hist[$k] ?? 0) + $a;
            $t = self::outcome($o); $reason[$t] = ($reason[$t] ?? 0) + $a;
        }
        return array('tot' => $tot, 'day' => $day, 'cat' => array_values(array_slice($cat, 0, 10)), 'ship_hist' => $hist, 'reason' => $reason,
                     'ndays' => count($day), 'prod' => $prod, 'vend' => $vend);
    }
}
