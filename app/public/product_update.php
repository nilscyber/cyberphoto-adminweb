<?php
spl_autoload_register(function ($class) {
    include $class . '.php';
});

session_start();
extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_SKIP);
extract($_COOKIE, EXTR_SKIP);

$pac_exist    = false;
$is_hcampaign = (isset($_GET['hcampaign']) && $_GET['hcampaign'] === "yes");

// Sverige only
$sv = true;
$fi = false;
$no = false;

/**
 * Beräkna förvalt "Tid för uppdatering" för helgkampanj (Sverige).
 */
function getFridayTime()
{
    $get_today_day = date('w', time());

    if (!empty($_COOKIE['weekendFromSE'])) {
        $friday = $_COOKIE['weekendFromSE'];
    } elseif ($get_today_day == 5) {
        // Idag fredag  sätt 17:00 eller nu om vi redan passerat den tiden
        $today_release = date("Y-m-d H:i:s", mktime(17, 0, 0, date("m"), date("d"), date("Y")));
        if (strtotime($today_release) > time()) {
            $friday = $today_release;
        } else {
            $friday = date("Y-m-d H:i:s");
        }
    } elseif ($get_today_day == 6 || $get_today_day == 0) {
        // Lördag/söndag  använd nu
        $friday = date("Y-m-d H:i:s");
    } else {
        // Hitta nästa fredag kl 17:00
        $i = 1;
        do {
            $makedate     = mktime(17, 0, 0, date("m"), date("d") + $i, date("Y"));
            $getdayofweek = date('w', $makedate);

            if ($getdayofweek == 5) {
                $friday = date("Y-m-d H:i:s", $makedate);
                break;
            }
            $i++;
        } while ($i < 10);
    }

    return $friday;
}

/**
 * Beräkna förvalt "Tid för återgång" (söndag kväll) för helgkampanj (Sverige).
 */
function getSundayTime()
{
    $get_today_day = date('w', time());

    if (!empty($_COOKIE['weekendToSE'])) {
        $sunday = $_COOKIE['weekendToSE'];
    } elseif ($get_today_day == 0) {
        $sunday = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y")));
    } else {
        $i = 1;
        do {
            $makedate     = mktime(23, 59, 59, date("m"), date("d") + $i, date("Y"));
            $getdayofweek = date('w', $makedate);

            if ($getdayofweek == 0) {
                $sunday = date("Y-m-d H:i:s", $makedate);
                break;
            }
            $i++;
        } while ($i < 10);
    }

    return $sunday;
}

// Säkerställ att några centrala variabler finns
if (!isset($campaign_time)) { $campaign_time = ""; }
if (!isset($addfrom))       { $addfrom       = ""; }
if (!isset($addprice))      { $addprice      = ""; }

if ($is_hcampaign) {
    if ($campaign_time == "") {
        $campaign_time = getFridayTime();
    }
    if ($addfrom == "") {
        $addfrom = getSundayTime();
    }
}

$blogg    = new CBlogg();
$product  = new CProduct();
$adintern = new CWebADIntern();

// Plocka in parametrar om de kommer via GET
if (!isset($artnr) && isset($_GET['artnr'])) {
    $artnr = $_GET['artnr'];
}
if (!isset($m_product_id) && isset($_GET['m_product_id'])) {
    $m_product_id = $_GET['m_product_id'];
}
if (!isset($edit) && isset($_GET['edit'])) {
    $edit = $_GET['edit'];
}
if (!isset($ID) && isset($_GET['ID'])) {
    $ID = $_GET['ID'];
}
if (!isset($force_lang) && isset($_GET['force_lang'])) {
    $force_lang = $_GET['force_lang'];
}

if (!empty($artnr)) {
    if (!$product->verifyMProductID($artnr, $m_product_id)) {
        echo "Förbjuden åtgärd....";
        exit;
    }
}

/**
 * EDIT-LÄGE  hämta befintlig uppdatering
 */
if ($edit == "yes") {
    $rows = $product->getProductUpdateInfo($ID);

    if (!$submC) {
        $addfrom = $rows->updatetime;
    }
    $m_product_update_id = $rows->m_product_update_id;
    $add_country_id      = $rows->m_pricelist_id;

    if (!$submC) {
        // Historik: olika prislistor, nya uppdrag är alltid SE (1)
        if ($add_country_id == 1000280) {
            $add_country = 3;
        } elseif ($add_country_id == 1000018) {
            $add_country = 2;
        } else {
            $add_country = 1;
        }

        if ($rows->isupdtpricelist == "Y") {
            $check_addprice = "yes";
            $addprice       = $rows->pricelist;
            // Sverige: 25 % moms
            $addprice = round($addprice * 1.25, 2);
        }

        if ($rows->isupdtname == "Y") {
            $check_name = "yes";
            $addname    = $rows->name;
        }
        if ($rows->isupdtdescription == "Y") {
            $check_comment = "yes";
            $addcomment    = $rows->description;
        }
        if ($rows->isupdtselfservice == "Y") {
            $check_showweb = "yes";
            $showweb       = ($rows->isselfservice == "Y") ? "yes" : "";
        }
        if ($rows->isupdtexclautopricing == "Y") {
            $check_priceshape = "yes";
            $priceshape       = ($rows->isexclautopricing == "Y") ? "yes" : "";
        }
        if ($rows->isupdtdiscontinued == "Y") {
            $check_utgangen = "yes";
            $utgangen       = ($rows->discontinued == "Y") ? "yes" : "";
        }
        if ($rows->isTradeIn == -1) {
            $check_addprice = "";
        }
    }
}

/**
 * Hämta produktdata
 */
$rows = $product->getArticleInfo($artnr);
if ($rows->isTradeIn == -1) {
    $checktradein = "yes";
}

/*
if (isset($_COOKIE['login_mail']) &&
   ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || $_COOKIE['login_mail'] == 'borje@cyberphoto.nu')) {
    echo "Tillverkare: " . $rows->tillverkar_id . "<br>";
    echo "UPC/EAN: " . $rows->upc . "<br>";
}
*/

$tillverkare = $rows->tillverkare;
$bild        = $rows->bild;
$beskrivning = str_replace('`', '"', $rows->beskrivning);

if (!$subm && !$submC) {
    // Nettopriser och utpriser Sverige
    $netto_se         = $rows->art_id;
    $netto_moms       = $netto_se + $netto_se * $rows->momssats;

    $utpris_se        = $rows->utpris;
    $utpris_moms_se   = $rows->utpris + $rows->utpris * $rows->momssats;

    $marginal_se      = $rows->utpris != 0 ? (($rows->utpris - $rows->art_id) / $rows->utpris) * 100 : 0;
    $marginal_tb_se   = $utpris_se - $netto_se;
    $marginal_tb_moms_se = $marginal_tb_se + ($marginal_tb_se * $rows->momssats);

    // Förvalt prislista: alltid Sverige
    if (!isset($add_country) || $add_country == "") {
        $add_country = 1;
    }

    if ($addfrom == "") {
        $addfrom = date("Y-m-d H:i:s");
    }

    if ($addprice == "") {
        $addprice = round($utpris_moms_se, 0);
    }

    if ($addname == "") {
        $addname = $beskrivning;
    }
    if ($addcomment == "") {
        $addcomment = $rows->kommentar;
    }
}

/**
 * Autotext för helgkampanj
 */
if ($is_hcampaign && $extra_info == "" && !$subm) {
    if (!empty($_COOKIE['weekendTextSE'])) {
        $extra_info  = $_COOKIE['weekendTextSE'];
        $extra_info .= " - Ordinarie pris ";
        $extra_info .= $addprice . "kr";
    } else {
        $extra_info  = "Kampanj! Endast i helgen - Ordinarie pris ";
        $extra_info .= $addprice . "kr";
    }
}

/**
 * NYTT UPPDRAG
 */
if ($subm) {

    $olright = true;

	if ($check_showweb == "yes" && $showweb == "yes") {

		$saleStart = $product->getSaleStartDate($m_product_id);
		if ($saleStart) {
			$saleTs = strtotime($saleStart);

			if ($addfrom != "" && strtotime($addfrom) < $saleTs) {
				$olright = false;
				$wrongmess .= '<p class="wrongmess">- Spärr: Produkten kan inte publiceras innan säljstart. Säljstart: '.$saleStart.'</p>';
			}

			if ($is_hcampaign && $campaign_time != "" && strtotime($campaign_time) < $saleTs) {
				$olright = false;
				$wrongmess .= '<p class="wrongmess">- Spärr: Kampanjstart ligger före säljstart. Säljstart: '.$saleStart.'</p>';
			}
		}
	}

    if ($addprice <> $init_price) {
        $check_addprice = "yes";
        $diffprice      = (float)$addprice - (float)$init_price;
    }

    if ($addfrom != "") {
        if (!$blogg->isValidDateTime($addfrom)) {
            $olright    = false;
            $wrongmess .= "<p class=\"wrongmess\">- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
        }
    }

    if ($check_addprice != "yes" &&
        $check_showweb != "yes" &&
        $check_utgangen != "yes" &&
        $check_name != "yes" &&
        $check_comment != "yes" &&
        $check_priceshape != "yes") {

        $olright    = false;
        $wrongmess .= "<p class=\"wrongmess\">- Du måste välja någon händelse. Sorry ;)</p>";
    }

    if ($check_addprice == "yes" && $rows->isTradeIn != -1) {
        if ($addprice == 0) {
            $olright    = false;
            $wrongmess .= "<p class=\"wrongmess\">- Det är INTE tillåtet att sätta 0 (noll) kronor via det automatiska systemet. Gå in på produkten i affärssystemet.</p>";
        }

        if ($addprice > 0) {
            // Sverige  25 % moms
            $addprice_VAT = round($addprice / 1.25, 2);
        }
    }

    if ($is_hcampaign) {

        if ($if_extra_info != "yes") {
            unset($extra_info);
        }

        if ($addprice_campaign <> $init_price) {
            $check_addprice_campaign = "yes";
            $diffprice_campaign      = (float)$addprice_campaign - (float)$init_price;
        }

        if ($addprice_campaign == "" || $addprice_campaign == 0) {
            $olright    = false;
            $wrongmess .= "<p class=\"wrongmess\">- Kampanjpriset måste vara satt samt mer än 0kr.</p>";
        }

        if ($if_extra_info == "yes" && $extra_info != "") {
            $campaign_comment = true;
            $check_dependdate = "yes";
        } else {
            $campaign_comment = false;
        }

        if ($addprice_campaign > 0) {
            $addprice_campaign_VAT = round($addprice_campaign / 1.25, 2);
        }
    }

    if ($olright) {

        $addcreatedby = isset($_COOKIE['login_mail']) ? $_COOKIE['login_mail'] : '';

        // Helgkampanj  kampanjpris
        if ($is_hcampaign) {
            $product->makeProductUpdate(
                $m_product_id,
                $campaign_time,
                $add_country,
                $check_addprice,
                $addprice_campaign_VAT,
                $check_showweb,
                $showweb,
                $check_utgangen,
                $utgangen,
                $check_name,
                $addname,
                $check_comment,
                $addcomment
            );

            if ($campaign_comment) {
                $product->addExtrainfo(
                    $artnr,
                    $add_country,
                    $addactive,
                    $campaign_time,
                    $addfrom,
                    $extra_info,
                    $addhtml,
                    $check_dependdate,
                    $addascampaign,
                    $addcreatedby,
                    $internal_comment,
                    $campaign_comment
                );
            }
        }

        // Standarduppdatering
        if (!$product->makeProductUpdate(
            $m_product_id,
            $addfrom,
            $add_country,
            $check_addprice,
            $addprice_VAT,
            $check_showweb,
            $showweb,
            $check_utgangen,
            $utgangen,
            $check_name,
            $addname,
            $check_comment,
            $addcomment,
            $check_priceshape,
            $priceshape
        )) {
            $olright = false;
            $wrongmess .= "<p class=\"wrongmess\">- Denna handling gick ej att utföra... Sorry ;)</p>";
        }

        // Värdepaket (PAC)
        if ($check_addprice_pac == "yes" && $check_addprice == "yes") {

            if ($diffprice > 0) {
                $pac_init_price = $pac_init_price + $diffprice;
            } else {
                $pac_init_price = $pac_init_price - abs($diffprice);
            }

            $pac_addprice_VAT = round($pac_init_price / 1.25, 2);

            if ($is_hcampaign) {

                if ($diffprice_campaign > 0) {
                    $pac_init_price_campaign = $pac_init_price + $diffprice_campaign;
                } else {
                    $pac_init_price_campaign = $pac_init_price - abs($diffprice_campaign);
                }

                $pac_addprice_campaign_VAT = round($pac_init_price_campaign / 1.25, 2);

                $product->makeProductUpdate(
                    $pac_m_product_id,
                    $campaign_time,
                    $add_country,
                    $check_addprice,
                    $pac_addprice_campaign_VAT,
                    $check_showweb,
                    $showweb,
                    $check_utgangen,
                    $utgangen,
                    $check_name,
                    $addname,
                    $check_comment,
                    $addcomment
                );
            }

            $product->makeProductUpdate(
                $pac_m_product_id,
                $addfrom,
                $add_country,
                $check_addprice,
                $pac_addprice_VAT,
                $check_showweb,
                $showweb,
                $check_utgangen,
                $utgangen,
                $check_name,
                $addname,
                $check_comment,
                $addcomment
            );
        }

        $uppdate_ok = true;
    }
}

/**
 * ÄNDRA BEFINTLIGT UPPDRAG (edit-läge)
 */
if ($submC) {

    $olright = true;

	if ($check_showweb == "yes" && $showweb == "yes") {

		$scheduled = ($run_now == "yes") ? date("Y-m-d H:i:s") : $addfrom;

		$saleStart = $product->getSaleStartDate($m_product_id);
		if ($saleStart) {
			if ($scheduled != "" && strtotime($scheduled) < strtotime($saleStart)) {
				$olright = false;
				$wrongmess .= '<p class="wrongmess">- Spärr: Produkten kan inte publiceras innan säljstart. Säljstart: '.$saleStart.'</p>';
			}
		}
	}

    if ($addprice <> $init_price && $checktradein != "yes") {
        $check_addprice = "yes";
    }

    if ($run_now == "yes") {
        $addfrom = date("Y-m-d H:i:s");
    } else {
        if ($addfrom != "") {
            if (!$blogg->isValidDateTime($addfrom)) {
                $olright    = false;
                $wrongmess .= "<p class=\"wrongmess\">- Ogiltigt från datum. Skall formateras så här, 2009-01-01 15:00:00</p>";
            }
        }
    }

    if ($check_addprice == "yes") {
        if ($addprice == 0) {
            $olright    = false;
            $wrongmess .= "<p class=\"wrongmess\">- Det är INTE tillåtet att sätta 0 (noll) kronor via det automatiska systemet. Gå in på produkten i affärssystemet.</p>";
        }

        if ($addprice > 0) {
            $addprice_VAT = round($addprice / 1.25, 2);
        }
    }

    if ($olright) {
        if ($product->changeProductUpdate(
            $m_product_update_id,
            $m_product_id,
            $addfrom,
            $add_country,
            $check_addprice,
            $addprice_VAT,
            $check_showweb,
            $showweb,
            $check_utgangen,
            $utgangen,
            $check_name,
            $addname,
            $check_comment,
            $addcomment,
            $check_priceshape,
            $priceshape
        )) {
            $uppdate_ok = true;
        } else {
            $wrongmess .= "<p class=\"wrongmess\">- Denna handling gick ej att utföra... Sorry ;)</p>";
        }
    }
}

// Kolla om värdepaket finns (Sverige)
if ($edit != "yes" && !$subm && !$submC) {
    if ($product->getIfArticleExist($artnr . "pac")) {
        $pac_exist        = true;
        $pac_artnr        = $artnr . "pac";
        $pac_m_product_id = $product->getMProductID($artnr . "pac");
        $rows_pac         = $product->getArticleInfo($pac_artnr);

        $pac_addprice      = $rows_pac->utpris;
        $pac_addprice_moms = $rows_pac->utpris + $rows_pac->utpris * $rows->momssats;
        $pac_addprice      = round($pac_addprice_moms, 0);
    }
}

// Marginalfärg
$fontcolor_se = ($marginal_tb_se < 0) ? "tbtgcolorred" : "tbtgcolorgreen";

/**
 * Kalkylator  Sverige
 * Tre fält: nettpris, marginal %, utpris inkl. moms
 */
if (!isset($calc))       $calc       = false;
if (!isset($calcnetto))  $calcnetto  = "";
if (!isset($calcmargin)) $calcmargin = "";
if (!isset($calcprice))  $calcprice  = "";

$calc_error = "";

if ($calc) {
    // Normalisera decimaler (komma ? punkt)
    $calcnetto_raw  = str_replace(',', '.', $calcnetto);
    $calcmargin_raw = str_replace(',', '.', $calcmargin);
    $calcprice_raw  = str_replace(',', '.', $calcprice);

    $haveNetto  = ($calcnetto_raw  !== "" && is_numeric($calcnetto_raw));
    $haveMargin = ($calcmargin_raw !== "" && is_numeric($calcmargin_raw));
    $havePrice  = ($calcprice_raw  !== "" && is_numeric($calcprice_raw));

    $filled = ($haveNetto ? 1 : 0) + ($haveMargin ? 1 : 0) + ($havePrice ? 1 : 0);

    if ($filled < 2) {
        $calc_error = "Fyll i minst två fält för att kunna räkna.";
    } elseif ($filled > 2) {
        $calc_error = "Minst ett fält måste vara tomt för att kunna räkna ut något ;)";
    } else {
        // Omvandling till float
        $netto   = $haveNetto  ? (float)$calcnetto_raw  : null;
        $margin  = $haveMargin ? (float)$calcmargin_raw : null; // %
        $price   = $havePrice  ? (float)$calcprice_raw  : null; // inkl moms
        $moms    = 0.25;

        if ($haveNetto && $haveMargin && !$havePrice) {
            // Netto + marginal -> pris
            $margin_dec   = $margin / 100.0;
            $price_ex_moms = $netto / (1 - $margin_dec);
            $price        = round($price_ex_moms * (1 + $moms), 0);
        } elseif ($haveNetto && !$haveMargin && $havePrice) {
            // Netto + pris -> marginal
            $price_ex_moms = $price / (1 + $moms);
            if ($price_ex_moms > 0) {
                $margin = round((($price_ex_moms - $netto) / $price_ex_moms) * 100, 2);
            } else {
                $margin = 0;
            }
        } elseif (!$haveNetto && $haveMargin && $havePrice) {
            // Marginal + pris -> netto
            $margin_dec    = $margin / 100.0;
            $price_ex_moms = $price / (1 + $moms);
            $netto         = $price_ex_moms * (1 - $margin_dec);
        }

        // Skriv tillbaka värden i samma format
        if ($netto !== null)  { $calcnetto  = number_format($netto, 2, ',', ''); }
        if ($margin !== null) { $calcmargin = number_format($margin, 2, ',', ''); }
        if ($price !== null)  { $calcprice  = number_format($price, 0, ',', ''); }
    }
} else {
    // Förvalda värden första gången sidan laddas
    if ($calcnetto === "")  $calcnetto  = number_format($netto_se, 2, ',', '');
    if ($calcmargin === "") $calcmargin = number_format($marginal_se, 2, ',', '');
    if ($calcprice === "")  $calcprice  = number_format($utpris_moms_se, 0, ',', '');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>CyberPhoto - Uppdatera <?php echo $tillverkare . " " . $beskrivning; ?></title>
    <style>
        body {
            background-image: url(/order/logo.png);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: top right;
            background-color: #f4f5f7;
            font-family: Verdana, Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }
        h1 {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #111827;
            margin: 0 0 10px 0;
        }
        td {
            font-family: Verdana, Arial, Helvetica;
            font-size: 11px
        }
        .page-wrap {
            max-width: 960px;
            margin: 0 auto;
        }
        .card {
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(15,23,42,0.15);
            padding: 16px 20px;
            margin-bottom: 16px;
        }
        .card-header {
            font-weight: bold;
            margin-bottom: 8px;
            color: #111827;
        }
        .card-subheader {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 4px;
        }
        .textbox_green {
            padding: 3px 6px;
            background: #E6FFE6;
            border: 1px solid #9AE6B4;
            border-radius: 4px;
        }
        .textbox_white {
            padding: 3px 6px;
            background: #ffffff;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
        }
        .tbtgcolorgreen { color:#16a34a; font-weight:bold }
        .tbtgcolorred   { color:#dc2626; font-weight:bold }
        .wrongmess      { color:#dc2626; font-weight:bold }
        .hr_grey {
            border: 0;
            background-color: #e5e7eb;
            height: 1px;
            width: 100%;
            margin: 12px 0;
        }
        input:disabled {
            background:#f3f4f6;
        }
        .btn-primary {
            background: #2563eb;
            border: 1px solid #1d4ed8;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 14px;
            font-size: 11px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary[disabled] {
            opacity: .6;
            cursor: default;
        }
        .floatright   { float: right; }
        .top20        { margin-top: 20px; }
        .tcenter      { text-align: center; }
        .meta-row {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .flag-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background:#eff6ff;
            color:#1d4ed8;
            font-size:10px;
            font-weight:600;
        }
        .calc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 8px;
            align-items: center;
        }
        .calc-grid label {
            font-size: 10px;
            text-transform: uppercase;
            color:#6b7280;
            letter-spacing: .05em;
        }
        .calc-error {
            color:#dc2626;
            font-size: 11px;
            margin-top: 6px;
        }
        .small-muted {
            font-size: 10px;
            color:#6b7280;
        }
        .product-image img {
            max-height: 140px;
            border-radius: 4px;
        }
    </style>
</head>

<?php if ($uppdate_ok) { ?>
    <body onload="javascript:window.open('','_self').close();">
<?php } else { ?>
    <?php if ($is_hcampaign) { ?>
        <body onload="document.update_form.addprice_campaign.focus();document.update_form.addprice_campaign.select();">
    <?php } else { ?>
        <body onload="document.update_form.addfrom.focus();document.update_form.addfrom.select();">
    <?php } ?>
<?php } ?>

<div class="page-wrap">

<?php if ($bild != "") { ?>
    <div class="product-image floatright top20">
        <img src="<?php echo "/thumbs/large/bilder/".$bild; ?>" alt="">
    </div>
<?php } ?>

<div class="card">
    <div class="card-subheader">Produktuppdatering</div>
    <?php if (!empty($m_product_update_id)) { ?>
        <h1>Ändra uppdateringen för <?php echo $tillverkare . " " . $beskrivning; ?></h1>
    <?php } else { ?>
        <h1>Uppdatera <?php echo $tillverkare . " " . $beskrivning; ?></h1>
    <?php } ?>
    <div class="meta-row">Artikelnr: <?php echo htmlspecialchars($artnr); ?></div>

    <?php
    if (!empty($wrongmess)) {
        echo $wrongmess;
    }
    $adintern->checkProductUpdate($artnr);
    ?>

    <?php if (!$uppdate_ok) { ?>
    <form name="update_form" method="post">
    <?php if (!empty($m_product_update_id)) { ?>
        <input type="hidden" value="true" name="submC">
        <input type="hidden" value="<?php echo $m_product_update_id; ?>" name="m_product_update_id">
        <input type="hidden" value="<?php echo $checktradein; ?>" name="checktradein">
    <?php } else { ?>
        <input type="hidden" value="true" name="subm">
    <?php } ?>
        <input type="hidden" value="<?php echo $m_product_id; ?>" name="m_product_id">
        <input type="hidden" value="<?php echo $artnr; ?>" name="artnr">
        <input type="hidden" value="<?php echo $force_lang; ?>" name="force_lang">
        <input type="hidden" value="<?php echo $addprice; ?>" name="init_price">
    <?php if ($is_hcampaign) { ?>
        <input type="hidden" value="yes" name="hcampaign">
    <?php } ?>
    <?php if ($pac_exist) { ?>
        <input type="hidden" value="<?php echo $pac_m_product_id; ?>" name="pac_m_product_id">
        <input type="hidden" value="<?php echo $pac_artnr; ?>" name="pac_artnr">
        <input type="hidden" value="<?php echo $pac_addprice; ?>" name="pac_init_price">
    <?php } ?>

        <table border="0" cellpadding="4" cellspacing="0" width="100%">
          <?php if ($is_hcampaign) { ?>
              <tr>
                <td colspan="3">
                    <input onclick="this.select()" class="textbox_green" type="text" name="campaign_time" size="19" value="<?php echo $campaign_time; ?>">
                    &nbsp;Tid för uppdatering
                </td>
              </tr>
              <tr>
                <td colspan="3">
                    <input onclick="this.select()" class="textbox_green" type="text" name="addfrom" size="19" value="<?php echo $addfrom; ?>">
                    &nbsp;Tid för återgång
                </td>
              </tr>
          <?php } else { ?>
              <tr>
                <td colspan="3">
                    <input onclick="this.select()" class="textbox_green" type="text" name="addfrom" size="19" value="<?php echo $addfrom; ?>">
                    &nbsp;Tid för uppdatering
                </td>
              </tr>
          <?php } ?>

          <?php if ($edit == "yes") { ?>
              <tr>
                <td colspan="3">
                    <label>
                        <input type="checkbox" value="yes"<?php if ($run_now == "yes") { echo " checked"; } ?> name="run_now">
                        &nbsp;Kör denna nu direkt
                    </label>
                </td>
              </tr>
          <?php } ?>

          <tr><td colspan="3"><hr class="hr_grey"></td></tr>

          <!-- Prislista: Sverige -->
          <tr>
            <td colspan="3">
                <span class="flag-label">
                    <img border="0" src="sv_mini.jpg" alt="">
                    <span>Prislista: Sverige</span>
                    <span class="pill">Standard</span>
                </span>
                <input type="hidden" name="add_country" value="1">
            </td>
          </tr>

          <?php if ($rows->isTradeIn == -1) { ?>
              <tr><td colspan="3"><hr class="hr_grey"></td></tr>
              <tr>
                <td colspan="3">
                    <span class="bold">*****</span>
                    <span class="italic bold wrongmess">Detta är en inbytesprodukt och prissättning måste göras i Adempiere</span>
                    <span class="bold">*****</span>
                </td>
              </tr>
          <?php } else { ?>
              <?php if ($is_hcampaign) { ?>
                  <tr>
                    <td colspan="2">
                        <label>
                            <input type="checkbox" name="check_addprice_campaign" value="yes" checked>
                            Helgens pris --------------------->
                        </label>
                    </td>
                    <td>
                        <input onclick="this.select()" class="textbox_white" type="text" name="addprice_campaign" size="7" value="<?php echo $addprice_campaign; ?>">
                        &nbsp;(inkl. moms)
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                        <label>
                            <input type="checkbox" name="check_addprice" value="yes" checked>
                            Vanligt pris --------------------->
                        </label>
                    </td>
                    <td>
                        <input onclick="this.select()" class="textbox_white" type="text" name="addprice" size="7" value="<?php echo $addprice; ?>">
                        &nbsp;(inkl. moms)
                    </td>
                  </tr>
              <?php } else { ?>
                  <tr>
                    <td colspan="2">
                        <label>
                            <input type="checkbox" name="check_addprice" value="yes" <?php if ($check_addprice == "yes") { ?>checked<?php } ?>>
                            Uppdatera pris --------------------->
                        </label>
                    </td>
                    <td>
                        <input onclick="this.select()" class="textbox_white" type="text" name="addprice" size="7" value="<?php echo $addprice; ?>">
                        &nbsp;(inkl. moms)
                    </td>
                  </tr>
				  <tr>
					<td colspan="2">
						<label>
							<input type="checkbox" name="check_priceshape" value="yes" <?php if ($check_priceshape == "yes") { ?>checked<?php } ?>>
							Uteslut från PriceShape ----------->
						</label>
					</td>
					<td>
						<label>
							<input type="checkbox" name="priceshape" value="yes" <?php if ($priceshape == "yes") { ?>checked<?php } ?>>
							Uteslut
						</label>
					</td>
				  </tr>
              <?php } ?>
          <?php } ?>

          <?php if ($pac_exist) { ?>
          <tr>
            <td colspan="3">
                <label>
                    <input type="checkbox" name="check_addprice_pac" value="yes" <?php if (($check_addprice_pac == "yes" || $check_addprice_pac == "") && $pac_addprice > 1) { ?>checked<?php } ?>>
                    Ja, uppdatera även värdepaketet (ändras med motsvarande bruttoändring)
                </label>
            </td>
          </tr>
          <?php } ?>

          <tr><td colspan="3"><hr class="hr_grey"></td></tr>

          <?php if ($is_hcampaign) { ?>
              <tr>
                <td colspan="2">
                    <label>
                        <input type="checkbox" name="if_extra_info" value="yes" <?php if ($if_extra_info == "yes" || $if_extra_info == "") { ?>checked<?php } ?>>
                        Lägg till extra info ---------->
                    </label>
                </td>
                <td><input class="textbox_white" type="text" name="extra_info" size="50" value="<?php echo $extra_info; ?>"></td>
              </tr>
          <?php } else { ?>
              <tr>
                <td colspan="2">
                    <label>
                        <input type="checkbox" name="check_showweb" value="yes" <?php if ($check_showweb == "yes") { ?>checked<?php } ?>>
                        Uppdatera visas på webben ------->
                    </label>
                </td>
                <td>
                    <label>
                        <input type="checkbox" name="showweb" value="yes" <?php if ($showweb == "yes") { ?>checked<?php } ?>>
                        Visas på webben
                    </label>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                    <label>
                        <input type="checkbox" name="check_utgangen" value="yes" <?php if ($check_utgangen == "yes") { ?>checked<?php } ?>>
                        Uppdatera utgången --------------->
                    </label>
                </td>
                <td>
                    <label>
                        <input type="checkbox" name="utgangen" value="yes" <?php if ($utgangen == "yes") { ?>checked<?php } ?>>
                        Sätt utgången
                    </label>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                    <label>
                        <input type="checkbox" name="check_name" value="yes" <?php if ($check_name == "yes") { ?>checked<?php } ?>>
                        Uppdatera namnet ----------------->
                    </label>
                </td>
                <td><input class="textbox_white" type="text" name="addname" size="50" value="<?php echo $addname; ?>"></td>
              </tr>
              <tr>
                <td colspan="2">
                    <label>
                        <input type="checkbox" name="check_comment" value="yes" <?php if ($check_comment == "yes") { ?>checked<?php } ?>>
                        Uppdatera kommentaren ---------->
                    </label>
                </td>
                <td><input class="textbox_white" type="text" name="addcomment" size="50" value="<?php echo $addcomment; ?>"></td>
              </tr>
          <?php } ?>
        </table>

        <div style="margin-top:12px;">
            <input type="submit" class="btn-primary" value="Spara" onclick="this.disabled=true;this.value='Behandlas...'; this.form.submit();">
        </div>
    </form>
    <?php } ?>
</div>

<?php if (empty($m_product_update_id)) { ?>
<div class="card">
    <div class="card-header">Pris & marginal (Sverige)</div>
    <table border="0" cellpadding="4" cellspacing="0">
      <tr>
      	<td colspan="3"><b>Nettopriser</b></td>
        <td width="40">&nbsp;</td>
        <td colspan="2"><b>Utpriser</b></td>
      	<td width="40">&nbsp;</td>
        <td><b>Marginal TG</b></td>
      	<td width="40">&nbsp;</td>
      	<td colspan="2"><b>Marginal TB</b></td>
      </tr>
      <tr>
        <td><img border="0" src="sv_mini.jpg" alt="">&nbsp;</td>
        <td align="right"><?php echo number_format($netto_se, 2, ',', ' '); ?></td>
        <td align="right">&nbsp;(<?php echo number_format(round($netto_moms), 0, ',', ' '); ?>)&nbsp; SEK</td>
        <td align="right">&nbsp;</td>
        <td align="right"><?php echo number_format(round($utpris_se), 0, ',', ' '); ?></td>
        <td align="right">&nbsp;(<?php echo number_format(round($utpris_moms_se), 0, ',', ' '); ?>)&nbsp; SEK</td>
        <td align="right">&nbsp;</td>
        <td align="right" class="<?php echo $fontcolor_se; ?>"><?php echo number_format($marginal_se, 2, ',', '.'); ?>&nbsp;%</td>
        <td align="right">&nbsp;</td>
        <td align="right" class="<?php echo $fontcolor_se; ?>"><?php echo number_format($marginal_tb_se, 2, ',', ' '); ?></td>
        <td align="right" class="<?php echo $fontcolor_se; ?>">&nbsp;(<?php echo number_format(round($marginal_tb_moms_se), 0, ',', ' '); ?>)&nbsp; SEK</td>
      </tr>
      <tr>
        <td colspan="11" class="small-muted">Priser inom parentes anger inkl. moms.</td>
      </tr>
    </table>
</div>

<div class="card">
    <div class="card-header">Priskalkylator (Sverige)</div>
    <form>
        <input type="hidden" name="calc" value="true">
        <input type="hidden" name="m_product_id" value="<?php echo $m_product_id; ?>">
        <input type="hidden" name="artnr" value="<?php echo $artnr; ?>">
        <input type="hidden" name="force_lang" value="<?php echo $force_lang; ?>">

        <div class="calc-grid">
            <div>
                <label for="calcnetto">Nettopris</label><br>
                <input class="textbox_white tcenter" type="text" id="calcnetto" name="calcnetto" size="7" value="<?php echo $calcnetto; ?>">
            </div>
            <div>
                <label for="calcmargin">Marginal (%)</label><br>
                <input class="textbox_white tcenter" type="text" id="calcmargin" name="calcmargin" size="7" value="<?php echo $calcmargin; ?>">
            </div>
            <div>
                <label for="calcprice">Utpris inkl. moms</label><br>
                <input class="textbox_white tcenter" type="text" id="calcprice" name="calcprice" size="7" value="<?php echo $calcprice; ?>">
            </div>
            <div style="margin-top:14px;">
                <input type="submit" class="btn-primary" value="Räkna" onclick="this.disabled=true;this.value='Behandlas...'; this.form.submit();">
            </div>
        </div>
        <?php if ($calc_error != "") { ?>
            <div class="calc-error"><?php echo $calc_error; ?></div>
        <?php } else { ?>
            <div class="small-muted" style="margin-top:6px;">
                Fyll i två fält (t.ex. nettpris + marginal eller nettpris + utpris) så räknas det tredje ut automatiskt.
            </div>
        <?php } ?>
    </form>
</div>
<?php } ?>

<div class="card">
    <div class="card-header">Automatiska prisjusteringar</div>
    <?php
        // Visar befintliga uppdrag för denna artikel
        $adintern->checkProductUpdate($artnr, true);
    ?>
</div>

<?php if ($uppdate_ok) { ?>
<div class="card">
    <h1>Uppdraget sparat!</h1>
    <p>&nbsp;</p>
    <a href="javascript:window.open('','_self').close();" class="btn-primary">Stäng detta fönster</a>
</div>
<?php } ?>

</div> <!-- /page-wrap -->
</body>
</html>
