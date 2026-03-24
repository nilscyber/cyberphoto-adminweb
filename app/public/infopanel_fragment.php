<?php
date_default_timezone_set('Europe/Stockholm');

spl_autoload_register(function ($class) {
    include $class . '.php';
});

$dagensdatum = date('Y-m-d H:i');

/* =========================
   DATA
========================= */
$infopanel = new CInfopanel();
$infopanel->setNotPrintedByCountry();
$infopanel->setPrintedByCountry();

$temp = new CTemp();

/* KPI (live) */
$utskrivna             = (int)$infopanel->getOrdersFromADNew();
$ej_utskrivna          = (int)$infopanel->getNotPrintedOrdersFromADNew();
$utskrivna_instabox    = (int)$infopanel->getOrdersFromADInstabox();
$ej_utskrivna_instabox = (int)$infopanel->getNotPrintedInstabox();

/* PostNord/Instabox volymer */
$tot_postnord = $utskrivna + $ej_utskrivna;
$tot_instabox = $utskrivna_instabox + $ej_utskrivna_instabox;

/* TOTAL (alternativ B): utskrivna + ej utskrivna */
$total_all = $tot_postnord + $tot_instabox;

/* Driveout */
$driveout_printed    = (int)$infopanel->getOrdersFromADDriveOut();
$driveout_notprinted = (int)$infopanel->getNotPrintedDriveOut();

if (isset($_GET['force_driveout'])) {
    $driveout_printed = (int)$_GET['force_driveout'];
}
if (isset($_GET['force_driveout_np'])) {
    $driveout_notprinted = (int)$_GET['force_driveout_np'];
}
$driveout_total = $driveout_printed + $driveout_notprinted;

/* Incoming goods */
$showTomorrow   = ((int)date('H') >= 18);
$goodsDateLabel = $showTomorrow ? 'IMORGON' : 'IDAG';
$incomingHtml   = $infopanel->renderIncomingGoodsTodaySummary('', 10);

/* Temp */
$tVal = $temp->showLastTempInfopanel(2);
$tVal = is_string($tVal) ? trim($tVal) : $tVal;

/* =========================
   REGLER
========================= */
$rules = array(
    'total_rules' => array(
        array('from' => '00:00', 'to' => '10:30', 'mode' => 'all',         'green' => -1,  'orange' => null, 'red' => null),
        array('from' => '10:31', 'to' => '12:00', 'mode' => 'all',         'green' => null,'orange' => 50,   'red' => 100),
        array('from' => '12:01', 'to' => '14:30', 'mode' => 'all',         'green' => -1,  'orange' => null, 'red' => null),
        array('from' => '14:31', 'to' => '15:30', 'mode' => 'no_instabox', 'green' => null,'orange' => 65,   'red' => 100),
        array('from' => '15:31', 'to' => '16:30', 'mode' => 'all',         'green' => null,'orange' => 50,   'red' => 75),
        array('from' => '16:31', 'to' => '17:05', 'mode' => 'no_instabox', 'green' => null,'orange' => 20,   'red' => 40),
        array('from' => '17:06', 'to' => '23:59', 'mode' => 'all',         'green' => -1,  'orange' => null, 'red' => null),
    ),

    // ingen 13:30-överlapp
    'instabox_rules' => array(
        array('from' => '00:00', 'to' => '13:29', 'green' => -1,  'orange' => null, 'red' => null),
        array('from' => '13:30', 'to' => '14:30', 'green' => null,'orange' => 20,   'red' => 30),
        array('from' => '14:31', 'to' => '17:10', 'green' => null,'orange' => 10,   'red' => 20),
        array('from' => '17:11', 'to' => '23:59', 'green' => -1,  'orange' => null, 'red' => null),
    ),

    'visual' => array(
        'fill_opacity_ok'   => 0.08,
        'fill_opacity_warn' => 0.16,
        'fill_opacity_crit' => 0.20
    )
);

/* =========================
   HELPERS (PHP5)
========================= */
function _hhmm_ts($hhmm){
    return strtotime(date('Y-m-d').' '.$hhmm.':00');
}
function _rule_for_now($ruleList, $nowTs){
    $n = count($ruleList);
    for ($i=0; $i<$n; $i++){
        $r = $ruleList[$i];
        $fromTs = _hhmm_ts($r['from']);
        $toTs   = _hhmm_ts($r['to']);
        if ($nowTs >= $fromTs && $nowTs <= $toTs){
            return $r;
        }
    }
    return $n ? $ruleList[0] : array();
}
function _status_from_rule($value, $rule){
    if (isset($rule['green']) && $rule['green'] === -1) return 'is-ok';

    $orange = (isset($rule['orange']) ? $rule['orange'] : null);
    $red    = (isset($rule['red']) ? $rule['red'] : null);

    if ($red !== null && $value >= (int)$red) return 'is-crit';
    if ($orange !== null && $value >= (int)$orange) return 'is-warn';

    return 'is-ok';
}
function _pct_fill($value, $rule){
    $orange = (isset($rule['orange']) ? $rule['orange'] : null);
    $red    = (isset($rule['red']) ? $rule['red'] : null);

    $den = null;
    if ($red !== null) $den = (int)$red;
    else if ($orange !== null) $den = (int)$orange;

    if (!$den || $den <= 0) return 0;

    $p = ($value / $den) * 100.0;
    if ($p < 0) $p = 0;
    if ($p > 100) $p = 100;
    return (int)round($p);
}
function _warn_text($rule){
    $orange = (isset($rule['orange']) ? $rule['orange'] : null);
    $red    = (isset($rule['red']) ? $rule['red'] : null);

    if ($orange === null && $red === null) return '';

    $parts = array();
    if ($orange !== null) $parts[] = 'Orange vid '.(int)$orange.'+';
    if ($red !== null)    $parts[] = 'Röd vid '.(int)$red.'+';

    return 'Varning '.$rule['from'].''.$rule['to'].': '.implode('  ', $parts);
}

/* =========================
   STATUS: TOTAL
========================= */
$nowTs = time();

$totalRule = _rule_for_now($rules['total_rules'], $nowTs);
$totalMode = isset($totalRule['mode']) ? $totalRule['mode'] : 'all';

$totalForStatus = $total_all;
$totalVolymLabel = 'Volym: PostNord + Instabox';
if ($totalMode === 'no_instabox') {
    $totalForStatus  = $tot_postnord;
    $totalVolymLabel = 'Volym: PostNord (Instabox exkl.)';
}

$cls_total = _status_from_rule($totalForStatus, $totalRule);
$totalFillPct = _pct_fill($totalForStatus, $totalRule);
$totalWarnTxt = _warn_text($totalRule);

/* =========================
   STATUS: INSTABOX
========================= */
$instaRule = _rule_for_now($rules['instabox_rules'], $nowTs);
$cls_instabox = _status_from_rule($tot_instabox, $instaRule);
$instaFillPct = _pct_fill($tot_instabox, $instaRule);
$instaWarnTxt = _warn_text($instaRule);

/* Enkla thresholds */
function kpiStatusClassSimple($value, $warn, $crit) {
    if ($value >= $crit) return 'is-crit';
    if ($value >= $warn) return 'is-warn';
    return 'is-ok';
}
$cls_tot_postnord = kpiStatusClassSimple($tot_postnord, 80, 120);
$cls_ej_postnord  = kpiStatusClassSimple($ej_utskrivna, 3, 8);
$cls_ej_instabox  = kpiStatusClassSimple($ej_utskrivna_instabox, 1, 4);

/* Fill färger */
$vis = $rules['visual'];
$op_ok   = isset($vis['fill_opacity_ok'])   ? $vis['fill_opacity_ok']   : 0.08;
$op_warn = isset($vis['fill_opacity_warn']) ? $vis['fill_opacity_warn'] : 0.16;
$op_crit = isset($vis['fill_opacity_crit']) ? $vis['fill_opacity_crit'] : 0.20;

function _fill_rgba($status, $op_ok, $op_warn, $op_crit){
    if ($status === 'is-crit') return 'rgba(220,38,38,'.$op_crit.')';
    if ($status === 'is-warn') return 'rgba(245,158,11,'.$op_warn.')';
    return 'rgba(22,163,74,'.$op_ok.')';
}
$totalFillColor = _fill_rgba($cls_total, $op_ok, $op_warn, $op_crit);
$instaFillColor = _fill_rgba($cls_instabox, $op_ok, $op_warn, $op_crit);

/* Instabox float/fill: OK => ingen fill/float */
if ($cls_instabox === 'is-ok') {
    $instaFillPct  = 0;
    $instaFloatY   = 0;
} else {
    $instaFloatY = (100 - $instaFillPct) * 0.35;
    if ($instaFloatY < 0) $instaFloatY = 0;
    if ($instaFloatY > 35) $instaFloatY = 35;
}

/* Driveout banner */
$showDriveoutBanner = ($driveout_total > 0);
$driveoutBannerCls = '';
if ($showDriveoutBanner && $driveout_notprinted > 0) {
    $driveoutBannerCls = ' pulse';
}
?>
<style>
  :root{
    /* kontroll: öka/minska om TV:n äter kanter */
    --safe-r: 18px;
  }

  *, *:before, *:after{ box-sizing:border-box; }

  html, body{
    height:100%;
    margin:0;
    padding:0;
    background: transparent;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    color:#111827;
    opacity:1 !important;
    filter:none !important;
  }

  .shell{ height:100%; width:100%; padding:0; }
  .wrap{
    height:100%;
    width:100%;
    max-width:none;
    margin:0;
    display:grid;
    grid-template-rows: 64px 1fr 42px;
    background: transparent;
    border:0;
    border-radius:0;
    overflow:hidden;
    box-shadow:none;
  }

  .topbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding: 10px calc(18px + var(--safe-r)) 10px 18px; /* SAFE höger */
    border-bottom: 1px solid #e5e7eb;
    color:#111827;
  }
  .title{
    font-weight: 900;
    font-size: 26px;
    letter-spacing: .2px;
    white-space: nowrap;
  }
  .meta{
    display:flex;
    gap: 14px;
    align-items:center;
    font-weight: 900;
    font-size: 18px;
    white-space: nowrap;
  }
  .meta .time{ font-variant-numeric: tabular-nums; }

  .content{
    display:grid;
    grid-template-columns: 58% 20% 22%;
    gap: 18px;
    padding: 16px calc(28px + var(--safe-r)) 16px 28px; /* SAFE höger */
    min-height:0;
  }

  .kpis{
    display:grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
    gap: 18px;
    min-height:0;
  }

  .card{
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 8px 18px rgba(0,0,0,.06);
    padding: 16px 18px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:relative;
    overflow:hidden;
    min-height:0;
    min-width:0;
  }

  .card:before{
    content:'';
    position:absolute;
    left:0; top:0; bottom:0;
    width: 12px;
    background:#16a34a;
    opacity:.9;
    z-index:2;
  }
  .card.is-warn:before{ background:#f59e0b; }
  .card.is-crit:before{ background:#dc2626; }

  .label{
    font-size: 18px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing:.3px;
  }
  .sub{
    margin-top:6px;
    color:#374151;
    font-weight: 900;
    font-size: 14px;
    line-height:1.25;
  }
  .sub .hint{
    display:block;
    margin-top:6px;
    font-size: 12px;
    letter-spacing:.2px;
    color:#6b7280;
    font-weight:900;
  }

  .value{
    font-size: clamp(120px, 9.8vw, 300px);
    line-height:.95;
    font-weight: 900;
    letter-spacing: -2px;
    text-align:center;
    margin-top:10px;
    font-variant-numeric: tabular-nums;
    color:#111827 !important;
    opacity: 1 !important;
    filter:none !important;
    -webkit-text-fill-color: #111827;
    z-index:3;
    position:relative;
    padding: 0 6px;
  }

  .totalCol{ display:grid; min-height:0; min-width:0; }
  .totalCard:before{ background:#111827; }
  .totalCard .value{
    font-size: clamp(80px, 6.0vw, 220px);
    text-align:center;
    align-self:flex-end;
    margin-top:10px;
    width:100%;
  }

  .fillLayer{
    position:absolute;
    left:0; right:0; bottom:0;
    height: var(--fill-h, 0%);
    background: var(--fill-bg, transparent);
    z-index:1;
    transition: height 320ms ease;
  }

  .floatValue{
    position:relative;
    transform: translateY(var(--float-y, 0%));
    transition: transform 320ms ease;
  }

  @keyframes pulseWarn {
    0%   { box-shadow: 0 8px 18px rgba(0,0,0,.06); }
    50%  { box-shadow: 0 14px 26px rgba(245,158,11,.18); }
    100% { box-shadow: 0 8px 18px rgba(0,0,0,.06); }
  }
  @keyframes pulseCrit {
    0%   { box-shadow: 0 8px 18px rgba(0,0,0,.06); }
    50%  { box-shadow: 0 14px 26px rgba(220,38,38,.18); }
    100% { box-shadow: 0 8px 18px rgba(0,0,0,.06); }
  }
  .card.is-warn.pulse { animation: pulseWarn 1.4s ease-in-out infinite; }
  .card.is-crit.pulse { animation: pulseCrit 1.4s ease-in-out infinite; }

  .instaboxTotalCard .value{ text-align:center; }
  .instaboxTotalCard .value.floatValue{ margin-top: 8px; }

  /* Högerkolumn */
  .side{
    display:flex;
    flex-direction:column;
    min-height:0;
    min-width:0;
  }

  .driveout-banner{
    width:100%;
    border-radius: 18px;
    padding: 12px 14px;
    background:#f59e0b;
    color:#111827;
    box-shadow: 0 8px 18px rgba(0,0,0,.08);
    border: 1px solid rgba(0,0,0,.08);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
    margin-bottom: 12px;
    min-width:0;
  }
  .driveout-banner .left{
    display:flex;
    flex-direction:column;
    gap: 4px;
    min-width:0;
  }
  .driveout-banner .ttl{
    font-weight: 1000;
    letter-spacing: .9px;
    text-transform: uppercase;
    font-size: 16px;
    line-height:1.1;
  }
  .driveout-banner .subtxt{
    font-weight: 900;
    font-size: 13px;
    opacity: .9;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .driveout-banner .num{
    font-weight: 1000;
    font-size: 22px;
    letter-spacing: .2px;
    background: rgba(255,255,255,.35);
    border: 1px solid rgba(0,0,0,.08);
    padding: 8px 12px;
    border-radius: 14px;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
    flex: 0 0 auto;
  }

  @keyframes drivePulse {
    0%   { box-shadow: 0 8px 18px rgba(0,0,0,.08); }
    50%  { box-shadow: 0 14px 26px rgba(245,158,11,.35); }
    100% { box-shadow: 0 8px 18px rgba(0,0,0,.08); }
  }
  .driveout-banner.pulse{ animation: drivePulse 1.2s ease-in-out infinite; }

  .sideHeader{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding: 12px 18px;
    border: 1px solid #e5e7eb;
    border-radius: 18px 18px 0 0;
    background:#fff;
    box-shadow: 0 8px 18px rgba(0,0,0,.06);
  }
  .sideHeader .h{ font-weight:900; font-size:18px; }
  .sideHeader .d{ color:#6b7280; font-weight:900; font-size:14px; letter-spacing:.6px; text-transform:uppercase; }

  .sideBody{
    border: 1px solid #e5e7eb;
    border-top:0;
    border-radius: 0 0 18px 18px;
    background:#fff;
    box-shadow: 0 8px 18px rgba(0,0,0,.06);
    padding: 10px 12px;
    overflow:auto;
    min-height:0;
  }

  .ig-table{
    width:100%;
    border-collapse: collapse;
    font-size: 16px;
    font-weight: 900;
    color:#111827;
  }
  .ig-table th{
    text-align:left;
    padding: 8px 8px;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 0;
    background:#fff;
    z-index:2;
  }
  .ig-table th.num, .ig-table td.num{ text-align:right; width: 90px; }
  .ig-table td{
    padding: 9px 8px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: top;
  }
  .ig-table tbody tr:nth-child(odd){ background:#f8fafc; }
  .ig-table tfoot td{
    padding: 10px 8px;
    border-top: 2px solid #e5e7eb;
    border-bottom:0;
    background:#fff;
  }

  .footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding: 8px calc(18px + var(--safe-r)) 8px 18px; /* SAFE höger */
    border-top: 1px solid #e5e7eb;
    color:#6b7280;
    font-weight:900;
    font-size: 13px;
  }
</style>

<div class="shell">
  <div class="wrap">
    <div class="topbar">
      <div class="title">Logistikstatus</div>

      <div class="meta">
        <div class="time"><?php echo htmlspecialchars($dagensdatum, ENT_QUOTES, 'UTF-8'); ?></div>
        <div>Temperatur: <?php echo htmlspecialchars((string)$tVal, ENT_QUOTES, 'UTF-8'); ?> &deg;C</div>
      </div>
    </div>

    <div class="content">
      <div class="kpis">
        <div class="card <?php echo $cls_tot_postnord; ?>">
          <div>
            <div class="label">UTSKRIVNA POSTNORD</div>
            <div class="sub">Totalt: <?php echo (int)$tot_postnord; ?></div>
          </div>
          <div class="value"><?php echo (int)$utskrivna; ?></div>
        </div>

        <div class="card <?php echo $cls_ej_postnord; ?>">
          <div>
            <div class="label">EJ UTSKRIVNA POSTNORD</div>
          </div>
          <div class="value"><?php echo (int)$ej_utskrivna; ?></div>
        </div>

        <?php
          $instaInline = 'style="--fill-h: '.$instaFillPct.'%; --fill-bg: '.$instaFillColor.'; --float-y: '.$instaFloatY.'%;"';
          $instaPulse = ($cls_instabox !== 'is-ok') ? ' pulse' : '';
        ?>
        <div class="card instaboxTotalCard <?php echo $cls_instabox.$instaPulse; ?>" <?php echo $instaInline; ?>>
          <div class="fillLayer"></div>
          <div>
            <div class="label">UTSKRIVNA INSTABOX</div>
            <div class="sub">
              Totalt: <?php echo (int)$tot_instabox; ?>
              <?php if ($instaWarnTxt !== '') { ?>
                <span class="hint"><?php echo htmlspecialchars($instaWarnTxt, ENT_QUOTES, 'UTF-8'); ?></span>
              <?php } ?>
            </div>
          </div>
          <div class="value floatValue"><?php echo (int)$utskrivna_instabox; ?></div>
        </div>

        <div class="card <?php echo $cls_ej_instabox; ?>">
          <div>
            <div class="label">EJ UTSKRIVNA INSTABOX</div>
          </div>
          <div class="value"><?php echo (int)$ej_utskrivna_instabox; ?></div>
        </div>
      </div>

      <?php
        $totalInline = 'style="--fill-h: '.$totalFillPct.'%; --fill-bg: '.$totalFillColor.';"';
        $totalPulse = ($cls_total !== 'is-ok') ? ' pulse' : '';
      ?>
      <div class="totalCol">
        <div class="card totalCard <?php echo $cls_total.$totalPulse; ?>" <?php echo $totalInline; ?>>
          <div class="fillLayer"></div>
          <div>
            <div class="label">TOTALT</div>
            <div class="sub">
              <?php echo htmlspecialchars($totalVolymLabel, ENT_QUOTES, 'UTF-8'); ?>
              <?php if ($totalWarnTxt !== '') { ?>
                <span class="hint"><?php echo htmlspecialchars($totalWarnTxt, ENT_QUOTES, 'UTF-8'); ?></span>
              <?php } ?>
            </div>
          </div>
          <div class="value"><?php echo (int)$total_all; ?></div>
        </div>
      </div>

      <div class="side">
        <?php if ($showDriveoutBanner) { ?>
          <div class="driveout-banner<?php echo $driveoutBannerCls; ?>">
            <div class="left">
              <div class="ttl">DRIVEOUT</div>
              <div class="subtxt">
                <?php echo 'Utskrivna: '.(int)$driveout_printed.' &bull; Ej utskrivna: '.(int)$driveout_notprinted; ?>
              </div>
            </div>
            <div class="num"><?php echo (int)$driveout_total; ?></div>
          </div>
        <?php } ?>

        <div class="sideHeader">
          <div class="h">Ankommande gods</div>
          <div class="d"><?php echo $goodsDateLabel; ?></div>
        </div>
        <div class="sideBody">
          <?php
          if (trim($incomingHtml) !== '') {
              echo $incomingHtml;
          } else {
              echo '<div style="color:#374151;font-weight:900;font-size:16px;padding:10px 2px;">Inget ankommande gods att visa.</div>';
          }
          ?>
        </div>
      </div>
    </div>

    <div class="footer">
      <div>Uppdateras automatiskt (Ajax)</div>
      <div>Senast renderad: <?php echo date('H:i:s'); ?></div>
    </div>
  </div>
</div>
