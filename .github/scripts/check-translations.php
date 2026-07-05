<?php
/** CI-Check: alle translations/*.php geben Arrays mit identischen Schluesseln zurueck. */
require __DIR__ . '/../../config.php';
$dir = __DIR__ . '/../../translations/';

function txkeys($a) {
    $out = array();
    foreach ($a as $k => $v) {
        $out[] = $k;
        if (is_array($v) && $k !== 'faq') {
            foreach (array_keys($v) as $sk) $out[] = $k . '.' . $sk;
        }
        if ($k === 'faq') $out[] = 'faq#' . count($v);
    }
    sort($out);
    return $out;
}

$fail = 0;
foreach (LEGILO_GEN_LANGS as $lc) {
    if (!in_array($lc, LEGILO_LANGS, true)) {
        echo "$lc: fehlt in LEGILO_LANGS (Feature-Labels kommen aus widget/lang/)\n";
        $fail++;
    }
}
$ref = txkeys(require $dir . 'en.php');
foreach (LEGILO_GEN_LANGS as $lc) {
    $tx = require $dir . $lc . '.php';
    $diffA = array_diff($ref, txkeys($tx));
    $diffB = array_diff(txkeys($tx), $ref);
    if ($diffA || $diffB) {
        echo "$lc.php: Schluessel weichen ab. fehlt=[" . implode(',', $diffA) . "] extra=[" . implode(',', $diffB) . "]\n";
        $fail++;
    } else {
        echo "$lc.php: OK (" . count(txkeys($tx)) . " Schluessel)\n";
    }
}
exit($fail ? 1 : 0);
