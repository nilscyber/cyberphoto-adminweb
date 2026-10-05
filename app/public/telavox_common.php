<?php
// telavox_common.php
// Gemensamt för Telavox-statistikens sidor: behörighetskontroll, hjälpfunktioner,
// flikrad och stilar. Inkluderas direkt efter top.php, före eventuell POST-hantering.

if (!CCheckIP::hasPermission('telavox_stats')) {
    exit;
}

function tvx_h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/** Spara ett meddelande som visas på nästa sidvisning. $type: ok | error */
function tvx_flash($type, $text)
{
    $_SESSION['telavox_flash'] = array('type' => $type, 'text' => $text);
}

function tvx_redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function tvx_valid_date($date)
{
    return is_string($date)
        && preg_match('/^(\d{4})-(\d\d)-(\d\d)$/', $date, $m)
        && checkdate((int)$m[2], (int)$m[3], (int)$m[1]);
}

/** Sekunder som m:ss, eller streck om värdet saknas. */
function tvx_seconds($seconds)
{
    if ($seconds === null) {
        return '&ndash;';
    }
    $seconds = (int)round($seconds);
    return intdiv($seconds, 60) . ':' . str_pad((string)($seconds % 60), 2, '0', STR_PAD_LEFT);
}

/** Tal med tecken, t.ex. "+123" eller "−45" (äkta minustecken). */
function tvx_signed($n, $decimals = 0)
{
    $n = round($n, $decimals);
    $abs = number_format(abs($n), $decimals, ',', "\u{00A0}");
    return ($n > 0 ? '+' : ($n < 0 ? "\u{2212}" : '')) . $abs;
}

function tvx_percent($ratio)
{
    return $ratio === null ? '&ndash;' : number_format($ratio * 100, 1, ',', '') . ' %';
}

function tvx_number($n)
{
    return number_format((float)$n, 0, ',', "\u{00A0}");
}

/** Skriver ut stilar, rubrik, flikrad och eventuellt flash-meddelande. Anropas efter header.php. */
function tvx_page_start($active, $title)
{
    $tabs = array(
        'dashboard' => array('telavox.php', 'Dashboard'),
        'import'    => array('telavox_import.php', 'Import'),
        'queues'    => array('telavox_queues.php', 'Köer'),
        'agents'    => array('telavox_agents.php', 'Användare'),
        'imports'   => array('telavox_imports.php', 'Importhistorik'),
    );

    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
    ?>
<style>
.tvx-wrap { padding: 14px 16px; max-width: 1200px; }
.tvx-tabs { display: flex; flex-wrap: wrap; gap: 4px; margin: 0 0 16px; border-bottom: 1px solid #e5e7eb; }
.tvx-tabs a { padding: 7px 14px; font-size: 13px; font-weight: 600; color: #374151; text-decoration: none; border-radius: 5px 5px 0 0; }
.tvx-tabs a:hover { background: #f3f4f6; }
.tvx-tabs a.active { background: #0d9488; color: #fff; }
.tvx-sub { color: #6b7280; font-size: 13px; margin: 0 0 14px; max-width: 800px; }
.tvx-wrap h2 { font-size: 15px; margin: 22px 0 8px; }

.tvx-msg { padding: 9px 12px; border-radius: 6px; font-size: 13px; margin: 0 0 14px; border: 1px solid; max-width: 800px; }
.tvx-msg.ok    { background: #dcfce7; color: #166534; border-color: #86efac; }
.tvx-msg.error { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.tvx-msg.warn  { background: #fef9c3; color: #854d0e; border-color: #fde047; }
.tvx-msg ul { margin: 6px 0 0 18px; padding: 0; }

.tvx-card { display: inline-block; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.07); padding: 18px 22px 14px; margin: 0 16px 18px 0; vertical-align: top; }
.tvx-card .tvx-card-title { font-size: 15px; font-weight: 700; color: #111; margin: 0 0 14px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; }
.tvx-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.tvx-row > label { flex: 0 0 130px; font-size: 13px; font-weight: 600; color: #374151; }
.tvx-wrap input[type="text"], .tvx-wrap input[type="date"], .tvx-wrap select {
  font-size: 13px; font-family: Arial, sans-serif; padding: 5px 8px; border: 1px solid #d1d5db; border-radius: 4px; color: #111; background: #fff; box-sizing: border-box;
}
.tvx-wrap input[type="text"]:focus, .tvx-wrap input[type="date"]:focus, .tvx-wrap select:focus { outline: none; border-color: #2dd4bf; box-shadow: 0 0 0 2px rgba(45,212,191,.18); }
.tvx-card input[type="text"], .tvx-card select { width: 240px; }
.tvx-checks { display: flex; flex-direction: column; gap: 4px; font-size: 13px; }

.tvx-btn { font-size: 13px; font-weight: 700; font-family: Arial, sans-serif; padding: 6px 18px; border: none; border-radius: 5px; background: #0d9488; color: #fff; cursor: pointer; text-decoration: none; display: inline-block; }
.tvx-btn:hover { background: #0f766e; }
.tvx-btn[disabled] { background: #9ca3af; cursor: not-allowed; }
.tvx-btn.secondary { background: #fff; color: #374151; border: 1px solid #d1d5db; font-weight: 600; }
.tvx-btn.secondary:hover { background: #f3f4f6; }
.tvx-btn.small { padding: 3px 10px; font-size: 12px; }
.tvx-btn.danger { background: #fff; color: #991b1b; border: 1px solid #fca5a5; font-weight: 600; }
.tvx-btn.danger:hover { background: #fee2e2; }
.tvx-inline { display: inline; margin: 0; }
.tvx-wrap .table-list { width: auto; min-width: 520px; margin-bottom: 8px; }
.tvx-wrap .table-list td { vertical-align: middle; }
.tvx-wrap .table-list tbody th { text-align: left; font-weight: 600; padding-right: 24px; }
</style>
<div class="tvx-wrap">
<h1><?php echo tvx_h($title); ?></h1>
<div class="tvx-tabs">
<?php foreach ($tabs as $key => $tab): ?>
  <a href="<?php echo $tab[0]; ?>"<?php if ($key === $active) { ?> class="active"<?php } ?>><?php echo tvx_h($tab[1]); ?></a>
<?php endforeach; ?>
</div>
    <?php
    if (!empty($_SESSION['telavox_flash'])) {
        $flash = $_SESSION['telavox_flash'];
        unset($_SESSION['telavox_flash']);
        echo '<div class="tvx-msg ' . ($flash['type'] === 'ok' ? 'ok' : 'error') . '">' . tvx_h($flash['text']) . '</div>';
    }
}
