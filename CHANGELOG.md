# Changelog

## Unreleased

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

## 0.1.0 - 2026-07-04

First public release.

- Widget: 16 functions, 4 one-click profiles, 17 languages (auto-detection, RTL
  for Arabic and Hebrew), read-aloud with point-and-read mode, text alignment
  left/center/right, page structure list, no tracking, localStorage only after
  interaction.
- Configurator at legilo.eu in 11 languages: live preview, feature toggles,
  bidirectional embed code, self-hosted download with the dyslexia font baked in.
- WordPress wrapper plugin (wordpress/legilo/).
- SEO: hreflang, sitemap.xml, robots.txt, OpenGraph image.
