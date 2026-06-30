# Performance / Core Web Vitals Audit — techwhizmart.com
**Date:** 2026-06-30
**Pages analysed:** Homepage (`/`) and product page (`/product/jbl-charge-4-…/`)
**Method:** Lab / heuristic — HTML source analysis, HTTP header inspection (curl), and
`scripts/preload_check.py`. **CrUX / PSI field data was unavailable** (no Google API
key configured; PSI also returned a rate-limit error during the one unauthenticated
attempt). All severity ratings are therefore based on known WordPress/WooCommerce
performance patterns, resource counts, and server-response timing measured from this
auditing environment — not from Chrome user field data. Treat LCP/INP/CLS ratings as
"likely" rather than measured.

---

## What Works

| Signal | Evidence |
|---|---|
| HTTP/2 active | `HTTP/2 200` confirmed on homepage and product page |
| Brotli compression | `content-encoding: br` on HTML responses |
| Static assets long-cached | Theme CSS: `max-age=31536000` (1 year); jQuery: `max-age=604800` (7 days) |
| HCDN CDN present | `server: hcdn` on all responses (Hostinger CDN) |
| Speculation Rules (prefetch) | Inline `<script type="speculationrules">` with conservative prefetch rule present on homepage |
| LCP image `fetchpriority="high"` | Hero and product lead image both carry `fetchpriority="high"` |
| Product image dimensions set | LCP product image has explicit `width="500" height="500"` — no CLS from that element |
| Elementor `font_display-swap` | `<meta name="generator" content="Elementor 4.1.4; … font_display-swap">` — locally-hosted Google Fonts use `font-display: swap` |
| Icon font preloaded | `<link rel="preload" … as="font" …>` for `rhicons.woff2` |
| WooCommerce scripts largely deferred | `wc-add-to-cart`, `wc-cart-fragments`, `wc-flexslider`, `wc-photoswipe`, product variation JS all carry `defer` or `data-wp-strategy="defer"` |
| `srcset` / `sizes` on images | Both hero and product images carry multi-breakpoint `srcset` |
| bfcache-compatible signals | No `cache-control: no-store`, no `unload` listener detected |

---

## Issues

### CRITICAL

#### C1 — LCP Hero Image Is Lazy-Loaded Despite `fetchpriority="high"` Conflict
**Severity:** Critical | **Affects:** Homepage LCP

The first visible image — the drone hero banner — carries both `fetchpriority="high"` and `class="lazyload"` with its real URL in `data-src`, while `src` is set to a 1×1 transparent GIF (`blank.gif`). The browser honours `fetchpriority="high"` on the GIF placeholder (43 bytes), not the real image. The lazy-load JavaScript (unveil.js / lazyload) must execute first before swapping `data-src` into `src`, adding an entire JS execution cycle to LCP load time. This is a direct contradiction that defeats both hints.

**Evidence:**
```html
<img fetchpriority="high" … 
     src="…/rehub-theme/images/default/blank.gif"
     data-src="…/uploads/2025/03/f-cinematic-drone-1-1024x490.jpg"
     class="lazyload attachment-large …" …/>
```
`preload_check.py` reports `"preload_lcp_candidate": true` and `"fetchpriority_high": 2` — but this is a false positive caused by the GIF placeholder inheriting the attribute.

**Fix:** For the first above-the-fold image, set `src` (not `data-src`) to the real image URL and remove the `lazyload` class. Add a matching `<link rel="preload" as="image" href="…drone-1-1024x490.jpg" fetchpriority="high">` in `<head>`. Convert the image to WebP/AVIF (currently JPEG at ~57 KB; WebP estimate ~30–35 KB). Keep `lazyload` only on below-the-fold images.

---

#### C2 — 30 of 43 External Scripts Load Without `defer` or `async`
**Severity:** Critical | **Affects:** Homepage and product page LCP, INP

43 external JavaScript files are present on the homepage; 30 of them have neither `defer`, `async`, nor `type="module"`. Two are in `<head>` (jQuery core + jQuery Migrate), blocking HTML parsing immediately. The remaining 28 blocking scripts are in the footer but still form a long parser-blocking chain before the browser can paint.

**Evidence — blocking scripts (30 total):**
```
jquery-core-js        jquery.min.js          (in <head>, line 98)
jquery-migrate-js     jquery-migrate.min.js  (in <head>, line 99)
rehubtablechart-js    tablechart.js
rehubcompare-js       comparechart.js
awsm-ead-pdf-object   pdfobject.min.js       (Embed Any Document)
awsm-ead-public-js    embed-public.min.js    (Embed Any Document)
pt-cv-content-views   cv.js                  (Content Views plugin)
sourcebuster-js       sourcebuster.min.js
wc-order-attribution  order-attribution.min.js
rhinview-js           inview.js
rhpgwmodal-js         pgwmodal.js
rhunveil-js           unveil.js
rhhoverintent-js      hoverintent.js
rhcountdown-js        countdown.js
rehub-js              custom.js
rhsticky-js           jquery.sticky.js
rhajaxaddtocart-js    ajaxcart.js
rhquantity-js         quantity.js
elementor-webpack     webpack.runtime.min.js
elementor-frontend-m  frontend-modules.min.js
jquery-ui-core-js     core.min.js
elementor-frontend-js frontend.min.js
owlcarousel-js        owl.carousel.min.js
owlinit-js            owlinit.js
eael-general-js       general.min.js         (Essential Addons)
rhniceselect-js       niceselect.js
rhwoodropcat-js       woodropcat.js
rehubuserlogin-js     userlogin.js
underscore-js         underscore.min.js
wp-util-js            wp-util.min.js
```
Product page adds 2 more blocking scripts: `postviews.js` (rehub-framework) and `main-front.js` (wp-automatic).

**Fix:** Use a caching/performance plugin (WP Rocket, LiteSpeed Cache, or FlyingPress) to add `defer` to all non-critical scripts. jQuery and jQuery Migrate can be deferred on most WooCommerce pages if `defer`-aware dependency handling is used. Remove or deactivate plugins whose JS is never used (Embed Any Document, Content Views, if not actively in use on the page).

---

### HIGH

#### H1 — Homepage Not Cached by CDN (DYNAMIC on Every Request)
**Severity:** High | **Affects:** TTFB, LCP

The HCDN CDN is present but `x-hcdn-cache-status: DYNAMIC` on every HTML request for both homepage and product page. `cache-control: public, max-age=0; expires: [now]` tells the CDN not to cache. Even static CSS/JS assets show `x-hcdn-cache-status: MISS` on repeat requests, meaning no edge-cache hit was observed.

Measured TTFB from auditing server (network hop included):
- Homepage: **691 ms** (total 768 ms, 122,923 bytes)
- Product page: **876 ms** (total 969 ms, 204,093 bytes)

A TTFB above 600 ms is a known LCP risk even before the browser has parsed a single byte.

**Root cause:** WooCommerce's `wc-cart-fragments` script forces a per-session AJAX call and typically prevents server-side caching unless fragment caching is used. `wc_cart_hash` cookie is set, breaking generic CDN cache rules.

**Fix:**
1. Configure page caching in LiteSpeed Cache or WP Rocket with WooCommerce cart-fragment exclusions. Cache HTML for logged-out users; bypass for cart/checkout/account pages.
2. In Hostinger hPanel, enable the Object Cache and Page Cache options if available.
3. Set `cache-control: public, max-age=300` (or higher with `stale-while-revalidate`) for homepage and category pages. Target TTFB < 200 ms from the CDN edge.

---

#### H2 — 32 Render-Blocking CSS Files in `<head>` (Homepage); 22 on Product Page
**Severity:** High | **Affects:** LCP, render delay

Every `<link rel="stylesheet">` in `<head>` with `media="all"` blocks rendering. Homepage has 32 CSS files; product page has 22.

**Homepage CSS breakdown:**
- **Theme (ReHub):** rhstyle, rhslidingpanel, rhcompare, rehubicons, rhelementor, rehub-woocommerce, rhquantity, rhcarousel, rhfilterpanel, rhniceselect (10 files)
- **Elementor:** elementor-icons, elementor-frontend, elementor-post-15, widget-image, widget-divider, widget-heading, e-shapes, elementor-post-11040, elementor-icons-shared-0, elementor-icons-fa-solid (10 files)
- **Plugins:** pt-cv-public-style (Content Views), awsm-ead-public (Embed Any Document), style_cpta_front (PDF Attachment), eael-general (Essential Addons), wc-blocks-style (WooCommerce blocks), photoswipe x2 (6 files)
- **Fonts (external):** Poppins from `fonts.googleapis.com` (1 file — render-blocking external request)
- **Fonts (HTTP, not HTTPS):** `elementor-gf-local-roboto`, `elementor-gf-local-robotoslab`, `elementor-gf-local-stylescript` loaded over **http://** — mixed-content issue
- **Inline CSS:** 14 `<style>` blocks totalling ~40 KB (unavoidable but large)

**Fix:**
1. Use a performance plugin to generate critical CSS inline and load non-critical CSS asynchronously (`media="print" onload="this.media='all'"` pattern).
2. Consolidate/merge CSS files where possible (Elementor's per-widget CSS files can be combined).
3. Load `fonts.googleapis.com/css?family=Poppins` with `<link rel="preconnect">` and add `&display=swap` to the URL.
4. Fix the three `http://` local font CSS URLs (Roboto, Roboto Slab, Style Script) — change to `https://` to prevent mixed-content blocking.

---

#### H3 — No Modern Image Formats (Zero WebP/AVIF Across Both Pages)
**Severity:** High | **Affects:** LCP, page weight

Zero WebP or AVIF images detected on either homepage or product page. All images are JPEG or PNG.

**Evidence:**
- Hero banner: JPEG (`f-cinematic-drone-1-1024x490.jpg`), `content-type: image/jpeg`, `content-length: 58,506 bytes`. WebP equivalent expected ~30–35 KB (40–45% saving).
- Product images (26 total): All JPEG sourced from Amazon CDN paths proxied through `wp-content/uploads/https://m.media-amazon.com/…` — not locally optimised images, not converted to WebP.
- Logo: PNG (`Screenshot_2025-01-29…-removebg-preview.png`) with empty `width=""` and `height=""` attributes — see CLS risk below.
- Favicon images: PNG format (`cropped-Screenshot-2025-01-29…`).

**Fix:**
1. Install and configure an image optimisation plugin (Imagify, ShortPixel, or WebP Express) to auto-convert uploads to WebP/AVIF and serve with `<picture>` or `.htaccess` content-negotiation.
2. For product images pulled from Amazon via `wp-automatic`, the plugin configuration should download and locally store images so they can be processed; serving proxied Amazon CDN URLs prevents any local optimisation and adds an extra DNS resolution step.
3. Convert logo to SVG or WebP; set explicit pixel dimensions on the `<img>` tag.

---

### MEDIUM

#### M1 — Logo Image Missing `width` and `height` Attributes (CLS Risk)
**Severity:** Medium | **Affects:** CLS

The site logo `<img>` on both pages has `height="" width=""` — empty strings, not numeric values. The browser cannot reserve layout space before the image loads, causing a layout shift when it paints.

**Evidence:**
```html
<img src="…/Screenshot_2025-01-29_at_5.24.07_AM-removebg-preview.png"
     alt="TechWhizMart" height="" width="" />
```

**Fix:** Set explicit pixel values (`width="238" height="50"` or whatever the rendered dimensions are) or switch to an SVG logo with an intrinsic `viewBox`.

---

#### M2 — Google Fonts External Request Without `preconnect` + Missing `display=swap` Parameter
**Severity:** Medium | **Affects:** LCP (font render delay / FOUT)

The Poppins font is requested from `fonts.googleapis.com` without a `display=swap` parameter in the URL and without a `<link rel="preconnect">` hint. While Elementor's locally-hosted fonts use `font-display: swap`, the Yoast/theme-registered Poppins CSS (`id='Poppins-css'`) uses `//fonts.googleapis.com/css?family=Poppins:700,normal&subset=latin` — missing `&display=swap`. This is a separate, earlier-loading request that can block text rendering.

**Evidence:**
```html
<link rel='stylesheet' id='Poppins-css'
      href='//fonts.googleapis.com/css?family=Poppins:700,normal&subset=latin&ver=7.0'
      media='all' />
```
No `<link rel="preconnect" href="https://fonts.googleapis.com">` found in `<head>`.

**Fix:**
1. Add `<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>` and `<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>` early in `<head>`.
2. Change the stylesheet URL to add `&display=swap`.
3. Ideally, subsume this into Elementor's local font mechanism (already configured) and deregister the redundant theme-level Google Fonts request.

---

#### M3 — WordPress Emoji Script Loads on All Pages
**Severity:** Medium | **Affects:** INP (main thread), HTTP requests

The WordPress emoji detection script (`wp-emoji-release.min.js`) is present on every page. It runs a canvas-based emoji support test using a `Worker` + `OffscreenCanvas` on page load, adding main-thread and network overhead on pages that are not blog posts and have no emoji content.

**Evidence:** Lines 981–984 of home.html contain the full inline emoji detection code that dynamically loads `wp-emoji-release.min.js` from `s.w.org`.

**Fix:** Add `remove_action('wp_head', 'print_emoji_detection_script', 7); remove_action('wp_print_styles', 'print_emoji_styles');` to `functions.php`, or use a performance plugin option to disable emoji support site-wide.

---

#### M4 — Unused Plugin Assets Loaded on Irrelevant Pages
**Severity:** Medium | **Affects:** LCP, page weight

Several plugin CSS/JS files are loaded on pages where they serve no function:

| Plugin | Asset | Loaded On | Needed? |
|---|---|---|---|
| Embed Any Document | `embed-public.min.js`, `embed-public.min.css` | Homepage | No PDF embeds on homepage |
| Content Views | `cv.js`, `cv.css` | Homepage | No content-view shortcodes visible |
| Custom Post Type PDF Attachment | `style_front.css` | Homepage | No PDF attachments on homepage |
| WP Automatic | `main-front.js`, `wp-automatic.css` | Product page | Auto-import plugin JS not needed front-end |

**Fix:** Use Asset CleanUp Pro or WP Rocket's asset management to conditionally load these only on pages where the shortcode or post type is actually present.

---

#### M5 — Product Page TTFB 876 ms; HTML Payload 204 KB
**Severity:** Medium | **Affects:** LCP (resource load delay)

The product page HTML is 204 KB uncompressed (Brotli reduces in-flight size, but the browser must still decompress and parse it). Combined with the 876 ms TTFB, the browser cannot begin parsing the document until nearly 1 second has elapsed. Industry guidance targets TTFB < 200 ms for a "good" LCP subpart.

The `wc_cart_fragments` AJAX call on page load also adds a second network round-trip for every visitor.

**Fix:** Page caching (see H1). Consider removing `cart-fragments` and replacing with a server-side cart count approach (the `woocommerce_add_to_cart_fragments` filter can be disabled for non-logged-in users if the cart icon only shows a count).

---

### LOW

#### L1 — Three Local Font CSS Files Served Over HTTP (Mixed Content)
**Severity:** Low | **Affects:** Security / browser warnings, potential blocking in strict environments

Three Elementor-localised Google Font CSS files use `http://` scheme:
```
http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/roboto.css
http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/robotoslab.css
http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/stylescript.css
```
Browsers on HTTPS pages will either upgrade these (the `Content-Security-Policy: upgrade-insecure-requests` header is present, so they should be upgraded automatically) or block them. The CSP upgrade directive mitigates the immediate breakage but the source URLs should be corrected at the Elementor level.

**Fix:** In Elementor > Settings > Advanced, regenerate CSS files. If regeneration does not fix the scheme, update `siteurl` in `wp_options` to ensure all internal URLs resolve to `https://`.

---

#### L2 — CDN Edge Nodes Not Caching Static Assets (Repeated MISS)
**Severity:** Low | **Affects:** Returning-visitor performance

Two consecutive `curl -I` requests to `style.css` (versioned URL) both returned `x-hcdn-cache-status: MISS`. This may be a CDN warm-up issue (cold edges in the audit environment's geo-location), but if consistent for real users it means every static asset requires an origin fetch.

**Fix:** In Hostinger's hPanel CDN settings, verify that `wp-content/` paths are included in the CDN ruleset. Check that the `Vary: Accept-Encoding` response header is not preventing caching for brotli-encoded assets.

---

## Summary Table

| ID | Issue | Severity | Expected LCP Impact |
|---|---|---|---|
| C1 | LCP hero lazy-loaded (GIF placeholder defeats fetchpriority) | Critical | Very High |
| C2 | 30/43 scripts blocking without defer/async | Critical | High |
| H1 | HTML not CDN-cached; TTFB 691–876 ms | High | High |
| H2 | 32 render-blocking CSS files in `<head>` | High | High |
| H3 | Zero WebP/AVIF images; all JPEG/PNG | High | Medium |
| M1 | Logo `width`/`height` empty (CLS risk) | Medium | CLS |
| M2 | Google Fonts without preconnect/display=swap | Medium | Medium |
| M3 | WordPress emoji script on all pages | Medium | Low |
| M4 | Unused plugin CSS/JS on non-relevant pages | Medium | Low |
| M5 | Product page 204 KB HTML + 876 ms TTFB | Medium | Medium |
| L1 | Local font CSS via http:// (mixed content) | Low | None |
| L2 | CDN static asset MISS on repeat requests | Low | Low |

---

## Data Availability Note

**CrUX field data:** Not available. No Google API key is configured. Chrome User Experience
Report (28-day p75 LCP/INP/CLS) cannot be retrieved. PSI also returned a rate-limit error
on an unauthenticated attempt. **All assessments above are lab/heuristic** — derived from
HTML source inspection, `curl` header measurements, resource counts, and known
WordPress/WooCommerce performance patterns. To obtain real Core Web Vitals verdicts,
configure a Google API key and run `python3 scripts/pagespeed_check.py https://techwhizmart.com/ --json`
or visit https://cruxvis.withgoogle.com for field data.

---

## Recommended Priority Order

1. **Fix C1 immediately** — remove `lazyload` class from the hero `<img>`, set the real image in `src`, add `<link rel="preload" as="image">` in `<head>`. This single change is the highest-leverage LCP improvement possible without infrastructure changes.
2. **Enable page caching (H1)** — LiteSpeed Cache or WP Rocket with WooCommerce cart exclusions. Target TTFB < 200 ms from CDN edge.
3. **Defer non-critical JS (C2)** — use WP Rocket / FlyingPress JS optimisation to add `defer` to all footer scripts. Remove or conditionally load unused plugin JS (M4).
4. **Convert images to WebP (H3)** — enable automatic WebP generation for uploads; for product images imported by wp-automatic, configure local storage and format conversion.
5. **Load CSS asynchronously / extract critical CSS (H2)** — generate above-the-fold critical CSS inline; defer the remaining 20+ stylesheets.
