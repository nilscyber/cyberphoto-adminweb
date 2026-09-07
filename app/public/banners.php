<?php
include_once("top.php");
include_once("header.php");

$selfUrl = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8');

$add           = $_GET['add'] ?? '';
$confirmdelete = $_GET['confirmdelete'] ?? '';
// $addid/$addidc sätts av top.php vid change/copypost — defaulta bara om de saknas
$addid  = $addid  ?? '';
$addidc = $addidc ?? '';
// $wrongmess sätts av top.php vid valideringsfel — tilldela inte om den här
?>
<style>
.banners-toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    background: #fafafa;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 16px;
}

.banners-confirm-box {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fff8f0;
    border: 1px solid #fbbf24;
    border-radius: 8px;
    padding: 12px 16px;
    margin: 16px 0;
}

.banners-confirm-box h2 {
    margin: 0 0 4px 0;
    font-size: 15px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: none;
    line-height: 1.4;
}

.btn-danger {
    background: #dc2626;
    color: #fff;
}
.btn-danger:hover { background: #b91c1c; color: #fff; }

.btn-ghost {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}
.btn-ghost:hover { background: #e5e7eb; color: #111; }

.btn-add {
    background: #d1f2f0;
    color: #065f46;
    border: 1px solid #6ee7b7;
}
.btn-add:hover { background: #a7f3d0; color: #065f46; }

.tv-sim {
    margin-top: 28px;
}
.tv-sim h2 {
    font-size: 16px;
    margin: 0 0 4px 0;
}
.tv-sim p.tv-sim-hint {
    margin: 0 0 14px 0;
    font-size: 12px;
    color: #6b7280;
}
.tv-sim-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
@media (max-width: 900px) {
    .tv-sim-grid { grid-template-columns: 1fr; }
}
.tv-sim-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #111;
    overflow: hidden;
}
.tv-sim-card .tv-sim-label {
    background: #fafafa;
    border-bottom: 1px solid #e5e7eb;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.tv-sim-card .tv-sim-frame {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 */
}
.tv-sim-card .tv-sim-frame iframe {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}
</style>

<h1>TV:s butiken</h1>

<form method="GET">
    <div class="banners-toolbar">
        <?php include("front_choosesection.php"); ?>
    </div>
</form>

<?php if ($add === 'yes' || $addid !== '' || $addidc !== ''): ?>
    <?php if ($wrongmess): ?>
        <p><?= $wrongmess ?></p>
    <?php endif; ?>
    <?php include("front_addform.php"); ?>
<?php endif; ?>

<?php if ($confirmdelete !== ''): ?>
<div class="banners-confirm-box">
    <div>
        <h2>Bekräfta borttagning</h2>
        <p style="margin:0;font-size:13px;">Är du säker på att du vill ta bort denna banner?</p>
    </div>
    <a href="<?= $selfUrl ?>?delete=<?= (int)$confirmdelete ?>" class="btn btn-danger">Ta bort</a>
    <a href="<?= $selfUrl ?>" class="btn btn-ghost">Avbryt</a>
</div>
<?php endif; ?>

<?php if ($add !== 'yes' && $addid === '' && $addidc === ''): ?>
<div style="margin: 12px 0;">
    <a href="<?= $selfUrl ?>?add=yes" class="btn btn-add">+ Lägg till post på denna TV</a>
</div>
<?php endif; ?>

<?php
$banners->getBannerAdminNow($_SESSION['bannerdepartment'], $_SESSION['bannersection']);
$banners->getBannerAdminNow($_SESSION['bannerdepartment'], $_SESSION['bannersection'], true);
?>

<div class="tv-sim">
    <h2>Förhandsvisning – TV:s i butiken</h2>
    <p class="tv-sim-hint">Visar exakt samma sidor som TV-skärmarna i butiken laddar in. Varje ruta uppdaterar sig själv automatiskt var 25:e sekund, precis som i butiken.</p>
    <div class="tv-sim-grid">
        <?php for ($tv = 1; $tv <= 4; $tv++): ?>
        <div class="tv-sim-card">
            <div class="tv-sim-label">
                <span>TV<?= $tv ?></span>
                <a href="/butiken_tv<?= $tv ?>.php" target="_blank" rel="noopener" style="font-weight:400;">Öppna i eget fönster</a>
            </div>
            <div class="tv-sim-frame">
                <iframe src="/butiken_tv<?= $tv ?>.php" title="TV<?= $tv ?>" loading="lazy"></iframe>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>

<?php include_once("footer.php"); ?>
