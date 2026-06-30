# Performance & Images — phillipstrang.com

## Performance (Core Web Vitals)

**Field data unavailable:** no Google API key configured, so no CrUX field CWV (LCP/INP/CLS) and no PageSpeed lab run. Live headless rendering was also blocked in the audit sandbox. The following is inferred from HTML/headers and is **low confidence**.

### Observations
- **Cloudflare CDN** fronts the origin; a WordPress caching/optimization plugin is active (`wpo` / WP-Optimize artifacts in robots + headers). Both are positive for TTFB and asset delivery.
- Homepage payload: ~87 KB HTML, **14 external scripts, 4 stylesheets, 11 inline `<style>` blocks**. Script count is moderate; the inline-style proliferation suggests page-builder (Elementor/WPBakery-style) output, which commonly inflates CSS/DOM.
- **Image-heavy templates:** "books in order" and series pages load 19–31 book-cover images. Without explicit `width`/`height` and `loading=lazy` on every cover, these risk **CLS** and slow **LCP** on mobile.

### Recommendations (verify with real data first)
1. Configure a Google PageSpeed/CrUX API key and re-run `scripts/pagespeed_check.py` for real LCP/INP/CLS.
2. Ensure all cover images are served as WebP/AVIF with explicit dimensions and lazy-loading below the fold.
3. Audit the page-builder CSS/JS for render-blocking resources; defer non-critical JS.

## Images

### What works
- **Good alt-text coverage:** on `/ian-rankins-rebus-books-in-order/`, 29 of 31 images have non-empty `alt` (~94%).
- `og:image` present with declared dimensions.

### Findings
- **MEDIUM — Confirm modern formats & sizing.** Cover-heavy pages need WebP/AVIF + responsive `srcset` + explicit `width`/`height`. Could not measure file weights live; `scripts/parse_html.py` shows 12–31 images per template. Run an image-weight pass with the site's media library or `scripts/fetch_page.py` per asset.
- **LOW — A few missing alts.** 2 images on the Rebus page lack alt text; sweep templates so every `<img>` (especially cover art) has descriptive alt (book title + "book cover").
- **LOW — Lazy-loading.** Verify `loading="lazy"` on below-the-fold covers to protect LCP/INP on the long list pages.
