<?php
// Export måste ske innan något outputas (top.php/header.php skickar HTML)
if (isset($_GET['export']) && $_GET['export'] == '1') {
    include_once("top.php"); // behövs för att få $sales

    $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
    if ($days <= 0) { $days = 30; }

    $rows = $sales->getFocusProductsSimple($days);
    $sales->exportFocusProductsSimpleCsv($rows);
    exit;
}

include_once("top.php");
include_once("header.php");

echo "<h1>Våra fokusprodukter</h1>";

// Parametrar
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
if ($days <= 0) { $days = 30; }

$rows = $sales->getFocusProductsSimple($days);

// Admin kör latin1 i output ? fixar Ã¤ osv (closure för att undvika redeclare)
$h_latin1 = function ($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES);
};
?>

<style>
  .focus-actions { display:flex; justify-content:flex-end; align-items:center; gap:10px; margin: 8px 0 12px; }
  .btn {
    display:inline-block; padding:8px 10px; border-radius:8px;
    border:1px solid #cfcfcf; background:#f6f6f6; color:#222; text-decoration:none;
    font-size: 13px;
  }
  .btn:hover { background:#ededed; }

  .copy-artnr { cursor:pointer; }
  .copy-artnr:hover { text-decoration: underline; }

  .num { text-align:right; white-space:nowrap; }

  /* Kategorirader */
  .group-row td{
    background:#f1f2f3;
    font-weight:700;
  }
  .cat-link{
    color: inherit;
    text-decoration: none;
    display: inline-block;
    width: 100%;
  }
  .cat-link:hover{
    text-decoration: underline;
  }
</style>

<?php if (!empty($rows)) { ?>
  <div class="focus-actions">
    <a class="btn" href="?export=1&days=<?php echo (int)$days; ?>">Exportera till Excel</a>
  </div>
<?php } ?>

<table class="table-list" style="width:100%;">
  <thead>
    <tr>
      <th>Artnr</th>
      <th>Produkt</th>
      <th class="num">Lagersaldo</th>
      <th class="num">Sålda 30d</th>
    </tr>
  </thead>

  <tbody>
  <?php if (empty($rows)) { ?>
    <tr><td colspan="4">Inga fokusprodukter hittades.</td></tr>
  <?php } else {

    $currentCat = null;

    foreach ($rows as $r) {

      $catName  = (string)$r['category_name'];
      $catValue = (string)$r['category_value'];

      if ($catName !== $currentCat) {
        $currentCat = $catName;

        $catLabel = $h_latin1($currentCat);

        if ($catValue !== '') {
          $catUrl = 'https://admin.cyberphoto.se/lagerstatus.php?katID=' . urlencode($catValue);
          echo '<tr class="group-row"><td colspan="4">'
             . '<a class="cat-link" href="' . htmlspecialchars($catUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">'
             . '<strong>' . $catLabel . '</strong>'
             . '</a>'
             . '</td></tr>';
        } else {
          echo '<tr class="group-row"><td colspan="4"><strong>' . $catLabel . '</strong></td></tr>';
        }
      }

      $artnr = (string)$r['artnr'];
      $pid   = (int)$r['m_product_id'];

      // Länk i admin
      $productUrl = 'search_dispatch.php?q=' . urlencode($artnr) . '&open=product&id=' . $pid . '#';
  ?>
      <tr>
        <td>
          <span class="copy-artnr" data-copy="<?php echo htmlspecialchars($artnr, ENT_QUOTES); ?>" title="Klicka för att kopiera">
            <?php echo htmlspecialchars($artnr, ENT_QUOTES); ?>
          </span>
        </td>

        <td>
          <a href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>" target="_blank" rel="noopener">
            <?php echo $h_latin1($r['product_label']); ?>
          </a>
        </td>

        <td class="num"><?php echo (int)$r['onhand_qty']; ?></td>
        <td class="num"><?php echo (int)$r['sold_30d']; ?></td>
      </tr>
  <?php
    }
  } ?>
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
