<?php

/*

PHP object
author		Stefan Sjöberg
version		1.0 2025-04-10

*/

include_once 'Db.php';
include_once 'CCheckIpNumber.php';

Class CCrontab {

	function __construct() {
		
	}

	function goodsExpectationValue() {
		global $dagensdatum;

		if ($dagensdatum == "") {
			$dagensdatum = date("Y-m-d", time());
		}


		$select = "SELECT SUM((col.qtyordered - col.qtydelivered) * price.pricelimit) AS totsumma ";
		$select .= "FROM c_orderline col ";
		$select .= "JOIN c_bpartner bp ON col.c_bpartner_id = bp.c_bpartner_id ";
		$select .= "JOIN c_order o ON col.c_order_id = o.c_order_id ";
		$select .= "JOIN m_product p ON col.m_product_id = p.m_product_id ";
		$select .= "JOIN Ad_User au ON au.Ad_User_id = bp.salesrep_id ";
		$select .= "JOIN m_productprice price ON price.m_product_id = p.m_product_id ";
		$select .= "WHERE o.c_doctype_id = 1000016 AND NOT o.docstatus IN ('VO') AND col.qtyordered > col.qtydelivered ";
		$select .= "AND price.m_pricelist_version_id=1000000 ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}

		$res = (Db::getConnectionAD()) ? @pg_query(Db::getConnectionAD(), $select) : false;

		if ($res && pg_num_rows($res) > 0) {
			
			$row = $res ? pg_fetch_object($res) : null;
			
			$justnu = (round($row->totsumma,0));
			
			$this->goodsAddValue($justnu);

		}
				
	}
	
	function goodsAddValue($value) {

		$updt  = "INSERT INTO cyberadmin.goods_expectations ";
		$updt .= "(goodsValue) ";
		$updt .= "VALUES ";
		$updt .= "('$value') ";

		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $updt;
			exit;
		}
		
		$res = mysqli_query(Db::getConnection(true), $updt);

	}
	
}
?>
