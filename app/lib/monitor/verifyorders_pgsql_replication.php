<?php
error_reporting(0);
include("Db.php");


// clients that should be working
//$clients = array (  '192.168.100.80', '192.168.100.70' );
//$clientsStr = "'192.168.100.80', '192.168.100.70'";

$clients = array (  '192.168.1.80','192.168.1.91' ,'192.168.1.151');
$clientsStr = "'192.168.1.80','192.168.1.91','192.168.1.151' ";

// diff that's acceptable, arbitrary for now
$acceptableDiff= 10000;


//TODO: add warning, not just error
$s = 'select pg_xlog_location_diff(sent_location, write_location) as diff, *  from pg_stat_replication WHERE 
	client_addr IN (' . $clientsStr . ')';//\'192.168.100.80\' ';
//echo $s . "\n";

$res = (Db::getConnectionAD(true)) ? @pg_query(Db::getConnectionAD(true), $s) : false;

$workingClients = array();
$nonWorkingClients = array();
$maxDiff = 0;
while ($res && $row = pg_fetch_object($res)) {
	if ($maxDiff < $row->diff)
		$maxDiff = $row->diff;
    if ($row->diff <= $acceptableDiff) {
        
        $workingClients[] = $row->client_addr;
    } else {
        $nonWorkingClients[] = $row->client_addr;
    }
}
foreach ($clients as $client) {
	if (!in_array($client, $workingClients)) {
	      if (!in_array($client, $nonWorkingClients))
            $nonWorkingClients[] = $client; 
    }
}
$nonWorkingFormat = '';
foreach ($nonWorkingClients as $s) {
    $nonWorkingFormat  == '' ? ($nonWorkingFormat = $s) : ( $nonWorkingFormat .= "," . $s );   
}
$workingFormat = '';
foreach ($workingClients as $s) {
    $workingFormat  == '' ? ($workingFormat = $s) : ( $workingFormat .= "," . $s );   
}
if (count($nonWorkingClients)>0) {
    echo "2";
    $mess = "Non working clients: " . $nonWorkingFormat . " : max diff: " . $maxDiff;
} else {
    echo "0";   
    $mess = "Working clients: " . $workingFormat;
}

echo " CyberPhoto_Pgsql_Replication_Check - " . $mess . "\n";
?>
