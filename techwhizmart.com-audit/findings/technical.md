# Technical SEO Findings — techwhizmart.com
**Audit date:** 2026-06-30
**Platform:** WordPress 7.0 + WooCommerce 10.8.1 + Elementor (REHub theme) + Yoast SEO
**Overall Technical Score: 38/100**

---

## Category Scores

| Category | Score | Status |
|---|---|---|
| Crawlability | 45/100 | Fail |
| Indexability | 20/100 | Fail |
| Security | 30/100 | Fail |
| URL Structure | 75/100 | Pass |
| Mobile / Viewport | 70/100 | Partial |
| Core Web Vitals (lab) | 35/100 | Fail |
| Structured Data | 60/100 | Partial |
| JavaScript Rendering | 50/100 | Partial |
| IndexNow Protocol | 0/100 | Fail |

---

## CRITICAL Issues

### C1 — 360 Trashed Products Indexable and Self-Canonicalised (Index Bloat)

**Evidence:**
- `product-sitemap.xml` contains 671 product `<loc>` entries; 360 carry the `__trashed` slug suffix (53.6% of all product URLs).
- Three sampled URLs all return HTTP 200:
  - `https://techwhizmart.com/product/marcy-smith-cage-machine-with-workout-bench-and-weight-bar-home-gym-equipment-sm-4008__trashed/` → 200
  - `https://techwhizmart.com/product/mango-steam-contemporary-modern-home-entertainment-liquor-bar-catalina-table-medium-clear__trashed/` → 200
  - `https://techwhizmart.com/product/radclo-mini-drone-with-camera-1080p-hd-fpv-foldable-drone-with-carrying-case-2-batteries-90-adjustable-lens-one-key-take-off-land-altitude-hold-360-flip-toys-gifts-for-kids-and-adu__trashed/` → 200
- Each returns a self-pointing canonical (e.g. `<link rel="canonical" href="https://techwhizmart.com/product/marcy-smith-cage-machine...__trashed/" />`), so Google treats them as independently indexable pages.
- All carry full Product + Offer + BreadcrumbList JSON-LD schema — Googlebot will crawl and attempt to index every one.
- WooCommerce's "Trash" action sets post_status = `trash` but does NOT redirect the old permalink; WordPress continues to serve the `__trashed`-suffixed URL as a live page.

**Impact:** 360 near-duplicate, stale product pages consume crawl budget, dilute PageRank across the product catalogue, risk a thin-content/low-quality signal, and expose discontinued/off-niche products (home gym machines, liquor bar tables) that contradict the site's gadget identity.

**Fix:**
1. In WordPress Admin, go to Products > Trash. Permanently delete all trashed products (Products > All Products > Trash > Select All > Delete Permanently). This causes WooCommerce to 404 the URL.
2. If any trashed products should be preserved (e.g. for historical order references), add `<meta name="robots" content="noindex">` to the `__trashed` post type template, or add a Yoast/Rank Math noindex rule for `post_status = trash` via a custom filter.
3. After deletion, submit an updated `product-sitemap.xml` to Google Search Console to expedite deindexing.
4. Alternatively (short-term): add a server-level redirect rule (`.htaccess` or Nginx) that 301-redirects any URL matching `/__trashed/` to the shop root or the nearest live category.

---

### C2 — robots.txt: Two Stacked `User-agent: *` Blocks Conflict

**Evidence (full robots.txt as fetched):**
```
User-agent: *
Disallow: /wp-content/uploads/wc-logs/
Disallow: /wp-content/uploads/woocommerce_transient_files/
Disallow: /wp-content/uploads/woocommerce_uploads/
Disallow: /*?add-to-cart=
Disallow: /*?*add-to-cart=
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php

# START YOAST BLOCK
# ---------------------------
User-agent: *
Disallow:

Sitemap: http://techwhizmart.com/sitemap_index.xml
# ---------------------------
# END YOAST BLOCK
```

The second `User-agent: *` block (Yoast-generated) has a bare `Disallow:` (empty value = allow everything). RFC 9309 and Google's robots.txt parser treat multiple records for the same user-agent as separate, each fully applicable — the most permissive applicable rule wins per Google's implementation. The practical result is that the WooCommerce Disallow rules in block 1 are effectively overridden by block 2's empty Disallow, meaning `/*?add-to-cart=` parameter URLs (which generate near-duplicate product pages) may be crawled despite the intent to block them.

Additionally, the `Sitemap:` directive in the Yoast block points to `http://techwhizmart.com/sitemap_index.xml` (HTTP, not HTTPS).

**Fix:**
1. Merge into a single `User-agent: *` block. Remove the Yoast-generated duplicate by disabling Yoast's robots.txt edit feature (Yoast SEO > Tools > File Editor) and managing robots.txt via a plugin such as "Yoast SEO: Robots.txt" editor or directly via hosting file manager.
2. Update the `Sitemap:` line to `https://techwhizmart.com/sitemap_index.xml`.

Merged recommended robots.txt:
```
User-agent: *
Disallow: /wp-admin/
Disallow: /wp-content/uploads/wc-logs/
Disallow: /wp-content/uploads/woocommerce_transient_files/
Disallow: /wp-content/uploads/woocommerce_uploads/
Disallow: /*?add-to-cart=
Disallow: /*?*add-to-cart=
Allow: /wp-admin/admin-ajax.php

Sitemap: https://techwhizmart.com/sitemap_index.xml
```

---

### C3 — Sitemap Index: All `<loc>` Entries Use HTTP (Protocol Mismatch)

**Evidence:** `sitemap_index.xml` served over HTTPS but every `<loc>` entry uses `http://`:
```xml
<loc>http://techwhizmart.com/post-sitemap.xml</loc>
<loc>http://techwhizmart.com/page-sitemap.xml</loc>
<loc>http://techwhizmart.com/product-sitemap.xml</loc>
<!-- ... all 163+ sitemaps use http:// -->
```

The individual product-sitemap.xml `<loc>` entries correctly use `https://` for product page URLs, but the index itself references child sitemaps via HTTP. Googlebot follows the 301 redirects from `http://` to `https://`, but this adds redirect hops per sitemap fetch, wastes crawl budget on redirect resolution, and creates inconsistency with the canonical HTTPS site.

**Fix:**
In Yoast SEO, ensure WordPress Address (URL) and Site Address (URL) in Settings > General both use `https://`. Yoast derives sitemap URLs from the WordPress home URL. If already set correctly, the mismatch suggests a cached sitemap. Flush Yoast's sitemap cache (Yoast SEO > Tools > clear sitemap cache) and regenerate. Verify with `curl -sI https://techwhizmart.com/sitemap_index.xml`.

---

## HIGH Issues

### H1 — Missing Security Headers (5 of 6 Critical Headers Absent)

**Evidence (from live HTTP response headers, all verified):**
| Header | Status |
|---|---|
| `Strict-Transport-Security` (HSTS) | MISSING |
| `X-Frame-Options` | MISSING |
| `X-Content-Type-Options` | MISSING |
| `Referrer-Policy` | MISSING |
| `Permissions-Policy` | MISSING |
| `Content-Security-Policy` | Partial — only `upgrade-insecure-requests` present |

The only security header present is `Content-Security-Policy: upgrade-insecure-requests`, which is a minimal directive that upgrades HTTP sub-resource requests but provides no clickjacking, MIME-sniffing, or referrer protection.

Missing HSTS is particularly important: without it, the first HTTP request from a new visitor is redirected via 301 (confirmed), but a network attacker can strip the redirect on initial load. HSTS eliminates this downgrade attack vector.

**Fix (add to `.htaccess` or Hostinger's HTTP headers panel):**
```
Strict-Transport-Security: max-age=31536000; includeSubDomains
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```
Submit the domain to the HSTS preload list (hstspreload.org) after 30 days of stable HSTS deployment.

---

### H2 — Homepage Has No H1 Tag

**Evidence:** Full homepage HTML (122,882 chars, decompressed) contains zero `<h1>` elements. The `<title>` tag is present ("TechWhizMart | Buy Cutting-Edge Gadgets & Innovative Tech Online") and the meta description is present, but there is no H1 in the rendered static HTML.

Elementor page builder renders the above-the-fold section before JS executes; if the H1 is injected by JavaScript it would not be visible in the raw HTML, but Googlebot renders JavaScript — the more likely cause is that the Elementor hero/slider widget uses an `<h2>` or styled `<div>` instead of an H1 element.

**Fix:** Edit the Elementor homepage template. Add a Heading widget set to `H1` tag for the primary page headline (e.g. "Buy Cutting-Edge Gadgets & Tech Online"). This must be in the static HTML, not hidden via CSS (a visually-hidden H1 is acceptable).

---

### H3 — Tag Archive Bloat: 164 Indexable Post Tags

**Evidence:** `post_tag-sitemap.xml` contains 164 `<loc>` entries. Tag archive pages for a blog are typically thin, single-facet listings with minimal unique content. For a gadget e-commerce site where the blog content is already off-niche (drone travel photography), these 164 tag archives compound the topical dilution.

**Fix:**
1. In Yoast SEO > Search Appearance > Taxonomies > Tags, set "Show Tags in search results" to "No" (adds `noindex` to all tag archives).
2. Remove orphaned / low-count tags from individual posts to reduce the total tag count over time.

---

### H4 — 163 `pa_*` Product Attribute Archives Indexable (Faceted Crawl Trap)

**Evidence:** `sitemap_index.xml` lists 163 `pa_*` sitemaps (e.g. `pa_age-range-description`, `pa_annual-energy-consumption`, `pa_arm-style`, `pa_audio-driver-type`, etc.). Each is a WooCommerce product attribute taxonomy archive. These generate hundreds to thousands of low-unique-content listing pages with attribute-specific URLs.

**Fix:**
1. In Yoast SEO > Search Appearance > Taxonomies, set all `Product Attribute` (`pa_*`) taxonomies to noindex.
2. Add `Disallow: /product-attribute/` to robots.txt to block crawling entirely.
3. Consider removing the `pa_*` sitemaps from the sitemap index via a Yoast filter (`wpseo_sitemap_index`).

---

### H5 — Default WordPress `sample-page` Publicly Indexed

**Evidence:**
- `https://techwhizmart.com/sample-page/` returns HTTP 200.
- `<title>Sample Page - TechWhizMart</title>`
- `<link rel="canonical" href="https://techwhizmart.com/sample-page/" />`
- No `<meta name="robots" content="noindex">` present.
- Listed in `page-sitemap.xml`.

This is the WooCommerce/WordPress default placeholder page. It has no commercial value and signals to Google that the site has not been properly configured.

**Fix:** Either delete the page in WordPress Admin > Pages, or set it to noindex in Yoast. Remove it from the sitemap.

---

## MEDIUM Issues

### M1 — Core Web Vitals: LCP Hero Image Uses JavaScript Lazy Loading (Contradicts fetchpriority)

**Evidence:**
- The hero slider images carry `fetchpriority="high"` and `decoding="async"` on the `<img>` element.
- However, the actual image URL is in `data-src`, not `src`. The `src` attribute contains a 1x1 blank GIF placeholder: `src="https://techwhizmart.com/wp-content/themes/rehub-theme/images/default/blank.gif"`.
- This means the browser's preload scanner sees a 1x1 GIF as the "high priority" image, not the actual hero image. The real image (`f-cinematic-drone-1-1024x490.jpg`) is only loaded after JavaScript executes and swaps `data-src` into `src`.
- Only 1 `<link rel="preload">` tag is present and it points to a font (rhicons.woff2), not the hero image.
- No native `loading="lazy"` is used; all lazy loading is JavaScript-driven (theme's custom lazy loader).

**Impact:** LCP element (the hero banner) cannot load until JS parses and executes, creating a guaranteed LCP delay. Field data not available (no Google API creds), but lab testing would likely show LCP > 4s (Poor) on mobile. The `fetchpriority="high"` on the blank GIF is wasted; it prioritises a 43-byte placeholder.

**Fix:**
1. For the above-the-fold hero image, set `src` (not just `data-src`) to the actual image URL and add `<link rel="preload" as="image" href="[hero-image-url]" fetchpriority="high">` in `<head>`.
2. Native lazy loading (`loading="lazy"`) should be used for below-the-fold images.
3. Configure REHub theme's lazy loading to exclude the first slider image (most themes have a "lazy load offset" or "exclude first image" option).

---

### M2 — Excessive Render-Blocking Resources in `<head>`

**Evidence:**
- 28 CSS `<link rel="stylesheet">` tags load in `<head>` (render-blocking by default).
- 7 `<script>` tags in `<head>` (some may be synchronous).
- 70 total `<script>` tags across the full page.
- 32 total stylesheets loaded.
- Sources include: REHub theme styles, Elementor, WooCommerce, content-views plugin, embed-any-document plugin, custom-post-type-pdf-attachment plugin, Google Fonts (external request to fonts.googleapis.com).

**Impact:** Each render-blocking stylesheet in `<head>` delays First Contentful Paint and LCP. 28 synchronous CSS files is very high for an e-commerce homepage.

**Fix:**
1. Enable WP Rocket, LiteSpeed Cache, or W3 Total Cache for CSS/JS minification and concatenation.
2. Load Google Fonts asynchronously: replace the `<link>` with a preconnect + preload pattern, or use `font-display: swap` in a locally-hosted font file.
3. Audit installed plugins and remove unused ones (embed-any-document, custom-post-type-pdf-attachment appear unrelated to a gadget store).
4. Use Elementor's built-in "Improved Asset Loading" (Elementor > Settings > Performance) to load CSS only on pages that use those widgets.

---

### M3 — Homepage Product Widgets Return Empty ("No products for this criteria")

**Evidence:** The decompressed homepage HTML contains the literal string "No products for this criteria." inside a widget container. This means WooCommerce product listing widgets on the homepage are misconfigured or referencing a category/tag/filter that returns no results.

**Impact:** Visitors landing on the homepage see no product recommendations. This is a conversion and content-quality issue; Googlebot also sees a content-thin homepage (6 images total, 0 H1, empty product blocks).

**Fix:** In Elementor, locate the product grid/carousel widgets and verify their query settings (category, tag, product type, stock status). Confirm WooCommerce has live (published, in-stock) products that match the widget filter.

---

### M4 — XML Sitemap Child Sitemaps Use HTTP in `sitemap_index.xml` (Repeat: Structural Detail)

Already covered under C3. Note additionally that Googlebot must resolve 163+ HTTP→HTTPS redirects when processing the sitemap index, consuming additional crawl budget on each re-crawl.

---

### M5 — WordPress XML-RPC Endpoint Exposed

**Evidence:**
- `<link rel="pingback" href="https://techwhizmart.com/xmlrpc.php">` present in homepage HTML.
- `https://techwhizmart.com/xmlrpc.php` returns HTTP 405 (Method Not Allowed for GET), confirming the endpoint is live and accepts POST requests.
- `x-robots-tag: noindex, follow` is present on that URL, preventing it from appearing in SERPs, but the endpoint itself is active.

**Impact:** XML-RPC is a vector for brute-force login attacks, DDoS amplification via pingback, and credential stuffing. It is not an SEO-blocking issue but contributes to site security posture and potential downtime risk that affects Core Web Vitals field data.

**Fix:**
1. If not using XML-RPC (Jetpack, mobile app, or REST API are the modern alternatives), disable it. Add to `.htaccess`:
   ```apache
   <Files xmlrpc.php>
     Order Deny,Allow
     Deny from all
   </Files>
   ```
2. Remove the `<link rel="pingback">` from `<head>` via `remove_action('wp_head', 'xmlrpc_rsd_link')` and related hooks, or use a plugin such as "Disable XML-RPC".

---

### M6 — Google Fonts Loaded as Render-Blocking External Request

**Evidence:** `<link rel='stylesheet' id='Poppins-css' href='//fonts.googleapis.com/css?family=Poppins:700,normal&subset=latin&ver=7.0' media='all' />`

This is a synchronous render-blocking request to an external domain (fonts.googleapis.com) that must complete before the browser can render text. DNS resolution + TLS handshake + CSS download adds 100–300ms on first load.

**Fix:** Self-host Poppins using the `google-webfonts-helper` tool and serve from the same origin. Add `font-display: swap` to the `@font-face` declaration to prevent invisible text during load (eliminates FOIT).

---

## LOW Issues

### L1 — Redirect Chain Health (PASS — Documented for Reference)

All tested redirects are single-hop 301s:
- `http://techwhizmart.com/` → 301 → `https://techwhizmart.com/` (HTTP to HTTPS: correct)
- `https://www.techwhizmart.com/` → 301 → `https://techwhizmart.com/` (www to non-www: correct)
- `https://techwhizmart.com/shop/page/1/` → 301 → `https://techwhizmart.com/shop/` (pagination canonical: correct)

No multi-hop chains detected. Redirect configuration is healthy.

---

### L2 — Mobile Viewport Declared (PASS — Minor Note)

**Evidence:** `<meta name="viewport" content="width=device-width, initial-scale=1.0" />` present in homepage `<head>`. This is correct. No `user-scalable=no` or `maximum-scale=1` restrictions detected (which would fail Google's mobile-friendliness criteria).

No mobile-specific issues beyond the LCP/render-blocking concerns already documented under M1 and M2, which affect mobile performance more severely than desktop.

---

### L3 — WP REST API JSON Endpoint Exposed

**Evidence:** `Link: <https://techwhizmart.com/wp-json/>; rel="https://api.w.org/"` in response headers, and `wp-json` referenced in homepage HTML. The REST API is publicly accessible, exposing user enumeration endpoints (`/wp-json/wp/v2/users`).

**Fix:** If not needed for front-end functionality, restrict REST API to authenticated users via a plugin (e.g. "Disable REST API") or a filter in `functions.php`:
```php
add_filter('rest_authentication_errors', function($result) {
    if (!is_user_logged_in()) {
        return new WP_Error('rest_not_logged_in', 'API access restricted.', ['status' => 401]);
    }
    return $result;
});
```

---

### L4 — IndexNow Protocol Not Implemented

**Evidence:** No IndexNow key file found, no `<meta name="indexnow-key">` tag, no IndexNow plugin active. The site does not submit URL change notifications to Bing or Yandex via IndexNow.

**Fix:** Install the "IndexNow" WordPress plugin (official, by Microsoft) or enable IndexNow in Yoast Premium (v19+). This notifies Bing and Yandex within minutes of publishing/updating content, compared to hours/days for crawl discovery.

---

### L5 — Theme Version Exposed in Asset URLs

**Evidence:** Asset URLs include version strings: `style.css?ver=19.8.4` (REHub theme version). This is low severity but aids fingerprinting for known-vulnerability targeting.

**Fix:** Filter WordPress version strings from asset URLs using `add_filter('style_loader_src', 'remove_ver_css_js', 10, 2)` or a security plugin.

---

## JavaScript Rendering Assessment

**Rendering type:** Server-Side Rendered (SSR) WordPress with client-side enhancement via Elementor.

- Static HTML (raw fetch without JS execution) yields the full page structure, navigation, and meta tags — confirming SSR. Googlebot does not need to execute JavaScript to read canonical, title, meta description, or structured data.
- However, product listing widgets (WooCommerce AJAX-powered grids) and Elementor dynamic content may depend on JavaScript for population. The homepage's empty "No products for this criteria" suggests either a JS-dependent widget returning no results, or a misconfigured server-side query.
- 70 total `<script>` tags and 344 Elementor references indicate heavy client-side dependency for interactive features, but core SEO signals are SSR-safe.
- The `is_spa: False` signal from render_page confirms this is not a client-side SPA shell.

**Verdict:** No JavaScript rendering crisis for crawlability, but the lazy-loading conflict (M1) and empty product widgets (M3) are JS-dependency issues with real SEO impact.

---

## Structured Data Summary

**Homepage schema (1 JSON-LD block, 5 types in @graph):**
- `WebPage` — present
- `ImageObject` — present
- `BreadcrumbList` — present (homepage breadcrumb)
- `WebSite` with `SearchAction` (Sitelinks Searchbox eligible) — present
- `Organization` with logo — present, but no `contactPoint` or `sameAs` social links

**Product pages:** Full `Product` + `Offer` + `UnitPriceSpecification` + `BreadcrumbList` — present on both live and trashed products (the latter being the core problem in C1).

**Gaps:**
- No `FAQPage` schema on blog posts
- No `Review`/`AggregateRating` schema on product pages (opportunity for rich results)
- Organization `sameAs` social profile links missing
- `contactPoint` missing from Organization (reduces eligibility for Knowledge Panel)

---

## Summary of Findings

| Severity | Count | Key Items |
|---|---|---|
| Critical | 3 | 360 indexable trashed products, robots.txt dual UA block, sitemap HTTP protocol |
| High | 5 | Missing security headers, no homepage H1, tag archive bloat (164), pa_* bloat (163), sample-page indexed |
| Medium | 6 | LCP lazy-load conflict, 28 render-blocking CSS, empty product widgets, XML-RPC exposed, Google Fonts blocking, sitemap redirect overhead |
| Low | 5 | Redirect health (PASS), viewport (PASS), REST API exposed, IndexNow absent, theme version fingerprint |

**Top immediate actions (by impact/effort ratio):**
1. Permanently delete or noindex 360 trashed product pages
2. Merge robots.txt into single `User-agent: *` block with HTTPS sitemap line
3. Add 5 missing security headers via `.htaccess`
4. Add H1 to homepage via Elementor
5. Noindex tag archives and `pa_*` attribute archives in Yoast
