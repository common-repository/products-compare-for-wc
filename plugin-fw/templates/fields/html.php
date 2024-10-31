<?php

/**
 * Template for displaying the html field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

list ( $html ) = evdpl_plugin_fw_extract($field, 'html');

$html = !!$html ? $html : '';

echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
