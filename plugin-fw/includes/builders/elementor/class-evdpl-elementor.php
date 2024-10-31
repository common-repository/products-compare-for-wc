<?php

/**
 * Elementor Class.
 *
 * @class   EVDPL_Elementor
 * @package EVDPL\PluginFramework\Classes
 * @since 1.0.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Elementor')) {

    /**
     * EVDPL_Elementor class.
     *
     */
    class EVDPL_Elementor {

        /**
         * The single instance of the class.
         *
         * @var EVDPL_Elementor
         */
        private static $instance;

        /**
         * The registered widgets.
         *
         * @var array
         */
        private $widgets;

        /**
         * Singleton implementation.
         *
         * @return EVDPL_Elementor
         */
        public static function instance() {
            return !is_null(self::$instance) ? self::$instance : self::$instance = new self();
        }

        /**
         * EVDPL_Elementor constructor.
         */
        private function __construct() {
            if (defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.0.0', '>=')) {
                add_action('init', array($this, 'init'), 20);
            }
        }

        /**
         * Register Elementor widget
         *
         * @param string $widget_name    The widget name.
         * @param array  $widget_options The widget options.
         */
        public function register_widget($widget_name, $widget_options) {
            if (!isset($widget_options['name'])) {
                $widget_options['name'] = $widget_name;
            }
            $this->widgets[$widget_name] = $widget_options;
        }

        /**
         * Let's start with Elementor
         */
        public function init() {
            if ($this->widgets) {
                $this->load_files();
                add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
                add_action('elementor/elements/categories_registered', array($this, 'add_evdpl_category'));

                add_action('elementor/editor/after_enqueue_styles', array($this, 'enqueue_styles'));
                add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_styles'));
            }
        }

        /**
         * Load files
         */
        private function load_files() {
            require_once 'class-evdpl-elementor-widget.php';
        }

        /**
         * Register Elementor Widgets
         */
        public function register_widgets() {
            foreach ($this->widgets as $widget) {
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new EVDPL_Elementor_Widget(array(), array('evdpl_data' => $widget)));
            }
        }

        /**
         * Add "EVDPL" group for Elementor widgets
         *
         * @param Elementor\Elements_Manager $elements_manager Elements Manager.
         */
        public function add_evdpl_category($elements_manager) {
            $elements_manager->add_category(
                    'evdpl',
                    array(
                        'title' => 'EVDPL',
                        'icon' => 'fa fa-plug',
                        'active' => false,
                    )
            );
        }

        /**
         * Enqueue styles in elementor
         */
        public function enqueue_styles() {
            if (\Elementor\Plugin::$instance->preview->is_preview_mode() || \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                wp_enqueue_style('evdpl-plugin-fw-icon-font');
                wp_enqueue_style('evdpl-plugin-fw-elementor');
            }
        }

    }

}

EVDPL_Elementor::instance();
