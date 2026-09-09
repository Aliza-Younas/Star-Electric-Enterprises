# Migration audit — Star Electric Enterprises

Reconciliation of the WordPress destination against the approved catalogue
payload. Every figure is produced by `Star_Electric_Importer::audit()`, which
counts both sides from the same definition so a mismatch names a real
difference rather than two ways of counting.

Run it from **Star Electric → Run audit** in the dashboard, or over WP-CLI with
`wp star-electric audit`.

- **Source of truth**: `https://aliza-younas.github.io/Star-Electric-Enterprises/`
- **Destination**: `https://salmon-antelope-713580.hostingersite.com/`
- **Payload**: `wordpress/data/` — `taxonomies.json`, `media.json`,
  `ranges.json`, `products.ndjson`

---

## Result

| Measure | Source | WordPress | Status |
|---|---:|---:|---|
| Products | 4,348 | 4,348 | OK |
| — quote-only (no published price) | 2,548 | 2,548 | OK |
| — priced | 1,800 | 1,800 | OK |
| — on a genuine source sale | 19 | 19 | OK |
| — variable | 105 | 105 | OK |
| Variations | 479 | 479 | OK |
| Out of stock | 341 | 341 | OK |
| Ranges (never products) | 181 | 181 | OK |
| Departments (`product_cat`, top level) | 10 | 10 | OK |
| Subcategories (`product_cat`, children) | 40 | 40 | OK |
| Brands (`star_brand`) | 8 | 8 | OK |
| Cross-category departments (`star_department`) | 3 | 3 | OK |
| Media masters | 2,686 | 2,686 | OK |
| **Failed imports** | 0 | **0** | OK |
| **Duplicate source ids** | 0 | **0** | OK |
| **Products priced at zero** | 0 | **0** | OK |

Every line reconciles. There is no measure where the destination differs from
the payload.

---

## How each measure is counted

| Measure | Source side | WordPress side |
|---|---|---|
| Products | records in `products.ndjson` | published posts of type `product` |
| Quote-only | `quote_only: true` | `_star_electric_quote_only = yes` |
| Priced | `quote_only: false` | `_star_electric_quote_only = no` |
| On sale | a `sale_price` below its `regular_price` | `_star_electric_discount > 0` |
| Variable | a non-empty `variations` array | the `variable` term of `product_type` |
| Variations | the variation entries, added up | posts of type `product_variation` |
| Out of stock | `availability: "Out of Stock"` | `_star_electric_availability = Out of Stock` |
| Ranges | records in `ranges.json` | published posts of type `star_range` |
| Departments | `categories` in `taxonomies.json` | `product_cat` terms with no parent, excluding "uncategorized" |
| Subcategories | `subcategories`, added up | `product_cat` terms with a parent |
| Brands / departments | `brands` / `departments` | terms in `star_brand` / `star_department` |
| Media masters | entries in `media.json` | entries in the importer's persisted media map |
| Failed imports | — | payload source ids with no matching post |
| Duplicate source ids | — | `_star_electric_source_id` values held by more than one product |
| Products priced at zero | — | any `_price` meta that is numerically zero |

---

## The three defect rows

These are not quantities to be matched; they are counts that must be zero.

**Failed imports — 0.** Every one of the 4,348 source ids resolves to a
published product. The check is a set difference between the payload's ids and
the ids WordPress holds, not a per-record lookup, so it cannot be fooled by a
product that exists twice.

**Duplicate source ids — 0.** Every product carries
`_star_electric_source_id`, and the importer matches on it, so a second run
updates in place. The check is a `GROUP BY … HAVING COUNT(*) > 1` over that
meta: if any record had ever been imported twice, it would show here.

**Products priced at zero — 0.** This is the failure mode this catalogue must
never have. 2,548 products have no published price; they are stored with **no
price at all**, not zero and not one. A `_price` of 0 could only mean a
quote-only product had been given an invented price, so it is counted as a
defect rather than as a quantity.

---

## Differences that are legitimate, and why

**Media masters were 2,054 during the migration and are 2,686 now.** The
original export recorded only each product's primary photograph. The approved
product page also shows gallery shots, and 632 of them were referenced by
products but never exported, so WordPress had nothing to show beyond the first
image. The exporter now records the gallery as well. Nothing was removed; 632
images were added. 2,686 masters serve 4,348 products because the same
photograph is reused wherever a source publishes it against more than one item —
the importer fetches each master once and every later product reuses the
attachment id.

**Ten departments but only seven contain products.** Industrial Control, Power
& Energy and Earthing Material are published by their sources at range level
only: the source lists family names with no model, specification, image or
price behind them. They are real departments in the payload and are listed with
a count of zero rather than hidden, which is honest information about the
catalogue. Their content appears as ranges.

**Eight brands but only five have products.** ABB Furse, Himel and Hyundai
Electric are the range-level brands, for the same reason. The brand directory
lists them separately under "Listed at product-family level", and the homepage
brand grid shows them reading "0 products" — which is what the approved page
shows.

**181 ranges are not products and are not counted as products.** Turning one
into a WooCommerce product would mean inventing a SKU and a price. They are a
`star_range` post type: browsable, filterable, enquirable, and impossible to add
to a cart.

**479 variations against 105 variable products.** Every variation is a real one
the source publishes. 104 of the 105 variable products are priced; one — the
Pakistan Cables CAT6 U/UTP PVC reel — is quote-only, and its two sheath colours
are shown as swatches so an enquiry can name one.

**A department term count is not a product count.** `star_department` terms
carry ranges as well as products, so Networking Solutions has 6 objects but 4
products. The rails and counts on the front end count products.

---

## What the reconciliation caught

- **An invented department.** Three earthing ranges carry their category slug in
  their `departments` field, and `wp_set_object_terms()` creates a term it
  cannot find — so WordPress had a fourth department called
  "earthing-material", with a public archive whose title was a slug. The
  importer now only assigns departments the payload declares; the stray term
  was deleted.
- **632 missing gallery images**, described above.
- **The "Key points" bullets were not imported at all.** Only four products
  carry them, and the field was silently dropped.
- **Category blurbs were not imported.** The approved category page prints one
  under the department title; they are now stored as the term description.
- **No term carried the catalogue's own order.** Brands, departments and
  subcategories were being listed alphabetically or by count, where the
  approved site lists them in payload order. Each term now records its position.
