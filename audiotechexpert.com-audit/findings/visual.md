# Visual & Mobile — audiotechexpert.com

**Score: 58/100.** Assessed from the saved rendered DOM (homepage.html) + on-page sweep. **Screenshots could not be captured** — Chromium returns `ERR_CONNECTION_CLOSED` through this environment's egress proxy (Playwright/Chromium can't use the loopback HTTPS proxy here). Findings below are DOM-derived; a manual browser check is recommended to confirm the popup behaviour.

## What works
- **Viewport meta correct**: `width=device-width, initial-scale=1` → mobile-responsive baseline (Astra theme is responsive).
- **Clean, logical primary nav**: Headphones · Microphones · Guides (Buying Advice, Recording & Production) · About Us. Consistent across header/mobile/footer.
- **Logo has explicit dimensions** (247×82) → no nav-bar layout shift.
- Hero headline present: "Audio Gear, Tested and Explained".

## Findings

### HIGH — Above-the-fold is stock imagery + prose, no product/value signal
Homepage hero uses a **hotlinked Unsplash stock photo**; the first meaningful content is editorial prose. For an affiliate site, the first screen shows no product picks, no "top pick" signal, and (per SXO) roundup pages bury the first product box ~300 words down. On mobile (390px) the user sees only the intro before scrolling. **Fix:** add a "Top Picks" visual block (thumbnail + price + CTA) above the fold on the homepage and roundups; replace the stock hero with an original branded image.

### HIGH — CLS risk from images without dimensions
12 of 15 homepage `<img>` (all Unsplash) lack `width`/`height` → visible reflow as images load, most pronounced on mobile. **Fix:** explicit dimensions on every image (see performance.md).

### MEDIUM — Duplicate/"Home" H1 and a "Tested" claim that conflicts with methodology
Two H1s: a wasted `<h1>Home</h1>` (theme entry-title) plus the hero `<h1>Audio Gear, Tested and Explained</h1>`. Beyond the heading-hierarchy issue, the visible hero word **"Tested"** contradicts /how-we-choose/ ("not a testing laboratory with acoustic measurement rigs") — a trust inconsistency a discerning visitor will notice. **Fix:** single descriptive H1; reword hero to match the actual (research-based) methodology, e.g. "Audio Gear, Researched and Explained".

### MEDIUM — Newsletter/lead-magnet popup may be an intrusive mobile interstitial
DOM shows popup/lightbox machinery: Elementor popup classes, `hostinger-reach/subscription-view.js`, Mailchimp (`mc4wp`), and the `/free-cheatsheet/` lead magnet. If this fires as a full-screen overlay on mobile at load, it risks Google's intrusive-interstitial treatment and hurts UX. **Fix:** verify in a real mobile browser; ensure the popup is delayed/scroll-triggered and easily dismissible, not an immediate full-screen overlay.

### LOW — Tap targets / legibility not directly measurable without render
Astra defaults are generally mobile-safe, but the Elementor menu and AAWP product boxes should be checked manually for 44px tap targets and contrast. **Fix:** manual mobile QA pass.

## Limitation
Screenshots unavailable in this environment (Chromium cannot egress via the agent proxy). Re-run `scripts/capture_screenshot.py` locally, or use PageSpeed Insights "view treated screenshot" / Google Mobile-Friendly test for visual confirmation of above-the-fold and mobile layout.
