<?php
/**
 * The Template for displaying the 'copy-to-clipboard'
 *
 * @var array $field The field.
 *
 * @package EVDPL\PluginFramework\Templates\Fields\Resources
 * @since   3.6.2
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $value, $force_value, $readonly, $std, $custom_attributes, $data ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'name', 'value', 'force_value', 'readonly', 'std', 'custom_attributes', 'data');

$readonly = isset($readonly) ? !!$readonly : true;
$wrapper_id = !!$field_id ? $field_id . '-wrapper' : '';
$wrapper_class = 'evdpl-plugin-fw-copy-to-clipboard';
if ($readonly) {
    $wrapper_class .= ' evdpl-plugin-fw-copy-to-clipboard--readonly';
}
if (isset($force_value)) {
    $value = $force_value;
}
?>
<div id="<?php echo esc_attr($wrapper_id); ?>" class="<?php echo esc_attr($wrapper_class); ?>">

    <div class="evdpl-plugin-fw-copy-to-clipboard__field-wrap">
        <input type="text"
               id="<?php echo esc_attr($field_id); ?>"
               name="<?php echo esc_attr($name); ?>"
               class="evdpl-plugin-fw-copy-to-clipboard__field <?php echo esc_attr($class); ?>"
               value="<?php echo esc_attr($value); ?>"

               <?php if ($readonly) : ?>
                   readonly
               <?php endif; ?>

               <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
               <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?>
               >
        <div class="evdpl-plugin-fw-copy-to-clipboard__tip"><?php echo esc_html_x('Copied!', 'Copy-to-clipboard message', 'evdpl-plugin-fw'); ?></div>
    </div>
    <div class="evdpl-plugin-fw-copy-to-clipboard__copy">
        <i class="evdpl-plugin-fw-copy-to-clipboard__copy__icon evdpl-icon evdpl-icon-copy"></i>
        <span class="evdpl-plugin-fw-copy-to-clipboard__copy__text"><?php echo esc_html_x('Copy', 'Copy-to-clipboard button text', 'evdpl-plugin-fw'); ?></span>
    </div>
</div>
