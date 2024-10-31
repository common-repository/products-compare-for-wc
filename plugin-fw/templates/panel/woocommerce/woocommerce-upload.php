<?php
/**
 * The Template for displaying WooCommerce upload.
 *
 * @var EVDPL_Plugin_Panel_WooCommerce $this   The EVDPL WooCommerce Panel.
 * @var string                       $id     The option ID.
 * @var string                       $name   The name.
 * @var array                        $option The option array.
 * @var string                       $value  The value.
 * @var string                       $desc   The description.
 * 
 * @package    EVDPL\PluginFramework\Templates
 */
defined('ABSPATH') || exit; // Exit if accessed directly.

$hidden_val = get_option($id . '-evdpl-attachment-id', 0);
?>

<tr valign="top">
    <th scope="row" class="image_upload">
        <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($name); ?></label>
    </th>
    <td class="forminp forminp-color plugin-option">
        <div id="<?php echo esc_attr($id); ?>-container" class="evdpl_options rm_option rm_input rm_text rm_upload"
        <?php if (isset($option['deps'])) : ?>
                 data-field="<?php echo esc_attr($id); ?>"
                 data-dep="<?php echo esc_attr($this->get_id_field($option['deps']['ids'])); ?>"
                 data-value="<?php echo esc_attr($option['deps']['values']); ?>"
             <?php endif ?>
             >
            <div class="option">
                <input type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>"
                       value="<?php echo in_array($value, array('1', 1), true) ? '' : esc_attr($value); ?>" class="evdpl-plugin-fw-upload-img-url"/>
                <input type="hidden" name="<?php echo esc_attr($id); ?>-evdpl-attachment-id" id="<?php echo esc_attr($id); ?>-evdpl-attachment-id" value="<?php echo esc_attr($hidden_val); ?>"/>
                <input type="button" value="<?php esc_attr_e('Upload', 'evdpl-plugin-fw'); ?>" id="<?php echo esc_attr($id); ?>-button"
                       class="evdpl-plugin-fw-upload-button"/>
            </div>
            <div class="clear"></div>
            <span class="description"><?php echo wp_kses_post($desc); ?></span>

            <div class="evdpl-plugin-fw-upload-img-preview" style="margin-top:10px;">
                <?php
                $file = $value;
                if (preg_match('/(jpg|jpeg|png|gif|ico)$/', $file)) {
                    $file_url = EVDPL_CORE_PLUGIN_URL . 'assets/images/sleep.png';
                    echo '<img src="' . esc_url($file_url) . '" data-src="' . esc_attr($file) . '" />';
                }
                ?>
            </div>
        </div>
    </td>
</tr>
