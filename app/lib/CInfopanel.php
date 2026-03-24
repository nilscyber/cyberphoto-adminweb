<?php
Class CInfopanel extends CTurnOver
{
	var $notPrintedByCountry;
	var $printedByCountry;
	function GetNotPrintedByCountry($country)
	{
		$select = "SELECT ic.m_pricelist_id, count(*) AS antal ";
		$select .= "FROM M_InOut_Candidate_v ic ";
		$select .= "WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select .= "GROUP BY ic.m_pricelist_id ";
		$select .= "ORDER BY ic.m_pricelist_id ASC ";
		
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$country_id = 1;
		$no_flag =0;
		while ($res && $row = pg_fetch_object($res)) {
			
			if ($country_id == 1) {
				if ($row->m_pricelist_id == 1000280) {
					$no_flag = $row->antal;
				} elseif ($row->m_pricelist_id == 1000018) {
					$fi_flag = $row->antal;
				} else {
					$sv_flag = $row->antal;
				}
			}
			if ($country_id == 2) {
				if ($row->m_pricelist_id == 1000280) {
					if (!$fi_flag) {
						$no_flag = $row->antal;
					} else {
						$no_flag = $row->antal;
					}
				} else {
					$fi_flag = $row->antal;
				}
			}
			if ($country_id == 3) {
				$no_flag = $row->antal;
			}
			$country_id++;
		}
		$country = 'no';
		#if($country == 'no')
			return $no_flag;
	}
	function setNotPrintedByCountry()
	{
		$select = "SELECT ic.m_pricelist_id, count(*) AS antal ";
		$select .= "FROM M_InOut_Candidate_v ic ";
		$select .= "WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 ";
		$select .= "GROUP BY ic.m_pricelist_id ";
		$select .= "ORDER BY ic.m_pricelist_id ASC ";
		
		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$country_id = 1;
		$no_flag =0;
		$this->notPrintedByCountry = array('se' => 0, 'fi' => 0, 'no' => 0);
		while ($res && $row = pg_fetch_object($res)) {
			if ($row->m_pricelist_id == 1000000) {
				$this->notPrintedByCountry['se'] = $row->antal;
			} else if ($row->m_pricelist_id == 10000018) {
				$this->notPrintedByCountry['fi'] = $row->antal;
			} else if ($row->m_pricelist_id == 1000280) {
				$this->notPrintedByCountry['no'] = $row->antal;
			} else {
				// if not Norway or Finland, add to Sweden as standard
				$this->notPrintedByCountry['se'] += $row->antal;
			} 
		}
	}	
	function setPrintedByCountry()
	{

		$select = "SELECT COUNT(io.*) AS antal, o.m_pricelist_id  FROM m_inout io, c_order o WHERE  ";
		$select .= "io.c_order_id = o.c_order_id AND ";		
		$select .= "io.docstatus IN ('IP', 'IN') AND io.deliveryViaRule IN ('S')  ";
	    $select .= "AND io.isSOTrx = 'Y' AND io.isInDispute!='Y' and io.isActive='Y' AND io.m_rma_id IS NULL ";
	    $select .= "GROUP BY o.m_pricelist_id ";

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;
		$country_id = 1;
		$no_flag =0;
		$this->printedByCountry = array('se' => 0, 'fi' => 0, 'no' => 0);
		while ($res && $row = pg_fetch_object($res)) {
			if ($row->m_pricelist_id == 1000000) {
				$this->printedByCountry['se'] = $row->antal;
			} else if ($row->m_pricelist_id == 10000018) {
				$this->printedByCountry['fi'] = $row->antal;
			} else if ($row->m_pricelist_id == 1000280) {
				$this->printedByCountry['no'] = $row->antal;
			} else {
				// if not Norway or Finland, add to Sweden as standard
				$this->printedByCountry['se'] += $row->antal;
			} 
		}
	}	

	function reminder($arr) # Påminnelser nere i högra hörnet, ex: "skriv ut northbike"
	{
		foreach($arr as $rows)
		{
			if(date('H:i') > $rows[1] && date('H:i') < $rows[2])
			{
				if(!empty($rows[3]) && strpos($rows[3], date('N')) || $rows[3] == date('N'))
					$ret .= $rows[0];
				elseif(empty($rows[3]))
					$ret .= $rows[0];
			}
		}
		return $ret;
	}
	
	function getOrdersFromADNB() { # Northbike - Utskrivna
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL ";
		return self::countOrders($select, Db::getConnectionNB());
	}
	function getNotPrintedOrdersFromADNBNew() { # Northbike - Ej Utskrivna
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000002 ";
		return self::countOrders($select, Db::getConnectionNB());
	}
	
	/*
	 * $infopanel->printedByCountry['se'] och $infopanel->notPrintedByCountry['se'] används istället
	 */
	function getOrdersFromADNew() { # Cyberphoto - Utskrivna
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL AND NOT M_FreightCategory_ID IN (1000012,1000023,1000063) ";
		return self::countOrders($select, Db::getConnectionAD());
	}
	function getNotPrintedOrdersFromADNew() { #Cyberphoto - Ej Utskrivna
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 AND NOT ic.M_FreightCategory_ID IN (1000012,1000023,1000063) ";
		return self::countOrders($select, Db::getConnectionAD());
	}
	
	function getOrdersFromADExpress() {
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL AND M_FreightCategory_ID = 1000012  ";
		return self::countOrders($select, Db::getConnectionAD());
	}
	function getOrdersFromADDriveOut() {
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL AND M_FreightCategory_ID = 1000023  ";
		return self::countOrders($select, Db::getConnectionAD());
	}
	function getOrdersFromADInstabox() {
		$select = "SELECT COUNT(*) AS antal FROM m_inout WHERE docstatus IN ('IP', 'IN') AND deliveryViaRule IN ('S')  AND isSOTrx = 'Y' AND isInDispute!='Y' and isActive='Y' AND m_rma_id IS NULL AND M_FreightCategory_ID = 1000063  ";
		return self::countOrders($select, Db::getConnectionAD());
	}
	function getNotPrintedExpress() {
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.M_FreightCategory_ID = 1000012  AND ic.AD_Client_ID=1000000 ";
		return self::countOrders($select, Db::getConnectionAD());
	}	
	function getNotPrintedDriveOut() {
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 AND ic.M_FreightCategory_ID = 1000023 ";
		return self::countOrders($select, Db::getConnectionAD());
	}
	function getNotPrintedInstabox() {
		$select = "SELECT count(*) AS antal FROM M_InOut_Candidate_v ic WHERE ic.deliveryviarule='S' AND ic.AD_Client_ID=1000000 AND ic.M_FreightCategory_ID = 1000063 ";
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
			// echo $select;
		}
		return self::countOrders($select, Db::getConnectionAD());
	}
	
	function countOrders($sql, $db)
	{
		#$db = ($fdb == 'NB') ? Db::getConnectionNB() : Db::getConnectionAD();
		$res = ($db) ? @pg_query($db, $sql) : false;
		$row = $res ? pg_fetch_object($res) : null;
		
		$ret = ($res && pg_num_rows($res) > 0) ? $row->antal : 0;
		return $ret;
	}

public function renderIncomingGoodsSummary($date = '', $mode = 'auto', $maxRows = 8)
{
    // $mode: 'today' | 'tomorrow' | 'auto'
    // auto => efter 18:00 visa imorgon, annars idag
    if ($date === '' || $date === null) {
        $date = date('Y-m-d');
    }

    if ($mode === 'auto') {
        $mode = ((int)date('H') >= 18) ? 'tomorrow' : 'today';
    }

    if ($mode === 'tomorrow') {
        $date = date('Y-m-d', strtotime($date . ' +1 day'));
    }

    $lookForward  = date('Y-m-d', strtotime($date . ' +3 day'));
    $lookBackward = date('Y-m-d', strtotime($date . ' -3 day'));

    $maxRows = (int)$maxRows;
    if ($maxRows < 3) {
        $maxRows = 3;
    } elseif ($maxRows > 20) {
        $maxRows = 20;
    }

    // Vi kör “logistikskärm”: fokusera på Leverantör + Antal (som din screenshot).
    // Vill du senare visa vikt/inköpare igen kan vi bygga en “expanded view”.
    $sql  = "SELECT bp.name, SUM(col.qtyordered - col.qtydelivered) AS totantal ";
    $sql .= "FROM c_orderline col ";
    $sql .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
    $sql .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
    $sql .= "WHERE o.c_doctype_id = 1000016 ";
    $sql .= "AND NOT o.docstatus IN ('VO') ";
    $sql .= "AND col.qtyordered > col.qtydelivered ";
    // Samma logik som “not_only_today=yes” i din gamla: +-3 dagar + precisionfilter
    $sql .= "AND ( ";
    $sql .= "(col.datepromised = $1) ";
    $sql .= "OR (col.datepromised > $2 AND col.datepromised < $1) ";
    $sql .= "OR (col.datepromised < $3 AND col.datepromised > $1 AND NOT col.datepromisedprecision = 'D') ";
    $sql .= ") ";
    $sql .= "AND NOT col.datepromisedprecision = 'U' ";
    $sql .= "GROUP BY bp.name ";
    $sql .= "ORDER BY totantal DESC, bp.name ASC ";

    $db = Db::getConnectionAD(false);
    $res = ($db) ? @pg_query_params($db, $sql, array($date, $lookBackward, $lookForward)) : false;

    if (!$res) {
        // Fail soft: panelen ska inte krascha
        return '<div class="empty">Kunde inte hämta ankommande gods.</div>';
    }

    $rows = array();
    $total = 0;

    while ($res && $r = pg_fetch_row($res)) {
        $name  = (string)$r[0];
        $antal = (int)round($r[1], 0);

        $rows[] = array($name, $antal);
        $total += $antal;
    }

    if (count($rows) === 0) {
        return '<div class="empty">Inget ankommande gods att visa.</div>';
    }

    // Begränsa rader för TV-läsbarhet
    $displayRows = array_slice($rows, 0, $maxRows);

    // HTML (matchar din “Ankommande gods”-stil: Leverantör + Antal + total)
    $html  = '<table class="ig-table">';
    $html .= '<thead><tr><th>Leverantör</th><th class="num">Antal</th></tr></thead>';
    $html .= '<tbody>';

    $i = 0;
    foreach ($displayRows as $row) {
        $i++;
        $name  = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
        $antal = (int)$row[1];

        $html .= '<tr>';
        $html .= '<td class="name">' . $name . '</td>';
        $html .= '<td class="num">' . $antal . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '<tfoot><tr><td class="sumlabel">Totalt</td><td class="num sum">' . (int)$total . ' st</td></tr></tfoot>';
    $html .= '</table>';

    // Om det finns fler rader än vi visar: liten hint (utan att bli plottrig)
    if (count($rows) > $maxRows) {
        $html .= '<div class="ig-more">+' . (count($rows) - $maxRows) . ' fler leverantörer</div>';
    }

    return $html;
}

public function getIncomingGoodsTodayData($date = '', $limit = 10)
{
    if ($date == '') {
        $date = date('Y-m-d');
    }

    $limit = (int)$limit;
    if ($limit <= 0) {
        $limit = 10;
    }

    $sql  = "SELECT bp.name, SUM(col.qtyordered - col.qtydelivered) AS totantal ";
    $sql .= "FROM c_orderline col ";
    $sql .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
    $sql .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
    $sql .= "WHERE o.c_doctype_id = 1000016 ";
    $sql .= "AND NOT o.docstatus IN ('VO') ";
    $sql .= "AND col.qtyordered > col.qtydelivered ";
    $sql .= "AND col.datepromised = '" . pg_escape_string($date) . "' ";
    $sql .= "AND col.datepromisedprecision = 'D' ";
    $sql .= "AND NOT col.datepromisedprecision = 'U' ";
    $sql .= "AND bp.isvendor = 'Y' ";
    $sql .= "GROUP BY bp.name ";
    $sql .= "ORDER BY totantal DESC, bp.name ASC ";
    $sql .= "LIMIT " . $limit;

    $res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $sql) : false;

    $rows = array();
    $total = 0;

    if ($res && pg_num_rows($res) > 0) {
        while ($res && $r = pg_fetch_row($res)) {
            $name  = (string)$r[0];
            $antal = (int)round($r[1], 0);

            $rows[] = array(
                'name'  => $name,
                'antal' => $antal,
            );

            $total += $antal;
        }
    }

    return array(
        'date'  => $date,
        'rows'  => $rows,
        'total' => $total,
    );
}

public function renderIncomingGoodsTodaySummary($date = '', $limit = 10)
{
    $data = $this->getIncomingGoodsTodayData($date, $limit);

    if (empty($data['rows'])) {
        return '<div style="color:#374151;font-weight:900;font-size:16px;padding:10px 2px;">Inget ankommande gods att visa.</div>';
    }

    $out = '';
    $out .= '<table class="ig-table">';
    $out .= '<thead><tr>';
    $out .= '<th>Leverantör</th>';
    $out .= '<th class="num">Antal</th>';
    $out .= '</tr></thead>';
    $out .= '<tbody>';

    foreach ($data['rows'] as $row) {
        $out .= '<tr>';
        $out .= '<td>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</td>';
        $out .= '<td class="num">' . (int)$row['antal'] . '</td>';
        $out .= '</tr>';
    }

    $out .= '</tbody>';
    $out .= '<tfoot><tr>';
    $out .= '<td>Total</td>';
    $out .= '<td class="num">' . (int)$data['total'] . ' st</td>';
    $out .= '</tr></tfoot>';
    $out .= '</table>';

    return $out;
}


}
