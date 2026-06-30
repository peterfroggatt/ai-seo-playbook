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
- **Next perf step:** LiteSpeed Cache → Image Optimization → enable WebP (addresses the images half of the Performance score).
- **Revert:** deactivate LiteSpeed Cache / remove the vary mu-plugin.

## Not applied — needs host config or editorial work
- **301 redirects** — ~20 `-2` duplicate posts, `/recording-production/` pair, `/headphone-guides-old/` → need Redirection plugin (free) or Yoast Premium. (If Redirection plugin is installed, these can be automated via its REST API.)
- **Security headers / xmlrpc / expose_php** — Hostinger `.htaccess`/PHP settings.
- **AAWP-cookie CDN caching** — Hostinger CDN config.
- **Image self-hosting + WebP**, author photo, first-hand testing evidence, comparison tables — editorial/host.
- **42 empty categories** — left intact (look like intended structure, not cruft); review to populate or noindex.

## Credential note
Application Password "Claude Auto" was used for these changes. Revoke it in Users → Profile → Application Passwords when remediation is complete.
