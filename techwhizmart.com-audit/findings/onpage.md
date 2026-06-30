# On-Page SEO Findings — techwhizmart.com

**Category Score: 48 / 100**

On-page *mechanics* (titles, meta descriptions, canonicals, breadcrumbs) are mostly
correct — but heading semantics, URL hygiene, and internal-linking architecture
are broken, and on-page copy quality is poor (see content.md).

## What works
- **Title tags**: Descriptive and brand-suffixed. Home: `TechWhizMart | Buy Cutting-Edge Gadgets & Innovative Tech Online`. Products: `JBL Charge 4 - Waterproof Portable Bluetooth Speaker - Black - TechWhizMart`.
- **Meta descriptions**: Present and reasonable length on homepage and templated product/blog pages (Yoast-generated).
- **Canonicals**: Self-referencing, correct HTTPS, on every sampled template.
- **Breadcrumbs**: Present and marked up (BreadcrumbList schema) site-wide.
- **URL structure**: Clean `/product/`, `/product-category/`, `/blog-slug/` patterns (Technical scored URL structure 75/100).

## Findings

### [CRITICAL] Homepage has zero `<h1>`
The homepage renders **no `<h1>`** element (confirmed in 122k-char decompressed HTML). The Elementor/Rehub hero uses non-semantic containers. Search engines and AI extractors lose the single strongest on-page topical signal. **Fix:** add one H1 Heading widget with the primary headline (e.g. "Cutting-Edge Tech Gadgets & Electronics").

### [HIGH] 360 product URLs carry ugly, non-descriptive `__trashed` slugs
e.g. `/product/marcy-smith-cage-machine-...-sm-4008__trashed/`. These are live, buyable products (status-publish, in-stock) whose slugs were corrupted by a trash-then-restore / duplicate import. Ugly slugs hurt keyword relevance, CTR, and shareability, and signal low quality at scale. **Fix:** rename slugs to clean keyword slugs and 301 the old `__trashed` URLs; de-duplicate where a clean twin already exists.

### [HIGH] Internal-linking architecture is incoherent
The 147-post blog is a self-contained silo about drone travel photography with **no commercial links into the product catalog** and no topical relationship to it. There is no working pillar/cluster structure, and the homepage product modules are empty ("No products for this criteria"), so the home page passes little internal equity to PLPs/PDPs. **Fix:** rebuild internal linking so editorial content links to relevant category/product pages; fix homepage product modules; add contextual related-product links.

### [MEDIUM] About/contact/trust pages are unlinked or missing from navigation
The only "About" content is an unlisted 219-word boilerplate page (`/techwhizmart-buy-cutting-edge-gadgets-innovative-tech-online/`) not linked in nav/footer. No visible contact method/address. **Fix:** publish a real About + Contact page, link from header/footer.

### [MEDIUM] Default WordPress `sample-page/` still published & in sitemap
`/sample-page/` returns 200 and is in `page-sitemap.xml`. **Fix:** delete it.

### [LOW] No `<meta name="robots">` directives anywhere
Pages rely on default index,follow. Acceptable, but thin/utility pages (cart, my-account, wishlist, compare) should be `noindex` (some are in the page sitemap). **Fix:** noindex utility/account pages.
