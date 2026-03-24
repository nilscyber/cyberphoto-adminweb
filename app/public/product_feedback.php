<?php
include_once("top.php");
include_once("Db.php");
include_once("header.php");

echo "<h1>Missad försäljning</h1>";

// ====== Parametrar ======
$artnr = isset($_POST['artnr'])
    ? $_POST['artnr']
    : (isset($_GET['artnr']) ? $_GET['artnr'] : '');

$ordernr = isset($_POST['ordernr'])
    ? trim($_POST['ordernr'])
    : (isset($_GET['ordernr']) ? trim($_GET['ordernr']) : '');

$reason_id = isset($_GET['reason_id']) ? (int)$_GET['reason_id'] : 0;
$details   = isset($_GET['details'])   ? 1 : 0;
$popup     = isset($_GET['popup'])     ? 1 : 0;

// Backwards-compat: show=art ska tolkas som details=1
if (!$details && isset($_GET['show']) && $_GET['show'] === 'art') {
    $details = 1;
}

// ====== 1) Statistik för vald orsak ======
if ($reason_id > 0 && !$details) {

    echo $statistics->getArticlesForReason($reason_id);

// ====== 2) Detaljvy för artikel (?artnr=...&details=1) ======
} elseif ($details && $artnr !== '') {

    echo $statistics->getReportForArticle($artnr);

// ====== 3) Rapportformulär för artikel (?artnr=...) ======
} elseif ($artnr !== '') {

    if (!isset($_COOKIE['login_ok']) || $_COOKIE['login_ok'] !== "true") {
        echo "<p style='color: red;'>Du måste vara inloggad för att kunna rapportera missad försäljning.</p>";

    } else {
        $errors    = array();
        $submitted = false;

        // --- POST-hantering (sammanfattad) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $conn       = Db::getConnection(true);
            $email      = mysqli_real_escape_string($_COOKIE['login_mail']);
            $artnr_in   = mysqli_real_escape_string($artnr);

            $ordernr_raw = isset($_POST['ordernr']) ? trim($_POST['ordernr']) : '';
            $ordernr_sql = ($ordernr_raw === '' || $ordernr_raw === '0')
                ? 'NULL'
                : (string)((int)$ordernr_raw);

            $reason_in  = isset($_POST['reason_id']) ? (int)$_POST['reason_id'] : 0;
            $notes      = isset($_POST['notes']) ? mysqli_real_escape_string($_POST['notes']) : '';

            if ($reason_in === 0) {
                $errors[] = "Du måste välja en orsak.";
            }

            // Dubblettkoll bara om ordernummer finns
            if (empty($errors) && $ordernr_sql !== 'NULL') {
                $sql_check = "
                    SELECT COUNT(*) AS antal
                    FROM product_feedback
                    WHERE artnr = '$artnr_in'
                      AND ordernr = $ordernr_sql
                ";
                $res_check = mysqli_query($conn, $sql_check);
                $row_check = mysqli_fetch_assoc($res_check);
                if ((int)$row_check['antal'] > 0) {
                    $errors[] = "Denna produkt/order är redan rapporterad.";
                }
            }

            if (empty($errors)) {
                $sql = "
                    INSERT INTO product_feedback
                        (artnr, ordernr, user_email, reason_id, notes)
                    VALUES
                        ('$artnr_in', $ordernr_sql, '$email', $reason_in, '$notes')
                ";
                mysqli_query($conn, $sql);

                echo "<p style='color: green; font-weight: bold;'>Tack! Rapporten är registrerad.</p>";

                if ($popup) {
                    echo "<script>setTimeout(function() { window.close(); }, 1000);</script>";
                }

                $submitted = true;
            } else {
                foreach ($errors as $err) {
                    echo "<p style='color: red;'>" . htmlspecialchars($err) . "</p>";
                }
            }
        }

        // --- Formulär om ej inskickat OK ---
        if (!$submitted) {
            ?>
			<form method="post">
				<input type="hidden" name="artnr" value="<?php echo htmlspecialchars($artnr); ?>">

				<p><strong>Rapportör:</strong>
					<?php echo htmlspecialchars($_COOKIE['login_name']); ?>
				</p>

				<?php
				// Om vi kommer från en orderlänk: visa ordernumret låst + skicka vidare som hidden
				if ($ordernr !== '') {
					echo "<p><strong>Ordernummer:</strong> " . htmlspecialchars($ordernr) . "</p>";
					echo '<input type="hidden" name="ordernr" value="' . htmlspecialchars($ordernr) . '">';
				} else {
					// Inget ordernr i länken ? låt användaren skriva in det manuellt (valfritt)
					$ordVal = isset($_POST['ordernr']) ? $_POST['ordernr'] : '';
					echo '<p><label><strong>Ordernummer (valfritt):</strong><br>';
					echo '<input type="text" name="ordernr" value="' . htmlspecialchars($ordVal) . '" size="12">';
					echo '</label></p>';
				}
				?>

				<p>Välj en orsak:</p>
				<?php
				$conn    = Db::getConnection(false);
				$options = mysqli_query($conn, "SELECT id, label FROM product_feedback_reasons ORDER BY id");
				while ($opt = mysqli_fetch_assoc($options)) {
					$id      = (int)$opt['id'];
					$label   = htmlspecialchars($opt['label']);
					$checked = (isset($_POST['reason_id']) && $_POST['reason_id'] == $id)
						? ' checked'
						: '';
					echo "<label><input type='radio' name='reason_id' value='$id'$checked> $label</label><br>";
				}
				?>

				<br>
				<label>Övrig info (frivilligt):<br>
					<textarea name="notes" rows="4" cols="40"><?php
						echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '';
					?></textarea>
				</label><br><br>

				<button type="submit">Skicka rapport</button>
			</form>
            <?php
        }
    }

// ====== 4) Huvudstatistik ======
} else {

    echo $statistics->getReportByReason();
    echo $statistics->getDailyReportByArticle(24);
    echo $statistics->getReportByUser();
}

include_once("footer.php");
?>
