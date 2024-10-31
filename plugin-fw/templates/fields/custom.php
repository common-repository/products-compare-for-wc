<?php

/**
 * Template for displaying the custom field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

if (isset($field['action'])) {
    do_action($field['action'], $field);
}
