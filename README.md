# cb-peoplesafe2027

Theme for peoplesafe.com: Peoplesafe and OKAlone consolidated into one WordPress
install, with three regions (UK, US, AU) served by Polylang Pro. It was forked
from `cb-skeleton2026` and shares its CSS/JS/build system; blocks and site
settings use native block.json/edit.js blocks and a plain Settings API page
instead of ACF. See "Adding a block" below.

## Regions

One install, one language per region: `en_GB` at `/uk/` (default), `en_US` at
`/us/`, `en_AU` at `/au/`, `en_CA` at `/ca/`. The site root `/` is a language-neutral hub where
visitors choose a region. The code is in `inc/regions.php` and `inc/regions/`,
and does nothing when Polylang is not active.

| Module | Does |
|---|---|
| `hreflang.php` | adds `x-default` (the site root) to every page with alternates |
| `mismatch.php` | a slug that exists in another region is a 404 |
| `routing.php` | merged post type paths, landing pages, front page, one canonical URL |
| `menu.php` | one menu, resolved to the visitor's region |
| `root.php` | serves the Root hub at `/` |
| `language-switcher.php` | the header region dropdown, with flags (`img/flags`, flag-icons, MIT) |

URL shape: `/uk/guides|whitepapers|brochures|infographics|video/{slug}/`,
`/uk/case-studies/{slug}/` and `/uk/stories/{slug}/` (term User story),
`/uk/partnerships/{slug}/` and `/uk/accreditations/{slug}/` (term Accreditation),
`/uk/news/{slug}/` and `/uk/blogs/{slug}/`, landing pages at `/uk/{slug}/`,
taxonomies under `/jurisdiction/`. Decisions: DEC-023 to DEC-027 in the project
tracker.

### Post types and taxonomies

Registered in `inc/posttypes.php` and `inc/taxonomies.php` from one table each
(add a row for a new one): Products, Case studies, Resources, Legislation,
Partners, Careers, Landing pages and People (non-public), with their taxonomies
(`resource_type`, `jurisdiction` and the filters). Resources, Case studies, Partners and Landing pages
register with `rewrite => false` because `routing.php` builds their paths (a term
can give an item its own path base: see `cb_peoplesafe2027_term_routes()`).
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

### Buttons

One system in `src/css/buttons.css`: `.button` plus `--primary`, `--outline`,
`--outline-white`, `--white` (set `--button-accent` on a banner for its text
colour) and `--small`. `.button-group` lays them out. Full width below md. The
core Buttons block gets the same look. Tokens are the `--button-*` ones.

### CTA buttons and the Home hero

`inc/cta-buttons.php` prints any block's row of buttons
(`cb_peoplesafe2027_cta_buttons()`), and `blocks/_shared/CtaButtonsField.js` is
the matching editor field, so Hero, Home hero, Product header and CTA banner
share one set of controls and one markup. In the editor each button has title and URL side by side (the URL suggests pages and posts as you type, takes plain text otherwise, and keeps same-site links as root-relative paths so they survive a move between environments), then dialog id and style side by side. A button either links or opens a
`<dialog>` by id (`data-dialog-target`, `src/js/dialog.js`). The Home hero is
`blocks/home-hero` (animation layers in `img/home-hero/`, CSS in
`src/blocks/home-hero.css`, reduced-motion safe).

URL fields use `blocks/_shared/SiteUrlInput.js`, a wrapper around core's
`URLInput` that closes its suggestion list when focus leaves the field or a
suggestion is chosen, and never opens it for a value changed from outside
(undo, a row move). Use it for any new URL field, not `URLInput` directly.

### Hero and breadcrumbs

`blocks/hero` is the page hero: breadcrumbs, title, intro and buttons on the
left, the image on the right (below md the image comes first). The title and the
image default to the page's own title and featured image, so most pages only
need an intro and buttons. Breadcrumbs are a theme template part
(`cb_peoplesafe2027_render_breadcrumbs()`, styled in `src/css/breadcrumbs.css`),
not a block, and start at the region's home.

The optional `label` is small blue text above the title, for product pages (it
replaces the legacy Product header block). When set, the label is the page's H1
and the title is rendered as an H2 at the H1 size, as on the live product pages;
with no label the title stays the H1 and nothing changes.

### Text and media

`blocks/text-and-media` is text beside an image or a video (or text only). Fields:
pre-title, title, content (nested paragraph, list and heading blocks), buttons, media type, media position (left or right),
width (half and half, or wide text and narrow media), text under the video and a
video transcript (a native `<details>`). A video is one pasted YouTube or Vimeo
link, shown as a poster behind a play button: `inc/video.php` works out the
provider, embed URL and poster (Vimeo's comes from its oEmbed endpoint, cached a
week, or upload a poster), and `src/js/video-facade.js` loads the player only
when it is clicked. By default ("Play the video": pop-out) it opens in a pop-out player, one shared native `<dialog>` that also stops Lenis, closes on Esc, the close button or a backdrop click, returns focus to the poster and removes the player so the video stops. Below md, or with "In place", it plays where the poster is. The embeds are trimmed: YouTube gets no annotations and the white progress bar (its title bar, related videos and branding cannot be switched off from the URL), Vimeo gets no title, byline, portrait, badge, Vimeo logo or picture-in-picture and the brand yellow controls (the logo and colour need a paid Vimeo plan on the video's owner). Below md the media comes first. It replaces the legacy
text-image, text-image-2024, text-video and text-video-2024 blocks.

The Ticks list style comes in two colours: Ticks (teal) and Ticks, orange (`is-style-check-orange`, which only changes `--tick-colour`); the tick list in Text and media and the Feature list have a Tick colour setting for the same choice.

An image is framed by default (rounded corners, shadow, white background); the **Frame the image** switch turns that off for transparent images (`framed`, class `text-and-media__image--plain`).

Text and media can also show a **tick list** in the media slot (Media: Tick list; short rich-text lines, rendered as a Ticks list), and supports a **background colour** through the block's native Color panel (the `surface` palette colour for the light grey band, with no field of its own), which together make the "Why choose Peoplesafe" bands on the Councils and Retail pages. The title stays in the text column.

### CTA banner

`blocks/cta-banner` is a full-width gradient band: colour (orange, pink, blue or
plain), title, text, buttons and an optional image that shows on the left from lg
up. The buttons are the shared CTA button group, using the white variant on the
gradients with the text colour taken from the colour chosen. It replaces the
legacy gradient-cta block (its "green" colour becomes blue). `page.php` adds no
container of its own, so the banner runs edge to edge; every other block draws
its own `.container`.

### Child page index

`blocks/child-page-index` lists the published pages directly under the current
page, in menu order then title. It is used on the Legal hub (`/uk/legal/`) and
needs no editing when a child is added, moved or renamed.

### Sliders and the Quote block

Every slider uses one shared wrapper (`src/js/slider.js`, built to `js/slider.min.js`) around Embla, which is vendored into `js/vendor/` (DEC-029). A block prints `<div class="slider" data-slider data-slider-label="...">` with a `.slider__viewport`, `.slider__track` and `.slider__slide` children, and calls `cb_peoplesafe2027_enqueue_slider()` from its `render.php`, so the code loads only on pages that have a slider. The wrapper adds the arrows, the dots, the carousel and slide ARIA, keyboard arrows, and `inert` on slides out of view. Slides per view and the gap are CSS (`--slides-per-view`, `--slider-gap` on the `.slider`, set per breakpoint). There is no autoplay, and movement is off for visitors who prefer reduced motion.

`blocks/quote` is one or more quotes (quote, attribution, optional logo, optional photo). One quote is a static panel; two or more become a slider. The panel's gradient circle cycles blue, orange, pink by position. It replaces the legacy big-quote, big-quote-slider, big-quote-slider-2024 and case-study-quotes-2024 blocks.

### Related content

`blocks/related-content` shows cards for products, case studies, resources or posts. Show either items the editor picks one by one (type, then item) or the latest of one type (1 to 24, case studies can be limited to a sector). The title defaults from the type ("Related products") and can be set or hidden. Cards (image, title, a trimmed excerpt; a product uses its product header intro when it has one) sit in the shared slider, so they are plain cards when they all fit and arrows and dots when they do not: one on a phone, two from md, three from lg, four from lg when every card is a product. The whole card is the link. It replaces the legacy related-products, related-case-studies, related-resources, latest-posts, guides and latest-posts-author blocks; the case-user-combo block is handled separately.

### FAQ

`blocks/faq` is a title (an h2, optional) and questions with rich answers (bold, italic, links and paragraphs). It renders an accordion of native `<details>` elements (`src/css/accordion.css`, no JavaScript), closed to begin with, one open at a time within a block, with each question as an h3 inside its summary. Any number of FAQ blocks can sit on a page: each one queues its questions with `cb_peoplesafe2027_queue_faq_schema()` (`inc/faq.php`), and a single `FAQPage` JSON-LD script is printed in the footer holding all of them, with a repeated question listed once. Answers keep only the tags Google accepts. It replaces the legacy faq-block (the old `queue_faq_schema()` collector in `inc/utilities.php` is gone).

### Logo slider

`blocks/logo-slider` is a centred title (default "Some of our clients") over a row of client logos in the shared slider: two across on a phone, three from md, four from lg, five from xl. When they all fit it is a still row with no controls; when they do not it loops. It drifts along by itself every 3 seconds by default (a per-block "Rotate automatically" setting), with no arrows, dots or pause button (`data-slider-controls="none"` on the shared slider); autoplay pauses while the pointer or focus is on it, off screen or in a hidden tab, and there is none for reduced motion. Each logo sits in a square white card with a 2px soft border and rounded corners, filling about 70% of it, as on the live site; it works best with square logo images. The logos are chosen as a gallery (several at once in the media library, shown as thumbnails with move and remove buttons). An optional "View our case studies" button goes to all case studies, or to one sector with `?sector=slug`, which `cb_peoplesafe2027_routing_archives()` applies to the case study archive. Alt text comes from the media library. It replaces the legacy logo-slider, clients-slider and featured-in blocks.

### Side tabs

`blocks/side-tabs` is a title and intro (both optional) over 2 to 6 tabs (title, text, an optional bulleted list, an optional button, an image). From md up the tabs are stacked beside a large panel of images; below md only the tabs show. The tabs are native `<details>` elements sharing a `name`, so one is open at a time (the first to begin with) and they open and close with the same height animation as the FAQ. `src/js/side-tabs.js` shows the image of the open tab and stops the open tab being closed by clicking it. It is the legacy side-tab-group block; the top tab group is a separate block (Top tabs, not yet built).

### Top tabs

`blocks/top-tabs` is two to five tabs along the top (each a round icon over a title), with the open tab's panel below: image on the left, then a pre-title, an H2, text and an outline button. Each tab has `title`, `icon`, `preTitle`, `heading`, `content`, `linkText`/`link`/`linkTarget` and `image`. The pills are buttons with `aria-expanded`/`aria-controls`; `src/js/top-tabs.js` shows the open panel and hides the rest (the first panel shows without JavaScript). From lg up the pills share one row (`--tabs` columns) and the panel spans it; below lg they stack with each panel under its pill and a chevron. The panel fades in over `--transition-slow` (not for reduced motion). It replaces the legacy top tab group (the Side tabs block replaced the side group).

### Caption slider

`blocks/caption-slider` is a slider of white cards (an icon in a pale orange circle over a caption, the whole card a link) in the shared slider: two across on a phone, up to five on a wide screen, looping with dots and arrows, and a still row when everything fits. Attributes: `title` (optional) and `cards` (`image`, `caption`, `link`, `linkTarget`). It replaces the legacy caption slider ("Industries using MySOS").

### Service status

A list of services, each operational, degraded or down, under an overall banner worked out from them: every service operational is "Fully operational", every service down is "Down", anything else is "Partly degraded". The "Last updated" line is the page's own modified date, so changing a status and saving the page is the whole workflow. State is written as text as well as shown in colour (teal, yellow, orange). An optional Message field (bold, italic, links; Enter starts a new paragraph) shows above the banner for an incident notice. It renders nothing without services.

### Feature list

`blocks/feature-list` is an optional pre-title and a title over a checklist of terms, each with a short definition (links allowed), as ticks in one column, or two from lg. It supports a background colour through the native Color panel. It replaces the legacy feature-list block and the hand-built native "benefits" lists (the NHS page).

### Stats counter

`blocks/stats-counter` is a title (default "Protection you can count on") over 2 to 4 stats (what goes before the number, the number, what goes after it, a label, a description) and an optional image (the standard cut-out when empty, or hidden). Each number is printed at its final value; `src/js/stats-counter.js` counts it up from 0 over 2 seconds the first time it is 60% in view, and stops on the exact value (decimals supported). It does nothing for reduced motion, and a visually hidden copy of the final value is what screen readers read. The numbers are `--col-orange-400`, as on the live site. It replaces the legacy stat-spinner block; the empty spinner-2024 leftover is dropped.

### Sector cards and the Sector group

`blocks/sector-cards` lists every page directly under the page it sits on (the Sectors page), in page order, as cards: each card is 16:9 (the image three parts, the title one, as on the live site), with the page's featured image cropped to fill its part (about 2.4:1) and a curved bottom edge (the live site's SVG mask), then its title. The Public sector / Private sector buttons come from `src/js/sector-filter.js` and are hidden until it runs (without it every card shows). Whether a sector is public, private or both is the page's `cb_sector_group` meta (`inc/sectors.php`), edited in a "Sector group" panel in the page sidebar that appears only on pages directly under the Sectors page (`blocks/_editor-sector-group`, loaded like the Lede format). The hero cut-out lives in the Hero block's own image field, so the featured image is free to be the card photo. It replaces the legacy sector-nav block, whose public/private list was hard-coded in the template.

### Image columns

`blocks/image-columns` is two to five images side by side, each with an optional bold title and centred text below it (`items`: image, title and text; the column count follows the number of items, five across from lg). Images show at their natural size, never stretched, so they are uploaded at the size wanted; alt text comes from the media library. The text is a shared RichField limited to bold, italic, links and text colour (`mark` is allowed through `wp_kses` for the colour). It has an optional background colour (`supports.color.background`) and no title or intro of its own: put an ordinary heading and paragraph above it. Used for the three-image section on Retail and the five values on Careers.

### Benefits panel

`blocks/benefits-panel` is a tinted panel (the block's own background colour, a pale tint by default) with a title and introduction over a 12-column mosaic, and an optional footnote under it. Each of the `items` is a benefit card (icon, title, text) or a photo and is a quarter, a third or half of the row wide (`span` 3, 4 or 6), optionally starting a quarter in (`offset`); a card with `stack` set sits under the card before it, so two small cards share one tile. Photos fill their tile; one column on a phone. Built for the Careers page, where the live page had it as hand-written HTML.

### Principle tiles

`blocks/principle-tiles` is a centred title and introduction over two to six colour tiles, each with a heart and a short line of text (`tiles`: `text`, `colour` of yellow, teal, blue, pink or orange). Six across from lg, three from md, one on a phone. The colours are the brand tokens, and the text is white on them and black on yellow for contrast. Built for "How We Think About Customers" on Careers, where the live page had it as hand-written HTML.

### Case studies and stories

Case studies and user stories are one post type, `case_study`: a story has the User story case study type. Paths come from `routing.php`: `/uk/case-studies/{slug}/` and `/uk/stories/{slug}/`. Sectors are the `sector` taxonomy; what a story was about (verbal abuse, medical, entrapment) is the `incident` taxonomy, translatable in code (`inc/taxonomies.php`). The featured image is the customer's logo, and the excerpt is the short description shown on the cards (the live site's hover text).

`single-case_study.php` shows a Hero (title, breadcrumbs, the logo) over the post's own blocks. `archive-case_study.php` serves both archives: `/uk/case-studies/` is a grid of cards (12 a page) with a sector filter (`?sector=`), and `/uk/stories/` is an intro, a video and the incident types as tiles, with `?incident=` listing those stories in full. The intro text, the stories text under the video and the video address are in Site-Wide Settings, Case studies and stories (`inc/options.php`); the page styling is `src/blocks/case-hub.css`.

`blocks/case-story-pair` is two or three chosen case studies as cards beside a navy panel that moves through chosen user stories (up to six) in the shared slider, one story in full at a time (`caseStudies` and `stories` hold post IDs; the two titles default to Case Studies and User Stories). It replaces the legacy case-user-combo block on the NHS, Councils and Utilities sector pages.

The pages are built from the clone by `checks/build_case_studies.py` (outside the theme): the legacy quotes, calls to action, related products, file downloads and feature lists become our blocks, and the images and PDFs are copied across.

### Pull quote

`blocks/pullquote` is one large quote on a rounded coloured panel (`theme`: yellow, navy or light) with a big translucent opening quote mark and the attribution (`attribution`) bold on the right. It is for the quotes in the middle of a case study (the live site's big quote block); the Quote block is the one with logos, photos and a slider. The case studies importer (`checks/build_case_studies.py`) makes the live big quotes into Pull quotes.

### Pushthrough and Newsletter signup

- **Pushthrough** (`blocks/pushthrough`): a call to action inside an article, at the width of the text. Layout "panel" (label, title, text, a line of short facts, button) or "strip" (title and text beside the button), in navy, teal, orange or light. The title is an `h3`, so it never joins an article's Quick links. Buttons are the shared CTA buttons.
- **Newsletter signup** (`blocks/newsletter-signup`): a teal gradient panel with a title, text, an email box and a Sign Up button, and the illustration on the right. Submitting opens the signup page (`/newsletter-signup/` in the visitor's region, or the address set on the block) with `?email=`; `src/js/form-frame.js` passes that to the Pardot form on that page. The signup page is a draft until it has a form address (a Form block needs a Pardot URL).

### List styles

The core List block has five styles: Ticks, Ticks orange, Ticks two columns, Two columns and Numbered steps (circled numbers, a bold first line when an item starts with bold text). They are registered in `inc/blocks.php`; the look is in `src/css/typography.css`.

### Blogs, news and single posts

- Blogs and News are posts (News is a post in the News category), at `/uk/blogs/{slug}/` and `/uk/news/{slug}/`; `single.php` shows one. `archive.php` lists them as a grid of cards (a lead card, two halves, then thirds), with a search box, Load more and (blogs) category pills. The cards are drawn in `inc/posts-index.php`, which also serves `cb/v1/posts` (stream, region, category, q, offset, lead) so the first page and every batch match; `src/js/posts-index.js` wires the search, pills and button. Without JavaScript the pills are `?category=` links and the pages are numbered.
- The author is a Person post picked in the Author box on the post (meta `cb_author`); it is not shown on the page. It feeds the schema, and a post with none is credited to the organisation. Authors have the "Authors" team term so they are easy to tell from the senior management on the team page.
- A single post has a meta row (category, date, reading time, author), a 9/3 layout with a sticky Quick links sidebar of its h2s (two or more; `src/js/post-toc.js` marks the one in view), related products or resources at the full width under the article, the next and previous posts, the newsletter signup and three related posts (the same kind, sharing the most categories, topped up with the newest).
- Share (`inc/share.php`, `src/js/share.js`): a Share button in the post's meta row opens the site's native dialog with X, LinkedIn, Facebook, WhatsApp, email, Copy link and, on phones, the device's own share sheet. Each network is a plain link to its share address: no plugin and nothing loaded from them until chosen. `cb_peoplesafe2027_share_button()` and `cb_peoplesafe2027_share_dialog( $url, $title )` can go on any template.
- Schema (`inc/posts.php`): BlogPosting or NewsArticle with the section, author and time required; the author as a Person; CollectionPage and ItemList on the listings. The News listing is made indexable and given its own canonical address (Yoast treats it as the News category). The listings' SEO titles and descriptions are in Site-Wide Settings.

### Knowledge Hub

`/uk/resources/` is `archive-resource.php` drawing `inc/knowledge-hub.php` (the other paths under it, such as `/guides/`, stay listings). From the top: the Hero with the introduction (Site-Wide Settings), a search over everything (`cb/v1/posts` with `stream=all`: blogs, news, resources and case studies, each card badged with its kind and the matched words highlighted; the rest of the page steps aside while a search is showing), a tile for each kind of content with how many there are, the latest three across blogs, news, guides and whitepapers, a row of the newest four guides, videos, infographics, whitepapers and case studies, each with View all, and the newsletter signup. The schema is a CollectionPage whose list is the tiles. Its SEO title and description are in Site-Wide Settings.

### Entrance animations

Put `data-animate="fade-up"`, `"fade-left"` or `"fade-right"` on any element and it
fades in once, the first time it scrolls into view (`src/js/scroll-animate.js`,
`src/css/animate.css`). Nothing is hidden without JavaScript or with reduced
motion, and below md the sideways ones rise instead. Text and media uses it:
the text and the media slide in from their own sides.

### Smooth scrolling and GSAP

Lenis (smooth scrolling) is on every page; GSAP and ScrollTrigger load only on
pages whose block calls `cb_peoplesafe2027_enqueue_motion()`, and then expose
`window.cbMotion` (`{ gsap, ScrollTrigger, mm }`, `src/js/motion.js`). Use
`mm.add('(prefers-reduced-motion: no-preference)', ...)` for every GSAP
animation. Prefer CSS where it does the job. All three libraries are served from
`js/vendor/`, copied from `node_modules` by `npm run vendor` (which `npm run
dist` runs first); versions are in `package.json`. Nothing loads from a CDN.

Things Lenis changes, so you know where to look:

- **An element with its own scrollbar** (a long menu, a dialog body) needs
  `data-lenis-prevent`, or the page scrolls instead. The mobile menu has it.
- **Anything that locks the page** must call `window.cbLenis.stop()` and
  `.start()`. `dialog.js` does. A consent banner or other overlay that locks
  scrolling will need the same.
- **In-page links and the skip link** are handled in `smooth-scroll.js`, so the
  page glides and keyboard focus still moves to the target. Don't set
  `scroll-behavior: smooth` in CSS: it fights Lenis.
- **Reduced motion** turns Lenis off; touch devices keep native scrolling.
- Sticky and fixed elements, `IntersectionObserver` and Ctrl+F are unaffected.
- Iframes ignore the wheel while the page is smooth scrolling (Lenis's own rule).

### Third-party requests

The theme loads no libraries, fonts or icons from a CDN. What still reaches
another site: the Trustpilot script (its own widget; it cannot be self-hosted),
the YouTube or Vimeo player once someone presses play, and Google Analytics and
Tag Manager if their IDs are set in Site-Wide Settings. Video posters are saved
on this site (`uploads/cb-video-posters`) so a page with a video asks the
provider for nothing until play.

### Temporary page list

`blocks/temp-page-list` lists the region's pages (nested) and the other content
types on the front end, for logged-in editors only, until the real navigation
exists. It is on the four region homes. Delete the block from those pages and
the `blocks/temp-page-list` folder (and `src/blocks/temp-page-list.css`) before
launch.

### Style guide

`style-guide.php` is a page template ("Style Guide") that shows every token in
`tokens.css` as it renders, plus headings, text and buttons. It reads
`tokens.css`, so it cannot drift. Editors only (anyone else sees the 404) and
always `noindex`, so it is safe on live. Add a section when a component or
block is built. The local page is `/uk/style-guide/`.

### Needs the cbp-peoplesafe plugin

Polylang decides the `/` to `/uk/` redirect before the theme loads, and the Root
hub content type has to exist whatever the theme. Both are in the
`cbp-peoplesafe` plugin (`ChillibyteUK/cbp-peoplesafe`). Deploy and activate it
with the theme.

### Polylang Pro settings

- Languages: UK (`en_GB`, default), US (`en_US`), AU (`en_AU`), CA (`en_CA`); directory URLs;
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

## Links, block spacing and image alt text

Content links use `--col-link` (the brand yellow darkened, `--col-yellow-600`: 5.0:1 on white and 4.6:1 on the page background, AA needs 4.5:1; 4.2:1 against black body text, so they stand out without an underline), a step heavier than the light body text, and underline on hover. The bright yellow stays for buttons and fills. Every content block pads itself top and bottom with `--block-padding` (90px), so the gaps between blocks are even. Image alt text comes only from the media library (there is no alt field on the blocks); an image with no alt there renders `alt=""`, so decorative images stay silent and meaningful ones need alt text added in the media library.

## Block patterns

Every file in `patterns/` is a block pattern, registered by `cb_peoplesafe2027_register_patterns()` in `inc/blocks.php` (WordPress only does this by itself for block themes). A file starts with a header comment (Title, Slug, Description, Categories, Post Types, Keywords, Viewport Width) followed by the pattern's block markup. Patterns appear under "Peoplesafe pages" in the inserter and are starting points: once inserted they are ordinary blocks that can be edited freely. `patterns/sector-page.php` is a sector page: Hero, two Text and media blocks, a Quote, Related content (the latest case studies) and a CTA banner. To add one, copy that file, change the header and the markup.

## Editor fields

Blocks are edited through form fields. Rich text in a field (`blocks/_shared/RichField.js`) is one plain rich-text field with its format buttons in a floating toolbar, never WordPress's multi-line (`multiline`) mode, which re-parses the field on every keystroke and sends the cursor back to the start. Enter starts a new line, stored as `<br>`, and the page turns each line into a paragraph (`cb_peoplesafe2027_rich_paragraphs()`). A list that is only plain text is a "one item per line" box (`cb_peoplesafe2027_lines_to_items()`). Images use the shared `ImagePicker`, and a gallery of images (the Logo slider) uses the media library's multiple selection.

## Native first, custom blocks second

Use native WordPress blocks (paragraph, heading, list, image, buttons) wherever they do the job. Plain text pages, such as the legal pages, are a Hero plus core blocks, with no entrance animation. Build a custom block only for content that needs consistent styling and that editors would find confusing or easy to get wrong with core blocks: the hero, CTA banner, FAQ, product header and so on. Layout blocks such as Columns are the exception: prefer one of our blocks over a hand-built column layout.

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
