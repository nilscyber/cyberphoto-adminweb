<div class="container_white padding2">

	<table border="0" cellpadding="3" cellspacing="0" width="100%">
	  <tr>
		<td colspan="6">&nbsp;<b>Välj objektivtyp</b></td>
	  </tr>
	  <!--
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="999" onClick="submit()"<?php if ($objtype == 999) echo " checked";?>></td>
		<td colspan="5"><?php if ($objtype == 999) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Visa alla<?php else: ?>Näytä kaikki<?php endif; ?></b></td>
	  </tr>
	  -->
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="45" onClick="submit()"<?php if ($objtype == 45) echo " checked";?>></td>
		<td>
		<?php if ($objtype == 45) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Laajakulma<?php else: ?>Vidvinkel<?php endif; ?></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="375" onClick="submit()"<?php if ($objtype == 375) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
		<?php if ($objtype == 375) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Laajakulmazoom<?php else: ?>Vidvinkelzoom<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="228" onClick="submit()"<?php if ($objtype == 228) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
		<?php if ($objtype == 228) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Telejatkeet<?php else: ?>Telekonvertrar<?php endif; ?></b></font></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="50" onClick="submit()"<?php if ($objtype == 50) echo " checked";?>></td>
		<td>
		<?php if ($objtype == 50) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Normaali<?php else: ?>Normal<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="374" onClick="submit()"<?php if ($objtype == 374) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
		<?php if ($objtype == 374) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Normaalizoom<?php else: ?>Normalzoom<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="49" onClick="submit()"<?php if ($objtype == 49) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
		<?php if ($objtype == 49) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Välirenkaat<?php else: ?>Mellanringar<?php endif; ?></b></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="42" onClick="submit()"<?php if ($objtype == 42) echo " checked";?>></td>
		<td>
		<?php if ($objtype == 42) { ?><b><font color="#85000D"><?php } ?>Tele</b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="373" onClick="submit()"<?php if ($objtype == 373) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
		<?php if ($objtype == 373) { ?><b><font color="#85000D"><?php } ?>Telezoom</b></a>
		</td>
		<td width="25">&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="43" onClick="submit()"<?php if ($objtype == 43) echo " checked";?>></td>
		<td>
		<?php if ($objtype == 43) { ?><b><font color="#85000D"><?php } ?>Macro</b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="376" onClick="submit()"<?php if ($objtype == 376) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
		<?php if ($objtype == 376) { ?><b><font color="#85000D"><?php } ?>Superzoom</b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="999" onClick="submit()"<?php if ($objtype == 999) echo " checked";?>></td>
		<td><?php if ($objtype == 999) { ?><b><font color="#85000D"><?php } ?><?php if ($fi && !$sv): ?>Näytä kaikki<?php else: ?>Alla objektiv<?php endif; ?></b></td>
	  </tr>
	</table>
    
</div>
