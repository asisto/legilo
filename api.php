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
<meta name="theme-color" content="#10314f">
<meta name="description" content="<?php echo htmlspecialchars($brand); ?> widget reference: embed options, all URL parameters, JavaScript API and theming.">
<link rel="icon" type="image/svg+xml" href="<?php echo htmlspecialchars($baseUrl); ?>/assets/favicon.svg">
<link rel="canonical" href="<?php echo htmlspecialchars($baseUrl); ?>/api">
<title><?php echo htmlspecialchars($brand); ?> API and options</title>
<style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #1a1a1a; background: #f6f4ef; }
    a { color: #10314f; }
    :focus-visible { outline: 3px solid #e8a600; outline-offset: 2px; border-radius: 4px; }
    header { background: #10314f; color: #fff; padding: 18px 28px; }
    header a { color: #fff; text-decoration: none; font-weight: 600; }
    main { max-width: 860px; margin: 0 auto; padding: 30px 28px 60px; }
    .card { background: #fff; border: 1px solid #e3ded2; border-radius: 10px; padding: 22px 26px; margin-bottom: 18px; }
    h1 { font-size: 24px; margin: 0 0 6px; }
    h2 { font-size: 17px; margin: 0 0 12px; }
    p, li { font-size: 14px; line-height: 1.65; }
    p { margin: 0 0 10px; }
    .muted { color: #56606b; font-size: 13px; }
    pre { background: #0f1720; color: #d7e3f0; border-radius: 6px; padding: 10px 12px; font-family: Consolas, Monaco, monospace; font-size: 12.5px; line-height: 1.55; overflow-x: auto; }
    code { font-family: Consolas, Monaco, monospace; font-size: 13px; background: #f0ece3; border-radius: 4px; padding: 1px 5px; }
    pre code { background: none; padding: 0; }
    table { border-collapse: collapse; width: 100%; font-size: 13.5px; }
    th, td { border: 1px solid #e3ded2; padding: 7px 10px; text-align: left; vertical-align: top; }
    th { background: #f6f4ef; }
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
        statement in the panel footer.</p>
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
' . $brand . '.reset()            // reset all visitor settings (like the panel button)
' . $brand . '.set(key, level)    // activate a function programmatically, e.g. set("contrast", 1)
' . $brand . '.get(key)           // current level of a function (0 = off), undefined if not configured
' . $brand . '.features()         // [{ key, levels, state }, ...] for building your own UI
' . $brand . '.destroy()          // remove the widget from the page entirely
' . $brand . '.version            // version string, e.g. "' . LEGILO_VERSION . '"'); ?></code></pre>
        <p><code>set()</code> behaves exactly like a click in the panel: the level is clamped to the
        function's range (see the feature table: most functions have 2 levels, multi-level ones up
        to 4), the change is applied, saved and announced to screen readers. Levels above the
        maximum are clamped, unknown or unconfigured keys return <code>false</code>.</p>
<pre><code><?php echo htmlspecialchars(
'// Example: your own dark mode switch, widget embedded with hide=1
' . $brand . '.set("contrast", ' . $brand . '.get("contrast") === 1 ? 0 : 1);'); ?></code></pre>
        <p>Typical pattern: embed with <code>hide=1</code> (and optionally <code>css=none</code>)
        and build your own controls with <code>open()</code>, <code>set()</code> and
        <code>features()</code>.</p>
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
