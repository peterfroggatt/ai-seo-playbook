# Sitemap Architecture Audit — techwhizmart.com
**Audit date:** 2026-06-30
**Platform:** WordPress + WooCommerce + Yoast SEO
**Sitemap index:** https://techwhizmart.com/sitemap_index.xml

---

## What Works

- Sitemap index is valid, well-formed XML (Yoast-generated, correct sitemapindex namespace).
- `lastmod` timestamps are present on every child sitemap entry in the index — values are date-specific (not all identical), indicating Yoast is writing real modification timestamps.
- HTTPS canonical URLs are used inside child sitemaps themselves (e.g. `https://techwhizmart.com/product/jbl-charge-4.../`) even though the index references them via http://.
- Product categories (product_cat, 34 URLs) and blog post-tags (164 URLs) are split into separate child sitemaps — clean separation of content types.
- Core product pages that are valid (311 non-trashed products) are included.
- Blog posts (147 URLs) and product categories (34 URLs) are appropriate inclusions.

---

## Issues

### [CRITICAL] 360 Trashed Products Indexed — HTTP 200, No Noindex Signal

**Count:** 360 of 671 product sitemap URLs (53.7%) contain `__trashed` in the slug.

**Evidence:**
- Confirmed via `grep -c '__trashed' product-sitemap.xml` = 360.
- Three sampled trashed URLs all returned HTTP 200 (not 410 or 404):
  - `https://techwhizmart.com/product/marcy-smith-cage-machine-.../__trashed/` → 200
  - `https://techwhizmart.com/product/roblox-digital-gift-card-.../__trashed/` → 200
  - `https://techwhizmart.com/product/8bitdo-sn30-pro-.../__trashed/` → 200
- The sampled trashed product page carries `<meta name='robots' content='index, follow, ...'>` — fully indexable.
- Page title shows the original product name (e.g. "8Bitdo Sn30 Pro Bluetooth Controller..."). Body class confirms `single-product postid-3693` — WordPress is serving full product template.
- Canonical self-references the `__trashed` URL, meaning Google is invited to index the `__trashed` slug as a distinct URL.

**Impact:** Google crawls and indexes 360 product pages with `__trashed` in the URL — garbage slugs that will never rank, waste crawl budget, and dilute product index quality. These are deleted products that WordPress marks as "trashed" but does not serve as 410/404.

**Fix:**
1. In WordPress Admin, permanently delete (not just trash) all trashed WooCommerce products, OR
2. Add a server rule (Nginx/Apache) to return 410 Gone for any URL matching `__trashed`.
3. Remove all `__trashed` URLs from the sitemap immediately by regenerating the Yoast sitemap after permanent deletion.
4. Yoast Sitemap settings: confirm "Post Types" > Products only includes `publish` status (Yoast should not include trashed posts — this suggests the products were trashed *after* the sitemap was cached, or a Yoast sitemap cache bug is at play; clear Yoast sitemap cache after cleanup).

---

### [CRITICAL] 163 pa_* Attribute Archive Sitemaps — Thin/Junk Pages Flooding the Index

**Count:** 163 `pa_*` (WooCommerce product attribute) child sitemaps listed in the index, out of 171 total child sitemaps (95% of all child sitemaps are attribute archives).

**Estimated total attribute URLs:** Sampled 7 sitemaps — pa_brand=252, pa_item-weight=240, pa_color=31, pa_special-feature=20, pa_operating-system=8, pa_connector-type=2, pa_connectivity-technology=1. Conservative average ~50 URLs/sitemap across 163 sitemaps = approximately 8,000–13,000 attribute archive URLs in total.

**Evidence of thin/junk content:**
- `https://techwhizmart.com/color/balck/` — URL slug is a typo ("balck" instead of "black"). Page title: "Balck Archives - TechWhizMart". This is a live, indexed archive page for a misspelled WooCommerce attribute value.
- `https://techwhizmart.com/color/01-magnetic/` — attribute value is a product code fragment, not a color.
- `https://techwhizmart.com/color/black%ef%bc%88265lb-15w-belt%ef%bc%89/` — URL-encoded Japanese full-width brackets wrapping a product spec string used as a "color" attribute.
- `https://techwhizmart.com/brand/brand-adyoom/`, `brand-baoinse/`, `brand-benfu/` — micro-brands with likely 1–3 products each; no editorial content.
- pa_* attributes include: `pa_date-first-available`, `pa_domestic-shipping`, `pa_international-shipping`, `pa_manufacturer-part-number`, `pa_national-stock-number`, `pa_pricing`, `pa_release-date` — these are data fields, not navigational categories. Archive pages for "release date = 2024-09-28" have zero search intent.
- `https://techwhizmart.com/brand/brand-3m/` returned a 404-style page ("Page not found - TechWhizMart") with HTTP 200 — confirmed soft 404 behaviour on attribute archives.

**Impact:** Thousands of thin, auto-generated archive pages submitted to Google. Pages are either soft-404s, typo URLs, or product-spec-as-taxonomy pages with no search demand. Classic crawl budget drain and index quality signal dilution. Google may apply a quality filter across the entire site.

**Fix (Yoast):**
1. Yoast SEO > Search Appearance > Taxonomies: set ALL `pa_*` taxonomies to "No" for "Show in search results" (this adds noindex AND removes from sitemap automatically).
2. Do this for every attribute taxonomy — there is no shortcut; each must be disabled individually, OR use the Yoast REST API / WP-CLI to bulk-update.
3. After saving, regenerate Yoast sitemaps. All 163 pa_* child sitemaps will disappear from the index.
4. Request removal of already-crawled attribute pages via Google Search Console URL Removal tool (temporary block while noindex propagates).

---

### [HIGH] Protocol Inconsistency — Child Sitemaps Declared as http:// in a https:// Site

**Evidence:** Every `<loc>` entry in `sitemap_index.xml` uses `http://`:
```
<loc>http://techwhizmart.com/post-sitemap.xml</loc>
<loc>http://techwhizmart.com/product-sitemap.xml</loc>
... (all 171 child sitemaps)
```
The site itself is fully HTTPS (confirmed: home and product pages serve over HTTPS with 200 responses). The index was already noted in robots.txt as declaring `http://` too.

**Impact:** Googlebot will follow the http:// URL and be redirected (301) to https://, which it resolves correctly — so this is not a blocking error. However: (a) it creates unnecessary redirect hops during sitemap discovery; (b) it is a signal of stale/misconfigured Yoast settings; (c) GSC may report sitemap errors if it encounters protocol mismatches.

**Fix:**
- Yoast SEO > General > Features: ensure "XML Sitemaps" is enabled (it is — but the WordPress "Site Address (URL)" setting in Settings > General must be set to `https://techwhizmart.com` with no trailing slash). Once the WordPress site URL uses https://, Yoast will regenerate all sitemap `<loc>` values with https://. Flush Yoast sitemap cache after the change.

---

### [HIGH] Utility/Transactional Pages in Sitemap — Cart, Checkout, My Account, Wishlist, Compare, Sample Page

**Count:** 6 of 12 page-sitemap URLs are utility or junk pages that should never be indexed.

**Evidence from page-sitemap.xml:**
```
https://techwhizmart.com/cart/           (noindex confirmed via meta robots)
https://techwhizmart.com/checkout/       (302 redirect; noindex not confirmed on final destination)
https://techwhizmart.com/my-account/     (noindex confirmed via meta robots)
https://techwhizmart.com/wishlist/       (200, no noindex confirmed)
https://techwhizmart.com/compare-products/  (200, no noindex confirmed)
https://techwhizmart.com/sample-page/    (200, "index, follow" — WordPress default page, zero commercial value)
```

Additionally flagged from page-sitemap.xml:
- `https://techwhizmart.com/techwhizmart-buy-cutting-edge-gadgets-innovative-tech-online/` — a page whose slug is the full site tagline. Likely a duplicate homepage or an orphaned page created by mistake.
- `/our-blog/`, `/blog/`, `/blog-posts/` — three separate blog-related pages listed, suggesting redundant or orphaned blog index pages.

**Contradictions observed:** cart and my-account correctly carry `noindex` meta tags (Yoast/WooCommerce correctly applies them) yet they appear in the sitemap. This is a Yoast configuration failure: Yoast should automatically exclude noindexed pages from the sitemap, but is failing to do so here — likely because the page post type is included in the sitemap settings without filtering.

**Impact:** Submitting noindexed pages in a sitemap is contradictory and wastes Googlebot crawl allocation on the sitemap itself. Google will also soft-penalise sitemap quality if it consistently finds noindexed or low-value URLs.

**Fix:**
- Yoast SEO > Search Appearance > Content Types > Pages: ensure "Show in search results" is "Yes" but review which specific pages are excluded. Yoast should not include pages with noindex — check if a Yoast bug/version issue is causing this.
- Explicitly set the following pages to "noindex" in their individual Yoast meta box: cart, checkout, my-account, wishlist, compare-products, sample-page, and the tagline-slug page.
- Delete or redirect the orphaned blog index pages (`/our-blog/`, `/blog-posts/`) to `/blog/` if `/blog/` is the canonical blog index.
- Permanently delete the WordPress default "Sample Page".

---

### [HIGH] Author Sitemaps — Thin Authorship Pages for Non-Editorial Authors

**Count:** 2 author archive URLs.

**Evidence:**
```
https://techwhizmart.com/author/peter-froggatt/
https://techwhizmart.com/author/techwhizmart/
```
`techwhizmart` is the site/store account — an author archive for an e-commerce platform account has no editorial value. Neither author has a bio, byline prominence, or topical authority signals visible from the audit context.

**Impact:** Author archive pages on e-commerce sites are thin by nature. They surface a list of posts by a generic account with no E-E-A-T signals. If the authors are real human contributors with bios, they may be worth keeping — but the `techwhizmart` account archive is definitively a store account, not a person.

**Fix:**
- Yoast SEO > Search Appearance > Archives > Author archives: set to "Disabled" (noindex) if authors are not meaningful editorial contributors. This removes the author sitemap entirely.
- If `peter-froggatt` is a real editorial contributor with a genuine author bio page, consider keeping that one and disabling `techwhizmart`.

---

### [MEDIUM] Post Tag Sitemap — 164 URLs, Likely Thin Archives

**Count:** 164 post_tag URLs in post_tag-sitemap.xml.

**Context:** 147 blog posts generating 164 tags suggests nearly a 1:1 tag-to-post ratio — a classic over-tagging pattern where unique tags are created per post rather than reused as navigational taxonomy.

**Impact:** Most tag archives will have 1–3 posts. These are thin archive pages with no navigational search intent. Submitted to the index, they dilute quality signals.

**Fix:**
- Audit tag usage: consolidate tags to a small controlled vocabulary (20–40 tags maximum for 147 posts).
- Yoast SEO > Search Appearance > Taxonomies > Tags: set to "No" for search results (noindex + sitemap exclusion) until tags are consolidated into meaningful topic clusters.

---

### [MEDIUM] Category Sitemap — Duplicate and Structural Issues

**Count:** 9 WordPress `category` URLs (distinct from 34 WooCommerce `product_cat` URLs).

**Evidence from category-sitemap.xml:**
```
https://techwhizmart.com/category/drone/
https://techwhizmart.com/category/drones/        (duplicate: singular vs plural)
https://techwhizmart.com/category/post/          (meaningless system category)
https://techwhizmart.com/category/uncategorized/ (WordPress default)
```
"drone" and "drones" are likely the same category created twice. "post" and "uncategorized" are junk categories.

**Fix:**
- Merge `drone` and `drones` into a single canonical category. Redirect the removed slug.
- Set `uncategorized` to noindex in Yoast, or reassign all posts and delete it.
- Delete or redirect `/category/post/`.

---

### [LOW] Sitemap Index lastmod Values — Some Child Sitemaps Stale

**Evidence:** Several pa_* child sitemap entries carry `lastmod` dates from 2024-09-28 (oldest observed: `pa_computer-memory-type`, `pa_display-technology`, `pa_effective-still-resolution`, `pa_furniture-finish`, `pa_maximum-incline-percentage`, `pa_model`, `pa_noise`, `pa_phone-talk-time`).

The post-sitemap has a current `lastmod` of `2026-04-29` and the product-sitemap `2026-03-04`, indicating active content updates. The stale pa_* dates suggest these attribute archives have not had any products added since late 2024 — reinforcing that they are largely orphaned.

**Impact:** Low direct SEO impact (Google ignores lastmod accuracy signals for ranking), but stale dates are a signal of abandoned taxonomy structure.

---

### [INFO] priority and changefreq Tags

A quick check of the product sitemap confirms Yoast is not emitting `<priority>` or `<changefreq>` tags in child sitemaps — this is correct. Yoast removed these from its output in v17.x as Google confirmed it ignores them. No action required.

---

## URL Count Summary

| Sitemap | URLs | Keep? |
|---|---|---|
| product-sitemap.xml | 671 (311 valid + 360 trashed) | Keep 311 valid only |
| post-sitemap.xml | 147 | Keep |
| page-sitemap.xml | 12 (6 utility/junk) | Keep ~4 (home, featured-items, one blog index, about/contact if present) |
| product_cat-sitemap.xml | 34 | Keep |
| category-sitemap.xml | 9 (4 junk/duplicate) | Keep ~5 after cleanup |
| post_tag-sitemap.xml | 164 | Remove until tags consolidated |
| product_tag-sitemap.xml | 1 | Remove (only 1 tag — "books" — irrelevant to store) |
| author-sitemap.xml | 2 | Remove or reduce to 1 |
| pa_brand-sitemap.xml | 252 | Remove (noindex all pa_*) |
| pa_color-sitemap.xml | 31 | Remove |
| ~161 other pa_* sitemaps | ~8,000–13,000 est. | Remove all |
| **TOTAL (estimated)** | **~9,500–14,500** | **Target: ~500** |

---

## Recommended Sitemap Architecture (Post-Cleanup)

**Target: single clean sitemap index with 4 child sitemaps, ~500 total URLs.**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://techwhizmart.com/product-sitemap.xml</loc>
  </sitemap>
  <sitemap>
    <loc>https://techwhizmart.com/product_cat-sitemap.xml</loc>
  </sitemap>
  <sitemap>
    <loc>https://techwhizmart.com/post-sitemap.xml</loc>
  </sitemap>
  <sitemap>
    <loc>https://techwhizmart.com/page-sitemap.xml</loc>
  </sitemap>
</sitemapindex>
```

**Keep in sitemap:**
- Products (publish status only, no trashed) — target ~311 URLs
- Product categories (product_cat) — 34 URLs, review for thin/empty ones
- Blog posts — 147 URLs
- Key pages only: homepage, featured-items (if has real content), single canonical blog index — ~3–5 URLs

**Remove from sitemap (noindex or delete the underlying pages):**
- All 163 `pa_*` attribute sitemaps (noindex via Yoast taxonomy settings)
- All `__trashed` product URLs (permanently delete products in WP admin)
- Cart, checkout, my-account, wishlist, compare-products, sample-page (already partially noindexed — fix the sitemap inclusion bug)
- Author archives (set to noindex in Yoast)
- Post tags (164 URLs — noindex until consolidated)
- Product tags (1 URL — noindex)
- WordPress blog categories: drone/drones duplicate, uncategorized, post
- Orphaned pages: tagline-slug page, /our-blog/, /blog-posts/

---

## Yoast SEO Configuration Fixes (Priority Order)

1. **Settings > General > WordPress Address:** Change to `https://techwhizmart.com` → flush Yoast sitemap cache → fixes all http:// child sitemap URLs.

2. **Search Appearance > Taxonomies:** For every `pa_*` taxonomy (brand, color, connectivity-technology, etc.) — set "Show in search results" = No. This applies noindex AND removes from sitemap. Must be done for all 163 attribute types — use WP-CLI for bulk operation: `wp post-type list` + loop over `pa_*` taxonomies updating Yoast options.

3. **Search Appearance > Archives > Author Archives:** Set to Disabled (noindex) to remove author-sitemap.xml.

4. **Search Appearance > Taxonomies > Tags:** Set post tags to "No" until tag taxonomy is audited and consolidated.

5. **Permanently delete trashed products** in WooCommerce (Products > Trash > Empty Trash). After deletion, regenerate Yoast sitemap (Yoast SEO > Tools > Reindex or install Yoast SEO > XML Sitemaps cache flush).

6. **Individual page noindex + sitemap exclusion:** In the Yoast meta box on each of: cart, checkout, my-account, wishlist, compare-products, sample-page, tagline-slug page — set "Allow search engines to show this page" = No.

7. **Delete or redirect orphaned blog index pages** (`/our-blog/`, `/blog-posts/`) to `/blog/` (or whichever is the canonical blog index). Set the winner as the canonical blog page.

8. **Merge duplicate categories:** Redirect `drones` to `drone` (or vice versa). Delete `uncategorized` and `post` categories after reassigning any posts.
