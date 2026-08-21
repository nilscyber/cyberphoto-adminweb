<?php
	// mestsalda_kat.php
	// Mest sålda kategorier senaste 30 dagarna, räknat på levererade orderrader
	// i ADempiere (PostgreSQL) - inte den gamla MariaDB-tabellen mostSoldArticles
	// som inte längre uppdateras med aktuell försäljning.
	include_once("top.php");
	include_once("header.php");
	require_once "Db.php";

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";

	echo "<h1>Sålda de senaste 30 dagarna (kategorier)</h1>\n";

	$pg = Db::getConnectionAD(false);
	if (!$pg) {
		echo "<p>Kunde inte ansluta till ADempiere-databasen.</p>\n";
		include_once("footer.php");
		exit;
	}
	@pg_set_client_encoding($pg, "UTF8");

	$sql = "
		SELECT pc.m_product_category_id AS kategori_id,
		       pc.name                  AS kategori,
		       SUM(ol.qtydelivered)     AS antal
		FROM c_orderline ol
		INNER JOIN c_order o             ON o.c_order_id = ol.c_order_id
		INNER JOIN m_product p           ON p.m_product_id = ol.m_product_id
		INNER JOIN m_product_category pc ON pc.m_product_category_id = p.m_product_category_id
		WHERE o.issotrx = 'Y'
		  AND o.docstatus NOT IN ('VO','RE')
		  AND o.c_doctypetarget_id NOT IN (1000027, 1000026)
		  AND ol.qtydelivered > 0
		  AND o.created >= NOW() - INTERVAL '30 days'
		GROUP BY pc.m_product_category_id, pc.name
		ORDER BY antal DESC
	";

	$res = @pg_query($pg, $sql);

	echo "<table class=\"table-list\">\n";
	echo "<thead><tr><th>Kategori</th><th style=\"text-align:right;\">Antal</th></tr></thead>\n";
	echo "<tbody>\n";

	$totsalda = 0;
	$antalrader = 0;

	if ($res) {
		while ($row = pg_fetch_assoc($res)) {
			$kategori_id = (int)$row['kategori_id'];
			$kategori    = htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8');
			$antal       = (int)$row['antal'];

			echo "<tr>";
			echo "<td><a href=\"mestsalda.php?kategori_nr=" . $kategori_id . "\">" . $kategori . "</a></td>";
			echo "<td style=\"text-align:right;\">" . $antal . "</td>";
			echo "</tr>\n";

			$totsalda += $antal;
			$antalrader++;
		}
		pg_free_result($res);
	}

	if ($antalrader == 0) {
		echo "<tr><td colspan=\"2\"><i>Inga produkter sålda senaste 30 dagarna</i></td></tr>\n";
	}

	echo "</tbody>\n";
	echo "<tfoot><tr><td>Totalt</td><td style=\"text-align:right;\">" . $totsalda . "</td></tr></tfoot>\n";
	echo "</table>\n";

	include_once("footer.php");
?>
