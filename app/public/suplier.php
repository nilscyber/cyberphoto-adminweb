<?php
require_once("CWebADInternSuplier.php");
$adintern = new CWebADInternSuplier();
?>
<html>

<head>
<link rel="stylesheet" type="text/css" href="/css/suplier.css" />
<title>CyberPhoto - Leverantörsuppgifter</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
<h1>Mer uppgifter om leverantören</h1>
<div><?php $adintern->displaySuplierInfo($artnr); ?></div>
</body>

</html>