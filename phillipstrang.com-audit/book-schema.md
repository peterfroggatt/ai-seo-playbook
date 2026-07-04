# Ready-to-paste `Book` schema — phillipstrang.com

Add one `Book` JSON-LD block per book page, inside `<head>` (or anywhere in the
body). It links into the site's **existing** Yoast entity graph via `@id`
(`#organization`, `#website`) instead of duplicating it, so it won't conflict
with the Yoast output already on the page.

Best inserted via the page's SEO/custom-HTML field, or with a small WordPress
function that injects it on the `cook`/`tremayne`/etc. book templates.

> **Recommended: use the snippet, not per-page pastes.**
> `book-schema-snippet.php` in this folder generates this exact Book block on
> **every** book page automatically, pulling the title, cover, published date,
> series, and geni.us buy link straight from each post. Install it once (Code
> Snippets plugin) and all ~144 book pages are covered — no manual entry, and
> new books get schema the moment they're published. The templates below are
> for reference or one-off manual insertion.

---

## 1. Concrete example — *Murder House* (real data, paste as-is)

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Book",
  "@id": "https://phillipstrang.com/dci-isaac-cook-series/murder-house-phillip-strang/#book",
  "name": "Murder House",
  "url": "https://phillipstrang.com/dci-isaac-cook-series/murder-house-phillip-strang/",
  "image": "https://phillipstrang.com/wp-content/uploads/2017/03/Flatten-3D-Book-MURDER-HOUSE-v2.jpg",
  "description": "A body hidden in a fireplace for thirty years. Elderly suspects guarding family secrets. DCI Isaac Cook must solve a decades-old murder before the truth is lost forever.",
  "inLanguage": "en-US",
  "genre": ["Crime fiction", "Mystery", "Thriller"],
  "bookFormat": "https://schema.org/EBook",
  "datePublished": "2017-03-08",
  "author": {
    "@type": "Person",
    "name": "Phillip Strang",
    "url": "https://phillipstrang.com/about/"
  },
  "publisher": { "@id": "https://phillipstrang.com/#organization" },
  "isPartOf": {
    "@type": "BookSeries",
    "name": "DCI Isaac Cook",
    "url": "https://phillipstrang.com/dci-isaac-cook-series/"
  },
  "potentialAction": {
    "@type": "ReadAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "https://geni.us/murderhouse",
      "actionPlatform": [
        "https://schema.org/DesktopWebPlatform",
        "https://schema.org/MobileWebPlatform"
      ]
    },
    "expectsAcceptanceOf": {
      "@type": "Offer",
      "category": "purchase",
      "availability": "https://schema.org/InStock"
    }
  }
}
</script>
```

Notes on this example:
- `datePublished` `2017-03-08` is taken from the page's own published date — confirm it matches the book's actual release and adjust if needed.
- The `ReadAction` → `geni.us/murderhouse` is the book's existing universal buy link, so **no price or ISBN is invented**. This is Google's Book-Actions pattern and validates without a price.
- `bookFormat` is set to `EBook`; if a print edition exists, see the multi-edition variant in §3.

---

## 2. Reusable template (fill the `{{PLACEHOLDERS}}`)

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Book",
  "@id": "{{CANONICAL_URL}}#book",
  "name": "{{BOOK_TITLE}}",
  "url": "{{CANONICAL_URL}}",
  "image": "{{COVER_IMAGE_URL}}",
  "description": "{{BACK_COVER_BLURB}}",
  "inLanguage": "en-US",
  "genre": ["Crime fiction", "Mystery", "Thriller"],
  "bookFormat": "https://schema.org/EBook",
  "datePublished": "{{YYYY-MM-DD}}",
  "author": {
    "@type": "Person",
    "name": "Phillip Strang",
    "url": "https://phillipstrang.com/about/"
  },
  "publisher": { "@id": "https://phillipstrang.com/#organization" },
  "isPartOf": {
    "@type": "BookSeries",
    "name": "{{SERIES_NAME}}",
    "url": "{{SERIES_PAGE_URL}}"
  },
  "potentialAction": {
    "@type": "ReadAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "{{BUY_LINK}}",
      "actionPlatform": [
        "https://schema.org/DesktopWebPlatform",
        "https://schema.org/MobileWebPlatform"
      ]
    },
    "expectsAcceptanceOf": {
      "@type": "Offer",
      "category": "purchase",
      "availability": "https://schema.org/InStock"
    }
  }
}
</script>
```

Field source map (from the existing page markup):
- `{{CANONICAL_URL}}` → the page's `<link rel="canonical">`
- `{{COVER_IMAGE_URL}}` → the page's `og:image`
- `{{BACK_COVER_BLURB}}` → the meta description / on-page synopsis
- `{{BUY_LINK}}` → the page's `geni.us` / Amazon link
- `{{SERIES_NAME}}` / `{{SERIES_PAGE_URL}}` → the series this title belongs to

---

## 3. Optional add-ons (only if the data is genuinely on the page)

**Multiple editions (ebook + paperback)** — replace `bookFormat` with `workExample`:

```json
"workExample": [
  {
    "@type": "Book",
    "bookFormat": "https://schema.org/EBook",
    "inLanguage": "en-US",
    "potentialAction": {
      "@type": "ReadAction",
      "target": "{{EBOOK_BUY_LINK}}"
    }
  },
  {
    "@type": "Book",
    "bookFormat": "https://schema.org/Paperback",
    "isbn": "{{PAPERBACK_ISBN_13}}",
    "inLanguage": "en-US"
  }
]
```

**Ratings** — add `aggregateRating` ONLY if a rating is actually displayed on the
page (e.g. you surface aggregated reviews). Do not paste Amazon/Goodreads numbers
that aren't shown on-page — that violates Google's review-snippet policy and risks
a manual action:

```json
"aggregateRating": {
  "@type": "AggregateRating",
  "ratingValue": "{{AVG_RATING}}",
  "reviewCount": "{{NUMBER_OF_REVIEWS}}",
  "bestRating": "5"
}
```

**ISBN** — add `"isbn": "{{ISBN_13}}"` to the top-level `Book` (print) or to the
relevant `workExample` edition when you have it.

---

## 4. Validate before shipping
1. Paste a finished block into the **[Schema Markup Validator](https://validator.schema.org/)**
   and Google's **[Rich Results Test](https://search.google.com/test/rich-results)**.
2. Confirm no conflict with the existing Yoast `WebPage` graph (they coexist; the
   Book just adds a node).
3. Roll out to one book page, recrawl in Search Console (URL Inspection → Test Live
   → Request Indexing), then template across all book pages.
```
