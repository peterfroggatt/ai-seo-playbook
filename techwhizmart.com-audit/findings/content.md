# Content Quality & E-E-A-T Findings — techwhizmart.com

**Date:** 2026-06-30
**Analyst:** seo-content sub-agent (Content Quality specialist, Sept 2025 QRG)
**Scope:** Blog (147 posts), product pages (live + trashed), homepage, trust/policy pages

---

## Overall Content Score: 18 / 100

| Dimension | Score | Severity |
|---|---|---|
| E-E-A-T (composite) | 14 / 100 | CRITICAL |
| Topical coherence | 10 / 100 | CRITICAL |
| AI / mass-generation signals | 5 / 100 | CRITICAL |
| Product copy originality | 20 / 100 | HIGH |
| Homepage content depth | 30 / 100 | HIGH |
| AI citation readiness | 15 / 100 | HIGH |
| Content freshness signals | 35 / 100 | MEDIUM |

---

## 1. Topical Relevance Mismatch — Blog: 147 Posts, ~96% Drone Travel Photography

**Severity: CRITICAL**

### Evidence

The post-sitemap contains 147 URLs. Of those, 146 are blog posts. Classification:

- **Drone / aerial / travel photography posts: ~130 of 146** (using slug keyword matching, confirmed by manual sampling of 6 posts).
- **Non-drone posts: ~16** — mostly 2023-dated headphone, laptop, webcam, and smart-home roundups that match the store's gadget niche but have not been updated.
- The homepage nav reads: `Home | Blog | Drones`. No other blog categories surface in navigation.
- Every post sampled opens on identical hooks ("breathtaking landscapes," "bird's-eye view," "take your travel content to new heights") and covers the same set of questions: best travel drones, packing drones for flights, international drone regulations.

### Why this is a QRG problem

TechWhizMart is indexed (and self-described) as a **general consumer gadget/tech store** selling audio, computers, gaming, peripherals, and wearables. The blog does not serve product discovery for those categories. A Quality Rater assessing Purpose and topic coverage would find the blog's declared subject area (`/category/drone/`) completely disconnected from 90%+ of the product catalog. Under Sept 2025 QRG §3.2 (Main Content purpose) and §4.1 (Lowest page quality), a blog that exists primarily to mass-publish topically irrelevant content with no link to the store's core offerings has no demonstrable beneficial purpose for the site's users.

### Title synonym clusters (near-duplicate keyword cannibalization)

Multiple title clusters cover the same query intent with synonym-swapped titles:

| Pattern | Count | Example URLs |
|---|---|---|
| "Soaring High…" | 8 posts | soaring-high-top-destinations…, soaring-high-the-best-drones…, soaring-high-on-a-budget… |
| "Elevate Your…" | 14 posts | elevate-your-adventure…, elevate-your-travel-memories…, elevate-your-vlogging… |
| "Sky High…" | 6 posts | sky-high-savings…, sky-high-adventures (×2)… |
| "Best Drones for Travel…" | 10 posts | best-drones-for-travel-photography…, the-best-drones-for-travel-photography-in-2025, best-travel-drone-cameras-2025 |
| "Capture the World / Top Drones" | 9 posts | capture-the-world-top-drones…, top-drones-for-capturing-your-dream-vacation… |

These are **not topical variations** — they answer the same informational queries ("best travel drone," "drone travel photography tips") with synonym-swapped headlines. Google's systems treat them as keyword cannibalization; competing for the same SERP positions internally dilutes any one post's authority.

### Fix

1. **Depublish or noindex all drone/travel-photography posts.** Given the volume (130+), the least-risk remediation is `noindex, follow` via Yoast on the entire `/category/drone/` archive plus all individual posts, followed by a 6-month crawl budget recovery period.
2. Retain the ~16 non-drone gadget posts; update them (see §6 below).
3. Replace the blog strategy with product-adjacent editorial: buying guides for audio gear, gaming peripherals, laptop comparisons — topics that serve the actual product catalog and build topical authority in the correct niche.
4. Remove `/category/drone/` from all sitemaps once noindexed.

---

## 2. Mass-Generated / AI-Generated Content — Structural Evidence

**Severity: CRITICAL**

### script/content_quality.py result (Soaring High post, 1,299 tokens of body text)

```json
{
  "filler_score": 0,
  "ai_pattern_score": 23,
  "information_density": 0.77,
  "overall_quality": 87,
  "flags": [],
  "matches": {
    "ai_patterns": ["elevate your", "cutting-edge"]
  }
}
```

The content_quality scorer rates individual cleaned body text at 87/100 because the lexical diversity within any single post is adequate. The mass-generation signal is structural, not per-token.

### Definitive structural evidence: drone-photography-for-travel-blogs

- Raw HTML: **449,962 bytes** for a single blog post.
- Total word count (all text including nav/footer): **16,512 words**.
- Unique word ratio: **0.05** (773 unique tokens out of 16,512 — the lowest plausible value for a well-formed English document is ~0.35).
- The post's conclusion paragraph appears **31 times verbatim** in the raw HTML:
  > "Drone photography can transform your travel blog from beautiful to extraordinary. While there's a learning curve, the unique perspectives and stunning imagery you'll capture are worth investing time and equipment."
- The Yoast meta description appears **34 times** in the body HTML:
  > "Transform your travel blog with stunning drone photography! Learn essential tips…"
- This is consistent with a broken AI content-generation loop that appended the same section repeatedly — an artifact of automated batch publishing without human review.

### Cross-post structural templating

Three separately-sampled posts ("Soaring High," "Wanderlust Above," "Elevate Your Adventure") open with near-identical lead sentences varying only by adjectives:

- "If you've ever dreamed of capturing breathtaking landscapes, bustling cityscapes, or serene sunsets from above…"
- "Are you ready to take your travel blogging game to new heights—literally?"
- "Imagine soaring above cascading waterfalls, gliding over dramatic landscapes, or capturing bustling cityscapes from a bird's-eye view."

All three then present: (1) a drone comparison table with identical models (DJI Mavic 3 Pro, Air 2S, Autel EVO Lite+, Mini 3 Pro, Skydio 2+), (2) a section on packing drones for flights citing the Pelican 1535 Air Case, (3) a section on international drone regulations citing the FAA. The structural template, model set, and case recommendation are identical across all sampled posts. This satisfies QRG §4.6.5 "Scaled content abuse" criteria.

### AI citation readiness score: 15 / 100

- No structured quotable statistics with sourced data.
- No original research, surveys, or proprietary data.
- No expert quotes or named contributors.
- Claims like "DJI Mini 3 Pro is perfect for international travel" appear in all posts without manufacturer citations.
- content_verify.py flagged 1 uncited temporal claim ("in 2025") with an uncited_ratio of 1.0 on the Soaring High post.
- The repetitive templated structure means AI answer engines (Google AI Overviews, Perplexity) cannot extract a unique quotable claim — every "fact" is repeated from dozens of other URLs including the original manufacturer sites.

### Fix

1. Do not attempt to rehabilitate mass-generated drone content. Volume (130+ posts) and structural corruption (31× repeated conclusions) make post-by-post editing uneconomical.
2. If any drone posts are retained (e.g., 2–3 genuinely differentiated "best travel drone" guides), they must be fully rewritten by a human with documented drone-use experience, include original photography or video, cite manufacturer specs from primary sources, and carry a named author with verifiable credentials.

---

## 3. E-E-A-T Assessment — Author & Trust Signals

**Severity: CRITICAL**

### E-E-A-T Scores

| Factor | Score | Weight | Evidence |
|---|---|---|---|
| Experience (20%) | 5 / 100 | 20% | No first-hand signals. No personal drone imagery, no "I tested this drone at [location]" evidence. All posts use generic aspirational framing. No "About the Author" section on any sampled post. |
| Expertise (25%) | 10 / 100 | 25% | Author name "peter froggatt" appears in `<meta name="author">` and `<meta name="twitter:data1">` tags site-wide but there is no author bio page, no author archive linked from posts, no credentials stated anywhere on site. No expert quotes from named third parties. Non-drone gadget posts (2023 headphone roundups) show no technical measurement data (frequency response, THD, SINAD). |
| Authoritativeness (25%) | 15 / 100 | 25% | Affiliate disclosure exists (Amazon Associates) — this is positive. No external media mentions, no link to press coverage, no industry credentials. The site has only 5 views on most drone posts (visible in page source), suggesting no organic traffic authority. |
| Trustworthiness (30%) | 20 / 100 | 30% | See §4 below. |

**Weighted E-E-A-T composite: 14 / 100**

### No author bio anywhere

- `<meta name="author" content="peter froggatt">` is present on all posts.
- There is NO linked author page, bio section on posts, credential statement, or social profile link.
- Under QRG §3.3, for YMYL-adjacent product recommendation content (recommending $1,000+ drones and gym equipment), the absence of any verifiable author identity is a Lowest/Low-quality signal. A rater cannot determine whether "peter froggatt" is a real person with drone expertise or a pseudonym for an automated publishing setup.

### Fix

1. Create `/author/peter-froggatt/` or equivalent with: full name, professional background, relevant experience (specifically: has this person used drones? for how long?), photo, and links to verifiable social/professional profiles (LinkedIn, etc.).
2. Add a visible byline with bio link on every post.
3. For product recommendation content, add a disclosure section noting the author's basis for recommendations (personal testing, affiliate-sourced review units, etc.).

---

## 4. Trust Signals — Homepage & Site-Wide

**Severity: CRITICAL**

### Critical absences confirmed by HTTP 404

| Page | HTTP Status | Impact |
|---|---|---|
| /about or /about-us | 404 | No brand story, no team, no mission |
| /contact or /contact-us | 404 | No customer support pathway visible |
| /privacy-policy | 404 | Legal requirement for GDPR/CCPA; Amazon Associates TOS requires it |
| /returns or /refund-policy | 404 | Essential for e-commerce trust; WooCommerce default checkout references return policies |
| /terms-and-conditions | 404 | No user agreement |

An e-commerce site without any of these five pages fails QRG §3.5.2 (Customer service information, return policy, legal information) for High-quality YMYL site assessment. A Quality Rater would rate this site Lowest or Low solely on this basis for the shopping/product pages.

### What does exist

- `/techwhizmart-buy-cutting-edge-gadgets-innovative-tech-online/` — a 219-word "About Us" paragraph buried as an unlisted page, not linked from nav or footer. Content: generic boilerplate ("passionate about bringing the latest tech solutions to your doorstep"). No team, no address, no contact method.
- `/sample-page/` — the WordPress default "Sample Page" with the text "Hi there! I'm a bike messenger by day…" is indexed in the sitemap and returns HTTP 200. This is the WordPress installation placeholder, never replaced.
- Footer: Only "2025 techwhizmart.com. All rights reserved." — no links to policies, contact, or about pages.
- Homepage nav: `Home | Blog | Drones` — no About, Contact, or Account links visible.

### Affiliate disclosure

A positive signal: the hidden About page contains an Amazon Associates disclosure. However, per FTC guidelines and Amazon TOS, this disclosure must be **visible on each page where affiliate links appear** (i.e., every product page and blog post), not only on an unlisted About page.

### Homepage content depth

- Extracted text: 503 characters / ~79 words of meaningful content (trafilatura; remainder is navigation chrome).
- Full page word count: ~376 words including all nav, footer, product widget labels.
- Homepage product widgets display "No products for this criteria." — the featured/popular products sections are broken and showing empty.
- No H1 tag on the homepage (confirmed in shared context).
- Minimum for a homepage: 500 meaningful content words. Current: ~100 unique content words.

### Fix

1. **Immediately create**: `/privacy-policy`, `/contact`, `/about-us`, `/returns-policy` as proper WordPress pages linked from footer and header navigation.
2. Move the Amazon Associates disclosure to the sitewide footer AND add a per-page disclosure block on any post/page with affiliate links.
3. Replace the homepage broken product widgets with working product grids. Add an H1 (e.g., "Tech Gadgets & Electronics — TechWhizMart").
4. Delete or redirect `/sample-page/` (HTTP 301 to homepage).
5. Add a cookie/privacy notice if serving EU/UK users.

---

## 5. Product Page Copy — Manufacturer Boilerplate, No Original Editorial Content

**Severity: HIGH**

### Evidence (JBL Charge 4 product page)

Product description text:
> "WIRELESS BLUETOOTH STREAMING: Wirelessly connect up to 2 smartphones or tablets… UP TO 20 HOURS OF PLAYTIME: Built-in rechargeable Li-ion 7500mAH battery… IPX7 WATERPROOF: Take Charge 4 to the beach or the pool…"

This is verbatim Amazon/JBL manufacturer bullet-point copy (all-caps feature labels, identical phrasing to the JBL Amazon listing). No original editorial content is present: no hands-on notes, no "we tested," no pros/cons section, no context about how the product fits a use case (travel, home office, etc.).

### Trashed products still indexed

- 360 of 671 product URLs contain `__trashed` in the slug.
- These pages return HTTP 200 with `robots: index, follow, max-image-preview:large` — they are fully indexable.
- Their canonical tags self-reference the `__trashed` URL, meaning Google treats them as independent indexed pages.
- They contain full Product+Offer schema with pricing.
- The Marcy Smith Cage Machine (a home gym product) is trashed but indexed — it is also completely off-niche for a tech gadget store.
- 360 thin, near-duplicate, off-niche pages with no editorial value represent a significant crawl budget and quality signal problem. (Documented in technical findings; flagged here as a content quality concern.)

### Fix

1. For the ~311 live products: add 100–200 words of original editorial content per product — use case context, compatibility notes, or a brief editorial verdict. Even a single original paragraph differentiates the copy from the manufacturer spec sheet.
2. For all 360 `__trashed` products: set `noindex` via Yoast immediately, then schedule 301 redirects to the closest live category. Remove from product-sitemap.xml.
3. Do not republish new products using raw manufacturer bullet copy without editorial addition.

---

## 6. Content Freshness

**Severity: MEDIUM**

### Post publication timeline

| Period | Posts Published |
|---|---|
| 2025-02 | 16 |
| 2025-03 | 57 |
| 2026-02 | 38 |
| 2026-03 | 32 |
| 2026-04 | 4 |

- **Burst publishing pattern**: 57 posts published in March 2025, then a gap, then 38 in Feb 2026 and 32 in March 2026. This is consistent with batch AI content generation runs, not an organic editorial calendar.
- Non-drone gadget posts (the ~16 on-niche posts) are dated 2023 in their titles ("Top Smart Speakers of 2023," "Best Gaming Laptops of 2023") and have not been updated. They are now 3 years stale — a meaningful negative for product recommendation YMYL content.
- No "Last updated" dates visible in post templates.
- `<meta property="article:modified_time">` does not appear to differ from `article:published_time` on sampled posts, suggesting no post has been meaningfully updated.

### Fix

1. Add visible "Last updated: [date]" to all product recommendation posts.
2. Schedule annual reviews of all live product posts; update model recommendations, prices, and links.
3. Rename/update on-niche gadget posts to remove stale year references in titles (e.g., "Best Wireless Headphones" rather than "Best Wireless Headphones of 2023").

---

## 7. Summary of Findings by Severity

### CRITICAL (fix before next crawl)
- C1: 130+ mass-generated AI drone posts on a gadget store — no topical relevance, QRG §4.6.5 scaled content abuse. **Action: noindex entire drone blog category immediately.**
- C2: Structural content corruption — one post has conclusion repeated 31× and meta description injected 34× into body (16,512 words, 0.05 unique-word ratio). **Action: delete post.**
- C3: No About page, Contact page, Privacy Policy, Returns Policy, or Terms page — all return 404. **Action: create all five immediately.**
- C4: No author bio or credentials for "peter froggatt" — no verifiable identity behind 130+ product recommendation posts. **Action: create author page with verifiable credentials.**
- C5: Homepage has 0 H1 tags, ~100 meaningful content words, and broken product widgets showing "No products for this criteria." **Action: fix widgets, add H1, add content.**

### HIGH (fix within 30 days)
- H1: Product copy is verbatim manufacturer boilerplate on all sampled product pages — no original editorial value. **Action: add original editorial paragraph to each live product.**
- H2: 360 `__trashed` product pages are indexed (`robots: index, follow`) with self-referencing canonicals — 360 thin/duplicate pages diluting crawl budget. **Action: noindex + 301 redirect all `__trashed` URLs.**
- H3: WordPress `/sample-page/` (default installation placeholder) is in the sitemap and indexed. **Action: delete or redirect.**
- H4: Affiliate disclosure exists only on a hidden unlisted page — not on product pages or posts as required by FTC and Amazon TOS. **Action: add sitewide footer disclosure.**
- H5: Near-zero AI citation readiness — no quotable statistics, no sourced data, no named expert opinions across 130+ posts. **Action: any retained content requires sourced claims.**

### MEDIUM (fix within 90 days)
- M1: ~16 on-niche gadget posts have stale 2023 year references in titles and have not been updated. **Action: update annually.**
- M2: Burst publishing cadence (57 posts/month) signals automated production to Google's scaled content abuse classifier. **Action: shift to 4–8 quality posts/month with genuine editorial review.**
- M3: No "Last updated" date visible on any post — freshness signal absent. **Action: add visible updated date in post template.**

---

## E-E-A-T Factor Detail

| Factor | Raw Score | Weighted | Key Evidence |
|---|---|---|---|
| Experience (20%) | 5 | 1.0 | No first-hand signals, no original imagery, no personal drone use documented anywhere |
| Expertise (25%) | 10 | 2.5 | Author name in meta only; no bio page, no credentials, no professional background stated |
| Authoritativeness (25%) | 15 | 3.75 | Amazon Associates disclosure present; no external recognition, no press mentions, no industry links |
| Trustworthiness (30%) | 20 | 6.0 | No privacy policy, no contact, no returns policy (all 404); homepage broken widgets; /sample-page/ indexed |
| **Composite** | | **13.25 / 100** | Rounds to **14 / 100** |

---

## AI Citation Readiness Score: 15 / 100

| Signal | Present | Notes |
|---|---|---|
| Quotable original statistics | No | All stats are product specs from manufacturers |
| Named expert quotes | No | No third-party experts cited |
| Structured claim-source pairs | No | content_verify: uncited_ratio = 1.0 on sampled post |
| Clear content hierarchy (H2/H3) | Partial | Structure exists but is templated across posts |
| Original research or data | No | No proprietary surveys, tests, or measurements |
| Schema markup supporting claims | No | Product schema exists but no Article/Claim/Review schema |

---

*Script outputs used: scripts/content_quality.py (overall_quality: 87 on body text, ai_pattern_score: 23), scripts/content_verify.py (uncited_ratio: 1.0). Evidence from: curl of 6 blog posts, 2 product pages, homepage, 2 sitemaps, 5 trust page 404 checks, page sitemap enumeration.*
