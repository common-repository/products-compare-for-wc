<?php

/**
 * Main class
 *
 * @author EVDPL
 * @package Products Compare for WC
 * @version 1.0.0
 */
defined('EVDPL_WOOCOMPARE') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Woocompare')) {

    /**
     * Products Compare for WC
     */
    class EVDPL_Woocompare {

        /**
         * Plugin object
         */
        public $obj = null;

        /**
         * AJAX Helper
         */
        public $ajax = null;

        /**
         * Constructor
         */
        public function __construct() {

            add_action('widgets_init', array($this, 'evdpl_register_widgets'));

            #Load Plugin Framework.
            add_action('after_setup_theme', array($this, 'evdpl_plugin_fw_loader'), 1);

            if ($this->evdpl_is_frontend()) {
                #Require frontend class.
                require_once 'class.evdpl-woocompare-frontend.php';

                $this->obj = new EVDPL_Woocompare_Frontend();
            } elseif ($this->is_admin()) {
                #Requires admin classes.
                require_once 'class.evdpl-woocompare-admin.php';

                $this->obj = new EVDPL_Woocompare_Admin();
            }

            #Add image size.
            EVDPL_Woocompare_Helper::evdpl_set_image_size();

            #Let's filter the woocommerce image size.
            add_filter('woocommerce_get_image_size_evdpl-woocompare-image', array($this, 'evdpl_filter_wc_image_size'), 10, 1);

            return $this->obj;
        }

        /**
         * Detect if is frontend
         */
        public function evdpl_is_frontend() {
            $is_ajax = ( defined('DOING_AJAX') && DOING_AJAX );
            $context_check = isset($_REQUEST['context']) && sanitize_text_field(wp_unslash($_REQUEST['context'])) === 'frontend'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $actions_to_check = apply_filters('evdpl_woocompare_actions_to_check_frontend', array('woof_draw_products'));
            $action_check = isset($_REQUEST['action']) && in_array(sanitize_text_field(wp_unslash($_REQUEST['action'])), $actions_to_check, true); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

            return (bool) (!is_admin() || ( $is_ajax && ( $context_check || $action_check ) ) );
        }

        /**
         * Detect if is admin
         */
        public function is_admin() {
            $is_ajax = ( defined('DOING_AJAX') && DOING_AJAX );
            $is_admin = ( is_admin() || $is_ajax && isset($_REQUEST['context']) && sanitize_text_field(wp_unslash($_REQUEST['context'])) === 'admin' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return apply_filters('evdpl_woocompare_check_is_admin', (bool) $is_admin);
        }

        /**
         * Load Plugin Framework
         */
        public function evdpl_plugin_fw_loader() {

            if (!defined('EVDPL_CORE_PLUGIN')) {
                global $plugin_fw_data;
                if (!empty($plugin_fw_data)) {
                    $plugin_fw_file = array_shift($plugin_fw_data);
                    require_once $plugin_fw_file;
                }
            }
        }

        /**
         * Load and register widgets
         */
        public function evdpl_register_widgets() {
            register_widget('EVDPL_Woocompare_Widget');
        }

        /**
         * Filter WooCommerce image size attr
         */
        public function evdpl_filter_wc_image_size($size) {

            $size_opt = get_option('evdpl_woocompare_image_size', array());

            return array(
                'width' => isset($size_opt['width']) ? absint(esc_attr($size_opt['width'])) : 600,
                'height' => isset($size_opt['height']) ? absint(esc_attr($size_opt['width'])) : 600,
                'crop' => isset($size_opt['crop']) ? 1 : 0,
            );
        }

    }

}
