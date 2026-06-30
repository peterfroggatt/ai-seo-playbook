# Performance & Core Web Vitals — techwhizmart.com
**Audit date:** 2026-06-30
**Analyst:** seo-performance agent

> **LAB ESTIMATES ONLY — NO FIELD/CrUX DATA AVAILABLE.**
> Google PageSpeed Insights API and Chrome User Experience Report (CrUX) credentials were not configured for this audit. All LCP/CLS/INP values below are estimates derived from static HTML analysis, curl transfer timing, and resource inventories — not real-user measurements. Validate with PageSpeed Insights (https://pagespeed.web.dev) before prioritising fixes.

---

## Stack & Infrastructure

| Item | Value |
|------|-------|
| CMS | WordPress 7.0 |
| Page builder | Elementor 4.1.4 (external CSS print method) |
| E-commerce | WooCommerce 10.8.1 |
| Analytics | Google Site Kit (GTM container GT-NS8RPB5D) |
| Theme | Rehub 19.8.4 |
| Host / CDN | Hostinger hcdn CDN, HTTP/2 + h3/QUIC (alt-svc advertised) |
| PHP | 8.2.30 |
| Compression | Brotli (`content-encoding: br`) |
| Cache | `cache-control: public, max-age=0` — DYNAMIC, not cached at edge |

---

## Page-Weight Summary (wire bytes, Brotli-compressed HTML only)

| Template | Compressed HTML | Uncompressed HTML |
|----------|-----------------|-------------------|
| Homepage | ~123 KB | ~122 KB (stored) |
| Product page (JBL Charge 4) | ~197 KB | ~202 KB (stored) |
| Blog post (~16k-word drone post) | ~436 KB | ~450 KB (stored) |

Note: these figures are the HTML document only. Total page weight including all sub-resources (CSS, JS, images, fonts) is substantially higher — see asset breakdown below.

### Measured asset weights (curl, uncompressed delivery)

**CSS — Homepage (32 stylesheets total, 618 KB aggregate uncompressed)**

| File | Bytes |
|------|-------|
| rehub-theme/style.css | 139,210 |
| Roboto Google Font (self-hosted) | 107,244 |
| elementor/frontend.min.css | 54,666 |
| font-awesome/fontawesome.min.css | 58,071 |
| content-views plugin CSS | 87,197 |
| rehub woocommerce.css | 40,587 |
| elementor/frontend-modules.min.js | 50,602 |
| Roboto Slab font CSS | 24,885 |
| wc-blocks.css | 14,006 |
| (+ 23 further CSS files) | ~40,145 |
| **Total CSS sampled** | **~618 KB** |

**JavaScript — Homepage (43 external scripts)**

| File | Bytes | Blocking? |
|------|-------|-----------|
| Google Tag Manager gtag/js | 483,839 | async (ok) |
| jQuery core 3.7.1 | 87,553 | **BLOCKING** |
| elementor/frontend-modules.min.js | 50,602 | **BLOCKING** |
| elementor/frontend.min.css (CSS) | 54,666 | — |
| jQuery migrate 3.4.1 | 13,577 | **BLOCKING** |
| rehub/custom.js | 21,016 | **BLOCKING** |
| elementor/webpack.runtime.min.js | 5,819 | **BLOCKING** |
| elementor/frontend.min.js | 32,098 | **BLOCKING** |
| Site Kit WC events JS | 2,506 | async (ok) |

**Images — key files measured**

| File | Bytes | Format | Notes |
|------|-------|--------|-------|
| Logo (PNG) | 104,549 | PNG | No WebP/AVIF variant; no lazy; no dimensions on header instance |
| Blog LCP hero (WebP) | ~17,750 | WebP | Good format; missing lazy=eager + fetchpriority=high in some placements |
| Blog inline AVIF images | ~8,000–16,000 each | AVIF | Good format |
| Product hero JPG (Amazon CDN) | ~40,000–50,000 each | JPG | Not modern format; sourced from Amazon media CDN |
| Blog JPG inline images | 7,000–49,000 each | JPG | Mixed — some large |

---

## TTFB & Transfer Timing (curl from audit environment)

| Page | TTFB | Total transfer | DNS | TLS |
|------|------|----------------|-----|-----|
| Homepage | 281 ms | 323 ms | 2.7 ms | 30 ms |
| Product page | 374 ms | 406 ms | — | — |
| Blog post | 339 ms | 410 ms | — | — |

- TTFB is acceptable (sub-400 ms) but cache-control: max-age=0 means every request hits origin PHP — no edge caching benefit.
- `x-hcdn-cache-status: DYNAMIC` confirms no CDN cache hit on any sampled request.
- `render_page.py` returned `render_ms: null` — Playwright headless rendering was not available in this environment, so JavaScript-dependent render timing could not be captured.

---

## Render-Blocking Resources

**[CRITICAL]** Every sampled page loads 27–30 render-blocking scripts in `<head>` before any paint can occur.

Homepage blocking scripts (30 of 43 total):
- `jquery.min.js` + `jquery-migrate.min.js` — loaded synchronously, highest priority, block all parsing
- `elementor/webpack.runtime.min.js`, `frontend-modules.min.js`, `frontend.min.js` — three Elementor JS files, all blocking
- `rehub-theme/js/custom.js`, `tablechart.js`, `comparechart.js`, `pgwmodal.js`, `countdown.js`, `inview.js`, `unveil.js`, `hoverintent.js`, `jquery.sticky.js`, `ajaxcart.js`, `quantity.js`, `niceselect.js`, `woodropcat.js`, `owlinit.js`, `owl.carousel.min.js`, `userlogin.js` — theme scripts all blocking
- `embed-any-document/js/pdfobject.min.js` + `embed-public.min.js` — PDF plugin, likely unused on homepage
- `woocommerce/order-attribution.min.js`, `sourcebuster.min.js` — WooCommerce analytics, blocking
- `content-views/cv.js` — post-view plugin, blocking
- `wp-includes/underscore.min.js`, `wp-util.min.js` — WP core utilities, blocking
- `essential-addons-for-elementor-lite/general.min.js` — blocking

Only 13 of 43 scripts use `defer` or `async`. GTM loads async (correct); WooCommerce front-end cart/product scripts are deferred (correct). Everything else is synchronous.

**Impact on LCP:** With 30+ blocking scripts executing before first paint, LCP is severely delayed. Estimated LCP on a mid-tier mobile device on 4G: likely 4–6 s (poor threshold is >4.0 s).

---

## Elementor CSS/JS Overhead

| Asset | Size | Issue |
|-------|------|-------|
| frontend.min.css | 54,666 B | Loads on every page regardless of widgets used |
| frontend-modules.min.js | 50,602 B | BLOCKING |
| frontend.min.js | 32,098 B | BLOCKING |
| webpack.runtime.min.js | 5,819 B | BLOCKING |
| eicons (icon font CSS) | 21,976 B | Full icon set, likely only a fraction used |
| font-awesome.min.css | 58,071 B | Full FA5 library; most icons unused |
| elementor.css (theme) | 1,896 B | Minor |
| essential-addons CSS | 7,746 B | Loads on all pages |
| **Elementor subtotal (CSS+JS)** | **~233 KB** | All load-critical, much is unused |

The Elementor "external CSS print method" (confirmed in stack notes) means per-page Elementor CSS is written to separate files (`post-11040.css`, `post-15.css`) rather than inlined — this is the correct setting for performance, but those files add extra HTTP requests.

Font Awesome 5 (58 KB CSS + icon font files) is loaded globally. On the blog post, which uses no Elementor widgets, this is entirely wasted.

---

## Image Formats & Lazy Loading

### Homepage
- **6 images total**
- **0 use lazy loading** — all above-the-fold images are acceptable without lazy, but no `fetchpriority=high` on the logo/LCP candidate
- Logo PNG: 104,549 bytes — should be WebP/AVIF or at minimum a compressed SVG
- Two images reference GIF placeholders from the theme (`default/` path with no extension visible) — likely placeholder GIFs used for lazy-load reveal; the JS-based unveil.js pattern is used instead of native `loading=lazy`
- Banner PNG (404 at time of audit) — broken image on homepage
- **No `<link rel="preload">` for the hero/LCP image** — only the theme icon font is preloaded

### Product Page (JBL Charge 4)
- **26 images**
- 19 use `loading=lazy` (good)
- Product images are JPG sourced from Amazon CDN (`m.media-amazon.com`), not WebP/AVIF — WooCommerce/WP Automatic has mirrored Amazon images but kept JPG format
- Hero product image: `loading=eager` + `fetchpriority=high` correctly set
- GIF placeholder pattern used for related-product thumbnails (unveil.js lazy)
- No WebP conversion applied to product images

### Blog Post (drone-photography-for-travel-blogs — ~450 KB HTML)
- **43 images**
- Only 7 of 43 use `loading=lazy` — **36 images load eagerly including images far below the fold**
- Hero image correctly uses WebP + `fetchpriority=high` — good
- 28 images use WebP or AVIF — good format choice for most inline content
- 1 image is missing `width`/`height` attributes — CLS risk
- A large AVIF image (481x321) is repeated at least 8 times in the HTML as the same `src` pointing to the same file — apparent template/block repetition, inflating DOM and parallel HTTP requests
- Several JPG inline images remain (not converted)
- Total estimated image payload for the blog: 400–700 KB (uncompressed)

---

## Web Font Loading

- Google Fonts (Poppins) requested via external `//fonts.googleapis.com` link — **this request failed (HTTP 000) in the audit environment**, suggesting a connectivity/CSP issue or the font is conditionally loaded. If it resolves in browsers it is a render-blocking cross-origin request.
- Roboto and Roboto Slab are self-hosted via Elementor's Google Fonts local cache (`/uploads/elementor/google-fonts/`) — good for privacy/connection overhead, but:
  - The CSS files themselves are large (Roboto: 107,244 bytes — contains all weights/subsets)
  - **No `font-display` declaration was found in any sampled HTML** — fonts likely use the browser default (`block` or `auto`), causing FOIT (Flash of Invisible Text) and contributing to LCP delay
- Only 1 preload hint exists sitewide: the theme's custom icon font (`rhicons.woff2`) — no preload for Roboto or Roboto Slab, which are the body/heading fonts

**[HIGH]** Missing `font-display: swap` on body fonts (Roboto, Roboto Slab) will delay text rendering and inflate LCP.

---

## Third-Party Scripts

| Script | Load method | Size | Impact |
|--------|-------------|------|--------|
| Google Tag Manager (`gtag/js?id=GT-NS8RPB5D`) | `async` | 483,839 B | Main thread work after load; large |
| Google Site Kit WC events | `async` | 2,506 B | Minimal |
| WP Automatic plugin (`main-front.js`) | **BLOCKING** | unknown | Appears on product + blog, not homepage |
| Content Views plugin (`cv.js`) | **BLOCKING** | included in 87 KB CSS bundle | Fires on all pages |

GTM loads async (correct) but at 484 KB it is the single largest JS asset on the page. GTM also introduces unknown third-party tag weight depending on what tags are fired inside the container — this was not measurable from HTML alone.

---

## Caching

`cache-control: public, max-age=0` with `x-hcdn-cache-status: DYNAMIC` on all three sampled pages means:
- Zero CDN caching — every visitor hits the origin PHP server
- Browsers re-validate on every navigation (effectively no browser cache for HTML)
- Static assets (CSS/JS/images) may cache separately if they have their own headers — not verified per-asset, but WordPress typically sets longer TTLs on versioned assets

**[HIGH]** Enabling full-page caching (WP Super Cache, W3 Total Cache, or LiteSpeed Cache on Hostinger) would be the single highest-impact change for TTFB and LCP.

---

## Core Web Vitals Risk Estimates (LAB, no CrUX data)

### LCP (Largest Contentful Paint) — Estimated: POOR (>4.0 s on mobile)
Primary LCP candidate per template:
- **Homepage:** Logo PNG (104 KB, no preload, PNG format) or banner image (currently 404). With 30 blocking scripts, paint is delayed severely.
- **Product page:** Hero product JPG (`fetchpriority=high`, `loading=eager`) — format is JPG not WebP, sourced cross-origin from Amazon CDN. LCP candidate is correct but format/blocking-JS overhead risks poor score.
- **Blog post:** Hero WebP with `fetchpriority=high` — best of the three. Still delayed by 27 blocking scripts.

Key LCP risk factors:
1. 27–30 render-blocking scripts on every page
2. No `<link rel="preload">` for LCP images (only icon font is preloaded)
3. Homepage logo is PNG (104 KB), not WebP/AVIF
4. `cache-control: max-age=0` — no HTML caching, every load cold
5. GTM 484 KB payload executing on main thread post-load

### INP (Interaction to Next Paint) — Estimated: NEEDS IMPROVEMENT to POOR
- Heavy jQuery dependency (core + migrate) with 15+ WooCommerce JS files and 10+ Rehub theme JS files all synchronously parsed
- WooCommerce cart fragments (`cart-fragments.min.js`) fires an AJAX request on every page load — known INP contributor
- Rehub theme scripts (carousel, dropdown, sticky, quantity, ajaxcart) add significant event-listener surface area
- 27 inline `<script>` blocks per page add parse overhead
- Elementor frontend JS (3 files, ~88 KB blocking) registers widget interaction handlers on DOMContentLoaded
- No evidence of `scheduler.yield()`, task chunking, or `requestIdleCallback` patterns

### CLS (Cumulative Layout Shift) — Estimated: NEEDS IMPROVEMENT
- 1 image on blog post missing `width`/`height` — direct CLS risk
- Two homepage images reference GIF placeholder paths — if the GIF has different dimensions than the final image, layout shift occurs
- Blog post AVIF image repeated 8 times with same file but potentially loaded in different layout contexts
- Font FOIT/FOUT: no `font-display: swap` means text may shift when fonts load
- WooCommerce cart fragment AJAX injects content dynamically — can cause layout shift in header area
- Homepage "No products for this criteria" empty widget: the Elementor product grid widget renders with a container but no products. If JS attempts to populate it after initial render, or if CSS reserves space that collapses, this is a CLS source. Even without JS-driven shift, the empty state is a content quality failure (see homepage notes in shared context).

---

## Homepage: Empty Product Widget

The "Today's Popular Picks / based on what's trending" section renders: `No products for this criteria.`

Performance implications:
- The WooCommerce product query still executes server-side (PHP + DB query) even though it returns zero results — wasted server processing time on every homepage load
- Elementor product carousel JS (owl.carousel.min.js, 29 KB blocking) is loaded to power a widget that shows nothing
- The empty widget likely causes a layout shift as the carousel initialises, finds no slides, and collapses or renders a minimal-height container

---

## Prioritised Fixes

### P1 — Critical (direct CWV impact)

**1. Enable full-page caching**
- Install LiteSpeed Cache (free, Hostinger-native) or activate WP Super Cache
- Target: `cache-control: public, max-age=3600` minimum for HTML; static assets to max-age=31536000
- Expected LCP impact: -500 ms to -1 s TTFB reduction

**2. Defer or async all non-critical JavaScript**
- Add `defer` to all Rehub theme scripts (custom.js, tablechart.js, comparechart.js, etc.)
- Add `defer` to WooCommerce order-attribution.min.js, sourcebuster.min.js
- Add `defer` to Elementor frontend JS (webpack.runtime, frontend-modules, frontend)
- Add `defer` to embed-any-document scripts (or remove plugin if unused)
- Add `defer` to content-views cv.js
- jQuery must remain blocking (Elementor/WooCommerce dependency) — but migrate can be deferred once JS is audited
- Expected LCP impact: -1–2 s

**3. Preload LCP image per template**
Add to `<head>` for each page type:
```html
<!-- Homepage -->
<link rel="preload" as="image" href="/wp-content/uploads/2025/01/logo.webp" fetchpriority="high">
<!-- Blog post -->
<link rel="preload" as="image" href="[hero-webp-url]" fetchpriority="high">
```
Implement via Rank Math/Yoast custom head injection or a must-use plugin.

**4. Convert logo PNG to WebP/AVIF**
- Logo PNG is 104,549 bytes — convert to WebP (<10 KB expected) or SVG
- Serve via `<picture>` element with AVIF + WebP + PNG fallback
- Remove the 404 banner PNG from homepage or fix/replace it

### P2 — High

**5. Add `font-display: swap` to all web font declarations**
- Edit Elementor Google Fonts self-hosted CSS files (roboto.css, robotoslab.css) to add `font-display: swap` to each `@font-face` block
- Or use a plugin such as OMGF (Optimize My Google Fonts) which handles this automatically
- Prevents FOIT; reduces LCP by allowing text to paint with fallback font

**6. Fix lazy loading on blog post images**
- 36 of 43 blog images lack `loading=lazy` — add to all images not in the initial viewport
- In WordPress/Elementor: ensure "Lazy Load" is enabled in Elementor > Settings > Performance and in the theme settings
- Expected: reduces initial page data transfer on the 450 KB blog post significantly

**7. Remove or fix the empty homepage product widget**
- Either fix the WooCommerce query (check if "Popular Picks" uses a tag/category filter that returns no results — likely due to product status mismatch or off-niche catalog drift)
- Or replace with a static curated product grid
- Eliminates wasted DB query + carousel JS overhead for zero content

**8. Enable WooCommerce cart fragments deferral**
- `cart-fragments.min.js` fires an AJAX call (`/?wc-ajax=get_refreshed_fragments`) on every page load even when cart is empty
- Disable via filter if mini-cart is not displayed on every page, or use the "cart fragments" disable option in caching plugins

### P3 — Medium

**9. Subset/remove Font Awesome 5**
- 58,071 bytes of CSS for icons — audit actual icon usage and either subset FA or switch to SVG icons for the few icons actually used
- Elementor's own eicons (21,976 B) may duplicate coverage

**10. Remove embed-any-document plugin (pdfobject.min.js + embed-public.min.js) if not needed sitewide**
- These two blocking scripts add overhead on every page including the homepage where PDFs are almost certainly not embedded

**11. Convert product images from JPG to WebP**
- WP Automatic imports Amazon JPGs — run through ShortPixel or Imagify to generate WebP versions
- WooCommerce + Hostinger CDN will serve WebP to supporting browsers automatically once `<picture>` fallbacks are set

**12. Add `width`/`height` to the one blog image missing dimensions**
- Eliminates a direct CLS contributor

**13. Audit GTM container**
- At 484 KB, the GTM payload is the largest single JS asset — audit the container for unused/redundant tags that inflate it

---

## Summary Scorecard (Lab Estimates)

| Metric | Estimated Status | Threshold |
|--------|-----------------|-----------|
| LCP | POOR (est. 4–6 s mobile) | Good: ≤2.5 s |
| INP | NEEDS IMPROVEMENT (est. 300–500 ms) | Good: ≤200 ms |
| CLS | NEEDS IMPROVEMENT (est. 0.1–0.2) | Good: ≤0.1 |
| TTFB | NEEDS IMPROVEMENT (374–410 ms) | Good: <200 ms (lab) |

**Estimated overall Lighthouse Performance score: 35–45 / 100** (mobile simulation; would be higher on desktop)

These are conservative lab estimates. Real-user CrUX data may differ. Run PageSpeed Insights on all three URLs to confirm before scoping fix effort.
