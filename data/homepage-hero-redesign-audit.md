# Homepage Hero Redesign — Audit

Redesign of the homepage campaign banner, the first thing a visitor sees below
the site header. The global header, search, account area and main navigation
were not touched.

Completed 2026-09-08.

---

## 1. References studied

| Site | What was taken from it |
|---|---|
| [Powerhouse Express](https://powerhouseexpress.com.pk/) | The strongest single idea: a **campaign navigator built into the bottom edge of the banner card** — labelled tabs with an active state, not floating dots underneath. Also: a coloured field behind the product rather than a pale one, a compact banner, and three small promo tiles below. |
| [Electric Market PK](https://electricmarket.pk/) | A **dark navy field makes light products pop**, and a **layered multi-product cluster** reads as "this shop has a real catalogue". Their execution is cluttered and leans on discount claims we cannot make, so the density was taken and the noise was not. |
| [Aqua Electrical](https://aquaelectrical.com/) | A manufacturer brand banner — abstract artwork and display type. Useful as a counter-example: a retailer's hero must merchandise products, not run a brand film. |
| Pakistan Cables, Coarts, Himel, Wahid, ABB Electrification | Studied for product presentation during the earlier imagery work in this repository. Confirmed the house style the hero should match: a clean stage, a real product, no lifestyle scene. |

Nothing was copied — no markup, CSS, artwork, campaign graphic or wording. What
was taken are design principles.

---

## 2. Audit of the previous hero

Measured at 1456px: banner **984 × 575px**, of which the products occupied a
circle roughly 250px across containing three images at 60–110px each.

| Criterion | Score /5 | Finding |
|---|---:|---|
| First impression | 1 | Reads as a slide deck, not a shop. |
| Ecommerce credibility | 2 | Nothing signals "serious electrical supplier". |
| Visual hierarchy | 2 | Headline, copy and CTA all sit at similar weight on the same pale field. No depth anywhere. |
| Product emphasis | 1 | **The single worst problem.** Three products totalling maybe 4% of the banner area. |
| Composition | 1 | Text left, one enormous white circle right. The circle was decoration occupying the space the products should have had. |
| Image scale | 1 | Products drawn from the 480px department discs, whose subject is scaled to 80% and then shown small — a double reduction. |
| Image quality | 3 | The masters are fine; they were being used far below their intended size. |
| Colour balance | 1 | A single pale `#F5F5FA`-family wash across the whole banner. Almost no navy, almost no red. |
| Contrast | 1 | Navy text on pale lavender. Products dropped in with `mix-blend-mode: multiply`, which washed them out further. |
| CTA hierarchy | 3 | Primary/secondary were correctly differentiated, but sat in dead space. |
| Information density | 1 | ~150px of empty band above the eyebrow and ~120px below the buttons. |
| Whitespace | 1 | Not whitespace — unused space. Roughly half the banner carried nothing. |
| Typography | 3 | The type itself is fine; it was too small for the area it had to hold. |
| Navigation | 2 | The five category tabs floated at the bottom looking like a separate control strip, not part of the campaign. |
| Responsiveness | 3 | It reflowed without breaking, but the mobile hero was the desktop one scaled down. |
| Motion | 3 | Transform-based track, correct pause behaviour. Sound mechanics, no visible polish. |
| Accessibility | 4 | Roles, labels, live region and keyboard support were already in place and were kept. |
| Premium feel | 1 | None. |

**Root cause of the empty space.** `.hcom__grid` used `align-items: stretch`.
The department menu beside the banner is taller than the campaign, so it
stretched the banner column and the banner inherited a height it had no content
to fill. That is why the composition floated in the middle of a tall pale box.

Additional confirmed weaknesses, in the terms the brief used: enormous unused
space, excessively pale background, tiny product composition, a giant white
circle wasting the merchandising area, content disconnected from the products,
a PowerPoint feel, no commercial impact, detached slide navigation, flat
hierarchy, no depth, an ordinary CTA area, and a category strip that read as
separate UI.

---

## 3. Directions considered

**A — Premium split editorial.** Navy editorial panel, generous type, a single
hero product on a light stage. *Rejected:* one product per slide understates a
4,348-product catalogue, and it drifts toward a manufacturer brand page rather
than a shop.

**B — Dark technical showcase.** Whole banner in near-black navy, products lit
from above, technical overlays. *Rejected:* our catalogue is dominated by white
and light-grey products (MCBs, sockets, panels, fans). They read well on navy,
but the black glass smart switches and the dark floodlight disappear into it,
and five dark slides in a row would make the whole first viewport heavy against
the white product sections below.

**C — Layered campaign composition across an angled split. → SELECTED.**
A navy campaign panel and a light merchandising stage, divided by a leaning
edge rather than a straight line, with a composed cluster of two-to-three real
products **crossing the seam**.

**Why C.** It solves the specific problem this catalogue has: white products
need a dark ground to pop, dark products need a light one, and letting the
cluster straddle the boundary guarantees contrast on one edge whatever the
product colours are. It also does what the brief asked and the other two do
not — makes the two halves read as a single banner instead of two boxes. The
angle and the seam-crossing cluster are the signature; the rest is restraint.

---

## 4. Final design

**Geometry.** Banner 470px at desktop (406px campaign + 64px navigator), 440px
at 1024–1279, 470px stacked below 860px. Corner radius 12px (`--r-md`), matching
the rest of the site. The old 575px is gone, and the departments strip is now
visible in the first viewport at 1920 and reachable with a short scroll at 1366.

**Zones.** Copy column 46%, merchandising stage 58% — they overlap, which is
the point. The navy panel is 61% wide with its right edge clipped to lean
104px left as it descends.

**Background.** Navy panel: a 134° gradient `#2C2488 → #221B66 → #171149`, a
soft red radial bleeding up from the bottom-left corner, and a 38px blueprint
grid at 5.5% white, masked out before it reaches the seam. Light stage: a
radial from white to `#E5E8F2`. Three tones (`navy`, `deep`, `ink`) give each
campaign its own depth without changing the system.

**Typography.** Title `clamp(28px, 3.05vw, 46px)`, weight 800, line-height 1.07,
letter-spacing −0.02em. Body 14.5px/1.6 at 72% white, capped at 40ch. Eyebrow
11.5px, 700, 0.14em tracking, in a red tint (`#FF8585`) that clears contrast on
navy, preceded by a 22px red rule. Mobile title 28px, 25px below 400px.

**Colour split.** Roughly 70% navy foundation, 20% light stage, under 10% red —
and the red is confined to the eyebrow rule, the primary button and the active
progress line.

**Product composition.** One pre-built transparent WebP per campaign rather than
three stacked `<img>` elements: it lets the products be scaled, overlapped and
given real grounding shadows, and it is one request instead of three. The
largest product occupies 58–70% of the composition height.

**Navigator.** A navy strip on the bottom edge of the banner: five numbered,
labelled entries, each with its own progress line. Active entry gets a white
label, a red number and a red line that fills over the 7s autoplay. Below 860px
it collapses to five progress segments — the campaign name is already the
eyebrow at the top of the slide.

**Controls.** A pair of 42px chips at the bottom-right of the campaign area,
10px radius, white on a soft shadow. They were first placed centred on the left
and right edges, where the left one sat on top of the body copy; the pair
placement removed that collision entirely.

---

## 5. Campaigns

| # | Campaign | Headline | Products in the composition |
|---|---|---|---|
| 01 | Circuit Protection | Built to Protect Every Circuit | Aqua 12-way distribution box, Aqua 4-pole 63A RCCB, Aqua 3-pole 63A MCB |
| 02 | Wires & Cables | Power Every Connection | Pakistan Cables solar pair, LV armoured cable, general wiring fan |
| 03 | Switches & Sockets | Control, Beautifully Finished | Aqua Xtreme grey 2-switch/2-socket, Glass Wi-Fi 4-gang, Xtreme black multi-function socket |
| 04 | Lighting & Fixtures | Light Designed Around Your Space | Aqua 32W square surface panel, Coarts Alpha downlight, Coarts Astro G2 floodlight |
| 05 | Fans & Smart | Smarter Control Starts Here | Wahid Crystal inverter fan, Aqua Glass Wi-Fi 8-gang, Wahid Ace 56" fan |

Each has its own arrangement, not one layout with the picture swapped. Every
product is a verified catalogue master; nothing was generated, recoloured,
re-badged or merged into a composite object. No slide claims a discount,
delivery term, warranty, dealership or ranking.

---

## 6. How the compositions were built

`hero_build.py` (session scratchpad) cuts each master off its white studio
background and stacks two or three of them on a 1200×860 transparent canvas
with soft elliptical contact shadows.

Two problems were found and fixed during the build:

1. **The cut-out ate white products.** A flood fill from the frame edge at a
   238 white threshold leaked through anti-aliased boundaries into the products'
   own white areas — the MCB body, a panel face and the floodlight's LED array
   came out full of holes. Raising the threshold to 252 and adding a
   morphological closing before the edge feather fixed it.
2. **A lifestyle photograph nearly shipped.** The first Lighting composition
   used an Alteco panel image that is actually an office ceiling scene. The
   builder now refuses any source where less than 20% of the frame is lifted as
   background, and that image was replaced.

---

## 7. Responsive strategy

| Width | Treatment |
|---|---|
| ≥1280 | Full split. Banner 470px, copy 46%, stage 58%, department menu beside it. |
| 1024–1279 | Same split, banner 440px, stage pulled back to 54% and the copy given a right gutter — without that the body copy ran under the product cluster. |
| 861–1023 | Department menu gives way to the departments strip below. Copy and stage rebalanced to 50/50 with the stage at 50%. |
| ≤860 | **A different hero, not a scaled one.** The angle becomes a horizontal sweep: copy on navy at the top, the product cluster standing on the light stage beneath it. Arrows hidden. |
| ≤480 | Secondary CTA hidden — two stacked buttons were eating the product zone, and the secondary destination is still in the header and the navigation. |

Verified at 1920, 1440, 1366, 1024, 768, 430, 390 and 375: **no horizontal
overflow and no broken images at any width.**

---

## 8. Motion and accessibility

- Track transition 580ms `cubic-bezier(.33,1,.68,1)`; copy rises 14px, the
  product drifts 26px.
- **Transform only, never opacity.** An entrance that animates opacity from 0
  leaves the campaign invisible if the animation stalls — which is exactly what
  happened during QA. Content is now visible by default and the motion is an
  enhancement on top.
- Autoplay 7s, paused on hover, on focus within the banner, and when the tab is
  hidden. The progress line is a CSS animation whose `animation-play-state`
  follows the timer, so what the visitor sees matches the clock.
- Reduced motion: no track transition, no entrance animation, no animated
  progress, and autoplay does not start.
- Kept from the previous build: carousel roles, per-slide labels, a polite live
  region announcing "Slide n of 5", arrow-key support, pointer swipe with
  horizontal-intent detection, and `tabindex="-1"` on off-screen slide controls.

---

## 9. Performance

| | |
|---|---:|
| Hero composition assets | 5 files, 399 KB total |
| LCP image (campaign 01) | 58 KB, `fetchpriority="high"` |
| Campaigns 02–05 | `fetchpriority="low"` |
| Requests per slide | 1 (was 3) |

`loading="lazy"` was tried for campaigns 02–05 and **reverted**: the track is
translated rather than scrolled, so an off-screen slide's lazy image is not
reliably fetched before that slide arrives, and the merchandising zone rendered
empty. Eager at low priority keeps them out of the LCP's way without that risk.

The 13 department disc images the old hero drew from are unchanged and still
used by the departments strip, so nothing was orphaned.

---

## 10. Before and after

**Before.** 984 × 575px. A pale lavender field, a 250px white circle, three
products at 60–110px, roughly half the banner empty, a flat hierarchy, and a
detached tab strip. It looked like a presentation slide.

**After.** 984 × 470px. An angled navy campaign panel against a light
merchandising stage, a layered cluster of real products crossing the seam at
roughly 8× the previous visual area, a red primary CTA against navy, and a
numbered campaign navigator built into the banner's bottom edge. 105px shorter,
so the departments strip and the first product rail come up sooner.

**Why it is stronger:** the products are the subject rather than a detail; there
is genuine depth and contrast instead of a single pale wash; the navigation
belongs to the campaign; the composition is specific to this catalogue rather
than a generic template; and the first viewport now reads as an electrical
supplier's shop rather than a slide.

**Kept deliberately:** the global header and navigation, the department menu
beside the banner (capped to 13 rows so the two columns finish together), the
three promo tiles, and the white-stage category carousel below.
