# Schema / Structured Data Audit — techwhizmart.com

**Audit date:** 2026-06-30
**Pages sampled:** home, 4 products (JBL Charge 4, RedThunder K10, GoPro Hero12, DOQAUS headphones), 1 category (/product-category/audio/), 1 blog post (/top-5-professional-drones-for-2025-a-comprehensive-guide-2/)

---

## What Works

- **JSON-LD format used throughout.** All schema is output as `<script type="application/ld+json">` blocks — the preferred format. No Microdata or RDFa in use.
- **`https://schema.org` context.** All blocks use the correct HTTPS context. (Note: Product blocks use `https://schema.org/` with a trailing slash — technically valid but inconsistent.)
- **Yoast @graph pattern is correctly implemented.** WebPage, WebSite, Organization, BreadcrumbList, and ImageObject are all inter-linked via `@id` references.
- **WebSite + SearchAction present on every page.** The Sitelinks Search Box markup is correctly structured.
- **BreadcrumbList on all sampled pages.** Home → Shop → Product path is present on every product page. Category page also carries BreadcrumbList.
- **Organization has a logo.** `logo.url` points to a real uploaded PNG with correct width/height dimensions.
- **Product schema present on all 4 sampled product pages.** `@type: Product`, `name`, `description`, `sku`, `offers` with `availability` and `priceCurrency` are present.
- **`priceValidUntil` set far in the future (2027-12-31).** Avoids the common WooCommerce error of expired price dates causing merchant-listing disqualification.
- **Sale price + ListPrice modelled correctly.** On products with a sale, both `UnitPriceSpecification` entries are present with the correct `priceType: https://schema.org/ListPrice` for the original price.
- **Article schema on blog posts.** Yoast emits `@type: Article` with `author`, `datePublished`, `dateModified`, `headline`, `wordCount`, and `publisher` (linked to Organization). Person entity with `@id` is also included.
- **All JSON-LD blocks parse without errors.** No syntax errors detected across all sampled pages.

---

## Issues

### CRITICAL

#### C1 — Product schema missing `price` at Offer level; only nested inside `priceSpecification`

**Evidence (all 4 products):**
```json
"offers": [{
  "@type": "Offer",
  "priceSpecification": [{
    "@type": "UnitPriceSpecification",
    "price": "114.95",
    "priceCurrency": "USD"
  }]
}]
```

Google's merchant-listing requirements for Product rich results require `price` and `priceCurrency` as **direct properties of Offer**, not only inside `priceSpecification`. Using only `UnitPriceSpecification` without a top-level `price` on the Offer causes Google's Rich Results Test to fail the merchant-listing eligibility check. This affects all ~311 active products.

**Fix — add `price` and `priceCurrency` directly on Offer:**
```json
"offers": [{
  "@type": "Offer",
  "price": "114.95",
  "priceCurrency": "USD",
  "priceValidUntil": "2027-12-31",
  "availability": "https://schema.org/InStock",
  "itemCondition": "https://schema.org/NewCondition",
  "url": "https://techwhizmart.com/product/jbl-charge-4-waterproof-portable-bluetooth-speaker-black/",
  "seller": {
    "@type": "Organization",
    "name": "TechWhizMart",
    "url": "https://techwhizmart.com"
  },
  "priceSpecification": [{
    "@type": "UnitPriceSpecification",
    "price": "114.95",
    "priceCurrency": "USD",
    "validThrough": "2027-12-31"
  }]
}]
```
Fix via WooCommerce plugin (e.g. Yoast WooCommerce SEO add-on, or a custom `woocommerce_structured_data_product` filter in `functions.php`).

---

#### C2 — `itemCondition` absent from all Offer blocks

**Evidence:** Zero occurrences of `itemCondition` across all 4 sampled products. Google's Product rich results documentation lists `itemCondition` as a **required** property for merchant-listing eligibility (alongside `price`, `priceCurrency`, `availability`).

**Fix — add to every Offer:**
```json
"itemCondition": "https://schema.org/NewCondition"
```
These are new consumer electronics; `NewCondition` is correct. Add this alongside the `price` fix above.

---

#### C3 — Product image URL is a malformed double-path string (broken `@id` and crawler-unfetchable)

**Evidence (all 4 products, and the ImageObject primaryimage on product WebPage):**
```
"image": "https://techwhizmart.com/wp-content/uploads/https://m.media-amazon.com/images/I/411XhhP64tL.jpg"
```
This is a corrupted URL — a full Amazon CDN URL has been concatenated onto the local `wp-content/uploads/` path. The result is not a real file on the TechWhizMart server; it will 404. Google cannot crawl this image, which means:
- Product rich results cannot display an image (images are required for merchant listings).
- The `primaryImageOfPage` on WebPage also points to the same broken URL.
- Sitewide scope: this pattern appears to affect all 671 products (images were imported from Amazon without proper local hosting).

**Fix:** Images must either be properly imported to the WordPress media library (so they resolve as `https://techwhizmart.com/wp-content/uploads/2025/01/image.jpg`) or the schema `image` field must point to the real CDN URL directly (e.g. `https://m.media-amazon.com/images/I/411XhhP64tL.jpg`). A bulk fix via WP-CLI:
```bash
wp search-replace 'https://techwhizmart.com/wp-content/uploads/https://' 'https://' --all-tables
```
Verify images actually resolve after the fix. Note: hotlinking Amazon CDN images in schema (and on-page) may violate Amazon's ToS — proper import to local hosting is the recommended path.

---

### HIGH

#### H1 — `brand` property absent from all Product schema blocks

**Evidence:** None of the 4 sampled products contain a `brand` property in their Product block, despite selling clearly branded goods (JBL, RedThunder, GoPro, DOQAUS).

Google's documentation marks `brand` as a **recommended** property for Product rich results and a **required** property for Google Shopping / merchant-center merchant-listing eligibility. Absence of brand reduces eligibility for free product listings.

**Fix — add to each Product block:**
```json
"brand": {
  "@type": "Brand",
  "name": "JBL"
}
```
This requires extracting brand from the WooCommerce `pa_brand` attribute (the sitemap shows `pa_brand` with 252 URLs, confirming brands are stored as product attributes). A `woocommerce_structured_data_product` filter can read `wc_get_product_terms( $product->get_id(), 'pa_brand' )` and inject the brand node.

---

#### H2 — `gtin` / `mpn` absent from all Product blocks; `sku` is a WooCommerce internal integer, not a real SKU

**Evidence:**
```json
"sku": 12955   // JBL Charge 4 — this is the WooCommerce post ID, not a real manufacturer SKU
"sku": 13276   // RedThunder K10
"sku": 12802   // GoPro Hero12
"sku": 13137   // DOQAUS headphones
```
These integers are WooCommerce internal post IDs, not meaningful product identifiers. No `gtin8`, `gtin12`, `gtin13`, `gtin14`, or `mpn` is present on any product.

For Google's merchant-listing program, at minimum one of GTIN/MPN/SKU must be a meaningful, externally-verifiable identifier. Using post IDs as SKUs provides no value and could trigger a "missing identifier" policy warning in Google Merchant Center if products are submitted via Search Console's Shopping feed.

**Fix:** Populate the WooCommerce SKU field with the manufacturer's actual part number (or ASIN, or EAN/UPC where available). Then surface it in schema:
```json
"sku": "JBLCHARGE4BLKAM",
"gtin12": "050036342787"
```
If GTINs are not known, at minimum correct the SKU to a real identifier rather than the internal post ID.

---

#### H3 — No `aggregateRating` or `Review` on any product; zero-rating display confirmed in HTML

**Evidence:** The WooCommerce reviews section rendered in product HTML shows:
```html
<span class="orangecolor font200 fontbold">0.0</span> <span class="greycolor font90">out of 5</span>
```
No customer reviews exist on the site (WooCommerce `commentCount: 0` confirmed in blog Article schema; product pages show 0.0/5 average). The star/rating CSS classes present in the HTML (35 matches per page) are theme UI scaffolding, not actual rating data.

**Assessment:** `AggregateRating` schema must NOT be added at this time. Adding it with a value of 0 or fabricating a rating would violate Google's spam policies. This is an **opportunity gap**, not a schema error.

**Recommended action:** Enable WooCommerce's native review system and encourage post-purchase reviews via transactional email (e.g. WooCommerce Follow-Ups or AutomateWoo). Once real ratings exist, Yoast + WooCommerce will emit `aggregateRating` automatically. A product with a 4+ star AggregateRating in schema can trigger star rich results in SERPs — a significant CTR uplift for an electronics retailer.

---

#### H4 — Blog post Article schema missing `image` as a direct property (only `thumbnailUrl` and a referenced ImageObject)

**Evidence (blog post):**
```json
{
  "@type": "Article",
  "image": {
    "@id": "https://techwhizmart.com/top-5-professional-drones-for-2025-a-comprehensive-guide-2/#primaryimage"
  }
}
```
The Article's `image` is an `@id` reference to the ImageObject node in the same graph. This is structurally valid in JSON-LD. However, Google's Article rich results documentation and Rich Results Test expect `image` to resolve to a URL with minimum dimensions of 1200px width for "Top Stories". The referenced ImageObject (`width: 1110, height: 740`) is **below the 1200px minimum width** required for Top Stories eligibility.

**Fix:** Upload a version of article featured images at 1200px minimum width. Update the ImageObject width/height values accordingly. The `caption` field ("DJI Drone") is generic — use descriptive, keyword-rich captions per article.

Additionally, Article schema is missing `description` as a direct property (present on WebPage node but not on the Article node itself).

---

### MEDIUM

#### M1 — Organization `sameAs` contains only Pinterest; no Facebook, Instagram, YouTube, Twitter/X, or LinkedIn

**Evidence (consistent across all pages):**
```json
"sameAs": [
  "https://au.pinterest.com/techwhizmart/"
]
```
A single `sameAs` link (Pinterest only) gives Google and LLMs minimal entity disambiguation signal. For an e-commerce brand, social profiles across multiple platforms strengthen the Knowledge Graph entity.

**Fix:** Add all active social profiles to the `sameAs` array in the Organization block. This can be set in Yoast SEO > Settings > Site Representation > Social profiles:
```json
"sameAs": [
  "https://au.pinterest.com/techwhizmart/",
  "https://www.facebook.com/techwhizmart",
  "https://www.instagram.com/techwhizmart",
  "https://twitter.com/techwhizmart",
  "https://www.youtube.com/@techwhizmart"
]
```
Only add profiles that actually exist and are active.

---

#### M2 — Organization missing `contactPoint`, `telephone`, `email`, `address`

**Evidence:** The Organization block contains only `name`, `url`, `logo`, `image`, and `sameAs`. For an e-commerce business, Google recommends including customer service contact information.

**Fix (add to Organization, or use a separate sitewide LocalBusiness block):**
```json
"contactPoint": {
  "@type": "ContactPoint",
  "contactType": "customer service",
  "availableLanguage": "English",
  "email": "support@techwhizmart.com"
}
```
If the business has a physical address (even just a registered address), adding `@type: ["Organization", "OnlineStore"]` with `address` strengthens merchant-listing eligibility.

---

#### M3 — Home page `primaryImageOfPage` points to a blank placeholder GIF

**Evidence:**
```json
{
  "@type": "ImageObject",
  "@id": "https://techwhizmart.com/#primaryimage",
  "url": "https://techwhizmart.com/wp-content/themes/rehub-theme/images/default/blank.gif",
  "contentUrl": "https://techwhizmart.com/wp-content/themes/rehub-theme/images/default/blank.gif"
}
```
The home page's primary image resolves to a 1×1 transparent GIF (the Rehub theme's lazy-load placeholder). This is the image Google uses for OG/Social preview fallback and for the WebPage representation in the Knowledge Graph. The OG `og:image` meta tag also references this same blank GIF.

**Fix:** Set a real homepage featured image (banner, logo lockup, or hero product image) at minimum 1200×630px in WordPress > Appearance > Customize, or via Yoast SEO's social settings. This will propagate to both the schema `primaryImageOfPage` and the `og:image` meta tag.

---

#### M4 — BreadcrumbList final item missing `item` (URL) property on all product and blog pages

**Evidence (consistent across all pages sampled):**
```json
{
  "@type": "ListItem",
  "position": 3,
  "name": "JBL Charge 4 &#8211; Waterproof Portable Bluetooth Speaker &#8211; Black"
  // no "item" property
}
```
Google's BreadcrumbList documentation states that `item` (the URL) is optional for the last breadcrumb — this is not technically a validation error. However, the HTML entity `&#8211;` (en dash) appearing in the `name` value is undesirable; schema `name` values should be plain text, not HTML-encoded entities.

**Fix:** Configure Yoast to decode HTML entities in breadcrumb names, or use a filter:
```php
add_filter('wpseo_breadcrumb_single_link', function($link) {
    $link['text'] = html_entity_decode($link['text'], ENT_QUOTES, 'UTF-8');
    return $link;
});
```

---

#### M5 — Category page (`/product-category/audio/`) has no `description` on CollectionPage and no ItemList of products

**Evidence:**
```json
{
  "@type": "CollectionPage",
  "@id": "https://techwhizmart.com/product-category/audio/",
  "name": "Audio Archives - TechWhizMart"
  // no description, no ItemList
}
```
Category pages surface no product listing schema. Adding an `ItemList` with links to the products shown on the page would give Google richer signals about the category's content and enable list-style rich results.

**Fix — add ItemList to category pages:**
```json
{
  "@type": "ItemList",
  "name": "Audio Products",
  "url": "https://techwhizmart.com/product-category/audio/",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "url": "https://techwhizmart.com/product/jbl-charge-4-waterproof-portable-bluetooth-speaker-black/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "url": "https://techwhizmart.com/product/doqaus-bluetooth-headphones-over-ear-..."
    }
  ]
}
```
Also set a proper WooCommerce category description in WooCommerce > Products > Categories, which Yoast will then use for the CollectionPage `description`.

---

### LOW / INFO

#### L1 — `dateModified` absent from Product schema blocks

**Evidence:** Product JSON-LD blocks (Block 2, emitted by WooCommerce) contain no `dateModified`. The Yoast-emitted WebPage node (Block 1) also lacks `dateModified` for product pages (only `datePublished` present). Google uses `dateModified` to assess content freshness for products. Products with price changes should reflect the modification date.

**Fix:** Add `dateModified` via a WooCommerce structured data filter, pulling from `$product->get_date_modified()`.

---

#### L2 — `@context` inconsistency: Yoast graph uses `https://schema.org`, WooCommerce Product block uses `https://schema.org/`

**Evidence:**
- Block 1 (Yoast): `"@context": "https://schema.org"`
- Block 2 (WooCommerce): `"@context": "https://schema.org/"`

The trailing slash variant is technically valid per the Schema.org specification but creates a minor inconsistency. Google's parsers handle both, but standardising to `https://schema.org` (no trailing slash) is best practice.

---

#### L3 — Blog breadcrumb incorrectly routes through "Shop" not "Blog"

**Evidence:**
```json
"itemListElement": [
  {"position": 1, "name": "Home", "item": "https://techwhizmart.com/"},
  {"position": 2, "name": "Shop", "item": "https://techwhizmart.com/shop/"},
  {"position": 3, "name": "Top 5 Professional Drones for 2025..."}
]
```
A blog post's breadcrumb path Home → Shop → Post is incorrect. Blog posts belong under Home → Blog, not Home → Shop. This is a Yoast / WordPress permalink configuration issue (posts may be assigned to the "shop" page as parent). Correct the WordPress post hierarchy or Yoast's breadcrumb settings.

---

#### L4 — `WebPage` on product pages missing `dateModified`; only `datePublished` present

**Evidence:**
```json
{
  "@type": "WebPage",
  "datePublished": "2025-01-28T23:27:07+00:00"
  // no dateModified
}
```
All 4 product WebPage nodes have `datePublished` but no `dateModified`. Yoast should emit `dateModified` automatically from the WordPress `post_modified` field — its absence suggests these products have never been updated since import, or the field is not being passed through. Not critical, but worth resolving to signal content freshness.

---

#### L5 — Person author entity (`peter froggatt`) uses lowercase name and only a Gravatar image

**Evidence (Article/Person node):**
```json
{
  "@type": "Person",
  "name": "peter froggatt",
  "sameAs": ["https://techwhizmart.com"]
}
```
The author name is lowercase (`peter froggatt` rather than `Peter Froggatt`). The `sameAs` points only to the site root, not to an external author profile (LinkedIn, Google Scholar, etc.), limiting E-E-A-T signals. The `sameAs` value of the site's own homepage is circular and adds no entity disambiguation value.

**Fix:** Correct the display name in WordPress user profile. Add real `sameAs` URLs for external author profiles where they exist.

---

## Missing Opportunities

### OPP1 — No `FAQPage` schema detected on any page
**Context:** FAQ rich results were retired by Google on May 7, 2026. No SERP benefit from adding FAQPage. However, FAQ markup still aids AI/LLM citation and entity resolution. If the site adds FAQ content to product pages or category pages for GEO (Generative Engine Optimization) purposes, using FAQPage is acceptable — but adding it should not be prioritised for SERP gains.

### OPP2 — No `Review` schema on blog posts that review products
**Context:** Several blog posts appear to be product guides/comparisons ("Top 5 Professional Drones for 2025"). If any post constitutes a standalone product review, it could carry `@type: ["Article", "Review"]` with `reviewRating` and `itemReviewed`. This would enable Review snippet eligibility. Requires editorial review of post content before implementing.

### OPP3 — No `VideoObject` schema despite likely YouTube embeds in blog posts
Not confirmed from the sampled blog post HTML, but with 147 blog posts on a tech product site, YouTube embeds are probable. Any page embedding a video should carry VideoObject schema with `name`, `description`, `thumbnailUrl`, `uploadDate`, and `contentUrl` (or `embedUrl`) for Video rich result eligibility.

### OPP4 — No `WebPage` subtype (`ProductPage`) on product pages
Yoast emits `WebPage` for product pages rather than the more precise `ItemPage` subtype. Google supports `ItemPage` as a valid product page type. Not a blocking issue with Yoast's graph approach, but worth noting.

---

## Priority Fix Order

| Priority | Issue | Impact | Effort |
|----------|-------|--------|--------|
| 1 | **C3** — Broken image URLs (double-path) | Blocks all product rich result images | High (bulk DB fix) |
| 2 | **C1** — Missing `price`/`priceCurrency` on Offer | Merchant-listing disqualification | Medium (filter) |
| 3 | **C2** — Missing `itemCondition` | Merchant-listing disqualification | Low (filter) |
| 4 | **H1** — Missing `brand` | Reduced Shopping eligibility | Medium (filter + attribute) |
| 5 | **H2** — Fake SKU (post ID), no GTIN/MPN | Identifier policy risk | Medium (data entry) |
| 6 | **H3** — No reviews/AggregateRating | Lost star rich results | High (business process) |
| 7 | **M3** — Blank GIF as primaryImage on home | Poor social/knowledge graph rep | Low (media setting) |
| 8 | **M1** — Single sameAs (Pinterest only) | Weak entity disambiguation | Low (Yoast setting) |
| 9 | **H4** — Article image below 1200px | Top Stories ineligible | Medium (re-upload) |
| 10 | **M2** — No contactPoint on Organization | Customer service signal | Low (code) |
