# Sitemap Audit — audiotechexpert.com

**Score: 51/100**

## Validation by sub-sitemap
- **sitemap_index.xml** — PASS. Well-formed, 5 children, HTTPS, ISO-8601 lastmod on all. XSL uses protocol-relative URL (cosmetic).
- **post-sitemap.xml (285)** — CONDITIONAL. Valid XML, HTTPS, lastmod present, inline `image:image` present (good). But contains 21 WP auto-slug duplicate posts (below).
- **page-sitemap.xml (20)** — FAIL. Contains `/headphone-guides-old/` (indexed legacy orphan) and BOTH `/guides/recording-production/` + `/recording-production/` (identical titles, both self-canonical, both index,follow → duplicate-indexation conflict). Breadcrumbs suggest `/guides/recording-production/` is the intended canonical.
- **category-sitemap.xml (58)** — PASS w/ warnings. Legitimate taxonomy (brand/type/use-case). `/category/guides/` missing lastmod (minor).
- **post_tag-sitemap.xml (310)** — FAIL (index-bloat). See below.
- **author-sitemap.xml (1)** — `/author/peter_froggatt/` 200. Single-author site gains little from an indexed author archive; enrich for E-E-A-T or noindex.

## CRITICAL — 310 tag archives (46% of the 674-URL index)
- 310 tags > 285 posts. 87% (271/310) are compound keyword-stuffed slugs, e.g.
  `/tag/acoustic-guitar-microphone-guitar-pickup-l-r-baggs-shure-sm57-audio-technica-instrument-microphone/` (98 chars).
- All self-canonical, no noindex → indexable. Most archives hold ~1 post = thin doorway pages. Helpful-Content/QRG liability + crawl-budget waste.
- **Fix:** Yoast → Search Appearance → Taxonomies → Tags → "Show Tags in search results?" = No (auto-adds noindex + removes from sitemap). Highest-impact sitemap fix.

## HIGH — 21 WordPress auto-slug duplicate posts in sitemap
WordPress appended `-2`…`-7` when a slug already existed → genuine duplicate posts (NOT the legitimate `-2026` year slugs). Confirmed examples:
`/recording-2/`, `/transparency-mode-vs-anc-explained-2/`, `/noise-cancelling-vs-noise-isolating-difference-2/`, `/open-back-vs-closed-back-headphones-difference-2/`, `/headphone-frequency-response-explained-2/`, `/bluetooth-5-3-vs-5-0-headphones-2/`, `/what-is-aptx-vs-ldac-aac-2/`, `/how-to-care-for-headphones-cleaning-maintenance-2/`, `/what-is-head-tracking-headphones-2/`, `/how-to-choose-headphones-for-travel-2/`, `/bone-conduction-headphones-explained-2/`, `/how-to-choose-gaming-headphones-guide-2/`, `/how-to-choose-headphones-for-mixing-2/`, `/headphone-amplifiers-explained-when-needed-2/`, `/how-to-monitor-with-headphones-studio-mixing-2/`, `/what-is-crossfeed-mixing-headphones-2/`, `/polar-patterns-explained-cardioid-omnidirectional-2/`, `/how-to-choose-a-microphone-beginners-guide-7/` (collision #7), `/best-noise-cancelling-headphones-2026-2/`, `/best-in-ear-monitors-earbuds-2026-2/`, `/rode-wireless-go-ii-vs-dji-mic-2/` (verify — "Mic 2" may be a real model name).
- **Fix:** Audit each; 301 the `-N` duplicate to its original (or delete) and remove from sitemap.

## MEDIUM — No dedicated image sitemap
Inline `image:loc` only (no title/caption). For a review site, an image sitemap can drive Google Images discovery. Opportunity, not a penalty.

## Prioritised fixes
1. Noindex tags + drop post_tag-sitemap from index (CRITICAL)
2. Resolve 21 auto-slug duplicate posts (HIGH)
3. Canonicalize recording-production duplicate pair (HIGH)
4. Redirect/noindex `/headphone-guides-old/` (HIGH)
5. `/category/guides/` lastmod (MEDIUM/cosmetic)
6. Author archive enrich-or-noindex (LOW)
7. Image sitemap (LOW)

## Score breakdown
XML validity 18/20 · URL health 14/20 · Index hygiene 4/20 · Canonical integrity 3/20 · lastmod 8/10 · Image coverage 4/10.
