# SEO Re-Audit #8 — audiotechexpert.com

**Date:** 2026-07-01 (performance pass) · Verifies the CLS/render-blocking work and re-checks integrity/holding state.
**Method:** fresh live checks — rendered HTML on home + 2 posts, REST raw content on all 271 posts, headers, `/llms.txt`. No Google field data; measures *implemented state*, not rankings.

## Health Score trend: 51 → … → 75 → 76 → **77**

| Category | Weight | #7 | **#8** | This round |
|---|---|---|---|---|
| Technical SEO | 22% | 82 | **82** | — |
| Content Quality | 23% | 69 | **69** | — |
| On-Page SEO | 20% | 84 | **84** | — |
| Schema | 10% | 87 | **87** | — |
| Performance (CWV) | 10% | 63 | **72** | **+9** |
| AI Search (GEO) | 10% | 68 | **68** | — |
| Images | 5% | 72 | **72** | — |

**Composite 76.1 → 77.0 ≈ 77.**

## What moved — Performance 63 → 72 (verified live)
- **CLS resolved sitewide.** Images missing width/height went from **13/13 (home) and 7/11 (post) → 0** across the sampled pages (76 images checked, 0 without dimensions). LiteSpeed "Add Missing Sizes" + the theme now emit dimensions on every image, including the AAWP/Amazon product images that previously shifted layout.
- **Render-blocking reduced.** jQuery Migrate dropped (mu-plugin); head render-blocking scripts down to ~1/page (jQuery core, deliberately left un-deferred to protect Elementor). Lazy-load active (43 lazy images across 3 pages) with SVG placeholders reserving space.
- **Amazon CDN preconnect** live on every page (AAWP product images load cross-origin from `m.media-amazon.com`). Brotli serving.
- *Estimate, not field data:* CLS — the measurable lab defect — is fixed; LCP depends on whether the above-fold image is lazy-loaded (see watch item). Confirm with CrUX/GSC over 2–4 weeks.

## Integrity regression found and fixed
- **A newly published post (id 5205, `upgrade-headphones-or-source`, published today) contained the fabricated "After fifteen years of working across studio sessions…" claim.** Post count had risen 270 → 271. Fixed live with the approved honest voice (verified: 0 "fifteen years", "obsessively…" present).
- **Root cause / open risk:** the content pipeline that publishes new posts still uses the fabricated template. The Batch 9 fix cleaned existing posts; it did not change the source. **Every new post will reintroduce the claim until the source template/prompt is fixed or a save-time guard is installed.** Recommend one of: (a) fix the generator's author-intro template, or (b) install a `save_post` guard mu-plugin that auto-rewrites the pattern on publish. This is now the single most important open item — it silently undoes E-E-A-T work.

## Holding (verified)
- 0 "tested" titles; 15 comparison tables; 14 measurement-backed roundups; single homepage H1; hero "Compared and Explained"; `/llms.txt` 200; xmlrpc 403; REST users 404. One niche roundup still leads with a generic product (unchanged, flagged).

## The path from 77 to 80
1. **Stop the credential regression** (pipeline template or save-time guard) — protects everything already banked.
2. **Long-tail roundups (~61)** — continue the measurement/spec-backed pattern; also sweep any *new* posts for fabricated claims / overclaims as they publish.
3. **Confirm LCP** (VPI or hero exclude) once field data is in.

## Bottom line
**51 → 77.** Performance now joins the "done" column — CLS fixed, render-blocking trimmed, Amazon images preconnected. The headline caveat is not the score but the **live content pipeline still minting fabricated-credential intros**: worth fixing at the source before it quietly erodes the E-E-A-T gains. The remaining points to 80 are the long-tail roundups plus that pipeline fix.

*Full change record: `../REMEDIATION-LOG.md` (Batches 9–13).*
