<?php
	include_once("top.php");
	include_once("header.php");
	if ($country == "")
		$country = "sv";
?>
<style>
/* ── Inkommande ordrar – modern design ────────────────────────────── */
.io-page h1 {
	font-size: 1.3rem; font-weight: 700; color: #1e293b;
	margin: 1rem 0 0.9rem; letter-spacing: -0.01em;
}
.io-filters {
	display: flex; flex-wrap: wrap; gap: 0.5rem 1.5rem;
	align-items: center; background: #f8fafc;
	border: 1px solid #e2e8f0; border-radius: 8px;
	padding: 0.7rem 1rem; margin-bottom: 1.25rem;
}
.io-filter-item {
	display: flex; align-items: center; gap: 0.45rem;
	font-size: 0.83rem; color: #475569;
	cursor: pointer; user-select: none; white-space: nowrap;
}
.io-filter-item input[type=checkbox] {
	width: 15px; height: 15px; cursor: pointer;
	accent-color: #3b82f6; flex-shrink: 0;
}
/* Table container */
.io-wrap { overflow-x: auto; margin-top: 0.25rem; }
.io-table {
	border-collapse: collapse; width: 100%;
	font-size: 0.82rem; font-family: system-ui, -apple-system, sans-serif;
	border-radius: 8px; overflow: hidden;
	box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.io-table th {
	background: #1e293b; color: #e2e8f0;
	padding: 0.6rem 0.75rem; text-align: left;
	white-space: nowrap; font-weight: 600; font-size: 0.77rem;
	letter-spacing: 0.04em; text-transform: uppercase;
}
.io-table th.r { text-align: right; }
.io-table th.c { text-align: center; }
.io-table td {
	padding: 0.44rem 0.75rem;
	border-bottom: 1px solid #f1f5f9;
	vertical-align: middle; color: #334155;
}
.io-table tbody tr:hover td { background: #eff6ff !important; }
.io-row-a td { background: #ffffff; }
.io-row-b td { background: #f8fafc; }
/* Day header */
.io-day-hdr td {
	background: #e2e8f0 !important; font-weight: 700;
	color: #1e293b; padding: 0.4rem 0.75rem;
	font-size: 0.8rem; letter-spacing: 0.02em;
	border-top: 2px solid #cbd5e1;
}
/* Count row */
.io-count-row td {
	background: #f1f5f9 !important; color: #64748b;
	font-style: italic; font-size: 0.78rem;
	border-bottom: 2px solid #e2e8f0;
}
/* Total row */
.io-total-row td {
	background: #1e293b !important; color: #f1f5f9 !important;
	font-weight: 600; border-top: 2px solid #0f172a;
}
/* Links */
.io-order-link {
	color: #2563eb; text-decoration: none; font-weight: 600;
	font-variant-numeric: tabular-nums;
}
.io-order-link:hover { text-decoration: underline; }
.io-cust-btn {
	display: inline-block; padding: 0.15rem 0.55rem;
	background: #dbeafe; color: #1d4ed8; border-radius: 4px;
	font-size: 0.78rem; text-decoration: none; white-space: nowrap;
}
.io-cust-btn:hover { background: #bfdbfe; }
/* Delivered icon */
.io-delivered-yes { color: #16a34a; font-size: 1.05rem; font-weight: 700; }
.io-delivered-no  { color: #e2e8f0; }
/* Misc */
.io-note { color: #64748b; font-style: italic; font-size: 0.78rem; }
.io-negative { color: #dc2626 !important; }
</style>
<?php
	echo "<div class=\"io-page\">\n";
	echo "<h1>Marginalstruktur inkommande ordrar</h1>\n";
	echo "<form method=\"GET\">\n";
	echo "<div class=\"io-filters\">\n";

	$chk = ($delivered == "no") ? " checked" : "";
	echo "<label class=\"io-filter-item\"><input type=\"checkbox\" name=\"delivered\" value=\"no\" onClick=\"submit()\"$chk> Visa endast Ej levererade</label>\n";

	if ($delivered == "no") {
		$chk = ($part_delivered == "no") ? " checked" : "";
		echo "<label class=\"io-filter-item\"><input type=\"checkbox\" name=\"part_delivered\" value=\"no\" onClick=\"submit()\"$chk> Visa Ej dellevererade</label>\n";
	}

	if ($delivered != "no") {
		$chk = ($one_week == "yes") ? " checked" : "";
		echo "<label class=\"io-filter-item\"><input type=\"checkbox\" name=\"one_week\" value=\"yes\" onClick=\"submit()\"$chk> Visa en vecka bakåt</label>\n";
	}

	$chk = ($only_delivered == "yes") ? " checked" : "";
	echo "<label class=\"io-filter-item\"><input type=\"checkbox\" name=\"only_delivered\" value=\"yes\" onClick=\"submit()\"$chk> Endast levererade</label>\n";

	$chk = ($sales_by_seller == "yes") ? " checked" : "";
	echo "<label class=\"io-filter-item\"><input type=\"checkbox\" name=\"sales_by_seller\" value=\"yes\" onClick=\"submit()\"$chk> EJ webborder</label>\n";

	$chk = ($group_by_litium == "yes") ? " checked" : "";
	echo "<label class=\"io-filter-item\"><input type=\"checkbox\" name=\"group_by_litium\" value=\"yes\" onClick=\"submit()\"$chk> Endast webborder</label>\n";

	$chk = ($only_shop == "yes") ? " checked" : "";
	echo "<label class=\"io-filter-item\"><input type=\"checkbox\" name=\"only_shop\" value=\"yes\" onClick=\"submit()\"$chk> Endast butiken</label>\n";

	echo "</div>\n";
	echo "</form>\n";

	$turnover->displayIncommingOrders(true, false, false);

	echo "</div>\n";

	include_once("footer.php");
?>
