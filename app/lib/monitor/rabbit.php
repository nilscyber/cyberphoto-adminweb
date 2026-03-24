<?php

//error_reporting(E_ALL);
error_reporting(0);
require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$exchange = 'router';
$queue = 'testqueue';
//$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);

$mess = "Litium_Verify_RabbitMQ";

try {
	$connection = new AMQPStreamConnection('35.195.84.43', 5672, 'cppro', 'cppro');
	
} catch (Exception $e) {
	//echo "connection error\n";
	$err = preg_replace( "/\r|\n/", "", $e );
	echo "2 " . $mess . " - RabbitMQ problem: " . $err . "\n";	
	exit;
}
echo "0 " . $mess . " - RabbitMQ inga problem" . "\n";
$connection->close();
exit;

/*
    The following code is the same both in the consumer and the producer.
    In this way we are sure we always have a queue to consume from and an
        exchange where to publish messages.
*/
/*
    name: $queue
    passive: false
    durable: true // the queue will survive server restarts
    exclusive: false // the queue can be accessed in other channels
    auto_delete: false //the queue won't be deleted once the channel is closed.
*/
$channel->queue_declare($queue, false, true, false, false);
/*
    name: $exchange
    type: direct
    passive: false
    durable: true // the exchange will survive server restarts
    auto_delete: false //the exchange won't be deleted once the channel is closed.
*/
$channel->exchange_declare($exchange, 'direct', false, true, false);
$channel->queue_bind($queue, $exchange);
//$messageBody = implode(' ', array_slice($argv, 1));
$messageBody = "meddelandet vi skickar";

$message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
$channel->basic_publish($message, $exchange);
$channel->close();
$connection->close();