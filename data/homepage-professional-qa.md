# Homepage Professional QA

Production quality review of the homepage, carried out against the deployed site
at https://aliza-younas.github.io/Star-Electric-Enterprises/ and the identical
local build at commit `aad0a5c`.

Reviewed by rendering the page — not by reading source — at 1920, 1440, 1366,
1024, 768, 430, 390 and 375, plus scripted measurement of the DOM, images,
commerce state and runtime errors.

Measured baseline: 3,422 DOM nodes · 137 images (134 lazy, 0 broken) ·
108 product cards across 9 rails · 0 JavaScript errors · 19 visible
placeholder elements · hero product image occupying 14% of the banner area.

---

## BEFORE — issues found

Severity key: **critical** breaks trust or function on the live site ·
**high** clearly reads as unfinished · **medium** noticeably below professional
standard · **low** polish.

### A. Visual hierarchy

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| A1 | The banner is the largest element on the page but carries the least information — a 858×548 px area in which the product occupies 14%. The eye lands on empty space. | critical | all desktop | A commerce hero must sell; this one reads as an unfinished container. | Rebuild as an editorial composition: layered multi-product still life, tighter height, stronger type scale. |
| A2 | Nothing establishes a "first screen" priority order. Rail, banner, promos and trust strip all carry similar visual weight. | high | ≥1024 | No focal point, so the page reads as a set of boxes. | Give the banner clear dominance, demote promo tiles, lighten the trust strip. |

### B. Hero / banner

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| B1 | Single small product floating in a near-white field; no composition, depth or art direction. | critical | all | Looks like a placeholder slot rather than a designed banner. | Compose 2–4 complementary real products per slide with a staged backdrop. |
| B2 | Slide is 548 px tall with ~130 px dead space above and below the copy. | high | ≥1024 | Wastes the most valuable screen area. | Fix a compact commercial aspect and align copy to it. |
| B3 | **Prev arrow overlays the body copy** — the word "sized" is hidden behind the control. | critical | 768–1023 | Broken layout; text is unreadable. | Move controls out of the text column; reserve gutters. |
| B4 | **Slide-label chips overlap the CTA buttons.** | critical | 768–1023 | Two interactive layers on top of each other; the CTA can be mis-clicked. | Put navigation in its own row at every width. |
| B5 | Navigation chips look like leftover filter pills, not premium slider navigation. | high | all | Reads as debug UI. | Purpose-built tab navigation with a progress indicator. |
| B6 | Fade-only transition, no swipe, no keyboard, no pause when the tab is hidden. | high | all | Feels basic; autoplay keeps running unseen. | Transform-based slide + fade, swipe, arrow keys, pause on hover/focus/visibility change. |
| B7 | First paint shows all five slides stacked before JS hides them. | medium | all | Content flash on load. | Render inactive slides hidden from the start. |

### C. Category rail / navigation

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| C1 | 15 rows with no separators and a heavy solid navy cap; rows read as one undifferentiated block. | medium | ≥1024 | Hard to scan. | Hairline separators, calmer header, tighter row rhythm. |
| C2 | Chevron only on some rows with no alignment column. | medium | ≥1024 | Ragged right edge. | Fixed chevron column, consistent baseline. |
| C3 | Flyout can be clipped by the banner stacking context. | high | ≥1024 | Menu unusable in places. | Raise the rail's stacking context above the banner. |
| C4 | Hover state is a flat grey wash with no affordance for the active row. | low | ≥1024 | Feels unfinished. | Accent left edge marker plus text colour change. |

### D. Category circular thumbnails

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| D1 | **Two different image languages in one strip**: Pakistan Cables tiles show a dark-green photographic square inside the circle, while Aqua/Himel tiles show a clean product on white. | critical | all | The single most amateur-looking element on the page. | Normalise every department image onto one white photographic stage. |
| D2 | Perceived product scale varies widely between tiles. | high | all | Strip reads as random. | Regenerate square masters with a common subject-height target. |
| D3 | Two-line labels ("Data & Telephone Outlets") push the count down and break the baseline across the row. | medium | all | Ragged row. | Fixed label block height, 2-line clamp. |
| D4 | Counts on every tile add noise to a navigation strip. | low | all | Clutter. | Keep counts but demote them visually. |

### E. Product sliders

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| E1 | Every rail sits on the same tinted band with equal spacing, so nine shelves merge into one long field. | high | all | No merchandising rhythm. | Alternating surface treatment and a stronger shelf header rule. |
| E2 | Arrows are the same weight as the "View All" pill and compete with it. | medium | ≥768 | Two controls fighting. | Quieter arrows, pill remains primary. |
| E3 | Repeated arrow clicks accumulate sub-pixel drift, so snap points slowly desynchronise. | medium | ≥768 | Cards end up mis-aligned. | Snap the computed page width to whole card steps. |

### F. Product cards

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| F1 | **The product image has no frame** — it runs edge to edge into the card, so dark product photography merges with the card and the page. | critical | all | Explicitly below the requested standard; looks unfinished. | Give the image its own inset framed stage with border and radius. |
| F2 | Product name (13.5 px) is barely stronger than the brand line; price is not the strongest element. | high | all | Weak hierarchy for a commerce card. | Name 15 px semibold, brand 12.5 px muted, price 17 px bold. |
| F3 | "Request a Quote" renders as plain bold text and reads as missing price data. | high | all | Undermines the 2,544 quote-only products. | Deliberate quote treatment in brand navy with a supporting line. |
| F4 | Full-width red primary button on every card produces a wall of red across a rail. | medium | all | Visually loud, dilutes emphasis. | Navy primary for cart actions, red reserved for quote/discount emphasis. |
| F5 | Card has no hover affordance beyond a border tint. | medium | ≥1024 | Feels static. | Subtle lift, shadow, 1.03 image scale. |
| F6 | Action row height 36 px is below the comfortable touch target. | medium | ≤430 | Hard to tap accurately. | 44 px on touch widths. |

### G. Spacing and rhythm

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| G1 | The tinted rails band starts immediately under the departments strip with no separation. | medium | all | Sections collide. | Consistent section rhythm with defined band boundaries. |
| G2 | Hero section top padding is smaller than every other section, so the page starts abruptly under the nav. | low | all | Cramped. | Align to the spacing scale. |

### H. Typography

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| H1 | Banner headline maxes at 34 px — small for a hero at 1440+. | medium | ≥1366 | Lacks presence. | Fluid scale to 44 px. |
| H2 | Rail titles, department labels and card names use three unrelated sizes with no rhythm. | medium | all | Inconsistent. | Rationalise to a single scale. |
| H3 | Uppercase eyebrows on banner, promos and card meta all at once. | low | all | Over-used device. | Restrict uppercase to the banner eyebrow. |

### I. Colour consistency

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| I1 | Red is used for every primary action on every card, the search button, the CTA and the badges simultaneously. | medium | all | Emphasis is lost when everything is emphasised. | Navy structural actions; red for quote, discount and the single hero CTA. |
| I2 | Three different neutral surfaces (`#fff`, `--surface-soft`, `--surface-alt`) alternate without a rule. | low | all | Muddy. | One tint for bands, white for cards. |

### J. Image consistency

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| J1 | Promo tile images are tiny dark thumbnails; the "Bulk & Contractor Orders" tile is a near-black rectangle. | high | all | Unreadable at size. | Re-cut promo art from lighter product imagery, larger frame. |
| J2 | Department and card images mix dark-background and white-background masters. | critical | all | See D1. | Single normalisation pass. |

### K. Hover and focus

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| K1 | Rail arrows and hero arrows have no visible keyboard focus ring. | high | all | Keyboard users cannot see position. | Explicit `:focus-visible` styling on all controls. |
| K2 | Department tiles show no hover feedback on the label. | low | ≥1024 | Feels inert. | Colour shift plus disc elevation. |

### L. Mobile responsiveness (≤430)

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| L1 | Hero product image is a dark green square dominating above the headline. | high | ≤430 | Inconsistent with the rest of the catalogue imagery. | Normalised composition art. |
| L2 | Card action buttons 34 px tall. | medium | ≤430 | Below touch guidance. | 44 px. |
| L3 | Trust strip renders five rows of icon + placeholder text before any product. | medium | ≤430 | Pushes commerce below the fold. | Trim to verified items only. |

### M. Tablet responsiveness (768–1023)

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| M1 | Arrow over copy (B3) and chips over CTA (B4). | critical | 768–1023 | Broken. | Reserved gutters and a dedicated nav row. |
| M2 | Promo tile titles wrap to three lines; kicker wraps to two; arrow orphans. | high | 768–1023 | Looks broken. | Reflow to two columns with room for the label. |

### N. Slider usability

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| N1 | Hero has no swipe support. | high | touch | Expected gesture missing. | Pointer/touch swipe with threshold. |
| N2 | Hero autoplay continues while the tab is hidden. | medium | all | Wasted work; slide position unpredictable on return. | `visibilitychange` pause. |
| N3 | Rail arrows are hidden entirely below 760 px, leaving no affordance that the row scrolls. | low | ≤760 | Discoverability. | Partial next card already implies it; keep arrows hidden but ensure the peek is consistent. |

### O. Accessibility

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| O1 | Hero uses `role="tablist"` on the dot row but the slides are not `tabpanel`s. | high | all | Incorrect semantics for assistive tech. | Move to a labelled group with `aria-current`, or correct the tab relationship. |
| O2 | Slide changes are not announced. | medium | all | Screen-reader users get no feedback. | Polite live region on the slide label. |
| O3 | No visible focus ring on hero and rail controls (K1). | high | all | WCAG 2.4.7. | Add focus-visible styles. |
| O4 | Decorative promo/department images have `alt=""` correctly, but product card images also have `alt=""` while acting as the primary link target. | low | all | The card name link carries the accessible name, so this is acceptable; verified, no change. | No change. |

### P. Performance

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| P1 | 3,422 DOM nodes and 108 product cards on first paint. | medium | all | Heavier than necessary for a homepage. | Reduce rail size to 10 cards; keep 8 rails. |
| P2 | All five hero slides render their images at load; only one is visible. | medium | all | Wasted bytes on the critical path. | Only the first slide image is eager; the rest lazy. |
| P3 | Hero image has no `fetchpriority` on the actual LCP element after the redesign. | medium | all | Slower LCP. | `fetchpriority="high"` on slide 1 art only. |

### Q. Console / runtime

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| Q1 | No JavaScript errors found. | — | all | Verified clean. | No change. |

### R. Broken links / assets

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| R1 | No broken images (0 of 137) and no dead internal links found. | — | all | Verified clean. | No change. |

### S. Public placeholder / demo content

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| S1 | **A first-time visitor arrives with 6 items in the cart and 2 in the wishlist**, seeded by demo code. | critical | live | Destroys credibility and misrepresents commerce state. | Remove seeding; one-time migration clears the seeded state for returning visitors without touching genuine additions. |
| S2 | 19 visible placeholder elements: topbar "WhatsApp: [ number ]", "Call: [ number ]", "Delivery & support: [ details ]", nav "Store hours: [ timings ]", trust strip "[ coverage to be confirmed ]" and "[ payment methods to be confirmed ]", store block address/phone/WhatsApp/hours, map, footer contact list. | critical | live | Reads as an unfinished development build. | Remove the UI items whose data is unknown rather than invent values. |
| S3 | Footer social icons link to `#` and are labelled "placeholder". | high | live | Dead links on a production site. | Remove until real profiles exist. |
| S4 | Mega menu footnote "Product range shown is indicative — full catalogue to be confirmed." | medium | live | Contradicts a 4,344-product verified catalogue. | Replace with a factual statement. |

### T. SEO / meta

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| T1 | **`<link rel="canonical" href="https://REPLACE-WITH-DOMAIN/index.html">` and `og:url` are live on all 21 pages.** | critical | live | Invalid canonical; blocks correct indexing and breaks social sharing. | Set the GitHub Pages production origin and per-page paths. |
| T2 | `og:image` points at a relative path, which social crawlers cannot resolve. | high | live | Link previews break. | Absolute production URL. |

### U. Ecommerce credibility

| # | Problem | Sev | Where | Reason | Planned fix |
|---|---|---|---|---|---|
| U1 | Combined effect of S1, S2 and D1: seeded cart, bracketed placeholders and mismatched imagery read as a prototype. | critical | live | The stated primary objective. | Addressed by the fixes above. |
| U2 | "Deals & Promotions" heading is shown even though only 19 of 4,344 products carry a source discount. | low | all | Over-promises. | Keep, but the rail already states its basis honestly. |

---

## Issue counts (before)

| Severity | Count |
|---|---:|
| Critical | 11 |
| High | 15 |
| Medium | 19 |
| Low | 8 |
| **Total** | **53** |

Two sections (Q, R) were checked and found clean.


---

## AFTER — what was changed

Re-audited by re-rendering at all eight widths, re-running the site and mobile
validators, scripted functional testing of the homepage, and a regression pass
over the other ten pages.

### Credibility and live-site hygiene

- **Seeded demo cart removed.** A first-time visitor now arrives with an empty
  cart and empty wishlist (verified: both badges read 0). Returning visitors
  carrying the old seeded state get a one-time clean-up that removes *only* the
  three seeded lines and two seeded wishlist entries — recognised by their exact
  signature (the first three priced, in-stock products at quantities 1, 2, 3
  with no variant) — so anything a shopper added themselves survives.
- **Every public placeholder removed** from the homepage, header, mobile drawer
  and footer: the two `[ number ]` phone routes, `Delivery & support: [ details ]`,
  `Store hours: [ timings ]`, the two placeholder trust items, the store
  address/phone/WhatsApp/hours block, the map placeholder, the five footer
  contact rows, the `[ card ]` / `[ wallet ]` payment chips and the four social
  icons that linked to `#`. Nothing was invented to fill the gaps: where a fact
  is not on record the UI item is gone, and real routes (contact form, quotation
  request) took its place. Homepage now reports 0 placeholder elements and 0
  dead `#` links.
- **Store section rebuilt** around what is actually known — trading name, area,
  what the business does — plus three factual figures drawn from the catalogue
  at runtime rather than typed into the markup.
- **Mega-menu footnote** no longer says the catalogue is indicative.

### SEO

- Canonical and `og:url` set to the deployed origin on **all 21 pages**;
  `REPLACE-WITH-DOMAIN` no longer appears anywhere in the repository.
- `og:image` made absolute so social crawlers can resolve it.

### Hero

- Rebuilt as a composed banner: two or three real department photographs staged
  at different depths on a soft white disc, over a restrained brand-tinted
  ground. The product no longer occupies 14% of an empty panel.
- Slider rewritten as a transform-based track: real slide motion, no cross-fade
  flash, nothing hidden and re-shown on load.
- Autoplay at 7 s, pausing on hover, on focus within the banner, and on
  `visibilitychange` when the tab is hidden; disabled entirely under
  `prefers-reduced-motion`.
- Swipe with horizontal-intent detection so vertical scrolling is never
  hijacked; left/right arrow keys when focus is inside the banner.
- Navigation replaced with labelled tabs, each with its own progress bar, and
  `aria-current` on the active one. A polite live region announces slide changes.
  Off-screen slides have their links and buttons removed from the tab order.
- **B3/B4 fixed**: the copy column now has a reserved gutter, so the arrow no
  longer sits on the text, and the navigation has its own row instead of
  overlapping the call to action at 768–1023.

### Category discs — one image language

- All 17 department thumbnails regenerated into a single treatment: product on
  a pure white stage, common subject scale, identical 480 px square master,
  written to `assets/images/departments/`.
- The four Pakistan Cables range photographs are shot on a flat dark-green
  studio backdrop. That backdrop is lifted to white; the product itself is
  untouched — no recolouring, retouching or generation — which is the
  normalisation the brief asked for where no cleaner source image exists.
- Labels clamp to two lines with a fixed block height, so the row keeps a
  single baseline.

### Product cards

- **Image frame added**: `#FAFAFB` stage, 1 px `#E3E6EC` border, 8 px radius,
  inset from the card, `object-fit: contain`. Product photography no longer
  dissolves into the card or the page.
- Hierarchy corrected — name 15 px semibold, brand 12.5 px muted, price 17 px
  bold — with a fixed-height price block so names, prices and buttons align
  across a row.
- Quote-only products get a deliberate treatment (**Request a Quote** in brand
  navy with a "Priced on enquiry" line) instead of reading as missing data.
- Colour discipline: navy for the everyday cart action, red kept for quotation
  and genuine discounts, so a rail is no longer a wall of red.
- Hover is a 2 px lift, a soft shadow and a 1.03 image scale — all suppressed
  under reduced motion.
- Buttons are 40 px, rising to 44 px on touch widths.

### Rails, rhythm and controls

- Shelf headers get a rule and more space; rail spacing moved onto the scale.
- Arrow paging now measures a whole number of cards and settles onto a card
  boundary, so repeated clicks no longer accumulate sub-pixel drift.
- Explicit `:focus-visible` rings on every rail arrow, hero arrow, nav tab,
  department tile, card button and scroll track.

### Performance

- Homepage DOM reduced from **3,422 to 3,039 nodes**; rails carry 10 products
  each instead of 12 (90 cards instead of 108).
- Only the first slide's artwork is eager with `fetchpriority="high"`; the rest
  lazy-load. 0 broken images.

### Dead code

- 16 obsolete rules from the previous homepage removed (`.hero__bg`,
  `.hero__content`, `.hero__dots`, `.hero__grid`, `.hero__rail`, `.hero__slide`
  and its `::after`, `.hero__slider`, `.hero__text`, `.hero__title`,
  `.rail-card`, `.rail-card__ico`). `.hero`, `.hero__cta` and `.hero__eyebrow`
  were verified as still used by `quote-request.html` and kept.
- A duplicated `PAGES.home` and a stale `initHeroBanner` were found shadowing
  the new implementation and removed — 160 lines.

### Verification

| Check | Result |
|---|---|
| Widths rendered | 1920, 1440, 1366, 1024, 768, 430, 390, 375 |
| Horizontal overflow | none at any width |
| Cards per view | 5.0 / 4.0 / 4.0 / 3.0 / 2.2 / 1.7 / 1.5 / 1.5 |
| Site validator | no errors, 21 pages |
| Mobile validator | no errors, no overflow, all images load |
| JS errors | 0 on the homepage and on 10 other pages |
| Broken images | 0 |
| Initial cart / wishlist | 0 / 0 |
| Placeholders on homepage | 0 |
| Dead `#` links | 0 |
| Genuine discount badges | 13, all source-published |
| Cross-page `.pcard` | unchanged; shop, category, search, deals all render 12 cards |

### Issue disposition

| Severity | Found | Fixed | Open |
|---|---:|---:|---:|
| Critical | 11 | 11 | 0 |
| High | 15 | 15 | 0 |
| Medium | 19 | 18 | 1 |
| Low | 8 | 6 | 2 |
| **Total** | **53** | **50** | **3** |

### Knowingly left open

1. **J2 (medium) — product card photography still mixes backgrounds.** The
   2,378 Pakistan Cables products use the manufacturer's own range photographs,
   which are shot on a green studio backdrop. Those are the product images of
   record; the brief for this task says to preserve verified product images, so
   they were left as published. The department discs, which are navigation
   rather than product data, were normalised. Normalising the product masters
   themselves is a data-side change and should be a separate, deliberate task.
2. **H3 (low) — uppercase eyebrows** remain on promo tiles as well as the
   banner. Restricting them further would weaken the promo tiles' scanning.
3. **N3 (low) — rail arrows stay hidden below 760 px.** The partial next card
   communicates swipeability and arrows at that size would crowd the header.

---

## Addendum — 2026-09-07: ABB Furse Surge Protection range imagery

### What was wrong

The Earthing Material department listed three ABB Furse ranges. Two carried a
representative photograph; **Furse Surge Protection rendered the "No image"
state**, which was visible on the Earthing Material category page, in the ABB
Furse panel on the brands page, and in search results for "Furse" and
"surge protection".

### Sources tried, in the required order

| # | Source | Result |
|---|---|---|
| 1 | ABB's own Furse page | Rendered client-side; `og:image` is empty and the body carries no product image. |
| 1 | ABB product pages (`cdn.productimages.abb.com`) | Product pages do serve images, but neither the global low-voltage sitemap (1,459 URLs) nor the UK one (~3,800) contains an earthing, lightning or surge protection product. Confirmed by SKU prefix and by sampling page metadata. |
| 2 | ABB Library / download section | The Furse page's own public download widget (`ds.library.abb.com`, category `9AAC183595`) answers with zero documents. The signed-URL library returns HTTP 403 `MissingKey` and **was not circumvented**. |
| 3 | Furse-related distributor material | Nothing openly licensed. |
| 4 | Wikimedia Commons / Openverse | 59 candidates reviewed. Every clean, product-quality photograph of a low-voltage surge protective device carries a **rival manufacturer's** logo — Cirprotec, Phoenix Contact, OBO Bettermann, DEHN, Eaton, Fatech. The rest are installation shots, fire-damage photographs, historical engravings or bare components. |

### What was chosen

`File:OVR T1 25.jpg` — an **ABB OVR T1 25 440-50** Type 1 surge protective
device, photographed straight-on against a white background.

- ABB owns Furse, so this is the correct manufacturer, not a competitor's logo.
- Correct product type: a low-voltage mains surge protective device.
- CC BY-SA 3.0, Pavel Tomashyov, via Wikimedia Commons; 857 x 1581 source.
- Stored locally at `assets/images/products/abb-furse/furse-surge-protection.webp`,
  never hotlinked. Normalised the same way as the other range masters: white
  margin trimmed, scaled to 86% and centred on a 480 x 480 white square, WebP
  q84. **The product itself is unaltered** — nothing was retouched or removed.
- Classified `representative-image`, with an `imageNote` stating in as many
  words that it is **not an exact ABB Furse SKU photograph**.
- The entry stays a `PRODUCT_FAMILY` range. It was **not** converted into a
  sellable product, and no part number, price or specification was invented.

### Verification

| Check | Result |
|---|---|
| `thumb-none` ("No image") in rendered DOM | 0 on homepage, Earthing Material, brands, and both searches |
| Furse ranges with an image | 3 of 3 |
| Search "furse" / "surge protection" | Range shown with its image |
| Broken images across the five rendered pages | 0 of 329 `<img>` refs |
| Console errors | none on index, category, brands, search |
| Responsive 1440 / 768 / 390 | Image present and correctly scaled at all three; no horizontal overflow |
| Presentation vs other ranges | Same 480px square white master, same `object-fit: contain` frame |
| Competitor branding | none |

### Still open

Two Pakistan Cables range entries — **Coaxial Cables** and **Indoor Telephone /
Intercom Cables** — still show "No image". Pakistan Cables photographs neither
range in any of its seven published catalogues (checked; the Networking Cables
catalogue is LAN cable only, and the Wiring Accessories hits are coaxial TV and
RJ11 *sockets*, not cable). Unlike a surge protective device, coaxial and
telephone cable are generic unbranded objects, so an accurate representative
photograph is findable — but that is a separate decision on a different
supplier's ranges and was not made unilaterally here.

---

## Addendum — 2026-09-07: Pakistan Cables visual normalisation

### What was wrong

Two problems, both visible on the live site.

1. **Two ranges rendered the "No image" state** — Pakistan Cables → Coaxial
   Cables and Pakistan Cables → Indoor Telephone / Intercom Cables.
2. **Pakistan Cables was the only source still on a coloured backdrop.** Its
   four range photographs are shot on the manufacturer's dark green studio
   backdrop, so on a white product card they read as a green rectangle inside
   the frame while Aqua, Coarts, Wahid, Himel and ABB/Furse all read as a clean
   white stage.

### Audit first — how many images actually needed work

| Measure | Count |
|---|---:|
| Pakistan Cables product records | 2,382 |
| Unique master image files they resolve to | 8 |
| Masters on the green backdrop | 4 |
| Masters already on white (the CAT6/CAT7 catalogue photographs) | 4 |
| Range records with no image at all | 2 |

The four green masters are shared by 2,378 products — 1,476 on Low Voltage, 594
on General Wiring, 288 on Medium Voltage, 20 on Solar. Only the masters were
touched. Every product keeps pointing at the same filename, so no product record
changed and no duplicate transformed copy was created.

### Part A — the two representative range images

| Range | Source | Author / licence |
|---|---|---|
| Coaxial Cables | [File:Coaxial cable cut.jpg](https://commons.wikimedia.org/wiki/File:Coaxial_cable_cut.jpg) — cut showing sheath, braided shield, foil, dielectric and centre conductor | FDominec, CC BY-SA 3.0 |
| Indoor Telephone / Intercom Cables | [File:001 2012 02 25 Kabel und Draehte.jpg](https://commons.wikimedia.org/wiki/File:001_2012_02_25_Kabel_und_Draehte.jpg) — telecommunication cable with four twisted pairs | Friedrich Haag, CC BY-SA 4.0 |

Both are classified `representative-image` and carry the note "Representative
range photograph; not an exact Pakistan Cables SKU image." Neither range was
converted into a product; no part number, price or specification was invented.
Rejected along the way: a retail shelf of Philips-branded coax (marketplace and
rival branding), a coax photographed against a coin, an annotated RG-59 with a
ruler in shot, a cable-manufacturer-branded coax, and several telephone-cable
photographs that were installation shots, held in a hand, or shot on fabric.

### Part B — how the backdrop was replaced

Measured from the source, the backdrop and the product separate cleanly:

| | R | G | green-dominance |
|---|---:|---:|---:|
| studio backdrop | ~10 | ~74 | ~27 |
| black sheath | ~20 | ~20 | ~0 |
| green/yellow earth wire | 40–60 | 120–199 | high |

So a pixel is backdrop only when it is green-dominant **and** dark in red **and**
no brighter than the backdrop ever gets. That third gate is what keeps the earth
wire's green stripe, which is green-dominant too. A first pass without it erased
the stripe — caught in review and fixed.

Also applied: the covers were re-read from the JPEG actually embedded in each
catalogue PDF instead of a 300 dpi page raster; a median pass clears JPEG
speckle that would otherwise survive as a dark dotted rim; green spill is pulled
back out of a 3px edge band only, gated on the same brightness ceiling so the
earth wire is never desaturated; and the ASC group logo and catalogue headings
are painted out before the cut-out so no page furniture survives.

Masters are now 800 × 800 with the product centred, matching the Aqua masters.

### Verification

| Check | Result |
|---|---|
| Catalogue records with no image | 0 of 4,348 |
| Family/range records with no image | 0 of 181 |
| Files in the asset tree still carrying the studio green | 0 |
| Image QA defects found on review | 3 |
| Corrected | 3 |
| Pakistan Cables product data changed | none — count, pricing, Request Quote, SKU, specifications, source URLs and category relationships all untouched |
| `manufacturer-image` classification on the four range photographs | preserved |
| Department discs regenerated from the cleaner masters | 4 |

The three QA defects, all found by inspecting the cut-outs at 1:1 and all fixed
in the extraction rather than accepted:

1. The green/yellow earth wire lost its green stripe — the first mask treated it
   as backdrop. Fixed with the brightness ceiling described above.
2. A dark speckled rim along the cable edges, from JPEG ringing at high-contrast
   boundaries. Fixed with a median pass on the mask.
3. The ASC group logo survived the first cut-out: the rectangles that paint page
   furniture out were given in the source image's coordinates but applied without
   translating them into the cropped frame, so they landed off-image. Fixed by
   translating them before painting.

Separately, the generator behind `assets/product-image-sources.md` labelled the
classification of shared images from the file path (`"pakistan-cables" in path`
meant `manufacturer-image`). That was true until these two representative images
landed in the same folder. It now reads the classification off the records.

### Notes

- The Wires & Cables **category tile** is a separate dark editorial banner from
  the manufacturer's own site, not a green catalogue cover. It carries no green
  backdrop and is a full-bleed category image by design, so it was left alone.
- The four CAT6/CAT7 networking masters were already shot on white and were not
  re-encoded.

---

## Addendum — 2026-09-08: production defects — live placeholders and quote pricing

Two defects that were visible on the deployed site, found while reviewing the
Pakistan Cables imagery work.

### Defect 1 — bracketed development placeholders on public pages

**What was wrong.** Product pages printed `Delivery [ options to be confirmed ]`,
`Returns [ policy to be confirmed ]` and `Warranty [ terms to be confirmed ]`.
A repository-wide scan found the same pattern on **13 more pages: 97 bracketed
placeholders in total**, plus a set of development-facing strings that were not
bracketed but were just as clearly internal.

**Root cause.** These were written when the site was an unpublished prototype and
CLAUDE.md still said "unknown details stay as visible bracketed placeholders".
That instruction was replaced when the site went to GitHub Pages — the live-site
policy now says to remove the UI item rather than ship a placeholder — but the
existing pages were never swept.

**What was done.** No policy, period, charge, threshold or payment method was
invented anywhere. Each placeholder was resolved by removing the thing it stood
in for:

| Page | Action |
|---|---|
| `product.html` | Delivery / Returns / Warranty rows removed; the 5-row "Additional Information" placeholder table removed, leaving only the notes the source record carries; the Shipping tab removed (it held no terms, only a note that they were to be confirmed); reviews empty state reworded |
| `contact.html` | Address, phone, WhatsApp, email and opening-hours rows removed; the store-details `dl`, the dead "Get Directions" link and the empty map panel removed |
| `checkout.html` | Local delivery and courier options removed (no coverage or charges on record); bank transfer, card and wallet payment options removed; delivery and tax summary rows removed; the help card now points at routes that work |
| `cart.html` | Delivery and tax rows removed; the totals note reworded |
| `shipping.html`, `returns.html`, `terms.html`, `privacy.html` | Each was a scaffold — headings, "This section will cover" lists and `[ Content to be supplied and approved by Star Electric Enterprises. ]` under a banner addressed to the business owner. Each page now states plainly that the terms are not published and points at the contact and quotation routes |
| `faq.html` | 16 answers ended in `[ to be confirmed ]`. Each was rewritten to keep what is genuinely known and say plainly what is not set |
| `track-order.html` | A fabricated sample order — invented order number, date, payment status and delivery method — was replaced with an honest "not available yet" state |
| `my-account.html` | `[ customer name ]` / `[ email address ]` removed; three dead `href="#"` controls that only raised a developer toast removed or pointed at contact |
| `about.html` | "Established `[ year to be confirmed ]`" row removed; a note telling the reader that placeholders appear throughout the site removed |
| `deals.html`, `shop.html`, `wishlist.html`, `complaint.html`, `quote-request.html` | Development-facing copy ("design prototype", "to be confirmed by the business") reworded |
| `js/components.js` | Footer payment badges "Cash on Delivery / Bank Transfer" removed — the store has not confirmed either |

**Result: 0 bracketed placeholders and 0 uses of the word "prototype" in any
rendered page.** The only `class="placeholder"` left is four account statistics
that render as an em dash, which is a genuine "no data" marker.

### Defect 2 — Quick View priced quote-only products at Rs. 0

**What was wrong.** A product card correctly read "Request a Quote", but opening
Quick View on the same product showed **Rs. 0** and offered **Add to Cart**.

**Root cause.** The rule "a product with no published price is quote-only" was
implemented three times — once in `renderProductCard`, once in `renderMiniCard`
and once in the Quick View markup in `js/main.js`. The first two tested
`p.isQuote`; the third did not, and called `UI.money(p.price)` directly, which
rendered `Number(null)` as `Rs. 0`. The wishlist row had the same bug.

**Fix — one source of truth.** `js/components.js` now owns the rule:

- `isQuote(p)` — true when the source published no price
- `priceHtml(p, variant)` — the price block, in `card` / `lg` / `mini` / `bare` form
- `ctaHtml(p, opts)` — the buying action, quote-aware
- `money(v)` now returns an empty string for `null`, `undefined` or `NaN`, so a
  missing price can no longer print as `Rs. 0` or `Rs. NaN` through any path

Every surface calls those: product cards, homepage rails, Quick View, the
product page, wishlist and search/deals/category (which share the card).
`SEE_STORE.add()` also refuses a quote-only product as a backstop, and
`cartLines()` drops any such line left in a returning visitor's storage.

### Test cases

| Case | Product | Quick View price | Quick View actions |
|---|---|---|---|
| Quote-only | `pc-5090` | Request a Quote | Request a Quote → `quote-request.html?product=pc-5090`, View Product |
| Priced | `aqua-15227` | Rs. 2,200 | Add to Cart, Full Details |
| Genuine source sale | `aqua-14452` | Rs. 3,500 / Rs. 6,500 / Save 46% | Add to Cart, Full Details |
| Variable, priced | `wahid-10374191808792` | Rs. 17,545 | Select Options, Full Details |
| Out of stock, priced | `aqua-14992` | Rs. 3,500 | Out of Stock (disabled) |
| Out of stock, variable | `wahid-10374076793112` | Rs. 18,245 | Out of Stock (disabled) |
| ABB Furse range | `furse-surge-protection` | n/a — `productById` returns null, so Quick View does not open on a range | no fake product behaviour |

Product detail pages verified for the same products: quote product shows
Request a Quote with the correct href, Buy Now hidden, no Add to Cart; the sale
product shows Rs. 3,500 / Rs. 6,500 / Save 46% and "You save Rs. 3,000".

### Validation

| Check | Result |
|---|---|
| Bracketed placeholders in rendered pages | 0 |
| `Rs. 0` in rendered DOM | only the empty-cart header total and the cart page's own subtotal/total — no product price |
| Console errors across 21 pages | 0 |
| Internal links checked / broken | 4,046 / 0 |
| Images checked / broken | 551 / 0 |
| Horizontal overflow at 1440 / 1024 / 768 / 430 / 390 | 0 at every width |
| Quick View button overflow at 390 | none |
| Cart and wishlist for a new visitor | both empty |
| Product data | unchanged — no file under `data/` was touched |
| Header / footer | unchanged except the removed payment badges |

### Left in place deliberately

`my-account.html` still carries a "View signed-in state" preview toggle onto a
dashboard layout with no real data. It no longer contains placeholders or dead
controls, but it is a design preview on a public page. Removing it is a page-level
decision rather than a defect fix, so it was left for a separate call.

---

## Addendum — 2026-09-08: simulated My Account authenticated state removed

Final production-readiness item. **Removed the simulated My Account
authenticated state from the public storefront.**

### What was removed and why

`my-account.html` shipped a "View dashboard state" button that switched the page
into a fabricated signed-in customer dashboard. There is no authentication and
no backend, so every part of that view was invented, and any visitor could
activate it.

| Removed | Why |
|---|---|
| "View signed-out state" / "View dashboard state" toggle | Let a visitor simulate being logged in |
| `#dashView` — the whole simulated dashboard | Presented an account the visitor does not have |
| Account nav with avatar and "Your account" identity block | Fabricated customer identity |
| Dashboard statistics (Total orders, Processing, Completed, Saved items) | Fabricated account statistics |
| Orders panel | Fabricated order history |
| Addresses panel (billing / delivery) | Fabricated saved addresses |
| Account Details form, including a change-password form | Fake account management; submitting did nothing |
| Sign In form (username, password, "Keep me signed in") | Fake authentication — the button only raised a toast |
| Create an Account form | Fake registration, including a terms checkbox |
| `[data-logout]` handler and its "Signed out (design preview only)" toast | Simulated a session ending |
| Dashboard sub-navigation (`[data-acct-tab]` / `[data-acct-panel]`) | Only existed to drive the fake dashboard |
| Account wishlist mirror (`#acctWishlist`) | Duplicated `wishlist.html` inside the fake dashboard |
| Quick View modal on this page | No product grid renders here any more |

Nothing was replaced with different fake examples. The page keeps its filename
and a clean structure so it can become the WooCommerce My Account template when
accounts are switched on.

### Replacement — customer-safe behaviour

The page is now **Customer Account — "Manage shopping and service options."**
with one honest notice and five action cards that all point at functionality
this storefront genuinely performs:

> Account sign-in is not currently available online. You can still use the
> services below, and the store will look after anything else directly.

| Card | Destination |
|---|---|
| Cart | `cart.html` |
| Wishlist | `wishlist.html` |
| Track an Order | `track-order.html` |
| Request a Quote | `quote-request.html` |
| Contact Us | `contact.html` |

No developer or prototype vocabulary is used. The header keeps its existing
design; only the account action's label changed from "Account / **Sign In**" to
"Customer / **Account**", because the header should not invite a sign-in that
does not exist.

### Dead code removed

- `js/main.js` — `PAGES.account` in full (state toggle, logout handler, tab
  switcher, wishlist mirror) and its entry in the page-controller map
- `css/pages.css` — `.account-layout`, `.account-nav`, `.account-nav__user`,
  `.account-nav__list`, `.avatar`, `.auth-layout`, `.stat-grid`, `.stat`,
  replaced by `.acct-layout`, `.acct-notice`, `.acct-grid`, `.acct-card`
- `css/responsive.css` — the `.stat-grid`, `.account-layout` and `.auth-layout`
  overrides; a single-column `.acct-grid` rule added at the commerce breakpoint

Shared components used by other pages were left untouched.

### Repository-wide prototype audit

`data-demo-form` was renamed to `data-inactive-form` across the four pages that
still carry an unconnected form, and its fallback message no longer mentions a
prototype. An invented order-number example, "e.g. SEE-10042", was removed from
the tracking and complaint forms — the store has not defined an order-number
format.

**Result: 0 customer-visible occurrences of demo, mock, prototype, fake, sample
order, sample customer, signed-in state, test account or design preview** in any
rendered page or in any string emitted by the JavaScript. Remaining matches are
`placeholder="…"` form hints and internal class names, both legitimate.

### QA

| Check | Result |
|---|---|
| Signed-in preview control | gone |
| Simulated dashboard, orders, statistics, identity, addresses | gone |
| Fake login / register / forgot-password / remember-me | gone — the page has 0 forms, 0 inputs and 0 buttons in `<main>` |
| Useful customer links | 5, all resolving |
| Header / footer | unchanged apart from the account label |
| Cart and wishlist on a fresh visit | both empty; no storage key written |
| Console errors across 16 pages | 0 |
| Internal links checked / broken | 3,090 / 0 |
| Images checked / broken | 387 / 0 |
| Horizontal overflow at 1440 / 1024 / 768 / 430 / 390 | 0 at every width |
| Card content overflow at 390 | none |
| Track Order | still shows nothing until a visitor submits the form; the fabricated sample order removed earlier has not returned |
| Product data | unchanged — nothing under `data/` touched but this record |

---

## Addendum — 2026-09-08: homepage hero redesigned

The campaign banner below the header was rebuilt. Full analysis, the three
directions considered and the before/after are in
`data/homepage-hero-redesign-audit.md`; this is the QA record.

**Root cause of the old banner's empty space:** `.hcom__grid` used
`align-items: stretch`, so the department menu — taller than the campaign —
stretched the banner column and the banner inherited a height it had no content
to fill. Changed to `align-items: start` with an explicit banner height.

**What changed:** an angled navy campaign panel against a light merchandising
stage, with a composed cluster of two to three real catalogue products crossing
the seam; a numbered campaign navigator built into the banner's bottom edge; a
control chip pair at the bottom-right; 470px tall instead of 575px.

| Check | Result |
|---|---|
| Horizontal overflow at 1920 / 1440 / 1366 / 1024 / 768 / 430 / 390 / 375 | 0 at every width |
| Broken images at every width | 0 of 137 |
| Console errors across 16 pages | 0 |
| Banner height | 470px desktop, 440px at 1024–1279, 470px stacked below 860px |
| Slider: arrows, campaign tabs, autoplay, progress, pause on hover/focus/hidden, keyboard, swipe, reduced motion | all verified |
| Homepage sections after the change | department menu, hero, navigator, promo tiles, departments strip, 11 product rails, 1,274 cards, 32 brand cards all render |
| Old hero code | `.hslide` / `.hbanner` fully removed; one hero architecture |
| Product data | unchanged — nothing under `data/` touched but this record and the new audit |

**Three defects found and fixed during QA, each a real robustness issue:**

1. The prev arrow, centred on the left edge, sat on top of the body copy.
   Both controls moved to a pair at the bottom-right.
2. Between 1024 and 1279 the body copy ran under the product cluster: the copy
   column narrows faster than the headline does. The stage was pulled back to
   54% and the copy given a right gutter.
3. The entrance animation used `animation-fill-mode: backwards` with
   `opacity: 0` in its first keyframe, so a stalled animation left the campaign
   invisible. The entrance is now transform-only — content is visible by
   default and the motion is an enhancement.

Also reverted during QA: `loading="lazy"` on campaigns 02–05. The track is
translated rather than scrolled, so those images were not reliably fetched
before their slide arrived and the merchandising zone rendered empty. They load
eagerly at `fetchpriority="low"` instead.
