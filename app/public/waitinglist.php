<?php
extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_SKIP);
extract($_COOKIE, EXTR_SKIP);
require_once("CWebADInternSuplier.php");
$adintern = new CWebADInternSuplier();
?>
<html>

<head>
<link rel="stylesheet" type="text/css" href="/css/pricelist.css" />
<link rel="stylesheet" type="text/css" href="/css/suplier.css" />
<title>CyberPhoto - Uppgifter om köplats</title>

<STYLE>
<!--
  tr { background-color: #FFFFFF}
  .normal { background-color: #FFFFFF }
  .highlight { background-color: #EAEAEA }
//-->
</style>

</head>

<body>
<h1>Uppgifter om köplats (ej skickade)</h1>
<div><?php $adintern->displayQueueList($artnr); ?></div>
</body>

</html>