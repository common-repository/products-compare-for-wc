<?php

/**
 * Main settings array
 *
 * @author EVDPL
 * @package Products Compare for WC
 * @version 1.0.0
 */
defined('EVDPL_WOOCOMPARE') || exit; // Exit if accessed directly.

$options = array(
    'general' => array(
        array(
            'name' => __('General Settings', 'evdpl-woocommerce-compare'),
            'type' => 'title',
            'desc' => '',
            'id' => 'evdpl_woocompare_general',
        ),
        array(
            'name' => __('Link or Button', 'evdpl-woocommerce-compare'),
            'desc_tip' => __('Choose if you want to use a link or a button for the comepare actions.', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_is_button',
            'default' => 'button',
            'type' => 'select',
            'class' => 'wc-enhanced-select',
            'options' => array(
                'link' => __('Link', 'evdpl-woocommerce-compare'),
                'button' => __('Button', 'evdpl-woocommerce-compare'),
            ),
        ),
        array(
            'name' => __('Link/Button text', 'evdpl-woocommerce-compare'),
            'desc' => __('Type the text to use for the button or the link of the compare.', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_button_text',
            'default' => __('Compare', 'evdpl-woocommerce-compare'),
            'type' => 'text',
        ),
        array(
            'name' => __('Show button in single product page', 'evdpl-woocommerce-compare'),
            'desc' => __('Say if you want to show the button in the single product page.', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_compare_button_in_product_page',
            'default' => 'yes',
            'type' => 'checkbox',
        ),
        array(
            'name' => __('Show button in products list', 'evdpl-woocommerce-compare'),
            'desc' => __('Say if you want to show the button in the products list.', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_compare_button_in_products_list',
            'default' => 'no',
            'type' => 'checkbox',
        ),
        array(
            'name' => __('Open automatically lightbox', 'evdpl-woocommerce-compare'),
            'desc' => __('Open link after click into "Compare" button".', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_auto_open',
            'default' => 'yes',
            'type' => 'checkbox',
        ),
        array(
            'type' => 'sectionend',
            'id' => 'evdpl_woocompare_general_end',
        ),
        array(
            'name' => __('Table Settings', 'evdpl-woocommerce-compare'),
            'type' => 'title',
            'desc' => '',
            'id' => 'evdpl_woocompare_table',
        ),
        array(
            'name' => __('Table title', 'evdpl-woocommerce-compare'),
            'desc' => __('Type the text to use for the table title.', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_table_text',
            'default' => __('Compare products', 'evdpl-woocommerce-compare'),
            'type' => 'text',
        ),
        array(
            'name' => __('Fields to show', 'evdpl-woocommerce-compare'),
            'desc' => __('Select the fields to show in the comparison table and order them by drag&drop (are included also the woocommerce attributes)', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_fields_attrs',
            'default' => 'all',
            'type' => 'woocompare_attributes',
        ),
        array(
            'name' => __('Repeat "Price" field', 'evdpl-woocommerce-compare'),
            'desc' => __('Repeat the "Price" field at the end of the table', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_price_end',
            'default' => 'yes',
            'type' => 'checkbox',
        ),
        array(
            'name' => __('Repeat "Add to cart" field', 'evdpl-woocommerce-compare'),
            'desc' => __('Repeat the "Add to cart" field at the end of the table', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_add_to_cart_end',
            'default' => 'no',
            'type' => 'checkbox',
        ),
        array(
            'name' => __('Image size', 'evdpl-woocommerce-compare'),
            'desc' => __('Set the size for the images', 'evdpl-woocommerce-compare'),
            'id' => 'evdpl_woocompare_image_size',
            'type' => 'woocompare_image_width',
            'default' => array(
                'width' => 220,
                'height' => 154,
                'crop' => 1,
            ),
            'std' => array(
                'width' => 220,
                'height' => 154,
                'crop' => 1,
            ),
        ),
        array(
            'type' => 'sectionend',
            'id' => 'evdpl_woocompare_table_end',
        ),
    ),
);

return apply_filters('evdpl_woocompare_general_settings', $options);
