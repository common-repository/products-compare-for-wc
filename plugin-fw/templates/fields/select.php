<?php
/**
 * Template for displaying the select field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $value, $options, $multiple, $placeholder, $buttons, $custom_attributes, $data ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'name', 'value', 'options', 'multiple', 'placeholder', 'buttons', 'custom_attributes', 'data');

$multiple = !empty($multiple);
$class = isset($class) ? $class : 'evdpl-plugin-fw-select';
$name = isset($name) ? $name : '';
$name = !!$name && $multiple ? $name . '[]' : $name;

if ($multiple && !is_array($value)) {
    $value = array();
}
?>
<select id="<?php echo esc_attr($field_id); ?>"
        name="<?php echo esc_attr($name); ?>"
        class="<?php echo esc_attr($class); ?>"
        data-value="<?php echo $multiple ? esc_attr(implode(',', $value)) : esc_attr($value); ?>"

        <?php if ($multiple) : ?>
            multiple
        <?php endif; ?>

        <?php if (isset($std)) : ?>
            data-std="<?php echo $multiple && is_array($std) ? esc_attr(implode(',', $std)) : esc_attr($std); ?>"
        <?php endif; ?>

        <?php if (isset($placeholder)) : ?>
            data-placeholder="<?php echo esc_attr($placeholder); ?>"
        <?php endif; ?>

        <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?>
        >
            <?php foreach ($options as $key => $item) : ?>
                <?php if (is_array($item)) : ?>
            <optgroup label="<?php echo esc_attr($item['label']); ?>">
                <?php foreach ($item['options'] as $option_key => $option) : ?>
                    <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $value); ?>><?php echo esc_html($option); ?></option>
                <?php endforeach; ?>
            </optgroup>
        <?php else : ?>
            <option value="<?php echo esc_attr($key); ?>"
            <?php
            if ($multiple) {
                selected(true, in_array($key, $value)); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
            } else {
                selected($key, $value);
            }
            ?>
                    ><?php echo esc_html($item); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
</select>

<?php
// Let's add buttons if they are set.
if (isset($buttons)) {
    $button_field = array(
        'type' => 'buttons',
        'buttons' => $buttons,
    );
    evdpl_plugin_fw_get_field($button_field, true);
}
?>
