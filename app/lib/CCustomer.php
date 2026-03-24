<?php
/*
 * Created on 2006-jan-27
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include("CConnect_ms.php");
 
 Class CCustomer {
 	
 	var $namn, $co, $adress, $postnr, $postadr;  	
 	var $lnamn, $lco, $ladress, $lpostnr, $lpostadr;
 	var $res;
 	
 	function CCustomer() {
				 			
 	}
 	function initialize() {
		 		
 	}
 	function storeResult() {
 		$this->res = mssql_query("SELECT namn FROM Ordertabell WHERE ordernr = 10000");
 		 		
 	}
 	
 }
 
 Class CNewOrder {
 	function newOrder() {
 		
 	}
 }
 
 
?>
