<?php

/**
 * Template for displaying the ajax-products field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

$field['type'] = 'ajax-posts';
$field_data = array(
    'post_type' => 'product',
    'placeholder' => __('Search Product', 'evdpl-plugin-fw'),
    'action' => 'evdpl_plugin_fw_json_search_products',
);
if (isset($field['data'])) {
    $field_data = wp_parse_args($field['data'], $field_data);
}

$field['data'] = $field_data;

evdpl_plugin_fw_get_field($field, true);
