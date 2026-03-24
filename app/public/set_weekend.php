<?php 
	include_once("top.php");
	$manual_pagetitle = "Sätt manuellt datum & tid för helgkampanjer";
	include_once("header.php");
	
	?>
	
	<h1>Sätt manuellt datum & tid för helgkampanjer</h1>
	<p><?php echo $wrongmess; ?></p>
	<form method="POST">
	<input type="hidden" value=true name="submTime">
	  <table>
	  <?php if ($sv) { ?>
		<tr>
		  <td><img border="0" src="sv_mini.jpg"></td>
		  <td>&nbsp;</td>
		</tr>
		<tr>
		  <td>Från:</td>
		  <td><input type="text" name="weekendFromSE" size="30" value="<?php echo $_COOKIE['weekendFromSE']; ?>" placeholder="Formateras: 2016-01-31 23:59:59"></td>
		</tr>
		<tr>
		  <td>Till:</td>
		  <td><input type="text" name="weekendToSE" size="30" value="<?php echo $_COOKIE['weekendToSE']; ?>" placeholder="Formateras: 2016-01-31 23:59:59"></td>
		</tr>
		<tr>
		  <td>Text:</td>
		  <td><input type="text" name="weekendTextSE" size="50" value="<?php echo $_COOKIE['weekendTextSE']; ?>" placeholder="Texten avser allt före - Ordninarie pris 1000 kr"></td>
		</tr>
	  </table>
		<hr noshade color="#000000" align="left" width="400" size="1">
	  <?php } ?>
	  <?php if ($fi) { ?>
	  <table>
		<tr>
		  <td><img border="0" src="fi_mini.jpg"></td>
		  <td>&nbsp;</td>
		</tr>
		<tr>
		  <td>Från:</td>
		  <td><input type="text" name="weekendFromFI" size="30" value="<?php echo $_COOKIE['weekendFromFI']; ?>" placeholder="Formateras: 2016-01-31 23:59:59"></td>
		</tr>
		<tr>
		  <td>Till:</td>
		  <td><input type="text" name="weekendToFI" size="30" value="<?php echo $_COOKIE['weekendToFI']; ?>" placeholder="Formateras: 2016-01-31 23:59:59"></td>
		</tr>
		<tr>
		  <td>Text:</td>
		  <td><input type="text" name="weekendTextFI" size="50" value="<?php echo $_COOKIE['weekendTextFI']; ?>"></td>
		</tr>
		<tr>
		  <td>Text SV:</td>
		  <td><input type="text" name="weekendTextFISE" size="50" value="<?php echo $_COOKIE['weekendTextFISE']; ?>"></td>
		</tr>
	  </table>
		<hr noshade color="#000000" align="left" width="400" size="1">
	  <?php } ?>
	  <?php if ($no) { ?>
	  <table>
		<tr>
		  <td><img border="0" src="no_mini.jpg"></td>
		  <td>&nbsp;</td>
		</tr>
		<tr>
		  <td>Från:</td>
		  <td><input type="text" name="weekendFromNO" size="30" value="<?php echo $_COOKIE['weekendFromNO']; ?>" placeholder="Formateras: 2016-01-31 23:59:59"></td>
		</tr>
		<tr>
		  <td>Till:</td>
		  <td><input type="text" name="weekendToNO" size="30" value="<?php echo $_COOKIE['weekendToNO']; ?>" placeholder="Formateras: 2016-01-31 23:59:59"></td>
		</tr>
		<tr>
		  <td>Text:</td>
		  <td><input type="text" name="weekendTextNO" size="50" value="<?php echo $_COOKIE['weekendTextNO']; ?>"></td>
		</tr>
		<hr noshade color="#000000" align="left" width="400" size="1">
	  <?php } ?>
	  </table>
	  <p><input type="submit" value="Spara" name="B1"></p>
	</form>
	<p>&nbsp;</p>
	<p>Formateras enligt: 2016-01-31 23:59:59</p>
	<p>Texten avser allt före "- Ordninarie pris 1000 kr"</p>

<?php
	if ($fi) {
		$no_switch_url = preg_replace("/cyberphoto.fi/", "cyberphoto.no", $currentUrl) ;
		$se_switch_url = preg_replace("/cyberphoto.fi/", "cyberphoto.se", $currentUrl) ;
	} elseif ($no) {
		$fi_switch_url = preg_replace("/cyberphoto.no/", "cyberphoto.fi", $currentUrl) ;
		$se_switch_url = preg_replace("/cyberphoto.no/", "cyberphoto.se", $currentUrl) ;
	} else {
		$no_switch_url = preg_replace("/cyberphoto.se/", "cyberphoto.no", $currentUrl) ;
		$fi_switch_url = preg_replace("/cyberphoto.se/", "cyberphoto.fi", $currentUrl) ;
	}

	echo "<h2>Byt i andra länder</h2>\n";
	echo "<div class=\"\">\n";
	if ($fi || $no) {
		echo "<a class=\"greylink\" href=\"" . $se_switch_url . "\"><img title=\"Visa motsvarande sida i Sverige\" border=\"0\" src=\"/order/admin/sv_mini.jpg\"></a>&nbsp;&nbsp;";
	}
	if (!$fi) {
	echo "<a class=\"greylink\" href=\"" . $fi_switch_url . "\"><img title=\"Visa motsvarande sida i Finland\" border=\"0\" src=\"/order/admin/fi_mini.jpg\"></a>&nbsp;&nbsp;";
	}
	if (!$no) {
	echo "<a class=\"greylink\" href=\"" . $no_switch_url . "\"><img title=\"Visa motsvarande sida i Norge\" border=\"0\" src=\"/order/admin/no_mini.jpg\"></a>&nbsp;&nbsp;";
	}
	echo "</div>\n";
	
	
	include_once("footer.php");
?>