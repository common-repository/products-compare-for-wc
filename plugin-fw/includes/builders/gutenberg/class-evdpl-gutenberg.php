<?php

/**
 * EVDPL Gutenberg Class
 * handle Gutenberg blocks and shortcodes.
 *
 * @class   EVDPL_Gutenberg
 * @package EVDPL\PluginFramework\Classes
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Gutenberg')) {

    /**
     * EVDPL_Gutenberg class.
     *
     */
    class EVDPL_Gutenberg {

        /**
         * The single instance of the class.
         * @var EVDPL_Gutenberg
         */
        private static $instance;

        /**
         * Registered blocks
         * @var array
         */
        private $registered_blocks = array();

        /**
         * Blocks to register
         * @var array
         */
        private $to_register_blocks = array();

        /**
         * Blocks args
         * @var array
         */
        private $blocks_args = array();

        /**
         * Block category slug
         * @var string
         */
        private $category_slug = 'evdpl-blocks';

        /**
         * Singleton implementation.
         * @return EVDPL_Gutenberg
         */
        public static function instance() {
            return !is_null(self::$instance) ? self::$instance : self::$instance = new self();
        }

        /**
         * EVDPL_Gutenberg constructor.
         */
        private function __construct() {
            add_action('init', array($this, 'init'));
            add_action('init', array($this, 'register_blocks'), 30);
            add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
            add_action('wp_ajax_evdpl_plugin_fw_gutenberg_do_shortcode', array($this, 'do_shortcode'));
            add_action('wc_ajax_evdpl_plugin_fw_gutenberg_do_shortcode', array($this, 'do_shortcode'));
        }

        /**
         * Initialization
         */
        public function init() {
            
        }

        /**
         * Enqueue scripts for gutenberg
         */
        public function enqueue_block_editor_assets() {
            $ajax_url = function_exists('WC') ? add_query_arg('wc-ajax', 'evdpl_plugin_fw_gutenberg_do_shortcode', trailingslashit(site_url())) : admin_url('admin-ajax.php');
            $gutenberg = array('ajaxurl' => $ajax_url);
            $asset_file = include EVDPL_CORE_PLUGIN_PATH . '/dist/gutenberg/index.asset.php';

            $gutenberg_assets_url = EVDPL_CORE_PLUGIN_URL . '/dist/gutenberg';

            wp_register_script(
                    'evdpl-gutenberg',
                    $gutenberg_assets_url . '/index.js',
                    $asset_file['dependencies'],
                    $asset_file['version'],
                    true
            );

            wp_localize_script('evdpl-gutenberg', 'evdpl_gutenberg_ajax', $gutenberg); // Deprecated! Kept for backward compatibility.
            wp_localize_script('evdpl-gutenberg', 'evdpl_gutenberg', $this->blocks_args); // Deprecated! Kept for backward compatibility.

            wp_localize_script('evdpl-gutenberg', 'evdplGutenberg', $gutenberg);
            wp_localize_script('evdpl-gutenberg', 'evdplGutenbergBlocks', $this->blocks_args);

            wp_enqueue_script('evdpl-gutenberg');
            wp_enqueue_style('evdpl-gutenberg', $gutenberg_assets_url . '/style-index.css', array(), evdpl_plugin_fw_get_version());
        }

        /**
         * Add blocks to gutenberg editor.
         */
        public function register_blocks() {
            $block_args = array();
            foreach ($this->to_register_blocks as $block => $args) {
                if (isset($args['style'])) {
                    $block_args['style'] = esc_attr($args['style']);
                }

                if (isset($args['script'])) {
                    $block_args['script'] = esc_attr($args['script']);
                }

                if (register_block_type("evdpl/{$block}", $block_args)) {
                    $this->registered_blocks[] = $block;
                }
            }

            if (!empty($this->registered_blocks)) {
                add_filter('block_categories', array($this, 'block_category'), 10, 2);
            }
        }

        /**
         * Add block category
         *
         * @param array   $categories The block categories.
         * @param WP_Post $post       The current post.
         *
         * @return array The block categories.
         */
        public function block_category($categories, $post) {
            return array_merge(
                    $categories,
                    array(
                        array(
                            'slug' => 'evdpl-blocks',
                            'title' => _x('EVDPL', '[gutenberg]: Category Name', 'evdpl-plugin-fw'),
                        ),
                    )
            );
        }

        /**
         * Add new blocks to Gutenberg
         *
         * @param string|array $blocks The blocks to be added.
         *
         * @return bool True if the blocks was successfully added, false otherwise.
         */
        public function add_blocks($blocks) {
            $added = false;
            if (!empty($blocks)) {
                $added = true;
                if (is_array($blocks)) {
                    $this->to_register_blocks = array_merge($this->to_register_blocks, $blocks);
                } else {
                    $this->to_register_blocks[] = $blocks;
                }
            }

            return $added;
        }

        /**
         * Return an array with the registered blocks
         *
         * @return array
         */
        public function get_registered_blocks() {
            return $this->registered_blocks;
        }

        /**
         * Return an array with the blocks to register
         *
         * @return array
         */
        public function get_to_register_blocks() {
            return $this->to_register_blocks;
        }

        /**
         * Return an array with the block(s) arguments
         *
         * @param string $block_key The block key.
         *
         * @return array|false
         */
        public function get_block_args($block_key = 'all') {
            if ('all' === $block_key) {
                return $this->blocks_args;
            } elseif (isset($this->blocks_args[$block_key])) {
                return $this->blocks_args[$block_key];
            }

            return false;
        }

        /**
         * Retrieve the default category slug
         *
         * @return string
         */
        public function get_default_blocks_category_slug() {
            return $this->category_slug;
        }

        /**
         * Set the block arguments
         *
         * @param array $args The block arguments.
         */
        public function set_block_args($args) {
            foreach ($args as $block => $block_args) {

                // Add Default Keywords.
                $default_keywords = array('evdpl');
                if (!empty($block_args['shortcode_name'])) {
                    $default_keywords[] = esc_attr($block_args['shortcode_name']);
                }

                $args[$block]['keywords'] = !empty($args[$block]['keywords']) ? array_merge($args[$block]['keywords'], $default_keywords) : $default_keywords;

                if (count($args[$block]['keywords']) > 3) {
                    $args[$block]['keywords'] = array_slice($args[$block]['keywords'], 0, 3);
                }

                if (empty($block_args['category'])) {
                    // Add the EVDPL block category.
                    $args[$block]['category'] = $this->get_default_blocks_category_slug();
                }

                $args[$block]['do_shortcode'] = isset($block_args['do_shortcode']) ? !!$block_args['do_shortcode'] : true;

                if (isset($block_args['attributes'])) {
                    foreach ($block_args['attributes'] as $attr_name => $attributes) {

                        if (!empty($attributes['options']) && is_array($attributes['options'])) {
                            $options = array();
                            foreach ($attributes['options'] as $v => $l) {
                                // Prepare options array for react component.
                                $options[] = array(
                                    'label' => $l,
                                    'value' => $v,
                                );
                            }
                            $args[$block]['attributes'][$attr_name]['options'] = $options;
                        }

                        if (empty($attributes['remove_quotes'])) {
                            $args[$block]['attributes'][$attr_name]['remove_quotes'] = false;
                        }

                        // Special Requirements for Block Type.
                        if (!empty($attributes['type'])) {
                            $args[$block]['attributes'][$attr_name]['controlType'] = $attributes['type'];
                            $args[$block]['attributes'][$attr_name]['type'] = 'string';

                            switch ($attributes['type']) {
                                case 'select':
                                    // Add default value for multiple.
                                    if (!isset($attributes['multiple'])) {
                                        $args[$block]['attributes'][$attr_name]['multiple'] = false;
                                    }

                                    if (!empty($attributes['multiple'])) {
                                        $args[$block]['attributes'][$attr_name]['type'] = 'array';
                                    }
                                    break;

                                case 'color':
                                case 'colorpicker':
                                    if (!isset($attributes['disableAlpha'])) {
                                        // Disable alpha gradient for color picker.
                                        $args[$block]['attributes'][$attr_name]['disableAlpha'] = true;
                                    }
                                    break;

                                case 'number':
                                    $args[$block]['attributes'][$attr_name]['type'] = 'integer';
                                    break;

                                case 'toggle':
                                case 'checkbox':
                                    $args[$block]['attributes'][$attr_name]['type'] = 'boolean';
                                    break;
                            }
                        }
                    }
                }
            }

            $this->blocks_args = array_merge($this->blocks_args, $args);
        }

        /**
         * Get a do_shortcode in ajax call to show block preview
         * */
        public function do_shortcode() {
            // phpcs:disable WordPress.Security.NonceVerification
            $current_action = current_action();
            $shortcode = !empty($_REQUEST['shortcode']) ? wp_unslash($_REQUEST['shortcode']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

            if (!apply_filters('evdpl_plugin_fw_gutenberg_skip_shortcode_sanitize', false)) {
                $shortcode = sanitize_text_field(stripslashes($shortcode));
            }

            ob_start();

            do_action('evdpl_plugin_fw_gutenberg_before_do_shortcode', $shortcode, $current_action);
            echo do_shortcode(apply_filters('evdpl_plugin_fw_gutenberg_shortcode', $shortcode, $current_action));
            do_action('evdpl_plugin_fw_gutenberg_after_do_shortcode', $shortcode, $current_action);

            $html = ob_get_clean();
            if (is_ajax()) {
                wp_send_json(array('html' => $html));
            }

            // phpcs:enable
        }

    }

}

if (!function_exists('EVDPL_Gutenberg')) {

    /**
     * Single instance of EVDPL_Gutenberg
     *
     * @return EVDPL_Gutenberg
     */
    function EVDPL_Gutenberg() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
        return EVDPL_Gutenberg::instance();
    }

}

EVDPL_Gutenberg();
