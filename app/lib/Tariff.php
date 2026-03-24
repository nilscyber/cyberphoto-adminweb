<?php

function getRentValue($levid, $basketvalue) {
	
$select = "SELECT rate FROM tariffs where ids = '$levid' ";
$res = mysqli_query($select);
extract(mysqli_fetch_array($res));

$calcMonthValue = $rate * $basketvalue;

return round($calcMonthValue, 0);

}	

?>
