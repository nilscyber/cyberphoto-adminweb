<?php
// telavox_queues.php
// Administrera köer för Telavox-statistiken.

include_once("top.php");
include_once("telavox_common.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    $id     = (int)($_POST['id'] ?? 0);
    $name   = trim((string)($_POST['name'] ?? ''));
    $tqid   = trim((string)($_POST['telavox_queue_id'] ?? ''));
    $tqid   = $tqid === '' ? null : $tqid;

    try {
        if ($action === 'add' || $action === 'save') {
            if ($name === '' || mb_strlen($name) > 100) {
                tvx_flash('error', 'Ange ett könamn (högst 100 tecken).');
            } elseif ($tqid !== null && !preg_match('/^\d{1,20}$/', $tqid)) {
                tvx_flash('error', 'Telavox kö-id ska bara innehålla siffror.');
            } elseif ($action === 'add') {
                CTelavoxRepository::addQueue($name, $tqid);
                tvx_flash('ok', 'Kön "' . $name . '" är tillagd.');
            } else {
                CTelavoxRepository::updateQueue($id, $name, $tqid, !empty($_POST['active']));
                tvx_flash('ok', 'Kön "' . $name . '" är sparad.');
            }
        } elseif ($action === 'delete') {
            if (CTelavoxRepository::deleteQueue($id)) {
                tvx_flash('ok', 'Kön är borttagen.');
            } else {
                tvx_flash('error', 'Kön har importer och kan inte tas bort. Avaktivera den i stället.');
            }
        }
    } catch (\Throwable $e) {
        if (CTelavoxRepository::isDuplicateError($e)) {
            tvx_flash('error', 'Det finns redan en kö med det namnet eller det Telavox kö-id:t.');
        } else {
            error_log('telavox_queues: ' . $e->getMessage());
            tvx_flash('error', 'Kunde inte spara kön (databasfel).');
        }
    }
    tvx_redirect('telavox_queues.php');
}

include_once("header.php");
tvx_page_start('queues', 'Telefonistatistik - Köer');

$queues = CTelavoxRepository::getQueues();
?>

<p class="tvx-sub">
  Varje importerad fil kopplas till en kö. Telavox kö-id är numret i exportens filnamn
  (<span class="nowrap">queue_<b>2023402</b>_...</span>) och används för att förvälja rätt kö vid import.
  Det fylls i automatiskt vid första importen om det lämnas tomt.
</p>

<form method="post" class="tvx-card">
  <div class="tvx-card-title">Lägg till kö</div>
  <input type="hidden" name="action" value="add">
  <div class="tvx-row">
    <label for="tvx-new-name">Namn</label>
    <input type="text" id="tvx-new-name" name="name" maxlength="100" required>
  </div>
  <div class="tvx-row">
    <label for="tvx-new-tqid">Telavox kö-id</label>
    <input type="text" id="tvx-new-tqid" name="telavox_queue_id" maxlength="20" placeholder="valfritt">
  </div>
  <button type="submit" class="tvx-btn">Lägg till</button>
</form>

<?php if (empty($queues)): ?>
  <p>Inga köer är upplagda ännu.</p>
<?php else: ?>
<table class="table-list">
  <thead>
    <tr>
      <th>Namn</th>
      <th>Telavox kö-id</th>
      <th class="c">Aktiv</th>
      <th class="num">Importer</th>
      <th class="num">Samtal</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($queues as $q): $formId = 'tvx-q-' . (int)$q['id']; ?>
    <tr>
      <td><input type="text" name="name" form="<?php echo $formId; ?>" value="<?php echo tvx_h($q['name']); ?>" maxlength="100" required></td>
      <td><input type="text" name="telavox_queue_id" form="<?php echo $formId; ?>" value="<?php echo tvx_h($q['telavox_queue_id']); ?>" maxlength="20" size="10"></td>
      <td class="c"><input type="checkbox" name="active" value="1" form="<?php echo $formId; ?>"<?php if ($q['active']) { ?> checked<?php } ?>></td>
      <td class="num"><?php echo tvx_number($q['import_count']); ?></td>
      <td class="num"><?php echo tvx_number($q['call_count']); ?></td>
      <td class="nowrap">
        <form method="post" id="<?php echo $formId; ?>" class="tvx-inline">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="id" value="<?php echo (int)$q['id']; ?>">
          <button type="submit" class="tvx-btn small">Spara</button>
        </form>
        <?php if ((int)$q['import_count'] === 0): ?>
        <form method="post" class="tvx-inline" onsubmit="return confirm('Ta bort kön?');">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int)$q['id']; ?>">
          <button type="submit" class="tvx-btn small danger">Ta bort</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<?php
echo "</div>";
include_once("footer.php");
