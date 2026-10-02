# Changelog

## 0.4.0 - 2026-10-02

- Widget: no silent read-aloud anymore. Without a usable voice for the page
  language the read-aloud button is disabled and the panel explains why and
  that voices can be added free of charge in the system's speech settings
  (new texts in all 37 languages). features() reports available: false for
  tts, set("tts", ...) returns false. If the voice list turns out empty after
  read-aloud was switched on, it is switched off again (legilo:change with
  source "auto").
- Widget: new parameter ttscloud=1 (default 0). Read-aloud then prefers the
  browser's online voices where available (e.g. "Google US English" in
  Chrome): they come first in the voice selector and are the automatic
  choice, marked as online with a note that the text goes to the browser
  vendor; visitors can switch to a local voice at any time. Without online
  voices (e.g. Firefox, Safari) the local voices are used. Without ttscloud
  only local voices are used. No API key, no server, no costs: the online
  voices come from the browser itself.
- Widget: read-aloud marking also works with online voices. Google voices in
  Chrome send no word boundary events, so the word marker stayed empty; now
  online voices speak sentence by sentence and the spoken sentence is marked.
  Voices that do send word events (local voices, Edge online voices) still
  mark the single word.
- Widget: voices() marks online voices (online: true), values() reports
  voiceOnline, features() reports cloud for tts.
- Configurator: "Offer online voices" option with an explanation (11
  languages). API reference: voices, online voices and the no-voice state.
- WordPress plugin 0.1.3: "Offer online voices" setting.

## 0.3.0 - 2026-10-01

- Widget: read-aloud uses only local voices (localService). Cloud voices that
  send the text to the browser vendor (most "Google" voices in Chrome,
  "Online (Natural)" in Edge) are never used anymore, not even as a fallback;
  without a local voice for the page language read-aloud stays silent.
  Before, a missing local voice silently fell back to a cloud voice.
- Widget: better automatic voice choice among the local voices - quality
  names (Natural, Neural, Premium, Enhanced, Siri) first, then the system's
  default voice, then an exact region match.
- Widget: voice selector in the panel (below the speed chips, visible while
  read-aloud is on and the system offers more than one local voice); the
  choice is stored with the other settings. New labels in all 37 languages.
- Widget: Legilo.voices() and Legilo.setVoice(name); values() reports the
  voice in use, legilo:change reports a voice change as key "ttsvoice".
- Widget: fix - icon detection was too greedy. Every class starting with "fa"
  (fancybox-content, favorites, nav-fade, ...) counted as an icon, so font
  size and point-and-read skipped text in such elements. Icons are now
  recognized by whole class tokens only (fa, fas, far, fab, fal, fad, fat,
  fa-*, icon, icon-*, *-icon, glyphicon(-*), material-icons/-symbols); the
  CSS exclusion for the font functions follows the same rule.

## 0.2.0 - 2026-10-01

Page integration release: the widget now tells the page what it does, so
sites with their own controls, iframes or audio (e.g. 3D tours) can follow.

- Widget: DOM events on document (local only, nothing is sent anywhere).
  legilo:change fires on every change of a setting (panel, profile, reset,
  API, read-aloud speed, automatic end of "read page", and once after loading
  when stored settings or system preferences were applied); detail carries
  key, level, states, rate, values, source and profile. It fires only on real
  changes, so a handler may call Legilo.set() without causing a loop.
- Widget: legilo:open and legilo:close when the panel opens or closes, plus
  Legilo.isOpen().
- Widget: legilo:speechstart and legilo:speechend when the voice really starts
  and ends, exactly once per start; a text replacing a running one keeps
  speech "on" in between (e.g. for pausing background music).
- Widget: Legilo.values() returns the actual values behind the active
  settings - font scale, spacing values, font family, dyslexia font URL and
  @font-face CSS - so pages can carry them over into their own iframes.
- Widget: new parameter tts=both|read|hover chooses which read-aloud modes the
  button offers (e.g. tts=hover for 3D tours). Legilo.features() lists the
  offered modes as "allowed"; set() rejects modes that are not offered.
- Widget: font size and point-and-read also cover elements with their own
  text that are not in the fixed tag list, e.g. a div with a direct text node.
  Icons stay untouched (icon classes, <i>, SVG, icon-font glyphs).
- Widget: read-aloud skips hidden text (display:none, visibility:hidden, the
  hidden attribute, aria-hidden="true") and icon-font glyphs.
- Widget: fix - nested elements (e.g. a span inside a paragraph, a link inside
  that span) were scaled twice or more at larger font sizes. Original sizes
  are now all read before any element is scaled.
- Widget: legilo.js is cached for 1 hour instead of 24 hours; unknown
  parameters are ignored, so &v=0.2.0 works as a cache buster.
- Configurator: new "read-aloud modes" option in all 11 languages.
- API reference: new events section, values(), isOpen(), tts parameter.
- WordPress plugin 0.1.2: new "read-aloud modes" setting.

## 0.1.1 - 2026-10-01

- Widget: Legilo.speak(text, { interrupt }) and Legilo.stopSpeaking() - pages
  can announce their own texts through the read-aloud engine (chunking, voice,
  visitor speed). Speaks only while the visitor has turned read-aloud on,
  never uninvited; { interrupt: false } only speaks when nothing else plays.
- Widget: big cursor, reading guide and reading mask also work inside
  same-origin iframes (cursor style mirrored into the frame, pointer moves
  translated back into the top viewport; late or reloaded frames included).
- Widget: with hide=1 a click outside no longer closes the panel, so the site
  owner's own button toggles it reliably (closing via own button, panel X or
  Esc).
- Project page: sunny yellow redesign, carried over to /api and the legal
  notice; new OpenGraph image.
- Project page: "no sign-up, not even an email address" in the intro and FAQ;
  the WordPress hint now points to the official plugin directory (11
  languages).
- WordPress plugin 0.1.1: native settings page (position, colors, button,
  language, features, behavior) instead of a script URL field, with an
  optional import field for legilo.eu embed codes.

## 0.1.0 - 2026-07-05

First public release (GitHub release v0.1.0). Contains the initial version
from 2026-07-04 (see below) plus everything developed before publishing:

- Project page: new "check your website" section. Runs the Google PageSpeed
  Insights API (Lighthouse accessibility category) directly from the visitor's
  browser; no server involvement, honest wording (automated checks are a start,
  not proof of conformance). Full report: score, findings grouped like
  Lighthouse with the affected elements (snippet, selector, axe explanation),
  passed checks, manual-check list, desktop/mobile choice, and a branded
  print view for saving the report as PDF.
- Widget: page effects no longer apply to print (dark mode used to print dark
  pages); the widget itself is hidden in print.
- Widget: reading guide and reading mask now follow the finger on touch
  devices.
- Widget: point-and-read also speaks the focused element, so the mode works
  with the keyboard.
- Widget: profile toggles are announced via the live region (they were silent
  for screen readers).
- Widget: big cursor keeps click feedback - links and buttons get a large hand
  cursor instead of the arrow.
- Widget: panel is now a proper non-modal dialog (removed the misleading
  aria-modal and the focus trap; Esc and outside click still close it).
- Widget: tidier panel. Feature cards are compact single rows and only active
  features show their state, multi-level features indicate the current level
  with dots, the profile chips got a heading, and "hide for this session" is
  now a quiet text link instead of competing with the reset button.
- Widget: theming API. All panel building blocks are exposed via CSS
  ::part() names so site owners can restyle the widget from their own
  stylesheet without losing the Shadow-DOM isolation; commented template in
  docs/legilo-theme.css.
- Widget: small Legilo icon link in the panel footer so visitors can find the
  project behind the button.
- Widget: expert mode css=none. Loads the panel without built-in styling
  (light DOM plus a tiny functional layer) so site owners can build their own
  design from scratch; the configurator links a copyable nested CSS skeleton,
  and the project page itself ships that skeleton as a live reference.
- Widget: public identifiers renamed from a11yw-* to legilo-* (host and
  overlay ids, localStorage/sessionStorage keys, injected style id, font-size
  data attribute).
- Widget: read-aloud now highlights the spoken word on the page (CSS Custom
  Highlight API plus utterance boundary events, no DOM changes; browsers or
  voices without support simply read without the marker). Works for full-page
  reading and point-and-read.
- Widget: JavaScript API extended with Legilo.toggle() and Legilo.reset().
- Widget: programmatic function control - Legilo.set(key, level),
  Legilo.get(key) and Legilo.features() behave like panel clicks (applied,
  saved, announced), enabling fully custom UIs together with hide=1 and
  css=none. The API reference is now linked from the integration notes and
  the hide-button option on all 11 language pages, not just the footer.
- Project page: developer reference at /api - embed options, all URL
  parameters (generated from the option schema), feature keys, JavaScript API,
  theming and storage notes; linked from the footer and the README.
- Project page: friendlier configurator forms (colored card accents, calmer
  input styling with clear focus states, brand-colored checkboxes and slider).
- Project page: the "integration notes" card is gone - its content (embed
  overrides, language detection, CSP, keyboard, privacy-policy note, CSS
  skeleton) now lives on /api, and a compact "For developers" card below the
  embed tabs links there in all 11 languages. The css=none option links
  straight to the skeleton section on /api; the skeleton itself moved to
  config.php (legilo_css_skeleton) so the live preview and the docs share one
  source.
- Widget: new function "color blindness" (18th) - daltonization filters for
  red, green and blue weakness that shift confusable colors apart (single
  SVG color matrix per type, inline defs, works combined with the other
  color functions).
- Widget: read-aloud speed - slower/normal/faster chips appear while
  read-aloud is active; the choice is persisted with the other settings.
- Widget: system preferences as start values (only until the visitor picks
  something, never persisted without interaction): prefers-contrast: more
  starts in a high-contrast mode, prefers-color-scheme: dark starts dark -
  but only when the page itself is light, dark sites stay untouched.
- Widget: the panel now grows with its content and only caps at the viewport
  edge instead of a fixed 600 px, so it no longer scrolls unnecessarily; with
  an odd number of cards the last one spans the full width (general rule,
  works for every features= selection).
- Widget: nicer back button in the structure view (chip style matching the
  profile buttons, brand-colored hover).
- Project page: the widget panel opens automatically once when the visitor
  reaches the demo section; the demo got more material to test with (a link
  in the running text, a quote, five colored chips for the color functions).
- Project page: the URL embed tab now says it is the recommended way
  (updates arrive automatically), in all 11 languages.
- Configurator: the feature tiles are sortable (drag and drop, Alt+arrow
  keys) and their order IS the card order in the widget panel - the features
  parameter is order-sensitive now, pasting an embed code restores the order.
  Sortable tiles show a small grey grip handle; profiles and page structure
  have fixed spots in the panel (chips on top, structure at the bottom), so
  their tiles can only be toggled, not moved.
- Project page: logo and name in the header and footer now link to the home
  page of the current language.
- Configurator: changing any setting opens the preview panel right away so
  the change is visible (desktop only).
- Widget: profiles hide themselves when not all of their functions are
  configured - a bundle that could only half-apply would mislead.
- Widget: 20 new languages (37 total) - Bulgarian, Croatian, Czech, Danish,
  Estonian, Finnish, Greek, Hungarian, Irish, Latvian, Lithuanian, Maltese,
  Romanian, Slovak, Slovenian, Swedish, plus Indonesian, Vietnamese, Persian
  (right-to-left) and Thai. With lang=auto all languages are baked in
  (~36 KB gzipped); a fixed lang keeps the file small (~16 KB gzipped).
- Project page: the language switcher is sorted alphabetically by code, so
  visitors find their language faster.
- SEO: titles and intros now carry the category term people actually search
  ("accessibility widget" / "Barrierefreiheits-Widget") alongside the honest
  "reading aid" product term - naming the category, never claiming
  conformance. New FAQ entry positions Legilo against overlays like accessiBe
  and UserWay. JSON-LD structured data (SoftwareApplication with price 0 plus
  FAQPage) added to all 11 language pages.

### Initial version - 2026-07-04

- Widget: 16 functions, 4 one-click profiles, 17 languages (auto-detection, RTL
  for Arabic and Hebrew), read-aloud with point-and-read mode, text alignment
  left/center/right, page structure list, no tracking, localStorage only after
  interaction.
- Configurator at legilo.eu in 11 languages: live preview, feature toggles,
  bidirectional embed code, self-hosted download with the dyslexia font baked in.
- WordPress wrapper plugin (wordpress/legilo/).
- SEO: hreflang, sitemap.xml, robots.txt, OpenGraph image.
