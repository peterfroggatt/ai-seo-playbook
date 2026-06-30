# Content Quality & E-E-A-T — phillipstrang.com

**Business type:** Author / Publisher (Phillip Strang — crime & thriller author, 150+ books across 18 series). Sells via Amazon; site is the brand hub + lead-gen (free book → email list).

## What works
- **First-party Experience & Expertise (E-E-A-T):** content is authored by the novelist himself. `/about/` is 814 words with a real bio (16 series, 150+ novels, British/Australian/American detectives). Strong author-entity signal reinforced by `Person` schema sitewide.
- **Genuinely useful flagship content:** "books in order" reading-order pages (e.g. `/ian-rankins-rebus-books-in-order/`, 1,958 words, 31 covers with Amazon links) are well-structured, scannable, and match clear search intent.
- **Compelling, hand-written meta descriptions** on money pages (book pages, `/complete-book-list/`, `/about/`).

## Findings

### HIGH — Programmatic listicle content at scale (thin-content risk)
The blog is ~1,643 posts dominated by templated patterns: "Best-Selling [genre] Novels in 2025", "Best [genre] Novels with [trope]", "Best Crime Fiction Authors Set in [place]". Sampled depth varies wildly:
- `/best-selling-hard-boiled-mystery-novels-in-2025/`: 3,885 words (substantial) **but 0 H1**.
- `/best-cozy-mystery-novels-with-amateur-sleuths/`: 477 words covering "4 mysteries" — thin for the topic.
At this volume, low-depth entries risk Google's "scaled content abuse" / Helpful Content signals dragging sitewide quality. **Action:** audit posts by word count + engagement; consolidate or `noindex`+improve the thinnest 20–30%, and ensure each targets a distinct query.

### HIGH — Thin/empty pages indexed
- `/about-2/`: **13 words** — an empty duplicate of `/about/`, fully indexable.
- `/series-cook` (`/dci-isaac-cook-series/`): **132 words**, meta description auto-truncated from body ("…The son of Jamaican").
- `/my-books/`: 164 words (acceptable as a visual index but text-thin).
These should be expanded, merged, or `noindex`ed.

### MEDIUM — Auto-generated / templated meta descriptions on some pages
`/dci-isaac-cook-series/` shows a body-derived, mid-sentence description. Series and category pages need hand-written meta descriptions.

### MEDIUM — "Books in order" pages about *other* authors are off-brand E-E-A-T
Pages targeting Rankin/Cleeves/Slaughter/McDermid etc. capture high-intent crime-fiction traffic, which is smart — but ensure each adds original value (curation, reading notes, "if you like X, try Phillip Strang's Y") rather than reproducing publisher lists, and that they internally link back to Strang's own series to convert the borrowed traffic.

## AI citation readiness
List/"in order" content is well-formatted for extraction (clear headings, ordered lists, dates), which is citable — **but** see GEO findings: the site blocks most AI crawlers, so this citability is largely unrealized off-Google.
