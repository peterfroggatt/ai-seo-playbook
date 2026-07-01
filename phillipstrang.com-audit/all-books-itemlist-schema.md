# Ready-to-paste `ItemList` schema — Phillip Strang Books page

Structured-data listing of all **116 titles** (16 series/groups, reading order) extracted from `/complete-book-list/`.
Add inside `<head>` of `/phillip-strang-books/` (and/or `/complete-book-list/`). It coexists with the existing Yoast graph.

> Reconcile against your true catalog first — this reflects what the site's Complete Book List currently links (see notes at bottom).

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Phillip Strang Books in Order",
  "description": "Complete list of Phillip Strang crime fiction novels across 16 series/groups, in reading order.",
  "url": "https://phillipstrang.com/phillip-strang-books/",
  "numberOfItems": 116,
  "itemListOrder": "https://schema.org/ItemListOrderAscending",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Dark Streets",
      "url": "https://geni.us/darkstreets"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Pinchgut",
      "url": "https://geni.us/pinchgut"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "Island Shadows",
      "url": "https://geni.us/islandshadows"
    },
    {
      "@type": "ListItem",
      "position": 4,
      "name": "Manly Ferry",
      "url": "https://geni.us/manlyferry"
    },
    {
      "@type": "ListItem",
      "position": 5,
      "name": "Harbour Depths",
      "url": "https://geni.us/harbourdepths"
    },
    {
      "@type": "ListItem",
      "position": 6,
      "name": "Bondi Rip",
      "url": "https://geni.us/bondirip"
    },
    {
      "@type": "ListItem",
      "position": 7,
      "name": "Murder is a Tricky Business",
      "url": "https://geni.us/mitb"
    },
    {
      "@type": "ListItem",
      "position": 8,
      "name": "Murder House",
      "url": "https://geni.us/murderhouse"
    },
    {
      "@type": "ListItem",
      "position": 9,
      "name": "Murder by Numbers",
      "url": "https://geni.us/murderbynumbers2"
    },
    {
      "@type": "ListItem",
      "position": 10,
      "name": "Murder in Little Venice",
      "url": "https://geni.us/milv"
    },
    {
      "@type": "ListItem",
      "position": 11,
      "name": "Murder is the Only Option",
      "url": "https://geni.us/mitoo"
    },
    {
      "@type": "ListItem",
      "position": 12,
      "name": "Murder in Notting Hill",
      "url": "https://geni.us/minh"
    },
    {
      "@type": "ListItem",
      "position": 13,
      "name": "Murder in Room 346",
      "url": "https://geni.us/mir346"
    },
    {
      "@type": "ListItem",
      "position": 14,
      "name": "Murder of a Silent Man",
      "url": "https://geni.us/moasm"
    },
    {
      "@type": "ListItem",
      "position": 15,
      "name": "Murder has no Guilt",
      "url": "https://geni.us/mhng"
    },
    {
      "@type": "ListItem",
      "position": 16,
      "name": "Murder in Hyde Park",
      "url": "https://geni.us/mihp"
    },
    {
      "@type": "ListItem",
      "position": 17,
      "name": "Six Years Too Late",
      "url": "https://geni.us/sixyearstoolate"
    },
    {
      "@type": "ListItem",
      "position": 18,
      "name": "Grave Passion",
      "url": "https://geni.us/gravepassion"
    },
    {
      "@type": "ListItem",
      "position": 19,
      "name": "The Slaying of Joe Foster",
      "url": "https://geni.us/tsojf"
    },
    {
      "@type": "ListItem",
      "position": 20,
      "name": "The Hero's Fall",
      "url": "https://geni.us/theherosfall"
    },
    {
      "@type": "ListItem",
      "position": 21,
      "name": "The Vicar's Confession",
      "url": "https://geni.us/thevicarsconfession"
    },
    {
      "@type": "ListItem",
      "position": 22,
      "name": "Guilty Until Proven Innocent",
      "url": "https://geni.us/gupi"
    },
    {
      "@type": "ListItem",
      "position": 23,
      "name": "Devil House",
      "url": "https://geni.us/devilhouse"
    },
    {
      "@type": "ListItem",
      "position": 24,
      "name": "Deadly Secrets",
      "url": "https://geni.us/deadlysecretsps"
    },
    {
      "@type": "ListItem",
      "position": 25,
      "name": "Murder Without Reason",
      "url": "https://geni.us/murderwithoutreason"
    },
    {
      "@type": "ListItem",
      "position": 26,
      "name": "Death Unholy",
      "url": "https://geni.us/deathunholy"
    },
    {
      "@type": "ListItem",
      "position": 27,
      "name": "Death and the Assassin's Blade",
      "url": "https://geni.us/datab"
    },
    {
      "@type": "ListItem",
      "position": 28,
      "name": "Death and the Lucky Man",
      "url": "https://geni.us/datlm"
    },
    {
      "@type": "ListItem",
      "position": 29,
      "name": "Death at Coombe Farm",
      "url": "https://geni.us/dacf"
    },
    {
      "@type": "ListItem",
      "position": 30,
      "name": "Death by a Dead Man's Hand",
      "url": "https://geni.us/dbadmh"
    },
    {
      "@type": "ListItem",
      "position": 31,
      "name": "Death in the Village",
      "url": "https://geni.us/ditv"
    },
    {
      "@type": "ListItem",
      "position": 32,
      "name": "Burial Mound",
      "url": "https://geni.us/burialmound"
    },
    {
      "@type": "ListItem",
      "position": 33,
      "name": "The Body in the Ditch",
      "url": "https://geni.us/tbitd"
    },
    {
      "@type": "ListItem",
      "position": 34,
      "name": "The Horse's Mouth",
      "url": "https://geni.us/thehorsesmouth"
    },
    {
      "@type": "ListItem",
      "position": 35,
      "name": "Montfield's Madness",
      "url": "https://geni.us/montfield"
    },
    {
      "@type": "ListItem",
      "position": 36,
      "name": "Dust and Bones",
      "url": "https://geni.us/dustandbones"
    },
    {
      "@type": "ListItem",
      "position": 37,
      "name": "Forgotten Bones",
      "url": "https://geni.us/forgottenbones"
    },
    {
      "@type": "ListItem",
      "position": 38,
      "name": "Sunburnt Silence",
      "url": "https://geni.us/sunburntsilence"
    },
    {
      "@type": "ListItem",
      "position": 39,
      "name": "Ember Grave",
      "url": "https://geni.us/embergrave"
    },
    {
      "@type": "ListItem",
      "position": 40,
      "name": "Fractured Signals",
      "url": "https://geni.us/fracturedsignals"
    },
    {
      "@type": "ListItem",
      "position": 41,
      "name": "Shrouded Legacy",
      "url": "https://geni.us/shroudedlegacy"
    },
    {
      "@type": "ListItem",
      "position": 42,
      "name": "Final Stretch",
      "url": "https://geni.us/finalstretch"
    },
    {
      "@type": "ListItem",
      "position": 43,
      "name": "Shotgun Council",
      "url": "https://geni.us/shotguncouncil"
    },
    {
      "@type": "ListItem",
      "position": 44,
      "name": "Broken Fences",
      "url": "https://geni.us/brokenfences"
    },
    {
      "@type": "ListItem",
      "position": 45,
      "name": "The Sheep's Back",
      "url": "https://geni.us/thesheepsback"
    },
    {
      "@type": "ListItem",
      "position": 46,
      "name": "Last Orders",
      "url": "https://geni.us/lastorders"
    },
    {
      "@type": "ListItem",
      "position": 47,
      "name": "Prescription for Murder",
      "url": "https://geni.us/prescriptionformurder"
    },
    {
      "@type": "ListItem",
      "position": 48,
      "name": "Hardened Evidence",
      "url": "https://geni.us/hardenedevidence"
    },
    {
      "@type": "ListItem",
      "position": 49,
      "name": "Fatal Frequencies",
      "url": "https://geni.us/fatalfrequencies"
    },
    {
      "@type": "ListItem",
      "position": 50,
      "name": "Locust Moon",
      "url": "https://geni.us/locustmoon"
    },
    {
      "@type": "ListItem",
      "position": 51,
      "name": "Blood Claim",
      "url": "https://geni.us/bloodclaim"
    },
    {
      "@type": "ListItem",
      "position": 52,
      "name": "Terminal Bore",
      "url": "https://geni.us/terminalbore"
    },
    {
      "@type": "ListItem",
      "position": 53,
      "name": "Feral Ground",
      "url": "https://geni.us/feralground"
    },
    {
      "@type": "ListItem",
      "position": 54,
      "name": "The Dark Loch",
      "url": "https://geni.us/thedarkloch"
    },
    {
      "@type": "ListItem",
      "position": 55,
      "name": "The Burning Moor",
      "url": "https://geni.us/theburningmoor"
    },
    {
      "@type": "ListItem",
      "position": 56,
      "name": "The Silent Glen",
      "url": "https://geni.us/thesilentglen"
    },
    {
      "@type": "ListItem",
      "position": 57,
      "name": "The Frozen Peaks",
      "url": "https://geni.us/thefrozenpeaks"
    },
    {
      "@type": "ListItem",
      "position": 58,
      "name": "The Raven's Cry",
      "url": "https://geni.us/theravenscry"
    },
    {
      "@type": "ListItem",
      "position": 59,
      "name": "The Last Cairn",
      "url": "https://geni.us/thelastcairn"
    },
    {
      "@type": "ListItem",
      "position": 60,
      "name": "The Whisky Grave",
      "url": "https://geni.us/thewhiskygrave"
    },
    {
      "@type": "ListItem",
      "position": 61,
      "name": "The Forester's Fall",
      "url": "https://geni.us/theforestersfall"
    },
    {
      "@type": "ListItem",
      "position": 62,
      "name": "The Frozen Descent",
      "url": "https://geni.us/thefrozendescent"
    },
    {
      "@type": "ListItem",
      "position": 63,
      "name": "The Ferry Crossing",
      "url": "https://geni.us/theferrycrossing"
    },
    {
      "@type": "ListItem",
      "position": 64,
      "name": "The Shepherd's Stone",
      "url": "https://geni.us/theshepherdsstone"
    },
    {
      "@type": "ListItem",
      "position": 65,
      "name": "The Winter Lodge",
      "url": "https://geni.us/thewinterlodge"
    },
    {
      "@type": "ListItem",
      "position": 66,
      "name": "Ridge Lines",
      "url": "https://geni.us/ridgelines"
    },
    {
      "@type": "ListItem",
      "position": 67,
      "name": "Hidden Cargo",
      "url": "https://geni.us/hiddencargo"
    },
    {
      "@type": "ListItem",
      "position": 68,
      "name": "Killing Ghosts",
      "url": "https://geni.us/killingghosts"
    },
    {
      "@type": "ListItem",
      "position": 69,
      "name": "Jakarta Rain",
      "url": "https://geni.us/jakartarain"
    },
    {
      "@type": "ListItem",
      "position": 70,
      "name": "Ascension Protocol",
      "url": "https://geni.us/ascensionprotocol"
    },
    {
      "@type": "ListItem",
      "position": 71,
      "name": "Ghost Council",
      "url": "https://geni.us/ghostcouncil"
    },
    {
      "@type": "ListItem",
      "position": 72,
      "name": "The Red Dust",
      "url": "https://geni.us/thereddustps"
    },
    {
      "@type": "ListItem",
      "position": 73,
      "name": "The Dark Mine",
      "url": "https://geni.us/thedarkmine"
    },
    {
      "@type": "ListItem",
      "position": 74,
      "name": "Bone Country",
      "url": "https://geni.us/bonecountry"
    },
    {
      "@type": "ListItem",
      "position": 75,
      "name": "The Salt Lakes",
      "url": "https://geni.us/thesaltlakes"
    },
    {
      "@type": "ListItem",
      "position": 76,
      "name": "The Silent Hills",
      "url": "https://geni.us/thesilenthills"
    },
    {
      "@type": "ListItem",
      "position": 77,
      "name": "The Architect",
      "url": "https://geni.us/thearchitectps"
    },
    {
      "@type": "ListItem",
      "position": 78,
      "name": "Ghost Tracks",
      "url": "https://geni.us/ghosttracks"
    },
    {
      "@type": "ListItem",
      "position": 79,
      "name": "The Drowning Land",
      "url": "https://geni.us/thedrowningland"
    },
    {
      "@type": "ListItem",
      "position": 80,
      "name": "Songline Silence",
      "url": "https://geni.us/songlinesilence"
    },
    {
      "@type": "ListItem",
      "position": 81,
      "name": "Burning Season",
      "url": "https://geni.us/burningseason"
    },
    {
      "@type": "ListItem",
      "position": 82,
      "name": "Ancient Waters",
      "url": "https://geni.us/ancientwaters"
    },
    {
      "@type": "ListItem",
      "position": 83,
      "name": "Crocodile Tears",
      "url": "https://geni.us/crocodiletears2"
    },
    {
      "@type": "ListItem",
      "position": 84,
      "name": "Red Centre Justice",
      "url": "https://geni.us/redcentrejustice"
    },
    {
      "@type": "ListItem",
      "position": 85,
      "name": "Painted Lies",
      "url": "https://geni.us/paintedlies"
    },
    {
      "@type": "ListItem",
      "position": 86,
      "name": "Murder at the Henley-on-Todd",
      "url": "https://geni.us/mathont"
    },
    {
      "@type": "ListItem",
      "position": 87,
      "name": "Shadows of Uluru",
      "url": "https://geni.us/shadowsofuluru"
    },
    {
      "@type": "ListItem",
      "position": 88,
      "name": "The Devil's Puppet",
      "url": "https://geni.us/thedevilspuppet"
    },
    {
      "@type": "ListItem",
      "position": 89,
      "name": "Dust and Echo",
      "url": "https://geni.us/dustandecho"
    },
    {
      "@type": "ListItem",
      "position": 90,
      "name": "The Lakeview Murder",
      "url": "https://geni.us/thelakeviewmurder"
    },
    {
      "@type": "ListItem",
      "position": 91,
      "name": "The Grasmere Grave",
      "url": "https://geni.us/thegrasmeregrave"
    },
    {
      "@type": "ListItem",
      "position": 92,
      "name": "The Haweswater Secret",
      "url": "https://geni.us/thehaweswatersecret"
    },
    {
      "@type": "ListItem",
      "position": 93,
      "name": "Elements of Death",
      "url": "https://geni.us/elementsofdeath"
    },
    {
      "@type": "ListItem",
      "position": 94,
      "name": "Poisoned Ground",
      "url": "https://geni.us/poisonedground"
    },
    {
      "@type": "ListItem",
      "position": 95,
      "name": "Secrets in the Sand",
      "url": "https://geni.us/secretsinthesand"
    },
    {
      "@type": "ListItem",
      "position": 96,
      "name": "Bones in the Breakwater",
      "url": "https://geni.us/bonesinthebreakwater"
    },
    {
      "@type": "ListItem",
      "position": 97,
      "name": "Hermit's Hollow",
      "url": "https://geni.us/hermitshollow"
    },
    {
      "@type": "ListItem",
      "position": 98,
      "name": "Cliff Edge",
      "url": "https://geni.us/cliffedge"
    },
    {
      "@type": "ListItem",
      "position": 99,
      "name": "Burning Evidence",
      "url": "https://geni.us/burningevidence"
    },
    {
      "@type": "ListItem",
      "position": 100,
      "name": "Tracks in the Red Dust",
      "url": "https://geni.us/tracksinthereddust"
    },
    {
      "@type": "ListItem",
      "position": 101,
      "name": "The Opal Tomb",
      "url": "https://geni.us/theopaltomb"
    },
    {
      "@type": "ListItem",
      "position": 102,
      "name": "The Last Waterhole",
      "url": "https://geni.us/thelastwaterhole"
    },
    {
      "@type": "ListItem",
      "position": 103,
      "name": "The Devil's Marbles",
      "url": "https://geni.us/thedevilsmarbles"
    },
    {
      "@type": "ListItem",
      "position": 104,
      "name": "Shearing Shed Murder",
      "url": "https://geni.us/shearingshedmurder"
    },
    {
      "@type": "ListItem",
      "position": 105,
      "name": "Terminal Velocity",
      "url": "https://geni.us/terminalvelocity"
    },
    {
      "@type": "ListItem",
      "position": 106,
      "name": "The Amsterdam Killer",
      "url": "https://geni.us/theamsterdamkiller"
    },
    {
      "@type": "ListItem",
      "position": 107,
      "name": "The Prague Hunter",
      "url": "https://geni.us/thepraguehunterps"
    },
    {
      "@type": "ListItem",
      "position": 108,
      "name": "The Berlin Captives",
      "url": "https://geni.us/theberlincaptives"
    },
    {
      "@type": "ListItem",
      "position": 109,
      "name": "The Union Man",
      "url": "https://geni.us/theunionman"
    },
    {
      "@type": "ListItem",
      "position": 110,
      "name": "The Reckoning",
      "url": "https://geni.us/thereckoningps"
    },
    {
      "@type": "ListItem",
      "position": 111,
      "name": "Hostage of Islam",
      "url": "https://geni.us/hostageoffear"
    },
    {
      "@type": "ListItem",
      "position": 112,
      "name": "The Haberman Virus",
      "url": "https://geni.us/thehabermanvirus"
    },
    {
      "@type": "ListItem",
      "position": 113,
      "name": "Prelude to War",
      "url": "https://geni.us/preludetowar"
    },
    {
      "@type": "ListItem",
      "position": 114,
      "name": "Malika's Revenge",
      "url": "https://geni.us/malikasrevenge"
    },
    {
      "@type": "ListItem",
      "position": 115,
      "name": "Verrall's Nightmare",
      "url": "https://geni.us/verrallsnightmare"
    },
    {
      "@type": "ListItem",
      "position": 116,
      "name": "Forgotten Girls",
      "url": "https://geni.us/forgottengirls"
    }
  ]
}</script>
```

## Notes / data-quality flags
- **Source:** the 116 titles that are actually linked (geni.us buy links) on `/complete-book-list/`.
- **`Malika's Revenge` and `Verrall's Nightmare`** are grouped under *Steve Case* here but are standalones on the site — move them to a Standalone group if you prefer.
- **`Forgotten Girls`** is linked but not under a clear series header — placed in *Standalone / Other*.
- **Site copy says "150+ novels / 18 series"** but the list enumerates 116 titles / ~16 groups. Update either the catalog or the "150+/18" copy so they match.
- If you split the page per series, you can instead emit one `ItemList` per series (each with its own `name`) — ask and I'll regenerate in that shape.
## Validate
1. Paste into <https://validator.schema.org/> and Google's <https://search.google.com/test/rich-results>.
2. Roll out on the page, then request re-index in Search Console.
