<?php
require_once("CWebADInternSuplier.php");
$adintern = new CWebADInternSuplier();
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