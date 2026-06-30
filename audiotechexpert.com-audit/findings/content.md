# Content Quality & E-E-A-T Audit — audiotechexpert.com

**Score: 52/100 · E-E-A-T verdict: WEAK-TO-MODERATE** — fine for informational guides; materially deficient for product *recommendations* under Google's reviews system. Core vulnerability: the gap between claimed experience and demonstrable first-hand evidence.

> **Auditor correction:** The homepage is NOT missing its H1. Direct DOM check shows **2 `<h1>` tags** — `<h1 class="entry-title">Home</h1>` (wasted theme title) plus `<h1>Audio Gear, Tested and Explained</h1>` (hero) — and **5 H2s**. The real issue is *multiple H1 + a useless "Home" heading*, not absence. (The earlier "0 H1" came from a `parse_html.py` quirk.)

## E-E-A-T breakdown (weighted ~48/100)
Experience 28 · Expertise 55 · Authoritativeness 40 · Trustworthiness 62.

## What works
1. **Affiliate disclosure present & consistent** — inline per-post ("reader-supported… affiliate links… commission at no extra cost") + sitewide footer ("As an Amazon Associate…"). Two-layer, meets FTC.
2. **Named, linkable author** — "Phillip Strang" byline on posts and About; links to phillipstrang.com (real, verifiable person). (Note: WP username is `peter_froggatt`; public byline is "Phillip Strang".)
3. **/how-we-choose/ is substantive (~964 words)** — 4-step process, 6 scoring criteria, editorial rules, and a candid admission it is "not a testing laboratory with acoustic measurement rigs." Unusually transparent.
4. **Roundups are structured** — each pick gets a named role ("best value", "best for X") + one genuine limitation; no padding.
5. **Genuine domain knowledge in explainers** — accurate technical detail (impedance, frequency response, codec bitrates).
6. **Consistent single H1 + multiple H2 on individual posts**; visible "Updated" dates on roundups.
7. **Readability** ~ FK grade 9–11, scannable, consistent British English.

## Findings

### CRITICAL — No hands-on testing evidence in affiliate roundups
/how-we-choose/ admits no physical testing. Roundups source claims from Amazon listing copy (e.g. WH-1000XM6 recommended because "Sony's own marketing calls it 'The Best Noise Canceling Wireless Headphones'… based on everything its title tells us"). No measurements, listening impressions, or unit photos. Google's reviews system rewards "evidence… of your own experience with the product"; this profile is exactly what the helpful-content system targets. **Fix:** add original photos + concrete per-pick impressions to top 5–10 roundups, or reframe explicitly as "curated research roundups" rather than reviews.

### CRITICAL — No author photograph; Person schema incomplete; credential mismatch
About page (391 words) has zero photos of the author (all 3 imgs = logo); alt-text placeholder "The person behind the reviews" suggests an image was planned but never uploaded. No `Person` schema `image`. Worse, a **credential mismatch**: About describes a "writer and long-time audio enthusiast / 150+ crime novels," but post intros claim "After fifteen years building signal chains across studios, live venues, and broadcast facilities." If real, feature it on About; if rhetorical, rewrite honestly. **Fix:** real author photo, author avatar on bylines, full `Person` schema (`image`, `url`, `sameAs` → phillipstrang.com), reconcile credentials.

### HIGH — Imagery is stock/AI-generated, no original product photography
Homepage hero = hotlinked Unsplash. A review hero filename `48arKdwIqXYlgqCAspDgB.jpg` has the signature of an AI-generated asset. Product images are Amazon CDN thumbnails (AAWP), not editorial. Reinforces the zero-experience signal. **Fix:** photograph real units; if AI images retained, add IPTC `DigitalSourceType` (scripts/iptc_ai_label.py); self-host the homepage hero.

### HIGH — "Reviews" framing vs research-only methodology (QRG/FTC alignment)
Brand = "Honest Headphone & Mic Reviews"; URLs say "best-…"; yet methodology confirms no testing. **Fix:** soften to "Guides"/"Recommendations", add 1–2 sentences of original analysis beyond the Amazon listing per roundup (owner-review patterns, third-party measurements like rtings.com), and clarify research-tier vs hands-on.

### MEDIUM — Thin-content/index bloat: 310 tags + 58 categories
More tag archives than posts; categories average <5 posts. **Fix:** noindex tag archives (Yoast), merge/noindex sub-4-post categories, keep core hubs.

### MEDIUM — Contact page very thin (~88 words)
No visible email/address; body may be a form shortcode stripped from raw HTML — verify it renders. **Fix:** confirm form renders, add intro + response-time + fallback email.

### MEDIUM — About page shallow on audio niche (391 words)
Leads with fiction career; no gear list, no audio background. **Fix:** expand to 600–800 words with audio-specific credibility.

### MEDIUM — Update dates only on roundups, not explainers
Codec/ANC explainers from 6+ months ago risk staleness. **Fix:** visible "Last reviewed" + review schedule.

### LOW — Templated intros; no external citations; no Review/ItemList schema; no AI-citation structure
Repeated 4-clause intro pattern; no outbound authority links; AAWP product boxes emit no JSON-LD. **Fix:** vary intros, add 1–2 authoritative outbound links/post, add Article/ItemList (and Review where credibly earned) schema.

## AI citation readiness: 34/100
Lacks structured data, external citations, and verifiable first-hand claims AI systems use to select quotable passages. /how-we-choose/ is the best citation candidate.
