	<?php if ($utpris_campaign > 800 && !$fi && strtotime('2015-12-31 23:59:59') > strtotime("now")) { ?>
	<div>
		<a onClick="_gaq.push(['_trackEvent', 'FrontClickMobil', 'Sektion_forsakring', 'forsakra.png']);" href="/info.php?article=forsakring">
		<img border="0" title="Köp till en försäkring till din nya mobil. Klicka här för att läsa mer!" src="/pic/forsakra.png"></a>
	</div>
	<?php } ?>
	<div>
		<br>
		<a onClick="_gaq.push(['_trackEvent', 'FrontClickMobil', 'Jabra BT3030 BlueTooth headset', 'JABRA.png']);" href="/info.php?article=BT3030">
		<img border="0" src="/plugg/JABRA.png"></a>
	</div>
	<?php if (strtotime('2013-06-20 00:00:01') < strtotime("now") && strtotime('2013-08-31 23:59:59') > strtotime("now")) { ?>
		<br>
		<a target="_parent" onClick="_gaq.push(['_trackEvent', 'ProductClickPlugg', 'Samsung 25%', '25OFF.png']);" href="http://www.cyberphoto.se/mobil/samsung-shop">
		<img border="0" src="/pic/25OFF.png"></a>
	<?php } ?>
	<?php if ($tillverkar_id_campaign != 17 && $ejmer == "nu") { ?>
	<div style="margin-top: 10px;">
		<a onClick="_gaq.push(['_trackEvent', 'FrontClickMobil', 'Sektion_fsecure', 'fsecure.png']);" href="/info.php?article=FMI812VR001IN">
		<img border="0" title="Säkra din telefon med F-Secure. Klicka här för att läsa mer!" src="/pic/fsecure.png"></a>
	</div>
	<?php } ?>
