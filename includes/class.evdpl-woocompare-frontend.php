<?php
/**
 * Frontend class
 *
 * @author EVDPL
 * @package Products Compare for WC
 * @version 1.0.0
 */
defined('EVDPL_WOOCOMPARE') || exit; // Exit if accessed directly.

if (!class_exists('EVDPL_Woocompare_Frontend')) {

    /**
     * EVDPL Custom Login Frontend
     */
    class EVDPL_Woocompare_Frontend {

        /**
         * Plugin version
         */
        public $version = EVDPL_WOOCOMPARE_VERSION;

        /**
         * The list of products inside the comparison table
         */
        public $products_list = array();

        /**
         * Plugin version
         * @since 1.0.0
         * @var string
         */
        public $template_file = 'compare.php';

        /**
         * Stylesheet file
         * @since 1.0.0
         * @var string
         */
        public $stylesheet_file = 'compare.css';

        /**
         * The name of cookie name
         * @since 1.0.0
         * @var string
         */
        public $cookie_name = 'evdpl_woocompare_list';

        /**
         * The action used to view the table comparison
         * @since 1.0.0
         * @var string
         */
        public $action_view = 'evdpl-woocompare-view-table';

        /**
         * The action used to add the product to compare list
         * @since 1.0.0
         * @var string
         */
        public $action_add = 'evdpl-woocompare-add-product';

        /**
         * The action used to add the product to compare list
         * @since 1.0.0
         * @var string
         */
        public $action_remove = 'evdpl-woocompare-remove-product';

        /**
         * The action used to reload the compare list widget
         * @since 1.0.0
         * @var string
         */
        public $action_reload = 'evdpl-woocompare-reload-product';

        /**
         * The standard fields
         * @since 1.0.0
         * @var array
         */
        public $default_fields = array();

        /**
         * Constructor
         * @since 1.0.0
         * @return EVDPL_Woocompare_Frontend
         */
        public function __construct() {

            add_action('init', array($this, 'evdpl_init_variables'), 1);
            add_action('init', array($this, 'evdpl_populate_products_list'), 10);

            #Add link or button in the products list.
            if ('yes' === esc_attr(get_option('evdpl_woocompare_compare_button_in_product_page', 'yes'))) {
                add_action('woocommerce_single_product_summary', array($this, 'evdpl_add_compare_link'), 35);
            }
            if ('yes' === esc_attr(get_option('evdpl_woocompare_compare_button_in_products_list', 'no'))) {
                add_action('woocommerce_after_shop_loop_item', array($this, 'evdpl_add_compare_link'), 20);
            }
            add_action('init', array($this, 'evdpl_add_product_to_compare_action'), 15);
            add_action('init', array($this, 'evdpl_remove_product_from_compare_action'), 15);
            add_action('wp_enqueue_scripts', array($this, 'evdpl_enqueue_scripts'));
            add_action('template_redirect', array($this, 'evdpl_compare_table_html'));
            add_action('init', array($this, 'evdpl_register_iframe_scripts'), 15);

            #Add the shortcode.
            add_shortcode('evdpl_compare_button', array($this, 'evdpl_compare_button_sc'));

            #AJAX.
            add_action('wc_ajax_' . $this->action_add, array($this, 'evdpl_add_product_to_compare_ajax'));
            add_action('wc_ajax_' . $this->action_remove, array($this, 'evdpl_remove_product_from_compare_ajax'));
            add_action('wc_ajax_' . $this->action_reload, array($this, 'evdpl_reload_widget_list_ajax'));

            #AJAX no priv.
            add_action('wp_ajax_nopriv_' . $this->action_add, array($this, 'evdpl_add_product_to_compare_ajax'));
            add_action('wp_ajax_nopriv_' . $this->action_remove, array($this, 'evdpl_remove_product_from_compare_ajax'));
            add_action('wp_ajax_nopriv_' . $this->action_reload, array($this, 'evdpl_reload_widget_list_ajax'));

            return $this;
        }

        /**
         * Init class variables
         */
        public function evdpl_init_variables() {
            global $sitepress;

            $lang = isset($_REQUEST['lang']) ? sanitize_text_field(wp_unslash($_REQUEST['lang'])) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

            if (defined('ICL_LANGUAGE_CODE') && $lang && isset($sitepress)) {
                $sitepress->switch_lang($lang, true);
            }

            #Set coookiename.
            if (is_multisite()) {
                $this->cookie_name .= '_' . get_current_blog_id();
            }

            #Populate default fields for the comparison table.
            $this->default_fields = EVDPL_Woocompare_Helper::evdpl_standard_fields();
        }

        /**
         * Populate the compare product list
         */
        public function evdpl_populate_products_list() {

            global $sitepress;

            #WPML Support.
            $lang = isset($_REQUEST['lang']) ? sanitize_text_field(wp_unslash($_REQUEST['lang'])) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            // Get cookie val.
            $the_list = isset($_COOKIE[$this->cookie_name]) ? json_decode(sanitize_text_field(wp_unslash($_COOKIE[$this->cookie_name]))) : array();

            #Switch lang for WPML.
            if (defined('ICL_LANGUAGE_CODE') && $lang && isset($sitepress)) {
                $sitepress->switch_lang($lang, true);
            }

            foreach ($the_list as $product_id) {
                if (function_exists('wpml_object_id_filter')) {
                    $product_id_translated = wpml_object_id_filter($product_id, 'product', false);
                    #Get all product of current lang.
                    if ($product_id_translated !== $product_id) {
                        continue;
                    }
                }

                #Check for deleted|private products.
                $product = wc_get_product($product_id);
                if (!$product || 'publish' !== $product->get_status()) {
                    continue;
                }

                $this->products_list[] = absint($product_id);
            }

            do_action('evdpl_woocompare_after_populate_product_list', $this->products_list);
        }

        /**
         * Enqueue the scripts and styles in the page
         */
        public function evdpl_enqueue_scripts() {

            wp_register_script('evdpl-woocompare-main', EVDPL_WOOCOMPARE_ASSETS_URL . '/js/woocompare.js', array('jquery'), EVDPL_WOOCOMPARE_VERSION, true);
            #Enqueue and add localize.
            wp_enqueue_script('evdpl-woocompare-main');

            #Localize script args.
            $args = apply_filters(
                    'evdpl_woocompare_main_script_localize_array',
                    array(
                        'ajaxurl' => WC_AJAX::get_endpoint('%%endpoint%%'),
                        'actionadd' => $this->action_add,
                        'actionremove' => $this->action_remove,
                        'actionview' => $this->action_view,
                        'actionreload' => $this->action_reload,
                        'added_label' => apply_filters('evdpl_woocompare_compare_added_label', __('Added', 'evdpl-woocommerce-compare')),
                        'table_title' => apply_filters('evdpl_woocompare_compare_table_title', __('Product Comparison', 'evdpl-woocommerce-compare')),
                        'auto_open' => esc_attr(get_option('evdpl_woocompare_auto_open', 'yes')),
                        'loader' => EVDPL_WOOCOMPARE_ASSETS_URL . '/images/loader.gif',
                        'button_text' => esc_attr(get_option('evdpl_woocompare_button_text', __('Compare', 'evdpl-woocommerce-compare'))),
                        'cookie_name' => $this->cookie_name,
                        'close_label' => _x('Close', 'Label for popup close icon', 'evdpl-woocommerce-compare'),
                    )
            );

            wp_localize_script('evdpl-woocompare-main', 'evdpl_woocompare', $args);

            #Colorbox.
            wp_register_style('jquery-colorbox', EVDPL_WOOCOMPARE_ASSETS_URL . '/css/colorbox.css', array(), '1.4.21');
            wp_enqueue_style('jquery-colorbox', EVDPL_WOOCOMPARE_ASSETS_URL . '/css/colorbox.css', array(), '1.4.21');
            wp_enqueue_script('jquery-colorbox', EVDPL_WOOCOMPARE_ASSETS_URL . '/js/jquery.colorbox-min.js', array('jquery'), '1.4.21', true);
            
            wp_register_style('evdpl-dataTables', EVDPL_WOOCOMPARE_ASSETS_URL . '/css/jquery.dataTables.css');
            #Enqueue and add localize.
            wp_enqueue_style('evdpl-dataTables');

            #Widget.
            if (is_active_widget(false, false, 'evdpl-woocompare-widget', true) && !is_admin()) {
                wp_enqueue_style('evdpl-woocompare-widget', EVDPL_WOOCOMPARE_ASSETS_URL . '/css/widget.css', array(), EVDPL_WOOCOMPARE_VERSION);
            }
        }
        
        function evdpl_register_iframe_scripts(){
            wp_register_style( 'Open-Sans-Style','//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800"/');
        }

        /**
         * The fields to show in the table
         * @since 1.0.0
         * @param array $products An array of products.
         * @return mixed|void
         */
        public function fields($products = array()) {

            $fields = get_option('evdpl_woocompare_fields', array());

            foreach ($fields as $field => $show) {
                if ($show) {
                    if (isset($this->default_fields[$field])) {
                        $fields[$field] = $this->default_fields[$field];
                    } else {
                        if (taxonomy_exists($field)) {
                            $fields[$field] = wc_attribute_label($field);
                        }
                    }
                } else {
                    unset($fields[$field]);
                }
            }

            return apply_filters('evdpl_woocompare_filter_table_fields', $fields, $products);
        }

        /**
         * Render the compare table
         */
        public function evdpl_compare_table_html() {
            global $woocommerce, $sitepress;

            if ((!defined('DOING_AJAX') || !DOING_AJAX ) && (!isset($_REQUEST['action']) || sanitize_text_field(wp_unslash($_REQUEST['action'])) !== $this->action_view )) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                return;
            }

            #Check if is add to cart.
            if (isset($_REQUEST['add-to-cart'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $product_id = absint(sanitize_text_field($_REQUEST['add-to-cart'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                wp_safe_redirect(get_permalink($product_id));
                exit;
            }

            #WPML Support: Localize Ajax Call.
            $lang = isset($_REQUEST['lang']) ? sanitize_text_field(wp_unslash($_REQUEST['lang'])) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if (defined('ICL_LANGUAGE_CODE') && $lang && isset($sitepress)) {
                $sitepress->switch_lang($lang, true);
            }

            $args = $this->vars();
            $args['fixed'] = false;
            $args['iframe'] = 'yes';

            #Extract args.
            extract($args); // phpcs:ignore
            #Remove all styles from compare template.
            add_action('wp_print_styles', array($this, 'evdpl_remove_all_styles'), 100);

            #Remove admin bar.
            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
            remove_action('wp_head', '_admin_bar_bump_cb');

            #Remove filters before render compare popup.
            add_action('wp_enqueue_scripts', array($this, 'actions_before_load_popup'), 99);

            $plugin_path = EVDPL_WOOCOMPARE_TEMPLATE_PATH . '/' . $this->template_file;

            if (defined('WC_TEMPLATE_PATH')) {
                $template_path = get_template_directory() . '/' . WC_TEMPLATE_PATH . $this->template_file;
                $child_path = get_stylesheet_directory() . '/' . WC_TEMPLATE_PATH . $this->template_file;
            } else {
                $template_path = get_template_directory() . '/' . $woocommerce->template_url . $this->template_file;
                $child_path = get_stylesheet_directory() . '/' . $woocommerce->template_url . $this->template_file;
            }

            foreach (array('child_path', 'template_path', 'plugin_path') as $var) {
                if (file_exists(${$var})) {
                    include ${$var};
                    exit();
                }
            }
        }

        
        /**
         * Return the array with all products and all attributes values
         */
        public function evdpl_get_products_list($products = array()) {
            $list = array();

            if (empty($products)) {
                $products = $this->products_list;
            }

            $products = apply_filters('evdpl_woocompare_exclude_products_from_list', $products);
            $fields = $this->fields($products);

            foreach ($products as $product_id) {

                $product = $this->wc_get_product($product_id);
                if (!$product) {
                    continue;
                }

                $product->fields = array();

                #Custom attributes.
                foreach ($fields as $field => $name) {

                    switch ($field) {
                        case 'title':
                            $product->fields[$field] = $product->get_title();
                            break;
                        case 'price':
                            $product->fields[$field] = $product->get_price_html();
                            break;
                        case 'image':
                            $product->fields[$field] = absint($product->get_image_id());
                            break;
                        case 'description':
                            $description = apply_filters('woocommerce_short_description', $product->get_short_description());
                            $product->fields[$field] = apply_filters('evdpl_woocompare_products_description', $description);
                            break;
                        case 'stock':
                            $availability = $product->get_availability();
                            if (empty($availability['availability'])) {
                                $availability['availability'] = __('In stock', 'evdpl-woocommerce-compare');
                            }
                            $product->fields[$field] = sprintf('<span>%s</span>', esc_html($availability['availability']));
                            break;
                        case 'sku':
                            $sku = $product->get_sku();
                            if (!$sku) {
                                $sku = '-';
                            }
                            $product->fields[$field] = $sku;
                            break;
                        case 'weight':
                            $weight = $product->get_weight();
                            $weight = $weight ? ( wc_format_localized_decimal($weight) . ' ' . esc_attr(get_option('woocommerce_weight_unit')) ) : '-';

                            $product->fields[$field] = sprintf('<span>%s</span>', esc_html($weight));
                            break;
                        case 'dimensions':
                            $dimensions = function_exists('wc_format_dimensions') ? wc_format_dimensions($product->get_dimensions(false)) : $product->get_dimensions();
                            if (!$dimensions) {
                                $dimensions = '-';
                            }

                            $product->fields[$field] = sprintf('<span>%s</span>', esc_html($dimensions));
                            break;
                        default:
                            if (taxonomy_exists($field)) {
                                $product->fields[$field] = array();
                                $terms = get_the_terms($product_id, $field);
                                if (!empty($terms)) {
                                    foreach ($terms as $term) {
                                        $term = sanitize_term($term, $field);
                                        $product->fields[$field][] = $term->name;
                                    }
                                }
                                $product->fields[$field] = implode(', ', $product->fields[$field]);
                            } else {
                                do_action_ref_array('evdpl_woocompare_field_' . $field, array($product, &$product->fields));
                            }
                            break;
                    }
                }

                $list[$product_id] = $product;
            }

            return $list;
        }

        /**
         * The URL of product comparison table
         */
        public function evdpl_view_table_url($product_id = false) {
            $url_args = array(
                'action' => $this->action_view,
                'iframe' => 'yes',
            );

            $lang = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : false;
            if ($lang) {
                $url_args['lang'] = $lang;
            }

            return apply_filters('evdpl_woocompare_view_table_url', esc_url_raw(add_query_arg($url_args, remove_query_arg('wc-ajax'))), $product_id);
        }

        /**
         * The URL to add the product into the comparison table
         */
        public function evdpl_add_product_url($product_id) {
            $url_args = array(
                'action' => $this->action_add,
                'id' => $product_id,
            );

            $lang = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : false;
            if ($lang) {
                $url_args['lang'] = $lang;
            }

            return apply_filters('evdpl_woocompare_add_product_url', esc_url_raw(add_query_arg($url_args, site_url())), $this->action_add);
        }

        /**
         * The URL to remove the product into the comparison table
         */
        public function evdpl_remove_product_url($product_id) {
            $url_args = array(
                'action' => $this->action_remove,
                'id' => $product_id,
            );

            return apply_filters('evdpl_woocompare_remove_product_url', esc_url_raw(add_query_arg($url_args, site_url())), $this->action_remove);
        }

        /**
         *  Add the link to compare
         */
        public function evdpl_add_compare_link($product_id = false, $args = array()) {
            extract($args); // phpcs:ignore
            global $evdpl_woocompare;

            if (!$product_id) {
                global $product;
                $product_id = !is_null($product) ? $product->get_id() : 0;
            }

            #Return if product doesn't exist.
            if (empty($product_id) || apply_filters('evdpl_woocompare_remove_compare_link_by_cat', false, $product_id)) {
                return;
            }

            $is_button = !isset($button_or_link) || !$button_or_link ? esc_attr(get_option('evdpl_woocompare_is_button', 'button')) : $button_or_link;

            $the_list = isset($_COOKIE[$this->cookie_name]) ? json_decode(sanitize_text_field(wp_unslash($_COOKIE[$this->cookie_name]))) : array();

            if (!isset($button_text) || 'default' === $button_text) {
                $button_text = esc_attr(get_option('evdpl_woocompare_button_text', __('Compare', 'evdpl-woocommerce-compare')));
                do_action('wpml_register_single_string', 'Plugins', 'plugin_evdpl_compare_button_text', $button_text);
                $button_text = apply_filters('wpml_translate_single_string', $button_text, 'Plugins', 'plugin_evdpl_compare_button_text');
            }

            if (in_array($product_id, $the_list)) {
                echo wp_kses_post("<p class='compare-message' id='" . $product_id . "'>" . __("Product already added in the comparison list.") . "</p>");
                printf('<a href="%s" class="compare added button" data-product_id="%d" rel="nofollow">%s</a>', esc_url($evdpl_woocompare->obj->evdpl_view_table_url()), $product_id, apply_filters('evdpl_woocompare_compare_added_label', __('Added', 'evdpl-woocommerce-compare')));
            } else {
                printf('<a href="%s" class="%s" data-product_id="%d" rel="nofollow">%s</a>', $this->evdpl_add_product_url($product_id), 'compare' . ( 'button' === $is_button ? ' button' : '' ), $product_id, esc_attr($button_text));
            }
        }

        /**
         * Return the url of stylesheet position
         */
        public function evdpl_stylesheet_url() {
            global $woocommerce;

            $filename = $this->stylesheet_file;

            $plugin_path = array(
                'path' => EVDPL_WOOCOMPARE_DIR . '/assets/css/style.css',
                'url' => EVDPL_WOOCOMPARE_ASSETS_URL . '/css/style.css',
            );

            if (defined('WC_TEMPLATE_PATH')) {
                $template_path = array(
                    'path' => get_template_directory() . '/' . WC_TEMPLATE_PATH . $filename,
                    'url' => get_template_directory_uri() . '/' . WC_TEMPLATE_PATH . $filename,
                );
                $child_path = array(
                    'path' => get_stylesheet_directory() . '/' . WC_TEMPLATE_PATH . $filename,
                    'url' => get_stylesheet_directory_uri() . '/' . WC_TEMPLATE_PATH . $filename,
                );
            } else {
                $template_path = array(
                    'path' => get_template_directory() . '/' . $woocommerce->template_url . $filename,
                    'url' => get_template_directory_uri() . '/' . $woocommerce->template_url . $filename,
                );
                $child_path = array(
                    'path' => get_stylesheet_directory() . '/' . $woocommerce->template_url . $filename,
                    'url' => get_stylesheet_directory_uri() . '/' . $woocommerce->template_url . $filename,
                );
            }

            foreach (array('child_path', 'template_path', 'plugin_path') as $var) {
                if (file_exists(${$var}['path'])) {
                    return ${$var}['url'];
                }
            }
        }

        /**
         * Generate template vars
         */
        protected function vars() {
            $vars = array(
                'products' => $this->evdpl_get_products_list(),
                'fields' => $this->fields(),
                'repeat_price' => esc_attr(get_option('evdpl_woocompare_price_end', 'yes')),
                'repeat_add_to_cart' => esc_attr(get_option('evdpl_woocompare_add_to_cart_end', 'no')),
            );

            return $vars;
        }

        /**
         * The action called by the query string
         */
        public function evdpl_add_product_to_compare_action() {
            if (defined('DOING_AJAX') && DOING_AJAX || !isset($_REQUEST['id']) || !isset($_REQUEST['action']) || sanitize_text_field(wp_unslash($_REQUEST['action'])) !== $this->action_add) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                return;
            }

            $product_id = absint($_REQUEST['id']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $product = wc_get_product($product_id);
            #Don't add the product if doesn't exist.
            if ($product && !in_array($product_id, $this->products_list, true)) {
                $this->evdpl_add_product_to_compare($product_id);
            }

            wp_safe_redirect(esc_url(remove_query_arg(array('id', 'action'))));
            exit();
        }

        /**
         * The action called by AJAX
         */
        public function evdpl_add_product_to_compare_ajax() {

            if (!isset($_REQUEST['id']) || !isset($_REQUEST['action']) || sanitize_text_field(wp_unslash($_REQUEST['action'])) !== $this->action_add) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                die();
            }

            $product_id = absint(sanitize_text_field($_REQUEST['id'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $product = wc_get_product($product_id);
            #Don't add the product if doesn't exist.
            if ($product && !in_array($product_id, $this->products_list, true)) {
                $this->evdpl_add_product_to_compare($product_id);
            }

            do_action('evdpl_woocompare_add_product_action_ajax');

            $json = apply_filters(
                    'evdpl_woocompare_add_product_action_json',
                    array(
                        'table_url' => $this->evdpl_view_table_url($product_id),
                        'widget_table' => $this->evdpl_list_products_html(),
                    )
            );

            echo wp_json_encode($json);
            die();
        }

        /**
         * Add a product in the products comparison table
         */
        public function evdpl_add_product_to_compare($product_id) {

            $this->products_list[] = absint($product_id);
            setcookie($this->cookie_name, wp_json_encode($this->products_list), 0, COOKIEPATH, COOKIE_DOMAIN, false, false);

            do_action('evdpl_woocompare_after_add_product', $product_id);
        }

        /**
         * The action called by the query string
         */
        public function evdpl_remove_product_from_compare_action() {
            if (defined('DOING_AJAX') && DOING_AJAX || !isset($_REQUEST['id']) || !isset($_REQUEST['action']) || sanitize_text_field(wp_unslash($_REQUEST['action'])) !== $this->action_remove) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                return;
            }

            $this->evdpl_remove_product_from_compare(sanitize_text_field(wp_unslash($_REQUEST['id']))); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            #Redirect.
            $redirect = esc_url(remove_query_arg(array('id', 'action')));

            if (isset($_REQUEST['redirect']) && 'view' === sanitize_text_field(wp_unslash($_REQUEST['redirect']))) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $redirect = esc_url(remove_query_arg('redirect', add_query_arg('action', $this->action_view, $redirect)));
            }

            wp_safe_redirect($redirect);
            exit();
        }

        /**
         * The action called by AJAX
         */
        public function evdpl_remove_product_from_compare_ajax() {

            if (!isset($_REQUEST['id']) || !isset($_REQUEST['action']) || sanitize_text_field(wp_unslash($_REQUEST['action'])) !== $this->action_remove) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                die();
            }

            $lang = isset($_REQUEST['lang']) ? sanitize_text_field(wp_unslash($_REQUEST['lang'])) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

            $this->evdpl_remove_product_from_compare(sanitize_text_field(wp_unslash($_REQUEST['id']))); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

            do_action('evdpl_woocompare_remove_product_action_ajax');

            header('Content-Type: text/html; charset=utf-8');

            if (isset($_REQUEST['responseType']) && 'product_list' === sanitize_text_field(wp_unslash($_REQUEST['responseType']))) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                echo wp_kses_post($this->evdpl_list_products_html($lang));
            } else {
                $this->evdpl_compare_table_html();
            }

            die();
        }

        /**
         * Return the list of widget table, used in AJAX
         */
        public function evdpl_reload_widget_list_ajax() {

            if (!isset($_REQUEST['action']) || sanitize_text_field(wp_unslash($_REQUEST['action'])) !== $this->action_reload) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                die();
            }

            $lang = isset($_REQUEST['lang']) ? sanitize_text_field(wp_unslash($_REQUEST['lang'])) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

            echo wp_kses_post($this->evdpl_list_products_html($lang));
            die();
        }

        /**
         * The list of products as HTML list
         */
        public function evdpl_list_products_html($lang = false) {
            ob_start();

            global $sitepress;

            if (defined('ICL_LANGUAGE_CODE') && $lang && isset($sitepress)) {
                $sitepress->switch_lang($lang, true);
            }

            if (empty($this->products_list)) {
                echo '<li class="list_empty">' . esc_html__('No products to compare', 'evdpl-woocommerce-compare') . '</li>';

                return ob_get_clean();
            }

            foreach ($this->products_list as $product_id) {
                $product = $this->wc_get_product($product_id);
                if (!$product) {
                    continue;
                }
                ?>
                <li>
                    <a href="<?php echo esc_attr($this->evdpl_remove_product_url($product_id)); ?>" data-product_id="<?php echo esc_attr($product_id); ?>" class="remove" title="<?php esc_html_e('Remove', 'evdpl-woocommerce-compare'); ?>">x</a>
                    <a class="title" href="<?php echo esc_attr(get_permalink($product_id)); ?>"><?php echo esc_html($product->get_title()); ?></a>
                </li>
                <?php
            }

            $return = ob_get_clean();

            return apply_filters('evdpl_woocompare_widget_products_html', $return, $this->products_list, $this);
        }

        /**
         * Remove a product from the comparison table
         */
        public function evdpl_remove_product_from_compare($product_id) {

            if ('all' === $product_id) {
                $this->products_list = array();
            } else {
                foreach ($this->products_list as $k => $id) {
                    if (absint($product_id) === absint($id)) {
                        unset($this->products_list[$k]);
                    }
                }
            }

            setcookie($this->cookie_name, wp_json_encode(array_values($this->products_list)), 0, COOKIEPATH, COOKIE_DOMAIN, false, false);

            do_action('evdpl_woocompare_after_remove_product', $product_id);
        }

        /**
         * Remove all styles from the compare
         */
        public function evdpl_remove_all_styles() {
            global $wp_styles;

            $wp_styles->queue = array_filter(
                    $wp_styles->queue,
                    function ($v) {
                        return strpos($v, 'evdpl-proteo') !== false;
                    }
            );
            
            wp_enqueue_style( 'Open-Sans-Style','//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800"/');
            wp_enqueue_style('jquery-colorbox');
            wp_enqueue_style('jquery-dataTables',esc_url(EVDPL_WOOCOMPARE_URL).'assets/css/jquery.dataTables.css');
            wp_enqueue_style('compare-iframe',esc_url($this->evdpl_stylesheet_url()));
        }

        /**
         * Show the html for the shortcode
         */
        public function evdpl_compare_button_sc($atts, $content = null) {
            $atts = shortcode_atts(
                    array(
                        'product' => false,
                        'type' => 'default',
                        'container' => 'yes',
                    ),
                    $atts
            );

            $product_id = 0;

            /**
             * Retrieve the product ID in these steps:
             * - If "product" attribute is not set, get the product ID of current product loop
             * - If "product" contains ID, post slug or post title
             */
            if (!$atts['product']) {
                global $product;
                $product_id = $product->get_id();
            } else {
                global $wpdb;
                $product = $wpdb->get_row($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE ID = %d OR post_name = %s OR post_title = %s LIMIT 1", esc_attr($atts['product']), esc_attr($atts['product']), esc_attr($atts['product']))); // phpcs:ignore
                if (!empty($product)) {
                    $product_id = $product->ID;
                }
            }

            #Make sure to get always the product id of current language.
            if (function_exists('wpml_object_id_filter')) {
                $product_id = wpml_object_id_filter($product_id, 'product', false);
            }

            #If product ID is 0, maybe the product doesn't exists or is wrong.. in this case, doesn't show the button.
            if (empty($product_id)) {
                return '';
            }

            ob_start();
            if ('yes' === esc_attr($atts['container'])) {
                echo '<div class="woocommerce product compare-button">';
            }
            $this->evdpl_add_compare_link(
                    $product_id,
                    array(
                        'button_or_link' => ( 'default' === esc_attr($atts['type']) ? false : esc_attr($atts['type']) ),
                        'button_text' => empty($content) ? 'default' : $content,
                    )
            );
            if ('yes' === esc_attr($atts['container'])) {
                echo '</div>';
            }

            return ob_get_clean();
        }

        /**
         * Wrap for wc_get_product
         * @param integer $product_id The product ID.
         * @return mixed
         * @depreacted
         */
        public function wc_get_product($product_id) {
            $wc_get_product = function_exists('wc_get_product') ? 'wc_get_product' : 'get_product';

            return $wc_get_product($product_id);
        }

        /**
         * Do action before loads compare popup
         */
        public function actions_before_load_popup() {
            #Removes WooCommerce Product Filter scripts.
            wp_dequeue_script('prdctfltr-main-js');
            wp_dequeue_script('prdctfltr-history');
            wp_dequeue_script('prdctfltr-ionrange-js');
            wp_dequeue_script('prdctfltr-isotope-js');
            wp_dequeue_script('prdctfltr-scrollbar-js');
        }

    }

}
