# GEO / AI-Search Readiness Audit — techwhizmart.com
**Audit date:** 2026-06-30
**Platform:** WordPress + WooCommerce + Yoast SEO (server-rendered)

---

## GEO Health Score

| Dimension | Weight | Score | Weighted |
|-----------|--------|-------|---------|
| Citability | 25% | 28/100 | 7.0 |
| Structural Readability | 20% | 42/100 | 8.4 |
| Multi-Modal Content | 15% | 30/100 | 4.5 |
| Authority & Brand Signals | 20% | 18/100 | 3.6 |
| Technical Accessibility | 20% | 72/100 | 14.4 |
| **TOTAL** | | | **37.9 / 100** |

**Overall GEO Readiness: 38 / 100 — Poor**

---

## What Works

- **Server-rendered HTML (no SPA).** All pages deliver complete HTML on first response. AI crawlers receive fully-parsed content without requiring JavaScript execution. Trafilatura and similar extractors can process pages cleanly.
- **robots.txt: no AI crawlers blocked.** The file contains only standard WooCommerce/WordPress disallow rules (`/wp-admin/`, `/*?add-to-cart=`, WC upload directories). GPTBot, OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended, and CCBot are all implicitly allowed under `User-agent: *` with no blocking directives.
- **Organization schema present** on every page via Yoast's `@graph` pattern — name, URL, logo. Product pages carry a second separate `Product` block with SKU, description, Offer with price/currency/availability/validThrough, and seller identity. BreadcrumbList is present sitewide.
- **Article schema on blog posts** with `datePublished`, `dateModified`, `headline`, `wordCount`, and `publisher` referencing the Organization node.
- **Blog H1 present on posts.** Sampled posts ("Best DSLR Cameras for Live Streaming in 2025", "The Best Drones for Travel Photography in 2025") each carry a single H1 matching the page title — correct for AI passage extraction.
- **Descriptive blog titles** use year-tagged, answer-intent patterns ("Best X for Y in 2025") that align with how LLMs surface buying-guide queries.
- **Meta robots: max-snippet:-1** is set sitewide, explicitly permitting AI systems to use unlimited text snippets in generated answers.

---

## Issues

### CRITICAL

#### C1 — No llms.txt file
**Evidence:** `GET https://techwhizmart.com/llms.txt` → HTTP 404. `/well-known/ai.txt` also 404.
**Impact:** AI agents (Claude, GPT-4o browsing, Perplexity) that respect llms.txt cannot discover the site's canonical content inventory, preferred citation context, or licensing intent. Without it, crawlers must infer structure from the sitemap alone; product and buying-guide pages may be deprioritised or cited with less confidence.
**Fix:** Create `/llms.txt` at the webroot. Minimum viable content:
```
# TechWhizMart — AI access guide
> TechWhizMart sells consumer electronics and tech gadgets online.

## Shop
- Shop all products: https://techwhizmart.com/shop/
- Categories: https://techwhizmart.com/product-category/

## Buying guides
- Blog / buying guides: https://techwhizmart.com/blog/
- Best DSLR cameras for live streaming: https://techwhizmart.com/best-dslr-cameras-for-live-streaming-in-2025/
- Best drones for travel photography: https://techwhizmart.com/the-best-drones-for-travel-photography-in-2025/

## About
- About TechWhizMart: https://techwhizmart.com/about/
```
Add a `Robots` section if you want to restrict training vs. inference use separately. Reference: llmstxt.org spec. Effort: 1 hour.

#### C2 — Product pages carry zero Product schema (Yoast schema absent; only a bare WooCommerce block)
**Evidence:** The product page (`/product/jbl-charge-4-waterproof-portable-bluetooth-speaker-black/`) has two JSON-LD blocks. The Yoast `@graph` block contains only `WebPage`, `ImageObject`, `BreadcrumbList`, `WebSite`, and `Organization` — no `Product` type. The second block is a bare WooCommerce-generated `Product` object that is missing:
- `brand` (no `@type: Brand` node)
- `aggregateRating` (no reviews/ratings exposed in schema)
- `review` array
- `gtin` / `mpn` (manufacturer part numbers)
- `category`
- `additionalProperty` for specs (battery life, waterproof rating, connectivity)

The `description` field in the Product block contains bullet-point text (three lines separated by `\n`) rather than a structured prose description, which reduces extractability.

The `thumbnailUrl` in the WebPage block points to a broken path (`wp-content/uploads/https://m.media-amazon.com/...`) — a concatenation error indicating the image was imported from Amazon without proper local storage.

**Impact:** AI systems that use Product schema to build product knowledge graphs (Google Shopping AI, Bing Copilot shopping tab, Perplexity shopping answers) will have an incomplete picture of this product. ChatGPT's shopping tools cannot surface brand, rating, or spec data for citations.
**Fix:** Install a WooCommerce schema plugin (e.g., Rank Math's WooCommerce module, or a dedicated Product schema plugin) that outputs full `Product` markup including `brand`, `aggregateRating`, `gtin`, and `additionalProperty` for each spec. Alternatively extend Yoast's schema output via `wpseo_schema_graph` filter. Effort: 4–8 hours developer time.

---

### HIGH

#### H1 — Homepage missing H1
**Evidence:** Confirmed in `_CONTEXT.md` from prior parse: 0 H1 tags on `https://techwhizmart.com/`. This is the most-crawled page; AI systems use the H1 to anchor their understanding of what the entity is and does.
**Impact:** Without an H1, AI crawlers cannot perform reliable passage-level extraction from the homepage. The homepage also cannot contribute a clean anchor text signal to entity resolution.
**Fix:** Add `<h1>Buy Cutting-Edge Gadgets & Consumer Tech Online</h1>` (or similar) as the primary visible hero heading. In the WordPress theme (rehub-theme), this is typically controlled in the page builder or `front-page.php`. Effort: 30 minutes.

#### H2 — Organization `sameAs` contains only one social profile (Pinterest AU)
**Evidence:** Homepage and product page `Organization` JSON-LD `sameAs` array: `["https://au.pinterest.com/techwhizmart/"]`. No Facebook, Instagram, YouTube, LinkedIn, Twitter/X, or Wikidata/Wikipedia entries.
**Impact:** AI entity resolution (used by Google's Knowledge Graph, ChatGPT's browsing, and Perplexity) depends on `sameAs` cross-references to confirm that "TechWhizMart" the schema entity matches "TechWhizMart" the social entity. A single Pinterest URL provides very weak disambiguation. The brand-mention correlation data shows YouTube presence correlates ~0.737 with AI citations — the strongest single signal — and this site has no YouTube `sameAs`. Reddit and Wikipedia presence are also highly correlated; neither is present.
**Fix:** Add all official social profiles to the `sameAs` array in Yoast SEO > Search Appearance > Knowledge Graph. At minimum: Facebook page URL, YouTube channel URL (create one if absent), LinkedIn company page, and Twitter/X handle URL. If the business has a Wikidata entity, add the Wikidata URL. Effort: 1–2 hours.

#### H3 — No FAQ schema on any blog or product page
**Evidence:** Checked blog post `best-dslr-cameras-for-live-streaming-in-2025/` and `the-best-drones-for-travel-photography-in-2025/`: `FAQPage` not found in either. Product page also has no FAQ block.
**Impact:** FAQ schema is the primary mechanism by which AI Overviews and Perplexity extract and attribute question-answer pairs directly from a page. Without it, even well-structured Q&A content is not machine-readable as discrete answer units.
**Fix:** Add `FAQPage` JSON-LD (or use a WordPress FAQ block plugin that outputs it) to: (a) all buying-guide blog posts — add 3–5 questions per post addressing "What is the best X for Y?", "How do I choose X?", "What specs matter for X?"; (b) all product pages — add questions like "Is the JBL Charge 4 waterproof?", "How long does the JBL Charge 4 battery last?". Effort: Plugin install 1 hour + content per post 20 min each.

#### H4 — Blog content has no author E-E-A-T signals and thin authorship
**Evidence:**
- Twitter meta tag reveals author as "peter froggatt" on DSLR post.
- Cell phones post schema shows author `techwhizmart` (the site username, not a real person name).
- No author biography pages with credentials, no author photo, no author `sameAs` linking to LinkedIn or social profiles.
- Article schema uses `@id` references to person nodes but those nodes contain only `name` — no `jobTitle`, `description`, `sameAs`, `image`.
**Impact:** AI citation systems (especially ChatGPT and Perplexity, which surface attribution) deprioritise content without verifiable human expertise. For YMYL-adjacent product recommendation content, lack of author credentials is a hard negative signal.
**Fix:** Create full author profiles for each contributor in WordPress Users > Profile. Add bio text (50–150 words), photo, and at minimum a LinkedIn URL. In Yoast, navigate to User > Yoast SEO > Social and add social profiles — these populate the Person schema `sameAs`. Effort: 2–4 hours.

#### H5 — Outdated "2021" slug with title rewritten to "2025"
**Evidence:** URL `https://techwhizmart.com/the-best-cell-phones-of-2021-a-comprehensive-guide/` has page `<title>` "The Best Cell Phones of 2025: A Comprehensive Guide" and H1 matching the 2025 title. The slug still says `2021`. The schema shows `datePublished: 2025-02-10`, `dateModified: 2025-03-03`. Products listed: iPhone 12 Pro Max, Samsung Galaxy S21 Ultra, Google Pixel 5, OnePlus 9 Pro, Samsung Galaxy A52 — all 2020–2021 generation devices, now four to five years old.
**Impact:** AI systems that evaluate content freshness will penalise pages where the slug, content, and date signals are inconsistent. Listing 2021-era phones in a "2025 guide" without acknowledging they are legacy devices creates a factual reliability signal that reduces citation probability. This is a strong anti-citation signal for tools like Perplexity that cross-reference product recency.
**Fix:** Either (a) redirect the old slug to a genuinely updated 2025 post with current phones (Pixel 9, iPhone 16, Galaxy S25), or (b) update the post content to explicitly address current models, add a "last reviewed" date block, and set up a 301 redirect from the `2021` slug to a new `2025` slug. Effort: 2 hours content + 30 minutes redirect.

---

### MEDIUM

#### M1 — Blog passages are below optimal citability length; no self-contained answer blocks
**Evidence:** Sampled paragraphs from DSLR post: 81w, 57w, 55w, 21w, 37w, 79w, 32w, 72w, 89w. The optimal AI citation passage length is 134–167 words. No single paragraph in the sampled content meets this threshold. The "Key Features" section uses a `<p>` intro followed by implied list items rather than a structured definition block. There are no bolded summary sentences, call-out boxes, or "In short:" answer openers that allow AI extractors to isolate a self-contained passage.
**Fix:** Restructure the opening of each buying guide section: lead with a direct answer sentence (40–60 words), then expand in the same paragraph to 134–167 words total. Example for DSLR post: "The Canon EOS 90D is the best DSLR for live streaming in 2025 because [X reason in 15 words]. Its 32.5 MP APS-C sensor and Dual Pixel CMOS AF deliver [specific outcome]..." Use `<strong>` for the lead sentence so screen readers and extractors recognise it as a summary. Effort: 30–45 min per post.

#### M2 — No external citations or sourced statistics in blog content
**Evidence:** DSLR blog post has zero non-social external links. Drones post links only to Amazon product pages (5 links). No links to manufacturer spec sheets, benchmark studies, regulatory bodies (FAA for drone regulations would be relevant), or primary sources for any claimed specifications.
**Impact:** AI systems that assess citation-worthiness use the presence of sourced claims as a quality signal. Content that makes assertions ("the Canon EOS 90D boasts excellent autofocus") without linking to evidence is treated as opinion rather than citable fact by systems like Perplexity that surface attributed answers.
**Fix:** For each factual spec claim, add a hyperlink to the manufacturer's official spec page or a credible tech publication review. For the drones post, link to FAA drone registration requirements. For camera posts, link to manufacturer datasheets or DPReview/RTings benchmark pages. Aim for 2–4 external citations per 1,000 words. Effort: 1–2 hours per post.

#### M3 — robots.txt has duplicate `User-agent: *` blocks and sitemap declared as HTTP
**Evidence:**
```
User-agent: *
Disallow: /wp-content/uploads/wc-logs/
...

# START YOAST BLOCK
User-agent: *
Disallow:
Sitemap: http://techwhizmart.com/sitemap_index.xml
```
Two issues: (1) duplicate `User-agent: *` stanzas — the Yoast block's empty `Disallow:` effectively overrides the first block for some crawlers, creating ambiguity about what is actually blocked; (2) the Sitemap directive uses `http://` not `https://`, which may cause crawlers to follow the HTTP URL and receive a redirect before accessing the sitemap.
**Impact:** While no AI crawler is currently blocked, the ambiguity between the two `User-agent: *` blocks could cause some crawlers to parse only the second (Yoast) stanza, missing the WooCommerce-specific disallows. The HTTP sitemap URL is a minor friction point.
**Fix:** Merge into a single `User-agent: *` stanza. Update the Sitemap directive to `https://techwhizmart.com/sitemap_index.xml`. In Yoast SEO settings, disable the Yoast robots.txt block and manage the file manually or through a dedicated robots.txt plugin. Effort: 30 minutes.

#### M4 — No brand presence on Wikipedia, Wikidata, YouTube, or Reddit
**Evidence:** `Organization.sameAs` contains only `https://au.pinterest.com/techwhizmart/`. No Wikipedia article found for "TechWhizMart". No Wikidata entity verifiable from schema. YouTube channel not referenced anywhere in site markup. Reddit brand mentions not detectable from on-page signals.
**Impact:** Brand mention correlation research shows YouTube mentions correlate ~0.737 with AI citation frequency — the strongest measurable signal. Wikipedia/Wikidata entity presence is required for Google's Knowledge Panel and strongly influences ChatGPT's factual recall of a brand. Without these, TechWhizMart is effectively an unknown entity to AI systems operating from pre-training knowledge; it can only be cited via live crawl, not from parametric memory.
**Fix:** (1) Create a YouTube channel and publish at minimum 4–6 product review or buying guide videos matching blog content — this is the single highest-ROI action for AI citation frequency. (2) Create a LinkedIn company page if not already present. (3) Engage on tech subreddits (r/gadgets, r/electronics) with helpful answers that naturally reference the site. (4) Once the brand has sufficient notability signals, consider a Wikidata stub. Effort: YouTube channel setup 4 hours; ongoing content creation.

#### M5 — Product page WebPage description is sitewide boilerplate
**Evidence:** The product page's `WebPage` schema `description` field reads: "Discover the latest tech and gadgets at TechWhizMart. Shop a wide range of smart devices, electronics, and accessories at unbeatable prices..." — identical to the homepage and confirmed boilerplate across products. The `Product` description field correctly contains product-specific bullet text, but the `WebPage` description (used by Google's Knowledge Graph and AI for page-level summarisation) is generic.
**Impact:** AI systems that use `WebPage.description` to summarise what a page is about will produce inaccurate or generic summaries for product pages, reducing the likelihood that a specific product query surfaces this page as a citation.
**Fix:** In Yoast SEO, ensure the product-level meta description (which populates `WebPage.description`) is unique per product. For WooCommerce, this is set in the Yoast SEO box on each product edit screen. The description should match the Product name and key specs: "Buy the JBL Charge 4 Waterproof Portable Bluetooth Speaker (Black) at TechWhizMart — IPX7 rated, 20-hour battery, USB charging, $114.95." Effort: 30 min setup + template for bulk editing.

---

### LOW

#### L1 — No RSL 1.0 or equivalent AI licensing declaration
**Evidence:** No `llms.txt`, no `<meta name="robots">` AI-specific licensing directives, no RSL 1.0 license file. Content is implicitly "all rights reserved" for AI training purposes.
**Impact:** AI training pipelines (CCBot, Common Crawl) will crawl but may not attribute licensing. For a store that benefits from AI citation, this is a missed opportunity to signal "allow inference, restrict training" — a common preference for e-commerce content.
**Fix:** Add to `llms.txt` (once created per C1): `License: https://creativecommons.org/licenses/by/4.0/` for buying guide content, or use RSL 1.0 to allow AI inference use while restricting bulk training. Effort: 15 minutes (bundled with C1 fix).

#### L2 — No Speakable schema on blog content
**Evidence:** No `Speakable` property found in any sampled blog post schema.
**Impact:** Google's voice and audio AI surfaces (`speakable` content) and some AI briefing tools preferentially cite content with `Speakable` markup. Buying guide posts are strong candidates.
**Fix:** Add `speakable` property to the `Article` schema on buying-guide posts, referencing the H1 and first substantive paragraph CSS selectors. Effort: 1–2 hours developer time or Rank Math plugin configuration.

---

## Platform-Specific Scores (estimated)

| Platform | Score | Primary Bottleneck |
|----------|-------|--------------------|
| Google AI Overviews | 35/100 | No FAQ schema, homepage H1 missing, thin passage length |
| ChatGPT (browsing/citations) | 28/100 | No llms.txt, weak entity signals, no YouTube/Wikipedia |
| Perplexity | 40/100 | Good server-render, but stale content signals (2021 slug), no sourced citations |
| Bing Copilot | 42/100 | Product schema partially present; missing brand/rating nodes |

---

## Summary Table

| ID | Severity | Issue | Est. Effort |
|----|----------|-------|-------------|
| C1 | CRITICAL | No llms.txt | 1 hour |
| C2 | CRITICAL | Product schema missing brand, rating, specs, broken image URL | 4–8 hours |
| H1 | HIGH | Homepage H1 absent | 30 min |
| H2 | HIGH | Organization sameAs: only 1 social profile, no YouTube/Wikipedia | 1–2 hours |
| H3 | HIGH | No FAQ schema anywhere | 2–4 hours |
| H4 | HIGH | No author E-E-A-T (bio, credentials, social sameAs) | 2–4 hours |
| H5 | HIGH | 2021 slug with 2025 title + obsolete product listings | 2–3 hours |
| M1 | MEDIUM | Blog passages below 134-word citability threshold | 30–45 min/post |
| M2 | MEDIUM | No external citations or sourced statistics | 1–2 hours/post |
| M3 | MEDIUM | robots.txt duplicate stanzas + HTTP sitemap URL | 30 min |
| M4 | MEDIUM | No YouTube, Wikipedia, Wikidata, Reddit brand presence | Ongoing |
| M5 | MEDIUM | Product WebPage.description is sitewide boilerplate | 30 min setup |
| L1 | LOW | No AI licensing declaration (RSL 1.0 / llms.txt) | 15 min |
| L2 | LOW | No Speakable schema on buying-guide posts | 1–2 hours |
