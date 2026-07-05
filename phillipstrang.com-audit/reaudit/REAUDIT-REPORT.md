# SEO Re-Audit — phillipstrang.com

**Re-audit date:** 2026-07-05
**Baseline audit:** 2026-06-30 (health score **65/100**)
**Method:** live verification of every original finding (server-HTML fetch of robots.txt,
llms.txt, sitemaps, homepage, About, Blog, Reading Guides, two book pages, and the
legacy/funnel URLs). Note: JS-rendered card widgets and field CWV/GSC data are not
observable from this environment, so those are excluded.

---

## Updated health score: **~74 / 100**  (was 65)

Real, measurable progress — driven by structured data, index hygiene, and compliance.
The score is still held back by two **book-page technical bugs** surfaced in this
re-audit and the **unaddressed thin-content footprint**, plus the untouched
**off-site authority** ceiling.

---

## Scorecard vs the original Top 5 issues

| # | Original issue | Status now | Evidence |
|---|---|---|---|
| 1 | Index bloat (funnel/legacy/dupe indexable) | ✅ **Largely fixed** | `/free-book/` now `noindex`; `/thank-you/` & `/book-list-old/` return 404; `/about-2/` now canonicalises to `/about/` |
| 2 | No Book schema on book pages | ✅ **Fixed** | `Book` JSON-LD live on `/…/murder-is-a-tricky-business/` and `/tremayne/burial-mound/` |
| 3 | AI crawlers blocked | ⚠️ **Unchanged (decision needed)** | robots.txt still Disallows GPTBot, ClaudeBot, Google-Extended, CCBot, Amazonbot, Bytespider. `llms.txt` now exists (200) — which *contradicts* the block |
| 4 | Yoast title template broken | ⚠️ **Half fixed** | Home/About/Blog/Reading-Guides titles are clean & branded. **Book/post pages still render "Title -"** (dangling separator, no brand) |
| 5 | Missing H1s + thin content | ⚠️ **Half fixed** | Guides & About now have exactly one H1. **Blog and every book page still have 0 H1.** ~1,513 thin programmatic pages still live |

## Scorecard vs the original Top 5 quick wins

| Quick win | Status |
|---|---|
| `/reading-guides/` canonical | ✅ Fixed (self-canonical, clean title "Crime Fiction Reading Guides") |
| `http://` URLs in sitemaps | ✅ Fixed (0 non-https locs) |
| noindex funnel/legacy pages | ✅ Mostly (see #1) |
| Yoast title template | ⚠️ Half (book/post pages still broken) |
| `llms.txt` + AI policy | ✅ llms.txt added — ⚠️ but AI-crawler block still on (policy call pending) |

## Additional work delivered since baseline (verified live)

- ✅ **Amazon-compliant affiliate disclosure** live on book pages, About, Reading Guides
- ✅ **Person (author) schema** sitewide — the E-E-A-T entity signal
- ✅ **Menu restructured** 9 flat items → 5 with dropdowns (`Phillip Strang Books` carries a dropdown; authority concentrated)
- ✅ **About page** rebuilt: broken links fixed, counts reconciled to catalogue (19 series / 144 novels), Person schema
- ✅ **Book List** page rebuilt; **author "authors-like-X" funnels** live

---

## Remaining priorities (this re-audit's action list)

### 🔴 HIGH — book pages are the money pages and have two technical bugs
1. **Broken title tags on all book/post pages** — e.g. `<title>Murder is a Tricky Business -</title>`.
   The Yoast title template for the book post type / series taxonomy ends in a
   dangling `%%sep%%` with an empty `%%sitename%%`. Fix: Yoast → Search Appearance →
   (the book post type + the series taxonomy) → set title to
   `%%title%% %%sep%% %%sitename%%`. This hits every book's SERP snippet.
2. **No H1 on book pages or `/blog/`.** The book title should be the page's single `<h1>`.
   The h1-normalize snippet doesn't cover these templates. Either extend it to the
   book post type or fix the template so the title outputs as `<h1>`.

### 🟠 MEDIUM
3. **Homepage renders 3 H1s** ("PHILLIP", "STRANG", "PHILLIP STRANG") — the logo/title
   markup. Collapse to one H1.
4. **Decide the AI-crawler policy.** Right now `llms.txt` invites AI engines while
   robots.txt blocks GPTBot/ClaudeBot/Google-Extended/CCBot. Pick one: unblock to be
   citable in AI answers (recommended for a citable author with first-party E-E-A-T),
   or keep blocked and drop the llms.txt pretence.

### 🔵 STRATEGIC — the real ceiling (unchanged since baseline)
5. **Thin programmatic content (~1,513 pages).** Untouched. Still the single largest
   drag on site-wide quality signals. The CUT/consolidation plan exists
   (`blog-triage.xlsx`, `redirects.csv`) but hasn't been executed.
6. **Off-site authority / backlinks.** The domain's real ranking ceiling. No link
   acquisition or E-E-A-T outreach has started (`authority-and-internal-linking-plan.md`).

---

## Bottom line
The on-page/technical sprint worked: schema, hygiene, compliance, and structure are
materially better, and nothing is regressed. The next highest-ROI move is **not more
polish** — it's (a) the two book-page bugs (title + H1) because they hit every money
page, then (b) finally executing the thin-content CUT and starting authority-building.
Those are where the remaining 25+ points live.
