<?php
include_once("top.php");
include_once("header.php");

/* ===== Konfig ===== */
$webId = 1652736;
$excl  = array(1000362,1000441);

/* ===== Datum in / default ===== */
$todayYmd     = date('Y-m-d');
$yesterdayYmd = date('Y-m-d', strtotime('-1 day'));

$df = isset($_GET['from']) ? $_GET['from'] : $yesterdayYmd;
$dt = isset($_GET['to'])   ? $_GET['to']   : $yesterdayYmd;

/* tidsfönster: [from 00:00, to +1d 00:00) */
$from_ts = date('Y-m-d 00:00:00', strtotime($df));
$to_ts   = date('Y-m-d 00:00:00', strtotime($dt.' +1 day'));

/* ===== Hämta data (tre anrop) ===== */
$all     = $statistics->getUnitsPerOrderBasic($from_ts, $to_ts, 'total',  $webId, $excl);
$web     = $statistics->getUnitsPerOrderBasic($from_ts, $to_ts, 'web',    $webId, $excl);
$manual  = $statistics->getUnitsPerOrderBasic($from_ts, $to_ts, 'manual', $webId, $excl);

/* ===== Hjälpare ===== */
$h  = function($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); };
$nf = function($n, $dec=0){ return number_format((float)$n, $dec, ',', ' '); };

function renderKpiTable($title, $row, $nf, $h){
    echo '<h3 style="margin:12px 0 4px 0">'.$h($title).'</h3>';
    echo '<table class="kpi"><thead><tr>
            <th></th><th>Orders</th><th>Units</th><th>Units/Order</th><th>1 st</th><th>2 st</th><th>3+ st</th>
          </tr></thead><tbody>';
    echo '<tr><td>Period</td>';
    echo '<td>'.$nf($row['orders']).'</td>';
    echo '<td>'.$nf($row['units']).'</td>';
    echo '<td>'.$nf($row['avg_units_per_order'],2).'</td>';
    echo '<td>'.$nf($row['orders_1_item']).'</td>';
    echo '<td>'.$nf($row['orders_2_items']).'</td>';
    echo '<td>'.$nf($row['orders_3plus_items']).'</td>';
    echo '</tr></tbody></table>';
}

/* ===== Snabbval (datum) ===== */
$sv_today_from = $todayYmd;     $sv_today_to = $todayYmd;
$sv_yday_from  = $yesterdayYmd; $sv_yday_to  = $yesterdayYmd;
$sv_7_from     = date('Y-m-d', strtotime('-7 day'));   $sv_to_yday = $yesterdayYmd;
$sv_30_from    = date('Y-m-d', strtotime('-30 day'));

$isoMondayThisWeek = date('Y-m-d', strtotime('monday this week'));
$isoMondayPrevWeek = date('Y-m-d', strtotime($isoMondayThisWeek.' -7 day'));
$isoSundayPrevWeek = date('Y-m-d', strtotime($isoMondayPrevWeek.' +6 day'));

$firstOfThisMonth = date('Y-m-01');
$firstOfPrevMonth = date('Y-m-01', strtotime('-1 month', strtotime($firstOfThisMonth)));
$lastOfPrevMonth  = date('Y-m-t', strtotime($firstOfPrevMonth));

function isActiveRange($df,$dt,$a,$b){ return ($df===$a && $dt===$b); }
$activeToday   = isActiveRange($df,$dt,$sv_today_from,$sv_today_to);
$activeYday    = isActiveRange($df,$dt,$sv_yday_from,$sv_yday_to);
$active7       = isActiveRange($df,$dt,$sv_7_from,$sv_to_yday);
$active30      = isActiveRange($df,$dt,$sv_30_from,$sv_to_yday);
$activePrevW   = isActiveRange($df,$dt,$isoMondayPrevWeek,$isoSundayPrevWeek);
$activePrevMon = isActiveRange($df,$dt,$firstOfPrevMonth,$lastOfPrevMonth);
?>
<style>
.formwrap{margin:12px 0;padding:12px;border:1px solid #eee;border-radius:8px}
.formline{margin:8px 0}
.kpi{border-collapse:separate;border-spacing:0;width:100%;max-width:980px;margin:8px 0 16px 0}
.kpi th,.kpi td{padding:8px 10px;border-bottom:1px solid #eee;text-align:right}
.kpi th:first-child,.kpi td:first-child{text-align:left}
.kpi thead th{background:#f9fafb;border-bottom:2px solid #e5e7eb}
.btn{display:inline-block;padding:6px 12px;border:1px solid #d0d7de;background:#fff;border-radius:6px;text-decoration:none;margin-right:6px}
.btn:hover{background:#f5f5f5}
.btn-primary{display:inline-block;padding:8px 16px;border:1px solid #0969da;background:#0969da;color:#fff;border-radius:6px;text-decoration:none}
.btn-primary:hover{background:#0757b5}
.btn.active{background:#fff2e0;border-color:#f4a261}
.btn-row{margin-top:6px}
</style>

<h1>Statistik: antal produkter per order</h1>

<form method="get" action="" class="formwrap">
  <div class="formline">
    <label>Från:&nbsp;<input type="date" name="from" value="<?php echo $h($df); ?>"></label>
    &nbsp;&nbsp;
    <label>Till:&nbsp;<input type="date" name="to" value="<?php echo $h($dt); ?>"></label>
  </div>

  <div class="btn-row">
    <a class="btn <?php echo $activeToday?'active':''; ?>"   href="?from=<?php echo $h($sv_today_from);  ?>&to=<?php echo $h($sv_today_to);   ?>">Idag</a>
    <a class="btn <?php echo $activeYday?'active':''; ?>"    href="?from=<?php echo $h($sv_yday_from);   ?>&to=<?php echo $h($sv_yday_to);    ?>">Gårdagen</a>
    <a class="btn <?php echo $active7?'active':''; ?>"       href="?from=<?php echo $h($sv_7_from);      ?>&to=<?php echo $h($sv_to_yday);   ?>">Senaste 7 dagarna</a>
    <a class="btn <?php echo $active30?'active':''; ?>"      href="?from=<?php echo $h($sv_30_from);     ?>&to=<?php echo $h($sv_to_yday);   ?>">Senaste 30 dagarna</a>
    <a class="btn <?php echo $activePrevW?'active':''; ?>"   href="?from=<?php echo $h($isoMondayPrevWeek); ?>&to=<?php echo $h($isoSundayPrevWeek); ?>">Föregående vecka</a>
    <a class="btn <?php echo $activePrevMon?'active':''; ?>" href="?from=<?php echo $h($firstOfPrevMonth); ?>&to=<?php echo $h($lastOfPrevMonth);  ?>">Föregående månad</a>
  </div>

  <div class="btn-row">
    <button type="submit" class="btn-primary">Visa</button>
  </div>
</form>

<h2>Resultat för perioden <?php echo $h($df); ?>  <?php echo $h($dt); ?></h2>

<?php
renderKpiTable('Alla ordrar totalt',   $all,    $nf, $h);
renderKpiTable('Webbordrar',           $web,    $nf, $h);
renderKpiTable('Manuella ordrar',      $manual, $nf, $h);

include_once("footer.php");
