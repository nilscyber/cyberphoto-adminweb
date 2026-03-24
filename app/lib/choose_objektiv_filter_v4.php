<div class="container_white padding2">

	<table border="0" cellpadding="3" cellspacing="0" width="100%">
	  <tr>
		<td colspan="6">&nbsp;<b>Välj egenskap efter ditt önskemål</b></td>
	  </tr>
	  <tr>
		<td width="25"><input type="checkbox" name="fullformat" value="yes" onClick="submit()"<?php if ($fullformat == "yes") echo " checked";?><?php if ($aps == "yes" || $MF == 9 || $MF == 777) echo " disabled";?>></td>
		<td>
		<?php if ($fullformat == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Täyskoon kennolle<?php else: ?>Anpassad för fullformat<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="checkbox" name="weather" value="yes" onClick="submit()"<?php if ($weather == "yes") echo " checked";?>></td>
		<td>
		<?php if ($weather == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Sääsuojattu<?php else: ?>Har väderskydd<?php endif; ?></b></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="checkbox" name="aps" value="yes" onClick="submit()"<?php if ($aps == "yes") echo " checked";?><?php if ($fullformat == "yes" || $MF == 777) echo " disabled";?>></td>
		<td>
		<?php if ($aps == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Ainoastaan APS-C kennolle<?php else: ?>Endast för APS-C sensor<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="checkbox" name="silent" value="yes" onClick="submit()"<?php if ($silent == "yes") echo " checked";?>></td>
		<td>
		<?php if ($silent == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Hiljainen moottori<?php else: ?>Har tyst motor<?php endif; ?></b></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="checkbox" name="bildstab" value="yes" onClick="submit()"<?php if ($MF != 3 && $MF != 7 && $MF != 13 && $MF != 14 && $MF != 29 && $MF != 777) echo " disabled";?><?php if ($bildstab == "yes") echo " checked";?>></td>
		<td>
		<?php if ($bildstab == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Kuvanvakaajalla<?php else: ?>Har bildstabilisering<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="checkbox" name="tested" value="yes" onClick="submit()"<?php if ($tested == "yes") echo " checked";?>></td>
		<td>
		<?php if ($tested == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Vain testatut objektiivit<?php else: ?>Endast testade objektiv<?php endif; ?></b></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="checkbox" name="internfokus" value="yes" onClick="submit()"<?php if ($internfokus == "yes") echo " checked";?>></td>
		<td>
		<?php if ($internfokus == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Vain sisäisellä tarkennuksella<?php else: ?>Endast internfokusering<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="checkbox" name="motor" value="yes" onClick="submit()"<?php if ($MF == "" || $MF == 3) echo " disabled";?><?php if ($motor == "yes") echo " checked";?>></td>
		<td>
		<?php if ($motor == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Sisäänrakennetulla moottorilla<?php else: ?>Har inbyggd motor<?php endif; ?></b></a>
		</td>
	  </tr>
	</table>
    
</div>
