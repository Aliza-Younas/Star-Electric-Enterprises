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

## 9. Elementor conversion QA

The conversion is a change to how the site is edited, not to how it looks, so
its acceptance test is that nothing changed. Three harnesses were used, each
answering a different question.

**Did the server send the same markup?** `htmlparity.py` compares the live page
against a capture taken before the change, element by element, ignoring the
things that legitimately differ between two requests - whitespace between tags,
cache-buster query strings, nonces and LiteSpeed's cache comment.

**Do the components compute the same styles?** `wpcompare.py` renders two pages
of the site at the same viewport and compares font size, colour, padding and box
width for a list of components.

**Do the pages look the same?** `pixdiff.py` and `pixpages.py` compare
screenshots pixel for pixel and write a side-by-side plus a heat map of every
differing pixel, so a real shift can be told from an antialiasing wobble.

**Is the difference in the markup at all?** `mirrorshot.py` saves the page as it
was and as it is, and renders both from `file://` in the same browser, one after
the other. A screenshot of a live page also measures when its images happened to
load; this measures only the markup. It is the tie-breaker when a live
comparison reports a difference the markup diff cannot account for.

**Did the difference survive a second render?** `verify.py` captures the page
twice and keeps only the pixels that differ from the baseline in *both*. The
department photographs carry `sizes="auto"`, so the browser resolves their
srcset against a layout box it has not finished computing and can pick a
different candidate between two renders of the same page - up to half a percent
of the pixels, on a page that has not changed at all. A difference that
survives two independent renders is the page; one that does not is the camera.
`verify.py` reports both numbers side by side.

| Step | Result |
|---|---|
| Moving the homepage markup into the plugin | Identical across all 6,149 elements |
| Elementor Site Settings | Only change: Roboto and Roboto Slab no longer loaded |
| Elementor homepage vs the PHP homepage | 15 of 15 components identical at 1440 |
| Elementor homepage vs the baseline | Pixel-identical at 375 and 390; wider widths differ only inside the hero carousel, which is on a different slide in the two captures |
| Header and footer in Theme Builder | Contact page differs only where the header shows a cart badge, which is session state |
| FAQ | Pixel-identical at all eight widths, worst 0.006% |
| Responsive sweep, 20 pages x 8 widths | No horizontal overflow, no broken images |
| Live smoke test, 28 URLs | 200 on all, no console errors, no PHP notices |
| Catalogue reconciliation | All 16 rows still OK - the conversion touched no product data |
| Elementor editors | All 8 documents open, 22 Star Electric widgets registered, no errors |
| Save and revert | A label was changed, confirmed live, and reverted cleanly |
| About | Body markup identical once Elementor's wrapper `<div>`s are set aside; worst reproducible pixel difference 0.021% at all eight widths, all of it one three-pixel antialiasing band |
| Contact | Body markup identical; worst reproducible pixel difference 0.008% |
| Request a Quote | Body markup identical apart from one collapsed space; worst reproducible pixel difference 0.021% |
| Submit a Complaint | Body markup identical; **0.000% at all eight widths** |
| Track Order | Body markup identical; **0.000% at all eight widths** |
| Categories | Body markup identical apart from one collapsed space; **0.000% at all eight widths** |
| Brands | Body markup identical; **0.000% at all eight widths** |
| Deals | Body markup identical; 0.000% at seven of eight widths. At 1366 the live comparison reported 0.043-0.100%, and rendering the two markups side by side from disk settled it: **0.000%**. The difference was when a lazy image happened to load, not what the page is |
| Track Order | Body markup identical; **0.000% at all eight widths** |
| Categories | Body markup identical apart from one collapsed space; **0.000% at all eight widths** |
| Brands | Body markup identical; **0.000% at all eight widths** |
| Deals | Body markup identical; 0.000% at seven of eight widths, and 0.000% at 1366 once the two markups were rendered side by side from disk |
| Shop | Body markup identical; **0.000%** |
| Department and subcategory archives | Body markup identical; 0.119%, all of it the three-pixel band |
| Brand archive | Body markup identical; 0.566%, image resampling and the three-pixel band |
| Product search | Body markup identical; 0.007% |
| Product page, priced | Body markup identical; **0.000% - not one pixel of 4200 x 1440** |
| Product page, quote-only | Body markup identical; 0.060% |
| Cart | Body markup identical; 0.008% |
| Checkout | Body markup identical; 0.008% |
| Customer account | Body markup identical; **0.000%** |
| Wishlist | Body markup identical; 0.043% |
| The three enquiry forms, end to end | 14 checks each: nonce, honeypot, every required field, a malformed address, a good submission, the stored enquiry, and the enquiry removed again |

### What the conversion QA found

- **The front-page setting silently did not save.** WordPress settings forms
  post to `options.php`, not to `options-reading.php`. Posting to the latter
  returns 200 and changes nothing, so the homepage briefly became the empty
  blog index. Caught by the pixel diff, which reported 48-59% of pixels
  changed.
- **Moving the homepage markup dropped the brand section's disclaimer** -
  "Manufacturers", and the line stating that no dealership, distribution or
  authorisation relationship is implied. Caught by the element-level markup
  comparison. That sentence is what keeps the brand tiles from reading as a
  dealership claim.
- **An Elementor page rendered at 615px instead of 1440.** The theme's
  `page.php` wraps content in `.prose`, which is 76ch wide. Converted pages use
  Elementor's Full Width template instead.
- **Saving an Elementor document cleared the page's template.** Elementor's
  save writes the whole page-settings record, so a save that passes no settings
  clears what was there - including *Elementor Full Width*. The FAQ page fell
  back to the theme's `page.php` and rendered a second page head inside the
  76ch `.prose` wrapper. Caught by auditing the template of every converted
  page rather than trusting the earlier measurement, which had been taken
  before the regression. The tooling now re-asserts the template after every
  save, and all six converted pages were checked.
- **Elementor's reset closed up the rule between the two halves of a form.**
  Elementor zeroes `hr` margins inside `.elementor`, which took 24px off each
  side of both rules on the quote page and shifted everything below them -
  16% of the pixels at 375. Found by comparing the computed style of 36
  components on the page before and against after; exactly one differed. The
  approved rhythm is restored in the child theme rather than changed.
- **Every "Request a Quote" button on a product page was dead.** The link was
  `?product=<id>`, and WordPress owns `product` as WooCommerce's post-type query
  var, so it answered with a 404 before any template ran - on any page, which is
  how it was missed. The link now says `quote_product`, and the quote page
  carries the product through into the request again. This one predates the
  Elementor work; the end-to-end form test is what found it.
- **A form that renders is not a form that works.** The first version of that
  test looked for its marker anywhere in the admin page, and the search screen
  echoes the search term back - so it reported a stored enquiry that did not
  exist. It now reads the list table's own rows.
- **Elementor's theme locations switched on WooCommerce's default product
  styles.** The location wrapper carries `get_post_class()`, which includes
  WooCommerce's own `product` class, and that makes every
  `.woocommerce div.product ...` rule in WooCommerce's stylesheet apply - rules
  the theme's own templates never matched, because they had no such ancestor.
  Two changed the product page at once: a 30px margin under the add-to-cart
  form, and WooCommerce's green 1.25em price. Found by comparing the computed
  style of 185 selectors, which is also what proved the fix: 0 of 185 differ
  now. The class is taken back off the rendered wrapper, because Elementor adds
  it after both `get_post_class()` and its own attribute filters have run -
  neither could reach it.
- **The same location prints an empty notices wrapper above the product**,
  worth 30px of blank space the approved page does not have. Hidden until
  WooCommerce puts a notice in it, exactly as on the cart.
- **A "page unchanged after saving" check that could never pass.** Saving a
  document bumps the post's modified time, and both LiteSpeed's own comment and
  Rank Math's `dateModified` carry a timestamp into the markup. Nineteen
  documents reported a difference that was two clocks. Both are normalised now,
  the way nonces and cache-busters already were.
- **The end-to-end form test was following a dead link.** It looked for
  `?product=` on a product page, which is how the broken quote route was found
  in the first place; it now follows the link through to the quote page and
  checks the product arrives with it.
- **LiteSpeed served a stale page after its own purge reported success.** The
  Toolbox purge links return 200 and leave the cached HTML in place, so a
  screenshot or a markup capture taken straight after a deploy can be of the
  previous build. A whole round of image measurements was made against a stale
  `/about/` before this was noticed - the fix that was being measured had
  already been deployed and was working. `baseline.py` and `htmlparity.py` now
  request every page with a unique query string, which PHP always answers, and
  the query string is normalised out of the comparison.
- **The same picture rendered two ways on two pages.** WordPress adds
  `decoding="async"` to an image rendered inside the main loop and not to the
  same image rendered outside it - which is exactly the difference between a
  section drawn by a PHP template and the same section drawn by an Elementor
  widget. The attribute is invisible, but it puts the browser on a different
  image-scaling path, and it showed up as a 0.28% pixel difference on the
  department cards. Proved by rendering the same saved page with and without
  the attribute. The plugin now removes it everywhere, so both paths draw the
  same picture.
- **The screenshot harness was framing narrow widths** in a wrapper page,
  because old headless Chrome clamped `--window-size` to about 500px.
  `--headless=new` honours 375 directly, and framing silently failed on any
  page that sends `X-Frame-Options` - which is how the mobile My Account
  baseline came out as a broken-image icon.

---

## 10. Per-product Elementor documents

Every product owns its own Elementor layout, drawn by one Single Product shell.
The measurements below are of the seeded layout against the product page as the
shared template drew it, rendered from disk so that what is compared is the
markup rather than when its images happened to load.

| Product | 1920 | 1440 | 1366 | 768 | 1024, 430, 390, 375 |
|---|---|---|---|---|---|
| Simple priced | 0.049% | **0.000%** | **0.000%** | **0.000%** | see below |
| Quote-only | 0.000% | **0.000%** | **0.000%** | **0.000%** | see below |
| Genuine sale | **0.000%** | **0.000%** | **0.000%** | 0.162% | see below |
| Variable, priced | 0.011% | 0.028% | 0.017% | **0.000%** | see below |
| Variable, quote-only | 0.004% | **0.000%** | **0.000%** | **0.000%** | see below |
| Out of stock | 0.013% | **0.000%** | **0.000%** | **0.000%** | see below |
| Several gallery images | **0.000%** | **0.000%** | 0.074% | **0.000%** | see below |

### The one defect still open

At 1024, 430, 390 and 375 every product differs by 10-21%, and all of it is one
cause: eight pixels of section padding.

The detail block is an Elementor container carrying the approved
`section section--sm` classes, and Elementor gives every container a padding of
its own. The first attempt at putting the approved padding back used a selector
specific enough to beat Elementor - and specific enough to beat the responsive
scale as well, so the section kept one padding at every width instead of
stepping down. Measured, not guessed: comparing the computed style of the page
before and after shows exactly one property differing, `padding-top` on
`.section--sm`, 48px against 40px at 1024 and 32px against 40px at 390. Nothing
else on the page differs at any width.

The fix is in the repository: the approved rules name `.e-con.section` beside
`.section`, in the stylesheet and at each of the three breakpoints, so the
cascade resolves exactly as it always did and no value is restated anywhere. It
is not on the site yet - the admin session expired before it could be uploaded.

---

## 11. Editability and what the conversion cost

### Every document opens, edits and saves without harm

`editability.py` opens each of the twenty-two documents through Elementor's own
editor URL, reads the document back out of the editor's config, checks that each
of its sections carries a Navigator name rather than "Container #118", saves it,
and then checks that the page is byte-for-byte what it was and that its page
template - or, for a theme template, its display condition - survived.

**132 checks, 0 failures.** Nothing is left rendering from a PHP template that
Elementor was supposed to replace, no document is a draft, no two templates
claim the same condition, and the regression that started this - Elementor's
save clearing a page's template - cannot happen unnoticed again.

Two things had to be fixed in the harness before that number meant anything.
Saving a document bumps the post's modified time, and both LiteSpeed's own
comment and Rank Math's `dateModified` carry a timestamp into the markup;
nineteen documents were reporting a difference that was two clocks. And the
end-to-end form test was still following `?product=`, the dead quote link.

### What the conversion cost

Measured the same way on both sides: the markup as it was before the conversion
against the markup the site serves now, rendered from disk with the assets still
coming from the live site.

| Page | HTML | Elements | CSS files / KB | JS files / KB |
|---|---|---|---|---|
| Home | 398 KB → 418 KB | 6,149 → 6,262 | 13 / 456 → 15 / 322 | 14 / 164 → 23 / 352 |
| Shop | 132 KB → 133 KB | 2,034 → 2,049 | 15 / 457 → 16 / 458 | 23 / 349 → 23 / 349 |
| Department archive | 136 KB → 137 KB | 2,111 → 2,126 | 15 / 457 → 16 / 458 | 23 / 349 → 23 / 349 |
| Product page | 110 KB → 113 KB | 1,367 → 1,402 | 17 / 468 → 18 / 470 | 29 / 427 → 29 / 427 |
| About | 102 KB → 107 KB | 1,285 → 1,323 | 15 / 458 → 15 / 321 | 22 / 345 → 22 / 345 |
| Contact | 86 KB → 89 KB | 1,047 → 1,069 | 15 / 458 → 15 / 321 | 22 / 345 → 22 / 345 |
| Cart | 147 KB → 148 KB | 927 → 944 | 16 / 472 → 16 / 335 | 26 / 437 → 26 / 437 |

Elementor's containers and widget wrappers cost **15 to 38 elements and 1 to 5
KB of HTML per page** - the honest price of making a page editable, and the
whole of it.

The Elementor runtime itself was paid for once, when the header and footer moved
into the Theme Builder: that is the home page's 14 → 23 scripts, and it is the
same on every page. Against it, the pages that are not WooCommerce pages now
load **135 KB less CSS** than they did.

Cumulative layout shift is **0.000 on both sides of every page measured**. First
contentful paint could not be told apart: repeated runs of the same page varied
by more than the difference between the two sides, so no claim is made about it
either way. Neither figure comes from a real visitor's browser, and neither
should be read as one.

---

## 12. Reconciliation

See `migration-audit.md`. Every one of sixteen measures reconciles, including
the three that are defect counts rather than quantities: **0 failed imports, 0
duplicate source ids, 0 products priced at zero.**
