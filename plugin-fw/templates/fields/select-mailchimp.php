<?php
/**
 * Template for displaying the select-mailchimp field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $multiple, $std, $value, $options, $button_name, $custom_attributes, $data ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'name', 'multiple', 'std', 'value', 'options', 'button_name', 'custom_attributes', 'data');

$multiple = !empty($multiple);
?>

<select id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($name); ?>" class="evdpl-plugin-fw-select" <?php if ($multiple) : ?> multiple<?php endif; ?> <?php if (isset($std)) : ?>data-std="<?php echo $multiple && is_array($std) ? esc_attr(implode(',', $std)) : esc_attr($std); ?>"<?php endif; ?>  <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?> <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?> >
    <?php foreach ($options as $key => $item) : ?>
        <option value="<?php echo esc_attr($key); ?>"<?php selected($key, $value); ?>><?php echo esc_html($item); ?></option>
    <?php endforeach; ?>
</select>
<input type="button" class="button-secondary <?php echo isset($class) ? esc_attr($class) : ''; ?>" value="<?php echo esc_attr($button_name); ?>"/>
<span class="spinner"></span>
