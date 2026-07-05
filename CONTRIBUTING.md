# Contributing

Thanks for helping! The most valuable contributions right now:

## Translations (most wanted)

All translations were machine-generated and need native review:

- Widget languages: `widget/lang/<code>.json` (especially ar, he, zh, ja, ko, hi)
- Configurator pages: `translations/<code>.php`

Fix wording directly and open a pull request - even single-string fixes are
welcome. New widget language: copy `widget/lang/en.json`, translate it, add the
code to `A11YW_LANGS` and `a11yw_lang_names()` in `config.php`.

## Code

- Vanilla JS (ES5 style, no build step) and plain procedural PHP. No
  dependencies, no frameworks.
- ASCII punctuation only in code and strings: no em dashes, no typographic
  quotes, no ellipsis character. Accented letters in translations are fine.
- Do NOT copy code from GPL projects; this project is MIT.
- Test locally: any Apache + PHP with mod_rewrite; `php -l` for syntax,
  `node --check widget/widget.js` for the widget.

## Positioning (non-negotiable)

Legilo is a reading aid. Never describe it as making a website conform to
WCAG, EN 301 549 or national accessibility laws - not in code comments, UI
strings, or documentation. Pull requests with compliance claims will be asked
to reword.
