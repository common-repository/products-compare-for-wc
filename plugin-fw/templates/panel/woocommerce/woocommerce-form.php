<?php
/**
 * The Template for displaying the WooCommerce form.
 *
 * @var EVDPL_Plugin_Panel_WooCommerce $this       The EVDPL WooCommerce Panel.
 * @var string                       $option_key The current option key ( see EVDPL_Plugin_Panel::get_current_option_key() ).
 * @package    EVDPL\PluginFramework\Templates
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

$content_class = apply_filters('evdpl_admin_panel_content_class', 'evdpl-admin-panel-content-wrap');
$container_id = $this->settings['page'] . '_' . $option_key;
$reset_warning = __('If you continue with this action, you will reset all options in this page.', 'evdpl-plugin-fw') . '\n' . __('Are you sure?', 'evdpl-plugin-fw');
?>

<div id="<?php echo esc_attr($container_id); ?>" class="evdpl-plugin-fw  evdpl-admin-panel-container">

    <?php do_action('evdpl_framework_before_print_wc_panel_content', $option_key); ?>

    <div class="<?php echo esc_attr($content_class); ?>">
        <form id="plugin-fw-wc" method="post">

            <?php $this->add_fields(); ?>

            <p class="submit" style="float: left;margin: 0 10px 0 0;">
                <?php wp_nonce_field('evdpl_panel_wc_options_' . $this->settings['page'], 'evdpl_panel_wc_options_nonce'); ?>
                <input class="button-primary" id="main-save-button" type="submit" value="<?php esc_html_e('Save Options', 'evdpl-plugin-fw'); ?>"/>
            </p>

            <?php if (apply_filters('evdpl_framework_show_float_save_button', true)) : ?>
                <button id="evdpl-plugin-fw-float-save-button" class="evdpl-plugin-fw__button--primary evdpl-plugin-fw-animate__appear-from-bottom" data-default-label="<?php esc_attr_e('Save Options', 'evdpl-plugin-fw'); ?>" data-saved-label="<?php esc_attr_e('Options Saved', 'evdpl-plugin-fw'); ?>"><i class="evdpl-icon evdpl-icon-save"></i> <?php esc_html_e('Save Options', 'evdpl-plugin-fw'); ?></button>
            <?php endif; ?>
        </form>
        <form id="plugin-fw-wc-reset" method="post">
            <input type="hidden" name="evdpl-action" value="wc-options-reset"/>
            <?php wp_nonce_field('evdpl_wc_reset_options_' . $this->settings['page'], 'evdpl_wc_reset_options_nonce'); ?>
            <input type="submit" name="evdpl-reset" class="button-secondary" value="<?php esc_html_e('Reset Defaults', 'evdpl-plugin-fw'); ?>"
                   onclick="return confirm('<?php echo esc_attr($reset_warning); ?>');"/>
        </form>
    </div>

    <?php do_action('evdpl_framework_after_print_wc_panel_content', $option_key); ?>

</div>
