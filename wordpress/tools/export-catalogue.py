"""
Export the approved catalogue into the payload the WordPress importer reads.

The importer must be idempotent and resumable, so everything it needs is
resolved here rather than at import time:

  taxonomies.json  categories (with subcategories), brands, departments
  media.json       every unique image master, once, with its provenance
  ranges.json      the family/range records - navigation entities, never products
  products.ndjson  one verified product per line, ready to become a WC product

Media is deduplicated deliberately: 4,348 products resolve to 2,044 unique
files because Pakistan Cables shares range photography across sizes. The
importer uploads each file once and reuses the attachment ID.
"""
import collections
import glob
import io
import json
import os

PROJ = r"D:\Star electric enterprises"
OUT = os.path.join(PROJ, "wordpress", "data")


def load(rel):
    return json.load(io.open(os.path.join(PROJ, rel), encoding="utf-8"))


def main():
    os.makedirs(OUT, exist_ok=True)
    idx = load("data/catalogue-index.json")
    fams = load("data/families.json")["families"]
    products = idx["products"]

    # full detail records, keyed by product id, for specs and source provenance
    detail = {}
    for f in sorted(glob.glob(os.path.join(PROJ, "data", "products", "*.json"))):
        for r in json.load(io.open(f, encoding="utf-8")):
            detail[r["id"]] = r

    # ---------------------------------------------------------------- media
    media = {}

    def note(path, rec):
        if not path or path in media:
            return
        src = (rec or {}).get("source", {})
        media[path] = {
            "file": path,
            "source_url": src.get("url", ""),
            "source_domain": src.get("domain", ""),
            "image_type": (rec or {}).get("imageType") or "",
            "image_note": (rec or {}).get("imageNote") or "",
        }

    for p in products:
        rec = detail.get(p["id"])
        note(p.get("img"), rec)
        # The gallery shots as well as the primary image. Exporting only the
        # primary one left 632 photographs behind: the approved product page
        # shows them, and WordPress had nothing to show.
        for extra in ((rec or {}).get("images") or {}).get("gallery") or []:
            note(extra, rec)
    for f in fams:
        note(f.get("img"), None)
    for c in idx["categories"]:
        note("assets/images/categories/%s.webp" % c["slug"], None)

    # ----------------------------------------------------------- taxonomies
    depts = collections.Counter()
    for p in products:
        for d in (p.get("depts") or []):
            depts[d] += 1
    tax = {
        "categories": idx["categories"],
        "brands": idx["brands"],
        "departments": [{"slug": k, "count": v} for k, v in sorted(depts.items())],
    }

    # -------------------------------------------------------------- ranges
    ranges = [{
        "source_id": f["id"], "slug": f["slug"], "name": f["name"],
        "brand": f["brand"], "category": f["category"], "subcategory": f["subcategory"],
        "series": f["series"], "kind": f["kind"], "reason": f["reason"],
        "summary": f["summary"], "image": f.get("img") or "",
        "image_type": f.get("imgType") or "",
        "departments": f.get("depts") or [],
        "source_url": f["sourceUrl"], "source_domain": f["sourceDomain"],
    } for f in fams]

    # ------------------------------------------------------------ products
    lines = []
    for p in products:
        d = detail.get(p["id"], {})
        src = d.get("source", {})
        quote = p["priceType"] == "quote" or p["price"] is None
        lines.append(json.dumps({
            "source_id": p["id"],
            "slug": p["slug"],
            "name": p["name"],
            "sku": p.get("sku") or "",
            "model": p.get("model") or "",
            "brand": p.get("brand") or "",
            "category": p.get("category") or "",
            "subcategory": p.get("subcategory") or "",
            "series": p.get("series") or "",
            "departments": p.get("depts") or [],
            # Pricing. A quote-only product carries no price at all - never 0.
            "quote_only": quote,
            "regular_price": None if quote else (p.get("regularPrice") or p.get("price")),
            "sale_price": (p.get("price") if (not quote and p.get("regularPrice")
                                              and p.get("discountPercent")) else None),
            "availability": p.get("availability") or "",
            "type": "variable" if p.get("hasVariations") else "simple",
            "variations": d.get("variations") or [],
            "specifications": d.get("specifications") or {},
            "features": d.get("features") or [],
            "short_description": d.get("shortDescription") or "",
            "image": p.get("img") or "",
            "gallery": (d.get("images") or {}).get("gallery") or [],
            "image_type": d.get("imageType") or "",
            "image_note": d.get("imageNote") or "",
            "source_url": src.get("url", ""),
            "source_domain": src.get("domain", ""),
            "source_checked": (src.get("checkedAt") or "")[:10],
            "import_notes": d.get("importNotes") or [],
        }, ensure_ascii=False))

    def write(name, obj):
        io.open(os.path.join(OUT, name), "w", encoding="utf-8").write(
            json.dumps(obj, ensure_ascii=False, indent=1))
        return os.path.getsize(os.path.join(OUT, name))

    s1 = write("taxonomies.json", tax)
    s2 = write("media.json", sorted(media.values(), key=lambda m: m["file"]))
    s3 = write("ranges.json", ranges)
    io.open(os.path.join(OUT, "products.ndjson"), "w", encoding="utf-8").write(
        "\n".join(lines) + "\n")
    s4 = os.path.getsize(os.path.join(OUT, "products.ndjson"))

    quote_n = sum(1 for l in lines if '"quote_only": true' in l)
    print("  taxonomies.json  %7d bytes  %d categories, %d brands, %d departments"
          % (s1, len(tax["categories"]), len(tax["brands"]), len(tax["departments"])))
    print("  media.json       %7d bytes  %d unique masters" % (s2, len(media)))
    print("  ranges.json      %7d bytes  %d range records" % (s3, len(ranges)))
    print("  products.ndjson  %7d bytes  %d products (%d quote-only, %d priced)"
          % (s4, len(lines), quote_n, len(lines) - quote_n))
    missing = [m["file"] for m in media.values()
               if not os.path.exists(os.path.join(PROJ, m["file"].replace("/", os.sep)))]
    print("  media files missing on disk:", len(missing))
    for m in missing[:5]:
        print("     ", m)


if __name__ == "__main__":
    main()
