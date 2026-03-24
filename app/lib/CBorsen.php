<?php

$conn_borsen = Db::getConnectionDb('cyberborsen');

function countAdd($kategori_id) {

$select = "SELECT id FROM saljes WHERE kategori_id = '$kategori_id' ";
$res = mysqli_query($select);
if ($res)
echo mysqli_num_rows($res);

}

?>
