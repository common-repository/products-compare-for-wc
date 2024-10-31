<?php
/**
 * The Template for displaying the WooCommerce option row.
 *
 * @var array  $field       The field.
 * @var string $description The description.
 * @package    EVDPL\PluginFramework\Templates
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

$default_field = array(
    'id' => '',
    'title' => isset($field['name']) ? $field['name'] : '',
    'desc' => '',
);
$field = wp_parse_args($field, $default_field);
$extra_row_class = isset($field['extra_row_class']) ? $field['extra_row_class'] : '';

$display_row = !in_array($field['type'], array('hidden', 'html', 'sep', 'simple-text', 'title', 'list-table'), true);
$display_row = isset($field['evdpl-display-row']) ? !!$field['evdpl-display-row'] : $display_row;
$is_required = !empty($field['required']);

$extra_row_classes = $is_required ? array('evdpl-plugin-fw--required') : array();
$extra_row_classes = (array) apply_filters('evdpl_plugin_fw_panel_wc_extra_row_classes', $extra_row_classes, $field);

$row_classes = array('evdpl-plugin-fw-panel-wc-row', $field['type']);
$row_classes = array_merge($row_classes, $extra_row_classes, array($extra_row_class));
$row_classes = implode(' ', $row_classes);
?>
<tr valign="top" class="<?php echo esc_attr($row_classes); ?>" <?php echo evdpl_field_deps_data($field); ?>>
    <?php if ($display_row) : ?>
        <th scope="row" class="titledesc">
            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo wp_kses_post($field['title']); ?></label>
        </th>
        <td class="forminp forminp-<?php echo esc_attr($field['type']); ?>">
            <?php evdpl_plugin_fw_get_field($field, true); ?>
            <?php echo '<span class="description">' . wp_kses_post($field['desc']) . '</span>'; ?>
        </td>
    <?php else : ?>
        <td colspan="2">
            <?php evdpl_plugin_fw_get_field($field, true); ?>
        </td>
    <?php endif; ?>
</tr>
