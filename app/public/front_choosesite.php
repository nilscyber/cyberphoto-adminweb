<?php
$sites = [
    1 => ['img' => 'sv.jpg', 'label' => 'Sverige'],
    2 => ['img' => 'no.jpg', 'label' => 'Norge'],
    3 => ['img' => 'fi.jpg', 'label' => 'Finland'],
    4 => ['img' => 'fisv.jpg', 'label' => 'Finland (SV)'],
];
?>
<style>
.site-selector {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

.site-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 6px 10px;
    border-radius: 6px;
    border: 2px solid transparent;
    text-decoration: none;
    font-size: 11px;
    color: #374151;
    background: #f9fafb;
    transition: border-color 0.15s, background 0.15s;
}

.site-btn:hover {
    border-color: #6ee7b7;
    background: #f0fdf4;
}

.site-btn.active {
    border-color: #10b981;
    background: #d1fae5;
    color: #065f46;
    font-weight: 700;
}

.site-btn img {
    width: 32px;
    height: auto;
    border-radius: 2px;
}
</style>

<div class="site-selector">
<?php foreach ($sites as $id => $site): ?>
    <a href="?choose_site=<?= $id ?>" class="site-btn <?= $_SESSION['bannersite'] == $id ? 'active' : '' ?>">
        <img src="<?= htmlspecialchars($site['img']) ?>" alt="<?= htmlspecialchars($site['label']) ?>">
        <?= htmlspecialchars($site['label']) ?>
    </a>
<?php endforeach; ?>
</div>
