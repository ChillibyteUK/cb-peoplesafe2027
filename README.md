# cb-peoplesafe2027

Theme for peoplesafe.com: Peoplesafe and OKAlone consolidated into one WordPress
install, with three regions (UK, US, AU) served by Polylang Pro. It was forked
from `cb-skeleton2026` and shares its CSS/JS/build system; blocks and site
settings use native block.json/edit.js blocks and a plain Settings API page
instead of ACF. See "Adding a block" below.

## Regions

One install, one language per region: `en_GB` at `/uk/` (default), `en_US` at
`/us/`, `en_AU` at `/au/`. The site root `/` is a language-neutral hub where
visitors choose a region. The code is in `inc/regions.php` and `inc/regions/`,
and does nothing when Polylang is not active.

| Module | Does |
|---|---|
| `hreflang.php` | adds `x-default` (the site root) to every page with alternates |
| `mismatch.php` | a slug that exists in another region is a 404 |
| `routing.php` | merged post type paths, landing pages, front page, one canonical URL |
| `menu.php` | one menu, resolved to the visitor's region |
| `root.php` | serves the Root hub at `/` |
| `language-switcher.php` | the header switcher (unstyled) |

URL shape: `/uk/guides|whitepapers|brochures|infographics|video/{slug}/`,
`/uk/news/{slug}/` and `/uk/blogs/{slug}/`, landing pages at `/uk/{slug}/`,
taxonomies under `/jurisdiction/`. Decisions: DEC-023 to DEC-027 in the project
tracker.

### Post types and taxonomies

Registered in `inc/posttypes.php` and `inc/taxonomies.php` from one table each
(add a row for a new one): Products, Case studies, Resources, Legislation,
Partners, Careers, Landing pages and People (non-public), with their taxonomies
(`resource_type`, `jurisdiction` and the filters). Resources and Landing pages
register with `rewrite => false` because `routing.php` builds their paths.
Everything translatable is ticked in Polylang (not People, so one person serves
every region). Terms for News, the resource types, User story and the landing
types are seeded once per region from the CLI, not by the theme.

A post's slug is shared across regions only if its language is set first, so
set the language before the slug when creating one by script.

### Design tokens

`src/css/tokens.css` is the one place for colour, type, spacing, radius, shadow,
motion, z-index, button and layout values: use a token, never a literal.
`html` is 18px, so 1rem = 18px (16px small text is `--fs-small`). Breakpoints
are in `src/build/tokens.config.js`: sm 576, md 768, lg 992, xl 1240, xxl 1400.
All gradients share `--gradient-angle` (-30deg). `npm run generate-theme-json`
turns the literal `--col-*` colours and `--fs-*` sizes into the editor palette.
The agreed set and the reasoning are in `style-token-proposal.md` (project
folder, outside the theme).

### Needs the cbp-peoplesafe plugin

Polylang decides the `/` to `/uk/` redirect before the theme loads, and the Root
hub content type has to exist whatever the theme. Both are in the
`cbp-peoplesafe` plugin (`ChillibyteUK/cbp-peoplesafe`). Deploy and activate it
with the theme.

### Polylang Pro settings

- Languages: UK (`en_GB`, default), US (`en_US`), AU (`en_AU`); directory URLs;
  default language not hidden; remove `/language/`.
- Share slugs and Translate slugs on (Pro, on by default).
- Set a post's language **before** its slug, or slugs are not shared.
- Tick custom taxonomies translatable **before** creating terms, or the terms
  have no language and duplicates appear.
- Set the front page to the UK home; the theme normalises its region URLs.

### Set-up order on a new site

1. Install Polylang Pro, create the three languages with the settings above.
2. Activate this theme and the `cbp-peoplesafe` plugin.
3. Add a Root hub item (wp-admin, Root hub) and publish it. No WP-CLI is
   needed. Settings, Regions picks which one shows when there are several.
4. Save Settings, Permalinks once.
5. Assign the menu to the Primary location in the default region.

### Testing

The Polylang prototype checks (29 in all; phase 2 is `phase2.py`) live on the
test install as a `proto-types.php` must-use plugin and `proto-tests/`. They are
a test harness and are **not** deployed. Keep test-only code out of the theme;
code that carries over (filters, routing) belongs here.

## This is Bootstrap-*named*, not Bootstrap

Class names (`container`, `row`, `col-6`, `btn`, `navbar`, `d-flex`, etc.)
follow Bootstrap's conventions for familiarity — but every one of them is
implemented in this theme's own lightweight CSS. There is no Bootstrap
dependency, no Sass, no jQuery, no Popper. Don't assume parity with actual
Bootstrap docs/behaviour, especially for anything not covered below.

## What's in, what's not

- **Layout**: CSS Grid, not flexbox, for `.row`/`.col-*` (`.navbar` itself is
  flexbox — a single-row toolbar is a legitimate flex case). Nested `.row`
  uses `subgrid` where supported, as progressive enhancement only. `.col-*`
  spans are only meaningful relative to their own `.row`'s column count
  (12 by default) — you can't mix a 12-col span and a 5-col span in the same
  `.row`. For "N similar items, however many fit" layouts (card grids,
  feature lists) use `.grid` instead — `repeat(auto-fit, minmax(...))`,
  no fixed column count, no breakpoint classes, tune the minimum item width
  per instance with `style="--grid-min: 10rem"`.
- **Nav**: functional responsive nav + mobile toggle, vanilla JS
  (`src/js/nav-toggle.js`), no Bootstrap JS.
- **Modals**: native `<dialog>` (`src/js/dialog.js`), not a JS component.
- **Forms**: minimal, deliberately unopinionated base — expect to override it.
- **Buttons/cards**: no framework opinion at all — bring your own per project.
- **Tables**: real but optional (`src/css/tables.css`) — uncomment the
  `@import` in `src/css/theme.css` if a project needs one.
- **Icons**: inline SVG, no icon font.
- **No sidebars, no comments, no tags, no author archives, no `archive.php`,
  no `search.php`** — all confirmed rare-to-never in real usage; add them
  per-project if a project genuinely needs one.
- **WooCommerce**: not here at all. Ecommerce projects use the Understrap
  parent/child setup separately — this skeleton is for everything else.

## Design tokens

`src/css/tokens.css` — CSS custom properties (`--col-*`, `--fs-*`, `--space-*`,
etc.), no Sass variables. `src/build/generate-theme-json.js` reads this file
to produce `theme.json` so colors/font sizes show up in the block editor.

Breakpoints are the one exception — they live in `src/build/tokens.config.js`,
not `tokens.css`, because a CSS custom property can't be read inside an
`@media` condition. `src/css/nav.css` hand-duplicates the `lg` breakpoint in
a literal `@media` query for the same reason — keep it in sync with that
file if a breakpoint ever changes.

## Browser support

Modern evergreen only (last 2 versions of Chrome/Firefox/Safari/Edge, see
`.browserslistrc`). No IE11, no legacy fallback layer. Grid, subgrid
(progressive enhancement), `clamp()`, native `<dialog>`, `:has()`, and CSS
nesting are all used without hesitation.

## Build

```
pnpm install
npm run watch        # rebuilds theme-wide CSS/JS on save
npm run watch-bs     # same, plus browser-sync live reload (proxies localhost/)
npm run blocks:build # compiles every block's src/index.js
npm run blocks:start # watch mode for block JS
npm run dist         # one-off full build: css + js + blocks
npm run generate-theme-json   # regenerate theme.json from tokens.css
```

`npm run css` runs three steps: `generate-utilities.js` (produces
`src/css/utilities.css` — the breakpoint-suffixed grid/utility classes — and
`src/css/blocks.css`, a concatenation of `src/blocks/*.css`), then
PostCSS (`postcss-import` + `postcss-nesting` + `autoprefixer`), then
minification. This is entirely separate from block JS — `blocks:build` is
its own step, run by `@wordpress/scripts` against `webpack.config.js`, which
compiles each `blocks/{slug}/src/index.js` to `blocks/{slug}/build/index.js`.

## Adding a block

```
./add_block.sh
```

Prompts for a block name, then for each field: its name and type (text,
textarea, richtext, image, url, link, number, select, checkbox). Generates
`block.json` (attributes), `src/index.js` + `src/edit.js` (the editing UI,
using WordPress's own field components — no ACF, no `get_field()`), and
`render.php` (front-end template, reading straight from `$attributes`).
No registration step — `inc/blocks.php` auto-loads every `blocks/*/block.json`
it finds. Run `npm run blocks:build` after scaffolding, or the block won't
render correctly until `build/index.js` exists.

Not yet supported by `add_block.sh` directly: repeater, gallery,
relationship, post_object, file. Repeater and single-post fields have a
documented drop-in pattern instead — `blocks/_shared/RepeaterField.js` and
`blocks/_shared/PostTypePicker.js` (see the `blocks/_shared/` entry under
`CLAUDE.md`'s "File layout" for how to wire one into a generated `edit.js`
by hand). Relationship (a whole grid of posts via `WP_Query`) still has no
drop-in — build it by hand. The
**CBP Block Builder** plugin (`ChillibyteUK/cbp-block-builder`, wp-admin GUI,
local-only) covers repeater/gallery/post_type generation that `add_block.sh`
doesn't, if it's active on this environment.

If the block needs custom styles, add `src/blocks/{block-slug}.css`; it's
picked up automatically on the next `npm run css`, no registration step.
Load order for these is alphabetical by filename, not registration order.

`rm_block.sh` removes a block: the whole `blocks/{slug}` directory plus its
`src/blocks/{slug}.css` if present. Again, no registration to undo.
