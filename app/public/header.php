<?php
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	if (preg_match("/categories\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"sv\" lang=\"sv\">\n";
	} else {
		echo "<html>\n\n";
	}
	echo "<head>\n";
	// echo "<link rel=\"shortcut icon\" href=\"https://admin.cyberphoto.se/admin.ico\">\n";
	echo "<link rel=\"icon\" type=\"image/png\" href=\"https://admin.cyberphoto.se/favicon.png\">\n";
	echo "<meta charset=\"utf-8\">\n";
	$admin->displayPageTitle();
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"global.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_badges.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	// echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_search_product.css?ver=ad" . date("ynjGi") . "\">\n";
	// echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_drawer.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<script type=\"text/javascript\" src=\"/javascript/winpop.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"/javascript/simpletreemenu.js\"></script>\n";
	// echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/tradein.css?v=g" . date("ynjGi") . "\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"simpletree.css?ver=tree" . date("ynjGi") . "\">\n";
	echo "<script language=\"javascript\">\n";
	echo "\tfunction flip(rid)\n";
	echo "\t{\n";
    echo "\tcurrent=(document.getElementById(rid).style.display == 'none') ? 'block' : 'none';\n";
    echo "\tdocument.getElementById(rid).style.display = current;\n";
	echo "\t}\n";
	echo "</script>\n";
	if (preg_match("/salesreport\.php/i", $_SERVER['PHP_SELF']) || preg_match("/logistik\.php/i", $_SERVER['PHP_SELF']) || preg_match("/goods_expectation\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<script type=\"text/javascript\" src=\"/javascript/cal2.js\"></script>\n";
		echo "<script type=\"text/javascript\" src=\"/javascript/cal_conf2.js\"></script>\n";
		if ($details != "yes" && $ref_dagensdatum == $dagensdatum) {
			echo "<meta http-equiv=\"Refresh\" content=\"60\">\n";
		}
	}
	if (preg_match("/receivedOrders\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<meta http-equiv=\"Refresh\" content=\"300\">\n";
	}
	// if (preg_match("/incomingOrders\.php/i", $_SERVER['PHP_SELF']) || preg_match("/password_recovery\.php/i", $_SERVER['PHP_SELF']) || preg_match("/product_updates\.php/i", $_SERVER['PHP_SELF']) || preg_match("/not_priced\.php/i", $_SERVER['PHP_SELF'])) {
	if (preg_match("/password_recovery\.php/i", $_SERVER['PHP_SELF']) || preg_match("/product_updates\.php/i", $_SERVER['PHP_SELF']) || preg_match("/not_priced\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<meta http-equiv=\"Refresh\" content=\"60\">\n";
	}
	if (preg_match("/salesreport\.php/i", $_SERVER['PHP_SELF'])) {
		$statistics->showSalesValueGraph();
	}
	if (preg_match("/index\.php/i", $_SERVER['PHP_SELF']) && $_COOKIE['login_ok'] == "true") {
		echo "<script type=\"text/javascript\" src=\"AjaxLoaderStatus.js\"></script>\n";
		echo "<SCRIPT LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">\n";
		echo "<!--\n";
		echo "var interval = setInterval(\"showValues()\",60000);\n";
		echo "//-->\n";
		echo "</SCRIPT>\n";
		// $store->showStockValueGraph();
		// $store->showStockDiffGraph();
	}
	if (preg_match("/searchlogg\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<script type=\"text/javascript\" src=\"AjaxLoaderSearch.js\"></script>\n";
		echo "<SCRIPT LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">\n";
		echo "<!--\n";
		echo "var interval = setInterval(\"showSearch()\",30000);\n";
		echo "//-->\n";
		echo "</SCRIPT>\n";
		// $store->showStockDiffGraph();
	}
	if (preg_match("/lagervarde\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<script type=\"text/javascript\" src=\"AjaxLoader.js\"></script>\n";
		echo "<SCRIPT LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">\n";
		echo "<!--\n";
		echo "var interval = setInterval(\"showStoreValue()\",60000);\n";
		echo "//-->\n";
		echo "</SCRIPT>\n";
		$store->showStockValueGraph();
		// $store->showStockDiffGraph();
	}
	if (preg_match("/lagerstatus\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<script type=\"text/javascript\" src=\"/javascript/preLoadingMessage.js\"></script>";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/loading.css\">";
	}
	if (preg_match("/banners\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<script type=\"text/javascript\">\n";
		echo "function alertPrio() {\n";
		echo "\tif (document.addbannerform.addprio.checked)\n";
		echo "\t\talert('OBS! Använd denna funktion sparsamt');\n";
		echo "}\n";
		echo "</script>\n";
	}
	if (preg_match("/pricelist\.php/i", $_SERVER['PHP_SELF']) || preg_match("/accessories\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<script language=\"javascript\">\n";
		echo "\tfunction sf()\n";
		echo "\t{\n";
		echo "\tdocument.log.addartnr.focus();\n";
		echo "\t}\n";
		echo "</script>\n";
	}
	if (preg_match("/cms\.php/i", $_SERVER['PHP_SELF'])) {
		?>
		<?php if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") { ?>
			<script src="/tinymce_4313/tinymce.min.js"></script>
			<script type="text/javascript">
			tinymce.init({
			  selector: 'textarea',
			  language: 'sv_SE', 
			  height: 1200,
			  theme: 'modern',
			 
			  plugins: [
				'spellchecker advlist autolink lists link image charmap print preview hr anchor pagebreak',
				'searchreplace wordcount visualblocks visualchars code fullscreen',
				'insertdatetime media nonbreaking save table contextmenu directionality',
				'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager code' 
			  ],
				
				
			  toolbar1: 'save newdocument insertfile undo redo paste | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link ',
			  toolbar2: 'print preview | forecolor backcolor emoticons | responsivefilemanager | image media code ',
			  media_live_embeds: true, 
			  paste_as_text: true, 
			  image_advtab: true,
			  templates: [
				{ title: 'Test template 1', content: 'Test 1' },
				{ title: 'Test template 2', content: 'Test 2' }
			  ],
			  relative_urls : false,
			  document_base_url : "https://www.cyberphoto.se/",
			  convert_urls : true, 
			  content_css: [
				"/css/product.css"
			  ], 
			  external_filemanager_path:"/filemanager/",
			   filemanager_title:"Responsive Filemanager" ,
			   external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}
			 });

			</script>
		<?php } else { ?>
			<script src="/order/tinymce_4313/tinymce.min.js"></script>
			<script type="text/javascript">
			tinymce.init({
			  selector: 'textarea',
			  language: 'sv_SE', 
			  height: 1200,
			  theme: 'modern',
			 
			  plugins: [
				'spellchecker advlist autolink lists link image charmap print preview hr anchor pagebreak',
				'searchreplace wordcount visualblocks visualchars code fullscreen',
				'insertdatetime media nonbreaking save table contextmenu directionality',
				'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager code' 
			  ],
				
				
			  toolbar1: 'save newdocument insertfile undo redo paste | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link ',
			  toolbar2: 'print preview | forecolor backcolor emoticons | responsivefilemanager | image media code ',
			  media_live_embeds: true, 
			  paste_as_text: true, 
			  image_advtab: true,
			  templates: [
				{ title: 'Test template 1', content: 'Test 1' },
				{ title: 'Test template 2', content: 'Test 2' }
			  ],
			  relative_urls : false,
			  document_base_url : "https://www.cyberphoto.se/",
			  convert_urls : true, 
			  content_css: [
				"/css/product.css"
			  ], 
			  external_filemanager_path:"/order/filemanager/",
			   filemanager_title:"Responsive Filemanager" ,
			   external_plugins: { "filemanager" : "/order/filemanager/plugin.min.js"}
			 });

			</script>
		<?php } ?>
		<?php
	}
	if (preg_match("/ticketsXX\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"turnover.css?ver=tree" . date("ynjGi") . "\">\n";
		echo "<script type=\"text/javascript\" src=\"/javascript/cal2.js\"></script>\n";
		echo "<script type=\"text/javascript\" src=\"/javascript/cal_conf2.js\"></script>\n";
		echo "<script type=\"text/javascript\" src=\"/javascript/preLoadingMessage.js\"></script>\n";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/loading.css\">\n";
		
	?>
    <script src="https://www.google.com/jsapi?key=ABQIAAAA9eSbKtf2wfmrT9ysElV8BhTG1pS_6_qPDCCjvIf9iwcLx8kv-BTB38WVIDyy9PsELQsFyFuwsrd3ug" type="text/javascript"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
			
			data.addColumn('string');
		<?php foreach ($arrSent as $arr) {
			if ($arr->noOfTickets > $maxSent) $maxSent = $arr->noOfTickets;
			echo "data.addColumn('number', '" . $arr->usr . " (" . $arr->noOfTickets . ")')\n\t\t";
		} ?>

        data.addRows(1);

			data.setValue(0, 0,'');
		<?php $i = 1; foreach ($arrSent as $arr) {
			echo "data.setValue(0, " . $i++ . "," . $arr->noOfTickets . ")\n\t\t";
		} ?>
		
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_Sent'));
        chart.draw(data, {width: 1000, height: 400, chartArea:{left:50,top:50,width:"65%",height:"65%"}, is3D: true, legend: 'right', legendTextStyle: {color: 'black', fontName: 'verdana', fontSize: 12}, min: 0, max: <?php echo $maxSent; ?>,  is3D:true, colors:[ <?php for($i=0; $i<=sizeof($arrSent); $i++  ) {  echo "{color:'" . colorBar($i) . "'}"; if ($i<=sizeof($arrSent)-1) echo ",";  } ?> ]});
      }
    </script>

    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
			data.addColumn('string');

		<?php foreach ($arrClosed as $arr) {
			if ($arr->noOfTickets > $maxClosed) $maxClosed = $arr->noOfTickets;
			echo "data.addColumn('number', '" . $arr->usr . " (" . $arr->noOfTickets . ")')\n\t\t";
		} ?>

        data.addRows(1);
		data.setValue(0, 0,'');
			
		<?php $i = 1; foreach ($arrClosed as $arr) {
			echo "data.setValue(0, " . $i++ . "," . $arr->noOfTickets . ")\n\t\t";
		} ?>
		
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_Closed'));
        chart.draw(data, {width: 1000, height: 400, chartArea:{left:50,top:50,width:"65%",height:"65%"}, is3D: true, legend: 'right', legendTextStyle: {color: 'black', fontName: 'verdana', fontSize: 12}, min: 0, max: <?php echo $maxClosed; ?>,  is3D:true, colors:[ <?php for($i=0; $i<=sizeof($arrClosed); $i++  ) {  echo "{color:'" . colorBar($i) . "'}"; if ($i<=sizeof($arrClosed)-1) echo ",";  } ?> ]});
      }
    </script>	

    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
			data.addColumn('string');
		<?php foreach ($arrSentPeriod as $arr) {
			if ($arr->noOfTickets > $maxSent) $maxSent = $arr->noOfTickets;
			echo "data.addColumn('number', '" . $arr->usr . " (" . $arr->noOfTickets . ")')\n\t\t";
		} ?>

        data.addRows(1);

			data.setValue(0, 0,'');
		<?php $i = 1; foreach ($arrSentPeriod as $arr) {
			echo "data.setValue(0, " . $i++ . "," . $arr->noOfTickets . ")\n\t\t";
		} ?>
		
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_Sent_peroid'));
        chart.draw(data, {width: 1000, height: 400, chartArea:{left:50,top:50,width:"65%",height:"65%"}, is3D: true, legend: 'right', legendTextStyle: {color: 'black', fontName: 'verdana', fontSize: 12}, min: 0, max: <?php echo $maxSent; ?>,  is3D:true, colors:[ <?php for($i=0; $i<=sizeof($arrSentPeriod); $i++  ) {  echo "{color:'" . colorBar($i) . "'}"; if ($i<=sizeof($arrSentPeriod)-1) echo ",";  } ?> ]});
      }
    </script>
	
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
			data.addColumn('string');
			
		<?php foreach ($arrClosedPerdiod as $arr) {
			if ($arr->noOfTickets > $maxClosed) $maxClosed = $arr->noOfTickets;
			echo "data.addColumn('number', '" . $arr->usr . " (" . $arr->noOfTickets . ")')\n\t\t";
		} ?>

        data.addRows(1);
		data.setValue(0, 0,'');
			
		<?php $i = 1; foreach ($arrClosedPerdiod as $arr) {
			echo "data.setValue(0, " . $i++ . "," . $arr->noOfTickets . ")\n\t\t";
		} ?>
		
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_Closed_peroid'));
        chart.draw(data, {width: 1000, height: 400, chartArea:{left:50,top:50,width:"65%",height:"65%"}, is3D: true, legend: 'right', legendTextStyle: {color: 'black', fontName: 'verdana', fontSize: 12}, min: 0, max: <?php echo $maxClosed; ?>,  is3D:true, colors:[ <?php for($i=0; $i<=sizeof($arrClosedPerdiod); $i++  ) {  echo "{color:'" . colorBar($i) . "'}"; if ($i<=sizeof($arrClosedPerdiod)-1) echo ",";  } ?> ]});
      }
    </script>	
	<?php
		
	}
    ?>
<style>

.progress-container {
    width: 100%;
    max-width: 100%; /* Full bredd */
    margin: auto;
}

.progress-section {
    margin-bottom: 20px;
    /* text-align: center; /* Centrera sammanställningstexten */
}

h2 {
    font-size: 18px;
    margin-bottom: 10px;
}

/* Grundläggande styling för alla progress-bars */
.progress-bar {
    width: 100%;
    height: 35px; /* Lite högre för bättre synlighet */
    background: #ddd;
    border-radius: 5px;
    overflow: hidden;
    display: flex;
    align-items: center; /* Centrerar text i höjdled */
    position: relative;
}

/* Arbetad tid */
.work-progress .progress-done {
    background: #4CAF50; /* Grön färg */
}

/* Försäljning */
.sales-progress .progress-done {
    background: #4CAF50; /* Blå färg */
}

/* Styling för framsteg */
.progress-done, .progress-left {
    height: 100%;
    display: flex;
    align-items: center; /* Centrerar text i höjdled */
    justify-content: center;
    font-weight: bold;
    color: white;
}

/* Kvarvarande tid */
.progress-left {
    background: #ff9800;
    color: black;
}

/* Sammanställningstext under stapeln */
.progress-summary {
    font-size: 14px;
    margin-top: 5px;
    color: #333;
}

/* Animeringar */
@keyframes grow-work {
    from {
        width: 0;
    }
    to {
        width: <?= min($work_percentage, 100) ?>%;
    }
}

@keyframes grow-sales {
    from {
        width: 0;
    }
    to {
        width: <?= min($sales_percentage, 100) ?>%;
    }
}

.work-animate {
    animation: grow-work 2s ease-in-out;
}

.sales-animate {
    animation: grow-sales 2s ease-in-out;
}

.sales-gron-text {
    color: green;
}

.sales-rod-text {
    color: red;
}

/* Header container */
#admin-top{
  display:flex; align-items:center; justify-content:space-between;
  gap:16px; padding:10px 0 8px 0;
  float: left;
}

/* Brand: ikon + ADMIN */
.brand{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; }
.brand-icon{ width:200px; height:45px; display:block; }
.brand-text{
  font-weight:800; letter-spacing:0.08em;
  font-size:20px; line-height:1;
  color:#111;
}
.brand-text::first-letter{ color:#c81e1e; } /* liten accent */

/* Quick-search (höger) */
.qs-bar{ display:flex; gap:10px; flex:1; justify-content:flex-end; flex-wrap:wrap; }
.qs-form{ margin:0; }
.qs-form input[type="text"]{
  height:36px; padding:0 12px; min-width:230px;
  border:1px solid #cfd6e0; border-radius:8px; font-size:14px;
}
.qs-form input[type="text"]:focus{
  outline:none; border-color:#8ab4f8; box-shadow:0 0 0 3px rgba(138,180,248,.35);
}

.qs-bar input{
  height:36px; padding:0 12px; min-width:230px;
  border:1px solid #cfd6e0; border-radius:8px;
  font-size:14px; background:#fff;
}
.qs-bar input:focus{
  outline:none; border-color:#8ab4f8; box-shadow:0 0 0 3px rgba(138,180,248,.35);
}

/* Responsivt */
@media (max-width: 1100px){
  .qs-bar input{ min-width:200px; }
}
@media (max-width: 800px){
  #admin-top{ flex-direction:column; align-items:stretch; gap:8px; }
  .qs-bar{ justify-content:stretch; }
  .qs-bar input{ flex:1; min-width:0; }
}

.dw-icon-btn{ color:#111827; } /* default */
.dw-icon-btn.is-active{
  color:#b91c1c;
  border-color:#fecaca;
  background:#fef2f2;
}
</style>

    <?php

	echo "</head>\n\n";
	if (preg_match("/pricelist\.php/i", $_SERVER['PHP_SELF']) && $addart == "yes") {
		echo "<body onLoad=sf()>\n\n";
	} elseif (preg_match("/accessories\.php/i", $_SERVER['PHP_SELF']) && $addart == "yes") {
		echo "<body onLoad=sf()>\n\n";
	} elseif (preg_match("/adtrigger\.php/i", $_SERVER['PHP_SELF']) && $addart == "yes") {
		echo "<body onLoad=sf()>\n\n";
	} elseif (preg_match("/lagervarde\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<body onload=\"showStoreValue()\">\n\n";
	} elseif (preg_match("/searchlogg\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<body onload=\"showSearch();\">\n\n";
	} elseif (preg_match("/index\.php/i", $_SERVER['PHP_SELF'])) {
		echo "<body onload=\"showValues();\">\n\n";
	} else {
		echo "<body>\n\n";
	}

	echo "<div id=\"centertop\"></div>\n";
	echo "<div id=\"centermiddle\">\n";
	echo "<div id=\"content\">\n";

	echo '<div id="admin-top">';
	echo '<a href="https://' . $_SERVER['HTTP_HOST'] . '" class="brand">';
	echo '    <img src="/img/admin-logo-new-5.png" alt="Admin" class="brand-icon">';
	echo '  </a>';

	echo '  <div class="qs-bar">';

	  // PRODUKT
	echo '    <form class="qs-form" action="/search_dispatch.php" method="get">';
	echo '      <input type="hidden" name="mode" value="product">';
	echo '      <input type="hidden" name="page" value="1">';
	echo '      <input id="searchProduct" name="q" type="text" placeholder="S&ouml;k produkt (P)" autocomplete="off">';
	echo '    </form>';

	  // KUND
	echo '    <form class="qs-form" action="/search_dispatch.php" method="get">';
	echo '      <input type="hidden" name="mode" value="customer">';
	echo '      <input type="hidden" name="page" value="1">';
	echo '      <input id="searchCustomer" name="q" type="text" placeholder="S&ouml;k kund/leverant&ouml;r (K)" autocomplete="off">';
	echo '    </form>';

	  // ORDER
	echo '    <form class="qs-form" action="/search_dispatch.php" method="get">';
	echo '      <input type="hidden" name="mode" value="order">';
	echo '      <input type="hidden" name="page" value="1">';
	echo '      <input id="searchOrder" name="q" type="text" placeholder="S&ouml;k order (O)" autocomplete="off">';
	echo '    </form>';
	?>
	<!-- Filter-ikon (inline SVG, samma stil som kugghjulet) -->
	<button type="button" id="prefBtn" class="dw-icon-btn" title="Filtrera resultat">
	  <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
		   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
		<!-- klassisk tratt -->
		<path d="M3 4h18l-7 8v6l-4 2v-8L3 4z"/>
	  </svg>
	</button>
	<?php

	echo '  </div>';
	echo '</div>';

	echo "<div class=\"dateheading\">" . date("H:i") . " - " . $admin->weekday(date("N")) . " " . date("j") . " " . $admin->monthname(date("n")) . " " . date("Y") . " - Temperatur just nu: " . $temp->showLastTemp(2) . " &#8451;</div>\n";
	if ($_COOKIE['login_ok'] == "true") {
		if ($_COOKIE['login_userid'] == 99) {
			echo "<div class=\"dateheading\">Inloggad: <a class=\"user_name\" class=\"logout\" href=\"/profile.php\">" . $_COOKIE['login_name'] . "</a> (KOPPLINGEN TILL ADEMPIERE SAKNAS) - IP-adress: <span class=\"ipnr\">" . $_SERVER['REMOTE_ADDR'] . "</span> - <a class=\"logout\" href=\"logout.php\">Logga ut</a></div>\n";
		} else {
			echo "<div class=\"dateheading\">Inloggad: <a class=\"user_name\" class=\"logout\" href=\"/profile.php\">" . $_COOKIE['login_name'] . "</a> - IP-adress: <span class=\"ipnr\">" . $_SERVER['REMOTE_ADDR'] . "</span> - <a class=\"logout\" href=\"logout.php\">Logga ut</a></div>\n";
		}
	} else {
		echo "<div class=\"dateheading\"><a href=\"$auth_link\"> Logga in</a> - IP-adress: <span class=\"ipnr\">" . $_SERVER['REMOTE_ADDR'] . "</span></div>\n";
	}

	echo "<div class=\"clear hr_gray\"></div>\n";

	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		if(isset($_COOKIE['login_ok'])) {
			echo "<div class=\"align_left\">login_ok: " . $_COOKIE['login_ok'] . "</div>\n";
		}
		if(isset($_COOKIE['login_name'])) {
			echo "<div class=\"align_left\">login_name: " . $_COOKIE['login_name'] . "</div>\n";
		}
		if(isset($_COOKIE['login_mail'])) {
			echo "<div class=\"align_left\">login_mail: " . $_COOKIE['login_mail'] . "</div>\n";
		}
		if(isset($_COOKIE['login_userid'])) {
			echo "<div class=\"align_left\">login_userid: " . $_COOKIE['login_userid'] . "</div>\n";
		}
		if ($_COOKIE['login_ok'] == "true") {
			echo "<div class=\"align_left\">inloggad: ja</div>\n";
		}
		if ($_COOKIE['login_userid'] > 1000000 && $_COOKIE['login_userid'] < 2000000) {
			echo "<div class=\"align_left\">ID korrekt: ja</div>\n";
		}
		if(isset($_SESSION['return_to'])) {
			echo "<div class=\"align_left\">" . $_SESSION['return_to'] . "</div>\n";
			if ($_SESSION['return_to'] == "https://admin.cyberphoto.se/index.php") {
				echo "<div class=\"align_left\">japp</div>\n";
			} else {
				echo "<div class=\"align_left\">nope</div>\n";
			}
		}
	}
	echo "<div class=\"top5\"></div>\n";
	include_once("menu.php");
	echo "\n<div id=\"mainpanel\">\n\n";
?>