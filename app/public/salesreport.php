<?php 
include_once("top.php");
include_once("header.php");

echo "<h1>Försäljningsrapport</h1>";
echo "<div class=\"clear\"></div>\n";

// Sätt dagens datum om inget valt
if ($dagensdatum == '') {
    $dagensdatum = date('Y-m-d');
}

// $details kommer ju från POST sedan tidigare logik
$details = (isset($details) ? $details : '');

// Hämta dagsdata + jämförelse mot fjolårets dag
$dailySummary = $sales->getDailyTurnoverSummary($dagensdatum);
$comparison   = $sales->getDailyTurnoverComparison($dagensdatum);

// Från jämförelsen
$compareDate    = isset($comparison['compare_date']) ? $comparison['compare_date'] : null;
// Årtal att visa i gauge-texten (baserat på jämförelsedatumet)
if (!empty($compareDate)) {
    $compareYear = date('Y', strtotime($compareDate));
} else {
    // fallback om något skulle saknas
    $compareYear = date('Y', strtotime('-1 year', strtotime($dagensdatum)));
}
$todayTotal     = (float)$comparison['today_total'];
$lastYearTotal  = (float)$comparison['last_year_total'];
$dayDiffAmount  = (float)$comparison['diff_amount'];   // kr
$dayDiffPercent = (float)$comparison['diff_percent'];  // t.ex. -7.0

// --- GAUGE-LOGIK (NY  ring = hur nära fjolåret) ---
// Från jämförelsen
$compareDate    = isset($comparison['compare_date']) ? $comparison['compare_date'] : null;
$todayTotal     = (float)$comparison['today_total'];
$lastYearTotal  = (float)$comparison['last_year_total'];
$dayDiffAmount  = (float)$comparison['diff_amount'];   // kr
$dayDiffPercent = (float)$comparison['diff_percent'];  // t.ex. -73.4

// 1) Index mot fjolåret: hur stor andel av fjolårets nivå har vi nått?
if ($lastYearTotal <= 0 && $todayTotal <= 0) {
    $gaugeIndex = 0.0;
} elseif ($lastYearTotal <= 0) {
    // Ingen rimlig jämförelse  betrakta det som 100 %
    $gaugeIndex = 100.0;
} else {
    $gaugeIndex = ($todayTotal / $lastYearTotal) * 100.0;
}
$gaugeIndex = round($gaugeIndex, 1);

// 2) Båglängd enligt mänsklig logik:
//
// - Om vi ligger UNDER fjolåret (index <= 100)
//   => visa hur stor del av fjolårets nivå vi nått (0100 %)
// - Om vi ligger ÖVER fjolåret (index > 100)
//   => visa hur mycket vi passerat fjolåret (0100 %, upp till dubbelt)
if ($gaugeIndex <= 100) {
    // Under målet: t.ex. index 32,3 => 32,3 % av cirkeln
    $gaugePercent = max(0.0, $gaugeIndex);  // 0100
} else {
    // Över målet: t.ex. index 136,1 => diff 36,1 => 36,1 % av cirkeln
    $above        = $gaugeIndex - 100.0;    // 08
    $gaugePercent = max(0.0, min($above, 100.0)); // 0100 (klipper vid +100 %)
}

// 3) SVG-beräkningar
$gaugeRadius        = 54;
$gaugeCircumference = 2 * M_PI * $gaugeRadius;
$gaugeOffset        = $gaugeCircumference * (1 - ($gaugePercent / 100.0));
if ($gaugeOffset < 0) {
    $gaugeOffset = 0;
} elseif ($gaugeOffset > $gaugeCircumference) {
    $gaugeOffset = $gaugeCircumference;
}

// 4) Färgklass: grön vid plus, röd/orange vid minus
$gaugeTrendClass = ($dayDiffPercent >= 0) ? 'gauge-positive' : 'gauge-negative';


// Visa FI/NO om man väljer datum t.o.m. 2016-06-15 (din cutoff)
$showNordics = (strtotime("2016-06-15") >= strtotime($dagensdatum));

// Hjälpfunktion för diff-text + klass
if (!function_exists('salesPctDiffData')) {
    function salesPctDiffData($today, $last) {
        if ($last == 0) {
            return [
                'value' => 0,
                'label' => '0%',
                'class' => 'diff-neutral'
            ];
        }
        $pct = (($today - $last) / $last) * 100;
        $label = number_format($pct, 1, ',', ' ') . '%';

        if ($pct > 0.0001) {
            $class = 'diff-positive';
        } elseif ($pct < -0.0001) {
            $class = 'diff-negative';
        } else {
            $class = 'diff-neutral';
        }

        return [
            'value' => $pct,
            'label' => $label,
            'class' => $class
        ];
    }
}
?>

<div class="sales-dashboard">
    <!-- Vänsterpanel: datumval + tabell / detaljer -->
    <div class="sales-panel-left">
        <div class="sales-filter-bar">
            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="show" value="overall">

                <div class="filter-group">
                    <label for="sales-date" class="filter-label">Annat datum:</label>
                    <input
                        type="date"
                        id="sales-date"
                        name="firstinput"
                        value="<?= htmlspecialchars($dagensdatum); ?>"
                        class="filter-input"
                    >
                    <?php if ($ref_dagensdatum != $dagensdatum): ?>
                        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?show=overall" class="filter-link">
                            Idag
                        </a>
                    <?php endif; ?>
                </div>

                <div class="filter-group">
                    <label class="filter-checkbox-label">
                        <input
                            type="checkbox"
                            name="details"
                            value="yes"
                            <?php if ($details === 'yes') echo 'checked'; ?>
                        >
                        Visa detaljer
                    </label>
                </div>

                <div class="filter-actions">
                    <input type="submit" value="Visa" class="filter-button">
                </div>
            </form>
        </div>

        <h2 class="panel-title">
            Leveranser från CyberPhoto  <?= htmlspecialchars($dagensdatum); ?>
        </h2>

        <?php if ($details === 'yes'): ?>

            <?php
            // Detaljvy: lista utleveranser
            $deliveries = $sales->getDailyDeliveriesDetails($dagensdatum);
            ?>

            <div class="sales-details-card">
			<?php if (empty($deliveries)): ?>
				<p class="no-data">Inga utleveranser för valt datum.</p>
			<?php else: ?>
				<table class="sales-detail-table">
					<thead>
					<tr>
						<th>Ordernr</th>
						<th>Tidpunkt</th>
						<th>Av</th>
						<th class="text-right">Totalt</th>
						<th>Valuta</th>
						<th class="text-right">Omräknat SEK</th>
						<th>Typ</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($deliveries as $row): ?>
						<tr>
							<td>
								<?php if (!empty($row['order_no'])): ?>
									<?php
									$orderUrl = '/search_dispatch.php'
											  . '?mode=order&page=1&q=' . rawurlencode($row['order_no']);
									?>
									<a href="<?= htmlspecialchars($orderUrl); ?>" target="_blank">
										<?= htmlspecialchars($row['order_no']); ?>
									</a>
								<?php endif; ?>
							</td>
							<td><?= date('Y-m-d H:i:s', strtotime($row['timestamp'])); ?></td>
							<td><?= strtoupper(htmlspecialchars($row['user_code'])); ?></td>
							<td class="text-right">
								<?= number_format($row['amount'], 0, ',', ' '); ?>
							</td>
							<td><?= htmlspecialchars($row['currency']); ?></td>
							<td class="text-right">
								<?= number_format($row['amount_sek'], 0, ',', ' '); ?>
							</td>
							<td>
								<?php if ($row['via'] === 'P'): ?>
									<span class="summary-dot summary-dot-store"></span>Lagershop
								<?php else: ?>
									<span class="summary-dot summary-dot-ship"></span>Speditör
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
            </div>

        <?php else: ?>

		<?php
		// Data till jämförelsetabellen
		$todayStats = isset($comparison['today']) ? $comparison['today'] : $dailySummary;
		$lastStats  = isset($comparison['last_year']) ? $comparison['last_year'] : null;
		?>

		<div class="comparison-table-card">
			<?php if (empty($lastStats)): ?>
				<p class="no-data">Ingen historik för föregående år att jämföra med.</p>
			<?php else: ?>
				<h3>Jämförelse med föregående år</h3>
				<table class="comparison-table">
					<thead>
					<tr>
						<th></th>
						<th><?= htmlspecialchars($todayStats['date']); ?></th>
						<th><?= htmlspecialchars($lastStats['date']); ?></th>
						<th class="text-right">Diff SEK</th>
						<th class="text-right">Diff antal</th>
					</tr>
					</thead>
					<tbody>
					<?php
					// Belopp
					$diffTotalAmount    = salesPctDiffData($todayStats['total_amount_sek'], $lastStats['total_amount_sek']);
					$diffShipAmount     = salesPctDiffData($todayStats['ship_amount'],      $lastStats['ship_amount']);
					$diffStoreAmount    = salesPctDiffData($todayStats['warehouse_amount'], $lastStats['warehouse_amount']);
					$diffAvgOrderAmount = salesPctDiffData($todayStats['avg_order_value'],  $lastStats['avg_order_value']);

					// Antal
					$diffTotalOrders = salesPctDiffData($todayStats['total_orders'],    $lastStats['total_orders']);
					$diffShipOrders  = salesPctDiffData($todayStats['ship_orders'],     $lastStats['ship_orders']);
					$diffStoreOrders = salesPctDiffData($todayStats['warehouse_orders'], $lastStats['warehouse_orders']);
					?>
					<tr>
						<td>
							<span class="summary-dot summary-dot-total"></span>
							<strong>Totalt</strong>
						</td>
						<td><?= number_format($todayStats['total_amount_sek'], 0, ',', ' ') ?> SEK (<?= (int)$todayStats['total_orders'] ?>)</td>
						<td><?= number_format($lastStats['total_amount_sek'], 0, ',', ' ') ?> SEK (<?= (int)$lastStats['total_orders'] ?>)</td>
						<td class="text-right diff-cell <?= $diffTotalAmount['class']; ?>">
							<?= $diffTotalAmount['label']; ?>
						</td>
						<td class="text-right diff-cell <?= $diffTotalOrders['class']; ?>">
							<?= $diffTotalOrders['label']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="summary-dot summary-dot-ship"></span>
							Skickas speditör
						</td>
						<td><?= number_format($todayStats['ship_amount'], 0, ',', ' ') ?> SEK (<?= (int)$todayStats['ship_orders'] ?>)</td>
						<td><?= number_format($lastStats['ship_amount'], 0, ',', ' ') ?> SEK (<?= (int)$lastStats['ship_orders'] ?>)</td>
						<td class="text-right diff-cell <?= $diffShipAmount['class']; ?>">
							<?= $diffShipAmount['label']; ?>
						</td>
						<td class="text-right diff-cell <?= $diffShipOrders['class']; ?>">
							<?= $diffShipOrders['label']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="summary-dot summary-dot-store"></span>
							Hämtas lagershop
						</td>
						<td><?= number_format($todayStats['warehouse_amount'], 0, ',', ' ') ?> SEK (<?= (int)$todayStats['warehouse_orders'] ?>)</td>
						<td><?= number_format($lastStats['warehouse_amount'], 0, ',', ' ') ?> SEK (<?= (int)$lastStats['warehouse_orders'] ?>)</td>
						<td class="text-right diff-cell <?= $diffStoreAmount['class']; ?>">
							<?= $diffStoreAmount['label']; ?>
						</td>
						<td class="text-right diff-cell <?= $diffStoreOrders['class']; ?>">
							<?= $diffStoreOrders['label']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="summary-dot summary-dot-avg"></span>
							Snittorder
						</td>
						<td><?= number_format($todayStats['avg_order_value'], 0, ',', ' ') ?> SEK</td>
						<td><?= number_format($lastStats['avg_order_value'], 0, ',', ' ') ?> SEK</td>
						<td class="text-right diff-cell <?= $diffAvgOrderAmount['class']; ?>">
							<?= $diffAvgOrderAmount['label']; ?>
						</td>
						<td class="text-right diff-cell">
							&nbsp;
						</td>
					</tr>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

        <?php endif; ?>
    </div>

    <!-- Högerpanel: mätare vs fjolåret -->
    <div class="sales-panel-right">
        <div class="kpi-card gauge-card <?= $gaugeTrendClass; ?>">
            <div class="kpi-header">
                Skillnad motsvarande dag i fjol
            </div>

            <div class="kpi-amount <?= $gaugeTrendClass; ?>">
                <?= ($dayDiffAmount >= 0 ? '+' : ''); ?>
                <?= number_format($dayDiffAmount, 0, ',', ' '); ?> kr
                <span class="kpi-percent">
                    (<?= number_format($dayDiffPercent, 1, ',', ' '); ?>%)
                </span>
            </div>

            <?php
            $compareUrl = '';
            if (!empty($compareDate)) {
                $compareUrl = htmlspecialchars($_SERVER['PHP_SELF'])
                            . '?show=overall&firstinput=' . urlencode($compareDate);
            }
            ?>
            <div class="kpi-subtitle">
                Jämför <?= htmlspecialchars($dagensdatum); ?>
                <?php if (!empty($compareDate)): ?>
                    med <a href="<?= $compareUrl; ?>" class="kpi-link-date">
                        <?= htmlspecialchars($compareDate); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="gauge-donut">
                <svg viewBox="0 0 140 140" class="gauge-svg" aria-hidden="true">
                    <!-- Bakgrundsringen -->
                    <circle
                        class="gauge-track"
                        cx="70" cy="70" r="54"
                        stroke-width="12"
                    ></circle>

                    <!-- Fyllda delen: visar index (t.ex. 93% av fjolåret) -->
                    <circle
                        class="gauge-progress <?= $gaugeTrendClass; ?>"
                        cx="70" cy="70" r="54"
                        stroke-width="12"
                        stroke-dasharray="<?= $gaugeCircumference; ?>"
                        stroke-dashoffset="<?= $gaugeOffset; ?>"
                    ></circle>
                </svg>

				<div class="gauge-center">
					<div class="gauge-label">Differens</div>
					<div class="gauge-value">
						<?= ($dayDiffPercent >= 0 ? '+' : '') . number_format($dayDiffPercent, 1, ',', ' '); ?>%
					</div>
					<div class="gauge-scale">
						0  100% vs <?= htmlspecialchars($compareYear); ?>
					</div>
				</div>

            </div>

			<div class="gauge-legend">
				<span>0%</span>
				<span>50%</span>
				<span>100%</span>
			</div>

        </div>
    </div>
</div>

<div class="progress-container">
    <!-- Arbetad tid -->
    <div class="progress-section">
        <h2>Arbetad tid denna månad</h2>
        <div class="progress-bar work-progress">
            <div class="progress-done work-animate" style="width: <?= min($work_percentage, 100) ?>%;">
                <?= $work_percentage ?>%
            </div>
            <div class="progress-left" style="width: <?= max(100 - $work_percentage, 0) ?>%;">
                <?= max(100 - $work_percentage, 0) ?>%
            </div>
        </div>
        <p class="progress-summary">Arbetade timmar: <strong><?= $worked_hours ?>h</strong> av totalt <strong><?= $total_hours ?>h</strong>  
        (Återstår: <strong><?= $remaining_hours ?>h</strong>)</p>
    </div>

    <!-- Försäljning -->
    <div class="progress-section">
        <h2>Försäljning denna månad jämfört med <?php echo $manader[date('n')]; ?> <?php echo date('Y', strtotime('-1 year')); ?> (uppdateras var 15:e minut)</h2>
        <div class="progress-bar sales-progress">
            <div class="progress-done sales-animate" style="width: <?= min($sales_percentage, 100) ?>%;">
                <?= $sales_percentage ?>%
            </div>
            <div class="progress-left" style="width: <?= max(100 - $sales_percentage, 0) ?>%;">
                <?= max(100 - $sales_percentage, 0) ?>%
            </div>
        </div>
        <p class="progress-summary">Försäljning hittills: <strong><?= number_format($sales_this_year, 0, ',', ' ') ?> kr</strong>  
        (<?php echo $manader[date('n')]; ?> <?php echo date('Y', strtotime('-1 year')); ?> totalt: <strong><?= number_format($sales_last_year, 0, ',', ' ') ?> kr</strong>)
        <?php if ($differens_between_years > 0) { ?>
            Differens: <strong style="color: green;"><?= number_format($differens_between_years, 0, ',', ' ') ?> kr</strong></p>
        <?php } else { ?>
            Differens: <strong style="color: red;"><?= number_format($differens_between_years, 0, ',', ' ') ?> kr</strong></p>
        <?php } ?>
    </div>
</div>

<?php
include_once("footer.php");
?>
