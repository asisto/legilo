<?php
/**
 * WCAG-Widget: Mini-Router.
 *
 *   /WCAG-Widget/                  -> 302 auf /<lang> (Accept-Language, sonst en)
 *   /WCAG-Widget/<lang>            -> Konfigurator (alle Sprachen in LEGILO_GEN_LANGS)
 *   /WCAG-Widget/legilo.js?...     -> generiertes Widget-JS (Config eingebacken, cachebar;
 *                                     Dateiname = strtolower(LEGILO_BRAND) . '.js')
 *   /WCAG-Widget/download?...      -> dasselbe JS als Datei-Download (self-hosted, Font eingebettet)
 *   /WCAG-Widget/generate[/<lang>], /widget.js -> alte URLs, leiten auf die neuen weiter
 */

require __DIR__ . '/config.php';

$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = trim(rawurldecode(substr($path, strlen($base))), '/');

if ($route === '' || $route === 'index.php' || $route === 'generate') {
    header('Location: ' . $base . '/' . legilo_detect_gen_lang(), true, 302);
    exit;
}
if (preg_match('#^generate/([a-z]{2})$#', $route, $m)) {
    header('Location: ' . $base . '/' . $m[1], true, 301);
    exit;
}
if (preg_match('#^([a-z]{2})$#', $route, $m) && in_array($m[1], LEGILO_GEN_LANGS, true)) {
    $genLang = $m[1];
    require __DIR__ . '/generate.php';
    exit;
}
switch ($route) {
    case strtolower(LEGILO_BRAND) . '.js':
        legilo_serve_js(false);
        exit;
    case 'widget.js': // Alt-URL -> gebrandeter Dateiname, Query bleibt erhalten
        $qs = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : '';
        header('Location: ' . $base . '/' . strtolower(LEGILO_BRAND) . '.js' . $qs, true, 301);
        exit;
    case 'download':
        legilo_serve_js(true);
        exit;
    case 'impressum':
    case 'imprint':
        require __DIR__ . '/imprint.php';
        exit;
    case 'api':
        require __DIR__ . '/api.php';
        exit;
    case 'robots.txt':
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\nAllow: /\nSitemap: " . legilo_base_url() . "/sitemap.xml\n";
        exit;
    case 'sitemap.xml':
        legilo_sitemap();
        exit;
    default:
        legilo_404();
        exit;
}

function legilo_detect_gen_lang() {
    $header = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']) : '';
    foreach (explode(',', $header) as $part) {
        $code = substr(trim($part), 0, 2);
        if (in_array($code, LEGILO_GEN_LANGS, true)) return $code;
    }
    return 'en';
}

/* ---------------------------------------------------------------- */

function legilo_serve_js($download) {
    $cfg = legilo_params();
    $baseUrl = legilo_base_url();

    $tpl = file_get_contents(__DIR__ . '/widget/widget.js');
    if ($tpl === false) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
        echo '/* widget template missing */';
        return;
    }

    // Dyslexie-Font: Download-Variante bettet ihn als data-URI ein (läuft ohne diesen
    // Server), URL-Variante referenziert unser selbst gehostetes woff2.
    $fontCss = '';
    $fontFile = __DIR__ . '/assets/fonts/opendyslexic-400.woff2';
    if (is_file($fontFile)) {
        if ($download) {
            $src = 'data:font/woff2;base64,' . base64_encode(file_get_contents($fontFile));
        } else {
            $src = $baseUrl . '/assets/fonts/opendyslexic-400.woff2';
        }
        $fontCss = "@font-face{font-family:'OpenDyslexic';src:url('" . $src
            . "') format('woff2');font-weight:400;font-style:normal;font-display:swap;}";
    }
    $cfg['fontCss'] = $fontCss;
    // Ziel des kleinen Info-Links im Panel-Fuss
    $cfg['homeUrl'] = $baseUrl;

    // Bei fixer Sprache nur diese + en (Fallback) einbacken, bei "auto" alle Sprachen
    $langs = $cfg['lang'] === 'auto' ? LEGILO_LANGS : array_unique(array($cfg['lang'], 'en'));
    $i18n = array();
    foreach ($langs as $lc) {
        $file = __DIR__ . '/widget/lang/' . $lc . '.json';
        if (is_file($file)) $i18n[$lc] = json_decode(file_get_contents($file), true);
    }

    $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    $js = str_replace(
        array('/*__CFG__*/{}', '/*__I18N__*/{}', '__VERSION__', '__BRAND__'),
        array(json_encode($cfg, $flags), json_encode($i18n, $flags), LEGILO_VERSION, LEGILO_BRAND),
        $tpl
    );
    $js = legilo_minify_js($js);

    $etag = 'W/"' . md5($js) . '"';
    header('Content-Type: application/javascript; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('ETag: ' . $etag);

    if ($download) {
        header('Content-Disposition: attachment; filename="' . strtolower(LEGILO_BRAND) . '.js"');
        header('Cache-Control: no-store');
    } else {
        header('Cache-Control: public, max-age=86400');
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            http_response_code(304);
            return;
        }
    }
    echo $js;
}

/** Sitemap: alle Sprachversionen der Projektseite inkl. hreflang-Alternates. */
function legilo_sitemap() {
    $base = legilo_base_url();
    header('Content-Type: application/xml; charset=utf-8');
    $alts = '';
    foreach (LEGILO_GEN_LANGS as $al) {
        $alts .= '  <xhtml:link rel="alternate" hreflang="' . $al . '" href="' . $base . '/' . $al . '"/>' . "\n";
    }
    $alts .= '  <xhtml:link rel="alternate" hreflang="x-default" href="' . $base . '/en"/>' . "\n";
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
    foreach (LEGILO_GEN_LANGS as $lc) {
        echo " <url>\n  <loc>" . $base . '/' . $lc . "</loc>\n" . $alts . " </url>\n";
    }
    echo " <url>\n  <loc>" . $base . "/api</loc>\n </url>\n";
    echo "</urlset>\n";
}

/**
 * Konservatives zeilenweises Minify: Einrueckung, Leerzeilen und ganze
 * Kommentarzeilen entfernen. Beruehrt nie den Inhalt einer Code-Zeile
 * (kein Risiko fuer Strings mit "//" oder "/*"). Der /*!-Header mit dem
 * Lizenzhinweis bleibt erhalten.
 */
function legilo_minify_js($js) {
    $out = array();
    $inComment = false; // normaler Blockkommentar
    $inLicense = false; // /*!-Header, wird behalten
    foreach (explode("\n", $js) as $line) {
        $t = trim($line);
        if ($inLicense) {
            $out[] = $line;
            if (strpos($t, '*/') !== false) $inLicense = false;
            continue;
        }
        if ($inComment) {
            if (strpos($t, '*/') !== false) $inComment = false;
            continue;
        }
        if ($t === '') continue;
        if (substr($t, 0, 3) === '/*!') {
            $out[] = $line;
            if (strpos($t, '*/') === false) $inLicense = true;
            continue;
        }
        if (substr($t, 0, 2) === '/*') {
            if (strpos($t, '*/') === false) $inComment = true;
            continue;
        }
        if (substr($t, 0, 2) === '//') continue;
        $out[] = $t;
    }
    return implode("\n", $out);
}

function legilo_404() {
    http_response_code(404);
    header('Content-Type: text/html; charset=iso-8859-1');
    // Bewusst die Apache-Standardseite nachgebaut: der Ordner-Aufruf soll nichts verraten.
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n<html><head>\n"
        . "<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "<p>The requested URL was not found on this server.</p>\n</body></html>\n";
}
