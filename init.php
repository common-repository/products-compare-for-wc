<?php
/**
 * Plugin Name: Products Compare for WC
 * Plugin URI: http://wpenv1.evdpl.com/
 * Description: The <code><strong>Products Compare for WC</strong></code> plugin allow you to compare in a simple and efficient way products on sale in your shop and analyze their main features in a single table.
 * Version: 1.0.0
 * Author: Evincedev
 * Author URI: https://evincedev.com/
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: evdpl-woocommerce-compare
 * Domain Path: /languages/
 * WC requires at least: 4.5
 * WC tested up to: 5.4
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Error message if WooCommerce is not installed
 * @since 1.0.0
 */
function evdpl_woocompare_install_woocommerce_admin_notice() {
    ?>
    <div class="error">
        <p><?php esc_html_e('Products Compare for WC is enabled but not effective. It requires WooCommerce in order to work.', 'evdpl-woocommerce-compare'); ?></p>
    </div>
    <?php
}

if (!function_exists('evdpl_plugin_registration_hook')) {
    require_once 'plugin-fw/evdpl-plugin-registration-hook.php';
}
register_activation_hook(__FILE__, 'evdpl_plugin_registration_hook');

if (!defined('EVDPL_WOOCOMPARE_VERSION')) {
    define('EVDPL_WOOCOMPARE_VERSION', '2.5.2');
}
if (!defined('EVDPL_WOOCOMPARE_FREE_INIT')) {
    define('EVDPL_WOOCOMPARE_FREE_INIT', plugin_basename(__FILE__));
}
if (!defined('EVDPL_WOOCOMPARE_INIT')) {
    define('EVDPL_WOOCOMPARE_INIT', plugin_basename(__FILE__));
}
if (!defined('EVDPL_WOOCOMPARE')) {
    define('EVDPL_WOOCOMPARE', true);
}
if (!defined('EVDPL_WOOCOMPARE_FILE')) {
    define('EVDPL_WOOCOMPARE_FILE', __FILE__);
}
if (!defined('EVDPL_WOOCOMPARE_URL')) {
    define('EVDPL_WOOCOMPARE_URL', plugin_dir_url(__FILE__));
}
if (!defined('EVDPL_WOOCOMPARE_DIR')) {
    define('EVDPL_WOOCOMPARE_DIR', plugin_dir_path(__FILE__));
}
if (!defined('EVDPL_WOOCOMPARE_TEMPLATE_PATH')) {
    define('EVDPL_WOOCOMPARE_TEMPLATE_PATH', EVDPL_WOOCOMPARE_DIR . 'templates');
}
if (!defined('EVDPL_WOOCOMPARE_ASSETS_URL')) {
    define('EVDPL_WOOCOMPARE_ASSETS_URL', EVDPL_WOOCOMPARE_URL . 'assets');
}
if (!defined('EVDPL_WOOCOMPARE_SLUG')) {
    define('EVDPL_WOOCOMPARE_SLUG', 'evdpl-woocommerce-compare');
}
/* Plugin Framework Version Check */
if (!function_exists('evdpl_maybe_plugin_fw_loader') && file_exists(EVDPL_WOOCOMPARE_DIR . 'plugin-fw/init.php')) {
    require_once EVDPL_WOOCOMPARE_DIR . 'plugin-fw/init.php';
}
evdpl_maybe_plugin_fw_loader(EVDPL_WOOCOMPARE_DIR);

/**
 * Init plugin
 * @since 1.0.0
 */
function evdpl_woocompare_constructor() {

    global $woocommerce;

    if (!isset($woocommerce) || !function_exists('WC')) {
        add_action('admin_notices', 'evdpl_woocompare_install_woocommerce_admin_notice');
        return;
    }

    load_plugin_textdomain('evdpl-woocommerce-compare', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    #Load required classes and functions.
    require_once 'includes/class.evdpl-woocompare-helper.php';
    require_once 'widgets/class.evdpl-woocompare-widget.php';
    require_once 'includes/class.evdpl-woocompare.php';

    global $evdpl_woocompare;
    $evdpl_woocompare = new EVDPL_Woocompare();
}

add_action('plugins_loaded', 'evdpl_woocompare_constructor', 11);
