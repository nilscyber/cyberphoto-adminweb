<?php
// telavox_agents.php
// Administrera användare (agenter) för Telavox-statistiken och deras anknytningar.
// En anknytning kopplas till en användare under en period, så att historiken
// stämmer även om anknytningen senare tas över av någon annan.

include_once("top.php");
include_once("telavox_common.php");

$editId = (int)($_GET['edit'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = (string)($_POST['action'] ?? '');
    $agentId = (int)($_POST['agent_id'] ?? 0);
    $back    = 'telavox_agents.php' . ($agentId > 0 ? '?edit=' . $agentId : '');

    // Tolka giltighetsperiod från formuläret. Tomt från-datum = "från början".
    $validFrom = trim((string)($_POST['valid_from'] ?? ''));
    $validTo   = trim((string)($_POST['valid_to'] ?? ''));
    $validFrom = $validFrom === '' ? CTelavoxImporter::DEFAULT_VALID_FROM : $validFrom;
    $validTo   = $validTo === '' ? null : $validTo;
    $periodError = null;
    if (!tvx_valid_date($validFrom) || ($validTo !== null && !tvx_valid_date($validTo))) {
        $periodError = 'Ogiltigt datum. Använd formatet ÅÅÅÅ-MM-DD.';
    } elseif ($validTo !== null && $validTo < $validFrom) {
        $periodError = 'Till-datum kan inte vara före från-datum.';
    }

    try {
        if ($action === 'add_agent' || $action === 'save_agent') {
            $name = trim((string)($_POST['name'] ?? ''));
            if ($name === '' || mb_strlen($name) > 100) {
                tvx_flash('error', 'Ange ett namn (högst 100 tecken).');
            } elseif ($action === 'add_agent') {
                $agentId = (int)CTelavoxRepository::addAgent($name);
                $back = 'telavox_agents.php?edit=' . $agentId;
                tvx_flash('ok', 'Användaren "' . $name . '" är tillagd. Lägg till anknytning och köer nedan.');
            } else {
                CTelavoxRepository::updateAgent($agentId, $name, !empty($_POST['active']));
                CTelavoxRepository::setAgentQueues($agentId, (array)($_POST['queues'] ?? array()));
                tvx_flash('ok', 'Användaren "' . $name . '" är sparad.');
            }
        } elseif ($action === 'delete_agent') {
            if (CTelavoxRepository::deleteAgent($agentId)) {
                $back = 'telavox_agents.php';
                tvx_flash('ok', 'Användaren är borttagen.');
            } else {
                tvx_flash('error', 'Användaren har samtal och kan inte tas bort. Avaktivera användaren i stället.');
            }
        } elseif ($action === 'add_extension') {
            $extension = CTelavoxCsvParser::normalizeExtension($_POST['extension'] ?? '');
            // Återanvänd anknytning utan angivet från-datum: börja vid första samtalet utan användare
            if ($extension !== null && trim((string)($_POST['valid_from'] ?? '')) === '') {
                $suggested = CTelavoxRepository::suggestValidFrom($extension);
                if ($suggested !== null && ($validTo === null || $validTo >= $suggested)) {
                    $validFrom = $suggested;
                }
            }
            if (!CTelavoxRepository::getAgent($agentId)) {
                $back = 'telavox_agents.php';
                tvx_flash('error', 'Välj en användare.');
            } elseif ($extension === null) {
                tvx_flash('error', 'Ange en anknytning (siffror).');
            } elseif ($periodError !== null) {
                tvx_flash('error', $periodError);
            } elseif (CTelavoxRepository::extensionOverlaps($extension, $validFrom, $validTo)) {
                tvx_flash('error', 'Anknytning ' . $extension . ' är redan kopplad till en användare under en överlappande period.');
            } else {
                CTelavoxRepository::addExtension($agentId, $extension, $validFrom, $validTo);
                tvx_flash('ok', 'Anknytning ' . $extension . ' är kopplad. Redan importerade samtal har uppdaterats.');
            }
            if (!empty($_POST['from_list'])) {
                $back = 'telavox_agents.php';
            }
        } elseif ($action === 'save_extension') {
            $ext = CTelavoxRepository::getExtension((int)($_POST['extension_id'] ?? 0));
            if (!$ext) {
                tvx_flash('error', 'Anknytningen finns inte längre.');
            } elseif ($periodError !== null) {
                tvx_flash('error', $periodError);
            } elseif (CTelavoxRepository::extensionOverlaps($ext['extension'], $validFrom, $validTo, (int)$ext['id'])) {
                tvx_flash('error', 'Perioden överlappar en annan koppling för anknytning ' . $ext['extension'] . '.');
            } else {
                CTelavoxRepository::updateExtension((int)$ext['id'], $validFrom, $validTo);
                tvx_flash('ok', 'Perioden för ' . $ext['extension'] . ' är sparad. Redan importerade samtal har uppdaterats.');
            }
        } elseif ($action === 'delete_extension') {
            CTelavoxRepository::deleteExtension((int)($_POST['extension_id'] ?? 0));
            tvx_flash('ok', 'Kopplingen är borttagen. Berörda samtal saknar nu användare.');
        }
    } catch (\Throwable $e) {
        if (CTelavoxRepository::isDuplicateError($e)) {
            tvx_flash('error', 'Det finns redan en användare med det namnet.');
        } else {
            error_log('telavox_agents: ' . $e->getMessage());
            tvx_flash('error', 'Kunde inte spara (databasfel).');
        }
    }
    tvx_redirect($back);
}

include_once("header.php");
tvx_page_start('agents', 'Telefonistatistik - Användare');

$agent = $editId > 0 ? CTelavoxRepository::getAgent($editId) : null;
$defaultFrom = CTelavoxImporter::DEFAULT_VALID_FROM;

if ($agent):
    // ------------------------------------------------------- Redigera en användare
    $queues       = CTelavoxRepository::getQueues();
    $agentQueues  = CTelavoxRepository::getAgentQueueIds($editId);
    $extensions   = CTelavoxRepository::getExtensions($editId);
?>
<p><a href="telavox_agents.php">&laquo; Alla användare</a></p>

<form method="post" class="tvx-card">
  <div class="tvx-card-title"><?php echo tvx_h($agent['name']); ?></div>
  <input type="hidden" name="action" value="save_agent">
  <input type="hidden" name="agent_id" value="<?php echo $editId; ?>">
  <div class="tvx-row">
    <label for="tvx-name">Namn</label>
    <input type="text" id="tvx-name" name="name" value="<?php echo tvx_h($agent['name']); ?>" maxlength="100" required>
  </div>
  <div class="tvx-row">
    <label for="tvx-active">Aktiv</label>
    <input type="checkbox" id="tvx-active" name="active" value="1"<?php if ($agent['active']) { ?> checked<?php } ?>>
  </div>
  <div class="tvx-row" style="align-items:flex-start">
    <label>Tillhör normalt köerna</label>
    <div class="tvx-checks">
      <?php foreach ($queues as $q): ?>
      <label><input type="checkbox" name="queues[]" value="<?php echo (int)$q['id']; ?>"<?php if (in_array((int)$q['id'], $agentQueues, true)) { ?> checked<?php } ?>> <?php echo tvx_h($q['name']); ?></label>
      <?php endforeach; ?>
      <?php if (empty($queues)): ?><span class="muted">Inga köer upplagda.</span><?php endif; ?>
    </div>
  </div>
  <button type="submit" class="tvx-btn">Spara</button>
</form>

<form method="post" class="tvx-card">
  <div class="tvx-card-title">Lägg till anknytning</div>
  <input type="hidden" name="action" value="add_extension">
  <input type="hidden" name="agent_id" value="<?php echo $editId; ?>">
  <div class="tvx-row">
    <label for="tvx-ext">Anknytning</label>
    <input type="text" id="tvx-ext" name="extension" maxlength="20" placeholder="t.ex. 0902007094" required>
  </div>
  <div class="tvx-row">
    <label for="tvx-from">Gäller från</label>
    <input type="date" id="tvx-from" name="valid_from">
  </div>
  <div class="tvx-row">
    <label for="tvx-to">Gäller till</label>
    <input type="date" id="tvx-to" name="valid_to">
  </div>
  <p class="muted" style="font-size:12px;max-width:380px">Lämna datumen tomma om anknytningen alltid har tillhört användaren och gäller tills vidare.</p>
  <button type="submit" class="tvx-btn">Lägg till</button>
</form>

<h2>Anknytningar</h2>
<?php if (empty($extensions)): ?>
  <p class="muted">Användaren har inga anknytningar.</p>
<?php else: ?>
<table class="table-list">
  <thead>
    <tr><th>Anknytning</th><th>Gäller från</th><th>Gäller till</th><th></th></tr>
  </thead>
  <tbody>
  <?php foreach ($extensions as $e): $formId = 'tvx-e-' . (int)$e['id']; ?>
    <tr>
      <td><?php echo tvx_h($e['extension']); ?></td>
      <td><input type="date" name="valid_from" form="<?php echo $formId; ?>" value="<?php echo $e['valid_from'] === $defaultFrom ? '' : tvx_h($e['valid_from']); ?>"></td>
      <td><input type="date" name="valid_to" form="<?php echo $formId; ?>" value="<?php echo tvx_h($e['valid_to']); ?>"></td>
      <td class="nowrap">
        <form method="post" id="<?php echo $formId; ?>" class="tvx-inline">
          <input type="hidden" name="action" value="save_extension">
          <input type="hidden" name="agent_id" value="<?php echo $editId; ?>">
          <input type="hidden" name="extension_id" value="<?php echo (int)$e['id']; ?>">
          <button type="submit" class="tvx-btn small">Spara</button>
        </form>
        <form method="post" class="tvx-inline" onsubmit="return confirm('Ta bort kopplingen? Samtal med anknytningen kommer att sakna användare.');">
          <input type="hidden" name="action" value="delete_extension">
          <input type="hidden" name="agent_id" value="<?php echo $editId; ?>">
          <input type="hidden" name="extension_id" value="<?php echo (int)$e['id']; ?>">
          <button type="submit" class="tvx-btn small danger">Ta bort</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<p class="muted" style="font-size:12px">Tomt från-datum betyder från början, tomt till-datum tills vidare.</p>
<?php endif; ?>

<form method="post" style="margin-top:24px" onsubmit="return confirm('Ta bort användaren?');">
  <input type="hidden" name="action" value="delete_agent">
  <input type="hidden" name="agent_id" value="<?php echo $editId; ?>">
  <button type="submit" class="tvx-btn danger">Ta bort användaren</button>
</form>

<?php
else:
    // ------------------------------------------------------------- Lista användare
    $agents     = CTelavoxRepository::getAgents();
    $queueNames = CTelavoxRepository::getQueueNamesByAgent();
    $unknown    = CTelavoxRepository::getUnknownExtensions();
    $today      = date('Y-m-d');

    $extByAgent = array();
    foreach (CTelavoxRepository::getExtensions() as $e) {
        $current = $e['valid_from'] <= $today && ($e['valid_to'] === null || $e['valid_to'] >= $today);
        $extByAgent[$e['agent_id']][] = array('extension' => $e['extension'], 'current' => $current);
    }
?>

<form method="post" class="tvx-card">
  <div class="tvx-card-title">Lägg till användare</div>
  <input type="hidden" name="action" value="add_agent">
  <div class="tvx-row">
    <label for="tvx-new-name">Namn</label>
    <input type="text" id="tvx-new-name" name="name" maxlength="100" required>
  </div>
  <button type="submit" class="tvx-btn">Lägg till</button>
</form>

<?php if (!empty($unknown)): ?>
<h2>Okända anknytningar</h2>
<p class="tvx-sub">Anknytningar som förekommer i importerade samtal men saknar användare. Koppla dem så uppdateras samtalen direkt.</p>
<table class="table-list">
  <thead>
    <tr><th>Anknytning</th><th class="num">Samtal</th><th>Första &ndash; sista samtal</th><th>Koppla till (period valfri)</th></tr>
  </thead>
  <tbody>
  <?php foreach ($unknown as $u): ?>
    <tr>
      <td><?php echo tvx_h($u['extension']); ?></td>
      <td class="num"><?php echo tvx_number($u['call_count']); ?></td>
      <td class="nowrap"><?php echo tvx_h($u['first_date']); ?> &ndash; <?php echo tvx_h($u['last_date']); ?></td>
      <td class="nowrap">
        <?php if (empty($agents)): ?>
          <span class="muted">Lägg till en användare först</span>
        <?php else: ?>
        <form method="post" class="tvx-inline">
          <input type="hidden" name="action" value="add_extension">
          <input type="hidden" name="from_list" value="1">
          <input type="hidden" name="extension" value="<?php echo tvx_h($u['extension']); ?>">
          <select name="agent_id" required>
            <option value="">Välj användare</option>
            <?php foreach ($agents as $a): ?>
            <option value="<?php echo (int)$a['id']; ?>"><?php echo tvx_h($a['name']); ?></option>
            <?php endforeach; ?>
          </select>
          <input type="date" name="valid_from" title="Gäller från (tomt = från början)">
          &ndash;
          <input type="date" name="valid_to" title="Gäller till (tomt = tills vidare)">
          <button type="submit" class="tvx-btn small">Koppla</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<h2>Användare</h2>
<?php if (empty($agents)): ?>
  <p>Inga användare är upplagda ännu. De kan också skapas direkt i importens förhandsgranskning.</p>
<?php else: ?>
<table class="table-list">
  <thead>
    <tr>
      <th>Namn</th>
      <th>Anknytningar</th>
      <th>Köer</th>
      <th class="num">Samtal</th>
      <th class="c">Aktiv</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($agents as $a): ?>
    <tr>
      <td><a href="telavox_agents.php?edit=<?php echo (int)$a['id']; ?>"><?php echo tvx_h($a['name']); ?></a></td>
      <td>
        <?php
        $parts = array();
        foreach ($extByAgent[$a['id']] ?? array() as $e) {
            $parts[] = $e['current'] ? tvx_h($e['extension']) : '<span class="muted" title="Inte aktuell idag">' . tvx_h($e['extension']) . '</span>';
        }
        echo $parts ? implode(', ', $parts) : '<span class="muted">&ndash;</span>';
        ?>
      </td>
      <td><?php echo isset($queueNames[$a['id']]) ? tvx_h($queueNames[$a['id']]) : '<span class="muted">&ndash;</span>'; ?></td>
      <td class="num"><?php echo tvx_number($a['call_count']); ?></td>
      <td class="c"><?php echo $a['active'] ? 'Ja' : 'Nej'; ?></td>
      <td><a class="tvx-btn small secondary" href="telavox_agents.php?edit=<?php echo (int)$a['id']; ?>">Redigera</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<?php
endif;

echo "</div>";
include_once("footer.php");
