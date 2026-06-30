# Action Plan — phillipstrang.com

Prioritized remediation. Effort: **S** = <1h, **M** = a few hours, **L** = day+.

## Phase 1 — Critical Fixes (Week 1)

| # | Action | Severity | Effort |
|---|---|---|---|
| 1 | Fix `/reading-guides/` canonical — change from `…/series__trashed/reading-guides/` to `https://phillipstrang.com/reading-guides/` | Medium | S |
| 2 | Set WordPress **Settings → General → Site Address (URL)** to `https://` so Yoast sitemaps/robots stop emitting `http://` | Medium | S |
| 3 | `noindex,follow` all funnel/utility pages and remove from sitemap: `/free-book/`, `/thank-you/`, `/welcome-aboard/`, `/welcome-aboard-free-du/`, `/join-my-arc-team/`, `/du-thank-you-books/`, `/du-thank-you-free-dl-lead-gen/`, sign-up pages | High | M |
| 4 | Resolve duplicate/legacy pages — 301 to the canonical winner, then delete: `/about-2/`→`/about/`; `/book-list-old/`→`/complete-book-list/`; `/di-tremayne-series-2/`→`/di-tremayne-series/`; `/phillip-strang-crime-fiction-author-old/`, `/du-thank-you-free-dl-lead-gen-old/` | High | M |
| 5 | Repair Yoast title template — remove dangling ` -`, end every title with `— Phillip Strang` (Yoast → Search Appearance, per post type) | High | S |
| 6 | Add exactly one descriptive `<h1>` to post + archive templates (fixes 0-H1 on programmatic posts and `/blog/`) | High | M |

## Phase 2 — High-Impact Improvements (Weeks 2–3)

| # | Action | Severity | Effort |
|---|---|---|---|
| 7 | Add `Book` schema to all book pages (author, isbn, bookFormat, inLanguage, workExample) + `Offer`/`Product` where price shown — use `scripts/schema_generate.py` | High | L |
| 8 | Add `ItemList`/`ListItem` (each a `Book`) to "books in order" pages (`/complete-book-list/`, `/my-books/`, per-author pages) | Medium | M |
| 9 | **AI-crawler policy decision** — keep training blocked if desired, but allow citation crawlers (GPTBot, PerplexityBot) so the brand can be cited in AI answers; update robots.txt accordingly | High | S |
| 10 | Add `llms.txt` curating canonical high-value pages (complete book list, series hubs, about, free book) | Medium | S |
| 11 | Write unique meta descriptions for all series + category pages (replace auto snippets) | Medium | M |
| 12 | Expand thin pages: `/dci-isaac-cook-series/` (132w), `/my-books/` (164w) — add intro copy, reading order, internal links | Medium | M |
| 13 | Configure Google PageSpeed/CrUX API key; run `scripts/pagespeed_check.py` for real LCP/INP/CLS | Info | S |

## Phase 3 — Content & Authority (Month 2)

| # | Action | Severity | Effort |
|---|---|---|---|
| 14 | Audit the ~1,643 programmatic posts by word count + engagement; consolidate or `noindex`+improve the thinnest 20–30%; ensure each targets a distinct query | High | L |
| 15 | Strengthen competitor-author "books in order" pages with original curation and internal links to relevant Strang series ("if you like Rebus, try DCI Isaac Cook") | Medium | L |
| 16 | Convert cover images to WebP/AVIF with explicit `width`/`height`, `srcset`, and `loading="lazy"` below the fold | Medium | M |
| 17 | Add `AggregateRating`/`Review` and small `FAQPage` blocks only where genuinely supported on-page | Low | M |
| 18 | Backlink profile review — configure Moz/Bing API (or DataForSEO) and run `/seo backlinks` | Medium | M |

## Phase 4 — Monitoring & Iteration (Ongoing)

| # | Action | Effort |
|---|---|---|
| 19 | Connect Search Console + GA4; track indexation/clicks after pruning | M |
| 20 | Capture an SEO drift baseline (`scripts/drift_baseline.py`) and re-compare after each deploy | S |
| 21 | Re-audit quarterly; monitor AI-Overview/ChatGPT citations after the access-policy change | S |

---

### Sequencing note
Phase 1 items 3–4 (index hygiene) plus 5–6 (titles/H1) deliver the fastest ranking-signal cleanup for the least effort. The `Book` schema (item 7) is the highest-ceiling opportunity for a book-selling author and should not slip past Phase 2.
