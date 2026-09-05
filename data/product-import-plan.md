# Product Import Plan — Star Electric Enterprises

Phase 0 output. Architecture, access strategy and normalised schema for the
catalogue build. Written after a live reconnaissance pass over all seven
approved sources on 2026-09-05.

---

## 1. Approved sources — nothing else may supply product data

| # | Source | Domain | Platform | Access | Products discovered |
|---|---|---|---|---|---|
| 1 | Pakistan Cables | `pakistancables.com` | WooCommerce | ✅ Store API | **2,378** |
| 2 | Aqua Electrical | `aquaelectrical.com` | WooCommerce + Elementor | ✅ Store API (`?rest_route=`) | **1,641** |
| 3 | Wahid Fans | `wahidfans.com` | Shopify | ✅ `products.json` | **104** |
| 4 | Electro Traders | `electrotraders.com.pk` | Static HTML (Bootstrap) | ✅ HTML crawl | **32** |
| 5 | Himel | `himel.com` | SPA behind Akamai | ⚠️ **Partial** | 132 model codes |
| 6 | Hyundai / Jubilee | `jubileecorporation.com/hyundai/` | WordPress, single page | ✅ (source is limited) | 8 families |
| 7 | Coarts Lighting | `coartslighting.com` | Wix | ✅ Sitemap + product pages | **229** |

**Total addressable ≈ 4,524 catalogue entries.**

---

## 2. Access strategy per source

### 2.1 Pakistan Cables — WooCommerce Store API
```
https://www.pakistancables.com/wp-json/wc/store/v1/products?per_page=100&page=N
```
- `X-WP-Total: 2378`. Public, unauthenticated, documented read-only endpoint.
- `robots.txt` permits it (only `/wp-admin/`, cart and wc-logs are disallowed).

**Critical pricing finding.** The API reports `price: "1"`, `regular_price: "1"`
for products. This is **not a real price** — it is a WooCommerce placeholder so
the product record validates. The live product page shows a **"Request for
quote"** button, no price element (`woocommerce-Price-amount` count = 0) and no
add-to-cart. Verified on
`/product/1c-25-mm2-tcu-xlpoxlpo-1515-kv-2/`.

→ Every Pakistan Cables product is imported as
`price: null`, `priceType: "quote"`, `priceStatus: "Request Quote"`.
The sentinel `1` is discarded, never displayed.

**Other findings:** all 2,378 products sit in a single `Uncategorized` term, and
product pages carry **no product photography**. The real structure lives in
seven attributes: Conductor, Conductor CSA, Conductor Shape, Cores, Insulation
Material, Sheath/Jacket Material, Voltage. Categories for this source will be
derived from those attributes, not from the (empty) WooCommerce taxonomy.

### 2.2 Aqua Electrical — WooCommerce Store API
```
https://aquaelectrical.com/?rest_route=/wc/store/v1/products&per_page=100&page=N
```
- Pretty permalinks are off for REST; the `?rest_route=` form works.
- `X-WP-Total: 1641`, **88 categories** with real parent/child nesting.
- Carries genuine retail pricing, sale/regular prices, stock state and images.

### 2.3 Wahid Fans — Shopify
```
https://wahidfans.com/products.json?limit=250&page=N
```
- Page 1 → 104 products, page 2 → 0. Catalogue complete at **104**.
- Real variants with per-variant `price`, `compare_at_price`, `sku`,
  `available`; options such as Face Decor and Wire Type.
- `robots.txt` disallows only cart/checkout/account paths.

### 2.4 Electro Traders — static HTML
- `/shop_products` lists **32** products at `/single_product/<id>` (ids 2–34).
- Six brand pages: `/products/{abb,omron,phoenix,schneider,siemens,weidmuller}`.
- No sitemap, no robots.txt, no API. Crawl the 32 product pages plus the brand
  pages directly.

### 2.5 Himel — PARTIAL-BLOCKED
- `https://www.himel.com/Sitemap.xml` → 1,497 URLs; 479 for the `/gb` locale.
- **Category pages are accessible** (`/gb/final-distribution`,
  `/gb/low-voltage-distribution`, etc. all return 200) and expose the full
  family hierarchy with model codes grouped under headings.
- **`/gb/product-details/<MODEL>` pages return Akamai "Access Denied"** to
  automated requests, both via curl and headless Chrome.

Per the brief, anti-bot protection **will not be circumvented**. Himel is
therefore imported at family/model level from the sitemap and the accessible
category pages: model code, family, category path and source URL. Detailed
specifications are recorded as unavailable and the source is marked
`PARTIAL-BLOCKED` in the audit. No Himel specification will be invented or
taken from another site.

### 2.6 Hyundai / Jubilee Corporation
- `https://jubileecorporation.com/hyundai/` is a **single partner page**. There
  are no per-product pages, no linked PDFs and no sub-pages (`page-sitemap.xml`
  contains exactly one Hyundai URL).
- The page names **8 product families**, some with rating ranges:
  Air Circuit Breakers (UAN-Series, 630A–6300A) · Moulded Case Circuit Breakers ·
  Miniature Circuit Breaker (Hi-Series, 6A–63A) · Motor Protection Circuit
  Breaker (HM-Series, 0.63A–80A) · Residual Current Devices · Magnetic
  Contactors · Vacuum Circuit Breakers (12kV–15kV) · Industrial Inverters (VFDs).

→ Imported as 8 family entries with exactly the ratings the page states.
No individual SKUs will be fabricated, because the source publishes none.

### 2.7 Coarts Lighting — Wix
- `https://www.coartslighting.com/store-products-sitemap.xml` → **229** product
  pages under `/product-page/<slug>`.
- `robots.txt` allows crawling for a normal user agent (only `?lightbox=` and
  some internal Wix paths are disallowed; `PetalBot` is banned, we are not it).

---

## 3. Crawl etiquette

- Sequential requests, ~1–1.5 s apart per host; no parallel hammering.
- Honest descriptive user agent.
- `robots.txt` respected for every host.
- Retry with backoff on 429/5xx; never retry aggressively.
- No CAPTCHA solving, no authentication bypass, no anti-bot evasion.
- Public read-only JSON endpoints preferred over HTML scraping wherever the
  platform provides them — fewer requests and more accurate data.

---

## 4. Data architecture

```
data/
├── products/
│   ├── pakistan-cables.json
│   ├── aqua-electrical.json
│   ├── wahid-fans.json
│   ├── electro-traders.json
│   ├── himel.json
│   ├── hyundai.json
│   └── coarts-lighting.json
├── categories.json          normalised Star Electric category tree
├── brands.json              brands actually represented in the catalogue
├── catalogue-index.json     lightweight index the frontend loads first
├── product-import-plan.md   this file
├── research-progress.md     live progress log
└── product-import-audit.md  per-source completeness audit
```

Pricing is kept in its own nested object with its own `checkedAt` timestamp so a
future price refresh can rewrite `pricing` alone without touching the rest of
the product record.

---

## 5. Normalised product schema

```jsonc
{
  "id": "aqua-1234",                  // <source-key>-<source id>
  "slug": "aqua-simplus-2-gang-switch",
  "brand": "Aqua",
  "manufacturer": "Aqua Electrical",

  "name": "…",
  "model": null,
  "sku": null,

  "source": {
    "website": "Aqua Electrical",
    "domain": "aquaelectrical.com",
    "url": "https://aquaelectrical.com/product/…",
    "checkedAt": "2026-09-05T00:00:00Z"
  },

  "category": "switches-sockets",     // normalised Star Electric category
  "subcategory": "modular-switches",
  "sourceCategories": ["Small Switches", "Simplus"],   // verbatim from source
  "series": "Simplus",

  "pricing": {
    "currency": "PKR",
    "price": 450,
    "regularPrice": 600,
    "salePrice": 450,
    "discountPercent": 25,
    "priceType": "fixed",             // fixed | from | variation | quote | unavailable
    "priceStatus": null,              // "Request Quote" when priceType === "quote"
    "checkedAt": "2026-09-05T00:00:00Z"
  },

  "availability": "In Stock",         // or "Out of Stock" / "Not specified"

  "images": { "featured": "assets/images/products/aqua/….webp", "gallery": [] },

  "variations": [
    { "name": "Light Wood / Copper", "options": {"Face Decor": "Light Wood"},
      "sku": "ace-series-01", "price": 17545, "regularPrice": null,
      "availability": "In Stock", "image": null }
  ],

  "specifications": { "Conductor": "TCU", "Cores": "1" },   // source terminology
  "features": [],
  "shortDescription": "",             // original concise factual summary
  "catalogueUrl": null,
  "datasheetUrl": null,
  "importNotes": []                   // e.g. "detail page blocked by source"
}
```

### Rules baked into the importer

- `priceType: "quote"` ⇒ `price`, `regularPrice`, `salePrice` all `null`, and the
  UI must render **Request a Quote**, never `Rs. 0`.
- `discountPercent` is only set when the source supplies both a regular and a
  lower sale price; it is computed as
  `round(((regular - sale) / regular) * 100)`. Never invented.
- `availability` defaults to `"Not specified"` unless the source states it.
- A product with no `source.url` is invalid and is rejected by validation.
- Specifications keep the source's own terminology and values verbatim; only
  marketing prose is rewritten into a short original summary.
- Per-product-type specification fields (cables vs breakers vs fans vs lighting)
  are kept as-is rather than forced into one shared shape.

---

## 6. Phases

| Phase | Scope | Status |
|---|---|---|
| 0 | Architecture, access strategy, schema | **complete** |
| 1 | Pakistan Cables (2,378) | pending |
| 2 | Aqua Electrical (1,641) | pending |
| 3 | Wahid Fans (104) | pending |
| 4 | Electro Traders (32) | pending |
| 5 | Himel (132, partial) | pending |
| 6 | Hyundai / Jubilee (8 families) | pending |
| 7 | Coarts Lighting (229) | pending |
| 8 | Category normalisation + dedupe | pending |
| 9 | Frontend integration | pending |
| 10 | Full QA / catalogue audit | pending |

Each completed and validated phase is committed and pushed separately so
progress is never lost.

---

## 7. Commercial position

Listing a brand's products **does not** imply Star Electric Enterprises is an
authorised distributor, official dealer, exclusive dealer, certified partner or
importer of that brand. No such claim appears anywhere in the imported data or
the storefront, and none may be added without the owner supplying it.

Image rights are tracked separately in `assets/product-image-sources.md`; source
imagery is retained with full traceability and is marked
`permission-to-confirm-before-production`.
