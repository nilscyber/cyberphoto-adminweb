<?php
//stomp_cyber_t.php är den som är aktiv...

// include a library
require_once("Stomp.php");
// make a connection
$con = new Stomp("tcp://frugalitas.cyberphoto.se:61613");
// connect
$con->connect();
// send a message to the queue
//echo $newordernr;
$con->send("/queue/neworder", $newordernr, array('persistent'=>'true'));
// subscribe to the queue
//$con->subscribe("/queue/neworder");

/**
// receive a message from the queue
$msg = $con->readFrame();

print_r($msg);

// do what you want with the message
if ( $msg->body == $newordernr) {
    echo "Worked\n";
    // mark the message as received in the queue
    $con->ack($msg);
} else {
    echo "Failed\n";
}

// disconnect
*/
$con->disconnect();
