<div id="box_contact_cust">

<div class="contact_cust_info">Vi hjälper dig hitta rätt!</div>

<?php
if ($abb_contact_mess != "") {
	echo "<div class=\"contact_cust_mess bold\">" . $abb_contact_mess . "</div>\n";
} else {
?>
<div class="contact_cust_t">Skicka oss ditt telefonnummer så ringer vi upp dig</div>
<form method="post">
<input type="hidden" name="article" value="<?php echo $article; ?>">
<div class="left5 floatleft"><input class="contact_cust_f" type="text" name="contact_phone" size="35" placeholder="Exempel: 0701234578" onkeypress="return numbersonly(this, event)" maxlength="12"></div>
<div class="left5 floatleft"><input class="contact_cust_s" type="submit" value="Skicka"></div>
</form>
<?php } ?>
<div class="clear"></div>

</div>