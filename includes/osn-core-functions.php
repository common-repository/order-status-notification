<?php
/**
 * Order_Status_Notification Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package Order_Status_Notification\Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


/**
 * Get customer phone form order id
 * @return string
 */
function osn_get_customer_phone( $order_id ) {
  
  $customer_phone = '';

  if ( !empty( $order_id ) ) {
    $order = wc_get_order( $order_id );

    if ( $order ) {
      //Check customer phone from meta
      $customer_phone = $order->get_billing_phone();;

      if ( empty( $customer_phone ) ) {
        
        $user_id = $order->get_user_id();
        
        if ( !empty( $user_id ) ) {
          $customer_phone = get_user_meta( $user_id, 'user_phone', true );
        }
      }
    }
  }

  return $customer_phone;
}

