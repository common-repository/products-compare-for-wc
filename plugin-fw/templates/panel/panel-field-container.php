<?php
/**
 * The Template for displaying the panel field container.
 *
 * @var array $option   The field.
 * @var mixed $db_value The field value stored in DB.
 * @since      1.0.0
 * @package    EVDPL\PluginFramework\Templates
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

$field_id = $this->get_id_field($option['id']);
$name = $this->get_name_field($option['id']);
$field_type = $option['type'];

$field = $option;
$field['id'] = $field_id;
$field['name'] = $name;
$field['value'] = $db_value;
if (!empty($custom_attributes)) {
    $field['custom_attributes'] = $custom_attributes;
}

$container_id = $field_id . '-container';
$container_classes = array(
    'evdpl_options',
    'evdpl-plugin-fw-field-wrapper',
    "evdpl-plugin-fw-{$field_type}-field-wrapper",
);
$container_classes = implode(' ', $container_classes);
?>
<div id="<?php echo esc_attr($container_id); ?>" class="<?php echo esc_attr($container_classes); ?>" <?php echo evdpl_panel_field_deps_data($option, $this); ?>>
    <div class="option">
        <?php evdpl_plugin_fw_get_field($field, true, false); ?>
    </div>

    <?php if (!empty($field['desc'])) : ?>
        <span class="description"><?php echo wp_kses_post($field['desc']); ?></span>
    <?php endif; ?>

    <div class="clear"></div>
</div>
