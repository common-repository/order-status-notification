<?php
/**
 * Order Status Notification Admin
 *
 * @class    OSN_Admin
 * @package  Order Status Notification/Admin
 * @version  1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * OSN_Admin class.
 */
class OSN_Admin {

  /**
   * Constructor.
   */
  public function __construct() {
    add_action( 'init', array( $this, 'includes' ) );
    add_action( 'wp_ajax_osn_test_notification', array( $this, 'osn_test_notification' ) );
  }

  /**
   * Include any classes we need within admin.
   */
  public function includes() {
    include_once dirname( __FILE__ ) . '/class-osn-admin-assets.php';
  }


  /**
   * Trigger test notification
   *
   * @since 1.0
   * @return JSON Object
   */
  public function osn_test_notification() {
    $phone = isset( $_POST['phone_number'] ) ? sanitize_text_field( $_POST['phone_number'] ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';
    $selected_service = isset( $_POST['service_name'] ) ? sanitize_text_field( $_POST['service_name'] ) : 'twilio';

    $response = [];

    if ( empty( $phone ) || empty( $message ) ) {
      $response['status'] = 'error';
    }
    else {
      $notification = new Order_Status_Notification_Service();
      $response = $notification->send_notification( $selected_service, $phone, $message );

      $response_status = isset( $response['errors'] ) ? 'error' : 'success' ;
      
      $response['status'] = $response_status;
    }

    wp_send_json( $response );

    wp_die();
  }

}

return new OSN_Admin();
