<?php
if ($addfrom == "") {
	$addfrom = date("Y-m-d H:i:s", time()); 
}
if ($addto == "") {
	if (date('j') > 25) {
		$addto = date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+2,1-1,date("Y"))); 
	} else {
		$addto = date("Y-m-d 23:59:59",mktime(0,0,0,date("n")+1,1-1,date("Y"))); 
	}
}
if ($addnotifyfrom == "") {
	if (date('j') > 25) {
		$addnotifyfrom = date("Y-m-d 09:30:00",mktime(0,0,0,date("n")+2,1-4,date("Y"))); 
	} else {
		$addnotifyfrom = date("Y-m-d 09:30:00",mktime(0,0,0,date("n")+1,1-4,date("Y"))); 
	}
}
if ($addkampanjkod == "") {
	$addkampanjkod = $campaign->createRandomPassword(6);
}
if ($addactive_se == "" && $addactive_fi == "" && $addactive_no == "" && $addartikelnr != "") {
	
	if ($fi) {
		$addactive_fi = -1;
	} elseif ($no) {
		$addactive_no = -1;
	} else {
		$addactive_se = -1;
	}
	
}
?>
<?php
if ($_COOKIE['login_ok'] != "true") {
	echo "<div class=\"container_loggin\">\n";
	echo "<span class=\"not_loggin\">Du är Ej inloggad och kommer därför inte kunna utföra åtgärden!</span>\n";
	echo "</div>\n";
	echo "<div class=\"clear\"></div>\n";
}
?>
<div class="framebox">
<div class=top10>
	<?php 
	if ($wrongmess) {
		echo "<div class=\"wrongmess\"><ul>" . $wrongmess . "</ul></div>";
	}
	?>
	<form>
	  <?php if ($addid !="") { ?>
	  <input type="hidden" value="<?php echo $addid; ?>" name="addid">
	  <input type="hidden" value=true name="submC">
	  <?php } else { ?>
	  <input type="hidden" value=true name="subm">
	  <input type="hidden" value="yes" name="add">
	  <?php } ?>
	  <?php if ($copyid > 0) { ?>
	  <input type="hidden" value=<?php echo $copyid; ?> name="copyid">
	  <?php } ?>
	  <table border="0" cellpadding="1" cellspacing="3">
		<tr>
		  <td>Eventuell titel på sidan</td>
		  <td colspan="4"><input type="text" name="addtitle_se" size="58" value="<?php echo $addtitle_se; ?>">&nbsp;<img border="0" src="sv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addtitle_fi" size="58" value="<?php echo $addtitle_fi; ?>">&nbsp;<img border="0" src="fi_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="6"><input type="text" name="addtitle_no" size="58" value="<?php echo $addtitle_no; ?>">&nbsp;<img border="0" src="no_mini.jpg"></td>
		</tr>
		<tr>
		  <td colspan="5"><hr noshade color="#000000" align="left" size="1"></td>
		</tr>
		<tr>
		  <td>Kampanjkod <b><font color="#FF0000">*</font></b></td>
		  <td colspan="4">
		  <?php if ($addid !="") { ?>
			<input style="text-transform: uppercase; font-weight: bold; background: #EBEBEB" type="text" name="addkampanjkod" size="20" value="<?php echo $addkampanjkod; ?>" onfocus="this.blur()"></td>
		  <?php } else { ?>
			<input style="text-transform: uppercase; font-weight: bold" type="text" name="addkampanjkod" size="20" value="<?php echo $addkampanjkod; ?>"></td>
		  <?php } ?>
		</tr>
		<tr>
		  <td>Gäller från <b><font color="#FF0000">*</font></b></td>
		  <td><input type="text" name="addfrom" size="20" value="<?php echo $addfrom; ?>" style="background-color: #93E393"></td>
		  <td>&nbsp;</td>
		  <td>Gäller till <b><font color="#FF0000">*</font></b></td>
		  <td><input type="text" name="addto" size="20" value="<?php echo $addto; ?>" style="background-color: #93E393"></td>
		</tr>
		<tr>
		  <td>Aktiv: <img border="0" src="sv_mini.jpg"></td>
		  <td><input type="checkbox" name="addactive_se" value="yes" <?php if ($addactive_se != "0" && $addactive_se != "") { ?> checked <?php } ?>></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <?php if ($change != "") { ?>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?change=<?php echo $addid; ?>&now=yes">Avsluta tiden som är nu</a></td>
		  <?php } else { ?>
			<td>&nbsp;</td>
		  <?php } ?>
		</tr>
		<tr>
		  <td>Aktiv: <img border="0" src="fi_mini.jpg"></td>
		  <td><input type="checkbox" name="addactive_fi" value="yes" <?php if ($addactive_fi != "0" && $addactive_fi != "") { ?> checked <?php } ?>></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		</tr>
		<tr>
		  <td>Aktiv: <img border="0" src="no_mini.jpg"></td>
		  <td><input type="checkbox" name="addactive_no" value="yes" <?php if ($addactive_no != "0" && $addactive_no != "") { ?> checked <?php } ?>></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		</tr>
		
		<tr>
		  <td>Personlig rabattkod: <img border="0" src="personal_mini.png"></td>
		  <td colspan="4"><input type="checkbox" name="addpersonal_discount" value="yes" <?php if ($addpersonal_discount != "0" && $addpersonal_discount != "") { ?> checked <?php } ?>>&nbsp; (rabattkoden slutar gälla direkt efter order är lagd)</td>
		</tr>
		
		<?php if (!$campaign->checkPaKopet($addid) && !$campaign->checkPaKopet($copyid)) { ?>
		
		<tr>
		  <td colspan="5"><hr noshade color="#000000" align="left" size="1"></td>
		</tr>
		<?php if ($addkategori == "" && $addmanufacturer == "") { ?>
		<tr>
		  <td>Avser artikelnr</td>
		  <td colspan="4"><input type="text" name="addartikelnr" size="20" style="background-color: #FFBA53" value="<?php echo $addartikelnr; ?>"></td>
		</tr>
		<?php } ?>
		<?php if ($addartikelnr == "") { ?>
		<tr>
		  <td>Avser kategori</td>
		  <td colspan="4">
			<select size="1" name="addkategori" style="background-color: #FFBA53">
			<option></option>
			<?php $campaign->getKategori(); ?>
			</select>
		  </td>
		</tr>
		<tr>
		  <td>Avser tillverkare</td>
		  <td colspan="4">
			<select size="1" name="addmanufacturer" style="background-color: #FFBA53">
			<option></option>
			<?php $campaign->getManufacturer(); ?>
			</select>
		  </td>
		</tr>
		<?php } ?>
		<?php if ($adddiscountamount == "" && $adddiscountamount_fi == "" && $adddiscountamount_no == "" && $adddiscountoutprice == "" && $adddiscountoutprice_fi == "" && $adddiscountoutprice_no == "") { ?>
		<tr>
		  <td>Rabatt %</td>
		  <td><input type="text" name="adddiscountpercent" size="10" style="background-color: #93E393" value="<?php echo $adddiscountpercent; ?>"><img border="0" src="sv_mini.jpg"></td>
		  <td>&nbsp;</td>
		  <td><input type="text" name="adddiscountpercent_fi" size="10" style="background-color: #66CCFF" value="<?php echo $adddiscountpercent_fi; ?>"><img border="0" src="fi_mini.jpg"></td>
		  <td><input type="text" name="adddiscountpercent_no" size="10" style="background-color: #FF6666" value="<?php echo $adddiscountpercent_no; ?>"><img border="0" src="no_mini.jpg"></td>
		</tr>
		<?php } ?>
		<?php if ($adddiscountpercent == "" && $adddiscountpercent_fi == "" && $adddiscountpercent_no == "" && $adddiscountoutprice == "" && $adddiscountoutprice_fi == "" && $adddiscountoutprice_no == "") { ?>
		<tr>
		  <td>Rabatt SEK/EUR/NOK</td>
		  <td><input type="text" name="adddiscountamount" size="10" style="background-color: #93E393" value="<?php echo $adddiscountamount; ?>"><img border="0" src="sv_mini.jpg"></td>
		  <td>&nbsp;</td>
		  <td><input type="text" name="adddiscountamount_fi" size="10" style="background-color: #66CCFF" value="<?php echo $adddiscountamount_fi; ?>"><img border="0" src="fi_mini.jpg"></td>
		  <td><input type="text" name="adddiscountamount_no" size="10" style="background-color: #FF6666" value="<?php echo $adddiscountamount_no; ?>"><img border="0" src="no_mini.jpg"></td>
		</tr>
		<?php } ?>
		<?php if ($adddiscountpercent == "" && $adddiscountpercent_fi == "" && $adddiscountpercent_no == "" && $adddiscountamount == "" && $adddiscountamount_fi == "" && $adddiscountamount_no == "") { ?>
		<!--
		<tr>
		  <td>Nytt pris SEK/EUR/NOK</td>
		  <td><input type="text" name="adddiscountoutprice" size="10" style="background-color: #93E393" value="<?php echo $adddiscountoutprice; ?>"><img border="0" src="/order/admin/sv_mini.jpg"></td>
		  <td>&nbsp;</td>
		  <td><input type="text" name="adddiscountoutprice_fi" size="10" style="background-color: #66CCFF" value="<?php echo $adddiscountoutprice_fi; ?>"><img border="0" src="/order/admin/fi_mini.jpg"></td>
		  <td><input type="text" name="adddiscountoutprice_no" size="10" style="background-color: #FF6666" value="<?php echo $adddiscountoutprice_no; ?>"><img border="0" src="/order/admin/no_mini.jpg"></td>
		</tr>
		-->
		<?php } ?>

		<?php } ?>

		<tr>
		  <td colspan="5"><hr noshade color="#000000" align="left" size="1"></td>
		</tr>
		<tr>
		  <td></td>
		  <td colspan="4"><span style="font-family: Arial; font-weight: bold; font-size: 22px; text-decoration: none;">NYTT! Summorna anges nu inkl. moms!</span></td>
		</tr>
		<tr>
		  <td colspan="5"><hr noshade color="#000000" align="left" size="1"></td>
		</tr>
		<tr>
		  <td>Kampanjtext</td>
		  <td colspan="4"><input type="text" name="addcampaigntext" size="58" value="<?php echo $addcampaigntext; ?>">&nbsp;<img border="0" src="sv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addcampaigntext_fi" size="58" value="<?php echo $addcampaigntext_fi; ?>">&nbsp;<img border="0" src="fi_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addcampaigntext_fi_sv" size="58" value="<?php echo $addcampaigntext_fi_sv; ?>">&nbsp;<img border="0" src="fisv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="6"><input type="text" name="addcampaigntext_no" size="58" value="<?php echo $addcampaigntext_no; ?>">&nbsp;<img border="0" src="no_mini.jpg"></td>
		</tr>
		<tr>
		  <td colspan="5"><hr noshade color="#000000" align="left" size="1"></td>
		</tr>
		<tr>
		  <td>Bildlänk</td>
		  <td colspan="4"><input type="text" name="addpicturelinc" size="58" value="<?php echo $addpicturelinc; ?>">&nbsp;<img border="0" src="sv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addpicturelinc_fi" size="58" value="<?php echo $addpicturelinc_fi; ?>">&nbsp;<img border="0" src="fi_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addpicturelinc_fi_sv" size="58" value="<?php echo $addpicturelinc_fi_sv; ?>">&nbsp;<img border="0" src="fisv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="6"><input type="text" name="addpicturelinc_no" size="58" value="<?php echo $addpicturelinc_no; ?>">&nbsp;<img border="0" src="no_mini.jpg"></td>
		</tr>
		<tr>
		  <td colspan="5"><hr noshade color="#000000" align="left" size="1"></td>
		</tr>
		<tr>
		  <td>Produktlänk</td>
		  <td colspan="4"><input type="text" name="addlinc" size="58" value="<?php echo $addlinc; ?>">&nbsp;<img border="0" src="sv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addlinc_fi" size="58" value="<?php echo $addlinc_fi; ?>">&nbsp;<img border="0" src="fi_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addlinc_fi_sv" size="58" value="<?php echo $addlinc_fi_sv; ?>">&nbsp;<img border="0" src="fisv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="6"><input type="text" name="addlinc_no" size="58" value="<?php echo $addlinc_no; ?>">&nbsp;<img border="0" src="no_mini.jpg"></td>
		</tr>
		<tr>
		  <td colspan="5"><hr noshade color="#000000" align="left" size="1"></td>
		</tr>
		<tr>
		  <td>Extern länk</td>
		  <td colspan="4"><input type="text" name="addexternallinc" size="58" value="<?php echo $addexternallinc; ?>">&nbsp;<img border="0" src="sv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addexternallinc_fi" size="58" value="<?php echo $addexternallinc_fi; ?>">&nbsp;<img border="0" src="fi_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="4"><input type="text" name="addexternallinc_fi_sv" size="58" value="<?php echo $addexternallinc_fi_sv; ?>">&nbsp;<img border="0" src="fisv_mini.jpg"></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="6"><input type="text" name="addexternallinc_no" size="58" value="<?php echo $addexternallinc_no; ?>">&nbsp;<img border="0" src="no_mini.jpg"></td>
		</tr>
		<tr>
		  <td colspan="5"><hr noshade color="#000000" align="left" size="1"></td>
		</tr>


		<tr>
		  <td>Visa bild</td>
		  <td><input type="checkbox" name="addshowpicture" value="yes" <?php if ($addshowpicture != "0" && $addshowpicture != "") { ?> checked <?php } ?>></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		</tr>
		<tr>
		  <td>Avser sida <b><font color="#FF0000">*</font></b></td>
		  <td colspan="4">
			<select size="1" name="addsite">
			<option></option>
			<?php $campaign->getSite(); ?>
			</select>
		  </td>
		</tr>
		<tr>
		  <td valign="top">Intern kommentar</td>
		  <td colspan="4"><textarea rows="4" name="addcomment" cols="47"><?php echo $addcomment; ?></textarea></td>
		</tr>
		<tr>
		  <td>Avisera mig</td>
		  <td colspan="4"><input type="checkbox" name="addnotify" value="yes" <?php if ($addnotify != 0) { ?> checked <?php } ?>>&nbsp;(ange klockslaget som aviseringen skall skickas nedan)</td>
		</tr>
		<tr>
		  <td>Avisera tidpunkt</td>
		  <td><input type="text" name="addnotifyfrom" size="20" value="<?php echo $addnotifyfrom; ?>"></td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		</tr>
		<?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") { ?>
		<?php } ?>
		<tr>
		  <td></td>
		  <td colspan="4">
		  <p><input type="submit" value="<?php if ($addid !="") { ?>Uppdatera kampanjen<?php } elseif ($addidc !="") {?>Kopiera post<?php } else { ?>Lägg till kampanjen<?php } ?>" name="skicka" class="button"></p>
		  </td>
		</tr>
		</table>
	</form>        
</div>
<?php
if ($addid !="") {
	// echo "<div class=\"left5\"><p><a href=\"" . $_SERVER['PHP_SELF'] . "?show=" . $addid . "&discountCode=" . $discountCode . "\">- Avbryt uppdateringen</a></p></div>\n"; 
	echo "<div class=\"left5\"><p><a href=\"" . $_SERVER['PHP_SELF'] . "?show=" . $addid . "\">- Avbryt uppdateringen</a></p></div>\n"; 
}
?>
</div>

<?php
if ($addid != "" && $addartikelnr == "" && $addkategori == "" && $addmanufacturer == "") { 
if ($addartnrincludedcount == "") {
		$addartnrincludedcount = 1;
}
?>
	<div class="framebox2">
	<h2>Kampanjen omfattar dessa artiklar</h2>
	<div class=top10>
	<?php
	$campaign->getCampaignArticle($addid);
	$addIncID = $campaign->getCampaignArticleInclID($addid);
	?>
	</div>
	</div>
	<div class="top10"></div>
	<div class="framebox2">
		<?php 
		if ($wrongmess2) {
			echo "<div class=\"wrongmess\"><ul>" . $wrongmess2 . "</ul></div>";
		}
		?>
		<form name="log">
		  <input type="hidden" value=true name="submArt">
		  <input type="hidden" value="<?php echo $change; ?>" name="change">
		  <input type="hidden" value="<?php echo $addIncID; ?>" name="oldIncID">
		  <table border="0" cellpadding="5" cellspacing="3">
			<tr>
			  <td>Lägg till artikel som skall omfattas i kampanjen</td>
			  <td><input type="text" name="addartnr" size="28" value="<?php echo $addartnr; ?>"></td>
			  <td><input type="submit" value="Lägg till produkt" name="skicka" class="button"></td>
			</tr>
		  </table>
		</form>

	</div>
	<hr noshade color="#000000" align="left" size="1">
	<?php
	if ($manualDelete == "YES") {
		echo "<div class=\"wrongmess\"><ul>Denna artikel kan endast tas bort av Sjabo eller Nils!</ul></div>";
	}
	?>
	<div class="top10"></div>
	<div class="framebox3">
	<h2>Vi skickar med desssa produkter</h2>
	<div class=top10>
	<?php
	$addIncID = $campaign->getCampaignArticleInclID($addid);
	// echo $addIncID;
	$campaign->getCampaignArticleIncluded($addIncID);
	?>
	</div>
	</div>
	<div class="top10"></div>
	<div class="framebox3">
		<?php 
		if ($wrongmess3) {
			echo "<div class=\"wrongmess\"><ul>" . $wrongmess3 . "</ul></div>";
		}
		?>
		<form name="log">
		  <input type="hidden" value=true name="submArtIncluded">
		  <input type="hidden" value="<?php echo $change; ?>" name="change">
		  <input type="hidden" value="<?php echo $addIncID; ?>" name="oldIncID">
		  <table border="0" cellpadding="5" cellspacing="0">
			<tr>
			  <td colspan="2">Lägg till artikel som skall skickas med på köpet</td>
			</tr>
			<tr>
			  <td>Antal</td>
			  <td><input type="text" name="addartnrincludedcount" size="1" value="<?php echo $addartnrincludedcount; ?>"></td>
			</tr>
			<tr>
			  <td>Artikel</td>
			  <td><input type="text" name="addartnrincluded" size="22" value="<?php echo $addartnrincluded; ?>"></td>
			</tr>
			<tr>
			  <td>Tillåt slut i lager</td>
			  <td><input type="checkbox" name="addno_store" value="yes" <?php if ($addno_store != "0" && $addno_store != "") { ?> checked <?php } ?>></td>
			</tr>
			<tr>
			  <td></td>
			  <td><input type="submit" value="Lägg till produkt" name="skicka" class="button"></td>
			</tr>
		  </table>
		</form>

	</div>
<?php } ?>