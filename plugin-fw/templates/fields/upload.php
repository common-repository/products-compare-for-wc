<?php
/**
 * Template for displaying the text field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $value, $std, $custom_attributes, $data ) = evdpl_plugin_fw_extract($field, 'id', 'class', 'name', 'value', 'std', 'custom_attributes', 'data');

$file = $value;
?>
<div class="evdpl-plugin-fw-upload-container <?php echo!empty($class) ? esc_attr($class) : ''; ?>">
    <div class="evdpl-plugin-fw-upload-img-preview" style="margin-top:10px;">
        <?php if (preg_match('/(jpg|jpeg|png|gif|ico|svg)$/', $file)) : ?>
            <img src="<?php echo esc_url($file); ?>" style="max-width:600px; max-height:300px;"/>
        <?php endif ?>
    </div>
    <input type="text" id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($name); ?>" class="evdpl-plugin-fw-upload-img-url" value="<?php echo esc_attr($value); ?>" <?php if (isset($default)) : ?> data-std="<?php echo esc_attr($default); ?>" <?php endif; ?> <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?> <?php echo isset($data) ? evdpl_plugin_fw_html_data_to_string($data) : ''; ?> />
    <button class="evdpl-plugin-fw-upload-button" id="<?php echo esc_attr($field_id); ?>-button"><?php esc_html_e('Upload', 'evdpl-plugin-fw'); ?></button>
    <button type="button" id="<?php echo esc_attr($field_id); ?>-button-reset" class="evdpl-plugin-fw-upload-button-reset" data-default="<?php echo isset($default) ? esc_attr($default) : ''; ?>" ><?php esc_html_e('Reset', 'evdpl-plugin-fw'); ?></button>
</div>
