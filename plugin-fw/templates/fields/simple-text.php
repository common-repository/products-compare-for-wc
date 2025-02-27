<?php
/**
 * Template for displaying the simple-text field
 *
 * @var array $field The field.
 * @package EVDPL\PluginFramework\Templates\Fields
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

list ( $field_id, $class, $desc, $custom_attributes, $data ) = evdpl_plugin_fw_extract( $field, 'id', 'class', 'desc', 'custom_attributes', 'data' );

$field_id = ! empty( $field_id ) ? $field_id : '';
$class    = ! empty( $class ) ? $class : '';
?>
<p id="<?php echo esc_attr( $field_id ); ?>"
		class="<?php echo esc_attr( $class ); ?>"

	<?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php echo isset( $data ) ? evdpl_plugin_fw_html_data_to_string( $data ) : ''; ?>
>
	<?php echo wp_kses_post( $desc ); ?>
</p>
