	<?php
	if ($_COOKIE['login_ok'] != "true") {
		echo "<div class=\"container_loggin\">\n";
		echo "<span class=\"not_loggin\">Du är Ej inloggad och kommer därför inte kunna utföra åtgärden!</span>\n";
		echo "</div>\n";
		echo "<div class=\"clear\"></div>\n";
	}
	?>
	<div style="margin-bottom:24px;">
	<form>
	<?php if ($addID != "") { ?>
		<input type="hidden" value="<?php echo $addID; ?>" name="addID">
		<input type="hidden" value="true" name="submC">
	<?php } else { ?>
		<input type="hidden" value="true" name="subm">
		<input type="hidden" value="yes" name="add">
	<?php } ?>
	<div style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:20px 24px;max-width:520px;">
		<?php if ($addID != "") { ?>
		<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
			<label style="width:130px;font-size:13px;font-weight:600;">Aktiv:</label>
			<input type="checkbox" name="addActive" value="yes" <?php if ($addActive == "-1" || $addActive == "yes") { ?>checked<?php } ?> style="width:16px;height:16px;accent-color:#0d9488;">
		</div>
		<?php } ?>
		<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
			<label style="width:130px;font-size:13px;font-weight:600;">Filter <span style="color:#ef4444;">*</span></label>
			<input type="text" name="addWord" size="40" value="<?php echo htmlspecialchars($addWord); ?>" style="flex:1;border:1px solid #d1d5db;border-radius:4px;padding:6px 10px;font-size:13px;outline:none;" onfocus="this.style.borderColor='#2dd4bf'" onblur="this.style.borderColor='#d1d5db'">
		</div>
		<div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:16px;">
			<label style="width:130px;font-size:13px;font-weight:600;padding-top:4px;">Egen notis:</label>
			<textarea rows="3" name="addComment" style="flex:1;border:1px solid #d1d5db;border-radius:4px;padding:6px 10px;font-size:13px;resize:vertical;outline:none;" onfocus="this.style.borderColor='#2dd4bf'" onblur="this.style.borderColor='#d1d5db'"><?php echo htmlspecialchars($addComment); ?></textarea>
		</div>
		<div style="display:flex;align-items:center;gap:12px;">
			<div style="width:130px;"></div>
			<button type="submit" name="skicka" style="padding:8px 20px;background:#0d9488;color:#fff;border:none;border-radius:5px;font-size:13px;font-weight:600;cursor:pointer;">
				<?php if ($addID != "") { ?>Uppdatera<?php } elseif ($addidc != "") { ?>Kopiera post<?php } else { ?>Lägg till<?php } ?>
			</button>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>" style="font-size:13px;color:#6b7280;text-decoration:none;">Avbryt</a>
		</div>
		<p style="margin:14px 0 0;font-size:12px;color:#6b7280;"><span style="color:#ef4444;">*</span> Obligatoriskt</p>
	</div>
	</form>
	</div>
