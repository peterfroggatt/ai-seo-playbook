# GEO / AI-Search Readiness — techwhizmart.com
**Audit date:** 2026-06-30
**Auditor:** seo-geo agent

---

## GEO Health Score: 28 / 100

| Dimension | Weight | Raw | Weighted |
|-----------|--------|-----|----------|
| Citability | 25% | 22/100 | 5.5 |
| Structural Readability | 20% | 30/100 | 6.0 |
| Multi-Modal Content | 15% | 35/100 | 5.3 |
| Authority & Brand Signals | 20% | 18/100 | 3.6 |
| Technical Accessibility | 20% | 38/100 | 7.6 |
| **Total** | 100% | — | **28 / 100** |

---

## 1. AI Crawler Accessibility

**Status: ALLOWED (default, no explicit rules)**
**Severity: INFO**

`robots.txt` contains two stacked `User-agent: *` blocks — one from WooCommerce defaults and one from Yoast (empty `Disallow:`). Neither block names any AI-specific crawler.

```
User-agent: *
Disallow: /wp-content/uploads/wc-logs/
Disallow: /wp-content/uploads/woocommerce_transient_files/
Disallow: /wp-content/uploads/woocommerce_uploads/
Disallow: /*?add-to-cart=
...

# START YOAST BLOCK
User-agent: *
Disallow:
Sitemap: http://techwhizmart.com/sitemap_index.xml
# END YOAST BLOCK
```

**Affected crawlers (all allowed by default):**
- GPTBot (OpenAI search) — allowed
- OAI-SearchBot (OpenAI) — allowed
- ClaudeBot (Anthropic) — allowed
- PerplexityBot — allowed
- Google-Extended — allowed
- CCBot (Common Crawl / training) — allowed
- anthropic-ai — allowed

**Finding:** All AI crawlers can index the site. This is the correct posture for AI search visibility. However, the absence of explicit `Allow` directives for these bots means the site is not actively signalling AI-readiness. The duplicate `User-agent: *` blocks are a technical defect (the second Yoast block overrides/shadows the first), and the `Sitemap:` line uses `http://` not `https://`, breaking sitemap discovery for crawlers that pin protocols.

**Fix:** Add explicit `Allow` directives for key AI crawlers. Correct the sitemap protocol to `https://`. Collapse the duplicate `User-agent: *` blocks into a single clean block.

```
User-agent: GPTBot
Allow: /

User-agent: OAI-SearchBot
Allow: /

User-agent: ClaudeBot
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: Google-Extended
Allow: /

Sitemap: https://techwhizmart.com/sitemap_index.xml
```

---

## 2. llms.txt

**Status: ABSENT (HTTP 404)**
**Severity: HIGH**

`https://techwhizmart.com/llms.txt` returns 404. The `llms.txt` standard (analogous to `robots.txt` for LLM consumption) lets the site declare machine-readable context about its purpose, key URLs, preferred citation format, and licensing. Without it, AI systems must infer site purpose from crawled content alone — and given the site's severe topical incoherence (drone-photography blog + gym equipment + LEGO + liquor bar tables on a "tech gadget store"), the inference will be poor.

**Fix (effort: 1–2 hours):** Create `/llms.txt` at the domain root via a static file or WordPress rewrite:

```
# TechWhizMart — Consumer Electronics & Tech Gadgets
# https://techwhizmart.com/

> TechWhizMart is an online retailer of consumer electronics, audio equipment,
> gaming peripherals, drones, wearables, and tech accessories. All prices in USD.

## Key sections
- Shop: https://techwhizmart.com/shop/
- Audio & Headphones: https://techwhizmart.com/product-category/audio/
- Gaming: https://techwhizmart.com/product-category/consoles-and-vr/
- Wearables: https://techwhizmart.com/product-category/wearable-gadgets/

## Licensing
Content: Copyright TechWhizMart. Product descriptions may be cited with attribution.
```

---

## 3. Passage-Level Citability

**Severity: HIGH**

### 3a. Product Pages

Sampled pages: `jbl-charge-4-waterproof-portable-bluetooth-speaker-black`, `fashion-meets-function-the-best-smartwatches-of-the-year`.

**What AI systems need:** Self-contained answer passages of 134–167 words, direct answers in the first 40–60 words of each section, question-based headings (H2/H3), and attributable statistics.

**What exists:**

The JBL Charge 4 product page has a Product schema block with a bullet-list description (spec bullets like "WIRELESS BLUETOOTH STREAMING", "UP TO 20 HOURS OF PLAYTIME", "IPX7 WATERPROOF"). However:
- The on-page visible description is a **machine-translated/spun rewrite** of the Amazon listing: *"Introducing the JBL cost 4 moveable Bluetooth speaker with full-spectrum highly effective sound and a built-in energy financial institution to cost your units"* — this is garbled English that no AI system would cite as authoritative.
- There is a single `H2` heading: "Specification: JBL Charge 4…" followed by an attribute table. The attribute table is helpful (structured data) but it's the only citable structure.
- No FAQ section, no comparison content ("vs Sony SRS-XB43"), no "Who should buy this" passage, no expert prose a generative engine could extract.
- **Passage word count in citable blocks: ~50–80 words** — below the 134-word optimal citability threshold. Spec bullets don't read as prose passages to LLMs.
- No `aggregateRating` or `review` data on the Product schema (zero reviews present). AI systems weight user-generated authority signals heavily.
- Image URL is malformed: `https://techwhizmart.com/wp-content/uploads/https://m.media-amazon.com/images/I/411XhhP64tL.jpg` — the Amazon CDN URL is nested inside the local path, which will 404, breaking visual context for multi-modal AI systems.

**Citability score for product pages: 15/100**

### 3b. Blog Posts (Drone Photography)

Sampled: `soar-high-the-top-travel-drones-of-2025-for-aerial-adventurers` (and confirmed pattern across 147 posts).

The drone blog posts are long-form AI-generated content (confirmed by shared context: one post ~16k words of markup). Structural issues:

- The fetched blog post returned a **404-equivalent layout** (headings were `<h2>404</h2>` and `<h2>Archives</h2>`) despite being a listed URL — indicating broken routing or slug mismatch.
- Where blog posts do render, they carry only WebSite + Organization schema — **no Article, BlogPosting, or Person schema** — meaning AI systems cannot identify the author, publication date, or content type.
- No byline found in any sampled post (`author` CSS class queries returned zero results).
- The JSON-LD `Organization` block lists `sameAs: ['https://au.pinterest.com/techwhizmart/']` — a single Pinterest profile, which is a negligible authority signal.
- Drone travel photography content has zero topical relationship to the store's actual products (electronics, audio, gaming). Even if these posts achieved AI citations, they would cite "TechWhizMart" in the context of drone photography — not consumer tech retail — which actively harms brand identity in AI answer engines.

**Citability score for blog posts: 20/100** (structural issues + topical mismatch + missing Article schema)

---

## 4. Brand & Entity Signals

**Severity: HIGH**

### 4a. Organization Schema
Present on all sampled pages via Yoast's `@graph` block:

```json
{
  "@type": "Organization",
  "name": "TechWhizMart",
  "url": "https://techwhizmart.com/",
  "sameAs": ["https://au.pinterest.com/techwhizmart/"]
}
```

**Missing from `sameAs`:** Facebook, Instagram, Twitter/X, YouTube, LinkedIn, Wikidata/Wikipedia. The `sameAs` array with only one Pinterest URL signals to AI systems that this brand has minimal verifiable web presence. Pinterest's AI citation correlation is low; YouTube's is the highest measured (~0.737 correlation with AI citations).

**No Wikipedia or Wikidata entity** exists for TechWhizMart (expected for a small e-commerce store, but notable — Wikipedia entity presence is a strong AI citation signal).

**No Reddit presence** detected in schema or confirmed from content (Reddit is a high-correlation AI citation source).

### 4b. Social Profile Coverage

| Platform | Present | Evidence |
|----------|---------|---------|
| Pinterest | Yes | sameAs + au.pinterest.com URL |
| YouTube | No | Not in sameAs |
| Facebook | No | Not in sameAs |
| Instagram | No | Not in sameAs |
| Twitter/X | No | Not in sameAs |
| LinkedIn | No | Not in sameAs |
| Wikipedia | No | No entity found |
| Reddit | No | No r/TechWhizMart or brand mentions confirmed |

**Primary image of page** resolves to a 1x1 blank GIF: `https://techwhizmart.com/wp-content/themes/rehub-theme/images/default/blank.gif` — this is the Yoast primaryImage fallback. No real OG/logo image is registered in the Organization schema image field at the @graph level.

### 4c. Author / E-E-A-T Signals
Zero author bylines found on any sampled page. Blog posts have no Person schema, no bio, no credentials. Product descriptions are spun/machine-translated Amazon listings. These are the core E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness) gaps that prevent AI systems from treating the site as a citable source.

**Authority & Brand Signals score: 18/100**

---

## 5. Answer-Readiness (AI Overview / ChatGPT / Perplexity Citability)

**Severity: HIGH**

AI Overview (Google), ChatGPT with search, and Perplexity all prioritise pages that:
1. Directly answer a named question in the first sentence of a section
2. Use question-format H2/H3 headings ("What is the battery life of the JBL Charge 4?")
3. Contain citable prose passages of 134–167 words
4. Have structured data (Product, FAQPage, HowTo, Review schema)
5. Link to primary sources or cite verifiable stats

**Current state:**

| Signal | Status |
|--------|--------|
| Question-format headings | Absent — H2s are spec labels or numbered list items |
| FAQPage schema | Absent on all sampled pages |
| HowTo schema | Absent |
| Review schema with aggregateRating | Absent (0 reviews on all sampled products) |
| Citable prose passages (134–167 words) | Absent — descriptions are spec bullets or spun text |
| Comparison content | Absent — no "vs" pages, no comparison tables with prose |
| Statistics with attribution | Absent |
| Direct answer in first 40 words | Absent — pages open with navigation chrome |

**No page on techwhizmart.com would currently be cited by Google AI Overview, ChatGPT, or Perplexity** for any product-related query. The site might appear in raw web results but the AI extraction layer finds no citable passage structure.

---

## 6. Strategic Issue: Off-Topic Blog vs. Store Identity

**Severity: CRITICAL**

147 blog posts are almost entirely about drone travel photography. The store sells consumer electronics (headphones, speakers, gaming peripherals, wearables). This creates a fundamental AI search identity conflict:

- **Topical authority mismatch:** AI systems build entity associations. If TechWhizMart is cited by AI for "drone photography tips", it is cited as a photography blog, not a tech retailer. This actively dilutes the store's e-commerce entity signal.
- **Zero commercial intent alignment:** A Perplexity user asking "best Bluetooth speaker under $100" will never be cited to a drone photography article on techwhizmart.com.
- **Citation impossibility:** Even if the blog posts were structurally perfect, no AI system answering "travel drone photography" queries would cite an unknown e-commerce store without topical authority, Wikipedia presence, or YouTube brand mentions.
- **Crawl budget waste:** 147 off-topic posts + 360 trashed-but-indexed product URLs consume crawl budget that should go to live product pages.

**Recommendation:** Either (a) pivot all blog content to product-adjacent topics that earn commercial citations (e.g., "Best Bluetooth Speakers for Outdoor Use 2026 — Tested", "JBL vs Sony: Which Portable Speaker Wins?"), or (b) move the drone blog to a subdomain/separate domain and noindex it from the main store.

---

## 7. Technical Accessibility for AI Crawlers

**Severity: MEDIUM**

| Check | Status |
|-------|--------|
| Server-side rendering (SSR) | Pass — WordPress/Elementor renders HTML server-side; no SPA shell |
| robots.txt AI access | Pass (default allow) |
| HTTPS canonical | Pass |
| Sitemap protocol | FAIL — `http://` in Sitemap directive and all sitemap `<loc>` entries |
| llms.txt | FAIL — 404 |
| Broken image URLs in Product schema | FAIL — nested Amazon CDN paths |
| 360 trashed products returning HTTP 200 | FAIL — index bloat, dilutes crawl authority |
| H1 on homepage | FAIL — zero H1 tags |
| Primary OG image | FAIL — resolves to blank GIF |
| pa_* attribute sitemaps (170+ URLs) | FAIL — faceted attribute archives indexed, no crawl value |

**Technical Accessibility score: 38/100**

---

## Top 5 Highest-Impact Fixes

### Fix 1: Create /llms.txt [CRITICAL | Effort: 2 hours]
Add a machine-readable site declaration at `https://techwhizmart.com/llms.txt` declaring the store's purpose, key category URLs, and licensing intent. Immediate signal to all LLM crawlers about site identity.

### Fix 2: Rewrite product descriptions as citable prose [HIGH | Effort: ongoing, 1–2 hours/product]
Replace spun/machine-translated Amazon bullets with original 150–200 word prose descriptions structured as: opening sentence (direct answer to "what is this?"), key specs in prose form, who it's for, and one comparison anchor. Add FAQPage schema with 3–5 Q&A pairs per product page. Target the 134–167 word optimal passage length.

### Fix 3: Expand Organization sameAs + build YouTube channel [HIGH | Effort: 1 week for social, ongoing for YouTube]
Add Facebook, Instagram, Twitter/X, and LinkedIn to `sameAs`. Create and populate a YouTube channel with product reviews — YouTube brand mention correlation with AI citations is 0.737, the strongest measured signal. Even 5–10 short product review videos materially improves AI brand recognition.

### Fix 4: Add Article/BlogPosting + Person schema to all posts; or redirect/noindex off-topic blog [HIGH | Effort: 4 hours for schema fix; strategic decision required for topic pivot]
If the drone blog is kept: add `Article` or `BlogPosting` schema with `author` (Person), `datePublished`, and `publisher`. Add author bio pages with `Person` schema and credential fields. If the blog is off-topic by design, noindex all 147 posts and redirect crawl authority to product categories.

### Fix 5: Eliminate trashed product URL index bloat [MEDIUM | Effort: 4–8 hours]
360 `__trashed` product URLs return HTTP 200, self-canonicalise, and appear in the product sitemap. These should return 404 (or 301 to category) and be removed from the sitemap. This reclaims crawl budget for the 310 live products and removes near-duplicate thin pages that depress the site's overall perceived content quality.

---

## Platform-Specific Scores

| Platform | Score | Reason |
|----------|-------|--------|
| Google AI Overviews | 12/100 | No FAQPage schema, no citable passages, spun product text, no E-E-A-T signals |
| ChatGPT (search) | 15/100 | No llms.txt, no structured Q&A, brand entity absent from Wikipedia/Reddit/YouTube |
| Perplexity | 18/100 | SSR helps (page renders), but no direct-answer passages, no attribution, topical incoherence |
| Bing Copilot | 20/100 | Product schema present (helps slightly), but no reviews, no aggregateRating, spun descriptions |

---

## Evidence Log

| Finding | Source | Verified |
|---------|--------|---------|
| robots.txt no AI-specific rules | Direct fetch 2026-06-30 | Yes |
| /llms.txt = 404 | HTTP status check 2026-06-30 | Yes |
| Organization sameAs = 1 Pinterest URL only | JSON-LD parse, homepage | Yes |
| Product schema missing aggregateRating | JSON-LD parse, JBL product page | Yes |
| Product image URL malformed (nested Amazon CDN path) | JSON-LD parse | Yes |
| primaryImage = blank GIF | JSON-LD @graph WebPage node | Yes |
| 0 H1 tags on homepage | Heading parse | Yes |
| Blog post schema = WebSite + Organization only (no Article) | JSON-LD parse | Yes |
| No author byline in any sampled page | CSS class query | Yes |
| 360 trashed URLs in product sitemap | product-sitemap.xml parse | Yes |
| 147 posts, nearly all drone photography | post-sitemap.xml + shared context | Yes |
| JBL description = spun Amazon text | On-page text extraction | Yes |
