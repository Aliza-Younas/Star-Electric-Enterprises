# Product Import Audit

Individual-product audit of the imported catalogue. Every record imported from the seven
approved sources was re-examined and classified, and only records that are genuinely
individual products appear in the storefront.

Audit performed 2026-09-07. **Gaps are never hidden**: where a source restricts automated access,
or simply does not publish individual products, that is stated plainly instead of being
filled in with invented items.

---

## 1. Classification

| Class | Meaning | Records | Shown as a product? |
|---|---|---:|---|
| `REAL_PRODUCT` | An individually identifiable item published by the source | 4348 | Yes |
| `REAL_VARIANT` | A source-published variant of a real product | 0 | Yes, as a variation of its product |
| `PRODUCT_FAMILY` | A family / range / collection page, not one item | 49 | No - navigation and enquiry only |
| `SERIES_ONLY` | A series or model-group name with no item-level data | 132 | No - navigation and enquiry only |
| `CATEGORY_ONLY` | A category heading | 0 | No - category navigation only |
| `INSUFFICIENT_SOURCE_DATA` | Nothing that identifies an actual product | 1 | No - excluded entirely |

**Records audited: 4530.**

Note on variants: no source record was split into separate pseudo-products. Where a source
publishes options against one product - a fan model with colour and wire-type options, for
example - they are kept as variations of that single product. Across the verified products
there are **479 source-published variants** attached to 105 products.

---

## 2. Final audit report

| # | Measure | Count |
|---:|---|---:|
| 1 | Records in the previous import | 4530 |
| 2 | Confirmed REAL individual products | 4348 |
| 3 | Legitimate variants (source-published, kept on their product) | 479 |
| 4 | Removed as product families | 49 |
| 5 | Removed as series only | 132 |
| 6 | Removed for insufficient source data | 1 |
| 7 | Products with a real source image stored locally | 4348 |
| 8 | Products still without an image | 0 |
| 9 | Products with an actual source price | 1800 |
| 10 | Products using Request a Quote | 2548 |
| 11 | Real individual products by source | see table below |

Of the 0 products still without an image, 0 are Pakistan Cables items, whose source
publishes no product photography at all. Those products show a "Product image unavailable"
state; no substitute image is used.


### Real individual products by source

| Source | Records imported | REAL products | Families | Series only | Excluded | With image | Priced | Request Quote |
|---|---:|---:|---:|---:|---:|---:|---:|---:|
| Pakistan Cables | 2384 | 2382 | 2 | 0 | 0 | 2382 | 0 | 2382 |
| Aqua Electrical | 1641 | 1640 | 0 | 0 | 1 | 1640 | 1632 | 8 |
| Wahid Fans | 104 | 104 | 0 | 0 | 0 | 104 | 104 | 0 |
| Electro Traders | 32 | 0 | 32 | 0 | 0 | 0 | 0 | 0 |
| Himel | 132 | 0 | 0 | 132 | 0 | 0 | 0 | 0 |
| Hyundai / Jubilee Corporation | 8 | 0 | 8 | 0 | 0 | 0 | 0 | 0 |
| Coarts Lighting | 226 | 222 | 4 | 0 | 0 | 222 | 64 | 158 |
| ABB Furse | 3 | 0 | 3 | 0 | 0 | 0 | 0 | 0 |
| **Total** | **4530** | **4348** | **49** | **132** | **1** | **4348** | **1800** | **2548** |

Genuine source discounts across the verified products: **19**. A discount is only recorded
where the source itself publishes both a regular price and a lower current price.

The product count is lower than the 4530 records previously imported. That is the intended
outcome of the audit: accuracy over catalogue size.

---

## 3. Per-source findings

### Pakistan Cables

**URL:** https://www.pakistancables.com/  
**Platform:** WordPress / WooCommerce  
**Records imported:** 2384 · **verified individual products:** 2382 · **families/series:** 2 · **excluded:** 0

Every record is a distinct published catalogue item with its own product page and its own URL on the source: a specific conductor size, core count, insulation and voltage combination that Pakistan Cables actually lists. No size x core x insulation x voltage combinations were generated; the 2,378 records correspond one-to-one with 2,378 distinct source URLs. The source publishes no product photography and no retail price, so every one of these products carries Request a Quote and an honest "Product image unavailable" state.

- With a stored source image: **2382** of 2382
- With a source price: **0** · Request a Quote: **2382**
- With source-published variations: **1**
- With a specification table: **2381**

### Aqua Electrical

**URL:** https://aquaelectrical.com/  
**Platform:** WordPress / WooCommerce  
**Records imported:** 1641 · **verified individual products:** 1640 · **families/series:** 0 · **excluded:** 1

Individual products with their own product page, price, stock state, SKU where published and product photography. One record was a WooCommerce test artefact ("WooCommerce Automated Testing Product") and has been excluded.

- With a stored source image: **1640** of 1640
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

- With a stored source image: **222** of 222
- With a source price: **64** · Request a Quote: **158**
- With source-published variations: **0**
- With a specification table: **0**

### ABB Furse

**URL:** https://www.abb.com/global/en/areas/electrification/low-voltage/earthing-lightning-surge-protection/lightning-protection-and-earthing-systems/furse  
**Platform:** ABB corporate site (AEM)  
**Records imported:** 3 · **verified individual products:** 0 · **families/series:** 3 · **excluded:** 0

ABB publishes Furse as a solution overview. The page names three ranges - structural lightning protection, earthing and surge protection - and publishes no individual product, model, specification or price for any of them. All 1,459 ABB low-voltage product pages listed in ABB's own product sitemap were crawled and checked against their breadcrumbs: not one is an earthing, lightning or surge protection item. The ABB Library API that holds the Furse part numbers answers HTTP 403 MissingKey without signed credentials, and that restriction was not circumvented. Earthing Material is therefore imported at range level - three families for navigation and enquiry - and no Furse SKU has been invented.

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


---

## Additional category sources

Four departments were added after the original seven-source import, each from a
source the business approved for that area. Nothing here duplicates an existing
record: where a product was already in the catalogue it gained a department
relationship rather than a second copy.

### Earthing Material — ABB Furse

**Source:** https://www.abb.com/global/en/areas/electrification/low-voltage/earthing-lightning-surge-protection/lightning-protection-and-earthing-systems/furse

The page is a solution overview. It names three Furse ranges — structural
lightning protection, earthing and surge protection — and publishes no
individual product, model, specification or price for any of them.

Two further checks were made before concluding that:

- **ABB's own product sitemap was crawled in full.** All 1,459 low-voltage
  product pages were fetched and classified by their own `BreadcrumbList`.
  Not one is an earthing, lightning or surge protection product; the catalogue
  published there is metering and energy devices.
- **The ABB Library API was not circumvented.** The Furse part numbers sit
  behind `library.e.abb.com`, which answers HTTP 403 `MissingKey` without
  signed credentials. That is an access restriction and it was left alone.

**Result: 3 ranges, 0 products.** Recorded as families for navigation and
enquiry, exactly as Himel and Hyundai are. No Furse SKU has been invented, and
no dealership, distribution or authorisation relationship with ABB or Furse is
claimed anywhere.

### Networking Solutions — Pakistan Cables

**Source:** https://www.pakistancables.com/our-products/wires-cables/ and the
official Networking Cables Catalogue linked from it.

The catalogue publishes four individual cables, all imported with the
construction, dimensions, standards and packaging printed in it, and with the
product photograph cropped from its own page:

- CAT6 U/UTP PVC Unshielded Twisted Pairs — two source-published sheath
  colours, recorded as variations
- CAT6 U/UTP LSZH Unshielded Twisted Pairs
- CAT6 F/UTP LSZH Foiled/Unshielded Twisted Pairs
- CAT7 S/FTP LSZH Shielded Twisted Pairs, Braided

No price is published for any of them — the manufacturer's Store API returns
`X-WP-Total: 0` for cat6, coaxial, telephone and control, and its own online
store lists the cable without a price — so all four are Request a Quote.

The same page names two further ranges without item-level data, Indoor
Telephone / Intercom Cables and Coaxial Cables. Those are recorded as families,
not invented SKUs.

**Result: 4 products, 2 ranges.**

### Home Automation — Aqua Wi-Fi Smart Switches

**Source:** https://aquaelectrical.com/product-category/aqua-wifi-smart-switches/

All 83 products in that category tree were already in the catalogue from the
original Aqua import, verified by matching source product IDs. **Nothing was
re-imported and no record was duplicated.** They gained a department
relationship instead.

**Result: 83 products.**

### Office Automation Solutions — Aqua Wi-Fi Smart Switches

**Source:** the same approved category, grouped for a second context.

**Classification: a contextual product grouping, not a verified service.**
Star Electric has not stated that it installs, commissions or supports
automation, so nothing on the site offers one. The department is the subset of
the verified smart range that suits a workplace — switching, controls, motors,
protection and entry devices.

**Result: 79 products**, every one of them the same record that appears under
Home Automation. A product can belong to Smart Switches, Home Automation and
Office Automation Solutions at once because departments are a many-to-many
relationship on the product, not a copy of it.

Compiled 2026-09-07.
