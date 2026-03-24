<?php
include_once "top.php";
include_once "header.php";

// Konfig (GET + förval föregående månad)  samma upplägg som inköp
$dr_id      = 'sales_kpi';
$dr_method  = 'get';
$dr_default = 'prevMonth';
$dr_action  = ''; // samma sida

// (1) Rendera filtret  och hämta date-värdena via $GLOBALS
include __DIR__ . '/date_filter_widget.php';
$dr = isset($GLOBALS['dr_exports_'.$dr_id]) ? $GLOBALS['dr_exports_'.$dr_id] : array('from'=>date('Y-m-d'), 'to'=>date('Y-m-d'), 'preset'=>'custom');
$from = $dr['from']; $to = $dr['to'];

// (2) Hämta data (sälj)
// $rows = $statistics->getSalesStats($from, $to);
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';
$rows  = $statistics->getSalesStats($from, $to, $debug);


// --- render som tidigare ---
$h = function($s){
  if ($s === null) return '';
  $s = (string)$s;
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$fmtInt = function($n){ return number_format((int)$n, 0, ',', ' '); };
$fmtDec = function($n){ return number_format((float)$n, 2, ',', ' '); };

// Summeringar
$total_orders = 0; 
$total_lines  = 0; 
$total_totallines = 0.0;
$total_tb = 0.0;
$total_prodnet = 0.0; // NYTT

if ($rows) {
  foreach ($rows as $r) {
    $total_orders     += (int)$r['antal_saljordrar'];
    $total_lines      += (int)$r['antal_orderrader_totalt'];
    $total_totallines += (float)$r['totallines_sum'];
    $total_tb         += (float)$r['tb_summa'];
	$total_prodnet    += isset($r['prod_net_sum']) ? (float)$r['prod_net_sum'] : 0.0; // NYTT
  }
}
$total_tg = ($total_prodnet > 0) ? round(100 * $total_tb / $total_prodnet, 2) : 0.0;
?>
<h1>Statistik: säljordrar per säljare</h1>
<h3>Resultat för perioden <?php echo $h($from); ?>  <?php echo $h($to); ?></h3>

<table style="width:100%;border-collapse:separate;border-spacing:0;font-size:14px">
  <thead>
    <tr>
      <th style="text-align:left;padding:8px;border-bottom:2px solid #eee">Säljare</th>
      <th style="text-align:right;padding:8px;border-bottom:2px solid #eee">Antal säljordrar</th>
      <th style="text-align:right;padding:8px;border-bottom:2px solid #eee">Antal orderrader totalt</th>
      <th style="text-align:right;padding:8px;border-bottom:2px solid #eee">Snitt rader per order</th>
      <th style="text-align:right;padding:8px;border-bottom:2px solid #eee">Totalt (SEK)</th>
      <th style="text-align:right;padding:8px;border-bottom:2px solid #eee">TB (SEK)</th>
      <th style="text-align:right;padding:8px;border-bottom:2px solid #eee">TG (%)</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows): foreach ($rows as $r): ?>
      <tr>
        <td style="padding:8px;border-bottom:1px solid #f1f1f1"><?php echo $h($r['salesrep']); ?></td>
        <td style="padding:8px;border-bottom:1px solid #f1f1f1;text-align:right"><?php echo $fmtInt($r['antal_saljordrar']); ?></td>
        <td style="padding:8px;border-bottom:1px solid #f1f1f1;text-align:right"><?php echo $fmtInt($r['antal_orderrader_totalt']); ?></td>
        <td style="padding:8px;border-bottom:1px solid #f1f1f1;text-align:right"><?php echo $fmtDec($r['snitt_rader_per_order']); ?></td>
        <td style="padding:8px;border-bottom:1px solid #f1f1f1;text-align:right"><?php echo $fmtDec($r['totallines_sum']); ?></td>
        <td style="padding:8px;border-bottom:1px solid #f1f1f1;text-align:right"><?php echo $fmtDec($r['tb_summa']); ?></td>
        <td style="padding:8px;border-bottom:1px solid #f1f1f1;text-align:right"><?php echo $fmtDec($r['tb_marginal_pct']); ?> %</td>
      </tr>
    <?php endforeach; else: ?>
      <tr><td colspan="7" style="padding:12px;text-align:center;color:#666">Inga träffar för valt intervall.</td></tr>
    <?php endif; ?>
    <?php if ($rows): ?>
      <tr>
        <td style="padding:10px 8px;font-weight:bold;text-align:right;border-top:2px solid #ddd">Totalt:</td>
        <td style="padding:10px 8px;font-weight:bold;text-align:right;border-top:2px solid #ddd"><?php echo $fmtInt($total_orders); ?></td>
        <td style="padding:10px 8px;font-weight:bold;text-align:right;border-top:2px solid #ddd"><?php echo $fmtInt($total_lines); ?></td>
        <td style="padding:10px 8px;font-weight:bold;text-align:right;border-top:2px solid #ddd"></td>
        <td style="padding:10px 8px;font-weight:bold;text-align:right;border-top:2px solid #ddd"><?php echo $fmtDec($total_totallines); ?></td>
        <td style="padding:10px 8px;font-weight:bold;text-align:right;border-top:2px solid #ddd"><?php echo $fmtDec($total_tb); ?></td>
        <td style="padding:10px 8px;font-weight:bold;text-align:right;border-top:2px solid #ddd"><?php echo $fmtDec($total_tg); ?> %</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include_once "footer.php"; ?>
