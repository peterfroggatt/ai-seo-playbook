# Image SEO & Optimization Findings — techwhizmart.com

**Category Score: 22 / 100**

Dominated by a catastrophic defect: most product images are broken (404).

## Findings

### [CRITICAL] Product images are malformed and return 404
Product `<img>` sources are stored as concatenated paths that prepend the WordPress
uploads directory to a full external Amazon URL:
`https://techwhizmart.com/wp-content/uploads/https://m.media-amazon.com/images/I/411XhhP64tL.jpg`
This **301-redirects** to a collapsed-slash variant and then **404s** (verified;
returns a 90KB HTML 404 page, not an image). The intended source was the Amazon
image `https://m.media-amazon.com/images/I/411XhhP64tL.jpg`. Impact: products show
broken images to shoppers, are disqualified from Google image rich results and
free merchant listings, and Product schema `image` is unusable. **Fix:** re-import
images locally (`wp media import <amazon-url>`) and re-attach to each product, or
correct the stored URL. This is the highest-impact fix for catalog UX and image SEO.

### [HIGH] Homepage primary/OG image is a blank 1×1 GIF
The schema `primaryImageOfPage` / OG image resolves to a blank 1×1 placeholder GIF
(JS lazy-load pattern not hydrated for crawlers). Social/AI previews and `ImageObject`
schema carry no real image. **Fix:** set a real, static OG image (1200×630) and a
real primary image; preload the LCP image instead of a GIF placeholder.

### [HIGH] Logo is a 104 KB PNG with no modern format
Homepage LCP candidate logo = 104,549-byte PNG, no WebP/SVG/AVIF variant. **Fix:**
export logo as optimized SVG or WebP (<15 KB), add `fetchpriority="high"` + preload.

### [HIGH] Lazy-loading largely absent on content images
On the sampled long blog post, only 7 of 43 images carry `loading="lazy"` — 36
load eagerly including far-below-the-fold images. One AVIF image is referenced 8
times (template copy-paste), triggering 8 identical requests. At least one image
lacks `width`/`height` (CLS contributor). **Fix:** enable site-wide lazy-load,
deduplicate, and add intrinsic dimensions to all images.

### [MEDIUM] LCP placeholder anti-pattern
Hero images set `fetchpriority="high"` on a 1×1 blank GIF placeholder while the
real image sits in `data-src` and loads only after JS. This guarantees delayed LCP.
**Fix:** serve the real LCP image directly with `fetchpriority="high"`, no JS gate.

### [LOW] Alt text
Homepage decorative images carry `alt` attributes; product/gallery alt text appears
auto-generated from titles. Once images are re-imported, ensure descriptive,
keyword-relevant alt text per product. Re-audit after the 404 fix.

## What works
- WebP/AVIF *is* used for some blog/theme imagery (format capability exists).
- Self-hosted fonts avoid third-party image/CDN latency (see performance.md).
