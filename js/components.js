/* ==========================================================================
   STAR ELECTRIC ENTERPRISES — Shared UI components
   --------------------------------------------------------------------------
   The header, mega menu, mobile drawer and footer are defined ONCE here and
   injected into every page. That is deliberate: on WordPress these three
   functions become header.php, the nav walker and footer.php, and no page
   markup has to change.

   renderProductCard() is the single product card used by the home page,
   shop, category, search, deals, wishlist and "related products" — it maps
   directly onto content-product.php in the future theme.
   ========================================================================== */
window.SEE_UI = (function () {
  "use strict";

  var D = window.SEE_DATA;

  /* ----------------------------------------------------------------------
     Utilities
     ---------------------------------------------------------------------- */
  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
  }
  function money(v) { return "Rs. " + Number(v).toLocaleString("en-PK"); }
  function discount(p) { return p.oldPrice ? Math.round((1 - p.price / p.oldPrice) * 100) : 0; }

  var STOCK = {
    "in":  { cls: "pill--in",  label: "In Stock" },
    "low": { cls: "pill--low", label: "Low Stock" },
    "out": { cls: "pill--out", label: "Out of Stock" }
  };

  /* ----------------------------------------------------------------------
     Icon set (inline SVG — external sprite <use> is blocked on file://)
     ---------------------------------------------------------------------- */
  var ICONS = {
    search:   '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
    user:     '<circle cx="12" cy="8.5" r="3.6"/><path d="M4.8 20a7.2 7.2 0 0 1 14.4 0"/>',
    heart:    '<path d="M12 20s-7.3-4.4-7.3-9.3A4.2 4.2 0 0 1 12 8.2a4.2 4.2 0 0 1 7.3 2.5C19.3 15.6 12 20 12 20Z"/>',
    cart:     '<path d="M3 5h2.3l2.2 9.5h9.2L19 8H7"/><circle cx="9.5" cy="18.5" r="1.6"/><circle cx="16.5" cy="18.5" r="1.6"/>',
    menu:     '<path d="M4 7h16M4 12h16M4 17h16"/>',
    close:    '<path d="m6 6 12 12M18 6 6 18"/>',
    chevdown: '<path d="m7 10 5 5 5-5"/>',
    chevright:'<path d="m10 7 5 5-5 5"/>',
    arrowright:'<path d="M5 12h13M13 6l6 6-6 6"/>',
    arrowup:  '<path d="M12 19V6M6 12l6-6 6 6"/>',
    phone:    '<path d="M5 4h4l1.6 4-2.2 1.6a12 12 0 0 0 6 6L16 13.4l4 1.6v4a1 1 0 0 1-1.1 1A16.5 16.5 0 0 1 4 6.1 1 1 0 0 1 5 4Z"/>',
    whatsapp: '<path d="M20 11.8a8 8 0 0 1-11.9 7L4 20l1.3-3.9A8 8 0 1 1 20 11.8Z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5"/>',
    pin:      '<path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/>',
    clock:    '<circle cx="12" cy="12" r="9"/><path d="M12 6.5V12l4 2"/>',
    truck:    '<path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
    shield:   '<path d="M12 3.5 19 6v6c0 4.2-2.9 7.4-7 8.5-4.1-1.1-7-4.3-7-8.5V6Z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
    headset:  '<path d="M12 3a7 7 0 0 0-7 7v4a3 3 0 0 0 3 3h1v-7H7"/><path d="M19 14v-4a7 7 0 0 0-7-7"/><path d="M19 14a3 3 0 0 1-3 3h-1v-7h1a3 3 0 0 1 3 3Z"/><path d="M16 17v1a3 3 0 0 1-3 3h-1"/>',
    lock:     '<rect x="4.5" y="10" width="15" height="10" rx="3"/><path d="M8.5 10V7.5a3.5 3.5 0 0 1 7 0V10"/><path d="M12 14v2"/>',
    tag:      '<path d="M11 3H5a2 2 0 0 0-2 2v6l10 10 8-8L11 3Z"/><circle cx="7.8" cy="7.8" r="1.4"/>',
    grid:     '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
    list:     '<path d="M4 6h16M4 12h16M4 18h16"/>',
    eye:      '<path d="M2.8 12S6.6 5.8 12 5.8 21.2 12 21.2 12 17.4 18.2 12 18.2 2.8 12 2.8 12Z"/><circle cx="12" cy="12" r="3"/>',
    filter:   '<path d="M4 6h16M7 12h10M10 18h4"/>',
    check:    '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
    plus:     '<path d="M12 5v14M5 12h14"/>',
    minus:    '<path d="M5 12h14"/>',
    trash:    '<path d="M4 7h16M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/><path d="M6 7v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7"/>',
    box:      '<path d="m12 3 8 4.2v9.6L12 21l-8-4.2V7.2Z"/><path d="m4 7.2 8 4.3 8-4.3M12 21v-9.5"/>',
    doc:      '<path d="M6 3h7l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M13 3v5h5"/>',
    mail:     '<rect x="3" y="5.5" width="18" height="13" rx="2.5"/><path d="m3.5 7 8.5 6 8.5-6"/>',
    upload:   '<path d="M12 16V5M8 9l4-4 4 4"/><path d="M4 16v2.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V16"/>',
    star:     '<path d="m12 3.6 2.6 5.4 5.9.8-4.3 4.2 1 5.9L12 17.1 6.8 19.9l1-5.9L3.5 9.8l5.9-.8z"/>',
    bolt:     '<path d="M13.5 3 6 13h5l-1.5 8L18 11h-5.5z"/>',
    info:     '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
    map:      '<path d="m4 20 6-2.2 4 2.2 6-2.4V4l-6 2.4-4-2.2L4 6.4Z"/><path d="M10 4.2v13.6M14 6.4V20"/>',
    logout:   '<path d="M15 12H4M8 8l-4 4 4 4"/><path d="M11 4h7a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-7"/>',
    building: '<path d="M4 21V6a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v15"/><path d="M14 10h5a1 1 0 0 1 1 1v10"/><path d="M7 9h3M7 13h3M7 17h3M17 14h1M17 18h1"/>',
    refresh:  '<path d="M4 12a8 8 0 0 1 13.7-5.6L20 8"/><path d="M20 4v4h-4"/><path d="M20 12a8 8 0 0 1-13.7 5.6L4 16"/><path d="M4 20v-4h4"/>'
  };

  function icon(name, cls) {
    var body = ICONS[name] || "";
    return '<svg class="ico ' + (cls || "") + '" viewBox="0 0 24 24" aria-hidden="true">' + body + "</svg>";
  }

  function stars(rating, cls) {
    var out = '<span class="stars ' + (cls || "") + '" aria-hidden="true">';
    for (var i = 1; i <= 5; i++) {
      out += '<svg viewBox="0 0 24 24" class="' + (i <= Math.round(rating) ? "is-on" : "") + '">' +
             ICONS.star + "</svg>";
    }
    return out + "</span>";
  }

  /* ----------------------------------------------------------------------
     Primary navigation definition (single source of truth)
     ---------------------------------------------------------------------- */
  var NAV = [
    { key: "home",     label: "Home",                href: "index.html" },
    { key: "shop",     label: "Shop",                href: "shop.html" },
    { key: "category", label: "Categories",          href: "category.html", mega: true },
    { key: "brands",   label: "Brands",              href: "brands.html" },
    { key: "deals",    label: "Deals",               href: "deals.html" },
    { key: "quote",    label: "Bulk / Project Orders", href: "quote-request.html" },
    { key: "about",    label: "About",               href: "about.html" },
    { key: "contact",  label: "Contact",             href: "contact.html" }
  ];

  var LOGO = "assets/logo/star-electric-logo-trimmed.png";

  function logoMarkup(cls) {
    return '<a class="logo ' + (cls || "") + '" href="index.html" aria-label="Star Electric Enterprises — home">' +
             '<img src="' + LOGO + '" alt="Star Electric Enterprises">' +
           "</a>";
  }

  /* ----------------------------------------------------------------------
     HEADER  →  header.php
     ---------------------------------------------------------------------- */
  function renderHeader(active) {
    var catOptions = D.CATEGORIES.map(function (c) {
      return '<option value="' + esc(c.slug) + '">' + esc(c.name) + "</option>";
    }).join("");

    var navItems = NAV.map(function (n) {
      var isActive = n.key === active ? " is-active" : "";
      if (n.mega) {
        return '<li class="has-mega">' +
                 '<a class="nav__link' + isActive + '" href="' + n.href + '" aria-haspopup="true" aria-expanded="false">' +
                   esc(n.label) + icon("chevdown") +
                 "</a>" + megaMarkup() +
               "</li>";
      }
      return '<li><a class="nav__link' + isActive + '" href="' + n.href + '">' + esc(n.label) + "</a></li>";
    }).join("");

    return '' +
    '<div class="topbar">' +
      '<div class="container topbar__inner">' +
        '<p class="topbar__brand">' +
          "<strong>Star Electric Enterprises</strong>" +
          '<span class="topbar__dot" aria-hidden="true">&bull;</span>' +
          '<span class="topbar__loc">' + icon("pin") + "Saddar, Rawalpindi</span>" +
        "</p>" +
        '<ul class="topbar__links">' +
          '<li><a href="contact.html" data-placeholder="WhatsApp number to be added">' + icon("whatsapp") +
            'WhatsApp: <span class="placeholder">[ number ]</span></a></li>' +
          '<li><a href="contact.html" data-placeholder="Phone number to be added">' + icon("phone") +
            'Call: <span class="placeholder">[ number ]</span></a></li>' +
          '<li class="topbar__item topbar__item--delivery">' + icon("truck") +
            'Delivery &amp; support: <span class="placeholder">[ details ]</span></li>' +
        "</ul>" +
      "</div>" +
    "</div>" +

    '<header class="header" id="siteHeaderBar">' +
      '<div class="container header__inner">' +
        '<button class="icon-btn header__burger" id="navToggle" type="button" ' +
                'aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">' +
          icon("menu") +
        "</button>" +

        logoMarkup() +

        '<form class="search" role="search" id="searchForm" action="search-results.html" method="get">' +
          '<label class="sr-only" for="searchCat">Search within category</label>' +
          '<div class="search__catwrap">' +
            '<select class="search__cat" id="searchCat" name="product_cat">' +
              '<option value="">All Categories</option>' + catOptions +
            "</select>" +
            icon("chevdown", "search__caret") +
          "</div>" +
          '<label class="sr-only" for="searchInput">Search products</label>' +
          '<input class="search__input" id="searchInput" name="s" type="search" autocomplete="off" ' +
                 'placeholder="Search cables, switches, breakers, lighting, fans and more...">' +
          '<button class="search__btn" type="submit">' + icon("search") + "<span>Search</span></button>" +
          '<div class="search__suggest" id="searchSuggest" hidden></div>' +
        "</form>" +

        '<div class="header__actions">' +
          '<a class="action" href="my-account.html">' +
            '<span class="action__ico">' + icon("user") + "</span>" +
            '<span class="action__txt"><small>Account</small><strong>Sign In</strong></span>' +
          "</a>" +
          '<a class="action" href="wishlist.html">' +
            '<span class="action__ico">' + icon("heart") +
              '<span class="badge" data-count="wishlist">0</span></span>' +
            '<span class="action__txt"><small>Saved</small><strong>Wishlist</strong></span>' +
          "</a>" +
          '<a class="action" href="cart.html">' +
            '<span class="action__ico">' + icon("cart") +
              '<span class="badge" data-count="cart">0</span></span>' +
            '<span class="action__txt"><small>Cart</small><strong data-cart-total>Rs. 0</strong></span>' +
          "</a>" +
          '<a class="btn btn--accent header__quote" href="quote-request.html">Request a Quote</a>' +
        "</div>" +
      "</div>" +
    "</header>" +

    '<nav class="nav" aria-label="Primary">' +
      '<div class="container nav__inner">' +
        '<ul class="nav__list">' + navItems + "</ul>" +
        '<p class="nav__aside">' + icon("clock") +
          'Store hours: <span class="placeholder">[ timings ]</span></p>' +
      "</div>" +
    "</nav>";
  }

  /* Mega menu built from the category tree */
  function megaMarkup() {
    var cols = D.CATEGORIES.map(function (c) {
      var links = c.subs.map(function (s) {
        return '<a href="category.html?cat=' + esc(c.slug) + "&sub=" + esc(s.slug) + '">' + esc(s.name) + "</a>";
      }).join("");
      return '<div class="mega__col">' +
               '<a class="mega__title" href="category.html?cat=' + esc(c.slug) + '">' +
                 icon("chevright") + esc(c.name) +
               "</a>" +
               '<div class="mega__links">' + links + "</div>" +
             "</div>";
    }).join("");

    return '<div class="mega">' +
             '<div class="mega__grid">' + cols + "</div>" +
             '<div class="mega__foot">' +
               "<span>Product range shown is indicative — full catalogue to be confirmed.</span>" +
               '<a class="link-more" href="shop.html">Browse the full shop' + icon("arrowright") + "</a>" +
             "</div>" +
           "</div>";
  }

  /* ----------------------------------------------------------------------
     MOBILE DRAWER
     ---------------------------------------------------------------------- */
  function renderDrawer(active) {
    var main = NAV.map(function (n) {
      return '<li><a href="' + n.href + '" class="' + (n.key === active ? "is-active" : "") + '">' +
               esc(n.label) + "</a></li>";
    }).join("");

    var cats = D.CATEGORIES.map(function (c) {
      return '<li><a href="category.html?cat=' + esc(c.slug) + '">' + esc(c.name) + icon("chevright") + "</a></li>";
    }).join("");

    return '<div class="drawer__scrim" data-drawer-close></div>' +
      '<div class="drawer__panel" role="dialog" aria-modal="true" aria-label="Site menu">' +
        '<div class="drawer__head">' +
          '<span class="drawer__title">Menu</span>' +
          '<button class="icon-btn" type="button" data-drawer-close aria-label="Close menu">' + icon("close") + "</button>" +
        "</div>" +
        '<div class="drawer__body">' +
          '<ul class="drawer__nav">' + main + "</ul>" +
          '<p class="drawer__sep">Shop by category</p>' +
          '<ul class="drawer__nav">' + cats + "</ul>" +
          '<p class="drawer__sep">Customer service</p>' +
          '<ul class="drawer__nav">' +
            '<li><a href="track-order.html">Track Order</a></li>' +
            '<li><a href="faq.html">FAQs</a></li>' +
            '<li><a href="complaint.html">Submit a Complaint</a></li>' +
          "</ul>" +
        "</div>" +
        '<div class="drawer__foot">' +
          '<a class="btn btn--accent btn--block" href="quote-request.html">Request a Quote</a>' +
          '<p class="drawer__meta">Saddar, Rawalpindi<br>' +
            '<span class="placeholder">[ phone / WhatsApp to be added ]</span></p>' +
        "</div>" +
      "</div>";
  }

  /* ----------------------------------------------------------------------
     FOOTER  →  footer.php
     ---------------------------------------------------------------------- */
  function renderFooter() {
    var shopLinks = D.CATEGORIES.slice(0, 6).map(function (c) {
      return '<li><a href="category.html?cat=' + esc(c.slug) + '">' + esc(c.name) + "</a></li>";
    }).join("");

    return '' +
    '<footer class="footer">' +
      '<div class="container footer__grid">' +

        '<div class="footer__col footer__col--about">' +
          logoMarkup("logo--footer") +
          '<p class="footer__about">Star Electric Enterprises is an electrical products store based in ' +
            "Saddar, Rawalpindi, supplying wiring, protection, lighting, fans and power equipment to " +
            "homes, offices, commercial projects, electricians and contractors.</p>" +
          '<ul class="social" aria-label="Social media placeholders">' +
            '<li><a href="#" aria-label="Facebook page placeholder" data-placeholder="Facebook page to be added">' +
              '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M14.2 8.6h2.3V6.1h-2.3a3.2 3.2 0 0 0-3.2 3.2v1.4H9v2.4h2v6h2.6v-6h2l.4-2.4h-2.4V9.5c0-.5.2-.9.6-.9Z"/></svg></a></li>' +
            '<li><a href="#" aria-label="Instagram profile placeholder" data-placeholder="Instagram profile to be added">' +
              '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="4.5" y="4.5" width="15" height="15" rx="4.5"/><circle cx="12" cy="12" r="3.6"/><circle cx="16.6" cy="7.5" r=".9"/></svg></a></li>' +
            '<li><a href="#" aria-label="WhatsApp placeholder" data-placeholder="WhatsApp number to be added">' + icon("whatsapp") + "</a></li>" +
            '<li><a href="#" aria-label="Email placeholder" data-placeholder="Email address to be added">' + icon("mail") + "</a></li>" +
          "</ul>" +
        "</div>" +

        '<div class="footer__col">' +
          '<h2 class="footer__title">Shop</h2>' +
          '<ul class="footer__links">' + shopLinks +
            '<li><a href="brands.html">Brands</a></li>' +
            '<li><a href="deals.html">Deals</a></li>' +
          "</ul>" +
        "</div>" +

        '<div class="footer__col">' +
          '<h2 class="footer__title">Customer Service</h2>' +
          '<ul class="footer__links">' +
            '<li><a href="contact.html">Contact Us</a></li>' +
            '<li><a href="faq.html">FAQs</a></li>' +
            '<li><a href="track-order.html">Track Order</a></li>' +
            '<li><a href="returns.html">Returns</a></li>' +
            '<li><a href="complaint.html">Submit a Complaint</a></li>' +
            '<li><a href="my-account.html">My Account</a></li>' +
          "</ul>" +
        "</div>" +

        '<div class="footer__col footer__col--business">' +
          '<h2 class="footer__title">Business</h2>' +
          '<ul class="footer__links">' +
            '<li><a href="about.html">About Us</a></li>' +
            '<li><a href="quote-request.html">Bulk / Project Orders</a></li>' +
            '<li><a href="quote-request.html">Request a Quote</a></li>' +
            '<li><a href="shipping.html">Shipping</a></li>' +
            '<li><a href="privacy.html">Privacy Policy</a></li>' +
            '<li><a href="terms.html">Terms &amp; Conditions</a></li>' +
          "</ul>" +
        "</div>" +

        '<div class="footer__col">' +
          '<h2 class="footer__title">Contact</h2>' +
          '<ul class="footer__contact">' +
            "<li><span>Store</span> Star Electric Enterprises</li>" +
            "<li><span>Area</span> Saddar, Rawalpindi</li>" +
            '<li><span>Address</span> <em class="placeholder">[ to be added ]</em></li>' +
            '<li><span>Phone</span> <em class="placeholder">[ to be added ]</em></li>' +
            '<li><span>WhatsApp</span> <em class="placeholder">[ to be added ]</em></li>' +
            '<li><span>Email</span> <em class="placeholder">[ to be added ]</em></li>' +
            '<li><span>Hours</span> <em class="placeholder">[ to be added ]</em></li>' +
          "</ul>" +
        "</div>" +
      "</div>" +

      '<div class="footer__bar">' +
        '<div class="container footer__barinner">' +
          '<p>&copy; <span data-year>2026</span> Star Electric Enterprises, Saddar, Rawalpindi. All rights reserved.</p>' +
          '<ul class="footer__legal">' +
            '<li><a href="privacy.html">Privacy Policy</a></li>' +
            '<li><a href="terms.html">Terms &amp; Conditions</a></li>' +
            '<li><a href="shipping.html">Shipping</a></li>' +
            '<li><a href="returns.html">Returns</a></li>' +
          "</ul>" +
          '<ul class="pay" aria-label="Payment method placeholders">' +
            "<li>Cash on Delivery</li><li>Bank Transfer</li>" +
            '<li class="placeholder">[ card ]</li><li class="placeholder">[ wallet ]</li>' +
          "</ul>" +
        "</div>" +
      "</div>" +
    "</footer>";
  }

  /* ----------------------------------------------------------------------
     PRODUCT CARD  →  content-product.php
     ---------------------------------------------------------------------- */
  function renderProductCard(p, opts) {
    opts = opts || {};
    var off = discount(p);
    var out = p.stock === "out";
    var st = STOCK[p.stock];
    var href = "product.html?id=" + encodeURIComponent(p.id);
    var saved = window.SEE_STORE && SEE_STORE.inWishlist(p.id);

    var badges = "";
    if (off > 0) { badges += '<span class="tag tag--sale">-' + off + "%</span>"; }
    if (p.tags.indexOf("new") !== -1) { badges += '<span class="tag tag--new">New</span>'; }
    if (p.tags.indexOf("best") !== -1) { badges += '<span class="tag tag--best">Best Seller</span>'; }

    var cta = out
      ? '<button class="btn btn--ghost btn--block pcard__add" type="button" disabled>Out of Stock</button>'
      : (p.type === "variable"
          ? '<a class="btn btn--ghost btn--block pcard__add" href="' + href + '">Select Options</a>'
          : '<button class="btn btn--ghost btn--block pcard__add js-add" type="button" data-id="' + esc(p.id) + '">' +
              icon("cart") + "Add to Cart</button>");

    return '' +
    '<li class="pcard" data-id="' + esc(p.id) + '">' +
      '<div class="pcard__media">' +
        '<div class="pcard__badges">' + badges + "</div>" +
        '<div class="pcard__tools">' +
          '<button class="tool-btn js-wish' + (saved ? " is-active" : "") + '" type="button" data-id="' + esc(p.id) + '" ' +
                  'aria-pressed="' + (saved ? "true" : "false") + '" aria-label="Add to wishlist">' + icon("heart") + "</button>" +
          '<button class="tool-btn js-quick" type="button" data-id="' + esc(p.id) + '" aria-label="Quick view">' + icon("eye") + "</button>" +
        "</div>" +
        '<a href="' + href + '" tabindex="-1">' +
          '<img src="' + D.imageUrl(p.img) + '" alt="' + esc(p.name) + '" ' +
               'loading="lazy" decoding="async" width="1200" height="900">' +
        "</a>" +
      "</div>" +

      '<div class="pcard__body">' +
        '<p class="pcard__meta">' +
          '<span class="pcard__brand">' + esc(D.brandName(p.brand)) + "</span>" +
          '<span class="pcard__cat">' + esc(D.categoryName(p.cat)) + "</span>" +
        "</p>" +
        '<h3 class="pcard__name"><a href="' + href + '">' + esc(p.name) + "</a></h3>" +
        '<p class="pcard__desc">' + esc(p.short || "") + "</p>" +
        '<div class="rating">' + stars(p.rating) +
          '<span class="rating__count">(no reviews yet)</span>' +
        "</div>" +
        '<p class="price">' +
          '<span class="price__now">' + money(p.price) + "</span>" +
          (p.oldPrice ? '<span class="price__old">' + money(p.oldPrice) + "</span>" : "") +
          (off > 0 ? '<span class="price__off">Save ' + off + "%</span>" : "") +
        "</p>" +
        '<span class="pill ' + st.cls + '">' + st.label + "</span>" +
        cta +
      "</div>" +
    "</li>";
  }

  function renderProductGrid(list, opts) {
    return list.map(function (p) { return renderProductCard(p, opts); }).join("");
  }

  /* ----------------------------------------------------------------------
     Smaller shared renderers
     ---------------------------------------------------------------------- */
  function renderCategoryCard(c) {
    /* The photograph is the primary visual; the line-art icon is gone from
       category cards entirely (icons remain only for functional UI). */
    return '<li><a class="cat-card" href="category.html?cat=' + esc(c.slug) + '">' +
             '<span class="cat-card__photo">' +
               '<img src="' + D.categoryImage(c.slug) + '" alt="' + esc(c.name) + ' products" ' +
                    'loading="lazy" decoding="async" width="900" height="560">' +
             "</span>" +
             '<span class="cat-card__txt">' +
               '<span class="cat-card__name">' + esc(c.name) + "</span>" +
               '<span class="cat-card__meta">' + esc(c.subs.length) + " subcategories</span>" +
             "</span>" +
           "</a></li>";
  }

  function renderBrandCard(b) {
    return '<li><a class="brand-card" href="shop.html?brand=' + esc(b.slug) + '">' +
             '<span class="brand-card__mark" aria-hidden="true">' + esc(b.mark) + "</span>" +
             '<span class="brand-card__name">' + esc(b.name) + "</span>" +
             '<span class="brand-card__note">logo placeholder</span>' +
           "</a></li>";
  }

  function renderSubcatCard(catSlug, s) {
    return '<li><a class="subcat-card" href="category.html?cat=' + esc(catSlug) + "&sub=" + esc(s.slug) + '">' +
             '<img src="' + D.imageUrl(s.icon) + '" alt="" loading="lazy" width="46" height="36" aria-hidden="true">' +
             "<span>" + esc(s.name) + "</span>" +
           "</a></li>";
  }

  function renderBreadcrumb(items) {
    var html = items.map(function (it, i) {
      var last = i === items.length - 1;
      if (last) { return '<li aria-current="page">' + esc(it.label) + "</li>"; }
      return '<li><a href="' + esc(it.href) + '">' + esc(it.label) + "</a></li>" +
             '<li class="sep" aria-hidden="true">/</li>';
    }).join("");
    return '<nav aria-label="Breadcrumb"><ol class="breadcrumb">' + html + "</ol></nav>";
  }

  function renderEmpty(iconName, title, text, ctaHref, ctaLabel) {
    return '<div class="empty-state">' +
             '<span class="empty-state__ico">' + icon(iconName) + "</span>" +
             "<h3>" + esc(title) + "</h3>" +
             "<p>" + esc(text) + "</p>" +
             (ctaHref ? '<a class="btn btn--accent" href="' + esc(ctaHref) + '">' + esc(ctaLabel) + "</a>" : "") +
           "</div>";
  }

  return {
    esc: esc, money: money, discount: discount, STOCK: STOCK,
    icon: icon, stars: stars, NAV: NAV, LOGO: LOGO,
    renderHeader: renderHeader, renderDrawer: renderDrawer, renderFooter: renderFooter,
    renderProductCard: renderProductCard, renderProductGrid: renderProductGrid,
    renderCategoryCard: renderCategoryCard, renderBrandCard: renderBrandCard,
    renderSubcatCard: renderSubcatCard, renderBreadcrumb: renderBreadcrumb,
    renderEmpty: renderEmpty
  };
})();
