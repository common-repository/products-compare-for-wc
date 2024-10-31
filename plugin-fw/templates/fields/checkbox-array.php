<?php
/**
 * Template for displaying the checkbox-array field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 * @since   1.0.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $name, $class, $options, $value, $data, $custom_attributes ) = evdpl_plugin_fw_extract($field, 'id', 'name', 'class', 'options', 'value', 'data', 'custom_attributes');

$class = isset($class) ? $class : '';
$class = 'evdpl-plugin-fw-checkbox-array ' . $class;

$value = is_array($value) ? $value : array();
?>
<div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($field_id); ?>"
<?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
<?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?>
     >
         <?php foreach ($options as $key => $label) : ?>
             <?php
             $checkbox_id = sanitize_key($field_id . '-' . $key);
             ?>
        <div class="evdpl-plugin-fw-checkbox-array__row">
            <input type="checkbox" id="<?php echo esc_attr($checkbox_id); ?>" name="<?php echo esc_attr($name); ?>[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $value, true)); ?> />
            <label for="<?php echo esc_attr($checkbox_id); ?>"><?php echo wp_kses_post($label); ?></label>
        </div>
    <?php endforeach; ?>
</div>
