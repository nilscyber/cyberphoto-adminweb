<?php if ($fi) { ?>
	<?php if (preg_match("/placeOrder\.php/i", $_SERVER['PHP_SELF']) && $frameless && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") { ?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-8710854-1']);
		  _gaq.push(['_setDomainName', 'cyberphoto.fi']);
		  _gaq.push(['_setAllowLinker', true]);
		  _gaq.push(['_trackPageview']);
		  _gaq.push(['_addTrans','<?php echo $ordernrladdaom; ?>','CyberPhoto','<?php echo round($orderrow->netto,0); ?>','<?php echo round($orderrow->moms,0); ?>','','<?php echo $orderrow->lpostadr; ?>','','']);
		  _gaq.push(['_addItem','<?php echo $ordernrladdaom; ?>','123456','','','<?php echo round($orderrow->netto,0); ?>','1']);
		  _gaq.push(['_trackTrans']);
		  
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	<?php } else { ?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-8710854-1']);
		  _gaq.push(['_setDomainName', 'cyberphoto.fi']);
		  _gaq.push(['_setAllowLinker', true]);
		  _gaq.push(['_trackPageview']);
		  _gaq.push(['_setAllowHash', false]);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	<?php } ?>
<?php } elseif ($no) { ?>
	<?php if (preg_match("/placeOrder\.php/i", $_SERVER['PHP_SELF']) && $frameless && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") { ?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-44685763-1']);
		  _gaq.push(['_setDomainName', 'cyberphoto.no']);
		  _gaq.push(['_setAllowLinker', true]);
		  _gaq.push(['_trackPageview']);
		  _gaq.push(['_addTrans','<?php echo $ordernrladdaom; ?>','CyberPhoto','<?php echo round($orderrow->netto,0); ?>','<?php echo round($orderrow->moms,0); ?>','','<?php echo $orderrow->lpostadr; ?>','','']);
		  _gaq.push(['_addItem','<?php echo $ordernrladdaom; ?>','123456','','','<?php echo round($orderrow->netto,0); ?>','1']);
		  _gaq.push(['_trackTrans']);
		  
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	<?php } else { ?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-44685763-1']);
		  _gaq.push(['_setDomainName', 'cyberphoto.no']);
		  _gaq.push(['_setAllowLinker', true]);
		  _gaq.push(['_trackPageview']);
		  _gaq.push(['_setAllowHash', false]);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	<?php } ?>
<?php } else { ?>
	<?php if (preg_match("/placeOrder\.php/i", $_SERVER['PHP_SELF']) && $frameless && $_SERVER['REMOTE_ADDR'] != "192.168.1.89") { ?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-8041840-1']);
		  _gaq.push(['_setDomainName', 'cyberphoto.se']);
		  _gaq.push(['_setAllowLinker', true]);
		  _gaq.push(['_trackPageview']);
		  _gaq.push(['_addTrans','<?php echo $ordernrladdaom; ?>','CyberPhoto','<?php echo round($orderrow->netto,0); ?>','<?php echo round($orderrow->moms,0); ?>','','<?php echo $orderrow->lpostadr; ?>','','']);
		  _gaq.push(['_addItem','<?php echo $ordernrladdaom; ?>','123456','','','<?php echo round($orderrow->netto,0); ?>','1']);
		  _gaq.push(['_trackTrans']);
		  
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	<?php } else { ?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-8041840-1']);
		  _gaq.push(['_setDomainName', 'cyberphoto.se']);
		  _gaq.push(['_setAllowLinker', true]);
		  _gaq.push(['_trackPageview']);
		  _gaq.push(['_setAllowHash', false]);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	<?php } ?>
<?php } ?>