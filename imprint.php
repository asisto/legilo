<?php
/**
 * Legal notice (/impressum, /imprint) - nur Englisch, private Angaben
 * (bewusst ohne Firma/MwSt-Nummer, das Projekt laeuft privat).
 * Alle Sprachversionen der Projektseite verlinken hierher.
 */
if (!function_exists('legilo_schema')) {
    require __DIR__ . '/config.php';
}
$brand = LEGILO_BRAND;
$baseUrl = legilo_base_url();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<meta name="theme-color" content="#ffd35c">
<link rel="icon" type="image/svg+xml" href="<?php echo htmlspecialchars($baseUrl); ?>/assets/favicon.svg">
<title>Legal notice - <?php echo htmlspecialchars($brand); ?></title>
<style>
    * { box-sizing: border-box; }
    /* Gleiche Optik wie die Projektseite: Sonnengelb-Kopf, runde Karten, Pill-Formen */
    body { margin: 0; font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #17253a; background: #fbf7ef; }
    a { color: #14406b; }
    :focus-visible { outline: 3px solid #b56a00; outline-offset: 2px; border-radius: 6px; }
    header { background: #ffd35c; padding: 18px 28px; border-radius: 0 0 20px 20px; }
    header a { color: #17253a; text-decoration: none; font-weight: 800; font-size: 16px; }
    main { max-width: 760px; margin: 0 auto; padding: 30px 28px 60px; }
    .card { background: #fff; border: 1.5px solid #ece3d1; border-radius: 18px; padding: 22px 26px; margin-bottom: 18px; }
    h1 { font-weight: 800; letter-spacing: -0.02em; font-size: 28px; margin: 0 0 6px; }
    h2 { font-weight: 800; letter-spacing: -0.02em; font-size: 18px; margin: 0 0 12px; }
    p { font-size: 14px; line-height: 1.65; margin: 0 0 10px; }
    .muted { color: #5a6579; font-size: 13px; }
</style>
</head>
<body>
<header><a href="<?php echo htmlspecialchars($baseUrl); ?>/">&larr; <?php echo htmlspecialchars($brand); ?></a></header>
<main>
    <h1>Legal notice</h1>
    <p class="muted">Information about the operator of this website.</p>

    <div class="card">
        <h2>Operator</h2>
        <p>This website is a private, non-commercial project, operated by:</p>
        <p>
            Stefan P&uuml;rgstaller<br>
            39040 Auer (BZ)<br>
            Italy
        </p>
        <p>
            E-mail: <a href="mailto:service@legilo.eu">service@legilo.eu</a>
        </p>
    </div>

    <div class="card">
        <h2>About this service</h2>
        <p><?php echo htmlspecialchars($brand); ?> is a free reading-aid widget provided as is, without warranty.
        It is a reading aid and does not create conformance with WCAG, EN 301 549 or national
        accessibility laws.</p>
    </div>

    <div class="card">
        <h2>External links</h2>
        <p>This website contains links to external third-party websites. The respective provider is
        responsible for the content of those sites.</p>
    </div>
</main>
</body>
</html>
