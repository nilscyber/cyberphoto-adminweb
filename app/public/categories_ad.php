<?php
include_once("top.php");
include_once("header.php");

$conn    = Db::getConnectionAD();
$selfUrl = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8');
$catid   = isset($_GET['catid']) ? (int)$_GET['catid'] : 0;

// ── 1. Ladda alla aktiva kategorier ──────────────────────────────────────
$sqlCat = "SELECT m_product_category_id, m_product_category_parent_id, name, value
           FROM m_product_category
           WHERE ad_client_id = 1000000 AND isactive = 'Y'
           ORDER BY sort_priority DESC, name ASC";

$resCat = $conn ? @pg_query($conn, $sqlCat) : false;

$categories = [];   // [id => ['name'=>..., 'parent'=>...]]
$byParent   = [];   // [parentId => [childId, ...]]  (null parent → key 0)

while ($resCat && $row = pg_fetch_assoc($resCat)) {
    $id  = (int)$row['m_product_category_id'];
    $pid = ($row['m_product_category_parent_id'] === null) ? 0 : (int)$row['m_product_category_parent_id'];
    $categories[$id]  = ['name' => $row['name'], 'value' => $row['value'], 'parent' => $pid];
    $byParent[$pid][] = $id;
}
if ($resCat) pg_free_result($resCat);

// ── 2. Räkna synliga produkter per kategori (samma kriterier som söksidan) ──
$sqlCnt = "
    SELECT p.m_product_category_id, COUNT(*) AS cnt
    FROM m_product p
    LEFT JOIN (
        SELECT m_product_id, SUM(qtyavailable) AS qtyavailable
        FROM m_product_cache
        WHERE m_warehouse_id = 1000000
        GROUP BY m_product_id
    ) ps ON ps.m_product_id = p.m_product_id
    WHERE p.ad_client_id = 1000000
      AND p.isselfservice = 'Y'
      AND (p.launchdate IS NULL OR p.launchdate <= NOW())
      AND (p.demo_product = 'N' OR COALESCE(ps.qtyavailable, 0) > 0)
      AND (p.discontinued = 'N'  OR COALESCE(ps.qtyavailable, 0) > 0)
    GROUP BY p.m_product_category_id";

$resCnt = $conn ? @pg_query($conn, $sqlCnt) : false;
$productCounts = [];
while ($resCnt && $row = pg_fetch_assoc($resCnt)) {
    $productCounts[(int)$row['m_product_category_id']] = (int)$row['cnt'];
}
if ($resCnt) pg_free_result($resCnt);

// ── Hjälpfunktioner ──────────────────────────────────────────────────────

function getAncestorIds(int $catid, array $categories): array {
    $path = [];
    $cur  = $catid;
    while ($cur && isset($categories[$cur])) {
        $path[] = $cur;
        $cur    = $categories[$cur]['parent'];
    }
    return $path; // includes $catid itself
}

function renderTree(
    int $parentId,
    array $byParent,
    array $categories,
    array $productCounts,
    int $activeCat,
    array $openIds,
    string $selfUrl
): string {
    $children = $byParent[$parentId] ?? [];
    if (empty($children)) {
        return '';
    }

    $out = "<ul class=\"cat-ul\">\n";
    foreach ($children as $id) {
        $name        = htmlspecialchars($categories[$id]['name'], ENT_QUOTES, 'UTF-8');
        $cnt         = $productCounts[$id] ?? 0;
        $hasChildren = !empty($byParent[$id]);
        $isActive    = ($id === $activeCat);
        $isOpen      = in_array($id, $openIds, true);

        $linkClass = 'cat-link' . ($isActive ? ' cat-link--active' : '');
        $badge     = $cnt > 0
            ? ' <span class="cat-badge">' . $cnt . '</span>'
            : ' <span class="cat-badge cat-badge--zero">0</span>';
        $arrow     = $hasChildren ? '<span class="cat-arrow">' . ($isOpen ? '▾' : '▸') . '</span> ' : '<span class="cat-leaf">•</span> ';

        $out .= "<li class=\"cat-li" . ($isActive ? ' cat-li--active' : '') . "\">\n";
        $out .= "  $arrow<a href=\"{$selfUrl}?catid={$id}\" class=\"{$linkClass}\">{$name}</a>{$badge}\n";

        if ($hasChildren && $isOpen) {
            $out .= renderTree($id, $byParent, $categories, $productCounts, $activeCat, $openIds, $selfUrl);
        }

        $out .= "</li>\n";
    }
    $out .= "</ul>\n";
    return $out;
}

// Vilka kategorier ska vara öppna? Alla förfäder till vald kategori.
$openIds = $catid ? getAncestorIds($catid, $categories) : [];

// Brödsmula
$breadcrumb = [];
if ($catid && isset($categories[$catid])) {
    $cur = $catid;
    while ($cur && isset($categories[$cur])) {
        array_unshift($breadcrumb, $cur);
        $cur = $categories[$cur]['parent'];
    }
}
?>
<style>
.cat-toolbar   { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:14px; }
.cat-toolbar a { display:inline-block; padding:4px 12px; border-radius:5px; font-size:12px;
                 font-weight:600; background:#e0f2f1; color:#065f46; text-decoration:none;
                 border:1px solid #6ee7b7; }
.cat-toolbar a:hover { background:#a7f3d0; }

.cat-breadcrumb { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px;
                  padding:6px 12px; font-size:13px; margin-bottom:14px; }
.cat-breadcrumb a { color:#059669; text-decoration:none; }
.cat-breadcrumb a:hover { text-decoration:underline; }

.cat-ul  { list-style:none; margin:0 0 0 18px; padding:0; }
.cat-li  { padding:2px 0; font-size:13px; }
.cat-li--active { font-weight:700; }

.cat-link        { color:#1d4ed8; text-decoration:none; }
.cat-link:hover  { text-decoration:underline; }
.cat-link--active { color:#1e3a8a; }

.cat-arrow { color:#6b7280; font-size:11px; }
.cat-leaf  { color:#9ca3af; font-size:11px; }

.cat-badge        { display:inline-block; margin-left:5px; padding:0 5px; border-radius:10px;
                    font-size:10px; font-weight:600; background:#dbeafe; color:#1e40af; }
.cat-badge--zero  { background:#f3f4f6; color:#9ca3af; }

.cat-products  { margin-top:20px; }
.cat-products h2 { font-size:15px; margin-bottom:8px; }
.table-list td, .table-list th { padding:4px 8px; }
</style>

<h1>Kategori-träd ADempiere</h1>

<?php if (!$conn): ?>
<p style="color:#b00;font-weight:bold;">Kan inte ansluta till ADempiere-databasen.</p>
<?php else: ?>

<div class="cat-toolbar">
    <a href="<?= $selfUrl ?>">Visa alla</a>
</div>

<?php if ($breadcrumb): ?>
<div class="cat-breadcrumb">
    <a href="<?= $selfUrl ?>">Start</a>
    <?php foreach ($breadcrumb as $bid): ?>
        &raquo; <a href="<?= $selfUrl ?>?catid=<?= $bid ?>"><?= htmlspecialchars($categories[$bid]['name'], ENT_QUOTES, 'UTF-8') ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div style="display:flex; gap:30px; align-items:flex-start;">

    <!-- Träd -->
    <div style="min-width:320px; max-width:480px;">
        <?= renderTree(0, $byParent, $categories, $productCounts, $catid, $openIds, $selfUrl) ?>
    </div>

    <!-- Produktlista för vald kategori -->
    <?php if ($catid && isset($categories[$catid])): ?>
    <div class="cat-products" style="flex:1;">
        <?php
        $catName  = htmlspecialchars($categories[$catid]['name'],  ENT_QUOTES, 'UTF-8');
        $catValue = htmlspecialchars($categories[$catid]['value'], ENT_QUOTES, 'UTF-8');
        ?>
        <h2>Produkter i <?= $catName ?> (<?= $catValue ?>)</h2>
        <?php
        $sqlProd = "
                    SELECT p.m_product_id, p.value AS artnr, manu.name AS tillverkare, p.name AS beskrivning,
                           COALESCE(ps.qtyavailable, 0) AS lager
                    FROM m_product p
                    JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id
                    LEFT JOIN (
                        SELECT m_product_id, SUM(qtyavailable) AS qtyavailable
                        FROM m_product_cache
                        WHERE m_warehouse_id = 1000000
                        GROUP BY m_product_id
                    ) ps ON ps.m_product_id = p.m_product_id
                    WHERE p.ad_client_id = 1000000
                      AND p.m_product_category_id = $catid
                      AND p.isselfservice = 'Y'
                      AND (p.launchdate IS NULL OR p.launchdate <= NOW())
                      AND (p.demo_product = 'N' OR COALESCE(ps.qtyavailable, 0) > 0)
                      AND (p.discontinued = 'N'  OR COALESCE(ps.qtyavailable, 0) > 0)
                    ORDER BY manu.name ASC, p.name ASC";

        $resProd = @pg_query($conn, $sqlProd);
        $numProd = $resProd ? pg_num_rows($resProd) : 0;
        ?>
        <p style="font-size:12px;color:#6b7280;">Visar <?= $numProd ?> aktiva produkter</p>
        <?php if ($numProd > 0): ?>
        <table class="table-list" style="font-size:12px;">
            <thead>
                <tr>
                    <th>Artnr</th>
                    <th>Tillverkare</th>
                    <th>Beskrivning</th>
                    <th style="text-align:right;">Lager</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($resProd && $prod = pg_fetch_assoc($resProd)): ?>
                <tr>
                    <td><a target="_blank" href="https://localhost/search_dispatch.php?mode=product&q=<?= urlencode($prod['artnr']) ?>&open=product&id=<?= (int)$prod['m_product_id'] ?>"><?= htmlspecialchars($prod['artnr'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><?= htmlspecialchars($prod['tillverkare'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($prod['beskrivning'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="text-align:right;"><?= (int)$prod['lager'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color:#6b7280;">Inga aktiva produkter i denna kategori.</p>
        <?php endif; ?>
        <?php if ($resProd) pg_free_result($resProd); ?>
    </div>
    <?php endif; ?>

</div>

<?php endif; ?>

<?php include_once("footer.php"); ?>
