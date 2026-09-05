/* ==========================================================================
   STAR ELECTRIC ENTERPRISES — DEMO DATA (prototype only)
   --------------------------------------------------------------------------
   IMPORTANT
   Everything in this file is placeholder content created to demonstrate the
   layout. Product names, prices, ratings, stock levels and brand names are
   NOT real business data. Nothing here should be published as-is.

   On WordPress this whole file disappears: categories become product_cat
   terms, brands become a product_brand taxonomy, and PRODUCTS becomes the
   WooCommerce product loop.
   ========================================================================== */
window.SEE_DATA = (function () {
  "use strict";

  var IMG = "assets/images/products/";
  var CAT_IMG = "assets/images/categories/";

  /* Product types for which a real, openly-licensed photograph has been
     sourced (see assets/image-sources.md). Anything not listed here still
     uses the original line-art placeholder because no acceptable licensed
     photograph could be found - see the "gaps" section of image-sources.md. */
  var PHOTO = [
    "flex-cable", "flex-cable-alt", "cable-gland",
    "connector", "switch-plate", "socket", "mcb", "mcb-alt", "distribution-box",
    "fuse", "changeover", "relay", "led-bulb", "floodlight",
    "ceiling-fan", "exhaust-fan", "energy-meter", "smart-plug", "tester",
    "drill", "drill-alt", "tape", "tape-alt", "extension-board",
    "extension-board-alt"
  ];

  /* ----------------------------------------------------------------------
     CATEGORY TREE  →  WooCommerce product_cat
     ---------------------------------------------------------------------- */
  var CATEGORIES = [
    {
      slug: "circuit-protection", name: "Circuit Protection & Distribution",
      blurb: "Breakers, fuses, distribution boxes and changeover gear for safe power distribution.",
      icon: "mcb",
      subs: [
        { slug: "circuit-breakers", name: "Circuit Breakers", icon: "mcb" },
        { slug: "fuses", name: "Fuses", icon: "fuse" },
        { slug: "distribution-boxes", name: "Distribution Boxes", icon: "distribution-box" },
        { slug: "protection-relays", name: "Protection Relays", icon: "relay" },
        { slug: "changeover-switches", name: "Changeover Switches", icon: "changeover" }
      ]
    },
    {
      slug: "electrical-accessories", name: "Electrical Accessories",
      blurb: "Everyday consumables and fittings that complete an electrical installation.",
      icon: "tape",
      subs: [
        { slug: "insulation-tape", name: "Insulation Tape", icon: "tape" },
        { slug: "extension-boards", name: "Extension Boards", icon: "extension-board" },
        { slug: "junction-boxes", name: "Junction Boxes", icon: "distribution-box" },
        { slug: "connectors", name: "Connectors", icon: "connector" },
        { slug: "conduits", name: "Conduits", icon: "conduit" }
      ]
    },
    {
      slug: "fans-ventilation", name: "Fans & Ventilation",
      blurb: "Ceiling, bracket and exhaust fans for homes, offices and commercial spaces.",
      icon: "ceiling-fan",
      subs: [
        { slug: "ceiling-fans", name: "Ceiling Fans", icon: "ceiling-fan" },
        { slug: "exhaust-fans", name: "Exhaust Fans", icon: "exhaust-fan" },
        { slug: "bracket-fans", name: "Bracket Fans", icon: "ceiling-fan" },
        { slug: "fan-regulators", name: "Fan Regulators", icon: "switch-plate" }
      ]
    },
    {
      slug: "lighting-fixtures", name: "Lighting & Fixtures",
      blurb: "LED bulbs, panels, downlights and outdoor fixtures for every kind of space.",
      icon: "led-bulb",
      subs: [
        { slug: "led-bulbs", name: "LED Bulbs", icon: "led-bulb" },
        { slug: "panel-lights", name: "Panel Lights", icon: "panel-light" },
        { slug: "downlights", name: "Downlights", icon: "downlight" },
        { slug: "outdoor-lighting", name: "Outdoor Lighting", icon: "floodlight" },
        { slug: "smart-lighting", name: "Smart Lighting", icon: "smart-switch" }
      ]
    },
    {
      slug: "power-energy", name: "Power & Energy",
      blurb: "Stabilisers, meters and backup power equipment for reliable supply.",
      icon: "stabilizer",
      subs: [
        { slug: "voltage-stabilisers", name: "Voltage Stabilisers", icon: "stabilizer" },
        { slug: "energy-meters", name: "Energy Meters", icon: "energy-meter" },
        { slug: "ups-inverters", name: "UPS & Inverters", icon: "stabilizer" },
        { slug: "solar-accessories", name: "Solar Accessories", icon: "panel-light" }
      ]
    },
    {
      slug: "smart-home", name: "Smart Home Devices",
      blurb: "App and voice controlled switches, plugs and sensors for modern installations.",
      icon: "smart-switch",
      subs: [
        { slug: "smart-switches", name: "Smart Switches", icon: "smart-switch" },
        { slug: "smart-plugs", name: "Smart Plugs", icon: "smart-plug" },
        { slug: "smart-sensors", name: "Smart Sensors", icon: "relay" },
        { slug: "hubs-gateways", name: "Hubs & Gateways", icon: "distribution-box" }
      ]
    },
    {
      slug: "switches-sockets", name: "Switches & Sockets",
      blurb: "Modular plates, power sockets and industrial outlets in a range of finishes.",
      icon: "switch-plate",
      subs: [
        { slug: "modular-switches", name: "Modular Switches", icon: "switch-plate" },
        { slug: "power-sockets", name: "Power Sockets", icon: "socket" },
        { slug: "switch-plates", name: "Switch Plates", icon: "switch-plate" },
        { slug: "industrial-sockets", name: "Industrial Sockets", icon: "socket" },
        { slug: "dimmers", name: "Dimmers", icon: "switch-plate" }
      ]
    },
    {
      slug: "tools-accessories", name: "Tools & Accessories",
      blurb: "Testers, hand tools and power tools for electricians and site teams.",
      icon: "drill",
      subs: [
        { slug: "testers-meters", name: "Testers & Meters", icon: "tester" },
        { slug: "hand-tools", name: "Hand Tools", icon: "tester" },
        { slug: "power-tools", name: "Power Tools", icon: "drill" },
        { slug: "tool-kits", name: "Tool Kits", icon: "drill" }
      ]
    },
    {
      slug: "wiring-accessories", name: "Wiring & Accessories",
      blurb: "Copper cable, flexible cord, glands and conduit for wiring of every scale.",
      icon: "cable-coil",
      subs: [
        { slug: "electrical-cables", name: "Electrical Cables", icon: "cable-coil" },
        { slug: "cable-accessories", name: "Cable Accessories", icon: "flex-cable" },
        { slug: "cable-glands", name: "Cable Glands", icon: "cable-gland" },
        { slug: "conduits", name: "Conduits", icon: "conduit" },
        { slug: "connectors", name: "Connectors", icon: "connector" }
      ]
    }
  ];

  /* ----------------------------------------------------------------------
     BRANDS  →  future product_brand taxonomy
     Placeholder names only. Star Electric Enterprises has NOT stated any
     dealership, distribution or authorisation relationship, so none is
     implied here. Replace with confirmed brands before publication.
     ---------------------------------------------------------------------- */
  var BRANDS = [
    { slug: "brand-a", name: "Placeholder Brand A", mark: "PA" },
    { slug: "brand-b", name: "Placeholder Brand B", mark: "PB" },
    { slug: "brand-c", name: "Placeholder Brand C", mark: "PC" },
    { slug: "brand-d", name: "Placeholder Brand D", mark: "PD" },
    { slug: "brand-e", name: "Placeholder Brand E", mark: "PE" },
    { slug: "brand-f", name: "Placeholder Brand F", mark: "PF" },
    { slug: "brand-g", name: "Placeholder Brand G", mark: "PG" },
    { slug: "brand-h", name: "Placeholder Brand H", mark: "PH" },
    { slug: "brand-i", name: "Placeholder Brand I", mark: "PI" },
    { slug: "brand-j", name: "Placeholder Brand J", mark: "PJ" },
    { slug: "brand-k", name: "Placeholder Brand K", mark: "PK" },
    { slug: "brand-l", name: "Placeholder Brand L", mark: "PL" }
  ];

  /* ----------------------------------------------------------------------
     PRODUCTS  →  WooCommerce product loop
     price / oldPrice in PKR. rating + reviews are DEMO values (reviews are
     deliberately 0 so no fake review count is ever displayed).
     ---------------------------------------------------------------------- */
  function p(o) {
    o.reviews = 0;                       // never fabricate review counts
    o.gallery = o.gallery || [o.img];
    o.type = o.type || "simple";
    return o;
  }

  var PRODUCTS = [
    p({ id: "SEE-1001", slug: "single-core-copper-wire-1-5mm", name: "Single Core Copper Wire 1.5mm — 90m Coil",
        brand: "brand-a", cat: "wiring-accessories", sub: "electrical-cables", img: "cable-coil",
        gallery: ["cable-coil"],
        price: 12500, oldPrice: 14200, rating: 4.5, stock: "in", type: "variable",
        attr: { label: "Conductor Size", options: ["1.0mm", "1.5mm", "2.5mm", "4.0mm"] },
        short: "Solid copper conductor with PVC insulation for concealed and surface wiring.",
        bullets: ["Solid electrolytic copper conductor", "PVC insulated, flame retardant grade", "Supplied on a 90 metre coil"],
        specs: { "Conductor": "Solid copper", "Insulation": "PVC", "Coil Length": "90 m", "Sizes Available": "1.0 / 1.5 / 2.5 / 4.0 mm", "Country of Origin": "To be confirmed" },
        tags: ["featured", "best"] }),

    p({ id: "SEE-1002", slug: "three-core-flexible-cable-1mm", name: "3-Core Flexible Cable 1.0mm — 50m Roll",
        brand: "brand-b", cat: "wiring-accessories", sub: "electrical-cables", img: "flex-cable",
        gallery: ["flex-cable", "flex-cable-alt"],
        price: 9800, oldPrice: null, rating: 4, stock: "in",
        short: "Multi-strand flexible cable for appliances, extensions and portable equipment.",
        bullets: ["Fine stranded copper for flexibility", "Three core with earth", "50 metre roll"],
        specs: { "Cores": "3", "Conductor": "Stranded copper", "Roll Length": "50 m" },
        tags: ["featured", "new"] }),

    p({ id: "SEE-1003", slug: "pvc-conduit-pipe-20mm", name: "PVC Conduit Pipe 20mm — 3m Length",
        brand: "brand-c", cat: "wiring-accessories", sub: "conduits", img: "conduit",
        price: 240, oldPrice: null, rating: 4, stock: "in",
        short: "Rigid PVC conduit for protecting concealed wiring runs.",
        bullets: ["Rigid PVC, impact resistant", "20mm outer diameter", "3 metre standard length"],
        specs: { "Material": "PVC", "Diameter": "20 mm", "Length": "3 m" },
        tags: ["new"] }),

    p({ id: "SEE-1004", slug: "brass-cable-gland-set", name: "Brass Cable Gland 20mm — Pack of 10",
        brand: "brand-a", cat: "wiring-accessories", sub: "cable-glands", img: "cable-gland",
        price: 1850, oldPrice: 2150, rating: 4.5, stock: "in",
        short: "Brass compression glands for terminating armoured and unarmoured cable.",
        bullets: ["Nickel plated brass body", "Includes locknut and washer", "Pack of 10"],
        specs: { "Material": "Brass", "Size": "20 mm", "Pack": "10 pieces" },
        tags: ["deal"] }),

    p({ id: "SEE-1005", slug: "terminal-connector-block", name: "Terminal Connector Block 15A — Strip of 12",
        brand: "brand-d", cat: "wiring-accessories", sub: "connectors", img: "connector",
        price: 320, oldPrice: 420, rating: 4, stock: "in",
        short: "Screw terminal connector strip for junction and distribution work.",
        bullets: ["15A rated brass terminals", "Polyamide housing", "Snap-apart 12 way strip"],
        specs: { "Rating": "15 A", "Ways": "12", "Housing": "Polyamide" },
        tags: ["deal"] }),

    p({ id: "SEE-2001", slug: "modular-switch-plate-6-gang", name: "Modular Switch Plate — 6 Gang",
        brand: "brand-e", cat: "switches-sockets", sub: "modular-switches", img: "switch-plate",
        gallery: ["switch-plate"],
        price: 1450, oldPrice: 1750, rating: 4.5, stock: "in", type: "variable",
        attr: { label: "Gang Configuration", options: ["2 Gang", "4 Gang", "6 Gang", "8 Gang"] },
        short: "Modular front plate with smooth rocker action and a flush profile.",
        bullets: ["Polycarbonate front plate", "Silver alloy contacts", "Fits standard modular boxes"],
        specs: { "Gangs": "6", "Rating": "16 A", "Finish": "Matte white", "Mounting": "Flush box" },
        tags: ["featured", "deal"] }),

    p({ id: "SEE-2002", slug: "universal-power-socket-16a", name: "Universal Power Socket 16A with Shutter",
        brand: "brand-e", cat: "switches-sockets", sub: "power-sockets", img: "socket",
        price: 620, oldPrice: null, rating: 4, stock: "low",
        short: "Shuttered universal socket accepting round and flat pin plugs.",
        bullets: ["Child safety shutter", "16A rated", "Accepts multiple plug types"],
        specs: { "Rating": "16 A", "Shutter": "Yes", "Type": "Universal" },
        tags: ["featured", "best"] }),

    p({ id: "SEE-2003", slug: "industrial-socket-outlet", name: "Industrial Socket Outlet 32A — IP44",
        brand: "brand-f", cat: "switches-sockets", sub: "industrial-sockets", img: "socket",
        price: 3400, oldPrice: null, rating: 4, stock: "in",
        short: "Weather resistant industrial outlet for workshop and site power.",
        bullets: ["IP44 splash protection", "32A three pin", "Impact resistant enclosure"],
        specs: { "Rating": "32 A", "Protection": "IP44", "Poles": "3" },
        tags: [] }),

    p({ id: "SEE-3001", slug: "miniature-circuit-breaker-32a", name: "Miniature Circuit Breaker 32A Single Pole",
        brand: "brand-g", cat: "circuit-protection", sub: "circuit-breakers", img: "mcb",
        gallery: ["mcb", "mcb-alt"],
        price: 890, oldPrice: 1100, rating: 5, stock: "in", type: "variable",
        attr: { label: "Current Rating", options: ["6A", "10A", "16A", "20A", "32A", "40A"] },
        short: "Thermal magnetic MCB for overload and short circuit protection.",
        bullets: ["C-curve tripping characteristic", "DIN rail mounting", "Available 6A to 40A"],
        specs: { "Rating": "32 A", "Poles": "1", "Curve": "C", "Mounting": "35mm DIN rail" },
        tags: ["featured", "deal", "best"] }),

    p({ id: "SEE-3002", slug: "distribution-board-8-way", name: "Distribution Board — 8 Way Metal Enclosure",
        brand: "brand-g", cat: "circuit-protection", sub: "distribution-boxes", img: "distribution-box",
        price: 5400, oldPrice: null, rating: 4, stock: "in",
        short: "Powder coated metal consumer unit with DIN rail and neutral bar.",
        bullets: ["8 module capacity", "Powder coated steel", "Includes neutral and earth bars"],
        specs: { "Ways": "8", "Material": "Powder coated steel", "Mounting": "Surface" },
        tags: ["featured", "new"] }),

    p({ id: "SEE-3003", slug: "cartridge-fuse-link-63a", name: "Cartridge Fuse Link 63A — Pack of 3",
        brand: "brand-h", cat: "circuit-protection", sub: "fuses", img: "fuse",
        price: 1250, oldPrice: 1500, rating: 4, stock: "in",
        short: "HRC cartridge fuse links for distribution boards and panels.",
        bullets: ["High rupturing capacity", "Ceramic body", "Pack of 3"],
        specs: { "Rating": "63 A", "Type": "HRC cartridge", "Pack": "3 pieces" },
        tags: ["deal"] }),

    p({ id: "SEE-3004", slug: "manual-changeover-switch-63a", name: "Manual Changeover Switch 63A — 4 Pole",
        brand: "brand-f", cat: "circuit-protection", sub: "changeover-switches", img: "changeover",
        price: 7900, oldPrice: null, rating: 4.5, stock: "low",
        short: "Rotary changeover for switching between mains and backup supply.",
        bullets: ["4 pole rotary mechanism", "Lockable handle position", "Enclosed housing"],
        specs: { "Rating": "63 A", "Poles": "4", "Operation": "Manual rotary" },
        tags: [] }),

    p({ id: "SEE-3005", slug: "overload-protection-relay", name: "Overload Protection Relay 9–13A",
        brand: "brand-h", cat: "circuit-protection", sub: "protection-relays", img: "relay",
        price: 4200, oldPrice: null, rating: 4, stock: "in",
        short: "Adjustable thermal overload relay for motor starter assemblies.",
        bullets: ["Adjustable 9–13A range", "Manual and auto reset", "Contactor mounting"],
        specs: { "Range": "9–13 A", "Reset": "Manual / Auto", "Mounting": "Contactor" },
        tags: [] }),

    p({ id: "SEE-4001", slug: "led-bulb-12w-cool-white", name: "LED Bulb 12W Cool White — B22",
        brand: "brand-i", cat: "lighting-fixtures", sub: "led-bulbs", img: "led-bulb",
        gallery: ["led-bulb"],
        price: 480, oldPrice: 650, rating: 4.5, stock: "in", type: "variable",
        attr: { label: "Wattage", options: ["7W", "9W", "12W", "18W"] },
        short: "Energy efficient LED bulb with a wide beam and instant start.",
        bullets: ["12W output", "Cool white colour temperature", "B22 bayonet cap"],
        specs: { "Wattage": "12 W", "Cap": "B22", "Colour": "Cool white", "Voltage": "220–240 V" },
        tags: ["featured", "best", "deal"] }),

    p({ id: "SEE-4002", slug: "slim-led-panel-light-18w", name: "Slim LED Panel Light 18W Round",
        brand: "brand-i", cat: "lighting-fixtures", sub: "panel-lights", img: "panel-light",
        price: 1250, oldPrice: 1500, rating: 4, stock: "in",
        short: "Low profile recessed panel for false ceilings in homes and offices.",
        bullets: ["18W slim profile", "Even diffused output", "Includes driver"],
        specs: { "Wattage": "18 W", "Shape": "Round", "Mounting": "Recessed" },
        tags: ["featured", "new"] }),

    p({ id: "SEE-4003", slug: "cob-downlight-7w", name: "COB Downlight 7W — Adjustable",
        brand: "brand-j", cat: "lighting-fixtures", sub: "downlights", img: "downlight",
        price: 890, oldPrice: null, rating: 4, stock: "in",
        short: "Directional COB downlight for accent and task lighting.",
        bullets: ["Tilting gimbal housing", "7W COB module", "Aluminium heat sink"],
        specs: { "Wattage": "7 W", "Beam": "Adjustable", "Body": "Aluminium" },
        tags: ["new"] }),

    p({ id: "SEE-4004", slug: "led-floodlight-50w", name: "LED Floodlight 50W — Outdoor IP65",
        brand: "brand-j", cat: "lighting-fixtures", sub: "outdoor-lighting", img: "floodlight",
        price: 3600, oldPrice: 4200, rating: 4.5, stock: "in",
        short: "Weatherproof floodlight for yards, gates and commercial frontage.",
        bullets: ["IP65 weather rating", "Toughened glass front", "Adjustable mounting bracket"],
        specs: { "Wattage": "50 W", "Protection": "IP65", "Body": "Die cast aluminium" },
        tags: ["deal", "best"] }),

    p({ id: "SEE-5001", slug: "ceiling-fan-56-inch", name: "Ceiling Fan 56 inch — Copper Winding",
        brand: "brand-k", cat: "fans-ventilation", sub: "ceiling-fans", img: "ceiling-fan",
        gallery: ["ceiling-fan"],
        price: 14900, oldPrice: 16800, rating: 4.5, stock: "in", type: "variable",
        attr: { label: "Sweep Size", options: ["48 inch", "56 inch", "60 inch"] },
        short: "Full size ceiling fan with copper winding and a balanced blade set.",
        bullets: ["100% copper winding", "56 inch sweep", "Powder coated finish"],
        specs: { "Sweep": "56 inch", "Winding": "Copper", "Blades": "3", "Voltage": "220–240 V" },
        tags: ["best", "deal"] }),

    p({ id: "SEE-5002", slug: "exhaust-fan-10-inch", name: "Exhaust Fan 10 inch — Plastic Body",
        brand: "brand-k", cat: "fans-ventilation", sub: "exhaust-fans", img: "exhaust-fan",
        price: 3200, oldPrice: null, rating: 4, stock: "in",
        short: "Wall or window mounted exhaust fan for kitchens and washrooms.",
        bullets: ["10 inch blade", "Lightweight ABS body", "Easy clean front grill"],
        specs: { "Size": "10 inch", "Body": "ABS plastic", "Mounting": "Wall / window" },
        tags: ["best"] }),

    p({ id: "SEE-6001", slug: "automatic-voltage-stabiliser-1000va", name: "Automatic Voltage Stabiliser 1000VA",
        brand: "brand-l", cat: "power-energy", sub: "voltage-stabilisers", img: "stabilizer",
        price: 11500, oldPrice: null, rating: 4.5, stock: "in",
        short: "Automatic stabiliser protecting appliances from supply fluctuation.",
        bullets: ["1000VA capacity", "Automatic voltage correction", "Overload cut-off"],
        specs: { "Capacity": "1000 VA", "Input Range": "To be confirmed", "Protection": "Overload cut-off" },
        tags: ["best"] }),

    p({ id: "SEE-6002", slug: "digital-energy-meter-single-phase", name: "Digital Energy Meter — Single Phase",
        brand: "brand-l", cat: "power-energy", sub: "energy-meters", img: "energy-meter",
        price: 4700, oldPrice: 5300, rating: 4, stock: "low",
        short: "Single phase sub-meter with a digital consumption display.",
        bullets: ["Digital kWh display", "DIN rail mounting", "Single phase two wire"],
        specs: { "Phase": "Single", "Display": "Digital LCD", "Mounting": "DIN rail" },
        tags: ["best", "new"] }),

    p({ id: "SEE-7001", slug: "smart-wifi-switch-2-gang", name: "Smart Wi-Fi Switch — 2 Gang Touch",
        brand: "brand-c", cat: "smart-home", sub: "smart-switches", img: "smart-switch",
        gallery: ["smart-switch"],
        price: 4300, oldPrice: 4900, rating: 4.5, stock: "in",
        short: "Touch panel switch with app control and scheduling support.",
        bullets: ["Glass touch panel", "Wi-Fi app control", "Works with common voice assistants"],
        specs: { "Gangs": "2", "Connectivity": "Wi-Fi 2.4 GHz", "Panel": "Tempered glass" },
        tags: ["new", "deal"] }),

    p({ id: "SEE-7002", slug: "smart-plug-16a", name: "Smart Plug 16A with Energy Monitoring",
        brand: "brand-c", cat: "smart-home", sub: "smart-plugs", img: "smart-plug",
        price: 2600, oldPrice: null, rating: 4, stock: "in",
        short: "Plug-in smart socket with scheduling and consumption tracking.",
        bullets: ["16A rated", "Energy monitoring", "Timer and schedule support"],
        specs: { "Rating": "16 A", "Connectivity": "Wi-Fi 2.4 GHz", "Monitoring": "Yes" },
        tags: ["new"] }),

    p({ id: "SEE-8001", slug: "non-contact-voltage-tester", name: "Non-Contact Voltage Tester Pen",
        brand: "brand-d", cat: "tools-accessories", sub: "testers-meters", img: "tester",
        price: 780, oldPrice: 950, rating: 4.5, stock: "in",
        short: "Pocket tester that detects live conductors without contact.",
        bullets: ["Audible and visual alert", "Pocket clip", "Battery included"],
        specs: { "Detection": "Non-contact", "Alert": "Buzzer + LED", "Battery": "AAA" },
        tags: ["new", "deal"] }),

    p({ id: "SEE-8002", slug: "cordless-drill-12v", name: "Cordless Drill Machine 12V with Case",
        brand: "brand-b", cat: "tools-accessories", sub: "power-tools", img: "drill",
        gallery: ["drill", "drill-alt"],
        price: 8900, oldPrice: null, rating: 4, stock: "out",
        short: "Compact cordless drill driver with a carry case and bit set.",
        bullets: ["12V lithium battery", "Variable speed trigger", "Carry case included"],
        specs: { "Voltage": "12 V", "Chuck": "10 mm", "Includes": "Case and bits" },
        tags: ["new"] }),

    p({ id: "SEE-9001", slug: "pvc-insulation-tape-pack", name: "PVC Insulation Tape — Pack of 10",
        brand: "brand-a", cat: "electrical-accessories", sub: "insulation-tape", img: "tape",
        gallery: ["tape", "tape-alt"],
        price: 350, oldPrice: 450, rating: 4, stock: "in",
        short: "Flame retardant PVC tape for joint insulation and identification.",
        bullets: ["Flame retardant grade", "Strong adhesive", "Pack of 10 rolls"],
        specs: { "Material": "PVC", "Pack": "10 rolls", "Width": "18 mm" },
        tags: ["deal", "best"] }),

    p({ id: "SEE-9002", slug: "extension-board-4-socket", name: "Extension Board 4 Socket with Switch",
        brand: "brand-e", cat: "electrical-accessories", sub: "extension-boards", img: "extension-board",
        gallery: ["extension-board", "extension-board-alt"],
        price: 1100, oldPrice: 1350, rating: 4, stock: "in",
        short: "Four way extension board with individual switch and indicator.",
        bullets: ["4 universal sockets", "Master switch with indicator", "Flexible lead"],
        specs: { "Sockets": "4", "Switch": "Master", "Lead Length": "To be confirmed" },
        tags: ["best", "deal"] })
  ];

  /* ----------------------------------------------------------------------
     Helpers used by every page
     ---------------------------------------------------------------------- */
  function categoryBySlug(slug) {
    for (var i = 0; i < CATEGORIES.length; i++) {
      if (CATEGORIES[i].slug === slug) { return CATEGORIES[i]; }
    }
    return null;
  }
  function brandBySlug(slug) {
    for (var i = 0; i < BRANDS.length; i++) {
      if (BRANDS[i].slug === slug) { return BRANDS[i]; }
    }
    return null;
  }
  function brandName(slug) {
    var b = brandBySlug(slug);
    return b ? b.name : "";
  }
  function categoryName(slug) {
    var c = categoryBySlug(slug);
    return c ? c.name : "";
  }
  function productById(id) {
    for (var i = 0; i < PRODUCTS.length; i++) {
      if (PRODUCTS[i].id === id) { return PRODUCTS[i]; }
    }
    return null;
  }
  function byTag(tag, limit) {
    var out = PRODUCTS.filter(function (x) { return x.tags.indexOf(tag) !== -1; });
    return limit ? out.slice(0, limit) : out;
  }
  function imageUrl(key) {
    return IMG + key + (PHOTO.indexOf(key) !== -1 ? ".webp" : ".svg");
  }
  function categoryImage(slug) { return CAT_IMG + slug + ".webp"; }
  function hasPhoto(key) { return PHOTO.indexOf(key) !== -1; }

  return {
    IMG: IMG,
    CATEGORIES: CATEGORIES,
    BRANDS: BRANDS,
    PRODUCTS: PRODUCTS,
    categoryBySlug: categoryBySlug,
    categoryName: categoryName,
    brandBySlug: brandBySlug,
    brandName: brandName,
    productById: productById,
    byTag: byTag,
    imageUrl: imageUrl,
    categoryImage: categoryImage,
    hasPhoto: hasPhoto
  };
})();
