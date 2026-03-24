<?php
$todayTitle = date('Y-m-d');
?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=1920, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Infopanel - <?php echo $todayTitle; ?></title>

  <style>
    :root{
      --bg: #eef2f6;
      --panel: #ffffff;
      --text: #111827;
      --muted: #6b7280;
    }

    html, body { height: 100%; }
    body{
      margin:0;
      background: var(--bg);
      color: var(--text);
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }

    #app{
      height:100%;
      padding: 0;
      box-sizing: border-box;
    }

    #panel{
      height: 100%;
      width: 100%;
      background: var(--panel);
      border: 0;
      border-radius: 0;
      box-shadow: none;
      overflow: hidden;
      position: relative;
    }

    .stale{
      position:absolute;
      left:0; right:0; top:0;
      background: #111827;
      color:#fff;
      padding: 10px 14px;
      font-weight: 900;
      letter-spacing: .2px;
      display:none;
      z-index: 10;
    }
    .stale.is-on{ display:block; }

    .loading{
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      color: var(--muted);
      font-weight: 900;
      font-size: 22px;
    }
	/* ===== TV / Kiosk overrides (lägg sist så den vinner) ===== */
	@media screen {
	  html, body {
		color: #111827 !important;
		opacity: 1 !important;
		filter: none !important;
	  }

	  /* Lite luft runt om, men inte mobil-kollaps */
	  #app{
		padding: 14px 14px !important;
		height: 100%;
		box-sizing: border-box;
	  }

	  /* Panelen får lagom kantmarginal på TV */
	  #panel{
		height: calc(100% - 0px);
		max-width: 1850px !important;  /* justera: 17001950 */
		margin: 0 auto !important;
		border: 1px solid #e5e7eb !important;
		border-radius: 18px !important;
		box-shadow: 0 10px 24px rgba(0,0,0,.08) !important;
		background: #fff !important;
	  }

	  /* Om Chromium/Pi ger konstiga font-skalningar */
	  body{
		-webkit-text-size-adjust: 100% !important;
		text-size-adjust: 100% !important;
	  }
	}
  </style>
</head>

<body>
  <div id="app">
    <div id="panel">
      <div id="staleBanner" class="stale">DATA EJ UPPDATERAD  visar senast kända läge</div>
      <div id="panelBody" class="loading">Laddar infopanel</div>
    </div>
  </div>

  <script>
    (function(){
      const elBody  = document.getElementById('panelBody');
      const elStale = document.getElementById('staleBanner');

      const REFRESH_MS = 45000;
      let lastOkTs = 0;

      // Viktigt: skicka vidare querystring (så force_driveout funkar via infopanel.php)
      const pageParams = window.location.search ? window.location.search.replace(/^\?/, '') : '';

      async function loadPanel(){
        try{
          let url = 'infopanel_fragment.php?sid=' + Date.now();
          if (pageParams) {
            url += '&' + pageParams;
          }

          const res = await fetch(url, { cache: 'no-store' });
          if(!res.ok) throw new Error('HTTP ' + res.status);
          const html = await res.text();

          elBody.innerHTML = html;

          lastOkTs = Date.now();
          elStale.classList.remove('is-on');
        }catch(err){
          if (lastOkTs > 0) {
            elStale.classList.add('is-on');
          } else {
            elBody.textContent = 'Kunde inte ladda panelen.';
          }
        }
      }

      loadPanel();
      setInterval(loadPanel, REFRESH_MS);
    })();
  </script>
</body>
</html>
