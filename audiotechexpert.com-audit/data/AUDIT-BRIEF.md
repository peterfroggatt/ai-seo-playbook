# Audit Brief — audiotechexpert.com

## Target
- Site: https://audiotechexpert.com
- Platform: WordPress (Yoast SEO plugin), PHP 8.3, Hostinger (hcdn CDN)
- Plugins detected: Yoast SEO, AAWP (Amazon Affiliate for WordPress — `aawp-country` cookie)
- Business type: **Publisher / Affiliate review site** (headphones & microphones reviews + guides)
- Monetization: Amazon affiliate (AAWP)

## Site size (from sitemap_index.xml)
- Posts: 285
- Pages: 20
- Categories: 58
- Tags: 310  (MORE tags than posts — tag-archive index-bloat risk)
- Authors: 1 (single-author site)

## robots.txt
- `User-agent: *  Disallow:` (everything allowed)
- Sitemap: https://audiotechexpert.com/sitemap_index.xml

## Homepage facts already gathered (do not re-derive)
- Title: "Audio Tech Expert — Honest Headphone & Mic Reviews" (~51 chars, good)
- Meta description: present, good length
- Canonical: self-referential (good)
- **H1: MISSING (0 h1 tags). No h2/h3 detected either.**
- Word count: ~479
- Schema graph (Yoast): WebPage, ImageObject, BreadcrumbList, WebSite
  - WebSite `name` is EMPTY string
  - primaryImageOfPage = hotlinked Unsplash stock photo (images.unsplash.com)
  - No Organization or Person/author schema on homepage
- Saved full HTML: audiotechexpert.com-audit/homepage.html (163KB)
- URL list: audiotechexpert.com-audit/data/all-urls.txt (363 post+page+category URLs)

## Notable page URLs
- /headphone-guides-old/  (likely orphaned legacy page — check noindex/redirect)
- /guides/recording-production/ AND /recording-production/  (possible duplicate paths)
- /about-us/, /how-we-choose/, /contact/, /free-cheatsheet/  (trust/lead pages)

## Environment notes
- Outbound HTTPS works via agent proxy (url_safety.py patched to allow the loopback proxy host).
- bs4/lxml/requests installed globally. No Google API creds. No Moz/Bing creds (Common Crawl tier only).
- Working dir: /home/user/ai-seo-playbook
- Write findings to: audiotechexpert.com-audit/findings/<category>.md
