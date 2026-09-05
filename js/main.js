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
  function on(el, ev, fn) { if (el) { el.addEventListener(ev, fn); } }
  function closest(t, sel) { return t && t.closest ? t.closest(sel) : null; }

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

    /* Seed a small demo cart the first time so the cart, checkout and
       account screens can be reviewed in a populated state. */
    if (!mem.seeded) {
      mem.seeded = true;
      mem.cart = [
        { id: "SEE-1001", qty: 2, variant: "1.5mm" },
        { id: "SEE-3001", qty: 4, variant: "32A" },
        { id: "SEE-4001", qty: 6, variant: "12W" }
      ];
      mem.wishlist = ["SEE-5001", "SEE-4004"];
      write();
    }

    function cartLines() {
      return mem.cart.map(function (l) {
        var p = D.productById(l.id);
        return p ? { line: l, product: p, total: p.price * l.qty } : null;
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
        return p.name.toLowerCase().indexOf(term) !== -1 ||
               D.categoryName(p.cat).toLowerCase().indexOf(term) !== -1 ||
               D.brandName(p.brand).toLowerCase().indexOf(term) !== -1;
      }).slice(0, 6);

      var cats = D.CATEGORIES.filter(function (c) {
        return c.name.toLowerCase().indexOf(term) !== -1;
      }).slice(0, 3);

      var html = "";
      if (hits.length) {
        html += '<p class="suggest__head">Products</p>';
        html += hits.map(function (p) {
          return '<a class="suggest__item" href="product.html?id=' + encodeURIComponent(p.id) + '">' +
                   '<img src="' + D.imageUrl(p.img) + '" alt="" aria-hidden="true">' +
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
        html = '<p class="suggest__empty">No demo products match &ldquo;' + UI.esc(q) +
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
      var off = UI.discount(p);
      var st = UI.STOCK[p.stock];
      var href = "product.html?id=" + encodeURIComponent(p.id);

      $("#qvBody").innerHTML =
        '<div class="modal__grid">' +
          '<div class="modal__media"><img src="' + D.imageUrl(p.img) + '" alt="' + UI.esc(p.name) + '" decoding="async"></div>' +
          '<div class="modal__body">' +
            '<p class="pcard__meta"><span class="pcard__brand">' + UI.esc(D.brandName(p.brand)) + "</span>" +
              '<span class="pcard__cat">' + UI.esc(D.categoryName(p.cat)) + "</span></p>" +
            "<h2 id=\"qvTitle\" style=\"font-size:21px\">" + UI.esc(p.name) + "</h2>" +
            '<div class="rating">' + UI.stars(p.rating) + '<span class="rating__count">(no reviews yet)</span></div>' +
            '<p class="price price--lg"><span class="price__now">' + UI.money(p.price) + "</span>" +
              (p.oldPrice ? '<span class="price__old">' + UI.money(p.oldPrice) + "</span>" : "") +
              (off ? '<span class="price__off">Save ' + off + "%</span>" : "") + "</p>" +
            '<span class="pill ' + st.cls + '">' + st.label + "</span>" +
            "<p class=\"t-sm t-muted\">" + UI.esc(p.short || "") + "</p>" +
            '<div class="btn-row" style="margin-top:8px">' +
              (p.stock === "out"
                ? '<button class="btn btn--accent" type="button" disabled>Out of Stock</button>'
                : '<button class="btn btn--accent js-add" type="button" data-id="' + UI.esc(p.id) + '">Add to Cart</button>') +
              '<a class="btn btn--ghost" href="' + href + '">Full Details</a>' +
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

  /* ---------- HOME ---------- */
  PAGES.home = function () {
    var cg = $("#categoryGrid");
    if (cg) { cg.innerHTML = D.CATEGORIES.map(UI.renderCategoryCard).join(""); }

    var bg = $("#brandGrid");
    if (bg) { bg.innerHTML = D.BRANDS.slice(0, 12).map(UI.renderBrandCard).join(""); }

    fill("#featuredGrid", D.byTag("featured", 8));
    fill("#bestGrid", D.byTag("best", 8));
    fill("#newGrid", D.byTag("new", 8));
    fill("#dealsGrid", D.byTag("deal", 4));

    /* Hero campaign rotator */
    var slider = $("#heroSlider");
    if (slider) {
      var slides = $$(".hero__slide", slider);
      var dots = $$("#heroDots button");
      var i = 0, timer = null;
      function go(n) {
        i = (n + slides.length) % slides.length;
        slides.forEach(function (s, k) { s.classList.toggle("is-active", k === i); });
        dots.forEach(function (d, k) { d.setAttribute("aria-selected", k === i ? "true" : "false"); });
      }
      dots.forEach(function (d, k) {
        d.addEventListener("click", function () { go(k); restart(); });
      });
      function restart() {
        window.clearInterval(timer);
        timer = window.setInterval(function () { go(i + 1); }, 6000);
      }
      go(0); restart();
      slider.addEventListener("mouseenter", function () { window.clearInterval(timer); });
      slider.addEventListener("mouseleave", restart);
    }
  };

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
      brands: opts.brand ? [opts.brand] : [],
      stock: [], rating: 0, type: [],
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
      if (state.onlyDeals && !p.oldPrice) { return false; }
      if (state.cats.length && state.cats.indexOf(p.cat) === -1) { return false; }
      if (state.subs.length && state.subs.indexOf(p.sub) === -1) { return false; }
      if (state.brands.length && state.brands.indexOf(p.brand) === -1) { return false; }
      if (state.stock.length && state.stock.indexOf(p.stock) === -1) { return false; }
      if (state.type.length && state.type.indexOf(p.type) === -1) { return false; }
      if (state.rating && p.rating < state.rating) { return false; }
      if (state.min !== null && p.price < state.min) { return false; }
      if (state.max !== null && p.price > state.max) { return false; }
      if (state.query) {
        var q = state.query.toLowerCase();
        var hay = (p.name + " " + D.categoryName(p.cat) + " " + D.brandName(p.brand) + " " + (p.short || "")).toLowerCase();
        if (hay.indexOf(q) === -1) { return false; }
      }
      return true;
    }

    function sortList(list) {
      var l = list.slice();
      if (state.sort === "price-asc") { l.sort(function (a, b) { return a.price - b.price; }); }
      else if (state.sort === "price-desc") { l.sort(function (a, b) { return b.price - a.price; }); }
      else if (state.sort === "name") { l.sort(function (a, b) { return a.name.localeCompare(b.name); }); }
      else if (state.sort === "rating") { l.sort(function (a, b) { return b.rating - a.rating; }); }
      else if (state.sort === "discount") {
        l.sort(function (a, b) { return UI.discount(b) - UI.discount(a); });
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
          var html = "";
          html += state.page > 1
            ? '<a href="#" data-page="' + (state.page - 1) + '">Prev</a>'
            : '<span class="is-gap">Prev</span>';
          for (var n = 1; n <= pages; n++) {
            html += n === state.page
              ? '<span class="is-current">' + n + "</span>"
              : '<a href="#" data-page="' + n + '">' + n + "</a>";
          }
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
      if (state.rating) { chips.push({ t: "rating", v: state.rating, label: state.rating + "★ & up" }); }
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
      } else if (kind === "rating") {
        state.rating = el.checked ? parseFloat(val) : 0;
        $$('[data-filter="rating"]').forEach(function (o) { if (o !== el) { o.checked = false; } });
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
        if (t === "rating") { state.rating = 0; }
        if (t === "price") { state.min = null; state.max = null; }
        $$("[data-filter]").forEach(function (o) {
          if (o.type === "checkbox" && o.value === v) { o.checked = false; }
          if (o.getAttribute("data-filter") === "rating") { o.checked = false; }
          if (t === "price" && (o.getAttribute("data-filter") === "min" || o.getAttribute("data-filter") === "max")) { o.value = ""; }
        });
        state.page = 1;
        render();
        return;
      }

      if (closest(e.target, "[data-clear-all]")) {
        e.preventDefault();
        state.cats = []; state.brands = []; state.stock = []; state.type = [];
        state.rating = 0; state.min = null; state.max = null; state.page = 1;
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
      '<p class="field__hint" style="margin-top:8px">Prices in PKR. Demo values only.</p>';

    var stock = ["in", "low", "out"].map(function (s) {
      return '<label class="filter-opt"><input type="checkbox" data-filter="stock" value="' + s + '">' +
             "<span>" + UI.STOCK[s].label + '</span><span class="count">' +
             D.PRODUCTS.filter(function (p) { return p.stock === s; }).length + "</span></label>";
    }).join("");

    var rating = [4, 3].map(function (r) {
      return '<label class="filter-opt"><input type="checkbox" data-filter="rating" value="' + r + '">' +
             "<span>" + UI.stars(r) + " &amp; up</span></label>";
    }).join("");

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
           group("rating", "Rating", rating, false) +
           group("type", "Product type", types, false);
  }

  function buildFilterUI() {
    $$("[data-filters]").forEach(function (el, i) {
      el.innerHTML = filterPanelHTML("f" + i);
    });
  }

  /* ---------- CATEGORY ---------- */
  PAGES.category = function () {
    var slug = param("cat") || D.CATEGORIES[0].slug;
    var sub = param("sub");
    var cat = D.categoryBySlug(slug) || D.CATEGORIES[0];

    var bc = $("#breadcrumb");
    if (bc) {
      var items = [{ label: "Home", href: "index.html" }, { label: "Shop", href: "shop.html" }];
      if (sub) {
        items.push({ label: cat.name, href: "category.html?cat=" + cat.slug });
        var s = cat.subs.filter(function (x) { return x.slug === sub; })[0];
        items.push({ label: s ? s.name : sub });
      } else {
        items.push({ label: cat.name });
      }
      bc.innerHTML = UI.renderBreadcrumb(items);
    }

    var t = $("#catTitle"); if (t) { t.textContent = cat.name; }
    var b = $("#catBlurb"); if (b) { b.textContent = cat.blurb; }
    var art = $("#catArt");
    if (art) { art.src = D.categoryImage(cat.slug); art.alt = cat.name + " products"; }
    document.title = cat.name + " — Star Electric Enterprises";

    var sg = $("#subcatGrid");
    if (sg) { sg.innerHTML = cat.subs.map(function (s) { return UI.renderSubcatCard(cat.slug, s); }).join(""); }

    buildFilterUI();
    var c = catalog({ cat: slug, sub: sub });
    $$('[data-filter="cat"][value="' + slug + '"]').forEach(function (o) { o.checked = true; });
    if (c) { c.render(); }

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
    var id = param("id") || D.PRODUCTS[0].id;
    var p = D.productById(id) || D.PRODUCTS[0];
    var off = UI.discount(p);
    var st = UI.STOCK[p.stock];
    var cat = D.categoryBySlug(p.cat);

    document.title = p.name + " — Star Electric Enterprises";
    var md = $("#metaDesc");
    if (md) { md.setAttribute("content", p.short || p.name); }

    var bc = $("#breadcrumb");
    if (bc) {
      bc.innerHTML = UI.renderBreadcrumb([
        { label: "Home", href: "index.html" },
        { label: "Shop", href: "shop.html" },
        { label: cat ? cat.name : "Products", href: "category.html?cat=" + p.cat },
        { label: p.name }
      ]);
    }

    /* Gallery */
    var main = $("#galleryMain");
    if (main) { main.src = D.imageUrl(p.gallery[0]); main.alt = p.name; }
    var thumbs = $("#galleryThumbs");
    if (thumbs) {
      thumbs.innerHTML = p.gallery.map(function (g, i) {
        return '<button class="gallery__thumb" type="button" role="tab" aria-selected="' + (i === 0) + '" ' +
               'data-src="' + D.imageUrl(g) + '" aria-label="View image ' + (i + 1) + '">' +
               '<img src="' + D.imageUrl(g) + '" alt="" loading="lazy" aria-hidden="true"></button>';
      }).join("");
      thumbs.addEventListener("click", function (e) {
        var b = closest(e.target, ".gallery__thumb");
        if (!b) { return; }
        $$(".gallery__thumb", thumbs).forEach(function (o) { o.setAttribute("aria-selected", "false"); });
        b.setAttribute("aria-selected", "true");
        if (main) { main.src = b.getAttribute("data-src"); }
      });
    }

    var badges = $("#galleryBadges");
    if (badges) {
      badges.innerHTML = (off ? '<span class="tag tag--sale">-' + off + "%</span>" : "") +
                         (p.tags.indexOf("new") !== -1 ? '<span class="tag tag--new">New</span>' : "");
    }

    setText("#pdpBrand", D.brandName(p.brand));
    setText("#pdpTitle", p.name);
    setText("#pdpSku", p.id);
    setText("#pdpCat", cat ? cat.name : "");
    setHTML("#pdpCat2", cat
      ? '<a class="link-inline" href="category.html?cat=' + cat.slug + '">' +
        UI.esc(cat.name) + "</a>"
      : "");
    setHTML("#pdpRating", UI.stars(p.rating, "stars--lg") + '<span class="rating__count">(no reviews yet)</span>');
    setHTML("#pdpPrice",
      '<span class="price__now">' + UI.money(p.price) + "</span>" +
      (p.oldPrice ? '<span class="price__old">' + UI.money(p.oldPrice) + "</span>" : "") +
      (off ? '<span class="price__off">Save ' + off + "%</span>" : ""));
    var save = $("#pdpSave");
    if (save) {
      save.textContent = p.oldPrice ? "You save " + UI.money(p.oldPrice - p.price) : "";
      save.hidden = !p.oldPrice;
    }
    var stockEl = $("#pdpStock");
    if (stockEl) { stockEl.className = "pill " + st.cls; stockEl.textContent = st.label; }
    setText("#pdpShort", p.short || "");
    setHTML("#pdpBullets", (p.bullets || []).map(function (b) { return "<li>" + UI.esc(b) + "</li>"; }).join(""));

    /* Variations */
    var varWrap = $("#pdpVariations");
    var chosen = "";
    if (varWrap) {
      if (p.type === "variable" && p.attr) {
        chosen = p.attr.options[0];
        varWrap.innerHTML =
          '<div class="variation">' +
            '<p class="variation__label">' + UI.esc(p.attr.label) + ': <span id="varChosen">' + UI.esc(chosen) + "</span></p>" +
            '<div class="swatches" id="varSwatches">' +
              p.attr.options.map(function (o, i) {
                return '<button class="swatch" type="button" aria-pressed="' + (i === 0) + '" data-val="' + UI.esc(o) + '">' +
                       UI.esc(o) + "</button>";
              }).join("") +
            "</div>" +
          "</div>";
        varWrap.addEventListener("click", function (e) {
          var s = closest(e.target, ".swatch");
          if (!s) { return; }
          $$(".swatch", varWrap).forEach(function (o) { o.setAttribute("aria-pressed", "false"); });
          s.setAttribute("aria-pressed", "true");
          chosen = s.getAttribute("data-val");
          setText("#varChosen", chosen);
          var addBtn = $("#pdpAdd");
          if (addBtn) { addBtn.setAttribute("data-variant", chosen); }
        });
      } else {
        varWrap.hidden = true;
      }
    }

    var addBtn = $("#pdpAdd");
    if (addBtn) {
      addBtn.setAttribute("data-id", p.id);
      addBtn.setAttribute("data-variant", chosen);
      addBtn.setAttribute("data-qty-from", "#pdpQty");
      if (p.stock === "out") { addBtn.disabled = true; addBtn.textContent = "Out of Stock"; }
    }
    var wishBtn = $("#pdpWish");
    if (wishBtn) {
      wishBtn.setAttribute("data-id", p.id);
      if (SEE_STORE.inWishlist(p.id)) { wishBtn.classList.add("is-active"); }
    }
    var buyNow = $("#pdpBuy");
    if (buyNow) {
      buyNow.addEventListener("click", function (e) {
        e.preventDefault();
        if (p.stock === "out") { return; }
        var q = parseInt(($("#pdpQty") || {}).value, 10) || 1;
        SEE_STORE.add(p.id, q, chosen);
        syncCounts();
        window.location.href = "checkout.html";
      });
    }

    /* Specifications table */
    var specs = $("#pdpSpecs");
    if (specs && p.specs) {
      specs.innerHTML = Object.keys(p.specs).map(function (k) {
        return "<tr><th scope=\"row\">" + UI.esc(k) + "</th><td>" + UI.esc(p.specs[k]) + "</td></tr>";
      }).join("");
    }

    /* Related + recently viewed */
    var related = D.PRODUCTS.filter(function (x) { return x.cat === p.cat && x.id !== p.id; }).slice(0, 4);
    if (related.length < 4) {
      related = related.concat(D.PRODUCTS.filter(function (x) {
        return x.cat !== p.cat && x.id !== p.id;
      }).slice(0, 4 - related.length));
    }
    fill("#relatedGrid", related);

    var seen = [];
    try {
      seen = JSON.parse(window.sessionStorage.getItem("see_seen") || "[]");
    } catch (e) { seen = []; }
    var recent = seen.filter(function (x) { return x !== p.id; })
                     .map(function (x) { return D.productById(x); })
                     .filter(Boolean).slice(0, 4);
    var rv = $("#recentSection");
    if (recent.length && rv) { rv.hidden = false; fill("#recentGrid", recent); }
    seen.unshift(p.id);
    try {
      window.sessionStorage.setItem("see_seen", JSON.stringify(seen.slice(0, 8)));
    } catch (e) { /* ignore */ }
  };

  function setText(sel, v) { var el = $(sel); if (el) { el.textContent = v; } }
  function setHTML(sel, v) { var el = $(sel); if (el) { el.innerHTML = v; } }

  /* ---------- BRANDS ---------- */
  PAGES.brands = function () {
    var grid = $("#brandDirectory");
    if (!grid) { return; }
    var letter = "";
    var query = "";

    function render() {
      var list = D.BRANDS.filter(function (b) {
        if (letter && b.name.replace(/^Placeholder\s+Brand\s+/i, "").charAt(0).toUpperCase() !== letter) { return false; }
        if (query && b.name.toLowerCase().indexOf(query.toLowerCase()) === -1) { return false; }
        return true;
      });
      grid.innerHTML = list.length ? list.map(UI.renderBrandCard).join("") : "";
      var empty = $("#brandEmpty");
      if (empty) { empty.hidden = list.length > 0; }
      var cnt = $("#brandCount");
      if (cnt) { cnt.textContent = list.length + " brand placeholders"; }
    }

    var az = $("#azBar");
    if (az) {
      var letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
      var available = {};
      D.BRANDS.forEach(function (b) {
        available[b.name.replace(/^Placeholder\s+Brand\s+/i, "").charAt(0).toUpperCase()] = true;
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
            '<span class="cart-item__img"><img src="' + D.imageUrl(p.img) + '" alt="' + UI.esc(p.name) + '" loading="lazy"></span>' +
            "<span>" +
              '<span class="cart-item__name"><a href="product.html?id=' + encodeURIComponent(p.id) + '">' +
                UI.esc(p.name) + "</a></span>" +
              '<span class="cart-item__var">' + UI.esc(D.brandName(p.brand)) + "</span>" +
            "</span>" +
          "</div>" +
        "</td>" +
        '<td data-label="Price"><span class="price__now">' + UI.money(p.price) + "</span></td>" +
        '<td data-label="Stock"><span class="pill ' + st.cls + '">' + st.label + "</span></td>" +
        '<td data-label="Actions">' +
          '<div class="btn-row">' +
            (p.stock === "out"
              ? '<button class="btn btn--ghost btn--sm" type="button" disabled>Out of Stock</button>'
              : '<button class="btn btn--accent btn--sm js-add" type="button" data-id="' + UI.esc(p.id) + '">Add to Cart</button>') +
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
              '<span class="cart-item__img"><img src="' + D.imageUrl(p.img) + '" alt="' + UI.esc(p.name) + '" loading="lazy"></span>' +
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
        '<span class="mini-item__img"><img src="' + D.imageUrl(x.product.img) + '" alt="' + UI.esc(x.product.name) + '" loading="lazy"></span>' +
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
      toast("Demo tracking result shown — not connected to real orders.", "info");
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
