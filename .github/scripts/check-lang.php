<?php
/** CI-Check: alle widget/lang/*.json valide und strukturgleich zu en.json. */
$dir = __DIR__ . '/../../widget/lang/';
require __DIR__ . '/../../config.php';

function langkeys($a, $prefix = '') {
    $out = array();
    foreach ($a as $k => $v) {
        if ($k === 'rtl') continue; // optional
        $out[] = $prefix . $k;
        if (is_array($v) && !isset($v[0])) $out = array_merge($out, langkeys($v, $k . '.'));
        if (is_array($v) && isset($v[0])) $out[] = $prefix . $k . '#' . count($v);
    }
    sort($out);
    return $out;
}

$en = json_decode(file_get_contents($dir . 'en.json'), true);
if (!$en) { fwrite(STDERR, "en.json unlesbar\n"); exit(1); }
$ref = langkeys($en);
$fail = 0;
foreach (LEGILO_LANGS as $lc) {
    $j = json_decode(file_get_contents($dir . $lc . '.json'), true);
    if ($j === null) { echo "$lc.json: INVALIDES JSON\n"; $fail++; continue; }
    $diffA = array_diff($ref, langkeys($j));
    $diffB = array_diff(langkeys($j), $ref);
    if ($diffA || $diffB) {
        echo "$lc.json: Struktur weicht ab. fehlt=[" . implode(',', $diffA) . "] extra=[" . implode(',', $diffB) . "]\n";
        $fail++;
    } else {
        echo "$lc.json: OK\n";
    }
}
exit($fail ? 1 : 0);
