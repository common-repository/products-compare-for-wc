<?php
/**
 * Template for displaying the title field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $desc, $std, $custom_attributes, $data ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'name', 'desc', 'std', 'custom_attributes', 'data');

$class = isset($class) ? $class : 'title';
?>
<h3 id="<?php echo esc_attr($field_id); ?>" class="<?php echo esc_attr($class); ?>" <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?> <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?> ><?php echo wp_kses_post($desc); ?></h3>
