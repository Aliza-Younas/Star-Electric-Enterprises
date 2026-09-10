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

import importlib
import os
import sys
import zipfile

HERE = os.path.dirname(os.path.abspath(__file__))

# The checker is a sibling script whose name is hyphenated like the rest of the
# tooling, so it has to be imported by name rather than with a plain import.
sys.path.insert(0, HERE)
php_syntax_check = importlib.import_module("php-syntax-check")

WORDPRESS = os.path.dirname(HERE)
CONTENT = os.path.join(WORDPRESS, "wp-content")
PAYLOAD = os.path.join(WORDPRESS, "data")

PAYLOAD_FILES = ("taxonomies.json", "media.json", "ranges.json", "products.ndjson")

# Editor droppings and OS clutter must never reach a production server.
SKIP_NAMES = {".DS_Store", "Thumbs.db", "desktop.ini"}
SKIP_EXT = {".pyc", ".log", ".orig", ".rej", ".swp"}
SKIP_DIRS = {"__pycache__", ".git", "node_modules"}

# The plugin's own data/ directory is where the payload is staged locally. It is
# added to the archive explicitly below, so walking it as well would put every
# file in twice.
SKIP_TREE = {os.path.normpath(os.path.join(CONTENT, "plugins", "star-electric-core", "data"))}

# The payload must never be readable over HTTP. Until these existed, the whole
# 5 MB products file could be downloaded by anyone who guessed the path. They
# live here rather than in the plugin's own data/ directory, which is not
# versioned - a guard that only exists on one machine is not a guard.
GUARDS = {
    "data/.htaccess": os.path.join(HERE, "payload-deny.htaccess"),
    "data/index.php": os.path.join(HERE, "payload-index.php"),
}


def keep(name):
    if name in SKIP_NAMES:
        return False
    return os.path.splitext(name)[1].lower() not in SKIP_EXT


def add_tree(zf, root, arc_root, extra=()):
    """Add a directory to the archive under arc_root, returning the file count."""
    count = 0
    for dirpath, dirnames, filenames in os.walk(root):
        dirnames[:] = sorted(
            d for d in dirnames
            if d not in SKIP_DIRS
            and os.path.normpath(os.path.join(dirpath, d)) not in SKIP_TREE
        )
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


def build(outdir, slug, source, extra=(), text=()):
    path = os.path.join(outdir, slug + ".zip")
    with zipfile.ZipFile(path, "w", zipfile.ZIP_DEFLATED, compresslevel=9) as zf:
        count = add_tree(zf, source, slug, extra)
        for arcname, source_file in text:
            zf.write(source_file, slug + "/" + arcname)
            count += 1
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

    # A PHP parse error inside the plugin takes every wp-admin page down, and
    # wp-admin is the only way in - so a broken file cannot be replaced by the
    # same route that shipped it. Never package one.
    print("Checking PHP syntax\n")
    if php_syntax_check.run([CONTENT]):
        print("\nPHP problems found. Nothing packaged.")
        return 1

    print("\nBuilding upload packages into %s\n" % outdir)

    build(outdir, "star-electric-child", os.path.join(CONTENT, "themes", "star-electric-child"))

    # The payload rides inside the plugin: the importer reads it from its own
    # data/ directory, so activating the plugin is all the deployment there is.
    payload = [(os.path.join(PAYLOAD, f), "data/" + f) for f in PAYLOAD_FILES]

    _, plugin_size = build(
        outdir,
        "star-electric-core",
        os.path.join(CONTENT, "plugins", "star-electric-core"),
        payload,
        sorted(GUARDS.items()),
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
