=== Legilo - Reading Aid and Accessibility Widget ===
Contributors: legiloeu
Tags: accessibility, reading aid, readability, dyslexia, text to speech
Requires at least: 6.3
Tested up to: 7.1
Stable tag: 0.1.1
Requires PHP: 7.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Adds the free Legilo reading-aid widget to your website: contrast, font size, reading mask, read-aloud and more, in 37 languages.

== Description ==

Legilo is a free reading-aid widget: one button that lets your visitors adjust
contrast, font size, spacing, fonts (including a dyslexia font), a reading mask,
read-aloud and more. 37 languages with automatic detection, no tracking, no
cookies, no account.

Configure everything under Settings, Legilo: position, colors, button style,
language and the set of features. The plugin builds the widget URL from these
settings itself; it always loads the script from legilo.eu.

Honest positioning: Legilo is a reading aid. It does NOT make your website
conform to WCAG, EN 301 549 or national accessibility laws - conformance is
created in your website's source code, not by a widget.

== Installation ==

1. Install and activate the plugin.
2. Optional: adjust position, colors, language and features under Settings,
   Legilo. Without configuration the default widget is used.
3. Optional: try out settings with a live preview at https://legilo.eu and
   paste the embed code into the import field on the settings page; only the
   settings are taken over from it.

== External Services ==

This plugin embeds the Legilo widget script from the third-party service
legilo.eu. When a page is viewed, the visitor's browser requests that script
from legilo.eu; as with any web request, this technically transmits the
visitor's IP address and user agent to that server. No cookies are set, no
personal data is stored or shared, and nothing else is sent. Service provider:
Stefan Puergstaller, https://legilo.eu/imprint (legal notice).

== Screenshots ==

1. The widget panel on a website: profiles, font size, contrast, reading mask, read-aloud and more.
2. The settings page: import from legilo.eu, position, colors, button, language, features and behavior.

== Frequently Asked Questions ==

= Does this make my site accessible? =
No. Legilo is a comfort extra for your visitors, not a compliance tool.

= Does it load anything from third parties? =
Only the Legilo script itself from legilo.eu. The widget sets no cookies and
sends no data anywhere.

= Can I self-host the script instead? =
Yes, for developers: download your configured legilo.js from legilo.eu, put it
on your own server and point the plugin to it in code:
`add_filter('legilo_script_url', function () { return 'https://your-site.example/legilo.js'; });`
Then no request to legilo.eu is made at all.

== Changelog ==

= 0.1.1 =
* Settings page with native fields (position, colors, button, language,
  features, behavior) instead of a script URL field; the script URL is now
  built by the plugin and always points to legilo.eu.
* Optional import field: paste a legilo.eu embed code to fill the settings.
* Self-hosting moved to the legilo_script_url filter.
* Requires PHP 7.0.

= 0.1.0 =
* First release.
