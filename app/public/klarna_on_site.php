<?php 
	include_once("top.php");
	include_once("header.php");
	
	echo "<h1>Klarna On Site</h1>";
	echo "Denna exempelprodukt kostar <b>10 000 kr</b><br><br>";
	?>

	<script>
		window.Klarna.OnsiteMessaging.refresh();
	</script>
	
<!-- Placement v2 -->
<klarna-placement
  data-key="credit-promotion-badge"
  data-locale="sv-SE"
  data-purchase-amount="1000000"
></klarna-placement>
<!-- end Placement -->
	
	
	<?php
	
	include_once("footer.php");
?>