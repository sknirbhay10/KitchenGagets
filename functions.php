<?php 
function add_theme_scripts() {
	wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( 'storefront-style' ), 
        wp_get_theme()->get('Version') // this only works if you have Version in the style header
    );
	wp_enqueue_style( 'datatable-css', 'https://cdn.datatables.net/1.10.23/css/jquery.dataTables.css'); 
	wp_enqueue_script( 'datatable-script', 'https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js', array ( 'jquery' ), '', true);
  }
  add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );



/* Remove Download Menu Link  */

function my_account_menu_order() {
 	$menuOrder = array(
 		'dashboard'          => __( 'Dashboard', 'woocommerce' ),
 		'orders'             => __( 'Appliances Estimate', 'woocommerce' ),
 		//'downloads'          => __( 'Download', 'woocommerce' ),
 		'edit-address'       => __( 'Addresses', 'woocommerce' ),
 		'edit-account'    	=> __( 'Account Details', 'woocommerce' ),
 		'customer-logout'    => __( 'Logout', 'woocommerce' ),
 	);
 	return $menuOrder;
 }
 add_filter ( 'woocommerce_account_menu_items', 'my_account_menu_order' );



add_filter( 'woocommerce_my_account_my_orders_query', 'custom_my_account_orders_query', 20, 1 );
function custom_my_account_orders_query( $args ) {
	$args['limit'] = -1;
    return $args;
}


/* Add Bill to column in My Order Tab  */
function add_my_account_orders_column( $columns ) {

    $new_columns = array();

    foreach ( $columns as $key => $name ) {

        $new_columns[ $key ] = $name;
        // add ship-to after order status column
        if ( 'order-status' === $key ) {
            $new_columns['order-bill-to'] = __( 'Billing to', 'textdomain' );
        }
    }

    return $new_columns;
}
add_filter( 'woocommerce_my_account_my_orders_columns', 'add_my_account_orders_column' );


function my_orders_bill_to_column( $order ) {

	//$formatted_shipping = $order->get_formatted_shipping_address();
	$formatted_billing = $order->get_formatted_billing_address();
	echo ! empty( $formatted_billing ) ? $formatted_billing : '–';
}
add_action( 'woocommerce_my_account_my_orders_column_order-bill-to', 'my_orders_bill_to_column' );


/* Email to shop manager for new order  */
add_filter( 'woocommerce_email_recipient_customer_processing_order', 'shop_email_recipient', 10, 2 );
function shop_email_recipient( $recipient, $order ) {
    if ( ! is_a( $order, 'WC_Order' ) ) return $recipient;

	$user_info = get_userdata($order->user_id);
    $user_email=$user_info->user_email;  
	
    // Email to user email id
    $recipient = $recipient .','. $user_email;
	 return $recipient;
}

/* Change Woocommerce sale flash Text  */
add_filter('woocommerce_sale_flash', 'woocommerce_custom_sale_text', 10, 3);
function woocommerce_custom_sale_text($text, $post, $_product)
{
    return '<span class="onsale">Offer Price</span>';
}


/* Update Thank you page Title  */
add_filter( 'woocommerce_endpoint_order-received_title', 'thank_you_title' );
 
function thank_you_title( $old_title ){
 
 	return 'Appliance Estimate';
 
}
/* Hide Text Editor fro product */
add_action('init', 'init_remove_support',100);
function init_remove_support(){
    $post_type = 'product';
    remove_post_type_support( $post_type, 'editor');
}


/* Change title placeholder for product  */
function change_title_placeholder( $title ){
    $screen = get_current_screen();
 
    if  ( 'product' == $screen->post_type ) {
         $title = 'Enter Model No';
    }
 
    return $title;
}
 
add_filter( 'enter_title_here', 'change_title_placeholder' );

