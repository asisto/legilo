<?php
/**
 * Plugin Name: Legilo - Reading Aid and Accessibility Widget
 * Plugin URI: https://legilo.eu
 * Description: Adds the free Legilo reading-aid widget to your website. Configure position, colors, language and features under Settings, Legilo. Note: Legilo is a reading aid and does not make your site conform to WCAG or national accessibility laws.
 * Version: 0.1.2
 * Requires at least: 6.3
 * Requires PHP: 7.0
 * Author: Stefan Puergstaller
 * Author URI: https://github.com/asisto
 * License: MIT
 * Text Domain: legilo
 */

if (!defined('ABSPATH')) exit;

define('LEGILO_OPTION', 'legilo_settings');
define('LEGILO_BASE_URL', 'https://legilo.eu/legilo.js');

/**
 * Allowed widget parameters (mirrors the schema of the legilo.eu configurator).
 * Only these keys with these values ever end up in the script URL.
 */
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
        'lang' => array('type' => 'enum', 'values' => array_merge(array('auto'), array_keys(legilo_langs())), 'default' => 'auto'),
        'features' => array('type' => 'list', 'values' => array_keys(legilo_features()), 'default' => array_keys(legilo_features())),
        'mobile' => array('type' => 'enum', 'values' => array('show', 'hide'), 'default' => 'show'),
        'hide' => array('type' => 'bool', 'default' => 0),
        'hotkey' => array('type' => 'bool', 'default' => 0),
        'css' => array('type' => 'enum', 'values' => array('base', 'none'), 'default' => 'base'),
        'statement' => array('type' => 'url', 'default' => ''),
        'tts' => array('type' => 'enum', 'values' => array('both', 'read', 'hover'), 'default' => 'both'),
    );
}

function legilo_features() {
    return array(
        'profiles' => __('Profiles', 'legilo'),
        'fontsize' => __('Font size', 'legilo'),
        'spacing' => __('Spacing', 'legilo'),
        'font' => __('Font', 'legilo'),
        'align' => __('Align text', 'legilo'),
        'contrast' => __('Contrast', 'legilo'),
        'saturation' => __('Colors', 'legilo'),
        'bluefilter' => __('Blue filter', 'legilo'),
        'colorblind' => __('Color blindness', 'legilo'),
        'links' => __('Highlight links', 'legilo'),
        'focus' => __('Highlight focus', 'legilo'),
        'cursor' => __('Big cursor', 'legilo'),
        'guide' => __('Reading guide', 'legilo'),
        'mask' => __('Reading mask', 'legilo'),
        'animations' => __('Stop animations', 'legilo'),
        'images' => __('Hide images', 'legilo'),
        'tts' => __('Read aloud', 'legilo'),
        'structure' => __('Page structure', 'legilo'),
    );
}

function legilo_langs() {
    return array(
        'en' => 'English', 'de' => 'Deutsch', 'it' => 'Italiano', 'fr' => 'Francais',
        'es' => 'Espanol', 'pt' => 'Portugues', 'nl' => 'Nederlands', 'pl' => 'Polski',
        'tr' => 'Turkce', 'ru' => 'Russkij', 'uk' => 'Ukrainska', 'ar' => 'Arabi',
        'he' => 'Ivrit', 'zh' => 'Zhongwen', 'ja' => 'Nihongo', 'ko' => 'Hangugeo',
        'hi' => 'Hindi', 'bg' => 'Balgarski', 'cs' => 'Cestina', 'da' => 'Dansk',
        'el' => 'Ellinika', 'et' => 'Eesti', 'fa' => 'Farsi', 'fi' => 'Suomi',
        'ga' => 'Gaeilge', 'hr' => 'Hrvatski', 'hu' => 'Magyar', 'id' => 'Bahasa Indonesia',
        'lt' => 'Lietuviu', 'lv' => 'Latviesu', 'mt' => 'Malti', 'ro' => 'Romana',
        'sk' => 'Slovencina', 'sl' => 'Slovenscina', 'sv' => 'Svenska', 'th' => 'Thai',
        'vi' => 'Tieng Viet',
    );
}

/** Validate one raw value against its schema entry; falls back to the default. */
function legilo_validate($raw, $def) {
    if ($raw === null || $raw === '') return $def['default'];
    switch ($def['type']) {
        case 'enum':
            $raw = strtolower(trim((string) $raw));
            return in_array($raw, $def['values'], true) ? $raw : $def['default'];
        case 'int':
            if (!is_numeric($raw)) return $def['default'];
            return max($def['min'], min($def['max'], (int) $raw));
        case 'hex':
            $raw = ltrim(strtolower(trim((string) $raw)), '#');
            if (preg_match('/^[0-9a-f]{6}$/', $raw)) return $raw;
            if (preg_match('/^[0-9a-f]{3}$/', $raw)) {
                return $raw[0] . $raw[0] . $raw[1] . $raw[1] . $raw[2] . $raw[2];
            }
            return $def['default'];
        case 'bool':
            return in_array(strtolower(trim((string) $raw)), array('1', 'true', 'yes', 'on'), true) ? 1 : 0;
        case 'list':
            if (is_string($raw)) $raw = explode(',', $raw);
            if (!is_array($raw)) return $def['default'];
            $items = array();
            foreach ($raw as $item) {
                $item = strtolower(trim((string) $item));
                if (in_array($item, $def['values'], true) && !in_array($item, $items, true)) {
                    $items[] = $item;
                }
            }
            return count($items) ? $items : $def['default'];
        case 'url':
            $url = esc_url_raw(trim((string) $raw), array('https', 'http'));
            return $url !== '' ? substr($url, 0, 500) : $def['default'];
    }
    return $def['default'];
}

function legilo_defaults() {
    $out = array();
    foreach (legilo_schema() as $key => $def) $out[$key] = $def['default'];
    return $out;
}

function legilo_settings() {
    $stored = get_option(LEGILO_OPTION, array());
    if (!is_array($stored)) $stored = array();
    $out = array();
    foreach (legilo_schema() as $key => $def) {
        $out[$key] = array_key_exists($key, $stored) ? legilo_validate($stored[$key], $def) : $def['default'];
    }
    return $out;
}

/**
 * Build the widget script URL from the validated settings. The host is fixed;
 * only whitelisted parameters are appended (and only when they differ from
 * the default, so the default configuration loads the plain script).
 */
function legilo_script_url() {
    $schema = legilo_schema();
    $params = array();
    foreach (legilo_settings() as $key => $value) {
        if ($value === $schema[$key]['default']) continue;
        if ($key === 'features') {
            if (count($value) === 0) continue;
            $value = implode(',', $value);
        }
        $params[$key] = $value;
    }
    $url = LEGILO_BASE_URL;
    if ($params) $url .= '?' . http_build_query($params);
    /**
     * Developers who self-host the downloaded legilo.js can point the plugin
     * to their own copy: add_filter('legilo_script_url', fn() => '...');
     */
    return apply_filters('legilo_script_url', $url);
}

/** Load the widget script (deferred, in the footer, no dependencies). */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('legilo', legilo_script_url(), array(), '0.1.2',
        array('in_footer' => true, 'strategy' => 'defer'));
});

/**
 * Sanitize the settings form. The optional import field accepts an embed code
 * or configurator URL from legilo.eu; only its known query parameters are
 * extracted, the URL itself is discarded.
 */
function legilo_sanitize($input) {
    if (!is_array($input)) $input = array();
    $schema = legilo_schema();

    $import = isset($input['import']) ? trim((string) $input['import']) : '';
    if ($import !== '') {
        if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $import, $m)) $import = $m[1];
        $query = (string) wp_parse_url($import, PHP_URL_QUERY);
        parse_str($query, $imported);
        foreach ($imported as $key => $value) {
            if (isset($schema[$key])) $input[$key] = $value;
        }
        if ($query === '') {
            add_settings_error('legilo', 'legilo_import',
                __('No settings found in the pasted embed code, nothing imported.', 'legilo'), 'warning');
        } else {
            add_settings_error('legilo', 'legilo_import',
                __('Settings imported from the embed code.', 'legilo'), 'success');
        }
    }

    $out = array();
    foreach ($schema as $key => $def) {
        if ($def['type'] === 'bool') {
            $out[$key] = empty($input[$key]) ? 0 : 1;
        } elseif ($key === 'features') {
            $out[$key] = legilo_validate(isset($input[$key]) ? $input[$key] : array(), $def);
        } elseif ($key === 'mobile') {
            $out[$key] = empty($input['mobile_hide']) && !isset($input['mobile'])
                ? 'show' : (isset($input['mobile']) ? legilo_validate($input['mobile'], $def) : 'hide');
        } else {
            $out[$key] = legilo_validate(isset($input[$key]) ? $input[$key] : null, $def);
        }
    }
    return $out;
}

add_action('admin_init', function () {
    register_setting('legilo', LEGILO_OPTION, array(
        'type' => 'array',
        'sanitize_callback' => 'legilo_sanitize',
        'default' => array(),
    ));
});

add_action('admin_menu', function () {
    add_options_page('Legilo', 'Legilo', 'manage_options', 'legilo', 'legilo_settings_page');
});

function legilo_settings_page() {
    if (!current_user_can('manage_options')) return;
    $s = legilo_settings();
    $name = LEGILO_OPTION;
    $pos_labels = array(
        'tl' => __('top left', 'legilo'), 'tc' => __('top center', 'legilo'), 'tr' => __('top right', 'legilo'),
        'lc' => __('left center', 'legilo'), 'rc' => __('right center', 'legilo'),
        'bl' => __('bottom left', 'legilo'), 'bc' => __('bottom center', 'legilo'), 'br' => __('bottom right', 'legilo'),
    );
    $size_labels = array('s' => __('small', 'legilo'), 'm' => __('medium', 'legilo'), 'l' => __('large', 'legilo'));
    $icon_labels = array(
        'access' => __('Accessibility figure', 'legilo'), 'person' => __('Person', 'legilo'),
        'eye' => __('Eye', 'legilo'), 'aa' => __('Aa letters', 'legilo'),
    );
    ?>
    <div class="wrap">
        <h1>Legilo</h1>
        <p><?php esc_html_e('The widget loads from legilo.eu with exactly the settings below. You can also try them out with a live preview at', 'legilo'); ?>
            <a href="https://legilo.eu" target="_blank" rel="noopener">legilo.eu</a>
            <?php esc_html_e('and paste the embed code into the import field.', 'legilo'); ?></p>
        <form action="options.php" method="post">
            <?php settings_fields('legilo'); ?>
            <?php // css=none is a pro option without a form field (set via import); keep it across saves ?>
            <input type="hidden" name="<?php echo esc_attr($name); ?>[css]" value="<?php echo esc_attr($s['css']); ?>">
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="legilo_import"><?php esc_html_e('Import from legilo.eu (optional)', 'legilo'); ?></label></th>
                    <td>
                        <input type="text" id="legilo_import" name="<?php echo esc_attr($name); ?>[import]" value=""
                               class="large-text code" placeholder="&lt;script src=&quot;https://legilo.eu/legilo.js?color=0b5fb0&quot;&gt;&lt;/script&gt;">
                        <p class="description"><?php esc_html_e('Paste an embed code here and save: its settings fill the fields below. Only the settings are taken over, never the URL itself.', 'legilo'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="legilo_pos"><?php esc_html_e('Position', 'legilo'); ?></label></th>
                    <td>
                        <select id="legilo_pos" name="<?php echo esc_attr($name); ?>[pos]">
                            <?php foreach ($pos_labels as $val => $label): ?>
                                <option value="<?php echo esc_attr($val); ?>" <?php selected($s['pos'], $val); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="legilo_offx" style="margin-left:12px;"><?php esc_html_e('Offset X', 'legilo'); ?></label>
                        <input type="number" id="legilo_offx" name="<?php echo esc_attr($name); ?>[offx]" value="<?php echo esc_attr($s['offx']); ?>" min="0" max="400" step="1" style="width:70px;">
                        <label for="legilo_offy"><?php esc_html_e('Y', 'legilo'); ?></label>
                        <input type="number" id="legilo_offy" name="<?php echo esc_attr($name); ?>[offy]" value="<?php echo esc_attr($s['offy']); ?>" min="0" max="400" step="1" style="width:70px;"> px
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="legilo_color"><?php esc_html_e('Colors', 'legilo'); ?></label></th>
                    <td>
                        <input type="color" id="legilo_color" name="<?php echo esc_attr($name); ?>[color]" value="#<?php echo esc_attr($s['color']); ?>">
                        <label for="legilo_color"><?php esc_html_e('Button', 'legilo'); ?></label>
                        <input type="color" id="legilo_color2" name="<?php echo esc_attr($name); ?>[color2]" value="#<?php echo esc_attr($s['color2']); ?>" style="margin-left:12px;">
                        <label for="legilo_color2"><?php esc_html_e('Icon', 'legilo'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="legilo_size"><?php esc_html_e('Button', 'legilo'); ?></label></th>
                    <td>
                        <select id="legilo_size" name="<?php echo esc_attr($name); ?>[size]">
                            <?php foreach ($size_labels as $val => $label): ?>
                                <option value="<?php echo esc_attr($val); ?>" <?php selected($s['size'], $val); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="<?php echo esc_attr($name); ?>[icon]" aria-label="<?php esc_attr_e('Icon', 'legilo'); ?>">
                            <?php foreach ($icon_labels as $val => $label): ?>
                                <option value="<?php echo esc_attr($val); ?>" <?php selected($s['icon'], $val); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="legilo_radius" style="margin-left:12px;"><?php esc_html_e('Corner radius', 'legilo'); ?></label>
                        <input type="number" id="legilo_radius" name="<?php echo esc_attr($name); ?>[radius]" value="<?php echo esc_attr($s['radius']); ?>" min="0" max="50" step="1" style="width:70px;">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="legilo_lang"><?php esc_html_e('Language', 'legilo'); ?></label></th>
                    <td>
                        <select id="legilo_lang" name="<?php echo esc_attr($name); ?>[lang]">
                            <option value="auto" <?php selected($s['lang'], 'auto'); ?>><?php esc_html_e('automatic (browser language)', 'legilo'); ?></option>
                            <?php foreach (legilo_langs() as $val => $label): ?>
                                <option value="<?php echo esc_attr($val); ?>" <?php selected($s['lang'], $val); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Features', 'legilo'); ?></th>
                    <td>
                        <fieldset style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:4px;max-width:760px;">
                            <legend class="screen-reader-text"><?php esc_html_e('Features', 'legilo'); ?></legend>
                            <?php foreach (legilo_features() as $val => $label): ?>
                                <label><input type="checkbox" name="<?php echo esc_attr($name); ?>[features][]" value="<?php echo esc_attr($val); ?>"
                                    <?php checked(in_array($val, $s['features'], true)); ?>> <?php echo esc_html($label); ?></label>
                            <?php endforeach; ?>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Behavior', 'legilo'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php esc_html_e('Behavior', 'legilo'); ?></legend>
                            <label><input type="checkbox" name="<?php echo esc_attr($name); ?>[mobile_hide]" value="1" <?php checked($s['mobile'], 'hide'); ?>>
                                <?php esc_html_e('Hide on small screens', 'legilo'); ?></label><br>
                            <label><input type="checkbox" name="<?php echo esc_attr($name); ?>[hide]" value="1" <?php checked($s['hide'], 1); ?>>
                                <?php esc_html_e('Hide the built-in button (open the panel from your own button via the JavaScript API)', 'legilo'); ?></label><br>
                            <label><input type="checkbox" name="<?php echo esc_attr($name); ?>[hotkey]" value="1" <?php checked($s['hotkey'], 1); ?>>
                                <?php esc_html_e('Keyboard shortcut Alt+A opens the panel', 'legilo'); ?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="legilo_tts"><?php esc_html_e('Read-aloud modes', 'legilo'); ?></label></th>
                    <td>
                        <select id="legilo_tts" name="<?php echo esc_attr($name); ?>[tts]">
                            <option value="both" <?php selected($s['tts'], 'both'); ?>><?php esc_html_e('Both: read page and point & read', 'legilo'); ?></option>
                            <option value="read" <?php selected($s['tts'], 'read'); ?>><?php esc_html_e('Only read page', 'legilo'); ?></option>
                            <option value="hover" <?php selected($s['tts'], 'hover'); ?>><?php esc_html_e('Only point & read', 'legilo'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="legilo_statement"><?php esc_html_e('Accessibility statement URL (optional)', 'legilo'); ?></label></th>
                    <td>
                        <input type="url" id="legilo_statement" name="<?php echo esc_attr($name); ?>[statement]" value="<?php echo esc_attr($s['statement']); ?>" class="regular-text" placeholder="https://example.com/accessibility">
                        <p class="description"><?php esc_html_e('If set, the panel links to your accessibility statement.', 'legilo'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <p style="max-width:640px;color:#646970;">
            <?php esc_html_e('Honest note: Legilo is a reading aid for your visitors. It does not create conformance with WCAG, EN 301 549 or national accessibility laws - that happens in your site\'s source code.', 'legilo'); ?>
        </p>
    </div>
    <?php
}
