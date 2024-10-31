<?php

/**
 * Define constants and include Plugin Framework files.
 *
 * @package EVDPL\PluginFramework
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
!defined('EVDPL_CORE_PLUGIN') && define('EVDPL_CORE_PLUGIN', true);
!defined('EVDPL_CORE_PLUGIN_PATH') && define('EVDPL_CORE_PLUGIN_PATH', dirname(__FILE__));
!defined('EVDPL_CORE_PLUGIN_URL') && define('EVDPL_CORE_PLUGIN_URL', untrailingslashit(plugins_url('/', __FILE__)));
!defined('EVDPL_CORE_PLUGIN_TEMPLATE_PATH') && define('EVDPL_CORE_PLUGIN_TEMPLATE_PATH', EVDPL_CORE_PLUGIN_PATH . '/templates');

require_once 'evdpl-functions.php';
require_once 'evdpl-woocommerce-compatibility.php';
require_once 'evdpl-plugin-registration-hook.php';
require_once 'includes/class-evdpl-metabox.php';
require_once 'includes/class-evdpl-plugin-panel.php';
require_once 'includes/class-evdpl-plugin-panel-woocommerce.php';
require_once 'includes/class-evdpl-ajax.php';
require_once 'includes/class-evdpl-plugin-subpanel.php';
require_once 'includes/class-evdpl-plugin-common.php';
require_once 'includes/class-evdpl-gradients.php';

require_once 'includes/class-evdpl-video.php';
require_once 'includes/class-evdpl-pointers.php';
require_once 'includes/class-evdpl-icons.php';
require_once 'includes/class-evdpl-assets.php';
require_once 'includes/class-evdpl-debug.php';

require_once 'includes/privacy/class-evdpl-privacy.php';
require_once 'includes/privacy/class-evdpl-privacy-plugin-abstract.php';

require_once 'includes/class-evdpl-post-type-admin.php';

// Gutenberg Support.
if (class_exists('WP_Block_Type_Registry')) {
    require_once 'includes/builders/gutenberg/class-evdpl-gutenberg.php';
}

require_once 'includes/builders/elementor/class-evdpl-elementor.php';

// load from theme folder...
load_textdomain('evdpl-plugin-fw', get_template_directory() . '/core/plugin-fw/evdpl-plugin-fw-' . apply_filters('plugin_locale', get_locale(), 'evdpl-plugin-fw') . '.mo') ||
// ...or from plugin folder.
        load_textdomain('evdpl-plugin-fw', dirname(__FILE__) . '/languages/evdpl-plugin-fw-' . apply_filters('plugin_locale', get_locale(), 'evdpl-plugin-fw') . '.mo');

if (!function_exists('evdpl_add_action_links')) {

    /**
     * Add the action links to plugin admin page
     *
     * @param array  $links       The plugin links.
     * @param string $panel_page  The panel page.
     * @param bool   $is_premium  Is this plugin premium? True if the plugin is premium. False otherwise.
     * @param string $plugin_slug The plugin slug.
     *
     * @return   array
     * @since    1.0.0
     */
    function evdpl_add_action_links($links, $panel_page = '', $is_premium = false, $plugin_slug = '') {
        $links = is_array($links) ? $links : array();
        if (!empty($panel_page)) {
            $links[] = sprintf('<a href="%s">%s</a>', admin_url("admin.php?page={$panel_page}"), _x('Settings', 'Action links', 'evdpl-plugin-fw'));
        }

        if ($is_premium && class_exists('EVDPL_Plugin_Licence')) {
            $links[] = sprintf('<a href="%s">%s</a>', EVDPL_Plugin_Licence()->get_license_activation_url($plugin_slug), __('License', 'evdpl-plugin-fw'));
        }

        return $links;
    }

}
