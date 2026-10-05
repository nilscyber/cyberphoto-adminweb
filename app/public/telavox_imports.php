<?php
// telavox_imports.php
// Importhistorik för Telavox-statistiken. En import kan tas bort, då försvinner
// även dess samtal och filen kan importeras på nytt.

include_once("top.php");
include_once("telavox_common.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        try {
            CTelavoxRepository::deleteImport((int)($_POST['id'] ?? 0));
            tvx_flash('ok', 'Importen och dess samtal är borttagna.');
        } catch (\Throwable $e) {
            error_log('telavox_imports: ' . $e->getMessage());
            tvx_flash('error', 'Kunde inte ta bort importen (databasfel).');
        }
    }
    tvx_redirect('telavox_imports.php');
}

include_once("header.php");
tvx_page_start('imports', 'Telefonistatistik - Importhistorik');

$imports = CTelavoxRepository::getImports();
?>

<?php if (empty($imports)): ?>
  <p>Inga importer är gjorda ännu. <a href="telavox_import.php">Importera en fil</a>.</p>
<?php else: ?>
<table class="table-list">
  <thead>
    <tr>
      <th>Importerad</th>
      <th>Kö</th>
      <th>Fil</th>
      <th>Period</th>
      <th class="num">Rader</th>
      <th class="num">Samtal</th>
      <th class="num" title="Samtal som redan fanns importerade (överlappande period)">Överhoppade</th>
      <th>Status</th>
      <th>Av</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($imports as $imp): $failed = ($imp['status'] !== 'completed'); ?>
    <tr>
      <td class="nowrap"><?php echo tvx_h(substr($imp['imported_at'], 0, 16)); ?></td>
      <td><?php echo tvx_h($imp['queue_name']); ?></td>
      <td><?php echo tvx_h($imp['filename']); ?></td>
      <td class="nowrap"><?php echo tvx_h($imp['period_from']); ?> &ndash; <?php echo tvx_h($imp['period_to']); ?></td>
      <td class="num"><?php echo tvx_number($imp['row_count']); ?></td>
      <td class="num"><?php echo tvx_number($imp['call_count']); ?></td>
      <td class="num"><?php echo tvx_number($imp['skipped_count']); ?></td>
      <td<?php if ($failed) { ?> class="text-negative" title="<?php echo tvx_h($imp['error_message']); ?>"<?php } ?>>
        <?php echo $failed ? 'Misslyckad' : 'Klar'; ?>
      </td>
      <td class="muted"><?php echo tvx_h($imp['imported_by']); ?></td>
      <td>
        <form method="post" class="tvx-inline"
              onsubmit="return confirm('<?php echo $failed ? 'Ta bort loggraden?' : 'Ta bort importen och dess ' . (int)$imp['call_count'] . ' samtal?'; ?>');">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int)$imp['id']; ?>">
          <button type="submit" class="tvx-btn small danger">Ta bort</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<?php
echo "</div>";
include_once("footer.php");
