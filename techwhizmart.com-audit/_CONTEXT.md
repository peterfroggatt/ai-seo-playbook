# Audit Context — techwhizmart.com

## CRITICAL: Network/proxy setup (do this FIRST in every Bash session)
All outbound HTTPS in this sandbox MUST go through a loopback agent proxy. The repo's
url_safety DNS-pinning rejects the proxy's loopback IP, so scripts fail unless you load
a local shim. Before running ANY repo script (render_page.py, fetch_page.py, parse_html.py,
schema_*.py, content_*.py, pagespeed_check.py, etc.), export:

```
export PYTHONPATH="/tmp/claude-0/-home-user-ai-seo-playbook/6205bd64-2d00-542f-abd5-761a133da255/scratchpad:/home/user/ai-seo-playbook/scripts:$PYTHONPATH"
```

Then scripts work normally. `curl -sSL <url>` also works directly through the proxy without the shim.
Do NOT disable TLS, do NOT unset HTTPS_PROXY. If you get a 403 from the proxy, the host is policy-blocked — report it.

## Site profile
- Platform: WordPress + WooCommerce + Yoast SEO. Server: hcdn (Hostinger). Server-rendered (NOT an SPA).
- Business type: E-COMMERCE (consumer tech/gadgets/electronics retail).
- Home title: "TechWhizMart | Buy Cutting-Edge Gadgets & Innovative Tech Online"

## Page inventory (from sitemap_index.xml)
- product-sitemap.xml: 671 URLs — BUT 360 (54%) are `__trashed` slugs returning HTTP 200 (index bloat + ugly URLs)
- post-sitemap.xml: 147 posts (blog)
- page-sitemap.xml: 12 pages (includes cart, checkout, my-account, wishlist, compare-products, sample-page — utility pages should be noindex/excluded)
- product_cat-sitemap.xml: 34 ; category-sitemap.xml: 9 ; author-sitemap.xml: 2
- ~165 `pa_*` product-attribute sitemaps (pa_brand=252 URLs, pa_color=31, etc.) — WooCommerce layered-nav attribute archives flooding the index (index bloat / thin pages)
- sitemap_index lists child sitemaps as http:// (not https://) — protocol inconsistency

## Pre-fetched files (in techwhizmart.com-audit/)
- home.html (homepage), product.html (JBL Charge 4 product page), home-render.json

## On-page findings already confirmed
HOME: title OK (~56 chars); meta desc 230 chars (too long, truncates); canonical OK; **H1 MISSING (0 h1)**;
  schema: WebPage, BreadcrumbList, WebSite+SearchAction, Organization (good); OG present; Twitter only `card`.
PRODUCT (JBL Charge 4): H1 present; Product+Offer+UnitPriceSpecification schema present (good); canonical OK;
  **meta desc is generic boilerplate** ("Discover the latest tech and gadgets at TechWhizMart...") — same template across products = duplicate meta descriptions; 26 imgs, 8 empty alt; lazy-loading present.
robots.txt: duplicate `User-agent: *` blocks (Yoast block has empty Disallow); sitemap declared as http://.
Security headers MISSING on home: HSTS, X-Frame-Options, X-Content-Type-Options, CSP, Referrer-Policy, Permissions-Policy.
No Google API creds, no Moz/Bing creds, no DataForSEO MCP — use free/lab methods only; note field-data unavailable.

## Your job
Write findings to techwhizmart.com-audit/findings/<category>.md with: what works, issues (severity Critical/High/Medium/Low),
evidence (URLs, counts, snippets), and specific recommendations. Be evidence-based; verify with real fetches. Do not fabricate.
