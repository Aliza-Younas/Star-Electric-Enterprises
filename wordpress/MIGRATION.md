# WordPress Migration — Star Electric Enterprises

Migration of the approved static storefront at
`https://aliza-younas.github.io/Star-Electric-Enterprises/` onto WordPress at
`https://salmon-antelope-713580.hostingersite.com/`.

The static site is the **visual source of truth**. This is a migration, not a
redesign: no colour, type, spacing, component or section changes.

**Status: Phase 2 of 13 — foundation built locally, not yet deployed.**
Deployment is blocked on SSH credentials for the Hostinger account.

---

## Phase 1 — Destination audit (complete)

Read-only inspection of the destination on 2026-09-08:

| | |
|---|---|
| WordPress | 7.1 |
| Active theme | Hello Elementor |
| Elementor | 4.2.4, with Elementor Pro |
| Other plugins | Rank Math Pro (SEO), LiteSpeed Cache, Hostinger tools/AI/Reach |
| **WooCommerce** | **not installed** — `/wp-json/wc/v3/` returns 404 |
| Existing content | Stock install: "Hello world!" post, default page. Nothing of value |
| `/wp-admin/` | Redirects to `wp-login.php` |
| REST write access | 401 unauthenticated, as expected |

Hello Elementor is the right base, so the plan builds a child theme on it rather
than replacing it.

### Source reconciliation baseline

Recorded **before** any import so the audit can be checked afterwards.

| Measure | Count |
|---|---:|
| Verified individual products | 4,348 |
| — quote-only (no published price) | 2,548 |
| — priced | 1,800 |
| — with a genuine source sale price | 19 |
| — variable | 105 |
| — out of stock | 341 |
| Family / range records (never products) | 181 |
| Product categories | 10 |
| Brands | 8 |
| Departments (many-to-many) | 3 |
| **Unique image masters** | **2,054** |
| Static pages | 21 |

The media figure matters: 4,348 products resolve to 2,054 unique files because
Pakistan Cables deliberately shares range photography across sizes. The importer
uploads each master once and reuses the attachment ID.

---

## Phase 2 — Foundation (built, awaiting deployment)

### Export payload — `wordpress/data/`

Generated from the approved catalogue. Regenerate with the exporter in the
session scratchpad if the source data changes.

| File | Contents |
|---|---|
| `taxonomies.json` | 10 categories with subcategories, 8 brands, 3 departments |
| `media.json` | 2,054 unique masters with source URL, domain, image type and note |
| `ranges.json` | 181 range records |
| `products.ndjson` | 4,348 products, one per line, import-ready |

Verified at export: **0 media files missing on disk.**

### `star-electric-core` plugin

Business logic that must survive a theme change. Nothing here belongs in
`functions.php`.

| File | Responsibility |
|---|---|
| `star-electric-core.php` | Bootstrap, constants, WP-CLI registration, activation rewrite flush |
| `includes/class-quote-only.php` | The quote-only engine |
| `includes/class-taxonomies.php` | `star_brand` and `star_department` taxonomies |
| `includes/class-ranges.php` | `star_range` post type for the 181 family records |
| `includes/class-provenance.php` | Source metadata, admin panel, front-end image note |
| `includes/class-search.php` | Widens product search to SKU, model, series, specifications |
| `includes/class-shortcodes.php` | `[star_products]`, `[star_departments]`, `[star_ranges]` |
| `cli/class-import-command.php` | The catalogue importer |

**Quote-only products.** 2,548 products have no published price. WooCommerce has
no native "priced on enquiry" state, so they are stored with **no price at all** —
not zero, not one. Every consequence is handled: `get_price_html` shows *Request
a Quote*, `is_purchasable` returns false, the loop and single buttons become a
quotation link, `add_to_cart_validation` refuses the cart even if a button were
somehow rendered, and the structured-data offer has its price stripped rather
than publishing a zero. Meta key: `_star_electric_quote_only`.

**Many-to-many relationships.** One Aqua Wi-Fi switch belongs to Switches &
Sockets, Smart Switches, Home Automation and Office Automation Solutions at
once. WordPress taxonomies model this natively — one product, many terms, no
duplicate records. Brand is a separate taxonomy so a brand archive never
duplicates a product either.

**Ranges are not products.** The 181 family records have no model, specification
or price at source. Making them WooCommerce products would mean inventing a SKU
and a price, so they are a `star_range` post type: browsable, filterable by
brand and department, enquirable — and impossible to add to a cart.

### Importer

`wp star-electric <command> --dir=/path/to/static-site`

| Command | Purpose |
|---|---|
| `import-media` | Uploads each unique master once, records provenance |
| `import-taxonomies` | Categories with subcategories, brands, departments |
| `import-ranges` | The 181 range posts |
| `import-products` | The 4,348 products, `--batch` / `--offset` / `--limit` |
| `audit` | Reconciles WordPress counts against the source payload |

Properties, all required by the brief:

- **Idempotent** — every record stores `_star_electric_source_id` and is matched
  on it, so a second run updates instead of duplicating.
- **Resumable** — `--offset` resumes; the command reports the offset to use.
- **Batched** — caches are flushed every `--batch` records so a 4,348-record run
  does not exhaust memory.
- **Deduplicated** — a persisted media map means an image uploads once.
- **Honest** — failures are counted and named, never swallowed.

The `audit` command explicitly checks that **no product has a `_price` of 0**,
which is the failure mode this catalogue must never have.

### `star-electric-child` theme

Child of Hello Elementor. Presentation only.

`assets/css/styles.css`, `pages.css` and `responsive.css` are **the same files
the approved storefront ships**, copied verbatim and enqueued in order. The
WordPress build inherits the design system rather than reinterpreting it, which
is what makes visual parity achievable.

`functions.php` adds WooCommerce support, sets the approved 4-column /
12-per-page grid, removes star ratings and the reviews tab (no approved source
publishes ratings), adds a Specifications tab from imported metadata, and prints
the source domain on the product page.

---

## Deployment runbook

Once SSH access is available, from the WordPress root:

```
# 1. Back up first — never modify without one
wp db export ~/pre-migration-backup.sql
tar czf ~/pre-migration-wp-content.tar.gz wp-content

# 2. WooCommerce
wp plugin install woocommerce --activate

# 3. Custom code (rsync from the repo's wordpress/wp-content/)
wp theme activate star-electric-child
wp plugin activate star-electric-core

# 4. Permalinks
wp option update permalink_structure '/%postname%/'
wp rewrite flush --hard

# 5. Catalogue — in this order
wp star-electric import-media       --dir=/path/to/static-site
wp star-electric import-taxonomies  --dir=/path/to/static-site
wp star-electric import-ranges      --dir=/path/to/static-site
wp star-electric import-products    --dir=/path/to/static-site --batch=100

# 6. Reconcile — must show OK on every row
wp star-electric audit --dir=/path/to/static-site
```

---

## Outstanding decisions

| Item | Status |
|---|---|
| SSH credentials | **Required to proceed.** Everything above is built and waiting |
| Form recipient email | Requested; forms will not be wired to an invented address |
| Payment gateways | Agreed: leave disabled. The storefront already states no payment is taken online |
| Shipping zones and rates | Agreed: leave unconfigured. No delivery terms are on record |

Payment and shipping stay disabled deliberately. The approved storefront makes
no delivery or payment claim, and WooCommerce must not be the place one gets
invented.

---

## Remaining phases

3. Global shell — header, navigation, footer, mobile drawer, search
4. Data model — verify taxonomy import
5. Product import — media, products, prices, variations, quote products
6. Homepage — section by section against the approved live page
7. Shop, category archives, product pages
8. Cart, checkout, account, wishlist
9. Forms — quote, contact, complaint
10. Content pages — About, FAQ, Shipping, Returns, Privacy, Terms and the rest
11. SEO — titles, descriptions, canonicals to the WordPress domain, sitemap
12. Full QA — 1920 / 1440 / 1366 / 1024 / 768 / 430 / 390 / 375
13. Final live QA
