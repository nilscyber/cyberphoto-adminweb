<?php
include_once("top.php");

// ------------------------
// Input
// ------------------------
$queue  = isset($_GET['queue']) ? (string)$_GET['queue'] : ''; // UTF-8 value i URL
$closed = isset($_GET['closed']) ? (string)$_GET['closed'] : '';

$firstinput  = isset($_GET['firstinput']) ? (string)$_GET['firstinput'] : '';
$secondinput = isset($_GET['secondinput']) ? (string)$_GET['secondinput'] : '';

function normDate($s, $fallbackTs) {
    $s = trim((string)$s);
    if ($s === '') return date('Y-m-d', $fallbackTs);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return date('Y-m-d', $fallbackTs);
    return $s;
}

$firstinput  = normDate($firstinput, strtotime('-7 days'));
$secondinput = normDate($secondinput, strtotime('-1 day'));

$aktuelltDatum = date("Y-m-d");

// ------------------------
// Fetch queues for UI (UTF-8 från DB)
// ------------------------
$topQueues = $otrs->getTopQueues();
if (!is_array($topQueues)) $topQueues = array();

// scope-titel
$queueTitle = ($queue !== '') ? $queue : 'Alla köer';

// UI kör Latin-1 ? decoda allt som kommer från DB/UTF-8
$queueTitleOut = ($queue !== '' ? $queueTitle : 'Alla köer');

// ------------------------
// Fetch data (queue är UTF-8)
// ------------------------
$arrSent         = $otrs->getSentEmails2($aktuelltDatum, $queue);
$arrClosed       = $otrs->getClosedTickets2($aktuelltDatum, $queue);
$arrSentPeriod   = $otrs->getSentEmailsPeriod2($firstinput, $secondinput, $queue);
$arrClosedPeriod = $otrs->getClosedTicketsPerdiod2($firstinput, $secondinput, $queue);

if (!is_array($arrSent))         $arrSent = array();
if (!is_array($arrClosed))       $arrClosed = array();
if (!is_array($arrSentPeriod))   $arrSentPeriod = array();
if (!is_array($arrClosedPeriod)) $arrClosedPeriod = array();

include_once("header.php");

// ------------------------
// Helpers
// ------------------------
function colorBar($countBar) {
    if ($countBar == 0) return '#33CC33';
    elseif ($countBar == 1) return '#FFCC66';
    elseif ($countBar == 2) return '#0066FF';
    elseif ($countBar == 3) return '#FF6666';
    elseif ($countBar == 4) return '#CC00CC';
    elseif ($countBar == 5) return '#666666';
    elseif ($countBar == 6) return '#FFFF00';
    elseif ($countBar == 7) return '#996633';
    elseif ($countBar == 8) return '#008080';
    elseif ($countBar == 9) return '#0000CC';
    elseif ($countBar == 10) return '#FF0000';
    elseif ($countBar == 11) return '#99CC00';
    elseif ($countBar == 12) return '#990099';
    else return '#000000';
}

function buildColors($count) {
    $colors = array();
    for ($i=0; $i<$count; $i++) $colors[] = colorBar($i);
    return $colors;
}

function buildChartPayload($rows) {
    $max = 0;
    $series = array();
    foreach ($rows as $r) {
        $v = isset($r->noOfTickets) ? (int)$r->noOfTickets : 0;
        $u = isset($r->usr) ? (string)$r->usr : '';
        if ($v > $max) $max = $v;
        $series[] = array('label' => $u.' ('.$v.')', 'value' => $v);
    }
    return array('max'=>$max, 'series'=>$series);
}

$payloadSentToday    = buildChartPayload($arrSent);
$payloadClosedToday  = buildChartPayload($arrClosed);
$payloadSentPeriod   = buildChartPayload($arrSentPeriod);
$payloadClosedPeriod = buildChartPayload($arrClosedPeriod);

// ------------------------
// Styles
// ------------------------
echo <<<CSS
<style>
.tickets-wrap{max-width:1200px}
.tickets-muted{color:#6b7280;font-size:12px;margin-bottom:10px}

.otrs-scope{
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:10px;
  padding:16px 18px;
  margin-bottom:22px;
  max-width: 980px;
}

.scope-row.scope-queues{
  display:grid;
  grid-template-columns: 1fr 260px;
  gap:18px;
  align-items:start;
}

.group-title{
  display:block;
  font-size:12px;
  font-weight:900;
  color:#374151;
  margin-bottom:8px;
}

.radio-grid{
  display:grid;
  grid-template-columns: repeat(2, minmax(200px, 1fr));
  gap:6px 18px;
}
.radio-grid label,
.checkbox{font-size:13px;color:#111827;cursor:pointer;user-select:none}

.scope-options{
  max-width:260px;
  border-left:1px solid #e5e7eb;
  padding-left:16px;
  display:flex;
  flex-direction:column;
  gap:10px;
}

.scope-field{
  display:flex;
  flex-direction:column;
  gap:6px;
  width:100%;
}

.scope-field label{
  font-size:12px;
  font-weight:800;
  color:#374151;
}

.scope-field input[type='date']{
  height:34px;
  padding:4px 8px;
  border:1px solid #d1d5db;
  border-radius:8px;
  font-size:13px;
  background:#fff;
  width:100%;
}

.btn-primary{
  height:34px;
  padding:0 14px;
  background:#111827;
  color:#fff;
  border:none;
  border-radius:8px;
  font-size:13px;
  font-weight:800;
  cursor:pointer;
  min-width:110px;
}
.btn-primary:hover{background:#000}
.scope-options .btn-primary{width:100%;margin-top:6px}

.tickets-grid{display:grid;grid-template-columns:1fr;gap:18px}
.tickets-card{
  background:#fff;border:1px solid #e5e7eb;border-radius:12px;
  padding:14px 16px;box-shadow:0 1px 2px rgba(0,0,0,.04)
}
.tickets-card h3{margin:0 0 10px 0;font-size:16px}
.chart-box{min-height:420px}
.chart-empty{
  padding:14px;border:1px dashed #d1d5db;border-radius:10px;
  color:#6b7280;background:#fafafa
}
</style>
CSS;

echo "<div class='tickets-wrap'>";
echo "<h1>Statistik - OTRS</h1>";
echo "<div class='tickets-muted'>Scope: <strong>"
   . htmlspecialchars($queueTitleOut, ENT_QUOTES, 'UTF-8')
   . "</strong></div>";
?>

<div class="otrs-scope">
  <form method="get" action="" id="scopeForm">

    <div class="scope-row scope-queues">
      <div class="scope-group">
        <label class="group-title">Välj kö</label>

        <div class="radio-grid">
          <?php
          // Alla (tomt value = ingen filter)
          $chkAll = ($queue === '') ? 'checked' : '';
          echo '<label><input type="radio" name="queue" value="" '.$chkAll.' onchange="this.form.submit()"> Alla</label>';

          foreach ($topQueues as $nmUtf8) {
              $nmUtf8 = (string)$nmUtf8;               // UTF-8 från DB
              $nmOut  = $nmUtf8;          // Latin-1 till UI
              $chk    = ($queue === $nmUtf8) ? 'checked' : '';

              // VIKTIGT:
              // - value är UTF-8 (så DB-match funkar)
              // - label visas som Latin-1
              echo '<label>'
                 . '<input type="radio" name="queue" value="'.htmlspecialchars($nmUtf8, ENT_QUOTES, 'UTF-8').'" '.$chk.' onchange="this.form.submit()"> '
                 . htmlspecialchars($nmOut, ENT_QUOTES, 'UTF-8')
                 . '</label>';
          }
          ?>
        </div>
      </div>

      <div class="scope-group scope-options">
        <label class="group-title">Datum</label>

        <div class="scope-field">
          <label>Från datum</label>
          <input type="date" name="firstinput" value="<?php echo htmlspecialchars($firstinput, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="scope-field">
          <label>Till datum</label>
          <input type="date" name="secondinput" value="<?php echo htmlspecialchars($secondinput, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <button type="submit" class="btn-primary">Rapport</button>

        <label class="group-title" style="margin-top:12px;">Visa</label>
        <label class="checkbox">
          <input type="checkbox" name="closed" value="yes" <?php echo ($closed === 'yes') ? 'checked' : ''; ?> onchange="this.form.submit()">
          Visa även stängda ärenden
        </label>
      </div>
    </div>

  </form>
</div>

<div class="tickets-grid">
  <div class="tickets-card">
    <h3>Idag (<?php echo htmlspecialchars($aktuelltDatum, ENT_QUOTES, 'UTF-8'); ?>)  Skickade mail på ärenden</h3>
    <div id="chart_Sent" class="chart-box"></div>
  </div>

  <?php if ($closed === "yes") { ?>
  <div class="tickets-card">
    <h3>Idag (<?php echo htmlspecialchars($aktuelltDatum, ENT_QUOTES, 'UTF-8'); ?>)  Stängda ärenden</h3>
    <div id="chart_Closed" class="chart-box"></div>
  </div>
  <?php } ?>

  <div class="tickets-card">
    <h3>Period (<?php echo htmlspecialchars($firstinput, ENT_QUOTES, 'UTF-8'); ?> till <?php echo htmlspecialchars($secondinput, ENT_QUOTES, 'UTF-8'); ?>)  Skickade mail på ärenden</h3>
    <div id="chart_Sent_period" class="chart-box"></div>
  </div>

  <?php if ($closed === "yes") { ?>
  <div class="tickets-card">
    <h3>Period (<?php echo htmlspecialchars($firstinput, ENT_QUOTES, 'UTF-8'); ?> till <?php echo htmlspecialchars($secondinput, ENT_QUOTES, 'UTF-8'); ?>)  Stängda ärenden</h3>
    <div id="chart_Closed_period" class="chart-box"></div>
  </div>
  <?php } ?>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
(function(){
  google.charts.load('current', {packages:['corechart']});
  google.charts.setOnLoadCallback(function(){

    drawColumnChart('chart_Sent',
      <?php echo json_encode($payloadSentToday); ?>,
      <?php echo json_encode(buildColors(count($payloadSentToday['series']))); ?>
    );

    <?php if ($closed === "yes") { ?>
    drawColumnChart('chart_Closed',
      <?php echo json_encode($payloadClosedToday); ?>,
      <?php echo json_encode(buildColors(count($payloadClosedToday['series']))); ?>
    );
    <?php } ?>

    drawColumnChart('chart_Sent_period',
      <?php echo json_encode($payloadSentPeriod); ?>,
      <?php echo json_encode(buildColors(count($payloadSentPeriod['series']))); ?>
    );

    <?php if ($closed === "yes") { ?>
    drawColumnChart('chart_Closed_period',
      <?php echo json_encode($payloadClosedPeriod); ?>,
      <?php echo json_encode(buildColors(count($payloadClosedPeriod['series']))); ?>
    );
    <?php } ?>
  });

  function drawColumnChart(elId, payload, colors) {
    var el = document.getElementById(elId);
    if (!el) return;

    if (!payload || !payload.series || payload.series.length === 0) {
      el.innerHTML = '<div class="chart-empty"><strong>Inga data</strong><br>Inget att visualisera för valt urval. (Bra nyhet för inkorgen, mindre kul för graferna.)</div>';
      return;
    }

    var data = new google.visualization.DataTable();
    data.addColumn('string', '');
    for (var i=0; i<payload.series.length; i++) {
      data.addColumn('number', payload.series[i].label);
    }

    data.addRows(1);
    data.setValue(0, 0, '');
    for (var c=0; c<payload.series.length; c++) {
      data.setValue(0, c+1, Number(payload.series[c].value || 0));
    }

    var options = {
      width: 1100,
      height: 420,
      chartArea: {left: 50, top: 40, width: "70%", height: "70%"},
      legend: { position: 'right', textStyle: { color: 'black', fontName: 'verdana', fontSize: 12 } },
      minValue: 0,
      is3D: true
    };

    if (colors && colors.length) {
      options.colors = colors;
    }

    var chart = new google.visualization.ColumnChart(el);
    chart.draw(data, options);
  }
})();
</script>

<?php
echo "</div>";
include_once("footer.php");
?>
