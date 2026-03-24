<?php

	include_once("top.php");
	include_once("header.php");

	echo "<h1>Kategori-träd</h1>\n";
	echo "<div style=\"float: left; margin-left: 1px;\">Snabblänkar till:</div>\n";
	echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=583\"><b>Foto - Video</b></a> | </div>\n";
	echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=1000468\"><b>Drönare</b></a> | </div>\n";
	echo "<div style=\"float: left; margin-left: 3px;\"><a style=\"color:blue\" href=\"" . $_SERVER['PHP_SELF'] . "?catid=586\"><b>Hem - Teknik</b></a> | </div>\n";
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