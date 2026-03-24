<?php
require_once("CBlogg.php");
$blogg = new CBlogg();
?>
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Administrera</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/front\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="front.php">Frontsidan</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/banner\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="banner.php">Övriga banners</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/pricelist\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="pricelist.php">Prislistor</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/adtrigger\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="adtrigger.php">Annonser</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/blogg\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="blogg.php">Blogg <?php echo "(" . $blogg->getComments(0) . ")"; ?></a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/kommande_bloggar\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="kommande_bloggar.php">Kommande bloggar</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/abonnemang_mobil\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="abonnemang_mobil.php">Abonnemang - Mobil</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/abonnemang_data\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="abonnemang_data.php">Abonnemang - Data</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/kalkyl_abonnemang\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="kalkyl_abonnemang.php">Abonnemang - Kalkyl</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/departure\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="departure.php">Sista beställningstid</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/senaste_tester\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="senaste_tester.php">Senaste testerna</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/senaste_nytt\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="senaste_nytt.php">Senaste nyheterna</a></font></td>
      </tr>
	  <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/senaste_sysinfo\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="senaste_sysinfo.php">Senaste system-info</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/blacklist\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="blacklist.php">Svartlistade IP</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/webordercheck\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="webordercheck.php">Produkt bevakning</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/monitor_articles\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="monitor_articles.php">Lagersaldo nivåer</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Försäljning</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/salesreport\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="salesreport.php">Försäljningsrapport</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Statistik</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/mestsalda\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="mestsalda.php">Mest sålda</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/mestsalda_kat\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="mestsalda_kat.php">Mest sålda kategorier</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/missing_products\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="missing_products.php">Saknade produkter</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/office_products\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="office_products.php">Lager kontor</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/notshow_products\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="notshow_products.php">Ej visade produkter</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/searchwords\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="searchwords.php">Negativa sökord</a></font></td>
      </tr>
      <?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" || $_SERVER['REMOTE_ADDR'] == "192.168.1.95") { ?>
      <?php } ?>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Lagervärden</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/lagerstatus\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="lagerstatus.php">Kategori</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/supplier\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="supplier.php">Leverantör</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/lagervarde\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="lagervarde.php">Totalt lagervärde</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Felloggar</font></b></td>
      </tr>
	  <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/fellogg_systemkameror\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="fellogg_systemkameror.php">Systemkameror</a></font></td>
      </tr>
	  <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/fellogg_digitalkameror\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="fellogg_digitalkameror.php">Kompaktkameror</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/fellogg_video\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="fellogg_video.php">Videokameror</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/fellogg_objektiv\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="fellogg_objektiv.php">Objektiv</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/fellogg_mobil\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="fellogg_mobil.php">Mobiltelefoner</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/bad_parcel\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="bad_parcel.php">Negativ paketrabatt</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/allokerat\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="allokerat.php">Låsta produkter</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Övrigt</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/check_external\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="check_external.php">Intern/Extern</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/categories\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="categories.php">CyberPhoto kategorier</a></font></td>
      </tr>
      <?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") { ?>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Sjabo utveckling</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/poll\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="poll.php">Webbundersökning</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/ordinary_question\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="ordinary_question.php">Vanliga frågor</a></font></td>
      </tr>
	  <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/tomteverkstan2010\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="tomteverkstan2010.php">Tomteönskningar</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/promotioncode\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="promotioncode.php">Rabattkoder</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/delbetalningar\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="delbetalningar.php">Avbetalningar</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/incoming\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="incoming.php">Beställda varor</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/articles_in_stock\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="articles_in_stock.php">Surdegar</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/turnover\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="turnover.php">Aktuell omsättning</a></font></td>
      </tr>
	  <?php } ?>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><hr noshade color="#CCCCCC" size="1"></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a href="/order/admin/">Tillbaka till start</a></font></td>
      </tr>
    </table>
