# Research Progress Log

Live working log for the catalogue build. Updated as work proceeds so progress
is never lost mid-task.

Legend: `[x]` done · `[~]` in progress · `[ ]` not started · `[!]` blocked

---

## Phase 0 — Architecture & reconnaissance

- [x] Read CLAUDE.md and confirm project rules
- [x] Inspect current data architecture (`js/data.js`, 27 demo products)
- [x] Connectivity test — all 7 sources return HTTP 200
- [x] `robots.txt` retrieved and reviewed for all 7 hosts
- [x] Platform detection for all 7 sources
- [x] Structured-endpoint discovery (Store API / products.json / sitemaps)
- [x] Product counts established per source
- [x] Pricing model verified per source (incl. Pakistan Cables quote behaviour)
- [x] Himel access restriction identified and respected
- [x] `data/product-import-plan.md` written
- [x] `data/research-progress.md` written
- [x] `data/product-import-audit.md` written
- [x] Normalised schema established

---

## Phase 1 — Pakistan Cables

- [x] Navigation / platform mapped (WooCommerce)
- [x] Store API confirmed, `X-WP-Total: 2378`
- [x] Pricing model verified — "Request for quote", price `1` is a placeholder
- [x] Attribute model captured (7 attributes)
- [x] Confirmed: no product images on source, single `Uncategorized` term
- [x] All 2,378 products retrieved (24 API pages)
- [x] Normalised to schema, all Request Quote
- [x] Categories derived from the 7 source attributes
- [x] Validated + written to data/products/pakistan-cables.json
- [x] Individual-product audit — 2,378 distinct source URLs, one per product

## Phase 2 — Aqua Electrical

- [x] Platform confirmed (WooCommerce, `?rest_route=`)
- [x] `X-WP-Total: 1641`
- [x] 88 categories retrieved with parent/child relationships
- [x] All 1,641 products retrieved (17 API pages)
- [x] Categories resolved through parent chain (155 Panasonic)
- [x] Images downloaded locally, resized and converted to WebP
- [x] Normalised + validated
- [x] Individual-product audit — 1 WooCommerce test artefact excluded

## Phase 3 — Wahid Fans

- [x] Shopify `products.json` confirmed
- [x] Total confirmed (104; page 2 empty)
- [x] 104 products with real Shopify variants retrieved
- [x] Images downloaded locally
- [x] Normalised + validated
- [x] Individual-product audit — colour / size / wire-type options kept as
      variations of one product, exactly as the source publishes them

## Phase 4 — Electro Traders

- [x] Static site confirmed; no API/sitemap
- [x] `/shop_products` mapped → ids 2–34
- [x] 6 brand pages identified
- [x] 32 product pages crawled
- [x] Brand pages crawled (all six return an identical list - recorded)
- [x] Normalised + validated
- [!] Individual-product audit — **all 32 pages describe product ranges, not
      individual products.** Individual product catalogue not publicly exposed
      by approved source. Listed as families for navigation only; no
      pseudo-products created.

## Phase 5 — Himel  (PARTIAL-BLOCKED)

- [x] Sitemap retrieved (1,497 URLs; 479 for `/gb`)
- [x] 132 product model codes extracted
- [x] Category pages confirmed accessible
- [!] `/gb/product-details/*` blocked by Akamai — **not bypassed by design**
- [x] Category hierarchy extracted from accessible navigation
- [x] 132 family/model records built
- [x] Normalised + validated, marked PARTIAL-BLOCKED
- [!] Individual-product audit — **series/model-group entries only.** Individual
      product catalogue not publicly exposed by approved source through normal
      access. Listed as series for navigation only; **no 132 pseudo-products
      created.**

## Phase 6 — Hyundai / Jubilee

- [x] `/hyundai/` retrieved and analysed
- [x] Confirmed: single page, no per-product pages, no PDFs
- [x] 8 product families + stated rating ranges extracted
- [x] Normalised + validated
- [!] Individual-product audit — **8 families, not 8 products.** Individual
      product catalogue not publicly exposed by approved source. Hyundai is
      presented as a brand with eight families for navigation and enquiry.

## Phase 7 — Coarts Lighting

- [x] Wix store sitemap retrieved → 229 product URLs
- [x] 226 of 229 product pages crawled
- [x] Images downloaded locally
- [x] Normalised + validated
- [x] Individual-product audit — 221 individual products; 4 range entries moved
      to families; 1 record with no identifying product data excluded

---

## Phase 8 — Normalisation & dedupe

- [x] Category tree built from real source categories
- [x] Brands file built, with real-product counts and family-only counts
- [x] Duplicate detection by product id + slug uniqueness
- [x] Slug uniqueness enforced

## Phase 9 — Individual-product audit

- [x] All 4,521 imported records classified
- [x] Classification written back onto every record (`_class`, `_classReason`)
- [x] Storefront index rebuilt from verified individual products only
- [x] Families and series moved to `data/families.js`, navigation only
- [x] `data/product-import-audit.md` regenerated from the data

## Phase 10 — Images

- [x] Real source images downloaded for every verified product whose source
      publishes one
- [x] Resized to 800px on the long edge and converted to WebP
- [x] Duplicate files removed (sources repeat the featured image in galleries)
- [x] `assets/product-image-sources.md` generated with full traceability
- [x] Line-art fallback removed — products with no source image now show
      "Product image unavailable"

## Phase 11 — Frontend rebuild

- [x] Shop, category, search, deals listings driven by verified products only
- [x] Brand directory splits shoppable brands from family-only brands
- [x] Category pages show family ranges separately from products
- [x] Product page gallery shows stored source images only; never hotlinked
- [x] Quote request prefills from a product or a family
- [x] Star ratings removed from the codebase entirely

## Phase 12 — QA

- [x] Data quality checks pass
- [x] Responsive check at the target breakpoints
- [x] Broken link / asset check
- [x] Exhaustiveness audit reconciled

## Phase 13 — Additional category sources

- [x] ABB Furse page retrieved and analysed (three named ranges, no item-level data)
- [x] ABB product sitemap crawled in full — 1,459 low-voltage product pages
      checked against their own breadcrumbs, 0 earthing/lightning/surge items
- [!] ABB Library API requires signed credentials (HTTP 403 `MissingKey`) —
      **not circumvented by design**; Furse part numbers are not publicly reachable
- [x] Earthing Material created as 3 range records, no invented SKUs
- [x] Pakistan Cables Networking Cables Catalogue read; 4 individual cables imported
      with construction, dimensions, standards, packaging and catalogue photographs
- [x] Networking price check — Store API returns 0 results for cat6/coaxial/telephone/
      control and the online store shows no price, so all four are Request a Quote
- [x] Telephone/Intercom and Coaxial recorded as ranges, not products
- [x] Aqua smart tree audited — all 83 products already present, none duplicated
- [x] Home Automation and Office Automation Solutions added as many-to-many
      department relationships on the existing records
- [x] Office Automation Solutions documented as a product grouping, not a service
- [x] Departments wired into the rail, circular strip, product rails, category
      pages (`?dept=`) and search, including range results for range-only queries
- [x] Furse Surge Protection range image closed. ABB serves no reachable image for
      it (client-rendered page, no surge SKU in either product sitemap, library
      widget returns nothing, signed library **not** circumvented) and every clean
      openly-licensed SPD photograph carries a rival's logo, so the range now uses
      ABB's own OVR T1 surge protective device as a labelled representative image
- [!] Pakistan Cables **Coaxial Cables** and **Indoor Telephone / Intercom Cables**
      ranges still have no image — the manufacturer photographs neither in any of
      its published catalogues
