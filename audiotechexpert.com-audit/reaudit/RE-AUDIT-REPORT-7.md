# SEO Re-Audit #7 — audiotechexpert.com

**Date:** 2026-07-01 (measurement-backed money-page batch) · Verifies 14 top roundups upgraded to verified, cited substance + the under-100 generic→real promotion.
**Method:** fresh live checks — REST raw content on all 270 posts + rendered HTML, headers, `/llms.txt`. No Google field data; measures *implemented state*, not rankings.

## Health Score trend: 51 → 66 → 70 → 71 → 72 → 74 → 75 → **76**

| Category | Weight | #6 | **#7** | This round |
|---|---|---|---|---|
| Technical SEO | 22% | 82 | **82** | — |
| Content Quality | 23% | 66 | **69** | +3 |
| On-Page SEO | 20% | 84 | **84** | — |
| Schema | 10% | 87 | **87** | — |
| Performance (CWV) | 10% | 63 | **63** | — |
| AI Search (GEO) | 10% | 67 | **68** | +1 |
| Images | 5% | 72 | **72** | — |
| *SXO (supplementary)* | — | ~54 | **~56** | +2 |

**Composite 75.3 → 76.1 ≈ 76.**

## What moved (verified live)
- **14 top money-page roundups now carry verified, cited substance** (was spec-sheet paraphrase). Each "Best overall" opener rewritten with real figures — headphones cited as independent lab measurements (8 use that phrasing), audiophile open-backs and microphones cited via published specs + established reputation (6). Examples now on-page: under-200 Sony WH-CH720N (192 g, 40 h measured), under-300 Nothing Headphone (1) (~85% ANC, ~43 h), gaming INZONE H9 II (260 g, WH-1000XM6 driver), IEMs WF-1000XM6 (~88% ANC, 9 h 41 min), condenser Blue Yeti (3×14 mm, 4 patterns), dynamic SM58 (50 Hz–15 kHz, pneumatic shock-mount).
- **Integrity: the under-100 "Best overall" is no longer a generic no-name.** The JBL Tune 720BT (real, trusted, spec-backed) was promoted to #1; the generic set was demoted to "Best battery life" with its "120 h / Bluetooth 6.0" claims relabelled as **unverified manufacturer claims**. Scan now finds only **1** roundup with a generic-looking top pick (down from the earlier handful).
- **GEO +1:** hard-number, comparison-style openers are exactly what AI Overviews / featured snippets extract for "best X" queries.

## Everything else — verified holding
- Content integrity intact: **0** posts with "fifteen years", **0** "tested" titles, **0** first-person test claims; 87 on the honest "obsessively…" voice; 15 comparison tables.
- Homepage: single H1, hero "Compared and Explained". `/llms.txt` 200. Author box present. Security: xmlrpc 403, REST users 404.

## Honest read on +1
This round put **real, verifiable substance on the pages that actually rank and convert** — the top-tier headphone and microphone roundups — and removed the last obvious integrity blemish (a no-name product sitting at #1 of a money page). That lifts Content from 66 to 69. The composite moves modestly because it's 14 of ~270 posts, but those 14 are the highest-intent commercial pages, so the *value* outweighs the score delta. This is the first round where content **depth/evidence** improved, not just trust/hygiene.

## The path from 76 to 80
1. **Long-tail roundups (~61 remaining).** Same measurement/spec-backed pattern on the narrower use-case roundups (running, sleeping, kids, brand-specific, etc.). Diminishing per-page traffic, but completes the library and compounds the GEO/AI-citation signal. Batch-able.
2. **Performance (10% at 63).** Image width/height (CLS) + defer jQuery / self-host fonts (Elementor-sensitive). A focused pass could add ~+1–2 composite — the largest single untouched lever.
3. **Extend comparison tables / quick-answer blocks** to the remaining vs posts and add spec tables to roundups.

## Bottom line
**51 → 76.** The site is honest, well-signposted, and now its money pages carry verified substance rather than paraphrase. The last four points to 80 are a straightforward split: **finish the roundup library** (editorial, batch-able) and **do a Performance pass** (technical). Both are well-defined; neither requires re-litigating anything.

*Full change record: `../REMEDIATION-LOG.md` (Batches 9–12).*
