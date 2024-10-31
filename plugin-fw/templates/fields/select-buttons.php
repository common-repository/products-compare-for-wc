<?php

/**
 * Template for displaying the select-buttons field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

wp_enqueue_script('wc-enhanced-select');

$field['type'] = 'select';

if (empty($field['class'])) {
    unset($field['class']);
}

$add_label = isset($field['add_all_button_label']) ? $field['add_all_button_label'] : __('Add All', 'evdpl-plugin-fw');
$default_args = array(
    'multiple' => true,
    'class' => 'wc-enhanced-select',
    'buttons' => array(
        array(
            'name' => $add_label,
            'class' => 'evdpl-plugin-fw-select-all',
            'data' => array(
                'select-id' => $field['id'],
            ),
        ),
        array(
            'name' => __('Remove All', 'evdpl-plugin-fw'),
            'class' => 'evdpl-plugin-fw-deselect-all',
            'data' => array(
                'select-id' => $field['id'],
            ),
        ),
    ),
);

$field = wp_parse_args($field, $default_args);

evdpl_plugin_fw_get_field($field, true);
