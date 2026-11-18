/* Orderflöde (order_flow.php) – diagram, listor och export från window.OFLOW. Ren SVG, inga bibliotek. */
(function () {
  const D = window.OFLOW; if (!D) return;
  // O = ordrar [docno, datum, webb, hämtas, kund, status, arbetsdagar, rader]
  // L = rader  [order, produkt, antal, status, arbetsdagar, utleverans, i lager då, reserverat då, orsak, direktleverans]
  // P = produkter [artikel, id, namn, varumärke, kategori, inbyte]
  const O = D.O, L = D.L, P = D.P;
  const nf = new Intl.NumberFormat('sv-SE');
  const f = n => nf.format(Math.round(n));
  const pct = (a, b) => b > 0 ? (100 * a / b).toFixed(0) + ' %' : '–';
  const pct1 = (a, b) => b > 0 ? (100 * a / b).toFixed(1).replace('.', ',') + ' %' : '–';
  const C = { S: '#1baf7a', P: '#6250d6', N: '#eb6834', D: '#8b8a85', same: '#2a78d6' };
  const ST = ['S', 'P', 'N', 'D'];
  const STL = { S: 'Fritt i lager vid köp', P: 'I lager men lovat till tidigare ordrar', N: 'Saknades i lager', D: 'Direktleverans från leverantör' };
  const STO = { S: 'Komplett vid köp', P: 'Vara lovad till tidigare ordrar', N: 'Något saknades i lager', D: 'Direktleverans från leverantör' };
  const WHYO = { N: 'En vara saknades i lager vid köpet', P: 'Varan fanns men var lovad till tidigare ordrar', D: 'Ordern har direktleverans från leverantör',
                 pick: 'Allt fanns i lager – ordern hämtas i butik', other: 'Allt fanns i lager – annan orsak' };
  const WHYL = { 1: 'Väntade på en annan vara på samma order', 2: 'Ordern hämtas i butik', 3: 'Annan orsak' };
  const WK = ['sön', 'mån', 'tis', 'ons', 'tor', 'fre', 'lör'];
  const wkd = d => WK[new Date(d + 'T00:00:00').getDay()];
  const bucket = wd => wd < 0 ? 6 : wd === 0 ? 0 : wd <= 1 ? 1 : wd <= 3 ? 2 : wd <= 5 ? 3 : wd <= 10 ? 4 : 5;
  const wdText = wd => wd < 0 ? 'Ej levererad ännu' : wd === 0 ? 'Samma arbetsdag' : wd === 1 ? '1 arbetsdag' : wd + ' arbetsdagar';
  const whyO = o => o[6] === 0 ? '' : (o[5] === 'S' ? (o[3] ? 'pick' : 'other') : o[5]);

  const NS = 'http://www.w3.org/2000/svg';
  function el(tag, attrs, parent) { const e = document.createElementNS(NS, tag); for (const k in attrs) e.setAttribute(k, attrs[k]); if (parent) parent.appendChild(e); return e; }
  function txt(parent, x, y, s, attrs) { const t = el('text', Object.assign({ x, y }, attrs || {}), parent); t.textContent = s; return t; }
  function svg(id, w, h) { const host = document.getElementById(id); if (!host) return null; const s = el('svg', { viewBox: `0 0 ${w} ${h}`, width: w, height: h, role: 'img' }); host.appendChild(s); return s; }
  // skalans topp: fyra jämna steg som alltid blir heltal
  function niceMax(v) { if (v <= 4) return 4; if (v <= 8) return 8; const p = Math.pow(10, Math.floor(Math.log10(v))); const m = v / p; const n = [1, 1.2, 1.6, 2, 2.4, 3.2, 4, 6, 8, 10].find(x => m <= x + 1e-9); return n * p; }
  // stapelsegment; det översta i en stapel får rundade hörn upptill
  function seg(parent, x, y, w, h, fill, top) {
    if (!top) return el('rect', { x, y, width: w, height: h, fill }, parent);
    const r = Math.min(4, h, w / 2);
    return el('path', { d: `M${x},${y + h}V${y + r}Q${x},${y} ${x + r},${y}H${x + w - r}Q${x + w},${y} ${x + w},${y + r}V${y + h}Z`, fill }, parent);
  }

  const tip = document.getElementById('gi-tip');
  function hook(node, html, onShow, onHide) {
    node.classList.add('gi-mark'); node.setAttribute('tabindex', '0');
    const move = ev => {
      if (!ev.clientX) { const r = node.getBoundingClientRect(); ev = { clientX: r.left + r.width / 2, clientY: r.top }; }
      const w = tip.offsetWidth, h = tip.offsetHeight; let x = ev.clientX + 14, y = ev.clientY + 14;
      if (x + w > innerWidth - 8) x = ev.clientX - w - 14; if (y + h > innerHeight - 8) y = ev.clientY - h - 14;
      tip.style.left = x + 'px'; tip.style.top = y + 'px';
    };
    const show = ev => { tip.innerHTML = html; tip.hidden = false; move(ev); if (onShow) onShow(); };
    const hide = () => { tip.hidden = true; if (onHide) onHide(); };
    node.addEventListener('mouseenter', show); node.addEventListener('mousemove', move); node.addEventListener('mouseleave', hide);
    node.addEventListener('focus', show); node.addEventListener('blur', hide);
  }
  const CLICK = '<br><span class="gi-tip-hint">Klicka för att visa listan</span>';
  function clickable(node, fn) {
    node.classList.add('gi-click');
    if (!node.hasAttribute('tabindex')) node.setAttribute('tabindex', '0');
    node.addEventListener('click', fn);
    node.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fn(); } });
  }

  // ---------- urval: ett filter F plockar ordrar (kind 'o') eller orderrader (kind 'l') ur datat
  const linesOf = O.map(() => []);
  L.forEach((l, i) => linesOf[l[0]].push(i));
  function okOrder(o, F) {
    if (F.g === 'web' && !o[2]) return false;
    if (F.g === 'man' && o[2]) return false;
    if (F.g === 'ship' && o[3]) return false;
    if (F.g === 'pick' && !o[3]) return false;
    return !(F.day && o[1] !== F.day);
  }
  function select(F) {
    const out = [];
    if (F.kind === 'o') {
      O.forEach((o, i) => {
        if (okOrder(o, F) && (!F.st || o[5] === F.st) && (F.b === undefined || bucket(o[6]) === F.b) && (!F.why || whyO(o) === F.why)) out.push(i);
      });
    } else {
      L.forEach((l, i) => {
        const p = P[l[1]];
        if (okOrder(O[l[0]], F) && (!F.st || l[3] === F.st) && (F.b === undefined || bucket(l[4]) === F.b) && (!F.why || l[8] === +F.why)
            && (F.pi === undefined || l[1] === F.pi) && (F.bi === undefined || p[3] === F.bi) && (F.ki === undefined || p[4] === F.ki)) out.push(i);
      });
    }
    return out;
  }

  // ---------- listor: kolumnerna används både för tabellen på sidan och för CSV-exporten (x = bara i exporten)
  const esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  const link = (url, text) => `<a href="${esc(url)}" target="_blank" rel="noopener">${esc(text)}</a>`;
  const oUrl = no => '/search_dispatch.php?mode=order&page=1&q=' + encodeURIComponent(no);
  const pUrl = p => '/search_dispatch.php?mode=product&q=' + encodeURIComponent(p[0]) + '&open=product&id=' + p[1];
  const sUrl = no => '/search_dispatch.php?mode=inout&q=' + encodeURIComponent(no);
  const dot = st => `<i class="of-dot of-${st}"></i>`;
  // artiklarna som saknades eller var lovade på en order
  const short = oi => linesOf[oi].filter(i => L[i][3] === 'N' || L[i][3] === 'P').map(i => P[L[i][1]][0]).join(', ');
  const ord = l => O[l[0]];
  const COLS = {
    o: [
      { h: 'Order', v: o => o[0], html: o => link(oUrl(o[0]), o[0]) },
      { h: 'Datum', v: o => o[1] },
      { h: 'Kanal', v: o => o[2] ? 'Webb' : 'Manuell' },
      { h: 'Leverans', v: o => o[3] ? 'Hämtas i butik' : 'Skickas' },
      { h: 'Kund', v: o => o[4], cls: 'wrap' },
      { h: 'Rader', n: 1, v: o => o[7] },
      { h: 'Lagerläge vid köp', v: o => STO[o[5]], html: o => dot(o[5]) + esc(STO[o[5]]) },
      { h: 'Saknade eller lovade artiklar', v: (o, i) => short(i), cls: 'wrap' },
      { h: 'Levererad', v: o => wdText(o[6]) },
      { h: 'Arbetsdagar', n: 1, x: 1, v: o => o[6] < 0 ? '' : o[6] }
    ],
    l: [
      { h: 'Order', v: l => ord(l)[0], html: l => link(oUrl(ord(l)[0]), ord(l)[0]) },
      { h: 'Datum', v: l => ord(l)[1] },
      { h: 'Kanal', v: l => ord(l)[2] ? 'Webb' : 'Manuell' },
      { h: 'Leverans', x: 1, v: l => ord(l)[3] ? 'Hämtas i butik' : 'Skickas' },
      { h: 'Kund', x: 1, v: l => ord(l)[4] },
      { h: 'Artikel', v: l => P[l[1]][0], html: l => link(pUrl(P[l[1]]), P[l[1]][0]) },
      { h: 'Produkt', v: l => P[l[1]][2], cls: 'wrap' },
      { h: 'Varumärke', x: 1, v: l => D.B[P[l[1]][3]] },
      { h: 'Kategori', x: 1, v: l => D.K[P[l[1]][4]] },
      { h: 'Antal', n: 1, v: l => l[2] },
      { h: 'I lager vid köp', n: 1, v: l => l[9] ? '' : l[6] },
      { h: 'Lovat till tidigare ordrar', n: 1, v: l => l[9] ? '' : l[7] },
      { h: 'Lagerläge vid köp', v: l => STL[l[3]], html: l => dot(l[3]) + esc(STL[l[3]]) },
      { h: 'Levererad', v: l => wdText(l[4]) },
      { h: 'Arbetsdagar', n: 1, x: 1, v: l => l[4] < 0 ? '' : l[4] },
      { h: 'Utleverans', v: l => l[5], html: l => l[5] ? link(sUrl(l[5]), l[5]) : '' },
      { h: 'Fick vänta för att', v: l => WHYL[l[8]] || '', cls: 'wrap' }
    ]
  };
  const show = v => v === '' || v === null || v === undefined ? '' : (typeof v === 'number' ? nf.format(v) : esc(v));

  // ---------- CSV: semikolon + BOM så att svenskt Excel öppnar filen direkt
  const slug = s => s.toLowerCase().replace(/[åä]/g, 'a').replace(/ö/g, 'o').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
  function csvField(v) {
    if (v === null || v === undefined) return '';
    if (typeof v === 'number') return String(v).replace('.', ',');
    let s = String(v);
    if (/^[=+\-@\t\r]/.test(s)) s = "'" + s;   // text från kunder får aldrig tolkas som en formel i Excel
    return /[";\r\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
  }
  function download(name, head, rows) {
    const lines = [head.map(csvField).join(';')].concat(rows.map(r => r.map(csvField).join(';')));
    const blob = new Blob(['﻿' + lines.join('\r\n') + '\r\n'], { type: 'text/csv;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'orderflode_' + name + D.slug + '_' + D.from + '_' + D.to + '.csv';
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(() => URL.revokeObjectURL(a.href), 2000);
  }
  // en tabell på sidan, som den står
  function cellText(c) {
    let s = c.textContent.replace(/\s+/g, ' ').trim();
    if (s === '–') return '';
    if (c.classList.contains('n')) s = s.replace(/ /g, '');
    return s;
  }
  document.querySelectorAll('.gi-export[data-target]').forEach(b => b.addEventListener('click', () => {
    const t = document.getElementById(b.dataset.target); if (!t) return;
    download(b.dataset.name, [...t.querySelectorAll('thead th')].map(th => th.textContent.trim()), [...t.querySelectorAll('tbody tr')].map(tr => [...tr.cells].map(cellText)));
  }));

  // ---------- detaljlistan (#gi-detail): ordrarna eller raderna bakom en stapel eller en siffra
  const det = document.getElementById('gi-detail'), CAP = 500;
  let cur = null;
  function open(F, title, from) {
    const idx = select(F), src = F.kind === 'o' ? O : L, cols = COLS[F.kind].filter(c => !c.x);
    const t = document.getElementById('of-detail-table');
    t.tHead.innerHTML = '<tr>' + cols.map(c => `<th${c.n ? ' class="n"' : ''}>${c.h}</th>`).join('') + '</tr>';
    t.tBodies[0].innerHTML = idx.slice(0, CAP).map(i => '<tr>' + cols.map(c => {
      const cls = [c.n ? 'n' : '', c.cls || ''].filter(Boolean).join(' ');
      return `<td${cls ? ` class="${cls}"` : ''}>${c.html ? c.html(src[i], i) : show(c.v(src[i], i))}</td>`;
    }).join('') + '</tr>').join('');
    document.getElementById('gi-detail-title').textContent = title;
    const q = F.kind === 'l' ? idx.reduce((a, i) => a + L[i][2], 0) : 0;
    document.getElementById('gi-detail-count').textContent = idx.length === 0 ? 'Inga rader'
      : F.kind === 'o' ? `${f(idx.length)} ${idx.length === 1 ? 'order' : 'ordrar'}` : `${f(idx.length)} ${idx.length === 1 ? 'orderrad' : 'orderrader'} · ${nf.format(q)} ${q === 1 ? "enhet" : "enheter"}`;
    const more = document.getElementById('of-detail-more');
    more.hidden = idx.length <= CAP;
    more.textContent = `Visar de första ${f(CAP)} av ${f(idx.length)}. Exporten innehåller alla.`;
    // listan läggs direkt under det man klickade på
    const card = from && from.closest ? from.closest('.gi-card') : null;
    if (card && card !== det) { const host = card.parentElement.classList.contains('gi-grid2') ? card.parentElement : card; host.after(det); }
    cur = { F, idx, title };
    det.hidden = false; tip.hidden = true;
    det.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  document.getElementById('gi-detail-close').addEventListener('click', () => { det.hidden = true; });
  document.getElementById('of-detail-export').addEventListener('click', () => {
    if (!cur) return;
    const src = cur.F.kind === 'o' ? O : L, cols = COLS[cur.F.kind];
    download((cur.F.kind === 'o' ? 'ordrar_' : 'orderrader_') + slug(cur.title), cols.map(c => c.h), cur.idx.map(i => cols.map(c => c.v(src[i], i))));
  });
  // länkar med klassen of-pick: data-attributen är filtret
  document.addEventListener('click', e => {
    const a = e.target.closest ? e.target.closest('a.of-pick') : null; if (!a) return;
    e.preventDefault();
    const d = a.dataset, F = { kind: d.kind };
    ['g', 'st', 'day', 'why'].forEach(k => { if (d[k] !== undefined) F[k] = d[k]; });
    ['b', 'bi', 'ki', 'pi'].forEach(k => { if (d[k] !== undefined) F[k] = +d[k]; });
    open(F, d.title, a);
  });

  // ---------- ordrar per dag: staplar efter lagerläget vid köp
  const days = D.days;   // [datum, ordrar, S, P, N, D, samma arbetsdag, rader, rader fritt i lager]
  const DW = 900, DL = 54, DR = 10;
  const every = Math.ceil(days.length / 16);
  function dayLabels(s, H, x, i, d) {
    if (i % every !== 0) return;
    txt(s, x, H - 26, d.slice(5), { 'text-anchor': 'middle', 'font-size': '11.5' });
    txt(s, x, H - 12, wkd(d), { 'text-anchor': 'middle', class: 'muted', 'font-size': '11' });
  }
  (function () {
    if (!days.length) return;
    const W = DW, H = 300, Tm = 22, B = 44;
    const s = svg('of-day', W, H); if (!s) return; s.style.minWidth = '560px';
    const max = niceMax(Math.max(1, ...days.map(g => g[1])) * 1.1);
    const pw = W - DL - DR, ph = H - Tm - B; const y = v => Tm + ph - v / max * ph;
    const gr = el('g', { class: 'grid' }, s); const step = max / 4;
    for (let v = 0; v <= max + 1e-9; v += step) { el('line', { x1: DL, x2: W - DR, y1: y(v), y2: y(v) }, gr); txt(s, DL - 8, y(v) + 4, f(v), { 'text-anchor': 'end', class: 'muted', 'font-size': '11' }); }
    const bw = pw / days.length, bar = Math.min(bw * 0.62, 60);
    days.forEach((g, i) => {
      const d = g[0], n = g[1], x = DL + i * bw + (bw - bar) / 2;
      const head = `<b>${d} ${wkd(d)}</b>`;
      if (n > 0) {
        // hela kolumnen är klickbar, så att smala staplar går att träffa
        const hit = el('rect', { x: DL + i * bw, y: Tm, width: bw, height: ph, class: 'of-col-hit' }, s);
        hook(hit, `${head} · ${f(n)} ordrar<br>` + ST.map((k, j) => g[2 + j] > 0 ? `${STO[k]}: ${f(g[2 + j])} (${pct(g[2 + j], n)})<br>` : '').join('')
          + `Ute samma arbetsdag: ${f(g[6])} (${pct(g[6], n)})` + CLICK);
        clickable(hit, () => open({ kind: 'o', day: d }, `Ordrar ${d}`, hit));
      }
      const top = ST.reduce((a, k, j) => g[2 + j] > 0 ? j : a, -1); let acc = 0;
      ST.forEach((k, j) => {
        const v = g[2 + j]; if (v <= 0) return;
        const y0 = y(acc + v), y1 = y(acc), h = Math.max(y1 - y0 - 2, 0.5);
        const r = seg(s, x, y0 + 1, bar, h, C[k], j === top);
        hook(r, `${head} · ${STO[k]}<br>${f(v)} ordrar · ${pct1(v, n)} av dagens ${f(n)}` + CLICK);
        clickable(r, () => open({ kind: 'o', day: d, st: k }, `Ordrar ${d}: ${STO[k].toLowerCase()}`, r));
        acc += v;
      });
      if (days.length <= 16 && n > 0) txt(s, x + bar / 2, y(n) - 6, f(n), { 'text-anchor': 'middle', class: 'muted', 'font-size': '11' });
      dayLabels(s, H, x + bar / 2, i, d);
    });
    el('line', { x1: DL, x2: W - DR, y1: y(0), y2: y(0), class: 'axis' }, s);
  })();

  // ---------- andel kompletta vid köp och andel ute samma arbetsdag, per dag (samma x-skala som staplarna ovanför)
  (function () {
    if (!days.length) return;
    const W = DW, H = 224, Tm = 28, B = 44;
    const s = svg('of-daypct', W, H); if (!s) return; s.style.minWidth = '560px';
    const pw = W - DL - DR, ph = H - Tm - B; const y = v => Tm + ph - v / 100 * ph;
    const gr = el('g', { class: 'grid' }, s);
    [0, 25, 50, 75, 100].forEach(v => { el('line', { x1: DL, x2: W - DR, y1: y(v), y2: y(v) }, gr); txt(s, DL - 8, y(v) + 4, v + ' %', { 'text-anchor': 'end', class: 'muted', 'font-size': '11' }); });
    const bw = pw / days.length, cx = i => DL + i * bw + bw / 2;
    const series = [['Kompletta vid köp', 2, C.S, 'none'], ['Ute samma arbetsdag', 6, C.same, '7 5']];
    const val = (g, col) => g[1] > 0 ? 100 * g[col] / g[1] : null;
    const cross = el('line', { y1: Tm, y2: Tm + ph, class: 'of-cross', visibility: 'hidden' }, s);
    series.forEach(([name, col, color, dash]) => {
      // dagar utan ordrar ger ett avbrott i linjen
      let d = '', pen = false;
      days.forEach((g, i) => { const v = val(g, col); if (v === null) { pen = false; return; } d += (pen ? 'L' : 'M') + cx(i).toFixed(1) + ',' + y(v).toFixed(1); pen = true; });
      el('path', { d, fill: 'none', stroke: color, 'stroke-width': 2, 'stroke-dasharray': dash, 'stroke-linejoin': 'round', 'stroke-linecap': 'round' }, s);
      if (days.length <= 31) days.forEach((g, i) => { const v = val(g, col); if (v !== null) el('circle', { cx: cx(i), cy: y(v), r: 4, fill: color, stroke: '#fff', 'stroke-width': 2 }, s); });
    });
    days.forEach((g, i) => {
      const d = g[0];
      const hit = el('rect', { x: DL + i * bw, y: Tm, width: bw, height: ph, class: 'of-hit' }, s);
      hook(hit, `<b>${d} ${wkd(d)}</b> · ${f(g[1])} ordrar` + (g[1] > 0 ? `<br>Kompletta vid köp: ${pct1(g[2], g[1])} (${f(g[2])})<br>Ute samma arbetsdag: ${pct1(g[6], g[1])} (${f(g[6])})` : ''),
        () => { cross.setAttribute('x1', cx(i)); cross.setAttribute('x2', cx(i)); cross.setAttribute('visibility', 'visible'); }, () => cross.setAttribute('visibility', 'hidden'));
      dayLabels(s, H, cx(i), i, d);
    });
    el('line', { x1: DL, x2: W - DR, y1: y(0), y2: y(0), class: 'axis' }, s);
  })();

  // ---------- arbetsdagar till utleverans: staplar per intervall, delade efter lagerläget vid köp
  function hist(id, kind, total, noun) {
    const H0 = D.hist[kind], short = ['Samma dag', '1', '2–3', '4–5', '6–10', 'Över 10', 'Ej lev.'];
    const tot = k => ST.reduce((a, t) => a + H0[k][t], 0);
    const W = 440, H = 262, L0 = 46, R = 8, Tm = 24, B = 46; const s = svg(id, W, H); if (!s) return; s.style.minWidth = '380px';
    const max = niceMax(Math.max(1, ...short.map((x, k) => tot(k))) * 1.12); const pw = W - L0 - R, ph = H - Tm - B; const y = v => Tm + ph - v / max * ph;
    const gr = el('g', { class: 'grid' }, s); const step = max / 4;
    for (let v = 0; v <= max + 1e-9; v += step) { el('line', { x1: L0, x2: W - R, y1: y(v), y2: y(v) }, gr); txt(s, L0 - 6, y(v) + 4, f(v), { 'text-anchor': 'end', class: 'muted', 'font-size': '10.5' }); }
    const bw = pw / short.length, bar = Math.min(bw - 14, 44);
    short.forEach((lab, k) => {
      const n = tot(k), x = L0 + k * bw + (bw - bar) / 2;
      const top = ST.reduce((a, t, j) => H0[k][t] > 0 ? j : a, -1); let acc = 0;
      ST.forEach((t, j) => {
        const v = H0[k][t]; if (v <= 0) return;
        const y0 = y(acc + v), y1 = y(acc), h = Math.max(y1 - y0 - 2, 0.5);
        const r = seg(s, x, y0 + 1, bar, h, C[t], j === top);
        const name = (kind === 'o' ? STO : STL)[t];
        hook(r, `<b>${D.bk[k]}</b> · ${name}<br>${f(v)} ${noun} · ${pct1(v, n)} av stapeln, ${pct1(v, total)} av alla` + CLICK);
        clickable(r, () => open({ kind, b: k, st: t }, `${kind === 'o' ? 'Ordrar' : 'Orderrader'}: ${D.bk[k].toLowerCase()}, ${name.toLowerCase()}`, r));
        acc += v;
      });
      if (n > 0) txt(s, x + bar / 2, y(n) - 6, 100 * n / total < 10 ? pct1(n, total) : pct(n, total), { 'text-anchor': 'middle', class: 'ink', 'font-size': '11' });
      txt(s, x + bar / 2, H - 28, lab, { 'text-anchor': 'middle', 'font-size': '10.5' });
    });
    txt(s, L0 + pw / 2, H - 8, 'arbetsdagar från order till utleverans · andel av alla ' + noun, { 'text-anchor': 'middle', class: 'muted', 'font-size': '11' });
    el('line', { x1: L0, x2: W - R, y1: y(0), y2: y(0), class: 'axis' }, s);
  }
  hist('of-hist-o', 'o', D.no, 'ordrar');
  hist('of-hist-l', 'l', D.nl, 'orderrader');

  // ---------- varför inte samma arbetsdag: liggande staplar, färgen är lagerläget vid köp
  function bars(id, items, kind, noun, of) {
    const tot = items.reduce((a, b) => a + b[2], 0);
    const W = 440, H = items.length * 50 + 6, s = svg(id, W, H); if (!s) return; s.style.minWidth = '380px';
    const max = niceMax(Math.max(1, ...items.map(i => i[2])) * 1.05), L0 = 8, pw = W - L0 - 100;
    items.forEach(([key, lab, v, color], i) => {
      const y0 = 4 + i * 50; txt(s, L0, y0 + 10, lab, { class: 'ink', 'font-size': '12.5' });
      const w = Math.max(v / max * pw, v > 0 ? 2 : 0.5);
      const r = el('rect', { x: L0, y: y0 + 18, width: w, height: 16, fill: color, rx: 3 }, s);
      hook(r, `<b>${lab}</b><br>${f(v)} ${noun} · ${pct1(v, tot)} ${of}` + (v > 0 ? CLICK : ''));
      if (v > 0) clickable(r, () => open({ kind, why: key }, lab, r));
      txt(s, L0 + w + 8, y0 + 31, `${f(v)} · ${pct(v, tot)}`, { 'font-size': '12' });
    });
  }
  bars('of-why-o', ['N', 'P', 'D', 'pick', 'other'].map(k => [k, WHYO[k], D.why[k], C[k] || C.S]).filter(it => it[2] > 0 || it[0] === 'N' || it[0] === 'other'),
    'o', 'ordrar', 'av dem som inte gick ut samma arbetsdag');
  bars('of-why-l', [1, 2, 3].map(k => [k, WHYL[k], D.lwhy[k], C.S]), 'l', 'orderrader', 'av raderna som fanns i lager men fick vänta');

  // ---------- produktlistan: [produkt, rader, antal, manuella, saknades, lovat, saknades webb, saknades manuella, median dagar, ej levererade, lager nu]
  (function () {
    const table = document.getElementById('of-prod'); if (!table) return;
    const body = table.tBodies[0], q = document.getElementById('of-prod-q'), miss = document.getElementById('of-prod-miss');
    const count = document.getElementById('of-prod-count'), more = document.getElementById('of-prod-more'), moreText = document.getElementById('of-prod-moretext');
    const text = { value: r => P[r[0]][0], name: r => P[r[0]][2], brand: r => D.B[P[r[0]][3]] };
    const hay = D.products.map(r => { const p = P[r[0]]; return (p[0] + ' ' + p[2] + ' ' + D.B[p[3]] + ' ' + D.K[p[4]]).toLowerCase(); });
    const order = D.products.map((r, i) => i);
    let key = '4', dir = -1, limit = 100, rows = [];
    function sorted() {
      const val = text[key] ? text[key] : (key === 'share' ? r => r[4] / r[1] : r => r[+key]);
      const cmp = text[key] ? (a, b) => a.localeCompare(b, 'sv') : (a, b) => a - b;
      return order.slice().sort((i, j) => {
        const a = val(D.products[i]), b = val(D.products[j]);
        if (a === null || b === null) return a === b ? i - j : (a === null ? 1 : -1);   // utan värde hamnar sist
        return dir * cmp(a, b) || i - j;
      });
    }
    const num = (v, cls) => `<td class="n${cls ? ' ' + cls : ''}">${v === null || v === 0 ? '–' : nf.format(v)}</td>`;
    function draw() {
      const words = q.value.trim().toLowerCase().split(/\s+/).filter(Boolean);
      rows = sorted().filter(i => (!miss.checked || D.products[i][4] > 0) && words.every(w => hay[i].includes(w)));
      body.innerHTML = rows.slice(0, limit).map(i => {
        const r = D.products[i], p = P[r[0]], t = esc(p[0] + ' ' + p[2]);
        return `<tr><td>${link(pUrl(p), p[0])}</td><td>${esc(p[2])}</td><td>${esc(D.B[p[3]])}</td>`
          + `<td class="n"><a href="#" class="of-pick" data-kind="l" data-pi="${r[0]}" data-title="${t}">${nf.format(r[1])}</a></td>` + num(r[2]) + num(r[3])
          + (r[4] > 0 ? `<td class="n of-hot"><a href="#" class="of-pick" data-kind="l" data-pi="${r[0]}" data-st="N" data-title="${t}: saknades i lager vid köp">${nf.format(r[4])}</a></td>` : num(0))
          + `<td class="n">${r[4] > 0 ? pct(r[4], r[1]) : '–'}</td>` + num(r[6]) + num(r[7]) + num(r[5])
          + `<td class="n">${r[8] === null ? '–' : nf.format(r[8])}</td>` + num(r[9]) + `<td class="n">${r[10] === null ? '–' : nf.format(r[10])}</td></tr>`;
      }).join('') || '<tr><td colspan="14" class="of-empty">Inga produkter matchar.</td></tr>';
      count.textContent = rows.length === D.products.length ? `${f(rows.length)} produkter` : `${f(rows.length)} av ${f(D.products.length)} produkter`;
      more.hidden = rows.length <= limit;
      moreText.textContent = rows.length > limit ? `Visar ${f(limit)} av ${f(rows.length)}.` : '';
      table.querySelectorAll('thead th').forEach(th => { th.classList.toggle('of-asc', th.dataset.sort === key && dir > 0); th.classList.toggle('of-desc', th.dataset.sort === key && dir < 0); });
    }
    table.tHead.addEventListener('click', e => {
      const th = e.target.closest('th[data-sort]'); if (!th) return;
      if (th.dataset.sort === key) dir = -dir; else { key = th.dataset.sort; dir = text[key] ? 1 : -1; }
      limit = 100; draw();
    });
    q.addEventListener('input', () => { limit = 100; draw(); });
    miss.addEventListener('change', () => { limit = 100; draw(); });
    more.addEventListener('click', e => { e.preventDefault(); limit += 200; draw(); });
    document.getElementById('of-prod-export').addEventListener('click', () => {
      download('produkter', ['Artikel', 'Produkt', 'Varumärke', 'Kategori', 'Inbyte', 'Rader', 'Antal', 'Manuella rader', 'Saknades', 'Andel saknades', 'Saknades webb', 'Saknades manuella',
        'Lovat till tidigare ordrar', 'Median arbetsdagar', 'Ej levererade', 'Lager nu'],
        rows.map(i => { const r = D.products[i], p = P[r[0]]; return [p[0], p[2], D.B[p[3]], D.K[p[4]], p[5] ? 'Ja' : 'Nej', r[1], r[2], r[3], r[4], pct1(r[4], r[1]).replace(' ', ''), r[6], r[7], r[5], r[8], r[9], r[10]]; }));
    });
    draw();
  })();
})();
