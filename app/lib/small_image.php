			<table border="0" cellpadding="0" cellspacing="1">
			 <tr>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<?php echo "/bilder/".$bild; ?>', 0, false); return false;">
					   <img src="<?php echo "/thumbs/small/bilder/".$bild; ?>" style="cursor:hand">
					   </a>
			   </td>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<?php echo "/bilder/".$bild2; ?>', 1, true); return false;">
					   <img src="<?php echo "/thumbs2/small/bilder/".$bild2; ?>" style="cursor:hand">
				    </a>
			   </td>
			 <?php if ($bild3 != "" && $bild2 != "") { ?>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<?php echo "/bilder/".$bild3; ?>', 2, true); return false;">
					   <img src="<?php echo "/thumbs3/small/bilder/".$bild3; ?>" style="cursor:hand">
				    </a>
			   </td>
			 <?php } ?>
			 <?php if ($bild4 != "" && $bild3 != "") { ?>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<?php echo "/bilder/".$bild4; ?>', 3, true); return false;">
					   <img src="<?php echo "/thumbs4/small/bilder/".$bild4; ?>" style="cursor:hand">
				    </a>
			   </td>
			 <?php } ?>
			 <?php if ($bild5 != "" && $bild4 != "") { ?>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<?php echo "/bilder/".$bild5; ?>', 3, true); return false;">
					   <img src="<?php echo "/thumbs5/small/bilder/".$bild5; ?>" style="cursor:hand">
				    </a>
			   </td>
			 <?php } ?>
			 <?php if ($bild6 != "" && $bild5 != "") { ?>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<?php echo "/bilder/".$bild6; ?>', 3, true); return false;">
					   <img src="<?php echo "/thumbs6/small/bilder/".$bild6; ?>" style="cursor:hand">
				    </a>
			   </td>
			 <?php } ?>
			</tr>
			</table>
