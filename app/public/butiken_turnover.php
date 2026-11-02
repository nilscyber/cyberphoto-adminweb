<?php
	include_once("top.php");
	include_once("header.php");
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Butiken – omsättning &amp; pling</h1>\n";

	$turnover = $butiken->getTurnoverButikenData();
	$pling    = $butiken->getPlingButikenData();

	$months = array_unique(array_merge(array_keys($turnover), array_keys($pling)));
	rsort($months);

	if (count($months) > 0) {

		echo "<table class=\"table-list\">\n";
		echo "\t<thead>\n";
		echo "\t\t<tr>\n";
		echo "\t\t\t<th>Månad</th>\n";
		echo "\t\t\t<th>Ordrar i butik</th>\n";
		echo "\t\t\t<th>Omsättning</th>\n";
		echo "\t\t\t<th>Pling</th>\n";
		echo "\t\t</tr>\n";
		echo "\t</thead>\n";
		echo "\t<tbody>\n";

		foreach ($months as $month) {
			$antal = isset($turnover[$month]) ? $turnover[$month]['antal'] : "";
			$total = isset($turnover[$month]) ? number_format($turnover[$month]['total'], 0, ',', ' ') : "";
			$plingAntal = isset($pling[$month]) ? $pling[$month] : "";

			echo "\t\t<tr>\n";
			echo "\t\t\t<td>" . htmlspecialchars($month) . "</td>\n";
			echo "\t\t\t<td>$antal</td>\n";
			echo "\t\t\t<td>$total</td>\n";
			echo "\t\t\t<td>$plingAntal</td>\n";
			echo "\t\t</tr>\n";
		}

		echo "\t</tbody>\n";
		echo "</table>\n";

	}

	// Plingar per halvtimme, med samma periodväljare som telefonstatistiken
	include_once("telavox_common.php");
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

	$perSlot = $butiken->getPlingPerHalfHour($from, $to);
	$slotLabels = array();
	$slotAvg = array();
	foreach ($perSlot['slots'] as $i => $n) {
		$slotLabels[] = sprintf('%02d:%02d', intdiv($i, 2), ($i % 2) * 30);
		$slotAvg[]    = $perSlot['days'] > 0 ? round($n / $perSlot['days'], 1) : 0;
	}

	echo "<h2>Pling per halvtimme <span class=\"muted\" style=\"font-weight:400\">&ndash; " . tvx_h($from) . " – " . tvx_h($to) . "</span></h2>\n";
?>
<form method="get" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:12px;margin:0 0 14px">
  <div>
    <label for="tvx-period" style="display:block;font-size:12px;font-weight:600;margin-bottom:3px">Period</label>
    <select id="tvx-period" name="period">
      <?php foreach ($periods as $key => $p): ?>
      <option value="<?php echo $key; ?>" data-from="<?php echo tvx_h($p[1]); ?>" data-to="<?php echo tvx_h($p[2]); ?>"<?php if ($key === $period) { ?> selected<?php } ?>><?php echo tvx_h($p[0]); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label for="tvx-from" style="display:block;font-size:12px;font-weight:600;margin-bottom:3px">Från</label>
    <input type="date" id="tvx-from" name="from" value="<?php echo tvx_h($from); ?>">
  </div>
  <div>
    <label for="tvx-to" style="display:block;font-size:12px;font-weight:600;margin-bottom:3px">Till</label>
    <input type="date" id="tvx-to" name="to" value="<?php echo tvx_h($to); ?>">
  </div>
  <button type="submit" class="tvx-btn">Visa</button>
</form>
<script>
(function(){
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
<?php if ($perSlot['days'] === 0): ?>
  <p class="muted">Inga plingar under vald period.</p>
<?php else: ?>
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;margin-bottom:12px">
  <div style="font-size:13px;font-weight:700;margin:0 0 8px">Snitt antal pling per halvtimme <span class="muted" style="font-weight:400">&ndash; snitt över dagar med pling</span></div>
  <div style="position:relative;height:260px"><canvas id="pling-chart-slots"></canvas></div>
</div>
<table class="table-list">
  <thead>
    <tr><th>Tid</th><th class="num">Pling</th><th class="num">Snitt per dag</th></tr>
  </thead>
  <tbody>
  <?php foreach ($perSlot['slots'] as $i => $n):
      if ($n === 0) { continue; }
      $start = sprintf('%02d:%02d', intdiv($i, 2), ($i % 2) * 30);
      $end   = sprintf('%02d:%02d', intdiv($i + 1, 2) % 24, (($i + 1) % 2) * 30);
  ?>
    <tr>
      <td><?php echo $start . ' &ndash; ' . $end; ?></td>
      <td class="num"><?php echo tvx_number($n); ?></td>
      <td class="num"><?php echo number_format($n / $perSlot['days'], 1, ',', ' '); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<p class="muted" style="font-size:12px">Snitt per dag = pling i intervallet delat med antal dagar i perioden som har pling.</p>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function(){
  if (typeof Chart === 'undefined') { return; }
  Chart.defaults.font.family = 'Arial, sans-serif';
  Chart.defaults.font.size = 12;
  Chart.defaults.color = '#52514e';
  new Chart(document.getElementById('pling-chart-slots'), {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($slotLabels); ?>,
      datasets: [{ label: 'Snitt pling per dag', data: <?php echo json_encode($slotAvg); ?>, backgroundColor: '#2a78d6', maxBarThickness: 24, borderRadius: { topLeft: 3, topRight: 3 } }]
    },
    options: {
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { maxRotation: 90, autoSkip: false } },
        y: { beginAtZero: true, grid: { color: '#e1e0d9' }, border: { display: false } }
      }
    }
  });
})();
</script>
<?php endif; ?>
<?php
	include_once("footer.php");
?>
