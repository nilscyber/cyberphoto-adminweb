<?php

	include_once("top.php");
	include_once("header.php");

	echo "<h1>Kategori-träd</h1>\n";
	echo "<div style=\"float: left; margin-left: 1px;\">Snabblänkar till:</div>\n";
	echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=583\"><b>Foto - Video</b></a> | </div>\n";
	echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=585\"><b>Mobiltelefoni</b></a> | </div>\n";
	echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=584\"><b>Outdoor</b></a> | </div>\n";
	// echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=586\"><b>Ljud - Bild</b></a> | </div>\n";
	// echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=1000045\"><b>Batterier</b></a> | </div>\n";
	// echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=1000147\"><b>Cybairgun</b></a> | </div>\n";
	// echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=1000082\"><b>Hushåll</b></a></div>\n";
	echo "<div class=\"clear\"></div>\n";
	echo "<div id=\"xmlcat\">\n";
 
	include_once 'conn.php'; // Databasanslutningen
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		include_once 'items_class3.php'; // Kategori-klass
	} else {
		include_once 'items_class3.php'; // Kategori-klass
	}

	list_items(); 
	 

	function list_items () {

	   $sql = "SELECT kategori_id AS catid, kategori AS name, kategori_id_parent AS parent FROM Kategori WHERE visas = -1 "; 
	   $sql = "SELECT kategori_id AS catid, concat(kategori,visas) AS name, kategori_id_parent AS parent FROM Kategori  WHERE (visas = -1 OR lower(kategori) like '%fisk%')  "; 
	   $sql = "SELECT kategori_id AS catid, concat(kategori,visas) AS name, kategori_id_parent AS parent FROM Kategori  WHERE visas = -1 OR
	   kategori in ('** DOLD ** (FOTO)','** DOLD ** (MOBIL)','.','2000','Actionkamera','Administrativa avgifter','AK47, AK74','Alias','Alias objektiv','Armborst','Artiklar på köpet','Assembly','Astronomiska Teleskop paket','Bakmaskiner','Begagnat','Betessortiment','Betestillbehör','Bildmonteringsmateriel','Brödrostar','Digitalkamera stillbild / videokombination','Disney','DVD videokamera','Dölj i prestashop','E-bokläsare','E-bokläsare tillbehör','Elvispar','Epilatorer','Espressomaskiner','Filter till julkalendern','Fitness','Fodral & märkning CD/DVD','Fotopapper - A2','Fotopapper - A3','Fotoramar','Frakt','Frakt export','Franchi ','Fritöser','Fruktpressar','Förstoring optik','Försäkring','Förvaring','G36, G3','G-Cube','Gearbox ','Gevär CO2','Glassmaskiner','Glidin Rap','GPS-paket','Granatkastare','Grepp','Grillar','Gummibeten','Handkikare 15','Handkikare 7 - 9x ','Haspelrulle - broms bak','Haspelrulle - broms fram','Haspelspön','Haspelspön - teleskop','Headset','Headset - USB','Headset ej trådlöst ','Hi8 band','Hi8 videokamera','Hjälmar','Hjälpmedelsbatterier - Ej uppladdningsbara','HK416, M4 - M16','Hundtillbehör ','Hushåll ','Husky','Hållare/bordsställ paket','Hårddiskar','Hårvård','Hölster och västar','Hörlurar reservdelar','Hörlurar trådlösa (till TV-apparater)','iPad','iPad - fodral/skal','iPad - LCD-skydd','iPad - tillbehör','iPad - Övrigt','iPhone - Hörlurar','iPhone - LCD-skydd','iPhone - Skins / Skal','iPhone - tillbehör','iPhone - Övriga tillbehör','iPhone / iPad / iPod - Kablage','iPhone / iPad / iPod - Laddare','iPhone 4 - Skins / Skal ','IR-filter','IWI','Jigspön','Jointed Minnow','Jointed Shad rap','Kaffe','Kaffebryggare','Kamerablixtpaket','Kamerahuvud','Kameramotorer','Kameror- Begagnade','Kapselmaskiner','Kastruller','Keps/ Mössa','Kikare / dag-natt','Kikarsikten ','Kläder','Kläder & Skor','Kläder / Skor','Klädvård','Knappcell - Ej uppladdningsbara ','Knivpaket','Knivset ','Knivslipar','Knivställ','Kokprodukter','Kolsyremaskiner','Kolsyremaskiner - Tillbehör','Kolsyreprodukter','Kombinerade bryggare','Kompakta','Kompaktkamerapaket','Kompasser ','Konferenstelefoner','Kontantkort','Konvertrar','Kortbetalning','Kostnader','Kurser','Kök & Mat','Köksapparater','Köksassistenter','Köksknivar','Köksredskap','Köksvågar','Lagringsmedia - video','Lampor (används ej)','Laptops','Laserlibell/vattenpass','Lasersikten','LCD-Skydd Surfplattor ','Ledad flyt','LED-belysningspaket','LEDIG KATEGORI','LEDIG KATEGORI','LEDIG KATEGORI','LEDIG KATEGORI','Ledig kategori','LEDIG KATEGORI','LEDIG KATEGORI','Ledig kategori','LEDIG KATEGORI','LEDIG KATEGORI','Lego','Leksaker','Lithiumbatteri - Ej uppladdningsbara','Ljuddämpare','Ljudinspelare - paket','Ljudmixer','Ljusmätare tillbehör','Luftgevär CO2','Luftgevär Fjäder','Luftgevär PCP','Luftgevär Pump','Luftpistol CO2','Luftpistol Fjäder','Luftpistol Pump','Lågenergilampor','M249 SAW, M60','Macroblixt','Magasin','Magasin - Gevär ','Magasin - Pistol','Magnum','Manuellt fokuserade objektiv','Marina','Marknadsföring & Fakturering','Markör','Markör RAM','Masker','Matberedare','MCS','mcs-new','Mediaspelare','Mediaspelare - tillbehör','MemoryStick','Memorystick Duo Pro','Memorystick Micro (M2)','Memorystick Pro','Metspön','MicroMV videokamera','Micro-SD/SDHC','Micro-SDXC','Microstereo med iPhone-docka','Mid Thunder','Mini Fatrap','Miniräknare','Ministudiopaket','Minneskortsläsare Firewire','Minneskortsläsare USB 1.1','Minneskortsläsare övrigt','Minnesmedia ','Minnesmedia minneskort','Minnow Rap','Minnow Spoon','Mixers','Mobil - Kampanjprodukter ','Mobilabonnemang','Mobilabonnemang - förhöjd månadsavgift','Mobilabonnemang - övriga tjänster','Mobilsmycke','Mobilt','Mobilt bredband','Mobiltelefonpaket ','Mobiltelefonpaket - Halebop ','Mobiltelefonpaket - Tele2','Mobiltelefonpaket - Telia','Mobiltelefonpaket - tillbehör','MP5, MP7, MP9','Multimediacard','MV band','Möss','Navigering','Objektiv - Begagnade','Objektiv - gamla','Objektiv APS','Objektiv DX','Objektiv till videokameror','objektiv_till_4/3','Objektivkit','Objektivpaket','Odefinierad','Okända','Okänt','Optik & tillbehör','Orientering','Outdoor - Kampanj','P90, SCAR','Packing','Paint tillbehör','Paketlösning','Paketlösningar','Panoramahuvud','Pennor ','Personvård','Pimpelset','Pimpelspön','Pimpelspön - tillbehör','Pistol','Pistol eldriven','Pistol Fjäder ','Pistol Gas ','Pistol gas','Pistol PCP ','Pistong m.m','Popcornmaskiner','Precisionspipor ','Programvara','Programvara videoredigering (avancerad)','Projektionsdukar','Projektionsdukar (golvmodell)','Projektionsdukar (motordrivna)','Projektionsdukar (standard)','Projektionsdukar (wide/16:9)','Projektorer / Data','Projektorer / Hemmabio','Projektorpaket','Pulsmätare ','Rakapparater','Rattlin Rap','Raw Material','RC produkter - Bilar','RC produkter - Båtar','RC produkter - Flygplan','RC produkter - Helikopter','RC produkter - Reservdelar','Reparationer','Replikor','Resources','Ringblixtar','Ritplattor ','Ryggsäck äventyr','Ryggsäckar','Ryggsäckar och väskor','Räknare','SCAR','Scrapbooking album, med plastfickor','Scrapbooking album, utan plastfickor','Scrapbooking tillbehör','Scrapbooking tillbehörskit','Scrapbooking-kit','Scrapbookingpaket','SDHC, Secure Digital High Capacity','SDXC, Secure Digital eXtended Capacity','Serie 7 ring','Shad Rap','Shallow Magnum','Shallow Thunder','SIG','Sikten','Sikten','Silveroxid - Ej uppladdningsbara','Självrisk','Skitter Pop / Prop / Walk','Skrivare','Skrivarpaket','Skrivartillbehör övrigt - använd t ex kat 3 istället','Skydd ','Slangbellor & tillbehör','Sliver','Snabbladdare för Ammo','Sniper','Spinnrullar','Spinnspön','Spinnspön - Jerk / Jigg','Spinnspön - teleskop','Standardbryggare','Stativpaket foto/video','Stativväskor','Stavmixer','Stekhällar & tillbehör','Stekpannor','Steyr','Stormtändare ','Strykjärn','Ström till bärbara datorer','Strömförsörjning','Studioblixtvärdepaket','Studiohjälpmedel','Studiostativ','Super Shad rap','Surfplattor - Kampanj','Surfplattor Värdepaket','S-VHS-C band','Svärd','Systemkamerapaket','Systemkameror analoga','T2 adapter','Tail Dancer','Tandborste - eldriven','Tandborste - tillbehör','Telekonverter','Telekonverter videokameror','Termosar','Termosar','Till MP3, mediaspelare etc','Tillbehör','Tillbehör - Luftvapen','Tillbehör - surfplattor','Tillbehör 645','Tillbehör- Begagnade','Tillbehör ficklampor','Tillbehör kaffe ','Tillbehör knivar ','Tillbehör skor','Tillbehör telefoni','Tillbehörspaket','Tjänster','Trådlös styrning, slavstyrning','Trådlösa telefoner (ej mobiltelefon)','Trådlösa telefoner, headset & tillbehör','Trådlösa telefonpaket','Trådlöst övrigt','Träningstillbehör','Träningstillbehör','Tubkikarpaket','TwistCar','Twitchin Rap','Täckningsbidragsstöd','Ultrakompakta ','Uppgift saknas','USB - Specialkontakt ','Vakuumpackare - tillbehör','Vandringsstavar ','Vapentillbehör','Vattenkokare','Verktygsbatteri - Makita','VHS band','Video - pro','Video 8 band','Videoband','Videokamera med minneskort paket','Videokameraobjektiv','Videokamerapaket','Videokameror (inaktiv)','Videoprojektorer tillbehör','Videoredigering ','Videoredigering för kameror med analog utgång','Videoredigering tillbehör mm','Videoredigering övriga','Videoväska','Videoväska','Vidvinkelkonverter digitalkamera','WildEye Minnow','Våffeljärn','Väderstationer','Värdepaket Pistol PCP','Väskor - Laptop ','Väskor - Vattentätt','Väskor-National Geographic','xD minneskort','X-Rap','Zeiss','Åtel- & Viltkameror','Äldre digitalkameror','Öronmusslor - Jakt- / Komradio','Övrig film','Övriga ','Övriga','Övriga','Övriga bryggare','Övriga produkter','Övriga stativtillbehör','Övriga vapen','Övriga vapentillbehör','Övrigt','Övrigt bärbara datorer','Övrigt hushåll','Övrigt övrigt')
	   ";
		// $sql .= " AND isInsurable = -1 ";
       // $sql .= " ORDER BY sortPriority DESC, kategori_id ASC, name ASC";
       $sql .= " ORDER BY sortPriority DESC, name ASC, kategori_id ASC";
       // echo $sql;
	   $items = new ItemTree($sql);

	   $catid = isset($_GET['catid']) && intval($_GET['catid']) > 0 ? $_GET['catid'] : 0;

	   $tpl_nav = '<a href="categories.php?catid={catid}" class="navlink">{name}</a>';
	   $startlink = '<a href="categories.php" class="navlink" style="color:navy">Start</a>';
	   $nav_links = $items->get_navlinks($catid, $tpl_nav, $startlink);

	   $tpl_items = '&bull; <a href="categories.php?catid={catid}" class="treelink" style="color:blue">{name}</a>';
	   $tree = $items->show_tree($catid, $tpl_items, 'tree');
	   
	   $info = !empty($catid) ? '<div style="padding:10px">Visar information om '.$items->get_item_name($catid).'</div>' : '';
	   
	   $sproducts = !empty($catid) ? '<div style="padding:10px">Dessa aktiva produkter finns i denna kategori:<br><br>'.$items->showProductsInCategory($catid).'</div>' : '';
	   
	   $tpl_items = '&bull; <a href="categories.php?edit={catid}" class="treelink" style="color:blue">{name}</a>';
	   $admin = $items->show_tree(0, $tpl_items, 'tree');
	   
	   
	   // include 'top.tpl.php';

	   echo '
		 
		 <div style="background-color:#CCCCCC; padding:3px 15px; margin-bottom:15px; margin-top:15px;">
		 '.$nav_links.'
		 </div>
		 
		 '.$info.'
		 
		 '.$tree.'
		 
		 '.$sproducts.'
	   ';

	   // include 'bottom.tpl.php';
	}

	echo "</div>\n";
	include_once("footer.php");
 
?>
