# Image Completion Audit

Record of the pass that closed the remaining image gaps in the catalogue.
Completed 2026-09-07.

## Before and after

| Measure | Before | After |
|---|---:|---:|
| Verified individual products | 4348 | 4348 |
| Products with an image | 1,929 | 4348 |
| Products with **no** image | 2,415 | 0 |
| Families / series with an image | 0 | 181 |

## Image types across the catalogue

| Type | Products |
|---|---:|
| `exact-image` | 1948 |
| `manufacturer-image` | 2382 |
| `representative-image` | 18 |
| **Total** | **4348** |

## By source

| Source | Products | exact | manufacturer | representative | without an image |
|---|---:|---:|---:|---:|---:|
| Aqua Electrical | 1640 | 1624 | 0 | 16 | 0 |
| Coarts Lighting | 222 | 220 | 0 | 2 | 0 |
| Pakistan Cables | 2382 | 0 | 2382 | 0 | 0 |
| Wahid Fans | 104 | 104 | 0 | 0 | 0 |

## What was done, source by source

**Pakistan Cables (2,378 products).** The source publishes no photograph against any
individual cable size, and its own online store photographs only the printed coil labels,
not the cable, so those were rejected. Pakistan Cables does publish a product catalogue per
range, each with its own photograph of that range, and those four photographs now carry the
cable catalogue: General Wiring for single-core wiring cable, Low Voltage Cables for
multi-core power cable, Medium Voltage Cables for everything above 1 kV, and Solar Cables
for the XLPO / XLHFFR photovoltaic range. Each product page says plainly that this is the
manufacturer's range photograph rather than a photograph of that exact size.

**Aqua Electrical.** 35 products had an image URL that the source answers with a zero-byte
file. For 19 of them the WordPress-generated sizes of the same photograph are intact and
were used. The remaining 16 have no usable photograph at source and now carry a
correct-product-type representative image, labelled as such.

**Coarts Lighting.** Two products had no photograph at all. The highbay luminaire and the
smart door lock now carry representative product-type images, labelled as such.

**Himel (132 series).** The product-detail pages are still blocked by the source's
protection and were not accessed. The accessible category pages publish an official image
for each range, and those now illustrate all 132 series entries.

**Hyundai / Jubilee (8 families).** Seven families have a photograph on the approved source
page and now use it. The eighth, Industrial Inverters, has none there, and Hyundai
Electric's own site serves no product image without scripting, so that entry uses a
representative variable-frequency drive photograph.

**Electro Traders (32 ranges).** Each range page carries its own product photograph; all 32
now use it.

**Wahid Fans.** Already complete - every fan had its own source photography.

**Pakistan Cables visual normalisation (2026-09-07).** The four manufacturer range
photographs were shot on Pakistan Cables' own dark green studio backdrop, so on a
white product card they read as a green rectangle while every other source read as a
clean white stage. All four were re-read from the JPEG embedded in the catalogue PDF
rather than the earlier page raster, the backdrop was separated by its own colour and
replaced with white, and the result was centred on an 800px square to match the Aqua
masters. The cables themselves - sheath, printed text, armour, conductors, colours,
proportions - are carried through untouched. The two ranges the manufacturer does not
photograph at all, Coaxial Cables and Indoor Telephone / Intercom Cables, now carry
representative product-type photographs, labelled as such.

**ABB Furse (3 ranges).** ABB publishes Furse as a solution overview with no item-level
data and no image the site can reach: the Furse page is rendered client-side and its
og:image is empty, neither the global nor the UK low-voltage product sitemap contains an
earthing, lightning or surge protection product, and the ABB Library needs signed
credentials, which were not circumvented. All three ranges therefore use representative
product-type photographs - an earth rod, a lightning rod air termination, and for surge
protection an ABB OVR T1 surge protective device. The surge image is ABB's own brand
rather than a competitor's, is not an exact Furse part, and says so on the range entry.

## Categories

Six of the nine category tiles were rebuilt. Industrial Control was a low-quality collage,
Power & Energy a busy amateur photograph and Switches & Sockets a dull outlet shot; Smart
Home, Fans & Ventilation and Wires & Cables were upgraded to stronger imagery. They now use
photographs of products this shop actually lists, or the manufacturer's own range
photography, presented on a consistent white card at a single aspect ratio. Lighting,
Circuit Protection and Electrical Accessories kept their existing photographs, which were
already strong.

## Limitations

- Pakistan Cables does not photograph individual cable sizes. Its 2,378 products therefore
  share four manufacturer range photographs, and the product page says so on every one.
- 18 products and five family entries use a representative product-type photograph
  because neither their source nor the manufacturer publishes a usable image. Every one is
  labelled "Representative image" on its product page.
- No openly licensed, unbranded photograph of a low-voltage surge protective device
  exists: every clean one found carries a rival manufacturer's logo. ABB's own OVR device
  was used for the Furse surge protection range for that reason.
- Pakistan Cables photographs neither its Coaxial Cables nor its Indoor Telephone /
  Intercom Cables range in any of its seven published catalogues, so both use a
  representative product-type photograph from Wikimedia Commons, labelled as such.
- Himel's individual product pages remain blocked. The range images used are official Himel
  images from its accessible category pages, not per-model photographs.
- Brand logos remain monogram placeholders. Using manufacturers' logos would imply a
  dealership or distribution relationship that has not been established.
