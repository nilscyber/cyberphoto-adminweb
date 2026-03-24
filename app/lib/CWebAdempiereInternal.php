<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2011-03-15

*/

include("connections.php");
// include("connection_ad.php");

Class CWebADIntern {
	var $conn_my;
	var $conn_ad;

function __construct() {
	global $conn_ad;
	
	$this->conn_my = Db::getConnection();
        
	$this->conn_ad = Db::getConnectionAD();

}

function checkOnQueue($artnr) {
	
	$select = "SELECT m_storage.qtyreserved AS queue ";
	$select .= "FROM m_product ";
	$select .= "JOIN m_storage ON m_storage.m_product_id = m_product.m_product_id ";
	$select .= "WHERE m_product.value = '" . $artnr . "' ";
	// echo $select;

	$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
	$row = $res ? pg_fetch_object($res) : null;

		if ($res && pg_num_rows($res) > 0) {
			
			echo $row->queue;
			
		} else {
		
			echo "0&nbsp;";
		
		}
	
}

}
?>
