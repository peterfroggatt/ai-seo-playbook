# Fix: HTML uncacheable at CDN (audiotechexpert.com)

## The problem (confirmed live)
Every page response sets a cookie:
```
set-cookie: aawp-country=US; ... path=/; secure
x-hcdn-cache-status: DYNAMIC      <- CDN is NOT caching the HTML
x-hcdn-upstream-rt: 3.043         <- 3.0s PHP render on this request (uncached)
```
Measured TTFB swings from ~0.4s to ~3.0s because **every visitor triggers a full WordPress/PHP render** — nothing is served from the edge cache.

## Why
A CDN (and any full-page cache) will **never cache a response that sets a cookie** — it can't safely hand one visitor's cookie to the next. AAWP's **geotargeting** feature sets `aawp-country` on every response, so it makes 100% of your HTML uncacheable. Toggling the Hostinger CDN on/off does nothing while that cookie is present — **removing the cookie is the linchpin.**

---

## The fix — decide first: do you actually use international monetisation?

Your Amazon tag is `audiotechexpert-20` (a **US** Associates account). AAWP geotargeting sends, say, a UK visitor to amazon.co.uk — but you only earn on that if you've set up **Amazon OneLink / international associate tags**. If you haven't, geotargeting is costing you full-page caching while earning you **nothing** on international clicks. For most US-tag affiliate sites, **Option A is the right call.**

### Option A — Disable AAWP geotargeting (recommended if US-focused)
Removes the cookie → CDN can cache HTML.
1. WP Admin → **AAWP → Settings**.
2. Find the **Geotargeting** section (AAWP Pro 5.x; may be labelled "Geotargeting" or under "Internationalisation").
3. **Turn it off** (single store / disabled).
4. Save.
- **Effect:** all Amazon links use your default store (amazon.com, `-20` tag). The `aawp-country` cookie stops being set.
- **Trade-off:** international visitors get .com links instead of their local Amazon. If you don't have international tags, you weren't earning on those anyway.

### Option B — Keep geotargeting but make it cache-friendly (if you DO monetise internationally)
- In **AAWP → Settings → Geotargeting**, check for a **JavaScript / client-side** mode (handles country in the browser, no server cookie), or
- Switch to **Amazon OneLink** for international redirection (Amazon-side, cookieless to your CDN) and disable AAWP's server-side geotargeting.
This keeps localisation while letting the page itself stay cacheable.

---

## Then: turn on / refresh Hostinger caching
1. **hPanel → Websites → audiotechexpert.com → Dashboard.**
2. **Performance / Speed →** ensure **CDN** is enabled.
3. Hostinger runs **LiteSpeed** — if the **LiteSpeed Cache** plugin isn't active, install & activate it (WP Admin → Plugins → Add New → "LiteSpeed Cache"). Its defaults give you full-page caching at the server level. (Skip if you prefer to rely only on Hostinger's edge CDN.)
4. **Purge everything** after the AAWP change: hPanel **Cache Manager → Purge**, and LiteSpeed Cache → **Purge All**.

---

## Verify it worked
Load any page **twice** (first request warms the cache), then check:
```
curl -I https://audiotechexpert.com/
```
You want to see:
- **`x-hcdn-cache-status: HIT`** (was DYNAMIC)
- **no** `set-cookie: aawp-country=...` line
- `x-hcdn-upstream-rt` gone or tiny on cached hits

Expected result: TTFB drops from ~1–3s to tens of milliseconds on cache HITs → directly improves LCP, which is currently your worst Core Web Vital.

---

## Notes / caveats
- Exact menu labels vary slightly by AAWP and Hostinger version — I'm describing *where* to look; adjust to what you see. If you can't find the AAWP geotargeting toggle, tell me and I'll pin down the exact AAWP Pro 5.0.8 path.
- This one can't be done over the API — it's AAWP settings + hPanel. Once you've made the change, tell me and **I'll re-check the headers live** to confirm `HIT` and measure the TTFB improvement.
- Pair this with image WebP/self-hosting (the other half of the Performance score) for the full LCP win.
