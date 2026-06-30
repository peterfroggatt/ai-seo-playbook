# Schema.org / Structured Data — audiotechexpert.com

**Score: 34/100.** Yoast emits structurally valid JSON-LD on every page (correct @context, @id linking, ISO-8601 dates), but it's broken at the foundation (empty `WebSite.name` sitewide), missing the publisher identity layer (no Organization), and has zero commerce schema (no Review/Product/ItemList/aggregateRating) across 285 affiliate posts.

> Note: the SXO agent's parser reported schema `@type` as "?" — that was a parser artifact. Direct inspection confirms valid JSON-LD types; the real problems are the gaps below.

## Current state
- **Homepage:** WebPage, ImageObject (external Unsplash), BreadcrumbList, WebSite. WebSite `name`="" and `description`="". No Organization, no Person.
- **Posts:** Article (good — headline/dates/wordCount/author/image), WebPage, ImageObject (site-hosted, has width/height/caption), BreadcrumbList, Person (name/desc/image/url/sameAs), WebSite(name=""). No Review/Product/ItemList/aggregateRating.
- **Category /headphones/:** WebPage (not CollectionPage), ImageObject (Unsplash), BreadcrumbList, WebSite(name=""). No ItemList.
- **/about-us/:** WebPage, BreadcrumbList, WebSite(name=""). No Person, no Organization (the logical place for both).
- **Astra Microdata Organization** in header has empty `itemprop=name`/`url` (Customizer Site Identity not set).

## Validation
**CRITICAL**
- C-1 `WebSite.name` = "" on all pages (sitelinks-searchbox/brand label). 5-min Yoast fix.
- C-2 `WebSite.description` = "" on all pages.
- C-3 No Organization JSON-LD anywhere (publisher entity for E-E-A-T + AI attribution).
- C-4 Astra Microdata Organization has empty name/url.
- C-5 No Review/Product/ItemList across 285 affiliate posts — largest revenue-linked gap. AAWP stores ASIN/price but emits no schema.

**WARNING**
- W-1 `Article.keywords` is one comma-joined string inside an array (should be array of strings).
- W-2 `shure-sm7b-vs-rode-podmic-usb` Article missing `dateModified`.
- W-3 `Person.sameAs` only lists the site's own homepage (no phillipstrang.com / social).
- W-4 No Person schema on /about-us/ despite the bio there.
- W-5 Homepage/category `primaryImageOfPage` = external Unsplash (not permanent/owned).
- W-6 No Organization on /about-us/.

**INFO**
- I-1 Posts have visible "Frequently asked questions" Q&A sections but no FAQPage schema. FAQ rich results were retired (May 2026) → no SERP benefit, but markup aids AI/GEO citation.
- I-2 Category pages use WebPage, not CollectionPage.

## High-leverage additions (priority)
1. **WebSite.name** (Yoast → Search Appearance/Settings → site name = "Audio Tech Expert"). Trivial, sitewide.
2. **Organization** (Yoast Knowledge Graph w/ logo) — example below.
3. **Enrich Person** (worksFor → Organization, knowsAbout, sameAs → phillipstrang.com, hosted photo).
4. **ItemList + Product + Offer** on roundup posts (auto-buildable from AAWP ASIN/price meta) — rich-result eligibility for "best X" queries.

### Organization (add to homepage @graph)
```json
{
  "@context":"https://schema.org","@type":"Organization",
  "@id":"https://audiotechexpert.com/#organization",
  "name":"Audio Tech Expert","url":"https://audiotechexpert.com/",
  "logo":{"@type":"ImageObject","@id":"https://audiotechexpert.com/#logo",
    "url":"https://audiotechexpert.com/wp-content/uploads/.../audiotechexpert-logo.png",
    "width":200,"height":60,"caption":"Audio Tech Expert"},
  "description":"Independent, jargon-free reviews and guides for headphones and microphones.",
  "publishingPrinciples":"https://audiotechexpert.com/how-we-choose/",
  "sameAs":["https://audiotechexpert.com"]
}
```

### Person (enriched — /about-us/ + Yoast author profile)
```json
{
  "@context":"https://schema.org","@type":"Person",
  "name":"Phillip Strang","url":"https://audiotechexpert.com/author/peter_froggatt/",
  "image":{"@type":"ImageObject","url":".../phillip-strang-author.jpg","width":400,"height":400},
  "jobTitle":"Founder & Editor","worksFor":{"@id":"https://audiotechexpert.com/#organization"},
  "knowsAbout":["Headphones","Microphones","Audio equipment","Noise-cancelling","Bluetooth codecs"],
  "sameAs":["https://phillipstrang.com","https://audiotechexpert.com/author/peter_froggatt/"]
}
```
Keep the `@id` identical to Yoast's existing Person node so Article author refs resolve.

### ItemList + Product + Offer (roundup posts)
ItemList → ListItem(position) → Product(name, brand, description, category, offers{Offer: affiliate-tagged Amazon url, priceCurrency, price, availability, seller}). Build programmatically from AAWP ASIN/price via `wpseo_schema_graph_pieces` filter to avoid manual upkeep.

## Implementation order
1 WebSite.name (trivial/High) → 2 Organization (trivial/High) → 3 Person sameAs (low/Med) → 4 Person+Org on /about-us/ (low/Med) → 5 ItemList+Product on roundups (med/High) → 6 FAQPage (low/Info) → 7 site-host ImageObject (low) → 8 keywords array (low).
