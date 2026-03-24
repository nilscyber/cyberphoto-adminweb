<?php

/*

PHP login object
author		Nils Kohlström
version		0.9 2006-07-14

*/
	
// include("connections.php");

Class CJulkalender {
	var $conn_ext;

function __construct() {
        
	$conn_ext = Db::getConnectionDb('cyberadmin');

	/*
	$this->conn_my = @mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
	@mysqli_select_db($this->conn_my, "cyberadmin");
	*/

}

function insertVote($IP,$namn,$epost,$motiv,$lucka) {

mysqli_query("INSERT INTO cal09 (calIP,calName,calEmail,calMotiv,calLucka) VALUES ('$IP','$namn','$epost','$motiv','$lucka') ");

}


}
?>
