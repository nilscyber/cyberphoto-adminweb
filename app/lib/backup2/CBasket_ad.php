<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2011-02-28

*/

include("connections.php");

Class CBasket {
	var $conn_ms; var $conn_my; var $conn_fi;

function __construct() {
	global $fi;
        
	$this->conn_my = Db::getConnection();
	$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
	@mssql_select_db ("cyberphoto", $this->conn_ms);
	$this->conn_fi = $this->conn_ms;
}

}
?>
