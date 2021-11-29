<?php

use Inc\Base\Activate;
use Inc\Base\Deactivate;
use Inc\ClicknShipAPI;
/*
Plugin Name: ClicknShip Shipping plugin
Plugin URI: https://clicknship.com.ng/
Description: ClicknShip shipping method plugin
Version: 1.0.0
Author: Click and ship
Author URI: 
*/

if (!defined('WPINC')) {

    die;
}
defined('ABSPATH') or die('You do not have the right access');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}
/*
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    function clicknShip_shipping_method()
    {
        if (!class_exists('ClicknShip_Shipping_Method')) {
            class ClicknShip_Shipping_Method extends WC_Shipping_Method
            {

                public function __construct()
                {
                    $this->id                 = 'my';
                    $this->method_title       = __('ClicknShip Shipping', 'my');
                    $this->method_description = __('Custom Shipping Method', 'my');
                    $this->title = 'ClicknShip';


                    $this->init();

                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';

                    $this->title = "ClicknShip";
                    //$this->icon = apply_filters('woocommerce_my_icon', plugins_url('assets/cyberpay.jpeg', __FILE__));
                }


                function init()
                {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }


                function init_form_fields()
                {

                    $this->form_fields = array(

                        'enabled' => array(
                            'title' => __('Enable', 'my'),
                            'type' => 'checkbox',
                            'description' => __('Enable this shipping.', 'my'),
                            'default' => 'yes'
                        ),

                        'username' => array(
                            'title' => __('Username'),
                            'type' => 'text',
                            'description' => __('Username for ClicknShip account')

                        ),
                        'passsword' => array(
                            'title' => __('password'),
                            'type' => 'password',
                            'description' => __('Password for ClicknShip account')

                        ),
                        'phoneNumber' => array(
                            'title' => __('Phone Number'),
                            'type' => 'number',
                            'description' => __('Store Phone Number'),
                            'required'=>'required'

                        ),'StoreCity' => array(
                            'title' => __('Store City'),
                            'type' => 'select',
                            'options' => array(
                                '' => '',
                                'Aba' => 'Aba',
                                'Abakaliki' => 'Abakaliki',
                                'Abeokuta'  => 'Abeokuta',
                                'Abuja'   => 'Abuja',
                                'Ado Ekiti'    => 'Ado Ekiti',
                                'Akure' => 'Akure',
                                'Asaba' => 'Asaba',
                                'Awka' => 'Awka',
                                'Bauchi'  => 'Bauchi',
                                'Benin'   => 'Benin',
                                'Birnin Kebbi'    => 'Birnin Kebbi',
                                'Bonny' => 'Bonny',
                                'Calabar' => 'Calabar',
                                'Damaturu' => 'Damaturu',
                                'Dutse'  => 'Dutse',
                                'Eket'   => 'Eket',
                                'Gombe'    => 'Gombe',
                                'Gusau' => 'Gusau',
                                'Ibadan' => 'Ibadan',
                                'Ijebu Ode' => 'Ijebu Ode',
                                'Ikot Ekpene'  => 'Ikot Ekpene',
                                'Ile-ife'   => 'Ile-ife',
                                'Ilorin'    => 'Ilorin',
                                'Jalingo' => 'Jalingo',
                                'Jos' => 'Jos',
                                'Kaduna' => 'Kaduna',
                                'Kano' => 'Kano',
                                'Kastina'  => 'Kastina',
                                'Lafia'   => 'Lafia',
                                'Lagos Island'    => 'Lagos Island',
                                'Lokoja' => 'Lokoja',
                                'Maiduguri' => 'Maiduguri',
                                'Mainland' => 'Mainland',
                                'Makurdi'  => 'Makurdi',
                                'Minna'   => 'Minna',
                                'Nnewi'    => 'Nnewi',
                                'Nsukka' => 'Nsukka',
                                'Ofa' => 'Ofa',
                                'Ogbomosho' => 'Ogbomosho',
                                'Onitsha' => 'Onitsha',
                                'Oshogbo'  => 'Oshogbo',
                                'Owerri'   => 'Owerri',
                                'Oyo'    => 'Oyo',
                                'Port Harcourt' => 'Port Harcourt',
                                'Sagamu' => 'Sagamu',
                                'Sapele' => 'Sapele',
                                'Makurdi'  => 'Makurdi',
                                'Sokoto'   => 'Sokoto',
                                'Suleja'    => 'Suleja',
                                'Umuahia' => 'Umuahia',
                                'Uyo' => 'Uyo',
                                'Warri'  => 'Warri',
                                'Yenagoa'   => 'Yenagoa',
                                'Yola'    => 'Yola',
                                'Zaria' => 'Zaria',
                            ),

                        ),'StoreState' => array(
                            'title' => __('Store State'),
                            'type' => 'text',
                            'description' => __('Eg Delta')

                        ),
                        'locationID' => array(
                            'title' => __('Location ID'),
                            'type' => 'number',
                            'description' => __('ClickNShip Store Location ID')

                        ),
                        'shopName' => array(
                            'title' => __('Shop Name'),
                            'type' => 'text',
                            'description' => __('Your Shop name')

                        ),


                    );
                }


                public function getToken()
                {

                    $username = $this->settings['username'];
                    $password = $this->settings['passsword'];
                    $token = ClicknShipAPI::getToken($username, $password);
                    return $token;
                }

                public function calculate_shipping($package = array())
                {
                    global $woocommerce;
                    $shipping_city = $woocommerce->customer->get_shipping_city();
                
                    $shipping_state = $woocommerce->customer->get_shipping_state();

                    $items = $woocommerce->cart->get_cart();
                    
                    if (session_id() ==='') {
                        session_start();
                    }

                    $items = array();
                    foreach (WC()->cart->get_cart() as $cart_item) {
                        $item_name = $cart_item['data']->get_title();
                        $quantity = $cart_item['quantity'];
                        $price = $cart_item['data']->get_price();
                        $items[] = array(
                            "ItemName" => $item_name,
                            "ItemUnitCost" => $price,
                            "ItemQuantity" => $quantity,
                            "ItemColour" => " ",
                            "ItemSize" => " "
                        );
                    }
                    $_SESSION['items'] = $items;

                    $store_city = $this->settings['StoreCity'];
                    $_SESSION['storeCity'] =$store_city;
                    $store_raw_country = get_option('woocommerce_default_country');

                    // Split the country/state
                    $split_country = explode(":", $store_raw_country);

                    // Country and state separated:
                    $store_state   = $split_country[1];

                    //check if customer shipping address is acurate
                    // if ($shipping_city == "" || $shipping_state == "") {

                    //     $message = sprintf("You need to enter your full address to use ClicknShip shipping method");

                    //     $messageType = "error";

                    //     if (!wc_has_notice($message, $messageType)) {

                    //         wc_add_notice($message, $messageType);
                    //     }
                    // }

                    //call ClicknShip API


                    $weight = 0;
                    $cost = 0;

                    $items = array();
                    foreach ($package['contents'] as $item_id => $values) {
                        $_product = $values['data'];

                        $weight = $weight + (int) $_product->get_weight() * (int) $values['quantity'];
                
                    }

                    $weight = wc_get_weight($weight, 'kg');

                    if($weight == 0)
                    $weight = 1;
                    $check = $this->getToken();
                    $token = $check->access_token;
                    //store weight in session


                    if (isset($weight)) {
                        $_SESSION['token'] = $token;
                        $_SESSION['totalWeight'] = $weight;
                    }

                    if($store_city =='' || $shipping_city ==''){
                      $cost = 1000;
                        }
                        elseif($store_city !='' || $shipping_city !=''){

                            $params   = array(
                                "origin" => $store_city,
                                "destination" => $shipping_city,
                                "weight" => $weight,
                                "shipping_state" => $shipping_state,
                                "store_state" => $store_state,
                                "token" => $token
                            );

                        $townID =ClicknShipAPI::setTownID($token);
                        $cost = ClicknShipAPI::calculateDeliveryFee($params);
                        $cost = $cost[0]->TotalAmount;
                        }
                          if(!isset($townID)){
                            $cost = 1000;
                        }
                          
                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $cost
                    );
                    // $rate = $this->username;

                    $this->add_rate($rate);
                }
            }
        }
    }
    add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'clicknship_add_plugin_page_settings_link');
    function clicknship_add_plugin_page_settings_link( $links ) {
        $links[] = '<a href="' .
            admin_url( 'admin.php?page=wc-settings&tab=shipping&section=my' ) .
            '">' . __('Settings') . '</a>';
        return $links;
    }
    add_action('woocommerce_shipping_init', 'clicknShip_shipping_method');

    function add_clicknShip_shipping_method($methods)
    {
        $methods[] = 'ClicknShip_Shipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'add_clicknShip_shipping_method');

    add_action('woocommerce_thankyou', 'book_delivery', 10, 1);

    function book_delivery($order_id)
    {
        global $woocommerce;
        $order = new WC_Order($order_id);
        if ($order != "") {

            $order_id = $order->get_id(); // Get the order ID            
            $user_id  = $order->get_user_id(); // Get the costumer ID
            $user  = $order->get_user(); // Get the WP_User object
            $customerEmail = $order->get_billing_email();
            $customerPhone = $order->get_billing_phone();
            // if (session_id() =='') {
            //     session_start();
            // }
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
             }

            $totalWeight = $_SESSION['totalWeight'];
            $items = $_SESSION['items'];

            $token = $_SESSION['token'];
            $order_status  = $order->get_status(); // Get the order status 
            $paymentMethod = $order->get_payment_method(); // Get the payment method ID
            if ($paymentMethod == 'cod')
                $paymentMethod = " Pay On Delivery";
            else {
                $paymentMethod = " Prepaid";
            }
            $payment_title = $order->get_payment_method_title(); // Get the payment method title
            $recipentFirstname =   $order->get_shipping_first_name();
            $recipientLastName = $order->get_shipping_last_name();
            $customerName = $recipentFirstname . " " . $recipientLastName;
            $recipientAddress1 = $order->get_shipping_address_1();
            $recipientAddress2 = $order->get_shipping_address_2();
            $recipentAddress = $recipientAddress1 . " " . $recipientAddress2;
            $recipientCity = $_SESSION['destination'];
            $store_address     = get_option('woocommerce_store_address');
            $store = new ClicknShip_Shipping_Method();
            $storePhone = $store->settings['phoneNumber'];
            $senderTownID = $store->settings['locationID'];
            $recipientTownID = $_SESSION['locationID'];
            $senderName = $store->settings['shopName'];
            $shipping_state = $woocommerce->customer->get_shipping_state();
            $store_city  = $_SESSION['storeCity'];
            $store_raw_country = get_option('woocommerce_default_country');
            // Split the country/state
            $split_country = explode(":", $store_raw_country);

            $store_state   = $split_country[1];

            $description = array();
            foreach ($order->get_items() as $item) {
                $description[] = $item['name'];

                continue;
            }

            $description = implode(", ", $description);
            $orderNo = 'WC-' . rand(100, 999) . '-' . $order_id;
            $params   = array(
                "OrderNo" => $orderNo,
                "Description" => $description,
                "Weight" => $totalWeight,
                "SenderName" => $senderName,
                "SenderCity" => $store_city,
                "SenderTownID" => $senderTownID,
                "SenderAddress" => $store_address,
                "SenderPhone" => $storePhone,
                "RecipientName" => $customerName,
                "RecipientCity" => $recipientCity,
                "RecipientTownID" =>$recipientTownID,
                "RecipientAddress" => $recipentAddress,
                "RecipientPhone" => $customerPhone,
                "RecipientEmail" => $customerEmail,
                "PaymentType" => $paymentMethod,
                "DeliveryType" => 'Normal',
                "ShipmentItems" => $items

            );

            $confirmed = ClicknShipAPI::pickupRequest($token, $params);
            
            $trackingNumber = $confirmed->WaybillNumber;
            $_SESSION['track'] = $trackingNumber;
          
           
        }


        /* Do Something with order ID */
    }
 //   add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');

   
    /**
     * Display field value on the order edit page
     */
    
     //Update order with tracking number
    add_action('woocommerce_checkout_create_order_line_item', 'custom_checkout_create_order_line_item', 900, 4);
    function custom_checkout_create_order_line_item($item, $cart_item_key, $values, $order)
    {
        if (session_id() =='') {
            session_start();
        }
         
     //  $trackingNumber = $_SESSION['track'];
      //  if (!isset( $_SESSION['track'])) return;

        if (isset($_SESSION['track']))
        //print_r('yesssy');
            $item->update_meta_data('Tracking Number', $_SESSION['track']);
        
    // function end_session() {
    //     session_destroy ();
    // }
    }

    function clicknship_change_city_to_dropdown( $fields ) {

        $city_args = wp_parse_args( array(
            'type' => 'select',
            'options' => array(
                '' => '',
                'Aba' => 'Aba',
                'Abakaliki' => 'Abakaliki',
                'Abeokuta'  => 'Abeokuta',
                'Abuja'   => 'Abuja',
                'Ado Ekiti'    => 'Ado Ekiti',
                'Akure' => 'Akure',
                'Asaba' => 'Asaba',
                'Awka' => 'Awka',
                'Bauchi'  => 'Bauchi',
                'Benin'   => 'Benin',
                'Birnin Kebbi'    => 'Birnin Kebbi',
                'Bonny' => 'Bonny',
                'Calabar' => 'Calabar',
                'Damaturu' => 'Damaturu',
                'Dutse'  => 'Dutse',
                'Eket'   => 'Eket',
                'Gombe'    => 'Gombe',
                'Gusau' => 'Gusau',
                'Ibadan' => 'Ibadan',
                'Ijebu Ode' => 'Ijebu Ode',
                'Ikot Ekpene'  => 'Ikot Ekpene',
                'Ile-ife'   => 'Ile-ife',
                'Ilorin'    => 'Ilorin',
                'Jalingo' => 'Jalingo',
                'Jos' => 'Jos',
                'Kaduna' => 'Kaduna',
                'Kano' => 'Kano',
                'Kastina'  => 'Kastina',
                'Lafia'   => 'Lafia',
                'Lagos Island'    => 'Lagos Island',
                'Lokoja' => 'Lokoja',
                'Maiduguri' => 'Maiduguri',
                'Mainland' => 'Mainland',
                'Makurdi'  => 'Makurdi',
                'Minna'   => 'Minna',
                'Nnewi'    => 'Nnewi',
                'Nsukka' => 'Nsukka',
                'Ofa' => 'Ofa',
                'Ogbomosho' => 'Ogbomosho',
                'Onitsha' => 'Onitsha',
                'Oshogbo'  => 'Oshogbo',
                'Owerri'   => 'Owerri',
                'Oyo'    => 'Oyo',
                'Port Harcourt' => 'Port Harcourt',
                'Sagamu' => 'Sagamu',
                'Sapele' => 'Sapele',
                'Makurdi'  => 'Makurdi',
                'Sokoto'   => 'Sokoto',
                'Suleja'    => 'Suleja',
                'Umuahia' => 'Umuahia',
                'Uyo' => 'Uyo',
                'Warri'  => 'Warri',
                'Yenagoa'   => 'Yenagoa',
                'Yola'    => 'Yola',
                'Zaria' => 'Zaria',
            ),'input_class' => array(
                'wc-enhanced-select',
            )
        ), $fields['shipping']['shipping_city']);
       
        $fields['shipping']['shipping_city'] = $city_args;
        $fields['billing']['billing_city'] = $city_args; // Also change for billing field

        wc_enqueue_js( "
	jQuery( ':input.wc-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
		var select2_args = { minimumResultsForSearch: 5 };
		jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
	});" );
    
        return $fields;
    
    }
    add_filter( 'woocommerce_checkout_fields', 'clicknship_change_city_to_dropdown' );

    //add_action('woocommerce_review_order_before_cart_contents', 'my_validate_order', 10);
   // add_action('woocommerce_after_checkout_validation', 'my_validate_order', 10);




    function activate_first_plugin()
    {

        Activate::activate();
    }

    function deactivate_first_plugin()
    {

        Deactivate::deactivate();
    }
}
