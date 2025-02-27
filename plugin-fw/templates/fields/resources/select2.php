<?php
/**
 * The Template for displaying the select2 field.
 *
 * @var array  $args              Array of arguments.
 * @var string $custom_attributes Custom attributes.
 * @package EVDPL\PluginFramework\Templates\Fields\Resources
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<select
    id="<?php echo esc_attr($args['id']); ?>"
    class="<?php echo esc_attr($args['class']); ?>"
    name="<?php echo esc_attr($args['name']); ?>"
    data-placeholder="<?php echo esc_attr($args['data-placeholder']); ?>"
    data-allow_clear="<?php echo esc_attr($args['data-allow_clear']); ?>"
<?php echo!empty($args['data-action']) ? 'data-action="' . esc_attr($args['data-action']) . '"' : ''; ?>
    <?php echo!empty($args['data-multiple']) ? 'multiple="multiple"' : ''; ?>
    style="<?php echo esc_attr($args['style']); ?>"
    <?php echo $custom_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    >
    <?php if (!empty($args['value'])) : ?>
            <?php
            $values = $args['value'];

            if (!is_array($values)) {
                $values = explode(',', $values);
            }
            ?>
            <?php foreach ($values as $value) : ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected(true, true, true); ?> >
                <?php echo esc_html($args['data-selected'][$value]); ?>
            </option>
            <?php endforeach; ?>
    <?php endif; ?>
</select>
