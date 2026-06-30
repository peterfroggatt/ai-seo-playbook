# Shared Audit Context — techwhizmart.com

## CRITICAL: Network / proxy workaround
Outbound HTTPS normally goes through a local proxy at 127.0.0.1 that the repo's
`url_safety.py` SSRF guard refuses (it blocks loopback IPs), so the helper
scripts' `requests` fetches FAIL with a proxy error. The target is a PUBLIC IP
and direct egress works. **For EVERY fetch (curl, python scripts, render_page.py),
export `NO_PROXY` first:**

```bash
export NO_PROXY='techwhizmart.com' no_proxy='techwhizmart.com'
```

Then curl and the repo scripts (render_page.py, fetch_page.py, parse_html.py,
content_quality.py, schema_*.py, etc.) all work normally. Scripts are in the
repo root `scripts/` dir (NOT under the skill dir). Run from /home/user/ai-seo-playbook.

## Site profile
- Platform: WordPress 7.0 + WooCommerce 10.8.1 + Elementor + Yoast SEO + Google Site Kit
- Host: Hostinger (hcdn CDN), PHP 8.2.30
- Business type: **E-commerce** — consumer tech/gadget store, dropshipping-style
- Catalog has drifted OFF-NICHE: includes home gym machines, LEGO sets, liquor-bar
  tables, indoor cycling bikes alongside drones/headphones/consoles.
- Single language (no hreflang). Not a local business (no NAP/storefront).

## Inventory (from sitemap_index.xml)
- post-sitemap: 147 posts — almost ALL about "drone travel photography" (off-topic
  for a gadget store; long AI-style content, e.g. one post ~16k words of markup)
- page-sitemap: 12 pages — includes default WP `sample-page/`, plus wishlist,
  checkout, my-account, compare-products, cart, featured-items, our-blog, blog,
  blog-posts, and a duplicate-ish landing page
- product-sitemap: 671 product URLs — **360 are `__trashed` URLs that still return
  HTTP 200**, are self-canonicalizing, and carry full Product+Offer schema =
  index bloat / near-duplicate thin pages. ~311 "live" products.
- category-sitemap: 9 ; product_cat-sitemap: 34 ; post_tag-sitemap: 164 (tag bloat)
- Many `pa_*` product-attribute sitemaps (faceted attribute archives indexable)

## Confirmed technical facts (homepage unless noted)
- robots.txt: TWO stacked `User-agent: *` blocks (WooCommerce defaults + a Yoast
  block with empty `Disallow:`); Sitemap line uses **http://** not https://
- sitemap_index.xml `<loc>` entries use **http://techwhizmart.com** (protocol
  mismatch vs the https canonical site)
- Security headers MISSING: no HSTS, no X-Frame-Options, no X-Content-Type-Options,
  no Referrer-Policy, no Permissions-Policy. Only `Content-Security-Policy: upgrade-insecure-requests`.
- Homepage: title OK, meta description OK, canonical OK (https), viewport OK,
  **H1 = 0 (no H1 on homepage)**, 1 JSON-LD block (WebSite/Organization/Breadcrumb/
  WebPage/SearchAction via Yoast), 7 OG tags. Homepage product widgets render
  "No products for this criteria." (broken/empty product display).
- `/llms.txt` = 404 (not present)
- Product pages (live AND trashed): have Product + Offer + UnitPriceSpecification +
  BreadcrumbList schema, H1 present, ~1150 rendered words, self-canonical.
- No `<meta name="robots">` override on sampled pages (default index,follow).

## UPDATE — __trashed products are LIVE & buyable
Sampled __trashed product = `status-publish instock purchasable product-type-simple`
with a price and add-to-cart. So the 360 `__trashed` URLs are NOT WordPress-trashed;
they are live, sellable products carrying an ugly `__trashed` slug (botched
trash-then-restore or duplicate import). Fix = repair slugs / merge duplicates +
301 redirects, NOT delete. Neither sampled product has AggregateRating (no reviews).

## Output
Write findings to: `techwhizmart.com-audit/findings/<category>.md`
Keep evidence-backed and specific. Return a concise structured summary.
