<?php
/**
 * Template for displaying the toggle-element-fixed field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

$defaults = array(
    'id' => '',
    'class' => '',
    'name' => '',
    'elements' => array(),
    'title' => '',
    'subtitle' => '',
    'onoff_field' => true,
    'save_single_options' => false,
    'custom_attributes' => '',
);
$field = wp_parse_args($field, $defaults);

list ( $field_id, $class, $name, $elements, $the_title, $subtitle, $onoff_field, $save_single_options, $custom_attributes ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'name', 'elements', 'title', 'subtitle', 'onoff_field', 'save_single_options', 'subtitle', 'custom_attributes');

$name = !empty($name) ? $name : $field_id;
$value = get_option($name, array());
?>
<div class="evdpl-toggle_fixed_wrapper" id="<?php echo esc_attr($field_id); ?>">
    <div class="evdpl-toggle-elements">
        <div id="<?php echo esc_attr($field_id); ?>"
             class="evdpl-toggle-row fixed <?php echo!empty($subtitle) ? 'with-subtitle' : ''; ?> <?php echo esc_attr($class); ?>"
             <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
             >
            <div class="evdpl-toggle-title">
                <h3>
                    <span class="title"><?php echo wp_kses_post($the_title); ?></span>
                    <?php if (!empty($subtitle)) : ?>
                        <span class="subtitle"><?php echo wp_kses_post($subtitle); ?></span>
                    <?php endif; ?>
                </h3>
                <span class="evdpl-toggle"><span class="evdpl-icon evdpl-icon-arrow_right ui-sortable-handle"></span></span>
                <?php if (!empty($onoff_field)) : ?>
                    <span class="evdpl-toggle-onoff">
                        <?php
                        evdpl_plugin_fw_get_field(
                                array(
                                    'type' => 'onoff',
                                    'name' => "{$name}[enabled]",
                                    'id' => "{$field_id}_enabled",
                                    'value' => isset($value['enabled']) ? $value['enabled'] : 'no',
                                ),
                                true
                        );
                        ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="evdpl-toggle-content">
                <?php foreach ($elements as $element) : ?>
                    <?php
                    $element_id = $element['id'];
                    $element['name'] = false === $save_single_options ? "{$name}[{$element_id}]" : $element_id;
                    $element['id'] = "{$field_id}_{$element_id}";
                    $element['value'] = '';
                    $element_default = isset($element['default']) ? $element['default'] : '';
                    if (false === $save_single_options) {
                        $element['value'] = isset($value[$element_id]) ? $value[$element_id] : $element_default;
                    } else {
                        $element['value'] = get_option($element_id, $element_default);
                    }
                    ?>
                    <div class="evdpl-toggle-content-row <?php echo esc_attr($element['type']); ?>">
                        <label for="<?php echo esc_attr($element['id']); ?>"><?php echo esc_html($element['title']); ?></label>
                        <div class="evdpl-plugin-fw-option-with-description">
                            <?php evdpl_plugin_fw_get_field($element, true); ?>
                            <span class="description"><?php echo!empty($element['desc']) ? wp_kses_post($element['desc']) : ''; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
