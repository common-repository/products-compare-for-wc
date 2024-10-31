<?php
/**
 * Template for displaying the sidebarlist field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $value, $custom_attributes, $data ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'name', 'value', 'custom_attributes', 'data');

$class = isset($class) ? $class : 'evdpl-plugin-fw-select';
$options = evdpl_registered_sidebars();
?>
<select id="<?php echo esc_attr($field_id); ?>"
        name="<?php echo esc_attr($name); ?>"
        class="<?php echo esc_attr($class); ?>"

        <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
        <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?>
        >
            <?php foreach ($options as $key => $item) : ?>
        <option value="<?php echo esc_attr($key); ?>"<?php selected($key, $value); ?>><?php echo esc_html($item); ?></option>
    <?php endforeach; ?>
</select>
