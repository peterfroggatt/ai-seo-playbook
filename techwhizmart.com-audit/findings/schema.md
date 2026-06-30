# Schema.org Structured Data Findings — techwhizmart.com

**Audit date:** 2026-06-30
**Platform:** WordPress + WooCommerce 10.8.1 + Yoast SEO
**Pages sampled:** Homepage, 2 live product pages (JBL Charge 4, RedThunder K10), 1 trashed product page (Marcy Smith Cage Machine `__trashed`), 1 blog post (drone-photography-for-travel-blogs)
**Schema score: 34 / 100**

---

## Schema Score Breakdown

| Category | Weight | Score | Notes |
|---|---|---|---|
| Homepage (WebSite/Org/Search) | 20 | 12 | Present but thin Org; broken primaryImage |
| Product / Offer validity | 30 | 8 | Missing brand, condition, gtin/mpn, price top-level; broken image URLs |
| Merchant-listing requirements | 20 | 0 | No shippingDetails, no hasMerchantReturnPolicy on any product |
| Trashed product indexability | 10 | 0 | 360 `__trashed` pages emit full Product+Offer schema, indexed |
| Article / Person | 10 | 8 | Present but @type is generic Article not BlogPosting; author weak |
| Missing high-value types | 10 | 6 | AggregateRating, FAQPage, ItemList absent |

---

## 1. Homepage Schema

**URL:** `https://techwhizmart.com/`
**Format:** Single JSON-LD `@graph` block (Yoast standard)
**Blocks detected:** 1 (WebPage + ImageObject + BreadcrumbList + WebSite + Organization)

### What is present

- `WebSite` with `SearchAction` / `potentialAction` — correct `urlTemplate` using `{search_term_string}`, `query-input` uses the newer `PropertyValueSpecification` format. Valid.
- `Organization` with `name`, `url`, `logo` (ImageObject with width/height), `sameAs`.
- `WebPage` with `datePublished`, `dateModified`, `description`, `breadcrumb`, `inLanguage`.
- `BreadcrumbList` — single item "Home" (correct for homepage).

### Findings

#### [HIGH] Organization: primaryImage is a blank GIF placeholder

```json
"primaryImageOfPage": {
  "@id": "https://techwhizmart.com/#primaryimage"
},
"image": {
  "@id": "https://techwhizmart.com/#primaryimage"
}
```

The `ImageObject` that `#primaryimage` resolves to has:
```json
"url": "https://techwhizmart.com/wp-content/themes/rehub-theme/images/default/blank.gif"
```

A 1x1 blank GIF is the homepage's declared primary image. Google will not surface this in image search or Knowledge Panel. This is caused by the Rehub theme not having a real featured image set on the homepage.

**Fix:** Set a real featured image on the homepage in WP (1200×630 px minimum). Yoast will then reference it automatically.

#### [MEDIUM] Organization: severely thin `sameAs` array

Only one social profile is declared:
```json
"sameAs": ["https://au.pinterest.com/techwhizmart/"]
```

Missing: Facebook, Twitter/X, Instagram, YouTube, LinkedIn, Wikidata. A thin `sameAs` weakens entity disambiguation for Google's Knowledge Graph and AI citation.

**Fix:** Add all active social profiles to Yoast's "Social" settings (SEO → Social → Accounts). Yoast writes them to `sameAs` automatically. Example:
```json
"sameAs": [
  "https://au.pinterest.com/techwhizmart/",
  "https://www.facebook.com/techwhizmart",
  "https://www.instagram.com/techwhizmart",
  "https://twitter.com/techwhizmart"
]
```

#### [MEDIUM] Organization: no `contactPoint`, `address`, or `telephone`

For an e-commerce store, these signal trust and support Google Merchant Center entity matching. Currently absent.

**Fix:** Add via Yoast's Organization settings or a custom JSON-LD block:
```json
{
  "@type": "Organization",
  "@id": "https://techwhizmart.com/#organization",
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "customer service",
    "email": "support@techwhizmart.com",
    "availableLanguage": "English"
  }
}
```

#### [LOW] BreadcrumbList homepage item missing `item` URL

The single list item has `name: "Home"` but no `item` property with the URL. Google's guidelines require `item` on all but the last breadcrumb.

```json
// Current (incorrect)
{"@type": "ListItem", "position": 1, "name": "Home"}

// Fixed
{"@type": "ListItem", "position": 1, "name": "Home", "item": "https://techwhizmart.com/"}
```

Yoast omits `item` on the last breadcrumb by design, but the homepage is also the only breadcrumb here, so this is a Yoast edge case. Low impact, but worth noting.

#### [INFO] `@context` on WebSite/Organization graph uses `https://schema.org` (no trailing slash) — correct. Product blocks (separate script tag) use `https://schema.org/` (trailing slash) — technically valid but inconsistent across page.

---

## 2. Product Schema — Live Products

**Pages sampled:**
- `https://techwhizmart.com/product/jbl-charge-4-waterproof-portable-bluetooth-speaker-black/` (JBL Charge 4)
- `https://techwhizmart.com/product/redthunder-k10-wireless-gaming-keyboard-and-mouse-combo-led-backlit-rechargeable-3800mah-battery-mechanical-feel-anti-ghosting-keyboard-7d-3200dpi-mice-for-pc-gamer-black/` (RedThunder K10)

**Format:** Second separate JSON-LD block (not inside the Yoast `@graph`), `@type: Product`

### Google Merchant Listing Requirements — Property Audit

| Property | Required? | JBL | RedThunder | Notes |
|---|---|---|---|---|
| `name` | Yes | PASS | PASS | Present |
| `description` | Yes | PASS | PASS | Present |
| `image` | Yes | FAIL | FAIL | Broken concatenated URL (see below) |
| `offers.availability` | Yes | PASS | PASS | `schema.org/InStock` |
| `offers.price` (top-level) | Yes | FAIL | FAIL | Only inside `priceSpecification` list — not at `Offer.price` |
| `offers.priceCurrency` (top-level) | Yes | FAIL | FAIL | Same — only inside `priceSpecification` list |
| `offers.itemCondition` | Yes | FAIL | FAIL | Absent — required for merchant listings |
| `brand` | Yes | FAIL | FAIL | Absent on all sampled products |
| `gtin` / `gtin8/12/13/14` | Recommended | FAIL | FAIL | Absent |
| `mpn` | Recommended | FAIL | FAIL | Absent |
| `aggregateRating` | Recommended | FAIL | FAIL | Absent on all sampled products |
| `review` | Recommended | FAIL | FAIL | Absent |
| `sku` | Recommended | PASS | PASS | Present (numeric WooCommerce ID) |
| `offers.shippingDetails` | Required (merchant) | FAIL | FAIL | Absent |
| `offers.hasMerchantReturnPolicy` | Required (merchant) | FAIL | FAIL | Absent |
| `offers.priceValidUntil` | Recommended | PASS | PASS | `2027-12-31` |
| `offers.seller` | Recommended | PASS | PASS | Organization present |

### Findings

#### [CRITICAL] Broken product image URLs (all products)

All product image URLs follow this broken pattern:
```
https://techwhizmart.com/wp-content/uploads/https://m.media-amazon.com/images/I/411XhhP64tL.jpg
```

This is a WooCommerce/import artifact: the Amazon CDN URL was stored as the image path in the WordPress media library, so WP prepends its own domain and upload path to an already-absolute URL. The resulting URL returns a 404, meaning Google cannot crawl the declared product image. This disqualifies every product from image-powered rich results and merchant listings.

**Fix:** Re-import or re-attach product images properly. The actual Amazon image URL (`https://m.media-amazon.com/images/I/411XhhP64tL.jpg`) should be downloaded and hosted locally, or the WooCommerce product image URL should be corrected to a direct CDN URL without the domain prefix. Sideloading via WP CLI:
```bash
wp media import "https://m.media-amazon.com/images/I/411XhhP64tL.jpg" --post_id=PRODUCT_ID --featured_image
```

#### [CRITICAL] `Offer.price` and `Offer.priceCurrency` not at the Offer level

Google's Product structured data requires `price` and `priceCurrency` directly on the `Offer` object. TechWhizMart places them only inside a nested `priceSpecification` array:

```json
// Current (Google cannot read price from this)
"offers": [{
  "@type": "Offer",
  "priceSpecification": [
    {"@type": "UnitPriceSpecification", "price": "114.95", "priceCurrency": "USD"}
  ],
  "availability": "https://schema.org/InStock"
}]
```

```json
// Fixed (add price/priceCurrency at Offer level too)
"offers": [{
  "@type": "Offer",
  "price": "114.95",
  "priceCurrency": "USD",
  "priceSpecification": [
    {"@type": "UnitPriceSpecification", "price": "114.95", "priceCurrency": "USD", "validThrough": "2027-12-31"}
  ],
  "availability": "https://schema.org/InStock"
}]
```

This is a WooCommerce + Yoast WooCommerce SEO configuration issue — `price` and `priceCurrency` must appear at the `Offer` root. The UnitPriceSpecification can remain for additional detail (sale/list price distinction), but cannot substitute the top-level fields.

**Note:** `schema_ecommerce_validate.py` throws an `AttributeError` (line 194) when `priceSpecification` is a list rather than a dict — this is a bug in the validator script that needs fixing. Manual validation was performed above.

#### [CRITICAL] No `offers.shippingDetails` (OfferShippingDetails) on any product

Google requires `shippingDetails` for merchant listing eligibility. Absence prevents products from appearing in Google Shopping free listings.

**Fix:** Add per-product or site-wide shipping markup (WooCommerce Shipping Zones → Schema). Minimum required fields per `OfferShippingDetails`:
```json
"shippingDetails": {
  "@type": "OfferShippingDetails",
  "shippingRate": {
    "@type": "MonetaryAmount",
    "value": "0",
    "currency": "USD"
  },
  "shippingDestination": {
    "@type": "DefinedRegion",
    "addressCountry": "US"
  },
  "deliveryTime": {
    "@type": "ShippingDeliveryTime",
    "handlingTime": {"@type": "QuantitativeValue", "minValue": 1, "maxValue": 2, "unitCode": "DAY"},
    "transitTime": {"@type": "QuantitativeValue", "minValue": 3, "maxValue": 7, "unitCode": "DAY"}
  }
}
```

#### [CRITICAL] No `offers.hasMerchantReturnPolicy` on any product

Required for Google merchant listings. Absence disqualifies products from Shopping rich results.

**Fix:**
```json
"hasMerchantReturnPolicy": {
  "@type": "MerchantReturnPolicy",
  "applicableCountry": "US",
  "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
  "merchantReturnDays": 30,
  "returnMethod": "https://schema.org/ReturnByMail",
  "returnFees": "https://schema.org/FreeReturn"
}
```

Plugin recommendation: **Woo Product Schema** or **Schema Pro** can inject these at WooCommerce level. Alternatively, add via Yoast's "Schema" tab in WooCommerce product settings (requires Yoast WooCommerce SEO plugin).

#### [HIGH] No `brand` property on any product

Brand is required for Google's Product rich results (as of 2023 policy update) and mandatory for merchant listings when `gtin` is absent.

**Fix:** Add to each product:
```json
"brand": {
  "@type": "Brand",
  "name": "JBL"
}
```

In WooCommerce this can be automated by mapping a product attribute (`pa_brand`) to the schema `brand` field via Yoast WooCommerce SEO or a custom filter.

#### [HIGH] No `itemCondition` on any product

Required for merchant listings. All products are new but this is not declared.

**Fix:** Add to each `Offer`:
```json
"itemCondition": "https://schema.org/NewCondition"
```

#### [HIGH] No `gtin` or `mpn` on any product

Without GTIN, Google cannot match products to its catalog for Shopping. MPN is the fallback when GTIN is unavailable.

**Fix:** Add manufacturer GTINs (UPC/EAN/ISBN) to WooCommerce product data or use MPN:
```json
"gtin13": "0050375040806",
"mpn": "JBLCHARGE4BLKAM"
```

#### [HIGH] No `aggregateRating` or `review` on any product

WooCommerce has a built-in review/rating system, but ratings are not being surfaced in schema. This is the single highest-impact missing rich result — star ratings in SERPs dramatically improve CTR.

**Fix:** Enable Yoast WooCommerce SEO's product schema output and ensure `Enable product reviews` is on in WooCommerce settings. This will add:
```json
"aggregateRating": {
  "@type": "AggregateRating",
  "ratingValue": "4.5",
  "reviewCount": "23",
  "bestRating": "5",
  "worstRating": "1"
}
```

Alternatively, add a WooCommerce hook:
```php
add_filter('wpseo_schema_product', function($data, $context) {
  $product = wc_get_product($context->post->ID);
  if ($product && $product->get_review_count() > 0) {
    $data['aggregateRating'] = [
      '@type' => 'AggregateRating',
      'ratingValue' => (string) $product->get_average_rating(),
      'reviewCount' => (string) $product->get_review_count(),
      'bestRating' => '5',
      'worstRating' => '1',
    ];
  }
  return $data;
}, 10, 2);
```

---

## 3. Trashed Product Pages — Critical Indexability Issue

**Sample URL:** `https://techwhizmart.com/product/marcy-smith-cage-machine-with-workout-bench-and-weight-bar-home-gym-equipment-sm-4008__trashed/`

### Findings

#### [CRITICAL] 360 `__trashed` product URLs return HTTP 200 with full Product+Offer schema

Confirmed facts:
- HTTP status: **200 OK** (should be 410 Gone or 301 redirect)
- `<meta name="robots">`: `index, follow` (should be `noindex`)
- Canonical: self-referencing `__trashed` URL (not redirected to a live equivalent)
- JSON-LD: full `Product` block with `availability: InStock` and a price — identical structure to live products
- Present in `product-sitemap.xml` (360 out of 671 product sitemap entries are `__trashed`)

This is the most severe schema/indexability issue on the site. Google is crawling and potentially indexing 360 abandoned product pages that:
1. Carry `InStock` pricing for products no longer sold
2. Self-canonicalize (so canonical consolidation provides no relief)
3. Appear in the sitemap as first-class products

**Fix (in order of priority):**

1. **Immediate:** Add `noindex` to all `__trashed` posts via a WP filter:
```php
add_action('wp_head', function() {
  if (is_singular('product') && strpos(get_post_field('post_name', get_the_ID()), '__trashed') !== false) {
    echo '<meta name="robots" content="noindex, nofollow">' . PHP_EOL;
  }
});
```

2. **Correct:** Return HTTP 410 (Gone) for trashed products instead of 200:
```php
add_action('template_redirect', function() {
  if (is_singular('product') && strpos(get_post_field('post_name', get_the_ID()), '__trashed') !== false) {
    status_header(410);
    nocache_headers();
  }
});
```

3. **Remove from sitemap:** Add a Yoast filter to exclude `__trashed` slugs:
```php
add_filter('wpseo_exclude_from_sitemap_by_post_ids', function($excluded) {
  global $wpdb;
  $ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_name LIKE '%__trashed%' AND post_type='product'");
  return array_merge($excluded, $ids);
});
```

4. **Long-term:** In WooCommerce settings, configure trashed products to return 404/410 natively. Some cache plugins (WP Rocket, LiteSpeed Cache) have redirect rules for this.

---

## 4. Blog Post / Article Schema

**URL:** `https://techwhizmart.com/drone-photography-for-travel-blogs/`
**Format:** Single JSON-LD `@graph` block (Yoast)
**Nodes:** Article, WebPage, ImageObject, BreadcrumbList, WebSite, Organization, Person

### What is present

- `Article` with `headline`, `datePublished`, `dateModified`, `author` (linked Person node), `publisher`, `image`, `wordCount`, `inLanguage` — core required fields are present.
- `Person` node with `name: "peter froggatt"`, Gravatar `image`, `url` (author archive), `sameAs: ["https://techwhizmart.com"]`. Present but weak.
- `BreadcrumbList` with 3 items: Home → Shop → Post title. Correct structure with `item` URLs on non-final items.

### Findings

#### [MEDIUM] Article `@type` should be `BlogPosting` for blog posts

Google distinguishes `Article`, `BlogPosting`, and `NewsArticle`. For a blog on an e-commerce site, `BlogPosting` is the semantically correct type and is preferred for blog-style content by Google's rich results guidelines.

**Fix:** In Yoast → Search Appearance → Content Types → Posts, set the schema `@type` to `BlogPosting`.

#### [MEDIUM] Person node: `sameAs` points only to the site homepage

```json
"sameAs": ["https://techwhizmart.com"]
```

This resolves `sameAs` to the site Organization, not to the author's external identity. For E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness), author entity links should point to social profiles or authoritative external pages.

**Fix:** In WordPress user profile (Users → peter froggatt), fill in the biographical info and social profile URLs. Yoast will inject them into the Person node's `sameAs`:
```json
"sameAs": [
  "https://www.linkedin.com/in/peterfroggatt",
  "https://twitter.com/peterfroggatt"
]
```

#### [MEDIUM] Article author name is lowercase / not properly capitalised

`"name": "peter froggatt"` — all lowercase. While not a schema validation error, it signals an unconfigured WordPress user profile and undermines author credibility signals.

**Fix:** Update display name in WordPress user profile to "Peter Froggatt".

#### [LOW] BreadcrumbList: second item is "Shop" (wrong parent for a blog post)

The breadcrumb reads: Home → Shop → [Post Title]. A blog post should breadcrumb as Home → Blog → [Post Title]. The "Shop" intermediate is a WooCommerce artefact.

**Fix:** Set the blog post parent category in Yoast or create a proper blog category taxonomy that excludes "Shop" from the breadcrumb trail.

#### [INFO] Article lacks `description` and `keywords` properties

Both are recommended by Google for Article schema and aid AI/LLM citation extraction. The `WebPage` node has `description` but the `Article` node does not.

---

## 5. Missing High-Value Schema Types

### [HIGH] No `AggregateRating` on any Product

Covered in section 2. Star ratings are the single highest-CTR rich result available to WooCommerce stores. With 311 live products and no ratings in schema, this is a significant missed opportunity.

### [HIGH] No `shippingDetails` / `hasMerchantReturnPolicy` site-wide

Covered in section 2. Without these, no products qualify for Google's free Shopping listings or merchant-listing rich results.

### [MEDIUM] No `ItemList` on category/collection pages

Product category pages (e.g. `/product-category/electronics/`) do not emit `ItemList` schema. This enables carousel rich results for category pages.

**Fix:** Add `ItemList` to WooCommerce category archives:
```json
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Electronics",
  "url": "https://techwhizmart.com/product-category/electronics/",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "url": "https://techwhizmart.com/product/..."},
    {"@type": "ListItem", "position": 2, "url": "https://techwhizmart.com/product/..."}
  ]
}
```

### [MEDIUM] No `VideoObject` schema despite potential product video content

If product pages include embedded videos (YouTube or native), `VideoObject` schema should be added for video rich results.

### [INFO] FAQPage schema absent

FAQPage rich results were retired by Google on May 7, 2026 and no longer produce SERP features. Do not add `FAQPage` for Google SERP benefit. However, if GEO/AI citation visibility is a goal, `FAQPage` markup still aids LLM entity resolution — acceptable to add for that purpose only, with no expectation of a visual SERP feature.

### [INFO] No `WebPage` subtyping on key pages

The checkout, cart, and account pages use generic `WebPage`. These could use `CheckoutPage` and `ProfilePage` subtypes respectively for richer semantic signals.

---

## 6. Validator Tool Note

`scripts/schema_ecommerce_validate.py` throws an `AttributeError` at line 194 when a product's `Offer.priceSpecification` is a list (as is the case for all TechWhizMart products that have both a sale price and a list price). The line attempts `.get()` on a list object. All validation in this report was performed manually against the extracted JSON-LD. The validator script should be patched to handle `priceSpecification` as either a dict or a list.

---

## Summary: Top Issues by Priority

| # | Severity | Issue | Affected Pages |
|---|---|---|---|
| 1 | CRITICAL | 360 `__trashed` product pages return HTTP 200 + `index,follow` + full `Product/InStock` schema | 360 product URLs |
| 2 | CRITICAL | Broken product image URLs (`wp-content/uploads/https://...`) — images return 404 | All ~671 products |
| 3 | CRITICAL | No `shippingDetails` or `hasMerchantReturnPolicy` on any product — disqualified from Google Shopping | All products |
| 4 | CRITICAL | `Offer.price` / `Offer.priceCurrency` missing at Offer root level (only inside `priceSpecification` list) | All products |
| 5 | HIGH | No `aggregateRating` / `review` — zero star-rating rich results despite WooCommerce review capability | All products |
| 6 | HIGH | No `brand`, `itemCondition`, `gtin`/`mpn` on any product | All products |
| 7 | MEDIUM | Organization `sameAs` has only 1 social profile (Pinterest AU only) | Homepage |
| 8 | MEDIUM | Homepage `primaryImageOfPage` resolves to a blank.gif placeholder | Homepage |
| 9 | MEDIUM | Article `@type` should be `BlogPosting`; author Person `sameAs` is self-referential | All 147 blog posts |
| 10 | MEDIUM | No `ItemList` on product category pages | 34+ category pages |
