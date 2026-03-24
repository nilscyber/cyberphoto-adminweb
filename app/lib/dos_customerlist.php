<?php

// $q = trim($_GET['q']);
// $o = trim($_GET['o']);
// $q = iconv("UTF-8", "ISO-8859-1", mysqli_real_escape_string( nl2br ( $_GET['q'] ) ) );

// echo $q;

if ((strlen($q)) > 2) {

	$search_bp->getResultSearch($q);

}
/*
if ((strlen($o)) > 4) {

	$search_bp->showCustomerOrderLight($o);

}
*/
?> 