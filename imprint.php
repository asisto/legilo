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
<meta name="theme-color" content="#10314f">
<link rel="icon" type="image/svg+xml" href="<?php echo htmlspecialchars($baseUrl); ?>/assets/favicon.svg">
<title>Legal notice - <?php echo htmlspecialchars($brand); ?></title>
<style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #1a1a1a; background: #f2f4f7; }
    a { color: #10314f; }
    :focus-visible { outline: 3px solid #e8a600; outline-offset: 2px; border-radius: 4px; }
    header { background: #10314f; color: #fff; padding: 18px 28px; }
    header a { color: #fff; text-decoration: none; font-weight: 600; }
    main { max-width: 760px; margin: 0 auto; padding: 30px 28px 60px; }
    .card { background: #fff; border: 1px solid #dde3ea; border-radius: 10px; padding: 22px 26px; margin-bottom: 18px; }
    h1 { font-size: 24px; margin: 0 0 6px; }
    h2 { font-size: 17px; margin: 0 0 12px; }
    p { font-size: 14px; line-height: 1.65; margin: 0 0 10px; }
    .muted { color: #56606b; font-size: 13px; }
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
