<?php
	if ($numberpac > 1) {
		$artnr_new = $artnr;
	} else {
		if ($product_isSalesBundle && !(preg_match("/pac$/", $article))) {
			$artnr_new = $article;
		} elseif ($article1 != "") {
			$artnr_new = $article1;
		} elseif ($artnr3 != "") { // flera paket som "flera paketlösningar"
			$artnr_new = $artnr3;
		} elseif ($artnr2 != "") { // flera paket som köpknappar
			$artnr_new = $artnr2;
		} else {
			$artnr_new = $article . "pac";
	  	}
	}
	
	echo "<div class=\"container_white\">\n";
	
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $artnr2;
		echo "<h5>" . $artnr_new . "</h5>\n";
	}
?>
		<div align="left">
		  <center>
			<table border="0" cellpadding="2" width="100%" cellspacing="0">
			<tr>
			<td colspan="3"><b><font color="#2B2B2B" face="Arial" size="3"><?php echo l('Package details'); ?></font></td>
			<td align="right"><span onclick="show_hide('<?php echo $artnr_new; ?>');" style="cursor:pointer;"><img border="0" title="<?php echo l('Hide window'); ?>" src="/pic/kryss_l.gif"></td>
			</tr>
			<tr>
				  <td>&nbsp;</td>
				  <td>&nbsp;</td>
				  <td><font color="#2B2B2B" face="Verdana, Arial" size="1"><b><?php echo l('Quantity'); ?></b></font></td>
				  <td><font color="#2B2B2B" face="Verdana, Arial" size="1"><b><?php echo l('Availability'); ?></b></font></td>
			</tr>        
				  
				  <?php $bask->viewPacketDeliveryPictureNew($artnr_new, '1', $fi); ?>
				  
			  </table>
		  </center>
		</div>
		<?php if (preg_match("/telia/i", $artnr_new) && preg_match("/4g/i", $artnr_new)) { ?>
		<hr style="height: 1px; background-color: #85000d; color: #85000d; border:none;">
		<div align="left">
		  <?php CMobile::getPlanDescriptionPublicRight(300438); ?>
		</div>
		<?php } ?>
<?php
	echo "</div>\n";
	
	unset($artnr_new);
	unset($numberpac);
	unset($artnr2);
	unset($product_isSalesBundle);
	// unset($artnr3);

?>