# CLAUDE.md — Star Electric Enterprises

Permanent working instructions for Claude Code on this project.
Read this before starting any task in this repository.

---

## 1. Project

| | |
|---|---|
| **Business** | Star Electric Enterprises — electrical supplies store, Saddar, Rawalpindi, Pakistan |
| **Local working copy** | `D:\Star electric enterprises` |
| **GitHub** | https://github.com/Aliza-Younas/Star-Electric-Enterprises |
| **Remote** | `origin` → `https://github.com/Aliza-Younas/Star-Electric-Enterprises.git` |
| **Branch** | `main` |
| **Stack** | HTML5, CSS3, vanilla JavaScript. No framework, no build step. |
| **Future platform** | WordPress + WooCommerce (not yet — see §10) |

### Working copy rules

- `D:\Star electric enterprises` is the **only** working copy. Edit files there directly.
- Never create a second copy of the project.
- Never clone the repository into another directory unless explicitly asked.
- Never work from a temporary duplicate.
- Never run `git init` in a subfolder. There must be exactly one `.git`, at the project root.

Before Git operations, confirm `origin` still points at the repository above
(`git remote -v`). If it does not, stop and report rather than changing it silently.

---

## 2. The automatic workflow

For **every** website task, run this end to end without being asked to commit or push.
The user describes the change; everything below is your responsibility.

```
LOCAL FILES  →  VALIDATE  →  git status / git diff  →  COMMIT
             →  SAFE REMOTE CHECK  →  PUSH origin main  →  VERIFY CLEAN
```

1. **Understand** the requested change.
2. **Inspect** the relevant existing files before editing them.
3. **Edit** the local files in `D:\Star electric enterprises`.
4. **Preserve** existing working functionality unless the task requires changing it.
5. **Complete** the whole request — not the easy part of it.
6. **Validate** (§3).
7. Run `git status`.
8. Review `git diff`.
9. Confirm only intended changes are staged.
10. Confirm no secrets are being committed (§5).
11. Check the remote safely: `git fetch origin`, then compare. If `origin/main` is
    ahead and local work is already committed, integrate with
    `git pull --rebase origin main`.
12. **Commit** with a meaningful message (§4).
13. **Push**: `git push origin main`.
14. **Verify**: `git status` clean and up to date, `git log --oneline -1`.
15. **Report** in the format in §7.

Never ask "should I commit?", "should I push?" or "do you want me to sync?".
For a normal completed task, just do it.

**Do not push broken or half-finished work.** Editing files is not a reason to
push. If something is obviously wrong or incomplete, fix it, validate again,
then commit.

### Three live states, not one

Pushing to GitHub is step five of nine, not the finish line. The project exists
in three places, and a change is only done when every place it applies to is in
the intended state:

| | |
|---|---|
| **1. Source** | `https://github.com/Aliza-Younas/Star-Electric-Enterprises` |
| **2. WordPress** | `https://salmon-antelope-713580.hostingersite.com/` |
| **3. GitHub Pages** | `https://aliza-younas.github.io/Star-Electric-Enterprises/` |

So the workflow continues past step 15:

16. **Deploy** to WordPress — see the runbook in `wordpress/MIGRATION.md`, which
    covers reaching the origin server. A fix that is only committed is not live.
17. **Apply the frontend equivalent** to the static site, where one exists.
18. **Verify both live sites**, not the local files and not a cached copy.
    Purge LiteSpeed after any WordPress change that alters markup, CSS or JS,
    and confirm GitHub Pages is serving the new commit before claiming parity.

**Which changes go where.** PHP, WooCommerce, Elementor documents, the database,
AJAX handlers, REST security and anything in `wordpress/` can only run on
WordPress; GitHub Pages cannot execute them. Everything a visitor can see or
touch — design, spacing, typography, colour, imagery, header and footer, product
cards, responsive rules, front-end JavaScript — belongs on both, because the
static site is the approved visual reference and the two must not drift.

A WordPress-only fix still requires **checking** the static site rather than
assuming: the question is not "does this file exist there" but "does the visitor
now see or get something different there". On 2026-09-11 catalogue search by SKU
was dead on WordPress and working on GitHub Pages, and the static site was the
one that was right — the drift was only visible because both were tested.

State it per change, and never claim a state that was not tested:

```
BUG:            <description>
REPOSITORY:     PASS / FAIL   commit <hash>
WORDPRESS LIVE: PASS / FAIL   <url tested>
GITHUB PAGES:   PASS / FAIL / NOT APPLICABLE (verified, not assumed)  <url tested>
PARITY:         PASS / FAIL
```

Nothing is "fixed" until every applicable row passes.

---

## 3. Validation

Validate before committing. Match the depth to the task — a copy tweak does not
need a full responsive sweep, a layout change does.

Check whichever apply:

- HTML structure — unclosed tags, duplicate `id`s, `<label for>` targets
- CSS — brace balance, no undefined `var(--…)`
- JavaScript — no console errors, brackets balanced
- Broken links — every internal `href` resolves to a real page
- Asset paths — every `src` resolves; no broken images
- Responsive layout at **1920, 1440, 1366, 1024, 768, 430, 390, 375**
- No horizontal overflow (`documentElement.scrollWidth === clientWidth`)
- Navigation, active nav state, mobile drawer
- Forms render correctly (they are visual only — nothing submits)
- Product card consistency across grids
- Header/footer consistency across pages
- Accessibility — alt text, focus states, contrast, ARIA where used
- Obvious visual regressions

### How to actually run these

Chrome is available for headless rendering:

```
"C:\Program Files\Google\Chrome\Application\chrome.exe" --headless=new --disable-gpu \
  --no-sandbox --hide-scrollbars --virtual-time-budget=20000 \
  --window-size=1456,1200 --screenshot=out.png \
  "file:///D:/Star%20electric%20enterprises/index.html"
```

Environment gotchas worth knowing:

- **Chrome clamps `--window-size` to ~500px minimum.** To test 375/390/430,
  render the page inside an `<iframe>` of that exact width in a wrapper page and
  screenshot/inspect that. `--window-size=W,H` gives a viewport of `W-16`.
- Header/footer are injected by JavaScript, so link and image checks must run
  against the **rendered DOM** (`--dump-dom`), not the raw HTML source.
- `loading="lazy"` images below the fold appear blank in headless screenshots.
  That is not a bug — force `loading="eager"` in a probe to verify they load.
- Python 3 is available; Pillow is **not**. Use Chrome's canvas for any image
  resizing/format conversion.

Put throwaway validation scripts and screenshots in the session scratchpad
directory, never in the repository. Debug page copies (`_v_*.html`, `_m_*.html`,
`_i_*.html`, `_debug.html`, `_sheet.html`) are gitignored, but delete them anyway.

---

## 4. Commits

One meaningful commit per completed task. If a single request contains several
related changes, finish them all and make **one** commit. Multiple commits are
fine only for a large task with genuinely independent features.

Write messages that describe the actual work:

```
Improve homepage product imagery
Add responsive product category navigation
Fix mobile header and search layout
Improve shop filters and product cards
Update contact and quote request pages
```

Never use: `update`, `changes`, `test`, `fix`, `new`, `wip`.

Use `git add <specific files>` when other unrelated changes are present.
Use `git add .` only when every current change belongs to the completed task.

---

## 5. Never commit secrets

No passwords, API keys, access tokens, private credentials, `.env` files or
secret configuration — ever. Never print a token or credential in output, and
never write one into a project file.

`.gitignore` already excludes `.env*`, `*.pem`, `*.key`, `node_modules/`, OS
clutter, `*.log`, `temp/`, `tmp/`, personal `.vscode` settings and validation
scratch files.

**Everything that is part of the website is tracked and must stay tracked:** all
`.html`, `css/`, `js/`, `assets/logo/`, `assets/images/` (product photography,
category images, banners) and `assets/image-sources.md`.

---

## 6. Git safety

### Never run these automatically

```
git reset --hard          git clean -fd            git clean -xdf
git push --force          git push -f              git checkout -- .
git restore .             git restore --source (on uncommitted user work)
```

Never rewrite published history unless explicitly asked and approved.
Never delete local work to make synchronisation easier.

### If `origin/main` has changes you do not have

Do not push over them. `git fetch origin`, inspect, and integrate without
destroying anything. If it rebases cleanly, carry on automatically.

If there is a **genuine merge conflict**, STOP and report:
- which files conflict
- what changed locally
- what changed remotely

Do not guess which side is correct and do not discard either version.

### Pre-existing uncommitted changes

Check `git status` before starting. If uncommitted changes belong to your own
earlier work in this session, handle them normally. If you find **unrelated**
uncommitted work the user may have made by hand: do not delete, reset or
overwrite it. Work around it, stage only your own files, and ask before
committing anything of theirs.

---

## 7. Report format

End every task with a short summary:

```
Completed: <what changed, one or two lines>
Validation: Passed
Commit: a1b2c3d — Improve homepage category imagery
GitHub: Successfully pushed to origin/main
WordPress live: Deployed and verified — <url tested>
GitHub Pages: Deployed and verified — <url tested>, or Not applicable (checked)
Local: Clean and synchronized
```

If validation failed, or the push failed, say so plainly and explain what is
needed. Never report success that did not happen.

---

## 8. Exception — "do not push" / "local only"

If the user says **"do not push"** or **"local only"**, make the changes locally
and stop there. Do not commit or push until they ask. Everything else in the
workflow (inspect, edit, validate, report) still applies.

---

## 9. Codebase conventions

```
D:\Star electric enterprises
├── *.html                21 page templates
├── css/
│   ├── styles.css        design tokens + shared components
│   ├── pages.css         page-specific layouts
│   └── responsive.css    all breakpoints
├── js/
│   ├── data.js           demo product/category/brand data (prototype only)
│   ├── components.js     header, mega menu, drawer, footer, product card
│   └── main.js           behaviour + per-page controllers
└── assets/
    ├── logo/             the real logo files — never redraw or replace
    ├── images/           products, categories, banners
    └── image-sources.md  image licence + attribution record
```

- **Header, mega menu, mobile drawer and footer are defined once** in
  `js/components.js` and injected into every page. Change shared chrome there,
  never in individual page files — that is what makes the future
  `header.php` / `footer.php` split a copy-paste.
- Pages select their controller with `data-page` on `<body>`, and their active
  nav item with `data-nav` (falls back to `data-page`).
- `renderProductCard()` in `components.js` is the single product card used by
  every grid. Change it once.
- **Brand palette is sampled from the logo** — navy `#221B66`, red `#FD2A2A`.
  `--brand-accent` is `#DC1414` (same hue, passes AA with white text) for fills
  and red text; `--brand-accent-bright` holds the exact logo red for graphic
  accents only. Do not introduce unrelated colours.
- Spacing follows an 8px rhythm via `--sp-*` tokens.
- `js/data.js` has a `PHOTO` array listing which image keys resolve to `.webp`;
  anything absent falls back to `.svg` line-art. Keep it in sync with the files
  actually present in `assets/images/products/`.

---

## 10. Standing project constraints

These hold until the user says otherwise:

- **No WordPress or WooCommerce yet.** The design must be approved first. Keep
  the structure convertible to `header.php`, `footer.php`, `front-page.php`,
  `archive-product.php`, `single-product.php` and the WooCommerce pages.
- **No frameworks or build step** — no React, Next, Vue, Bootstrap, Tailwind,
  Vite. Vanilla only.
- **Never invent business facts.** Phone numbers, WhatsApp numbers, the address,
  opening hours, delivery charges, return windows, warranty terms, payment
  methods, establishment year, customer counts, reviews, ratings-as-real-data,
  awards, certifications and dealership/authorised-partner claims are all
  off-limits unless the user supplies them.
- **The live site shows no bracketed placeholders.** The site is deployed to
  GitHub Pages, so anything on it is public. Where a fact is not on record,
  remove the UI item that would have displayed it rather than shipping
  `[ number ]` or `[ to be confirmed ]` — and never invent a value to fill the
  gap. Keep the real route open instead (contact form, quotation request). This
  supersedes the earlier instruction to leave visible bracketed placeholders,
  which was written before the site was published.
- **Brand logos stay as monogram placeholders** until a confirmed brand list is
  supplied — real manufacturer logos would imply stock relationships that do not
  exist on record.
- **Store photography** on the About and Contact pages stays a labelled
  placeholder until the user provides real photographs of the shop. Never show
  another business's premises as if it were theirs.
- **Product/category photography** is real, openly-licensed imagery from
  Wikimedia Commons. Most is CC BY-SA, which **requires visible attribution**.
  A credits page is still owed before the site goes live — see
  `assets/image-sources.md`. Record the source, author and licence of any image
  added in future.
- Six product types still use line-art placeholders because no accurate
  openly-licensed photograph exists: `cable-coil`, `conduit`, `downlight`,
  `panel-light`, `smart-switch`, `stabilizer`.
- Demo product data is clearly marked as such. Review counts are deliberately
  zero so no fake review count is ever displayed.
