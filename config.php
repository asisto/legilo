<?php
/**
 * WCAG-Widget: Options-Schema und Validierung.
 * EINE Quelle der Wahrheit: wird vom JS-Generator (index.php) UND von der
 * Konfigurator-Seite (generate.php) verwendet.
 */

const LEGILO_VERSION = '0.1.0';

// Projektname (Arbeitstitel, zentral aenderbar; erscheint auf der Projektseite)
const LEGILO_BRAND = 'Legilo';

// Spenden-Link im Footer der Projektseite. Leer = Button wird nicht angezeigt.
const LEGILO_DONATE_URL = '';

// Oeffentliches Git-Repository (Footer-Link, WordPress-Plugin-Hinweis)
const LEGILO_GITHUB_URL = 'https://github.com/asisto/legilo';

// API-Key fuer den Website-Check (Google PageSpeed Insights, generate.php).
// Der Key liegt in config.local.php (gitignored, geht NICHT ins oeffentliche Repo);
// er steht zwar ohnehin sichtbar im Seiten-JS, ist aber per HTTP-Referrer auf
// legilo.eu beschraenkt. Ohne config.local.php bleibt der Check keyless und
// scheitert praktisch immer an der ausgeschoepften Anonym-Quota (429).
if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}
if (!defined('LEGILO_PSI_KEY')) {
    // Faengt auch eine noch nicht umbenannte config.local.php ab (alter Prefix)
    define('LEGILO_PSI_KEY', defined('A11YW_PSI_KEY') ? A11YW_PSI_KEY : '');
}

const LEGILO_FEATURES = array(
    'profiles', 'fontsize', 'spacing', 'font', 'align', 'contrast', 'saturation', 'bluefilter',
    'colorblind', 'links', 'focus', 'cursor', 'guide', 'mask', 'animations', 'images', 'tts', 'structure',
);

// Verfuegbare Widget-Sprachen (widget/lang/<code>.json)
const LEGILO_LANGS = array(
    'en', 'de', 'it', 'fr', 'es', 'pt', 'nl', 'pl', 'tr',
    'ru', 'uk', 'ar', 'he', 'zh', 'ja', 'ko', 'hi',
    'bg', 'cs', 'da', 'el', 'et', 'fa', 'fi', 'ga', 'hr', 'hu',
    'id', 'lt', 'lv', 'mt', 'ro', 'sk', 'sl', 'sv', 'th', 'vi',
);

// Sprachen der Generator-Oberflaeche (/<code>, Uebersetzungen in translations/<code>.php).
// Muss Teilmenge von LEGILO_LANGS sein (Feature-Labels kommen aus widget/lang/<code>.json).
// Reihenfolge = alphabetisch nach Code (so findet man sein Kuerzel im Umschalter am schnellsten).
const LEGILO_GEN_LANGS = array('de', 'en', 'es', 'fr', 'it', 'nl', 'pl', 'pt', 'ru', 'tr', 'uk');

/** Native Sprachnamen fuer Auswahlfelder. */
function legilo_lang_names() {
    return array(
        'en' => 'English', 'de' => 'Deutsch', 'it' => 'Italiano', 'fr' => 'Francais',
        'es' => 'Espanol', 'pt' => 'Portugues', 'nl' => 'Nederlands', 'pl' => 'Polski',
        'tr' => 'Turkce', 'ru' => 'Russkij (Русский)', 'uk' => 'Ukrainska (Українська)',
        'ar' => 'Arabi (العربية)', 'he' => 'Ivrit (עברית)', 'zh' => 'Zhongwen (中文)',
        'ja' => 'Nihongo (日本語)', 'ko' => 'Hangugeo (한국어)', 'hi' => 'Hindi (हिन्दी)',
        'bg' => 'Balgarski (Български)', 'cs' => 'Cestina', 'da' => 'Dansk',
        'el' => 'Ellinika (Ελληνικά)', 'et' => 'Eesti', 'fa' => 'Farsi (فارسی)',
        'fi' => 'Suomi', 'ga' => 'Gaeilge', 'hr' => 'Hrvatski', 'hu' => 'Magyar',
        'id' => 'Bahasa Indonesia', 'lt' => 'Lietuviu', 'lv' => 'Latviesu',
        'mt' => 'Malti', 'ro' => 'Romana', 'sk' => 'Slovencina', 'sl' => 'Slovenscina',
        'sv' => 'Svenska', 'th' => 'Thai (ไทย)', 'vi' => 'Tieng Viet (Tiếng Việt)',
    );
}

function legilo_schema() {
    return array(
        'pos' => array('type' => 'enum', 'values' => array('tl', 'tc', 'tr', 'lc', 'rc', 'bl', 'bc', 'br'), 'default' => 'br'),
        'offx' => array('type' => 'int', 'min' => 0, 'max' => 400, 'default' => 16),
        'offy' => array('type' => 'int', 'min' => 0, 'max' => 400, 'default' => 16),
        'color' => array('type' => 'hex', 'default' => '0b5fb0'),
        'color2' => array('type' => 'hex', 'default' => 'ffffff'),
        'size' => array('type' => 'enum', 'values' => array('s', 'm', 'l'), 'default' => 'm'),
        'radius' => array('type' => 'int', 'min' => 0, 'max' => 50, 'default' => 50),
        'icon' => array('type' => 'enum', 'values' => array('access', 'person', 'eye', 'aa'), 'default' => 'access'),
        'lang' => array('type' => 'enum', 'values' => array_merge(array('auto'), LEGILO_LANGS), 'default' => 'auto'),
        'features' => array('type' => 'list', 'values' => LEGILO_FEATURES, 'default' => LEGILO_FEATURES),
        'mobile' => array('type' => 'enum', 'values' => array('show', 'hide'), 'default' => 'show'),
        'hide' => array('type' => 'bool', 'default' => 0),
        'hotkey' => array('type' => 'bool', 'default' => 0),
        // css=none: Profi-Modus ohne eingebautes Panel-Styling (Licht-DOM statt
        // Shadow-DOM, Betreiber bringt komplett eigenes CSS mit)
        'css' => array('type' => 'enum', 'values' => array('base', 'none'), 'default' => 'base'),
        'statement' => array('type' => 'url', 'default' => ''),
    );
}

function legilo_validate($raw, $def) {
    if ($raw === null || $raw === '') return $def['default'];
    switch ($def['type']) {
        case 'enum':
            $raw = strtolower(trim($raw));
            return in_array($raw, $def['values'], true) ? $raw : $def['default'];
        case 'int':
            if (!is_numeric($raw)) return $def['default'];
            return max($def['min'], min($def['max'], (int)$raw));
        case 'hex':
            $raw = ltrim(strtolower(trim($raw)), '#');
            if (preg_match('/^[0-9a-f]{6}$/', $raw)) return $raw;
            if (preg_match('/^[0-9a-f]{3}$/', $raw)) {
                return $raw[0] . $raw[0] . $raw[1] . $raw[1] . $raw[2] . $raw[2];
            }
            return $def['default'];
        case 'bool':
            return in_array(strtolower(trim($raw)), array('1', 'true', 'yes', 'on'), true) ? 1 : 0;
        case 'list':
            $items = array();
            foreach (explode(',', strtolower($raw)) as $item) {
                $item = trim($item);
                if (in_array($item, $def['values'], true) && !in_array($item, $items, true)) {
                    $items[] = $item;
                }
            }
            return count($items) ? $items : $def['default'];
        case 'url':
            $raw = trim($raw);
            if (preg_match('#^https?://#i', $raw) || (strlen($raw) && $raw[0] === '/')) {
                return substr($raw, 0, 500);
            }
            return $def['default'];
    }
    return $def['default'];
}

/** Alle Parameter aus $_GET gegen das Schema validieren. Unbekannte Keys werden ignoriert. */
function legilo_params() {
    $out = array();
    foreach (legilo_schema() as $key => $def) {
        $out[$key] = legilo_validate(isset($_GET[$key]) ? $_GET[$key] : null, $def);
    }
    return $out;
}

/**
 * CSS-Grundgeruest fuer css=none (Profi-Modus): laeuft auf der Projektseite
 * live mit (Vorschau) und steht auf /api zum Kopieren.
 */
function legilo_css_skeleton() {
    return <<<'CSS'
/* Legilo skeleton for css=none - build your own design on top.
 * The widget ships only a tiny functional layer (panel show/hide,
 * screenreader helper, icon sizes). Everything visual is up to you.
 * Launcher position (pos/offx/offy) still comes from the embed URL. */

#legilo-host {
    /* Starting frame - adjust freely */
    .wrap { position: relative; }
    .trigger { width: 48px; height: 48px; }
    .panel {
        position: absolute; bottom: calc(100% + 10px); right: 0;
        width: 340px; max-height: 70vh;
        background: #fff; color: #111;
    }
    .body { overflow-y: auto; padding: 10px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
    .ft { min-height: 44px; cursor: pointer; text-align: start; }

    /* Your design */
    .head { } .head h2 { } .close { }
    .ptitle { } .profiles { } .prof { } .prof.on { }
    .ft.on { } .ft .top { } .ft .lbl { } .ft .st { } .st .dot { } .st .dot.on { }
    .backrow { } .back { } .structure-list { }
    .foot { } .row { } .reset { } .hidew { } .brandbtn { }
}
CSS;
}

/** Absolute Basis-URL dieses Ordners, z.B. http://local/00000_mizu/003_my.mizu.co/WCAG-Widget */
function legilo_base_url() {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return ($https ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $base;
}

/*
 * Uebergangs-Aliase fuer den alten a11yw-Prefix (Umbenennung 2026-07-05):
 * schuetzen vor Fatal Errors, falls der Server-Sync die Dateien nicht in
 * einem Rutsch uebertraegt und alte Aufrufer noch diese Namen erwarten.
 * Nach dem naechsten vollstaendigen Deploy loeschbar.
 */
if (!defined('A11YW_BRAND')) {
    define('A11YW_VERSION', LEGILO_VERSION);
    define('A11YW_BRAND', LEGILO_BRAND);
    define('A11YW_DONATE_URL', LEGILO_DONATE_URL);
    define('A11YW_GITHUB_URL', LEGILO_GITHUB_URL);
    define('A11YW_FEATURES', LEGILO_FEATURES);
    define('A11YW_LANGS', LEGILO_LANGS);
    define('A11YW_GEN_LANGS', LEGILO_GEN_LANGS);
}
if (!defined('A11YW_PSI_KEY')) {
    define('A11YW_PSI_KEY', LEGILO_PSI_KEY);
}
function a11yw_lang_names() { return legilo_lang_names(); }
function a11yw_schema() { return legilo_schema(); }
function a11yw_validate($raw, $def) { return legilo_validate($raw, $def); }
function a11yw_params() { return legilo_params(); }
function a11yw_base_url() { return legilo_base_url(); }
