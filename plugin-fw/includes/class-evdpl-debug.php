<?php

/**
 * EVDPL Debug Class.
 *
 * @class   EVDPL_Debug
 * @package EVDPL\PluginFramework\Classes
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Debug')) {

    /**
     * EVDPL_Debug class.
     *
     */
    class EVDPL_Debug {

        /**
         * The single instance of the class.
         * @var EVDPL_Debug
         */
        private static $instance;

        /**
         * Singleton implementation.
         * @return EVDPL_Debug
         */
        public static function instance() {
            return !is_null(self::$instance) ? self::$instance : self::$instance = new self();
        }

        /**
         * Deprecated singleton implementation.
         * Kept for backward compatibility.
         * @return EVDPL_Debug
         */
        public static function get_instance() {
            return self::instance();
        }

        /**
         * EVDPL_Debug constructor.
         */
        private function __construct() {
            add_action('init', array($this, 'init'));
        }

        /**
         * Init
         */
        public function init() {
            if (!is_admin() || defined('DOING_AJAX')) {
                return;
            }

            $is_debug = apply_filters('evdpl_plugin_fw_is_debug', isset($_GET['evdpl-debug'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

            if ($is_debug) {
                add_action('admin_bar_menu', array($this, 'add_debug_in_admin_bar'), 99);
            }
        }

        /**
         * Add debug node in admin bar.
         * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
         */
        public function add_debug_in_admin_bar($wp_admin_bar) {
            $args = array(
                'id' => 'evdpl-debug-admin-bar',
                'title' => 'EVDPL Debug',
                'href' => '',
                'meta' => array(
                    'class' => 'evdpl-debug-admin-bar',
                ),
            );
            $wp_admin_bar->add_node($args);

            $subnodes = array();

            foreach ($this->get_debug_information() as $key => $information) {
                $label = esc_attr($information['label']);
                $value = esc_attr($information['value']);
                $url = !empty($information['url']) ? esc_url($information['url']) : '';

                if (!!$value) {
                    $title = "<strong>$label:</strong> $value";
                } else {
                    $title = "<strong>$label</strong>";
                }

                $subnodes[] = array(
                    'id' => 'evdpl-debug-admin-bar-' . $key,
                    'parent' => 'evdpl-debug-admin-bar',
                    'title' => $title,
                    'href' => $url,
                    'meta' => array(
                        'class' => 'evdpl-debug-admin-bar-' . $key,
                    ),
                );

                if (isset($information['subsub'])) {
                    foreach ($information['subsub'] as $sub_key => $sub_value) {
                        $title = isset($sub_value['title']) ? esc_attr($sub_value['title']) : '';
                        $html = isset($sub_value['html']) ? wp_kses_post($sub_value['html']) : '';
                        $subnodes[] = array(
                            'id' => 'evdpl-debug-admin-bar-' . $key . '-' . $sub_key,
                            'parent' => 'evdpl-debug-admin-bar-' . $key,
                            'title' => $title,
                            'href' => '',
                            'meta' => array(
                                'class' => 'evdpl-debug-admin-bar-' . $key . '-' . $sub_key,
                                'html' => $html,
                            ),
                        );
                    }
                }
            }

            foreach ($subnodes as $subnode) {
                $wp_admin_bar->add_node($subnode);
            }
        }

        /**
         * Return an array of debug information.
         * @return array
         */
        public function get_debug_information() {
            // phpcs:disable WordPress.Security.NonceVerification.Recommended
            // phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_dump

            $debug = array(
                'plugin-fw-info' => array(
                    'label' => 'Framework',
                    'value' => $this->get_plugin_framework_info(),
                ),
                'wc-version' => array(
                    'label' => 'WooCommerce',
                    'value' => $this->get_woocommerce_version_info(),
                ),
                'screen-id' => array(
                    'label' => 'Screen ID',
                    'value' => $this->get_current_screen_info(),
                ),
                'post-meta' => array(
                    'label' => 'Post Meta',
                    'value' => '',
                    'url' => add_query_arg(array('evdpl-debug-post-meta' => 'all')),
                ),
                'option' => array(
                    'label' => 'Option',
                    'value' => '',
                    'url' => add_query_arg(array('evdpl-debug-option' => '')),
                ),
            );

            // Post Meta debug.
            global $post;
            if (!empty($_GET['evdpl-debug-post-meta']) && $post) {
                $meta_key = sanitize_key(wp_unslash($_GET['evdpl-debug-post-meta']));
                $meta_value = 'all' !== $meta_key ? get_post_meta($post->ID, $meta_key, true) : get_post_meta($post->ID);

                ob_start();
                echo '<pre>';
                var_dump($meta_value);
                echo '</pre>';
                $meta_value_html = ob_get_clean();

                $debug['post-meta']['value'] = esc_attr($meta_key);
                $debug['post-meta']['subsub'] = array(array('html' => $meta_value_html));
            }

            // Option debug.
            if (!empty($_GET['evdpl-debug-option'])) {
                $option_key = sanitize_key(wp_unslash($_GET['evdpl-debug-option']));
                $option_value = get_option($option_key);

                ob_start();
                echo '<pre>';
                var_dump($option_value);
                echo '</pre>';
                $option_value_html = ob_get_clean();

                $debug['option']['value'] = esc_attr($option_key);
                $debug['option']['subsub'] = array(array('html' => $option_value_html));
            }

            // phpcs:enable

            return $debug;
        }

        /** -----------------------------------------------------------
         *                          GETTER INFO
         *  -----------------------------------------------------------
         */

        /**
         * Return the current screen ID.
         * @return string
         */
        public function get_current_screen_info() {
            $screen = function_exists('get_current_screen') ? get_current_screen() : false;

            return !!$screen ? $screen->id : 'null';
        }

        /**
         * Return the WooCommerce version if active.
         *
         * @return string
         */
        public function get_woocommerce_version_info() {
            return function_exists('WC') ? WC()->version : 'not active';
        }

        /**
         * Return plugin framework information (version and loaded_by).
         *
         * @return string
         */
        public function get_plugin_framework_info() {
            $plugin_fw_version = evdpl_plugin_fw_get_version();
            $plugin_fw_loaded_by = basename(dirname(EVDPL_CORE_PLUGIN_PATH));

            return "$plugin_fw_version (by $plugin_fw_loaded_by)";
        }

    }

}
if (!function_exists('evdpl_debug')) {

    /**
     * Single instance of EVDPL_Debug
     *
     * @return EVDPL_Debug
     */
    function evdpl_debug() {
        return EVDPL_Debug::instance();
    }

    evdpl_debug();
}
