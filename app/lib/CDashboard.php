<?php

/*

PHP object
author		Stefan Sjöberg
version		1.0 2025-04-10

*/

include_once 'Db.php';
include_once 'CCheckIpNumber.php';

Class CDashboard {

	function __construct() {
		
	}

	function getStoreValueMysql() {

		$select = "SELECT stockvalue_Value, stockvalue_Date ";
		$select .= "FROM cyberadmin.stockvalue ";
		$select .= "WHERE stockvalue_Type = 2 "; // löpande
		$select .= "ORDER BY stockvalue_Date DESC ";
		$select .= "LIMIT 1 ";

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					return $stockvalue_Value;
					
				endwhile;
				
			}

	}

	function getIncommaingValueMysql() {

		$select = "SELECT goodsValue ";
		$select .= "FROM cyberadmin.goods_expectations ";
		$select .= "ORDER BY goodsID DESC ";
		$select .= "LIMIT 1 ";

		$res = mysqli_query(Db::getConnection(), $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
					extract($row);
					
					return $goodsValue;
					
				endwhile;
				
			}

	}
	
}
?>
