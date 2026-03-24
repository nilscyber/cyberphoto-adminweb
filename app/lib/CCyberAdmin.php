<?php


Class CCyberAdmin {

	var $conn_my;

	function __construct() {

		$this->conn_my = Db::getConnection(true);

	}

	function displayPageTitle() {
		global $manual_pagetitle, $ordernr;
		
		if ($manual_pagetitle != "") {
			echo "<title>" . $manual_pagetitle . "</title>\n";
		} elseif (preg_match("/salesreport\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Försäljningsrapport - CyberPhoto</title>\n";
		} elseif (preg_match("/lagervarde\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Aktuellt lagervärde - CyberPhoto</title>\n";
		} elseif (preg_match("/check_external\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Intern/Extern - CyberPhoto</title>\n";
		} elseif (preg_match("/categories\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Kategorier - CyberPhoto</title>\n";
		} elseif (preg_match("/monitor_articles\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Bevaka artiklar - CyberPhoto</title>\n";
		} elseif (preg_match("/blogg\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Bloggkommentarer - CyberPhoto</title>\n";
		} elseif (preg_match("/office_products\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Lager kontor - CyberPhoto</title>\n";
		} elseif (preg_match("/mestsalda\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Mest sålda - CyberPhoto</title>\n";
		} elseif (preg_match("/mestsalda_kat\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Mest sålda kategorier - CyberPhoto</title>\n";
		} elseif (preg_match("/missing_products\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Saknade produkter - CyberPhoto</title>\n";
		} elseif (preg_match("/blacklist\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Svartlistade IP-adresser - CyberPhoto</title>\n";
		} elseif (preg_match("/bad_parcel\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Aktiva värdepaket med negativ paketrabatt - CyberPhoto</title>\n";
		} elseif (preg_match("/lagerstatus\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Aktuell lagerstatus - CyberPhoto</title>\n";
		} elseif (preg_match("/banners\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Hantera banners - CyberPhoto</title>\n";
		} elseif (preg_match("/supplier\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Leverantörsstatus - CyberPhoto</title>\n";
		} elseif (preg_match("/rma_summary\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>RMA ärenden - CyberPhoto</title>\n";
		} elseif (preg_match("/rma\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Reparationer - CyberPhoto</title>\n";
		} elseif (preg_match("/doa\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>DOA - CyberPhoto</title>\n";
		} elseif (preg_match("/return\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Retur öppet köp - CyberPhoto</title>\n";
		} elseif (preg_match("/allokerat\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Låsta produkter - CyberPhoto</title>\n";
		} elseif (preg_match("/negative_sales\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Negativ försäljning - CyberPhoto</title>\n";
		} elseif (preg_match("/kommande_bloggar\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Kommande bloggar - CyberPhoto</title>\n";
		} elseif (preg_match("/receivedOrders\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Inkommande ordrar - CyberPhoto</title>\n";
		} elseif (preg_match("/supliers\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Aktuella leverantörer - CyberPhoto</title>\n";
		} elseif (preg_match("/departure\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Sista beställningstid från oss - CyberPhoto</title>\n";
		} elseif (preg_match("/abonnemang_mobil\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Abonnemang mobil - CyberPhoto</title>\n";
		} elseif (preg_match("/abonnemang_data\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Abonnemang data - CyberPhoto</title>\n";
		} elseif (preg_match("/abonnemang_kalkyl\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Abonnemang kalkyl - CyberPhoto</title>\n";
		} elseif (preg_match("/senaste_nytt\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Senaste nyheterna - CyberPhoto</title>\n";
		} elseif (preg_match("/senaste_tester\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Senaste testerna - CyberPhoto</title>\n";
		} elseif (preg_match("/senaste_sysinfo\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Senaste systeminformationen - CyberPhoto</title>\n";
		} elseif (preg_match("/pricelist\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Prislistor - CyberPhoto</title>\n";
		} elseif (preg_match("/adtrigger\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Länkar för annonser - CyberPhoto</title>\n";
		} elseif (preg_match("/fellogg_mobil\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Fellogg mobiltelefoner - CyberPhoto</title>\n";
		} elseif (preg_match("/fellogg_systemkameror\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Fellogg systemkameror - CyberPhoto</title>\n";
		} elseif (preg_match("/fellogg_digitalkameror\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Fellogg digitalkameror - CyberPhoto</title>\n";
		} elseif (preg_match("/fellogg_video\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Fellogg video - CyberPhoto</title>\n";
		} elseif (preg_match("/fellogg_objektiv\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Fellogg objektiv - CyberPhoto</title>\n";
		} elseif (preg_match("/fellogg_surfplattor\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Fellogg surfplattor - CyberPhoto</title>\n";
		} elseif (preg_match("/fellogg_ej_med_finland\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Produkter som Ej visas i Finland - CyberPhoto</title>\n";
		} elseif (preg_match("/goods_expectation\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Förväntad inkommande godsvolym - CyberPhoto</title>\n";
		} elseif (preg_match("/goods_delays\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Försenade godsvolymer - CyberPhoto</title>\n";
		} elseif (preg_match("/not_priced\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Ej prissatta produkter - CyberPhoto</title>\n";
		} elseif (preg_match("/best_tg\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Produkter med bäst täckningsgrad - CyberPhoto</title>\n";
		} elseif (preg_match("/notshow_products\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Produkter som finns i lager men visas EJ på webben - CyberPhoto</title>\n";
		} elseif (preg_match("/cancel_purchase\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Möjliga avbokningar - CyberPhoto</title>\n";
		} elseif (preg_match("/campaign\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Hantera kampanjer - CyberPhoto</title>\n";
		} elseif (preg_match("/lagerstatus_grupperat\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Aktuell lagerstatus grupperat på våra huvudkategorier - CyberPhoto</title>\n";
		} elseif (preg_match("/random_employees\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Slumpgeneratorn - CyberPhoto</title>\n";
		} elseif (preg_match("/goods_expectation_value\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Inkommande godsvolym i värde - CyberPhoto</title>\n";
		} elseif (preg_match("/customer_order\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Visa kundorder $ordernr - CyberPhoto</title>\n";
		} elseif (preg_match("/searchlogg\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Sökloggar - CyberPhoto</title>\n";
		} elseif (preg_match("/password_recovery\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Återställning lösenord - CyberPhoto</title>\n";
		} elseif (preg_match("/logistik\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Logistikflöden - CyberPhoto</title>\n";
		} elseif (preg_match("/menu_web\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Meny webbshop - CyberPhoto</title>\n";
		} elseif (preg_match("/tech_mobile\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Tekniska data mobiltelefoner - CyberPhoto</title>\n";
		} elseif (preg_match("/pay_methods\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Fördelning betalsätt - CyberPhoto</title>\n";
		} elseif (preg_match("/product_updates\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Kommande produktuppdateringar - CyberPhoto</title>\n";
		} elseif (preg_match("/cms\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>Innehållshanteringssystem - CyberPhoto</title>\n";
		} elseif (preg_match("/tickets\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>OTRS - CyberPhoto</title>\n";
		} elseif (preg_match("/dos_product\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>MATRIX - Sök PRODUKTER - CyberPhoto</title>\n";
		} elseif (preg_match("/dos_customer\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>MATRIX - Sök KUNDER - CyberPhoto</title>\n";
		} elseif (preg_match("/dos_order\.php/i", $_SERVER['PHP_SELF'])) {
			echo "<title>MATRIX - Sök ORDER - CyberPhoto</title>\n";
		} else {
			echo "<title>Admin - CyberPhoto</title>\n";
		}
	}

	function weekday($day) {

		if ($day == 1) {
			return "måndag";
		} elseif ($day == 2) {
			return "tisdag";
		} elseif ($day == 3) {
			return "onsdag";
		} elseif ($day == 4) {
			return "torsdag";
		} elseif ($day == 5) {
			return "fredag";
		} elseif ($day == 6) {
			return "lördag";
		} elseif ($day == 7) {
			return "söndag";
		} else {
			return "";
		}
		
	}

	function monthname($month) {

		if ($month == 1) {
			return "januari";
		} elseif ($month == 2) {
			return "februari";
		} elseif ($month == 3) {
			return "mars";
		} elseif ($month == 4) {
			return "april";
		} elseif ($month == 5) {
			return "maj";
		} elseif ($month == 6) {
			return "juni";
		} elseif ($month == 7) {
			return "juli";
		} elseif ($month == 8) {
			return "augusti";
		} elseif ($month == 9) {
			return "september";
		} elseif ($month == 10) {
			return "oktober";
		} elseif ($month == 11) {
			return "november";
		} elseif ($month == 12) {
			return "december";
		} else {
			return "";
		}
		
	}

}

?>
