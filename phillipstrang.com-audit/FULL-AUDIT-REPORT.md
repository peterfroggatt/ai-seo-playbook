# Full SEO Audit — phillipstrang.com

**Date:** 2026-06-30
**Site:** https://phillipstrang.com
**Business type:** Author / Publisher — Phillip Strang, crime & thriller novelist (150+ books across 18 series)
**Platform:** WordPress + Yoast SEO · PHP 8.2.30 · Cloudflare CDN
**Scope crawled/sampled:** sitemap enumeration (1,765 URLs) + representative corpus of 13 pages across every page type

---

## Executive Summary

### Overall SEO Health Score: **65 / 100**

phillipstrang.com is a technically solid, well-structured WordPress author site with genuine first-party E-E-A-T, valid schema foundations, strong internal linking, and clean HTTPS/security hygiene. Its score is held back by **index hygiene** (legacy, duplicate, and funnel pages all indexable), a **Yoast title-template bug**, **inconsistent H1s**, a **large volume of templated listicle content**, a **missing Book-schema opportunity** on book pages, and a **self-inflicted AI-search blackout** (most AI crawlers blocked).

None of the issues are penalty-level emergencies; most are high-leverage, low-effort fixes that a single focused sprint would clear.

### Top 5 issues
1. **Index bloat** — lead-gen/thank-you/legacy/duplicate pages are all `index,follow` (e.g. `/free-book/`, `/thank-you/`, `/about-2/` (13 words), `/book-list-old/`, `/di-tremayne-series-2/`).
2. **No Book/Product schema on book pages** — the biggest structured-data miss for an author selling 150+ titles.
3. **AI crawlers blocked** — GPTBot, Google-Extended, Bytespider, Amazonbot, Apple, Meta all disallowed; highly citable content is invisible to AI answer engines.
4. **Yoast title template broken** — many titles render as `About -`, `Free Book -`, `… in 2025 -` with a dangling separator and no brand.
5. **Missing H1s + thin templated content at scale** — a 3,885-word post and `/blog/` have 0 H1; ~1,643 programmatic listicles risk Helpful-Content/scaled-content signals.

### Top 5 quick wins
1. Fix the `/reading-guides/` canonical (it points to a `series__trashed/` redirecting URL).
2. Set WordPress Site Address to `https://` so sitemaps stop emitting `http://` URLs.
3. `noindex` the funnel/legacy/duplicate pages and drop them from the sitemap.
4. Repair the Yoast title template so every title ends `— Phillip Strang`.
5. Add `llms.txt` and make a deliberate AI-crawler citation policy decision.

### Data limitations (free-tier audit)
- **No Google API key** → no CrUX field Core Web Vitals, no GSC indexation, no GA4 traffic.
- **No Moz/Bing/DataForSEO** → no backlink profile or live SERP positions (Common Crawl tier only).
- **Headless browser egress blocked** in the audit sandbox → no live screenshots; mobile assessed from rendered HTML (responsive theme, viewport meta present).

---

## Category Scores

| Category | Score | Weight |
|---|---|---|
| Technical SEO | 72 | 22% |
| Content Quality | 62 | 23% |
| On-Page SEO | 66 | 20% |
| Schema / Structured Data | 60 | 10% |
| Performance (CWV)* | 70 | 10% |
| AI Search Readiness | 48 | 10% |
| Images | 75 | 5% |
| **Weighted total** | **65** | 100% |

\* Performance is a low-confidence estimate — no field data available.

---

## Technical SEO — 72/100

**Works:** HTTPS enforced (http→https, www→non-www, /index.php all 301); HSTS + CSP + nosniff + X-Frame-Options; valid robots.txt; Yoast sitemap index (1,765 URLs); Cloudflare CDN + caching plugin.

**Issues:**
- **HIGH — Index bloat.** Funnel/utility pages are indexable: `/free-book/`, `/thank-you/`, `/welcome-aboard/`, `/join-my-arc-team/`, `/du-thank-you-books/`, `/du-thank-you-free-dl-lead-gen/` (+ `-old`). → `noindex,follow` + remove from sitemap.
- **HIGH — Duplicate/legacy pages live (200, indexable):** `/about/` vs `/about-2/`; `/complete-book-list/` vs `/book-list-old/` vs `/my-books/`; `/di-tremayne-series/` vs `/di-tremayne-series-2/`; `/phillip-strang-crime-fiction-author-old/`. → 301 to canonical winners, delete orphans.
- **MEDIUM — Canonical to a `__trashed` URL.** `/reading-guides/` canonicals to `…/series__trashed/reading-guides/` (301 redirect). → point canonical to the live self URL.
- **MEDIUM — Sitemaps/robots emit `http://`.** Yoast `siteurl` is `http://`; sitemaps list http URLs. → set WordPress addresses to https.
- **LOW — Crawl scale.** 1,643 posts strain crawl budget (see Content).

Full detail: `findings/technical.md`.

---

## Content Quality — 62/100

**Works:** Real first-party author expertise; 814-word `/about/`; useful "books in order" reading-order pages (Rebus page = 1,958 words, 31 covers + buy links); hand-written meta on money pages.

**Issues:**
- **HIGH — Programmatic listicles at scale.** ~1,643 templated "Best [genre] novels / Best [authors] set in [place]" posts; depth ranges 3,885 → ~477 words. Risk of scaled-content/Helpful-Content drag. → audit by depth/engagement; consolidate or `noindex`+improve the thinnest 20–30%.
- **HIGH — Thin/empty indexed pages.** `/about-2/` (13 words), `/dci-isaac-cook-series/` (132 words), `/my-books/` (164 words).
- **MEDIUM — Auto/templated meta descriptions** on series/category pages.
- **MEDIUM — Competitor-author pages** must add original curation and link back to Strang's series to convert borrowed traffic.

Full detail: `findings/content.md`.

---

## On-Page SEO — 66/100

**Works:** Canonicals + breadcrumbs sitewide; ~37 homepage internal links; OG + Twitter cards; descriptive titles on key pages.

**Issues:**
- **HIGH — Yoast title template broken.** `About -`, `Free Book -`, `…in 2025 -`, `DCI Isaac Cook Series -` (dangling separator, empty `%%sitename%%`). → fix Search Appearance templates.
- **HIGH — Missing H1.** `/best-selling-hard-boiled-mystery-novels-in-2025/` (3,885 words) and `/blog/` have 0 H1. → one descriptive H1 per page.
- **MEDIUM — Missing/auto meta descriptions** (`/about-2/`, series pages).
- **MEDIUM — Inconsistent brand suffix/separator** ("-" vs "—" vs none).
- **LOW — Multiple H1s** on homepage/series pages; **no `og:description`/`twitter:description`** on homepage.

Full detail: `findings/onpage.md`.

---

## Schema / Structured Data — 60/100

**Works:** Valid Yoast JSON-LD graph sitewide (3/3 blocks parse); consistent `Person`/`Organization`/`WebSite`+`SearchAction`; `Article` on posts; `BreadcrumbList` everywhere.

**Issues:**
- **HIGH — No `Book`/`Product` schema on book pages.** `/cook/murder-house-phillip-strang/` emits only the generic WebPage graph. → add `Book` (author, isbn, bookFormat, inLanguage, workExample) + `Offer` where priced.
- **MEDIUM — No `ItemList`** on "books in order" pages. → mark each list as `ItemList` of `Book`s.
- **MEDIUM — No `Review`/`AggregateRating`** (only where ratings are genuinely shown).
- **LOW — No `FAQPage`** on guide content.

`scripts/schema_generate.py` can scaffold these. Full detail: `findings/schema.md`.

---

## Performance (CWV) — 70/100 *(low confidence — no field data)*

**Works:** Cloudflare CDN + caching plugin; moderate homepage payload (~87 KB HTML, 14 scripts).

**Issues:**
- **Field CWV not measured** — configure a Google API key and run `scripts/pagespeed_check.py`.
- **MEDIUM — Image-heavy templates** (19–31 covers per page) risk LCP/CLS → WebP/AVIF + explicit dimensions + lazy-load.
- **LOW — Page-builder CSS/JS bloat** (11 inline style blocks).

Full detail: `findings/performance-images.md`.

---

## AI Search Readiness (GEO) — 48/100

**Works:** AI-friendly content structure; strong author entity; Googlebot/AI-Overviews still allowed.

**Issues:**
- **HIGH — Most AI crawlers blocked.** `GPTBot`, `Google-Extended`, `Bytespider`, `Amazonbot`, `Applebot-Extended`, `meta-externalagent` all `Disallow: /`; `Content-Signal: ai-train=no`. Highly citable list content is invisible to ChatGPT/Perplexity/Gemini/Meta/Apple. **This is a policy decision** — blocking *training* is legitimate, but blocking *citation* (GPTBot/PerplexityBot) costs book-discovery reach. Recommend allowing citation crawlers while keeping training blocked if desired.
- **MEDIUM — No `llms.txt`** (404). → add one curating canonical high-value pages.
- **MEDIUM — Citability untapped** until access policy changes.

Full detail: `findings/geo.md`.

---

## Images — 75/100

**Works:** ~94% alt coverage on sampled image-heavy page; `og:image` with dimensions.

**Issues:**
- **MEDIUM — Confirm WebP/AVIF + responsive `srcset` + explicit sizing** on cover-heavy templates.
- **LOW — A few missing alts; verify `loading="lazy"`** below the fold.

---

## Methodology

Sitemap enumeration and page fetches via the skill's `fetch_page.py`/`parse_html.py` (through the environment's egress proxy) and `curl`; on-page elements, meta robots, canonicals, schema `@type`s, titles, H1 counts, and alt coverage extracted from a 13-page representative corpus plus the homepage. JSON-LD validated by parsing. Redirect/canonicalization and status codes verified live. Field data (CrUX/GSC/GA4), premium backlinks/SERP (Moz/Bing/DataForSEO), and live screenshots were unavailable in this environment (see Data Limitations). Scores follow the audit skill's weighting; the Performance score is an explicit low-confidence estimate.

See `ACTION-PLAN.md` for the prioritized, phased remediation plan.
