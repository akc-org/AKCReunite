<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.wpgoaltracker.com/
 * @since      1.0.0
 *
 * @package    Wp_Goal_Tracker_Ga
 * @subpackage Wp_Goal_Tracker_Ga/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Goal_Tracker_Ga
 * @subpackage Wp_Goal_Tracker_Ga/public
 * @author     yuvalo <support@wpgoaltracker.com>
 */
class Wp_Goal_Tracker_Ga_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private $gtga_events_queue = array("pending" => array("view_item" => array(), "begin_checkout" => array(), "add_to_cart" => array(), "view_cart" => array(), "purchase" => array()));

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name       The name of the plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function gtga_track_woocommerce_view_item__premium_only()
    {
        global $product;
        $store_name = get_bloginfo('name');

        if (is_a($product, 'WC_Product')) {
            $item_data = array(
                'currency' => get_woocommerce_currency(),
                'value' => $product->get_price(),
                'items' => array(
                    array(
                        'item_id' => $product->get_sku() ? $product->get_sku() : $product->get_id(),
                        'item_name' => $product->get_name(),
                        'price' => $product->get_price(),
                        'quantity' => 1,
                        'affiliation' => $store_name,
                    ),
                ),
            );

            // Add discount if the product is on sale
            if ($product->is_on_sale()) {
                $item_data[ 'items' ][ 0 ][ 'discount' ] = $product->get_regular_price() - $product->get_sale_price();
            }

            echo "<script>
										window.wpGoalTrackerGaEvents['pending']['view_item'] = " . json_encode($item_data) . ";
									</script>";
        }
    }

    public function gtga_send_ga_add_to_cart_event__premium_only($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {
        $product = wc_get_product($product_id);

        if (!$product) {
            return;
        }

        $item_data = array(
            'item_id' => $product->get_sku() ? $product->get_sku() : $product_id, // Use SKU if available, otherwise product ID
            'item_name' => $product->get_name(),
            'currency' => get_woocommerce_currency(),
            'value' => $product->get_price(),
            'items' => array(
                array(
                    'item_id' => $product->get_sku() ? $product->get_sku() : $product_id,
                    'item_name' => $product->get_name(),
                    'price' => $product->get_price(),
                    'quantity' => $quantity,
                    'affiliation' => get_bloginfo('name'), // Using the site name as affiliation
                ),
            ),
        );

        // echo "<script>
        //                 window.wpGoalTrackerGaEvents['pending']['add_to_cart'] = " . json_encode($item_data) . ";
        //             </script>";

        // This has to be queued and not as a script. Otherwise it will break the api.
        $this->gtga_events_queue[ "pending" ][ "add_to_cart" ] = $item_data;

    }

    // public function gtga_track_woocommerce_view_cart__premium_only()
    // {
    //     // Access the cart object
    //     $cart = WC()->cart->get_cart();

    //     // Array to hold items data
    //     $items = [  ];

    //     // Loop through each item in the cart
    //     foreach ($cart as $cart_item) {
    //         $product = $cart_item[ 'data' ];

    //         $items[  ] = [
    //             'item_id' => $product->get_sku() ?: $product->get_id(),
    //             'item_name' => $product->get_name(),
    //             'affiliation' => get_bloginfo('name'), // Using the site name as affiliation
    //             'price' => $product->get_price(),
    //             'quantity' => $cart_item[ 'quantity' ],
    //          ];
    //     }

    //     $items_json = json_encode($items);

    //     $event_data = array(
    //         'currency' => get_woocommerce_currency(),
    //         'value' => WC()->cart->cart_contents_total,
    //         'items' => $items);

    //     $ecommerce_settings = wp_goal_tracker_ga_get_options("ecommerceTrackingSettings");
    //     $woo_settings = $ecommerce_settings[ "wooCommerceSettings" ];

    //     if (isset($woo_settings[ "viewCart" ]) && $woo_settings[ "viewCart" ]) {
    //         echo "<script>
    //                                 window.wpGoalTrackerGaEvents['pending']['view_cart'] = " . json_encode($event_data) . ";
    //                             </script>";
    //         // $this->gtga_events_queue[ "pending" ][ "view_cart" ] = $event_data;
    //     }

    //     // Echo the JavaScript code
    //     if ((isset($woo_settings[ "addShippingInfo" ]) && $woo_settings[ "addShippingInfo" ]) || (isset($woo_settings[ "addPaymentInfo" ]) && $woo_settings[ "addPaymentInfo" ])) {
    //         echo "
    //     <script>
    //     var wpGoalTrackerWooData = {
    //      'items': $items_json,
    //      'value': " . WC()->cart->cart_contents_total . ",
    //      'currency': '" . get_woocommerce_currency() . "',
    //      'coupon': ''
    //     }
    //     </script>
    //     ";
    //     }

    // }

    public function gtga_send_purchase_event__premium_only($order_id)
    {
        // Get the order object
        $order = wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // Get order data
        $currency = $order->get_currency();
        $value = $order->get_total();
        $tax = $order->get_total_tax();
        $shipping = $order->get_shipping_total();
        $coupon_codes = $order->get_coupon_codes(); // Getting all coupon codes
        $coupons = implode(', ', $coupon_codes); // Converting array to string

        // Preparing items data
        $items = [  ];
        foreach ($order->get_items() as $item_id => $item) {
            // Get the product object
            $product = $item->get_product();

            if (!$product) {
                continue;
            }

            // Get product details
            $items[  ] = [
                'item_id' => $product->get_sku() ? $product->get_sku() : $product->get_id(),
                'item_name' => $product->get_name(),
                'affiliation' => get_bloginfo('name'), // You can customize this
                'price' => $product->get_price(),
                'quantity' => $item->get_quantity(), // Correct way to get the quantity
             ];
        }

        // Convert items array to JSON string
        $items_json = json_encode($items);
        $event_data = array(
            'transaction_id' => $order_id,
            'currency' => $currency,
            'value' => $value,
            'tax' => $tax,
            'shipping' => $shipping,
            'coupon' => $coupons,
            'items' => $items,
        );

        echo "<script>
								window.wpGoalTrackerGaEvents['pending']['purchase'] = " . json_encode($event_data) . ";
							</script>";
        // $this->gtga_events_queue[ "pending" ][ "purchase" ] = $event_data;

        // // Echo JavaScript code to send the event to Google Analytics
        // echo "<script>
        //         gtag('event', 'purchase', {
        //                 'transaction_id': '{$order_id}',
        //                 'value': {$value},
        //                 'currency': '{$currency}',
        //                 'tax': {$tax},
        //                 'shipping': {$shipping},
        //                 'coupon': '{$coupons}',
        //                 'items': {$items_json}
        //         });
        // </script>";
    }

    public function gtga_track_woocommerce_begin_checkout__primium_only()
    {
        // $cart = WC()->cart;

        // if (is_null(WC()->cart)) {
        //     WC()->frontend_includes();
        //     WC()->session = new WC_Session_Handler();
        //     WC()->session->init();
        //     WC()->customer = new WC_Customer(get_current_user_id(), true);
        //     WC()->cart = new WC_Cart();
        //     WC()->cart->get_cart();
        // }
        $cart = WC()->cart;

        $items = [  ];

        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item[ 'data' ];
            $items[  ] = [
                'item_id' => $product->get_sku() ? $product->get_sku() : $product->get_id(),
                'item_name' => $product->get_name(),
                'affiliation' => 'Your Store Name', // You can customize this
                'price' => $product->get_price(),
                'quantity' => $cart_item[ 'quantity' ],
             ];
        }

        $applied_coupons = $cart->get_applied_coupons();
        $coupon_string = !empty($applied_coupons) ? implode(", ", $applied_coupons) : "";
        $total_value = $cart->get_cart_contents_total();

        if (count($items) > 0) {
            $items_json = json_encode($items);

            $event_data = array(
                'currency' => get_woocommerce_currency(),
                'value' => $total_value,
                'items' => $items,
                'coupon' => $coupon_string,
            );

            $ecommerce_settings = wp_goal_tracker_ga_get_options("ecommerceTrackingSettings");
            $woo_settings = $ecommerce_settings[ "wooCommerceSettings" ];

            if (isset($woo_settings[ "beginCheckout" ]) && $woo_settings[ "beginCheckout" ]) {
                echo "
							<script>
									window.wpGoalTrackerGaEvents['pending']['begin_checkout'] = " . json_encode($event_data) . ";
							</script>
							";
            }

            // $this->gtga_events_queue[ "pending" ][ "begin_checkout" ] = $event_data;

            if ((isset($woo_settings[ "addShippingInfo" ]) && $woo_settings[ "addShippingInfo" ]) || (isset($woo_settings[ "addPaymentInfo" ]) && $woo_settings[ "addPaymentInfo" ])) {
                echo "
									<script>
									var wpGoalTrackerWooData = {
										'items': $items_json,
										'value': " . $total_value . ",
										'currency': '" . get_woocommerce_currency() . "',
										'coupon': '" . esc_js($coupon_string) . "'
									}
									</script>
									";
            }
        }
    }

// function gtga_track_woocommerce_remove_from_cart__premium_only() {
//     if (isset($_GET['removed_item'])) {
//         $cart = WC()->cart;

//         // Check if there are removed cart items
//         if (!empty($cart->removed_cart_contents)) {
//                 $items = [];
//                 $total_value = 0;

//                 // Iterate over removed cart items
//                 foreach ($cart->removed_cart_contents as $removed_cart_item) {
//                         $product_id = $removed_cart_item['product_id'];
//                         $product = wc_get_product($product_id);

//                         $items[] = [
//                                 'item_id' => $product->get_sku() ?: $product->get_id(),
//                                 'item_name' => $product->get_name(),
//                                 'affiliation' => get_bloginfo('name'),
//                                 'price' => $product->get_price(),
//                                 'quantity' => $removed_cart_item['quantity'],
//                         ];

//                         $total_value += $product->get_price() * $removed_cart_item['quantity'];
//                 }

//                 $item_data = [
//                         'currency' => get_woocommerce_currency(),
//                         'value' => $total_value,
//                         'items' => $items
//                 ];

//                 $item_json = json_encode($item_data);

//                     echo "
//                 <script>
//                         gtag('event', 'remove_from_cart', $item_json);
//                 </script>
//                 ";
//         }
// }
// }

    public function gtga_modify_add_to_cart_button__premium_only($args, $product)
    {

        $item_name = $product->get_name();
        $item_price = $product->get_price();
        $item_currency = get_woocommerce_currency();

        if ($product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock()) {
            $args[ 'attributes' ][ 'data-product_name' ] = $item_name;
            $args[ 'attributes' ][ 'data-product_price' ] = $item_price;
            $args[ 'attributes' ][ 'data-product_currency' ] = $item_currency;
        }
        return $args;
    }

/**
 * Register the scripts when the footer loads.
 *
 * @since    1.0.15
 */
    public function localize_script_in_footer()
    {

        // Check if data exists and the script is enqueued
        if (wp_script_is($this->plugin_name, 'enqueued')) {
        }
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Goal_Tracker_Ga_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Goal_Tracker_Ga_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        // wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-goal-tracker-ga-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Goal_Tracker_Ga_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Goal_Tracker_Ga_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        // Check if the user choose to disable tracking for logged in admin users.
        if ($this->should_skip_tracking_for_admins()) {
            return;
        }

        // Vimeo API
        $gtga_dependencies = array('jquery');

        if (gtg_fs()->is__premium_only()) {
            $options = get_option('wp_goal_tracker_ga_options');
            if (key_exists('videoSettings', $options) && $options[ 'videoSettings' ][ 'gaVimeoVideoTracking' ] == true) {
                wp_register_script(
                    'gtga_vimeo_api',
                    'https://player.vimeo.com/api/player.js',
                    array(),
                    false,
                    true
                );
                array_push($gtga_dependencies, 'gtga_vimeo_api');
            }
        }

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-goal-tracker-ga-public.js', $gtga_dependencies, $this->version, false);
        wp_localize_script($this->plugin_name, 'wpGoalTrackerGaEvents', $this->gtga_events_queue);

        wp_localize_script($this->plugin_name, 'wpGoalTrackerGa', $this->prepareSettings());
    }

    /**
     * Add the GA4 API code snippet to the page
     *
     */
    public function wp_goal_tracker_ga_add_ga4_code_snippet()
    {
        if ($this->should_skip_tracking_for_admins()) {
            return;
        }

        $options = wp_goal_tracker_ga_get_options();
        $general_settings = $options[ 'generalSettings' ];
        if (!isset($general_settings[ 'measurementID' ])) {
            return;
        }

        $ga_config_options = array();
        if ($general_settings[ 'gaDebug' ]) {
            $ga_config_options[ 'debug_mode' ] = true;
        }

        if ($general_settings[ 'disablePageView' ]) {
            $ga_config_options[ 'send_page_view' ] = false;
        }

        if (gtg_fs()->is__premium_only() && $general_settings[ 'trackUsers' ] && is_user_logged_in()) {
            // Generating a unique hash for the user based on user id and user name
            $user_token = get_current_user_id() . "_" . wp_get_current_user()->user_login;
            $hashed_token = hash('sha256', $user_token);
            $ga_config_options[ 'user_id' ] = $hashed_token;
        }

        $gtag_config = sizeof($ga_config_options) > 0 ? "," . wp_json_encode($ga_config_options) : "";

        $trackerCode = '<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_html($general_settings[ 'measurementID' ]) . '"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag("js", new Date());

			gtag("config", "' . esc_html($general_settings[ 'measurementID' ]) . '"' . ($gtag_config) . ');
		</script>';

        if (gtg_fs()->is__premium_only()) {
            if (isset($general_settings[ 'multiTrackers' ]) && $general_settings[ 'multiTrackers' ]) {
                $trackerCode = '<!-- Global site tag (gtag.js) - Google Analytics - Goal Tracker for Google Analytics -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_html($general_settings[ 'measurementID' ]) . '"></script>
				<script>
					window.dataLayer = window.dataLayer || [];
					function gtgatag(){dataLayer.push(arguments);}
					gtgatag("js", new Date());
					gtgatag("config", "' . esc_html($general_settings[ 'measurementID' ]) . '"' . ($gtag_config) . ');
				</script>';
            }
        }
        echo $trackerCode;
    }

    public function prepareSettings()
    {
        global $wp_query;

        $options = wp_goal_tracker_ga_get_options();
        $page_title = !empty($wp_query->post->post_title) ? $wp_query->post->post_title : '';
        $general_settings = $options[ 'generalSettings' ];

        $settings = array(
            'version' => $this->version,
            'is_front_page' => is_front_page(),
            'trackLinks' => $general_settings[ 'trackLinks' ],
            'trackEmailLinks' => $general_settings[ 'trackEmailLinks' ],
            'pageSpeedTracking' => $general_settings[ 'pageSpeedTracking' ],
            'click' => $options[ 'click' ],
            'visibility' => $options[ 'visibility' ],
            'pageTitle' => $page_title,
        );

        if (gtg_fs()->is__premium_only()) {
            $settings[ 'trackUsers' ] = $general_settings[ 'trackUsers' ];
            $settings[ 'videoSettings' ] = $options[ 'videoSettings' ];
            $settings[ 'formTrackingSettings' ] = $options[ 'formTrackingSettings' ];
            $settings[ 'ecommerceTrackingSettings' ] = $options[ 'ecommerceTrackingSettings' ];
            $settings[ "permittedRoles" ] = $options[ "generalSettings" ][ "permittedRoles" ];

            // Prepare data for placeholders
            global $wp_query;
            $page_name = !empty($wp_query->post->post_name) ? $wp_query->post->post_name : '';
            $post_id = !empty($wp_query->post->ID) ? $wp_query->post->ID : '';
            $category = !empty(get_the_category()[ 0 ]->name) ? get_the_category()[ 0 ]->name : '';
            $current_user = wp_get_current_user();
            $author_obj = !empty($wp_query->post->post_author) ? get_user_by(
                'id',
                $wp_query->post->post_author
            ) : '';

            $settings[ 'isSingle' ] = is_single();
            $settings[ 'isFrontPage' ] = is_front_page();
            $settings[ 'pageName' ] = $page_name;
            $settings[ 'postID' ] = $post_id;
            $settings[ 'category' ] = $category;
            $settings[ 'isUserLoggedIn' ] = is_user_logged_in();
            $settings[ 'currentUserName' ] = $current_user->user_login;
            $settings[ 'currentUserId' ] = $current_user->ID;
            $settings[ 'postAuthor' ] = !empty($author_obj) ? $author_obj->data->user_login : '';
        }

        return $settings;
    }

    public function addPublicPluginSettings()
    {
        $options = wp_goal_tracker_ga_get_options();
        $public_options = array();
    }

    private function should_skip_tracking_for_admins()
    {
        if (is_user_logged_in() && current_user_can('manage_options')) {
            $options = wp_goal_tracker_ga_get_options();
            $general_settings = $options[ 'generalSettings' ];
            if (key_exists('disableTrackingForAdmins', $general_settings) && $general_settings[ 'disableTrackingForAdmins' ] == true) {
                return true;
            }
        }
        return false;
    }
}
