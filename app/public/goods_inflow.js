/* Inleveransflöde (goods_inflow.php) – ritar diagrammen från window.INFLOW. Ren SVG, inga bibliotek. */
(function () {
  const D = window.INFLOW; if (!D) return;
  const T = D.tot;
  const nf = new Intl.NumberFormat('sv-SE');
  const f = n => nf.format(Math.round(n));
  const pct = (a, b) => b > 0 ? (100 * a / b).toFixed(0) + ' %' : '–';
  const pct1 = (a, b) => b > 0 ? (100 * a / b).toFixed(1).replace('.', ',') + ' %' : '–';
  const C = { direct: '#2a78d6', wait: '#eb6834', stock: '#1baf7a', in: '#52514e' };
  const NS = 'http://www.w3.org/2000/svg';
  function el(tag, attrs, parent) { const e = document.createElementNS(NS, tag); for (const k in attrs) e.setAttribute(k, attrs[k]); if (parent) parent.appendChild(e); return e; }
  function txt(parent, x, y, s, attrs) { const t = el('text', Object.assign({ x, y }, attrs || {}), parent); t.textContent = s; return t; }
  function svg(id, w, h) { const host = document.getElementById(id); if (!host) return null; const s = el('svg', { viewBox: `0 0 ${w} ${h}`, width: w, height: h, role: 'img' }); host.appendChild(s); return s; }

  const tip = document.getElementById('gi-tip');
  function hook(node, html) {
    node.classList.add('gi-mark'); node.setAttribute('tabindex', '0');
    const move = ev => {
      if (!ev.clientX) { const r = node.getBoundingClientRect(); ev = { clientX: r.left + r.width / 2, clientY: r.top }; }
      const w = tip.offsetWidth, h = tip.offsetHeight; let x = ev.clientX + 14, y = ev.clientY + 14;
      if (x + w > innerWidth - 8) x = ev.clientX - w - 14; if (y + h > innerHeight - 8) y = ev.clientY - h - 14;
      tip.style.left = x + 'px'; tip.style.top = y + 'px';
    };
    const show = ev => { tip.innerHTML = html; tip.hidden = false; move(ev); };
    node.addEventListener('mouseenter', show); node.addEventListener('mousemove', move); node.addEventListener('mouseleave', () => tip.hidden = true);
    node.addEventListener('focus', show); node.addEventListener('blur', () => tip.hidden = true);
  }
  function niceMax(v) { if (v <= 0) return 10; const p = Math.pow(10, Math.floor(Math.log10(v))); const m = v / p; const n = m <= 1 ? 1 : m <= 2 ? 2 : m <= 2.5 ? 2.5 : m <= 5 ? 5 : 10; return n * p; }

  // ---------- flöde
  (function () {
    const W = 900, H = 460, nw = 14, gap = 10;
    const s = svg('gi-flow', W, H); if (!s) return; s.style.minWidth = '680px';
    const waitTot = T.alloc - T.a01;
    const cols = [
      [{ id: 'in', label: 'Inlevererat', v: T.qty, c: C.in }],
      [{ id: 'A', label: 'Täckte väntande order', v: T.alloc, c: C.direct }, { id: 'B', label: 'Till lager', v: T.tl, c: C.stock }],
      [{ id: 'A1', label: 'Skickad inom 1 dag', v: T.a01, c: C.direct, src: 'A' }, { id: 'A2', label: 'Skickad efter 2–7 dagar', v: T.a27, c: C.wait, src: 'A' },
       { id: 'A3', label: 'Skickad efter mer än 7 dagar', v: T.o7, c: C.wait, src: 'A' }, { id: 'A5', label: 'Ej skickad ännu', v: T.ns, c: C.wait, src: 'A' },
       { id: 'B1', label: 'Slutsåld inom 1 dag', v: T.s1, c: C.stock, src: 'B' }, { id: 'B2', label: 'Slutsåld inom 2–7 dagar', v: T.s7, c: C.stock, src: 'B' },
       { id: 'B3', label: 'Slutsåld inom 8–30 dagar', v: T.s30, c: C.stock, src: 'B' }, { id: 'B4', label: 'Slutsåld efter mer än 30 dagar', v: T.sover30, c: C.stock, src: 'B' },
       { id: 'B5', label: 'Kvar i lager', v: T.kvar, c: C.stock, src: 'B' }]
    ];
    const xs = [30, 330, 610];
    const usable = H - 30 - gap * 8; const k = T.qty > 0 ? usable / T.qty : 0;
    const pos = {};
    cols.forEach((col, ci) => {
      let y = 15 + (ci === 0 ? gap * 4 : ci === 1 ? gap * 3.5 : 0);
      col.forEach((n, i) => { if (ci === 2 && i === 4) y += gap * 2; const h = Math.max(n.v * k, n.v > 0 ? 1.5 : 0); pos[n.id] = { x: xs[ci], y, h, n }; y += h + gap; });
    });
    const off = {};
    const links = [['in', 'A'], ['in', 'B']].concat(cols[2].map(n => [n.src, n.id]));
    const g = el('g', {}, s);
    links.forEach(([a, b]) => {
      const pa = pos[a], pb = pos[b]; if (pb.n.v <= 0) return; off[a] = off[a] || 0; off[b + '_in'] = off[b + '_in'] || 0;
      const h = pb.n.v * k; const y0 = pa.y + off[a], y1 = pb.y + off[b + '_in']; const x0 = pa.x + nw, x1 = pb.x; const cx = (x0 + x1) / 2;
      const p = el('path', { d: `M${x0},${y0} C${cx},${y0} ${cx},${y1} ${x1},${y1} L${x1},${y1 + h} C${cx},${y1 + h} ${cx},${y0 + h} ${x0},${y0 + h} Z`, fill: pb.n.c, 'fill-opacity': '0.28' }, g);
      hook(p, `<b>${pa.n.label} → ${pb.n.label}</b><br>${f(pb.n.v)} enheter · ${pct1(pb.n.v, T.qty)} av allt inlevererat`);
      off[a] += h; off[b + '_in'] += h;
    });
    const rightNodes = cols[2].map(n => pos[n.id]); let ly = 0;
    rightNodes.forEach((p, i) => { const want = p.y + p.h / 2; if (i === 4) ly += 8; p.ly = Math.max(want, ly + (i ? 30 : 0)); ly = p.ly; });
    const over = ly - (H - 16); if (over > 0) rightNodes.forEach(p => p.ly -= over);
    Object.values(pos).forEach(p => {
      const r = el('rect', { x: p.x, y: p.y, width: nw, height: p.h, fill: p.n.c, rx: 2 }, s);
      hook(r, `<b>${p.n.label}</b><br>${f(p.n.v)} enheter · ${pct1(p.n.v, T.qty)} av inlevererat` + (p.n.src ? `<br>${pct1(p.n.v, pos[p.n.src].n.v)} av "${pos[p.n.src].n.label}"` : ''));
      const lx = p.x + nw + 8, cy = p.y + p.h / 2;
      if (p.x === xs[2]) {
        if (p.n.v <= 0) { txt(s, lx + 14, p.ly + 4, `${p.n.label}: 0`, { class: 'muted', 'font-size': '11.5' }); return; }
        if (Math.abs(p.ly - cy) > 3) el('path', { d: `M${p.x + nw + 2},${cy} L${lx + 6},${p.ly}`, stroke: '#c3c2b7', 'stroke-width': 1, fill: 'none' }, s);
        el('circle', { cx: lx + 6, cy: p.ly, r: 2.5, fill: p.n.c }, s);
        txt(s, lx + 14, p.ly - 2, p.n.label, { class: 'ink', 'dominant-baseline': 'middle', 'font-size': '12.5' });
        txt(s, lx + 14, p.ly + 12, `${f(p.n.v)} · ${pct(p.n.v, pos[p.n.src].n.v)} av ${p.n.src === 'A' ? 'ordervarorna' : 'lagervarorna'}`, { class: 'muted', 'dominant-baseline': 'middle', 'font-size': '11.5' });
      } else {
        txt(s, lx, cy - 8, p.n.label, { class: 'ink', 'dominant-baseline': 'middle', 'font-size': '13', 'font-weight': '600' });
        txt(s, lx, cy + 8, `${f(p.n.v)} enheter · ${pct(p.n.v, T.qty)}`, { 'dominant-baseline': 'middle', 'font-size': '12' });
      }
    });
  })();

  // ---------- per dag (staplade kolumner)
  (function () {
    const days = Object.keys(D.day); if (!days.length) return;
    const W = 900, H = 300, L = 54, R = 10, Tm = 22, B = 44;
    const s = svg('gi-day', W, H); if (!s) return; s.style.minWidth = '560px';
    const max = niceMax(Math.max(...days.map(d => D.day[d].qty)) * 1.1);
    const pw = W - L - R, ph = H - Tm - B; const y = v => Tm + ph - v / max * ph;
    const gr = el('g', { class: 'grid' }, s); const step = max / 4;
    for (let v = 0; v <= max + 1e-9; v += step) { el('line', { x1: L, x2: W - R, y1: y(v), y2: y(v) }, gr); txt(s, L - 8, y(v) + 4, f(v), { 'text-anchor': 'end', class: 'muted', 'font-size': '11' }); }
    const bw = pw / days.length, bar = Math.min(bw * 0.62, 60);
    const names = { a01: 'Täckte order, ut inom 1 dag', wait: 'Täckte order men väntade', tl: 'Till lager' };
    const wk = ['sön', 'mån', 'tis', 'ons', 'tor', 'fre', 'lör'];
    const every = Math.ceil(days.length / 16);
    days.forEach((d, i) => {
      const g = D.day[d]; const segs = [['a01', g.a01, C.direct], ['wait', g.alloc - g.a01, C.wait], ['tl', g.tl, C.stock]];
      const x = L + i * bw + (bw - bar) / 2; let acc = 0;
      segs.forEach(([k, v, c]) => {
        if (v <= 0) return; const y0 = y(acc + v), y1 = y(acc); const h = Math.max(y1 - y0 - 2, 0.5);
        const r = el('rect', { x, y: y0 + 1, width: bar, height: h, fill: c, rx: k === 'tl' ? 3 : 0 }, s);
        hook(r, `<b>${d}</b> · ${names[k]}<br>${f(v)} enheter · ${pct1(v, g.qty)} av dagens ${f(g.qty)}`);
        acc += v;
      });
      if (days.length <= 16) txt(s, x + bar / 2, y(g.qty) - 6, f(g.qty), { 'text-anchor': 'middle', class: 'muted', 'font-size': '11' });
      if (i % every === 0) {
        const dt = new Date(d + 'T00:00:00');
        txt(s, x + bar / 2, H - 26, d.slice(5), { 'text-anchor': 'middle', 'font-size': '11.5' });
        txt(s, x + bar / 2, H - 12, wk[dt.getDay()], { 'text-anchor': 'middle', class: 'muted', 'font-size': '11' });
      }
    });
    el('line', { x1: L, x2: W - R, y1: y(0), y2: y(0), class: 'axis' }, s);
  })();

  // ---------- dagar till utleverans (histogram)
  (function () {
    const keys = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', 'ej'];
    const labels = keys.map(k => k === '15' ? '15+' : k);
    const vals = keys.map(k => D.ship_hist[k] || 0);
    const W = 440, H = 240, L = 46, R = 8, Tm = 22, B = 40; const s = svg('gi-days', W, H); if (!s) return; s.style.minWidth = '380px';
    const max = niceMax(Math.max(1, ...vals) * 1.12); const pw = W - L - R, ph = H - Tm - B; const y = v => Tm + ph - v / max * ph;
    const gr = el('g', { class: 'grid' }, s); const step = max / 4;
    for (let v = 0; v <= max + 1e-9; v += step) { el('line', { x1: L, x2: W - R, y1: y(v), y2: y(v) }, gr); txt(s, L - 6, y(v) + 4, f(v), { 'text-anchor': 'end', class: 'muted', 'font-size': '10.5' }); }
    const bw = pw / keys.length, bar = bw - 3;
    keys.forEach((k, i) => {
      const x = L + i * bw + 1.5; const v = vals[i]; const h = Math.max(y(0) - y(v), v > 0 ? 1.5 : 0);
      const col = i <= 1 ? C.direct : C.wait;
      const r = el('rect', { x, y: y(0) - h, width: bar, height: h, fill: col, rx: 2 }, s);
      const lab = k === 'ej' ? 'Ej skickad ännu' : k === '15' ? '15 dagar eller mer' : `${k} ${k === '1' ? 'dag' : 'dagar'}`;
      hook(r, `<b>${lab}</b><br>${f(v)} enheter · ${pct1(v, T.alloc)} av ordervarorna`);
      if (v > 0 && (i === 0 || k === 'ej' || v === Math.max(...vals))) txt(s, x + bar / 2, y(v) - 5, f(v), { 'text-anchor': 'middle', class: 'ink', 'font-size': '11' });
      if (i % 2 === 0 || k === 'ej') txt(s, x + bar / 2, H - 22, labels[i], { 'text-anchor': 'middle', 'font-size': '10.5' });
    });
    txt(s, L + pw / 2, H - 6, 'dagar från inleverans till utleverans', { 'text-anchor': 'middle', class: 'muted', 'font-size': '11' });
    el('line', { x1: L, x2: W - R, y1: y(0), y2: y(0), class: 'axis' }, s);
  })();

  // ---------- orsaker
  (function () {
    const order = ['Väntade på annan vara (inlevererad senare)', 'Andra rader öppna, ingen inleverans', 'Komplett men skickad senare', 'Ej skickad ännu'];
    const items = order.map(k => [k, D.reason[k] || 0]); const tot = items.reduce((a, b) => a + b[1], 0);
    const W = 440, H = items.length * 50 + 6, s = svg('gi-reason', W, H); if (!s) return; s.style.minWidth = '380px';
    const max = niceMax(Math.max(1, ...items.map(i => i[1])) * 1.05), L = 8, pw = W - L - 90;
    items.forEach(([lab, v], i) => {
      const y0 = 4 + i * 50; txt(s, L, y0 + 10, lab, { class: 'ink', 'font-size': '12.5' });
      const w = Math.max(v / max * pw, v > 0 ? 2 : 0.5);
      const r = el('rect', { x: L, y: y0 + 18, width: w, height: 16, fill: C.wait, rx: 3 }, s);
      hook(r, `<b>${lab}</b><br>${f(v)} enheter · ${pct1(v, tot)} av dem som inte gick direkt`);
      txt(s, L + w + 8, y0 + 31, `${f(v)} · ${pct(v, tot)}`, { 'font-size': '12' });
    });
  })();

  // ---------- lager: dagar till slutsålt
  (function () {
    const items = [['Slutsåld inom 1 dag', T.s1], ['Slutsåld inom 2–7 dagar', T.s7], ['Slutsåld inom 8–30 dagar', T.s30], ['Slutsåld efter mer än 30 dagar', T.sover30], ['Kvar i lager', T.kvar]];
    const W = 900, rowH = 32, H = items.length * rowH + 8, s = svg('gi-stock', W, H); if (!s) return; s.style.minWidth = '560px';
    const L = 250, max = niceMax(Math.max(1, ...items.map(i => i[1])) * 1.05), pw = W - L - 120;
    items.forEach(([lab, v], i) => {
      const y0 = 4 + i * rowH; txt(s, L - 12, y0 + 16, lab, { 'text-anchor': 'end', class: 'ink', 'font-size': '13', 'dominant-baseline': 'middle' });
      const w = Math.max(v / max * pw, v > 0 ? 2 : 0.5);
      const r = el('rect', { x: L, y: y0 + 6, width: w, height: 20, fill: C.stock, rx: 3 }, s);
      hook(r, `<b>${lab}</b><br>${f(v)} enheter · ${pct1(v, T.tl)} av det som gick till lager`);
      txt(s, L + w + 8, y0 + 16, `${f(v)} · ${pct(v, T.tl)}`, { 'font-size': '12', 'dominant-baseline': 'middle' });
    });
  })();

  // ---------- kategorier
  (function () {
    const cats = D.cat; if (!cats.length) return;
    const W = 440, rowH = 30, H = cats.length * rowH + 6, L = 170, pw = W - L - 60;
    const s = svg('gi-cats', W, H); if (!s) return; s.style.minWidth = '380px';
    const max = niceMax(Math.max(1, ...cats.map(c => c.qty)) * 1.02);
    cats.forEach((c, i) => {
      const y0 = 3 + i * rowH; const wait = c.alloc - c.a01;
      txt(s, L - 10, y0 + 15, c.name.length > 24 ? c.name.slice(0, 23) + '…' : c.name, { 'text-anchor': 'end', class: 'ink', 'font-size': '12', 'dominant-baseline': 'middle' });
      let x = L;
      [['Order, ut inom 1 dag', c.a01, C.direct], ['Order, väntade', wait, C.wait], ['Till lager', c.tl, C.stock]].forEach(([lab, v, col]) => {
        const w = v / max * pw; if (w <= 0) return;
        const r = el('rect', { x, y: y0 + 6, width: Math.max(w - 2, 0.5), height: 18, fill: col, rx: 2 }, s);
        hook(r, `<b>${c.name}</b> · ${lab}<br>${f(v)} enheter · ${pct1(v, c.qty)} av kategorins ${f(c.qty)}`);
        x += w;
      });
      txt(s, x + 6, y0 + 15, f(c.qty), { 'font-size': '11', 'dominant-baseline': 'middle' });
    });
  })();
})();
