# Remediation Log — audiotechexpert.com

Live changes applied via the WordPress REST API (authenticated as `peter_froggatt`, administrator). All changes reversible as noted.

## 2026-06-30 — Batch 1 (automated)

### ✅ 1. Site identity set (reversible)
- **Before:** site title `""`, tagline `""` (empty).
- **After:** title = "Audio Tech Expert"; tagline = "Honest headphone & microphone reviews and guides".
- **Method:** `POST /wp-json/wp/v2/settings`.
- **Verified live:** `WebSite` schema `name` now "Audio Tech Expert" (was empty); category `<title>` tags now end "… - Audio Tech Expert" (were dangling "Guides -"); `og:site_name` populated.
- **Fixes audit items:** Schema C-1 (empty WebSite.name), On-Page (dangling titles), GEO (AI attribution).
- **Revert:** set both fields back to "" via the same endpoint.

### ✅ 2. Tag bloat removed (reversible via manifest)
- **Before:** 553 tags (242 zero-post, 302 one-post, 9 ≥2-post). 98% keyword-stuffed comma-string junk (e.g. "gaming microphones, streaming microphone, USB microphone, …").
- **Action:** deleted all 544 tags with ≤1 post (`DELETE /wp/v2/tags/{id}?force=true`); kept the 9 genuine tags (audio quality, bluetooth headphones, headphones 2026, ldac, on-ear headphones, over-ear headphones, recording equipment, wireless audio, wireless headphones).
- **After:** 9 tags. `post_tag-sitemap.xml`: 310 → 9 `<loc>`.
- **Posts unaffected** (verified: sample posts still HTTP 200; only tag terms removed).
- **Restore record:** `data/deleted-tags-manifest.json` (all 544 deleted names/slugs/ids). Tags can be recreated from it if ever needed.
- **Fixes audit items:** Technical C-2 / Sitemap CRITICAL / Content (tag index bloat).

## 2026-06-30 — Batch 2 (automated, via Redirection plugin)

### ✅ 3. Duplicate URLs consolidated (reversible)
- **20 × 301 redirects created** via Redirection plugin REST API (`redirection/v1/redirect`), all verified (source 301 → target, target 200, no loops):
  - 18 WordPress `-2`/`-7` auto-slug duplicate posts → their clean originals.
  - `/recording-production/` → `/guides/recording-production/`.
  - `/headphone-guides-old/` → `/headphone-guides/`.
- **20 duplicate source posts/pages moved to Trash** (reversible) so they leave the post-sitemap; redirects persist independently of the trashed posts. `post-sitemap.xml`: 285 → 268.
- **Restore record:** `data/trashed-duplicates-manifest.json` (type/id/slug of each). Restore = un-trash in WP admin.
- **Fixes audit items:** Technical C-1 / Sitemap HIGH / On-Page / SXO (duplicate `-2` cannibalization, recording-production pair, headphone-guides-old).

### ⚠️ 3 skipped — need manual decision (year-slug ambiguity)
The loop-detector skipped these because the base slug itself already redirects (auto-redirecting them would risk a chain/loop). Each needs you to pick the canonical URL:
- `/best-noise-cancelling-headphones-2026-2/` — this `-2026-2` slug is the **live** page (the clean `/best-noise-cancelling-headphones/` already 301s to it). Recommend renaming it to a clean slug.
- `/best-in-ear-monitors-earbuds-2026-2/`
- `/rode-wireless-go-ii-vs-dji-mic-2/` ("Mic 2" may be a real product name — verify before touching).
Tell me the intended canonical for each and I'll apply the slug change + redirect.

## 2026-06-30 — Batch 3 (mu-plugin, schema)

### ✅ 4. Organization + Person schema (reversible)
- Installed `wp-content/mu-plugins/audiotechexpert-schema.php` (source: `snippets/audiotechexpert-schema.php`), hooking Yoast's `wpseo_schema_graph`.
- **Verified live** on homepage + post pages:
  - **Organization** node added (name "Audio Tech Expert", auto-detected logo, `publishingPrinciples` → /how-we-choose/).
  - **WebSite/Article `publisher`** → `#organization`.
  - **Person** (Phillip Strang) enriched: `sameAs` now includes `https://phillipstrang.com`, `worksFor` → Organization, `knowsAbout` populated.
- **Fixes audit items:** Schema C-3 (no Organization), W-3 (Person.sameAs self-only), W-4 (Person not linked to publisher).
- **Revert:** delete the mu-plugin file.
- **Not included (by design):** Product/Offer/ItemList — prices must be pulled live from AAWP, not hardcoded; pending AAWP version confirmation.

## 2026-06-30 — Batch 4 (installed & verified live)

Verified: HSTS + X-Content-Type-Options + X-Frame-Options + Referrer-Policy present; `X-Powered-By` removed; `xmlrpc.php` → 403; `/?author=1` → 403; `/wp-json/wp/v2/users` → 404 (no route); roundup ItemList+Product schema rendering with clean product names on `/best-…/` posts.

Files in `snippets/` (installed via Hostinger File Manager — no API path exists for these).
- `security.htaccess.txt` — paste above `# BEGIN WordPress` in `.htaccess`. Adds HSTS + X-Content-Type-Options + X-Frame-Options + Referrer-Policy, removes X-Powered-By, blocks `xmlrpc.php`, blocks `?author=N`. Fixes H-3, H-4, H-5, H-7.
- `audiotechexpert-hardening.php` → `wp-content/mu-plugins/`. Disables REST user enumeration (H-6), removes WP version + X-Powered-By.
- `audiotechexpert-roundup-schema.php` → `wp-content/mu-plugins/`. **v2 (AAWP Pro):** full ItemList + Product + **Offer with live price** on "best" roundups, read from AAWP's rendered `data-aawp-product-*` + `.aawp-product__price--current` (schema price == on-page price). Dedupes by ASIN, 6h cache, rebuilds on save. **Verified live:** e.g. best-noise-cancelling roundup → Sony WH-1000XM6 $458, Bose QC Ultra $379, etc., each with sku + image. Fully fixes C-5.

## 2026-07-01 — Batch 5 (performance: page caching)

### ✅ 5. LiteSpeed Cache enabled + AAWP-country vary (reversible)
- Activated **LiteSpeed Cache** plugin (full-page server cache) + installed `audiotechexpert-lscache-vary.php` mu-plugin registering `aawp-country` as a `litespeed_vary_cookies` entry (cache a separate copy per geotargeting country).
- **Verified live:** `x-litespeed-cache: hit` with `x-hcdn-upstream-rt` **~0.036–0.100s** on cache hits (was 0.8–3.0s full PHP render). Major TTFB/LCP win.
- **Open / unverifiable from here:** per-country vary correctness — AAWP geotargets by server IP, not the injectable cookie, and all probes originate from one US IP, so foreign-visitor output can't be simulated. Confirm via VPN test or by checking whether AAWP has non-US stores/tags configured (if US-only, nothing to vary → safe).
- **Revert:** deactivate LiteSpeed Cache / remove the vary mu-plugin.

### ✅ 6. Images: WebP + homepage Unsplash self-hosted (verified)
- LiteSpeed Image Optimization → WebP replacement ON (serves WebP with original fallback; verified `content-type: image/webp` on self-hosted images).
- Homepage: all 12 hotlinked `images.unsplash.com` images (incl. the CSS hero, which was a dead 404) replaced with self-hosted Media Library copies — **verified 0 Unsplash references remain; 16 self-hosted images all 200 + WebP; schema primaryImageOfPage now on-domain**.
- Removed the broken `.ate-hero::before` background (was requesting a since-deleted Unsplash photo → 404 on every load).
- **Remaining perf items:** explicit width/height on images (CLS); render-blocking jQuery/fonts/CSS (Elementor-sensitive — do last).

### ✅ 7. Category/landing images self-hosted site-wide (verified)
- Installed `audiotechexpert-unsplash-rewrite.php` (v1.1.1) — rewrites `images.unsplash.com/photo-*` → self-hosted uploads in rendered content AND Yoast og:image / Twitter image / schema ImageObject. Uploaded the 10 remaining category images (5 reused from homepage batch).
- **Verified:** 16 pages (landing pages + `/category/` archives) → **0 Unsplash references** (content + `<head>` OG/schema); replacements serving WebP.
- **Incident (resolved):** the first upload left a duplicate copy of the mu-plugin in `mu-plugins/` (browser `(1)` suffix), causing a "Cannot redeclare function" fatal → site-wide HTTP 500. Fixed by deleting the duplicate; plugin hardened with a double-load `define()` guard (v1.1.1) so a stray duplicate can no longer fatal the site. Lesson: keep exactly one copy of any mu-plugin.

## 2026-07-01 — Batch 8 (content: comparison tables)

### ✅ 8. First comparison table added (worked example)
- Inserted a quick-answer paragraph + real `<table>` comparison (ATH-M50x vs ATH-M70x) into post id 4883 (`/ath-m50x-vs-ath-m70x/`) via REST — verified rendering live (14 rows, live prices $159/$329, verified AT specs, attributed affiliate CTAs, no wpautop mangling). WordPress revision created (revertible).
- Addresses SXO/GEO "0 tables on vs posts" for this post; template + filled example in `snippets/`.
- Added the same pattern to 3 more vs posts (verified live, 13 rows each, working CTAs):
  - `/he400se-vs-hd-560s/` (id 4859) — also fixed a missing HD 560S affiliate link (post had no box for it; used ASIN B08J9MVB6W + site tag).
  - `/akg-k361-vs-k371/` (id 4891); `/dt-990-pro-vs-akg-k712-pro/` (id 4849).
  - Cross-brand tables omit the sensitivity row (brands publish different units → not comparable); "Our pick" on each flagged for owner's editorial confirmation.
- Added 2 more tables (responsive CSS): `/ath-m40x-vs-sony-mdr-7506/` (id 4845), `/sony-wh-1000xm5-vs-xm6/` (id 4683). 6 vs posts now have tables. `/bw-px7-s2-vs-sony-xm6/` (id 4687) — resolved the S3/S2 mismatch: renamed slug s3→s2, added 301 from the old S3 URL, then inserted the Px7 S2 vs XM6 table. Added `/sennheiser-momentum-4-vs-sony-xm6/` (id 4689) and `/sennheiser-hd600-vs-hd650/` (id 4680). Added 3 microphone tables: `/rode-nt1-vs-nt2-a/`, `/at2020-vs-at4040/`, `/shure-sm7b-vs-sm7db/`. Added 3 more mic tables (xm8500/sm58, sm7b/procaster, podmic/mv7+). **15 vs posts** now have responsive comparison tables. Note: the Shure MV7+ (B0CTJ7PVN1) has no price in its AAWP box on `/rode-podmic-vs-shure-mv7/` — used a 'Check price' link; worth checking AAWP's price data for that ASIN.
- Redirect chain collapsed: Redirection rule id 3 `/category/guides/recording-production/` repointed directly to `/guides/recording-production/` (was chaining via `/recording-production/`; now 1 hop).
- Remaining: roll the same pattern across the other "vs" posts; consider adding an HD 560S AAWP box for a live price.

## 2026-07-01 — Batch 9 (content: E-E-A-T credential-claim integrity)

### ✅ 9. Removed fabricated "fifteen years" professional-experience claims (88 posts)
- **Problem:** 88 posts opened with a fabricated professional-credential claim — variants of *"After fifteen years of working with audio gear across studio floors, live venues, and home listening rooms…"*, *"After fifteen years recording/mixing/designing…"*, *"placing lavalier microphones on presenters, actors, lecturers, and broadcast talent for over fifteen years…"*. These asserted a professional studio/live-sound/engineering history the author does not have — the single biggest E-E-A-T *trust* liability on the site (fabricated experience is exactly what Google's QRG flags as lowest-quality).
- **Fix (owner-approved voice):** replaced the fabricated clause with an honest hobbyist framing — **"After years of obsessively buying, using and comparing audio gear,"** (subject-form *"Years of obsessively buying, using and comparing audio gear have taught me…"* where grammar required; one post uses *"I have spent countless hours…"*).
- **Method:** two-pass, safety-first, all via `POST /wp/v2/posts/{id}` (each edit creates a WP revision → revertible):
  - **79 auto** — a bounded regex matched only a *sentence-leading* capital `After/Over … fifteen years …` clause up to the comma that closes the introductory adverbial (lookahead `(?=[^,.?!<]*[.?!<])` ensures it lands on the clause boundary even through the fabricated venue enumeration; `[^.?!<]` prevents crossing sentence/tag boundaries; matches >220 chars rejected). Works for both *"…, I have learned…"* and noun-subject *"…, the question I hear most often…"* continuations.
  - **9 manual** — hand-written exact replacements for the grammatically distinct cases the regex deliberately skipped (noun-subject *"Fifteen years … has taught me…"*, mid-sentence lowercase *"…answer I can give after fifteen years…"*, *"I have spent the better part of fifteen years…"*, and the lavalier claim). Each verified to match exactly once.
- **Verified live:** **0 of 270 posts** contain "fifteen years" in REST raw content afterward; rendered HTML spot-checks (auto + manual, pronoun + noun-subject) all read cleanly. 87 posts now carry the approved "obsessively…" opener, 1 the "countless hours" variant.
- **Fixes audit items:** Content / E-E-A-T (fabricated first-hand experience — the dominant remaining lever toward the 80 target). Editorial change; no plugin.

## 2026-07-01 — Batch 10 (content: measurement-backed reviews + "tested" overclaim sweep)

### ✅ 10a. Worked example — measurement-backed roundup review (honest E-E-A-T)
- **`/best-noise-cancelling-earbuds-2026/` (id 4828), "Best overall: Sony WF-1000XM6":** replaced the generic spec-sheet paraphrase with a substantive, **independently-verified** write-up in the author's voice — **~88% average ANC reduction** and **9 h 41 min** single-charge battery (both from independent lab measurement; verified via SoundGuys), the actual engineering reason (QN3e chip + extra feed-forward mic), and honest trade-offs. **No fabricated personal testing** — citations phrased as "independent lab testing measured…" (unnamed, per owner preference).
- **Verified live:** 88% / 9 h 41 min / QN3e all render on-page.
- **This is the template** for upgrading thin roundup blurbs. Rollout note: each product's numbers must be verified against a real source before publishing — this is verify-then-write, not blind mass-injection (injecting unverified specs would recreate the fabrication risk Batch 9 removed).

### ✅ 10b. Removed "Tested" overclaims from roundups (title + body)
- Roundups titled "…Tested and Ranked" / "Tried, Tested and Ranked" claimed hands-on testing of brand-new 2026 flagships that was not performed — the same trust liability as the "fifteen years" claims.
- **4 titles** softened to "**Compared and Ranked**" (the NC-earbuds roundup in 10a + `best-budget-wireless-earbuds-2026`, `best-bone-conduction-headphones-2026`, `best-headphones-for-running-gym-2026`).
- **6 bodies** fixed: five "We have tested and ranked…" → "We have **compared** and ranked…"; one "after testing dozens of wireless headphones across studio and consumer environments" → "after years of buying, using and comparing wireless headphones" (approved voice).
- **Verified live:** 0 posts retain "tested" in title; 0 retain first-person test claims (`we tested / we have tested / after testing dozens / in our tests / hands-on tested`) in body.
- **Fixes audit items:** Content / E-E-A-T (fabricated testing claims). Editorial; reversible via WP revisions.

## 2026-07-01 — Batch 11 (cleanup: homepage + investigated the rest)

### ✅ 11a. Homepage second H1 removed (live)
- The homepage rendered **two H1s**: the Astra theme page-title banner ("Home") + the hero ("Audio Gear, …"). Set the front page's Astra meta `site-post-title = disabled` via REST.
- **Verified live:** H1 count 2 → **1** (only the hero remains). Fixes the audit's "homepage two H1" item. Reversible (clear the meta).

### ✅ 11b. Homepage hero "Tested" → honest wording (data layer done; needs a 1-click Elementor flush to display)
- Hero H1 was **"Audio Gear, Tested and Explained"** — a testing claim on the site's most visible banner, contradicting the Batch 9/10 integrity work. Edited the Elementor HTML-widget text inside `_elementor_data` (single occurrence) via REST: **"Tested and Explained" → "Compared and Explained."**
- **Verified at source:** `_elementor_data` now contains the new phrase, old phrase gone; JSON still valid; page loads 200.
- **Display pending:** Elementor serves pre-rendered HTML from its **Element Caching** (stored outside postmeta), which a REST write cannot flush. LiteSpeed purges fine on save (the H1 change above rendered immediately). **Owner action (5s):** Elementor → Tools → **Regenerate CSS & Data** (or open Home in Elementor and click **Update**, or turn off the Element Caching experiment). Text will then show live.

### Investigated — deliberately NOT changed (risk/reward)
- **3 "legacy" slugs:** `rode-wireless-go-ii-vs-dji-mic-2` is **correct** — "DJI Mic 2" is a real product, not a WP duplicate suffix (left as-is). The two `-2026-2` slugs (`best-noise-cancelling-headphones-2026-2` id 4443, `best-in-ear-monitors-earbuds-2026-2` id 4476) are **top money pages**; the clean `-2026`/base slugs already 301 *into* them via redirect rules that aren't cleanly enumerable through the Redirection API — renaming risks a redirect loop on a revenue page for a purely cosmetic gain. **Left as-is** (they serve 200 correctly). Safe manual procedure available on request.
- **Author archive:** currently indexable. The original audit flagged it as thin/duplicate, but after the Batch 9–10 author-identity work (real photo in `Person.image`, visible author box, `sameAs`) it now functions as the **author-entity page** — recommend **keeping it indexed** rather than noindexing (index status doesn't affect the schema entity, and an indexable author page is an E-E-A-T asset). No change.
- **42 empty categories:** verified **low exposure** — most are excluded from `category-sitemap.xml` already, and several (e.g. `sony-comparisons`, `travel`) **canonicalize to their nested parent**, so they aren't competing duplicates. They look like intended taxonomy to populate later. Not worth a new mu-plugin upload (recurring duplicate-file 500 risk) for marginal gain. **Left intact**; optional noindex-empty-terms filter available if desired.

## 2026-07-01 — Batch 12 (measurement-backed roundups — batch 1)

### ✅ 12a. `/best-headphones-under-200-2026/` — "Best overall: Sony WH-CH720N" upgraded (live)
- Replaced spec-sheet paraphrase with verified, cited measurements in the author's voice: **~192 g** (one of the lightest wireless ANC over-ears measured), **just over 40 hours** measured ANC battery (beats Sony's 35-hour claim), and honest ANC detail (**~20 dB low-frequency reduction, peaking ~28 dB at 80 Hz**). Attribution: "independent lab testing / independent measurements" (unnamed). Sources: SoundGuys + RTINGS WH-CH720N reviews.
- **Verified live:** all figures render on-page.

### ⚠️ 12b. Finding — a handful of roundups lead with generic, unmeasurable products
- Sizing scan of all 75 `best-` roundups: **~62 lead with real, brand-name products** (measurement-backable). A small number lead with **unbranded generic Amazon listings** whose "specs" are seller marketing (no independent lab data exists), so they **cannot be honestly measurement-backed**. Confirmed offender: **`best-headphones-under-100-2026`**, whose "Best overall" is *"Hybrid Active Noise Cancelling Headphones (Bluetooth 6.0, 120H)"* (ASIN B0GSD8BWXV) — "Bluetooth 6.0" / "120H" are the seller's claims. Others: some phone-lavalier and sleep-earbud roundups (MAYBESTA, BAILIXIN, T33).
- **Owner decision:** promote a real product. Applied to under-100 (12c).

### ✅ 12c. `/best-headphones-under-100-2026/` — generic top pick replaced (live)
- **Promoted JBL Tune 720BT to "Best overall"** (was the generic "Hybrid ANC, Bluetooth 6.0, 120H"). New blurb is spec-backed and honest: ~76 h rated battery corroborated by reviewers, Bluetooth 5.3, JBL Pure Bass tuning, and an explicit "no ANC" caveat.
- **Demoted the generic to "Best battery life"** with its claims honestly reframed: *"the manufacturer claims up to 120 hours… these are unbranded seller specs with no independent lab verification, so treat them as claims rather than measured results."* No longer presented as the site's #1 recommendation, and no dubious spec asserted as fact.
- Reordered the top comparison box to put the JBL first. **Verified live:** H2 order now leads with JBL; generic no longer at #1.
- Note: two other slots on this page (home/office, sports) are also generic but already hedge with "claimed" — left as-is.

### ✅ 12d. Five more money-page roundups measurement-backed (live)
All lead with real, branded products; top-pick opener rewritten with verified figures. Headphones cited as "independent testing/measurements" (unnamed); microphones use published specs + established reputation (mics aren't ANC-measured, so no lab-test phrasing). All figures verified via SoundGuys/RTINGS/manufacturer + reviewer corroboration; verified live on-page.
- **`/best-headphones-under-300-2026/` — Nothing Headphone (1):** 40 mm KEF-tuned drivers; ANC ~85% avg cut (15–25 dB low-freq); ~43 h measured ANC battery (past 35 h rating), 80 h off.
- **`/best-gaming-headphones-2026/` — Sony INZONE H9 II:** 30 mm carbon WH-1000XM6 driver, FNATIC-tuned; ~260 g (≈57 g lighter than H9); ANC ~80% avg; up to 30 h battery.
- **`/best-headphones-commuting-2026/` — Soundcore Space One Pro:** FlexiCurve ~270° fold; measured ~288 g; Adaptive ANC ~84% avg; 40 h ANC / 60 h off, 5-min → ~5 h charge.
- **`/best-condenser-microphones-2026/` — Blue Yeti:** three 14 mm capsules → four patterns (cardioid/bi/omni/stereo); 48 kHz/16-bit; 20 Hz–20 kHz; plug-and-play.
- **`/best-dynamic-microphones-2026/` — Shure SM58-LC:** cardioid dynamic; tailored 50 Hz–15 kHz (bass roll-off + presence peak); spherical pop filter; pneumatic shock-mount.

**Measurement-backed roundup progress: 8 of 75** (under-100 promoted+backed, under-200, under-300, gaming, commuting, condenser, dynamic, + the earlier NC-earbuds Sony blurb).

### ✅ 12e. Six more money-page roundups measurement-backed (live)
Same method (verified figures; headphones cited as independent testing, audiophile open-backs + mics use published specs). Verified live on-page.
- **`/best-headphones-under-50-2026/` — Sony WH-CH520:** ~147 g; 55 h measured battery (past 50 h rating); BT 5.2 + Multipoint/Fast Pair.
- **`/best-audiophile-headphones-under-500-2026/` — Sennheiser HD 650:** open-back, 300 Ω, 42 mm transducer, <0.05% distortion; benchmark midrange; needs an amp.
- **`/best-microphones-for-streaming-2026/` — Blue Yeti:** three 14 mm capsules → four patterns; 48 kHz/16-bit; 20 Hz–20 kHz; headphone monitoring.
- **`/best-headphones-for-music-2026/` — Sennheiser HD 600:** open-back, 300 Ω, 16 Hz–30 kHz (±1 dB), 0.1% THD, 97 dB; reference tuning.
- **`/best-in-ear-monitors-earbuds-2026-2/` — Sony WF-1000XM6:** ANC ~88% avg; QN3e chip + extra mic; ~9 h 41 min measured battery; ~24 h with case.
- **`/best-noise-cancelling-headphones-2026-2/` — Sony WH-1000XM6:** ~254 g; QN3 processor + larger mic array vs XM5; ~31 h ANC / 40 h off.

**Measurement-backed roundup progress: 14 of 75.**

## 2026-07-01 — Batch 13 (performance pass)

### ✅ 13. Performance: CLS fixed + render-blocking reduced (live)
- Measured first (no blind toggles): CSS/fonts already optimised (0 render-blocking stylesheets, 0 Google-Fonts links — LiteSpeed inlines CSS). Real issues were (a) render-blocking jQuery + Migrate, (b) AAWP product images loading cross-origin from `m.media-amazon.com` with no preconnect and no width/height (CLS).
- **`audiotechexpert-performance.php` mu-plugin (installed, verified live):** preconnect + dns-prefetch to `m.media-amazon.com`; drops the unused jQuery Migrate shim; marks content images `decoding="async"`. Verified: Amazon preconnect present on home + posts; `jquery-migrate` no longer loaded.
- **LiteSpeed toggles (owner-applied, verified live):** Load JS Deferred; Lazy Load Images; **Add Missing Sizes**; Responsive Placeholder.
  - **CLS win verified:** images missing width/height went **13/13 (home) and 7/11 (post) → 0/0**. Below-fold Amazon images now lazy-load with SVG placeholders.
  - Head render-blocking scripts reduced 2 → 1 (jQuery Migrate gone). jQuery **core** remains render-blocking by design (LiteSpeed excludes it from defer to protect Elementor — safe call).
- **Open / watch:** the above-the-fold featured image is currently lazy-loaded; if it is the LCP element this can delay LCP. Fix path: VPI (viewport images) once generated, or add the hero to Lazy Load Excludes. Confirm via GSC/CrUX LCP field data over 2–4 weeks.
- **Reversible:** delete the mu-plugin / toggle LiteSpeed settings back.

## 2026-07-01 — Batch 14 (credential-regression guard)

### ✅ 14. Save-time credential guard (installed & verified live)
- **Context:** re-audit #8 caught a newly published post (5205) reintroducing the fabricated "fifteen years of working…" claim. Posts are generated by a **make.com + Claude** pipeline whose prompt still used the fabricated author intro.
- **`audiotechexpert-credential-guard.php` mu-plugin:** hooks `wp_insert_post_data` and rewrites the fabricated clause to the approved honest voice on every post save (REST, classic editor, or programmatic). Mirrors Batch 9 logic (sentence-leading "After/Over … fifteen years …," + the "Fifteen years of … has/have" subject form); leaves legitimate uses (e.g. "warranty lasts fifteen years") untouched. Unslash → clean → reslash so quotes are preserved.
- **Verified live:** created a REST draft containing "After fifteen years of working across studio sessions, live rigs, and home listening setups, I have learned a lot." → stored as "After years of obsessively buying, using and comparing audio gear, I have learned a lot." (fabrication removed, honest voice present); test draft deleted.
- **Root-cause fix (owner):** update the make.com Claude prompt so it never claims a specific number of years or professional studio/live/engineering experience, and never claims first-hand lab testing (attribute measurements to independent sources or manufacturer specs). Guard is the backstop; the prompt is the source fix.
- **Reversible:** delete the mu-plugin.

## Not applied — needs host config or editorial work
- **301 redirects** — ~20 `-2` duplicate posts, `/recording-production/` pair, `/headphone-guides-old/` → need Redirection plugin (free) or Yoast Premium. (If Redirection plugin is installed, these can be automated via its REST API.)
- **Security headers / xmlrpc / expose_php** — Hostinger `.htaccess`/PHP settings.
- **AAWP-cookie CDN caching** — Hostinger CDN config.
- **Image self-hosting + WebP**, author photo, first-hand testing evidence, comparison tables — editorial/host.
- **42 empty categories** — left intact (look like intended structure, not cruft); review to populate or noindex.

## Credential note
Application Password "Claude Auto" was used for these changes. Revoke it in Users → Profile → Application Passwords when remediation is complete.
