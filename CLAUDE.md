# cyberphoto-adminweb – Developer Guide

## Design system

### Shared CSS files
- **`app/public/admin_core.css`** – primary shared stylesheet (tables, badges, CSS variables)
- **`app/public/global.css`** – legacy global styles (still in use, do not remove)

`admin_core.css` is **NOT** loaded globally in `header.php` (kept commented out to avoid breaking legacy pages that use `firstrow`/`secondrow` row colors). Instead, include it explicitly per page directly after `include_once("header.php")`:

```php
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
```

Pages that currently include `admin_core.css`:
- `monitor_articles.php`
- `incomingOrders.php`
- `shop_products.php`
- `office_products.php`

Always use the classes from `admin_core.css` for new or modernised pages. Do **not** create page-specific `<style>` blocks that duplicate or override these.

### Tables
Use `<table class="table-list">` with `<thead>`, `<tbody>`, `<tfoot>` and `<th>` for header cells.

```html
<table class="table-list">
  <thead>
    <tr>
      <th>Kolumn A</th>
      <th>Kolumn B</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>...</td>
      <td>...</td>
    </tr>
  </tbody>
</table>
```

Key properties from `.table-list`:
- `border-collapse: collapse`, `font-size: 13px`
- `thead th` → teal background (`--tbl-head-bg: #d1f2f0`), bold, left-aligned
- `tbody tr:nth-child(even)` → light zebra (`--tbl-row-zebra: #fafafa`)
- `tbody tr:hover` → hover highlight (`--tbl-row-hover: #f3f4f6`)

**Do not** use `firstrow`/`secondrow` CSS classes, hardcoded `width` attributes, `align` attributes, or `<font>` tags.

### CSS variables (defined in `admin_core.css`)
```css
--tbl-border:     #e5e7eb
--tbl-head-bg:    #d1f2f0
--tbl-head-color: #111
--tbl-row-hover:  #f3f4f6
--tbl-row-zebra:  #fafafa
```

### Forms
Use a card layout consistent with `monitor_articles_add.php`:
- White background, `border: 1px solid #e5e7eb`, `border-radius: 8px`, light box-shadow
- Flexbox rows with a fixed-width label column (130px) and a field column
- Inputs/selects/textareas: `border: 1px solid #d1d5db`, `border-radius: 4px`, focus ring in teal (`#2dd4bf`)
- Submit button: teal background `#0d9488`, white text, `border-radius: 5px`

### Headings
Standard page heading: `<h1>Page title</h1>` — styled by the global stylesheet, no inline styles needed.

### Links
- Internal admin URLs: use **relative paths** (`/search_dispatch.php?...`) so they work on both localhost and production.
- Exception: links inside **e-mail notifications** must use the full absolute URL (`https://admin.cyberphoto.se/...`).

## Pages to align with the design system
All current pages use the shared design system. No known migration debt.

## Database
- **MariaDB** (`$db_r` / `$db_w`) – shop/admin data (`cyberphoto.*`)
- **PostgreSQL / ADempiere** (`$ad_r`) – products (`m_product`), stock (`m_product_cache`), orders (`c_order`)

### Product queries (PostgreSQL)
PostgreSQL folds unquoted aliases to lowercase: `AS isTradeIn` comes back as `istradein`, and `$rows->isTradeIn` is silently null (this broke trade-in pricing in `product_update.php` for months). Quote camelCase aliases (`AS "isTradeIn"`) or use lowercase property names.

Trade-in products (`m_product.istradein = 'Y'`) with tax category 1000000 ("Ingen moms") are sold under the margin scheme (VMB): their price-list price **is** the customer price, VAT applies only to the margin and is handled by ADempiere. Never divide or multiply their prices by 1.25. Their purchase price is the latest completed purchase-order line, not the price list's `pricelimit` (often stale).

Always include manufacturer name by joining `xc_manufacturer`:
```sql
SELECT p.value AS artnr,
       p.m_product_id,
       CASE WHEN manu.name IS NOT NULL AND manu.name <> ''
            THEN manu.name || ' ' || p.name
            ELSE p.name
       END AS name
FROM m_product p
LEFT JOIN xc_manufacturer manu ON manu.xc_manufacturer_id = p.xc_manufacturer_id
WHERE p.value = $1
```

## Anatomy of a page (what a new page needs)

Every page is a flat PHP file in `app/public/`, no router. Skeleton:

```php
<?php
	include_once("top.php");      // session, autoload, globals ($admin, $turnover, $adintern, ...)
	include_once("header.php");   // <html>, <head>, menu, opens #mainpanel
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin_core.css?ver=ad" . date("ynjGi") . "\">\n";
	echo "<h1>Rubrik</h1>\n";
	// ... content ...
	include_once("footer.php");   // closes #mainpanel, drawer + prefs modal
?>
```

- **Menu:** add an `<li>` in `app/public/menu.php` in the right category; the `id="current"` highlight is a `preg_match` on `$_SERVER['PHP_SELF']`, copy a neighbouring line.
- **Browser title:** `CCyberAdmin::displayPageTitle()` in `app/lib/CCyberAdmin.php` is one long `elseif` chain on the filename; add a branch or the tab says the default.
- **Classes:** `app/lib/<ClassName>.php`, autoloaded by `top.php` (`include $class . '.php'`) — `new CFoo()` just works, no require needed.
- **Globals:** `top.php` emulates register_globals (`extract($_GET)`, `$_POST`, `$_COOKIE`), so legacy code reads `$supID` directly. New code: read `$_GET` explicitly and validate.
- **Page-specific CSS/JS:** allowed as separate files next to the page (`goods_inflow.css`, `goods_inflow.js`), loaded with the same `?ver=ad` cache-buster. Not inline `<style>` blocks.
- `#mainpanel` is a fixed 1260 px; the sidebar menu takes the rest. Design for that width, not for phones.
- No login gate on plain GET in the local container: pages render for curl without cookies (the header just shows "Logga in").

## Querying ADempiere from here

- `Db::getConnectionAD()` is a **persistent** `pg_pconnect` (`$ad_r`). Use `pg_query_params($conn, $sql, [...])` with `$1..$n`; cast dates explicitly (`$2::date`). Never create temp tables or anything session-scoped — the connection is reused by the next request and the read host may be read-only.
- The read host (`AD_HOST`, erp-db-node3:5000, PostgreSQL 18, standalone — `pg_is_in_recovery()` is false) **has never been ANALYZEd**: `pg_stat_user_tables` shows 55k rows for `m_transaction` against 3.9M real. Plans that are fine on the dev DB can be 20–30× slower here. Concretely: a correlated subquery over a CTE that is referenced twice (hence materialized) is rescanned per outer row — write it as a grouped `LEFT JOIN … FILTER (WHERE …)` instead, and mark small per-row CTEs `AS MATERIALIZED` so they are not re-evaluated inside a nested loop. Time queries in the real container, not against the dev DB (see below).
- Heredoc SQL: use nowdoc (`<<<'SQL'`) so `$1` and `$this` are not interpolated.
- Warehouse ids: 1000000 = Umeå (Standard), 1000001 = Returavdelningen, 1000003 = Direktleverans, 1000006 = Butiken. `m_locator.m_warehouse_id` is the join from `m_storage`/`m_transaction` to a warehouse.
- Stock history = `m_transaction` (`movementtype` V+/V-/C+/C-/M+/M-/I+/I-, `created` is the completion timestamp, `movementdate` is date-only). Current stock = `m_storage.qtyonhand`.

## Local run, test and deploy

- `docker compose up -d` here → http://localhost:8091 (also https://localhost:8443, self-signed). Needs the external `proxy-net` docker network and `.env` (gitignored; `.env.example` lists the keys). `.env` points at the real MariaDB and the real AD read host, so local = live data.
- Syntax check inside the container (Git Bash mangles `/var/...` paths unless you set `MSYS_NO_PATHCONV=1`):
  ```bash
  MSYS_NO_PATHCONV=1 docker exec cyberphoto-adminweb php -l /var/www/html/public/<page>.php
  ```
  Timing a query: drop a small PHP script in the container with `docker cp` (use a Windows path for the source, `/tmp` is not what you think in Git Bash) and run it with `docker exec … php /tmp/x.php`.
- `docker cp` / curl from the host: `curl -s -k https://localhost:8443/<page>.php` returns the full page for GET-only pages.
- **Deploy = `git push origin main`, then `ssh dockops@docker-100` and `git pull --ff-only` in `/home/dockops/cyberphoto-adminweb`.** `app/` is volume-mounted into the container there, so PHP/CSS/JS changes are live at once; no build, no restart. The host name is plain `docker-100` (`docker-100.cyberphoto.local` does not resolve). Verify with `docker exec cyberphoto-adminweb curl -s http://localhost/<page>.php` on the box.
- Commit with an explicit pathspec (`git commit -m … -- <files>`): the checkout on docker-100 and this one can both carry untracked WIP that must not be swept in.
