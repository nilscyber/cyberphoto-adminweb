<?php
if ($fi) {
	$criteria .= " AND ej_med_fi=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen_fi=0 OR lagersaldo > 0 ) AND NOT (demo = -1 AND demo_fi != -1) ";
} elseif ($no) {
	$criteria .= " AND ej_med_no=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen_no=0 OR lagersaldo > 0) AND NOT (demo = -1 AND demo_no != -1) ";
} else {
	$criteria .= " AND ej_med=0 AND (demo=0 OR lagersaldo > 0) AND (utgangen=0 OR lagersaldo > 0) ";
}

?>