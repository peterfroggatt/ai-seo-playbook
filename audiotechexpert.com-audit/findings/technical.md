# Technical SEO — audiotechexpert.com

**Score: 57/100.**

## What works
- HTTPS sitewide, valid cert; http→https, www→non-www, and trailing-slash all 301 (single hop); proper 404s.
- Self-referential canonicals on all sampled posts; valid robots.txt (`User-agent:* / Disallow:`) + Yoast sitemap index (5 children).
- HTTP/2 + HTTP/3 (`alt-svc: h3`); PHP 8.3; Yoast v27.9.
- Server-rendered WordPress (is_spa false) — Googlebot sees full HTML.
- Correct viewport; generous `index,follow,max-image-preview:large,max-snippet:-1`.

## Critical
- **C-1 ~20 live `-2` duplicate posts, both self-canonical.** WP auto-slug collisions (`-2`…`-7`) create duplicate posts competing for identical intent (e.g. transparency-mode-vs-anc, noise-cancelling-vs-noise-isolating). The clean `/best-noise-cancelling-headphones/` 301s to `/best-noise-cancelling-headphones-2026-2/`. **Fix:** 301 weaker→stronger per pair; survivor self-canonicals; fix internal links.
- **C-2 310 tag archives indexed, near-zero content.** All 200, `index,follow`, self-canonical; 87% keyword-stuffed slugs; pages render only chrome (~1 post each). More tags (310) than posts (285). **Fix:** Yoast → Taxonomies → Tags → noindex (removes from sitemap too).

## High
- **H-1** `/guides/recording-production/` (7,096 chars) vs `/recording-production/` (3,880 chars) — identical title/H1, both indexed/self-canonical. → 301 to the `/guides/` URL.
- **H-2** `/headphone-guides-old/` 200, indexed, in sitemap, H1 "Headphone Guides Old", thin body. → 301 to `/headphone-guides/` or noindex.
- **H-3** `x-powered-by: PHP/8.3.30` on every response (fingerprinting). → `expose_php Off` / unset header.
- **H-4** No HSTS. → `Strict-Transport-Security: max-age=31536000; includeSubDomains`.
- **H-5** Missing `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` on HTML. → add via `.htaccess`.
- **H-6** `wp-json/wp/v2/users` exposes login slug `peter_froggatt` (display "Phillip Strang"). → disable REST user endpoint.
- **H-7** `xmlrpc.php` reachable (405) + `x-pingback` advertised. → block via `.htaccess`, disable pingbacks.

## Medium
- **M-1** Homepage two H1: theme `<h1>Home</h1>` + hero `<h1>Audio Gear, Tested and Explained</h1>`. → keep one descriptive H1.
- **M-2** `WebSite.name` empty in schema. → set in Yoast/WordPress.
- **M-3** Homepage primaryImage hotlinks Unsplash. → self-host branded image.
- **M-4/M-5/M-6** 19 scripts without async/defer; jQuery+Migrate sync in head; 16 blocking CSS, no critical-CSS/preconnect; 12/15 images without width/height (CLS); no LCP fetchpriority. → defer JS, critical CSS, image dimensions, preconnects.
- **M-7** IndexNow not enabled (`/indexnow` 404). → enable in Yoast.
- **M-8** `/author/peter_froggatt/` indexed (login-slug exposure + thin duplicate listing). → noindex author archive and/or change public slug.

## Low / Info
- **L-1 (Info)** `x-robots-tag: noindex` appears ONLY on robots.txt/XML/xmlrpc (text-plain/xml), never on HTML. **Confirmed harmless — not a site-wide noindex.**
- **L-2** Asset `?ver=` query strings expose plugin versions.
- **L-3** `-2026-2` commercial slug is awkward; clean up with C-1.
- **L-4** No `preconnect` hints for third-party origins.

## Scorecard
Crawlability 8/10 · Indexability 5/15 · Security 5/15 · URL structure 6/10 · Mobile 9/10 · CWV(lab) 6/15 · Structured data 6/10 · JS rendering 10/10 · IndexNow/crawl 2/5 → **57/100**.

## Top 5
1. Noindex 310 tag archives (1 toggle). 2. Consolidate ~20 `-2` duplicate posts. 3. Fix recording-production duplicate pair. 4. HSTS + security headers + PHP/REST/xmlrpc hardening. 5. Defer render-blocking JS/CSS + image dimensions.
