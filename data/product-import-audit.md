# Product Import Audit

Per-source completeness and traceability record. Counts are filled in as each
phase completes. **Gaps are never hidden** — where a source restricts automated
access, or simply does not publish a value, that is stated plainly.

Reconnaissance performed 2026-09-05.

---

## Summary

| Source | Discovered | Imported | Priced | Request Quote | Status |
|---|---|---|---|---|---|
| Pakistan Cables | 2,378 | 0 | — | — | IN PROGRESS |
| Aqua Electrical | 1,641 | 0 | — | — | IN PROGRESS |
| Wahid Fans | 104 | 0 | — | — | IN PROGRESS |
| Electro Traders | 32 | 0 | — | — | IN PROGRESS |
| Himel | 132 models | 0 | — | — | PARTIAL-BLOCKED |
| Hyundai / Jubilee | 8 families | 0 | — | — | IN PROGRESS |
| Coarts Lighting | 229 | 0 | — | — | IN PROGRESS |

---

## 1. Pakistan Cables

**URL:** https://www.pakistancables.com/
**Platform:** WordPress / WooCommerce
**Access method:** WooCommerce Store API (public, read-only)

- Categories discovered: **1** (`Uncategorized` — the source does not use the
  product-category taxonomy; structure is carried by attributes instead)
- Attributes discovered: **7** — Conductor, Conductor CSA, Conductor Shape,
  Cores, Insulation Material, Sheath/Jacket Material, Voltage
- Product pages discovered: **2,378** (`X-WP-Total`)
- Products imported: _pending_
- Products with prices: **0 expected**
- Products with Request Quote: **2,378 expected**
- Products with variations: _pending_
- Products with images: **0** — source product pages carry no product photography
- Pages crawled: _pending_
- Known inaccessible pages: none
- Status: **IN PROGRESS**

**Notes.** The Store API reports `price: "1"` / `regular_price: "1"`. This is a
WooCommerce placeholder, not a retail price: the live product page renders a
**"Request for quote"** button, contains no price element and offers no
add-to-cart. Verified on `/product/1c-25-mm2-tcu-xlpoxlpo-1515-kv-2/`. The
sentinel value is discarded on import — every product is stored as
`price: null`, `priceType: "quote"`. No Pakistan Cables price will be sourced
from anywhere else.

---

## 2. Aqua Electrical

**URL:** https://aquaelectrical.com/
**Platform:** WordPress / WooCommerce + Elementor
**Access method:** WooCommerce Store API via `?rest_route=` (pretty REST
permalinks are disabled on this host)

- Categories discovered: **88** (with parent/child nesting)
- Largest categories: Small Switches (548), Big Switches (436), Panasonic (155),
  Simplus (121), Tempered Glass Switches (113), Led Light (106), Accessories
  (82), Aqua WiFi Smart Switches (76), Circuit Breaker (54)
- Product pages discovered: **1,641** (`X-WP-Total`)
- Products imported: _pending_
- Products with prices: _pending_ (source publishes real retail pricing)
- Products with Request Quote: _pending_
- Products with variations: _pending_
- Products with images: _pending_
- Pages crawled: _pending_
- Known inaccessible pages: none
- Status: **IN PROGRESS**

**Notes.** `robots.txt` returns 404 (no restrictions published). Richest source
for pricing, sale prices, stock state and product imagery.

---

## 3. Wahid Fans

**URL:** https://wahidfans.com/
**Platform:** Shopify
**Access method:** `products.json` (Shopify's public product feed)

- Collections/types discovered: _pending_
- Product pages discovered: **104** (page 1 = 104, page 2 = 0 → complete)
- Products imported: _pending_
- Products with prices: _pending_
- Products with variations: _pending_ (real Shopify variants, e.g. Face Decor,
  Wire Type)
- Products with images: _pending_
- Pages crawled: _pending_
- Known inaccessible pages: none
- Status: **IN PROGRESS**

**Notes.** `robots.txt` disallows only cart/checkout/account/admin paths;
`products.json` is not restricted. Variant-level price, `compare_at_price`, SKU
and availability are all published.

---

## 4. Electro Traders

**URL:** https://electrotraders.com.pk/
**Platform:** Static HTML (Bootstrap "JANGO" theme) — not an ecommerce platform
**Access method:** direct HTML crawl

- Brand/category pages discovered: **6** — ABB, Omron, Phoenix, Schneider,
  Siemens, Weidmuller
- Product pages discovered: **32** (`/single_product/2` … `/single_product/34`)
- Products imported: _pending_
- Products with prices: _pending_
- Products with Request Quote: _pending_
- Products with images: _pending_
- Pages crawled: _pending_
- Known inaccessible pages: none
- Status: **IN PROGRESS**

**Notes.** No `robots.txt`, no sitemap, no API. This is a distributor brochure
site rather than a webshop, so pricing may well be absent — in which case
products are stored as Request Quote rather than given invented prices.

---

## 5. Himel

**URL:** https://www.himel.com/gb
**Platform:** JavaScript SPA behind Akamai
**Access method:** public `Sitemap.xml` + category pages

- Sitemap URLs: **1,497** total, **479** for the `/gb` locale
- Category sections discovered: **15+** — Final Distribution (15 URLs),
  Control Components (11), Residential (11), Commercial & Industrial (8),
  Home Electric (7), Low Voltage Distribution (6), Motor Management (6),
  Power Factor Correction (5), Metering (4), Voltage Stabilizers (3)
- Product model codes discovered: **132** (from sitemap `/gb/product-details/*`)
- Products imported: _pending_
- Products with prices: **0 expected** — Himel does not publish Pakistan retail
  pricing; all will be Request Quote
- Products with images: _pending_
- Pages crawled: _pending_

### Known inaccessible pages

**All `/gb/product-details/<MODEL>` pages** — 132 URLs. Both `curl` and headless
Chrome receive an Akamai block page:

```
Access Denied
You don't have permission to access
"http://www.himel.com/gb/product-details/HDW6" on this server.
Reference #18… https://errors.edgesuite.net/…
```

This protection was **not circumvented**, in line with the project rules.
Category pages (`/gb/final-distribution`, `/gb/low-voltage-distribution`, …)
return 200 normally and expose the family hierarchy with model codes, so those
are used instead.

- Status: **PARTIAL-BLOCKED**

**Notes.** Himel is imported at family/model level: model code, product family,
category path and source URL — all from accessible pages. Detailed
specifications, images and datasheets from the blocked detail pages are recorded
as unavailable. **Completeness of Himel specifications cannot be independently
guaranteed** and nothing has been substituted from other websites.

---

## 6. Hyundai / Jubilee Corporation

**URL:** https://jubileecorporation.com/hyundai/
**Platform:** WordPress (Yoast sitemaps)
**Access method:** direct page fetch

- Hyundai pages in sitemap: **1** (the partner page itself)
- Product families discovered: **8**
  - Air Circuit Breakers — UAN-Series, 630 A to 6300 A
  - Moulded Case Circuit Breakers
  - Miniature Circuit Breaker — Hi-Series, 6 A to 63 A
  - Motor Protection Circuit Breaker — HM-Series, 0.63 A to 80 A
  - Residual Current Devices
  - Magnetic Contactors
  - Vacuum Circuit Breakers — 12 kV to 15 kV
  - Industrial Inverters (Variable Frequency Drives)
- Individual product pages discovered: **0**
- Downloadable catalogues/PDFs discovered: **0**
- Products imported: _pending_
- Products with prices: **0 expected**
- Status: **IN PROGRESS**

**Notes.** This source publishes a partner overview page only — there are no
per-model pages, no specification tables and no downloadable catalogue linked
from it. Import is therefore limited to the 8 families and exactly the rating
ranges the page states. Individual Hyundai SKUs are **not** fabricated, and no
Hyundai data is taken from other websites. **Completeness cannot be
independently guaranteed** because the source does not publish a full model list.

---

## 7. Coarts Lighting

**URL:** https://www.coartslighting.com/
**Platform:** Wix
**Access method:** `store-products-sitemap.xml` + product pages

- Sitemaps discovered: 4 (blog posts, blog categories, store products, pages)
- Product pages discovered: **229** (`/product-page/<slug>`)
- Products imported: _pending_
- Products with prices: _pending_
- Products with variations: _pending_
- Products with images: _pending_
- Pages crawled: _pending_
- Known inaccessible pages: none so far
- Status: **IN PROGRESS**

**Notes.** `robots.txt` permits crawling for a standard user agent; `PetalBot` is
banned and `dotbot`/`AhrefsBot` are rate-limited, none of which applies here.
`?lightbox=` URLs are disallowed and will not be requested.

---

## Exhaustiveness statement

For each source the "discovered" figure is derived from the source's own
authoritative count where one exists — `X-WP-Total` for the two WooCommerce
sites, an exhausted `products.json` page walk for Shopify, and sitemap entry
counts for Coarts and Himel. Where no reliable total can be derived from the
source, that is stated rather than estimated.

Two sources cannot be certified complete, and are labelled as such:

- **Himel** — detail pages blocked by Akamai; PARTIAL-BLOCKED.
- **Hyundai / Jubilee** — the source publishes no full model list;
  completeness cannot be independently guaranteed.
