# E-commerce SEO Findings — techwhizmart.com

**Date:** 2026-06-30
**Platform:** WordPress 7.0 + WooCommerce 10.8.1 + Elementor + Yoast SEO
**Data source:** On-page analysis (static) — DataForSEO Merchant not available
**Overall E-commerce Score: 22/100**

---

## Score Breakdown

| Dimension | Score | Notes |
|---|---|---|
| Catalog Health | 8/25 | 54% of product URLs are trashed; still indexable; full schema |
| Product Schema | 6/20 | Present but missing merchant-listing-required fields on all products |
| Product Page SEO | 10/20 | Boilerplate descriptions, broken images, no related products |
| Category / PLP | 5/15 | Thin PLPs, no H1 on Shop page, 163 pa_* attribute archives indexable |
| Homepage | 0/10 | No H1, broken product widget, only 13 internal links |
| Internal Linking | 3/10 | Breadcrumb schema present but no visual breadcrumb; no related-products widget |

---

## 1. Catalog Health — Index Bloat / Trashed Products

### [CRITICAL] 360 trashed products return HTTP 200, self-canonicalize, and carry full Product schema

**Evidence:**
- product-sitemap.xml contains 671 URLs: 311 live, 360 with `__trashed` in the slug
- Sampled trashed URL: `https://techwhizmart.com/product/marcy-smith-cage-machine-with-workout-bench-and-weight-bar-home-gym-equipment-sm-4008__trashed/`
  - HTTP 200 confirmed
  - Canonical points to itself (the trashed URL) — not redirected, not noindexed
  - H1 present, title present, full Product+Offer JSON-LD present
  - `availability` in schema = `https://schema.org/InStock`
  - "Add to cart" button visible in rendered markup

**What this means:**
- Trashed products in WooCommerce are logically deleted but the plugin failed to 410/301 them or set `noindex`. They are fully indexable duplicate/thin pages.
- The `__trashed` slug suffix makes them near-duplicate content of any live equivalent. Where a trashed product has no live counterpart it is an abandoned orphan page with no navigation path back to the catalog.
- Google is being asked to crawl and index 671 product URLs of which 54% are garbage — this directly dilutes crawl budget, PageRank, and quality signals for the 311 real products.
- The schema declares `InStock` on products that cannot actually be purchased (the store cannot fulfill a trashed product), which is a false merchant claim — a policy violation risk with Google Shopping.

**Trashed products are effectively dead:** They show "Add to cart" in the DOM but the underlying WooCommerce product is in trash status, so any order attempt would either fail at checkout or produce an unfulfillable order. This is NOT a live product being sold.

**Off-niche concentration in trashed set:** 106 of 360 trashed products (29%) are clearly off-niche (gym machines, collectible books, sports games, etc.) — these were likely bulk-imported and then trashed, but never properly removed.

**Fix:**
1. Implement a server-side rule (Nginx/htaccess or WooCommerce hook) to 410 (Gone) any URL containing `__trashed` in the path. This is the correct HTTP response for deleted content.
2. Alternatively, add `<meta name="robots" content="noindex">` via Yoast to all trashed post statuses.
3. Remove all trashed product URLs from the product sitemap.
4. **Do not 301 redirect** trashed products unless a live equivalent exists at a clean URL.

**Expected impact:** Eliminating 360 thin/dead pages removes ~54% index bloat; crawl budget reclaimed for 311 live products; quality signals consolidated.

---

## 2. Catalog Coherence — Topical / Brand Dilution

### [HIGH] Off-niche products dilute the "tech gadgets" topical authority

**Evidence from live product set (310 products):**
- Confirmed off-niche live products include: reading pillows, VR children's activity books, cycling armbands, furniture-category descriptions (from pa_* attributes including `pa_furniture-finish`, `pa_room-type`, `pa_shelf-type`)
- Categories include `minibars` (liquor bar furniture — explicitly off-niche for a tech store)
- Trashed set contains: Marcy Smith gym machine ($999), LEGO sets, liquor bar tables, sport/soccer tabletop games, collectible fiction novels (Off Campus series), cycling bikes
- The shop page currently displays books like "The Score - Collector's Edition" and "The Mistake - Collector's Edition" as the first products shown

**Topical coherence assessment:** The store's declared niche ("cutting-edge gadgets & innovative tech solutions") is undermined by active product listings in home fitness, fiction books, children's toys, and bar furniture. Google's topic-modeling of the site will be confused, weakening ranking authority for the core tech/gadget intent.

**Fix:**
1. Immediately remove or noindex all live products outside the core tech niche (gaming, audio, drones, laptops, phones, peripherals, smart home, wearables).
2. Prune the `minibars` category entirely — it signals a different business vertical.
3. Define a coherent category taxonomy limited to tech subcategories and enforce it during product imports.
4. Establish an editorial content policy: no product imports outside SIC code 5731 (radio, TV, consumer electronics) + 5945 (hobby/toy/game shops) in the gaming/tech subset.

---

## 3. Product Page SEO

### [CRITICAL] Product images are broken — Amazon CDN URLs embedded in wp-content path

**Evidence (all 3 sampled products, including both live and trashed):**
```
src="https://techwhizmart.com/wp-content/uploads/https://m.media-amazon.com/images/I/411XhhP64tL.jpg"
```
The image URL is literally `https://techwhizmart.com/wp-content/uploads/` concatenated with an Amazon CDN absolute URL. This produces a path like `.../uploads/https://m.media-amazon.com/...` which is guaranteed to 404. Every product image on the site is broken at the filesystem level.

**Impact:** No product images render on the site. Google cannot crawl product images for image search, rich results, or Shopping. The schema's `"image"` field points to these same broken URLs, making schema validation fail on the image property.

**Fix:** Audit the import plugin (likely WooCommerce Amazon Affiliates or a custom dropship importer). The importer is concatenating the remote URL to the local upload path instead of fetching and saving the image locally. Either:
- Fix the importer to download images to the server
- Or update image URLs to point directly to the Amazon CDN origin (note: Amazon CDN images may be hotlink-protected and this approach is unreliable)
- Use a plugin like "Download Remote Images" to batch-fix existing products

### [HIGH] Descriptions are Amazon marketplace boilerplate — not original content

**Evidence:**
- JBL Charge 4 description: verbatim Amazon bullet-point listing format ("WIRELESS BLUETOOTH STREAMING:", "UP TO 20 HOURS OF PLAYTIME:", "IPX7 WATERPROOF:") — identical to the Amazon product page
- DOQAUS Headphones description: lengthy manufacturer spec copy in Amazon listing format ("[Built for Quality]:", "[Up to 90 Hours of Playtime]:") — over 600 characters of manufacturer-supplied text
- Trashed Marcy Gym Machine description: opens with "Make sure this fits by entering your model number." — a verbatim Amazon UI element copied as product copy

This is mass-duplicated manufacturer/marketplace content. Google's Helpful Content system and E-E-A-T evaluation will classify these as low-quality scraped pages. There is zero original editorial voice, no unique value-add, and no expertise signal.

**Fix:**
1. Write original short descriptions (150–300 words) for each product that add buying guidance, use-case context, or comparison value not available on Amazon
2. Use the Amazon bullet points as a starting reference only — rewrite in first-person editorial voice
3. Strip Amazon UI copy like "Make sure this fits by entering your model number" — this phrase has no place on a standalone store

### [HIGH] No AggregateRating schema on any product — rich stars ineligible

**Evidence:** Schema validator confirmed `aggregateRating` absent from all 3 sampled products. A reviews section (`<div id="reviews">`) exists in the HTML but contains no reviews (the review count link shows no number). There is no WooCommerce review data being surfaced.

**Impact:** Products cannot display star ratings in Google Search results or Shopping. Without ratings data, click-through rates on SERPs are lower than competitors who show stars.

**Fix:**
1. Enable WooCommerce's built-in review system and encourage post-purchase reviews
2. Consider a review aggregator plugin (e.g., Yotpo, Judge.me) for bulk review import
3. Once reviews exist, Yoast SEO will automatically output `aggregateRating` schema

### [HIGH] No related products section on product pages

**Evidence:** Both live sampled product pages have no related products widget in the DOM. The HTML does not contain WooCommerce's default "Related products" or "Upsells" section.

**Impact:** No internal linking between products; users who land on a product page have no discovery path except the navigation menu. Crawl depth from product pages is limited to header navigation (5–6 links). This suppresses average session depth and increases bounce signals.

**Fix:** Re-enable WooCommerce related products (disabled somewhere in theme settings or via plugin). Ensure Elementor product templates include the `[related_products]` shortcode or WooCommerce block.

### [MEDIUM] Product titles are raw Amazon listing strings — not optimized for search intent

**Evidence:**
- "DOQAUS Bluetooth Headphones Over Ear, 90 Hours Playtime Wireless Headphones with 3 EQ Modes,HiFi Stereo Headphones with Microphone and Soft Protein Earpads for iPhone/TV/PC/Home Office (Black-Red)"
- These are the exact Amazon title strings — 180+ characters, stuffed with features and variants, not shaped for search queries

**Impact:** Title tags generated from these names will be truncated in SERPs. The format (All-Caps, comma-separated features) is designed for Amazon's A9 algorithm, not Google's ranking. Titles lack natural language and read poorly as SERP snippets.

**Fix:** Shorten and reformat product titles to 50–60 characters in format: `[Brand] [Model] [Key Feature] — [Category]`. Keep full detail in the product description.

### [MEDIUM] No out-of-stock handling strategy

**Evidence:** No products in the sampled set show `OutOfStock` availability. All products — including trashed ones — declare `InStock`. There is no evidence of a stock management policy.

**Risk:** If dropship suppliers go out of stock, the store will continue to accept orders. No schema signal for `BackOrder` or `OutOfStock` is being used. Google Shopping policy requires accurate availability data.

**Fix:** Configure WooCommerce stock management; use `availability: https://schema.org/OutOfStock` for unavailable products; do not use `InStock` on trashed/deleted products.

### [MEDIUM] Breadcrumb: JSON-LD present but visual breadcrumb absent

**Evidence:** BreadcrumbList schema exists in JSON-LD (Home > Shop > Product Name) but the visual HTML breadcrumb is not rendered in the DOM. The schema's third `itemListElement` has an empty `"item"` value (no URL for the current product), which may cause a validation warning.

**Fix:** Render visual breadcrumbs in the Elementor product template. Fix the BreadcrumbList by ensuring the final list item includes a valid `"item"` URL.

---

## 4. Category / PLP Pages

### [HIGH] Shop page has no H1 and zero product grid output

**Evidence:**
- `https://techwhizmart.com/shop/` — H1 count: 0, H2 count: 0
- Product items detected via WooCommerce class: 34 elements, but the first 24 product links point to fiction books (Off Campus series) — not tech products
- Word count: 544 words (all navigation/boilerplate)
- No pagination links

**Impact:** The shop page is the site's main product listing page and it has no heading structure. The products displayed (fiction books) are not coherent with the store's declared category. This page will not rank for any competitive tech/gadget category terms.

### [HIGH] Category pages have no description copy — thin PLPs

**Evidence:**
- `https://techwhizmart.com/product-category/drones/` — H1: "Drones", meta description: MISSING, category description: MISSING/NONE
- All 34 categories are bare taxonomy archives with no editorial text above or below the product grid

**Impact:** Category pages are thin content by Google's standards. Without a category description (the WooCommerce taxonomy description field), Yoast has no content to generate a meta description from, resulting in MISSING meta descriptions across the full category layer.

**Fix:**
1. Write 100–200 word category introductions for each of the 34 categories — add them via WooCommerce's taxonomy description field
2. Add unique meta descriptions to each category via Yoast
3. Ensure Yoast's "Drones Archives - TechWhizMart" default title template is replaced with a keyword-targeted title (e.g., "Buy Drones Online | TechWhizMart")

### [CRITICAL] 163 pa_* attribute archive pages are indexable — massive thin-page factory

**Evidence:** The sitemap_index.xml contains exactly 163 `pa_*` attribute sitemaps alongside the standard 7 content sitemaps (posts, pages, products, categories, tags, product categories, product tags, author). These attribute archives include:
- `pa_date-first-available` — an archive of products filtered by their Amazon import date
- `pa_domestic-shipping`, `pa_international-shipping` — shipping attributes indexed as pages
- `pa_pricing` — a pricing attribute archive
- `pa_furniture-finish`, `pa_room-type`, `pa_shelf-type` — furniture attributes (confirms off-niche product import history)
- `pa_deck-length`, `pa_deck-width` — skateboard/furniture attributes
- Many duplicates: `pa_color` + `pa_color-name` + `pa_colour` (three overlapping color attributes)

Each of these is an indexable URL in Yoast's sitemap, meaning Googlebot is asked to crawl potentially thousands of thin attribute-filtered archive pages (e.g., `/product/attribute/pa_color/black/`) that each contain a small subset of already-thin product pages.

**Fix (High Priority):**
1. In Yoast SEO > Search Appearance > Taxonomies, set all `pa_*` taxonomy archives to "noindex" (show in search results: No)
2. Remove all `pa_*` sitemaps from the sitemap_index (Yoast will do this automatically once noindexed)
3. Consolidate duplicate attribute taxonomies (`pa_color`, `pa_color-name`, `pa_colour` should be one taxonomy)

**Expected impact:** Removing ~163 sitemap entries and potentially thousands of thin archive pages dramatically improves the site's quality signal to Googlebot.

### [MEDIUM] 34 product categories — off-niche categories should be removed

| Off-niche Category | Recommendation |
|---|---|
| `minibars` | Remove — furniture/liquor category has no place in a tech store |
| `hobbies-stuff` | Rename or restrict to tech hobbies (RC vehicles, telescopes) |
| `subscriptions` | Clarify scope — software subscriptions are on-niche, game subscriptions borderline |

The remaining 31 categories are broadly on-niche (audio, drones, gaming, laptops, peripherals, smart home, etc.).

---

## 5. Homepage

### [HIGH] Homepage broken product widget

**Evidence:** The "Today's Popular Picks" section and "Popular Categories" section on the homepage both render "No products for this criteria." in the HTML source. The Elementor widgets referencing WooCommerce product queries are returning empty results — likely because:
- The widget query targets a tag/category that has no published products assigned to it, OR
- The widget is misconfigured after the bulk product trash operation removed products from the expected query scope

**Impact:** The homepage — the most important page for brand impression and PageRank — shows empty product slots to both users and Googlebot. This is a crawlability and trust signal failure on the most-linked page of the site.

**Fix:**
1. Identify the Elementor widget query parameters (likely filtered by a specific tag or featured status)
2. Assign products to the expected category/tag, OR change the widget query to "latest products" or "featured products" with at least 8 live products assigned
3. Test with WooCommerce's featured product functionality

### [HIGH] Homepage has no H1

**Evidence:** `H1 count: 0` confirmed via HTML parse. The page renders a logo image and a hero section but no `<h1>` element. This is a confirmed finding from the shared context.

**Fix:** Add an H1 to the hero section. Suggested: "Shop Cutting-Edge Tech Gadgets & Electronics" — this consolidates the brand promise with the primary keyword target.

### [MEDIUM] Homepage only has 13 internal links

**Evidence:** 13 internal links total on the homepage — this is very low for an e-commerce store. The links go to: wishlist, cart, blog, and a handful of category links. No product links are visible (because the product widget is broken).

**Fix:** Once the product widget is fixed, the homepage will gain 8–16 product links automatically. Additionally add category navigation links in the hero section or a featured categories grid.

---

## 6. Product Schema Validation (scripts/schema_ecommerce_validate.py)

Three products tested: JBL Charge 4 (live), DOQAUS Headphones (live), Marcy Gym Machine (trashed).

**Note:** The validator script has a bug at line 194 (`'list' object has no attribute 'get'` when `priceSpecification` is an array rather than a dict). Validation was run manually against the same rule set.

### Schema Validation Results

| Field | JBL Charge 4 | DOQAUS Headphones | Marcy Gym [TRASHED] |
|---|---|---|---|
| `name` | PASS | PASS | PASS |
| `description` | PASS | PASS | PASS |
| `image` | FAIL (broken URL) | FAIL (broken URL) | FAIL (broken URL) |
| `offers.price` (via priceSpec) | PASS | PASS | PASS |
| `offers.priceCurrency` | PASS | PASS | PASS |
| `offers.availability` | PASS (InStock) | PASS (InStock) | FAIL (InStock on deleted product) |
| `hasMerchantReturnPolicy` | MISSING | MISSING | MISSING |
| `shippingDetails` | MISSING | MISSING | MISSING |
| `brand` | MISSING | MISSING | MISSING |
| `aggregateRating` | MISSING | MISSING | MISSING |
| `gtin` / `mpn` | MISSING | MISSING | MISSING |
| `seller` | PASS | PASS | PASS |

**Status: FAIL (all three products)**

| Severity | Count | Issues |
|---|---|---|
| Critical | 1–2 | Broken image URL (all); InStock on trashed product |
| High | 2 | `hasMerchantReturnPolicy` absent (all); `shippingDetails` absent (all) |
| Medium | 3 | `brand` missing; `aggregateRating` missing; `gtin`/`mpn` missing |
| Info | 1 | No `ProductGroup` for variant-supporting products |

**`hasMerchantReturnPolicy` and `shippingDetails` are required by Google for merchant listing eligibility.** Their absence means none of the store's 311 live products qualify for Google Shopping rich results, even if the image issue were fixed.

**Fix:**
1. Add `hasMerchantReturnPolicy` to every product's WooCommerce schema output (requires a custom plugin or Yoast configuration snippet) — minimum fields: `applicableCountry`, `returnPolicyCategory`
2. Add `shippingDetails` with `shippingDestination` and `deliveryTime` — WooCommerce Shipping Zones data can populate this
3. Fix image URLs (see section 3 above)
4. Add `brand` property — importable from the WooCommerce brand taxonomy or a custom field
5. Add `gtin13` or `mpn` where available — critical for Google Shopping deduplication

---

## 7. Additional Findings

### [MEDIUM] Sitemap protocol mismatch — http:// in sitemap_index

All `<loc>` entries in `sitemap_index.xml` use `http://techwhizmart.com` (HTTP) while the canonical site serves over HTTPS. This causes Googlebot to follow HTTP URLs from the sitemap before being redirected to HTTPS — wasting crawl budget on redirects for every sitemap-discovered URL.

**Fix:** Regenerate Yoast sitemaps after ensuring WordPress's Site URL is set to `https://` in Settings > General.

### [MEDIUM] 164 post_tags + 1 product_tag sitemap — tag bloat

164 post tags are indexed as separate archive pages. In combination with the pa_* archives, the site has hundreds of thin taxonomy pages competing for crawl budget.

**Fix:** Noindex post tags in Yoast SEO > Search Appearance > Taxonomies > Post Tags > "No".

### [LOW] schema_ecommerce_validate.py has a bug (line 194)

The script assumes `priceSpecification` is a dict but WooCommerce outputs it as a list of `UnitPriceSpecification` objects. The script raises `AttributeError: 'list' object has no attribute 'get'` and exits with code 2 on any WooCommerce product schema. Line 193–196 needs to handle both dict and list for `priceSpecification`.

---

## Priority Fix Roadmap

| Priority | Issue | Effort | Expected Impact |
|---|---|---|---|
| P0 Critical | 410 all `__trashed` URLs (server rule) | Low (1 htaccess rule) | Eliminates 360 thin pages; fixes InStock false claim |
| P0 Critical | Fix broken image URLs (importer bug) | Medium | Enables product images to load and be crawled |
| P1 High | Noindex all 163 pa_* attribute archives in Yoast | Low (UI setting) | Removes hundreds of thin pages from index |
| P1 High | Add `hasMerchantReturnPolicy` + `shippingDetails` to schema | Medium | Enables Google merchant listing rich results for all products |
| P1 High | Fix homepage broken product widget | Low | Restores key homepage content for users and Googlebot |
| P1 High | Add H1 to homepage | Low | Basic on-page ranking signal |
| P2 High | Write original product descriptions (no Amazon boilerplate) | High (ongoing) | Improves E-E-A-T; reduces duplicate content risk |
| P2 High | Add category descriptions to all 34 PLPs | Medium | Converts thin PLPs to indexable content pages |
| P2 High | Remove/noindex off-niche live products | Medium | Restores topical coherence |
| P3 Medium | Add `brand`, `aggregateRating`, `gtin`/`mpn` to schema | Medium | Improves Shopping eligibility and rich result completeness |
| P3 Medium | Enable related products widget | Low | Improves internal linking and session depth |
| P3 Medium | Fix sitemap_index to use https:// | Low | Removes redirect waste from sitemap crawl |
| P4 Low | Consolidate duplicate pa_* attributes (color/colour/color-name) | Low | Reduces taxonomy noise |
