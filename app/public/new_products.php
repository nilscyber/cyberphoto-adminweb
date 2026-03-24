<?php
// Export måste ske innan något outputas
if (isset($_GET['export']) && $_GET['export'] == '1') {
    include_once("top.php"); // laddar $sales

    $days = isset($_GET['days']) ? (int)$_GET['days'] : 14;
    if ($days <= 0) { $days = 14; }

    // Sida: visa bara produkter där launchdate har passerats (viktigt)
    $rows = $sales->getNewProductsForPage($days);

    $sales->exportNewProductsCsvLatin1($rows, 'nya_produkter_' . date('YmdHi') . '.csv');
    exit;
}

include_once("top.php");
include_once("header.php");

echo "<h1>Nya produkter</h1>";

$days = isset($_GET['days']) ? (int)$_GET['days'] : 14;
if ($days <= 0) { $days = 14; }

// Sida: visa bara produkter där launchdate har passerats (viktigt)
$rows = $sales->getNewProductsForPage($days);

// Undvik redeclare genom closure
$h_latin1 = function ($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES);
};
?>

<style>
  .actions { display:flex; justify-content:space-between; align-items:center; gap:10px; margin: 8px 0 12px; }
  .btn {
    display:inline-block; padding:8px 10px; border-radius:8px;
    border:1px solid #cfcfcf; background:#f6f6f6; color:#222; text-decoration:none;
    font-size: 13px;
  }
  .btn:hover { background:#ededed; }
  .num { text-align:right; white-space:nowrap; }
  .muted { color:#777; font-size:12px; }
  .copy-artnr { cursor:pointer; }
  .copy-artnr:hover { text-decoration: underline; }
</style>

<div class="actions">
  <div class="muted">
    Lanserade produkter. Period: senaste <b><?php echo (int)$days; ?></b> dagar.
  </div>

  <?php if (!empty($rows)) { ?>
    <a class="btn" href="?export=1&days=<?php echo (int)$days; ?>">Exportera till Excel</a>
  <?php } ?>
</div>

<table class="table-list" style="width:100%;">
  <thead>
    <tr>
      <th>Datum</th>
      <th>Artnr</th>
      <th>Produkt</th>
      <th>Leverantör</th>
      <th>Inköpare</th>
      <th class="num">Minlagersaldo</th>
      <th class="num">Maxlagersaldo</th>
    </tr>
  </thead>

  <tbody>
  <?php if (empty($rows)) { ?>
    <tr><td colspan="8">Inga nya produkter hittades.</td></tr>
  <?php } else { ?>
    <?php foreach ($rows as $r) {
		$artnr = (string)$r['artnr'];
		$pid   = (int)$r['m_product_id'];
		$productUrl = 'search_dispatch.php?q=' . urlencode($artnr) . '&open=product&id=' . $pid . '#';

		$productLabel = trim((string)$r['manufacturer'] . ' ' . (string)$r['description']);
    ?>
      <tr>
		<td><?php echo htmlspecialchars((string)$r['launch_date'], ENT_QUOTES); ?></td>

		<td>
		  <span class="copy-artnr" data-copy="<?php echo htmlspecialchars($artnr, ENT_QUOTES); ?>" title="Klicka för att kopiera">
			<?php echo htmlspecialchars($artnr, ENT_QUOTES); ?>
		  </span>
		</td>

		<td>
		  <a href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>" target="_blank" rel="noopener">
			<?php echo $h_latin1($productLabel); ?>
		  </a>
		</td>
        <td><?php echo $h_latin1($r['supplier']); ?></td>
        <td><?php echo $h_latin1($r['buyer']); ?></td>
        <td class="num"><?php echo (int)$r['min_stock']; ?></td>
        <td class="num"><?php echo (int)$r['max_stock']; ?></td>
      </tr>
    <?php } ?>
  <?php } ?>
  </tbody>
</table>

<script>
(function () {
  function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(text);
    }
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); } catch(e) {}
    document.body.removeChild(ta);
    return Promise.resolve();
  }

  document.addEventListener('click', function (e) {
    var el = e.target.closest('.copy-artnr');
    if (!el) return;
    var v = el.getAttribute('data-copy') || '';
    copyText(v);
  });
})();
</script>

<?php
include_once("footer.php");
?>
