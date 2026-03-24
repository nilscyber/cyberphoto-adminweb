<?php

function checkIpAdress($ip_adress) {
	
	if (eregi("81.8.240.", $ip_adress)) { // Detta är alla våra fasta IP nummer på CyberPhoto.

	$ip_splitt = substr("$ip_adress", 9);
	
		if (($ip_splitt > 65 && $ip_splitt < 95) || ($ip_splitt > 96 && $ip_splitt < 126)) {

		return true;

		} else {

		return false;

		}

	} elseif (eregi("192.168.1.", $ip_adress)) { // Våra IP adresser internt på CyberPhoto

	return true;

	} elseif ($ip_adress == "89.160.46.69") { // Special adresser. Sjabo hemma, etc.etc. Bara att fylla på.

	return true;
	
	} else {

	return false;
	
	}
	
}

?>
