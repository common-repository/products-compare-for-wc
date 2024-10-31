<?php

/**
 * EVDPL Assets Class. Assets Handler.
 *
 * @class      EVDPL_Assets
 * @package    EVDPL\PluginFramework\Classes
 * @since      1.0.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Assets')) {

    /**
     * EVDPL_Assets class.
     *
     */
    class EVDPL_Assets {

        /**
         * The framework version
         * @var string
         */
        public $version = '1.0.0';

        /**
         * The single instance of the class.
         * @var EVDPL_Assets
         */
        private static $instance;

        /**
         * Singleton implementation.
         * @return EVDPL_Assets
         */
        public static function instance() {
            return !is_null(self::$instance) ? self::$instance : self::$instance = new self();
        }

        /**
         * EVDPL_Assets constructor.
         */
        private function __construct() {
            $this->version = evdpl_plugin_fw_get_version();
            add_action('admin_enqueue_scripts', array($this, 'register_common_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'register_common_scripts'));
            add_action('elementor/editor/before_enqueue_styles', array($this, 'register_common_scripts'));

            add_action('admin_enqueue_scripts', array($this, 'register_styles_and_scripts'));
        }

        /**
         * Register common scripts
         */
        public function register_common_scripts() {
            wp_register_style('evdpl-plugin-fw-icon-font', EVDPL_CORE_PLUGIN_URL . '/assets/css/evdpl-icon.css', array(), $this->version);
            wp_register_style('evdpl-plugin-fw-elementor', EVDPL_CORE_PLUGIN_URL . '/assets/css/elementor.css', array(), $this->version);
        }

        /**
         * Register styles and scripts
         */
        public function register_styles_and_scripts() {
            global $wp_scripts, $woocommerce, $wp_version;

            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

            #Register scripts.
            wp_register_script('evdpl-ui', EVDPL_CORE_PLUGIN_URL . '/assets/js/evdpl-ui' . $suffix . '.js', array('jquery'), $this->version, true);
            wp_register_script('evdpl-colorpicker', EVDPL_CORE_PLUGIN_URL . '/assets/js/evdpl-colorpicker.min.js', array('jquery', 'wp-color-picker'), '3.0.0', true);
            wp_register_script('evdpl-plugin-fw-fields', EVDPL_CORE_PLUGIN_URL . '/assets/js/evdpl-fields' . $suffix . '.js', array('jquery', 'jquery-ui-datepicker', 'evdpl-colorpicker', 'jquery-ui-slider', 'jquery-ui-sortable', 'jquery-tiptip', 'evdpl-ui'), $this->version, true);
            wp_register_script('evdpl-date-format', EVDPL_CORE_PLUGIN_URL . '/assets/js/evdpl-date-format' . $suffix . '.js', array('jquery', 'jquery-ui-datepicker'), $this->version, true);

            wp_register_script('evdpl-metabox', EVDPL_CORE_PLUGIN_URL . '/assets/js/metabox' . $suffix . '.js', array('jquery', 'wp-color-picker', 'evdpl-plugin-fw-fields', 'evdpl-ui'), $this->version, true);
            wp_register_script('evdpl-plugin-panel', EVDPL_CORE_PLUGIN_URL . '/assets/js/evdpl-plugin-panel' . $suffix . '.js', array('jquery', 'wp-color-picker', 'jquery-ui-sortable', 'evdpl-plugin-fw-fields', 'evdpl-ui'), $this->version, true);
            wp_register_script('colorbox', EVDPL_CORE_PLUGIN_URL . '/assets/js/jquery.colorbox' . $suffix . '.js', array('jquery'), '1.6.3', true);
            wp_register_script('evdpl_how_to', EVDPL_CORE_PLUGIN_URL . '/assets/js/how-to' . $suffix . '.js', array('jquery'), $this->version, true);
            wp_register_script('evdpl-plugin-fw-wp-pages', EVDPL_CORE_PLUGIN_URL . '/assets/js/wp-pages' . $suffix . '.js', array('jquery'), $this->version, false);

            #Register styles.
            wp_register_style('evdpl-plugin-ui', EVDPL_CORE_PLUGIN_URL . '/assets/css/evdpl-plugin-ui.css', array('evdpl-plugin-fw-icon-font'), $this->version);
            wp_register_style('evdpl-plugin-style', EVDPL_CORE_PLUGIN_URL . '/assets/css/evdpl-plugin-panel.css', array('evdpl-plugin-ui'), $this->version);
            wp_register_style('jquery-ui-style', EVDPL_CORE_PLUGIN_URL . '/assets/css/jquery-ui/jquery-ui.min.css', array(), '1.11.4');
            wp_register_style('colorbox', EVDPL_CORE_PLUGIN_URL . '/assets/css/colorbox.css', array(), $this->version);
            wp_register_style('evdpl-upgrade-to-pro', EVDPL_CORE_PLUGIN_URL . '/assets/css/evdpl-upgrade-to-pro.css', array('colorbox'), $this->version);
            wp_register_style('evdpl-plugin-metaboxes', EVDPL_CORE_PLUGIN_URL . '/assets/css/metaboxes.css', array('evdpl-plugin-ui'), $this->version);
            wp_register_style('evdpl-plugin-fw-fields', EVDPL_CORE_PLUGIN_URL . '/assets/css/evdpl-fields.css', array('evdpl-plugin-ui'), $this->version);

            wp_register_style('raleway-font', '//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900', array(), $this->version);

            $wc_version_suffix = '';
            if (function_exists('WC') || !empty($woocommerce)) {
                $woocommerce_version = function_exists('WC') ? WC()->version : $woocommerce->version;
                $wc_version_suffix = version_compare($woocommerce_version, '3.0.0', '>=') ? '' : '-wc-2.6';

                wp_register_style('woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), $woocommerce_version);
            } else {
                wp_register_script('jquery-tiptip', EVDPL_CORE_PLUGIN_URL . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array('jquery'), '1.3', true);
                wp_register_script('select2', EVDPL_CORE_PLUGIN_URL . '/assets/js/select2/select2.min.js', array('jquery'), '4.0.3', true);
                wp_register_style('evdpl-select2-no-wc', EVDPL_CORE_PLUGIN_URL . '/assets/css/evdpl-select2-no-wc.css', false, $this->version);
            }

            wp_register_script('evdpl-enhanced-select', EVDPL_CORE_PLUGIN_URL . '/assets/js/evdpl-enhanced-select' . $wc_version_suffix . $suffix . '.js', array('jquery', 'select2'), $this->version, true);
            wp_localize_script(
                    'evdpl-enhanced-select',
                    'evdpl_framework_enhanced_select_params',
                    array(
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'search_posts_nonce' => wp_create_nonce('search-posts'),
                        'search_terms_nonce' => wp_create_nonce('search-terms'),
                        'search_customers_nonce' => wp_create_nonce('search-customers'),
                    )
            );

            wp_localize_script(
                    'evdpl-plugin-fw-fields',
                    'evdpl_framework_fw_fields',
                    array(
                        'admin_url' => admin_url('admin.php'),
                        'ajax_url' => admin_url('admin-ajax.php'),
                    )
            );

            wp_localize_script(
                    'evdpl-ui',
                    'evdpl_plugin_fw_ui',
                    array(
                        'i18n' => array(
                            'confirm' => _x('Confirm', 'Button text', 'evdpl-plugin-fw'),
                            'cancel' => _x('Cancel', 'Button text', 'evdpl-plugin-fw'),
                        ),
                    )
            );

            #Localize color-picker to avoid issues with WordPress 5.5.
            if (version_compare($wp_version, '5.5-RC', '>=')) {
                wp_localize_script(
                        'evdpl-colorpicker',
                        'wpColorPickerL10n',
                        array(
                            'clear' => __('Clear'),
                            'clearAriaLabel' => __('Clear color'),
                            'defaultString' => __('Default'),
                            'defaultAriaLabel' => __('Select default color'),
                            'pick' => __('Select Color'),
                            'defaultLabel' => __('Color value'),
                        )
                );
            }

            wp_enqueue_style('evdpl-plugin-fw-admin', EVDPL_CORE_PLUGIN_URL . '/assets/css/admin.css', array(), $this->version);
        }

    }

}

EVDPL_Assets::instance();
