<?php
/**
 * API- und Optionen-Referenz (/api), englisch (Entwickler-Doku).
 * Parameter-Tabelle und Feature-Liste werden dynamisch aus config.php
 * bzw. widget/lang/en.json erzeugt - eine Quelle der Wahrheit.
 */
if (!function_exists('legilo_schema')) {
    require __DIR__ . '/config.php';
}
$brand = LEGILO_BRAND;
$baseUrl = legilo_base_url();
$brandJs = strtolower($brand) . '.js';
$schema = legilo_schema();
$enLang = json_decode(file_get_contents(__DIR__ . '/widget/lang/en.json'), true);
$featureLabels = $enLang['f'];

/** Wertebereich eines Schema-Eintrags menschenlesbar machen. */
function legilo_api_values($def) {
    switch ($def['type']) {
        case 'enum': return implode(' | ', $def['values']);
        case 'int': return $def['min'] . ' - ' . $def['max'];
        case 'bool': return '0 | 1';
        case 'hex': return 'hex color without #, e.g. e05263';
        case 'list': return 'comma-separated feature keys in panel order (see below)';
        case 'url': return 'https URL or absolute path';
    }
    return '';
}
function legilo_api_default($def) {
    if (is_array($def['default'])) return 'all features';
    if ($def['default'] === '') return '(empty)';
    return (string)$def['default'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#ffd35c">
<meta name="description" content="<?php echo htmlspecialchars($brand); ?> widget reference: embed options, all URL parameters, JavaScript API and theming.">
<link rel="icon" type="image/svg+xml" href="<?php echo htmlspecialchars($baseUrl); ?>/assets/favicon.svg">
<link rel="canonical" href="<?php echo htmlspecialchars($baseUrl); ?>/api">
<title><?php echo htmlspecialchars($brand); ?> API and options</title>
<style>
    * { box-sizing: border-box; }
    /* Gleiche Optik wie die Projektseite: Sonnengelb-Kopf, runde Karten, Pill-Formen */
    body { margin: 0; font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #17253a; background: #fbf7ef; }
    a { color: #14406b; }
    :focus-visible { outline: 3px solid #b56a00; outline-offset: 2px; border-radius: 6px; }
    header { background: #ffd35c; padding: 18px 28px; border-radius: 0 0 20px 20px; }
    header a { color: #17253a; text-decoration: none; font-weight: 800; font-size: 16px; }
    main { max-width: 860px; margin: 0 auto; padding: 30px 28px 60px; }
    .card { background: #fff; border: 1.5px solid #ece3d1; border-radius: 18px; padding: 22px 26px; margin-bottom: 18px; }
    h1 { font-weight: 800; letter-spacing: -0.02em; font-size: 28px; margin: 0 0 6px; }
    h2 { font-weight: 800; letter-spacing: -0.02em; font-size: 18px; margin: 0 0 12px; }
    p, li { font-size: 14px; line-height: 1.65; }
    p { margin: 0 0 10px; }
    .muted { color: #5a6579; font-size: 13px; }
    pre { background: #17253a; color: #d7e3f0; border-radius: 12px; padding: 12px 14px; font-family: Consolas, Monaco, monospace; font-size: 12.5px; line-height: 1.55; overflow-x: auto; }
    code { font-family: Consolas, Monaco, monospace; font-size: 13px; background: #f0ead9; border-radius: 6px; padding: 1px 5px; }
    pre code { background: none; padding: 0; }
    table { border-collapse: collapse; width: 100%; font-size: 13.5px; }
    th, td { border: 1px solid #e3dac6; padding: 7px 10px; text-align: left; vertical-align: top; }
    th { background: #f6efdf; }
    td code { white-space: nowrap; }
    .tablewrap { overflow-x: auto; }
</style>
</head>
<body>
<header><a href="<?php echo htmlspecialchars($baseUrl); ?>/">&larr; <?php echo htmlspecialchars($brand); ?></a></header>
<main>
    <h1><?php echo htmlspecialchars($brand); ?> API and options</h1>
    <p class="muted">Developer reference. The configurator at <a href="<?php echo htmlspecialchars($baseUrl); ?>/">legilo.eu</a>
    generates all of this for you - this page is for fine-tuning by hand.</p>

    <div class="card">
        <h2>Embedding</h2>
        <p>Three equivalent ways to configure the widget. Priority:
        <code>window.<?php echo $brand; ?>Config</code> over <code>data-*</code> over URL parameters.</p>
<pre><code><?php echo htmlspecialchars(
'<!-- A) URL parameters -->
<scr' . 'ipt src="' . $baseUrl . '/' . $brandJs . '?color=e05263&pos=bl" defer></scr' . 'ipt>

<!-- B) data-* attributes -->
<scr' . 'ipt src="' . $baseUrl . '/' . $brandJs . '" data-color="e05263" data-pos="bl" defer></scr' . 'ipt>

<!-- C) config object before the script -->
<scr' . 'ipt>window.' . $brand . 'Config = { color: "e05263", pos: "bl" };</scr' . 'ipt>
<scr' . 'ipt src="' . $baseUrl . '/' . $brandJs . '" defer></scr' . 'ipt>'); ?></code></pre>
        <p>The download variant from the configurator ships the same file with your
        configuration and the dyslexia font baked in and runs without this server.</p>
    </div>

    <div class="card">
        <h2>Integration notes</h2>
        <p><strong>Language:</strong> with <code>lang=auto</code> (default) the widget follows the
        page's <code>lang</code> attribute, then the browser languages, falling back to English -
        37 widget languages are baked in. A fixed <code>lang</code> always wins, ships only
        that language plus English and keeps the file small.</p>
        <p><strong>Content Security Policy:</strong> on sites with a strict CSP, allow
        <code>style-src 'unsafe-inline'</code> (the widget injects its page effects as one
        style element) and <code>font-src data:</code> or this host (dyslexia font).</p>
        <p><strong>Keyboard:</strong> the panel itself is keyboard operable - Tab moves through
        the dialog, Esc closes it. With <code>hotkey=1</code>, visitors can open the panel via
        <kbd>Alt+Shift+A</kbd>.</p>
        <p><strong>Privacy policy:</strong> the widget stores visitor choices in
        <code>localStorage</code> only after active interaction and sends nothing to any server;
        a short mention in your privacy policy is enough.</p>
    </div>

    <div class="card">
        <h2>URL parameters</h2>
        <div class="tablewrap">
        <table>
            <tr><th>Parameter</th><th>Values</th><th>Default</th></tr>
            <?php foreach ($schema as $key => $def): ?>
            <tr>
                <td><code><?php echo $key; ?></code></td>
                <td><?php echo htmlspecialchars(legilo_api_values($def)); ?></td>
                <td><code><?php echo htmlspecialchars(legilo_api_default($def)); ?></code></td>
            </tr>
            <?php endforeach; ?>
        </table>
        </div>
        <p class="muted" style="margin-top:10px">Notes: <code>hide=1</code> hides the launcher (open the panel via the
        JavaScript API instead). <code>css=none</code> loads the panel unstyled in light DOM for fully
        custom CSS (skeleton in the configurator). <code>statement</code> links your accessibility
        statement in the panel footer. <code>tts</code> sets which read-aloud modes the button offers:
        <code>both</code> cycles off, "Read page", "Point &amp; read"; <code>read</code> or
        <code>hover</code> offer only one of them (e.g. <code>tts=hover</code> for 3D tours, where
        reading the whole page makes no sense). Unknown parameters are ignored, so
        <code>&amp;v=<?php echo LEGILO_VERSION; ?></code> works as a cache buster: the script is
        cached for one hour in the browser.</p>
    </div>

    <div class="card">
        <h2>Feature keys</h2>
        <p>For the <code>features</code> parameter (default: all). The order of the keys is the
        order of the cards in the panel. Profiles only appear when all of their functions are
        included: vision needs fontsize/links/focus/cursor, motion needs animations/saturation,
        focus needs mask/animations, dyslexia needs font/spacing/guide.</p>
        <div class="tablewrap">
        <table>
            <tr><th>Key</th><th>Function</th></tr>
            <?php foreach (LEGILO_FEATURES as $fk): if (!isset($featureLabels[$fk])) continue; ?>
            <tr><td><code><?php echo $fk; ?></code></td><td><?php echo htmlspecialchars($featureLabels[$fk]); ?></td></tr>
            <?php endforeach; ?>
        </table>
        </div>
    </div>

    <div class="card">
        <h2>JavaScript API</h2>
        <p>One global, available after the script has loaded:</p>
<pre><code><?php echo htmlspecialchars(
$brand . '.open()             // open the panel
' . $brand . '.close()            // close the panel
' . $brand . '.toggle()           // open or close, depending on state
' . $brand . '.isOpen()           // true while the panel is open
' . $brand . '.reset()            // reset all visitor settings (like the panel button)
' . $brand . '.set(key, level)    // activate a function programmatically, e.g. set("contrast", 1)
' . $brand . '.get(key)           // current level of a function (0 = off), undefined if not configured
' . $brand . '.values()           // actual values of the active settings, see below
' . $brand . '.speak(text, opts)  // read out a custom text, e.g. speak("Kitchen", { interrupt: false })
' . $brand . '.stopSpeaking()     // stop any running speech output
' . $brand . '.features()         // [{ key, levels, state }, ...] for building your own UI
' . $brand . '.destroy()          // remove the widget from the page entirely
' . $brand . '.version            // version string, e.g. "' . LEGILO_VERSION . '"'); ?></code></pre>
        <p><code>set()</code> behaves exactly like a click in the panel: the level is clamped to the
        function's range (see the feature table: most functions have 2 levels, multi-level ones up
        to 4), the change is applied, saved and announced to screen readers. Levels above the
        maximum are clamped, unknown or unconfigured keys return <code>false</code>. A read-aloud
        mode that the <code>tts</code> parameter does not offer also returns <code>false</code>;
        <code>features()</code> lists the offered modes for <code>tts</code> as <code>allowed</code>.</p>
<pre><code><?php echo htmlspecialchars(
'// Example: your own dark mode switch, widget embedded with hide=1
' . $brand . '.set("contrast", ' . $brand . '.get("contrast") === 1 ? 0 : 1);'); ?></code></pre>
        <p><code>speak()</code> lets your page announce its own texts through the widget's
        read-aloud engine: sentence chunking, voice selection and the visitor's speed setting
        are applied automatically. It only speaks while the visitor has turned on read-aloud
        in the panel and returns <code>false</code> otherwise, so the page can offer
        announcements without ever starting speech uninvited. By default a call interrupts
        running speech; pass <code>{ interrupt: false }</code> for low-priority announcements
        that should only be spoken when nothing else is playing. Useful where the content is
        not readable text, e.g. a 3D tour announcing rooms or info tags on selection.</p>
        <p>Note that the read-aloud mode "Read page" runs once: it switches itself off when
        the page has been read, and an interrupting announcement also ends it. After that
        <code>speak()</code> stays silent. For ongoing announcements the visitor should choose
        "Point &amp; read", which stays active until turned off (embed with
        <code>tts=hover</code> to offer only that mode). <code>speak()</code> exists
        since version 0.1.1; check for it before calling, e.g.
        <code>if (<?php echo htmlspecialchars($brand); ?>.speak) { ... }</code>.</p>
<pre><code><?php echo htmlspecialchars(
'// Example: a 3D tour announces the selected info tag
viewer.on("tag.select", function (tag) {
    ' . $brand . '.speak(tag.label + ". " + tag.text);          // interrupts running speech
});
viewer.on("room.enter", function (room) {
    ' . $brand . '.speak(room.name, { interrupt: false });      // only when nothing is playing
});'); ?></code></pre>
        <p>Typical pattern: embed with <code>hide=1</code> (and optionally <code>css=none</code>)
        and build your own controls with <code>open()</code>, <code>set()</code> and
        <code>features()</code>.</p>
        <p><code>values()</code> returns the actual values behind the active settings, so your
        page can carry them over to areas the widget cannot reach (your own iframes, canvas
        labels). <code>spacing</code> and <code>fontFamily</code> are <code>null</code> while off.
        <code>fontUrl</code> and <code>fontFaceCss</code> are always set, so the dyslexia font can be
        preloaded; in the download variant the font is embedded, then <code>fontUrl</code> is
        <code>null</code> and only <code>fontFaceCss</code> carries it.</p>
<pre><code><?php echo htmlspecialchars(
'{
    fontScale: 1.15,                 // 1 | 1.15 | 1.3 | 1.55
    spacing: { lineHeight: "1.6", letterSpacing: "0.12em", wordSpacing: "0.16em" },
    fontFamily: "\"OpenDyslexic\",Arial,sans-serif",
    fontUrl: "' . $baseUrl . '/assets/fonts/opendyslexic-400.woff2",
    fontFaceCss: "@font-face{font-family:\'OpenDyslexic\';src:url(...)...}"
}'); ?></code></pre>
    </div>

    <div class="card">
        <h2>Events</h2>
        <p>Since version 0.2.0 the widget dispatches plain DOM events on <code>document</code>.
        They stay in the browser, nothing is sent anywhere.</p>
        <div class="tablewrap">
        <table>
            <tr><th>Event</th><th>When</th><th><code>event.detail</code></th></tr>
            <tr><td><code>legilo:change</code></td><td>Every change of a setting: panel click,
                profile, reset, <code>set()</code>, read-aloud speed, the automatic end of "Read page",
                and once after loading when stored settings (or system preferences) were applied.
                Fires only when something really changed.</td>
                <td><code>key</code>, <code>level</code>, <code>states</code>, <code>rate</code>,
                <code>values</code>, <code>source</code>, <code>profile</code></td></tr>
            <tr><td><code>legilo:open</code><br><code>legilo:close</code></td><td>The panel opens or closes
                (launcher, own button via the API, close button, Esc, outside click).</td><td>-</td></tr>
            <tr><td><code>legilo:speechstart</code></td><td>The voice really starts speaking.</td>
                <td><code>source</code>: <code>page</code> | <code>hover</code> | <code>api</code></td></tr>
            <tr><td><code>legilo:speechend</code></td><td>Speech has ended, was stopped or failed; exactly once
                per start. When one text replaces another, speech stays "on" in between.</td>
                <td><code>reason</code>: <code>end</code> | <code>stop</code> | <code>error</code></td></tr>
        </table>
        </div>
        <p style="margin-top:10px">In <code>legilo:change</code>, <code>key</code> is the changed function
        (<code>ttsrate</code> for the speed) or <code>null</code> for profile, reset and load;
        <code>level</code> is its new level, <code>states</code> all levels as
        <code>{ key: level }</code>, <code>rate</code> the read-aloud speed (1 slower, 2 normal,
        3 faster), <code>values</code> the same object as <code>values()</code>, <code>source</code>
        one of <code>panel</code>, <code>profile</code>, <code>reset</code>, <code>api</code>,
        <code>auto</code>, <code>load</code>, and <code>profile</code> the profile key when a profile was
        toggled. Because the event only fires on real changes, a handler may call
        <code>set()</code> itself without causing a loop.</p>
<pre><code><?php echo htmlspecialchars(
'// Register before the script loads, or read the state with ' . $brand . '.values() later
document.addEventListener("legilo:change", function (e) {
    var v = e.detail.values;
    tourFrame.contentDocument.documentElement.style.fontSize = (16 * v.fontScale) + "px";
});
document.addEventListener("legilo:speechstart", function () { music.pause(); });
document.addEventListener("legilo:speechend", function () { music.play(); });
document.addEventListener("legilo:open", function () { myButton.setAttribute("aria-expanded", "true"); });
document.addEventListener("legilo:close", function () { myButton.setAttribute("aria-expanded", "false"); });'); ?></code></pre>
        <p class="muted">Font size covers a fixed list of text elements plus every element with
        its own visible text (e.g. a <code>div</code> with a direct text node); "Point &amp; read"
        uses the same fallback. Icons (icon classes, <code>&lt;i&gt;</code>, SVG, icon-font glyphs) stay
        untouched. Read-aloud skips text hidden with <code>display:none</code>,
        <code>visibility:hidden</code>, the <code>hidden</code> attribute or
        <code>aria-hidden="true"</code>.</p>
    </div>

    <div class="card">
        <h2>Theming</h2>
        <p>The panel lives in Shadow DOM; every building block is exposed via CSS
        <code>::part()</code>, so you can restyle it from your own stylesheet without
        losing the isolation:</p>
<pre><code><?php echo htmlspecialchars(
'#legilo-host::part(trigger) { border-radius: 10px; }
#legilo-host::part(panel)   { font-family: "Your Brand", sans-serif; }
#legilo-host::part(feature-active) { background: #14532d; }'); ?></code></pre>
        <p>Commented template with all part names:
        <a href="<?php echo htmlspecialchars(LEGILO_GITHUB_URL); ?>/blob/main/docs/legilo-theme.css" target="_blank" rel="noopener">legilo-theme.css</a>.
        For a fully custom design use <code>css=none</code> and start from
        <a href="#skeleton">the CSS skeleton below</a>.</p>
        <h2 id="skeleton" style="margin-top:18px">CSS skeleton for css=none</h2>
        <p>With <code>css=none</code> the panel loads unstyled in the light DOM (only a tiny
        functional layer ships). Copy this skeleton into your stylesheet and build your design
        on top - the project page itself runs on exactly this skeleton when you preview
        <code>css=none</code> in the configurator:</p>
<pre><code><?php echo htmlspecialchars(legilo_css_skeleton()); ?></code></pre>
    </div>

    <div class="card">
        <h2>Storage and privacy</h2>
        <p>The widget sets no cookies and sends nothing to any server. Visitor settings are
        stored in <code>localStorage</code> under <code>legilo:v1</code> only after active
        interaction; "hide for this session" uses <code>sessionStorage</code>
        (<code>legilo:hidden</code>). Read-aloud prefers local voices.</p>
        <p class="muted">Honest note: <?php echo htmlspecialchars($brand); ?> is a reading aid. It does not create
        conformance with WCAG, EN 301 549 or national accessibility laws.</p>
    </div>
</main>
</body>
</html>
