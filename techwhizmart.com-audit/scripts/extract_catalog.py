#!/usr/bin/env python3
"""One-off catalog extractor for techwhizmart.com affiliate rebuild.
Crawls the product sitemap, pulls ASIN/title/category/price/image per product,
dedupes by ASIN. Output: raw + deduped CSV. Direct egress (NO_PROXY) required.
"""
import csv, html, re, sys, os
from concurrent.futures import ThreadPoolExecutor, as_completed
import requests

os.environ.setdefault("NO_PROXY", "techwhizmart.com")
os.environ.setdefault("no_proxy", "techwhizmart.com")

BASE = "https://techwhizmart.com"
OUT_DIR = "techwhizmart.com-audit"
UA = {"User-Agent": "Mozilla/5.0 (compatible; SEO-Audit/1.0)"}
S = requests.Session()
S.trust_env = True

def get(url, **kw):
    return S.get(url, headers=UA, timeout=25, **kw)

def product_urls():
    xml = get(f"{BASE}/product-sitemap.xml").text
    return re.findall(r"<loc>([^<]+)</loc>", xml)

ASIN_RE   = re.compile(r"\bB0[A-Z0-9]{8}\b")          # uppercase real ASINs only
IMG_RE    = re.compile(r"m\.media-amazon\.com/images/I/([A-Za-z0-9_+-]+)\.(jpg|jpeg|png)", re.I)
CAT_RE    = re.compile(r"product_cat-([a-z0-9\-]+)")
TITLE_RE  = re.compile(r'<meta property="og:title" content="([^"]+)"', re.I)
TITLE2_RE = re.compile(r"<title>(.*?)</title>", re.I | re.S)
# price: prefer JSON-LD "price":"NN.NN", fallback to first woocommerce price amount
PRICE_LD  = re.compile(r'"price"\s*:\s*"?([0-9]+(?:\.[0-9]{1,2})?)"?')
PRICE_WC  = re.compile(r'woocommerce-Price-amount[^>]*>\s*<[^>]*>\s*([0-9,]+(?:\.[0-9]{2})?)', re.I)

def clean_title(t):
    t = html.unescape(t or "").strip()
    for suf in (" - TechWhizMart", " – TechWhizMart", " | TechWhizMart"):
        if t.endswith(suf):
            t = t[: -len(suf)]
    return t.strip()

def asin_pick(text):
    found = ASIN_RE.findall(text)
    if not found:
        return ""
    # most frequent uppercase B0 token = the product's ASIN
    return max(set(found), key=found.count)

def parse(url):
    row = {"asin": "", "title": "", "category_primary": "", "all_categories": "",
           "price": "", "recovered_image_url": "", "source_url": url,
           "slug_has_trashed": "__trashed" in url, "http_status": ""}
    try:
        r = get(url)
        row["http_status"] = r.status_code
        if r.status_code != 200:
            return row
        h = r.text
    except Exception as e:
        row["http_status"] = f"ERR:{type(e).__name__}"
        return row
    row["asin"] = asin_pick(h)
    m = TITLE_RE.search(h) or TITLE2_RE.search(h)
    row["title"] = clean_title(m.group(1) if m else "")
    cats = [c for c in dict.fromkeys(CAT_RE.findall(h))]  # dedupe, keep order
    row["all_categories"] = ";".join(cats)
    row["category_primary"] = cats[-1] if cats else ""    # most specific = last
    mi = IMG_RE.search(h)
    if mi:
        row["recovered_image_url"] = f"https://m.media-amazon.com/images/I/{mi.group(1)}.{mi.group(2)}"
    mp = PRICE_LD.search(h) or PRICE_WC.search(h)
    if mp:
        row["price"] = mp.group(1).replace(",", "")
    return row

def main():
    urls = product_urls()
    # drop the /shop/ entry that sometimes appears in product sitemap
    urls = [u for u in urls if "/product/" in u]
    print(f"Fetching {len(urls)} product URLs...", file=sys.stderr)
    rows = []
    with ThreadPoolExecutor(max_workers=10) as ex:
        futs = {ex.submit(parse, u): u for u in urls}
        for i, f in enumerate(as_completed(futs), 1):
            rows.append(f.result())
            if i % 50 == 0:
                print(f"  {i}/{len(urls)}", file=sys.stderr)
    rows.sort(key=lambda r: (r["category_primary"], r["title"]))

    cols = ["asin", "title", "category_primary", "all_categories", "price",
            "recovered_image_url", "source_url", "slug_has_trashed", "http_status"]
    raw_path = f"{OUT_DIR}/catalog-raw.csv"
    with open(raw_path, "w", newline="", encoding="utf-8") as fh:
        w = csv.DictWriter(fh, fieldnames=cols); w.writeheader(); w.writerows(rows)

    # dedupe by ASIN -> clean catalog
    by_asin = {}
    no_asin = []
    for r in rows:
        a = r["asin"]
        if not a:
            no_asin.append(r); continue
        if a not in by_asin:
            by_asin[a] = dict(r); by_asin[a]["url_count"] = 1
            by_asin[a]["has_trashed_dupe"] = r["slug_has_trashed"]
            by_asin[a]["has_clean_url"] = not r["slug_has_trashed"]
        else:
            e = by_asin[a]; e["url_count"] += 1
            e["has_trashed_dupe"] = e["has_trashed_dupe"] or r["slug_has_trashed"]
            e["has_clean_url"]   = e["has_clean_url"]   or (not r["slug_has_trashed"])
            # prefer a clean (non-trashed) source_url as canonical
            if r["slug_has_trashed"] is False and e["slug_has_trashed"] is True:
                for k in ("title","category_primary","all_categories","price",
                          "recovered_image_url","source_url","slug_has_trashed"):
                    e[k] = r[k]

    clean_cols = ["asin","title","category_primary","all_categories","price",
                  "recovered_image_url","canonical_source_url","url_count",
                  "has_clean_url","has_trashed_dupe"]
    clean_path = f"{OUT_DIR}/catalog.csv"
    with open(clean_path, "w", newline="", encoding="utf-8") as fh:
        w = csv.DictWriter(fh, fieldnames=clean_cols); w.writeheader()
        for a, e in sorted(by_asin.items(), key=lambda kv:(kv[1]["category_primary"], kv[1]["title"])):
            w.writerow({"asin": a, "title": e["title"],
                        "category_primary": e["category_primary"],
                        "all_categories": e["all_categories"], "price": e["price"],
                        "recovered_image_url": e["recovered_image_url"],
                        "canonical_source_url": e["source_url"],
                        "url_count": e["url_count"],
                        "has_clean_url": e["has_clean_url"],
                        "has_trashed_dupe": e["has_trashed_dupe"]})

    # stats
    total = len(rows)
    ok = sum(1 for r in rows if r["http_status"] == 200)
    with_asin = sum(1 for r in rows if r["asin"])
    uniq = len(by_asin)
    dup_urls = sum(e["url_count"] for e in by_asin.values()) - uniq
    trashed_dupe_asins = sum(1 for e in by_asin.values() if e["has_trashed_dupe"] and e["has_clean_url"])
    only_trashed = sum(1 for e in by_asin.values() if e["has_trashed_dupe"] and not e["has_clean_url"])
    cats = {}
    for e in by_asin.values():
        cats[e["category_primary"] or "(none)"] = cats.get(e["category_primary"] or "(none)", 0) + 1
    print("\n===== EXTRACTION SUMMARY =====")
    print(f"product URLs crawled        : {total}")
    print(f"  HTTP 200                  : {ok}")
    print(f"  with recoverable ASIN     : {with_asin}")
    print(f"  no ASIN found             : {len(no_asin)}")
    print(f"UNIQUE products (by ASIN)   : {uniq}")
    print(f"  duplicate URLs collapsed  : {dup_urls}")
    print(f"  ASINs with a __trashed dup: {trashed_dupe_asins}")
    print(f"  ASINs ONLY on trashed URL : {only_trashed}")
    print(f"\nUnique products per category (top 15):")
    for c, n in sorted(cats.items(), key=lambda x:-x[1])[:15]:
        print(f"  {n:4d}  {c}")
    print(f"\nWrote: {clean_path}  ({uniq} unique products)")
    print(f"Wrote: {raw_path}  ({total} raw rows)")

if __name__ == "__main__":
    main()
