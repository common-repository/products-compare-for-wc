<?php
/**
 * EVDPL Plugin Sub-panel Class.
 *
 * @class   EVDPL_Plugin_SubPanel
 * @package EVDPL\PluginFramework\Classes
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Plugin_SubPanel')) {

    /**
     * EVDPL_Plugin_SubPanel class.
     *
     */
    class EVDPL_Plugin_SubPanel extends EVDPL_Plugin_Panel {

        /**
         * Version of the class.
         *
         * @var string
         */
        public $version = '1.0.0';

        /**
         * List of settings parameters.
         *
         * @var array
         */
        public $settings = array();

        /**
         * EVDPL_Plugin_SubPanel constructor.
         *
         * @param array $args The panel arguments.
         */
        public function __construct($args = array()) {
            if (!empty($args)) {
                $this->settings = $args;
                $this->settings['parent'] = $this->settings['page'];
                $this->tabs_path_files = $this->get_tabs_path_files();

                add_action('admin_init', array($this, 'register_settings'));
                add_action('admin_menu', array(&$this, 'add_setting_page'));
                add_action('admin_bar_menu', array(&$this, 'add_admin_bar_menu'), 100);
                add_action('admin_init', array(&$this, 'add_fields'));
                add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            }
        }

        /**
         * Register Settings
         * Generate wp-admin settings pages by registering your settings and using a few callbacks to control the output
         *
         */
        public function register_settings() {
            register_setting('evdpl_' . $this->settings['page'] . '_options', 'evdpl_' . $this->settings['page'] . '_options', array(&$this, 'options_validate'));
        }

        /**
         * Add Setting SubPage
         * add Setting SubPage to WordPress administrator
         *
         */
        public function add_setting_page() {
            global $admin_page_hooks;
            $logo = evdpl_plugin_fw_get_default_logo();

            $admin_logo = function_exists('evdpl_get_option') ? esc_attr(evdpl_get_option('admin-logo-menu')) : '';

            if (!empty($admin_logo)) {
                $logo = $admin_logo;
            }

            if (!isset($admin_page_hooks['evdpl_plugin_panel'])) {
                $position = apply_filters('evdpl_plugins_menu_item_position', '62.32');
                add_menu_page('evdpl_plugin_panel', 'EVDPL', 'nosuchcapability', 'evdpl_plugin_panel', null, $logo, $position);
                // Prevent issues for backward compatibility.
                $admin_page_hooks['evdpl_plugin_panel'] = 'evdpl-plugins'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            }

            add_submenu_page('evdpl_plugin_panel', $this->settings['label'], $this->settings['label'], 'manage_options', $this->settings['page'], array($this, 'evdpl_panel'));
            remove_submenu_page('evdpl_plugin_panel', 'evdpl_plugin_panel');
        }

        /**
         * Show a tabbed panel to setting page
         * a callback function called by add_setting_page => add_submenu_page
         *
         */
        public function evdpl_panel() {
            $tabs = '';
            $current_tab = $this->get_current_tab();
            $evdpl_options = $this->get_main_array_options();

            foreach ($this->settings['admin-tabs'] as $tab => $tab_value) {
                $active_class = $current_tab === $tab ? ' nav-tab-active' : '';
                $url = '?page=' . $this->settings['page'] . '&tab=' . $tab;

                $tabs .= '<a class="nav-tab' . esc_attr($active_class) . '" href="' . esc_url($url) . '">' . wp_kses_post($tab_value) . '</a>';
            }
            ?>
            <div id="icon-themes" class="icon32"><br/></div>
            <h2 class="nav-tab-wrapper">
                <?php echo wp_kses_post($tabs);  ?>
            </h2>
            <?php
            $custom_tab_options = $this->get_custom_tab_options($evdpl_options, $current_tab);
            if ($custom_tab_options) {
                $this->print_custom_tab($custom_tab_options);

                return;
            }

            $panel_content_class = apply_filters('evdpl_admin_panel_content_class', 'evdpl-admin-panel-content-wrap');
            ?>
            <div id="wrap" class="evdpl-plugin-fw plugin-option evdpl-admin-panel-container">
                <?php $this->message(); ?>
                <div class="<?php echo esc_attr($panel_content_class); ?>">
                    <h2><?php echo wp_kses_post($this->get_tab_title()); ?></h2>
                    <?php if ($this->is_show_form()) : ?>
                        <form id="evdpl-plugin-fw-panel" method="post" action="options.php">
                            <?php do_settings_sections('evdpl'); ?>
                            <p>&nbsp;</p>
                            <?php settings_fields('evdpl_' . $this->settings['parent'] . '_options'); ?>
                            <input type="hidden" name="<?php echo esc_attr($this->get_name_field('current_tab')); ?>" value="<?php echo esc_attr($current_tab); ?>"/>
                            <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'evdpl-plugin-fw'); ?>" style="float:left;margin-right:10px;"/>
                        </form>
                        <form method="post">
                            <?php
                            $reset_warning = __('If you continue with this action, you will reset all options in this page.', 'evdpl-plugin-fw') . '\n' . __('Are you sure?', 'evdpl-plugin-fw');
                            ?>
                            <input type="hidden" name="evdpl-action" value="reset"/>
                            <input type="submit" name="evdpl-reset" class="button-secondary" value="<?php esc_attr_e('Reset to default', 'evdpl-plugin-fw'); ?>"
                                   onclick="return confirm('<?php echo esc_attr($reset_warning); ?>');"/>
                        </form>
                        <p>&nbsp;</p>
                    <?php endif ?>
                </div>
            </div>
            <?php
        }

    }

}
