# Visual & Mobile-Rendering Audit — techwhizmart.com
**Date:** 2026-06-30
**Pages analysed:** Homepage (`/`), Product page (`/product/jbl-charge-4-waterproof-portable-bluetooth-speaker-black/`)
**Screenshot status:** LIVE SCREENSHOTS NOT CAPTURED. Chromium cannot route through the sandbox loopback proxy (the `url_safety` DNS-pin blocks `127.0.0.1`). All findings below are derived from static analysis of the pre-fetched `home.html` and `product.html` files plus inline CSS/JS present in those documents.

---

## What Works

- **Viewport meta tag present and correct** on both pages: `<meta name="viewport" content="width=device-width, initial-scale=1.0" />` — browser will not default to 980 px desktop layout on mobile.
- **Responsive image srcsets** are present on the hero banner and most product images: the hero image ships 10 size variants (100 w – 2048 w) and the `sizes` attribute is set correctly (`(max-width: 840px) 100vw, 840px`).
- **Explicit width/height on images** — all 6 home-page images and 25 of 26 product images carry `width` and `height` attributes, which prevents layout shift while images load (good CLS posture in principle).
- **Mobile hamburger menu present** — the Rehub `dl-menuwrapper` / `dl-trigger` SVG hamburger is in the `responsive_nav_wrap` block, and a slide-out panel (`slidingpanel.css`) is loaded. The bottom toolbar (`#rhNavToolWrap`, fixed, 55 px height) provides a persistent mobile nav rail at the bottom of the screen.
- **Product H1 present** on the product page: `<h1 class="fontnormal font150"> JBL Charge 4 – Waterproof Portable Bluetooth Speaker – Black</h1>`.
- **Add-to-cart button present** on the product page (`<button type="submit" name="add-to-cart" … class="single_add_to_cart_button button alt">Add to cart</button>`); a `#float-panel-woo` sticky mobile panel is also present, which should keep the CTA accessible on scroll.
- **Price visible** ($114.95) in the product page markup above the add-to-cart button.
- **`decoding="async"`** set on most images, reducing main-thread decode pressure.
- **Speculation rules prefetch** block present in `home.html` — improves perceived navigation speed for internal links.
- **Logo has dimensions on mobile variant** (`width="160" height="50"` on `.logo_image_mobile`), preventing a flash of unsized logo.

---

## Issues

### [CRITICAL] Hero LCP image lazy-loaded despite `fetchpriority="high"` — direct LCP conflict

**Evidence:**
The above-the-fold hero image carries both `fetchpriority="high"` and `class="lazyload"` with the real URL only in `data-src`, while `src` points to a 1×1 blank GIF (`rehub-theme/images/default/blank.gif`). This pattern appears **twice** on the homepage (same image duplicated in sections 1 and 6).

```html
<img fetchpriority="high" decoding="async" width="840" height="402"
     src="https://techwhizmart.com/wp-content/themes/rehub-theme/images/default/blank.gif"
     data-src="https://techwhizmart.com/.../f-cinematic-drone-1-1024x490.jpg"
     class="lazyload attachment-large size-large wp-image-17663"
     alt="cinematic drone 2" … />
```

The browser preloads the blank GIF at high priority; the real hero image only loads after JavaScript initialises the lazysizes library. This means LCP is blocked until JS runs, directly harming Core Web Vitals (LCP score) on both desktop and mobile.

**Fix:** For the first above-the-fold image, replace the lazysizes pattern with a direct `src`/`srcset` and keep `fetchpriority="high"`. Only images below the fold should use `lazyload`. In WordPress/Elementor, disable Elementor's lazy-load setting for the first section, or add a `<link rel="preload" as="image">` for the hero URL in `<head>`.

---

### [CRITICAL] Homepage has zero H1 elements

**Evidence:** `re.findall(r'<h1[^>]*>.*?</h1>', html)` returns an empty list for `home.html`. The page title is the site name rendered as an Elementor heading widget (rendered as `<span class="elementor-heading-title">` or `<h2>`), not as an H1.

**Fix:** Assign H1 to the primary page heading. The welcome blurb ("Welcome to TechWhizMart, your one-stop shop…") or a keyword-rich hero headline should be wrapped in `<h1>`. Set this in the Elementor heading widget's HTML tag setting.

---

### [HIGH] Desktop logo missing width/height attributes — layout-shift risk

**Evidence:** The `.logo_image` anchor in the desktop header section contains:
```html
<img src="…Screenshot_2025-01-29_at_5.24.07_AM-removebg-preview.png"
     alt="TechWhizMart" height="" width="" />
```
Both attributes are present but empty. The mobile logo correctly specifies `width="160" height="50"`. On desktop, an unsized logo causes CLS as the header reflows once the PNG loads (1088×229 px per schema data). The header also has `logo_section_wrap hideontablet` CSS class, so this affects the desktop/laptop view only.

**Fix:** Set explicit `width` and `height` on the desktop logo `<img>` tag to match the natural image dimensions (or the rendered display size). In the Rehub theme logo settings, there is a "Logo width" field — populate it.

---

### [HIGH] 8 product gallery thumbnails have empty alt text

**Evidence:**
```
IMAGES WITH EMPTY alt: 8
<img loading="lazy" … class="attachment-thumbnail size-thumbnail" alt="" …>
```
All 8 are WooCommerce product gallery thumbnails (`_SS150_.jpg` size variants from Amazon). Empty alt on gallery images means screen-reader users receive no context for variant/angle images, and the images are invisible to Google Image search.

**Fix:** In WooCommerce product settings, add descriptive alt text to each gallery image (e.g., "JBL Charge 4 side view", "JBL Charge 4 charging port"). If images are imported programmatically, update the import script to set `post_excerpt` (WooCommerce uses this as alt on gallery images).

---

### [HIGH] Product images use invalid double-URL pattern — likely broken on non-cached requests

**Evidence:** All 30 image `src` values on the product page follow this pattern:
```
src="https://techwhizmart.com/wp-content/uploads/https://m.media-amazon.com/images/I/411XhhP64tL.jpg"
```
The URL `wp-content/uploads/https://…` is not a valid filesystem path and will return a 404 unless some rewrite rule or caching layer intercepts it. This is an Amazon affiliate/import plugin artefact where the plugin stored the external URL as the local attachment URL. If these 404 on a cold cache, every product page renders with broken images — severe for conversion and visual trust.

**Fix:** Run the WooCommerce import plugin's "Re-download / sideload images" function so images are stored locally under a proper path (`/wp-content/uploads/2025/…`). Remove the plugin's external-URL fallback if it is the cause.

---

### [HIGH] 32 CSS stylesheets loaded on homepage — excessive render-blocking resource count

**Evidence:** `len(stylesheets)` = 32 separate `<link rel="stylesheet">` calls in `home.html`, including per-widget Elementor files (`widget-image.min.css`, `widget-divider.min.css`, `widget-heading.min.css`), plugin stylesheets, and theme stylesheets, all with `media='all'`. On a 375 px mobile connection these are sequential render-blocking round-trips.

**Fix:** Enable Elementor's "Improved Asset Loading" experiment (Elementor → Settings → Experiments) which loads only widgets used on each page. Additionally, combine/minify remaining CSS via a plugin such as WP Rocket or LiteSpeed Cache. Target fewer than 10 render-blocking CSS requests.

---

### [MEDIUM] Google Fonts loaded via `<link>` — render-blocking on first visit

**Evidence:**
```html
<link rel='stylesheet' id='Poppins-css'
      href='//fonts.googleapis.com/css?family=Poppins:700,normal&subset=latin&ver=7.0'
      media='all' />
```
The Poppins font request from `fonts.googleapis.com` blocks rendering until it resolves. There is a `dns-prefetch` for `fonts.googleapis.com` but no `preconnect` for `fonts.gstatic.com` (where the actual WOFF2 files are served).

**Fix:** Add `<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>` in `<head>`. Better: use Elementor's local font feature (already partially in use — `elementor-gf-local-roboto-css` is self-hosted) to self-host Poppins as well, eliminating the third-party DNS lookup entirely.

---

### [MEDIUM] Elementor font CSS files loaded via HTTP (not HTTPS)

**Evidence:** Three self-hosted Google Font files load over plain HTTP:
```html
<link … href="http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/roboto.css" …>
<link … href="http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/robotoslab.css" …>
<link … href="http://techwhizmart.com/wp-content/uploads/elementor/google-fonts/css/stylescript.css" …>
```
Modern browsers will block mixed-content requests on HTTPS pages, causing these fonts to fail silently (fallback fonts render instead, causing a FOUT/layout shift).

**Fix:** Regenerate the Elementor font cache: Elementor → Tools → Regenerate CSS & Data. Also ensure the WordPress Site URL and Home URL are both set to `https://` in Settings → General.

---

### [MEDIUM] Link colour contrast risk — orange on white (#ffa500 / white background)

**Evidence:** The wp-custom-css block overrides the global link colour:
```css
a { color: #ffa500; }
a:hover { color: #ff0000; text-decoration: underline; }
```
Orange (`#ffa500`) on a white background yields a contrast ratio of approximately 3.0:1 — below the WCAG AA minimum of 4.5:1 for normal text. This applies to all body text links across every page.

**Fix:** Darken the link colour to at least `#b36a00` (≈4.6:1 on white) or change to the site's existing blue accent `#0181cb` (≈4.7:1). Update the wp-custom-css block in Appearance → Customise → Additional CSS.

---

### [MEDIUM] Homepage product carousels render as empty containers in static HTML

**Evidence:** Both "Today's Popular Picks" and the second product carousel section contain empty `<div class="re_carousel grid_woo products carouselpost" data-laizy="1">` containers with no child elements. Products are loaded client-side via AJAX (`data-laizy="1"`).

**Impact:** If JavaScript is slow or fails (slow mobile connection, JS error, bot crawl), users see blank sections with section headings but no products. Above-the-fold purchase intent signals (product images, prices, CTAs) are absent without JS. Google can render JS but there is a crawl delay risk.

**Fix:** Server-side render the initial 6 products (PHP) and use JS to progressively enhance into a carousel. This ensures content is always visible and immediately crawlable.

---

### [MEDIUM] Footer image (payment/trust badges) has empty alt text

**Evidence:**
```html
<img … class="aligncenter wp-image-15010 size-full"
     src="…/Screenshot-2025-01-29-at-5.24.45 AM.png"
     alt="" width="1558" height="344" …>
```
A 1558×344 px trust-badge image in the footer (visible on all pages) has no alt text. This is decorative in purpose but the image appears to contain payment logos and trust signals — a screen-reader user receives none of this context.

**Fix:** Add descriptive alt text: `alt="Accepted payment methods: Visa, Mastercard, PayPal, American Express"` (or similar depending on actual content). Also, filenames containing spaces and Unicode non-breaking spaces (` `) are technically fragile — rename to a clean slug.

---

### [LOW] Homepage hero image duplicated — same image used in sections 1 and 6

**Evidence:** Identical `src`/`data-src` for `f-cinematic-drone-1-1024x490.jpg` appears in two separate Elementor sections (element IDs `c6ce7fd` and `ec26f0f`), meaning the same drone banner image is shown twice on the homepage with no differentiation in alt text (`alt="cinematic drone 2"` in both cases).

**Fix:** Replace the second instance with a different product category image or promotional banner to improve visual variety and content depth.

---

### [LOW] "Deal of the Day" widget renders "No products for this criteria" — empty UI element

**Evidence:**
```html
<div class="deal_daywoo woocommerce position-relative …">
  …No products for this criteria.
</div>
```
An entire Elementor section is allocated to the "Deal of the Day" widget but it has no matching product. Visitors on mobile see a bordered, padded box with an error string.

**Fix:** Either configure a product to meet the widget's criteria (e.g. assign it to the "Deal of the Day" category), or remove the section until a deal is ready to promote.

---

### [LOW] Navigation menu extremely sparse — only 3 items (Home, Blog, Drones)

**Evidence:**
```html
<ul id="menu-main-menu" class="menu">
  <li>Home</li>
  <li>Blog</li>
  <li>Drones</li>
</ul>
```
The site has 10 product categories (Audio, Computers, Consoles and VR, Electronics, etc.) visible in the search dropdown but none appear in the main navigation. Mobile users have no clear pathway to browse by category without using search.

**Fix:** Add top-level category links to the main menu (Appearance → Menus) with a mega-menu or dropdown for sub-categories, leveraging Rehub's built-in vertical-menu support.

---

## Above-the-Fold Assessment (Homepage, Inferred)

| Element | Status |
|---|---|
| H1 heading | ABSENT — critical SEO and UX gap |
| Hero image | Present in markup, but deferred by lazyload JS — visible after JS init |
| Primary CTA (shop/browse) | ABSENT above the fold — no "Shop Now" or category link |
| Product listings | ABSENT — AJAX-loaded, blank on first render |
| Navigation | Present (hamburger on mobile, top nav on desktop, 3 items only) |
| Search bar | Present (desktop header, hidden on mobile inline) |
| Trust signals | Below fold (footer only) |

---

## Above-the-Fold Assessment (Product Page, Inferred)

| Element | Status |
|---|---|
| H1 (product name) | Present |
| Product image | Present with fetchpriority="high" (500×500 px, correct) |
| Price ($114.95) | Present |
| Add-to-cart button | Present; position at ~21% through page HTML |
| Sticky mobile CTA | `#float-panel-woo` panel present |
| Breadcrumb (visual) | Present in schema, likely rendered above product title |

---

## Mobile Responsiveness Summary

| Check | Result |
|---|---|
| Viewport meta | PASS |
| Hamburger menu | PASS (dl-trigger SVG + slide panel) |
| Bottom nav toolbar (55 px) | PASS (Rehub mobile rail) |
| Sticky cart on product | PASS (#float-panel-woo) |
| Image dimensions set (prevents reflow) | PASS (26/26 product, 6/6 home — except desktop logo empty) |
| Responsive srcsets | PASS |
| Base font size | PASS (body font-size 16 px per WP global styles) |
| No horizontal scroll risk | LIKELY PASS (max-width containers, no fixed-width overflows found in inline CSS) |
| Google Fonts mixed content (HTTP) | FAIL — 3 font CSS files served over HTTP |
| Link colour contrast | FAIL — #ffa500 on white ~3.0:1 (WCAG AA requires 4.5:1) |
| Hero image preload | FAIL — lazyload JS conflict defeats fetchpriority |
| Product images broken URL pattern | FAIL — double-https path pattern |
