<?php
if (($_COOKIE['login_ok'] ?? '') !== 'true') {
    echo '<p style="color:#dc2626;font-weight:600;">Du är ej inloggad och kan därför inte utföra åtgärden.</p>';
}

if ($addsort == "")     $addsort = 0;
if ($addcategory == "") $addcategory = 0;
if ($section == "")     $section = $_SESSION['bannersection'];

$formTitle = $addid ? 'Redigera post' : ($addidc ? 'Kopiera post' : 'Lägg till post');
$submitLabel = $addid ? 'Uppdatera' : ($addidc ? 'Kopiera post' : 'Lägg till');
?>

<div style="margin-top:12px;">
<h2><?= $formTitle ?></h2>

<form name="addbannerform" style="max-width:500px;">
    <?php if ($addid): ?>
        <input type="hidden" name="addid"  value="<?= (int)$addid ?>">
        <input type="hidden" name="submC" value="true">
    <?php else: ?>
        <input type="hidden" name="subm"   value="true">
        <input type="hidden" name="add"    value="yes">
        <input type="hidden" name="addidc" value="<?= htmlspecialchars($addidc, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <input type="hidden" name="addsection" value="<?= (int)$section ?>">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
        <div>
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px;">
                Gäller från <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="addfrom" class="select-modern"
                   value="<?= $addfrom ? htmlspecialchars($addfrom, ENT_QUOTES, 'UTF-8') : date('Y-m-d H:i:s') ?>"
                   style="width:100%;box-sizing:border-box;">
        </div>
        <div>
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px;">
                Gäller till <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="addto" class="select-modern"
                   value="<?= $addto ? htmlspecialchars($addto, ENT_QUOTES, 'UTF-8') : date('Y-m-d 23:59:59', mktime(0,0,0,date('n')+1,0,date('Y'))) ?>"
                   style="width:100%;box-sizing:border-box;">
        </div>
    </div>

    <div style="margin-bottom:20px;">
        <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;">
            Bild <span style="color:#dc2626;">*</span>
        </label>

        <input type="hidden" name="addpicture" id="addpicture"
               value="<?= htmlspecialchars($addpicture, ENT_QUOTES, 'UTF-8') ?>">

        <?php
        $prevSrc = '';
        if (!empty($addpicture)) {
            $p = $addpicture;
            $prevSrc = (strncmp($p,'/',1)===0 || strncmp($p,'http',4)===0)
                ? htmlspecialchars($p, ENT_QUOTES, 'UTF-8')
                : '/start3/' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
        }
        ?>
        <div id="banner-preview-wrap" style="<?= $prevSrc ? '' : 'display:none;' ?>margin-bottom:8px;">
            <img id="banner-preview" src="<?= $prevSrc ?>" alt="Förhandsgranskning"
                 style="max-height:120px;max-width:100%;border:1px solid #d1d5db;border-radius:6px;">
        </div>

        <label class="btn-ghost btn" for="banner-file-input"
               style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;padding:7px 14px;border-radius:6px;font-size:13px;font-weight:600;border:1px solid #d1d5db;background:#f9fafb;color:#374151;text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
            </svg>
            Välj bild
        </label>
        <input type="file" id="banner-file-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;">
        <span id="banner-upload-status" style="margin-left:8px;font-size:12px;color:#6b7280;"></span>
        <div style="margin-top:14px;font-size:11px;color:#6b7280;">För att passa våra TV-skärmar i butiken ska storleken vara: <b>1920 x 1044 px</b></div>
    </div>

    <button type="submit" style="padding:8px 20px;background:#2563eb;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer;">
        <?= $submitLabel ?>
    </button>
</form>
</div>

<script>
(function () {
    var fileInput   = document.getElementById('banner-file-input');
    var hiddenPath  = document.getElementById('addpicture');
    var preview     = document.getElementById('banner-preview');
    var previewWrap = document.getElementById('banner-preview-wrap');
    var status      = document.getElementById('banner-upload-status');
    if (!fileInput) return;

    fileInput.addEventListener('change', function () {
        var file = this.files[0];
        if (!file) return;
        status.style.color = '#6b7280';
        status.textContent = 'Laddar upp…';

        var formData = new FormData();
        formData.append('banner_image', file);

        fetch('banner_upload.php', { method: 'POST', body: formData })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.error) {
                    status.style.color = '#dc2626';
                    status.textContent = data.error;
                    return;
                }
                hiddenPath.value = data.path;
                preview.src = data.path;
                previewWrap.style.display = 'block';
                status.style.color = '#059669';
                status.textContent = '✓ ' + file.name;
            })
            .catch(function () {
                status.style.color = '#dc2626';
                status.textContent = 'Uppladdningen misslyckades.';
            });
    });
}());
</script>
