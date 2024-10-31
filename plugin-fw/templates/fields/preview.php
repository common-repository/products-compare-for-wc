<?php
/**
 * Template for displaying the preview field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $class, $value, $custom_attributes, $data ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'value', 'custom_attributes', 'data');

$class = !empty($class) ? $class : 'evdpl-plugin-fw-preview-field';
?>
<img id="<?php echo esc_attr($field_id); ?>" class="<?php echo esc_attr($class); ?>" src="<?php echo esc_url($value); ?>" <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?> <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?> >
