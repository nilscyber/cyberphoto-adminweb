<?php

?>
<div id="menypanel">

<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.seX') { ?>
<!-- Snabbsök ovanför menyn -->
<div class="quick-search">
  <form action="/search_dispatch.php" method="get">
    <input type="hidden" name="type" value="product">
    <input id="searchProduct" type="text" name="q" placeholder="Sök produkt" autocomplete="off">
  </form>
  <?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') { ?>
  <form action="/search_dispatch.php" method="get">
    <input type="hidden" name="type" value="customer">
    <input id="searchCustomer" type="text" name="q" placeholder="Sök kund" autocomplete="off">
  </form>
  <form action="/search_dispatch.php" method="get">
    <input type="hidden" name="type" value="order">
    <input id="searchOrder" type="text" name="q" placeholder="Sök order" autocomplete="off">
  </form>
  <?php } ?>
</div>
<?php } ?>
<!--
<div class="bottom5"><a class="bold uppercase span_red" href="https://www2.cyberphoto.se/fel" target="_blank">FELRAPPORT LITIUM</a></div>
-->

<a href="javascript:ddtreemenu.flatten('treemenuadmin', 'expand')">Expandera</a> | <a href="javascript:ddtreemenu.flatten('treemenuadmin', 'contact')">Minimera</a>

<ul id="treemenuadmin" class="treeview">
<li>Administrera
	<ul>
    <li><a href="banners.php?choose_site=1&choose_department=1&choose_section=201" <?php if (preg_match("/banners\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>TV:s butiken</a></li>
    <li><a href="check_incoming.php" <?php if (preg_match("/check_incoming\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Filter inkommande</a></li>
    <li><a href="monitor_articles.php" <?php if (preg_match("/monitor_articles\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Bevaka artiklar</a></li>
    <?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') { ?>
	<?php } ?>
	</ul>
</li>

<?php if (CCheckIP::checkIfLoginIsTradeIn($_SERVER['REMOTE_ADDR'])) { ?>	
<li>Inbyte
	<ul>
    <li><a href="inbyte_booked_not_shipped.php" <?php if (preg_match("/inbyte_booked_not_shipped\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Uppbokat, EJ skickat</a></li>
	<li><a href="marination.php" <?php if (preg_match("/marination\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Marinering</a></li>
	<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || $_COOKIE['login_mail'] == 'borje@cyberphoto.seX') { ?>
	<?php } ?>
	<li><a href="readyforsale.php" <?php if (preg_match("/readyforsale\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Klart att sälja</a></li>
    <li><a href="inbyte_waiting.php" <?php if (preg_match("/inbyte_waiting\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Har hyllplats</a></li>
    <li><a href="inbyte_KOT.php" <?php if (preg_match("/inbyte_KOT\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>KOT</a></li>
    <li><a href="inbyte_lager.php" <?php if (preg_match("/inbyte_lager\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Lagerstatus</a></li>
    <li><a href="inbyte_dubbletter.php" <?php if (preg_match("/inbyte_dubbletter\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Dubbletter</a></li>
    <li><a href="inbyte_noprice.php" <?php if (preg_match("/inbyte_noprice\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>EJ prissatta</a></li>
    <li><a href="inbyte_sold.php" <?php if (preg_match("/inbyte_sold\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Senast sålda</a></li>
    <li><a href="inbyte_incomming.php" <?php if (preg_match("/inbyte_incomming\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Inkommande</a></li>
    <li><a href="standard_locator.php" <?php if (preg_match("/standard_locator\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Saknad lagerplats</a></li>
    <li><a href="used_products.php" <?php if (preg_match("/used_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Beggat > 90d</a></li>
    <li><a href="text_templates.php" <?php if (preg_match("/text_templates\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Textmallar</a></li>
	<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') { ?>
		<li><a href="inbyte_wishlist.php" <?php if (preg_match("/inbyte_wishlist\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Önskelista</a></li>
	<?php } ?>
	</ul>
</li>
<?php } ?>


<li>Butiken
	<ul>
    <li><a href="WarehouseOrders.php?delivered=no&only_shop=yes" <?php if (preg_match("/WarehouseOrders\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Ordrar butiken</a></li>
	<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || $_COOKIE['login_mail'] == 'albin@cyberphoto.se') { ?>
    <li><a href="butiken_turnover.php" <?php if (preg_match("/butiken_turnover\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Butiksdata</a></li>
	<?php } ?>
    <?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.seX') { ?>
		<li><a href="butiken_pling.php" <?php if (preg_match("/butiken_pling\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Pling butiken</a></li>
	<?php } ?>
	</ul>
</li>

<li>Produkter
	<ul>
    <li><a href="not_refined.php" <?php if (preg_match("/not_refined\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Ej förädlade</a></li>
	<li><a href="new_products.php" <?php if (preg_match("/new_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Nya produkter</a></li>
	<li><a href="discontinued_products.php" <?php if (preg_match("/discontinued_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Utgångna produkter</a></li>
    <li><a href="demo_products.php" <?php if (preg_match("/demo_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Fynd > 90d</a></li>
    <?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || $_COOKIE['login_mail'] == 'thomas@cyberphoto.se' || $_COOKIE['login_mail'] == 'johan.eriksson@cyberphoto.se' || $_COOKIE['login_mail'] == 'kenneth.ly@cyberphoto.se') { ?>
    <li><a href="categories.php" <?php if (preg_match("/categories\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Kategorier ADempiere</a></li>
	<?php } ?>
	<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') { ?>
	<?php } ?>
	</ul>
</li>

<li>Försäljning
	<ul>
    <li><a href="salesreport.php" <?php if (preg_match("/salesreport\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Försäljningsrapport</a></li>
	<li><a href="salesreport_salesman.php" <?php if (preg_match("/salesreport_salesman\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Rapport säljare</a></li>
	<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || $_COOKIE['login_mail'] == 'albin@cyberphoto.se' || $_COOKIE['login_mail'] == 'patrick@cyberphoto.se') { ?>
	<?php } ?>
    <li><a href="average_value.php" <?php if (preg_match("/average_value\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Antal / Snittvärden</a></li>
    <li><a href="receivedOrders.php" <?php if (preg_match("/receivedOrders\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Inkommande ordrar</a></li>
    <li><a href="incomingOrders.php" <?php if (preg_match("/incomingOrders\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Marginalstruktur</a></li>
    <li><a href="best_tg.php" <?php if (preg_match("/best_tg\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Bäst täckningsgrad</a></li>
    <li><a href="pay_methods.php" <?php if (preg_match("/pay_methods\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Fördelning betalsätt</a></li>
	<li><a href="product_feedback.php" <?php if (preg_match("/product_feedback\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Missad försäljning</a></li>
	<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') { ?>
	<li><a href="focus_products.php" <?php if (preg_match("/focus_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Våra fokusprodukter</a></li>
	<?php } ?>
	</ul>
</li>

<?php if ($_COOKIE['login_mail'] == 'markus@cyberphoto.nu') { ?>	
<li>Tekniska data
	<ul>
    <li><a href="Tekn_cameras.php" <?php if (preg_match("/Tekn_cameras\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Kompaktkameror</a></li>
    <li><a href="Tekn_dslr.php" <?php if (preg_match("/Tekn_dslr\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Systemkameror</a></li>
    <li><a href="Tekn_video.php" <?php if (preg_match("/Tekn_video\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Videokameror</a></li>
    <li><a href="Tekn_lenses.php" <?php if (preg_match("/Tekn_lenses\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Objektiv</a></li>
    <li><a href="Tekn_mobile.php" <?php if (preg_match("/Tekn_mobile\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Mobiltelefoner</a></li>
    <li><a href="Tekn_tablets.php" <?php if (preg_match("/Tekn_tablets\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Surfplattor</a></li>
    <li><a href="Tekn_printer.php" <?php if (preg_match("/Tekn_printer\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Skrivare</a></li>
	</ul>
</li>
<?php } ?>

<li>Statistik
	<ul>
    <li><a href="mestsalda.php" <?php if (preg_match("/mestsalda\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Mest sålda</a></li>
    <li><a href="mestsalda_kat.php" <?php if (preg_match("/mestsalda_kat\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Mest sålda kategorier</a></li>
    <li><a href="missing_products.php" <?php if (preg_match("/missing_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Saknade produkter</a></li>
	<li><a href="statistics_order.php" <?php if (preg_match("/statistics_order\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Produkter per order</a></li>
    <li><a href="office_products.php" <?php if (preg_match("/office_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Lager kontor</a></li>
    <li><a href="shop_products.php" <?php if (preg_match("/shop_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Lager butik</a></li>
    <li><a href="dropshipment.php" <?php if (preg_match("/dropshipment\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Dropshipment</a></li>
    <li><a href="rma_summary.php" <?php if (preg_match("/rma_summary\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>RMA ärenden</a></li>
    <li><a href="tickets.php" <?php if (preg_match("/tickets\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>OTRS</a></li>
	<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') { ?>
	<?php } ?>
	</ul>
</li>

<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || $_COOKIE['login_mail'] == 'patrick@cyberphoto.se' || $_COOKIE['login_mail'] == 'sebastian.pihl@cyberphoto.se') { ?>
<li>Intern statistik
	<ul>
	<li><a href="statistics_inkopsordrar.php" <?php if (preg_match("/statistics_inkopsordrar\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Inköpsordrar</a></li>
	<li><a href="statistics_saljordrar.php" <?php if (preg_match("/statistics_saljordrar\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Säljordrar</a></li>
	</ul>
</li>
<?php } ?>

<li>Lagervärden
	<ul>
    <li><a href="lagerstatus_grupperat.php" <?php if (preg_match("/lagerstatus_grupperat\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Grupperat</a></li>
    <li><a href="lagerstatus.php" <?php if (preg_match("/lagerstatus\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Kategori</a></li>
    <li><a href="supplier.php" <?php if (preg_match("/supplier\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Leverantör</a></li>
    <li><a href="manufacturer.php" <?php if (preg_match("/manufacturer\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Tillverkare</a></li>
    <li><a href="lagervarde.php" <?php if (preg_match("/lagervarde\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Aktuellt lagervärde</a></li>
    <li><a href="goods_expectation_value.php" <?php if (preg_match("/goods_expectation_value\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Inkommande värde</a></li>
	<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se' || $_COOKIE['login_mail'] == 'emil.lindberg@cyberphoto.se') { ?>
		<li><a href="gauge_dashboard_warehouse.php" <?php if (preg_match("/gauge_dashboard_warehouse\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Gauge dashboard</a></li>
	<?php } ?>
	</ul>
</li>

<li>Loggar
	<ul>
    <li><a href="allokerat.php" <?php if (preg_match("/allokerat\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Låsta produkter</a></li>
    <li><a href="not_priced.php" <?php if (preg_match("/not_priced\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Ej prissatta produkter</a></li>
    <li><a href="negative_sales.php" <?php if (preg_match("/negative_sales\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Negativ försäljning</a></li>
    <li><a href="notshow_products.php" <?php if (preg_match("/notshow_products\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>I lager, Ej på webb</a></li>
    <li><a href="cancel_purchase.php" <?php if (preg_match("/cancel_purchase\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Möjliga avbokningar</a></li>
    <li><a href="product_updates.php" <?php if (preg_match("/product_updates\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Produktuppdateringar</a></li>
    <li><a href="password_recovery.php" <?php if (preg_match("/password_recovery\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Återställning lösenord</a></li>
	</ul>
</li>

<li>Logistik
	<ul>
    <li><a href="logistik.php" <?php if (preg_match("/logistik\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Logistikflöden</a></li>
    <li><a href="goods_expectation.php" <?php if (preg_match("/goods_expectation\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Förväntad godsvolym</a></li>
    <li><a href="goods_delays.php" <?php if (preg_match("/goods_delays\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Försenade godsvolym</a></li>
	</ul>
</li>

<li>Övrigt
	<ul>
    <li><a href="supliers.php" <?php if (preg_match("/supliers\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Aktuella leverantörer</a></li>
    <li><a href="cache.php" <?php if (preg_match("/cache\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Status på cachning</a></li>
	</ul>
</li>

<?php if ($_COOKIE['login_mail'] == 'stefan@cyberphoto.se') { ?>
<li>Development
	<ul>
    <li><a href="random_employees.php" <?php if (preg_match("/random_employees\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Slumpgeneratorn</a></li>
    <li><a href="check_external.php" <?php if (preg_match("/check_external\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Intern/Extern</a></li>
    <li><a href="development.php" <?php if (preg_match("/development\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Uteckling / test</a></li>
    <li><a href="front.php" <?php if (preg_match("/front\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Frontsidan</a></li>
    <li><a href="poll.php" <?php if (preg_match("/poll\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Webbundersökning</a></li>
    <li><a href="rma.php" <?php if (preg_match("/rma\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Reparationer</a></li>
    <li><a href="doa.php" <?php if (preg_match("/doa\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>DOA</a></li>
    <li><a href="return.php" <?php if (preg_match("/return\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Retur öppet köp</a></li>
    <li><a href="klarna_on_site.php" <?php if (preg_match("/klarna_on_site\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Klarna On Site</a></li>
	<li><a href="cms.php" <?php if (preg_match("/cms\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>CMS</a></li>
	<li><a href="menu_web.php" <?php if (preg_match("/menu_web\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Meny webbshop</a></li>
    <li><a href="specialdeals.php" <?php if (preg_match("/specialdeals\.php/i", $_SERVER['PHP_SELF'])) { ?>id="current"<?php } ?>>Specialdeals</a></li>
	</ul>
</li>
<?php } ?>
</ul>

<script type="text/javascript">

ddtreemenu.createTree("treemenuadmin", true, 365)

</script>



</div>