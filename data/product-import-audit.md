# Product Import Audit

Individual-product audit of the imported catalogue. Every record imported from the seven
approved sources was re-examined and classified, and only records that are genuinely
individual products appear in the storefront.

Audit performed 2026-09-05. **Gaps are never hidden**: where a source restricts automated access,
or simply does not publish individual products, that is stated plainly instead of being
filled in with invented items.

---

## 1. Classification

| Class | Meaning | Records | Shown as a product? |
|---|---|---:|---|
| `REAL_PRODUCT` | An individually identifiable item published by the source | 4344 | Yes |
| `REAL_VARIANT` | A source-published variant of a real product | 0 | Yes, as a variation of its product |
| `PRODUCT_FAMILY` | A family / range / collection page, not one item | 44 | No - navigation and enquiry only |
| `SERIES_ONLY` | A series or model-group name with no item-level data | 132 | No - navigation and enquiry only |
| `CATEGORY_ONLY` | A category heading | 0 | No - category navigation only |
| `INSUFFICIENT_SOURCE_DATA` | Nothing that identifies an actual product | 1 | No - excluded entirely |

**Records audited: 4521.**

Note on variants: no source record was split into separate pseudo-products. Where a source
publishes options against one product - a fan model with colour and wire-type options, for
example - they are kept as variations of that single product. Across the verified products
there are **477 source-published variants** attached to 104 products.

---

## 2. Final audit report

| # | Measure | Count |
|---:|---|---:|
| 1 | Records in the previous import | 4521 |
| 2 | Confirmed REAL individual products | 4344 |
| 3 | Legitimate variants (source-published, kept on their product) | 477 |
| 4 | Removed as product families | 44 |
| 5 | Removed as series only | 132 |
| 6 | Removed for insufficient source data | 1 |
| 7 | Products with a real source image stored locally | 1929 |
| 8 | Products still without an image | 2415 |
| 9 | Products with an actual source price | 1800 |
| 10 | Products using Request a Quote | 2544 |
| 11 | Real individual products by source | see table below |

Of the 2415 products still without an image, 2378 are Pakistan Cables items, whose source
publishes no product photography at all. Those products show a "Product image unavailable"
state; no substitute image is used.

A further **35** products name an image on their source page whose URL returns no
image: the source answers HTTP 200 with a zero-byte file (verified on
`aquaelectrical.com/wp-content/uploads/MCCB-40A-3Pole.jpg` among others), so there is
no photograph to store. These carry the same "Product image unavailable" state rather
than being given a stand-in picture:

- `aqua-8158` — AQUA MCCB III POLE 40 Amp (aquaelectrical.com)
- `aqua-8112` — AQUA BG D/POLE 30 Amp (aquaelectrical.com)
- `aqua-7763` — Aqua Quad Grey Series Multi Socket Plus Dual USB (aquaelectrical.com)
- `aqua-6881` — Aqua Wifi Smart Grey Multi Function Switch (aquaelectrical.com)
- `aqua-6485` — AQUA DYNAMIC 4 GANG SWITCH PLUS 2 SOCKET (aquaelectrical.com)
- `aqua-6443` — AQUA DYNAMIC DOUBLE DATA SOCKET (aquaelectrical.com)
- `aqua-6437` — AQUA DYNASTY 5 GANG SWITCH (aquaelectrical.com)
- `aqua-6429` — AQUA GLI 10 GANG SWITCH (aquaelectrical.com)
- `aqua-6148` — AQUA GLOW POWER SWITCH 32 AMP (aquaelectrical.com)
- `aqua-6142` — AQUA GLOW 15 AMP SWITCH SOCKET (aquaelectrical.com)
- `aqua-5986` — PARADISE ECO 6 GANG SWITCH PLUS 2 SOCKET (aquaelectrical.com)
- `aqua-5985` — PARADISE ECO 4 GANG SWITCH PLUS 2 SOCKET (aquaelectrical.com)
- `aqua-5983` — PARADISE ECO 10 GANG SWITCH (aquaelectrical.com)
- `aqua-5980` — PARADISE ECO DOUBLE DATA SOCKET (aquaelectrical.com)
- `aqua-5979` — PARADISE ECO SINGLE DATA SOCKET (aquaelectrical.com)
- `aqua-5773` — AQUA EDGE BROWN DOUBLE MULTI PLUS 2 USB (aquaelectrical.com)
- `aqua-5770` — AQUA EDGE BROWN DOUBLE MULTI SOCKET (aquaelectrical.com)
- `aqua-5769` — AQUA EDGE BROWN 9 GANG SWITCH PLUS 1 SOCKET (aquaelectrical.com)
- `aqua-5734` — AQUA EDGE BROWN 2 GANG SWITCH PLUS 1 SOCKET (aquaelectrical.com)
- `aqua-5491` — CENTURY CRYSTAL 1 GANG 2 WAY SWITCH (aquaelectrical.com)
- `aqua-5439` — AQUA SAPPHIRE 4 GANG SWITCH (aquaelectrical.com)
- `aqua-5426` — AQUA SAPPHIRE 3 GANG 1 WAY SWITCH (aquaelectrical.com)
- `aqua-5420` — AQUA BRAVO 4 SWITCH 1 SOCKET 1 DIMMER (aquaelectrical.com)
- `aqua-5415` — AQUA BRAVO DIGITAL DOUBLE USB (aquaelectrical.com)
- `aqua-5411` — AQUA BRAVO FAN DIMMER (aquaelectrical.com)
- `aqua-5404` — AQUA BRAVO BELL PUSH (aquaelectrical.com)
- `aqua-5305` — AQUA GLORY LED NIGHT LIGHT WITH SWITCH (aquaelectrical.com)
- `aqua-5304` — AQUA GLORY POWER PLUG (aquaelectrical.com)
- `aqua-5301` — AQUA GLORY SINGLE TV SOCKET (aquaelectrical.com)
- `aqua-5279` — AQUA GLORY 2 GANG SWITCH (aquaelectrical.com)
- `aqua-5250` — AQUA SINGLE POLE 32 AMP MCB BREAKER (aquaelectrical.com)
- `aqua-5246` — AQUA SINGLE POLE 10 AMP MCB BREAKER (aquaelectrical.com)
- `aqua-4769` — Aqua Icon Brown Single Data Socket (aquaelectrical.com)
- `aqua-4762` — Aqua Icon Brown Single Telephone Socket (aquaelectrical.com)
- `aqua-4756` — Aqua Icon Brown 3 Gang 1 Way Switch (aquaelectrical.com)

### Real individual products by source

| Source | Records imported | REAL products | Families | Series only | Excluded | With image | Priced | Request Quote |
|---|---:|---:|---:|---:|---:|---:|---:|---:|
| Pakistan Cables | 2378 | 2378 | 0 | 0 | 0 | 0 | 0 | 2378 |
| Aqua Electrical | 1641 | 1640 | 0 | 0 | 1 | 1605 | 1632 | 8 |
| Wahid Fans | 104 | 104 | 0 | 0 | 0 | 104 | 104 | 0 |
| Electro Traders | 32 | 0 | 32 | 0 | 0 | 0 | 0 | 0 |
| Himel | 132 | 0 | 0 | 132 | 0 | 0 | 0 | 0 |
| Hyundai / Jubilee Corporation | 8 | 0 | 8 | 0 | 0 | 0 | 0 | 0 |
| Coarts Lighting | 226 | 222 | 4 | 0 | 0 | 220 | 64 | 158 |
| **Total** | **4521** | **4344** | **44** | **132** | **1** | **1929** | **1800** | **2544** |

Genuine source discounts across the verified products: **19**. A discount is only recorded
where the source itself publishes both a regular price and a lower current price.

The product count is lower than the 4521 records previously imported. That is the intended
outcome of the audit: accuracy over catalogue size.

---

## 3. Per-source findings

### Pakistan Cables

**URL:** https://www.pakistancables.com/  
**Platform:** WordPress / WooCommerce  
**Records imported:** 2378 · **verified individual products:** 2378 · **families/series:** 0 · **excluded:** 0

Every record is a distinct published catalogue item with its own product page and its own URL on the source: a specific conductor size, core count, insulation and voltage combination that Pakistan Cables actually lists. No size x core x insulation x voltage combinations were generated; the 2,378 records correspond one-to-one with 2,378 distinct source URLs. The source publishes no product photography and no retail price, so every one of these products carries Request a Quote and an honest "Product image unavailable" state.

- With a stored source image: **0** of 2378
- With a source price: **0** · Request a Quote: **2378**
- With source-published variations: **0**
- With a specification table: **2377**

### Aqua Electrical

**URL:** https://aquaelectrical.com/  
**Platform:** WordPress / WooCommerce  
**Records imported:** 1641 · **verified individual products:** 1640 · **families/series:** 0 · **excluded:** 1

Individual products with their own product page, price, stock state, SKU where published and product photography. One record was a WooCommerce test artefact ("WooCommerce Automated Testing Product") and has been excluded.

- With a stored source image: **1605** of 1640
- With a source price: **1632** · Request a Quote: **8**
- With source-published variations: **0**
- With a specification table: **0**

### Wahid Fans

**URL:** https://wahidfans.com/  
**Platform:** Shopify  
**Records imported:** 104 · **verified individual products:** 104 · **families/series:** 0 · **excluded:** 0

Individual fans, each with the colour / size / wire-type options the source itself publishes as Shopify variants. Options are kept as variations of one product, exactly as the source structures them, and are not split into separate pseudo-products.

- With a stored source image: **104** of 104
- With a source price: **104** · Request a Quote: **0**
- With source-published variations: **104**
- With a specification table: **104**

### Electro Traders

**URL:** https://electrotraders.com.pk/  
**Platform:** Static HTML brochure site  
**Records imported:** 32 · **verified individual products:** 0 · **families/series:** 32 · **excluded:** 0

The source's 32 pages describe product RANGES (for example "Softstarters" and "PSR softstarter - The compact range"), not individual products: no model number, no SKU, no rating, no specification table and no per-item page exists. Individual product catalogue not publicly exposed by approved source. All 32 are therefore listed as product families for navigation and enquiry, and none is shown as a sellable product.

### Himel

**URL:** https://www.himel.com/gb  
**Platform:** JavaScript SPA behind Akamai  
**Records imported:** 132 · **verified individual products:** 0 · **families/series:** 132 · **excluded:** 0

Himel's individual product pages (/gb/product-details/<MODEL>) are protected by Akamai and return an Access Denied page. That protection was NOT circumvented. The accessible category pages expose series and model-group names only, with no item-level specification, image or price, so all 132 records are series entries. Individual product catalogue not publicly exposed by approved source through normal access. No pseudo-products have been created for them.

### Hyundai / Jubilee Corporation

**URL:** https://jubileecorporation.com/hyundai/  
**Platform:** WordPress partner page  
**Records imported:** 8 · **verified individual products:** 0 · **families/series:** 8 · **excluded:** 0

The approved source is a single partner page listing eight product families with stated rating ranges. There are no per-model pages, no specification tables and no downloadable catalogue. Individual product catalogue not publicly exposed by approved source. Hyundai is therefore presented as a brand with eight families for navigation and enquiry - NOT as eight sellable products.

### Coarts Lighting

**URL:** https://www.coartslighting.com/  
**Platform:** Wix  
**Records imported:** 226 · **verified individual products:** 222 · **families/series:** 4 · **excluded:** 0

Individual lighting products with their own product page, photography and, where the source publishes one, a price. Four records name a range rather than an item ("T8 LED Tube PC Range", "T8 LED Tube Glass Range", "F7 Series LED Track Light", "F2 Series LED Track Light"): each has its own page and a photograph, but no price, no model number, no SKU and no specification behind it, so they are listed as families rather than sold as products.

- With a stored source image: **220** of 222
- With a source price: **64** · Request a Quote: **158**
- With source-published variations: **0**
- With a specification table: **0**

---

## 4. Rules applied

- A record became a product only if the source publishes it as an individual item.
- No price was invented, estimated, or copied from another site. Where a source publishes
  no price, the product is Request a Quote; Rs. 0 and Rs. 1 placeholders are never shown.
  Pakistan Cables' Store API returns `price: "1"`, which the live page confirms is a
  WooCommerce placeholder rather than a retail price, so it is discarded on import.
- No image was invented. Only the image the product's own source published for it is used,
  downloaded locally rather than hotlinked. Where none exists the storefront says
  "Product image unavailable" - no stock photograph, category icon, generated render or
  another retailer's picture is substituted.
- No specification row is shown unless the source publishes that value.
- No ratings or reviews exist anywhere in the storefront: no approved source publishes them.
- No website protection was bypassed, and no data was taken from any source outside the
  seven approved ones.
- No dealership, distribution, authorisation or partnership relationship is claimed for any
  brand anywhere on the site.
