# Architecture — Star Electric Enterprises on WordPress

How the migrated storefront is put together, and why each piece is where it is.

The approved static site at
`https://aliza-younas.github.io/Star-Electric-Enterprises/` is the visual source
of truth. Everything below exists to reproduce it on WordPress without
reinterpreting it.

---

## 1. The shape of it

```
Hello Elementor                     parent theme (installed, unstyled)
└── star-electric-child             templates + the approved CSS
star-electric-core                  business logic, section renderers, widgets
Elementor Pro 4.2.3                 page and template content
WooCommerce 11.1                    products, cart, checkout, account, orders
Rank Math Pro                       SEO output
LiteSpeed Cache                     page cache (purge after every deploy)
```

The split between the two is deliberate and holds throughout: **anything that
would still be true after a redesign lives in the plugin**; anything that is
about how the page looks lives in the theme. Quote-only pricing, the taxonomy
model, the importer and the merchandising rules are all business facts about
this catalogue, so a future theme change cannot take them out.

### Why a child theme rather than a fresh theme

The destination already ran Hello Elementor with Elementor Pro. Building a child
theme keeps that install intact and lets the migration be a set of template
overrides rather than a theme swap. Hello Elementor's own stylesheets are
dequeued in `functions.php` — its button reset was repainting the approved
buttons, and overriding it rule by rule was more fragile than not loading it.

### Elementor

Elementor Pro owns the content of the pages that have been converted; the
approved stylesheets still own how everything looks. Nothing is pasted into an
HTML widget, embedded in an iframe or flattened into a single block: each
approved section is a widget of its own, and each widget renders the approved
markup from PHP.

**Where the line falls.** The approved stylesheets target exact class names -
`.hs`, `.rail`, `.mcard`, `.ptile`, `.why-card`, `.header__inner`. A section's
structure is therefore not something an editor may rearrange without the design
breaking, but what a section *says* is. So each section is one Star Electric
widget with a panel of content controls, rather than a tree of generic Elementor
heading, text and button widgets. Sections themselves can be reordered, removed
and duplicated from the Navigator.

**One source of markup.** `Star_Electric_Sections` and `Star_Electric_Chrome`
render every approved section. The Elementor widgets call them, and so does any
theme template that still renders that section, so the two cannot drift apart.

**Elementor containers are transparent.** Every container the conversion creates
carries `content_width: full` and zero padding, margin and gap, because the
approved sections already provide their own width and spacing. Verified by
measuring: the same components on an Elementor page and on the PHP page it
replaced report identical computed styles.

**Page template.** A converted page is set to *Elementor Full Width*
(`elementor_header_footer`). The theme's own `page.php` wraps content in
`.prose`, which is 76ch wide and would squeeze every section.

**Site Settings** carry the design system's own colours, typography presets and
1320px container so anyone reaching for a colour in Elementor is offered the
brand's values. Nothing is applied from there — the approved stylesheets do all
the styling — which is why setting them changed nothing on the site except that
Elementor stopped loading Roboto and Roboto Slab, which nothing used.

---

## 2. The theme — `star-electric-child`

Presentation only. It contains no rule about what a product costs or whether it
can be bought.

### The stylesheets are the approved site's own files

`assets/css/styles.css`, `pages.css` and `responsive.css` are copied verbatim
from the approved storefront and enqueued in that order. Nothing in them was
edited for WordPress. This is the single biggest reason visual parity is
achievable at all: the design system is inherited rather than reimplemented.

A fourth sheet, `assets/css/woocommerce.css`, is the **platform bridge**. It
exists only to reconcile the approved components with markup WooCommerce emits
that the static site never had — checkout field rows, notices, the quantity
input, the order-tracking result, the account screens — and to undo WooCommerce's
own paint where it lands on a class the approved design also uses. Its largest
single job is `table.shop_table`: the cart table has to carry that class for
WooCommerce's scripts to find it, and WooCommerce styles it, so the approved
`.table` component is restored over the top.

### Templates

| Template | Covers |
|---|---|
| `header.php` / `footer.php` | The document, then Elementor's header/footer location with the renderer as fallback |
| `page.php` | The default page: page head, then `the_content()`. Nothing routine uses it any more |
| `woocommerce/` | Overrides: cart, checkout, my account, order tracking, product card |
| `template-parts/` | Shared fragments, e.g. the department card grid |
| `inc/class-shell.php` | Breadcrumbs, page heads, icons, category art, availability pills |

Every page and archive is an Elementor document. The shop, the department,
subcategory, brand and search archives are drawn by one **Product Archive**
theme template; every product page by one **Single Product** theme template.

The archive template serves two of the approved site's shapes. `shop.html` leads
with a page head; `category.html` leads with a hero — eyebrow, title, blurb, two
buttons and the department's photograph — then a subcategory strip, and ends
with the ranges published at family level. A `product_cat` or `star_department`
archive gets the second shape; the shop, a brand view and a product search get
the first. Which one appears is the query's decision, not an editor's, because
that is how the approved storefront is built.

Two things Elementor's theme locations do had to be undone to keep the approved
pages as they were. Their wrapper carries `get_post_class()`, which includes
WooCommerce's own `product` class and so switches on every
`.woocommerce div.product ...` rule in WooCommerce's stylesheet — rules the
theme's templates never matched. And the single-product location prints an
empty notices wrapper above the product. Both are handled in
`star-electric-core.php` and `assets/css/woocommerce.css`, and both are
commented where they sit.

### Client-side behaviour

Small, single-purpose scripts, each loaded only where it is used:

| Script | Where | What |
|---|---|---|
| `shell.js` | everywhere | drawer, sticky header, disclosure panels, FAQ, variant swatches |
| `shop.js` | archives | filter drawer, sort submit |
| `wishlist.js` | everywhere | the heart control and the wishlist page |
| `brands.js` | `/brands/` | the A–Z bar and the search box |
| `recent.js` | product pages | "Recently Viewed" |
| `home.js` | homepage | the hero and the rails |

The wishlist and recently-viewed lists are ids in the browser's own storage —
`localStorage` for the wishlist, `sessionStorage` for the history — exactly as
the approved site keeps them. The **cards are rendered by the server** from
those ids, so the price, quote-only and availability rules are written once in
PHP. Reimplementing them in JavaScript is how a wishlist ends up showing "Rs. 0"
for a product the shop correctly prices on enquiry.

---

## 3. The plugin — `star-electric-core`

| File | Responsibility |
|---|---|
| `star-electric-core.php` | Bootstrap, constants, WP-CLI registration, rewrite flush |
| `includes/class-quote-only.php` | The quote-only engine and the shared price block |
| `includes/class-taxonomies.php` | `star_brand` and `star_department` |
| `includes/class-ranges.php` | The `star_range` post type |
| `includes/class-provenance.php` | Source metadata and the image note |
| `includes/class-search.php` | Product search widened to SKU, model, series, specs |
| `includes/class-navigation.php` | Merchandising: rails, counts, spread, deals, related |
| `includes/class-filters.php` | The shop filter panel and its query |
| `includes/class-shortcodes.php` | `[star_products]`, `[star_departments]`, `[star_ranges]` |
| `includes/class-forms.php` | Quote, contact and complaint submission and storage |
| `includes/class-wishlist.php` | Server-rendered wishlist rows and product cards |
| `includes/class-seo.php` | Titles, descriptions, canonicals, robots — through Rank Math |
| `includes/class-sections.php` | The approved page sections, one renderer each |
| `includes/class-chrome.php` | The global header and footer |
| `elementor/class-elementor.php` | Registers the widget category and the widgets |
| `elementor/widgets/` | One widget per approved section |
| `includes/class-importer.php` | The import engine and the reconciliation audit |
| `includes/class-admin-import.php` | The dashboard import screen and its AJAX endpoints |
| `cli/class-import-command.php` | A thin WP-CLI wrapper over the same engine |
| `data/` | The exported catalogue payload, carried inside the plugin |

### Quote-only products

2,548 of 4,348 products have no published price. WooCommerce has no native
"priced on enquiry" state, so those products are stored with **no price at all** —
not zero, not one. Every consequence is handled in `Star_Electric_Quote_Only`:

- `get_price_html` returns *Request a Quote*
- `is_purchasable` returns false
- the loop and single add-to-cart buttons become a quotation link
- `add_to_cart_validation` refuses the cart even if a button were rendered
- the structured-data offer has its price removed rather than publishing a zero

`price_block()` is the one place the approved price order is produced — current
price, original price, then *Save N%* — so the card, the product page, the
wishlist row and the mini card cannot drift apart.

### Ranges are not products

181 records are ranges: a family name the source publishes with no model,
specification, image or price behind it. Turning one into a WooCommerce product
would mean inventing a SKU and a price, so they are a `star_range` post type —
browsable, filterable by brand and department, enquirable, and impossible to add
to a cart. They appear at the foot of a category archive and on the brand
directory, always under a heading that says what they are.

### Taxonomies and the many-to-many problem

One Aqua Wi-Fi switch belongs to Switches & Sockets, Smart Switches, Home
Automation and Office Automation Solutions at once. WordPress models this
natively: one product, many terms, no duplicate records.

- `product_cat` — 10 departments, 40 subcategories. A product carries both its
  department and its subcategory term.
- `star_brand` — 8 brands. Rewrite base `product-brand`, **not** `brand`:
  WooCommerce 11 registers its own `product_brand` taxonomy on that base and the
  collision served a 404. Brand tiles link to the shop filtered by brand, which
  is where the approved site's brand tiles link.
- `star_department` — the 3 cross-category departments. The importer only
  assigns departments the payload declares, so a range carrying a category slug
  in its departments field cannot invent a fourth one.

### Merchandising

`Star_Electric_Navigation` is a faithful port of the approved `data.js`:

- **ranked()** — the approved `selectProducts()` score, computed in SQL: four
  points for a published price, two for confirmed stock, two for a photograph of
  the item itself, one for a genuine source discount. Ties keep catalogue order,
  which is ascending post id because the import file preserves it.
- **spread_ids()** — the approved `spreadProducts()`: group the ranked selection
  by subcategory, biggest group first, then one from each in rotation. Without
  it the Wires & Cables rail opens on four near-identical PVC boxes.
- **deal_ids()** — every product with a real source discount, biggest first,
  ties by id.
- **related()** — the approved rule: same subcategory, then the rest of the
  department, then the same brand. WooCommerce's own `wc_get_related_products()`
  picks at random and never matched.

Results are cached in a transient keyed on the term's own product count, so an
import retires the cache without anything having to remember to clear it.

### SEO

`class-seo.php` **delegates to Rank Math** when it is active, filtering
`rank_math/frontend/description`, `.../canonical`, `.../robots` and the OG site
name rather than printing a second set of tags — two canonicals is worse than a
wrong one. It falls back to emitting its own head tags if Rank Math is ever
removed. Cart, checkout, account, search and wishlist are marked noindex.

### Forms

Quote, contact and complaint all post to `Star_Electric_Forms`: nonce-checked,
sanitised, stored as a private `star_enquiry` post so nothing is lost if mail
fails, and emailed to the recipient recorded outside the repository. No
attachment is accepted, because the approved pages do not promise one.

---

## 4. The importer

Everything lives in `Star_Electric_Importer`. The dashboard screen and the
WP-CLI command are both thin wrappers, so a fix in one is a fix in both.

| Step | Records | Purpose |
|---|---:|---|
| media | 2,054 | Each unique master imported once, with its provenance |
| taxonomies | 61 | 10 categories, 40 subcategories, 8 brands, 3 departments |
| ranges | 181 | The family records |
| products | 4,348 | The catalogue, including 105 variable parents and 479 variations |

- **Idempotent** — every record stores `_star_electric_source_id` and is matched
  on it, so a second run updates in place and keeps its post id, media and URL.
  Variation children are matched on `<parent source id>#<index>`, and children
  the source no longer lists are deleted rather than orphaned.
- **Resumable** — each step takes an offset and returns the next; progress is
  persisted in an option, so a closed tab loses nothing.
- **Batched** — sized to finish inside one PHP request, so the host's execution
  limit is never the constraint.
- **Deduplicated** — a persisted media map means an image is fetched once and
  every later product reuses the attachment id. 2,054 masters for 4,348
  products.
- **Honest** — failures are counted, named and shown per step, never swallowed.

Media are copied over HTTPS from the approved storefront into the site's own
media library. Nothing is hotlinked. If the masters are bundled into the
plugin's `data/media/` directory they are used in preference and no outbound
request is made.

`audit()` reconciles sixteen measures against the payload and is the basis of
`migration-audit.md`.

---

## 5. What is deliberately absent

Every one of these is a business fact nobody has supplied, and a public page is
the wrong place to invent one:

- No payment gateway, and the checkout says so in the approved page's own words.
- No shipping zone, rate or delivery claim.
- No phone number, WhatsApp number, street address or opening hours.
- No return window, warranty term, establishment year, customer count, review,
  rating, award, certification or dealership claim.
- No manufacturer logos — brand tiles are monograms, because a real logo would
  imply a relationship that is not on record.
- No star ratings and no reviews tab: no approved source publishes them.
