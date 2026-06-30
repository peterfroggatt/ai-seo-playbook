# E-Commerce SEO Findings — techwhizmart.com

**Audit date:** 2026-06-30
**Platform:** WordPress + WooCommerce + Yoast SEO (Rehub theme)
**Data source:** On-page analysis (static + live fetches). Marketplace/live-SERP data unavailable — no DataForSEO MCP or Merchant API credentials configured.
**Pages analysed:** JBL Charge 4 product page (pre-fetched), /shop/, /shop/?orderby=price, /product-category/audio/, /product-category/audio/speakers/, second product page attempt (Beats Studio Buds — returned empty, product likely trashed).

---

## What Works

- **Product schema present:** A standalone `Product` JSON-LD block (separate from the Yoast `@graph`) is emitted server-side for the JBL Charge 4, containing `name`, `description`, `image`, `sku`, `offers` with `price`, `priceCurrency`, `availability` (InStock), `priceValidUntil`, and `seller`. This satisfies the minimum Google merchant-listing fields and means the page is theoretically eligible for the Shopping tab free listing.
- **Canonical tags on faceted URLs:** The `?orderby=price` variant canonicalises correctly back to `/shop/` — Yoast is handling sort-order parameters, preventing that specific duplicate.
- **Breadcrumb schema present:** `BreadcrumbList` in the Yoast `@graph` is well-formed (Home > Shop > Product name), enabling rich breadcrumb display in SERPs.
- **Product H1 present and matches title tag:** The JBL Charge 4 page has exactly one H1 ("JBL Charge 4 – Waterproof Portable Bluetooth Speaker – Black"), matching the `<title>` product segment.
- **Related products section:** Six related product links with titles and images are rendered server-side in a "Related Products" block, providing some internal link depth within the catalogue.
- **Lazy-loading on images:** `loading="lazy"` is applied to below-fold product and related-product images — correct for Core Web Vitals.
- **OG tags present:** `og:title`, `og:description`, `og:image`, `og:url` all present on product pages.
- **Category pages are indexable and canonicalised:** `/product-category/audio/` and `/product-category/audio/speakers/` return HTTP 200 with self-referencing canonicals and `index, follow` robots meta — no accidental noindex on core category pages.

---

## Issues

### CRITICAL

#### C-1: Product schema missing `brand`, `gtin`, `mpn`, `condition`, `review/aggregateRating` — blocks Shopping rich results and merchant-listing eligibility

**Evidence:**
The Product JSON-LD block for the JBL Charge 4 contains only:
```
name, url, description, image, sku (= WP post ID 12955, not a real SKU),
offers > price / priceCurrency / availability / priceValidUntil / seller
```
Missing fields:
- `brand` — required by Google for Shopping free listings on branded goods
- `gtin` / `gtin13` / `gtin8` — strongly recommended; without it Google cannot match to its product catalogue for Shopping tab placement
- `mpn` — manufacturer part number; needed when GTIN unavailable
- `itemCondition` — required for merchant listings to specify New/Used/Refurbished
- `aggregateRating` — without this, star-rich-result snippets cannot appear

**Impact:** Google's Merchant Center / Shopping tab requires at minimum `name + image + price + availability + brand` (or GTIN). The missing `brand` alone is sufficient to suppress enhanced Shopping eligibility. Combined absence of GTIN/MPN means Google cannot deduplicate or canonicalise the listing against other sellers.

**Fix:**
In the WooCommerce product editor or via a plugin (e.g. Yoast WooCommerce SEO, Schema Pro, or Rank Math), add:
```json
"brand": { "@type": "Brand", "name": "JBL" },
"gtin13": "0050036349488",
"mpn": "JBLCHARGE4BLKAM",
"itemCondition": "https://schema.org/NewCondition"
```
If GTINs are not available across the catalogue, add `mpn` at minimum. `aggregateRating` should only be added once genuine reviews exist (see C-3).

---

#### C-2: 360 `__trashed` product slugs returning HTTP 200 — index bloat, crawl budget waste, and brand damage

**Evidence (from sitemap analysis):** `product-sitemap.xml` lists 671 product URLs; 360 (54%) contain `__trashed` in the slug, e.g. `/product/sony-wh-1000xm5-wireless-headphones__trashed/`. These pages return HTTP 200 with indexable content rather than 410 Gone or a redirect. They appear in Yoast-generated XML sitemaps.

**Impact:**
- Googlebot wastes crawl budget on 360 dead pages that carry no commercial intent.
- These pages are thin / near-duplicate (WooCommerce trash does not wipe content). If indexed, they dilute the site's perceived content quality.
- The presence of trashed products with live URLs suggests these were auto-imported and then partially deleted — a signal of low editorial control.

**Fix:**
1. In WordPress Admin > Products, permanently delete all trashed products, or
2. If data must be retained: add `noindex` via Yoast per-page or use a bulk noindex plugin, AND return HTTP 410 for the slug, AND remove from all sitemaps.
Step 1 is strongly preferred — WordPress `__trashed` slugs serve no SEO purpose.

---

#### C-3: Zero customer reviews site-wide — disqualifies aggregateRating rich results and undermines trust signals

**Evidence:** The JBL Charge 4 product page shows `woo-avg-rating: 0.0 out of 5` and "There are no reviews yet." The `Product` schema contains no `aggregateRating` or `review` properties. The pattern of zero reviews on an audited product and zero trust-signal text (0 occurrences of "return", "shipping", "warranty", "refund", "delivery", "guarantee" in the page body) suggests this is site-wide.

**Impact:**
- No star ratings in organic search snippets.
- No basis for aggregateRating schema (adding fake ratings would violate Google's structured data policy).
- For a consumer-electronics retailer, zero reviews combined with no visible returns/shipping/warranty policy is a primary trust barrier — Google's Quality Raters Guidelines downweight e-commerce sites with no customer validation.

**Fix:**
1. Implement a post-purchase email sequence requesting reviews (WooCommerce Follow-Ups or Klaviyo).
2. Add a visible returns policy, shipping info, and warranty details to every product page (via a global WooCommerce tab or dedicated page linked from product pages).
3. Once genuine reviews accumulate (minimum 1), enable `aggregateRating` in schema output via Yoast WooCommerce SEO or custom code.

---

### HIGH

#### H-1: Boilerplate meta descriptions across all product pages — guarantees duplicate meta signals and missed CTR opportunity

**Evidence:** The JBL Charge 4 meta description reads:
> "Discover the latest tech and gadgets at TechWhizMart. Shop a wide range of smart devices, electronics, and accessories at unbeatable prices. Your one-stop destination for high-quality, innovative tech solutions!"

This is the site-level tagline, not a product-level description. It is identical on the product page, the shop page, and the home page. The same text also appears verbatim in:
- `<meta name="description">`
- `<meta property="og:description">`
- The Yoast `WebPage` schema `description` field
- The Yoast `WebSite` schema `description` field

**Impact:** Google will either ignore or auto-generate snippets for pages with duplicate/generic meta descriptions, losing keyword-rich, conversion-oriented copy in SERPs. With 311 non-trashed product pages potentially sharing one meta description, this is a large-scale duplicate content signal.

**Fix:** In Yoast SEO, configure the product meta description template to pull product-specific variables, e.g.:
```
%%wc_shortdesc%% – Shop %%title%% at TechWhizMart. %%price%% | Free shipping available.
```
If short descriptions are not populated, populate them (they also feed into schema `description`). For top-50 products by traffic, write unique meta descriptions manually.

---

#### H-2: Product images hotlinked from Amazon CDN via malformed local path — broken image pipeline and no asset control

**Evidence:** Every product image URL follows this pattern:
```
https://techwhizmart.com/wp-content/uploads/https://m.media-amazon.com/images/I/411XhhP64tL.jpg
```
This is a broken local path — the server is treating the Amazon CDN URL as a relative upload path. The images happen to resolve because the server is likely passing them through or the Amazon URLs are separately cached, but the `og:image`, all gallery thumbnails, and the Product schema `image` field all use this malformed path.

Additionally, the schema `thumbnailUrl` and ImageObject `url`/`contentUrl` contain the same broken path. The ImageObject reports `width: 500, height: 500` — below the 800px recommended minimum for image rich results.

**Impact:**
- If Amazon changes CDN paths or blocks hotlinking, all product images break simultaneously.
- The malformed URL pattern may prevent Googlebot from fetching images for Google Images indexing.
- 500×500px images do not meet the 800px minimum Google recommends for image rich results.
- No WebP versions — missed opportunity for performance and modern format adoption.

**Fix:**
1. Re-upload all product images locally to `/wp-content/uploads/` using a proper import tool (e.g. "Auto Upload Images" plugin to pull and save the Amazon images locally).
2. Convert to WebP on upload (Imagify, ShortPixel, or WP Smush).
3. Ensure main product images are at least 800×800px before upload.
4. Update WooCommerce product featured image assignments after re-upload.

---

#### H-3: 8 empty `alt` attributes on product page images — image SEO failure and accessibility violation

**Evidence:** Of 26 images on the JBL Charge 4 page, 8 have `alt=""`. The 8 empty-alt images are all product gallery images (additional views of the same product), e.g.:
```html
<img alt="" src="...uploads/https://m.media-amazon.com/images/I/41c37+QkP3L.jpg">
```
The primary hero image and the first 7 gallery thumbnails have acceptable (though templated) alt text ("JBL Charge 4 - Waterproof Portable Bluetooth Speaker - Black - Image 2" etc.). The footer logo also uses `alt=""` on one instance and `alt="Logo"` on another.

**Impact:** Empty alt on product gallery images means those images are invisible to Googlebot for Google Images, and they fail WCAG 2.1 AA (empty alt is only valid for decorative images — product photos are never decorative).

**Fix:** In WooCommerce, set alt text on all product gallery images from the media library. Use descriptive, keyword-rich alts that vary per image angle/feature, e.g.:
- Image 2: "JBL Charge 4 side profile – USB charging port"
- Image 3: "JBL Charge 4 IPX7 waterproof rating label"

---

#### H-4: ~165 WooCommerce attribute archive pages (`pa_*`) indexed with thin/duplicate content — index bloat

**Evidence (from sitemap analysis):** `sitemap_index.xml` references ~165 child sitemaps for WooCommerce product attribute taxonomies, e.g. `pa_brand` (252 URLs), `pa_color` (31 URLs). These generate URLs like `/pa_brand/jbl/` and `/pa_color/black/` which are thin archive pages listing products by attribute. They are currently indexable (`index, follow`).

**Impact:** ~400+ thin attribute archive pages dilute crawl budget and create near-duplicate content with category pages. Google treats low-quality archive flooding as an index quality issue.

**Fix:**
In Yoast SEO > Search Appearance > Taxonomies, set all `pa_*` taxonomies to `noindex`. Alternatively, use WooCommerce's own setting to disable attribute archive pages if they serve no direct SEO value. Redirect high-value attribute archives (e.g. `/pa_brand/jbl/`) to a properly optimised brand landing page.

---

#### H-5: Category pages missing H1 on /shop/, no unique intro content on any category, title templates are generic

**Evidence:**
- `/shop/` page: 0 H1 tags, title is "Shop - TechWhizMart" (generic), meta description is the same site-level boilerplate as product pages.
- `/product-category/audio/`: H1 present ("Audio"), but no category description text found. Title: "Audio Archives - TechWhizMart" — the word "Archives" is Yoast's default placeholder, signalling unconfigured SEO settings.
- `/product-category/audio/speakers/`: Same pattern — "Speakers Archives - TechWhizMart", no intro content.
- No `term-description`, `category-description`, or `archive-description` content found on any category page.

**Impact:** Category pages compete for high-volume head terms (e.g. "bluetooth speakers", "wireless earbuds"). Without unique introductory content, a keyword-relevant H1, and a custom title, these pages cannot rank for category-level queries. The "Archives" suffix actively signals to Google that these are auto-generated archive pages rather than curated landing pages.

**Fix:**
1. In Yoast SEO > Search Appearance > Taxonomies > Product Categories, change the title template from `%%term_title%% Archives - %%sitename%%` to `%%term_title%% | Shop [Category] Online – TechWhizMart`.
2. Add 100–150 words of unique introductory copy to each category via the WooCommerce category editor (this content renders above the product grid and is indexed).
3. Fix the /shop/ page: add an H1 (editable via Elementor or the Rehub theme settings) and a unique meta description.

---

### MEDIUM

#### M-1: Product schema `sku` field uses WP post ID rather than a real product identifier

**Evidence:** In the Product JSON-LD: `"sku": 12955` — this is the WordPress post ID, not a retailer SKU or manufacturer part number.

**Impact:** Not a blocking issue for rich results, but Google uses SKU for merchant listing deduplication. A WP post ID provides no commercial value and may confuse downstream integrations (Merchant Center feed, Google Shopping).

**Fix:** In WooCommerce > Product > SKU field, enter a meaningful SKU (ideally the manufacturer's part number if you don't have proprietary SKUs). Yoast WooCommerce SEO will then output the correct value in schema.

---

#### M-2: Product `og:type` set to `article` instead of `product`

**Evidence:**
```html
<meta property="og:type" content="article" />
```
This is the Yoast default for WooCommerce product pages when the WooCommerce SEO add-on is not installed.

**Impact:** Facebook/Pinterest shares will treat the page as an article rather than a product, losing access to product-specific Open Graph fields (`og:price:amount`, `og:availability`) used by social commerce features.

**Fix:** Install Yoast WooCommerce SEO (paid add-on) or add a custom `wpseo_opengraph_type` filter to return `product` for WooCommerce product post types.

---

#### M-3: Sitemap `siteurl` protocol mismatch — all sitemap children listed as `http://`

**Evidence (from context):** `sitemap_index.xml` lists child sitemaps as `http://` URLs despite the site serving exclusively over HTTPS. `robots.txt` similarly declares `Sitemap: http://...`.

**Impact:** Minor crawl inconsistency — Googlebot will follow the redirect, but it adds unnecessary redirect hops. Third-party tools that don't follow redirects will fail to parse the sitemap index.

**Fix:** In Yoast SEO > General > Features, ensure the site URL is configured as `https://`. Running a search-replace on the database (`wp search-replace 'http://techwhizmart.com' 'https://techwhizmart.com'`) will update all stored references.

---

#### M-4: Utility pages (cart, checkout, my-account, wishlist) included in sitemaps and indexable

**Evidence (from context):** `page-sitemap.xml` includes `/cart/`, `/checkout/`, `/my-account/`, `/wishlist/`, `/compare-products/`, `/sample-page/` — all should be noindex and excluded from sitemaps.

**Impact:** Wastes crawl budget; checkout/account pages may cause thin-content flags or accidental indexing.

**Fix:** In Yoast SEO, set each utility page to `noindex` individually, or apply the WooCommerce Yoast integration which auto-noindexes WooCommerce system pages. Remove from XML sitemaps.

---

#### M-5: No shipping, returns, or warranty information on product pages

**Evidence:** Body text search across the JBL Charge 4 product page returned 0 occurrences of "return", "shipping", "warranty", "refund", "delivery", or "guarantee". No dedicated WooCommerce shipping tab or custom trust-signal section is present.

**Impact:** Google's Product schema supports `shippingDetails` and `hasMerchantReturnPolicy` — both are now recommended for merchant-listing rich results. Their absence limits rich result eligibility. From a conversion standpoint, absence of this information increases abandonment.

**Fix:**
1. Add a WooCommerce shipping tab or global custom product tab (via "WooCommerce Tab Manager") containing shipping and returns information.
2. Add schema for shipping and returns:
```json
"shippingDetails": {
  "@type": "OfferShippingDetails",
  "shippingRate": { "@type": "MonetaryAmount", "value": "0", "currency": "USD" },
  "deliveryTime": { "@type": "ShippingDeliveryTime", "handlingTime": {...}, "transitTime": {...} }
},
"hasMerchantReturnPolicy": {
  "@type": "MerchantReturnPolicy",
  "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
  "merchantReturnDays": 30,
  "returnMethod": "https://schema.org/ReturnByMail"
}
```

---

### LOW

#### L-1: Product description is 3-bullet Amazon copy — not unique content

**Evidence:** The JBL Charge 4 description (also used verbatim in Product schema `description`) consists of 3 Amazon bullet points:
> "WIRELESS BLUETOOTH STREAMING: Wirelessly connect up to 2 smartphones or tablets..."
> "UP TO 20 HOURS OF PLAYTIME: Built-in rechargeable Li-ion 7500mAH battery..."
> "IPX7 WATERPROOF: Take Charge 4 to the beach or the pool..."

This is verbatim manufacturer/Amazon copy. Word count: approximately 60 words of actual product description (page total 1,063 words, mostly template/navigation chrome).

**Impact:** Low content uniqueness reduces differentiation from Amazon and other resellers for the same product. For high-competition electronics terms, unique value-added content (buying guides, compatibility notes, comparison context) is a ranking differentiator.

**Fix:** For top-revenue products, rewrite descriptions to add unique value: compatibility details, editorial recommendation rationale, comparison with related models in catalogue, local shipping/support notes. Minimum 200 words of unique copy per product.

---

#### L-2: Internal link anchor text from related products section is partially empty (image-only links)

**Evidence:** The related products section generates two anchor tags per product — one wrapping the image (no text, no meaningful aria-label) and one with the product title text. Example:
```html
<a href="/product/jbl-charge-5-..."><img alt="JBL CHARGE 5..."></a>
<a href="/product/jbl-charge-5-...">JBL CHARGE 5 – Portable Waterproof...</a>
```
The image links are duplicate anchors with no anchor text contribution.

**Impact:** Minor — the text link provides anchor context. However, the duplicate anchor pattern and the fact that related products span categories (earbuds listed as related to a speaker) reduces topical relevance of internal link equity.

**Fix:** Configure WooCommerce related products to pull from the same category only (via `woocommerce_related_products_args` filter or a plugin like "WooCommerce Related Products"). Remove duplicate image-only anchor tags or merge into a single linked image+text component.

---

#### L-3: No pagination on shop/category pages visible — unclear if pages beyond page 1 exist

**Evidence:** The `/shop/` page shows 15 product items in the HTML but no pagination links were found in the fetched HTML. If there are 311 non-trashed products and only 15 per page, approximately 20 pages of results exist — but the pagination links may be rendered client-side (Rehub theme uses AJAX filtering). If pagination is AJAX-only with no static `?paged=N` or `/page/N/` links, Googlebot cannot discover products beyond page 1.

**Impact (if confirmed):** Products on pages 2–20 of category/shop listing would receive zero internal link equity from category pages and may not be discoverable by Googlebot without explicit product sitemap submission.

**Fix:** Verify pagination renders in HTML (not JS-only). If AJAX-only: enable static fallback pagination in Rehub theme settings, or ensure all products are included in `product-sitemap.xml` (non-trashed ones are — but this should be verified after trashed product cleanup).

---

#### L-4: WooCommerce `xmlrpc.php` pingback endpoint exposed in `<head>`

**Evidence:**
```html
<link rel="pingback" href="https://techwhizmart.com/xmlrpc.php" />
```
This is a minor security surface exposure. Not a direct SEO issue but is flagged by security scanners and can contribute to spam/negative SEO vectors.

**Fix:** Remove the pingback link via `remove_action('wp_head', 'xmlrpc_rsd_link')` and disable XML-RPC if not needed via `add_filter('xmlrpc_enabled', '__return_false')`.

---

## Scores

| Dimension | Score | Notes |
|---|---|---|
| Product Schema (merchant-listing readiness) | 38/100 | Price/availability/sku present; brand/gtin/condition/aggregateRating all missing |
| Image Optimisation | 32/100 | 8/26 empty alts; broken CDN path pattern; 500px max; no WebP |
| Content Uniqueness / Depth | 25/100 | Boilerplate meta descriptions, Amazon copy descriptions, no unique category content |
| Internal Linking | 52/100 | Breadcrumbs and related products present; category links sparse; pagination unclear |
| Trust / Conversion Signals | 10/100 | Zero reviews; zero shipping/returns/warranty text on product pages |
| Index Health (bloat) | 20/100 | 360 trashed products indexed; ~165 attribute archives indexed; utility pages in sitemap |
| **Overall E-Commerce SEO** | **30/100** | Foundation exists but critical gaps block rich results and Shopping eligibility |

---

## Priority Action List

| Priority | Action | Expected Impact |
|---|---|---|
| Critical | Permanently delete 360 trashed products (or 410 + noindex + remove from sitemap) | Crawl budget recovery, quality signal improvement |
| Critical | Add `brand`, `gtin`/`mpn`, `itemCondition` to all Product schema | Shopping tab free listing eligibility |
| Critical | Fix boilerplate meta descriptions — configure Yoast template with product-specific variables | CTR improvement across 311 product pages |
| High | Re-upload all product images locally; convert to WebP; minimum 800px; fix all empty alts | Image rich results, Google Images traffic, page integrity |
| High | Implement post-purchase review collection; add shipping/returns to product pages | Trust signals, aggregateRating schema eligibility |
| High | Noindex all `pa_*` attribute archives (~400 URLs) | Index bloat reduction |
| High | Fix category page titles (remove "Archives"), add H1 to /shop/, add unique category descriptions | Category-level keyword rankings |
| Medium | Set `og:type = product` on product pages | Social commerce rich cards |
| Medium | Fix sitemap/robots.txt http:// → https:// | Crawl consistency |
| Medium | Noindex and remove utility pages (cart, checkout, my-account) from sitemaps | Crawl budget |
| Low | Replace Amazon bullet-point descriptions with unique editorial copy on top products | Content differentiation vs Amazon |
| Low | Restrict related products to same category; remove duplicate image-only anchor tags | Internal link relevance |

---

*Marketplace pricing competitiveness, SERP rank data, competitor pricing landscape, and Google Shopping impression data were not available for this audit — DataForSEO Merchant API credentials are not configured. These dimensions should be assessed when credentials are available.*
