<?php
// telavox_import.php
// Importera Telavox köexport (CSV). Flöde: välj kö + fil -> förhandsgranskning
// -> definitiv import. Kön väljs alltid av användaren och gäller alla samtal i filen.

include_once("top.php");
include_once("telavox_common.php");

const TVX_MAX_UPLOAD_BYTES = 10485760; // 10 MB

/** Ta bort en uppladdad fil som väntar på bekräftelse. */
function tvx_discard_upload()
{
    if (!empty($_SESSION['telavox_upload']['path']) && is_file($_SESSION['telavox_upload']['path'])) {
        @unlink($_SESSION['telavox_upload']['path']);
    }
    unset($_SESSION['telavox_upload']);
}

$error    = null;
$preview  = null;   // sätts när en fil ska förhandsgranskas
$queueId  = (int)($_POST['queue_id'] ?? 0);
$action   = $_SERVER['REQUEST_METHOD'] === 'POST' ? (string)($_POST['action'] ?? '') : '';

try {
    if ($action === 'upload') {
        tvx_discard_upload();
        $queue = CTelavoxRepository::getQueue($queueId);
        $file  = $_FILES['csv_file'] ?? null;

        if (!$queue) {
            $error = 'Välj vilken kö filen gäller.';
        } elseif (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $error = 'Välj en CSV-fil att importera.';
        } elseif ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > TVX_MAX_UPLOAD_BYTES) {
            $error = 'Filen är för stor.';
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Uppladdningen misslyckades (felkod ' . (int)$file['error'] . ').';
        } elseif (!in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), array('csv', 'txt'), true)) {
            $error = 'Endast CSV-filer kan importeras. Exportera som CSV från Telavox (inte Excel).';
        } else {
            $parsed = CTelavoxCsvParser::parse((string)file_get_contents($file['tmp_name']), $file['name']);
            if ($parsed['fatal'] !== null) {
                $error = $parsed['fatal'];
            } else {
                $path = sys_get_temp_dir() . '/telavox_' . bin2hex(random_bytes(16)) . '.csv';
                if (!move_uploaded_file($file['tmp_name'], $path)) {
                    $error = 'Kunde inte spara den uppladdade filen tillfälligt.';
                } else {
                    $_SESSION['telavox_upload'] = array(
                        'token'    => bin2hex(random_bytes(16)),
                        'path'     => $path,
                        'filename' => basename($file['name']),
                        'queue_id' => $queueId,
                    );
                    $preview = array('queue' => $queue, 'parsed' => $parsed);
                }
            }
        }
    } elseif ($action === 'cancel') {
        tvx_discard_upload();
        tvx_redirect('telavox_import.php');
    } elseif ($action === 'confirm') {
        $upload = $_SESSION['telavox_upload'] ?? null;
        if (!$upload || !hash_equals($upload['token'], (string)($_POST['token'] ?? '')) || !is_file($upload['path'])) {
            tvx_discard_upload();
            $error = 'Förhandsgranskningen har gått ut. Ladda upp filen igen.';
        } else {
            $parsed = CTelavoxCsvParser::parse((string)file_get_contents($upload['path']), $upload['filename']);

            // Användarens val för okända anknytningar: ny användare går före vald befintlig
            $mappings = array();
            foreach ((array)($_POST['map'] ?? array()) as $ext => $agentId) {
                $newName = trim((string)($_POST['newname'][$ext] ?? ''));
                if ($newName !== '') {
                    $mappings[$ext] = array('new_name' => mb_substr($newName, 0, 100));
                } elseif ((int)$agentId > 0) {
                    $mappings[$ext] = array('agent_id' => (int)$agentId);
                }
            }

            $result = CTelavoxImporter::import(
                (int)$upload['queue_id'], $parsed, $upload['filename'], $mappings, (string)($_COOKIE['login_mail'] ?? '')
            );
            tvx_discard_upload();
            if ($result['ok']) {
                tvx_flash('ok', $result['message']);
                tvx_redirect('telavox_imports.php');
            }
            $error   = $result['message'];
            $queueId = (int)$upload['queue_id'];
        }
    }

    if ($preview !== null) {
        $preview['analysis'] = CTelavoxImporter::analyze($queueId, $preview['parsed']);
    }
} catch (\Throwable $e) {
    error_log('telavox_import: ' . $e->getMessage());
    tvx_discard_upload();
    $preview = null;
    $error = 'Ett oväntat fel inträffade. Inget har importerats.';
}

include_once("header.php");
tvx_page_start('import', 'Importera Telavox-statistik');

if ($error !== null) {
    echo '<div class="tvx-msg error">' . tvx_h($error) . '</div>';
}

if ($preview !== null):
    // ------------------------------------------------------------ Förhandsgranskning
    $queue    = $preview['queue'];
    $parsed   = $preview['parsed'];
    $analysis = $preview['analysis'];
    $agents   = CTelavoxRepository::getAgents(true);

    $blockers = array();
    if ($analysis['duplicate']) {
        $blockers[] = CTelavoxImporter::duplicateMessage($analysis['duplicate']);
    }
    if (!empty($parsed['errors'])) {
        $blockers[] = 'Filen innehåller rader som inte kan tolkas. Rätta filen eller exportera den på nytt.';
    }
    if (!$analysis['duplicate'] && $analysis['new_count'] === 0) {
        $blockers[] = 'Alla samtal i filen finns redan importerade för kön.';
    }

    $warnings = array();
    if ($parsed['queue_name'] !== null && mb_strtolower($parsed['queue_name']) !== mb_strtolower($queue['name'])) {
        $warnings[] = 'Filen anger kön "' . $parsed['queue_name'] . '" men du har valt "' . $queue['name'] . '". Kontrollera att rätt kö är vald.';
    }
    if ($parsed['telavox_queue_id'] !== null && (string)$queue['telavox_queue_id'] !== '' && $parsed['telavox_queue_id'] !== (string)$queue['telavox_queue_id']) {
        $warnings[] = 'Filnamnet har Telavox kö-id ' . $parsed['telavox_queue_id'] . ' men vald kö har ' . $queue['telavox_queue_id'] . '.';
    }
?>

<?php foreach ($blockers as $msg): ?>
  <div class="tvx-msg error"><?php echo tvx_h($msg); ?></div>
<?php endforeach; ?>
<?php foreach ($warnings as $msg): ?>
  <div class="tvx-msg warn"><?php echo tvx_h($msg); ?></div>
<?php endforeach; ?>
<?php if (!empty($parsed['errors'])): ?>
  <div class="tvx-msg error">
    Fel i filen:
    <ul><?php foreach ($parsed['errors'] as $msg) { echo '<li>' . tvx_h($msg) . '</li>'; } ?></ul>
  </div>
<?php endif; ?>

<h2>Förhandsgranskning</h2>
<table class="table-list">
  <tbody>
    <tr><th>Vald kö</th><td><b><?php echo tvx_h($queue['name']); ?></b></td></tr>
    <tr><th>Fil</th><td><?php echo tvx_h($_SESSION['telavox_upload']['filename']); ?></td></tr>
    <tr><th>Kö enligt filen</th><td><?php echo $parsed['queue_name'] !== null ? tvx_h($parsed['queue_name']) : '<span class="muted">anges inte</span>'; ?></td></tr>
    <tr><th>Period</th><td><?php echo tvx_h($parsed['period_from']); ?> &ndash; <?php echo tvx_h($parsed['period_to']); ?></td></tr>
    <tr><th>Datarader i filen</th><td><?php echo tvx_number($parsed['row_count']); ?></td></tr>
    <tr><th>Samtal i filen</th><td><?php echo tvx_number(count($parsed['calls'])); ?></td></tr>
    <tr><th>In-/utloggningsrader</th><td><?php echo tvx_number($parsed['event_count']); ?> <span class="muted">(importeras inte)</span></td></tr>
    <tr><th>Redan importerade samtal</th><td><?php echo tvx_number($analysis['existing_count']); ?> <span class="muted">(hoppas över)</span></td></tr>
    <tr><th>Nya samtal att importera</th><td><b><?php echo tvx_number($analysis['new_count']); ?></b></td></tr>
    <tr><th>Okända anknytningar</th><td><?php echo count($analysis['unknown_extensions']); ?></td></tr>
  </tbody>
</table>

<?php if (!empty($analysis['outcomes'])): ?>
<h2>Avslutsstatus</h2>
<table class="table-list">
  <thead><tr><th>Kod</th><th>Betydelse</th><th>Räknas som</th><th class="num">Samtal</th></tr></thead>
  <tbody>
  <?php foreach ($analysis['outcomes'] as $code => $count): ?>
    <tr>
      <td><?php echo tvx_h($code); ?></td>
      <td><?php echo tvx_h(CTelavoxOutcome::label($code)); ?></td>
      <td><?php echo tvx_h(CTelavoxOutcome::classLabel(CTelavoxOutcome::classOf($code))); ?></td>
      <td class="num"><?php echo tvx_number($count); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<form method="post">
  <input type="hidden" name="token" value="<?php echo tvx_h($_SESSION['telavox_upload']['token']); ?>">

  <?php if (!empty($analysis['extensions'])): ?>
  <h2>Anknytningar</h2>
  <?php if (!empty($analysis['unknown_extensions'])): ?>
  <p class="tvx-sub">
    Okända anknytningar kan kopplas till en befintlig användare eller en ny. Kopplingen sparas och
    känns igen vid kommande importer. Lämnas en anknytning okopplad importeras samtalen ändå, utan användare,
    och kan kopplas i efterhand under Användare.
  </p>
  <?php endif; ?>
  <table class="table-list">
    <thead><tr><th>Anknytning</th><th class="num">Samtal</th><th>Användare</th></tr></thead>
    <tbody>
    <?php foreach ($analysis['extensions'] as $ext => $info): $ext = (string)$ext; ?>
      <tr>
        <td><?php echo tvx_h($ext); ?></td>
        <td class="num"><?php echo tvx_number($info['calls']); ?></td>
        <td>
          <?php if ($info['unknown_calls'] === 0): ?>
            <?php echo tvx_h($info['agent_name']); ?>
          <?php else: ?>
            <span class="text-negative">Okänd anknytning: <?php echo tvx_h($ext); ?></span>
            <?php if ($info['agent_name'] !== null): ?>
              <span class="muted">(<?php echo (int)$info['unknown_calls']; ?> samtal utanför perioden för <?php echo tvx_h($info['agent_name']); ?>)</span>
            <?php endif; ?>
            <br>
            <select name="map[<?php echo tvx_h($ext); ?>]">
              <option value="">Importera utan användare</option>
              <?php foreach ($agents as $a): ?>
              <option value="<?php echo (int)$a['id']; ?>"><?php echo tvx_h($a['name']); ?></option>
              <?php endforeach; ?>
            </select>
            eller ny användare:
            <input type="text" name="newname[<?php echo tvx_h($ext); ?>]" maxlength="100" placeholder="Namn">
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <p style="margin-top:18px">
    <?php if (empty($blockers)): ?>
    <button type="submit" name="action" value="confirm" class="tvx-btn">Importera <?php echo tvx_number($analysis['new_count']); ?> samtal till <?php echo tvx_h($queue['name']); ?></button>
    <?php endif; ?>
    <button type="submit" name="action" value="cancel" class="tvx-btn secondary" formnovalidate>Avbryt</button>
  </p>
</form>

<?php
else:
    // --------------------------------------------------------------- Uppladdning
    $queues = CTelavoxRepository::getQueues(true);
?>

<?php if (empty($queues)): ?>
  <p>Det finns inga aktiva köer. <a href="telavox_queues.php">Lägg upp en kö</a> innan du importerar.</p>
<?php else: ?>
<p class="tvx-sub">
  Exportera rådata för en kö från Telavox som CSV. Alla samtal i filen kopplas till den kö du väljer här.
  Du får se en förhandsgranskning innan något sparas.
</p>

<form method="post" enctype="multipart/form-data" class="tvx-card" id="tvx-import-form">
  <div class="tvx-card-title">Välj kö och fil</div>
  <input type="hidden" name="action" value="upload">
  <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo TVX_MAX_UPLOAD_BYTES; ?>">
  <div class="tvx-row">
    <label for="tvx-queue">Kö</label>
    <select id="tvx-queue" name="queue_id" required>
      <option value="">Välj kö</option>
      <?php foreach ($queues as $q): ?>
      <option value="<?php echo (int)$q['id']; ?>" data-tqid="<?php echo tvx_h($q['telavox_queue_id']); ?>"<?php if ((int)$q['id'] === $queueId) { ?> selected<?php } ?>><?php echo tvx_h($q['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="tvx-row" style="align-items:flex-start">
    <label for="tvx-file">CSV-fil</label>
    <div>
      <div id="tvx-drop" style="border:2px dashed #d1d5db;border-radius:8px;padding:22px 18px;text-align:center;width:330px;box-sizing:border-box;color:#6b7280;font-size:13px">
        <div id="tvx-drop-text">Dra hit en fil eller</div>
        <input type="file" id="tvx-file" name="csv_file" accept=".csv,.txt,text/csv" required style="margin-top:8px">
      </div>
    </div>
  </div>
  <button type="submit" class="tvx-btn">Importera</button>
</form>

<script>
(function(){
  var drop = document.getElementById('tvx-drop');
  var file = document.getElementById('tvx-file');
  var queue = document.getElementById('tvx-queue');
  var text = document.getElementById('tvx-drop-text');

  // Förvälj kö utifrån Telavox kö-id i filnamnet (queue_<id>_...), om ingen kö är vald
  function fileChosen() {
    if (!file.files.length) { return; }
    var name = file.files[0].name;
    text.textContent = name;
    var m = /^queue_(\d+)_/i.exec(name);
    if (m && !queue.value) {
      for (var i = 0; i < queue.options.length; i++) {
        if (queue.options[i].getAttribute('data-tqid') === m[1]) { queue.selectedIndex = i; break; }
      }
    }
  }
  file.addEventListener('change', fileChosen);

  ['dragenter', 'dragover'].forEach(function(ev){
    drop.addEventListener(ev, function(e){ e.preventDefault(); drop.style.borderColor = '#0d9488'; });
  });
  ['dragleave', 'drop'].forEach(function(ev){
    drop.addEventListener(ev, function(e){ e.preventDefault(); drop.style.borderColor = '#d1d5db'; });
  });
  drop.addEventListener('drop', function(e){
    if (e.dataTransfer && e.dataTransfer.files.length) {
      file.files = e.dataTransfer.files;
      fileChosen();
    }
  });
})();
</script>
<?php endif; ?>

<?php
endif;

echo "</div>";
include_once("footer.php");
