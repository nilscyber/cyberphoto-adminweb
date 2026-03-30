<?php
/**
 * AJAX-endpoint för bilduppladdning till banners.
 * Returnerar JSON: { "path": "/banner_images/abc123.jpg" }
 *                 { "error": "Felmeddelande" }
 */
session_start();

header('Content-Type: application/json; charset=utf-8');

// Måste vara inloggad
if (($_COOKIE['login_ok'] ?? '') !== 'true') {
    http_response_code(403);
    echo json_encode(['error' => 'Du måste vara inloggad.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['banner_image'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Ogiltig förfrågan.']);
    exit;
}

$file = $_FILES['banner_image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE   => 'Filen är för stor (server-gräns).',
        UPLOAD_ERR_FORM_SIZE  => 'Filen är för stor.',
        UPLOAD_ERR_PARTIAL    => 'Filen laddades bara upp delvis.',
        UPLOAD_ERR_NO_FILE    => 'Ingen fil valdes.',
        UPLOAD_ERR_NO_TMP_DIR => 'Temporär mapp saknas.',
        UPLOAD_ERR_CANT_WRITE => 'Kunde inte skriva till disk.',
    ];
    $msg = $uploadErrors[$file['error']] ?? 'Okänt uppladdningsfel.';
    http_response_code(400);
    echo json_encode(['error' => $msg]);
    exit;
}

// Validera filtyp via mime (inte bara filändelsen)
$allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);

if (!in_array($mime, $allowedMime, true)) {
    http_response_code(415);
    echo json_encode(['error' => 'Endast JPG, PNG, GIF och WebP är tillåtna.']);
    exit;
}

// Max 10 MB
if ($file['size'] > 10 * 1024 * 1024) {
    http_response_code(413);
    echo json_encode(['error' => 'Filen får inte vara större än 10 MB.']);
    exit;
}

$ext       = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'][$mime];
$uploadDir = __DIR__ . '/banner_images/';
$filename  = bin2hex(random_bytes(16)) . '.' . $ext;
$destPath  = $uploadDir . $filename;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Kunde inte spara filen.']);
    exit;
}

echo json_encode(['path' => '/banner_images/' . $filename]);
