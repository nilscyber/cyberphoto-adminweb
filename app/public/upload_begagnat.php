<?php
$target_file = __DIR__ . '/banner_images/Begagnatlogg_Diagram_chart1.svg';
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['svg_file']) || $_FILES['svg_file']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Ingen fil valdes eller uppladdningsfel.';
        $message_type = 'error';
    } else {
        $file = $_FILES['svg_file'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($ext !== 'svg') {
            $message = 'Endast SVG-filer tillåtna.';
            $message_type = 'error';
        } elseif (move_uploaded_file($file['tmp_name'], $target_file)) {
            $message = 'Diagrammet är uppdaterat! (' . date('Y-m-d H:i:s') . ')';
            $message_type = 'success';
        } else {
            $message = 'Kunde inte spara filen. Kontrollera rättigheter på banner_images/.';
            $message_type = 'error';
        }
    }
}

include_once("top.php");
include_once("header.php");

$last_modified = file_exists($target_file) ? date('Y-m-d H:i:s', filemtime($target_file)) : 'saknas';
?>

<h2>Ladda upp begagnatdiagram</h2>

<form method="post" enctype="multipart/form-data" style="margin-bottom:16px;">
    <label style="display:block;margin-bottom:8px;font-weight:bold;">Välj SVG-fil (Begagnatlogg_Diagram_chart1.svg)</label>
    <input type="file" name="svg_file" accept=".svg" style="margin-bottom:12px;">
    <br>
    <input type="submit" value="Ladda upp" class="btn_green">
</form>

<?php if ($message): ?>
    <p class="<?= $message_type === 'success' ? 'mark_green' : 'mark_red' ?> bold">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<p class="gray italic">Senast uppdaterad: <?= htmlspecialchars($last_modified) ?></p>

<?php include_once("footer.php"); ?>
