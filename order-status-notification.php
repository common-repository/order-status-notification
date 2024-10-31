<?php
/**
* Plugin Name: Order Status Notification For WooCommerce
* Description: This extension simply sends order notification to the customer and admin
* Plugin URI: http://wordpress.org
* Version: 1.0
* Author: WooExtension
* Author URI: http://wpfumes.net
* Text Domain: order-status-notification
* Domain Path: /languages/
*
* WC requires at least: 3.0
* WC tested up to: 4.8
*
* @package Order Status Notification
*/

defined( 'ABSPATH' ) || exit;

// Define OSN_PLUGIN_FILE.
if ( ! defined( 'OSN_PLUGIN_FILE' ) ) {
  define( 'OSN_PLUGIN_FILE', __FILE__ );
}

// include dependencies file
if ( ! class_exists( 'OSN_Dependencies' ) ){
  include_once dirname( __FILE__) . '/includes/class-order-status-notification-dependencies.php';
}

// Include the main OrderStatusNotification class.
if ( ! class_exists( 'Order_Status_Notification', false ) ) {
  include_once dirname( OSN_PLUGIN_FILE ) . '/includes/class-order-status-notification.php';
}

/**
 * Returns the main instance of OSN.
 *
 * @since  1.0
 * @return OrderStatusNotification
 */
function OSN() {
  return Order_Status_Notification::instance();
}

// Global for backwards compatibility.
$GLOBALS['order-status-notification'] = OSN();