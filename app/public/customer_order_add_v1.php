<div class="framebox">
<div>
	<?php 
	if ($wrongmess) {
		echo "<div class=\"wrongmess\"><ul>" . $wrongmess . "</ul></div>";
	}
	?>
	<form>
	  <table border="0" cellpadding="1" cellspacing="3">
		<tr>
		  <td>Order nr <b><font color="#FF0000">*</font></b></td>
		  <td><input style="font-weight: bold" type="text" name="ordernr" size="20" value="<?php echo $ordernr; ?>"></td>
		  <td><input type="submit" value="Visa" class="button"></td>
		</tr>
	  </table>
	</form>        
</div>
</div>