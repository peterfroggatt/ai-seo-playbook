# Schema / Structured Data — phillipstrang.com

**Implementation:** Yoast SEO JSON-LD graph on every page. 3 `ld+json` blocks on the homepage, **all valid JSON** (3/3 parsed). Per-page graph includes: `WebSite` (+ `SearchAction`), `Organization`, `Person`, `WebPage`, `BreadcrumbList`, `ImageObject`. Blog posts additionally emit `Article`.

## What works
- Valid, well-formed Yoast graph sitewide — entity (`Person` = Phillip Strang, `Organization`, `WebSite` with SearchAction) is consistently declared. Good foundation for Knowledge Graph / author entity.
- `Article` schema present on blog posts.
- `BreadcrumbList` everywhere supports breadcrumb rich results.

## Findings

### HIGH — Book pages have no `Book` / `Product` schema
`/cook/murder-house-phillip-strang/` (a book page) emits only the generic Yoast `WebPage` graph — **no `Book`, `Product`, `offers`, `author`, or `aggregateRating`**. For an author selling 150+ titles this is the single biggest structured-data miss. Add `Book` (with `workExample`/`Book` editions, `author`, `isbn`, `bookFormat`, `numberOfPages`, `inLanguage`) and, where pricing is shown, `Offer`/`Product`. This enables richer SERP treatment and feeds book/author entities.

### MEDIUM — "Books in order" list pages lack `ItemList`
Reading-order pages (e.g. `/ian-rankins-rebus-books-in-order/`, `/complete-book-list/`, `/my-books/`) present ordered book lists but emit no `ItemList`/`ListItem` for the books. Adding `ItemList` (each item a `Book`) makes the list machine-readable and carousel-eligible.

### MEDIUM — No `Review` / `AggregateRating`
No review or rating schema anywhere. If the site surfaces reader reviews or aggregates Amazon/Goodreads ratings (with a visible on-page source), `Review`/`AggregateRating` on book pages can earn star treatment. Only mark up ratings genuinely shown on the page.

### LOW — No `FAQPage` on guide content
"Books in order" and reading-guide pages often answer discrete questions ("How many Rebus books are there?", "What order to read X?"). A small `FAQPage` block on those pages is a low-effort enhancement (note: FAQ rich results are now limited by Google, but the markup still aids AI extraction).

## Generation help
`scripts/schema_generate.py` can scaffold `Book`/`ItemList`/`FAQPage` JSON-LD for these page types.
