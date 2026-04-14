<?php
session_start();

if ($_COOKIE['login_ok'] != 'true') {
    header('Location: index.php');
    exit;
}

$target_file = __DIR__ . '/banner_images/Begagnatlogg_Diagram_chart1.svg';
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['svg_file']) || $_FILES['svg_file']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Ingen fil valdes eller uppladdningsfel.';
        $message_type = 'error';
    } else {
        $file = $_FILES['svg_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($file['tmp_name']);

        if ($ext !== 'svg' || !in_array($mime, ['image/svg+xml', 'text/html', 'text/plain', 'text/xml', 'application/xml'])) {
            $message = 'Endast SVG-filer tillåtna.';
            $message_type = 'error';
        } else {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $message = 'Diagrammet är uppdaterat! (' . date('Y-m-d H:i:s') . ')';
                $message_type = 'success';
            } else {
                $message = 'Kunde inte spara filen. Kontrollera rättigheter på banner_images/.';
                $message_type = 'error';
            }
        }
    }
}

$last_modified = file_exists($target_file) ? date('Y-m-d H:i:s', filemtime($target_file)) : 'saknas';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Ladda upp begagnatdiagram</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 60px auto; padding: 0 20px; background: #f4f4f4; }
        h2 { color: #333; }
        .box { background: #fff; padding: 30px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,.15); }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        input[type=file] { display: block; margin-bottom: 20px; }
        button { background: #2e7d32; color: #fff; border: none; padding: 10px 24px; font-size: 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1b5e20; }
        .msg { margin-top: 20px; padding: 12px 16px; border-radius: 4px; }
        .success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .error   { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .meta { margin-top: 16px; font-size: 13px; color: #888; }
    </style>
</head>
<body>
<div class="box">
    <h2>Ladda upp begagnatdiagram</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="svg_file">Välj SVG-fil (Begagnatlogg_Diagram_chart1.svg)</label>
        <input type="file" id="svg_file" name="svg_file" accept=".svg">
        <button type="submit">Ladda upp</button>
    </form>
    <?php if ($message): ?>
        <div class="msg <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <p class="meta">Senast uppdaterad: <?= htmlspecialchars($last_modified) ?></p>
</div>
</body>
</html>
