# cyberphoto-adminweb – Developer Guide

## Design system

### Shared CSS files
- **`app/public/admin_core.css`** – primary shared stylesheet (tables, badges, CSS variables)
- **`app/public/global.css`** – legacy global styles (still in use, do not remove)

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
