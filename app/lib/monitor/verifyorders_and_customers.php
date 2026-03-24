<?php

//error_reporting(E_ALL);
error_reporting(0);
include("Db.php");

// hour of the day
$h = date("G");
// day of week (1 = monday etc)
$d = date("N");
// standard interval
$interval = '1 HOUR';

// if weekends
if ($d == 6 || $d == 7) {
	if ($h > 23 || $h < 7)
		$interval = '4 HOUR';
	else
		$interval = '2 HOUR';
} else {
	if ($h > 23 || $h < 7)
		$interval = '6 HOUR';
	else if ($h >= 7 || $h < 8)
		$interval = '2 HOUR';
	else {
		$interval = '1 HOUR';
	}	
}

/**
$s = 'SELECT count(*) as cnt from cyberorder.Kundnr where createdDate > (NOW() + INTERVAL - ' . $interval . ')';
$res = mysqli_query(Db::getConnection(true), $s);

$orderrow = mysqli_fetch_object($res);
$numberOfNewCustomers = $orderrow->cnt;
*/
$s = 'SELECT count(*) as cnt from C_BPartner where created > (NOW() - INTERVAL \'' . $interval . '\')';
$s = 'SELECT 1 as cnt ';

$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false),$s) : false;

$orderrow = $res ? pg_fetch_row($res) : null;

$numberOfNewCustomers = $orderrow[0];
/**
$s = 'SELECT count(*) as cnt from cyberorder.ordernr where ordernr is not null AND ordernr > 0 AND createdDate > (NOW() + INTERVAL - ' . $interval . ')';
$res = mysqli_query(Db::getConnectionAD(true), $s);
$orderrow = mysqli_fetch_object($res);
$numberOfNewOrders = $orderrow->cnt;
*/
$s = 'SELECT count(*) as cnt from C_Order where salesrep_id=1652736 AND  created > (NOW() - INTERVAL \'' . $interval . '\')';

$res = (Db::getConnectionAD(false)) ? @pg_query(Db::getConnectionAD(false), $s) : false;
$orderrow = $res ? pg_fetch_object($res) : null;
$numberOfNewOrders = $orderrow->cnt;
//echo ":".$numberOfNewOrders."\n";exit;
$mess = "New customers: " . $numberOfNewCustomers . ", new orders: " . $numberOfNewOrders . ", last  " . $interval;
if ($numberOfNewCustomers == 0 || $numberOfNewOrders == 0)
	echo "2";
else 
	echo "0";
echo " CyberPhoto_Orders_Customers_Check - " . $mess . "\n";
?>