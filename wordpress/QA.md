# QA record — Star Electric Enterprises on WordPress

What was tested, how it was tested, and what it found. Every number here comes
from a run against the live sites, not from reading the code.

- **Approved source of truth**: `https://aliza-younas.github.io/Star-Electric-Enterprises/`
- **WordPress destination**: `https://salmon-antelope-713580.hostingersite.com/`

The harnesses live in the session scratchpad, not in this repository — they are
throwaway test rigs, and the repository holds the site. What follows describes
what each one does so the same checks can be rebuilt.

---

## 1. How the checks are made

**Rendering.** Headless Chrome (`--headless=new`). Two things about it shaped
the harnesses:

- Chrome clamps `--window-size` to about 500px, so 375/390/430 are rendered
  inside an iframe of that exact width in a wrapper page.
- `--disable-web-security` is *ignored* under the new headless mode, so a
  wrapper page cannot read into a cross-origin frame. Each page is therefore
  mirrored to a local file with an injected `<base href>`: the markup is the
  server's own, the stylesheets and images still load from the live site, and
  the frame is same-origin and can be measured.

**Caching.** LiteSpeed Cache is active. Every deploy is followed by a full purge
(page, CSS/JS, LSCache, OPcache) or the next screenshot is of the previous
build. Several early QA rounds were wasted on stale HTML before this became
automatic.

**Comparison, not opinion.** Where a difference can be a number it is a number:
computed `font-size`, `color`, `padding`, box width and height for each
component, compared between the two sites at the same viewport. Screenshots are
then used for the things numbers cannot see.

---

## 2. Homepage rail parity

The approved homepage builds eleven rails from `spreadProducts()` in `data.js`:
score the selection, group it by subcategory, biggest group first, then one from
each in rotation. That algorithm was ported to PHP and then **proved against the
live approved page** before being compared with WordPress, so a mismatch could
not be blamed on a faulty port.

| Rail | Result |
|---|---|
| Wires & Cables | 10/10 identical |
| Switches & Sockets | 10/10 identical |
| Lighting & Fixtures | 10/10 identical |
| Fans & Ventilation | 10/10 identical |
| Circuit Protection | 10/10 identical |
| Wiring Accessories | 10/10 identical |
| Solar Panel Cables | 10/10 identical |
| Home Automation | 10/10 identical |
| Office Automation Solutions | 10/10 identical |
| Networking Solutions | 4/4 identical |
| Discounted at source | 10/10 identical |

Rail titles, counts and *View All* links match as well.

### What the comparison found

- The rails were ordered by post id, not by the approved score, so every rail
  after the first two showed the wrong ten products.
- The department rail count came from the term's own count, which includes
  ranges as well as products: Networking Solutions read 6 where the approved
  page reads 4.
- The deals rail ordered by discount alone, so equal discounts came back in
  whatever order MySQL chose, and the rail had no count and no *View All* link.

---

## 3. Component parity — the approved site against WordPress

Computed style compared for every listed component, at 1440 / 1024 / 768 / 390.

| Page | Components | Result |
|---|---:|---|
| Homepage | 43 | identical at all four widths |
| Shop | 32 | identical at all four widths |
| Category archive | 28 | identical at all four widths |
| My Account | 22 | identical at all four widths |
| Wishlist | 21 | identical at all four widths |
| Brands | 27 | identical at all four widths |
| Deals | 22 | identical at all four widths |
| Contact | 26 | identical at all four widths |
| Request a Quote | 26 | identical at all four widths |
| FAQ | 22 | identical at all four widths |
| Product (priced, sale, quote-only) | 21 | two differences, both deliberate |

Components checked include the header and its navigation, the mega menu, the
hero, department tiles, catalogue cards and their price blocks, rails, promo
cards, brand cards, buttons in every variant, panels, inputs, field labels, the
section heads and the footer. A component missing from **both** pages is
reported as such rather than counted as a match — an early version of this
harness used the wrong class names and reported "identical" for components that
existed on neither page.

The two product-page differences are the ones this migration chose:

- The approved page's ghost button reads *WhatsApp Inquiry* and is 164px wide;
  here it reads *Ask about this product* and is 212px. Both link to the contact
  form. No WhatsApp number is on record, and the label implied a channel the
  store has not confirmed.
- A quote-only product on the approved page ships a hidden *Buy Now* button.
  Here there is none to hide.

**Cart and checkout cannot be compared this way.** The approved cart is a static
mock-up that is always empty and ships its table markup hidden; WooCommerce
emits no table at all for an empty cart, and redirects `/checkout/` to the cart
when there is nothing in it. Both were verified by screenshot and by the
functional walk-through in §5 instead.

### What the comparison found

- **Hello Elementor's reset was painting every bare `<button>`** with a pink
  border and inverting it on hover — the FAQ accordion, the filter toggles and
  the A–Z bar. Two attempts to out-specify it failed (an attribute selector
  weighs the same as a class, so `[type=button]` beat a bare `button` rule); the
  parent theme's stylesheets are now dequeued entirely.
- **WooCommerce paints `table.shop_table`** — 9px/12px cells on a 1.5em line —
  and the cart table has to carry that class for WooCommerce's own scripts. The
  approved `.table` component is restored over the top.
- **The A–Z bar on the brand directory listed only the letters in use**, four
  buttons wide, where the approved bar shows the whole alphabet with the unused
  letters disabled. It was a different height at every breakpoint.
- **The brand directory sorted alphabetically**; the approved page lists brands
  in catalogue order, largest first. The order now comes from the payload,
  stored on the term at import.
- **The homepage brand grid hid the three brands with no individual products.**
  The approved grid shows all eight, the last three reading "0 products".
- **Product counts were printed with a thousands separator** where the approved
  page prints a bare number.
- **The category archive was the shop's page head**, where the approved
  `category.html` is a hero with an eyebrow, a blurb, two buttons and the
  department's photograph, followed by a subcategory strip and, at the foot, the
  ranges that source publishes at family level. All of it was missing.
- **Subcategory tiles had no picture** and collapsed to half the approved
  height, because only departments carry a thumbnail. They now show their
  department's line-art icon, which is what the approved tiles show.
- **Departments, subcategories and brands were listed alphabetically or by
  count** rather than in the catalogue's own order. Each term now records its
  position in the payload.
- **The product page dropped the "Key points" bullets** — four products carry
  them and the importer was not storing the field at all.
- **A quote-only variable product showed no variants**, where the approved page
  shows swatches so an enquiry can name one.
- **"Recently Viewed" was missing** from the product page.
- **Related products came from `wc_get_related_products()`**, which picks at
  random from shared categories and tags. The approved rule is: same
  subcategory, then the rest of the department, then the same brand.
- **The Contact, Complaint and Quote pages had been summarised rather than
  ported**, dropping seven aside cards and the "Find Us in Saddar" section.
- **Ranges were listed as bare names.** The approved page wraps each brand's
  ranges in a panel that says why they are ranges and not products, and shows
  each one's summary and its source. All of that was missing.
- **The product gallery collapsed to 78px.** `.gallery` is a two-column grid and
  the thumbnail rail was only rendered when there were two or more images, so a
  single-image product dropped its photograph into the thumbnail column.
- **632 gallery photographs had never been exported**, so every product page
  showed only its first image. See `migration-audit.md`.
- **The empty cart sat in the summary column.** WooCommerce prints an empty
  notices wrapper before the cart, and inside a `display:contents` grid that
  empty div took the first cell.
- **WooCommerce's own "Your cart is currently empty" notice** duplicated the
  approved empty-state panel directly beneath it.

---

## 4. Responsive sweep

Every migrated page at **1920, 1440, 1366, 1024, 768, 430, 390, 375**, checking
`documentElement.scrollWidth === clientWidth` and every image's
`naturalWidth`.

**Result: no horizontal overflow and no broken images at any width**, across 20
content pages plus the five representative product pages and the brand archive.

### What the sweep found

The cart table overflowed the page by 24px at 1440 and 1px at 1366. The cause
was not the table: `.sr-only` is `position:absolute` with no offset, so the
caption and the "Remove" label escaped `.cart-panel`'s `overflow:hidden` and
dragged the document's scroll width. Containing the panel fixed it; the panel
also scrolls its own table so an unusually long row can never move the page.

---

## 5. Functional QA

Sixty-four checks against the live store as an anonymous shopper — real
requests, a real WooCommerce cart session, real nonces.

| Area | Checks |
|---|---|
| Header and shell | header, mega menu, mobile drawer, search, cart/wishlist/account links, footer |
| Homepage | hero, department strip, 11 rails, View All links, deals rail, brand grid, no "Rs. 0" anywhere |
| Shop | product grid, 36 filter groups, pagination, page 2, brand filter (4,348 → 1,485), sorting |
| Category | archive, hero, subcategory strip |
| Priced product | price shown, Add to Cart present |
| Quote-only product | no price, no "Rs. 0", no Add to Cart, Request a Quote works |
| Sale product | sale price, original price, "Save N%" |
| Variable product | variation form, selectable options, not an empty parent |
| Out-of-stock product | says so, not purchasable |
| Cart | add, subtotal, quantity update recalculates (Rs. 2,200 → Rs. 6,600), invalid coupon refused, remove empties |
| Checkout | loads, billing fields work, states that ordering is not open, no invented gateway, no invented shipping |
| Account | the approved services grid, no fake login |
| Wishlist | renders, empty state |
| Order tracking | form with its nonce, unknown order rejected |
| Forms | quote, contact and complaint render with their nonces |

**Result: all checks pass.**

Form delivery was verified once, earlier in the migration, with a single test
submission. That test enquiry has been deleted; no repeat test mail was sent.

---

## 6. Content and SEO sweep

Twenty-three URLs checked for title, canonical, Open Graph URL, meta
description, robots directive, duplicate canonical tags, and for anything that
should never reach a public page.

| Check | Result |
|---|---|
| Every page returns 200 | pass |
| Every indexable page has a title and one canonical | pass |
| Every canonical points at the WordPress domain | pass |
| No GitHub Pages canonical anywhere | pass |
| No `REPLACE-WITH-DOMAIN`, localhost or staging URL | pass |
| No bracketed or curly placeholder | pass |
| No lorem ipsum, no WooCommerce sample content | pass |
| No PHP notice, warning or fatal error | pass |
| Cart, checkout, account and wishlist are noindex | pass |
| Structured data: priced product carries an offer | pass |
| Structured data: quote-only product carries **no price** | pass |
| `sitemap_index.xml` served | pass |

### What the sweep found

- **`/brand/aqua/` returned 404** while the brand directory linked to it.
  WooCommerce 11 registers its own `product_brand` taxonomy on the `brand`
  base. The rewrite base is now `product-brand`, and brand tiles link to the
  shop filtered by brand — which is where the approved brand tiles link.
- **`/privacy-policy/` was a public 404** while the footer linked to it: the
  WordPress draft page had never been published.
- **WooCommerce's sample "Refund and Returns Policy" page was live**, publicly
  offering a fabricated 30-day refund policy and `{email address}` placeholders.
  Trashed.
- **The store base was set to California, USA**, so every checkout would have
  shown a US state dropdown. Now Rawalpindi, Punjab.
- **Two sets of canonical and Open Graph tags** were being emitted, because Rank
  Math is active. The plugin now filters Rank Math's output instead.

---

## 7. Security check

| Check | Result |
|---|---|
| No credential in the repository or in its history | pass |
| Import endpoints are `wp_ajax_` only, never `nopriv` | pass |
| Import endpoints check `manage_options` **and** a nonce | pass |
| Settings save checks the admin referer | pass |
| Public AJAX (wishlist, recently viewed) is read-only, capped, ids cast to int | pass |
| Form submission is nonce-checked and every field sanitised by type | pass |
| Enquiry post type is not public, not in REST, not searchable | pass |
| No REST route added by the plugin | pass |
| No `var_dump`, `print_r`, `error_log` or `console.log` in shipped code | pass |
| Output escaped at the point of echo | pass |

### What the check found

The catalogue payload inside the plugin — `data/products.ndjson`, 5.3 MB — was
downloadable by anyone who guessed the path. It is not secret, but it is a
migration artefact and had no business being served. The payload directory now
carries an `.htaccess` deny and an `index.php`, and the file returns 403.

---

## 8. Final live smoke test

28 URLs on the live site — every content page, five representative products, a
department archive, a brand archive, a subcategory archive and a search — each
fetched over HTTP and then rendered in a browser.

| Check | Result |
|---|---|
| HTTP status | 200 on all 28 |
| PHP notices, warnings or fatals in the response | none |
| Browser console errors | none |
| Failed network requests | none |
| Page titles | present and distinct on all 28 |

---

## 9. Reconciliation

See `migration-audit.md`. Every one of sixteen measures reconciles, including
the three that are defect counts rather than quantities: **0 failed imports, 0
duplicate source ids, 0 products priced at zero.**
