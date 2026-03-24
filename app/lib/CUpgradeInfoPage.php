<?php

Class CUpgradeInfoPage {
	var $conn_my; 
	var $conn_master;

	function __construct() {
			
		$this->conn_my = Db::getConnection();
		
		// No database selected, use mysql_db_query or e.g: cyberorder.kund when querying 
		$this->conn_master = Db::getConnection(true);
		
	}

	function doUpgrade($artnr) {
	
		$update = "INSERT INTO cyberphoto.Info_page (artnr) VALUES ('$artnr') ";
		// echo $update;
		// exit;
		mysqli_query($this->conn_master, $update);
		
	}
	
}

?>