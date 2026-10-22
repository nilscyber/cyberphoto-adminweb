<?php

require_once("Db.php");

/**
 * Försäljningsexport: en rad per orderrad på kundorder, för analys och uppföljning.
 *
 * Tre lägen (samma kolumner):
 *   order  – orderingång: ordrar skapade i perioden (C_Order.Created)
 *   done   – slutförd försäljning: ordrar slutförda i perioden (C_Order.ProcessedOn)
 *   open   – öppen orderstock just nu: ordrar med minst en varurad som inte är fullt levererad (ingen period)
 *
 * Status: allt utom Utkast (DR), Annullerad (VO) och Återkallad (RE). Annullerade ordrar har
 * kvantitet 0 på alla rader i ADempiere och kan därför inte visas med värde.
 *
 * Definitioner som i adempiereTrunk/.claude/notes/orderline-sales-export.md: Webb = säljare 1652736,
 * Företag = affärspartnergrupp 1000000, varukostnad = bokförd kostnad sålda varor (konto 4010) på
 * leveransraderna, kreditering via kreditfaktura -> RMA-rad -> leveransrad -> orderrad.
 *
 * Bara SELECT/CTE (inga temp-tabeller: anslutningen är persistent och delas mellan anrop).
 */
class CSalesExport {

    private $ad_r;
    public $error = "";

    /** Kolumner som är tal (decimalkomma i Excel-format). */
    public static $numericColumns = array('Radnummer', 'Antal rader i ordern', 'Antal enheter', 'Levererat antal', 'Kvarstående antal',
        'Värde exkl moms', 'Kvarstående värde', 'Varukostnad', 'Krediterat antal', 'Krediterat belopp',
        'Lagersaldo', 'Tillgängligt i lager', 'Beställt hos leverantör');

    function __construct() {
        $this->ad_r = Db::getConnectionAD(false);
        if ($this->ad_r) {
            @pg_set_client_encoding($this->ad_r, "UTF8");
            // Läshosten har work_mem 4 MB; hash-joinen mot fact_acct spiller annars till disk och tar minuter.
            @pg_query($this->ad_r, "SET work_mem = '64MB'");
        }
    }

    /**
     * Delar upp perioden i kalendermånader (hela ordrar hamnar i exakt en bit, så radräkningen per order stämmer).
     * Varje bit körs som egen fråga: första byten kommer snabbt, proxyn hinner inte ge upp, och en avbruten
     * nedladdning slutar efter pågående bit i stället för att köra hela perioden klart i bakgrunden.
     * @return array av array(from, to); open-läget ger en enda bit med null-datum.
     */
    static function chunks($from, $to, $mode) {
        if ($mode === 'open') return array(array(null, null));
        $out = array();
        $cur = new DateTime($from); $end = new DateTime($to);
        while ($cur <= $end) {
            $last = (clone $cur)->modify('last day of this month');
            if ($last > $end) $last = clone $end;
            $out[] = array($cur->format('Y-m-d'), $last->format('Y-m-d'));
            $cur = (clone $last)->modify('+1 day');
        }
        return $out;
    }

    /**
     * Bygger frågan för ett läge. Parametrar för order/done: $1 = från (datum), $2 = till (datum, inklusive). open: inga.
     */
    private function sql($mode) {
        if ($mode === 'done') {
            $where = "o.processedon >= extract(epoch FROM $1::date::timestamp AT TIME ZONE 'Europe/Stockholm') * 1000
                  AND o.processedon <  extract(epoch FROM ($2::date + 1)::timestamp AT TIME ZONE 'Europe/Stockholm') * 1000";
            $cmdate = "AND i.dateinvoiced >= $1::date - 365";
        } elseif ($mode === 'open') {
            // id-listan hämtas i ett eget steg (query()): ger planeraren rätt radantal, annars hashas hela c_order
            $where = "o.c_order_id = ANY($1::int[])";
            $cmdate = "AND i.dateinvoiced >= current_date - 730";
        } else {
            $where = "o.created >= $1::date AND o.created < $2::date + 1";
            $cmdate = "AND i.dateinvoiced >= $1::date";
        }
        $openCols = "";
        $openJoins = "";
        if ($mode === 'open') {
            $openCols = <<<'SQL'
,
       st.qtyonhand AS "Lagersaldo", st.qtyavailable AS "Tillgängligt i lager", st.qtyordered AS "Beställt hos leverantör",
       vend.name AS "Leverantör",
       l.datepromised::date AS "Beräknat leveransdatum",
       CASE l.datepromisedprecision WHEN 'D' THEN 'På dagen' WHEN 'W' THEN 'Samma vecka' WHEN 'P' THEN 'Del av månad' WHEN 'M' THEN 'Samma månad' WHEN 'U' THEN 'Okänt' ELSE l.datepromisedprecision END AS "Leveransdatum precision",
       CASE WHEN l.qtyordered - l.qtydelivered <= 0 THEN 'Levererad'
            WHEN p.m_product_id IS NULL OR p.producttype <> 'I' THEN 'Tjänst/avgift'
            WHEN COALESCE(st.qtyonhand, 0) >= l.qtyordered - l.qtydelivered THEN 'Finns i lager, ej skickad'
            WHEN COALESCE(st.qtyonhand, 0) > 0 THEN 'Delvis i lager'
            WHEN COALESCE(st.qtyordered, 0) > 0 THEN 'Väntar på inleverans (beställd hos leverantör)'
            ELSE 'Saknas i lager, ej beställd' END AS "Restorsak"
SQL;
            $openJoins = <<<'SQL'

LEFT JOIN m_product_cache st ON st.m_product_id = p.m_product_id AND st.m_warehouse_id = 1000000
LEFT JOIN (SELECT DISTINCT ON (po.m_product_id) po.m_product_id, po.c_bpartner_id FROM m_product_po po WHERE po.isactive = 'Y'
           ORDER BY po.m_product_id, (po.iscurrentvendor = 'Y') DESC) tv ON tv.m_product_id = p.m_product_id
LEFT JOIN c_bpartner vend ON vend.c_bpartner_id = tv.c_bpartner_id
SQL;
        }

        return <<<SQL
WITH RECURSIVE up AS (
  SELECT c.m_product_category_id leaf, c.m_product_category_parent_id par, ARRAY[trim(c.name)] names, 1 depth
  FROM m_product_category c
  UNION ALL
  SELECT up.leaf, c.m_product_category_parent_id, trim(c.name) || up.names, up.depth + 1
  FROM up JOIN m_product_category c ON c.m_product_category_id = up.par WHERE up.depth < 8
),
cat AS (SELECT DISTINCT ON (leaf) leaf, names FROM up ORDER BY leaf, depth DESC),
ord AS MATERIALIZED (
  SELECT o.c_order_id, o.documentno, o.created::date AS orderdate,
         (to_timestamp(o.processedon / 1000.0) AT TIME ZONE 'Europe/Stockholm')::date AS donedate,
         o.docstatus, o.isdelivered, o.ispartdelivered, o.salesrep_id, o.ordermethod, o.deliveryviarule, o.c_bpartner_id,
         o.c_currency_id, o.c_conversiontype_id, o.dateacct, o.ad_client_id, o.ad_org_id
  FROM c_order o
  JOIN c_doctype dt ON dt.c_doctype_id = o.c_doctypetarget_id
  WHERE o.ad_client_id = 1000000 AND o.issotrx = 'Y' AND dt.docsubtypeso = 'SO' AND o.docstatus NOT IN ('DR','VO','RE')
    AND $where
),
lines AS MATERIALIZED (
  SELECT ol.c_orderline_id, ol.line, ol.c_order_id, ol.m_product_id, ol.c_charge_id, ol.qtyordered, ol.qtydelivered,
         ol.pricelimit, ol.linenetamt, ol.datepromised, ol.datepromisedprecision,
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
),
cm AS MATERIALIZED (
  SELECT sl.c_orderline_id, sum(il.qtyinvoiced) AS cr_qty,
         sum(CASE WHEN i.c_currency_id = 311 THEN il.linenetamt
                  ELSE currencyconvert(il.linenetamt, i.c_currency_id, 311, i.dateacct, i.c_conversiontype_id, i.ad_client_id, i.ad_org_id) END) AS cr_amt
  FROM c_invoice i
  JOIN c_doctype dt ON dt.c_doctype_id = i.c_doctypetarget_id
  JOIN c_invoiceline il ON il.c_invoice_id = i.c_invoice_id
  JOIN m_rmaline rl ON rl.m_rmaline_id = il.m_rmaline_id
  JOIN m_inoutline sl ON sl.m_inoutline_id = rl.m_inoutline_id
  JOIN lines l ON l.c_orderline_id = sl.c_orderline_id
  WHERE i.ad_client_id = 1000000 AND i.issotrx = 'Y' AND dt.docbasetype = 'ARC' AND i.docstatus IN ('CO','CL') $cmdate
  GROUP BY 1
),
x AS (
  SELECT l.*, o.c_currency_id, o.c_conversiontype_id, o.dateacct, o.ad_client_id, o.ad_org_id,
         CASE WHEN o.c_currency_id = 311 THEN l.linenetamt
              ELSE currencyconvert(l.linenetamt, o.c_currency_id, 311, o.dateacct, o.c_conversiontype_id, o.ad_client_id, o.ad_org_id) END AS netamt_sek
  FROM lines l JOIN ord o ON o.c_order_id = l.c_order_id
)
SELECT o.documentno AS "Ordernummer", o.orderdate AS "Orderdatum", o.donedate AS "Slutfört datum",
       CASE o.docstatus WHEN 'CO' THEN 'Slutförd' WHEN 'CL' THEN 'Avslutad' WHEN 'IP' THEN 'Under bearbetning'
            WHEN 'WP' THEN 'Inväntar betalning' WHEN 'WC' THEN 'Inväntar godkännande' WHEN 'AP' THEN 'Godkänd' WHEN 'NA' THEN 'Inte godkänd' WHEN 'IN' THEN 'Felaktig' ELSE o.docstatus END AS "Orderstatus",
       CASE WHEN o.isdelivered = 'Y' THEN 'Ja' ELSE 'Nej' END AS "Levererad",
       CASE WHEN o.ispartdelivered = 'Y' THEN 'Ja' ELSE 'Nej' END AS "Dellevererad",
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
       l.line AS "Radnummer", l.n_lines AS "Antal rader i ordern",
       l.qtyordered AS "Antal enheter", l.qtydelivered AS "Levererat antal",
       greatest(l.qtyordered - l.qtydelivered, 0) AS "Kvarstående antal",
       round(l.netamt_sek, 2) AS "Värde exkl moms",
       round(l.netamt_sek * greatest(l.qtyordered - l.qtydelivered, 0) / l.qtyordered, 2) AS "Kvarstående värde",
       round(CASE WHEN c.cogs IS NOT NULL THEN c.cogs + l.pricelimit * greatest(l.qtyordered - l.qtydelivered, 0)
                  ELSE l.pricelimit * l.qtyordered END, 2) AS "Varukostnad",
       COALESCE(cm.cr_qty, 0) AS "Krediterat antal", round(COALESCE(cm.cr_amt, 0), 2) AS "Krediterat belopp"$openCols
FROM x l
JOIN ord o ON o.c_order_id = l.c_order_id
LEFT JOIN cogs c ON c.c_orderline_id = l.c_orderline_id
LEFT JOIN cm ON cm.c_orderline_id = l.c_orderline_id
LEFT JOIN m_product p ON p.m_product_id = l.m_product_id
LEFT JOIN c_charge ch ON ch.c_charge_id = l.c_charge_id
LEFT JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
LEFT JOIN cat ON cat.leaf = p.m_product_category_id
LEFT JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id
LEFT JOIN c_bp_group bpg ON bpg.c_bp_group_id = bp.c_bp_group_id$openJoins
ORDER BY o.orderdate, o.documentno, l.line
SQL;
    }

    /**
     * Kör frågan och returnerar pg-resultatet (läses rad för rad av anroparen), eller false vid fel.
     * @param string $mode 'order', 'done' eller 'open' (open ignorerar datumen)
     */
    function query($from, $to, $mode = 'order') {
        $this->error = "";
        if (!$this->ad_r) { $this->error = "Ingen anslutning till ADempiere."; return false; }
        $mode = in_array($mode, array('order', 'done', 'open'), true) ? $mode : 'order';
        if ($mode === 'open') {
            // Steg 1: ordrar med minst en varurad som inte är fullt levererad (sekventiell läsning av c_orderline, ~1 s)
            $r = @pg_query($this->ad_r, "SELECT DISTINCT x.c_order_id FROM c_orderline x WHERE x.m_product_id IS NOT NULL AND x.qtyordered > x.qtydelivered");
            if (!$r) { $this->error = pg_last_error($this->ad_r); return false; }
            $ids = array();
            while ($row = pg_fetch_row($r)) { $ids[] = (int)$row[0]; }
            $params = array('{' . implode(',', $ids) . '}');
        } else {
            $params = array($from, $to);
        }
        $res = @pg_query_params($this->ad_r, $this->sql($mode), $params);
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
