<?php

require_once("Db.php");

/**
 * Försäljningsexport: en rad per orderrad på slutförda kundorder, för analys och uppföljning.
 *
 * Samma definitioner som orderradsfilen i adempiereTrunk/.claude/notes/orderline-sales-export.md
 * (Webb = säljare 1652736, Företag = affärspartnergrupp 1000000, varukostnad = bokförd kostnad sålda varor
 * konto 4010 på leveransraderna), plus kanal, kategorinivåer och slutförandedatum.
 *
 * Bara SELECT/CTE (inga temp-tabeller: anslutningen är persistent och delas mellan anrop).
 */
class CSalesExport {

    private $ad_r;
    public $error = "";

    /** Kolumner som är tal (decimalkomma i Excel-format). */
    public static $numericColumns = array('Radnummer', 'Antal rader i ordern', 'Antal enheter', 'Värde exkl moms', 'Varukostnad');

    function __construct() {
        $this->ad_r = Db::getConnectionAD(false);
        if ($this->ad_r) { @pg_set_client_encoding($this->ad_r, "UTF8"); }
    }

    /**
     * Parametrar: $1 = 'order' (period avser orderdatum) eller 'done' (period avser slutförandedatum),
     *             $2 = från (datum), $3 = till (datum, inklusive).
     */
    private function sql() {
        return <<<'SQL'
WITH RECURSIVE up AS (
  SELECT c.m_product_category_id leaf, c.m_product_category_parent_id par, ARRAY[trim(c.name)] names, 1 depth
  FROM m_product_category c
  UNION ALL
  SELECT up.leaf, c.m_product_category_parent_id, trim(c.name) || up.names, up.depth + 1
  FROM up JOIN m_product_category c ON c.m_product_category_id = up.par WHERE up.depth < 8
),
cat AS (SELECT DISTINCT ON (leaf) leaf, names FROM up ORDER BY leaf, depth DESC),
ord AS MATERIALIZED (
  SELECT o.c_order_id, o.documentno, o.dateordered::date AS orderdate,
         (to_timestamp(o.processedon / 1000.0) AT TIME ZONE 'Europe/Stockholm')::date AS donedate,
         o.docstatus, o.salesrep_id, o.ordermethod, o.deliveryviarule, o.c_bpartner_id,
         o.c_currency_id, o.c_conversiontype_id, o.dateacct, o.ad_client_id, o.ad_org_id
  FROM c_order o
  JOIN c_doctype dt ON dt.c_doctype_id = o.c_doctypetarget_id
  WHERE o.ad_client_id = 1000000 AND o.issotrx = 'Y' AND dt.docsubtypeso = 'SO' AND o.docstatus IN ('CO','CL')
    AND (($1::text = 'order' AND o.dateordered >= $2::date AND o.dateordered < $3::date + 1)
      OR ($1::text = 'done' AND o.processedon >= extract(epoch FROM $2::date::timestamp AT TIME ZONE 'Europe/Stockholm') * 1000
                            AND o.processedon <  extract(epoch FROM ($3::date + 1)::timestamp AT TIME ZONE 'Europe/Stockholm') * 1000))
),
lines AS MATERIALIZED (
  SELECT ol.c_orderline_id, ol.line, ol.c_order_id, ol.m_product_id, ol.c_charge_id, ol.qtyordered, ol.qtydelivered,
         ol.pricelimit, ol.linenetamt,
         count(*) OVER (PARTITION BY ol.c_order_id) AS n_lines
  FROM c_orderline ol JOIN ord o ON o.c_order_id = ol.c_order_id
  WHERE ol.qtyordered > 0
),
cogs AS MATERIALIZED (
  SELECT sl.c_orderline_id, sum(fa.amtacctdr - fa.amtacctcr) AS cogs
  FROM lines l
  JOIN m_inoutline sl ON sl.c_orderline_id = l.c_orderline_id
  JOIN fact_acct fa ON fa.ad_table_id = 319 AND fa.record_id = sl.m_inout_id AND fa.line_id = sl.m_inoutline_id AND fa.account_id = 1000049
  GROUP BY 1
)
SELECT o.documentno AS "Ordernummer", o.orderdate AS "Orderdatum", o.donedate AS "Slutfört datum",
       CASE o.docstatus WHEN 'CO' THEN 'Slutförd' WHEN 'CL' THEN 'Stängd' ELSE o.docstatus END AS "Orderstatus",
       CASE WHEN o.salesrep_id = 1652736 THEN 'Webb'
            WHEN o.ordermethod IN ('P','TO','MUO') THEN 'Telefon'
            WHEN o.ordermethod IN ('M','MO','O') THEN 'E-post'
            WHEN o.ordermethod IN ('F','FO') THEN 'Fax'
            WHEN o.deliveryviarule = 'P' THEN 'Butik'
            ELSE 'Övrig manuell' END AS "Kanal",
       CASE WHEN bp.c_bp_group_id = 1000000 THEN 'Företag' ELSE 'Privat' END AS "Kundtyp",
       bpg.name AS "Kundgrupp", trim(bp.value) AS "Kund-ID",
       COALESCE(cat.names[1], '(ingen kategori)') AS "Huvudkategori",
       COALESCE(cat.names[2], cat.names[1], '(ingen kategori)') AS "Överkategori",
       COALESCE(trim(pc.name), '(ingen kategori)') AS "Kategori",
       array_to_string(cat.names, ' > ') AS "Kategorisökväg",
       trim(p.value) AS "Artikelnummer", COALESCE(p.name, ch.name) AS "Produktnamn",
       CASE WHEN p.producttype = 'I' THEN 'Vara' WHEN p.producttype = 'S' THEN 'Tjänst' WHEN p.producttype = 'R' THEN 'Resurs'
            WHEN l.c_charge_id IS NOT NULL THEN 'Avgift' ELSE 'Övrigt' END AS "Radtyp",
       l.line AS "Radnummer", l.n_lines AS "Antal rader i ordern", l.qtyordered AS "Antal enheter",
       round(CASE WHEN o.c_currency_id = 311 THEN l.linenetamt
                  ELSE currencyconvert(l.linenetamt, o.c_currency_id, 311, o.dateacct, o.c_conversiontype_id, o.ad_client_id, o.ad_org_id) END, 2) AS "Värde exkl moms",
       round(CASE WHEN c.cogs IS NOT NULL THEN c.cogs + l.pricelimit * greatest(l.qtyordered - l.qtydelivered, 0)
                  ELSE l.pricelimit * l.qtyordered END, 2) AS "Varukostnad"
FROM lines l
JOIN ord o ON o.c_order_id = l.c_order_id
LEFT JOIN cogs c ON c.c_orderline_id = l.c_orderline_id
LEFT JOIN m_product p ON p.m_product_id = l.m_product_id
LEFT JOIN c_charge ch ON ch.c_charge_id = l.c_charge_id
LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
LEFT JOIN cat ON cat.leaf = p.m_product_category_id
LEFT JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id
LEFT JOIN c_bp_group bpg ON bpg.c_bp_group_id = bp.c_bp_group_id
ORDER BY o.orderdate, o.documentno, l.line
SQL;
    }

    /**
     * Kör frågan och returnerar pg-resultatet (läses rad för rad av anroparen), eller false vid fel.
     * @param string $basis 'order' eller 'done'
     */
    function query($from, $to, $basis = 'order') {
        $this->error = "";
        if (!$this->ad_r) { $this->error = "Ingen anslutning till ADempiere."; return false; }
        $res = @pg_query_params($this->ad_r, $this->sql(), array($basis === 'done' ? 'done' : 'order', $from, $to));
        if (!$res) { $this->error = pg_last_error($this->ad_r); return false; }
        return $res;
    }

    /** Kolumnnamn i resultatet, i ordning. */
    static function columns($res) {
        $cols = array();
        $n = pg_num_fields($res);
        for ($i = 0; $i < $n; $i++) { $cols[] = pg_field_name($res, $i); }
        return $cols;
    }
}
