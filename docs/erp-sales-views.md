# ERP sales views: invoiced sales vs. sales orders

Two pairs of views in the ADempiere database (schema `adempiere`) are the
standard way to read sales figures. Use them instead of writing a new query
against `C_Order` or `Fact_Acct`; they already carry the rules that are easy
to get wrong (trade-in margin scheme, web vs. manual, B2B vs. B2C, vendor,
category tree, cost).

| | Invoiced and posted sales | Sales orders ("what is being sold now") |
|---|---|---|
| Source | `Fact_Acct` (the ledger: revenue and cost postings of invoices and shipments) | `C_Order` / `C_OrderLine`, `IsSOTrx='Y'` |
| Line view | `v_sales_report_acct_v5` | `v_sales_report_order_v1` |
| Aggregate view | `v_powerbi_sales_v5` | `v_powerbi_orders_v1` |
| **Materialized view (read this)** | **`mv_powerbi_sales_v5`** | **`mv_powerbi_orders_v1`** |
| Date column | `dateacct` (accounting date, timestamp at midnight) | `orderdate` (= `C_Order.Created::date`, never edited) + `dateordered` (editable) |
| A sale counts when | the invoice (or shipment, for cost) is completed and posted | the order is prepared/completed, long before shipment and invoice |
| Cost / contribution | posted cost of goods (account 4010), `contributionpo` = PriceLimit-based | PriceLimit × qty only (`contribution` here = v5's `contributionpo`) |
| Credit memos / returns | included as negative rows | not included (a return is not an order) |
| Refresh | hourly at :10 (ADempiere scheduler 1000215) | hourly at :40 (scheduler 1000217) |
| Rows / size (prod, Oct 2026) | ~600 k / 150 MB, 5 years | ~550 k / 125 MB, 5 years |

The materialized views are refreshed `CONCURRENTLY`, so a read never blocks
and never sees a half-refreshed state. Reading them costs milliseconds; the
underlying views cost 15–60 s and are only for ad-hoc checks. Both pairs are
on the production database and on its streaming replicas (the admin read host
included), with identical content.

## Grain and columns of the materialized views

One row per *date × product × customer × seller × order state*. Sum the
amount columns, never average them.

Common to both (same names, same meaning):

| Column | Meaning |
|---|---|
| `linenetamt` | revenue excl. VAT in SEK; for a trade-in product it includes the margin-scheme charge (see below) |
| `vmbamt` | the part of `linenetamt` that is the margin-scheme charge (0 for everything else) |
| `currentcostprice` | PriceLimit × qty (the cost snapshot copied onto the line) |
| `contribution` | v5: `linenetamt` − posted cost of goods; v1: `linenetamt` − `currentcostprice` |
| `vendor` | current vendor of the product (`M_Product_PO`, `IsCurrentVendor` first) |
| `manufacturer`, `product_category`, `main_category` | product attributes (`XC_Manufacturer`, own category, root category) |
| `artno`, `descr`, `m_product_id`, `istradein` | the product |
| `customerno` | `C_BPartner.Value` of the customer |
| `isorganization` | `'Y'` = business customer (BP group of the customer); for B2B/B2C reporting use `C_BP_Group_ID = 1000000` on the line view |
| `seller`, `salesrep_id` | the order's / invoice's sales rep |
| `is_web` | `salesrep_id = 1652736` ("Weborder") — the definition of a web order |

Only in `mv_powerbi_sales_v5`: `dateacct`, `qtyshipped`, `qtyinvoiced`,
`contributionpo`.

Only in `mv_powerbi_orders_v1`: `orderdate`, `dateordered`, `docstatus`,
`qtyordered`, `qtydelivered`, `qtyinvoiced`, `orders` (distinct orders in
the row).

Both exclude freight (`artno NOT LIKE 'frakt%'`), non-item products
(`ProductType <> 'I'`) and anything older than 5 years.

## Scope of the order-based view

- `docstatus` **not** `DR` (draft), `VO` (voided), `RE` (reversed). So `CO`
  completed, `CL` closed, `IP` in progress, `WP` waiting payment and `IN`
  invalid are in — `IN` on purpose, it is the passing state after a failed
  prepare. Filter on `docstatus = 'CO'` when only completed orders should
  count; in practice the open set is a few hundred lines.
- Document type: offers (`ON`, `OB`) and return-material orders (`RM`) are
  out. Everything that remains is Standard Order.
- Lines without a product (charges) are out.

## The trade-in (margin scheme, "VMB") rule

A used product bought from a customer (`M_Product.IsTradeIn='Y'`) with tax
category 1000000 "Ingen moms" is sold under the margin scheme: the customer
price is entered as one price with tax 0, and only the margin carries 25 %
VAT. When the order is **completed**, ADempiere splits the line: the product
line is repriced to the purchase cost and a second line with charge 1000172
("3210 - Försäljning begagnat VMB 25%") carries the margin excl. VAT; both get
`pacKey` = the product's article number. The invoice and the ledger see those
two lines.

Both view pairs fold the charge back onto the product so that the product row
shows the full revenue excl. VAT:

- `mv_powerbi_sales_v5`: pairs the charge's posting with the product's
  posting through the order lines' `pacKey`.
- `mv_powerbi_orders_v1`, completed orders: same pairing on the order lines.
- `mv_powerbi_orders_v1`, **open orders** (not yet split, product line still
  at the gross price): applies the same arithmetic ADempiere will use, with
  PriceLimit as the cost — `unit_ex = (PriceActual + PriceLimit × 0.25) / 1.25`,
  `vmbamt = unit_ex − PriceLimit`. The line view marks these
  `vmbsource = 'estimated'` (vs `'charge'`); the estimate can move a little
  when the order completes.

Consequences: never multiply or divide a trade-in product's amounts by 1.25
yourself; `vmbamt` is the trade-in margin excl. VAT; for a trade-in product
`currentcostprice` on a completed order is the actual purchase cost. Verified
Aug 2026: order view and ledger view agree to the krona on every trade-in row.

## Reading them

```sql
-- sales per day, last 30 days, completed orders only, web vs manual
SELECT orderdate, is_web, sum(linenetamt) AS revenue, sum(contribution) AS contribution, sum(orders) AS orders
FROM adempiere.mv_powerbi_orders_v1
WHERE docstatus = 'CO' AND orderdate >= current_date - 30
GROUP BY 1, 2 ORDER BY 1, 2;

-- what is booked but not yet invoiced (open orders by product)
SELECT artno, descr, sum(qtyordered) AS qty, sum(linenetamt) AS revenue
FROM adempiere.mv_powerbi_orders_v1
WHERE docstatus <> 'CO'
GROUP BY 1, 2 ORDER BY 4 DESC;

-- invoiced sales per month, trade-in share
SELECT date_trunc('month', dateacct)::date AS month, sum(linenetamt) AS revenue,
       sum(linenetamt) FILTER (WHERE istradein = 'Y') AS tradein_revenue
FROM adempiere.mv_powerbi_sales_v5
GROUP BY 1 ORDER BY 1;
```

Line-level detail (order number, line, prices, `vmbsource`, delivery rule,
order method) is in `v_sales_report_order_v1` — filter it by `orderdate` or
`documentno`, never scan it whole from a web request (it is a view over the
raw tables).

Freshness: `mv_powerbi_orders_v1` lags up to an hour behind the order
entry; `mv_powerbi_sales_v5` additionally lags behind invoicing and posting.
Check a refresh with
`SELECT created, errormsg FROM ad_pinstance WHERE ad_process_id IN (1000323, 1000326) ORDER BY created DESC LIMIT 4;`
(1000323 = sales v5, 1000326 = orders v1; `errormsg` holds "refreshed in N s").

## Where the definitions live

DDL, install order and the design notes are in the ADempiere trunk working
copy on Nils' machine, `C:\backupC\workspace\adempiereTrunk\.claude\`:
`scripts/v_sales_report_acct_v5.sql`, `scripts/mv_powerbi_sales_v5.sql`,
`scripts/v_sales_report_order_v1.sql`, `scripts/mv_powerbi_orders_v1.sql`,
the two `refresh_*_scheduler.sql`, and `notes/margin-scheme-sales-views.md`
/ `notes/order-sales-views.md`. A change to a view is made there and applied
to the production database by Nils; this repo only reads.
