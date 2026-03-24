<?php
// date_filter_widget.php  minimal & säker (stoppar ALDRIG exekveringen)

// Konfig tas från sidans scope, annars defaults:
if (!isset($dr_id)      || !$dr_id)      $dr_id = 'purchase_kpi';
if (!isset($dr_method)  || !$dr_method)  $dr_method = 'get';
if (!isset($dr_action))                 $dr_action = '';
if (!isset($dr_default) || !$dr_default) $dr_default = 'prevMonth';

// Läs in från GET/POST
$src = (strtolower($dr_method)==='get') ? $_GET : $_POST;
$preset_key = 'preset_'.$dr_id;
$from_key   = 'from_'.$dr_id;
$to_key     = 'to_'.$dr_id;

$preset = isset($src[$preset_key]) ? (string)$src[$preset_key] : '';
$from   = isset($src[$from_key])   ? (string)$src[$from_key]   : '';
$to     = isset($src[$to_key])     ? (string)$src[$to_key]     : '';

// Sätt rimliga defaultdatum
if ($from==='' || $to==='') {
  $today = date('Y-m-d');
  switch ($dr_default) {
    case 'today':   $from=$today; $to=$today; break;
    case 'last7':   $from=date('Y-m-d', strtotime('-6 days')); $to=$today; break;
    case 'last30':  $from=date('Y-m-d', strtotime('-29 days')); $to=$today; break;
    case 'prevWeek':
      $dow=(int)date('N'); $thisMon=strtotime('-'.($dow-1).' days');
      $prevMon=strtotime('-7 days',$thisMon); $prevSun=strtotime('+6 days',$prevMon);
      $from=date('Y-m-d',$prevMon); $to=date('Y-m-d',$prevSun); break;
    case 'prevMonth':
      $firstThis=strtotime(date('Y-m-01')); $lastPrev=strtotime('-1 day',$firstThis);
      $firstPrev=strtotime(date('Y-m-01',$lastPrev));
      $from=date('Y-m-d',$firstPrev); $to=date('Y-m-d',$lastPrev); break;
    default:
      $y=date('Y-m-d',strtotime('-1 day')); $from=$y; $to=$y; break;
  }
  if ($preset==='') $preset=$dr_default;
}

// Exponera $from/$to/$preset till sidan:
$GLOBALS['dr_exports_'.$dr_id] = array('from'=>$from,'to'=>$to,'preset'=>$preset);

// Render (ingen onload-autosubmit, bara vid knapptryck)
$h = function($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); };
$active = function($name) use ($preset){ return $preset===$name ? ' dr-btn-active' : ''; };

// Engångs-CSS
if (!defined('DRW_CSS')) {
  define('DRW_CSS', true);
  echo '<style>.dr-wrap{border:1px solid #e5e7eb;padding:12px;border-radius:8px;background:#fff;margin-bottom:14px}
  .dr-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .dr-btn{padding:6px 10px;border:1px solid #e5e7eb;background:#f8fafc;border-radius:6px;cursor:pointer}
  .dr-btn:hover{background:#eef2f7}
  .dr-btn-active{border-color:#ea580c;background:#ffedd5;color:#9a3412}
  .dr-primary{padding:6px 12px;border:1px solid #2563eb;background:#2563eb;color:#fff;border-radius:6px;cursor:pointer}
  .dr-primary:hover{background:#1d4ed8;border-color:#1d4ed8}</style>';
}

$form_id = 'form_'.$dr_id;
echo '<div class="dr-wrap"><form method="'.$h($dr_method).'" action="'.$h($dr_action).'" id="'.$h($form_id).'">
<input type="hidden" name="'.$h($preset_key).'" value="'.$h($preset).'">
<div class="dr-row">
  <label>Från:&nbsp;<input type="date" name="'.$h($from_key).'" value="'.$h($from).'"></label>
  <label>Till:&nbsp;<input type="date" name="'.$h($to_key).'" value="'.$h($to).'"></label>

  <button type="submit" class="dr-primary" onclick="this.form.elements[\''.$h($preset_key).'\'].value=\'custom\'">Visa</button>

  <button type="button" class="dr-btn'.$active('today').'"     onclick="drwSet(\''.$h($form_id).'\',\''.$h($preset_key).'\',\''.$h($from_key).'\',\''.$h($to_key).'\',\'today\')">Idag</button>
  <button type="button" class="dr-btn'.$active('yesterday').'" onclick="drwSet(\''.$h($form_id).'\',\''.$h($preset_key).'\',\''.$h($from_key).'\',\''.$h($to_key).'\',\'yesterday\')">Gårdagen</button>
  <button type="button" class="dr-btn'.$active('last7').'"     onclick="drwSet(\''.$h($form_id).'\',\''.$h($preset_key).'\',\''.$h($from_key).'\',\''.$h($to_key).'\',\'last7\')">Senaste 7 dagarna</button>
  <button type="button" class="dr-btn'.$active('last30').'"    onclick="drwSet(\''.$h($form_id).'\',\''.$h($preset_key).'\',\''.$h($from_key).'\',\''.$h($to_key).'\',\'last30\')">Senaste 30 dagarna</button>
  <button type="button" class="dr-btn'.$active('prevWeek').'"  onclick="drwSet(\''.$h($form_id).'\',\''.$h($preset_key).'\',\''.$h($from_key).'\',\''.$h($to_key).'\',\'prevWeek\')">Föregående vecka</button>
  <button type="button" class="dr-btn'.$active('prevMonth').'" onclick="drwSet(\''.$h($form_id).'\',\''.$h($preset_key).'\',\''.$h($from_key).'\',\''.$h($to_key).'\',\'prevMonth\')">Föregående månad</button>
</div></form></div>';

?>
<script>
function drwSet(formId,presetName,fromName,toName,kind){
  var form=document.getElementById(formId);
  var f=form.elements[fromName], t=form.elements[toName], p=form.elements[presetName];
  function fmt(d){var y=d.getFullYear(),m=('0'+(d.getMonth()+1)).slice(-2),da=('0'+d.getDate()).slice(-2);return y+'-'+m+'-'+da;}
  var now=new Date();
  if(kind==='today'){var d=new Date(); f.value=fmt(d); t.value=fmt(d);}
  else if(kind==='yesterday'){var d=new Date(now.getFullYear(),now.getMonth(),now.getDate()-1); f.value=fmt(d); t.value=fmt(d);}
  else if(kind==='last7'){var e=new Date(now.getFullYear(),now.getMonth(),now.getDate()); var s=new Date(e.getFullYear(),e.getMonth(),e.getDate()-6); f.value=fmt(s); t.value=fmt(e);}
  else if(kind==='last30'){var e=new Date(now.getFullYear(),now.getMonth(),now.getDate()); var s=new Date(e.getFullYear(),e.getMonth(),e.getDate()-29); f.value=fmt(s); t.value=fmt(e);}
  else if(kind==='prevWeek'){var day=now.getDay(); if(day===0) day=7; var tm=new Date(now.getFullYear(),now.getMonth(),now.getDate()-(day-1));
    var pm=new Date(tm.getFullYear(),tm.getMonth(),tm.getDate()-7); var ps=new Date(pm.getFullYear(),pm.getMonth(),pm.getDate()+6); f.value=fmt(pm); t.value=fmt(ps);}
  else if(kind==='prevMonth'){var ft=new Date(now.getFullYear(),now.getMonth(),1); var lp=new Date(ft.getFullYear(),ft.getMonth(),0); var fp=new Date(lp.getFullYear(),lp.getMonth(),1); f.value=fmt(fp); t.value=fmt(lp);}
  p.value = kind; form.submit();
}
</script>
