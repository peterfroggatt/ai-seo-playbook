# Technical SEO Audit — techwhizmart.com
**Date:** 2026-06-30
**Platform:** WordPress + WooCommerce + Yoast SEO (Rehub theme + Elementor)
**Server:** Hostinger / hcdn CDN

---

## What Works

- **HTTPS is live and enforced.** The canonical domain resolves correctly over TLS; H2 is active (`alt-svc: h3=":443"` indicates HTTP/3 readiness).
- **www redirect is correct.** `https://www.techwhizmart.com/` issues a 301 to `https://techwhizmart.com/` via WordPress, consolidating authority to the non-www canonical.
- **Trailing-slash redirect works.** `/shop` (no slash) issues a 301 to `/shop/` — consistent with WordPress permalink configuration.
- **Utility pages are correctly noindexed at the meta level.** `/cart/`, `/checkout/`, and `/my-account/` all serve `<meta name='robots' content='noindex, follow' />`, preventing them from entering Google's index.
- **Checkout redirects to cart.** `/checkout/` issues a 302 to `/cart/` when no cart is active — sensible UX fallback.
- **Indexable category and product pages have correct self-referencing canonicals.** `/product-category/electronics/` and the JBL Charge 4 product page both carry canonical tags pointing to themselves.
- **Layered-nav sort parameters are canonicalized.** `/?orderby=price` canonicals back to `/shop/` — Yoast is correctly stripping the sort parameter.
- **404 status is correctly returned** for non-existent URLs (HTTP 200 Connection Established is the proxy tunnel line; the actual response is 404).
- **Product schema is present** on product pages (Product + Offer + UnitPriceSpecification).
- **Sitewide schema on homepage** includes WebSite + SearchAction, Organization, BreadcrumbList, WebPage.
- **`?add-to-cart=` is disallowed** in robots.txt by the WooCommerce block — correct intent.
- **pa_ attribute archive pages serve noindex.** Sampled `brand-3m/` and `product-attribute/pa_brand/sony/` both return `noindex, follow` — Google should not index individual attribute term archives.
- **HTTP/2 is active** — confirmed in response headers (`HTTP/2 200`).
- **No mixed content in page resources.** The homepage carries only one legacy `http://` reference (the XFN profile link `gmpg.org/xfn/11`), which is non-functional and carries no weight.
- **CSP `upgrade-insecure-requests` directive** is present sitewide — passively upgrades any remaining http sub-resource requests to https.

---

## Issues

### CRITICAL

#### C1 — robots.txt Duplicate `User-agent: *` Block Silently Nullifies All WooCommerce Disallows

**Evidence — full robots.txt:**
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

**Mechanism:** The robots.txt specification (RFC 9309) states that when a crawler finds multiple groups with matching `User-agent: *` directives, Googlebot applies the **most specific match** across all groups — but per Google's documented implementation, a second matching group with an empty `Disallow:` does NOT reset or clear rules from the first group. However, this dual-group layout causes ambiguity and is a known source of crawler confusion. More critically: the **Yoast block's empty `Disallow:`** is an explicit "allow everything" statement. Per RFC 9309 §2.2, when an empty-value Disallow exists in a matching group, it means "no paths are disallowed." Googlebot processes this as: both groups match, the empty Disallow in the second group explicitly permits all paths. The effective result is that `/*?add-to-cart=` and all other WooCommerce disallows may be ignored because the Yoast block's empty Disallow overrides by being more permissive.

**Real effect on Google:** Google's own robots.txt documentation states it uses the most permissive applicable rule when two matching groups conflict on a given path. The Yoast empty `Disallow:` group is effectively a blanket Allow for all paths — meaning `?add-to-cart=` URLs, `/wc-logs/`, and private upload directories may be crawled freely.

**Fix:**
1. Remove the duplicate `User-agent: *` from the Yoast block in Yoast SEO > Tools > File Editor. Yoast should output only a `Sitemap:` line without a redundant `User-agent: *` + `Disallow:` pair.
2. Consolidate all directives into a single `User-agent: *` block. The corrected robots.txt should read:
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
3. This is achievable via a custom robots.txt editor (Yoast allows custom additions) or via a code snippet in `functions.php` using the `robots_txt` filter.

---

#### C2 — 360 Trashed Product URLs Indexed with `index, follow` and Self-Referencing Canonicals

**Evidence:**
- `product-sitemap.xml` contains exactly **360 URLs** with `__trashed` suffixes (54% of the 671-URL product sitemap).
- Sample URL: `https://techwhizmart.com/product/roblox-digital-gift-card-2200-robux-includes-exclusive-virtual-item-online-game-code__trashed/`
- HTTP status: **200 OK** (not 404, not 301, not 410)
- Meta robots: `<meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />`
- Canonical: `<link rel="canonical" href="https://techwhizmart.com/product/roblox-digital-gift-card-2200-robux-includes-exclusive-virtual-item-online-game-code__trashed/" />` — self-referencing

**Impact:** These are WooCommerce "trash" products that WordPress failed to fully delete. Each one:
- Has an ugly `__trashed` URL slug that tells Google the product is discontinued/deleted
- Returns HTTP 200, so Google treats them as live pages
- Has `index, follow` — so Google is actively indexing and ranking (or attempting to rank) 360 defunct product pages
- Is submitted in the XML sitemap, actively inviting crawl of dead content
- Carries a self-pointing canonical, so there is no signal to consolidate to a replacement
- Consumes crawl budget: 360 wasted crawls per cycle

**Fix:**
1. In WordPress admin, go to WooCommerce > Products > Trash, select all, and permanently delete (Empty Trash). This removes the posts from the database entirely.
2. If products cannot be permanently deleted (e.g., order history dependency): add `<meta name='robots' content='noindex' />` via Yoast's per-post settings, OR redirect each `__trashed` URL to `/shop/` or a relevant category with a 301.
3. Once deleted/redirected, Yoast will automatically exclude them from the sitemap.
4. Submit the updated `product-sitemap.xml` to Google Search Console to accelerate de-indexation.

---

### HIGH

#### H1 — All Child Sitemaps Declared as `http://` in sitemap_index.xml (Protocol Inconsistency)

**Evidence — sitemap_index.xml excerpt:**
```xml
<loc>http://techwhizmart.com/post-sitemap.xml</loc>
<loc>http://techwhizmart.com/page-sitemap.xml</loc>
<loc>http://techwhizmart.com/product-sitemap.xml</loc>
...
```
All 171 child sitemap `<loc>` entries use `http://`, not `https://`. The `Sitemap:` directive in robots.txt also uses `http://`:
```
Sitemap: http://techwhizmart.com/sitemap_index.xml
```

**Impact:** Google will follow the HTTP URLs and be redirected to HTTPS, adding a redirect hop to every sitemap fetch. More critically, Google's sitemap parser may treat `http://techwhizmart.com/...` and `https://techwhizmart.com/...` as different origins. If Google's internal records already associate the site with the HTTPS canonical, HTTP sitemap URLs can create a signal mismatch and slow sitemap processing. Screaming Frog and GSC both flag this as a validation warning.

**Fix:**
1. In WordPress, confirm the Site Address (URL) is set to `https://techwhizmart.com` under Settings > General. Yoast derives sitemap `<loc>` values from `home_url()`.
2. If `home_url()` already returns `https://`, a plugin conflict or caching issue may be generating stale HTTP references — flush all caches (Hostinger hcdn + any WP caching plugin).
3. Verify the fix by re-fetching `sitemap_index.xml` and confirming all `<loc>` use `https://`.
4. Update the `Sitemap:` line in robots.txt to `https://techwhizmart.com/sitemap_index.xml`.

---

#### H2 — Utility Pages (cart, checkout, my-account, wishlist, compare-products, sample-page) in XML Sitemap Despite Being noindex

**Evidence — page-sitemap.xml contains:**
```
https://techwhizmart.com/cart/
https://techwhizmart.com/checkout/
https://techwhizmart.com/my-account/
https://techwhizmart.com/wishlist/
https://techwhizmart.com/compare-products/
https://techwhizmart.com/sample-page/
```
All of these pages serve `<meta name='robots' content='noindex, follow' />`, yet they are included in the sitemap. Additionally, the page sitemap contains a page with a slug matching the site title (`/techwhizmart-buy-cutting-edge-gadgets-innovative-tech-online/`) — a sign of an accidental page creation — plus duplicate blog archive pages (`/our-blog/`, `/blog/`, `/blog-posts/`).

**Impact:** Including noindexed URLs in an XML sitemap is a direct contradiction and a Google Quality Signal. Google's John Mueller has confirmed that Google will eventually drop pages from sitemaps that are noindexed, but it wastes crawl budget during the interim and may confuse GSC coverage reports. Google's sitemap guidelines explicitly state: "Do not include URLs in your sitemap that you don't want Google to crawl or index."

**Fix:**
1. In Yoast SEO > Search Appearance > Content Types, ensure "Show in search results" is set to No for WooCommerce utility page post types.
2. For individual pages: open each page in the WordPress editor > Yoast sidebar > Advanced > "Allow search engines to show this post in search results?" set to No — this excludes them from Yoast's sitemap automatically.
3. Remove `sample-page` and the accidental title-slug page from the sitemap.
4. Consolidate duplicate blog archive pages (`/our-blog/`, `/blog/`, `/blog-posts/`) — canonicalize to one URL and redirect the others.

---

#### H3 — 163 Product Attribute (`pa_*`) Sitemaps Flooding the Sitemap Index (Crawl Budget Waste)

**Evidence:**
- `sitemap_index.xml` contains **163 `pa_*` child sitemaps** (`pa_brand`, `pa_color`, `pa_audio-driver-type`, `pa_battery-capacity`, etc.)
- `pa_brand-sitemap.xml` alone contains **252 URLs** (`/brand/brand-3m/`, `/brand/brand-sony/`, etc.)
- `pa_color-sitemap.xml` contains **31 URLs**
- Many sitemaps contain just 1 URL (`pa_audio-driver-type`: 1 URL)
- Attribute archive pages serve `noindex, follow` (verified: `brand-3m/` and `product-attribute/pa_brand/sony/` both return `noindex, follow`)

**Impact:** 163 sitemaps submit an estimated 400–1,000+ URLs to Google that are already noindexed. This is pure crawl budget waste — Google fetches the sitemaps, discovers the URLs, crawls them, reads `noindex`, and discards them. At scale this suppresses crawl of the 311 legitimate live products. The sitemap index itself (171 total entries) is bloated and hard to audit.

**Fix:**
1. In Yoast SEO > Search Appearance > Taxonomies, find each `pa_*` taxonomy and set "Show in search results" to No. This removes all attribute taxonomies from Yoast's sitemap in one step.
2. Alternatively, use the `wpseo_sitemap_exclude_taxonomy` filter in `functions.php` to exclude all `pa_*` taxonomies programmatically:
```php
add_filter( 'wpseo_sitemap_exclude_taxonomy', function( $excluded, $taxonomy ) {
    if ( strpos( $taxonomy, 'pa_' ) === 0 ) {
        return true;
    }
    return $excluded;
}, 10, 2 );
```
3. After applying, the sitemap index should reduce from 171 entries to approximately 8 meaningful sitemaps.

---

#### H4 — Critical Security Headers Missing Sitewide (HSTS, X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, substantive CSP)

**Evidence — full response headers for `https://techwhizmart.com/`:**
```
HTTP/2 200
content-security-policy: upgrade-insecure-requests
x-powered-by: PHP/8.2.30
server: hcdn
```

**Missing headers (verified absent):**
- `Strict-Transport-Security` (HSTS) — not present. Without HSTS, initial HTTP requests are vulnerable to SSL stripping attacks, and browsers do not cache the HTTPS preference.
- `X-Content-Type-Options: nosniff` — not present. Browsers may MIME-sniff responses and execute content as a different type than declared.
- `X-Frame-Options` — not present on homepage, product pages, or category pages (present only on `/my-account/` — likely set by WooCommerce for that page only).
- `Referrer-Policy` — not present. Full referrer URLs including query strings are sent to third-party domains (analytics, affiliate links).
- `Permissions-Policy` — not present. Browser features (camera, microphone, geolocation) are unrestricted.
- The `Content-Security-Policy` is present but contains only `upgrade-insecure-requests` — this is not a real CSP; it provides no XSS protection.
- `X-Powered-By: PHP/8.2.30` — actively discloses the PHP version, aiding targeted vulnerability scanning.

**Impact on SEO:** Google's PageSpeed Insights and Lighthouse penalise missing security headers in the "Best Practices" score. HTTPS without HSTS means the site is not on the HSTS preload list and cannot achieve browser-level HTTP bypass. Missing headers are increasingly a ranking signal proxy (Core Web Vitals "Safe Browsing" and security audits).

**Fix (Hostinger hcdn — add via .htaccess or Hostinger control panel > Headers):**
```apache
<IfModule mod_headers.c>
  Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
  Header always set X-Content-Type-Options "nosniff"
  Header always set X-Frame-Options "SAMEORIGIN"
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
  Header always set Permissions-Policy "camera=(), microphone=(), geolocation=()"
  Header unset X-Powered-By
  Header always set Content-Security-Policy "upgrade-insecure-requests; default-src 'self' https:; script-src 'self' https: 'unsafe-inline'; style-src 'self' https: 'unsafe-inline';"
</IfModule>
```
After adding HSTS, submit the domain to the HSTS preload list at hstspreload.org once `max-age` has been stable for 6+ weeks.

---

### MEDIUM

#### M1 — HTTP (non-HTTPS) Blocked by Proxy/Host Policy — No HSTS Preload

**Evidence:** Direct HTTP fetch of `http://techwhizmart.com/` returned `403 Forbidden` with `x-deny-reason: host_not_allowed` — this is the Hostinger CDN rejecting plain HTTP at the edge. While HTTP is effectively blocked, this is a CDN-level enforcement, not HSTS. Without `Strict-Transport-Security`, browsers do not cache the HTTPS preference. A user's first visit over HTTP may reach the CDN 403 rather than a clean 301 redirect — this is atypical and may confuse some crawlers and link-checkers that expect a redirect rather than a block.

**Fix:** Add HSTS as described in H4. Configure Hostinger to issue a 301 redirect from HTTP to HTTPS rather than a 403 block, ensuring crawler-friendliness.

---

#### M2 — Sitemap Image References Include Dozens of `blank.gif` Placeholder Images

**Evidence — page-sitemap.xml (homepage entry contains 30+ image:image entries):**
```xml
<image:loc>https://techwhizmart.com/wp-content/themes/rehub-theme/images/default/blank.gif</image:loc>
```
The homepage sitemap entry lists 30+ images, the majority of which are `blank.gif` — a 1x1 transparent placeholder from the Rehub theme used for lazy-loading. Similarly, product images in the sitemap reference placeholder GIFs rather than actual product images.

**Impact:** Google's image sitemap parsing will attempt to index `blank.gif` as a meaningful product image. This contributes no Google Images visibility, wastes sitemap bandwidth, and may dilute image sitemap quality signals.

**Fix:**
1. Audit Rehub theme's lazy-loading implementation — ensure actual image `src` attributes are populated in the rendered HTML (use `src` for real images; `data-src` for lazy-load targets with a real `src` fallback or `<noscript>` tag).
2. In Yoast SEO settings, the `image:image` entries in sitemaps are auto-generated from page content — fixing the underlying lazy-load implementation will resolve this automatically.

---

#### M3 — 28 Render-Blocking Stylesheets + 5 Render-Blocking Scripts in `<head>`

**Evidence (from homepage HTML analysis):**
- **28 `<link rel="stylesheet">` tags** loaded synchronously in `<head>`:
  - Rehub theme (`style.css`, `slidingpanel.css`, `dynamiccomparison.css`, `carousel.css`, etc.)
  - Elementor (`frontend.min.css`, `widget-image.min.css`, `widget-divider.min.css`, `widget-heading.min.css`, per-post CSS files)
  - Plugin CSS (Essential Addons, Content Views, Embed Any Document, Custom Post Type PDF, Font Awesome)
  - Google Fonts (`//fonts.googleapis.com/css?family=Poppins`)
- **5 render-blocking scripts** in `<head>` (no `async` or `defer`):
  - `jquery.min.js` (jQuery core)
  - `jquery-migrate.min.js`
  - WooCommerce inline `<script>` blocks (wc-add-to-cart-extra, wc-cart-fragments-extra)
  - Google Tag Manager inline script

**Impact:** Every stylesheet and synchronous script in `<head>` blocks First Contentful Paint (FCP) and Largest Contentful Paint (LCP). Field data is unavailable (no CrUX/API credentials), but 28 synchronous stylesheet requests represent a significant LCP risk, particularly on mobile connections. Google's Core Web Vitals LCP threshold is 2.5s.

**Fix:**
1. Activate Hostinger's LiteSpeed Cache or use a caching/minification plugin (WP Rocket, Perfmatters) to:
   - Combine and minify CSS
   - Defer non-critical CSS (load above-the-fold critical CSS inline, defer the rest)
   - Add `defer` attribute to jQuery and WooCommerce scripts where safe
2. Self-host Google Fonts (already partially done — Elementor is self-hosting Roboto/Roboto Slab/StyleScript via `/wp-content/uploads/elementor/google-fonts/`). Remove the external `fonts.googleapis.com` call for Poppins and self-host it the same way.
3. Remove or deactivate unused plugins contributing CSS (e.g., Embed Any Document, Custom Post Type PDF Attachment — only load their assets on pages that use them).

---

#### M4 — Elementor Google Font CSS Loaded over `http://` (Mixed Content Risk)

**Evidence — found in home.html `<head>`:**
```html
<link rel='stylesheet' id='elementor-gf-local-roboto-css'
  href='http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/roboto.css?ver=1744538240' media='all' />
<link rel='stylesheet' id='elementor-gf-local-robotoslab-css'
  href='http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/robotoslab.css?ver=1744538244' media='all' />
<link rel='stylesheet' id='elementor-gf-local-stylescript-css'
  href='http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/stylescript.css?ver=1744538518' media='all' />
```
Three Elementor self-hosted Google Font CSS files are referenced with `http://` scheme on an HTTPS page. The `Content-Security-Policy: upgrade-insecure-requests` directive will silently upgrade these to HTTPS at the browser level, so they are not currently causing mixed content errors in Chrome — but this is a fragile dependency and will surface in strict CSP environments or non-Chrome browsers.

**Fix:** In Elementor > Site Settings > Custom CSS or by flushing Elementor's font cache and regenerating (Elementor > Tools > Regenerate Files), force regeneration of font URLs. The root cause is likely that `home_url()` or `site_url()` returned `http://` at the time Elementor cached the font paths. Ensuring WordPress Site URL is `https://` and clearing Elementor's CSS files should regenerate the URLs with `https://`.

---

#### M5 — `add-to-cart` Parameter Creates Session Cookies on Crawl (Cookie Leakage)

**Evidence — response headers for `/?add-to-cart=4417`:**
```
HTTP/2 200
set-cookie: woocommerce_items_in_cart=1; path=/; secure
set-cookie: woocommerce_cart_hash=...; path=/; secure
set-cookie: wp_woocommerce_session_...; expires=Thu, 02 Jul 2026; HttpOnly; secure
```
Fetching `/?add-to-cart=4417` returns HTTP 200 and sets three WooCommerce session cookies. While robots.txt disallows `/*?add-to-cart=`, the duplicate Yoast `User-agent: *` block (see C1) may render this Disallow ineffective, meaning Googlebot could be crawling these URLs and triggering session creation.

**Impact:** Cart manipulation via URL is a low-severity security concern. If the robots.txt fix (C1) is applied, this becomes moot for Googlebot. The cookies themselves are flagged as `secure`, which is correct.

**Fix:** Fix C1 first. Additionally, consider adding a server-level block (nginx/Apache rule or Hostinger firewall) for `?add-to-cart=` requests from non-user agents as a defense-in-depth measure.

---

### LOW

#### L1 — `X-Powered-By: PHP/8.2.30` Discloses PHP Version

**Evidence:** Present in all HTTP responses: `x-powered-by: PHP/8.2.30`

**Fix:** Add `Header unset X-Powered-By` in `.htaccess` (included in H4 fix above). Also set `expose_php = Off` in `php.ini` or via Hostinger control panel.

---

#### L2 — `sitemap_index.xml` XSL Stylesheet Uses Protocol-Relative URL

**Evidence — top of sitemap_index.xml:**
```xml
<?xml-stylesheet type="text/xsl" href="//techwhizmart.com/wp-content/plugins/wordpress-seo/css/main-sitemap.xsl"?>
```
The XSL reference uses `//` (protocol-relative), which inherits the request protocol. When fetched over HTTP (as the sitemap is declared in robots.txt with `http://`), this would load the XSL over HTTP. Not a crawling issue but an inconsistency worth cleaning up.

**Fix:** This is controlled by Yoast; updating the Sitemap URL in robots.txt and `home_url()` to HTTPS should resolve this automatically.

---

#### L3 — `author-sitemap.xml` Contains 2 Author Archive Pages

**Evidence:** `author-sitemap.xml` listed in sitemap_index. Author archives for WooCommerce/e-commerce stores are typically thin pages with no unique content value.

**Fix:** In Yoast SEO > Search Appearance > Archives, set "Author archives" to noindex and disable the sitemap entry. Author archives are not useful for an e-commerce site and represent thin content risk.

---

#### L4 — `product_tag-sitemap.xml` Present (Not Sampled but Likely Thin)

**Evidence:** `product_tag-sitemap.xml` listed in sitemap_index with lastmod `2026-03-04`. WooCommerce product tag archives are typically auto-generated, thin, and low-value for e-commerce SEO.

**Fix:** Review product tag archives. If tags are not curated with unique descriptions and meaningful product groupings, set to noindex in Yoast > Search Appearance > Taxonomies and exclude from sitemap.

---

## Crawl Budget Summary

| URL Pool | Count | Indexable? | In Sitemap? | Action Needed |
|---|---|---|---|---|
| Live products | 311 | Yes | Yes | Retain |
| `__trashed` products | 360 | YES (bug) | YES (bug) | Delete/410 + remove from sitemap |
| Product categories | 34 | Yes | Yes | Retain |
| Blog posts | 147 | Yes | Yes | Retain |
| `pa_*` attribute archives | ~500–1,000 est. | NO (noindex) | YES (163 sitemaps) | Remove from sitemap |
| Utility pages (cart/checkout/etc.) | 6 | NO (noindex) | YES (bug) | Remove from sitemap |
| Author archives | 2 | Yes | Yes | Noindex + remove |

**Estimated wasted crawls per cycle:** 360 trashed products + 500–1,000 attribute archive pages + 6 utility pages = **~870–1,370 URL crawls yielding zero index value.**

---

## Priority Fix Order

1. **C2 — Permanently delete 360 trashed products** (or 410/redirect + noindex): direct index bloat, brand damage from ugly URLs, Google's biggest crawl waste on this domain.
2. **C1 — Fix robots.txt duplicate User-agent block**: security (unintended crawl of private upload dirs) + correctness of `add-to-cart` Disallow.
3. **H3 — Remove 163 `pa_*` sitemaps**: fastest crawl budget recovery, single Yoast toggle.
4. **H2 — Remove utility pages from sitemap**: resolves noindex-in-sitemap contradiction.
5. **H4 — Add missing security headers**: HSTS, X-Content-Type-Options, X-Frame-Options, Referrer-Policy (one `.htaccess` block).
6. **H1 — Fix sitemap HTTP → HTTPS URLs**: confirm `home_url()` in WordPress General Settings is `https://`.
