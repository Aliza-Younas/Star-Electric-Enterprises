"""Build the theme and plugin ZIPs for upload through wp-admin.

The destination is reached through the WordPress dashboard only - no SSH, no
SFTP - so everything has to arrive as an uploadable archive:

    Appearance -> Themes  -> Add New -> Upload Theme
    Plugins    -> Add New -> Upload Plugin

The catalogue payload is copied into the plugin so the importer has its data the
moment the plugin activates. It compresses to a fraction of a megabyte, which
keeps the upload well inside any plausible upload_max_filesize.

    python wordpress/tools/build-packages.py [outdir]

Writes star-electric-child.zip and star-electric-core.zip, then prints the sizes
that decide whether the upload will be accepted.
"""

import os
import sys
import zipfile

HERE = os.path.dirname(os.path.abspath(__file__))
WORDPRESS = os.path.dirname(HERE)
CONTENT = os.path.join(WORDPRESS, "wp-content")
PAYLOAD = os.path.join(WORDPRESS, "data")

PAYLOAD_FILES = ("taxonomies.json", "media.json", "ranges.json", "products.ndjson")

# Editor droppings and OS clutter must never reach a production server.
SKIP_NAMES = {".DS_Store", "Thumbs.db", "desktop.ini"}
SKIP_EXT = {".pyc", ".log", ".orig", ".rej", ".swp"}
SKIP_DIRS = {"__pycache__", ".git", "node_modules"}


def keep(name):
    if name in SKIP_NAMES:
        return False
    return os.path.splitext(name)[1].lower() not in SKIP_EXT


def add_tree(zf, root, arc_root, extra=()):
    """Add a directory to the archive under arc_root, returning the file count."""
    count = 0
    for dirpath, dirnames, filenames in os.walk(root):
        dirnames[:] = sorted(d for d in dirnames if d not in SKIP_DIRS)
        for filename in sorted(filenames):
            if not keep(filename):
                continue
            full = os.path.join(dirpath, filename)
            rel = os.path.relpath(full, root).replace(os.sep, "/")
            zf.write(full, arc_root + "/" + rel)
            count += 1
    for src, arcname in extra:
        zf.write(src, arc_root + "/" + arcname)
        count += 1
    return count


def build(outdir, slug, source, extra=()):
    path = os.path.join(outdir, slug + ".zip")
    with zipfile.ZipFile(path, "w", zipfile.ZIP_DEFLATED, compresslevel=9) as zf:
        count = add_tree(zf, source, slug, extra)
    size = os.path.getsize(path)
    print("  %-26s %4d files  %7.2f MB  ->  %s" % (slug + ".zip", count, size / 1048576, path))
    return path, size


def main():
    outdir = sys.argv[1] if len(sys.argv) > 1 else os.path.join(WORDPRESS, "dist")
    os.makedirs(outdir, exist_ok=True)

    missing = [f for f in PAYLOAD_FILES if not os.path.exists(os.path.join(PAYLOAD, f))]
    if missing:
        print("Payload missing: %s" % ", ".join(missing))
        print("Run wordpress/tools/export-catalogue.py first.")
        return 1

    print("Building upload packages into %s\n" % outdir)

    build(outdir, "star-electric-child", os.path.join(CONTENT, "themes", "star-electric-child"))

    # The payload rides inside the plugin: the importer reads it from its own
    # data/ directory, so activating the plugin is all the deployment there is.
    payload = [(os.path.join(PAYLOAD, f), "data/" + f) for f in PAYLOAD_FILES]
    _, plugin_size = build(
        outdir,
        "star-electric-core",
        os.path.join(CONTENT, "plugins", "star-electric-core"),
        payload,
    )

    raw = sum(os.path.getsize(os.path.join(PAYLOAD, f)) for f in PAYLOAD_FILES)
    print("\n  payload %.2f MB raw, carried inside the plugin ZIP" % (raw / 1048576))

    # 2 MB is the smallest upload limit seen in the wild on shared PHP hosting.
    if plugin_size > 2 * 1048576:
        print("  WARNING: plugin ZIP exceeds 2 MB - check upload_max_filesize.")
    else:
        print("  Plugin ZIP is under 2 MB, so any plausible upload limit accepts it.")

    return 0


if __name__ == "__main__":
    sys.exit(main())
