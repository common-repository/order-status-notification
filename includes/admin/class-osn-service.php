<?php
/**
 * Order_Status_Notification Service
 *
 * @package Order_Status_Notification
 * @since 1.0
 */

defined( 'ABSPATH' ) || exit;


class Order_Status_Notification_Service {

  protected $data;

  protected $options;

  protected $plivo_auth;

  protected $plivo_token;

  protected $plivo_source_number;

  protected $admin_number;

  protected $twilio_sid;

  protected $twilio_token;

  public function __construct() {

    $this->twilio_sid           = get_option( 'osn_twilio_sid', true );

    $this->twilio_token         = get_option( 'osn_twilio_token', true );

    $this->plivo_auth           = get_option( 'osn_plivo_auth', true );

    $this->plivo_token          = get_option( 'osn_plivo_token', true );

    $this->plivo_source_number  = get_option( 'osn_plivo_number', true );

  }

  /**
   * Send Notification
   * 
   * @param Service Type, Phone Number, Message
   * @return array
   * @since 1.0
   */
  public function send_notification( $service_type, $phone_number, $message ) {

    $response = array();

    if ( $service_type == 'twilio' ) {

      $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->twilio_sid}/Messages.json";

      $twilio_number = get_option( 'osn_twilio_number', true );
      
      $data = http_build_query( 
                array(
                  'Body'    => $message,
                  'To'      => $phone_number,
                  'From'    => $twilio_number,
                )
              );

      $auth = base64_encode( $this->twilio_sid . ':' . $this->twilio_token );

      $args = array(
        'headers' => array(
        'Authorization' => "Basic $auth",
        ),
      'body'    => $data,
      );

      $response  = wp_remote_post( $url, $args );
      
      if ( !empty( $response ) ) {

        if ( isset( $response['body'] ) ) {
          
          $response_data = json_decode( $response['body'] );

          if ( !in_array( $response_data->status, array( 'accepted', 'queued') ) ) {
            $this->data['errors'][] = __( 'Your message was failed to be sent.', 'order-status-notification' );
            $this->data['errors'][] = sprintf( __( 'Error: %1$s - %2$s', 'order-status-notification' ), $response_data->message, $response_data->code );
          }
          else {
            $this->data['success'][] = __( 'Your message was sent successfully.', 'order-status-notification' );
          }
        }
      }
      else {
        $this->data['errors'][] = __( 'Your message could not be sent. Please try again later.', 'order-status-notification' ) ;
      }
      return $this->data;
    }

    else {
      
      $url = "https://api.plivo.com/v1/Account/{$this->plivo_auth}/Message/";

      $data = wp_json_encode( 
        array(
          'text' => $message,
          'dst'  => $phone_number,
          'src'  => $this->plivo_source_number,
        )
      );

      $auth = base64_encode( $this->plivo_auth . ':' . $this->plivo_token );

      $args = array(
        'headers' => array(
          'Authorization'   => "Basic $auth",
          'Content-Type'    => 'application/json',
        ),
        'body'    => $data,
      );

      $response = wp_remote_post( $url, $args );

      if ( !empty( $response ) ) {

        if ( isset( $response['body'] ) ) {
          
          $response_data = json_decode( $response['body'] );

          if ( isset( $response_data->error ) ) {
            $this->data['errors'][] = __( 'Your message was failed to be sent', 'order-status-notification' );
            $this->data['errors'][] = sprintf( __( 'Error: %s', 'order-status-notification' ), $response_data->error );
          }
          else {
            $this->data['success'][] = __( 'Your message was sent successfully.', 'order-status-notification' );
          }
        }
      } 
      else {
        $this->data['errors'][] = __( 'Your message could not be sent. Please try again later.', 'order-status-notification' );
      }
      return $this->data;
    }

  }

}