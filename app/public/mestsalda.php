<?php
	// mestsalda.php
	// Mest sålda produkter inom en vald kategori, räknat på levererade
	// orderrader i ADempiere (PostgreSQL) - inte den gamla MariaDB-tabellen
	// mostSoldArticles som inte längre uppdateras med aktuell försäljning.
	include_once("top.php");
	include_once("header.php");
	require_once "Db.php";

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	if ($alltimehigh == "yes") {
		echo "<h1>Sålda \"All Time High\"</h1>\n";
	} else {
		echo "<h1>Sålda de senaste 30 dagarna</h1>\n";
	}

	$pg = Db::getConnectionAD(false);
	if (!$pg) {
		echo "<p>Kunde inte ansluta till ADempiere-databasen.</p>\n";
		include_once("footer.php");
		exit;
	}
	@pg_set_client_encoding($pg, "UTF8");

	include("mestsalda_choose.php");

	if ($kategori_nr > 0) {

		$sql  = "SELECT p.m_product_id, p.value AS artnr, ";
		$sql .= "TRIM(COALESCE(manu.name,'') || ' ' || COALESCE(p.name,'')) AS produktnamn, ";
		$sql .= "SUM(ol.qtydelivered) AS antal ";
		$sql .= "FROM c_orderline ol ";
		$sql .= "INNER JOIN c_order o ON o.c_order_id = ol.c_order_id ";
		$sql .= "INNER JOIN m_product p ON p.m_product_id = ol.m_product_id ";
		$sql .= "LEFT JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id ";
		$sql .= "WHERE p.m_product_category_id = $1 ";
		$sql .= "AND o.issotrx = 'Y' ";
		$sql .= "AND o.docstatus NOT IN ('VO','RE') ";
		$sql .= "AND o.c_doctypetarget_id NOT IN (1000027, 1000026) ";
		$sql .= "AND ol.qtydelivered > 0 ";
		if ($alltimehigh != "yes") {
			$sql .= "AND o.created >= NOW() - INTERVAL '30 days' ";
		}
		$sql .= "GROUP BY p.m_product_id, p.value, manu.name, p.name ";
		$sql .= "ORDER BY antal DESC ";
		if ($alltimehigh == "yes") {
			$sql .= "LIMIT 200";
		}

		$res = @pg_query_params($pg, $sql, array((int)$kategori_nr));

		echo "<table class=\"table-list\">\n";
		echo "<thead><tr><th>#</th><th>Produkt</th><th style=\"text-align:right;\">Antal</th></tr></thead>\n";
		echo "<tbody>\n";

		$totsalda = 0;
		$countprod = 1;

		if ($res) {
			while ($row = pg_fetch_assoc($res)) {
				$artnr       = $row['artnr'];
				$productId   = (int)$row['m_product_id'];
				$produktnamn = htmlspecialchars($row['produktnamn'], ENT_QUOTES, 'UTF-8');
				$antal       = (int)$row['antal'];
				$link        = "/search_dispatch.php?mode=product&q=" . rawurlencode($artnr) . "&open=product&id=" . $productId;

				echo "<tr>";
				echo "<td>" . $countprod . "</td>";
				echo "<td><a target=\"_blank\" href=\"" . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . "\">" . $produktnamn . "</a></td>";
				echo "<td style=\"text-align:right;\">" . $antal . "</td>";
				echo "</tr>\n";

				$totsalda += $antal;
				$countprod++;
			}
			pg_free_result($res);
		}

		if ($countprod == 1) {
			echo "<tr><td colspan=\"3\"><i>Inga produkter sålda i denna kategori senaste månaden</i></td></tr>\n";
		}

		echo "</tbody>\n";
		echo "<tfoot><tr><td></td><td>Totalt</td><td style=\"text-align:right;\">" . $totsalda . "</td></tr></tfoot>\n";
		echo "</table>\n";
	}

	include_once("footer.php");
?>
