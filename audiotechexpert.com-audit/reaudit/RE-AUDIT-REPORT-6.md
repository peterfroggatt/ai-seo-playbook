# SEO Re-Audit #6 — audiotechexpert.com

**Date:** 2026-07-01 (author-identity + honesty + GEO batch) · Verifies the visible author box, author photo in schema, homepage hero/H1 fix, the "tested" overclaim sweep, the first measurement-backed roundup blurb, and the live `/llms.txt`.
**Method:** fresh live checks — REST raw content on all 270 posts + rendered HTML, schema, headers, `/llms.txt`. No Google field data (GSC/CrUX not wired here); this measures *implemented state*, not rankings.

## Health Score trend: 51 → 66 → 70 → 71 → 72 → 74 → **75**

| Category | Weight | #5 | **#6** | This round |
|---|---|---|---|---|
| Technical SEO | 22% | 82 | **82** | — |
| Content Quality | 23% | 63 | **66** | +3 |
| On-Page SEO | 20% | 82 | **84** | +2 |
| Schema | 10% | 86 | **87** | +1 |
| Performance (CWV) | 10% | 63 | **63** | — |
| AI Search (GEO) | 10% | 62 | **67** | +5 |
| Images | 5% | 72 | **72** | — |
| *SXO (supplementary)* | — | ~52 | **~54** | +2 |

**Composite 74.0 → 75.3 ≈ 75.**

## What moved (all verified live)
- **Author identity is now complete — readers *and* crawlers.** The visible "About the author" box (real photo + honest bio + link) renders **once** per post across all ~270 posts, and the author photo (`f-phillip…jpg`) is live in the post **schema `Person.image`** (confirmed on-page). At #5 the photo was crawler-only; now it's reader-facing too — the trust signal Google's guidelines look for on money pages.
- **Homepage is honest top-to-bottom (On-Page +2).** Hero H1 now reads **"Audio Gear, Compared and Explained"** (was "…Tested and Explained"), and the page carries a **single H1** (the redundant Astra "Home" title is gone). Verified: `home_h1_count = 1`, old phrase absent.
- **All "tested" overclaims gone (Content +3).** 0 posts have "tested" in the title; 0 have first-person test claims (`we tested / we have tested / after testing dozens / in our tests`) in the body. Combined with the 88-post credential fix, the content library no longer claims experience or testing that didn't happen.
- **GEO +5 — `/llms.txt` is live.** Returns **200** as **`text/plain`**, valid markdown, 47 curated links, with editorial notes that reinforce the author entity and disclose the affiliate model. This was a flagged 404 gap; now a concrete AI-citation asset. All AI crawlers remain allowed.
- **First measurement-backed roundup blurb live** (Sony WF-1000XM6: 88% ANC, 9 h 41 min battery, QN3e chip — independently verified) as the template for the roundup upgrade.

## Everything else — verified holding
- **Security (Technical 82):** HSTS + nosniff + X-Frame + Referrer-Policy present; X-Powered-By removed; xmlrpc 403; `?author=1` 403; REST users 404.
- **Schema 87:** Organization + Person (now with real photo) + WebSite + ItemList/Product/Offer.
- **Performance 63:** LiteSpeed + Brotli serving; unchanged (render-blocking JS/fonts + CLS still open).
- **Content structure:** 15 vs posts with comparison tables; 87 posts on the honest "obsessively…" voice.

## Honest read on +1
The big integrity win (removing fabricated credentials across 88 posts) was already booked at #5. This round is **polish + reach**: the author box makes the identity *visible*, the homepage is now honest, every testing overclaim is gone, and `llms.txt` opens an AI-citation channel. Real, worth doing — but a modest composite move because **content *depth* barely changed**: only **1 of 75 roundups** carries a measurement-backed write-up. That's the lever still untouched.

## The path from 75 to 80
1. **Measurement-backed roundup blurbs — THE remaining lever.** Roll the Sony template across the top money-page roundups (under-100/200/300, gaming, commuting, condenser/dynamic mics). Verify-then-write per product. Getting the top ~15 roundups to real, cited substance is worth the bulk of the remaining points and most of the affiliate-conversion upside.
2. **Performance (10% at 63):** explicit image width/height (CLS) + defer jQuery / self-host fonts (Elementor-sensitive). Could add ~+1 composite.
3. **Extend comparison tables / quick-answers** to the remaining vs posts and add spec tables to roundups.

## Bottom line
**51 → 75.** The site is now *honest and well-signposted* — real author, honest homepage, no fabricated experience or testing claims, live `llms.txt`, structured comparisons, and a sound technical/schema/security base. The last five points to 80 are almost entirely **editorial depth on the money pages**: measurement-backed roundup content, done a verified batch at a time.

*Full change record: `../REMEDIATION-LOG.md` (Batches 9–11).*
