<?php
	echo "\n</div>\n"; // slut mainpanel
	echo "\n<div class=\"clear\"></div>\n";
	echo "\n<div class=\"top5\"></div>\n";
	echo "\n<div class=\"clear hr_gray\"></div>\n";
	echo "<div id=\"sidfot\">Copyright © 1995 - " . date("Y", time()) . " CyberPhoto</div>\n";
	echo "</div>\n"; // slut content
	echo "</div>\n"; // slut centermiddle
	echo "<div id=\"centerbottom\"></div>\n";
	echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"wz_tooltip_front.js\"></script>\n";
?>
<!-- === Drawer/backdrop: kan ligga i din globala footer === -->
<div id="drawer-backdrop" class="drawer-backdrop" aria-hidden="true"></div>

<aside id="drawer" class="drawer" role="dialog" aria-modal="true" aria-labelledby="drawer-title">
  <header class="drawer-header">
    <h2 id="drawer-title">Detaljer</h2>
    <button type="button" id="drawer-close" aria-label="Stäng">×</button>
  </header>
  <div id="drawer-content" class="drawer-content"></div>
</aside>


<!-- Preferences Modal (global) -->
<div id="prefModal" class="pref-modal" aria-hidden="true">
  <div class="pref-dialog" role="dialog" aria-modal="true" aria-labelledby="prefTitle">
    <div class="pref-head">
      <strong id="prefTitle">Inställningar</strong>
      <button type="button" id="prefClose" class="pref-close" aria-label="Stäng">×</button>
    </div>
    <div class="pref-body">
      <label class="pref-row">
        <input type="checkbox" id="chkHideUsed" value="1">
        <span>Dölj begagnat</span>
      </label>
      <label class="pref-row">
        <input type="checkbox" id="chkHideDemo" value="1">
        <span>Dölj fyndvaror</span>
      </label>
    </div>
    <div class="pref-foot">
      <button type="button" id="prefSave" class="pref-btn">Spara</button>
    </div>
  </div>
</div>


<script>
(function(){
  // Förhindra dubbel init
  if (window.__drawerAjaxInit) return;
  window.__drawerAjaxInit = true;

  // ====== Konfiguration ======
  var DEBUG    = false;
  var ENDPOINT = 'ajax/drawer_details.php'; // relativ till /admin/

  // ====== Element ======
  var drawer    = document.getElementById('drawer');
  var backdrop  = document.getElementById('drawer-backdrop');
  var content   = document.getElementById('drawer-content');
  var closeBtn  = document.getElementById('drawer-close');
  var lastFocusEl = null;

  // ====== Små views ======
  function loadingView(){ return '<div id="drawer-loading" style="padding:12px;color:#666;">Laddar…</div>'; }
  function errorView(status){
    return '<div style="padding:12px;color:#b00;">Kunde inte hämta information (HTTP '+(status||'fel')+').</div>';
  }

  // ====== Öppna/stäng ======
  function openShell(html){
    if (!drawer || !content) return;
    content.innerHTML = html || '';
    lastFocusEl = document.activeElement;
    drawer.classList.add('drawer--open');
    if (backdrop) backdrop.classList.add('drawer-backdrop--open');
    try { drawer.setAttribute('tabindex','-1'); drawer.focus(); } catch(e){}
  }
  function closeDrawer(){
    if (!drawer || !content) return;
    drawer.classList.remove('drawer--open');
    if (backdrop) backdrop.classList.remove('drawer-backdrop--open');
    content.innerHTML = '';
    if (lastFocusEl && typeof lastFocusEl.focus === 'function') {
      try { lastFocusEl.focus(); } catch(e){}
    }
  }

  // ====== AJAX-hämtare (det här är den ENDA globala openDrawer du ska använda) ======
  function openDrawer(type, id){
    // Städa/säkra värden
    type = (type || 'product').toLowerCase();
    id   = (id || '').toString().trim();
    if (!id) id = '0';

    openShell(loadingView());

    var url = ENDPOINT + '?type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id);
    if (DEBUG) url += '&debug=1';

    return new Promise(function(resolve){
      var xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.onreadystatechange = function(){
        if (xhr.readyState !== 4) return;
        var txt = (xhr.responseText || '').trim();
        if (txt !== '') {
          content.innerHTML = txt;
        } else if (xhr.status !== 200) {
          content.innerHTML = errorView(xhr.status);
        } else {
          content.innerHTML = '<div style="padding:12px;">Inget innehåll att visa.</div>';
        }
        resolve();
      };
      xhr.send();
    });
  }

  // ====== Globala lyssnare ======
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' || e.keyCode === 27) closeDrawer();
  });
  if (backdrop) backdrop.addEventListener('click', closeDrawer);
  if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);

	// EN (1) klick-lyssnare för både .btn-more och .drawer-link
	document.addEventListener('click', function(e){
	  var el = e.target && e.target.closest ? e.target.closest('.btn-more, .drawer-link') : null;
	  if (!el) return;

	  // Släpp igenom modifierade klick och mittklick (ny flik/fönster)
	  if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button === 1) return;

	  // Bara “vanligt” vänsterklick
	  if (e.button !== 0) return;

	  e.preventDefault();

	  var type = (el.getAttribute('data-type') || 'product').toLowerCase();
	  var pid  = (el.getAttribute('data-pid') || '').trim();
	  var id   = (pid && /^\d+$/.test(pid)) ? pid : (el.getAttribute('data-id') || '').trim();

	  // Öppna drawer inline
	  if (typeof openDrawer === 'function') openDrawer(type, id);

	  // Uppdatera adressraden till länkens href (så delningslänken speglas)
	  var href = el.getAttribute('href');
	  if (href && history && history.replaceState) history.replaceState(null, '', href);
	}, false);

  // Exponera API globalt
  window.openDrawer = openDrawer;
  window.drawerClose = closeDrawer;
  window.dwToggleBlock = function(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
  };
})();
</script>



<script>
(function () {
  var map = {
    'p': '#searchProduct',
    'k': '#searchCustomer',
    'o': '#searchOrder'
  };

  function isTypingContext(el){
    if (!el) return false;
    var tag = el.tagName ? el.tagName.toLowerCase() : '';
    if (tag === 'input' || tag === 'textarea' || tag === 'select') return true;
    if (el.isContentEditable) return true;
    return false;
  }

  function focusAndSelect(sel) {
    var el = document.querySelector(sel);
    if (!el) return;
    el.focus();
    try { el.select(); } catch(e){}
    try { el.scrollIntoView({behavior:'smooth', block:'center'});} catch(e){}
  }

  document.addEventListener('keydown', function(e){
    var key = (e.key || '').toLowerCase();

    // 1) Alt+P/K/O funkar alltid (som tidigare)
    if (e.altKey && !e.ctrlKey && !e.metaKey && !e.shiftKey && map[key]) {
      e.preventDefault();
      focusAndSelect(map[key]);
      return;
    }

    // 2) Bara P/K/O när man INTE står i ett input/textarea/contenteditable
    if (!e.altKey && !e.ctrlKey && !e.metaKey && !e.shiftKey && map[key]) {
      if (isTypingContext(document.activeElement)) return; // låt bokstaven skrivas i fältet
      e.preventDefault();
      focusAndSelect(map[key]);
      return;
    }

    // Bonus: ESC = ta bort fokus från fält
    if (key === 'escape' && isTypingContext(document.activeElement)) {
      document.activeElement.blur();
    }
  }, false);
})();
</script>

<script>
(function(){
  document.querySelectorAll('form.qs-form').forEach(function(f){
    f.addEventListener('submit', function(ev){
      var mode = (f.querySelector('input[name="mode"]')||{}).value || 'product';
      var page = (f.querySelector('input[name="page"]')||{}).value || '1';
      var qEl  = f.querySelector('input[name="q"]');
      var q    = qEl ? (qEl.value || '').trim() : '';
      var dest = (f.getAttribute('action')||'/search_dispatch.php')
               + '?mode=' + encodeURIComponent(mode)
               + '&page=' + encodeURIComponent(page)
               + '&q='    + encodeURIComponent(q);

      // ?? Lägg till dina snabbfilter här (inkl. not_web)
      ['in_stock','discontinued','used_web','used_offweb','old_tradeins','not_web'].forEach(function(name){
        var el = f.querySelector('input[name="'+name+'"]');
        if (el && el.checked) dest += '&' + encodeURIComponent(name) + '=1';
      });

      ev.stopImmediatePropagation();
      ev.preventDefault();
      window.location.href = dest;
    }, true); // capture=true
  });
})();
</script>

<script>
(function(){
  window.triggerCustomer = function(id){
    try{
      var a = document.createElement('a');
      a.href = '#';
      a.className = 'drawer-link btn-more';
      a.setAttribute('data-type','customer');
      a.setAttribute('data-id', String(id));
      a.style.display = 'none';
      document.body.appendChild(a);
      a.click();
      setTimeout(function(){ a.remove(); }, 0);
    }catch(e){}
  };
})();
</script>

<script>
document.addEventListener('click', function(e){
  var a = e.target.closest && e.target.closest('a.popup-link');
  if (!a) return;
  e.preventDefault();
  var url  = a.getAttribute('href') || '#';
  var name = a.getAttribute('data-popup') || 'popup_win';

  // standard
  var w = 800, h = 900;
  // orderlänkar: 1000x600
  if (a.classList.contains('order-link')) { w = 1000; h = 600; }

  var feat = 'width='+w+',height='+h+',menubar=0,toolbar=0,location=0,status=0,resizable=1,scrollbars=1';
  var win = window.open(url, name, feat);
  if (win) { try { win.opener = null; } catch(_){} }
}, false);
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var modal = document.getElementById('prefModal');
  var btn   = document.getElementById('prefBtn');   // tratt-knappen (filter)
  var close = document.getElementById('prefClose');
  var save  = document.getElementById('prefSave');
  var chkUsed = document.getElementById('chkHideUsed');
  var chkDemo = document.getElementById('chkHideDemo');
  var box   = document.getElementById('activePrefs');

  // === Cookie helpers (utan regex, kan inte krascha) ===
  function getCookie(name){
    var target = '; ' + name + '=';
    var parts = ('; ' + document.cookie).split(target);
    if (parts.length < 2) return null;
    return decodeURIComponent(parts.pop().split(';').shift());
  }
  function setCookie(name, value, days){
    var d = new Date(); d.setTime(d.getTime() + (days*24*60*60*1000));
    var cookie = name + '=' + encodeURIComponent(value) +
                 '; expires=' + d.toUTCString() +
                 '; path=/' + '; SameSite=Lax';
    if (location.protocol === 'https:') cookie += '; Secure';
    document.cookie = cookie;
  }
  function delCookie(name){
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax';
  }

  // === Init checkboxes från cookies (om elementen finns) ===
  if (chkUsed) chkUsed.checked = (getCookie('pref_hide_tradein') === '1');
  if (chkDemo) chkDemo.checked = (getCookie('pref_hide_demo') === '1');

  // === Öppna / stäng modal ===
  if (btn && modal) {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      modal.classList.add('show');
    });
  }
  if (close && modal) close.addEventListener('click', function(){ modal.classList.remove('show'); });
  if (modal) modal.addEventListener('click', function(e){ if (e.target === modal) modal.classList.remove('show'); });

  // === Spara båda cookies + reload ===
  if (save) {
    save.addEventListener('click', function(){
      setCookie('pref_hide_tradein', (chkUsed && chkUsed.checked) ? '1' : '0', 365);
      setCookie('pref_hide_demo',    (chkDemo && chkDemo.checked) ? '1' : '0', 365);
      location.reload();
    });
  }

  // === Visuell indikator (ENDAST modal-filter ska påverka) ===
  var anyActive =
    (getCookie('pref_hide_tradein') === '1') ||
    (getCookie('pref_hide_demo') === '1');

  // A) färga tratten: toggla båda klassnamn för kompatibilitet
  if (btn) {
    btn.classList.toggle('active', !!anyActive);
    btn.classList.toggle('is-active', !!anyActive);
  }

  // B) “Aktiva inställningar”-box
  if (box) {
    var items = [];
    if (getCookie('pref_hide_tradein') === '1') {
      items.push('<span class="apill" data-key="pref_hide_tradein">Döljer: Begagnat <b>&times;</b></span>');
    }
    if (getCookie('pref_hide_demo') === '1') {
      items.push('<span class="apill" data-key="pref_hide_demo">Döljer: Fyndvaror <b>&times;</b></span>');
    }

    box.innerHTML = items.join(' ');
    var active = items.length > 0;
    box.style.display = active ? 'block' : 'none';
    box.classList.toggle('on', active);

    box.addEventListener('click', function(e){
      var pill = e.target.closest ? e.target.closest('.apill') : null;
      if (!pill) return;
      delCookie(pill.getAttribute('data-key'));
      location.reload();
    });
  }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  (function(){
    var url = new URL(window.location.href);
    var pid = url.searchParams.get('drawer');
    if (!pid) return;

    // Försök hitta länk i listan och simulera "vanligt klick" (så samma flöde körs)
    var el = document.querySelector('a.drawer-link[data-pid="'+pid+'"]');
    if (el) {
      // simulera klick utan modifierare
      var ev = new MouseEvent('click', { bubbles: true, cancelable: true, button: 0 });
      el.dispatchEvent(ev);
    } else if (typeof loadDrawer === 'function') {
      // Om raden inte finns i DOM (t.ex. ny flik utan listan laddad), öppna direkt
      loadDrawer('product', pid);
    }
  })();
});
</script>

<script>
/*!
 * Auto-open Customer Drawer on Single Hit
 * Non-invasive: kräver bara att kundrader renderar <a class="drawer-link" data-type="customer" data-id="...">
 * Förutsätter att window.openDrawer(type,id) finns (som i dina skript).
 */
(function(){
  // Skydd mot dubbelinit
  if (window.__autoCustomerHitInit) return;
  window.__autoCustomerHitInit = true;

  // Feature toggle om du vill kunna stänga av i konsollen
  window.__autoCustomerHitEnabled = true;

  var fired = false;
  var lastCheckTs = 0;

  function qsParam(name){
    try {
      var u = new URL(window.location.href);
      return u.searchParams.get(name) || '';
    } catch(e){ return ''; }
  }

  function getCustomerLinks(){
    // Sök brett i hela dokumentet – du använder redan delegating click på document
    return Array.prototype.slice.call(
      document.querySelectorAll('a.drawer-link[data-type="customer"][data-id]')
    );
  }

  function simulateClick(el){
    try {
      var ev = new MouseEvent('click', { bubbles:true, cancelable:true, button:0 });
      el.dispatchEvent(ev);
    } catch(e){}
  }

  function tryAutoOpen(reason){
    if (!window.__autoCustomerHitEnabled) return;
    if (fired) return; // öppnat redan
    if (typeof window.openDrawer !== 'function') return;

    var mode = (qsParam('mode') || '').toLowerCase();
    if (mode !== 'customer') return;

    var q = (qsParam('q') || '').trim();
    if (!q) return;

    // Debounce: undvik att spamma om DOM muterar mycket
    var now = Date.now();
    if (now - lastCheckTs < 80) return;
    lastCheckTs = now;

    var links = getCustomerLinks();
    if (links.length !== 1) return;

    var a = links[0];
    var id = (a.getAttribute('data-id') || '').trim();
    if (!id) return;

    // Markera som körd, öppna och spegla adressrad (så delning funkar)
    fired = true;
    simulateClick(a);

    // Sätt ?drawer=<id> i URL utan sidladdning – du har redan generellt stöd för att spegla länkar
    try {
      var url = new URL(window.location.href);
      url.searchParams.set('drawer', String(id));
      history.replaceState(null, '', url.toString());
    } catch(e){}
    // DEBUG-logg kan ge spårbarhet i felsök, men default tyst
    // console.debug('[auto-customer-hit] opened id=%s (%s)', id, reason||'');
  }

  // 1) Kör en gång efter DOMContentLoaded + litet delay (låter listan hinna rendera)
  document.addEventListener('DOMContentLoaded', function(){
    setTimeout(function(){ tryAutoOpen('dom-ready'); }, 120);
  });

  // 2) Observera DOM-förändringar (listor som fylls via server/partial render)
  // Vi observerar hela body – låg risk, men effektivt då du redan har enkel markup
  var mo;
  try {
    mo = new MutationObserver(function(mutations){
      // Minimera arbete: bara re-check när noder lagts till
      for (var i=0;i<mutations.length;i++){
        if (mutations[i].addedNodes && mutations[i].addedNodes.length){
          tryAutoOpen('mutation');
          break;
        }
      }
    });
    mo.observe(document.documentElement || document.body, { childList:true, subtree:true });
  } catch(e){}

  // 3) Fallback: kör ett par gånger till första sekunden (om renderingen är seg)
  var retries = 8, interval = 120, i = 0;
  var t = setInterval(function(){
    if (fired || i++ >= retries) { clearInterval(t); return; }
    tryAutoOpen('retry-'+i);
  }, interval);
})();
</script>

<script>
(function(){
  if (window.__copyChipInit) return;
  window.__copyChipInit = true;

  function copyText(txt, cb){
    txt = String(txt || '');
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(txt).then(function(){ cb && cb(true); }, function(){ cb && cb(false); });
    } else {
      try{
        var ta = document.createElement('textarea');
        ta.value = txt;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        var ok = document.execCommand('copy');
        document.body.removeChild(ta);
        cb && cb(ok);
      } catch(e){
        cb && cb(false);
      }
    }
  }

  document.addEventListener('click', function(e){
    var el = e.target && e.target.closest('.copy-chip');
    if (!el) return;

    var txt = el.getAttribute('data-copy') || '';
    if (!txt) return;

    copyText(txt, function(ok){
      if (!ok) return;
      var old = el.getAttribute('title') || '';
      el.setAttribute('title','Kopierat!');
      el.classList.add('copied');
      setTimeout(function(){
        el.classList.remove('copied');
        el.setAttribute('title', old || '');
      }, 1000);
    });
  }, false);
})();
</script>

<?php
	echo "</body>\n";
	echo "</html>\n";
?>