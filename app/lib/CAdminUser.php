<?php
require_once("CCheckIpNumber.php");
require_once("Db.php");

Class CAdminUser {


// =========================================================
//  Snooza offert (markera som hanterad X dagar framåt)
//  Lagrar i MariaDB: cyberadmin.user_quotation_snooze
// =========================================================
public static function snoozeQuotation($adUserId, $orderId, $days = 5)
{
    $adUserId = (int)$adUserId;
    $orderId  = (int)$orderId;
    $days     = (int)$days;

    if ($adUserId <= 0 || $orderId <= 0) {
        return false;
    }

    // MariaDB  skriv (resource, ext/mysql)
    $db = Db::getConnection(true);

    // Vi använder bara heltal, så direkt interpolation är okej
    $sql = "
        INSERT INTO cyberadmin.user_quotation_snooze (ad_user_id, c_order_id, snooze_until)
        VALUES ($adUserId, $orderId, DATE_ADD(CURDATE(), INTERVAL $days DAY))
        ON DUPLICATE KEY UPDATE
            snooze_until = VALUES(snooze_until)
    ";

    $result = mysqli_query($db, $sql);

    if ($result === false) {
        error_log('snoozeQuotation: ' . mysqli_error($db));
        return false;
    }

	$logSql = "
		INSERT INTO cyberadmin.user_quotation_snooze_log
			(ad_user_id, c_order_id, snoozed_at, snooze_days, snooze_until)
		VALUES
			($adUserId, $orderId, NOW(), $days, DATE_ADD(CURDATE(), INTERVAL $days DAY))
	";
	@mysqli_query($db, $logSql);

    return true;
}

private static function getSnoozeHistoryMapForOrders($adUserId, $orderIds)
{
    $adUserId = (int)$adUserId;
    if ($adUserId <= 0 || empty($orderIds)) {
        return array();
    }

    $db = Db::getConnection(false);

    // Rensa till int och bygg IN-lista
    $clean = array();
    foreach ($orderIds as $oid) {
        $oid = (int)$oid;
        if ($oid > 0) $clean[] = $oid;
    }
    if (empty($clean)) return array();

    $in = implode(',', $clean);

    $sql = "
        SELECT c_order_id,
               COUNT(*) AS snooze_count,
               MAX(snoozed_at) AS last_snoozed_at
        FROM cyberadmin.user_quotation_snooze_log
        WHERE ad_user_id = $adUserId
          AND c_order_id IN ($in)
        GROUP BY c_order_id
    ";

    $res = mysqli_query($db, $sql);
    if ($res === false) {
        error_log('getSnoozeHistoryMapForOrders: ' . mysqli_error($db));
        return array();
    }

    $map = array();
    while ($row = mysqli_fetch_assoc($res)) {
        $oid = (int)$row['c_order_id'];
        $map[$oid] = array(
            'count' => (int)$row['snooze_count'],
            'last'  => $row['last_snoozed_at']
        );
    }

    return $map;
}


// =========================================================
//  Hämta snoozade offert-ID:n för användaren (MariaDB)
//  Returnerar assoc-array: [c_order_id] => true
// =========================================================
private static function getUserSnoozedQuotationIds($adUserId)
{
    $adUserId = (int)$adUserId;
    if ($adUserId <= 0) {
        return array();
    }

    // MariaDB  läs (resource, ext/mysql)
    $db = Db::getConnection(false);

    $sql = "
        SELECT c_order_id
        FROM cyberadmin.user_quotation_snooze
        WHERE ad_user_id = $adUserId
          AND snooze_until >= CURDATE()
    ";

    $result = mysqli_query($db, $sql);

    if ($result === false) {
        error_log('getUserSnoozedQuotationIds: ' . mysqli_error($db));
        return array();
    }

    $ids = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $ids[(int)$row['c_order_id']] = true;
    }

    return $ids;
}


// =========================================================
//  Hämta användarens offerter från ADempiere (PostgreSQL)
//  - Offert = c_doctypetarget_id = 1000027
//  - Gäller 10 dagar från dateordered
//  - Tar med även utgångna (days_left kan vara negativt)
//  - Filtrerar bort annullerade (docstatus VO/RE)
//  - Filtrerar bort snoozade rader via MariaDB-hjälptabell
// =========================================================
private static function getUserQuotations($adUserId, $limit = 50)
{
    $dbPg     = Db::getConnectionAD(false); // PostgreSQL resource
    $adUserId = (int)$adUserId;
    $limit    = (int)$limit;

	$sql = "
		SELECT
			o.c_order_id,
			o.documentno,
			o.dateordered::date AS dateordered,
			COALESCE(o.validto::date, (o.dateordered::date + INTERVAL '10 day')::date) AS valid_to,
			(COALESCE(o.validto::date, (o.dateordered::date + INTERVAL '10 day')::date) - CURRENT_DATE) AS days_left,
			bp.name AS bp_name,
			o.totallines,
			(o.validto IS NOT NULL) AS has_manual_validto
		FROM c_order o
		JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id
		WHERE 
			o.salesrep_id = $1
			AND o.c_doctypetarget_id = 1000027
			AND o.isactive = 'Y'
			AND o.docstatus NOT IN ('VO','RE')
		ORDER BY
			COALESCE(o.validto::date, (o.dateordered::date + INTERVAL '10 day')::date) ASC,
			o.dateordered DESC,
			o.c_order_id DESC
		LIMIT $2
	";

    $result = ($dbPg) ? @pg_query_params($dbPg, $sql, array($adUserId, $limit)) : false;
    if ($result === false) {
        error_log('getUserQuotations: ' . pg_last_error($dbPg));
        return array();
    }

    $rows = array();
    while ($result && $row = pg_fetch_assoc($result)) {
        $rows[] = $row;
    }

    // ---- Filtrera bort snoozade offerter (MariaDB) ----
    $snoozedIds = self::getUserSnoozedQuotationIds($adUserId);

    if (!empty($snoozedIds)) {
        $filtered = array();
        foreach ($rows as $r) {
            $oid = isset($r['c_order_id']) ? (int)$r['c_order_id'] : 0;
            if ($oid > 0 && empty($snoozedIds[$oid])) {
                $filtered[] = $r;
            }
        }
        $rows = $filtered;
    }

    return $rows;
}


// =========================================================
//  NYA FUNKTIONEN: Hämta pågående ordrar för en säljare
//  - Standard orders (c_doctypetarget_id = 1000030)
//  - Ej annullerade (docstatus != 'VO', 'RE')
//  - Ej helt levererade/stängda (docstatus != 'CL')
//  - Ej markerade som levererade (isdelivered != 'Y')
// =========================================================
private static function getUserActiveOrders($adUserId, $limit = 50)
{
    $dbPg     = Db::getConnectionAD(false); // PostgreSQL resource
    $adUserId = (int)$adUserId;
    $limit    = (int)$limit;

    $sql = "
        SELECT
            o.c_order_id,
            o.documentno,
            o.dateordered::date AS dateordered,
            bp.name AS bp_name,
            o.totallines,
            o.docstatus,
            o.ispartdelivered
        FROM c_order o
        JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id
        WHERE 
            o.salesrep_id = $1
            AND o.c_doctypetarget_id = 1000030
            AND o.isactive = 'Y'
            AND o.docstatus NOT IN ('VO','RE','CL')
            AND COALESCE(o.isdelivered, 'N') = 'N'
        ORDER BY o.dateordered DESC
        LIMIT $2
    ";

    $result = ($dbPg) ? @pg_query_params($dbPg, $sql, array($adUserId, $limit)) : false;
    if ($result === false) {
        error_log('getUserActiveOrders: ' . pg_last_error($dbPg));
        return array();
    }

    $rows = array();
    while ($result && $row = pg_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}


// =========================================================
//  NYA FUNKTIONEN: Render HTML för pågående ordrar
//  - Returnerar tom sträng om inget att visa
//  - Enkel tabell: Orderdatum, Kund, Ordernummer, Belopp
//  - Status: Dellevererad (orange) eller Behandlas (grön)
// =========================================================
public static function renderUserActiveOrders($adUserId, $limit = 50)
{
    $adUserId = (int)$adUserId;
    if ($adUserId <= 0) {
        return '';
    }

    $orders = self::getUserActiveOrders($adUserId, $limit);

    if (empty($orders)) {
        return '';
    }

    ob_start();
    ?>
    <style>
        .order-status-partial  { font-weight:bold; color:#c05621; }
        .order-status-active   { font-weight:bold; color:#15803d; }
    </style>

    <table class="table-list with-drawer-gutter">
        <thead>
            <tr>
                <th style="width:120px;">Orderdatum</th>
                <th>Kund</th>
                <th style="width:120px;">Ordernr</th>
                <th style="width:120px;">Status</th>
                <th style="width:120px;" class="text-right">Belopp</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <?php
                // --- Status baserat på ispartdelivered ---
                $isPartDelivered = isset($o['ispartdelivered']) ? $o['ispartdelivered'] : 'N';
                
                if ($isPartDelivered === 'Y') {
                    $statusLabel = 'Dellevererad';
                    $statusClass = 'order-status-partial';
                } else {
                    $statusLabel = 'Behandlas';
                    $statusClass = 'order-status-active';
                }

                // Teckenkodning kundnamn
                $bpName = $o['bp_name'];

                // Orderlänk
                $docNo    = $o['documentno'];
                $orderUrl = 'search_dispatch.php?mode=order&page=1&q=' . urlencode($docNo);

                // Belopp
                $totalRaw  = isset($o['totallines']) ? (float)$o['totallines'] : 0.0;
                $totalDisp = number_format($totalRaw, 0, ',', ' ') . ' kr';
            ?>
            <tr>
                <td><?php echo htmlspecialchars($o['dateordered']); ?></td>
                <td><?php echo htmlspecialchars($bpName); ?></td>
                <td>
                    <a href="<?php echo htmlspecialchars($orderUrl); ?>" target="_blank">
                        <?php echo htmlspecialchars($docNo); ?>
                    </a>
                </td>
                <td class="<?php echo $statusClass; ?>">
                    <?php echo htmlspecialchars($statusLabel); ?>
                </td>
                <td class="text-right">
                    <?php echo htmlspecialchars($totalDisp); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php

    return ob_get_clean();
}


// =========================================================
//  Render: HTML-tabell för användarens offerter
//  - Returnerar tom sträng om inget att visa
//  - Färgkodade dagar kvar
//  - Länk till ordervisning
//  - Belopp (totallines)
//  - Åtgärdsknapp (check-ikon) för snooze 5 dagar
// =========================================================
public static function renderUserQuotations($adUserId, $limit = 50)
{
    $adUserId = (int)$adUserId;
    if ($adUserId <= 0) {
        return '';
    }

    $quotes = self::getUserQuotations($adUserId, $limit);

    // Inget att visa ? ingen widget
    if (empty($quotes)) {
        return '';
    }

	$orderIds = array();
	foreach ($quotes as $q) {
		$orderIds[] = (int)$q['c_order_id'];
	}
	$histMap = self::getSnoozeHistoryMapForOrders($adUserId, $orderIds);

    ob_start();
    ?>
    <style>
        .quote-days-green  { font-weight:bold; color:#15803d; }
        .quote-days-orange { font-weight:bold; color:#c05621; }
        .quote-days-red    { font-weight:bold; color:#b91c1c; }

        .quote-action-btn {
            display:inline-block;
            padding:2px 6px;
            border-radius:4px;
            border:1px solid #d1d5db;
            background:#f9fafb;
            text-decoration:none;
            line-height:1;
        }
        .quote-action-btn:hover {
            background:#e5e7eb;
        }
        .quote-action-icon {
            font-size:14px;
        }
    </style>

    <table class="table-list with-drawer-gutter">
        <thead>
            <tr>
                <th style="width:120px;">Datum</th>
                <th>Kund</th>
                <th style="width:140px;">Gäller till</th>
                <th style="width:110px;">Dagar kvar</th>
                <th style="width:120px;">Offertnr</th>
                <th style="width:120px;" class="text-right">Belopp</th>
				<th style="width:60px;" class="text-center">Hist.</th>
                <th style="width:60px;" class="text-center">Åtg.</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($quotes as $q): ?>
            <?php
                // --- Dagar kvar + färglogik ---
                $daysLeft  = isset($q['days_left']) ? (int)$q['days_left'] : null;
                $labelDays = '-';
                $daysClass = '';

                if ($daysLeft !== null) {
                    if ($daysLeft === 0) {
                        $labelDays = 'Idag';
                    } elseif ($daysLeft > 0) {
                        $labelDays = $daysLeft . ' dagar';
                    } else {
                        $labelDays = 'För ' . abs($daysLeft) . ' dagar sedan';
                    }

                    // =5 dagar = grön, 43 = orange, =2 eller passerade = röd
                    if ($daysLeft >= 5) {
                        $daysClass = 'quote-days-green';
                    } elseif ($daysLeft >= 3) {
                        $daysClass = 'quote-days-orange';
                    } else {
                        $daysClass = 'quote-days-red';
                    }
                }

                // Teckenkodning kundnamn
                $bpName = $q['bp_name'];

                // Orderlänk (ny flik)
                $docNo    = $q['documentno'];
                $orderUrl = 'search_dispatch.php?mode=order&page=1&q=' . urlencode($docNo);

                // Belopp (totallines)
                $totalRaw  = isset($q['totallines']) ? (float)$q['totallines'] : 0.0;
                $totalDisp = number_format($totalRaw, 0, ',', ' ') . ' kr';

                // Snooze-åtgärd (check-ikon)
                $actionUrl = 'profile_quote_action.php?action=snooze&order_id=' . (int)$q['c_order_id'];

				$oid = (int)$q['c_order_id'];
				$hist = isset($histMap[$oid]) ? $histMap[$oid] : null;

            ?>
            <tr>
                <td><?php echo htmlspecialchars($q['dateordered']); ?></td>
                <td><?php echo htmlspecialchars($bpName); ?></td>
                <td><?php echo htmlspecialchars($q['valid_to']); ?></td>
                <td class="<?php echo $daysClass; ?>">
                    <?php echo htmlspecialchars($labelDays); ?>
                </td>
                <td>
                    <a href="<?php echo htmlspecialchars($orderUrl); ?>" target="_blank">
                        <?php echo htmlspecialchars($docNo); ?>
                    </a>
                </td>
                <td class="text-right">
                    <?php echo htmlspecialchars($totalDisp); ?>
                </td>
				<td class="text-center">
				<?php if ($hist && $hist['count'] > 0): ?>
					<span class="quote-snoozed-icon"
						  title="Tidigare snoozad <?php echo (int)$hist['count']; ?> gång(er). Senast: <?php echo htmlspecialchars($hist['last']); ?>">
						&#8635;
					</span>
				<?php else: ?>
					&nbsp;
				<?php endif; ?>
				</td>
                <td class="text-center">
                    <a href="<?php echo htmlspecialchars($actionUrl); ?>"
                       class="quote-action-btn"
                       title="Markera denna som hanterad i 5 dagar">
                        <span class="quote-action-icon">&#10003;</span>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php

    return ob_get_clean();
}

// =========================================================
//  Behörighetskontroll: får användaren se global offert-översikt?
//  MariaDB: cyberadmin.profile_global_quote_access
// =========================================================
private static function hasGlobalQuoteAccess($viewerAdUserId)
{
    $viewerAdUserId = (int)$viewerAdUserId;
    if ($viewerAdUserId <= 0) {
        return false;
    }

    $db = Db::getConnection(false); // MariaDB read (resource)

    $sql = "
        SELECT ad_user_id
        FROM cyberadmin.profile_global_quote_access
        WHERE ad_user_id = $viewerAdUserId
        LIMIT 1
    ";

    $res = mysqli_query($db, $sql);
    if ($res === false) {
        error_log('hasGlobalQuoteAccess: ' . mysqli_error($db));
        return false;
    }

    return (mysqli_num_rows($res) > 0);
}


// =========================================================
//  Hämta alla snoozade offerter för alla användare (MariaDB)
//  Returnerar: $map[ad_user_id][c_order_id] = snooze_until (YYYY-MM-DD)
// =========================================================
private static function getAllSnoozedQuotationMap()
{
    $db = Db::getConnection(false); // MariaDB read (resource)

    $sql = "
        SELECT ad_user_id, c_order_id, snooze_until
        FROM cyberadmin.user_quotation_snooze
        WHERE snooze_until >= CURDATE()
    ";

    $res = mysqli_query($db, $sql);
    if ($res === false) {
        error_log('getAllSnoozedQuotationMap: ' . mysqli_error($db));
        return array();
    }

    $map = array();
    while ($row = mysqli_fetch_assoc($res)) {
        $uid = (int)$row['ad_user_id'];
        $oid = (int)$row['c_order_id'];
        $until = isset($row['snooze_until']) ? $row['snooze_until'] : '';

        if ($uid > 0 && $oid > 0) {
            if (!isset($map[$uid])) {
                $map[$uid] = array();
            }
            $map[$uid][$oid] = $until; // <-- datum istället för true
        }
    }

    return $map;
}


// =========================================================
//  Hämta ALLA offerter (PostgreSQL) med säljare + kund
//  Offert = c_doctypetarget_id = 1000027
//  Gäller 10 dagar från dateordered
//  Filtrerar bort docstatus VO/RE
// =========================================================
private static function getAllQuotationsForAllSalesreps($limitTotal = 500)
{
    $dbPg = Db::getConnectionAD(false); // PostgreSQL resource
    $limitTotal = (int)$limitTotal;

	$sql = "
		SELECT
			x.c_order_id,
			x.salesrep_id,
			x.salesrep_name,
			x.documentno,
			x.dateordered,
			x.valid_to,
			x.days_left,
			x.bp_name,
			x.totallines,
			x.has_manual_validto
		FROM (
			SELECT
				o.c_order_id,
				o.salesrep_id,
				u.name AS salesrep_name,
				o.documentno,
				o.dateordered::date AS dateordered,
				COALESCE(o.validto::date, (o.dateordered::date + INTERVAL '10 day')::date) AS valid_to,
				(COALESCE(o.validto::date, (o.dateordered::date + INTERVAL '10 day')::date) - CURRENT_DATE) AS days_left,
				bp.name AS bp_name,
				o.totallines,
				(o.validto IS NOT NULL) AS has_manual_validto,
				ROW_NUMBER() OVER (
					PARTITION BY o.salesrep_id
					ORDER BY
						COALESCE(o.validto::date, (o.dateordered::date + INTERVAL '10 day')::date) ASC,
						o.dateordered DESC,
						o.c_order_id DESC
				) AS rn
			FROM c_order o
			JOIN c_bpartner bp ON bp.c_bpartner_id = o.c_bpartner_id
			LEFT JOIN ad_user u ON u.ad_user_id = o.salesrep_id
			WHERE
				o.c_doctypetarget_id = 1000027
				AND o.isactive = 'Y'
				AND o.docstatus NOT IN ('VO','RE')
		) x
		WHERE x.rn <= $1
		ORDER BY
			x.salesrep_name NULLS LAST,
			x.valid_to ASC,
			x.dateordered DESC,
			x.c_order_id DESC
	";

    $res = ($dbPg) ? @pg_query_params($dbPg, $sql, array($limitTotal)) : false;
    if ($res === false) {
        error_log('getAllQuotationsForAllSalesreps: ' . pg_last_error($dbPg));
        return array();
    }

    $rows = array();
    while ($res && $row = pg_fetch_assoc($res)) {
        $rows[] = $row;
    }

    return $rows;
}


// =========================================================
//  Render: Global offert-översikt (grupperad per säljare)
//  - Syns bara om viewer har behörighet i MariaDB-tabellen
//  - Returnerar '' om inget att visa (så profile.php kan hålla sig clean)
//  - Respekterar snooze per säljare
//  - Begränsar per säljare (limitPerUser) och total (limitTotal)
// =========================================================
public static function renderGlobalQuotationsGrouped($viewerAdUserId, $limitPerUser = 15, $limitTotal = 500)
{
    $viewerAdUserId = (int)$viewerAdUserId;
    $limitPerUser   = (int)$limitPerUser;
    $limitTotal     = (int)$limitTotal;

    if ($viewerAdUserId <= 0) {
        return '';
    }

    // Behörighet
    if (!self::hasGlobalQuoteAccess($viewerAdUserId)) {
        return '';
    }

    // Hämta snooze-map (MariaDB) -> [ad_user_id][c_order_id] = snooze_until
    $snoozeMap = self::getAllSnoozedQuotationMap();

    // Hämta alla offerter (PostgreSQL)
    $rows = self::getAllQuotationsForAllSalesreps($limitTotal);
    if (empty($rows)) {
        return '';
    }

    // Gruppning per säljare med per-säljare-limit (OBS: vi filtrerar INTE bort snoozade)
    $groups = array();
    $groupMeta = array();
    $countPerUser = array();

    foreach ($rows as $r) {
        $sid = isset($r['salesrep_id']) ? (int)$r['salesrep_id'] : 0;
        if ($sid <= 0) {
            $sid = -1;
        }

        if (!isset($countPerUser[$sid])) {
            $countPerUser[$sid] = 0;
        }

        // Per-säljare-limit
        if ($limitPerUser > 0 && $countPerUser[$sid] >= $limitPerUser) {
            continue;
        }

        $countPerUser[$sid]++;

        if (!isset($groups[$sid])) {
            $groups[$sid] = array();
        }

        $groups[$sid][] = $r;

        if (!isset($groupMeta[$sid])) {
            $name = !empty($r['salesrep_name']) ? $r['salesrep_name'] : 'Okänd säljare';
            $groupMeta[$sid] = array(
                'name'  => $name,
                'count' => 0,
                'sum'   => 0.0
            );
        }

        $groupMeta[$sid]['count']++;

        $tot = isset($r['totallines']) ? (float)$r['totallines'] : 0.0;
        $groupMeta[$sid]['sum'] += $tot;
    }

    if (empty($groups)) {
        return '';
    }

    ob_start();
    ?>
    <style>
        .quote-days-green  { font-weight:bold; color:#15803d; }
        .quote-days-orange { font-weight:bold; color:#c05621; }
        .quote-days-red    { font-weight:bold; color:#b91c1c; }

        .global-group-title {
            margin: 14px 0 8px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .global-group-sub {
            font-weight: normal;
            color: #555;
        }

        .quote-snoozed-icon {
            display:inline-block;
            padding:2px 6px;
            border-radius:4px;
            border:1px solid #d1d5db;
            background:#f3f4f6;
            color:#6b7280;
            line-height:1;
            font-size:14px;
        }
    </style>

    <?php foreach ($groups as $sid => $list): ?>
        <?php
            $meta = isset($groupMeta[$sid]) ? $groupMeta[$sid] : array('name'=>'Okänd säljare','count'=>0,'sum'=>0);
            $sumDisp = number_format((float)$meta['sum'], 0, ',', ' ') . ' kr';
        ?>

        <div class="global-group-title">
            <?php echo htmlspecialchars($meta['name']); ?>
            <span class="global-group-sub">
                (<?php echo (int)$meta['count']; ?> st, <?php echo htmlspecialchars($sumDisp); ?>)
            </span>
        </div>

        <table class="table-list with-drawer-gutter">
            <thead>
                <tr>
                    <th style="width:120px;">Datum</th>
                    <th>Kund</th>
                    <th style="width:140px;">Gäller till</th>
                    <th style="width:110px;">Dagar kvar</th>
                    <th style="width:120px;">Offertnr</th>
                    <th style="width:120px;" class="text-right">Belopp</th>
                    <th style="width:60px;" class="text-center">Åtg.</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $q): ?>
                <?php
                    $daysLeft  = isset($q['days_left']) ? (int)$q['days_left'] : null;
                    $labelDays = '-';
                    $daysClass = '';

                    if ($daysLeft !== null) {
                        if ($daysLeft === 0) {
                            $labelDays = 'Idag';
                        } elseif ($daysLeft > 0) {
                            $labelDays = $daysLeft . ' dagar';
                        } else {
                            $labelDays = 'För ' . abs($daysLeft) . ' dagar sedan';
                        }

                        if ($daysLeft >= 5) {
                            $daysClass = 'quote-days-green';
                        } elseif ($daysLeft >= 3) {
                            $daysClass = 'quote-days-orange';
                        } else {
                            $daysClass = 'quote-days-red';
                        }
                    }

                    $bpName = $q['bp_name'];
                    $docNo  = $q['documentno'];
                    $orderUrl = 'search_dispatch.php?mode=order&page=1&q=' . urlencode($docNo);

                    $totalRaw  = isset($q['totallines']) ? (float)$q['totallines'] : 0.0;
                    $totalDisp = number_format($totalRaw, 0, ',', ' ') . ' kr';

                    // Snooze-status för just denna säljare + order
                    $oid = isset($q['c_order_id']) ? (int)$q['c_order_id'] : 0;
                    $snoozeUntil = '';
                    if ($oid > 0 && isset($snoozeMap[$sid]) && !empty($snoozeMap[$sid][$oid])) {
                        $snoozeUntil = $snoozeMap[$sid][$oid];
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($q['dateordered']); ?></td>
                    <td><?php echo htmlspecialchars($bpName); ?></td>
                    <td><?php echo htmlspecialchars($q['valid_to']); ?></td>
                    <td class="<?php echo $daysClass; ?>"><?php echo htmlspecialchars($labelDays); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($orderUrl); ?>" target="_blank">
                            <?php echo htmlspecialchars($docNo); ?>
                        </a>
                    </td>
                    <td class="text-right"><?php echo htmlspecialchars($totalDisp); ?></td>
                    <td class="text-center">
                        <?php if (!empty($snoozeUntil)): ?>
							<span class="quote-snoozed-icon" title="Snoozad till <?php echo htmlspecialchars($snoozeUntil); ?>">
								&#9208;
							</span>
                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php endforeach; ?>

    <?php
    return ob_get_clean();
}



}

?>
