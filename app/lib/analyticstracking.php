<?php if ($fi) { ?>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-8710854-1', 'auto');
	  ga('require', 'displayfeatures');
	  ga('send', 'pageview');
	  <?php if (preg_match("/placeOrder\.php/i", $_SERVER['PHP_SELF']) && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") { ?>
	  ga('require', 'ecommerce');
		ga('ecommerce:addTransaction', {
		  'id': '<?php echo $ordernrladdaom; ?>',
		  'affiliation': 'CyberPhoto',
		  'revenue': '<?php echo round($orderrow->netto,0); ?>',
		  'shipping': '',
		  'tax': '<?php echo round($orderrow->moms,0); ?>',
		  'currency': 'EUR'
		});
		ga('ecommerce:addItem', {
		  'id': '<?php echo $ordernrladdaom; ?>',
		  'name': '',
		  'sku': '123456',
		  'category': '',
		  'price': '<?php echo round($orderrow->netto,0); ?>',
		  'quantity': '1',
		  'currency': 'EUR'
		});
	   ga('ecommerce:send');
	  <?php } ?>

	</script>
<?php } elseif ($no) { ?>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-44685763-1', 'auto');
	  ga('require', 'displayfeatures');
	  ga('send', 'pageview');
	  <?php if (preg_match("/placeOrder\.php/i", $_SERVER['PHP_SELF']) && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") { ?>
	  ga('require', 'ecommerce');
		ga('ecommerce:addTransaction', {
		  'id': '<?php echo $ordernrladdaom; ?>',
		  'affiliation': 'CyberPhoto',
		  'revenue': '<?php echo round($orderrow->netto,0); ?>',
		  'shipping': '',
		  'tax': '<?php echo round($orderrow->moms,0); ?>',
		  'currency': 'NOK'
		});
		ga('ecommerce:addItem', {
		  'id': '<?php echo $ordernrladdaom; ?>',
		  'name': '',
		  'sku': '123456',
		  'category': '',
		  'price': '<?php echo round($orderrow->netto,0); ?>',
		  'quantity': '1',
		  'currency': 'NOK'
		});
	   ga('ecommerce:send');
	  <?php } ?>

	</script>
<?php } else { ?>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-8041840-1', 'auto');
	  ga('require', 'displayfeatures');
	  ga('send', 'pageview');
	  <?php 
	  if (preg_match("/placeOrder\.php/i", $_SERVER['PHP_SELF']) && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") { 
			Log::addLog("PlaceOrder, inside analyticstracking, orderrow->netto: " . round($orderrow->netto,0) . "\n Orderrow object: " . print_r($orderrow, true) . "\n Session vars: " . print_r($_SESSION, true) . " \n Allt annat " . print_r(get_defined_vars(), true), Log::LEVEL_INFO);	
	  ?>
	  ga('require', 'ecommerce');
		ga('ecommerce:addTransaction', {
		  'id': '<?php echo $ordernrladdaom; ?>',
		  'affiliation': 'CyberPhoto',
		  'revenue': '<?php echo round($orderrow->netto,0); ?>',
		  'shipping': '',
		  'tax': '<?php echo round($orderrow->moms,0); ?>',
		  'currency': 'SEK'
		});
		ga('ecommerce:addItem', {
		  'id': '<?php echo $ordernrladdaom; ?>',
		  'name': '',
		  'sku': '123456',
		  'category': '',
		  'price': '<?php echo round($orderrow->netto,0); ?>',
		  'quantity': '1',
		  'currency': 'SEK'
		});
	   ga('ecommerce:send');
	  <?php } ?>

	</script>
<?php } ?>