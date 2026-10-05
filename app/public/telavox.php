<?php
// telavox.php
// Dashboard för Telavox samtalsstatistik: nyckeltal per kö, agent och månad.
// Vad som räknas som besvarat/övergivet styrs av CTelavoxOutcome.

include_once("top.php");
include_once("telavox_common.php");
include_once("header.php");
tvx_page_start('dashboard', 'Telefonistatistik');

// Förinställda perioder: nyckel => [etikett, från, till]. Standard är föregående månad.
$prevMonth = strtotime('first day of last month');
$periods = array(
    'prev_month' => array('Föregående månad', date('Y-m-01', $prevMonth), date('Y-m-t', $prevMonth)),
    'this_year'  => array('Detta år', date('Y-01-01'), date('Y-m-d')),
    'last_year'  => array('Föregående år', (date('Y') - 1) . '-01-01', (date('Y') - 1) . '-12-31'),
    'custom'     => array('Anpassad period', null, null),
);
$period = (string)($_GET['period'] ?? 'prev_month');
if (!isset($periods[$period])) {
    $period = 'prev_month';
}

$from = $periods[$period][1];
$to   = $periods[$period][2];
if ($period === 'custom') {
    $from = tvx_valid_date($_GET['from'] ?? null) ? $_GET['from'] : $periods['prev_month'][1];
    $to   = tvx_valid_date($_GET['to'] ?? null) ? $_GET['to'] : $periods['prev_month'][2];
}

$filter = array(
    'from'     => $from,
    'to'       => $to,
    'queue_id' => (int)($_GET['queue_id'] ?? 0),
    'agent_id' => (int)($_GET['agent_id'] ?? 0),
);

// Periodtext till rubriken, alltid som datumintervall
$periodLabel = $filter['from'] . ' – ' . $filter['to'];

// Vad årsjämförelsen räknar: alla inkommande, bara besvarade eller bara övergivna kösamtal
$yearTypes = array(
    ''                         => 'Inkommande kösamtal',
    CTelavoxOutcome::ANSWERED  => 'Besvarade kösamtal',
    CTelavoxOutcome::ABANDONED => 'Övergivna kösamtal',
);
$yearType = (string)($_GET['type'] ?? '');
if (!isset($yearTypes[$yearType])) {
    $yearType = '';
}

try {
    $queues   = CTelavoxRepository::getQueues();
    $agents    = CTelavoxRepository::getAgents();
    $dataRange = CTelavoxStats::dateRange();
    $summary   = CTelavoxStats::summary($filter);
    $perQueue  = CTelavoxStats::perQueue($filter);
    $perMonth  = CTelavoxStats::perMonth($filter);
    $perAgent  = CTelavoxStats::perAgent($filter);
    $outcomes  = CTelavoxStats::perOutcome($filter);
    $byYear    = CTelavoxStats::totalsByYearMonth($filter + array('outcome_class' => $yearType));
} catch (\Throwable $e) {
    error_log('telavox dashboard: ' . $e->getMessage());
    echo '<div class="tvx-msg error">Kunde inte läsa statistiken från databasen. Kontrollera att Telavox-tabellerna är skapade.</div>';
    echo "</div>";
    include_once("footer.php");
    exit;
}

// Rubrik ovanför nyckeltalen: vald kö (och användare) samt period
$heading = 'Alla köer';
foreach ($queues as $q) {
    if ((int)$q['id'] === $filter['queue_id']) {
        $heading = $q['name'];
    }
}
foreach ($agents as $a) {
    if ((int)$a['id'] === $filter['agent_id']) {
        $heading .= ' · ' . $a['name'];
    }
}

$monthNames = array('', 'jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec');
$chart = array('labels' => array(), 'answered' => array(), 'abandoned' => array(), 'other' => array(), 'rate' => array());
foreach ($perMonth as $m) {
    $chart['labels'][]    = $monthNames[(int)substr($m['ym'], 5, 2)] . ' ' . substr($m['ym'], 2, 2);
    $chart['answered'][]  = $m['answered'];
    $chart['abandoned'][] = $m['abandoned'];
    $chart['other'][]     = $m['missed'] + $m['other'];
    $chart['rate'][]      = $m['answer_rate'] === null ? null : round($m['answer_rate'] * 100, 1);
}

// Årsjämförelse: de fem senaste åren, en serie per år med tolv månadsvärden (null = ingen data)
$chart['months'] = array_slice($monthNames, 1);
$chart['years']  = array();
foreach (array_slice($byYear, -5, null, true) as $year => $months) {
    $values = array();
    for ($i = 1; $i <= 12; $i++) {
        $values[] = $months[$i] ?? null;
    }
    $chart['years'][] = array('year' => $year, 'values' => $values);
}
?>
<style>
.tvx-filter { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px; margin: 0 0 18px; }
.tvx-filter label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 3px; }
.tvx-wrap h2.tvx-heading { font-size: 20px; margin: 4px 0 12px; padding-left: 14px; color: #111; }
.tvx-wrap h2.tvx-heading span { font-weight: 400; color: #6b7280; margin-left: 6px; }
.tvx-wrap .table-list tr.tvx-diff td { border-top: 2px solid #e5e7eb; font-style: italic; }
.tvx-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin: 0 0 18px; }
.tvx-kpi { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; }
.tvx-kpi .tvx-kpi-label { font-size: 12px; color: #6b7280; }
.tvx-kpi .tvx-kpi-value { font-size: 24px; font-weight: 700; color: #111; margin-top: 2px; }
.tvx-kpi .tvx-kpi-note { font-size: 11px; color: #6b7280; margin-top: 2px; }
.tvx-charts { display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 12px; margin: 0 0 8px; }
.tvx-chart { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; }
.tvx-chart .tvx-chart-title { font-size: 13px; font-weight: 700; color: #111; margin: 0 0 8px; }
.tvx-chart .tvx-chart-canvas { position: relative; height: 260px; }
</style>

<form method="get" class="tvx-filter">
  <div>
    <label for="tvx-period">Period</label>
    <select id="tvx-period" name="period">
      <?php foreach ($periods as $key => $p): ?>
      <option value="<?php echo $key; ?>" data-from="<?php echo tvx_h($p[1]); ?>" data-to="<?php echo tvx_h($p[2]); ?>"<?php if ($key === $period) { ?> selected<?php } ?>><?php echo tvx_h($p[0]); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label for="tvx-from">Från</label>
    <input type="date" id="tvx-from" name="from" value="<?php echo tvx_h($filter['from']); ?>">
  </div>
  <div>
    <label for="tvx-to">Till</label>
    <input type="date" id="tvx-to" name="to" value="<?php echo tvx_h($filter['to']); ?>">
  </div>
  <div>
    <label for="tvx-queue">Kö</label>
    <select id="tvx-queue" name="queue_id">
      <option value="0">Alla köer</option>
      <?php foreach ($queues as $q): ?>
      <option value="<?php echo (int)$q['id']; ?>"<?php if ((int)$q['id'] === $filter['queue_id']) { ?> selected<?php } ?>><?php echo tvx_h($q['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label for="tvx-agent">Användare</label>
    <select id="tvx-agent" name="agent_id">
      <option value="0">Alla användare</option>
      <?php foreach ($agents as $a): ?>
      <option value="<?php echo (int)$a['id']; ?>"<?php if ((int)$a['id'] === $filter['agent_id']) { ?> selected<?php } ?>><?php echo tvx_h($a['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label for="tvx-type">Årsjämförelsen visar</label>
    <select id="tvx-type" name="type">
      <?php foreach ($yearTypes as $key => $label): ?>
      <option value="<?php echo tvx_h($key); ?>"<?php if ($key === $yearType) { ?> selected<?php } ?>><?php echo tvx_h($label); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="tvx-btn">Visa</button>
</form>

<script>
(function(){
  // Förinställd period fyller i datumen; ändras ett datum för hand blir perioden "Anpassad"
  var period = document.getElementById('tvx-period');
  var from = document.getElementById('tvx-from');
  var to = document.getElementById('tvx-to');
  period.addEventListener('change', function(){
    var opt = period.options[period.selectedIndex];
    if (opt.getAttribute('data-from')) {
      from.value = opt.getAttribute('data-from');
      to.value = opt.getAttribute('data-to');
    }
  });
  [from, to].forEach(function(el){
    el.addEventListener('change', function(){ period.value = 'custom'; });
  });
})();
</script>

<h2 class="tvx-heading"><?php echo tvx_h($heading); ?> <span><?php echo tvx_h($periodLabel); ?></span></h2>

<?php if ($dataRange === null): ?>
  <p>Det finns ingen importerad statistik ännu. <a href="telavox_import.php">Importera en fil från Telavox</a> för att komma igång.</p>
<?php elseif ($summary['total'] === 0): ?>
  <p>Inga samtal matchar urvalet. Importerad statistik finns för <?php echo tvx_h($dataRange['first_date']); ?> &ndash; <?php echo tvx_h($dataRange['last_date']); ?>.</p>
<?php else: ?>

<?php if ($filter['agent_id'] > 0): ?>
  <div class="tvx-msg warn">Urvalet gäller en användare. Övergivna samtal når aldrig en användare och ingår därför inte, så svarsfrekvensen är inte jämförbar med köns.</div>
<?php endif; ?>

<div class="tvx-kpis">
  <div class="tvx-kpi">
    <div class="tvx-kpi-label">Inkommande kösamtal</div>
    <div class="tvx-kpi-value"><?php echo tvx_number($summary['total']); ?></div>
  </div>
  <div class="tvx-kpi">
    <div class="tvx-kpi-label">Besvarade</div>
    <div class="tvx-kpi-value"><?php echo tvx_number($summary['answered']); ?></div>
  </div>
  <div class="tvx-kpi">
    <div class="tvx-kpi-label">Övergivna</div>
    <div class="tvx-kpi-value"><?php echo tvx_number($summary['abandoned']); ?></div>
  </div>
  <div class="tvx-kpi">
    <div class="tvx-kpi-label">Svarsfrekvens</div>
    <div class="tvx-kpi-value"><?php echo tvx_percent($summary['answer_rate']); ?></div>
  </div>
  <div class="tvx-kpi">
    <div class="tvx-kpi-label">Snitt kötid</div>
    <div class="tvx-kpi-value"><?php echo tvx_seconds($summary['avg_queue_time']); ?></div>
    <div class="tvx-kpi-note">min:s, exkl. vidarekopplade</div>
  </div>
  <div class="tvx-kpi">
    <div class="tvx-kpi-label">Snitt handläggningstid</div>
    <div class="tvx-kpi-value"><?php echo tvx_seconds($summary['avg_handle_time']); ?></div>
    <div class="tvx-kpi-note">min:s, besvarade samtal</div>
  </div>
</div>

<div class="tvx-chart" style="margin-bottom:12px">
  <div class="tvx-chart-title"><?php echo tvx_h($yearTypes[$yearType]); ?> per år <span class="muted" style="font-weight:400">&ndash; alla importerade år, oberoende av datumfiltret</span></div>
  <div class="tvx-chart-canvas" style="height:340px"><canvas id="tvx-chart-years"></canvas></div>
  <table class="table-list" style="width:100%;margin-top:10px">
    <thead>
      <tr>
        <th>År</th>
        <?php foreach ($chart['months'] as $name): ?><th class="num"><?php echo tvx_h($name); ?></th><?php endforeach; ?>
        <th class="num">Totalt</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($chart['years'] as $y): ?>
      <tr>
        <td><?php echo (int)$y['year']; ?></td>
        <?php foreach ($y['values'] as $v): ?><td class="num"><?php echo $v === null ? '<span class="muted">&ndash;</span>' : tvx_number($v); ?></td><?php endforeach; ?>
        <td class="num"><b><?php echo tvx_number(array_sum($y['values'])); ?></b></td>
      </tr>
    <?php endforeach; ?>
    <?php
    // Differens för senaste året mot året före, bara för månader som finns båda åren
    $yearCount = count($chart['years']);
    if ($yearCount >= 2):
        $cur  = $chart['years'][$yearCount - 1];
        $prev = $chart['years'][$yearCount - 2];
        $diffSum = 0; $prevSum = 0;
    ?>
      <tr class="tvx-diff">
        <td>Diff <?php echo (int)$cur['year']; ?> mot <?php echo (int)$prev['year']; ?></td>
        <?php for ($i = 0; $i < 12; $i++):
            if ($cur['values'][$i] === null || $prev['values'][$i] === null) {
                echo '<td class="num"><span class="muted">&ndash;</span></td>';
                continue;
            }
            $d = $cur['values'][$i] - $prev['values'][$i];
            $diffSum += $d;
            $prevSum += $prev['values'][$i];
            $pct = $prev['values'][$i] > 0 ? ' (' . tvx_signed($d / $prev['values'][$i] * 100, 1) . ' %)' : '';
        ?><td class="num" title="<?php echo tvx_h(tvx_signed($d) . $pct); ?>"><?php echo tvx_signed($d); ?></td><?php endfor; ?>
        <td class="num nowrap" title="Summa för månaderna som finns båda åren"><b><?php echo tvx_signed($diffSum); ?></b><?php
            if ($prevSum > 0) { echo ' <span class="muted">(' . tvx_signed($diffSum / $prevSum * 100, 1) . '&nbsp;%)</span>'; } ?></td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="tvx-charts">
  <div class="tvx-chart">
    <div class="tvx-chart-title">Samtal per månad</div>
    <div class="tvx-chart-canvas"><canvas id="tvx-chart-calls"></canvas></div>
  </div>
  <div class="tvx-chart">
    <div class="tvx-chart-title">Svarsfrekvens per månad (%)</div>
    <div class="tvx-chart-canvas"><canvas id="tvx-chart-rate"></canvas></div>
  </div>
</div>

<h2>Per månad</h2>
<table class="table-list">
  <thead>
    <tr>
      <th>Månad</th>
      <th class="num">Samtal</th>
      <th class="num">Besvarade</th>
      <th class="num">Övergivna</th>
      <th class="num">Övriga</th>
      <th class="num">Svarsfrekvens</th>
      <th class="num">Snitt kötid</th>
      <th class="num">Snitt handläggning</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($perMonth as $m): ?>
    <tr>
      <td><?php echo tvx_h($m['ym']); ?></td>
      <td class="num"><?php echo tvx_number($m['total']); ?></td>
      <td class="num"><?php echo tvx_number($m['answered']); ?></td>
      <td class="num"><?php echo tvx_number($m['abandoned']); ?></td>
      <td class="num"><?php echo tvx_number($m['missed'] + $m['other']); ?></td>
      <td class="num"><?php echo tvx_percent($m['answer_rate']); ?></td>
      <td class="num"><?php echo tvx_seconds($m['avg_queue_time']); ?></td>
      <td class="num"><?php echo tvx_seconds($m['avg_handle_time']); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<h2>Per kö</h2>
<table class="table-list">
  <thead>
    <tr>
      <th>Kö</th>
      <th class="num">Samtal</th>
      <th class="num">Besvarade</th>
      <th class="num">Övergivna</th>
      <th class="num">Övriga</th>
      <th class="num">Svarsfrekvens</th>
      <th class="num">Snitt kötid</th>
      <th class="num">Snitt handläggning</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($perQueue as $q): ?>
    <tr>
      <td><?php echo tvx_h($q['queue_name']); ?></td>
      <td class="num"><?php echo tvx_number($q['total']); ?></td>
      <td class="num"><?php echo tvx_number($q['answered']); ?></td>
      <td class="num"><?php echo tvx_number($q['abandoned']); ?></td>
      <td class="num"><?php echo tvx_number($q['missed'] + $q['other']); ?></td>
      <td class="num"><?php echo tvx_percent($q['answer_rate']); ?></td>
      <td class="num"><?php echo tvx_seconds($q['avg_queue_time']); ?></td>
      <td class="num"><?php echo tvx_seconds($q['avg_handle_time']); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<h2>Per användare</h2>
<?php if (empty($perAgent)): ?>
  <p class="muted">Inga samtal med anknytning i urvalet.</p>
<?php else: ?>
<table class="table-list">
  <thead>
    <tr>
      <th>Användare</th>
      <th class="num">Hanterade samtal</th>
      <th class="num">Snitt handläggning</th>
      <th class="num">Snitt kötid</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($perAgent as $a): ?>
    <tr>
      <td>
        <?php if ($a['agent_id'] === null): ?>
          <a href="telavox_agents.php" class="text-negative">Okänd anknytning: <?php echo tvx_h($a['unknown_extension']); ?></a>
        <?php else: ?>
          <?php echo tvx_h($a['agent_name']); ?>
        <?php endif; ?>
      </td>
      <td class="num"><?php echo tvx_number($a['answered']); ?></td>
      <td class="num"><?php echo tvx_seconds($a['avg_handle_time']); ?></td>
      <td class="num"><?php echo tvx_seconds($a['avg_queue_time']); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<h2>Avslutsstatus</h2>
<table class="table-list">
  <thead>
    <tr><th>Kod</th><th>Betydelse</th><th>Räknas som</th><th class="num">Samtal</th></tr>
  </thead>
  <tbody>
  <?php foreach ($outcomes as $o): ?>
    <tr>
      <td><?php echo tvx_h($o['outcome']); ?></td>
      <td><?php echo tvx_h(CTelavoxOutcome::label($o['outcome'])); ?></td>
      <td><?php echo tvx_h(CTelavoxOutcome::classLabel(CTelavoxOutcome::classOf($o['outcome']))); ?></td>
      <td class="num"><?php echo tvx_number($o['total']); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<p class="muted" style="font-size:12px">Tider visas som min:s. Övriga = vidarekopplade samtal som inte besvarades eller där utfallet är okänt.</p>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function(){
  if (typeof Chart === 'undefined') { return; }
  var data = <?php echo json_encode($chart, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
  var ink = '#52514e', grid = '#e1e0d9', surface = '#ffffff';

  Chart.defaults.font.family = 'Arial, sans-serif';
  Chart.defaults.font.size = 12;
  Chart.defaults.color = ink;

  // Serierna har fasta färger (besvarade/övergivna/övriga) oavsett filter
  function series(label, values, color) {
    return {
      label: label, data: values, backgroundColor: color,
      borderColor: surface, borderWidth: { top: 2, right: 0, bottom: 0, left: 0 },
      maxBarThickness: 32
    };
  }

  // Årsjämförelse: tidigare år som staplar, senaste året som linje.
  // Färgen följer årtalet, så ett år behåller sin färg när nya år tillkommer.
  var yearColors = ['#2a78d6', '#eb6834', '#1baf7a', '#eda100', '#e87ba4', '#008300', '#4a3aa7', '#e34948'];
  var lastYear = data.years.length ? data.years[data.years.length - 1].year : null;
  new Chart(document.getElementById('tvx-chart-years'), {
    data: {
      labels: data.months,
      datasets: data.years.map(function(y){
        var color = yearColors[y.year % yearColors.length];
        if (y.year === lastYear && data.years.length > 1) {
          return {
            type: 'line', label: String(y.year), data: y.values, order: 0,
            borderColor: color, backgroundColor: color, borderWidth: 2,
            pointRadius: 4, pointHoverRadius: 6, pointBorderColor: surface, pointBorderWidth: 2, tension: 0
          };
        }
        return {
          type: 'bar', label: String(y.year), data: y.values, order: 1,
          backgroundColor: color, borderColor: surface, borderWidth: { top: 0, right: 1, bottom: 0, left: 1 },
          borderRadius: { topLeft: 3, topRight: 3 }, categoryPercentage: 0.8, barPercentage: 1
        };
      })
    },
    options: {
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'right', labels: { boxWidth: 12, boxHeight: 12 } } },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: grid }, border: { display: false } }
      }
    }
  });

  new Chart(document.getElementById('tvx-chart-calls'), {
    type: 'bar',
    data: {
      labels: data.labels,
      datasets: [
        series('Besvarade', data.answered, '#2a78d6'),
        series('Övergivna', data.abandoned, '#eb6834'),
        series('Övriga', data.other, '#1baf7a')
      ]
    },
    options: {
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12 } } },
      scales: {
        x: { stacked: true, grid: { display: false } },
        y: { stacked: true, beginAtZero: true, ticks: { precision: 0 }, grid: { color: grid }, border: { display: false } }
      }
    }
  });

  new Chart(document.getElementById('tvx-chart-rate'), {
    type: 'line',
    data: {
      labels: data.labels,
      datasets: [{
        label: 'Svarsfrekvens', data: data.rate,
        borderColor: '#2a78d6', backgroundColor: '#2a78d6',
        borderWidth: 2, pointRadius: 4, pointHoverRadius: 6, tension: 0
      }]
    },
    options: {
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: function(c){ return 'Svarsfrekvens: ' + String(c.parsed.y).replace('.', ',') + ' %'; } } }
      },
      scales: {
        x: { grid: { display: false } },
        y: { min: 0, max: 100, ticks: { callback: function(v){ return v + ' %'; } }, grid: { color: grid }, border: { display: false } }
      }
    }
  });
})();
</script>

<?php endif; ?>

<?php
echo "</div>";
include_once("footer.php");
