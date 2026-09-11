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
  /* Most of this catalogue is priced on enquiry, so an unpriced product must
     never fall through to a number. money() refuses anything that is not a real
     figure rather than printing "Rs. 0" or "Rs. NaN". */
  function money(v) {
    var n = Number(v);
    return (v === null || v === undefined || v === "" || !isFinite(n)) ? "" : "Rs. " + n.toLocaleString("en-PK");
  }
  function discount(p) { return p.oldPrice ? Math.round((1 - p.price / p.oldPrice) * 100) : 0; }

  /* ----------------------------------------------------------------------
     Pricing — one source of truth

     A product is quote-only when its source published no price. That single
     test, and the two renderers under it, are used by every surface that shows
     a price or a buying action: product cards, the homepage rails, quick view,
     the product page, wishlist and cart. Nothing re-implements the rule, so a
     null price cannot become "Rs. 0" on one surface while the card next to it
     reads "Request a Quote".
     ---------------------------------------------------------------------- */
  function isQuote(p) {
    return !!(p && (p.isQuote || p.price === null || p.price === undefined));
  }

  var QUOTE_HREF = "quote-request.html?product=";
  function quoteHref(p) { return QUOTE_HREF + encodeURIComponent(p.id); }

  /* Quote products never carry an old price, a saving or a discount badge —
     there is no figure to discount from. */
  function priceInner(p) {
    if (isQuote(p)) { return '<span class="price__quote">Request a Quote</span>'; }
    return '<span class="price__now">' + money(p.price) + "</span>" +
      (p.oldPrice ? '<span class="price__old">' + money(p.oldPrice) + "</span>" : "") +
      (p.discountPercent ? '<span class="price__off">Save ' + p.discountPercent + "%</span>" : "") +
      (p.priceType === "from" ? '<span class="price__from">from</span>' : "");
  }

  /* variant: "card" (default) | "lg" | "mini" | "bare".
     "bare" returns the spans only, for a container that is already .price. */
  function priceHtml(p, variant) {
    var v = variant || "card";
    if (v === "mini") {
      return isQuote(p)
        ? '<span class="mcard__quote"><b>Request a Quote</b><span>Priced on enquiry</span></span>'
        : '<span class="mcard__now">' + money(p.price) + "</span>" +
          (p.oldPrice ? '<span class="mcard__was">' + money(p.oldPrice) + "</span>" : "");
    }
    if (v === "bare") { return priceInner(p); }
    return '<p class="price' + (v === "lg" ? " price--lg" : "") +
           (isQuote(p) ? " price--quote" : "") + '">' + priceInner(p) + "</p>";
  }

  /* The buying action follows the same truth: a product with no published price
     cannot be added to a cart, so it is offered a quotation instead.
     opts.icon: true for every button, false for none, "add" for the cart button
     only — which is how the large product card has always looked. */
  function ctaHtml(p, opts) {
    opts = opts || {};
    var cls = opts.cls || "btn btn--ghost btn--block";
    var quoteCls = opts.quoteCls || "btn btn--accent btn--block";
    var href = "product.html?id=" + encodeURIComponent(p.id);
    var mode = opts.icon === undefined ? true : opts.icon;
    function ico(name, forAdd) {
      if (mode === true) { return icon(name); }
      if (mode === "add" && forAdd) { return icon(name); }
      return "";
    }
    if (isQuote(p)) {
      return '<a class="' + quoteCls + '" href="' + quoteHref(p) + '">' +
             ico("doc") + (opts.quoteLabel || "Request a Quote") + "</a>";
    }
    if (p.stock === "out") {
      return '<button class="' + cls + '" type="button" disabled>Out of Stock</button>';
    }
    if (p.type === "variable") {
      return '<a class="' + cls + '" href="' + href + '">' +
             ico("cart") + "Select Options</a>";
    }
    return '<button class="' + cls + ' js-add" type="button" data-id="' + esc(p.id) + '">' +
           ico("cart", true) + "Add to Cart</button>";
  }

  var STOCK = {
    "in":  { cls: "pill--in",  label: "In Stock" },
    "low": { cls: "pill--low", label: "Low Stock" },
    "out": { cls: "pill--out", label: "Out of Stock" },
    "unknown": { cls: "pill--unknown", label: "Availability not specified" }
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
    chevleft: '<path d="m14 7-5 5 5 5"/>',
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

  /* There is no star-rating renderer. No approved source publishes ratings or
     reviews, so the storefront has no way to draw one honestly. */

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
          '<li><a href="contact.html">' + icon("mail") + "Contact the store</a></li>" +
          '<li><a href="quote-request.html">' + icon("doc") + "Request a quotation</a></li>" +
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
            /* Not "Sign In": there is no sign-in to offer yet, and the header
               should not invite one. Same markup, same design. */
            '<span class="action__txt"><small>Customer</small><strong>Account</strong></span>' +
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
               "<span>Every product listed is an individual item published by one of our approved supplier sources.</span>" +
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
            '<li><span>Enquiries</span> <a href="contact.html">Contact form</a></li>' +
            '<li><span>Quotations</span> <a href="quote-request.html">Request a quotation</a></li>' +
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
          /* No payment-method badges: the store has not confirmed which methods
             it accepts, and the checkout no longer offers bank transfer. Listing
             them here would be a claim rather than a feature. */
        "</div>" +
      "</div>" +
    "</footer>";
  }

  /* ----------------------------------------------------------------------
     PRODUCT CARD  →  content-product.php
     ---------------------------------------------------------------------- */
  function renderProductCard(p, opts) {
    opts = opts || {};
    var href = "product.html?id=" + encodeURIComponent(p.id);
    var saved = window.SEE_STORE && SEE_STORE.inWishlist(p.id);
    var img = D.productImage(p);

    /* Badges are facts only: a discount badge requires a real source discount.
       There is no "Best Seller" - no approved source ranks sales. */
    var badges = "";
    if (p.discountPercent) { badges += '<span class="tag tag--sale">-' + p.discountPercent + "%</span>"; }
    if (isQuote(p)) { badges += '<span class="tag tag--quote">Request Quote</span>'; }

    /* Price and buying action both come from the shared pricing helpers, so an
       unpriced product can never render Rs. 0 or an Add to Cart button here. */
    var priceBlock = priceHtml(p, "card");

    var st = STOCK[p.stock] || { cls: "pill--unknown", label: "Not specified" };

    var cta = ctaHtml(p, {
      cls: "btn btn--ghost btn--block pcard__add",
      quoteCls: "btn btn--accent btn--block pcard__add",
      icon: "add"
    });

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
          (img ? '<img src="' + esc(img) + '" alt="' + esc(p.name) + '" loading="lazy" decoding="async" width="1200" height="900">'
               : '<span class="pcard__noimg">Product image unavailable</span>') +
        "</a>" +
      "</div>" +

      '<div class="pcard__body">' +
        '<p class="pcard__meta">' +
          '<span class="pcard__brand">' + esc(p.brand || "") + "</span>" +
          '<span class="pcard__cat">' + esc(D.categoryName(p.cat)) + "</span>" +
        "</p>" +
        '<h3 class="pcard__name"><a href="' + href + '">' + esc(p.name) + "</a></h3>" +
        (p.model ? '<p class="pcard__model">Model: ' + esc(p.model) + "</p>" : "") +
        priceBlock +
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
               '<span class="cat-card__meta">' + esc(c.count) + " products &middot; " +
                  esc(c.subs.length) + " subcategories</span>" +
             "</span>" +
           "</a></li>";
  }

  function renderBrandCard(b) {
    var n = b.count || 0;
    return '<li><a class="brand-card" href="shop.html?brand=' + esc(b.slug) + '">' +
             '<span class="brand-card__mark" aria-hidden="true">' + esc(b.mark) + "</span>" +
             '<span class="brand-card__name">' + esc(b.name) + "</span>" +
             '<span class="brand-card__note">' + n + (n === 1 ? " product" : " products") + "</span>" +
           "</a></li>";
  }

  /* ----------------------------------------------------------------------
     PRODUCT FAMILY PANEL
     A family is a range name the source publishes with no item-level model,
     specification, image or price behind it. It is rendered as navigation and
     enquiry only: no price, no stock pill, no add-to-cart, no product link.
     ---------------------------------------------------------------------- */
  function renderFamilyPanel(brandName, list, note) {
    var items = list.map(function (f) {
      return '<li class="family-item">' +
               '<span class="family-item__media">' +
                 (f.img ? '<img src="' + esc(f.img) + '" alt="" loading="lazy" decoding="async">'
                        : '<span class="thumb-none">No image</span>') +
               "</span>" +
               '<span class="family-item__body">' +
               '<span class="family-item__name">' + esc(f.name) + "</span>" +
               (f.series ? '<span class="family-item__series">Series: ' + esc(f.series) + "</span>" : "") +
               (f.summary ? '<span class="family-item__sum">' + esc(f.summary) + "</span>" : "") +
               '<span class="family-item__links">' +
                 '<a href="quote-request.html?family=' + encodeURIComponent(f.id) + '">Ask about this range</a>' +
                 '<a href="' + esc(f.sourceUrl) + '" rel="nofollow noopener" target="_blank">Source: ' +
                   esc(f.sourceDomain) + "</a>" +
               "</span>" +
               "</span>" +
             "</li>";
    }).join("");
    return '<article class="panel family-panel">' +
             '<h3 class="family-panel__title">' + esc(brandName) + "</h3>" +
             (note ? '<p class="family-panel__note">' + esc(note) + "</p>" : "") +
             '<ul class="family-list">' + items + "</ul>" +
           "</article>";
  }

  function renderSubcatCard(catSlug, s) {
    return '<li><a class="subcat-card" href="category.html?cat=' + esc(catSlug) + "&sub=" + esc(s.slug) + '">' +
             '<img src="' + D.imageUrl(s.icon) + '" alt="" loading="lazy" width="46" height="36" aria-hidden="true">' +
             "<span>" + esc(s.name) + "</span>" +
           "</a></li>";
  }


  /* ======================================================================
     HOMEPAGE MERCHANDISING COMPONENTS
     ----------------------------------------------------------------------
     A compact catalogue card, a circular department tile and the shell of a
     horizontal rail. These map onto WooCommerce later as content-product.php
     in a shortcode loop, so the markup is deliberately plain.
     ====================================================================== */

  /* The merchandising card follows a catalogue hierarchy: picture, name,
     brand, price, then the two actions. It carries no rating, no badge that
     is not a fact, and never a price the source did not publish. */
  function renderMiniCard(p) {
    var href = "product.html?id=" + encodeURIComponent(p.id);
    var img = D.productImage(p);

    /* Quote-only lines are most of this catalogue, so they read as a deliberate
       way to buy rather than as a price that failed to load.

       Navy carries the everyday cart action; red is reserved for quotation, so
       a rail of quote-only products does not become a wall of red. */
    var price = priceHtml(p, "mini");
    var cta = ctaHtml(p, {
      cls: "mcard__btn mcard__btn--primary",
      quoteCls: "mcard__btn mcard__btn--quote",
      quoteLabel: "Request Quote"
    });

    return '' +
    '<li class="mcard" data-id="' + esc(p.id) + '">' +
      '<a class="mcard__media" href="' + href + '" tabindex="-1" aria-hidden="true">' +
        (p.discountPercent ? '<span class="mcard__off">-' + p.discountPercent + "%</span>" : "") +
        (img ? '<img src="' + esc(img) + '" alt="" loading="lazy" decoding="async" width="600" height="600">'
             : '<span class="thumb-none">Product image unavailable</span>') +
      "</a>" +
      '<div class="mcard__body">' +
        '<h3 class="mcard__name"><a href="' + href + '">' + esc(p.name) + "</a></h3>" +
        '<p class="mcard__brand">By: <a href="shop.html?brand=' +
          encodeURIComponent(p.brandSlug || "") + '">' +
          esc(p.brand || "Not specified") + "</a></p>" +
        '<p class="mcard__price">' + price + "</p>" +
        '<div class="mcard__actions">' + cta +
          '<a class="mcard__btn mcard__btn--ghost" href="' + href + '" aria-label="View ' +
            esc(p.name) + '">' + icon("eye") + "<span>View</span></a>" +
        "</div>" +
      "</div>" +
    "</li>";
  }

  /* A horizontal rail: heading, View All, arrows and a scroll-snap track.
     One component serves every product row on the homepage. */
  function renderRail(opts) {
    var id = opts.id;
    var items = opts.items || [];
    if (!items.length) { return ""; }
    return '' +
    '<section class="rail" aria-labelledby="' + esc(id) + '-t">' +
      '<div class="rail__head">' +
        "<h2 class=\"rail__title\" id=\"" + esc(id) + "-t\">" + esc(opts.title) + "</h2>" +
        '<div class="rail__tools">' +
          (opts.viewAllUrl
            ? '<a class="rail__all" href="' + esc(opts.viewAllUrl) + '">View All' +
              (opts.count ? ' <span class="rail__count">' + opts.count + "</span>" : "") + "</a>"
            : "") +
          '<div class="rail__arrows">' +
            '<button class="rail__arrow" type="button" data-rail-prev aria-label="Previous ' +
              esc(opts.title) + ' products">' + icon("chevleft") + "</button>" +
            '<button class="rail__arrow" type="button" data-rail-next aria-label="More ' +
              esc(opts.title) + ' products">' + icon("chevright") + "</button>" +
          "</div>" +
        "</div>" +
      "</div>" +
      '<ul class="rail__track" id="' + esc(id) + '" tabindex="0" role="list">' +
        items.map(renderMiniCard).join("") +
      "</ul>" +
    "</section>";
  }

  /* Circular department tile. The picture is a real photograph of something
     the department contains - never a coloured icon disc. */
  function renderDepartmentTile(d) {
    var meta = d.count
      ? d.count.toLocaleString("en-PK") + (d.count === 1 ? " product" : " products")
      : (d.familyCount ? d.familyCount + " ranges" : "");
    return '' +
    '<li class="dept">' +
      '<a class="dept__link" href="' + esc(d.href) + '">' +
        '<span class="dept__disc">' +
          (d.img ? '<img src="' + esc(d.img) + '" alt="" loading="lazy" decoding="async" width="220" height="220">'
                 : "") +
        "</span>" +
        '<span class="dept__name">' + esc(d.label) + "</span>" +
        (meta ? '<span class="dept__meta">' + esc(meta) + "</span>" : "") +
      "</a>" +
    "</li>";
  }

  /* Left department rail beside the hero. Departments that have their own
     subcategories open a flyout; the rest are a plain link. */
  function renderHeroRail(items) {
    var list = items.map(function (c) {
      var subs = (c.subs || []).filter(function (s) { return s.count > 0; });
      if (!subs.length) {
        return '<li class="hrail__item"><a class="hrail__link" href="' + esc(c.href) + '">' +
               esc(c.label) + "</a></li>";
      }
      return '<li class="hrail__item hrail__item--has-sub">' +
               '<a class="hrail__link" href="' + esc(c.href) + '">' + esc(c.label) +
                 icon("chevright", "hrail__chev") + "</a>" +
               '<div class="hrail__flyout">' +
                 '<p class="hrail__flyhead">' + esc(c.label) +
                   ' <span>' + (c.count || 0).toLocaleString("en-PK") + " products</span></p>" +
                 "<ul>" + subs.map(function (s) {
                   return '<li><a href="' + esc(s.href) + '">' + esc(s.name) +
                          "<span>" + s.count + "</span></a></li>";
                 }).join("") + "</ul>" +
                 '<a class="hrail__flyall" href="' + esc(c.href) + '">Browse ' + esc(c.label) + "</a>" +
               "</div>" +
             "</li>";
    }).join("");
    return '<nav class="hrail" aria-label="Product departments">' +
             '<p class="hrail__head">All Departments</p>' +
             '<ul class="hrail__list">' + list + "</ul>" +
             '<a class="hrail__all" href="shop.html">View All Categories' +
               icon("arrowright") + "</a>" +
           "</nav>";
  }

  /* Hero campaign slide.
     The picture is a composition, not one floating object: two or three real
     department photographs are staged at different depths so the banner reads
     as art direction rather than an empty panel. Everything shown is catalogue
     photography already in the repository - no borrowed artwork, no invented
     discount, no stock imagery. */
  /* ----------------------------------------------------------------------
     HERO CAMPAIGN SLIDE

     One angled composition: a navy campaign panel carrying the copy, a light
     merchandising stage carrying the products, and a single composed product
     image that crosses the seam between them so the two halves read as one
     banner rather than two boxes. The composition is a pre-built transparent
     WebP of real catalogue products - see the hero build notes in
     data/homepage-hero-redesign-audit.md - so the browser lays out one image
     instead of stacking three and the products can be shown large.
     ---------------------------------------------------------------------- */
  function renderHeroSlide(s, i, first) {
    return '' +
    '<article class="hs" data-tone="' + esc(s.tone || "navy") + '" role="group" ' +
      'aria-roledescription="slide" aria-label="' + esc(s.eyebrow) + '">' +
      '<span class="hs__panel" aria-hidden="true"></span>' +
      '<div class="hs__copy">' +
        '<p class="hs__eyebrow">' + esc(s.eyebrow) + "</p>" +
        '<h2 class="hs__title">' + esc(s.title) + "</h2>" +
        '<p class="hs__text">' + esc(s.text) + "</p>" +
        '<div class="hs__cta">' +
          '<a class="btn btn--accent btn--lg" href="' + esc(s.href) + '">' + esc(s.cta) + "</a>" +
          (s.href2 ? '<a class="btn btn--outline btn--lg" href="' + esc(s.href2) + '">' +
                     esc(s.cta2) + "</a>" : "") +
        "</div>" +
      "</div>" +
      '<div class="hs__stage" aria-hidden="true">' +
        /* The track is translated, not scrolled, so a lazy image on an
           off-screen slide is not reliably fetched before that slide arrives -
           it showed an empty merchandising zone on slides two onwards. They
           load eagerly at low priority instead, which keeps them out of the
           way of the first slide's LCP without risking a blank campaign. */
        '<img class="hs__art" src="' + esc("assets/images/hero/hero-" + s.art + ".webp") +
          '" alt="" width="1200" height="860" decoding="async"' +
          (first ? ' fetchpriority="high">' : ' fetchpriority="low">') +
      "</div>" +
    "</article>";
  }

  /* The campaign navigator, attached to the bottom edge of the banner: a
     numbered, labelled entry per campaign with its own autoplay progress line,
     so the control names where it goes and shows how long is left. */
  function renderHeroNav(slides) {
    return slides.map(function (s, i) {
      return '<button class="hnav" type="button" data-slide="' + i + '" ' +
               'aria-label="' + esc(s.eyebrow) + '"' + (i === 0 ? ' aria-current="true"' : "") + ">" +
               '<span class="hnav__num">' + ("0" + (i + 1)).slice(-2) + "</span>" +
               '<span class="hnav__label">' + esc(s.eyebrow) + "</span>" +
               '<span class="hnav__bar"><span class="hnav__fill"></span></span>' +
             "</button>";
    }).join("");
  }

  /* Compact promotional tile under the hero. */
  function renderPromoTile(t) {
    return '' +
    '<a class="ptile" href="' + esc(t.href) + '">' +
      '<span class="ptile__body">' +
        '<span class="ptile__kicker">' + esc(t.kicker) + "</span>" +
        '<span class="ptile__title">' + esc(t.title) + "</span>" +
        '<span class="ptile__link">' + esc(t.cta) + icon("arrowright") + "</span>" +
      "</span>" +
      '<span class="ptile__media"><img src="' + esc(t.img) +
        '" alt="" loading="lazy" decoding="async" width="300" height="240"></span>' +
    "</a>";
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
    isQuote: isQuote, priceHtml: priceHtml, ctaHtml: ctaHtml, quoteHref: quoteHref,
    icon: icon, NAV: NAV, LOGO: LOGO,
    renderHeader: renderHeader, renderDrawer: renderDrawer, renderFooter: renderFooter,
    renderProductCard: renderProductCard, renderProductGrid: renderProductGrid,
    renderCategoryCard: renderCategoryCard, renderBrandCard: renderBrandCard,
    renderFamilyPanel: renderFamilyPanel,
    renderMiniCard: renderMiniCard, renderRail: renderRail,
    renderDepartmentTile: renderDepartmentTile, renderHeroRail: renderHeroRail,
    renderHeroSlide: renderHeroSlide, renderHeroNav: renderHeroNav,
    renderPromoTile: renderPromoTile,
    renderSubcatCard: renderSubcatCard, renderBreadcrumb: renderBreadcrumb,
    renderEmpty: renderEmpty
  };
})();
