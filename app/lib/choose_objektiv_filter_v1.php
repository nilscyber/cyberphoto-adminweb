<div id="filtercontainer">
<div class="roundtop">
<div class="infor1"></div>
<div class="infor2"></div>
<div class="infor3"></div>
<div class="infor4"></div>
</div>

<div class="content">

	<table border="0" cellpadding="2" cellspacing="0" width="100%">
	  <tr>
		<td colspan="6">&nbsp;<b>Välj egenskap efter ditt önskemål</b></td>
	  </tr>
	  <tr>
		<td width="25"><input type="checkbox" name="fullformat" value="yes" onClick="submit()"<?php if ($fullformat == "yes") echo " checked";?><?php if ($aps == "yes" || $MF == 9 || $MF == 777) echo " disabled";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/fullformat.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/fullformat_fi.php"); ?>')">
        <?php } ?>
		<?php if ($fullformat == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Anpassad för fullformat<?php else: ?>Täyskoon kennolle<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="checkbox" name="weather" value="yes" onClick="submit()"<?php if ($weather == "yes") echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/weather.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/weather_fi.php"); ?>')">
        <?php } ?>
		<?php if ($weather == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Har väderskydd<?php else: ?>Sääsuojattu<?php endif; ?></b></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="checkbox" name="aps" value="yes" onClick="submit()"<?php if ($aps == "yes") echo " checked";?><?php if ($fullformat == "yes" || $MF == 777) echo " disabled";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/aps.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/aps_fi.php"); ?>')">
        <?php } ?>
		<?php if ($aps == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Endast för APS-C sensor<?php else: ?>Ainoastaan APS-C kennolle<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="checkbox" name="silent" value="yes" onClick="submit()"<?php if ($silent == "yes") echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/silent.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/silent_fi.php"); ?>')">
        <?php } ?>
		<?php if ($silent == "yes") { ?><b><font color="#85000D"><?php } ?><?php if($sv): ?>Har tyst motor<?php else: ?>Hiljainen moottori<?php endif; ?></b></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="checkbox" name="bildstab" value="yes" onClick="submit()"<?php if ($MF != 3 && $MF != 7 && $MF != 777) echo " disabled";?><?php if ($bildstab == "yes") echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/bildstab.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/bildstab_fi.php"); ?>')">
        <?php } ?>
		<?php if ($bildstab == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Har bildstabilisering<?php else: ?>Kuvanvakaajalla<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="checkbox" name="tested" value="yes" onClick="submit()"<?php if ($tested == "yes") echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/testad.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/testad_fi.php"); ?>')">
        <?php } ?>
		<?php if ($tested == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Endast testade objektiv<?php else: ?>Vain testatut objektiivit<?php endif; ?></b></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="checkbox" name="internfokus" value="yes" onClick="submit()"<?php if ($internfokus == "yes") echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/internfokus.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/internfokus_fi.php"); ?>')">
        <?php } ?>
		<?php if ($internfokus == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Endast internfokusering<?php else: ?>Vain sisäisellä tarkennuksella<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="checkbox" name="motor" value="yes" onClick="submit()"<?php if ($MF == "" || $MF == 3) echo " disabled";?><?php if ($motor == "yes") echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/motor.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/motor_fi.php"); ?>')">
        <?php } ?>
		<?php if ($motor == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Har inbyggd motor<?php else: ?>Sisäänrakennetulla moottorilla<?php endif; ?></b></a>
		</td>
	  </tr>
	</table>
    
</div>

<div class="roundbottom">
<div class="infor4"></div>
<div class="infor3"></div>
<div class="infor2"></div>
<div class="infor1"></div>
</div>
</div>
    