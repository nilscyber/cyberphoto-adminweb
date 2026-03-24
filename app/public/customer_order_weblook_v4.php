<div class="clear"></div>
<div class="top20"></div>
<hr noshade color="#0000FF" align="left" width="850" size="1">
<?php if ($orderrow->docstatus == "IN") { ?>
<h6>Dokumentstatus är "Felaktig". Kunden ser ej denna order. Kontrollera och åtgärda detta i ADempiere.</h6>
<?php } ?>
<?php if ($orderrow->docstatus == "VO") { ?>
<h6>Denna order är annullerad. Kunden ser ej denna order.</h6>
<?php } ?>
