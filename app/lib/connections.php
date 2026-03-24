<?php

global $fi;

/**
  if ($fi) {
  $conn_fi = @mssql_pconnect ("62.80.138.170", "apache", "aKatöms#1");
  @mssql_select_db ("cyberphoto", $conn_fi);
  }
 */
$conn_my = Db::getConnection();
$conn_ms =  null;//@mssql_pconnect("81.8.240.66", "apache", "aKatöms#1");
//@mssql_select_db("cyberphoto", $conn_ms);
$conn_fi = $conn_ms;
$conn_master = Db::getConnection(true);

if ($fi)
    $conn_standard = $conn_fi;
else
    $conn_standard = $conn_ms;
?>