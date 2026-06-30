# On-Page SEO — phillipstrang.com

## What works
- **Canonical tags present** on every page; **breadcrumbs** (BreadcrumbList schema) sitewide.
- **Strong internal linking** from the homepage (~37 internal links) into series and book pages.
- **Open Graph + Twitter Card** present on the homepage (og:title, og:type, og:url, og:image + dimensions; twitter:card, twitter:image, twitter:site, twitter:title).
- **Descriptive, keyword-aligned titles** on key pages (e.g. "Complete list of Phillip Strang books in order across 18 crime fiction series", "Murder House - Phillip Strang").

## Findings

### HIGH — Yoast title template broken on many pages (empty brand suffix)
Multiple titles render with a trailing separator and **no site name**:
- `About -`, `Free Book -`, `Best-Selling Hard-Boiled Mystery Novels in 2025 -`, `DCI Isaac Cook Series -`.
This is a Yoast title-template misconfiguration (`%%title%% %%sep%% %%sitename%%` with an empty `%%sitename%%` on certain post types / a stray separator). Fix the Yoast Search Appearance templates so every title ends consistently with "— Phillip Strang" (and no dangling " -").

### HIGH — Missing H1 on programmatic posts and blog index
- `/best-selling-hard-boiled-mystery-novels-in-2025/`: **0 `<h1>`** on a 3,885-word article.
- `/blog/`: **0 `<h1>`**.
- `/best-cozy-mystery-novels-with-amateur-sleuths/`: 1 H1 (correct).
The H1 is inconsistent across templates. Every indexable page needs exactly one descriptive H1. Audit the post and archive templates.

### MEDIUM — Missing meta description on `/about-2/`; auto descriptions elsewhere
`/about-2/` has no meta description (also a duplicate — see Technical). `/dci-isaac-cook-series/` uses an auto-truncated description.

### MEDIUM — Inconsistent brand suffix in titles
Some titles use "- Phillip Strang", others "— Phillip Strang", others nothing. Standardize the separator and brand string for consistent SERP presentation and brand recognition.

### LOW — Multiple H1s on some pages
Homepage reports 3 H1s; `/dci-isaac-cook-series/` reports 2. Not harmful in HTML5 but tighten to one primary H1 per page for clarity.

### LOW — `og:description` / `twitter:description` absent
Homepage OG set omits a description tag; add for better social/link-unfurl previews.
