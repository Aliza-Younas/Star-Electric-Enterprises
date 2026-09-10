# WordPress Migration — Star Electric Enterprises

Migration of the approved static storefront at
`https://aliza-younas.github.io/Star-Electric-Enterprises/` onto WordPress at
`https://salmon-antelope-713580.hostingersite.com/`.

The static site is the **visual source of truth**. This is a migration, not a
redesign: no colour, type, spacing, component or section changes.

**Status: complete.** All thirteen phases are done, the destination is live,
and every reconciliation row matches. See `migration-audit.md` for the counts,
`QA.md` for what was tested and what it found, and `ARCHITECTURE.md` for how the
build is put together.

Deployment runs entirely through `wp-admin`. No SSH or SFTP access is required:
the theme and plugin arrive as ZIP uploads, and the catalogue import runs from a
dashboard screen in small AJAX batches.

Two things remain, and both are business decisions rather than migration work:
**payment gateway configuration** and **shipping zones and rates**. Neither is
guessed at. The checkout says so in the approved page's own words and routes to
the quotation and contact forms, which do work.

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
| `media.json` | 2,686 unique masters with source URL, domain, image type and note |
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
| `includes/class-importer.php` | The import engine — every step, resumable |
| `includes/class-admin-import.php` | The dashboard import screen and its AJAX endpoints |
| `cli/class-import-command.php` | A thin WP-CLI wrapper over the same engine |
| `data/` | The exported payload, carried inside the plugin |

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

All the work lives in `Star_Electric_Importer`. The dashboard screen and the
WP-CLI command are both thin wrappers over it, so fixing a bug in one fixes it
in the other.

| Step | Records | Purpose |
|---|---:|---|
| media | 2,054 | Each unique master imported once, with its provenance |
| taxonomies | 21 | Categories with subcategories, brands, departments |
| ranges | 181 | The family records, as `star_range` posts |
| products | 4,348 | The catalogue, including 105 variable parents |

Properties, all required by the brief:

- **Idempotent** — every record stores `_star_electric_source_id` and is matched
  on it, so a second run updates instead of duplicating. Variation children are
  matched on `<parent source id>#<index>`, and children the source no longer
  lists are deleted rather than left behind.
- **Resumable** — each step takes an offset and returns the next one. Progress is
  persisted in an option, so a closed browser tab, a dropped connection or a
  stopped run loses nothing: pressing Run again continues from the same record.
- **Batched** — a batch is sized to finish inside one PHP request (15 images, 20
  products), so no single request is long-running and the host's execution limit
  is never the constraint.
- **Deduplicated** — a persisted media map means an image is fetched once and
  every later product reuses the attachment ID.
- **Honest** — failures are counted, named and shown per step, never swallowed.

The audit explicitly checks that **no product has a `_price` of 0**, which is the
failure mode this catalogue must never have.

**Where images come from.** The destination cannot read this repository, so the
importer copies each master over HTTPS from the approved storefront into the
site's own media library. Nothing is hotlinked, and the source URL is
configurable on the import screen. If the masters are ever bundled into the
plugin's `data/media/` directory instead, they are used in preference and no
outbound request is made.

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

## Deployment runbook — dashboard only

Build the two upload packages first:

```
python wordpress/tools/export-catalogue.py     # only if the source data changed
python wordpress/tools/build-packages.py
```

That writes `wordpress/dist/star-electric-child.zip` (0.03 MB) and
`wordpress/dist/star-electric-core.zip` (0.25 MB, payload included). Both are
far below any plausible `upload_max_filesize`, which is what makes the
dashboard route viable.

Then, signed in at `/wp-admin/`:

1. **Back up.** Hostinger → Websites → Backups → create a manual backup, and
   confirm it completes before anything else is touched.
2. **WooCommerce.** Plugins → Add New → search *WooCommerce* → Install → Activate.
   Skip the setup wizard; the storefront takes no payment, so its questions do
   not apply.
3. **Theme.** Appearance → Themes → Add New → Upload Theme →
   `star-electric-child.zip` → Activate. Hello Elementor stays installed as the
   parent.
4. **Plugin.** Plugins → Add New → Upload Plugin → `star-electric-core.zip` →
   Activate.
5. **Permalinks.** Settings → Permalinks → Post name → Save. This also flushes
   the rules `/brand/…` and `/range/…` need.
6. **Import.** Star Electric → check the environment table reads *working* and
   *active* on every row, then run the steps in order: media, taxonomies,
   ranges, products. Each shows a progress bar and can be stopped and resumed.
7. **Reconcile.** Run the audit on the same screen. Every row must read `OK`,
   including *products priced 0*, which must be `0`.

The same steps are available over WP-CLI if shell access is ever added
(`wp star-electric import-media`, `… import-products`, `… audit`), but nothing in
the migration depends on it.

### If wp-admin goes fatal

The dashboard-only route has one failure mode worth writing down, because it
happened on 2026-09-10: a PHP parse error inside the plugin takes every wp-admin
page down, and wp-admin is the only way to upload a corrected plugin. The route
that shipped the fault cannot repair it.

The signature is unmistakable. The storefront is untouched — `/`, `/shop/`,
`/wp-json/` and `/wp-login.php` all return 200 — while everything that sets
`is_admin()` returns 500:

```
curl -s -o /dev/null -w "%{http_code}\n" https://salmon-antelope-713580.hostingersite.com/wp-admin/
curl -s -o /dev/null -w "%{http_code}\n" https://salmon-antelope-713580.hostingersite.com/wp-admin/admin-ajax.php?action=heartbeat
```

That split points at the plugin's admin code specifically: `star-electric-core.php`
requires `includes/class-admin-import.php` only under `is_admin()`, so a parse
error there is invisible to shoppers and fatal to the dashboard.

The way back in is Hostinger's own file manager, which does not go through
WordPress at all:

1. hPanel → **Websites** → the site → **File manager**.
2. Go to `public_html/wp-content/plugins/star-electric-core/`.
3. Replace the offending file with the repository's copy (upload it into the
   same directory and confirm the overwrite), or edit it in place.
4. Reload `/wp-admin/`. It comes straight back — nothing is cached and nothing
   needs reactivating.

If the faulty file cannot be identified quickly, rename the folder
`star-electric-core` to `star-electric-core-off` instead. WordPress then cannot
load the plugin, wp-admin returns, the storefront loses its catalogue chrome
until the folder is renamed back, and no data is lost either way. Prefer fixing
the file: renaming is the bigger hammer.

### Reaching the server at all

The dashboard is not the only way in, but the way in is not the obvious one.
`ftp.salmon-antelope-713580.hostingersite.com` resolves to Hostinger CDN edge
nodes (`Server: hcdn`) that answer 443 and drop everything else, so every FTP
and SSH attempt against that hostname times out at the TCP layer with no error
worth reading. **The origin server is a different address**, shown in hPanel
under Files -> FTP Accounts as "FTP IP (hostname)":

```
45.84.207.155     port 21   FTPS        -> 220 FTP Server ready.  (0.4s)
45.84.207.155     port 65002 SSH        -> SSH-2.0-OpenSSH_8.0    (0.4s)
```

Use the IP, never the hostname. With that, an FTP account rooted at
`public_html` is enough to replace a file that has taken the dashboard down,
and `wordpress/tools/` has no business holding the credential: it belongs in a
scratch file outside the repository, deleted after use.

Two things that cost time on 2026-09-10 and need not cost it again: Windows
PowerShell's `Set-Content -Encoding utf8` writes a byte-order mark, which rides
along on the first line of a credential file and makes curl reject the URL with
"No host part in the URL"; and the FTP username in the FTP Access panel is the
main account, which is not necessarily the account whose password was just
changed - a 530 with a correct-looking username usually means the password
belongs to a sub-account.

Prevention lives in the build:

```
python wordpress/tools/php-syntax-check.py
```

`build-packages.py` runs it first and refuses to package anything if it finds a
problem, so a file in this state cannot reach the server through the runbook
again.

---

## Outstanding decisions

| Item | Status |
|---|---|
| WordPress admin login | Supplied, used, and recorded outside the repository |
| SSH / SFTP | Not required. The dashboard route covers every deployment step |
| Form recipient email | Supplied, and recorded outside the repository |
| Payment gateways | Agreed: leave disabled. The storefront already states no payment is taken online |
| Shipping zones and rates | Agreed: leave unconfigured. No delivery terms are on record |

Payment and shipping stay disabled deliberately. The approved storefront makes
no delivery or payment claim, and WooCommerce must not be the place one gets
invented.

---

## Phase 14 — Elementor conversion (in progress)

The migration is complete and the frontend is approved; this phase changes how
that frontend is *edited*, not how it looks. See `ELEMENTOR-GUIDE.md` for the
editing instructions and `ARCHITECTURE.md` for where the line between Elementor
and the plugin falls.

**Converted and verified**

| Piece | Where it is edited |
|---|---|
| Site Settings — colours, typography, container | Elementor → Site Settings |
| Header | Templates → Theme Builder → Header → Site Header |
| Footer | Templates → Theme Builder → Footer → Site Footer |
| Homepage — 9 named sections | Pages → Home |
| FAQ, Shipping, Returns, Privacy Policy, Terms | Pages → each page |
| About, Contact, Request a Quote, Submit a Complaint | Pages → each page |
| Track Order, Categories, Brands, Deals | Pages → each page |
| Cart, Checkout, Customer Account, Wishlist | Pages → each page |
| Shop, department, subcategory, brand and search archives | Templates → Theme Builder → Product Archive |
| Every product page | Templates → Theme Builder → Single Product |

Their PHP templates are removed, because WordPress prefers a `page-{slug}.php`
over any page content and leaving them would have meant an Elementor page nobody
ever saw. The child theme now holds `header.php`, `footer.php`, the generic
`page.php` fallback, the WooCommerce template overrides and the shared parts —
and nothing else.

Nothing about the catalogue moved with them. Which products an archive shows, in
what order, at what price, whether a product can be bought at all, what a
variable product's variants are, what the three enquiry forms accept and where
they send it, and every total in the cart all stay in WooCommerce and in the
plugin's own classes. Elementor holds wording.

### Per-product Elementor documents

The Single Product theme template used to hold the whole product page, which
meant all 4,348 products shared one page: moving a section on one moved it on
every one of them. Each product now owns an Elementor document, and the Theme
Builder keeps a single shell that prints it.

| Piece | Where it lives |
|---|---|
| One Single Product shell | Templates → Theme Builder → Star Electric — Single Product Shell |
| Each product's own layout | Products → the product → Edit with Elementor |
| The default layout, seeding and the backfill | `Star_Electric_Product_Layout` |
| The backfill's button | Star Electric → Product page layouts |

Nothing was copied. A seeded document holds no title, price, SKU, stock state,
brand or photograph: it is a list of widgets that ask WooCommerce and the plugin
for the product being viewed. Product ids, metadata, images, categories, brands
and SEO are untouched.

---

## Phases 3 to 13 (complete)

| Phase | Delivered |
|---|---|
| 3. Global shell | Header, mega menu, mobile drawer, search, footer — one `header.php` / `footer.php` pair, from the approved `components.js` |
| 4. Data model | 10 departments, 40 subcategories, 8 brands, 3 cross-category departments, 181 ranges |
| 5. Product import | 4,348 products, 479 variations, 2,686 media masters, 2,548 quote-only products stored with no price at all |
| 6. Homepage | Hero, department strip, promo tiles, 11 product rails, deals, brand grid, bulk section — every rail matching the approved page position for position |
| 7. Shop and product | Shop archive, department and subcategory archives with the approved hero and subcategory strip, filters, sorting, pagination, product pages |
| 8. Commerce | Real WooCommerce cart and checkout on the approved layouts, My Account, wishlist |
| 9. Forms | Quote, contact and complaint: nonce-checked, sanitised, stored as private enquiries and emailed |
| 10. Content pages | About, FAQ, Categories, Brands, Deals, Shipping, Returns, Privacy, Terms, Track Order |
| 11. SEO | Titles, descriptions, canonicals and Open Graph through Rank Math; cart, checkout, account and wishlist noindex; quote-only products publish no price in their structured data |
| 12. Full QA | 1920 / 1440 / 1366 / 1024 / 768 / 430 / 390 / 375 — no horizontal overflow, no broken images |
| 13. Final live QA | Component parity, functional QA, reconciliation, security and credential checks — all recorded in `QA.md` |

### Deliberate differences from the approved page

Three, each of them a correction rather than a redesign:

- **"WhatsApp Inquiry" is "Ask about this product."** The approved button
  carries a WhatsApp icon and the word WhatsApp but links to the contact form —
  no number is on record, and the label implied a channel the store has not
  confirmed. The link goes to the same place.
- **The privacy page no longer says the forms are not connected.** On the
  approved static site that was true. Here the forms send an email and keep a
  copy, so the page says that instead.
- **The order-tracking page no longer says tracking is unavailable**, because it
  is now real: it asks for a billing email, which is what WooCommerce can
  actually match. The approved page asked for a phone number, which it cannot.

`/categories/` has no counterpart on the approved site, where "Categories" is
the header's mega menu. It is built from approved components so the department
tree has a real URL that can be linked, shared and indexed.
