# Search Experience Optimization (SXO) — audiotechexpert.com

**Gap score: 38/100.** Prose quality is high; the problem is systematic misalignment between SERP intent and what the site publishes.

> Correction: SXO's parser reported "malformed schema (@type '?')" and "homepage 0 H1" — both are artifacts. The dedicated schema agent confirmed valid JSON-LD (missing types, empty WebSite.name), and direct DOM check shows 2 H1s. Treat those two SXO items via the schema/technical findings.

## Page-type mismatches (SERP-backwards)
- **"best headphones for travel"** → SERP is 95% ranked listicles. Site has `/how-to-choose-headphones-for-travel/` (criteria guide, 0 product names, 0 affiliate links). **CRITICAL mismatch.** Pattern repeats for gaming/mixing/microphone "how-to-choose" guides.
- **"XLR vs USB microphone"** → SERP wants a comparison page with a verdict + product picks. Site has **4 competing URLs**, the prime slug `/xlr-vs-usb-microphones/` titled **"…for Crime Writers: A Working Author's Guide"** (the author's crime-novelist persona leaked into audio content — strong templated/AI-generation signal), none with a comparison table or named picks. **CRITICAL (niche-angle hijack + cannibalization).**
- **"best budget microphone"** → Site's `/best-microphones-under-100-2026/` is the right type (listicle, AAWP boxes, 19 affiliate links) but **0 comparison table** and weak-brand picks (FIFINE/TONOR/ALPOWL vs SERP consensus Samson Q2U, AT2020, Blue Yeti). MEDIUM.
- **"best noise cancelling headphones"** → Correct listicle type but lives on the ugly `-2026-2` duplicate slug, no scored methodology, no table. MEDIUM.

## Persona scoring (selected)
- NC-headphones roundup: Beginner 57/100 (answer buried, weak trust, late CTA); Prosumer 45/100 (no measured data/specs table → loses to RTINGS).
- USB-vs-XLR comparison: Beginner 29/100 (**0 affiliate links, no verdict above fold — critical dead end**); Prosumer 43/100.
- Travel guide: Beginner 32/100 (no picks, 0 CTA); Prosumer 44/100.

## Why good pages fail to rank
1. "How to choose" (educational) content aimed at "best X" (product-selection) SERPs — intent mismatch (CRITICAL).
2. Duplicate/near-duplicate `-2` pages + 4× XLR variants split equity (HIGH).
3. **Zero comparison tables** on all 9 pages audited — competitors win table featured snippets (HIGH).
4. Niche "crime writers" angle on a core commercial URL poisons relevance (CRITICAL).
5. Hub pages (`/best-headphones/`, `/best-microphones/`) are prose directories, not conversion listicles (CRITICAL).

## Affiliate conversion gaps
- No above-the-fold product/"Top Picks" box on hubs or roundups; first CTA appears ~300 words in.
- "How to choose" and "vs" pages have **0 affiliate path** despite high commercial intent — add "See our top picks for X" CTAs/internal links.
- No comparison tables (AAWP supports them) — biggest combined ranking+conversion lever.
- Verify affiliate disclosure appears before the first affiliate link (FTC/Amazon).

## Top recommendations
1. Convert `/best-headphones/` & `/best-microphones/` into conversion-ready listicle hubs (top-3 box + product cards).
2. Split each "how-to-choose" guide from a true "best X" listicle (guide becomes a subsection).
3. Consolidate XLR-vs-USB to one general-audience canonical; redirect the crime-writer + duplicate variants.
4. Fix all `-2`/`-2026-2` commercial slugs (redirect to clean canonical).
5. Add comparison tables + above-the-fold "Top Picks" + methodology statement to every roundup; add affiliate CTAs to guides/comparisons.

## Limitations
No GSC/DataForSEO rank data — assessments are intent-vs-content, not measured positions. 9 pages sampled of 285.
