# GEO / AI-Search Readiness — audiotechexpert.com

**Score: 41/100.** Right topics + full AI-crawler access, but structurally unprepared for AI citation.

Weighted: Citability 38 · Structural readability 52 · Multi-modal 20 · Authority/brand 28 · Technical accessibility 62.

## 1. AI crawler accessibility — INFO (open, but passive)
robots.txt `User-agent:* / Disallow:` → GPTBot, OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended, CCBot, Bingbot all ALLOWED. No AI-specific rules, no crawl hints. Correct posture for a publisher, but not deliberate.

## 2. llms.txt — HIGH (missing)
`/llms.txt` → 404. No machine-readable index of best/canonical content for LLM agents. **Fix:** publish llms.txt listing the top 25–30 comparison/guide pages with one-line descriptions + licensing.

## 3. Passage-level citability — HIGH
- Paragraphs cluster at 40–78 words; **0** in the ~134–167-word self-contained-citation range across 5 sampled posts.
- Direct-answer intros mixed; a **copy-paste intro artifact** (codec-compatibility sentence) appears on pages where it's off-topic (e.g. ANC page) → confuses AI passage extraction.
- **Zero `<table>`** on any comparison page — AI Overviews/Perplexity strongly favour tabular specs for "X vs Y".
- No FAQPage schema (Q&A content exists); H2s are topical not question-format; no TL;DR/Quick-Answer blocks; no `speakable`.
- Positive: inline specs/stats present (codec bitrates, dB, frequency ranges) — better in a table.

## 4. Entity & brand signals — HIGH
- **Empty `WebSite.name`** → AI engines literally cannot name the publisher as a source (most damaging single issue).
- No Organization schema; `Person` present but `sameAs` only self-links (no phillipstrang.com/social); no `article:author`/`rel=author` in HTML.
- No Wikipedia/Wikidata entity; **no YouTube** presence (YouTube has the strongest correlation with AI citation); no Reddit signal; no `sameAs` anywhere.

## 5. Technical AI accessibility — MEDIUM
- Server-rendered (good); but trafilatura extracted empty body → high boilerplate-to-content ratio (Elementor chrome) may hinder AI body isolation.
- `article:published_time` present; `article:modified_time` inconsistent; self-canonicals correct; no `SpeakableSpecification`.

## 6. Fitness on buyer queries
- **Google AI Overviews 28/100** — no tables, no early verdict, no FAQ schema, empty publisher name (no AIO source label).
- **ChatGPT/Browse 38/100** — allowed; good product-entity anchors (Shure SM7B, Blue Yeti, Neumann TLM 103); hurt by short paragraphs + no llms.txt.
- **Perplexity 35/100** — favours tables/bullets the site lacks; a competitor with a comparison table will be surfaced first.
- **Bing Copilot 40/100** — aligns with comparison-guide content; indexing status unknown.

## Top 5 recommendations
1. **Set WebSite.name** (Yoast → Site Representation) — fixes AI attribution. CRITICAL, 30 min.
2. **Add comparison tables** to every vs/best post (start top 10). HIGH.
3. **Publish /llms.txt** (top 25–30 guides). HIGH, ~2 h.
4. **Add 80–150-word "Quick Answer"/TL;DR** block under each H1 (template across 285 posts). HIGH.
5. **Add Organization + `sameAs`** to Person/Organization (phillipstrang.com, social, YouTube). HIGH.

Secondary: fix copy-paste intro artifact; add FAQPage (AI-only benefit; rich result retired May 2026); consistent `article:modified_time`; build a YouTube presence; noindex tag bloat so AI crawlers don't index thin archives.

Implementing the top 5 should move the score to ~68–72/100.
