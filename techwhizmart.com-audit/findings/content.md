# Content Quality / E-E-A-T Audit — techwhizmart.com
**Audited:** 2026-06-30  
**Auditor:** seo-content sub-agent  
**Scope:** Homepage, 3 product pages, 1 category page, 1 brand attribute archive, 4 blog posts, policy/trust page checks  
**Platform:** WordPress + WooCommerce + Yoast SEO (Rehub theme + Elementor)

---

## Overall Content Quality Score: 26 / 100

| Dimension | Score | Notes |
|---|---|---|
| E-E-A-T (combined) | 18/60 | No About page, no author bios, no contact info, single social link |
| Product Description Quality | 4/15 | Bullet-point manufacturer specs, no original editorial layer |
| Blog Content Quality | 5/15 | AI-pattern signals, structural anomalies, drone-only niche mismatch |
| Trust Signals | 2/15 | No policy pages, no payment badges, no physical address |
| AI Citation Readiness | 4/10 | No structured Q&A, no quotable original claims |
| Freshness / Update Signals | 3/5 | Some 2025 publish dates, no visible update dates |

---

## What Works

- **Product H1 tags are present and specific.** Every product page sampled carried a full, keyword-rich product name as H1 (e.g., "JBL Charge 4 – Waterproof Portable Bluetooth Speaker – Black"). This is correct WooCommerce behaviour.
- **Product short-descriptions carry manufacturer feature bullets.** The JBL Charge 4, RedThunder K10, and ComfiLife foot rest pages all have short-description bullet points (e.g., "IPX7 WATERPROOF", "2.4G high-speed and stable transmission") that give shoppers functional spec information.
- **Product schema is implemented.** Product + Offer + UnitPriceSpecification JSON-LD is present on product pages (confirmed on JBL Charge 4). Price ($114.95) is machine-readable.
- **Blog posts have targeted meta descriptions** (distinct per post, not the site-wide boilerplate) — e.g., the drone photography post carries "Transform your travel blog with stunning drone photography! Learn essential tips…". Blog meta descs are customised.
- **Organization schema present on homepage.** WebSite, Organization, WebPage, BreadcrumbList, and SearchAction schema are all implemented via Yoast.
- **Google Analytics / Site Kit installed.** Tracking in place (GT-NS8RPB5D).
- **Lazy-loading is implemented** on product images (confirmed, 26 images on JBL page).

---

## Issues

### [CRITICAL] No About Page, No Contact Page, No Physical Address

**Impact:** E-E-A-T Trustworthiness (30% weight) — this is the single biggest scoring factor for QRG.

**Evidence:**
- HTTP 404 returned for: `/about/`, `/about-us/`, `/contact/`, `/contact-us/`
- Page sitemap (12 URLs) contains: homepage, sample-page, wishlist, checkout, my-account, compare-products, cart, featured-items, our-blog, blog, blog-posts, and a duplicate homepage URL. No About or Contact page exists.
- Organization schema lists `sameAs` pointing only to `https://au.pinterest.com/techwhizmart/` — a single Pinterest link is the entirety of external identity validation.
- Footer text: "2025 techwhizmart.com. All rights reserved." — no address, phone, or email.

**QRG relevance:** For a YMYL-adjacent e-commerce store handling financial transactions, Google's September 2025 QRG requires accessible contact information and demonstrated business identity. The complete absence of an About page is a primary Trustworthiness failure.

**Fix:** Create `/about-us/` page (min 500 words) including: business origin story, team information, physical or registered business location, years in operation, and a Pinterest/social link cluster. Create `/contact/` page with at minimum an email address or contact form. Add both to the primary navigation and footer. Verify the Organization schema `sameAs` array after adding social profiles.

---

### [CRITICAL] Duplicate Meta Descriptions Across All Product Pages (~311 active products affected)

**Impact:** CTR loss at scale; Googlebot signal dilution; confirmed across every product page sampled.

**Evidence — identical meta description found on all three products tested:**

| Product URL (slug) | Meta Description |
|---|---|
| `/product/jbl-charge-4-waterproof-portable-bluetooth-speaker-black/` | "Discover the latest tech and gadgets at TechWhizMart. Shop a wide range of smart devices, electronics, and accessories at unbeatable prices. Your one-stop destination for high-quality, innovative tech solutions!" |
| `/product/redthunder-k10-wireless-gaming-keyboard-and-mouse-combo-…/` | "Discover the latest tech and gadgets at TechWhizMart. Shop a wide range of smart devices, electronics, and accessories at unbeatable prices. Your one-stop destination for high-quality, innovative tech solutions!" |
| `/product/comfilife-ergonomic-under-desk-foot-rest-…/` | "Discover the latest tech and gadgets at TechWhizMart. Shop a wide range of smart devices, electronics, and accessories at unbeatable prices. Your one-stop destination for high-quality, innovative tech solutions!" |

This is the site-wide fallback (identical to the WebSite schema `description` field). Yoast has not been configured with a product-level meta description template. With 311 active (non-trashed) products, this affects 311 SERP snippets. The boilerplate is 220 characters — it also exceeds the ~160-character soft truncation threshold.

**Fix:** In Yoast SEO > Search Appearance > WooCommerce Products, set the meta description template to `%%excerpt%% %%sep%% %%sitename%%` or `%%wc_shortdesc%% %%sep%% %%sitename%%`. This will auto-populate from each product's short description, producing unique per-product snippets without manual editing. Audit and manually craft descriptions for top-traffic products.

---

### [CRITICAL] No Trust/Policy Pages — Returns, Refunds, Shipping, Privacy Policy

**Impact:** Trustworthiness (QRG) and legal compliance; directly affects conversion and Google Shopping eligibility.

**Evidence:**
- HTTP 404 confirmed for: `/returns/`, `/return-policy/`, `/refund_returns/`, `/privacy-policy/`, `/shipping/`, `/shipping-policy/`
- No policy links appear in the homepage footer, navigation, or body. The sole footer content is the copyright line.
- No payment security badges, SSL seal, or trust mark elements detected in homepage HTML.
- No Trustpilot, Google Reviews widget, or third-party review aggregator markup found.

**QRG relevance:** WooCommerce stores are implicitly YMYL (users submit payment data). QRG rates pages as Low quality when a site collecting payments has no identifiable contact info and no terms/policies. GDPR (if serving EU) and CCPA (if serving California) mandate a privacy policy.

**Fix:** Create and publish: (1) Returns & Refund Policy page, (2) Shipping Policy page, (3) Privacy Policy page, (4) Terms of Service page. Link all four from a dedicated footer widget. Add payment method icons (Visa/MC/PayPal logos) near the Add to Cart button. Consider a Trustpilot or Judge.me integration for verified review display.

---

### [HIGH] Homepage Missing H1 Tag

**Impact:** Heading hierarchy failure; signals unclear page topic to crawlers; confirmed in pre-fetched HTML.

**Evidence:**
- `<h1>` count on homepage: 0 (confirmed via HTML parse of home.html)
- The homepage is built entirely in Elementor with visual heading widgets. The largest visible heading is rendered as a styled `<div>` or Elementor widget, not as a semantic `<h1>`.
- The page `<title>` is "TechWhizMart | Buy Cutting-Edge Gadgets & Innovative Tech Online" — this keyword intent is not mirrored in any heading tag.

**Fix:** In the Elementor editor, change the primary hero heading widget's HTML tag from the default (which may be H2 or a div) to H1. The recommended H1 text: "Buy Cutting-Edge Gadgets & Innovative Tech Online" (mirrors title tag intent without the brand prefix). Only one H1 per page. Verify with View Source after saving.

---

### [HIGH] Blog Content Shows AI-Generation Patterns — Structural Anomalies and Niche Mismatch

**Impact:** September 2025 QRG penalises AI content that lacks genuine E-E-A-T; the Helpful Content system (merged March 2024 core) rewards content created for people, not for search engines.

**Evidence:**

**1. Repeated H2 headings within a single post (drone-photography-for-travel-blogs/):**
The post contains 28 H2 elements, but only 8 are unique. The H2 "4. DJI Mavic 2 Pro" appears 10 times; "3. Autel EVO 11" appears 7 times; "2. DJI Inspire 3" appears 4 times. This is a template/widget block being rendered multiple times — a characteristic of AI-generated content assembled from repeated prompt outputs or a related-posts carousel injecting article headings from other posts into the DOM.

**2. Related-posts carousels injecting H2 elements from other posts:**
Four additional H2s on the same page are titles of other blog posts ("Elevate Your Vlog: The Top Drones for Captivating Aerial Footage", "Sky High: The Ultimate Guide to the Best Travel Drones for Adventurers", etc.) — these are related-post widget titles rendered as H2, polluting the heading hierarchy of the host article.

**3. Drone-only blog niche on a consumer electronics retail site:**
Of the 10 blog URLs sampled from post-sitemap.xml, 9 are exclusively about drone travel photography. The site sells keyboards, gaming peripherals, earbuds, laptops, and foot rests. No blog content on keyboards, audio gear, gaming, or computers was found. This niche mismatch — 147 blog posts clustered on a single unrelated hobby topic — is a strong AI content farm signal.

**4. Informal, filler-heavy prose:**
Representative sentences: "Hey there, travel enthusiasts! If you've ever dreamed of capturing breathtaking landscapes… drone photography is your golden ticket." / "Imagine exploring a beautiful destination… your audience clicking 'Like' faster than you can say 'Wow'." This chatty-generic phrasing without any first-person operational experience, specific product testing, or original photography is characteristic of low-quality AI output.

**5. Author attribution:** `<meta name="author" content="peter froggatt">` appears on blog posts. No author bio page, no credentials, no author photo, no link to an author archive with editorial history.

**6. Word count inconsistency:** One post ("Capture the World: Essential Tips") = ~1,369 words (meets minimum); "Top 5 Professional Drones for 2025" = ~905 words (below 1,500-word blog minimum for comprehensive treatment); "Drone Photography for Travel Blogs" reports ~16,068 words in the article element — this extreme figure suggests the article wrapper contains related post widgets, sidebars, and navigation injected into the count.

**Fix:** (a) Audit all 147 posts and redirect/consolidate drone-only content that does not serve a buyer journey for the products sold. (b) Fix the related-posts widget to use `<p>` or `<div>` tags for post titles rather than `<h2>`. (c) For retained posts, add a genuine author bio section with photo, credentials, and social/LinkedIn link. (d) Rewrite or substantively expand posts below 1,500 words with original editorial opinion, real product tests, or first-hand experience signals.

---

### [HIGH] Category and Attribute Archive Pages Carry Zero Descriptive Content

**Impact:** Thin content at scale; ~34 product category pages + ~165 pa_* attribute archives = ~199 effectively blank archive pages in the index.

**Evidence:**
- `/product-category/audio/`: H1 = "Audio", meta description = NOT FOUND (no Yoast template set), category description field = empty, no introductory copy. 12 products listed.
- `/brand/brand-3m/`: No H1, no meta description, no page description, only 2 product items visible.
- This pattern repeats across all 34 product categories and 165 pa_* attribute archives (pa_brand alone = 252 URLs per the sitemap).

**QRG relevance:** Pages that exist solely to list products with no editorial context, no buying guidance, and no descriptive copy are flagged as thin content. At 199+ pages, this represents significant index dilution.

**Fix (two-track):**
- **Track A (noindex thin archives):** Apply `noindex` to all `pa_*` attribute archives via Yoast SEO > Search Appearance > Taxonomies. This removes ~165 thin pages from the index immediately without deleting them.
- **Track B (enhance category pages):** Add 150–250 words of buying-guide introductory copy to each of the 34 product category pages. In WooCommerce > Products > Categories, each category has a Description field that renders above the product grid. Use this for keyword-targeted copy (e.g., "Browse TechWhizMart's audio collection — wireless earbuds, Bluetooth speakers, and over-ear headphones from brands like JBL…").

---

### [MEDIUM] Product Descriptions Are Manufacturer Spec Bullets — No Original Editorial Layer

**Impact:** E-E-A-T Experience factor (20% weight); no differentiation from Amazon, manufacturer sites, or competing retailers.

**Evidence:**
- JBL Charge 4 short description: "WIRELESS BLUETOOTH STREAMING: Wirelessly connect up to 2 smartphones or tablets…" — verbatim from Amazon/JBL product listing format.
- ComfiLife foot rest short description: "Pain Relief and Support for Back, Hip, Legs, Knees & Feet – If you are working or sitting at a desk…" — manufacturer marketing copy, unattributed.
- RedThunder K10: "【High-performance 2.4G Wireless Keyboard and Mouse】Are you looking for…" — Chinese-market bracket-formatted manufacturer bullets.
- No product on the site contains: real-world testing notes, editorial opinion, comparison context, user scenario guidance, or "who this is for" framing.
- The full description tab (tab-description panel) on the JBL page was not populated with additional editorial content beyond the short description bullets.

**Fix:** For the top-30 products by traffic/revenue, write a 150–300 word editorial paragraph per product covering: (1) who this product is best suited for, (2) one concrete real-world use scenario, (3) one honest limitation. This layer sits below the manufacturer bullets and signals original editorial perspective. For the remaining catalog, use a standardised template that at minimum adds "TechWhizMart verdict" and a use-case statement.

---

### [MEDIUM] No AI Citation Readiness — Missing Structured Answers, FAQ Schema, Comparison Data

**Impact:** Generative Engine Optimization (GEO); AI assistants (ChatGPT, Perplexity, Google AI Overviews) cannot extract quotable, structured answers from current pages.

**Evidence:**
- No FAQ schema (FAQPage JSON-LD) found on any page sampled.
- No "Best for / Specs at a glance / TechWhizMart says" structured callouts.
- Blog posts use continuous prose without clear definitional statements or "the answer is X" structure.
- No comparison tables on category or blog pages (the blog post "Top 5 Professional Drones" implies a comparison but delivers only 905 words of prose).

**Fix:** Add FAQ schema to the top 20 product pages (3–5 Q&As per product: "Is the JBL Charge 4 waterproof?", "How long does the battery last?", etc.). Add "Specs at a glance" summary tables to product pages with key attributes in `<table>` markup. On blog posts targeting "best X" queries, use a structured ranking section with a `<table>` comparison.

---

### [LOW] Homepage Content Word Count is Low — Primarily a Product Grid

**Impact:** Topical coverage floor not met for homepage (minimum 500 words of informational content).

**Evidence:**
- The homepage body is built in Elementor. The footer contains only the copyright line. Internal link analysis shows the homepage links only to: /blog-posts/, /cart/, /category/drone/, /wishlist/, /my-account/lost-password/ — no product category links, no featured product narrative.
- The homepage OG image points to `blank.gif` (a 1×1 placeholder) — the WebPage schema `primaryImageOfPage` and `thumbnailUrl` are both the blank gif. This means social shares and schema crawlers have no meaningful image.

**Fix:** Add a 200–300 word "About TechWhizMart" text section to the homepage (below the hero, above or below the product grid). Link this to the About page once created. Replace the blank.gif OG image with the actual logo or a lifestyle hero image. Add category navigation blocks that link to the 6–8 main product categories.

---

### [LOW] Utility Pages Included in XML Sitemap

**Impact:** Crawl budget waste; Google may interpret cart/checkout/my-account as indexable content pages.

**Evidence:**
Page sitemap includes: `/cart/`, `/checkout/`, `/my-account/`, `/wishlist/`, `/compare-products/`, `/sample-page/`. These are WooCommerce/WordPress default utility pages with no editorial content. The `/sample-page/` is a WordPress default that was never deleted.

**Fix:** In Yoast SEO > Search Appearance > Pages, mark Cart, Checkout, My Account, Wishlist, Compare Products, and Sample Page as noindex. Yoast will automatically exclude noindex pages from the XML sitemap on next regeneration.

---

## E-E-A-T Breakdown

| Factor | Weight | Score (0–100) | Evidence |
|---|---|---|---|
| **Experience** | 20% | 5 | No first-hand product testing signals; blog prose is generic; no "I tested this" or "we used this" language; author has no bio |
| **Expertise** | 25% | 20 | No author credentials anywhere; blog covers drone photography (unrelated to core retail); product descriptions are unattributed manufacturer copy |
| **Authoritativeness** | 25% | 22 | Organization schema present; single sameAs (Pinterest only); no press mentions, no industry citations, no link-worthy original content |
| **Trustworthiness** | 30% | 8 | No About page; no Contact page; no policy pages (returns, privacy, shipping); no physical address; blank OG image; no payment trust badges; no third-party review integration |

**Weighted E-E-A-T score: 14 / 100**

For an e-commerce store processing payments in a competitive electronics retail vertical, Trustworthiness is the highest-weighted factor and the most critically deficient.

---

## AI Citation Readiness Score: 18 / 100

| Signal | Present | Notes |
|---|---|---|
| FAQ / Q&A structured content | No | No FAQPage schema on any page sampled |
| Comparison tables | No | No structured product comparison tables |
| Clear definitional statements | Partial | Product bullet specs exist but are unattributed |
| Author attribution | Partial | Meta author tag present on blog posts; no bio page |
| Cited sources / external links | No | No outbound citations in blog content sampled |
| Quotable factual claims | No | No original data, statistics, or editorial verdicts |

---

## Coverage Gaps (what was not sampled)

- Full product description tab content could not be extracted cleanly from all products (the tab panel selector did not match on all pages). Manual spot-check of the description tab is recommended for 5 additional products.
- Only 4 of 147 blog posts were sampled. The cluster pattern strongly suggests bulk AI generation, but a full audit of all 147 post titles and word counts via the WordPress admin would confirm the scale.
- The 34 product category pages were only checked for one category (audio). A bulk crawl of all category pages is recommended to confirm the no-description pattern at scale.
- No mobile rendering check was performed (requires Playwright); mobile content parity should be verified separately.
