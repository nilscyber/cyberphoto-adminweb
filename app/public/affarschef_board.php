<?php
// affarschef_board.php
?>
<!doctype html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Affärschef - KPI</title>
<style>
  body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;background:#0f1115;color:#e8eaed}
  .wrap{padding:20px;height:100vh;box-sizing:border-box;display:flex;flex-direction:column}
  .top{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:14px}
  .title{font-size:28px;font-weight:800;letter-spacing:.3px}
  .meta{opacity:.7;font-size:14px}

  .grid{
    display:grid;
    grid-template-columns:1.55fr 0.55fr 0.9fr;
    gap:14px;
    align-items:start;
    flex:1;
    min-height:0;
  }

  .card{
    background:#171a21;
    border:1px solid #2a2f3a;
    border-radius:18px;
    padding:14px;
    box-shadow:0 10px 30px rgba(0,0,0,.25);
    box-sizing:border-box;
  }

  .kpi-label{opacity:.8;font-size:13px;margin-bottom:8px}
  .kpi-value{font-size:46px;font-weight:900;line-height:1}
  .kpi-sub{margin-top:8px;opacity:.85;font-size:16px}
  .muted{opacity:.65}

  .pill{display:inline-block;background:#0b3; color:#071; padding:4px 10px;border-radius:999px;font-weight:700;font-size:12px}
  .pill.warn{background:#3a2a00;color:#ffcc66}
  .pill.bad{background:#3a0b0b;color:#ff7777}

  /* Vänster: två fasta KPI-kort */
  .left-stack{
    display:grid;
    grid-template-rows:auto auto;
    gap:14px;
  }

  /* Lås KPI-kortens höjd (så de inte andas med andra kolumner) */
  .left-stack .card{height:170px}

  /* Mitten: kompakta kort som inte expanderar */
  .mid-col{
    display:flex;
    flex-direction:column;
    gap:14px;
    align-self:start;
  }
  .mid-card{
    display:flex;
    flex-direction:column;
    justify-content:center;
    height:170px; /* samma höjd som vänster, men INTE 1fr-stretch */
  }
  .mid-card .kpi-value{font-size:34px}
  .mid-card .kpi-sub{font-size:15px}

  /* Höger: listpanel fyller höjd, listor delar på utrymmet */
  .list-panel{
    display:flex;
    flex-direction:column;
    gap:12px;
    height:100%;
    min-height:0;
  }
  .list-head{display:flex;justify-content:space-between;align-items:flex-end}
  .list-head .kpi-label{margin:0}

  .list-split{
    display:grid;
    grid-template-rows:1fr 1fr;
    gap:12px;
    flex:1;
    min-height:0;
  }

  .list-block{display:flex;flex-direction:column;gap:8px;min-height:0}
  .list-title{display:flex;justify-content:space-between;align-items:center;font-weight:800}

  .count-badge{
    background:#0f1115;
    border:1px solid #2a2f3a;
    padding:2px 8px;
    border-radius:999px;
    font-size:12px;
    opacity:.9;
    min-width:26px;
    text-align:center;
  }

  .list-scroll{
    border:1px solid #2a2f3a;
    border-radius:14px;
    padding:10px;
    overflow:auto;
    background:rgba(0,0,0,.12);
    min-height:0;
  }

  .list-scroll::-webkit-scrollbar{width:10px}
  .list-scroll::-webkit-scrollbar-thumb{background:#2a2f3a;border-radius:999px}
  .list-scroll::-webkit-scrollbar-track{background:transparent}

  .list-item{
    display:flex;
    justify-content:space-between;
    gap:10px;
    padding:8px 6px;
    border-bottom:1px solid rgba(255,255,255,.06);
  }
  .list-item:last-child{border-bottom:none}

  .list-item .left{min-width:0}
  .list-item .art{font-weight:900;letter-spacing:.2px}
  .list-item .name{
    opacity:.82;
    font-size:12px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    max-width:420px;
  }
  .list-item .right{
    opacity:.72;
    font-size:12px;
    white-space:nowrap;
    text-align:right;
  }

  .list-empty{opacity:.6;font-size:13px}

  *{ -webkit-tap-highlight-color: transparent; }
</style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <div class="title">Affärschef - KPI</div>
    <div class="meta">
      <span id="statusPill" class="pill">LIVE</span>
      <span class="muted" style="margin-left:10px;">Senast uppdaterad:</span>
      <span id="lastUpdated"></span>
    </div>
  </div>

  <div class="grid">
    <!-- Vänster: KPI -->
    <div class="left-stack">
      <div class="card">
        <div class="kpi-label">Försäljning idag (leveranser)</div>
        <div class="kpi-value" id="salesSum"></div>
        <div class="kpi-sub"><span id="salesCount"></span> leveranser</div>
      </div>

      <div class="card">
        <div class="kpi-label">Estimerad dagsförsäljning</div>
        <div class="kpi-value" id="forecastSum"></div>
        <div class="kpi-sub muted" id="forecastSub">Om alla utskrivna & ej utskrivna plockordrar packas</div>
      </div>
    </div>

    <!-- Mitten: Order-kort (kompakt) -->
    <div class="mid-col">
      <div class="card mid-card">
        <div class="kpi-label">Ej utskrivna plockordrar</div>
        <div class="kpi-value" id="notPrintedSum"></div>
        <div class="kpi-sub"><span id="notPrintedCount"></span> ordrar</div>
      </div>

      <div class="card mid-card">
        <div class="kpi-label">Utskrivna plockordrar</div>
        <div class="kpi-value" id="printedSum"></div>
        <div class="kpi-sub"><span id="printedCount"></span> ordrar</div>
      </div>
    </div>

    <!-- Höger: listpanel -->
    <div class="card list-panel">
      <div class="list-head">
        <div class="kpi-label">Produkter</div>
        <div class="meta muted" style="opacity:.8;">Senaste 12h</div>
      </div>

      <div class="list-split">
        <div class="list-block">
          <div class="list-title">
            <span>Nya produkter</span>
            <span class="count-badge" id="createdCount">0</span>
          </div>
          <div class="list-scroll" id="createdList">
            <div class="list-empty">Laddar</div>
          </div>
        </div>

        <div class="list-block">
          <div class="list-title">
            <span>Utgångna produkter</span>
            <span class="count-badge" id="expiredCount">0</span>
          </div>
          <div class="list-scroll" id="expiredList">
            <div class="list-empty">Laddar</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
function formatSEK(n){
  if (n === null || typeof n === "undefined") return "";
  const v = Number(n);
  return v.toLocaleString('sv-SE', {maximumFractionDigits: 0}) + " kr";
}
function formatInt(n){
  if (n === null || typeof n === "undefined") return "";
  return Number(n).toLocaleString('sv-SE');
}
function safeText(s){
  return String(s || '')
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#039;');
}
function safeJsonParse(s){
  try { return JSON.parse(s); } catch(e){ return null; }
}
function setPill(ageSec){
  const pill = document.getElementById('statusPill');
  pill.className = 'pill';
  pill.textContent = 'LIVE';
  if (ageSec > 180) { pill.className = 'pill bad'; pill.textContent = 'DATA GAMMAL'; return; }
  if (ageSec > 90)  { pill.className = 'pill warn'; pill.textContent = 'FÖRDRÖJNING'; return; }
}

/* Auto-scroll */
const autoScrollControllers = {};
function startAutoScroll(containerId, opts){
  const el = document.getElementById(containerId);
  if (!el) return;
  stopAutoScroll(containerId);

  const cfg = { stepPx: 1, tickMs: 45, topPauseMs: 900, bottomPauseMs: 1500, afterUpdatePauseMs: 2500 };
  for (var k in (opts || {})) cfg[k] = opts[k];

  const ctrl = { el: el, timer: null, pausedUntil: 0, cfg: cfg };
  function pause(ms){ ctrl.pausedUntil = Date.now() + ms; }

  function tick(){
    const now = Date.now();
    if (now < ctrl.pausedUntil) return;

    if (ctrl.el.scrollHeight <= ctrl.el.clientHeight + 2) {
      ctrl.el.scrollTop = 0;
      return;
    }

    ctrl.el.scrollTop += ctrl.cfg.stepPx;
    const atBottom = (ctrl.el.scrollTop + ctrl.el.clientHeight) >= (ctrl.el.scrollHeight - 2);
    if (atBottom) {
      pause(ctrl.cfg.bottomPauseMs);
      setTimeout(function(){
        ctrl.el.scrollTop = 0;
        pause(ctrl.cfg.topPauseMs);
      }, ctrl.cfg.bottomPauseMs);
    }
  }

  ctrl.timer = setInterval(tick, cfg.tickMs);
  ctrl.onListUpdated = function(){ ctrl.el.scrollTop = 0; pause(ctrl.cfg.afterUpdatePauseMs); };
  autoScrollControllers[containerId] = ctrl;
}
function stopAutoScroll(containerId){
  const ctrl = autoScrollControllers[containerId];
  if (!ctrl) return;
  if (ctrl.timer) clearInterval(ctrl.timer);
  delete autoScrollControllers[containerId];
}
function notifyListUpdated(containerId){
  const ctrl = autoScrollControllers[containerId];
  if (ctrl && typeof ctrl.onListUpdated === 'function') ctrl.onListUpdated();
}

function renderList(containerId, countId, rows, mode){
  const el = document.getElementById(containerId);
  const countEl = document.getElementById(countId);
  if (!el || !countEl) return;

  countEl.textContent = rows.length;

  if (!rows.length){
    el.innerHTML = '<div class="list-empty">Inga poster</div>';
    notifyListUpdated(containerId);
    return;
  }

  el.innerHTML = rows.map(function(r){
    const art  = safeText(r.artnr);
    const name = safeText(r.name);
    const manu = safeText(r.manu);
    const cat  = safeText(r.cat);
    const by   = safeText(r.by);
    const t    = (mode === 'created') ? safeText(r.created) : safeText(r.discontinued);

    const leftName = (manu ? (manu + ' \u2013 ') : '') + (name || '');
    const rightTop = cat || '';
    const rightBot = (by && t) ? (by + ' \u2022 ' + t) : (by || t || '');

    return ''+
      '<div class="list-item">'+
        '<div class="left">'+
          '<div class="art">'+art+'</div>'+
          '<div class="name">'+safeText(leftName)+'</div>'+
        '</div>'+
        '<div class="right">'+
          '<div>'+safeText(rightTop)+'</div>'+
          '<div>'+safeText(rightBot)+'</div>'+
        '</div>'+
      '</div>';
  }).join('');

  notifyListUpdated(containerId);
}

async function fetchKpi(){
  const r = await fetch('/ajax/kpi_affarschef.php', {cache: 'no-store'});
  const j = await r.json();
  if (!j.ok) return;

  const d = j.data || {};

  const sales = d['sales.today.inout'] || {};
  const pr    = d['orders.printed'] || {};
  const npr   = d['orders.not_printed'] || {};

  const salesSum = Number(sales.sum || 0);
  const salesCnt = Number(sales.count || 0);

  const prSum = Number(pr.sum || 0);
  const prCnt = Number(pr.count || 0);

  const nprSum = Number(npr.sum || 0);
  const nprCnt = Number(npr.count || 0);

  document.getElementById('salesSum').textContent = formatSEK(salesSum);
  document.getElementById('salesCount').textContent = formatInt(salesCnt);

  document.getElementById('printedSum').textContent = formatSEK(prSum);
  document.getElementById('printedCount').textContent = formatInt(prCnt);

  document.getElementById('notPrintedSum').textContent = formatSEK(nprSum);
  document.getElementById('notPrintedCount').textContent = formatInt(nprCnt);

  const forecastSum = salesSum + prSum + nprSum;
  document.getElementById('forecastSum').textContent = formatSEK(forecastSum);

  const pipelineSum = prSum + nprSum;
  document.getElementById('forecastSub').innerHTML =
    'Om alla utskrivna & ej utskrivna plockordrar packas (<b>' + formatSEK(pipelineSum) + '</b> kvar i pipen)';

  const createdRaw = d['products.created'] || {};
  const discRaw    = d['products.discontinued'] || {};

  const createdObj = createdRaw.json ? safeJsonParse(createdRaw.json) : null;
  const discObj    = discRaw.json ? safeJsonParse(discRaw.json) : null;

  const createdRows = (createdObj && createdObj.rows) ? createdObj.rows : [];
  const discRows    = (discObj && discObj.rows) ? discObj.rows : [];

  renderList('createdList', 'createdCount', createdRows, 'created');
  renderList('expiredList', 'expiredCount', discRows, 'disc');

  const times = [
    sales.updated_at, pr.updated_at, npr.updated_at,
    createdRaw.updated_at, discRaw.updated_at
  ].filter(Boolean);

  times.sort();
  const latest = times.length ? times[times.length-1] : null;

  document.getElementById('lastUpdated').textContent = latest || '';

  if (latest){
    const dt = new Date(latest.replace(' ', 'T'));
    const ageSec = Math.max(0, (Date.now() - dt.getTime())/1000);
    setPill(ageSec);
  }
}

startAutoScroll('createdList', { stepPx: 1, tickMs: 45 });
startAutoScroll('expiredList', { stepPx: 1, tickMs: 45 });

fetchKpi();
setInterval(fetchKpi, 60000);
</script>
</body>
</html>
