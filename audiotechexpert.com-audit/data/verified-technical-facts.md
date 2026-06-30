# Independently verified technical facts (main auditor)

## Canonicalization — CLEAN (pass)
- http→https: 301 → https (1 redirect)
- www→non-www: 301 → non-www (consolidated)
- no-slash→slash: 301 normalizes trailing slash
- 404 page returns proper HTTP 404

## x-robots-tag — NOT a site-wide noindex (false alarm prevented)
- `x-robots-tag: noindex, follow` appears ONLY on the /robots.txt response (content-type text/plain). Harmless/normal.
- Homepage and review posts: NO x-robots-tag header; HTML `<meta robots>` = `index, follow`. Pages ARE indexable.

## Security headers — mostly MISSING (Medium)
- Strict-Transport-Security (HSTS): MISSING
- X-Frame-Options: MISSING
- X-Content-Type-Options: MISSING
- Referrer-Policy: MISSING
- Content-Security-Policy: only `upgrade-insecure-requests`
- permissions-policy: present (Hostinger default private-state-token block)

## Tag-archive index bloat — CONFIRMED (High)
- 310 tag archives, all self-canonical, NO noindex meta → all indexable.
- Slugs are auto-generated and keyword-stuffed, e.g.:
  `tag/acoustic-guitar-microphone-guitar-pickup-l-r-baggs-shure-sm57-audio-technica-instrument-microphone/`
- 310 tags > 285 posts. Many tag archives likely 1 post (thin). Index-bloat + Helpful-Content/quality liability for an affiliate site.

## Platform
- WordPress + Yoast SEO; PHP 8.3; Hostinger hcdn CDN; AAWP (Amazon affiliate) plugin.
- Homepage HTML served as x-hcdn-cache-status: DYNAMIC (not edge-cached).
