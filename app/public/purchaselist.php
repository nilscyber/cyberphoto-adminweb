<?php
require_once("CWebADInternSuplier.php");
$adintern = new CWebADInternSuplier();
$artnr = isset($_GET['artnr']) ? $_GET['artnr'] : '';
?>
<html>

<head>
<link rel="stylesheet" type="text/css" href="/css/suplier.css" />
<title>CyberPhoto - Uppgifter om inköpsordrar</title>
</head>

<body>
<h1>Uppgifter om inköpsordrar</h1>
<div><?php $adintern->displayPurchaseList($artnr); ?></div>
</body>

</html>