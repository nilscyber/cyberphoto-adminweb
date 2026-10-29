<?php

require_once("Db.php");

/**
 * Drawer-innehåll för en in- eller utleverans (M_InOut), samma upplägg som orderdrawern
 * (CSearch::renderOrderDetailsAD): rubrik + status, metarader, partnerkort, rader, summering,
 * interna kommentarer. Öppnas via ajax/drawer_details.php?type=inout&id=<dokumentnummer>
 * och search_dispatch.php?mode=inout&q=<dokumentnummer>.
 *
 * Dokumentnumren delas av alla M_InOut-typer (inleverans, utleverans, kundretur,
 * leverantörsretur) och är unika, så numret räcker som nyckel.
 */
class CInOutDetails {

    /** Rubrik och partneretikett per rörelsetyp. */
    private static $TYPES = array(
        'V+' => array('title' => 'Inleverans',       'partner' => 'Leverantör', 'order' => 'Inköpsorder'),
        'V-' => array('title' => 'Leverantörsretur', 'partner' => 'Leverantör', 'order' => 'Inköpsorder'),
        'C-' => array('title' => 'Utleverans',       'partner' => 'Kund',       'order' => 'Order'),
        'C+' => array('title' => 'Kundretur',        'partner' => 'Kund',       'order' => 'Order'),
    );

    public static function render($documentNo) {
        $documentNo = trim((string)$documentNo);
        if ($documentNo === '' || !ctype_digit($documentNo)) {
            return '<div class="dw-wrap"><p>Ogiltigt dokumentnummer.</p></div>';
        }
        $pg = Db::getConnectionAD(false);
        if (!$pg) return '<div class="dw-wrap"><p>Kunde inte ansluta till ADempiere-databasen.</p></div>';
        @pg_set_client_encoding($pg, "UTF8");
        $eh = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
        $qty = function ($n) { $n = (float)$n; return ($n == floor($n)) ? number_format($n, 0, ',', ' ') : number_format($n, 2, ',', ' '); };
        $amt = function ($n) { return number_format((float)$n, 2, ',', ' '); };

        // ----- huvud -----
        $sql = <<<'SQL'
SELECT io.m_inout_id, io.documentno, io.docstatus, io.movementtype, io.issotrx, io.movementdate::date movementdate,
       io.created, io.processedon, io.poreference, io.description, io.trackingno, io.nopackages,
       rv.documentno reversal_no, dt.name doctype, w.name warehouse, cu.name created_by,
       bp.value bp_value, bp.name bp_name, bp.name2 bp_name2, g.name bp_group,
       loc.address1, loc.address2, loc.postal, loc.city, ctrl.name country,
       u.name contact_name, u.email contact_email, COALESCE(NULLIF(u.phone, ''), u.phone2) contact_phone,
       o.documentno order_no, o.dateordered::date order_date, sh.name shipper
FROM m_inout io
JOIN c_doctype dt ON dt.c_doctype_id = io.c_doctype_id
JOIN m_warehouse w ON w.m_warehouse_id = io.m_warehouse_id
JOIN c_bpartner bp ON bp.c_bpartner_id = io.c_bpartner_id
LEFT JOIN c_bp_group g ON g.c_bp_group_id = bp.c_bp_group_id
LEFT JOIN ad_user cu ON cu.ad_user_id = io.createdby
LEFT JOIN c_bpartner_location bpl ON bpl.c_bpartner_location_id = io.c_bpartner_location_id
LEFT JOIN c_location loc ON loc.c_location_id = bpl.c_location_id
LEFT JOIN c_country_trl ctrl ON ctrl.c_country_id = loc.c_country_id AND ctrl.ad_language = 'sv_SE'
LEFT JOIN ad_user u ON u.ad_user_id = io.ad_user_id
LEFT JOIN c_order o ON o.c_order_id = io.c_order_id
LEFT JOIN m_shipper sh ON sh.m_shipper_id = io.m_shipper_id
LEFT JOIN m_inout rv ON rv.m_inout_id = io.reversal_id
WHERE io.documentno = $1
ORDER BY io.created DESC
LIMIT 1
SQL;
        $res = @pg_query_params($pg, $sql, array($documentNo));
        if (!$res) {
            // Persistent anslutning (pg_pconnect) som dött sedan förra anropet: återställ och försök en gång till
            $err = pg_last_error($pg);
            error_log('CInOutDetails: ' . $err . ' (återställer anslutningen)');
            if (@pg_connection_reset($pg)) {
                @pg_query($pg, "SET TIME ZONE 'Europe/Stockholm'");
                @pg_set_client_encoding($pg, "UTF8");
                $res = @pg_query_params($pg, $sql, array($documentNo));
            }
            if (!$res) return '<div class="dw-wrap"><p>Kunde inte läsa från ADempiere: ' . $eh($err !== '' ? $err : pg_last_error($pg)) . '</p></div>';
        }
        if (pg_num_rows($res) === 0) {
            return '<div class="dw-wrap"><p>Ingen in- eller utleverans hittades för nummer ' . $eh($documentNo) . '.</p></div>';
        }
        $io = pg_fetch_assoc($res);
        pg_free_result($res);
        $T = isset(self::$TYPES[$io['movementtype']]) ? self::$TYPES[$io['movementtype']] : array('title' => 'Leverans', 'partner' => 'Partner', 'order' => 'Order');
        $isPurchase = ($io['issotrx'] !== 'Y');

        // ----- rader: fakturerat = matchning mot leverantörsfaktura (inköp) eller kundfakturarader (försäljning) -----
        $invSql = $isPurchase
            ? "(SELECT COALESCE(sum(mi.qty), 0) FROM m_matchinv mi WHERE mi.m_inoutline_id = l.m_inoutline_id)"
            : "(SELECT COALESCE(sum(il.qtyinvoiced), 0) FROM c_invoiceline il JOIN c_invoice i ON i.c_invoice_id = il.c_invoice_id WHERE il.m_inoutline_id = l.m_inoutline_id AND i.docstatus IN ('CO','CL'))";
        $invNoSql = $isPurchase
            ? "(SELECT string_agg(DISTINCT i.documentno, ', ') FROM m_matchinv mi JOIN c_invoiceline il ON il.c_invoiceline_id = mi.c_invoiceline_id JOIN c_invoice i ON i.c_invoice_id = il.c_invoice_id WHERE mi.m_inoutline_id = l.m_inoutline_id)"
            : "(SELECT string_agg(DISTINCT i.documentno, ', ') FROM c_invoiceline il JOIN c_invoice i ON i.c_invoice_id = il.c_invoice_id WHERE il.m_inoutline_id = l.m_inoutline_id AND i.docstatus IN ('CO','CL'))";
        $sqlLines = "
SELECT l.line, l.movementqty, l.description note, l.m_product_id,
       p.value product_value, COALESCE(pt.name, p.name) product_name, m.name manufacturer_name, p.istradein,
       lc.value locator, ch.name charge_name,
       ol.line order_line, ol.qtyordered, ol.qtydelivered, ol.priceactual, o.documentno order_no, cur.iso_code currency,
       $invSql qty_invoiced, $invNoSql invoice_nos
FROM m_inoutline l
LEFT JOIN m_product p ON p.m_product_id = l.m_product_id
LEFT JOIN m_product_trl pt ON pt.m_product_id = p.m_product_id AND pt.ad_language = 'sv_SE'
LEFT JOIN xc_manufacturer m ON m.xc_manufacturer_id = p.xc_manufacturer_id
LEFT JOIN m_locator lc ON lc.m_locator_id = l.m_locator_id
LEFT JOIN c_charge ch ON ch.c_charge_id = l.c_charge_id
LEFT JOIN c_orderline ol ON ol.c_orderline_id = l.c_orderline_id
LEFT JOIN c_order o ON o.c_order_id = ol.c_order_id
LEFT JOIN c_currency cur ON cur.c_currency_id = o.c_currency_id
WHERE l.m_inout_id = $1
ORDER BY l.line";
        $lines = array();
        if ($r = @pg_query_params($pg, $sqlLines, array((int)$io['m_inout_id']))) {
            while ($row = pg_fetch_assoc($r)) $lines[] = $row;
            pg_free_result($r);
        }

        // ----- interna kommentarer (M_InOut = AD_Table_ID 319) -----
        $chat = array();
        $sqlChat = "SELECT ch.created, ad.name, ch.characterdata FROM cm_chat c JOIN cm_chatentry ch ON ch.cm_chat_id = c.cm_chat_id
                    JOIN ad_user ad ON ad.ad_user_id = ch.updatedby WHERE c.ad_table_id = 319 AND c.record_id = $1 ORDER BY ch.created";
        if ($r = @pg_query_params($pg, $sqlChat, array((int)$io['m_inout_id']))) {
            while ($row = pg_fetch_assoc($r)) $chat[] = $row;
            pg_free_result($r);
        }

        // ----- status: dokumentstatus, och för slutförda hur mycket som är fakturerat -----
        $statusMap = array(
            'DR' => array('Utkast', 'badge-status-draft'), 'IP' => array('Under bearbetning', 'badge-status-process'),
            'IN' => array('Under bearbetning', 'badge-status-process'), 'CO' => array('Slutförd', 'badge-status-sent'),
            'CL' => array('Stängd', 'badge-status-sent'), 'VO' => array('Annullerad', 'badge-status-cancel'),
            'RE' => array('Återförd', 'badge-status-cancel'),
        );
        $ds = strtoupper((string)$io['docstatus']);
        list($statusLabel, $statusCls) = isset($statusMap[$ds]) ? $statusMap[$ds] : array($ds, 'badge-status-process');
        $invBadge = '';
        if (in_array($ds, array('CO', 'CL'), true)) {
            $sumQ = 0; $sumInv = 0;
            foreach ($lines as $ln) { if ((float)$ln['movementqty'] > 0) { $sumQ += (float)$ln['movementqty']; $sumInv += min((float)$ln['qty_invoiced'], (float)$ln['movementqty']); } }
            if ($sumQ > 0) {
                if ($sumInv + 0.0001 >= $sumQ) $invBadge = '<span class="badge-status badge-status-sent">Fakturerad</span>';
                elseif ($sumInv > 0)          $invBadge = '<span class="badge-status badge-status-partial">Delvis fakturerad</span>';
                else                           $invBadge = '<span class="badge-status badge-status-draft">Ej fakturerad</span>';
            }
        }

        $completed = '';
        if ((float)$io['processedon'] > 0) $completed = date('Y-m-d H:i:s', (int)floor((float)$io['processedon'] / 1000));
        $created = substr((string)$io['created'], 0, 19);
        $orderUrl = function ($no) { return '/search_dispatch.php?mode=order&page=1&q=' . rawurlencode($no); };
        $inoutUrl = function ($no) { return '/search_dispatch.php?mode=inout&q=' . rawurlencode($no); };

        // ----- CSS (samma formspråk som orderdrawern) -----
        $html = '<style>
        .dw-table-orderlines{width:100%;border-collapse:separate;border-spacing:0;table-layout:auto}
        .dw-table-orderlines th,.dw-table-orderlines td{padding:6px 10px}
        .dw-table-orderlines thead th{background:#d1f2f0;font-weight:700}
        .dw-table-orderlines tr.ol-main,.dw-table-orderlines tr.ol-sub{background:#fff !important}
        .dw-table-orderlines tr.ol-main.is-invoiced{background:#bbf7d0 !important}
        .dw-table-orderlines tr.ol-sub.is-invoiced{background:#dcfce7 !important}
        .dw-table-orderlines tr.ol-sub td{font-size:11px;color:#444;border-top:0;padding-top:0;padding-bottom:8px}
        .dw-table-orderlines tr.ol-sep td{padding:0;height:12px;background:#fff;border-bottom:1px solid #cbcbcb}
        .dw-table-orderlines td.text-right{white-space:nowrap}
        .dw-address-card{border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;padding:8px 10px;margin:8px 0 10px}
        .dw-address-title{font-weight:700;margin-bottom:4px}
        .dw-section-compact{margin-bottom:14px}
        .order-status-wrap{display:flex;gap:8px;align-items:center;justify-content:flex-end;flex-wrap:wrap;margin-left:8px;margin-top:4px;margin-bottom:2px}
        .dw-title-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:2px}
        .dw-title{margin:0}
        .badge-status{display:inline-block;padding:2px 10px;border-radius:999px;border:1px solid #d1d5db;font-size:12px;font-weight:700}
        .badge-status-draft{background:#f3f4f6;border-color:#d1d5db;color:#6b7280}
        .badge-status-process{background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8}
        .badge-status-sent{background:#ecfdf5;border-color:#6ee7b7;color:#065f46}
        .badge-status-partial{background:#fff7ed;border-color:#fed7aa;color:#9a3412}
        .badge-status-cancel{background:#fee2e2;border-color:#fecaca;color:#991b1b}
        .badge-partner{display:inline-block;padding:1px 8px;border-radius:999px;font-size:11px;font-weight:700;border:1px solid #bfdbfe;background:#eff6ff;color:#1d4ed8;margin-left:6px;vertical-align:middle}
        .badge-tradein{display:inline-block;padding:0 6px;border-radius:999px;font-size:11px;font-weight:700;background:#fef3c7;border:1px solid #fde68a;color:#92400e;margin-left:4px}
        .copy-chip{cursor:pointer}
        .copy-chip:hover{background:#f3f4f6;border-radius:3px}
        .copy-chip.copied{background:#ecfdf5;box-shadow:inset 0 0 0 1px #34d399;border-radius:3px}
        .customer-contacts{margin-top:8px;padding-top:8px;border-top:1px solid #e5e7eb;font-size:13px}
        .order-chat-header{font-weight:700;margin-bottom:4px}
        .order-chat-entry{margin-bottom:10px;padding:8px 10px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb}
        .order-chat-meta{font-size:12px;color:#6b7280;margin-bottom:4px}
        .order-chat-body{font-size:13px;color:#111;line-height:1.5}
        .ord-summary{margin-top:10px;padding:10px 14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px}
        .ord-summary-top{display:flex;gap:20px;align-items:center;font-size:13px;flex-wrap:wrap}
        .ost-item{white-space:nowrap}
        .ost-lbl{color:#6b7280}
        .ost-val{font-weight:700;color:#111}
        .ost-sep{color:#d1d5db;font-size:16px;line-height:1}
        </style>';

        // ----- rubrik -----
        $html .= '<div class="dw-wrap"><div class="dw-header"><div class="dw-title-row">';
        $html .= '<h2 class="dw-title">' . $eh($T['title']) . ' <span class="copy-chip" data-copy="' . $eh($io['documentno']) . '" title="Kopiera nummer">' . $eh($io['documentno']) . '</span></h2>';
        $html .= '<div class="order-status-wrap"><span class="badge-status ' . $statusCls . '">' . $eh($statusLabel) . '</span>' . $invBadge . '</div>';
        $html .= '</div>';

        $row = function ($label, $valueHtml) { return '<div class="dw-row"><span class="dw-label">' . $label . ':</span> <span class="dw-value">' . $valueHtml . '</span></div>'; };
        $html .= '<div class="dw-section dw-section-compact">';
        $html .= $row($isPurchase && $io['movementtype'] === 'V+' ? 'Inlevererad' : 'Datum', '<strong>' . $eh($io['movementdate']) . '</strong>');
        if ($completed !== '') $html .= $row('Slutförd', '<strong>' . $eh($completed) . '</strong>');
        $html .= $row('Skapad', '<strong>' . $eh($created) . '</strong>' . ($io['created_by'] ? ' av <strong>' . $eh($io['created_by']) . '</strong>' : ''));
        $html .= $row('Lager', '<strong>' . $eh($io['warehouse']) . '</strong>');
        $html .= '</div></div>';

        $html .= '<div class="dw-section dw-section-compact">';
        // En leverans kan skapas från flera order: visa de order raderna kommer från, annars huvudets order
        $orderNos = array();
        foreach ($lines as $ln) { if (!empty($ln['order_no'])) $orderNos[$ln['order_no']] = true; }
        if (!$orderNos && !empty($io['order_no'])) $orderNos[$io['order_no']] = true;
        if ($orderNos) {
            $links = array();
            foreach (array_keys($orderNos) as $no) {
                $links[] = '<a href="' . $eh($orderUrl($no)) . '" target="_blank" rel="noopener"><strong>' . $eh($no) . '</strong></a>'
                         . (count($orderNos) === 1 && $no === $io['order_no'] && $io['order_date'] ? ' (' . $eh($io['order_date']) . ')' : '');
            }
            $html .= $row($T['order'], implode(', ', $links));
        }
        if (trim((string)$io['poreference']) !== '') $html .= $row($isPurchase ? 'Leverantörens referens' : 'Kundens referens', '<strong>' . $eh($io['poreference']) . '</strong>');
        if (!empty($io['shipper'])) $html .= $row('Leveranssätt', '<strong>' . $eh($io['shipper']) . '</strong>');
        if (trim((string)$io['trackingno']) !== '') $html .= $row('Spårningsnummer', '<strong>' . $eh($io['trackingno']) . '</strong>');
        if ((int)$io['nopackages'] > 0) $html .= $row('Antal kollin', '<strong>' . (int)$io['nopackages'] . '</strong>');
        if (!empty($io['reversal_no'])) {
            $html .= $row('Återförd mot', '<a href="' . $eh($inoutUrl($io['reversal_no'])) . '" target="_blank" rel="noopener"><strong>' . $eh($io['reversal_no']) . '</strong></a>');
        }
        $html .= $row('Dokumenttyp', $eh($io['doctype']));
        $html .= '</div>';

        // ----- partnerkort -----
        $html .= '<div class="dw-section"><div class="dw-address-card">';
        $html .= '<div class="dw-address-title">' . $eh($T['partner']) . ($io['bp_group'] && $io['bp_group'] !== $T['partner'] ? '<span class="badge-partner">' . $eh($io['bp_group']) . '</span>' : '') . '</div><p>';
        $html .= $eh($io['bp_name']) . ($io['bp_value'] !== '' ? ' (<span class="copy-chip" data-copy="' . $eh($io['bp_value']) . '" title="Kopiera nummer">' . $eh($io['bp_value']) . '</span>)' : '') . '<br>';
        if (trim((string)$io['bp_name2']) !== '' && trim((string)$io['bp_name2']) !== trim((string)$io['bp_name'])) $html .= $eh($io['bp_name2']) . '<br>';
        if (!empty($io['address1'])) $html .= $eh($io['address1']) . '<br>';
        if (!empty($io['address2'])) $html .= $eh($io['address2']) . '<br>';
        if (trim($io['postal'] . ' ' . $io['city']) !== '') $html .= $eh(trim($io['postal'] . ' ' . $io['city'])) . '<br>';
        if (!empty($io['country'])) $html .= $eh($io['country']);
        $html .= '</p>';
        if ($io['contact_name'] || $io['contact_email'] || $io['contact_phone']) {
            $html .= '<div class="customer-contacts">';
            if ($io['contact_name'])  $html .= '<div><strong>Kontakt:</strong> ' . $eh($io['contact_name']) . '</div>';
            if ($io['contact_email']) $html .= '<div><strong>E-post:</strong> ' . $eh($io['contact_email']) . '</div>';
            if ($io['contact_phone']) $html .= '<div><strong>Telefon:</strong> ' . $eh($io['contact_phone']) . '</div>';
            $html .= '</div>';
        }
        $html .= '</div></div>';

        if (trim((string)$io['description']) !== '') {
            $html .= '<div class="dw-section"><h3 class="dw-h3">Beskrivning</h3><p>' . nl2br($eh($io['description'])) . '</p></div>';
        }

        // ----- rader -----
        $html .= '<div class="dw-section"><h3 class="dw-h3">Rader</h3>';
        $totQty = 0; $totInv = 0; $totAmt = array();
        if (!$lines) {
            $html .= '<p>Inga rader hittades.</p>';
        } else {
            $html .= '<table class="dw-table dw-table-compact dw-table-orderlines"><thead><tr>'
                   . '<th>Artnr</th><th>Benämning</th><th class="text-center">Antal</th><th class="text-center">Fakt</th>'
                   . '<th class="text-right">Pris</th><th class="text-right">Belopp</th></tr></thead>';
            foreach ($lines as $ln) {
                $q = (float)$ln['movementqty']; $inv = (float)$ln['qty_invoiced'];
                $totQty += $q; $totInv += $inv;
                $isInv = ($q > 0 && $inv + 0.0001 >= $q);
                $article = trim((string)$ln['product_value']);
                $pid = (int)$ln['m_product_id'];
                $name = $pid > 0 ? trim(($ln['manufacturer_name'] ? $ln['manufacturer_name'] . ' ' : '') . $ln['product_name']) : (string)$ln['charge_name'];
                $nameHtml = $eh($name);
                if ($pid > 0 && $article !== '') {
                    $purl = '/search_dispatch.php?mode=product&q=' . rawurlencode($article) . '&open=product&id=' . $pid;
                    $nameHtml = '<a href="' . $eh($purl) . '" target="_blank" rel="noopener">' . $nameHtml . '</a>';
                }
                if ($ln['istradein'] === 'Y') $nameHtml .= '<span class="badge-tradein">inbyte</span>';
                $price = $ln['priceactual'] !== null && $ln['priceactual'] !== '' ? (float)$ln['priceactual'] : null;
                $cur = $ln['currency'] ?: '';
                if ($price !== null) { $totAmt[$cur] = (isset($totAmt[$cur]) ? $totAmt[$cur] : 0) + $price * $q; }

                $html .= '<tr class="ol-main' . ($isInv ? ' is-invoiced' : '') . '">';
                $html .= '<td>' . ($article !== '' ? '<span class="copy-chip" data-copy="' . $eh($article) . '">' . $eh($article) . '</span>' : '') . '</td>';
                $html .= '<td>' . $nameHtml . '</td>';
                $html .= '<td class="text-center">' . $qty($q) . '</td><td class="text-center">' . $qty($inv) . '</td>';
                $html .= '<td class="text-right">' . ($price !== null ? $amt($price) . ($cur && $cur !== 'SEK' ? ' ' . $eh($cur) : '') : '') . '</td>';
                $html .= '<td class="text-right">' . ($price !== null ? $amt($price * $q) . ($cur && $cur !== 'SEK' ? ' ' . $eh($cur) : '') : '') . '</td></tr>';

                $parts = array();
                if (!empty($ln['order_no'])) {
                    $parts[] = $eh($T['order']) . ' <a href="' . $eh($orderUrl($ln['order_no'])) . '" target="_blank" rel="noopener">' . $eh($ln['order_no']) . '</a> rad ' . (int)$ln['order_line']
                             . ': beställt ' . $qty($ln['qtyordered']) . ', levererat totalt ' . $qty($ln['qtydelivered']);
                }
                if (!empty($ln['locator'])) $parts[] = 'Lagerplats: ' . $eh($ln['locator']);
                if (!empty($ln['invoice_nos'])) $parts[] = 'Faktura: ' . $eh($ln['invoice_nos']);
                if (trim((string)$ln['note']) !== '') $parts[] = 'Notering: ' . $eh($ln['note']);
                $html .= '<tr class="ol-sub' . ($isInv ? ' is-invoiced' : '') . '"><td colspan="6">' . implode(' &middot; ', $parts) . '</td></tr>';
                $html .= '<tr class="ol-sep"><td colspan="6"></td></tr>';
            }
            $html .= '</table>';
        }
        $html .= '</div>';

        // ----- summering -----
        $html .= '<div class="ord-summary"><div class="ord-summary-top">';
        $html .= '<span class="ost-item"><span class="ost-lbl">Rader:</span> <strong class="ost-val">' . count($lines) . '</strong></span><span class="ost-sep">&middot;</span>';
        $html .= '<span class="ost-item"><span class="ost-lbl">Antal:</span> <strong class="ost-val">' . $qty($totQty) . '</strong></span><span class="ost-sep">&middot;</span>';
        $html .= '<span class="ost-item"><span class="ost-lbl">Fakturerat:</span> <strong class="ost-val">' . $qty($totInv) . '</strong></span>';
        foreach ($totAmt as $cur => $v) {
            $html .= '<span class="ost-sep">&middot;</span><span class="ost-item"><span class="ost-lbl">Värde exkl. moms' . ($isPurchase ? ' enligt inköpsorder' : '') . ':</span> <strong class="ost-val">' . $amt($v) . ' ' . $eh($cur ?: 'kr') . '</strong></span>';
        }
        $html .= '</div></div>';

        // ----- interna kommentarer -----
        if ($chat) {
            $html .= '<div class="dw-section"><div class="order-chat-header">Interna kommentarer</div>';
            foreach ($chat as $c) {
                $txt = nl2br(preg_replace('/(https:\/\/[^\s<>"]+)/', '<a href="$1" target="_blank" rel="noopener">$1</a>', $eh($c['characterdata'])));
                $html .= '<div class="order-chat-entry"><div class="order-chat-meta">' . $eh(substr($c['created'], 0, 19)) . ' &nbsp;&nbsp; <i>' . $eh($c['name']) . '</i></div><div class="order-chat-body">' . $txt . '</div></div>';
            }
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }
}
