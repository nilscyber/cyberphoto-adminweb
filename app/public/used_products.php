<?php
// ============================================================
// used_products_v1.php
// Sida: Begagnade produkter äldre än 90 dagar
// Visar begagnade produkter (istradein='Y') som:
//   - Har mer än 0 i lager
//   - Vars salestart har passerats
//   - Vars salestart passerades för mer än 90 dagar sedan
// Sorteras på salestart ASC (äldst överst)
// ============================================================

include_once("top.php");       // Initierar $admin, $cms och övriga globala objekt
require_once("CUsedProducts.php"); // Huvudfilen pekar på aktuell version

// HTML-escape (ISO-8859-1-kompatibel)
$h = function($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
};

// Visa alla (utan 90-dagarsgräns) om checkboxen är ibockad
$visaAlla = !empty($_GET['visa_alla']);
$maxDays  = $visaAlla ? 0 : 90;

// Hämta data
$tool = new CUsedProducts();
$rows = $tool->getOldProducts('used', $maxDays);

$total    = count($rows);
$sumNetto = 0.0;
foreach ($rows as $r) { $sumNetto += (float)$r['net_price']; }

include_once("header.php");
?>

<style>
/* ===== Sidspecifik CSS ===== */
.page-header {
    margin-bottom: 16px;
}
.result-bar {
    display: flex;
    align-items: stretch;
    gap: 14px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.result-info {
    display: inline-block;
    margin-bottom: 12px;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 10px;
    font-size: 15px;
    color: #111;
}
.result-info strong {
    font-size: 16px;
    font-weight: 800;
}

/* Tabellstilar */
.table-list {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.table-list th,
.table-list td {
    padding: 8px 10px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}
.table-list thead th {
    background: #d1f2f0;
    color: #111;
    font-weight: 700;
    text-align: left;
}
.table-list .text-right { text-align: right; }

/* Kolumnbredder */
.col-art     { width: 9%; white-space: nowrap; }
.col-prod    { width: auto; }
.col-age     { width: 10%; white-space: nowrap; }
.col-net     { width: 11%; white-space: nowrap; }

/* Åldersfärger */
.age-chip {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 12px;
}
.age-chip.age-warn  { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }  /* 90-180 dagar */
.age-chip.age-alert { background: #ffe4e6; color: #9f1239; border: 1px solid #fca5a5; }  /* 181-365 dagar */
.age-chip.age-old   { background: #fecaca; color: #7f1d1d; border: 1px solid #ef4444; font-weight: 900; } /* > 365 dagar */

/* Kopierbart artnr */
.copy-art {
    cursor: pointer;
    user-select: none;
    padding: 1px 4px;
    border-radius: 4px;
}
.copy-art:hover   { background: #f3f4f6; }
.copy-art.copied  { background: #ecfdf5; box-shadow: inset 0 0 0 1px #34d399; }

/* Produktlänk */
.table-list td a {
    color: #1d4ed8;
    text-decoration: none;
}
.table-list td a:hover { text-decoration: underline; }

/* Visa alla-checkbox */
.visa-alla-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #111;
    cursor: pointer;
    padding: 6px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    user-select: none;
}
.visa-alla-label input { cursor: pointer; }
.visa-alla-label.active {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
    font-weight: 700;
}

/* Summeringsrad */
.summary-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 24px;
    padding: 10px 10px;
    margin-top: 0;
    border-top: 2px solid #d1d5db;
    font-weight: 700;
    font-size: 14px;
    background: #f9fafb;
}

/* Ingen träff */
.no-results {
    padding: 24px;
    text-align: center;
    color: #6b7280;
    font-size: 14px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    margin-top: 12px;
}
</style>

<div style="padding: 16px;">

    <h1>Begagnade produkter äldre än 90 dagar och som måste åtgärdas</h1>

    <div class="result-bar">
        <div class="result-info">
            <strong><?php echo $total; ?> st</strong> produkter som kräver åtgärd
        </div>
        <form method="get" action="" style="margin:0">
            <label class="visa-alla-label<?php echo $visaAlla ? ' active' : ''; ?>">
                <input type="checkbox"
                       name="visa_alla"
                       value="1"
                       onchange="this.form.submit()"
                       <?php echo $visaAlla ? 'checked' : ''; ?>>
                Visa alla
            </label>
        </form>
    </div>

    <?php if ($total === 0): ?>

        <div class="no-results">
            Inga begagnade produkter äldre än 90 dagar hittades  bra jobbat! ??
        </div>

    <?php else: ?>

        <table class="table-list">
            <colgroup>
                <col class="col-art" />
                <col class="col-prod" />
                <col class="col-age" />
                <col class="col-net" />
            </colgroup>
            <thead>
                <tr>
                    <th>Artnr</th>
                    <th>Produkt</th>
                    <th class="text-right">Ålder (dagar)</th>
                    <th class="text-right">Netto</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r):

                $article    = $h($r['article']);
                $productFull = $h($r['product_full']);
                $pid        = (int)$r['m_product_id'];
                $ageDays    = (int)$r['age_days'];
                $netPrice   = (float)$r['net_price'];
                $salestart  = $h($r['salestart']);

                // Åldersbadge
                if ($ageDays > 365) {
                    $ageClass = 'age-old';
                } elseif ($ageDays > 180) {
                    $ageClass = 'age-alert';
                } else {
                    $ageClass = 'age-warn';
                }

                // Länk till drawer_details
                $drawerUrl = 'https://admin.cyberphoto.se/search_dispatch.php?mode=product&q='
                           . rawurlencode($r['article'])
                           . '&open=product&id=' . $pid;

                // Netto-visning (inkl moms om skattesats finns)
                $taxRate   = (float)$r['tax_rate'];
                $netFormatted = number_format((int)round($netPrice), 0, ',', ' ') . ' kr';
                $netTitle  = '';
                if ($taxRate > 0) {
                    $netInc = $netPrice * (1.0 + $taxRate / 100.0);
                    $netTitle = ' title="Inkl moms: ' . $h(number_format((int)round($netInc), 0, ',', ' ') . ' kr') . '"';
                }

            ?>
                <tr>
                    <td>
                        <span class="copy-art"
                              data-article="<?php echo $article; ?>"
                              title="Klicka för att kopiera artikelnummer">
                            <?php echo $article; ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo $h($drawerUrl); ?>"
                           target="_blank"
                           rel="noopener"
                           title="Säljstart: <?php echo $salestart; ?>">
                            <?php echo $productFull; ?>
                        </a>
                    </td>
                    <td class="text-right">
                        <span class="age-chip <?php echo $ageClass; ?>"
                              title="Säljstart: <?php echo $salestart; ?>">
                            <?php echo $ageDays; ?>d
                        </span>
                    </td>
                    <td class="text-right"<?php echo $netTitle; ?>>
                        <?php echo $netFormatted; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="summary-bar">
            <span>Summa netto</span>
            <span><?php
                $s = (string)(int)round($sumNetto);
                $out = '';
                $len = strlen($s);
                for ($ci = 0; $ci < $len; $ci++) {
                    if ($ci > 0 && ($len - $ci) % 3 === 0) $out .= '&#160;';
                    $out .= $s[$ci];
                }
                echo $out . ' kr';
            ?></span>
        </div>

    <?php endif; ?>

</div>

<script type="text/javascript">
(function() {
    // Kopiera artikelnummer vid klick
    function copyText(txt, cb) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(String(txt || '')).then(
                function() { cb && cb(true); },
                function() { cb && cb(false); }
            );
        } else {
            try {
                var ta = document.createElement('textarea');
                ta.value = String(txt || '');
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                var ok = document.execCommand('copy');
                document.body.removeChild(ta);
                cb && cb(ok);
            } catch (e) {
                cb && cb(false);
            }
        }
    }

    document.addEventListener('click', function(e) {
        var el = e.target && e.target.closest ? e.target.closest('.copy-art') : null;
        if (!el) return;

        var art = el.getAttribute('data-article') || (el.textContent || '').trim();
        if (!art) return;

        copyText(art, function(ok) {
            if (!ok) return;
            el.classList.add('copied');
            var oldTitle = el.getAttribute('title') || '';
            el.setAttribute('title', 'Kopierat!');
            setTimeout(function() {
                el.classList.remove('copied');
                el.setAttribute('title', oldTitle);
            }, 1000);
        });
    }, false);
})();
</script>

<?php include_once("footer.php"); ?>
