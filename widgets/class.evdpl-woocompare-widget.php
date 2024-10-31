<?php
/**
 * Main class
 *
 * @author EVDPL
 * @package EVDPL WooCommerce Ajax Navigation
 * @version 1.0.0
 */

if ( !defined( 'EVDPL_WOOCOMPARE' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'EVDPL_WOOCOMPARE' ) ) {
    /**
     * EVDPL WooCommerce Ajax Navigation Widget
     *
     * @since 1.0.0
     */
    class EVDPL_Woocompare_Widget extends WP_Widget {

        function __construct() {
            $widget_ops = array (
	            'classname' => 'evdpl-woocompare-widget',
	            'description' => __( 'The widget shows the list of products added in the comparison table.', 'evdpl-woocommerce-compare'
	            )
            );

	        parent::__construct( 'evdpl-woocompare-widget', __( 'Products Compare for WC Widget', 'evdpl-woocommerce-compare' ), $widget_ops );
        }


        function widget( $args, $instance ) {
            global $evdpl_woocompare;

            /**
             * WPML Support
             */
            $lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : false;

            extract( $args );

            do_action ( 'wpml_register_single_string', 'Widget', 'widget_evdpl_compare_title_text', $instance['title'] );
            $localized_widget_title = apply_filters ( 'wpml_translate_single_string', $instance['title'], 'Widget', 'widget_evdpl_compare_title_text' );

            echo wp_kses_post($before_widget . $before_title . $localized_widget_title . $after_title); ?>

            <ul class="products-list" data-lang="<?php echo esc_attr($lang) ?>">
                <?php echo wp_kses_post($evdpl_woocompare->obj->evdpl_list_products_html()); ?>
            </ul>

            <a href="<?php echo esc_url($evdpl_woocompare->obj->evdpl_remove_product_url('all')) ?>" data-product_id="all" class="clear-all" rel="nofollow"><?php _e( 'Clear all', 'evdpl-woocommerce-compare' ) ?></a>
            <a href="<?php echo esc_url($evdpl_woocompare->obj->evdpl_view_table_url()); ?>" class="compare added button" rel="nofollow"><?php _e( 'Compare', 'evdpl-woocommerce-compare' ) ?></a>

            <?php echo wp_kses_post($after_widget);
        }


        function form( $instance ) {
            global $woocommerce;

            $defaults = array(
                'title' => '',
            );

            $instance = wp_parse_args( (array) $instance, $defaults ); ?>

            <p>
                <label>
                    <?php _e( 'Title', 'evdpl-woocommerce-compare' ) ?>:<br />
                    <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
                </label>
            </p>
        <?php
        }

        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;

            $instance['title'] = strip_tags( $new_instance['title'] );

            return $instance;
        }

    }
}