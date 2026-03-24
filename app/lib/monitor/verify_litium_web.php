<?php
$mess = 'Litium_verify_'.$argv[1];
if ($argv[1] == 'web01')
	$url = 'https://web01.cyberphoto.se';        
else if ($argv[1] == 'web02')
	$url = 'https://web02.cyberphoto.se';
else if ($argv[1] == 'web04')
        $url = 'https://web04.cyberphoto.se';
else if ($argv[1] == 'web05')
        $url = 'https://web05.cyberphoto.se';
else if ($argv[1] == 'www')
	$url = 'https://www.cyberphoto.se';
else {
	echo "2 " . $mess . " - Felaktig webserver angiven:   " . $argv[1] . "\n";
	exit;
}
//$url = 'http://www2.cyberphoto.se/error.php';
$curl = curl_init($url);       
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); 
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
//change the timeout here to increase
curl_setopt($curl, CURLOPT_TIMEOUT, 60);  
curl_setopt($curl,CURLOPT_FAILONERROR,true);
$subject = curl_exec($curl); 
$err = 0;
if (curl_errno($curl)) {
    $err = curl_errno($curl);
}

$size = curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD); 

curl_close($curl);
if ($err > 0) 
	echo "2 " . $mess . " - Web " . $argv[1] . " har problem: Error:  " . $err . "\n";
else if ($size < 4000)
	echo "2 " . $mess . " - Web " . $argv[1] . " har problem: Storlek:  " . $size . "\n";
else
	echo "0 " . $mess . " - Web " . $argv[1] . " inga problem" . "\n";
