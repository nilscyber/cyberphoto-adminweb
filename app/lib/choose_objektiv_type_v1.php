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
		<td colspan="6">&nbsp;<b>Välj objektivtyp</b></td>
	  </tr>
	  <!--
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="999" onClick="submit()"<?php if ($objtype == 999) echo " checked";?>></td>
		<td colspan="5"><?php if ($objtype == 999) { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Visa alla<?php else: ?>Näytä kaikki<?php endif; ?></b></td>
	  </tr>
	  -->
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="45" onClick="submit()"<?php if ($objtype == 45) echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/vidvinkel.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/vidvinkel_fi.php"); ?>')">
        <?php } ?>
		<?php if ($objtype == 45) { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Vidvinkel<?php else: ?>Laajakulma<?php endif; ?></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="375" onClick="submit()"<?php if ($objtype == 375) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/vidvinkel_zoom.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/vidvinkel_zoom_fi.php"); ?>')">
        <?php } ?>
		<?php if ($objtype == 375) { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Vidvinkelzoom<?php else: ?> Laajakulmazoom<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="228" onClick="submit()"<?php if ($objtype == 228) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
		<?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/telekonvertrar.php"); ?>')">
		<?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/telekonvertrar_fi.php"); ?>')">
		<?php } ?>
		<?php if ($objtype == 228) { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Telekonvertrar<?php else: ?> Telejatkeet<?php endif; ?></b></font></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="50" onClick="submit()"<?php if ($objtype == 50) echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/normal.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/normal_fi.php"); ?>')">
        <?php } ?>
		<?php if ($objtype == 50) { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Normal<?php else: ?>Normaali<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="374" onClick="submit()"<?php if ($objtype == 374) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/normal_zoom.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/normal_zoom_fi.php"); ?>')">
        <?php } ?>
		<?php if ($objtype == 374) { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Normalzoom<?php else: ?>Normaalizoom<?php endif; ?></b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="49" onClick="submit()"<?php if ($objtype == 49) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
		<?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/mellanringar.php"); ?>')">
		<?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/mellanringar_fi.php"); ?>')">
		<?php } ?>
		<?php if ($objtype == 49) { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Mellanringar<?php else: ?> Välirenkaat<?php endif; ?></b></a>
		</td>
	  </tr>
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="42" onClick="submit()"<?php if ($objtype == 42) echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/tele.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/tele_fi.php"); ?>')">
        <?php } ?>
		<?php if ($objtype == 42) { ?><b><font color="#85000D"><?php } ?>Tele</b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="373" onClick="submit()"<?php if ($objtype == 373) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/tele_zoom.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/tele_zoom_fi.php"); ?>')">
        <?php } ?>
		<?php if ($objtype == 373) { ?><b><font color="#85000D"><?php } ?>Telezoom</b></a>
		</td>
		<td width="25">&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	  <tr>
		<td width="25"><input type="radio" name="objtype" value="43" onClick="submit()"<?php if ($objtype == 43) echo " checked";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/makro.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/makro_fi.php"); ?>')">
        <?php } ?>
		<?php if ($objtype == 43) { ?><b><font color="#85000D"><?php } ?>Macro</b></a>
		</td>
		<td width="25"><input type="radio" name="objtype" value="376" onClick="submit()"<?php if ($objtype == 376) echo " checked";?><?php if ($MF == 5) echo " disabled";?>></td>
		<td>
        <?php if ($sv) { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/superzoom.php"); ?>')">
        <?php } else { ?>
			<a class="choose" onMouseOver="return escape('<?php include ("explanation/lens/superzoom_fi.php"); ?>')">
        <?php } ?>
		<?php if ($objtype == 376) { ?><b><font color="#85000D"><?php } ?>Superzoom</b></a>
		</td>
		<td width="25">&nbsp;</td>
		<td>&nbsp;</td>
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
    