<?php
/**
 * Projektseite + Konfigurator (/de, /en, /fr, ... - alle Sprachen in LEGILO_GEN_LANGS,
 * Texte in translations/<code>.php).
 * Formular und Embed-Code sind bidirektional gekoppelt: Code einfügen setzt die
 * Formularfelder, Formular ändern erzeugt neuen Code und lädt die Vorschau neu.
 */
if (!function_exists('legilo_schema')) {
    require __DIR__ . '/config.php';
}
if (!isset($genLang) || !in_array($genLang, LEGILO_GEN_LANGS, true)) {
    $genLang = 'en';
}

$schema = legilo_schema();
$defaults = array();
foreach ($schema as $k => $def) $defaults[$k] = $def['default'];
$baseUrl = legilo_base_url();
$langNames = legilo_lang_names();
$brand = LEGILO_BRAND;
$brandJs = strtolower($brand) . '.js'; // oeffentlicher Dateiname des Widget-JS

// CSS-Grundgeruest fuer css=none: definiert in config.php, hier fuer die
// Live-Vorschau eingebettet, auf /api zum Kopieren dokumentiert.
$cssSkeleton = legilo_css_skeleton();

// Feature-Beschriftungen aus der Widget-Sprachdatei der Oberflächensprache
$widgetLang = json_decode(file_get_contents(__DIR__ . '/widget/lang/' . $genLang . '.json'), true);
$featureLabels = $widgetLang['f'];

// Uebersetzungen der Oberflaeche: eine Datei pro Sprache in translations/<code>.php
$T = require __DIR__ . '/translations/' . $genLang . '.php';

// Logo: "l" + drei Textzeilen, die mittlere vom Leselineal hervorgehoben
// (einfarbig via currentColor; Farbversion in assets/favicon.svg)
$logoSvg = '<svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><rect x="4" y="4" width="3.4" height="16" rx="1.7"/><rect x="9.8" y="4.8" width="7.6" height="2.8" rx="1.4"/><rect x="9.8" y="10.6" width="10.2" height="2.8" rx="1.4"/><rect x="9.8" y="16.4" width="5.4" height="2.8" rx="1.4"/></svg>';

// Funktions-Icons, identisch mit der FICONS-Map in widget/widget.js
function legilo_fico($inner) {
    return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor"'
        . ' stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $inner . '</svg>';
}
$ficons = array(
    'profiles' => legilo_fico('<path d="M5 4v5m0 4v7M12 4v9m0 4v3M19 4v3m0 4v9"/><circle cx="5" cy="10.5" r="1.8"/><circle cx="12" cy="15" r="1.8"/><circle cx="19" cy="8.5" r="1.8"/>'),
    'fontsize' => legilo_fico('<path d="M4 19V7m0 0h5m-5 0v3" stroke-width="1.6"/><path d="M13 19V4h7m-7 0v4"/>'),
    'spacing' => legilo_fico('<path d="M3 12h18M6 8l-3 4 3 4M18 8l3 4-3 4"/>'),
    'font' => legilo_fico('<path d="M6 19L11 5l5 14M8.2 14h5.6"/><path d="M19 12v7m0-5.5a2.6 2.6 0 1 0-2.6 4.3" stroke-width="1.6"/>'),
    'align' => legilo_fico('<path d="M4 6h16M4 10h10M4 14h16M4 18h8"/>'),
    'contrast' => legilo_fico('<circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor" stroke="none"/>'),
    'saturation' => legilo_fico('<path d="M12 3.5c3.6 4.5 6.3 7.7 6.3 10.7a6.3 6.3 0 0 1-12.6 0c0-3 2.7-6.2 6.3-10.7z"/>'),
    'bluefilter' => legilo_fico('<circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.5 1.5M16.9 16.9l1.5 1.5M18.4 5.6l-1.5 1.5M7.1 16.9l-1.5 1.5"/>'),
    'colorblind' => legilo_fico('<circle cx="9" cy="9.5" r="4.6"/><circle cx="15" cy="9.5" r="4.6"/><circle cx="12" cy="14.5" r="4.6"/>'),
    'links' => legilo_fico('<path d="M10 14a4 4 0 0 0 6 .4l2.6-2.6a4 4 0 0 0-5.7-5.7l-1.5 1.5"/><path d="M14 10a4 4 0 0 0-6-.4L5.4 12.2a4 4 0 0 0 5.7 5.7l1.5-1.5"/>'),
    'focus' => legilo_fico('<path d="M4 8V5.5A1.5 1.5 0 0 1 5.5 4H8M16 4h2.5A1.5 1.5 0 0 1 20 5.5V8M20 16v2.5a1.5 1.5 0 0 1-1.5 1.5H16M8 20H5.5A1.5 1.5 0 0 1 4 18.5V16"/><circle cx="12" cy="12" r="3" fill="currentColor" stroke="none"/>'),
    'cursor' => legilo_fico('<path d="M6 3l13 10.5-5.5.9 3 5.4-2.8 1.5-2.9-5.5L6 19.5z" fill="currentColor" stroke="none"/>'),
    'guide' => legilo_fico('<path d="M3 5.5h18M3 18.5h18"/><rect x="3" y="10" width="18" height="4" rx="1" fill="currentColor" stroke="none"/>'),
    'mask' => legilo_fico('<rect x="3" y="3.5" width="18" height="5.5" rx="1" fill="currentColor" stroke="none" opacity=".85"/><rect x="3" y="15" width="18" height="5.5" rx="1" fill="currentColor" stroke="none" opacity=".85"/><path d="M5 12h14" stroke-width="1.4"/>'),
    'animations' => legilo_fico('<circle cx="12" cy="12" r="9"/><path d="M10 9v6M14 9v6"/>'),
    'images' => legilo_fico('<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.4" fill="currentColor" stroke="none"/><path d="M5.5 16.5l3.5-3.5 2.5 2.5 3.5-3.5 3.5 3.5"/><path d="M4 4l16 16" stroke-width="1.6"/>'),
    'tts' => legilo_fico('<path d="M4 9.5v5h3.5L13 19V5L7.5 9.5H4z" fill="currentColor" stroke="none"/><path d="M16 9a4.2 4.2 0 0 1 0 6M18.6 6.5a8 8 0 0 1 0 11"/>'),
    'structure' => legilo_fico('<path d="M4 6h16M7 12h13M10 18h10"/><circle cx="4.5" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="7.5" cy="18" r="1" fill="currentColor" stroke="none"/>'),
);
?>
<!DOCTYPE html>
<html lang="<?php echo $genLang; ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#10314f">
<meta name="description" content="<?php echo htmlspecialchars($T['intro']); ?>">
<link rel="icon" type="image/svg+xml" href="<?php echo htmlspecialchars($baseUrl); ?>/assets/favicon.svg">
<link rel="canonical" href="<?php echo htmlspecialchars($baseUrl . '/' . $genLang); ?>">
<?php foreach (LEGILO_GEN_LANGS as $hl): ?>
<link rel="alternate" hreflang="<?php echo $hl; ?>" href="<?php echo htmlspecialchars($baseUrl . '/' . $hl); ?>">
<?php endforeach; ?>
<link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars($baseUrl . '/en'); ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo htmlspecialchars($brand); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($T['metaTitle']); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($T['intro']); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($baseUrl . '/' . $genLang); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($baseUrl); ?>/assets/og-image.png">
<meta name="twitter:card" content="summary_large_image">
<?php
// Strukturierte Daten: SoftwareApplication (Preis 0) + FAQPage aus den
// vorhandenen Texten der aktuellen Sprache. Slashes escaped lassen,
// damit "</" im JSON nie den script-Block beenden kann.
$ldFaq = array();
foreach ($T['faq'] as $qa) {
    $ldFaq[] = array(
        '@type' => 'Question',
        'name' => $qa[0],
        'acceptedAnswer' => array('@type' => 'Answer', 'text' => $qa[1]),
    );
}
$ld = array(
    '@context' => 'https://schema.org',
    '@graph' => array(
        array(
            '@type' => 'SoftwareApplication',
            'name' => $brand,
            'operatingSystem' => 'Web',
            'applicationCategory' => 'BrowserApplication',
            'description' => $T['intro'],
            'url' => $baseUrl . '/' . $genLang,
            'image' => $baseUrl . '/assets/og-image.png',
            'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR'),
            'inLanguage' => $genLang,
        ),
        array(
            '@type' => 'FAQPage',
            'mainEntity' => $ldFaq,
            'inLanguage' => $genLang,
        ),
    ),
);
?>
<script type="application/ld+json"><?php echo json_encode($ld, JSON_UNESCAPED_UNICODE); ?></script>
<title><?php echo htmlspecialchars($T['metaTitle']); ?></title>
<style>
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
    body { margin: 0; font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #1a1a1a; background: #f6f4ef; position: relative; }
    /* Wabenmuster rechts oben, nach aussen in den Papierton auslaufend. Sichtbarkeit:
       stroke-opacity im SVG; Reichweite: die beiden Ellipsen-Masse im radial-gradient. */
    body::before { content: ""; position: absolute; inset: 0; z-index: -1; pointer-events: none;
        background:
            radial-gradient(1200px 1100px at 100% 0, rgba(246,244,239,0) 30%, #f6f4ef 80%),
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='48' viewBox='0 0 27.713 48'%3E%3Cpath d='M13.856 0 27.713 8v16l-13.857 8v16M13.856 0 0 8v16l13.856 8' fill='none' stroke='%2310314f' stroke-opacity='.07' stroke-width='1'/%3E%3C/svg%3E");
        background-size: auto, 28px 48px;
        background-repeat: no-repeat, repeat; }
    a { color: #10314f; }
    :focus-visible { outline: 3px solid #e8a600; outline-offset: 2px; border-radius: 4px; }

    /* Hero */
    header.hero { position: relative; overflow: hidden; background: linear-gradient(150deg, #0c2740 0%, #10314f 40%, #1b4a74 75%, #26648c 100%); color: #fff; padding: 24px 28px 50px; }
    header.hero::before { content: ""; position: absolute; right: -140px; top: -140px; width: 460px; height: 460px; border-radius: 50%; background: radial-gradient(circle, rgba(255,207,64,.16) 0%, rgba(255,207,64,0) 70%); pointer-events: none; }
    header.hero::after { content: ""; position: absolute; left: 12%; bottom: -220px; width: 420px; height: 420px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,.07) 0%, rgba(255,255,255,0) 70%); pointer-events: none; }
    header.hero > * { position: relative; }
    .heronav { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; max-width: 1160px; margin: 0 auto; }
    .brand { display: flex; align-items: center; gap: 10px; font-size: 21px; font-weight: 700; letter-spacing: .02em; color: inherit; text-decoration: none; }
    a.fbrand { text-decoration: none; }
    .brand .logo { width: 38px; height: 38px; border-radius: 50%; background: #fff; color: #10314f; display: flex; align-items: center; justify-content: center; }
    .brand .logo svg { width: 26px; height: 26px; }
    .langswitch { display: flex; gap: 6px; }
    .langswitch a { color: #fff; text-decoration: none; font-size: 13px; padding: 5px 10px; border: 1px solid rgba(255,255,255,.4); border-radius: 6px; }
    .langswitch a.active { background: #fff; color: #10314f; font-weight: 600; }
    .herobody { max-width: 1160px; margin: 32px auto 0; }
    .herobody h1 { margin: 0 0 14px; font-size: clamp(28px, 4.5vw, 44px); line-height: 1.15; max-width: 700px; }
    .herobody p { margin: 0 0 26px; font-size: 16.5px; line-height: 1.6; opacity: .92; max-width: 700px; }
    .herobody p.namenote { margin: -14px 0 26px; font-size: 13.5px; opacity: .72; }
    .herobtns { display: flex; gap: 12px; flex-wrap: wrap; }
    .btn { display: inline-block; border: 0; border-radius: 8px; padding: 12px 20px; font-size: 14.5px; cursor: pointer; text-decoration: none; transition: transform .15s ease, box-shadow .15s ease, background .15s ease; }
    .btn:hover { transform: translateY(-1px); }
    .btn.cta { background: #ffcf40; color: #1a1a1a; font-weight: 600; box-shadow: 0 4px 16px rgba(0,0,0,.25); }
    .btn.cta:hover { background: #ffd95e; }
    .btn.ctaghost { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.55); }
    .btn.ctaghost:hover { background: rgba(255,255,255,.12); }
    .btn.primary { background: #10314f; color: #fff; }
    .btn.primary:hover { background: #1b4a74; }
    .btn.ghost { background: #e8edf3; color: #10314f; }
    .btn.ghost:hover { background: #d9e2ec; }
    @media (prefers-reduced-motion: reduce) { .btn, .btn:hover { transition: none; transform: none; } }

    /* Badges */
    .badges { position: relative; z-index: 1; max-width: 1160px; margin: -30px auto 0; padding: 0 28px; display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 14px; }
    .badge { background: #fff; border: 1px solid #e3ded2; border-radius: 12px; padding: 18px; box-shadow: 0 4px 14px rgba(16,49,79,.07); border-top: 3px solid #ffcf40; }
    .badge:nth-child(2) { border-top-color: #26648c; }
    .badge:nth-child(3) { border-top-color: #4c9a6f; }
    .badge:nth-child(4) { border-top-color: #e05263; }
    .badge h2 { margin: 0 0 6px; font-size: 15px; }
    .badge p { margin: 0; font-size: 13px; color: #4a5560; line-height: 1.5; }

    /* Funktions-Toggles (Teil des Konfigurators) */
    .sectionintro { margin: 6px 0 14px; font-size: 14px; color: #4a5560; max-width: 640px; line-height: 1.55; }
    .fgrid { display: grid; grid-template-columns: repeat(auto-fill, minmax(185px, 1fr)); gap: 8px; }
    .fgrid .fcard { cursor: grab; }
    .fgrid .fcard.fixed { cursor: pointer; }
    .fgrid .fcard.dragging { opacity: .45; }
    .fcard .grip { margin-left: auto; color: #c9c4b8; flex: none; }
    .fcard .grip svg { width: 14px; height: 14px; display: block; }
    .fcard { display: flex; align-items: center; gap: 9px; background: #fff; border: 1px solid #e3ded2; border-radius: 10px; padding: 9px 12px; font-size: 13.5px; margin: 0; cursor: pointer; user-select: none; transition: border-color .15s ease, box-shadow .15s ease, opacity .15s ease; }
    .fcard input { margin: 0; flex: none; accent-color: #10314f; }
    .fcard:hover { border-color: #26648c; box-shadow: 0 3px 10px rgba(16,49,79,.08); }
    .fcard:has(input:checked) { border-color: #a8c0d4; }
    .fcard:not(:has(input:checked)) { opacity: .55; }
    .fcard:not(:has(input:checked)) .fic { background: #eef1f5; color: #8a949e; }
    .fcard:focus-within { outline: 3px solid #e8a600; outline-offset: 2px; }
    @media (prefers-reduced-motion: reduce) { .fcard { transition: none; } }
    .fic { width: 30px; height: 30px; flex: none; border-radius: 8px; background: #e8edf3; color: #10314f; display: flex; align-items: center; justify-content: center; }
    .fic svg { width: 17px; height: 17px; }
    .fgrid .fcard:nth-child(4n+1) .fic { background: #e7f0ea; color: #2f6b4f; }
    .fgrid .fcard:nth-child(4n+2) .fic { background: #e8eef7; color: #26648c; }
    .fgrid .fcard:nth-child(4n+3) .fic { background: #faf0dd; color: #9a6a1a; }
    .fgrid .fcard:nth-child(4n) .fic { background: #f6e9ec; color: #a04b5e; }

    /* FAQ */
    .faq { max-width: 1160px; margin: 0 auto; padding: 22px 28px 8px; }
    .faq h2 { font-size: 22px; margin: 0 0 14px; }
    .faq details { background: #fff; border: 1px solid #e3ded2; border-radius: 10px; margin-bottom: 8px; overflow: hidden; }
    .faq summary { padding: 14px 18px; font-size: 14.5px; font-weight: 600; cursor: pointer; list-style: none; position: relative; padding-right: 40px; }
    .faq summary::-webkit-details-marker { display: none; }
    .faq summary:hover { background: #faf7f0; }
    .faq summary::after { content: "+"; position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #26648c; }
    .faq details[open] summary::after { content: "-"; }
    .faq details[open] summary { border-bottom: 1px solid #eef1f5; }
    .faq details p { margin: 0; padding: 13px 18px 16px; font-size: 13.5px; color: #3c4650; line-height: 1.6; }

    /* How */
    .how { max-width: 1160px; margin: 0 auto; padding: 30px 28px 8px; }
    .how h2, .honesty h2 { font-size: 22px; margin: 0 0 18px; }
    .steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 14px; }
    .step { background: #fff; border: 1px solid #e3ded2; border-radius: 12px; padding: 16px 18px; border-top: 3px solid #ffcf40; }
    .step:nth-child(2) { border-top-color: #26648c; }
    .step:nth-child(3) { border-top-color: #4c9a6f; }
    .step .num { width: 28px; height: 28px; border-radius: 50%; background: #10314f; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; margin-bottom: 10px; }
    .step h3 { margin: 0 0 6px; font-size: 15px; }
    .step p { margin: 0; font-size: 13px; color: #4a5560; line-height: 1.5; }

    /* Konfigurator */
    .confhead { max-width: 1160px; margin: 0 auto; padding: 30px 28px 0; }
    .confhead h2 { font-size: 22px; margin: 0; }
    .layout { display: grid; grid-template-columns: minmax(340px, 460px) 1fr; gap: 24px; padding: 16px 28px 10px; align-items: start; max-width: 1160px; margin: 0 auto; }
    @media (max-width: 980px) { .layout { grid-template-columns: 1fr; } }
    .card { background: #fff; border: 1px solid #e3ded2; border-radius: 10px; padding: 18px 20px; margin-bottom: 20px; }
    .card h2 { margin: 0 0 14px; font-size: 16px; }
    /* Farbakzente wie bei Badges und Schritten: verankert die Karten optisch */
    .layout .card { border-top: 3px solid #e3ded2; }
    .layout > div:first-child > .card:first-child { border-top-color: #ffcf40; }
    .layout > div:first-child > .card:nth-child(2) { border-top-color: #26648c; }
    .layout > div:last-child > .card { border-top-color: #4c9a6f; }
    label { display: block; font-size: 13px; margin: 10px 0 4px; color: #3c4650; }
    select, input[type=text], input[type=url], input[type=number] { width: 100%; padding: 8px 10px; border: 1px solid #cfc9bc; border-radius: 7px; font-size: 14px; background: #fff; transition: border-color .15s ease, box-shadow .15s ease; }
    select:hover, input[type=text]:hover, input[type=url]:hover, input[type=number]:hover { border-color: #a8b4c0; }
    select:focus, input[type=text]:focus, input[type=url]:focus, input[type=number]:focus { border-color: #26648c; box-shadow: 0 0 0 3px rgba(38,100,140,.12); }
    input[type=color] { width: 52px; height: 36px; padding: 3px; border: 1px solid #cfc9bc; border-radius: 8px; background: #fff; cursor: pointer; }
    input[type=range] { accent-color: #10314f; }
    input[type=checkbox] { accent-color: #10314f; width: 15px; height: 15px; flex: none; }
    @media (prefers-reduced-motion: reduce) { select, input[type=text], input[type=url], input[type=number] { transition: none; } }
    .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .colorrow { display: flex; align-items: center; gap: 10px; }
    .colorrow label { margin: 0; font-size: 13px; color: #3c4650; }
    .checkline { display: flex; align-items: center; gap: 8px; margin-top: 11px; font-size: 13.5px; }
    output.rv { font-size: 12.5px; color: #555; margin-left: 8px; }
    textarea.code { width: 100%; min-height: 56px; font-family: Consolas, Monaco, monospace; font-size: 12.5px; border: 1px solid #b9c2cc; border-radius: 6px; padding: 10px; resize: vertical; background: #0f1720; color: #d7e3f0; }
    .tabbar { display: flex; flex-wrap: wrap; gap: 4px; border-bottom: 1px solid #e3ded2; margin-bottom: 14px; }
    .tabbar button { background: transparent; border: 0; border-bottom: 3px solid transparent; padding: 7px 10px 9px; font-size: 14px; font-weight: 600; color: #56606b; cursor: pointer; }
    .tabbar button:hover { color: #10314f; }
    .tabbar button.active { color: #10314f; border-bottom-color: #ffcf40; }
    .btnrow { display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
    .hint { font-size: 12.5px; color: #56606b; margin-top: 8px; line-height: 1.5; }
    .skellink { font-size: 12px; white-space: nowrap; }
    .devcard { display: flex; align-items: center; gap: 14px; background: #10314f; color: #fff; border-radius: 10px; padding: 14px 18px; text-decoration: none; margin-bottom: 20px; transition: background .15s ease; }
    .devcard:hover { background: #1b4a74; }
    .devcard .devicon { width: 38px; height: 38px; flex: none; border-radius: 9px; background: rgba(255,255,255,.12); display: flex; align-items: center; justify-content: center; }
    .devcard .devicon svg { width: 20px; height: 20px; }
    .devcard .devtext { font-size: 12.5px; line-height: 1.5; color: #c6d4e2; }
    .devcard .devtext strong { font-size: 14px; color: #fff; }
    .devcard .devarrow { margin-inline-start: auto; font-size: 26px; line-height: 1; opacity: .6; }
    @media (prefers-reduced-motion: reduce) { .devcard { transition: none; } }
    .previewnote { background: #e8f4e9; border: 1px solid #9ecba3; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; }

    /* Ehrlichkeit */
    .honesty { max-width: 1160px; margin: 0 auto; padding: 22px 28px 8px; }
    .honestycols { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 14px; }
    .hcol { background: #fff; border: 1px solid #e3ded2; border-radius: 12px; padding: 18px 20px; }
    .hcol.no { background: #fff8f6; border-color: #eac8bf; }
    .hcol h3 { margin: 0 0 10px; font-size: 15px; }
    .hcol ul { margin: 0; padding-left: 18px; }
    .hcol li { font-size: 13.5px; color: #3c4650; line-height: 1.55; margin-bottom: 8px; }
    .honestylead { margin: 0 0 16px; font-size: 15px; color: #2c3540; line-height: 1.6; max-width: 860px; }
    .honestystats { display: flex; flex-wrap: wrap; gap: 10px; margin: 0 0 16px; }
    .honestystats .stat { background: #fff; border: 1px solid #e3ded2; border-radius: 999px; padding: 6px 14px; font-size: 13.5px; color: #3c4650; }
    .honestystats .stat b { font-size: 15px; color: #10314f; }
    .honestyproof { margin: 14px 0 0; font-size: 13.5px; color: #4a5560; line-height: 1.6; max-width: 860px; }

    /* Website-Check (PageSpeed Insights API, laeuft komplett im Browser des Besuchers) */
    .audit { max-width: 1160px; margin: 0 auto; padding: 22px 28px 8px; }
    .audit h2 { font-size: 22px; margin: 0 0 14px; }
    .auditrow { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px; }
    .auditrow input { flex: 1 1 260px; }
    .auditrow .btn { flex: none; }
    .auditwait { font-size: 13.5px; color: #56606b; }
    .auditwait::before { content: ""; display: inline-block; width: 12px; height: 12px; margin-right: 8px; vertical-align: -1px; border: 2px solid #26648c; border-top-color: transparent; border-radius: 50%; animation: a11ydemo-spin 1s linear infinite; }
    @media (prefers-reduced-motion: reduce) { .auditwait::before { animation: none; } }
    .auditerror { font-size: 13.5px; color: #a04b5e; background: #f6e9ec; border: 1px solid #eac8bf; border-radius: 8px; padding: 10px 14px; }
    .scorewrap { display: flex; align-items: flex-start; gap: 16px; margin: 16px 0 6px; }
    .scorecircle { width: 74px; height: 74px; flex: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 23px; font-weight: 700; border: 6px solid #178239; color: #178239; background: #e7f0ea; }
    .scorecircle.mid { border-color: #e8a600; color: #9a6a1a; background: #faf0dd; }
    .scorecircle.bad { border-color: #e05263; color: #a04b5e; background: #f6e9ec; }
    .audit details { background: #fbfaf7; border: 1px solid #e3ded2; border-radius: 10px; margin-bottom: 8px; }
    .audit summary { padding: 10px 14px; font-size: 13.5px; font-weight: 600; cursor: pointer; }
    .audit details p { margin: 0; padding: 4px 14px 12px; font-size: 13px; color: #3c4650; line-height: 1.6; }
    .auditopts { display: flex; gap: 16px; margin-top: 8px; font-size: 13px; }
    .auditopts label { display: flex; align-items: center; gap: 6px; margin: 0; cursor: pointer; }
    .auditopts input { margin: 0; accent-color: #10314f; }
    .auditgroup { margin: 16px 0 6px; font-size: 13.5px; color: #10314f; }
    .auditnodes { list-style: none; margin: 0; padding: 0 14px 12px; }
    .auditnodes li { border-top: 1px solid #eee7d9; padding: 8px 0 6px; font-size: 12.5px; }
    .auditnodes li.nmore { color: #56606b; }
    .auditnodes .nlabel { color: #1a1a1a; margin-bottom: 4px; white-space: pre-line; }
    .auditnodes code { display: block; background: #0f1720; color: #d7e3f0; border-radius: 6px; padding: 6px 9px; font-size: 11.5px; font-family: Consolas, Monaco, monospace; white-space: pre-wrap; word-break: break-all; }
    .auditnodes .nexp { color: #56606b; margin-top: 4px; white-space: pre-line; }
    .auditnodes .nsel { color: #8a949e; margin-top: 3px; font-family: Consolas, Monaco, monospace; font-size: 11px; word-break: break-all; }
    .auditextra { margin-top: 10px; }
    .auditsub { padding: 0 14px 10px; }
    .auditsub details { background: #fff; margin-top: 8px; }
    .auditpassed { list-style: none; margin: 0; padding: 4px 14px 12px; }
    .auditpassed li { font-size: 13px; color: #3c4650; padding: 3px 0; }
    .auditpassed li::before { content: "\2713"; color: #178239; margin-right: 8px; font-weight: 700; }
    #audit_meta { word-break: break-all; }

    /* PDF-Report: Druckansicht des Website-Checks mit Legilo-Kopf. "PDF" laeuft
       ueber den Browser-Druckdialog (Ziel "Als PDF speichern"), keine Bibliothek. */
    #printreport { display: none; }
    @media print {
        body.printing > *:not(#printreport) { display: none !important; }
        body.printing #printreport { display: block; }
        #printreport { font-size: 12px; color: #1a1a1a; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        #printreport .pr-head { display: flex; justify-content: space-between; align-items: center; background: #10314f; color: #fff; padding: 18px 22px; border-radius: 10px; }
        #printreport .pr-brand { display: flex; align-items: center; gap: 12px; }
        #printreport .pr-logo { width: 44px; height: 44px; border-radius: 50%; background: #fff; color: #10314f; display: flex; align-items: center; justify-content: center; }
        #printreport .pr-logo svg { width: 30px; height: 30px; }
        #printreport .pr-name { font-size: 22px; font-weight: 700; }
        #printreport .pr-tag { font-size: 12px; opacity: .92; }
        #printreport .pr-url { font-size: 14px; font-weight: 600; color: #ffcf40; }
        #printreport .pr-ad { margin: 12px 0 18px; padding: 10px 14px; background: #faf0dd; border-left: 4px solid #ffcf40; font-size: 11.5px; line-height: 1.55; }
        #printreport .pr-foot { margin-top: 20px; padding-top: 10px; border-top: 1px solid #b9c2cc; font-size: 10.5px; color: #555; line-height: 1.5; }
        #printreport details, #printreport .auditextra { page-break-inside: avoid; border: 1px solid #d9d2c2; }
        #printreport .btnrow { display: none; }
        #printreport code { background: #f1efe9 !important; color: #1a1a1a !important; }
        #printreport a { color: #10314f; }
    }

    /* Demo + Footer */
    .demo { padding: 22px 28px 34px; max-width: 1160px; margin: 0 auto; }
    .demo .card { padding: 26px 30px; }
    .demo h2 { font-size: 22px; margin: 0 0 14px; }
    .demo img { max-width: 100%; border-radius: 8px; }
    .demo table { border-collapse: collapse; width: 100%; font-size: 14px; }
    .demo td, .demo th { border: 1px solid #ccd4dc; padding: 7px 10px; text-align: left; }
    .spinner { width: 46px; height: 46px; border-radius: 8px; background: linear-gradient(135deg, #e05263, #10314f); animation: a11ydemo-spin 2.2s linear infinite; }
    @keyframes a11ydemo-spin { to { transform: rotate(360deg); } }
    .demo form .row2 { margin-bottom: 10px; }
    .dchip { width: 104px; height: 56px; border-radius: 8px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13.5px; }
    kbd { background: #e8edf3; border-radius: 4px; padding: 1px 6px; font-size: 12px; }
    footer.site { background: #10314f; color: #c6d4e2; padding: 26px 28px 90px; }
    footer.site .inner { max-width: 1160px; margin: 0 auto; display: flex; flex-direction: column; gap: 6px; }
    footer.site p { margin: 0; font-size: 13px; line-height: 1.6; }
    footer.site a { color: #c6d4e2; }
    footer.site .fbrand { display: flex; align-items: center; gap: 8px; color: #fff; font-weight: 600; font-size: 15px; margin-bottom: 6px; }
    footer.site .fbrand svg { width: 20px; height: 20px; }
    footer.site .donaterow { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-top: 14px; }
    footer.site .donaterow p { margin: 0; font-size: 13px; }
    footer.site .donatebtn { display: inline-block; padding: 6px 14px; border: 1px solid rgba(198,212,226,.45); border-radius: 7px; color: #c6d4e2; text-decoration: none; font-size: 13px; transition: background .15s ease, color .15s ease; }
    footer.site .donatebtn:hover { background: rgba(255,255,255,.1); color: #fff; }
    @media (prefers-reduced-motion: reduce) { footer.site .donatebtn { transition: none; } }
</style>
<?php // Das css=none-Geruest laeuft auf unserer eigenen Seite live mit: so bleibt
      // die Vorschau benutzbar, wenn man die Option im Konfigurator aktiviert,
      // und wir sind zugleich der Referenz-Nachweis, dass das Geruest reicht. ?>
<style>
<?php echo $cssSkeleton . "\n"; ?>
</style>
</head>
<body id="top">

<header class="hero">
    <div class="heronav">
        <a class="brand" href="<?php echo htmlspecialchars($baseUrl . '/' . $genLang); ?>"><span class="logo"><?php echo $logoSvg; ?></span> <?php echo htmlspecialchars($brand); ?></a>
        <nav class="langswitch" aria-label="Language">
            <?php foreach (LEGILO_GEN_LANGS as $gl): ?>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>/<?php echo $gl; ?>" hreflang="<?php echo $gl; ?>" title="<?php echo htmlspecialchars($langNames[$gl]); ?>"<?php if ($gl === $genLang) echo ' class="active" aria-current="page"'; ?>><?php echo strtoupper($gl); ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="herobody">
        <h1><?php echo htmlspecialchars($T['tagline']); ?></h1>
        <p><?php echo htmlspecialchars($T['intro']); ?></p>
        <p class="namenote"><?php echo htmlspecialchars($T['nameNote']); ?></p>
        <div class="herobtns">
            <a class="btn cta" href="#configurator"><?php echo $T['ctaConfig']; ?></a>
            <a class="btn ctaghost" href="#demo"><?php echo $T['ctaTest']; ?></a>
        </div>
    </div>
</header>

<main>

<div class="badges">
    <div class="badge"><h2><?php echo $T['badge1t']; ?></h2><p><?php echo $T['badge1x']; ?></p></div>
    <div class="badge"><h2><?php echo $T['badge2t']; ?></h2><p><?php echo $T['badge2x']; ?></p></div>
    <div class="badge"><h2><?php echo $T['badge3t']; ?></h2><p><?php echo $T['badge3x']; ?></p></div>
    <div class="badge"><h2><?php echo $T['badge4t']; ?></h2><p><?php echo $T['badge4x']; ?></p></div>
</div>

<section class="how">
    <h2><?php echo $T['howTitle']; ?></h2>
    <div class="steps">
        <div class="step"><div class="num">1</div><h3><?php echo $T['how1t']; ?></h3><p><?php echo $T['how1x']; ?></p></div>
        <div class="step"><div class="num">2</div><h3><?php echo $T['how2t']; ?></h3><p><?php echo $T['how2x']; ?></p></div>
        <div class="step"><div class="num">3</div><h3><?php echo $T['how3t']; ?></h3><p><?php echo $T['how3x']; ?></p></div>
    </div>
</section>

<div class="confhead" id="configurator">
    <h2><?php echo $T['configTitle']; ?></h2>
    <p class="sectionintro"><?php echo htmlspecialchars($T['featuresIntro']); ?>
    <?php echo htmlspecialchars($T['featuresSort']); ?></p>
    <div class="fgrid" role="group" aria-label="<?php echo htmlspecialchars($T['features']); ?>">
        <?php foreach (LEGILO_FEATURES as $fk): if (!isset($featureLabels[$fk])) continue;
            // Profile und Seitenstruktur haben feste Plaetze im Panel (Chips oben,
            // Struktur unten) - ihre Kacheln sind nur ein-/ausblendbar, nicht sortierbar.
            $fixed = ($fk === 'profiles' || $fk === 'structure'); ?>
        <label class="fcard<?php echo $fixed ? ' fixed' : ''; ?>"><input type="checkbox" class="f_feature" value="<?php echo $fk; ?>"><span class="fic"><?php echo $ficons[$fk]; ?></span><span><?php echo htmlspecialchars($featureLabels[$fk]); ?></span><?php if (!$fixed): ?><span class="grip" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.7"/><circle cx="15" cy="6" r="1.7"/><circle cx="9" cy="12" r="1.7"/><circle cx="15" cy="12" r="1.7"/><circle cx="9" cy="18" r="1.7"/><circle cx="15" cy="18" r="1.7"/></svg></span><?php endif; ?></label>
        <?php endforeach; ?>
    </div>
</div>

<div class="layout">

    <div><!-- options -->
        <div class="card">
            <h2><?php echo $T['appearance']; ?></h2>
            <div class="row2">
                <div>
                    <label for="f_pos"><?php echo $T['position']; ?></label>
                    <select id="f_pos">
                        <?php foreach ($T['posLabels'] as $v => $l) echo '<option value="' . $v . '">' . $l . '</option>'; ?>
                    </select>
                </div>
                <div>
                    <label for="f_size"><?php echo $T['btnsize']; ?></label>
                    <select id="f_size">
                        <option value="s"><?php echo $T['sizeS']; ?></option>
                        <option value="m"><?php echo $T['sizeM']; ?></option>
                        <option value="l"><?php echo $T['sizeL']; ?></option>
                    </select>
                </div>
            </div>
            <div class="row2">
                <div>
                    <label for="f_offx"><?php echo $T['offx']; ?></label>
                    <input type="number" id="f_offx" min="0" max="400">
                </div>
                <div>
                    <label for="f_offy"><?php echo $T['offy']; ?></label>
                    <input type="number" id="f_offy" min="0" max="400">
                </div>
            </div>
            <div class="row2" style="margin-top:10px">
                <div class="colorrow">
                    <input type="color" id="f_color">
                    <label for="f_color"><?php echo $T['btncolor']; ?></label>
                </div>
                <div class="colorrow">
                    <input type="color" id="f_color2">
                    <label for="f_color2"><?php echo $T['iconcolor']; ?></label>
                </div>
            </div>
            <div class="row2">
                <div>
                    <label for="f_icon"><?php echo $T['icon']; ?></label>
                    <select id="f_icon">
                        <?php foreach ($T['iconLabels'] as $v => $l) echo '<option value="' . $v . '">' . $l . '</option>'; ?>
                    </select>
                </div>
                <div>
                    <label for="f_radius"><?php echo $T['radius']; ?> <output class="rv" id="radius_out"></output></label>
                    <input type="range" id="f_radius" min="0" max="50" style="width:100%">
                </div>
            </div>
        </div>

        <div class="card">
            <h2><?php echo $T['behavior']; ?></h2>
            <div class="row2">
                <div>
                    <label for="f_lang"><?php echo $T['panellang']; ?></label>
                    <select id="f_lang">
                        <option value="auto"><?php echo $T['langauto']; ?></option>
                        <?php foreach (LEGILO_LANGS as $lc) echo '<option value="' . $lc . '">' . htmlspecialchars($langNames[$lc]) . '</option>'; ?>
                    </select>
                </div>
                <div>
                    <label for="f_mobile"><?php echo $T['mobile']; ?></label>
                    <select id="f_mobile">
                        <option value="show"><?php echo $T['mobileShow']; ?></option>
                        <option value="hide"><?php echo $T['mobileHide']; ?></option>
                    </select>
                </div>
            </div>
            <label for="f_statement"><?php echo $T['statement']; ?></label>
            <input type="url" id="f_statement" placeholder="https://www.example.com/accessibility">
            <div class="checkline">
                <input type="checkbox" id="f_hide">
                <label for="f_hide" style="margin:0"><?php echo $T['hidebtn']; ?></label>
            </div>
            <div class="checkline">
                <input type="checkbox" id="f_hotkey">
                <label for="f_hotkey" style="margin:0"><?php echo $T['hotkeybtn']; ?></label>
            </div>
            <div class="checkline">
                <input type="checkbox" id="f_css">
                <label for="f_css" style="margin:0"><?php echo $T['cssOpt']; ?></label>
                <a class="skellink" href="api#skeleton" title="<?php echo htmlspecialchars($T['cssTitle']); ?>">CSS &rsaquo;</a>
            </div>
        </div>

    </div>

    <div><!-- Code + Download -->
        <div class="previewnote">
            <?php echo $T['previewNote']; ?>
        </div>

        <div class="card">
            <div class="tabbar" role="tablist">
                <button type="button" class="active" id="tab_url" role="tab" aria-selected="true" aria-controls="pane_url"><?php echo $T['embedTitle']; ?></button>
                <button type="button" id="tab_self" role="tab" aria-selected="false" aria-controls="pane_self"><?php echo $T['selfTitle']; ?></button>
                <button type="button" id="tab_wp" role="tab" aria-selected="false" aria-controls="pane_wp">WordPress</button>
            </div>
            <div id="pane_url" role="tabpanel" aria-labelledby="tab_url">
                <p class="hint" style="margin-top:0"><?php echo $T['embedHint']; ?></p>
                <textarea class="code" id="embed" spellcheck="false" aria-label="<?php echo htmlspecialchars($T['embedTitle']); ?>"></textarea>
                <div class="btnrow">
                    <button class="btn primary copybtn" type="button" data-target="embed" data-copied="<?php echo htmlspecialchars($T['copied']); ?>"><?php echo $T['copy']; ?></button>
                </div>
            </div>
            <div id="pane_self" role="tabpanel" aria-labelledby="tab_self" hidden>
                <p class="hint" style="margin-top:0"><?php echo $T['selfHint']; ?></p>
                <textarea class="code" id="selfembed" spellcheck="false" readonly aria-label="<?php echo htmlspecialchars($T['selfTitle']); ?>"><?php echo htmlspecialchars('<script src="/path/' . $brandJs . '" defer></script>'); ?></textarea>
                <div class="btnrow">
                    <button class="btn primary copybtn" type="button" data-target="selfembed" data-copied="<?php echo htmlspecialchars($T['copied']); ?>"><?php echo $T['copy']; ?></button>
                    <a class="btn ghost" id="dlbtn" href="#" download><?php echo $T['download']; ?></a>
                </div>
            </div>
            <div id="pane_wp" role="tabpanel" aria-labelledby="tab_wp" hidden>
                <p class="hint" style="margin-top:0"><?php echo $T['wordpress']; ?></p>
                <div class="btnrow">
                    <a class="btn ghost" href="<?php echo htmlspecialchars($baseUrl . '/' . strtolower($brand) . '-wordpress.zip'); ?>" download><?php echo $T['wpDownload']; ?></a>
                </div>
            </div>
        </div>

        <?php // Entwickler-Details (Parameter, JS-API, Theming, CSP) leben auf /api ?>
        <a class="devcard" href="api">
            <span class="devicon"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7l-5 5 5 5M16 7l5 5-5 5M13.5 4l-3 16"/></svg></span>
            <span class="devtext"><strong><?php echo htmlspecialchars($T['devApiT']); ?></strong><br>
            <span><?php echo htmlspecialchars($T['devApiX']); ?></span></span>
            <span class="devarrow" aria-hidden="true">&rsaquo;</span>
        </a>

    </div>
</div>

<section class="honesty">
    <h2><?php echo htmlspecialchars($T['honestyTitle']); ?></h2>
    <p class="honestylead"><?php echo $T['honestyLead']; ?></p>
    <div class="honestystats">
        <?php foreach ($T['honestyStats'] as $s) echo '<span class="stat"><b>' . htmlspecialchars($s[0]) . '</b> ' . htmlspecialchars($s[1]) . '</span>'; ?>
    </div>
    <div class="honestycols">
        <div class="hcol">
            <h3><?php echo htmlspecialchars($T['honestyIs']); ?></h3>
            <ul>
                <?php foreach ($T['honestyIsList'] as $li) echo '<li>' . htmlspecialchars($li) . '</li>'; ?>
            </ul>
        </div>
        <div class="hcol no">
            <h3><?php echo htmlspecialchars($T['honestyNot']); ?></h3>
            <ul>
                <?php foreach ($T['honestyNotList'] as $li) echo '<li>' . htmlspecialchars($li) . '</li>'; ?>
            </ul>
        </div>
    </div>
    <p class="honestyproof"><?php echo $T['honestyProof']; ?></p>
</section>

<!-- Website check: calls the Google PageSpeed Insights API directly from the
     visitor's browser; this server is not involved in the test -->
<section class="audit" id="check">
    <h2><?php echo htmlspecialchars($T['auditTitle']); ?></h2>
    <div class="card" style="margin-bottom:0">
        <p class="sectionintro" style="margin-top:0"><?php echo htmlspecialchars($T['auditIntro']); ?></p>
        <form id="auditform">
            <label for="audit_url"><?php echo htmlspecialchars($T['auditUrl']); ?></label>
            <div class="auditrow">
                <input type="text" id="audit_url" placeholder="https://www.example.com" autocomplete="url" inputmode="url">
                <button class="btn primary" id="audit_btn" type="submit"><?php echo htmlspecialchars($T['auditBtn']); ?></button>
            </div>
            <div class="auditopts">
                <label><input type="radio" name="audit_strategy" value="DESKTOP" checked data-label="<?php echo htmlspecialchars($T['auditDesktop']); ?>"> <?php echo htmlspecialchars($T['auditDesktop']); ?></label>
                <label><input type="radio" name="audit_strategy" value="MOBILE" data-label="<?php echo htmlspecialchars($T['auditMobile']); ?>"> <?php echo htmlspecialchars($T['auditMobile']); ?></label>
            </div>
        </form>
        <div aria-live="polite">
            <p id="audit_status" class="auditwait" hidden><?php echo htmlspecialchars($T['auditWait']); ?></p>
            <p id="audit_error" class="auditerror" hidden><?php echo htmlspecialchars($T['auditError']); ?></p>
            <div id="audit_result" hidden>
                <div class="scorewrap">
                    <div class="scorecircle" id="audit_score" aria-hidden="true">0</div>
                    <div>
                        <strong><?php echo htmlspecialchars($T['auditScoreLabel']); ?>: <span id="audit_score_text"></span>/100</strong>
                        <p class="hint" style="margin:4px 0 0"><?php echo htmlspecialchars($T['auditManual']); ?></p>
                    </div>
                </div>
                <p class="hint" id="audit_meta"></p>
                <h3 id="audit_list_title" hidden><?php echo htmlspecialchars($T['auditIssuesTitle']); ?></h3>
                <p id="audit_none" hidden><?php echo htmlspecialchars($T['auditNoIssues']); ?></p>
                <div id="audit_list"></div>
                <details class="auditextra" id="audit_manual_wrap" hidden>
                    <summary id="audit_manual_title"></summary>
                    <div class="auditsub" id="audit_manual"></div>
                </details>
                <details class="auditextra" id="audit_passed_wrap" hidden>
                    <summary id="audit_passed_title"></summary>
                    <ul class="auditpassed" id="audit_passed"></ul>
                </details>
                <div class="btnrow">
                    <button class="btn ghost" id="audit_pdf" type="button"><?php echo htmlspecialchars($T['auditPdf']); ?></button>
                </div>
            </div>
        </div>
        <p class="hint"><?php echo htmlspecialchars($T['auditPrivacy']); ?></p>
    </div>
</section>

<section class="faq">
    <h2><?php echo htmlspecialchars($T['faqTitle']); ?></h2>
    <?php foreach ($T['faq'] as $qa): ?>
    <details>
        <summary><?php echo htmlspecialchars($qa[0]); ?></summary>
        <p><?php echo htmlspecialchars($qa[1]); ?></p>
    </details>
    <?php endforeach; ?>
</section>

<!-- Demo content -->
<div class="demo" id="demo">
    <h2><?php echo $T['demoTitle']; ?></h2>
    <div class="card">
        <p style="margin-top:0"><?php echo $T['demoIntro']; ?></p>
        <h3><?php echo $T['demoSub']; ?></h3>
        <p>Lorem ipsum dolor sit amet, <a href="#demo">consetetur sadipscing elitr</a>, sed diam nonumy
        eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et
        accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est.</p>
        <blockquote style="margin:12px 0;padding:10px 16px;border-left:4px solid #e3ded2;color:#454f59">
        Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum
        dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit.</blockquote>
        <h3><?php echo $T['demoColors']; ?></h3>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin:12px 0">
            <span class="dchip" style="background:#d32f2f"><?php echo $T['demoC1']; ?></span>
            <span class="dchip" style="background:#388e3c"><?php echo $T['demoC2']; ?></span>
            <span class="dchip" style="background:#1976d2"><?php echo $T['demoC3']; ?></span>
            <span class="dchip" style="background:#f57c00"><?php echo $T['demoC4']; ?></span>
            <span class="dchip" style="background:#7b1fa2"><?php echo $T['demoC5']; ?></span>
        </div>
        <div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;margin:14px 0">
            <img alt="<?php echo htmlspecialchars($T['demoImgAlt']); ?>" width="180" height="110"
                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='110'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0' stop-color='%23e05263'/%3E%3Cstop offset='1' stop-color='%2310314f'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='180' height='110' fill='url(%23g)'/%3E%3Ccircle cx='60' cy='50' r='24' fill='%23ffffff' opacity='.8'/%3E%3C/svg%3E">
            <div class="spinner" title="<?php echo htmlspecialchars($T['demoSpinner']); ?>"></div>
        </div>
        <table>
            <tr><th><?php echo $T['demoTableF']; ?></th><th><?php echo $T['demoTableT']; ?></th></tr>
            <tr><td><?php echo $T['demoRow1a']; ?></td><td><?php echo $T['demoRow1b']; ?></td></tr>
            <tr><td><?php echo $T['demoRow2a']; ?></td><td><?php echo $T['demoRow2b']; ?></td></tr>
            <tr><td><?php echo $T['demoRow3a']; ?></td><td><?php echo $T['demoRow3b']; ?></td></tr>
        </table>
        <h3><?php echo $T['demoForm']; ?></h3>
        <form onsubmit="return false" aria-label="Demo">
            <div class="row2">
                <div><label for="d_name"><?php echo $T['demoName']; ?></label><input type="text" id="d_name"></div>
                <div><label for="d_mail"><?php echo $T['demoMail']; ?></label><input type="text" id="d_mail"></div>
            </div>
            <button class="btn primary" type="submit"><?php echo $T['demoSubmit']; ?></button>
        </form>
    </div>
</div>

</main>

<!-- Print view of the website check (print only, filled via JS).
     MUST remain a direct child of <body>: the print rule hides all other body
     children (body.printing > *:not(#printreport)); inside <main> the report
     would be invisible along with the hidden <main> - exactly how printing broke once. -->
<div id="printreport" aria-hidden="true">
    <div class="pr-head">
        <div class="pr-brand">
            <span class="pr-logo"><?php echo $logoSvg; ?></span>
            <div>
                <div class="pr-name"><?php echo htmlspecialchars($brand); ?></div>
                <div class="pr-tag"><?php echo htmlspecialchars($T['tagline']); ?></div>
            </div>
        </div>
        <div class="pr-url"><?php echo htmlspecialchars(preg_replace('#^https?://#', '', $baseUrl)); ?></div>
    </div>
    <div class="pr-ad"><?php echo htmlspecialchars($T['intro']); ?></div>
    <div id="pr_body"></div>
    <div class="pr-foot"><?php echo htmlspecialchars($T['auditManual']); ?> &middot; <?php echo htmlspecialchars($T['footerFree']); ?> &middot; <?php echo htmlspecialchars(preg_replace('#^https?://#', '', $baseUrl)); ?></div>
</div>

<footer class="site">
    <div class="inner">
        <a class="fbrand" href="<?php echo htmlspecialchars($baseUrl . '/' . $genLang); ?>"><?php echo $logoSvg; ?> <?php echo htmlspecialchars($brand); ?></a>
        <p><?php echo htmlspecialchars($T['footerFree']); ?></p>
        <p><?php echo htmlspecialchars($T['footerTech']); ?></p>
        <p><a href="<?php echo htmlspecialchars($baseUrl); ?>/impressum"><?php echo htmlspecialchars($T['imprint']); ?></a> &middot; <a href="<?php echo htmlspecialchars($baseUrl); ?>/api">API</a> &middot; <a href="<?php echo htmlspecialchars(LEGILO_GITHUB_URL); ?>" target="_blank" rel="noopener">GitHub</a></p>
        <?php if (LEGILO_DONATE_URL !== ''): ?>
        <div class="donaterow">
            <p><?php echo htmlspecialchars($T['donateText']); ?></p>
            <a class="donatebtn" href="<?php echo htmlspecialchars(LEGILO_DONATE_URL); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($T['donate']); ?></a>
        </div>
        <?php endif; ?>
    </div>
</footer>

<script>
(function () {
    'use strict';
    var SCHEMA_DEFAULTS = <?php echo json_encode($defaults, JSON_UNESCAPED_SLASHES); ?>;
    var ALL_FEATURES = <?php echo json_encode(LEGILO_FEATURES); ?>;
    var BASE = <?php echo json_encode($baseUrl, JSON_UNESCAPED_SLASHES); ?>;
    var JSFILE = <?php echo json_encode($brandJs); ?>;
    var APIGLOBAL = <?php echo json_encode($brand); ?>;

    var $ = function (id) { return document.getElementById(id); };
    var embed = $('embed');
    var previewScript = null;
    var previewCounter = 0;
    var reloadTimer = null;

    function setForm(cfg) {
        $('f_pos').value = cfg.pos;
        $('f_size').value = cfg.size;
        $('f_offx').value = cfg.offx;
        $('f_offy').value = cfg.offy;
        $('f_color').value = '#' + cfg.color;
        $('f_color2').value = '#' + cfg.color2;
        $('f_icon').value = cfg.icon;
        $('f_radius').value = cfg.radius;
        $('f_lang').value = cfg.lang;
        $('f_mobile').value = cfg.mobile;
        $('f_statement').value = cfg.statement || '';
        $('f_hide').checked = !!parseInt(cfg.hide, 10);
        $('f_hotkey').checked = !!parseInt(cfg.hotkey, 10);
        $('f_css').checked = cfg.css === 'none';
        var feats = Array.isArray(cfg.features) ? cfg.features : String(cfg.features).split(',');
        document.querySelectorAll('.f_feature').forEach(function (cb) {
            cb.checked = feats.indexOf(cb.value) !== -1;
        });
        // Mirror the feature order into the tile grid (unlisted tiles keep
        // their relative order at the end, pinned tiles stay first/last)
        var fgrid = document.querySelector('.fgrid');
        feats.forEach(function (k) {
            var cb = fgrid.querySelector('.f_feature[value="' + k + '"]');
            if (cb) fgrid.appendChild(cb.closest('.fcard'));
        });
        fgrid.querySelectorAll('.f_feature').forEach(function (cb) {
            if (feats.indexOf(cb.value) === -1) fgrid.appendChild(cb.closest('.fcard'));
        });
        var prof = fgrid.querySelector('.f_feature[value="profiles"]');
        var struct = fgrid.querySelector('.f_feature[value="structure"]');
        if (prof) fgrid.insertBefore(prof.closest('.fcard'), fgrid.firstChild);
        if (struct) fgrid.appendChild(struct.closest('.fcard'));
        $('radius_out').value = cfg.radius + ' %';
    }

    function collect() {
        var feats = [];
        document.querySelectorAll('.f_feature').forEach(function (cb) { if (cb.checked) feats.push(cb.value); });
        if (!feats.length) feats = ALL_FEATURES.slice();
        return {
            pos: $('f_pos').value,
            size: $('f_size').value,
            offx: String(parseInt($('f_offx').value, 10) || 0),
            offy: String(parseInt($('f_offy').value, 10) || 0),
            color: $('f_color').value.replace('#', ''),
            color2: $('f_color2').value.replace('#', ''),
            icon: $('f_icon').value,
            radius: String($('f_radius').value),
            lang: $('f_lang').value,
            mobile: $('f_mobile').value,
            statement: $('f_statement').value.trim(),
            hide: $('f_hide').checked ? '1' : '0',
            hotkey: $('f_hotkey').checked ? '1' : '0',
            css: $('f_css').checked ? 'none' : 'base',
            features: feats
        };
    }

    function paramsString(cfg) {
        var p = new URLSearchParams();
        Object.keys(SCHEMA_DEFAULTS).forEach(function (k) {
            var def = SCHEMA_DEFAULTS[k];
            var val = cfg[k];
            if (k === 'features') {
                // Order-sensitive: the tile order in the configurator IS the
                // panel order, so a reordering must show up in the URL.
                var same = Array.isArray(val) && val.length === def.length &&
                    def.every(function (f, i) { return val[i] === f; });
                if (!same) p.set('features', val.join(','));
                return;
            }
            if (String(val) !== String(def) && val !== '') p.set(k, val);
        });
        return p.toString().replace(/%2C/g, ',');
    }

    function embedCode(ps) {
        return '<script src="' + BASE + '/' + JSFILE + (ps ? '?' + ps : '') + '" defer><\/script>';
    }

    function reloadWidget(ps, openAfter) {
        clearTimeout(reloadTimer);
        reloadTimer = setTimeout(function () {
            if (window[APIGLOBAL]) { try { window[APIGLOBAL].destroy(); } catch (e) {} }
            if (previewScript && previewScript.parentNode) previewScript.parentNode.removeChild(previewScript);
            previewScript = document.createElement('script');
            // One-time cache buster: the preview must never show a widget build
            // up to 24h old from the browser/Cloudflare cache.
            previewScript.src = BASE + '/' + JSFILE + (ps ? '?' + ps + '&' : '?') + '_=' + Date.now() + (++previewCounter);
            previewScript.defer = true;
            // After a settings change, open the panel right away (desktop only,
            // on small screens it would cover the whole configurator)
            if (openAfter && window.matchMedia('(min-width: 981px)').matches) {
                previewScript.addEventListener('load', function () {
                    if (window[APIGLOBAL]) window[APIGLOBAL].open();
                });
            }
            document.body.appendChild(previewScript);
        }, 350);
    }

    // Fit the textarea to its content (compact single line, grows for long URLs)
    function fitCode(ta) {
        ta.style.height = 'auto';
        ta.style.height = Math.max(56, ta.scrollHeight + 2) + 'px';
    }

    function update(skipEmbed, openAfter) {
        var cfg = collect();
        var ps = paramsString(cfg);
        if (!skipEmbed) { embed.value = embedCode(ps); fitCode(embed); }
        $('dlbtn').href = BASE + '/download' + (ps ? '?' + ps : '');
        $('radius_out').value = cfg.radius + ' %';
        reloadWidget(ps, openAfter);
    }

    // Bidirectional: parse pasted/edited code and populate the form
    // (also understands codes using the old file name widget.js)
    function parseEmbed() {
        var re = new RegExp('(?:' + JSFILE.replace('.', '\\.') + '|widget\\.js)(?:\\?([^"\'\\s>]*))?');
        var m = embed.value.replace(/&amp;/g, '&').match(re);
        if (!m) return;
        var p = new URLSearchParams(m[1] || '');
        var cfg = {};
        Object.keys(SCHEMA_DEFAULTS).forEach(function (k) {
            var def = SCHEMA_DEFAULTS[k];
            if (!p.has(k)) { cfg[k] = Array.isArray(def) ? def.slice() : def; return; }
            cfg[k] = k === 'features' ? p.get(k).split(',') : p.get(k);
        });
        ['color', 'color2'].forEach(function (k) {
            if (!/^[0-9a-f]{6}$/i.test(cfg[k])) cfg[k] = SCHEMA_DEFAULTS[k];
        });
        setForm(cfg);
        update(true, true);
    }

    document.querySelectorAll('select, input').forEach(function (el) {
        if (el.closest('.demo') || el.closest('.audit')) return;
        el.addEventListener('input', function () { update(false, true); });
        el.addEventListener('change', function () { update(false, true); });
    });
    embed.addEventListener('input', function () { parseEmbed(); fitCode(embed); });

    // Tiles are sortable: drag and drop plus Alt+arrow keys; the DOM order
    // of the tiles is collected as the features order (= panel order).
    // Profiles and structure have fixed spots in the panel (chips on top,
    // structure at the bottom), so their tiles are pinned first/last.
    var sortGrid = document.querySelector('.fgrid');
    var dragEl = null;
    function pinFixed() {
        var prof = sortGrid.querySelector('.f_feature[value="profiles"]');
        var struct = sortGrid.querySelector('.f_feature[value="structure"]');
        if (prof) sortGrid.insertBefore(prof.closest('.fcard'), sortGrid.firstChild);
        if (struct) sortGrid.appendChild(struct.closest('.fcard'));
    }
    sortGrid.querySelectorAll('.fcard:not(.fixed)').forEach(function (card) {
        card.draggable = true;
        card.addEventListener('dragstart', function (e) {
            dragEl = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            try { e.dataTransfer.setData('text/plain', ''); } catch (err) { }
        });
        card.addEventListener('dragend', function () {
            card.classList.remove('dragging');
            dragEl = null;
            pinFixed();
            update(false, true);
        });
        card.querySelector('input').addEventListener('keydown', function (e) {
            if (!e.altKey || (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight')) return;
            e.preventDefault();
            var sib = e.key === 'ArrowLeft' ? card.previousElementSibling : card.nextElementSibling;
            if (!sib || sib.classList.contains('fixed')) return;
            sortGrid.insertBefore(card, e.key === 'ArrowLeft' ? sib : sib.nextSibling);
            this.focus();
            update(false, true);
        });
    });
    sortGrid.addEventListener('dragover', function (e) {
        if (!dragEl) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var target = e.target.closest('.fcard');
        if (!target || target === dragEl || target.classList.contains('fixed')) return;
        var r = target.getBoundingClientRect();
        sortGrid.insertBefore(dragEl, (e.clientX - r.left) < r.width / 2 ? target : target.nextSibling);
    });
    sortGrid.addEventListener('drop', function (e) { e.preventDefault(); });

    // Wer zum Demo-Bereich springt oder scrollt, bekommt das Panel einmal
    // automatisch geoeffnet - direkt ausprobieren statt suchen.
    var demoSec = document.getElementById('demo');
    if (demoSec && 'IntersectionObserver' in window) {
        var demoOpened = false;
        new IntersectionObserver(function (entries, io) {
            entries.forEach(function (en) {
                if (en.isIntersecting && !demoOpened) {
                    demoOpened = true;
                    io.disconnect();
                    if (window[APIGLOBAL]) window[APIGLOBAL].open();
                }
            });
        }, { threshold: 0.2 }).observe(demoSec);
    }

    document.querySelectorAll('.copybtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var ta = $(btn.dataset.target);
            var label = btn.textContent;
            (navigator.clipboard ? navigator.clipboard.writeText(ta.value) : Promise.reject())
                .then(function () { btn.textContent = btn.dataset.copied; })
                .catch(function () { ta.select(); document.execCommand('copy'); btn.textContent = btn.dataset.copied; });
            setTimeout(function () { btn.textContent = label; }, 1500);
        });
    });

    // Tabs: embed via URL / self-hosting
    var tabs = document.querySelectorAll('.tabbar button');
    tabs.forEach(function (tb) {
        tb.addEventListener('click', function () {
            tabs.forEach(function (o) {
                var sel = o === tb;
                o.classList.toggle('active', sel);
                o.setAttribute('aria-selected', sel ? 'true' : 'false');
                $(o.getAttribute('aria-controls')).hidden = !sel;
            });
        });
    });

    setForm(Object.assign({}, SCHEMA_DEFAULTS, { features: SCHEMA_DEFAULTS.features.slice() }));
    update(false);
})();

// Website check: queries the Google PageSpeed Insights API directly from the
// visitor's browser (CORS-enabled; optional API key, quota counts per visitor IP).
(function () {
    'use strict';
    var $ = function (id) { return document.getElementById(id); };
    var form = $('auditform');
    if (!form) return;
    var LOCALE = <?php echo json_encode($genLang); ?>;
    var ELEMENTS_LABEL = <?php echo json_encode($T['auditElements']); ?>;
    var MANUAL_TITLE = <?php echo json_encode($T['auditManualTitle']); ?>;
    var PASSED_TITLE = <?php echo json_encode($T['auditPassedTitle']); ?>;
    var MORE_LABEL = <?php echo json_encode($T['auditMore']); ?>;
    var BRAND_FILE = <?php echo json_encode(strtolower($brand)); ?>;
    var ISSUES_TITLE = $('audit_list_title').textContent;
    var PSI_KEY = <?php echo json_encode(LEGILO_PSI_KEY); ?>;
    var running = false;

    // Lighthouse descriptions contain Markdown links [text](URL)
    function appendWithLinks(text, target) {
        var re = /\[([^\]]+)\]\((https?:[^)\s]+)\)/g;
        var last = 0, m;
        while ((m = re.exec(text))) {
            target.appendChild(document.createTextNode(text.slice(last, m.index)));
            var a = document.createElement('a');
            a.href = m[2];
            a.target = '_blank';
            a.rel = 'noopener';
            a.textContent = m[1];
            target.appendChild(a);
            last = m.index + m[0].length;
        }
        target.appendChild(document.createTextNode(text.slice(last)));
    }

    // One finding as an expandable detail: description plus the specific
    // affected elements (label, HTML snippet, selector, axe explanation).
    function renderFinding(f) {
        var d = document.createElement('details');
        var s = document.createElement('summary');
        var items = (f.audit.details && f.audit.details.items) || [];
        var n = items.length;
        s.textContent = f.audit.title + (n ? ' (' + ELEMENTS_LABEL + ': ' + n + ')' : '');
        d.appendChild(s);
        var p = document.createElement('p');
        appendWithLinks(f.audit.description || '', p);
        d.appendChild(p);
        var shown = items.slice(0, 10);
        var ul = document.createElement('ul');
        ul.className = 'auditnodes';
        shown.forEach(function (it) {
            var node = it.node || {};
            var li = document.createElement('li');
            if (node.nodeLabel && node.nodeLabel !== node.snippet) {
                var lbl = document.createElement('div');
                lbl.className = 'nlabel';
                lbl.textContent = node.nodeLabel;
                li.appendChild(lbl);
            }
            if (node.snippet) {
                var code = document.createElement('code');
                code.textContent = node.snippet;
                li.appendChild(code);
            }
            if (node.explanation) {
                var ex = document.createElement('div');
                ex.className = 'nexp';
                ex.textContent = node.explanation;
                li.appendChild(ex);
            }
            if (node.selector) {
                var sel = document.createElement('div');
                sel.className = 'nsel';
                sel.textContent = node.selector;
                li.appendChild(sel);
            }
            if (li.childNodes.length) ul.appendChild(li);
        });
        if (n > shown.length) {
            var more = document.createElement('li');
            more.className = 'nmore';
            more.textContent = '+ ' + (n - shown.length) + ' ' + MORE_LABEL;
            ul.appendChild(more);
        }
        if (ul.childNodes.length) d.appendChild(ul);
        return d;
    }

    function render(data, strategyLabel) {
        var lr = data.lighthouseResult || {};
        var cat = lr.categories && lr.categories.accessibility;
        if (!cat) throw new Error('no result');
        var score = Math.round((cat.score || 0) * 100);
        var circle = $('audit_score');
        circle.textContent = String(score);
        circle.className = 'scorecircle' + (score < 50 ? ' bad' : score < 90 ? ' mid' : '');
        $('audit_score_text').textContent = String(score);

        var groups = lr.categoryGroups || {};
        var fails = [], manual = [], passed = [];
        (cat.auditRefs || []).forEach(function (ref) {
            var a = lr.audits && lr.audits[ref.id];
            if (!a) return;
            if (a.scoreDisplayMode === 'manual') { manual.push(a); return; }
            if (typeof a.score !== 'number') return;
            if (a.score < 1) fails.push({ audit: a, weight: ref.weight || 0, group: ref.group || '' });
            else passed.push(a);
        });
        fails.sort(function (x, y) { return y.weight - x.weight; });

        // Findings grouped like the Lighthouse report (group titles arrive localized)
        var order = [], byGroup = {};
        fails.forEach(function (f) {
            if (!byGroup[f.group]) { byGroup[f.group] = []; order.push(f.group); }
            byGroup[f.group].push(f);
        });
        var list = $('audit_list');
        list.textContent = '';
        order.forEach(function (gk) {
            var g = groups[gk];
            if (g && g.title) {
                var h = document.createElement('h4');
                h.className = 'auditgroup';
                h.textContent = g.title;
                list.appendChild(h);
            }
            byGroup[gk].forEach(function (f) { list.appendChild(renderFinding(f)); });
        });
        $('audit_list_title').textContent = ISSUES_TITLE + ' (' + fails.length + ')';
        $('audit_list_title').hidden = !fails.length;
        $('audit_none').hidden = !!fails.length;

        // Items Lighthouse marks as "check manually"
        var mbox = $('audit_manual');
        mbox.textContent = '';
        manual.forEach(function (a) {
            var d = document.createElement('details');
            var s = document.createElement('summary');
            s.textContent = a.title;
            d.appendChild(s);
            var p = document.createElement('p');
            appendWithLinks(a.description || '', p);
            d.appendChild(p);
            mbox.appendChild(d);
        });
        $('audit_manual_title').textContent = MANUAL_TITLE + ' (' + manual.length + ')';
        $('audit_manual_wrap').hidden = !manual.length;
        $('audit_manual_wrap').open = false;

        // Passed automatic checks
        var plist = $('audit_passed');
        plist.textContent = '';
        passed.forEach(function (a) {
            var li = document.createElement('li');
            li.textContent = a.title;
            plist.appendChild(li);
        });
        $('audit_passed_title').textContent = PASSED_TITLE + ' (' + passed.length + ')';
        $('audit_passed_wrap').hidden = !passed.length;
        $('audit_passed_wrap').open = false;

        var meta = [lr.finalDisplayedUrl || lr.finalUrl || lr.requestedUrl || ''];
        if (strategyLabel) meta.push(strategyLabel);
        if (lr.lighthouseVersion) meta.push('Lighthouse ' + lr.lighthouseVersion);
        if (lr.fetchTime) { try { meta.push(new Date(lr.fetchTime).toLocaleString(LOCALE)); } catch (e) {} }
        $('audit_meta').textContent = meta.filter(Boolean).join('  |  ');

        $('audit_result').hidden = false;
    }

    form.addEventListener('submit', function (ev) {
        ev.preventDefault();
        if (running) return;
        // Tolerant input: strip quotes/whitespace, fix common scheme typos
        // (http:/, https//, //), otherwise prepend https://.
        var url = $('audit_url').value.trim().replace(/^["'<\s]+|["'>\s]+$/g, '');
        if (!url) return;
        var m = url.match(/^(https?)\s*(?:[:;]\s*\/{0,2}|\/{1,2})\s*(.+)$/i);
        if (m) {
            url = m[1].toLowerCase() + '://' + m[2].replace(/^[\/\s]+/, '');
        } else {
            url = 'https://' + url.replace(/^\/+/, '');
        }
        $('audit_url').value = url;
        running = true;
        $('audit_btn').disabled = true;
        $('audit_status').hidden = false;
        $('audit_error').hidden = true;
        $('audit_result').hidden = true;
        var strat = form.querySelector('input[name="audit_strategy"]:checked');
        var api = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed'
            + '?category=ACCESSIBILITY&strategy=' + (strat ? strat.value : 'DESKTOP')
            + '&locale=' + encodeURIComponent(LOCALE)
            + (PSI_KEY ? '&key=' + encodeURIComponent(PSI_KEY) : '')
            + '&url=' + encodeURIComponent(url);
        fetch(api).then(function (r) {
            if (!r.ok) throw new Error('http ' + r.status);
            return r.json();
        }).then(function (data) {
            render(data, strat ? strat.dataset.label : '');
        }).catch(function () {
            $('audit_error').hidden = false;
        }).then(function () {
            running = false;
            $('audit_btn').disabled = false;
            $('audit_status').hidden = true;
        });
    });

    // PDF report: clone the result into the print view (#printreport), expand all
    // details and open the browser print dialog (target "Save as PDF").
    // Deliberately without a PDF library so the page stays free of third-party code.
    $('audit_pdf').addEventListener('click', function () {
        var body = $('pr_body');
        body.textContent = '';
        var clone = $('audit_result').cloneNode(true);
        clone.hidden = false;
        clone.querySelectorAll('details').forEach(function (d) { d.open = true; });
        clone.querySelectorAll('[id]').forEach(function (el) { el.removeAttribute('id'); });
        clone.removeAttribute('id');
        body.appendChild(clone);
        var oldTitle = document.title;
        var host = ($('audit_url').value || '').replace(/^https?:\/\//i, '').replace(/[\/?#].*$/, '');
        document.title = BRAND_FILE + '-report-' + (host || 'website');
        document.body.classList.add('printing');
        var cleanup = function () {
            document.body.classList.remove('printing');
            document.title = oldTitle;
            window.removeEventListener('afterprint', cleanup);
        };
        window.addEventListener('afterprint', cleanup);
        window.print();
        setTimeout(cleanup, 1000);
    });
})();
</script>
</body>
</html>
