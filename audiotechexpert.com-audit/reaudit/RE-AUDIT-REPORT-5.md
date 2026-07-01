# SEO Re-Audit #5 — audiotechexpert.com

**Date:** 2026-07-01 (E-E-A-T credential-integrity batch) · Verifies the removal of fabricated "fifteen years" professional-experience claims across 88 posts.
**Method:** fresh live checks (REST raw content on all 270 posts + rendered HTML, schema graph, headers, sitemaps). No Google field data (GSC/CrUX not wired here) — this measures *implemented state*, not live rankings.

## Health Score trend: 51 → 66 → 70 → 71 → 72 → **74**

| Category | Weight | Baseline | #4 | **#5** | This round |
|---|---|---|---|---|---|
| Technical SEO | 22% | 57 | 82 | **82** | — |
| Content Quality | 23% | 52 | 56 | **63** | **+7** |
| On-Page SEO | 20% | 68 | 82 | **82** | — |
| Schema | 10% | 34 | 86 | **86** | — |
| Performance (CWV) | 10% | 38 | 63 | **63** | — |
| AI Search (GEO) | 10% | 41 | 62 | **62** | — |
| Images | 5% | 35 | 72 | **72** | — |
| *SXO (supplementary)* | — | 38 | ~52 | **~52** | — |

**Composite: 72.0 → 73.6 ≈ 74.** The entire move comes from Content; every other category is verified holding.

## What moved (verified live)
- **Fabricated professional-experience claims removed from 88 posts.** Every "After fifteen years of working with audio gear across studio floors, live venues, and home listening rooms…" / "…mixing/designing/recording…" / "placing lavalier microphones on presenters, actors… for over fifteen years" intro is gone. Replaced with the owner-approved honest hobbyist voice: **"After years of obsessively buying, using and comparing audio gear,"**
  - **Verified:** `fifteen years` now appears in **0 of 270 posts** (was 88). 87 posts carry the approved opener; 1 uses the "countless hours" variant.
  - **No residual author over-claims:** a scan for `two decades / twenty years / professional engineer / in my studio / mastering engineer` returned only 2 hits, both **false positives** describing the *reader/profession*, not the author.
- **Why this is the highest-value content change to date:** fabricated first-hand experience is precisely what Google's Quality Rater Guidelines flag as *lowest quality* — a single sitewide trust liability sitting on ~⅓ of the index. Removing it de-risks the whole content library and replaces it with an honest, defensible authority signal.

## Everything else — verified holding
- **Security (Technical 82):** HSTS + `nosniff` + `SAMEORIGIN` + `Referrer-Policy` present; `X-Powered-By` blank; `xmlrpc.php` → 403; `/?author=1` → 403; `/wp-json/wp/v2/users` → 404. All intact.
- **Schema 86:** homepage graph carries Organization + WebSite + WebPage + BreadcrumbList; posts carry the Organization publisher link. Roundup ItemList/Product/Offer intact.
- **Performance 63:** LiteSpeed cache active, Brotli (`content-encoding: br`) serving.
- **Content structure:** 15 vs posts still carry real `<table>` + Quick-Answer blocks.
- **GEO 62 / crawlers:** robots allows all AI crawlers (no GPTBot/ClaudeBot/Perplexity/Google-Extended blocks) — correct for citation.
- **Sitemaps:** post-sitemap 270 locs; tag-sitemap 11 (bloat stays cleared).

## Honest read on +2
This is a **de-risking win, not a ranking-power win.** We removed a serious negative (sitewide fabrication) and swapped in honest positioning — that lifts Content from 56 to 63. It does **not** yet add *positive* first-hand proof (testing photos, measurements, the author's real face in schema), which is what pushes Content into the 70s. So the score moves modestly but the *trust foundation* is now sound — future E-E-A-T additions build on honest ground instead of a fabrication that a manual review (or a sharp reader) could have torched.

## Remaining priorities (74 → 80)
1. **Author photo not yet in schema — install plugin v1.2.** The schema graph still shows **no author image** on posts (checked live). The v1.2 `audiotechexpert-schema.php` (uploaded, committed, sent) forces your real photo into `Person.image` + every byline via `get_avatar_url`. This is a **10-minute install** (overwrite the file in `wp-content/mu-plugins/`, keep exactly one copy) and a real E-E-A-T signal. **Biggest quick win on the board.**
2. **Add genuine first-hand testing evidence** to the top 10–15 roundups/reviews — one or two lines of specific, honest observation ("in a week of commuting with these…"). Gets Content from 63 toward ~72.
3. **Publish `/llms.txt`** — still 404. Cheap GEO win.
4. **Performance:** explicit image width/height (CLS); carefully defer jQuery / self-host fonts (Elementor-sensitive — do last).
5. **Homepage hero "Tested and Explained"** wording (Elementor widget) + cleanup: 2nd H1, author-archive noindex, 3 `-2026-2` slugs, 42 empty categories.

## Bottom line
**51 → 74.** The site's content is now **honest** — the fabricated-credential problem that undercut every review is gone across all 88 affected posts, verified end-to-end. The path to 80 is no longer about removing liabilities; it's about **adding real proof**: get your photo live in the schema (v1.2 install), then layer first-hand testing notes onto the money pages. Watch GSC's Pages + Performance reports over the next 2–4 weeks.

*Full change record: `../REMEDIATION-LOG.md` (Batch 9).*
