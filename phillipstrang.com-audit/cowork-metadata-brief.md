# Cowork Brief — KDP Catalogue Metadata Optimisation (Phillip Strang)

**Purpose:** systematically re-qualify and rewrite **keywords, categories, and blurbs** across the
crime/thriller catalogue, using *proven demand data* — not guesses — and output a per-book worksheet
ready to implement in KDP.

**Who runs it:** Claude Cowork (multi-file/agentic), reviewed by Phillip (or a VA) before anything
goes live in KDP.

**Reuse note:** this brief is deliberately generic on inputs. Swap the input files and it becomes the
**value-extraction ("lift") workflow for any acquired KU catalogue** — same rules, new data.

---

## 1. Inputs (provide these to Cowork)

| # | File | What it provides | Required? |
|---|---|---|---|
| A | Catalogue spreadsheet (`bs-phillip_strang…xlsx`) | Titles, series, ASINs, current Keyword 1/2, prices | Yes |
| B | KDP Royalties Estimator (`KDP_Royalties…xlsx`) | Which books/series earn (sales **and KENP**) → priority | Yes |
| C | Amazon Ads **Search-Term** reports (UK + AU) | **Proven converting search terms** = qualified keywords | Yes — the key input |
| D | Current blurbs per book | Source-of-truth text to rewrite (export from KDP Bookshelf, or the Amazon product pages) | Yes for blurb work |
| E | *(optional)* Publisher Rocket export | Estimated Amazon search volume + competition per keyword | Optional (adds volume data) |

> **Honest limit:** Cowork has **no live Amazon search-volume data.** Keyword *qualification* comes from
> C (your money-proven terms) and, if supplied, E. Without C or E, Cowork can only *suggest* candidates,
> not qualify them — so C is non-negotiable.

---

## 2. Keyword rules — QUALIFY, don't brainstorm

**Candidate sources, in priority order:**
1. **Converting terms from the ad search-term reports (C).** Any term with clicks + sales/KENP is proven
   demand *and* relevance. These are the gold pool. (e.g. from AU data: `australian detective series`,
   `british crime fiction`.)
2. Publisher Rocket terms (E), if supplied — demand > competition "sweet spot" only.
3. Derived terms from the book itself: setting, detective type, sub-genre, tropes.

**Every proposed keyword must pass all four gates — reject if it fails any:**
- **Demand** — evidence exists (ad-term, Rocket, or strong genre-standard term).
- **Relevance** — genuinely describes the book (Amazon suppresses off-topic keywords).
- **Winnable** — not so competitive the book can't surface.
- **Buyer language** — how crime readers actually phrase it.

**Hard rules:**
- Fill **all 7** keyword fields; each can be a **2–4 word phrase** (long-tail > single words).
- **Do NOT repeat** words already in the title / subtitle / series name (Amazon already indexes those — wasted slots).
- **Do NOT put competitor author names in KDP keyword fields** (ToS violation, can get titles flagged).
  Competitor authors belong in **Ads targeting only**, never metadata.
- Tag each keyword with its evidence source: `[ad]`, `[rocket]`, or `[derived]`.

---

## 3. Category rules — relevant AND winnable

- Amazon allows 3 at upload; **request up to 10** via KDP support. Cowork drafts that request email per book.
- Choose categories that are: **relevant** (true browse-node fit), **winnable** (nicher subcats where a
  top-10 rank — the bestseller badge — is achievable), and **trafficked** (readers actually browse them).
- Winnability heuristic: if the category's #1 book has only a *modest* sales rank, it's winnable; if it's
  dominated by heavyweights, skip it.
- Map by series flavour (Scottish Highlands → Scottish/British detective nodes; Australian → relevant
  regional/police-procedural nodes; FBI → US thriller nodes, etc.).
- **Output per book:** 3 primary categories + up to 7 additional, each labelled relevant/winnable, plus a
  ready-to-send **KDP-support request email**.

---

## 4. Blurb rules — Cowork writes, the market qualifies

**Structure (in this order):**
1. **Front-loaded hook** — a gripping first line/sentence. Mobile buyers see only ~2–3 lines before
   "read more," so line 1 must do the work.
2. **Stakes** — the core conflict / what's at risk.
3. **Hook question** — the open loop that forces the click-to-buy.
4. **Series/read-order line** — series name, book number, "Book 1 of…" or "The DCI Isaac Cook series
   continues…", plus a light "for readers who enjoy [sub-genre / comparable]" positioning.
5. **Soft CTA** — e.g. "Start the series today."

**Hard rules:**
- **Source of truth = the existing blurb + book (D).** Never fabricate plot, characters, or claims.
- ~150–200 words visible portion; no spoilers; dark-crime tone.
- Produce **two variants** per book with different lead angles (premise-led / character-led / atmosphere-led).
- Light HTML for the description (bold hook, line breaks) where the field supports it.
- Series consistency: every book in a series uses the same series-positioning line + reading-order signpost.

*(Blurbs can only be truly qualified by conversion — Amazon has no native A/B test. Phillip swaps the
variant on ad-driven titles and watches click-to-sale / BSR. Cowork produces; ad traffic tests.)*

---

## 5. Per-book output template (one row per book, delivered as a sheet + blurb doc)

```
Title | Series | Book # | Priority Tier |
7 Keywords (each tagged [ad]/[rocket]/[derived]) |
Categories: 3 primary + up to 7 more (each: relevant / winnable) |
KDP support request email (drafted) |
Blurb v1 | Blurb v2 |
Flags / uncertainties for human review
```

---

## 6. Prioritisation — do NOT process all ~144 at once

Use file B (earnings + KENP) to tier:
- **Tier 1 (do first):** the **series starters (Book 1s)** of the top-earning series (Lynch, Cook, Maya,
  and any others ranked high by KENP) **+ the boxsets currently advertised.** Highest leverage: discovery
  on starters, conversion on the ad-driven boxsets.
- **Tier 2:** remaining titles in the top-earning series.
- **Tier 3:** everything else.

Complete Tier 1 → Phillip reviews/approves → implement → **measure 30–60 days (conversion, BSR, KENP)
before scaling to Tier 2.** Prove the lift before spreading effort.

---

## 7. Guardrails (what Cowork must NOT do)

- Do **not** invent keywords with no demand evidence — flag "no data" instead of guessing.
- Do **not** put competitor author names in KDP keyword fields.
- Do **not** fabricate anything in a blurb.
- Do **not** assign a category the book doesn't genuinely fit.
- When uncertain, **flag for human review** rather than filling a slot.

---

## 8. Workflow summary

1. Ingest inputs A–E.
2. Build the **Tier-1** worksheet (§5).
3. Phillip/VA reviews & approves the shortlist.
4. Implement in KDP (+ send category requests).
5. Measure 30–60 days.
6. Repeat for Tier 2, then Tier 3.

**Then:** archive the finished brief as the reusable template for acquired-catalogue lift.
