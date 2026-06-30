# AI Search Readiness (GEO) — phillipstrang.com

Generative Engine Optimization: visibility in AI Overviews, ChatGPT, Perplexity, Gemini, Copilot.

## Findings

### HIGH — Most AI crawlers are blocked in robots.txt
The Cloudflare-managed robots block disallows the major AI agents:
```
User-agent: Amazonbot            Disallow: /
User-agent: Applebot-Extended    Disallow: /
User-agent: Bytespider           Disallow: /
User-agent: Google-Extended      Disallow: /
User-agent: GPTBot               Disallow: /
User-agent: meta-externalagent   Disallow: /
User-agent: CloudflareBrowserRenderingCrawler  Disallow: /
```
Plus content-signals: `Content-Signal: search=yes, ai-train=no`.

**Implication:** ChatGPT (GPTBot), Gemini/Vertex training (Google-Extended), Meta AI, and Apple Intelligence cannot ingest the site. The "books in order" content — which is exactly the kind of factual, list-structured material AI answer engines love to cite — is invisible to them.

**Nuance / decision needed:** Blocking *training* (`ai-train=no`, Google-Extended) is a legitimate rights choice and does **not** affect Google Search or AI Overviews (which use Googlebot, still allowed). But blocking **GPTBot** also blocks ChatGPT *search/browse* citations, and Bytespider/Amazonbot blocks remove other surfaces. Recommend a deliberate policy:
- Keep `ai-train=no` / Google-Extended blocked if the author wants to bar model training.
- **Consider allowing GPTBot and PerplexityBot** (currently PerplexityBot isn't explicitly listed — verify) so the brand can be *cited* in AI answers even if not used for training. Citation drives book discovery; training does not.

### MEDIUM — No `llms.txt`
`/llms.txt` and `/ai.txt` both 404. Add an `llms.txt` curating the canonical entry points (complete book list, series hubs, about/bio, free-book) to guide compliant AI agents to the highest-value, factual pages.

### MEDIUM — Citability is strong but untapped
Content structure is AI-friendly (clear H2s, ordered lists, dated "in 2025" framing, `Person`/`Article` schema). With crawlers blocked, this strength is wasted off-Google. Once access policy is set, the existing format needs little change to be citation-ready.

### LOW — Brand-entity signals are good
Consistent `Person`/`Organization` schema and a strong "about" page give AI engines a clear author entity to attach answers to — valuable for "who is Phillip Strang / what order to read his books" style queries inside Google's AI Overviews (which retain access).

## Summary
The biggest GEO lever here is a **policy decision**, not a technical fix: decide which AI agents may *cite* (vs. *train on*) the content, then align robots.txt + add llms.txt. Content quality for AI is already in good shape.
