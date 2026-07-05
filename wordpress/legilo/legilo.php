<?php
/**
 * Plugin Name: Legilo
 * Plugin URI: https://legilo.eu
 * Description: Adds the free Legilo reading-aid widget to your website. Configure it at legilo.eu and paste the embed URL under Settings, Legilo. Note: Legilo is a reading aid and does not make your site conform to WCAG or national accessibility laws.
 * Version: 0.1.0
 * Author: Stefan Puergstaller
 * Author URI: https://legilo.eu
 * License: MIT
 * Text Domain: legilo
 */

if (!defined('ABSPATH')) exit;

define('LEGILO_OPTION', 'legilo_script_url');
define('LEGILO_DEFAULT_URL', 'https://legilo.eu/legilo.js');

function legilo_script_url() {
    $url = trim((string) get_option(LEGILO_OPTION, ''));
    return $url !== '' ? $url : LEGILO_DEFAULT_URL;
}

/** Print the widget script before </body> (defer, no further dependencies). */
add_action('wp_footer', function () {
    echo '<script src="' . esc_url(legilo_script_url()) . '" defer></script>' . "\n";
});

/** Accept full embed codes as well: the URL is extracted from them. */
function legilo_sanitize_url($raw) {
    $raw = trim((string) $raw);
    if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $raw, $m)) {
        $raw = $m[1];
    }
    if ($raw === '') return '';
    $url = esc_url_raw($raw);
    if (strpos($url, 'https://') !== 0 || strpos($url, '.js') === false) {
        add_settings_error('legilo', 'legilo_url',
            __('Please enter an https URL of a Legilo script (e.g. https://legilo.eu/legilo.js?...) or a complete embed code.', 'legilo'));
        return get_option(LEGILO_OPTION, '');
    }
    return $url;
}

add_action('admin_init', function () {
    register_setting('legilo', LEGILO_OPTION, array(
        'type' => 'string',
        'sanitize_callback' => 'legilo_sanitize_url',
        'default' => '',
    ));
});

add_action('admin_menu', function () {
    add_options_page('Legilo', 'Legilo', 'manage_options', 'legilo', 'legilo_settings_page');
});

function legilo_settings_page() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>Legilo</h1>
        <p><?php esc_html_e('Configure the widget (position, color, language, features) at', 'legilo'); ?>
            <a href="https://legilo.eu" target="_blank" rel="noopener">legilo.eu</a>,
            <?php esc_html_e('then paste the embed code or the script URL here. Leave empty for the default widget.', 'legilo'); ?></p>
        <form action="options.php" method="post">
            <?php settings_fields('legilo'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="legilo_script_url"><?php esc_html_e('Embed code or script URL', 'legilo'); ?></label></th>
                    <td>
                        <input type="text" id="legilo_script_url" name="<?php echo esc_attr(LEGILO_OPTION); ?>"
                               value="<?php echo esc_attr(get_option(LEGILO_OPTION, '')); ?>" class="large-text code"
                               placeholder="https://legilo.eu/legilo.js?color=0b5fb0&amp;lang=auto">
                        <p class="description"><?php esc_html_e('Also works with a self-hosted legilo.js on your own server.', 'legilo'); ?></p>
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
