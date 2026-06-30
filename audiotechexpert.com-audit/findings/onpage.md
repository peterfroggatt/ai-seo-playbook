# On-Page SEO Findings — audiotechexpert.com

Method: direct fetch + BeautifulSoup parse of 18 representative URLs (homepage, category/landing pages, trust pages, review/guide posts). Raw data: `data/onpage_results.json`.

## Score: 68/100

## What works
- **Meta descriptions**: present on every page sampled, well-sized (133–159 chars), benefit-led copy.
- **Review/guide posts are well-optimized**: single H1, descriptive titles (49–60 chars), solid depth (1,300–1,600 words), self-referential canonicals, `index, follow` robots.
- **Modern robots meta**: `max-image-preview:large` enabled (good for image/AI surfaces).
- Clean, readable, keyword-relevant slugs.

## Findings

### HIGH — Empty WordPress site title breaks brand titles on key pages
Root-cause config issue. Several category/landing pages render title tags that are just the page name plus a dangling separator with **no brand**:
- `/guides/` → `"Guides -"` (8 chars)
- `/contact/` → `"Contact -"` (9 chars)
- `/headphone-comparisons/` → `"Headphone Comparisons -"` (23 chars)
These end in a trailing `" - "` because the WP **Site Title is empty** (same root cause as the empty `WebSite.name` in the homepage schema graph). Impact: weak, truncated SERP titles on money/category pages; lost brand reinforcement. Fix: set the WordPress Site Title to "Audio Tech Expert" and define Yoast title templates for archives/pages (e.g. `%%title%% %%sep%% %%sitename%%`).

### HIGH — Duplicate page + duplicate title (canonical confusion)
`/recording-production/` and `/guides/recording-production/` share the identical title `"Recording & Production Guides | Audio Tech Expert"` and overlapping content (578 vs 1,038 words). Two indexable URLs targeting the same intent split equity and risk the wrong one ranking. Fix: pick the canonical URL, 301-redirect the other (or cross-canonical), update internal links.

### MEDIUM/HIGH — Legacy `/headphone-guides-old/` is live and indexable
Returns 200, `index, follow`, 569 words — duplicates the purpose of `/headphone-guides/`. "-old" slug signals a leftover. Fix: 301-redirect to the current guides hub (or noindex if intentionally retained).

### MEDIUM — Multiple H1 tags on landing/archive templates
Homepage, `/best-headphones/`, `/best-microphones/`, `/guides/`, `/about-us/`, `/how-we-choose/`, `/recording-production/` each render **2 `<h1>` tags**. (Note: the repo `parse_html.py` reported 0 H1 on the homepage — a parser quirk; direct DOM parse finds 2.) Review posts correctly use a single H1. Fix: enforce one H1 per template (theme header + page title likely both emit H1).

### MEDIUM — Very short/auto-generated titles on category pages
Beyond the empty-brand issue, archive titles like `"Guides -"` and `"Contact -"` carry no descriptive/keyword value. Define intentional titles for every indexable category and page.

### LOW/MEDIUM — Images missing alt text
~3 `<img>` per page sampled have no `alt` attribute (consistent across templates → likely theme logo/decorative/affiliate-widget images). Audit and add descriptive alt, especially for product/affiliate images. (See image/visual + content findings for depth.)

## Recommendations (priority order)
1. Set WP Site Title = "Audio Tech Expert"; fix Yoast title templates for pages/archives; this also fixes the empty `WebSite.name` schema. (HIGH, ~30 min)
2. Resolve `/recording-production/` vs `/guides/recording-production/` duplication via 301 + canonical. (HIGH)
3. Redirect/noindex `/headphone-guides-old/`. (MEDIUM/HIGH)
4. Reduce to one H1 per template. (MEDIUM)
5. Write descriptive titles for thin-titled category/landing pages. (MEDIUM)
6. Add alt text to theme/affiliate images. (LOW/MEDIUM)
