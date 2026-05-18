<?php
include_once("top.php");
include_once("header.php");
?>
<style>
.index-layout {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
.index-main {
  flex: 1 1 0;
  min-width: 0;
}
.index-sidebar {
  flex: 0 0 320px;
  width: 320px;
}

/* Huvud-panel (orders/lager) */
.main-panel {
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: #fff;
  overflow: hidden;
  font-size: 13px;
}
.main-panel-header {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 10px 14px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 700;
  font-size: 13px;
  color: #111;
}
.main-panel-body {
  padding: 10px;
}

/* RSS-widget */
.rss-widget {
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: #fff;
  overflow: hidden;
  font-size: 13px;
}
.rss-header {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 10px 14px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 700;
  font-size: 13px;
  color: #111;
}
.rss-icon { display: flex; align-items: center; }
.rss-list {
  list-style: none;
  margin: 0;
  padding: 0;
}
.rss-item {
  display: flex;
  gap: 10px;
  padding: 10px 14px;
  border-bottom: 1px solid #f3f4f6;
}
.rss-item:last-child { border-bottom: none; }
.rss-thumb-link { flex-shrink: 0; }
.rss-thumb {
  width: 64px;
  height: 48px;
  object-fit: cover;
  border-radius: 5px;
  background: #f3f4f6;
  display: block;
}
.rss-body { flex: 1; min-width: 0; }
.rss-title {
  display: block;
  font-weight: 600;
  color: #111;
  text-decoration: none;
  line-height: 1.35;
  margin-bottom: 3px;
}
.rss-title:hover { text-decoration: underline; color: #0d9488; }
.rss-desc {
  margin: 0 0 4px;
  color: #6b7280;
  font-size: 12px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.rss-meta {
  display: flex;
  gap: 8px;
  font-size: 11px;
  color: #9ca3af;
}
.rss-source { font-weight: 700; color: #6b7280; }
.rss-empty { padding: 14px; color: #9ca3af; font-size: 13px; margin: 0; }

@media (max-width: 900px) {
  .index-layout { flex-direction: column; }
  .index-sidebar { width: 100%; flex: none; }
}
</style>

<?php if ($_COOKIE['login_ok'] == "true"): ?>
<div class="index-layout">
  <div class="index-main">
    <div class="main-panel">
      <div class="main-panel-header">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
        </svg>
        In- samt utgående ordrar, lagerstatus samt plock-situationen
      </div>
      <div class="main-panel-body">
        <div id="txtArea">Laddar sidan......<br><img border="0" src="ajax-loader.gif"></div>
      </div>
    </div>
  </div>
  <div class="index-sidebar">
    <?php include_once "rss_widget.php"; ?>
  </div>
</div>
<?php else: ?>
<h1>Välkommen, logga in för att fortsätta.</h1>
<?php endif; ?>

<?php include_once("footer.php"); ?>
