<div id="box_contact_cust">

<div class="contact_cust_info"><?php echo l('We will help you!'); ?></div>

<?php
if ($abb_contact_mess != "") {
	echo "<div class=\"contact_cust_mess bold\">" . $abb_contact_mess . "</div>\n";
} else {
?>
<div class="contact_cust_t"><?php echo l('Send us your phone number and we will call you'); ?></div>
<form method="post">
<input type="hidden" name="article" value="<?php echo $article; ?>">
<div class="left10 floatleft"><input class="contact_cust_f" type="text" name="contact_phone" size="35" placeholder="<?php echo l('Example: 07012345678'); ?>" onkeypress="return numbersonly(this, event)" maxlength="12"></div>
<div class="left10 floatleft"><input class="contact_cust_s" type="submit" value="<?php echo l('Send'); ?>"></div>
</form>
<?php } ?>
<div class="clear"></div>

</div>