/* ==========================================================================
   STAR ELECTRIC ENTERPRISES — Application behaviour
   --------------------------------------------------------------------------
   Vanilla JS only. No framework, no build step, no network requests.

   Cart and wishlist are a FRONT-END DEMO ONLY. Nothing is sent anywhere,
   no payment is processed and no account is authenticated. On WordPress
   these are replaced by WooCommerce AJAX endpoints and real sessions.
   ========================================================================== */
(function () {
  "use strict";

  var D = window.SEE_DATA;
  var UI = window.SEE_UI;

  /* ======================================================================
     0. HELPERS
     ====================================================================== */
  function $(s, c) { return (c || document).querySelector(s); }
  function $$(s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); }
  function param(name) {
    var m = new RegExp("[?&]" + name + "=([^&#]*)").exec(window.location.search);
    return m ? decodeURIComponent(m[1].replace(/\+/g, " ")) : "";
  }
  function on(el, ev, fn, opts) { if (el) { el.addEventListener(ev, fn, opts); } }
  function closest(t, sel) { return t && t.closest ? t.closest(sel) : null; }

  /* A thumbnail is rendered only when the product's own source published an
     image. Otherwise an honest empty state is shown - never a stand-in photo. */
  function thumb(p, cls) {
    var src = D.productImage(p);
    if (!src) {
      return '<span class="thumb-none ' + (cls || "") + '" aria-hidden="true">No image</span>';
    }
    return '<img class="' + (cls || "") + '" src="' + UI.esc(src) + '" alt="" loading="lazy" aria-hidden="true">';
  }

  /* ======================================================================
     1. DEMO STORE (cart + wishlist)
     localStorage is a per-viewer convenience only; every access is guarded
     because it throws in private windows and when site data is blocked.
     ====================================================================== */
  var SEE_STORE = (function () {
    var KEY = "see_demo_state_v1";
    var mem = { cart: [], wishlist: [], seeded: false };

    function read() {
      try {
        var raw = window.localStorage.getItem(KEY);
        if (raw) { mem = JSON.parse(raw); }
      } catch (e) { /* fall back to memory */ }
      mem.cart = mem.cart || [];
      mem.wishlist = mem.wishlist || [];
      return mem;
    }
    function write() {
      try { window.localStorage.setItem(KEY, JSON.stringify(mem)); } catch (e) { /* ignore */ }
    }

    read();

    /* No commerce state is ever created for a visitor. A first-time shopper
       arrives with an empty cart and an empty wishlist.

       Earlier builds seeded a demo cart so the cart and checkout screens could
       be reviewed populated, and that shipped to the live site where new
       visitors found items already in their basket. The seeding is gone. For
       anyone still carrying it, the seeded records are removed once - and only
       those: the seed was the first three priced, in-stock products with
       quantities 1, 2, 3 and no variant, so it can be recognised exactly and
       anything the shopper added themselves is left untouched. */
    if (mem.seeded && !mem.demoCleared) {
      var seeded = (D.PRODUCTS || []).filter(function (p) {
        return p.price !== null && p.stock !== "out";
      }).slice(0, 3).map(function (p) { return p.id; });

      mem.cart = mem.cart.filter(function (line, i) {
        var seedIndex = seeded.indexOf(line.id);
        return !(seedIndex !== -1 && !line.variant && line.qty === seedIndex + 1);
      });
      mem.wishlist = mem.wishlist.filter(function (id) {
        return seeded.slice(0, 2).indexOf(id) === -1;
      });
      mem.demoCleared = true;
      mem.seeded = false;
      write();
    }

    function cartLines() {
      return mem.cart.map(function (l) {
        var p = D.productById(l.id);
        /* Quote-only lines are dropped rather than rendered: they have no price
           to show or total, and add() no longer accepts them. This only ever
           matches a line left in a returning visitor's storage. */
        if (!p || UI.isQuote(p)) { return null; }
        return { line: l, product: p, total: p.price * l.qty };
      }).filter(Boolean);
    }
    function cartCount() {
      return mem.cart.reduce(function (n, l) { return n + l.qty; }, 0);
    }
    function cartSubtotal() {
      return cartLines().reduce(function (n, x) { return n + x.total; }, 0);
    }
    function add(id, qty, variant) {
      var p = D.productById(id);
      if (!p || p.stock === "out") { return false; }
      /* A product the source never priced cannot be carted: there is no figure
         to total. Every surface offers it a quotation instead, and this is the
         backstop so no future button can slip one in. */
      if (UI.isQuote(p)) { return false; }
      qty = qty || 1;
      for (var i = 0; i < mem.cart.length; i++) {
        if (mem.cart[i].id === id && mem.cart[i].variant === (variant || "")) {
          mem.cart[i].qty += qty; write(); return true;
        }
      }
      mem.cart.push({ id: id, qty: qty, variant: variant || "" });
      write();
      return true;
    }
    function setQty(index, qty) {
      if (!mem.cart[index]) { return; }
      mem.cart[index].qty = Math.max(1, qty);
      write();
    }
    function removeLine(index) { mem.cart.splice(index, 1); write(); }
    function clearCart() { mem.cart = []; write(); }

    function inWishlist(id) { return mem.wishlist.indexOf(id) !== -1; }
    function toggleWish(id) {
      var i = mem.wishlist.indexOf(id);
      if (i === -1) { mem.wishlist.push(id); } else { mem.wishlist.splice(i, 1); }
      write();
      return i === -1;
    }
    function wishlist() {
      return mem.wishlist.map(function (id) { return D.productById(id); }).filter(Boolean);
    }
    function removeWish(id) {
      var i = mem.wishlist.indexOf(id);
      if (i !== -1) { mem.wishlist.splice(i, 1); write(); }
    }

    return {
      state: function () { return mem; },
      cartLines: cartLines, cartCount: cartCount, cartSubtotal: cartSubtotal,
      add: add, setQty: setQty, removeLine: removeLine, clearCart: clearCart,
      inWishlist: inWishlist, toggleWish: toggleWish, wishlist: wishlist, removeWish: removeWish
    };
  })();
  window.SEE_STORE = SEE_STORE;

  /* ======================================================================
     2. TOAST
     ====================================================================== */
  function toast(msg, iconName) {
    var wrap = $("#toastWrap");
    if (!wrap) {
      wrap = document.createElement("div");
      wrap.className = "toast-wrap";
      wrap.id = "toastWrap";
      document.body.appendChild(wrap);
    }
    var el = document.createElement("div");
    el.className = "toast";
    el.setAttribute("role", "status");
    el.innerHTML = UI.icon(iconName || "check") + "<span>" + UI.esc(msg) + "</span>";
    wrap.appendChild(el);
    window.requestAnimationFrame(function () { el.classList.add("is-show"); });
    window.setTimeout(function () {
      el.classList.remove("is-show");
      window.setTimeout(function () { if (el.parentNode) { el.parentNode.removeChild(el); } }, 250);
    }, 2400);
  }

  /* ======================================================================
     3. HEADER / FOOTER MOUNT
     ====================================================================== */
  function mountShell() {
    var page = document.body.getAttribute("data-page") || "";
    /* data-nav lets a sub-page (product, search results) light up its parent
       nav item without changing which page controller runs. */
    var navKey = document.body.getAttribute("data-nav") || page;

    var head = $("#siteHeader");
    if (head) { head.innerHTML = UI.renderHeader(navKey); }

    var drawer = $("#mobileNav");
    if (drawer) { drawer.innerHTML = UI.renderDrawer(navKey); }

    var foot = $("#siteFooter");
    if (foot) { foot.innerHTML = UI.renderFooter(); }

    $$("[data-year]").forEach(function (el) { el.textContent = new Date().getFullYear(); });
    syncCounts();
  }

  function syncCounts() {
    var c = SEE_STORE.cartCount();
    var w = SEE_STORE.state().wishlist.length;
    $$('[data-count="cart"]').forEach(function (el) { el.textContent = c; });
    $$('[data-count="wishlist"]').forEach(function (el) { el.textContent = w; });
    $$("[data-cart-total]").forEach(function (el) { el.textContent = UI.money(SEE_STORE.cartSubtotal()); });
  }

  /* ======================================================================
     4. GLOBAL BEHAVIOUR
     ====================================================================== */
  function initDrawer() {
    var drawer = $("#mobileNav");
    var toggle = $("#navToggle");
    if (!drawer) { return; }

    function open() {
      drawer.hidden = false;
      document.body.classList.add("no-scroll");
      window.requestAnimationFrame(function () { drawer.classList.add("is-open"); });
      if (toggle) { toggle.setAttribute("aria-expanded", "true"); }
      var first = $(".drawer__nav a", drawer);
      if (first) { first.focus(); }
    }
    function close() {
      if (drawer.hidden) { return; }
      drawer.classList.remove("is-open");
      document.body.classList.remove("no-scroll");
      window.setTimeout(function () { drawer.hidden = true; }, 250);
      if (toggle) { toggle.setAttribute("aria-expanded", "false"); toggle.focus(); }
    }
    window.SEE_closeDrawer = close;

    document.addEventListener("click", function (e) {
      if (closest(e.target, "#navToggle")) { e.preventDefault(); open(); return; }
      if (closest(e.target, "[data-drawer-close]")) { e.preventDefault(); close(); }
    });
    window.addEventListener("resize", function () {
      if (window.innerWidth > 900 && !drawer.hidden) { close(); }
    });
  }

  /* Generic side drawer (shop filters) */
  function initSideDrawer(id) {
    var d = document.getElementById(id);
    if (!d) { return null; }
    function open() {
      d.hidden = false;
      document.body.classList.add("no-scroll");
      window.requestAnimationFrame(function () { d.classList.add("is-open"); });
    }
    function close() {
      d.classList.remove("is-open");
      document.body.classList.remove("no-scroll");
      window.setTimeout(function () { d.hidden = true; }, 250);
    }
    d.addEventListener("click", function (e) {
      if (closest(e.target, "[data-side-close]")) { e.preventDefault(); close(); }
    });
    return { open: open, close: close };
  }

  function initSearch() {
    var input = $("#searchInput");
    var box = $("#searchSuggest");
    var form = $("#searchForm");
    if (!input || !box) { return; }

    function hide() { box.hidden = true; }

    function show(q) {
      var term = q.trim().toLowerCase();
      if (term.length < 2) { hide(); return; }

      var hits = D.PRODUCTS.filter(function (p) {
        return matchesQuery(p, term);
      }).slice(0, 6);

      var cats = D.CATEGORIES.filter(function (c) {
        return c.name.toLowerCase().indexOf(term) !== -1;
      }).slice(0, 3);

      var html = "";
      if (hits.length) {
        html += '<p class="suggest__head">Products</p>';
        html += hits.map(function (p) {
          return '<a class="suggest__item" href="product.html?id=' + encodeURIComponent(p.id) + '">' +
                   thumb(p, "suggest__img") +
                   "<span>" + UI.esc(p.name) + "</span>" +
                   '<span class="suggest__cat">' + UI.esc(D.categoryName(p.cat)) + "</span>" +
                 "</a>";
        }).join("");
      }
      if (cats.length) {
        html += '<p class="suggest__head">Categories</p>';
        html += cats.map(function (c) {
          return '<a class="suggest__item" href="category.html?cat=' + UI.esc(c.slug) + '">' +
                   UI.icon("chevright") + "<span>" + UI.esc(c.name) + "</span></a>";
        }).join("");
      }
      if (!html) {
        html = '<p class="suggest__empty">No products match &ldquo;' + UI.esc(q) +
               '&rdquo;. Try &ldquo;cable&rdquo;, &ldquo;switch&rdquo; or &ldquo;LED&rdquo;.</p>';
      }
      box.innerHTML = html;
      box.hidden = false;
    }

    on(input, "input", function () { show(this.value); });
    on(input, "focus", function () { show(this.value); });
    document.addEventListener("click", function (e) {
      if (!closest(e.target, ".search")) { hide(); }
    });
    on(form, "submit", function (e) {
      // Demo: navigate to the search results page with the query attached.
      e.preventDefault();
      var q = input.value.trim();
      var cat = $("#searchCat") ? $("#searchCat").value : "";
      hide();
      window.location.href = "search-results.html?s=" + encodeURIComponent(q) +
                             (cat ? "&product_cat=" + encodeURIComponent(cat) : "");
    });
  }

  /* Search across name, model, SKU, brand, series, category and specification
     values, so "20A MCB", "2.5mm cable" or "HDB3w" all resolve. */
  function matchesQuery(p, term) {
    if (!term) { return true; }
    var hay = [p.name, p.model, p.sku, p.brand, p.series,
               D.categoryName(p.cat), String(p.sub || "").replace(/-/g, " "),
               (p.depts || []).join(" ").replace(/-/g, " "),
               D.departmentName ? D.departmentName(p.depts) : "",
               p.specText].join(" ").toLowerCase();
    var words = term.toLowerCase().split(/\s+/);
    for (var i = 0; i < words.length; i++) {
      if (words[i] && hay.indexOf(words[i]) === -1) { return false; }
    }
    return true;
  }

  /* Quick view */
  function initQuickView() {
    var modal = $("#quickView");
    if (!modal) { return; }
    var last = null, currentId = null;

    function open(id) {
      var p = D.productById(id);
      if (!p) { return; }
      currentId = id;
      last = document.activeElement;
      var st = UI.STOCK[p.stock];
      var href = "product.html?id=" + encodeURIComponent(p.id);

      $("#qvBody").innerHTML =
        '<div class="modal__grid">' +
          '<div class="modal__media">' +
            (D.productImage(p)
              ? '<img src="' + UI.esc(D.productImage(p)) + '" alt="' + UI.esc(p.name) + '" decoding="async">'
              : '<span class="pcard__noimg">Product image unavailable</span>') +
          "</div>" +
          '<div class="modal__body">' +
            '<p class="pcard__meta"><span class="pcard__brand">' + UI.esc(D.brandName(p.brand)) + "</span>" +
              '<span class="pcard__cat">' + UI.esc(D.categoryName(p.cat)) + "</span></p>" +
            "<h2 id=\"qvTitle\" style=\"font-size:21px\">" + UI.esc(p.name) + "</h2>" +
            UI.priceHtml(p, "lg") +
            '<span class="pill ' + st.cls + '">' + st.label + "</span>" +
            "<p class=\"t-sm t-muted\">" + UI.esc(p.short || "") + "</p>" +
            '<div class="btn-row" style="margin-top:8px">' +
              UI.ctaHtml(p, { cls: "btn btn--accent", quoteCls: "btn btn--accent", icon: false }) +
              '<a class="btn btn--ghost" href="' + href + '">' +
                (UI.isQuote(p) ? "View Product" : "Full Details") + "</a>" +
            "</div>" +
          "</div>" +
        "</div>";

      modal.hidden = false;
      document.body.classList.add("no-scroll");
      window.requestAnimationFrame(function () { modal.classList.add("is-open"); });
      var c = $(".modal__close", modal);
      if (c) { c.focus(); }
    }

    function close() {
      if (modal.hidden) { return; }
      modal.classList.remove("is-open");
      document.body.classList.remove("no-scroll");
      window.setTimeout(function () { modal.hidden = true; }, 200);
      if (last && last.focus) { last.focus(); }
      currentId = null;
    }

    window.SEE_openQuickView = open;
    window.SEE_closeQuickView = close;
    modal.addEventListener("click", function (e) {
      if (closest(e.target, "[data-close]")) { e.preventDefault(); close(); }
    });
  }

  /* Delegated product actions — works for every JS-rendered card */
  function initProductActions() {
    document.addEventListener("click", function (e) {
      var wish = closest(e.target, ".js-wish");
      if (wish) {
        e.preventDefault();
        var wid = wish.getAttribute("data-id");
        var added = SEE_STORE.toggleWish(wid);
        $$('.js-wish[data-id="' + wid + '"]').forEach(function (b) {
          b.classList.toggle("is-active", added);
          b.setAttribute("aria-pressed", added ? "true" : "false");
          if (b.classList.contains("tool-btn")) {
            b.setAttribute("aria-label", added ? "Remove from wishlist" : "Add to wishlist");
          }
        });
        syncCounts();
        toast(added ? "Saved to wishlist" : "Removed from wishlist", "heart");
        if (document.body.getAttribute("data-page") === "wishlist") { PAGES.wishlist(); }
        return;
      }

      var add = closest(e.target, ".js-add");
      if (add) {
        e.preventDefault();
        var aid = add.getAttribute("data-id");
        var qtyEl = add.getAttribute("data-qty-from") ? $(add.getAttribute("data-qty-from")) : null;
        var qty = qtyEl ? parseInt(qtyEl.value, 10) || 1 : 1;
        var variant = add.getAttribute("data-variant") || "";
        if (SEE_STORE.add(aid, qty, variant)) {
          syncCounts();
          var p = D.productById(aid);
          toast("Added to cart: " + p.name, "cart");
        }
        return;
      }

      var quick = closest(e.target, ".js-quick");
      if (quick) {
        e.preventDefault();
        if (window.SEE_openQuickView) { window.SEE_openQuickView(quick.getAttribute("data-id")); }
        return;
      }

      /* Links that are intentionally not wired up yet */
      var ph = closest(e.target, "[data-placeholder]");
      if (ph && (ph.getAttribute("href") === "#" || ph.tagName !== "A")) {
        e.preventDefault();
        toast(ph.getAttribute("data-placeholder"), "info");
      }
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        if (window.SEE_closeQuickView) { window.SEE_closeQuickView(); }
        if (window.SEE_closeDrawer) { window.SEE_closeDrawer(); }
      }
    });
  }

  /* Collapsible groups: accordions, filter groups, FAQ */
  function initCollapsibles() {
    document.addEventListener("click", function (e) {
      var btn = closest(e.target, "[data-toggle-panel]");
      if (!btn) { return; }
      e.preventDefault();
      var panel = document.getElementById(btn.getAttribute("aria-controls"));
      if (!panel) { return; }
      var openNow = btn.getAttribute("aria-expanded") === "true";
      btn.setAttribute("aria-expanded", openNow ? "false" : "true");
      panel.hidden = openNow;
    });
  }

  /* Tabs */
  function initTabs() {
    $$("[data-tabs]").forEach(function (group) {
      var btns = $$("[role=tab]", group);
      btns.forEach(function (b) {
        b.addEventListener("click", function () {
          btns.forEach(function (o) {
            o.setAttribute("aria-selected", "false");
            var pnl = document.getElementById(o.getAttribute("aria-controls"));
            if (pnl) { pnl.hidden = true; }
          });
          b.setAttribute("aria-selected", "true");
          var panel = document.getElementById(b.getAttribute("aria-controls"));
          if (panel) { panel.hidden = false; }
        });
      });
    });
  }

  /* Quantity steppers */
  function initQty() {
    document.addEventListener("click", function (e) {
      var btn = closest(e.target, "[data-qty]");
      if (!btn) { return; }
      e.preventDefault();
      var wrap = closest(btn, ".qty");
      var input = $("input", wrap);
      var v = parseInt(input.value, 10) || 1;
      v = btn.getAttribute("data-qty") === "up" ? v + 1 : Math.max(1, v - 1);
      input.value = v;
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });
  }

  /* Sticky header shadow + back to top */
  function initScroll() {
    var header = $("#siteHeaderBar");
    var top = $("#toTop");
    var ticking = false;
    function run() {
      var y = window.pageYOffset || document.documentElement.scrollTop;
      if (header) { header.classList.toggle("is-stuck", y > 8); }
      if (top) { top.hidden = y < 700; }
      ticking = false;
    }
    window.addEventListener("scroll", function () {
      if (!ticking) { window.requestAnimationFrame(run); ticking = true; }
    }, { passive: true });
    run();
    on(top, "click", function () { window.scrollTo({ top: 0, behavior: "smooth" }); });
  }

  /* Forms: this prototype never submits anywhere */
  function initForms() {
    document.addEventListener("submit", function (e) {
      var form = e.target;
      if (form.hasAttribute("data-demo-form")) {
        e.preventDefault();
        var msg = form.getAttribute("data-demo-form") ||
                  "This is a design prototype — the form is not connected yet.";
        toast(msg, "info");
      }
    });
  }

  /* ======================================================================
     5. PAGE CONTROLLERS
     ====================================================================== */
  var PAGES = {};

  function fill(sel, list) {
    var el = $(sel);
    if (el) { el.innerHTML = UI.renderProductGrid(list); }
  }

  /* ---------- SHOP / CATEGORY / SEARCH / DEALS (shared catalog engine) ---------- */
  function catalog(opts) {
    opts = opts || {};
    var state = {
      cats: opts.cat ? [opts.cat] : [],
      subs: opts.sub ? [opts.sub] : [],
      /* A department is a many-to-many grouping, so it filters alongside the
         category tree rather than replacing it. */
      dept: opts.dept || "",
      brands: opts.brand ? [opts.brand] : [],
      stock: [], type: [], pricing: [],
      min: null, max: null,
      sort: "relevance",
      view: "grid",
      page: 1,
      perPage: 12,
      query: opts.query || "",
      onlyDeals: !!opts.onlyDeals
    };

    var grid = $("#catalogGrid");
    var countEl = $("#resultCount");
    var pager = $("#pagination");
    var chipsEl = $("#activeChips");
    if (!grid) { return null; }

    function matches(p) {
      if (state.onlyDeals && !p.discountPercent) { return false; }
      if (state.dept && (p.depts || []).indexOf(state.dept) === -1) { return false; }
      if (state.cats.length && state.cats.indexOf(p.cat) === -1) { return false; }
      if (state.subs.length && state.subs.indexOf(p.sub) === -1) { return false; }
      if (state.brands.length && state.brands.indexOf(p.brand) === -1) { return false; }
      if (state.stock.length && state.stock.indexOf(p.stock) === -1) { return false; }
      if (state.type.length && state.type.indexOf(p.type) === -1) { return false; }
      if (state.pricing.length) {
        var okp = false;
        if (state.pricing.indexOf("priced") !== -1 && p.price !== null) { okp = true; }
        if (state.pricing.indexOf("quote") !== -1 && p.isQuote) { okp = true; }
        if (state.pricing.indexOf("discounted") !== -1 && p.discountPercent) { okp = true; }
        if (!okp) { return false; }
      }
      if (state.min !== null && (p.price === null || p.price < state.min)) { return false; }
      if (state.max !== null && (p.price === null || p.price > state.max)) { return false; }
      if (state.query && !matchesQuery(p, state.query)) { return false; }
      return true;
    }

    function sortList(list) {
      var l = list.slice();
      var HI = 9007199254740991;
      if (state.sort === "price-asc") {
        l.sort(function (a, b) {
          return (a.price === null ? HI : a.price) - (b.price === null ? HI : b.price); });
      } else if (state.sort === "price-desc") {
        l.sort(function (a, b) {
          return (b.price === null ? -1 : b.price) - (a.price === null ? -1 : a.price); });
      }
      else if (state.sort === "name") { l.sort(function (a, b) { return a.name.localeCompare(b.name); }); }
      else if (state.sort === "discount") {
        l.sort(function (a, b) { return (b.discountPercent || 0) - (a.discountPercent || 0); });
      }
      return l;
    }

    function render() {
      var list = sortList(D.PRODUCTS.filter(matches));
      var total = list.length;
      var pages = Math.max(1, Math.ceil(total / state.perPage));
      if (state.page > pages) { state.page = pages; }
      var start = (state.page - 1) * state.perPage;
      var slice = list.slice(start, start + state.perPage);

      grid.className = "product-grid" + (state.view === "list" ? " product-grid--list" : "");
      grid.innerHTML = slice.length
        ? UI.renderProductGrid(slice)
        : "";

      var emptyEl = $("#catalogEmpty");
      if (emptyEl) { emptyEl.hidden = slice.length > 0; }

      if (countEl) {
        countEl.textContent = total === 0
          ? "No products found"
          : "Showing " + (start + 1) + "–" + Math.min(start + state.perPage, total) + " of " + total + " products";
      }

      if (pager) {
        if (pages <= 1) { pager.innerHTML = ""; }
        else {
          /* The real catalogue runs to thousands of products, so the pager is
             windowed: first, last, and the pages either side of the current
             one. Printing every page number would overflow the bar. */
          var html = "";
          html += state.page > 1
            ? '<a href="#" data-page="' + (state.page - 1) + '">Prev</a>'
            : '<span class="is-gap">Prev</span>';

          var wanted = {};
          wanted[1] = true; wanted[pages] = true;
          for (var d = -2; d <= 2; d++) {
            var w = state.page + d;
            if (w >= 1 && w <= pages) { wanted[w] = true; }
          }
          var nums = Object.keys(wanted).map(Number).sort(function (a, b) { return a - b; });
          var prevNum = 0;
          nums.forEach(function (n) {
            if (prevNum && n > prevNum + 1) { html += '<span class="is-gap">&hellip;</span>'; }
            html += n === state.page
              ? '<span class="is-current">' + n + "</span>"
              : '<a href="#" data-page="' + n + '">' + n + "</a>";
            prevNum = n;
          });

          html += state.page < pages
            ? '<a href="#" data-page="' + (state.page + 1) + '">Next</a>'
            : '<span class="is-gap">Next</span>';
          pager.innerHTML = html;
        }
      }

      renderChips();
    }

    function renderChips() {
      if (!chipsEl) { return; }
      var chips = [];
      state.cats.forEach(function (c) { chips.push({ t: "cat", v: c, label: D.categoryName(c) }); });
      state.brands.forEach(function (b) { chips.push({ t: "brand", v: b, label: D.brandName(b) }); });
      state.stock.forEach(function (s) { chips.push({ t: "stock", v: s, label: UI.STOCK[s].label }); });
      state.pricing.forEach(function (v) {
        chips.push({ t: "pricing", v: v,
                     label: v === "priced" ? "Has a price"
                          : v === "quote" ? "Request a Quote" : "Discounted" }); });
      if (state.min !== null || state.max !== null) {
        chips.push({ t: "price", v: "price", label: "Rs. " + (state.min || 0) + " – " + (state.max || "any") });
      }
      chipsEl.innerHTML = chips.length
        ? chips.map(function (c) {
            return '<span class="chip">' + UI.esc(c.label) +
                   '<button type="button" data-chip-type="' + c.t + '" data-chip-value="' + UI.esc(c.v) + '" ' +
                   'aria-label="Remove filter">' + UI.icon("close") + "</button></span>";
          }).join("") + '<button class="btn btn--quiet btn--sm" type="button" data-clear-all>Clear all</button>'
        : "";
    }

    /* --- filter wiring (works for both the sidebar and the mobile drawer) --- */
    document.addEventListener("change", function (e) {
      var el = e.target;
      if (!el.matches || !el.matches("[data-filter]")) { return; }
      var kind = el.getAttribute("data-filter");
      var val = el.value;

      if (kind === "cat" || kind === "brand" || kind === "stock" || kind === "type") {
        var bucket = kind === "cat" ? state.cats
                   : kind === "brand" ? state.brands
                   : kind === "stock" ? state.stock : state.type;
        var i = bucket.indexOf(val);
        if (el.checked && i === -1) { bucket.push(val); }
        if (!el.checked && i !== -1) { bucket.splice(i, 1); }
        /* keep duplicate controls (sidebar + drawer) in sync */
        $$('[data-filter="' + kind + '"][value="' + val + '"]').forEach(function (o) { o.checked = el.checked; });
      } else if (kind === "pricing") {
        var pb = state.pricing;
        var pi = pb.indexOf(val);
        if (el.checked && pi === -1) { pb.push(val); }
        if (!el.checked && pi !== -1) { pb.splice(pi, 1); }
      } else if (kind === "min") {
        state.min = el.value === "" ? null : Number(el.value);
      } else if (kind === "max") {
        state.max = el.value === "" ? null : Number(el.value);
      }
      state.page = 1;
      render();
    });

    document.addEventListener("click", function (e) {
      var sortSel = closest(e.target, "#sortSelect");
      if (sortSel) { return; }

      var pg = closest(e.target, "[data-page]");
      if (pg && pager && pager.contains(pg)) {
        e.preventDefault();
        state.page = parseInt(pg.getAttribute("data-page"), 10);
        render();
        window.scrollTo({ top: grid.getBoundingClientRect().top + window.pageYOffset - 140, behavior: "smooth" });
        return;
      }

      var view = closest(e.target, "[data-view]");
      if (view) {
        e.preventDefault();
        state.view = view.getAttribute("data-view");
        $$("[data-view]").forEach(function (b) {
          b.setAttribute("aria-pressed", b.getAttribute("data-view") === state.view ? "true" : "false");
        });
        render();
        return;
      }

      var chip = closest(e.target, "[data-chip-type]");
      if (chip) {
        e.preventDefault();
        var t = chip.getAttribute("data-chip-type");
        var v = chip.getAttribute("data-chip-value");
        if (t === "cat") { state.cats.splice(state.cats.indexOf(v), 1); }
        if (t === "brand") { state.brands.splice(state.brands.indexOf(v), 1); }
        if (t === "stock") { state.stock.splice(state.stock.indexOf(v), 1); }
        if (t === "pricing") { state.pricing.splice(state.pricing.indexOf(v), 1); }
        if (t === "price") { state.min = null; state.max = null; }
        $$("[data-filter]").forEach(function (o) {
          if (o.type === "checkbox" && o.value === v) { o.checked = false; }
          if (o.getAttribute("data-filter") === "pricing") { o.checked = false; }
          if (t === "price" && (o.getAttribute("data-filter") === "min" || o.getAttribute("data-filter") === "max")) { o.value = ""; }
        });
        state.page = 1;
        render();
        return;
      }

      if (closest(e.target, "[data-clear-all]")) {
        e.preventDefault();
        state.cats = []; state.brands = []; state.stock = []; state.type = [];
        state.pricing = []; state.min = null; state.max = null; state.page = 1;
        $$("[data-filter]").forEach(function (o) {
          if (o.type === "checkbox") { o.checked = false; } else { o.value = ""; }
        });
        render();
      }
    });

    on($("#sortSelect"), "change", function () {
      state.sort = this.value;
      state.page = 1;
      render();
    });

    render();
    return { state: state, render: render };
  }

  /* ---------- SHOP ---------- */

  /* ---------- HOME ---------- */
  /* ======================================================================
     RAIL — one horizontal carousel used by every homepage row
     ----------------------------------------------------------------------
     Native scrolling with scroll-snap does the hard work: touch, trackpad
     and momentum come free, and the arrows just page the scroller. No
     carousel library, and nothing auto-advances a row of products.
     ====================================================================== */
  function initRail(track, prev, next) {
    if (!track) { return; }

    /* Page by a whole number of cards. Measuring the step each time and
       rounding to whole cards is what stops repeated clicks accumulating
       sub-pixel drift and desynchronising the snap points. */
    function step() {
      var first = track.firstElementChild;
      if (!first) { return track.clientWidth; }
      var gap = parseFloat(getComputedStyle(track).columnGap || 16) || 0;
      return first.getBoundingClientRect().width + gap;
    }
    function page() {
      var one = step();
      var per = Math.max(1, Math.round(track.clientWidth / one));
      return one * per;
    }
    function settle() {
      /* Land exactly on a card boundary after a page. */
      var one = step();
      if (!one) { return; }
      var max = track.scrollWidth - track.clientWidth;
      var target = Math.round(track.scrollLeft / one) * one;
      track.scrollLeft = Math.max(0, Math.min(max, target));
    }
    var still = window.matchMedia &&
                window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    function scrollBy(dir) {
      track.scrollBy({ left: dir * page(), behavior: still ? "auto" : "smooth" });
    }
    function sync() {
      var max = track.scrollWidth - track.clientWidth - 2;
      if (prev) { prev.disabled = track.scrollLeft <= 2; }
      if (next) { next.disabled = track.scrollLeft >= max; }
    }

    /* Re-check the arrows once scrolling has come to rest, and realign to a
       card boundary so the row never ends up half a card out. */
    var idle = null;
    function onScroll() {
      sync();
      window.clearTimeout(idle);
      idle = window.setTimeout(function () { if (still) { settle(); } sync(); }, 140);
    }

    on(prev, "click", function () { scrollBy(-1); });
    on(next, "click", function () { scrollBy(1); });
    on(track, "scroll", onScroll);
    on(window, "resize", function () { sync(); });

    /* Keyboard: the track is focusable, so arrow keys page it. */
    on(track, "keydown", function (e) {
      if (e.key === "ArrowRight") { e.preventDefault(); scrollBy(1); }
      else if (e.key === "ArrowLeft") { e.preventDefault(); scrollBy(-1); }
    });

    /* Pointer dragging on desktop. Touch already scrolls natively, so this
       only takes over for a mouse and never blocks a click on a card. */
    var down = false, startX = 0, startLeft = 0, moved = 0;
    on(track, "pointerdown", function (e) {
      if (e.pointerType === "touch" || e.button !== 0) { return; }
      down = true; moved = 0;
      startX = e.clientX; startLeft = track.scrollLeft;
    });
    on(track, "pointermove", function (e) {
      if (!down) { return; }
      var dx = e.clientX - startX;
      if (Math.abs(dx) > 4) {
        moved = Math.abs(dx);
        track.scrollLeft = startLeft - dx;
        track.classList.add("is-dragging");
      }
    });
    ["pointerup", "pointercancel", "pointerleave"].forEach(function (ev) {
      on(track, ev, function () {
        down = false;
        track.classList.remove("is-dragging");
      });
    });
    on(track, "click", function (e) {
      if (moved > 6) { e.preventDefault(); e.stopPropagation(); moved = 0; }
    }, true);

    sync();
  }

  /* Wire every rail inside a container that follows the standard markup. */
  function initRailsIn(root) {
    $$(".rail", root).forEach(function (sec) {
      initRail($(".rail__track", sec), $("[data-rail-prev]", sec), $("[data-rail-next]", sec));
    });
  }

  /* ---------- HOME ---------- */
  PAGES.home = function () {
    /* ---- department rail beside the banner ---- */
    var railEl = $("#heroRail");
    if (railEl) { railEl.innerHTML = UI.renderHeroRail(D.railSections()); }

    /* ---- campaign banner ----
       Each slide is composed from department photography already verified in
       this repository. Copy describes what the catalogue actually holds; no
       slide claims a discount, and none is claimed anywhere unless a source
       publishes one. */
    var slides = [
      { tone: "navy", eyebrow: "Circuit Protection",
        title: "Protection Sized to Your Load",
        text: "MCBs, RCCBs, distribution boxes and changeover gear, with Himel and " +
              "Hyundai ranges quoted against the rating you specify.",
        cta: "Shop Circuit Protection", href: "category.html?cat=circuit-protection",
        cta2: "Request a Quote", href2: "quote-request.html",
        art: ["circuit-protection", "distribution-boards", "industrial-control"] },

      { tone: "slate", eyebrow: "Wires & Cables",
        title: "Cable for Every Installation",
        text: "2,386 published items across building wire, power cable, LSZH, medium " +
              "voltage and solar, straight from the Pakistan Cables catalogue.",
        cta: "Shop Wires & Cables", href: "category.html?cat=wires-cables",
        cta2: "Bulk Enquiry", href2: "quote-request.html",
        art: ["power-cables", "wires-cables", "solar-cables"] },

      { tone: "red", eyebrow: "Switches & Sockets",
        title: "Wiring Devices for Home and Office",
        text: "Modular switches, sockets, data and telephone outlets across the Aqua " +
              "and Panasonic ranges, priced as the source publishes them.",
        cta: "Shop Switches & Sockets", href: "category.html?cat=switches-sockets",
        art: ["switches-sockets", "smart-switches", "data-outlets"] },

      { tone: "navy", eyebrow: "Lighting & Fixtures",
        title: "LED Lighting, Panel to Highbay",
        text: "Panels, downlights, track, floodlights and industrial highbay fittings " +
              "from the Coarts lighting catalogue.",
        cta: "Shop Lighting", href: "category.html?cat=lighting",
        art: ["lighting", "led-lighting"] },

      { tone: "slate", eyebrow: "Fans & Smart",
        title: "Fans, Smart Switches and Controls",
        text: "Ceiling, bracket and inverter fans alongside Wi-Fi switches, curtain " +
              "motors and smart controls.",
        cta: "Shop Fans", href: "category.html?cat=fans-ventilation",
        cta2: "Smart Home", href2: "category.html?cat=smart-home",
        art: ["fans-ventilation", "smart-home", "smart-switches"] }
    ];

    var track = $("#heroTrack");
    if (track) {
      track.innerHTML = slides.map(function (sl, i) {
        return UI.renderHeroSlide(sl, i, i === 0);
      }).join("");
      var nav = $("#heroNav");
      if (nav) { nav.innerHTML = UI.renderHeroNav(slides); }
      initHeroBanner(slides);
    }

    /* ---- compact promo tiles ---- */
    var promos = $("#heroPromos");
    if (promos) {
      promos.innerHTML = [
        { kicker: "Protection", title: "Breakers & Distribution",
          cta: "Shop now", href: "category.html?cat=circuit-protection",
          img: "assets/images/categories/circuit-protection.webp" },
        { kicker: "Smart electrical", title: "Wi-Fi Switches & Devices",
          cta: "Explore", href: "category.html?cat=smart-home",
          img: "assets/images/categories/smart-home.webp" },
        { kicker: "Projects", title: "Bulk & Contractor Orders",
          cta: "Request a quote", href: "quote-request.html",
          img: "assets/images/categories/wires-cables.webp" }
      ].map(UI.renderPromoTile).join("");
    }

    /* ---- department strip ---- */
    var strip = $("#deptStrip");
    if (strip) {
      strip.innerHTML = D.departments().map(UI.renderDepartmentTile).join("");
      var head = strip.parentNode.querySelector(".rail__head");
      initRail(strip, $("[data-rail-prev]", head), $("[data-rail-next]", head));
    }

    /* ---- one product rail per department ----
       Ten products each: enough to swipe through, small enough that the
       homepage never mounts a slice of the full catalogue. */
    var rails = $("#homeRails");
    if (rails) {
      rails.innerHTML = D.HOME_RAILS.map(function (r, i) {
        var items = D.spreadProducts(r.sel, 10);
        return UI.renderRail({
          id: "rail-" + i, title: r.title, items: items,
          count: D.selectProducts(r.sel).length,
          viewAllUrl: D.selectionUrl(r.sel)
        });
      }).join("");
      initRailsIn(rails);
    }

    /* ---- genuine source discounts only ---- */
    var dealsWrap = $("#dealsRail");
    if (dealsWrap) {
      var deals = D.deals(10);
      dealsWrap.innerHTML = deals.length
        ? UI.renderRail({ id: "rail-deals", title: "Discounted at source",
                          items: deals, count: D.deals().length,
                          viewAllUrl: "deals.html" })
        : UI.renderEmpty("tag", "No current discounts",
            "None of our sources is publishing a reduced price today. Everything else " +
            "is listed at its normal price or available on quotation.",
            "shop.html", "Browse the catalogue");
      initRailsIn(dealsWrap);
    }

    $$("[data-product-count]").forEach(function (el) {
      el.textContent = D.PRODUCTS.length.toLocaleString("en-PK");
    });

    var bg = $("#brandGrid");
    if (bg) { bg.innerHTML = D.BRANDS.slice(0, 12).map(UI.renderBrandCard).join(""); }
  };

  /* ======================================================================
     HERO BANNER
     ----------------------------------------------------------------------
     A transform-based slider: the track carries every slide side by side and
     is translated, so there is no cross-fade flash and nothing is hidden and
     re-shown on load. Autoplay pauses whenever the visitor is looking at it
     (hover), interacting with it (focus), or not looking at the page at all
     (hidden tab), and does not run at all under reduced motion.
     ====================================================================== */
  var HERO_INTERVAL = 7000;

  function initHeroBanner(slides) {
    var track = $("#heroTrack");
    if (!track) { return; }
    var items = $$(".hslide", track);
    if (!items.length) { return; }

    var nav = $("#heroNav");
    var buttons = nav ? $$(".hnav", nav) : [];
    var status = $("#heroStatus");
    var banner = $("#heroBanner");
    var still = window.matchMedia &&
                window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    var index = 0, timer = null, paused = false;

    function paint() {
      track.style.transform = "translate3d(" + (-index * 100) + "%,0,0)";
      items.forEach(function (el, i) {
        el.classList.toggle("is-active", i === index);
        /* Off-screen slides are removed from the tab order so keyboard focus
           never lands on a control the visitor cannot see. */
        $$("a,button", el).forEach(function (c) {
          if (i === index) { c.removeAttribute("tabindex"); }
          else { c.setAttribute("tabindex", "-1"); }
        });
      });
      buttons.forEach(function (b, i) {
        if (i === index) { b.setAttribute("aria-current", "true"); }
        else { b.removeAttribute("aria-current"); }
        b.classList.toggle("is-active", i === index);
      });
      if (status) {
        status.textContent = "Slide " + (index + 1) + " of " + items.length + ": " +
                             (slides[index] ? slides[index].eyebrow : "");
      }
    }

    function go(n) {
      index = (n + items.length) % items.length;
      paint();
    }

    function stop() { window.clearInterval(timer); timer = null; }
    function play() {
      stop();
      if (still || paused || document.hidden) { return; }
      timer = window.setInterval(function () { go(index + 1); }, HERO_INTERVAL);
    }
    function restart() { go(index); play(); }

    buttons.forEach(function (b, i) {
      on(b, "click", function () { go(i); play(); });
    });
    on($("#heroPrev"), "click", function () { go(index - 1); play(); });
    on($("#heroNext"), "click", function () { go(index + 1); play(); });

    /* Pause while the pointer is over the banner or focus is inside it. */
    on(banner, "mouseenter", function () { paused = true; stop(); });
    on(banner, "mouseleave", function () { paused = false; play(); });
    on(banner, "focusin", function () { paused = true; stop(); });
    on(banner, "focusout", function () { paused = false; play(); });

    /* Nothing should keep ticking in a tab nobody is looking at. */
    on(document, "visibilitychange", function () {
      if (document.hidden) { stop(); } else { play(); }
    });

    /* Keyboard: the banner takes arrow keys when anything inside it has focus. */
    on(banner, "keydown", function (e) {
      if (e.key === "ArrowRight") { e.preventDefault(); go(index + 1); play(); }
      else if (e.key === "ArrowLeft") { e.preventDefault(); go(index - 1); play(); }
    });

    /* Swipe. Horizontal intent only, so a vertical scroll is never hijacked. */
    var x0 = null, y0 = null, dx = 0;
    on(track, "pointerdown", function (e) {
      if (e.pointerType === "mouse" && e.button !== 0) { return; }
      x0 = e.clientX; y0 = e.clientY; dx = 0;
    });
    on(track, "pointermove", function (e) {
      if (x0 === null) { return; }
      dx = e.clientX - x0;
      if (Math.abs(dx) > Math.abs(e.clientY - y0) && Math.abs(dx) > 8) {
        track.style.transform = "translate3d(calc(" + (-index * 100) + "% + " + dx + "px),0,0)";
      }
    });
    ["pointerup", "pointercancel", "pointerleave"].forEach(function (ev) {
      on(track, ev, function () {
        if (x0 === null) { return; }
        if (Math.abs(dx) > 60) { go(index + (dx < 0 ? 1 : -1)); }
        else { paint(); }
        x0 = null; dx = 0;
        play();
      });
    });

    paint();
    play();
  }

  /* ======================================================================
     RAIL — one horizontal carousel used by every homepage row
     ----------------------------------------------------------------------
     Native scrolling with scroll-snap does the hard work: touch, trackpad
     and momentum come free, and the arrows just page the scroller. No
     carousel library, and nothing auto-advances a row of products.
     ====================================================================== */
  function initRail(track, prev, next) {
    if (!track) { return; }

    function page() {
      var first = track.firstElementChild;
      if (!first) { return track.clientWidth; }
      var step = first.getBoundingClientRect().width +
                 parseFloat(getComputedStyle(track).columnGap || 16);
      var per = Math.max(1, Math.floor(track.clientWidth / step));
      return step * per;
    }
    var still = window.matchMedia &&
                window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    function scrollBy(dir) {
      track.scrollBy({ left: dir * page(), behavior: still ? "auto" : "smooth" });
    }
    function sync() {
      var max = track.scrollWidth - track.clientWidth - 2;
      if (prev) { prev.disabled = track.scrollLeft <= 2; }
      if (next) { next.disabled = track.scrollLeft >= max; }
    }

    on(prev, "click", function () { scrollBy(-1); });
    on(next, "click", function () { scrollBy(1); });
    on(track, "scroll", sync);
    on(window, "resize", sync);

    /* Keyboard: the track is focusable, so arrow keys page it. */
    on(track, "keydown", function (e) {
      if (e.key === "ArrowRight") { e.preventDefault(); scrollBy(1); }
      else if (e.key === "ArrowLeft") { e.preventDefault(); scrollBy(-1); }
    });

    /* Pointer dragging on desktop. Touch already scrolls natively, so this
       only takes over for a mouse and never blocks a click on a card. */
    var down = false, startX = 0, startLeft = 0, moved = 0;
    on(track, "pointerdown", function (e) {
      if (e.pointerType === "touch" || e.button !== 0) { return; }
      down = true; moved = 0;
      startX = e.clientX; startLeft = track.scrollLeft;
    });
    on(track, "pointermove", function (e) {
      if (!down) { return; }
      var dx = e.clientX - startX;
      if (Math.abs(dx) > 4) {
        moved = Math.abs(dx);
        track.scrollLeft = startLeft - dx;
        track.classList.add("is-dragging");
      }
    });
    ["pointerup", "pointercancel", "pointerleave"].forEach(function (ev) {
      on(track, ev, function () {
        down = false;
        track.classList.remove("is-dragging");
      });
    });
    on(track, "click", function (e) {
      if (moved > 6) { e.preventDefault(); e.stopPropagation(); moved = 0; }
    }, true);

    sync();
  }

  /* Wire every rail inside a container that follows the standard markup. */
  function initRailsIn(root) {
    $$(".rail", root).forEach(function (sec) {
      initRail($(".rail__track", sec), $("[data-rail-prev]", sec), $("[data-rail-next]", sec));
    });
  }


  PAGES.shop = function () {
    buildFilterUI();
    var c = catalog({ brand: param("brand"), cat: param("cat") });
    /* Pre-tick anything that came in through the URL */
    if (param("brand")) {
      $$('[data-filter="brand"][value="' + param("brand") + '"]').forEach(function (o) { o.checked = true; });
    }
    if (param("cat")) {
      $$('[data-filter="cat"][value="' + param("cat") + '"]').forEach(function (o) { o.checked = true; });
    }
    if (c) { c.render(); }

    var side = initSideDrawer("filterDrawer");
    on($("#filterOpen"), "click", function (e) { e.preventDefault(); if (side) { side.open(); } });
  };

  /* --------------------------------------------------------------------
     Filter panel — rendered once here and injected into every
     [data-filters] container (the desktop sidebar AND the mobile drawer),
     so the four catalog pages share one definition.
     On WordPress this becomes the WooCommerce filter widget area.
     -------------------------------------------------------------------- */
  function countBy(key, value) {
    return D.PRODUCTS.filter(function (p) { return p[key] === value; }).length;
  }

  function filterPanelHTML(uid) {
    function group(id, title, body, open) {
      var pid = uid + "-" + id;
      return '<div class="filter-group">' +
               '<button class="filter-group__btn" type="button" data-toggle-panel ' +
                       'aria-expanded="' + (open ? "true" : "false") + '" aria-controls="' + pid + '">' +
                 UI.esc(title) + UI.icon("chevdown") +
               "</button>" +
               '<div class="filter-group__body" id="' + pid + '"' + (open ? "" : " hidden") + ">" + body + "</div>" +
             "</div>";
    }

    var cats = D.CATEGORIES.map(function (c) {
      return '<label class="filter-opt"><input type="checkbox" data-filter="cat" value="' + c.slug + '">' +
             "<span>" + UI.esc(c.name) + '</span><span class="count">' + countBy("cat", c.slug) + "</span></label>";
    }).join("");

    var brands = D.BRANDS.map(function (b) {
      var n = countBy("brand", b.slug);
      if (!n) { return ""; }
      return '<label class="filter-opt"><input type="checkbox" data-filter="brand" value="' + b.slug + '">' +
             "<span>" + UI.esc(b.name) + '</span><span class="count">' + n + "</span></label>";
    }).join("");

    var price =
      '<div class="price-range">' +
        '<label class="sr-only" for="' + uid + '-min">Minimum price</label>' +
        '<input class="input" id="' + uid + '-min" type="number" min="0" placeholder="Min" data-filter="min">' +
        "<span>&ndash;</span>" +
        '<label class="sr-only" for="' + uid + '-max">Maximum price</label>' +
        '<input class="input" id="' + uid + '-max" type="number" min="0" placeholder="Max" data-filter="max">' +
      "</div>" +
      '<p class="field__hint" style="margin-top:8px">Prices in PKR, exactly as published by each ' +
        "product's own source.</p>";

    var stock = ["in", "out", "unknown"].map(function (s) {
      return '<label class="filter-opt"><input type="checkbox" data-filter="stock" value="' + s + '">' +
             "<span>" + (UI.STOCK[s] ? UI.STOCK[s].label : s) + '</span><span class="count">' +
             D.PRODUCTS.filter(function (p) { return p.stock === s; }).length + "</span></label>";
    }).join("");

    /* No approved source publishes ratings, so a star filter would be fabricated.
       Filter by how the product is sold instead. */
    var pricing =
      '<label class="filter-opt"><input type="checkbox" data-filter="pricing" value="priced">' +
        '<span>Has a published price</span><span class="count">' +
        D.PRODUCTS.filter(function (x) { return x.price !== null; }).length + "</span></label>" +
      '<label class="filter-opt"><input type="checkbox" data-filter="pricing" value="quote">' +
        '<span>Request a Quote</span><span class="count">' +
        D.PRODUCTS.filter(function (x) { return x.isQuote; }).length + "</span></label>" +
      '<label class="filter-opt"><input type="checkbox" data-filter="pricing" value="discounted">' +
        '<span>Discounted at source</span><span class="count">' +
        D.PRODUCTS.filter(function (x) { return x.discountPercent; }).length + "</span></label>";

    var types =
      '<label class="filter-opt"><input type="checkbox" data-filter="type" value="simple">' +
        '<span>Simple products</span><span class="count">' + countBy("type", "simple") + "</span></label>" +
      '<label class="filter-opt"><input type="checkbox" data-filter="type" value="variable">' +
        '<span>Variable products</span><span class="count">' + countBy("type", "variable") + "</span></label>";

    return '<div class="filters__head">' +
             "<h2>Filters</h2>" +
             '<button class="btn btn--quiet btn--sm" type="button" data-clear-all>Clear all</button>' +
           "</div>" +
           group("cat", "Categories", cats, true) +
           group("brand", "Brands", brands, true) +
           group("price", "Price", price, true) +
           group("stock", "Availability", stock, true) +
           group("pricing", "How it is sold", pricing, false) +
           group("type", "Product type", types, false);
  }

  function buildFilterUI() {
    $$("[data-filters]").forEach(function (el, i) {
      el.innerHTML = filterPanelHTML("f" + i);
    });
  }

  /* ---------- CATEGORY ---------- */
  PAGES.category = function () {
    /* A department page (?dept=) is a merchandising view across categories; a
       category page (?cat=) is the taxonomy. Both render through the same
       catalogue engine. */
    var dept = param("dept");
    var department = dept ? D.departmentBySlug(dept) : null;
    var slug = param("cat") || D.CATEGORIES[0].slug;
    var sub = param("sub");
    var cat = D.categoryBySlug(slug) || D.CATEGORIES[0];

    var bc = $("#breadcrumb");
    if (bc) {
      var items = [{ label: "Home", href: "index.html" }, { label: "Shop", href: "shop.html" }];
      if (department) {
        items.push({ label: department.label });
      } else if (sub) {
        items.push({ label: cat.name, href: "category.html?cat=" + cat.slug });
        var s = cat.subs.filter(function (x) { return x.slug === sub; })[0];
        items.push({ label: s ? s.name : sub });
      } else {
        items.push({ label: cat.name });
      }
      bc.innerHTML = UI.renderBreadcrumb(items);
    }

    var heading = department ? department.label : cat.name;
    var t = $("#catTitle"); if (t) { t.textContent = heading; }
    var b = $("#catBlurb");
    if (b) { b.textContent = department ? (department.blurb || "") : cat.blurb; }
    var art = $("#catArt");
    if (art) {
      art.src = department ? department.img : D.categoryImage(cat.slug);
      art.alt = heading + " products";
    }
    document.title = heading + " — Star Electric Enterprises";

    /* Subcategory tiles only exist for a category that has them. A department
       view, or a category whose source publishes ranges rather than products,
       hides the heading rather than showing an empty row under it. */
    var sg = $("#subcatGrid");
    var st = $("#subcatTitle");
    var subs = (!department && cat.subs) ? cat.subs : [];
    if (sg) {
      sg.innerHTML = subs.map(function (s) { return UI.renderSubcatCard(cat.slug, s); }).join("");
      sg.hidden = !subs.length;
    }
    if (st) { st.hidden = !subs.length; }

    buildFilterUI();
    var c = department ? catalog({ dept: dept }) : catalog({ cat: slug, sub: sub });
    if (!department) {
      $$('[data-filter="cat"][value="' + slug + '"]').forEach(function (o) { o.checked = true; });
    }
    if (c) { c.render(); }

    /* Families in this category are shown below the products, clearly separated
       from them, because they are not individually purchasable items. */
    var fsec = $("#catFamilySection");
    var flist = $("#catFamilyList");
    if (fsec && flist) {
      var fams = department ? D.familiesForDepartment(dept) : D.familiesForCategory(slug);
      var byBrand = {};
      fams.forEach(function (f) {
        /* Electro Traders' six brand pages all return the same list, so the
           brand behind those ranges could not be established from the source
           and is not guessed here. */
        var key = (!f.brand || f.brand === "Not specified") ? "Brand not stated by source" : f.brand;
        (byBrand[key] = byBrand[key] || []).push(f);
      });
      var names = Object.keys(byBrand).sort();
      flist.innerHTML = names.map(function (n) {
        return UI.renderFamilyPanel(n, byBrand[n], byBrand[n][0].reason || "");
      }).join("");
      fsec.hidden = names.length === 0;
    }

    var side = initSideDrawer("filterDrawer");
    on($("#filterOpen"), "click", function (e) { e.preventDefault(); if (side) { side.open(); } });
  };

  /* ---------- SEARCH RESULTS ---------- */
  PAGES.search = function () {
    var q = param("s");
    var cat = param("product_cat");
    $$("[data-query]").forEach(function (el) { el.textContent = q || "(empty search)"; });
    document.title = "Search: " + (q || "all products") + " — Star Electric Enterprises";

    buildFilterUI();
    var c = catalog({ query: q, cat: cat });
    if (cat) { $$('[data-filter="cat"][value="' + cat + '"]').forEach(function (o) { o.checked = true; }); }
    if (c) { c.render(); }

    /* Ranges matching the query. A search for "Furse" or "coaxial" hits a
       range rather than a product, and that should not look like nothing. */
    var rsec = $("#searchRanges");
    var rlist = $("#searchRangesList");
    if (rsec && rlist) {
      var term = (q || "").toLowerCase().trim();
      var hits = !term ? [] : D.FAMILIES.filter(function (f) {
        var hay = [f.name, f.brand, f.series, f.summary,
                   String(f.category || "").replace(/-/g, " ")].join(" ").toLowerCase();
        return term.split(/\s+/).every(function (w) { return hay.indexOf(w) !== -1; });
      });
      var byBrand = {};
      hits.forEach(function (f) {
        var k = (!f.brand || f.brand === "Not specified") ? "Brand not stated by source" : f.brand;
        (byBrand[k] = byBrand[k] || []).push(f);
      });
      var names = Object.keys(byBrand).sort();
      rlist.innerHTML = names.map(function (n) {
        return UI.renderFamilyPanel(n, byBrand[n], byBrand[n][0].reason || "");
      }).join("");
      rsec.hidden = !hits.length;
    }

    var sugg = $("#suggestedCats");
    if (sugg) { sugg.innerHTML = D.CATEGORIES.slice(0, 6).map(UI.renderCategoryCard).join(""); }

    var side = initSideDrawer("filterDrawer");
    on($("#filterOpen"), "click", function (e) { e.preventDefault(); if (side) { side.open(); } });
  };

  /* ---------- DEALS ---------- */
  PAGES.deals = function () {
    buildFilterUI();
    var c = catalog({ onlyDeals: true });
    if (c) { c.state.sort = "discount"; c.render(); }

    var side = initSideDrawer("filterDrawer");
    on($("#filterOpen"), "click", function (e) { e.preventDefault(); if (side) { side.open(); } });
  };

  /* ---------- PRODUCT DETAIL ---------- */
  PAGES.product = function () {
    var id = param("id");
    var light = id ? D.productById(id) : null;

    /* An unknown id must never be answered with a different product: that would
       show one product's price and specification under another one's link. */
    if (!light) {
      var main = $("#main");
      var fam = id ? D.familyById(id) : null;
      if (main) {
        main.innerHTML =
          '<section class="section"><div class="container">' +
            UI.renderEmpty("box", fam ? "That is a product range, not a single product"
                                      : "Product not found",
              fam ? fam.name + " is a range published by " + fam.sourceDomain +
                    ", which does not list its individual products. Ask us about the range and " +
                    "we will quote against the rating or size you need."
                  : "We could not find that product in the catalogue. It may have been removed " +
                    "from the source it came from.",
              fam ? "quote-request.html?family=" + encodeURIComponent(fam.id) : "shop.html",
              fam ? "Ask about this range" : "Browse the catalogue") +
          "</div></section>";
      }
      document.title = (fam ? fam.name : "Product not found") + " — Star Electric Enterprises";
      return;
    }

    renderProductShell(light, null);          // paint immediately from the index
    D.loadDetail(id, function (full) {        // then enrich from the source record
      if (full) { renderProductShell(light, full); }
    });
  };

  function renderProductShell(p, full) {
    var cat = D.categoryBySlug(p.cat);
    document.title = p.name + " — Star Electric Enterprises";
    var md = $("#metaDesc");
    if (md) { md.setAttribute("content", (full && full.shortDescription) || p.name); }

    var bc = $("#breadcrumb");
    if (bc) {
      var items = [{ label: "Home", href: "index.html" },
                   { label: "Shop", href: "shop.html" }];
      if (cat) { items.push({ label: cat.name, href: "category.html?cat=" + p.cat }); }
      if (p.sub) {
        items.push({ label: subLabel(p.sub),
                     href: "category.html?cat=" + p.cat + "&sub=" + p.sub });
      }
      items.push({ label: p.name });
      bc.innerHTML = UI.renderBreadcrumb(items);
    }

    /* ---- gallery ----
       Only images downloaded from this product's own approved source are shown.
       Remote URLs are never hotlinked, no category icon or stock photograph is
       substituted, and where the source published nothing the gallery says so. */
    var gallery = [];
    if (full && full.images) {
      if (full.images.featured) { gallery.push(full.images.featured); }
      (full.images.gallery || []).forEach(function (g) { gallery.push(g); });
    }
    if (!gallery.length && p.img) { gallery.push(D.productImage(p)); }
    var main = $("#galleryMain");
    var noImg = $("#galleryNoImage");
    if (main) {
      if (gallery.length) {
        main.src = gallery[0];
        main.alt = p.name;
        main.hidden = false;
        if (noImg) { noImg.hidden = true; }
      } else {
        main.removeAttribute("src");
        main.hidden = true;
        if (noImg) { noImg.hidden = false; }
      }
    }
    var thumbs = $("#galleryThumbs");
    if (thumbs) {
      thumbs.innerHTML = gallery.length > 1 ? gallery.map(function (g, i) {
        return '<button class="gallery__thumb" type="button" role="tab" aria-selected="' + (i === 0) +
               '" data-src="' + UI.esc(g) + '" aria-label="View image ' + (i + 1) + '">' +
               '<img src="' + UI.esc(g) + '" alt="" loading="lazy" aria-hidden="true"></button>';
      }).join("") : "";
      thumbs.onclick = function (e) {
        var b = closest(e.target, ".gallery__thumb");
        if (!b) { return; }
        $$(".gallery__thumb", thumbs).forEach(function (o) { o.setAttribute("aria-selected", "false"); });
        b.setAttribute("aria-selected", "true");
        if (main) { main.src = b.getAttribute("data-src"); }
      };
    }

    /* Say plainly where the picture came from whenever it is not a photograph
       of this exact item. */
    var noteEl = $("#galleryImageNote");
    if (noteEl) {
      var kind = (full && full.imageType) || p.imgType;
      var extra = (full && full.imageNote) || "";
      if (kind === "manufacturer-image" || kind === "representative-image") {
        var label = kind === "manufacturer-image"
          ? "Manufacturer range image."
          : "Representative image.";
        noteEl.innerHTML = "<strong>" + label + "</strong> " + UI.esc(extra ||
          "This shows the correct product type, not a photograph of this exact item.");
        noteEl.hidden = false;
      } else {
        noteEl.hidden = true;
      }
    }

    var badges = $("#galleryBadges");
    if (badges) {
      badges.innerHTML = (p.discountPercent ? '<span class="tag tag--sale">-' + p.discountPercent + "%</span>" : "") +
                         (UI.isQuote(p) ? '<span class="tag tag--quote">Request Quote</span>' : "");
    }

    setText("#pdpBrand", p.brand || "");
    setText("#pdpTitle", p.name);
    setText("#pdpSku", p.sku || p.model || "Not specified");
    setText("#pdpCat", cat ? cat.name : "");
    setHTML("#pdpCat2", cat ? '<a class="link-inline" href="category.html?cat=' + p.cat + '">' +
                              UI.esc(cat.name) + "</a>" : "");

    /* ratings: no approved source publishes them, so the block is removed */
    var rate = $("#pdpRating");
    if (rate) { rate.innerHTML = '<span class="t-xs t-faint">No customer ratings published</span>'; }

    /* #pdpPrice is already the .price container, so only the inner spans. */
    setHTML("#pdpPrice", UI.priceHtml(p, "bare"));
    var save = $("#pdpSave");
    if (save) {
      var has = !UI.isQuote(p) && p.oldPrice && p.price;
      save.textContent = has ? "You save " + UI.money(p.oldPrice - p.price) : "";
      save.hidden = !has;
    }

    var st = UI.STOCK[p.stock] || { cls: "pill--unknown", label: "Availability not specified" };
    var stockEl = $("#pdpStock");
    if (stockEl) { stockEl.className = "pill " + st.cls; stockEl.textContent = st.label; }

    setText("#pdpShort", (full && full.shortDescription) || "");

    /* Description tab: only what the source actually publishes. */
    var desc = $("#pdpDescription");
    if (desc) {
      var body = "";
      if (full && full.shortDescription) {
        body += "<p>" + UI.esc(full.shortDescription) + "</p>";
      }
      if (full && (full.features || []).length) {
        body += "<h3>Key points</h3><ul>" + full.features.map(function (b) {
          return "<li>" + UI.esc(b) + "</li>"; }).join("") + "</ul>";
      }
      if (!body) {
        body = '<p class="t-muted">' + UI.esc(p.name) + " is listed exactly as " +
               UI.esc(p.sourceDomain || "its source") + " publishes it, and that source " +
               "publishes no description for it. The specifications tab shows every value " +
               "the source does publish.</p>";
      }
      desc.innerHTML = body;
    }
    setHTML("#pdpBullets", (full && full.features || []).map(function (b) {
      return "<li>" + UI.esc(b) + "</li>"; }).join(""));

    /* ---- variations: only real source variations ---- */
    var varWrap = $("#pdpVariations");
    var chosen = "";
    if (varWrap) {
      var vars = (full && full.variations) || [];
      if (vars.length) {
        varWrap.hidden = false;
        /* Swatch values are the source's own variant names. When a variant
           combines more than one option axis its name already spells them out,
           so the group is labelled generically rather than by one axis. */
        var optKeys = Object.keys(vars[0].options || {});
        var optName = optKeys.length === 1 ? optKeys[0] : (optKeys.length ? "Options" : "Option");
        chosen = vars[0].name;
        varWrap.innerHTML =
          '<div class="variation">' +
            '<p class="variation__label">' + UI.esc(optName) +
              ': <span id="varChosen">' + UI.esc(chosen) + "</span></p>" +
            '<div class="swatches">' + vars.map(function (v, i) {
              return '<button class="swatch" type="button" aria-pressed="' + (i === 0) +
                     '" data-val="' + UI.esc(v.name) + '"' +
                     (v.price ? ' data-price="' + v.price + '"' : "") + ">" +
                     UI.esc(v.name) + "</button>"; }).join("") +
            "</div>" +
          "</div>";
        varWrap.onclick = function (e) {
          var b = closest(e.target, ".swatch");
          if (!b) { return; }
          $$(".swatch", varWrap).forEach(function (o) { o.setAttribute("aria-pressed", "false"); });
          b.setAttribute("aria-pressed", "true");
          chosen = b.getAttribute("data-val");
          setText("#varChosen", chosen);
          var vp = b.getAttribute("data-price");
          if (vp) { setHTML("#pdpPrice", '<span class="price__now">' + UI.money(+vp) + "</span>"); }
          var ab = $("#pdpAdd");
          if (ab) { ab.setAttribute("data-variant", chosen); }
        };
      } else {
        varWrap.hidden = true;
      }
    }

    /* ---- buy area: Request a Quote when the source publishes no price ---- */
    var addBtn = $("#pdpAdd");
    var buyNow = $("#pdpBuy");
    var qtyField = $("#pdpQty");
    if (addBtn) {
      if (UI.isQuote(p)) {
        addBtn.outerHTML = '<a class="btn btn--accent btn--lg" id="pdpAdd" href="quote-request.html?product=' +
                           encodeURIComponent(p.id) + '">Request a Quote</a>';
        if (buyNow) { buyNow.hidden = true; }
        if (qtyField && qtyField.closest(".field")) { qtyField.closest(".field").hidden = true; }
      } else {
        addBtn.setAttribute("data-id", p.id);
        addBtn.setAttribute("data-variant", chosen);
        addBtn.setAttribute("data-qty-from", "#pdpQty");
        if (p.stock === "out") { addBtn.disabled = true; addBtn.textContent = "Out of Stock"; }
      }
    }
    var wishBtn = $("#pdpWish");
    if (wishBtn) {
      wishBtn.setAttribute("data-id", p.id);
      if (SEE_STORE.inWishlist(p.id)) { wishBtn.classList.add("is-active"); }
    }
    if (buyNow && !UI.isQuote(p)) {
      buyNow.onclick = function (e) {
        e.preventDefault();
        if (p.stock === "out") { return; }
        SEE_STORE.add(p.id, parseInt((qtyField || {}).value, 10) || 1, chosen);
        syncCounts();
        window.location.href = "checkout.html";
      };
    }

    /* ---- specifications, in the source's own terminology ---- */
    var specs = $("#pdpSpecs");
    if (specs) {
      var sp = (full && full.specifications) || {};
      var keys = Object.keys(sp);
      specs.innerHTML = keys.length ? keys.map(function (k) {
        var v = sp[k];
        return "<tr><th scope=\"row\">" + UI.esc(k) + "</th><td>" +
               UI.esc(Array.isArray(v) ? v.join(", ") : v) + "</td></tr>";
      }).join("") :
        '<tr><td colspan="2" class="placeholder">No specifications published by the source.</td></tr>';
    }

    /* ---- source attribution + import notes ---- */
    var meta = $("#pdpSourceMeta");
    if (meta && full) {
      meta.innerHTML =
        '<div><dt>Source</dt><dd><a class="link-inline" href="' + UI.esc(full.source.url) +
          '" target="_blank" rel="noopener nofollow">' + UI.esc(full.source.website) + "</a></dd></div>" +
        '<div><dt>Reference checked</dt><dd>' + UI.esc((full.source.checkedAt || "").slice(0, 10)) + "</dd></div>" +
        (full.model ? '<div><dt>Model</dt><dd>' + UI.esc(full.model) + "</dd></div>" : "") +
        (full.series ? '<div><dt>Series</dt><dd>' + UI.esc(full.series) + "</dd></div>" : "");
    }
    var notes = $("#pdpNotes");
    if (notes) {
      var ns = (full && full.importNotes) || [];
      notes.innerHTML = ns.length ? ns.map(function (n) {
        return '<li>' + UI.esc(n) + "</li>"; }).join("") : "";
      notes.hidden = !ns.length;
      /* The Additional Information tab holds nothing but these notes, so it is
         removed rather than opened onto an empty panel. */
      var addTab = $("#tabAdd");
      if (addTab && !ns.length) {
        addTab.hidden = true;
        var addPanel = $("#panelAdd");
        if (addPanel) { addPanel.hidden = true; }
      }
    }

    /* ---- related: same subcategory, then same category, then same brand ---- */
    var rel = D.PRODUCTS.filter(function (x) {
      return x.id !== p.id && x.sub === p.sub && x.cat === p.cat; }).slice(0, 4);
    if (rel.length < 4) {
      rel = rel.concat(D.PRODUCTS.filter(function (x) {
        return x.id !== p.id && x.cat === p.cat && rel.indexOf(x) === -1; })
        .slice(0, 4 - rel.length));
    }
    if (rel.length < 4) {
      rel = rel.concat(D.PRODUCTS.filter(function (x) {
        return x.id !== p.id && x.brand === p.brand && rel.indexOf(x) === -1; })
        .slice(0, 4 - rel.length));
    }
    fill("#relatedGrid", rel);

    /* ---- recently viewed ---- */
    var seen = [];
    try { seen = JSON.parse(window.sessionStorage.getItem("see_seen") || "[]"); }
    catch (e) { seen = []; }
    var recent = seen.filter(function (x) { return x !== p.id; })
                     .map(function (x) { return D.productById(x); })
                     .filter(Boolean).slice(0, 4);
    var rv = $("#recentSection");
    if (recent.length && rv) { rv.hidden = false; fill("#recentGrid", recent); }
    seen.unshift(p.id);
    try { window.sessionStorage.setItem("see_seen", JSON.stringify(seen.slice(0, 8))); }
    catch (e) { /* ignore */ }
  }

  function subLabel(slug) {
    return String(slug || "").replace(/-/g, " ").replace(/\b\w/g, function (c) {
      return c.toUpperCase(); });
  }

  function setText(sel, v) { var el = $(sel); if (el) { el.textContent = v; } }
  function setHTML(sel, v) { var el = $(sel); if (el) { el.innerHTML = v; } }

  /* ---------- BRANDS ---------- */
  PAGES.brands = function () {
    var grid = $("#brandDirectory");
    if (!grid) { return; }
    var letter = "";
    var query = "";

    /* Only brands that actually have verified individual products get a
       shoppable tile. Brands whose source publishes ranges only are listed
       separately, as families, and are never presented as products. */
    function matches(b) {
      if (letter && b.name.charAt(0).toUpperCase() !== letter) { return false; }
      if (query && b.name.toLowerCase().indexOf(query.toLowerCase()) === -1) { return false; }
      return true;
    }

    function render() {
      var shoppable = D.BRANDS.filter(function (b) { return b.count > 0 && matches(b); });
      var familyOnly = D.BRANDS.filter(function (b) { return b.count === 0 && matches(b); });

      grid.innerHTML = shoppable.length ? shoppable.map(UI.renderBrandCard).join("") : "";
      var empty = $("#brandEmpty");
      if (empty) { empty.hidden = shoppable.length > 0 || familyOnly.length > 0; }
      var cnt = $("#brandCount");
      if (cnt) {
        cnt.textContent = shoppable.length + (shoppable.length === 1 ? " brand" : " brands") +
          " with individual products" +
          (familyOnly.length ? " · " + familyOnly.length + " listed at family level" : "");
      }

      var fs = $("#familySection");
      var fd = $("#familyDirectory");
      if (fs && fd) {
        fd.innerHTML = familyOnly.map(function (b) {
          return UI.renderFamilyPanel(b.name, D.familiesForBrand(b.slug), b.note);
        }).join("");
        fs.hidden = familyOnly.length === 0;
      }
    }

    var az = $("#azBar");
    if (az) {
      var letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
      var available = {};
      D.BRANDS.forEach(function (b) {
        available[b.name.charAt(0).toUpperCase()] = true;
      });
      az.innerHTML = '<button type="button" data-letter="" aria-pressed="true">All</button>' +
        letters.map(function (l) {
          return '<button type="button" data-letter="' + l + '" aria-pressed="false"' +
                 (available[l] ? "" : " disabled") + ">" + l + "</button>";
        }).join("");
      az.addEventListener("click", function (e) {
        var b = closest(e.target, "[data-letter]");
        if (!b || b.disabled) { return; }
        letter = b.getAttribute("data-letter");
        $$("[data-letter]", az).forEach(function (o) { o.setAttribute("aria-pressed", o === b ? "true" : "false"); });
        render();
      });
    }
    on($("#brandSearch"), "input", function () { query = this.value; render(); });
    render();
  };

  /* ---------- WISHLIST ---------- */
  PAGES.wishlist = function () {
    var wrap = $("#wishlistBody");
    var empty = $("#wishlistEmpty");
    if (!wrap) { return; }
    var list = SEE_STORE.wishlist();

    if (!list.length) {
      wrap.innerHTML = "";
      if (empty) { empty.hidden = false; }
      var pn = $("#wishlistPanel");
      if (pn) { pn.hidden = true; }
      return;
    }
    if (empty) { empty.hidden = true; }
    var pn2 = $("#wishlistPanel");
    if (pn2) { pn2.hidden = false; }

    wrap.innerHTML = list.map(function (p) {
      var st = UI.STOCK[p.stock];
      return "<tr>" +
        '<td class="td-full">' +
          '<div class="cart-item">' +
            '<span class="cart-item__img">' + thumb(p) + "</span>" +
            "<span>" +
              '<span class="cart-item__name"><a href="product.html?id=' + encodeURIComponent(p.id) + '">' +
                UI.esc(p.name) + "</a></span>" +
              '<span class="cart-item__var">' + UI.esc(D.brandName(p.brand)) + "</span>" +
            "</span>" +
          "</div>" +
        "</td>" +
        '<td data-label="Price">' +
          (UI.isQuote(p) ? '<span class="price__quote">Request a Quote</span>'
                         : '<span class="price__now">' + UI.money(p.price) + "</span>") + "</td>" +
        '<td data-label="Stock"><span class="pill ' + st.cls + '">' + st.label + "</span></td>" +
        '<td data-label="Actions">' +
          '<div class="btn-row">' +
            UI.ctaHtml(p, { cls: "btn btn--ghost btn--sm", quoteCls: "btn btn--accent btn--sm", icon: false }) +
            '<button class="icon-btn icon-btn--sm" type="button" data-wish-remove="' + UI.esc(p.id) + '" ' +
              'aria-label="Remove from wishlist">' + UI.icon("trash") + "</button>" +
          "</div>" +
        "</td>" +
      "</tr>";
    }).join("");

    wrap.addEventListener("click", function (e) {
      var r = closest(e.target, "[data-wish-remove]");
      if (!r) { return; }
      e.preventDefault();
      SEE_STORE.removeWish(r.getAttribute("data-wish-remove"));
      syncCounts();
      toast("Removed from wishlist", "heart");
      PAGES.wishlist();
    }, { once: true });
  };

  /* ---------- CART ---------- */
  PAGES.cart = function () {
    var body = $("#cartBody");
    var empty = $("#cartEmpty");
    var panel = $("#cartPanel");
    var summary = $("#cartSummary");
    if (!body) { return; }

    function render() {
      var lines = SEE_STORE.cartLines();
      if (!lines.length) {
        body.innerHTML = "";
        if (empty) { empty.hidden = false; }
        if (panel) { panel.hidden = true; }
        if (summary) { summary.hidden = true; }
        syncCounts();
        return;
      }
      if (empty) { empty.hidden = true; }
      if (panel) { panel.hidden = false; }
      if (summary) { summary.hidden = false; }

      body.innerHTML = lines.map(function (x, i) {
        var p = x.product;
        return "<tr>" +
          '<td class="td-full">' +
            '<div class="cart-item">' +
              '<span class="cart-item__img">' + thumb(p) + "</span>" +
              "<span>" +
                '<span class="cart-item__name"><a href="product.html?id=' + encodeURIComponent(p.id) + '">' +
                  UI.esc(p.name) + "</a></span>" +
                '<span class="cart-item__var">' + UI.esc(D.brandName(p.brand)) +
                  (x.line.variant ? " &middot; " + UI.esc(x.line.variant) : "") + "</span>" +
              "</span>" +
            "</div>" +
          "</td>" +
          '<td data-label="Unit Price">' + UI.money(p.price) + "</td>" +
          '<td data-label="Quantity">' +
            '<div class="qty">' +
              '<button type="button" data-qty="down" aria-label="Decrease quantity">' + UI.icon("minus") + "</button>" +
              '<input type="number" min="1" value="' + x.line.qty + '" data-line="' + i + '" aria-label="Quantity">' +
              '<button type="button" data-qty="up" aria-label="Increase quantity">' + UI.icon("plus") + "</button>" +
            "</div>" +
          "</td>" +
          '<td data-label="Subtotal"><strong>' + UI.money(x.total) + "</strong></td>" +
          '<td data-label="">' +
            '<button class="icon-btn icon-btn--sm" type="button" data-remove="' + i + '" aria-label="Remove item">' +
              UI.icon("trash") + "</button>" +
          "</td>" +
        "</tr>";
      }).join("");

      var sub = SEE_STORE.cartSubtotal();
      setText("#sumSubtotal", UI.money(sub));
      setText("#sumTotal", UI.money(sub));
      syncCounts();
    }

    body.addEventListener("change", function (e) {
      var input = e.target;
      if (!input.matches || !input.matches("[data-line]")) { return; }
      SEE_STORE.setQty(parseInt(input.getAttribute("data-line"), 10), parseInt(input.value, 10) || 1);
      render();
    });
    body.addEventListener("click", function (e) {
      var r = closest(e.target, "[data-remove]");
      if (!r) { return; }
      e.preventDefault();
      SEE_STORE.removeLine(parseInt(r.getAttribute("data-remove"), 10));
      toast("Item removed from cart", "trash");
      render();
    });
    on($("#clearCart"), "click", function (e) {
      e.preventDefault();
      SEE_STORE.clearCart();
      toast("Cart cleared", "trash");
      render();
    });
    on($("#couponForm"), "submit", function (e) {
      e.preventDefault();
      toast("Coupon handling will be configured in WooCommerce.", "info");
    });

    render();
  };

  /* ---------- CHECKOUT ---------- */
  PAGES.checkout = function () {
    var wrap = $("#checkoutItems");
    if (!wrap) { return; }
    var lines = SEE_STORE.cartLines();

    if (!lines.length) {
      var host = $("#checkoutMain");
      if (host) {
        host.innerHTML = UI.renderEmpty("cart", "Your cart is empty",
          "Add products to your cart before proceeding to checkout.", "shop.html", "Browse the Shop");
      }
      var aside = $("#checkoutAside");
      if (aside) { aside.hidden = true; }
      return;
    }

    wrap.innerHTML = lines.map(function (x) {
      return '<div class="mini-item">' +
        '<span class="mini-item__img">' + thumb(x.product) + "</span>" +
        "<span>" +
          '<span class="mini-item__name">' + UI.esc(x.product.name) + "</span>" +
          '<span class="mini-item__qty">Qty ' + x.line.qty +
            (x.line.variant ? " &middot; " + UI.esc(x.line.variant) : "") + "</span>" +
        "</span>" +
        '<span class="mini-item__price">' + UI.money(x.total) + "</span>" +
      "</div>";
    }).join("");

    var sub = SEE_STORE.cartSubtotal();
    setText("#coSubtotal", UI.money(sub));
    setText("#coTotal", UI.money(sub));
  };

  /* ---------- MY ACCOUNT ---------- */
  PAGES.account = function () {
    /* Logged-out / logged-in preview toggle — design states only,
       there is no authentication in this prototype. */
    var loggedOut = $("#authView");
    var dash = $("#dashView");

    function show(which) {
      if (loggedOut) { loggedOut.hidden = which !== "auth"; }
      if (dash) { dash.hidden = which !== "dash"; }
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
    on($("#previewDash"), "click", function (e) { e.preventDefault(); show("dash"); });
    on($("#previewAuth"), "click", function (e) { e.preventDefault(); show("auth"); });
    $$("[data-logout]").forEach(function (b) {
      b.addEventListener("click", function (e) {
        e.preventDefault();
        show("auth");
        toast("Signed out (design preview only)", "logout");
      });
    });

    /* Dashboard sub-navigation */
    $$("[data-acct-tab]").forEach(function (b) {
      b.addEventListener("click", function (e) {
        e.preventDefault();
        var key = b.getAttribute("data-acct-tab");
        $$("[data-acct-tab]").forEach(function (o) {
          o.setAttribute("aria-current", o === b ? "true" : "false");
        });
        $$("[data-acct-panel]").forEach(function (pnl) {
          pnl.hidden = pnl.getAttribute("data-acct-panel") !== key;
        });
      });
    });

    /* Account wishlist mirrors the wishlist page */
    var aw = $("#acctWishlist");
    if (aw) {
      var list = SEE_STORE.wishlist();
      aw.innerHTML = list.length
        ? UI.renderProductGrid(list.slice(0, 4))
        : "";
      var e2 = $("#acctWishlistEmpty");
      if (e2) { e2.hidden = list.length > 0; }
    }
  };

  /* ---------- TRACK ORDER ---------- */
  PAGES.track = function () {
    var form = $("#trackForm");
    var result = $("#trackResult");
    if (!form || !result) { return; }
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      result.hidden = false;
      result.scrollIntoView({ behavior: "smooth", block: "start" });
      toast("Order tracking is not connected yet — please contact the store.", "info");
    });
  };

  /* ---------- FAQ ---------- */
  PAGES.faq = function () {
    var bar = $("#faqCats");
    if (!bar) { return; }
    bar.addEventListener("click", function (e) {
      var b = closest(e.target, "[data-faq-cat]");
      if (!b) { return; }
      var key = b.getAttribute("data-faq-cat");
      $$("[data-faq-cat]", bar).forEach(function (o) {
        o.setAttribute("aria-pressed", o === b ? "true" : "false");
      });
      $$("[data-faq-group]").forEach(function (g) {
        g.hidden = !(key === "all" || g.getAttribute("data-faq-group") === key);
      });
    });
  };

  /* ---------- QUOTE REQUEST ----------
     Prefills the requirement box when the visitor arrives from a product or
     from a product family. Only what the source itself publishes is written
     in: name, model, SKU or family name, plus the source page. */
  PAGES.quote = function () {
    var box = $("#qItems");
    if (!box) { return; }
    var pid = param("product");
    var fid = param("family");
    var lines = [];

    if (pid) {
      var p = D.productById(pid);
      if (p) {
        lines.push(p.name);
        if (p.brand) { lines.push("Brand: " + D.brandName(p.brand)); }
        if (p.model) { lines.push("Model: " + p.model); }
        if (p.sku) { lines.push("SKU: " + p.sku); }
      }
    } else if (fid) {
      var f = D.familyById(fid);
      if (f) {
        lines.push(f.name + " (product range)");
        if (f.brand) { lines.push("Brand: " + f.brand); }
        if (f.series) { lines.push("Series: " + f.series); }
        lines.push("The source publishes this as a range rather than individual " +
                   "products, so please tell us the rating, size or model you need.");
      }
    }

    if (lines.length && !box.value.trim()) {
      box.value = lines.join("\n") + "\n\nQuantity required: ";
      box.focus();
      try { box.setSelectionRange(box.value.length, box.value.length); } catch (e) { /* ignore */ }
    }
  };

  /* ======================================================================
     6. BOOT
     ====================================================================== */
  function init() {
    mountShell();
    initDrawer();
    initSearch();
    initQuickView();
    initProductActions();
    initCollapsibles();
    initTabs();
    initQty();
    initScroll();
    initForms();

    var page = document.body.getAttribute("data-page");
    var map = {
      home: PAGES.home, shop: PAGES.shop, category: PAGES.category,
      product: PAGES.product, search: PAGES.search, deals: PAGES.deals,
      brands: PAGES.brands, wishlist: PAGES.wishlist, cart: PAGES.cart,
      quote: PAGES.quote,
      checkout: PAGES.checkout, account: PAGES.account, track: PAGES.track,
      faq: PAGES.faq
    };
    if (map[page]) { map[page](); }

    /* Any page may drop in a category grid — fill it if the page
       controller has not already done so. */
    var cg = $("#categoryGrid");
    if (cg && !cg.children.length) {
      cg.innerHTML = D.CATEGORIES.map(UI.renderCategoryCard).join("");
    }
    var sc = $("#suggestedCats");
    if (sc && !sc.children.length) {
      sc.innerHTML = D.CATEGORIES.slice(0, 8).map(UI.renderCategoryCard).join("");
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
