# Performance / Core Web Vitals — audiotechexpert.com

**Score: 38/100 · Lab estimates only** (no CrUX/PSI field data — no Google API creds). Derived from HTML source, response headers, and resource enumeration; not real-user data.

## CWV lab estimates
| Metric | Estimate | Band |
|---|---|---|
| LCP | 4.5–6 s | Poor |
| INP | 250–350 ms | Needs Improvement |
| CLS | 0.10–0.18 | Needs Improvement |

## CRITICAL — LCP hero hotlinked from images.unsplash.com
Homepage LCP candidate is a third-party Unsplash image (also a CSS hero background via a 2nd Unsplash URL). No `fetchpriority="high"` (preload_check: 0), no `<link rel=preload>` (preload_lcp_candidate=false), `decoding="async"` only. CSS-background hero is invisible to the preload scanner. Third-party dependency = LCP fails if Unsplash throttles/down; no cache-control; no WebP. **Fix:** self-host + WebP, add `fetchpriority=high` + preload, add `width`/`height`.

## HIGH — HTML uncacheable at CDN (DYNAMIC on every page)
`x-hcdn-cache-status: DYNAMIC` on homepage + 2 posts; upstream RT 387–531 ms. Cause: **AAWP sets `aawp-country=US` cookie on every response**, so Hostinger CDN won't cache HTML → full PHP render per visit → ~400ms TTFB before any subresource. **Fix:** strip/ignore the AAWP cookie at the CDN/caching layer (or set country client-side); target `HIT` and ~50ms TTFB.

## HIGH — Render-blocking head + heavy JS stack
jQuery 3.7.1 + jQuery Migrate loaded **synchronously in `<head>`** (no async/defer); Astra `main.min.css` + 9 Elementor widget CSS files render-blocking; ~13 synchronous JS files at body end (Elementor Pro runtime, smartmenus, EAEL, etc.). Drives INP. **Fix:** defer jQuery, enable Elementor "Improved Asset Loading", trim widget CSS.

## HIGH — Google Fonts: 5 blocking requests, duplicated, no preconnect
Elementor loads Roboto/Roboto Slab/Inter; theme separately loads Outfit + Source Serif 4 = 5 render-blocking font CSS requests across 2 origins, only `dns-prefetch` for GTM (no `preconnect` to fonts.googleapis/gstatic). **Fix:** self-host fonts, dedupe families, `preconnect`.

## HIGH — CLS: Unsplash images lack width/height
11 homepage `<img>` to images.unsplash.com have no `width`/`height`/`aspect-ratio` (the `?w=800&h=600` are URL params, not attributes). FOUT from 5 fonts + Elementor lazy containers add shift. Logo correctly sets 247×82 (good). **Fix:** explicit dimensions on every img.

## HIGH — Images unoptimised (JPEG, not WebP/AVIF)
Review featured images are self-hosted JPEG (og:image type image/jpeg); no WebP/AVIF variants. ~30–50% larger than needed. Below-fold category images lack `loading="lazy"`. **Fix:** WebP/AVIF (Imagify/ShortPixel/native), lazy-load below-fold.

## MEDIUM — Third-party & preload
GTM/GA4 async (ok), Site Kit + Mailchimp deferred (ok). Speculation Rules present (conservative prefetch — positive). 0 preload hints, 0 prerender. **Fix:** add preconnects, preload LCP image.

## Prioritised
P1: self-host+optimize images & set dimensions; fix CDN cookie caching; defer jQuery; fetchpriority/preload LCP.
P2: self-host/dedupe fonts; WebP review images.
P3: lazy-load below-fold; preconnects; trim Elementor runtime on homepage.
