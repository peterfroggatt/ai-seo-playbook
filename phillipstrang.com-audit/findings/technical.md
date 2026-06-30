# Technical SEO — phillipstrang.com

**Platform:** WordPress + Yoast SEO, PHP 8.2.30, behind Cloudflare CDN.
**Scope:** ~1,765 indexable URLs (1,643 posts + 117 pages + 5 categories).

## What works
- **HTTPS enforced.** `http://` → `https://` 301, `www` → non-www 301, `/index.php` → `/` 301. Clean canonical host.
- **Strong security headers** (live): `Strict-Transport-Security: max-age=63072000`, `Content-Security-Policy`, `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, Permissions-Policy, Referrer-Policy.
- **Valid robots.txt** with a Yoast block (`Disallow:` empty = allow all for search) and a Cloudflare-managed content-signals block.
- **XML sitemap index present** at `/sitemap_index.xml` (Yoast): post-sitemap (1000), post-sitemap2 (643), page-sitemap (117), category-sitemap (5).
- **Cloudflare CDN** in front of origin; caching plugin (WP-Optimize / `wpo`) active.

## Findings

### HIGH — Index bloat: low-value pages are indexable
All sampled pages return `<meta name="robots" content="index, follow, ...">`, including funnel/utility pages that should be `noindex`:
- `/free-book/`, `/thank-you/` (200), `/welcome-aboard/` (200), `/du-thank-you-books/` (200), `/sign-up-death-unholy/`, `/sign-up-free-murder-is-a-tricky-business/`, `/join-my-arc-team/`, `/welcome-aboard-free-du/`, `/du-thank-you-free-dl-lead-gen/` (+ `-old`).
These dilute crawl budget and can surface in search instead of money pages. Set `noindex, follow` and remove from sitemap.

### HIGH — Duplicate / legacy pages live and indexable
Old and duplicate versions return 200 and are `index,follow`:
- `/about/` **and** `/about-2/` (the latter is 13 words — near-empty duplicate).
- `/complete-book-list/`, `/book-list-old/`, `/my-books/` (overlapping "books in order" intent).
- `/di-tremayne-series/` **and** `/di-tremayne-series-2/`.
- `/phillip-strang-crime-fiction-author-old/`, `/du-thank-you-free-dl-lead-gen-old/`.
Resolve each pair: 301 the loser to the canonical winner and delete the orphan.

### MEDIUM — Canonical points to a "__trashed" redirecting URL
`/reading-guides/` declares `<link rel="canonical" href="https://phillipstrang.com/series__trashed/reading-guides/">`. That target is a WordPress *trashed-slug* artifact and 301-redirects. A canonical must point to a live, self-referential 200 URL. Fix the canonical to `https://phillipstrang.com/reading-guides/`.

### MEDIUM — Sitemap and robots reference `http://` URLs
`/sitemap_index.xml` and all child sitemaps list `<loc>http://phillipstrang.com/...`, and robots.txt declares `Sitemap: http://phillipstrang.com/sitemap_index.xml`. The site serves `https://`. This is a WordPress/Yoast `siteurl`/`home` option set to `http://`. Update WordPress Address + Site Address to `https://` so sitemaps emit https URLs (avoids an unnecessary redirect hop on every crawled URL).

### LOW — Crawl scale vs. content value
1,643 blog posts is large for an author site and is dominated by templated "best X novels / best Y authors set in Z" listicles. Not a defect per se, but see Content findings — at this scale, crawl budget and quality signals matter.

## Not measured (environment limits)
- **CrUX field data / PageSpeed**: no Google API key configured — no field Core Web Vitals.
- **Live render/screenshots**: headless Chromium egress blocked in this sandbox; mobile layout assessed from rendered HTML (responsive WordPress theme, viewport meta present).
