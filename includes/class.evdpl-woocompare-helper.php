<?php

/**
 * Helper class
 *
 * @author EVDPL
 * @package Products Compare for WC
 * @version 1.0.0
 */
defined('EVDPL_WOOCOMPARE') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Woocompare_Helper')) {

    /**
     * Products Compare for WC helper
     * @since 1.0.0
     */
    class EVDPL_Woocompare_Helper {

        /**
         * Set the image size used in the comparison table
         * @since 1.0.0
         */
        public static function evdpl_set_image_size() {
            $size = get_option('evdpl_woocompare_image_size');
            if (!$size) {
                return;
            }

            add_image_size('evdpl-woocompare-image', esc_attr($size['width']), esc_attr($size['height']), isset($size['crop']));
        }

        /**
         * The list of standard fields
         *
         * @since 1.0.0
         * @access public
         * @param boolean $with_attr If merge attributes taxonomies to fields.
         * @return array
         */
        public static function evdpl_standard_fields($with_attr = true) {

            $fields = array(
                'image' => __('Image', 'evdpl-woocommerce-compare'),
                'title' => __('Title', 'evdpl-woocommerce-compare'),
                'price' => __('Price', 'evdpl-woocommerce-compare'),
                'add-to-cart' => __('Add to cart', 'evdpl-woocommerce-compare'),
                'description' => __('Description', 'evdpl-woocommerce-compare'),
                'sku' => __('Sku', 'evdpl-woocommerce-compare'),
                'stock' => __('Availability', 'evdpl-woocommerce-compare'),
                'weight' => __('Weight', 'evdpl-woocommerce-compare'),
                'dimensions' => __('Dimensions', 'evdpl-woocommerce-compare'),
            );

            if ($with_attr) {
                $fields = array_merge($fields, self::evdpl_attribute_taxonomies());
            }

            return apply_filters('evdpl_woocompare_standard_fields_array', $fields);
        }

        /**
         * Get Woocommerce Attribute Taxonomies
         * @since 1.0.0
         * @access public
         */
        public static function evdpl_attribute_taxonomies() {

            $attributes = array();

            $attribute_taxonomies = wc_get_attribute_taxonomies();
            if (empty($attribute_taxonomies)) {
                return array();
            }
            foreach ($attribute_taxonomies as $attribute) {
                $tax = wc_attribute_taxonomy_name($attribute->attribute_name);
                if (taxonomy_exists($tax)) {
                    $attributes[$tax] = ucfirst($attribute->attribute_name);
                }
            }

            return $attributes;
        }

    }

}
