# Editing the site in Elementor

A guide for editing Star Electric Enterprises without touching code.

Everything below is done from the WordPress dashboard at
`https://salmon-antelope-713580.hostingersite.com/wp-admin/`. You never need
Git, PHP or a developer to change wording, links, buttons or images.

---

## 1. The one thing to understand first

The site's *look* comes from a stylesheet that was approved before the build
started. The site's *content* lives in Elementor.

That is why each section of the page is a single Star Electric widget with a
panel full of text boxes, rather than a pile of separate heading, text and
button widgets you can drag around. You can change **what a section says**;
you cannot rearrange **how it is built**, because the approved design depends
on that structure.

In practice this means:

- ✅ Change a headline, a paragraph, a button label, a link, an image
- ✅ Add, remove or reorder rows — campaign slides, promo tiles, cards, FAQ
  questions, footer links
- ✅ Reorder or remove whole sections on a page
- ❌ Drag the logo out of the header, or split a product rail apart

If you ever need the second kind of change, that is a design change and needs
a developer.

**Products, prices, brands and departments are never typed into Elementor.**
They are read from the catalogue every time a page loads, so a price change or
a new import appears on the site by itself.

---

## 2. Where everything is

| What you want to edit | Where to go |
|---|---|
| Header | **Templates → Theme Builder → Header → Site Header** |
| Footer | **Templates → Theme Builder → Footer → Site Footer** |
| Homepage | **Pages → Home → Edit with Elementor** |
| FAQ | **Pages → FAQs → Edit with Elementor** |
| Shipping | **Pages → Shipping → Edit with Elementor** |
| Returns | **Pages → Returns → Edit with Elementor** |
| Privacy Policy | **Pages → Privacy Policy → Edit with Elementor** |
| Terms & Conditions | **Pages → Terms & Conditions → Edit with Elementor** |
| About | **Pages → About Us → Edit with Elementor** |
| Brand colours and fonts | **Elementor → Site Settings** |

Pages not in that list are still rendered by the theme and are not yet
Elementor-editable — see section 7.

---

## 3. Finding a section: the Navigator

Once the editor is open, press **⌘/Ctrl + I**, or right-click any section and
choose **Navigator**. You get a list of the page's sections by name:

```
HOME — Campaign Banner
HOME — Trust Strip
HOME — Shop by Department
HOME — Product Rails
HOME — Deals & Promotions
HOME — Shop by Brand
HOME — Bulk / Project CTA
HOME — Why Choose Us
HOME — Store Band
```

Click one to select it; its settings appear in the left panel. Drag a row in
the Navigator to move that section up or down the page.

---

## 4. The things you are most likely to change

### The hero campaign slides

**Pages → Home → Edit with Elementor → HOME — Campaign Banner → Campaign
slides.**

Each slide is a row. Open one and you get: the small label above the headline,
the headline, the supporting text, the button label and link, an optional
second button, the colour tone, and the image. Use the ✛ handle to reorder
slides, the ✕ to remove one, **Add Item** for a new one.

Leave **Campaign image** empty to keep the approved artwork; the *Approved
artwork name* box below it chooses which one (`protection`, `cables`,
`switches`, `lighting`, `smart`).

### The three promo tiles

Same section, **Promo tiles** panel. Each tile has a small label, a heading,
the link text and the link. Leave the image empty and set a department slug in
*Department picture* to reuse the department's own photograph.

### Why Choose Us

**HOME — Why Choose Us.** The heading block is at the top; each card below is a
row with an icon, a heading and a description. The icon list is the design's
own icon set — Elementor's icon library is deliberately not offered, because
mixing icon sets would be visible.

### The Bulk / Project call to action

**HOME — Bulk / Project CTA.** Heading, text, both buttons and the numbered
steps. The 01 / 02 / 03 numbers are added for you.

### A product rail

**HOME — Product Rails** shows every approved rail in order. To change which
rails appear, delete that section and add individual **Product Rail** widgets
instead: each one lets you set the heading, pick a department, and choose how
many products to show. The products themselves always come from the catalogue.

### The header

**Templates → Theme Builder → Header → Site Header.** Panels for the top bar
(location and links), the search box (placeholder and button label), the
account / wishlist / cart labels and the *Request a Quote* button, and the
mega-menu and mobile-drawer wording.

The menu items and the departments inside the mega menu come from the
catalogue and the site's navigation, not from this panel.

### The footer

**Templates → Theme Builder → Footer → Site Footer.** One panel per column.
The company text, every column heading and every link is editable; the Shop
column starts with a number of departments read from the catalogue (set how
many under *Departments listed first*) and then lists whatever extra links you
add.

### The About page

**Pages → About Us → Edit with Elementor.** Four sections:

- **ABOUT — Who We Are** holds the article and the sidebar. The article is a
  list of blocks — each heading and each paragraph is its own row, so one can
  be reworded, moved or removed without touching the rest. One block type is
  *Department list*, which draws itself from the catalogue.
- **ABOUT — How We Do Business** is the six cards and the standing note beneath
  them. That note records what the page deliberately does *not* claim — no
  establishment year, customer count, award, certification, dealership or
  partnership. Please leave it there until the business confirms those facts.
- **ABOUT — What We Stock** is the department cards. Only the heading and the
  link are editable; the cards come from the catalogue.
- **ABOUT — Ready to Order** is the closing call to action.

Write `{departments}` anywhere in the article, the sidebar or a section's
supporting line and the number of departments is filled in from the catalogue
when the page loads. `{site}` does the same for the business name. That is why
neither is typed out: the page cannot then disagree with the shop.

### FAQ questions

**Pages → FAQs → Edit with Elementor → FAQ — Questions.** Every question is a
row with a topic, the question and the answer. The topic filter buttons at the
top of the page build themselves from the topics you use, so typing a new
topic name creates a new filter.

To put a link in an answer, write it as `[the words](https://the-url)`.

---

## 5. Saving, and undoing a mistake

Click **Update** (bottom-left) to publish. Elementor keeps a full history:

- **Ctrl/⌘ + Z** undoes the last change while you are editing.
- **History** in the panel footer lists every action *and* every saved
  revision — click a revision to roll the page back to it.

If a page ever looks wrong after an edit, open History and pick the revision
from before your change.

---

## 6. Things that are deliberately not editable

These are not oversights. Please do not work around them:

- **No product prices, stock or names.** They come from the supplier
  catalogue. Editing them here would make the site disagree with the shop.
- **No delivery charge, return window or warranty term** on the Shipping,
  Returns, Privacy or Terms pages. The store has not set those. Those pages
  say so on purpose, and the widget repeats that warning in its panel.
- **No phone number, WhatsApp number or opening hours.** None has been
  supplied. When they are, ask for them to be added properly rather than
  typing them into a text box.
- **No brand logos.** Brand tiles are monograms because no dealership or
  distribution relationship is on record.
- **No discount, countdown or offer.** The Deals section shows only reductions
  a supplier's own catalogue publishes.

---

## 7. What is not in Elementor yet

These pages still render from PHP templates in the child theme and are **not**
editable in Elementor:

| Page | Template |
|---|---|
| Contact | `page-contact.php` |
| Request a Quote | `page-quote-request.php` |
| Submit a Complaint | `page-complaint.php` |
| Track Order | `page-track-order.php` |
| Categories | `page-categories.php` |
| Brands | `page-brands.php` |
| Deals | `page-deals.php` |
| Wishlist / Cart / Checkout / My Account | their own templates |
| Shop, category and product pages | `archive-product.php`, `single-product.php` |

They work exactly as before. Converting them follows the same pattern as the
pages above: a widget per section in
`star-electric-core/elementor/widgets/`, a document built from those widgets,
the result compared against the screenshot baseline, and only then the
`page-{slug}.php` template removed.

---

## 8. If something looks broken

1. **Purge the cache.** LiteSpeed Cache → Toolbox → Purge All. The site caches
   pages, so an edit can appear not to have taken.
2. **Check History** in the Elementor editor and roll back to the last good
   revision.
3. The pre-conversion backup is recorded in `wordpress/migration-audit.md`,
   and every code change is in Git.
