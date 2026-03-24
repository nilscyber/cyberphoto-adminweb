<?php

Class CCheckStore {

function getArtStore($artnr) {
	
	$select = "SELECT hyllplats FROM Hyllplats WHERE artnr = '" . $artnr . "' ";
	$res = mysqli_query($select);

	if (mysqli_num_rows($res) > 0) {

		while ($row = mysqli_fetch_array($res)):
	
		extract($row);

		echo $hyllplats;

		endwhile;

	} else {
	
		echo "ej bestämt";
		
	}

}	

}

?>
