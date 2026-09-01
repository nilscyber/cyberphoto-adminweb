<?php
	include_once("top.php");
	include_once("header.php");
	include_once(__DIR__ . "/../lib/CCronMailRecipients.php");

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	$savedKey = '';
	$errorMsg = '';

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_job_key'])) {
		$jobKey = (string)$_POST['save_job_key'];
		$to     = trim((string)($_POST['to_emails'] ?? ''));
		$cc     = trim((string)($_POST['cc_emails'] ?? ''));
		$bcc    = trim((string)($_POST['bcc_emails'] ?? ''));
		$who    = (string)($_COOKIE['login_mail'] ?? '');

		$ok = CCronMailRecipients::save($jobKey, $to, $cc, $bcc, $who);
		if ($ok) {
			$savedKey = $jobKey;
		} else {
			$errorMsg = "Kunde inte spara mottagare för \"" . htmlspecialchars($jobKey) . "\".";
		}
	}

	$jobs = CCronMailRecipients::getAllJobs();
?>
<style>
.cmr-card {
	background: #fff;
	border: 1px solid #e5e7eb;
	border-radius: 8px;
	box-shadow: 0 1px 4px rgba(0,0,0,.07);
	padding: 18px 22px;
	margin-bottom: 16px;
}
.cmr-card .cmr-title {
	font-size: 14px;
	font-weight: 700;
	color: #111;
	margin: 0 0 2px;
}
.cmr-card .cmr-file {
	font-size: 11px;
	color: #6b7280;
	margin-bottom: 14px;
}
.cmr-row {
	display: flex;
	align-items: flex-start;
	gap: 12px;
	margin-bottom: 10px;
}
.cmr-row label {
	flex: 0 0 60px;
	font-size: 13px;
	font-weight: 600;
	color: #374151;
	padding-top: 6px;
}
.cmr-row input[type="text"] {
	flex: 1;
	font-size: 13px;
	font-family: Arial, sans-serif;
	padding: 5px 8px;
	border: 1px solid #d1d5db;
	border-radius: 4px;
	color: #111;
	background: #fff;
	box-sizing: border-box;
}
.cmr-row input[type="text"]:focus {
	outline: none;
	border-color: #2dd4bf;
	box-shadow: 0 0 0 2px rgba(45,212,191,.18);
}
.cmr-actions {
	margin-top: 12px;
	padding-top: 12px;
	border-top: 1px solid #e5e7eb;
}
.cmr-btn-save {
	font-size: 13px;
	font-weight: 700;
	font-family: Arial, sans-serif;
	padding: 6px 18px;
	border: none;
	border-radius: 5px;
	background: #0d9488;
	color: #fff;
	cursor: pointer;
}
.cmr-btn-save:hover { background: #0f766e; }
.cmr-saved-msg {
	color: #0d9488;
	font-size: 12px;
	font-weight: 600;
	margin-left: 10px;
}
</style>

<h1>Mottagare för cronjobb-mail</h1>
<p style="color:#6b7280;font-size:13px;max-width:800px;">
	Här styrs vilka som får de automatiska mailutskicken från cronjobben.
	Flera adresser separeras med komma. Lämna ett fält tomt för att inte skicka
	på det sättet (Cc/Bcc är valfria). Ändringar gäller från nästa körning av
	respektive jobb.
</p>

<?php if ($errorMsg !== ''): ?>
	<div class="wrongmess"><?php echo $errorMsg; ?></div>
<?php endif; ?>

<?php foreach ($jobs as $job): ?>
	<div class="cmr-card">
		<div class="cmr-title"><?php echo htmlspecialchars($job['label']); ?></div>
		<div class="cmr-file"><?php echo htmlspecialchars($job['file']); ?></div>
		<form method="post">
			<input type="hidden" name="save_job_key" value="<?php echo htmlspecialchars($job['job_key']); ?>">

			<div class="cmr-row">
				<label>To</label>
				<input type="text" name="to_emails" value="<?php echo htmlspecialchars($job['to']); ?>">
			</div>
			<div class="cmr-row">
				<label>Cc</label>
				<input type="text" name="cc_emails" value="<?php echo htmlspecialchars($job['cc']); ?>">
			</div>
			<div class="cmr-row">
				<label>Bcc</label>
				<input type="text" name="bcc_emails" value="<?php echo htmlspecialchars($job['bcc']); ?>">
			</div>

			<div class="cmr-actions">
				<button type="submit" class="cmr-btn-save">Spara</button>
				<?php if ($savedKey === $job['job_key']): ?>
					<span class="cmr-saved-msg">Sparat</span>
				<?php endif; ?>
			</div>
		</form>
	</div>
<?php endforeach; ?>

<?php include_once("footer.php"); ?>
