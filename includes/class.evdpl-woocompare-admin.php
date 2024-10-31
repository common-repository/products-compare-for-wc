<?php
/**
 * Admin class
 *
 * @author EVDPL
 * @package Products Compare for WC
 * @version 1.0.0
 */
defined('EVDPL_WOOCOMPARE') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Woocompare_Admin')) {

    /**
     * Admin class.
     * The class manage all the admin behaviors.
     * @since 1.0.0
     */
    class EVDPL_Woocompare_Admin {

        /**
         * Plugin options
         * @since 1.0.0
         * @var array
         * @access public
         */
        public $options = array();

        /**
         * Plugin version
         * @since 1.0.0
         * @var string
         */
        public $version = EVDPL_WOOCOMPARE_VERSION;

        /**
         * Panel Object
         * @var EVDPL_Plugin_Panel_WooCommerce
         */
        protected $panel;

        /**
         * Compare panel page
         * @var string
         */
        protected $panel_page = 'evdpl_woocompare_panel';

        /**
         * Constructor
         * @access public
         * @since 1.0.0
         */
        public function __construct() {

            add_action('admin_menu', array($this, 'evdpl_register_panel'), 5);

            #Add action links.
            add_filter('plugin_action_links_' . plugin_basename(EVDPL_WOOCOMPARE_DIR . '/' . basename(EVDPL_WOOCOMPARE_FILE)), array($this, 'evdpl_action_links'));
            add_filter('evdpl_show_plugin_row_meta', array($this, 'evdpl_plugin_row_meta'), 10, 5);

            add_action('admin_init', array($this, 'default_options'), 99);
            add_action('admin_enqueue_scripts', array($this, 'evdpl_enqueue_styles_scripts'), 20);

            add_action('woocommerce_admin_field_woocompare_image_width', array($this, 'evdpl_admin_fields_woocompare_image_width'));
            add_action('woocommerce_admin_field_woocompare_attributes', array($this, 'evdpl_admin_fields_attributes'), 10, 1);
            add_filter('woocommerce_admin_settings_sanitize_option_evdpl_woocompare_fields_attrs', array($this, 'evdpl_admin_update_custom_option'), 10, 3);

            #EVDPL WCWL Loaded.
            do_action('evdpl_woocompare_loaded');
        }

        /**
         * Action Links: add the action links to plugin admin page
         * @since 1.0.0
         * @param array $links Links plugin array.
         * @return mixed
         * @use plugin_action_links_{$plugin_file_name}
         */
        public function evdpl_action_links($links) {

            $links[] = '<a href="' . admin_url("admin.php?page={$this->panel_page}") . '">' . __('Settings', 'evdpl-woocommerce-compare') . '</a>';

            return $links;
        }

        /**
         * Add a panel under EVDPL Plugins tab
         * @since    1.0
         * @return   void
         * @see plugin-fw/lib/evdpl-plugin-panel.php
         */
        public function evdpl_register_panel() {

            if (!empty($this->panel)) {
                return;
            }

            $admin_tabs = array(
                'general' => __('Settings', 'evdpl-woocommerce-compare'),
            );

            $args = array(
                'create_menu_page' => true,
                'parent_slug' => '',
                'page_title' => _x('Product Compare for WC', 'Admin Plugin Name', 'evdpl-woocommerce-compare'),
                'menu_title' => _x('Product Compare for WC', 'Admin Plugin Name', 'evdpl-woocommerce-compare'),
                'capability' => 'manage_options',
                'parent' => '',
                'parent_page' => 'evdpl_plugin_panel',
                'page' => $this->panel_page,
                'admin-tabs' => apply_filters('evdpl_woocompare_admin_tabs', $admin_tabs),
                'options-path' => EVDPL_WOOCOMPARE_DIR . '/plugin-options',
                'class' => evdpl_set_wrapper_class(),
                'plugin_slug' => EVDPL_WOOCOMPARE_SLUG,
            );

            if (!class_exists('EVDPL_Plugin_Panel_WooCommerce')) {
                require_once EVDPL_WOOCOMPARE_DIR . 'plugin-fw/lib/evdpl-plugin-panel-wc.php';
            }

            $this->panel = new EVDPL_Plugin_Panel_WooCommerce($args);
            $this->options = $this->panel->get_main_array_options();
        }

        /**
         * Set default custom options
         * @since 1.0.0
         */
        public function default_options() {
            foreach ($this->options as $section) {

                foreach ($section as $value) {

                    if (isset($value['std']) && isset($value['id'])) {

                        if ('image_width' === $value['type']) {
                            add_option($value['id'], $value['std']);
                        } elseif ('woocompare_attributes' === $value['type']) {

                            $value_id = str_replace('_attrs', '', $value['id']);

                            $in_db = esc_attr(get_option($value_id));
                            $in_db_original = esc_attr(get_option($value['id']));

                            #If options is already in db and not reset defaults continue.
                            if ($in_db && 'all' !== $in_db_original) {
                                continue;
                            }

                            if ('all' === $value['default']) {
                                $fields = EVDPL_Woocompare_Helper::evdpl_standard_fields();
                                $all = array();

                                foreach (array_keys($fields) as $field) {
                                    $all[$field] = true;
                                }

                                update_option($value_id, $all);
                            } else {
                                update_option($value_id, $value['std']);
                            }
                        }
                    }
                }
            }
        }

        /**
         * Add the action links to plugin admin page
         *
         * @since    1.0
         * @param array    $new_row_meta_args An array of plugin row meta.
         * @param string[] $plugin_meta An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
         * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
         * @param array    $plugin_data An array of plugin data.
         * @param string   $status Status of the plugin. Defaults are 'All', 'Active',
         * 'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
         * 'Drop-ins', 'Search', 'Paused'.
         * @return   array
         */
        public function evdpl_plugin_row_meta($new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status) {

            if (defined('EVDPL_WOOCOMPARE_INIT') && EVDPL_WOOCOMPARE_INIT === $plugin_file) {
                $new_row_meta_args['slug'] = EVDPL_WOOCOMPARE_SLUG;

            }

            return $new_row_meta_args;
        }

        /**
         * Register Pointer
         * @since 1.0.0
         */
        public function evdpl_register_pointer() {
            return false;
        }

        /**
         * Create new Woocommerce admin field: checkboxes
         * @access public
         * @since 1.0.0
         * @param array $value The field value.
         * @return void
         */
        public function evdpl_admin_fields_attributes($value) {
            $fields = EVDPL_Woocompare_Helper::evdpl_standard_fields();
            $all = array();
            $checked = get_option(str_replace('_attrs', '', $value['id']), 'all' === $value['default'] ? $all : array());

            foreach (array_keys($fields) as $field) {
                $all[$field] = true;
            }
            #Then add fields that are not still saved.
            foreach ($checked as $k => $v) {
                unset($all[$k]);
            }
            $checkboxes = array_merge($checked, $all);
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr($value['id']); ?>"><?php echo esc_html($value['name']); ?></label>
                </th>

                <td class="forminp attributes">
                    <p class="description"><?php echo wp_kses_post($value['desc']); ?></p>
                    <ul class="fields">
                        <?php
                        foreach ($checkboxes as $slug => $checked) :
                            if (!isset($fields[$slug])) {
                                continue;
                            }
                            ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="<?php echo esc_attr($value['id']); ?>[]" id="<?php echo esc_attr($value['id']); ?>_<?php echo esc_attr($slug); ?>" value="<?php echo esc_html($slug); ?>"<?php checked($checked); ?> /> <?php echo esc_html($fields[$slug]); ?>
                                </label>
                            </li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                    <input type="hidden" name="<?php echo esc_attr($value['id']); ?>_positions" value="<?php echo implode(',', array_keys($checkboxes)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped   ?>"/>
                </td>
            </tr>
            <?php
        }

        /**
         * Create new Woocommerce admin field: evdpl_wc_image_width
         * @access public
         * @since 1.0.0
         * @param array $value Field value.
         * @return void
         */
        public function evdpl_admin_fields_woocompare_image_width($value) {

            $width = WC_Admin_Settings::get_option($value['id'] . '[width]', $value['default']['width']);
            $height = WC_Admin_Settings::get_option($value['id'] . '[height]', $value['default']['height']);
            $crop = WC_Admin_Settings::get_option($value['id'] . '[crop]');
            $crop = ( 'on' === $crop || '1' === $crop ) ? 1 : 0;
            $crop = checked(1, $crop, false);
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc"><?php echo esc_html($value['title']); ?></th>
                <td class="forminp image_width_settings">

                    <input name="<?php echo esc_attr($value['id']); ?>[width]" id="<?php echo esc_attr($value['id']); ?>-width" type="text" size="3" value="<?php echo esc_attr($width); ?>"/> &times;
                    <input name="<?php echo esc_attr($value['id']); ?>[height]" id="<?php echo esc_attr($value['id']); ?>-height" type="text" size="3" value="<?php echo esc_attr($height); ?>"/>px

                    <label><input name="<?php echo esc_attr($value['id']); ?>[crop]" id="<?php echo esc_attr($value['id']); ?>-crop" type="checkbox" <?php echo esc_html($crop); ?> /> <?php esc_html_e('Do you want to hard crop the image?', 'evdpl-woocommerce-compare'); ?>
                    </label>
                    <p class="description"><?php echo esc_html($value['desc']); ?></p>

                </td>
            </tr>
            <?php
        }

        /**
         * Save the admin field: slider
         * @access public
         * @since 1.0.0
         * @param mixed $value The option value.
         * @param mixed $option The options array.
         * @param mixed $raw_value The option raw value.
         * @return mixed
         */
        public function evdpl_admin_update_custom_option($value, $option, $raw_value) {

            $val = array();
            $checked_fields = isset($_POST[$option['id']]) ? maybe_unserialize(wp_unslash($_POST[$option['id']])) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $fields = isset($_POST[$option['id'] . '_positions']) ? array_map('wc_clean', explode(',', wp_unslash($_POST[$option['id'] . '_positions']))) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

            foreach ($fields as $field) {
                $val[$field] = in_array($field, $checked_fields, true);
            }

            update_option(str_replace('_attrs', '', $option['id']), $val);

            return $value;
        }

        /**
         * Enqueue admin styles and scripts
         * @access public
         * @since 1.0.0
         * @return void
         */
        public function evdpl_enqueue_styles_scripts() {

            $min = !( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '.min' : '';

            if (isset($_GET['page']) && sanitize_text_field(wp_unslash($_GET['page'])) === $this->panel_page) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                wp_enqueue_script('jquery-ui');
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-mouse');
                wp_enqueue_script('jquery-ui-slider');
                wp_enqueue_script('jquery-ui-sortable');

                wp_enqueue_style('evdpl_woocompare_admin', EVDPL_WOOCOMPARE_URL . 'assets/css/admin.css', array(), EVDPL_WOOCOMPARE_VERSION);
                wp_enqueue_script('evdpl_woocompare', EVDPL_WOOCOMPARE_URL . 'assets/js/woocompare-admin' . $min . '.js', array('jquery', 'jquery-ui-sortable'), EVDPL_WOOCOMPARE_VERSION, true);
            }

            do_action('evdpl_woocompare_enqueue_styles_scripts');
        }

    }

}
