<?php
/**
 * Template for displaying the select-images field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $value, $options, $custom_attributes, $data ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'name', 'value', 'options', 'custom_attributes', 'data');

$class = isset($class) ? $class : 'evdpl-plugin-fw-select-images';
?>
<div id="<?php echo esc_attr($field_id); ?>-wrapper" class="evdpl-plugin-fw-select-images__wrapper" data-type="select-images">
    <select id="<?php echo esc_attr($field_id); ?>"
            name="<?php echo esc_attr($name); ?>"
            class="<?php echo esc_attr($class); ?>"
            style="display: none"
            <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
            <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?>
            >
                <?php foreach ($options as $key => $item) : ?>
                    <?php
                    $label = !empty($item['label']) ? $item['label'] : $key;
                    ?>
            <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $value); ?> ><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
    </select>

    <ul class="evdpl-plugin-fw-select-images__list">
        <?php foreach ($options as $key => $item) : ?>
            <?php
            $label = !empty($item['label']) ? $item['label'] : $key;
            $image = !empty($item['image']) ? $item['image'] : '';
            ?>
            <?php if ($image) : ?>
                <?php
                $selected_class = 'evdpl-plugin-fw-select-images__item--selected';
                $current_class = $key === $value ? $selected_class : '';
                ?>
                <li class="evdpl-plugin-fw-select-images__item <?php echo esc_attr($current_class); ?>"
                    data-type="select-images-item"
                    data-key="<?php echo esc_attr($key); ?>"
                    <?php echo isset($item['data']) ? evdpl_plugin_fw_html_data_to_string($item['data']) : ''; ?>
                    >
                        <?php if ($label) : ?>
                        <div class="evdpl-plugin-fw-select-images__item__label"><?php echo esc_html($label); ?></div>
                    <?php endif; ?>
                    <img class="evdpl-plugin-fw-select-images_src" src="<?php echo esc_url($image); ?>"/>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
