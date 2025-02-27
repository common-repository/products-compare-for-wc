<?php
/**
 * Template for displaying the checkbox field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $name, $class, $std, $value, $data, $custom_attributes, $desc_inline ) = evdpl_plugin_fw_extract($field, 'id', 'name', 'class', 'std', 'value', 'data', 'custom_attributes', 'desc-inline');
?>
<input type="checkbox" id="<?php echo esc_attr($field_id); ?>"
       name="<?php echo esc_attr($name); ?>" value="1"
       class="<?php echo!empty($class) ? esc_attr($class) : ''; ?>"
       <?php if (isset($std)) : ?>
           data-std="<?php echo esc_attr($std); ?>"
       <?php endif; ?>
       <?php checked(true, evdpl_plugin_fw_is_true($value)); ?>
       <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
       <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?>
       />
       <?php if (isset($desc_inline)) : ?>
    <span class='description inline'><?php echo wp_kses_post($desc_inline); ?></span>
<?php endif; ?>
