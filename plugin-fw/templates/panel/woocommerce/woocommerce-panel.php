<?php
/**
 * The Template for displaying the WooCommerce Panel.
 *
 * @var EVDPL_Plugin_Panel_WooCommerce $this           The EVDPL WooCommerce Panel.
 * @var string                       $page           The current page.
 * @var string                       $wrap_class     The wrapper class.
 * @var array                        $available_tabs The available tabs.
 * @package    EVDPL\PluginFramework\Templates
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

add_thickbox();
?>
<div class="wrap <?php echo esc_attr($wrap_class); ?>">
    <div id="icon-users" class="icon32"><br/></div>
        <?php
        do_action('evdpl_plugin_fw_before_woocommerce_panel', $page);

        if (!empty($available_tabs)) {
            $this->print_panel_content();
        }
        ?>
</div>
