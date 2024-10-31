<?php
/**
 * Order Status Notification Settings
 *
 * @author    dorishk
 * @category  Admin
 * @version   1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (  class_exists( 'WC_Settings_Page' ) ) :

/**
 * WC_Settings_Accounts
 */
class WC_Settings_Order_Status_Notification extends WC_Settings_Page {

  /**
  * Constructor.
  */
  public function __construct() {
    $this->id    = 'order_status_notification';
    $this->label = __( 'Order Status Notification', 'order-status-notification' );
    
    add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
    add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
    add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
    add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
    add_action( 'current_screen', array( $this, 'osn_add_tabs' ), 99 );

    add_action( 'woocommerce_admin_field_button' , array( $this, 'osn_admin_field_button' ), 10 );

  }


  /**
   * Get sections.
   *
   * @return array
   */
  public function get_sections() {
    
    $sections = array(
      ''           => __( 'General', 'food-store' ),
      'twilio'     => __( 'Twilio',  'food-store' ),
      'plivo'      => __( 'Plivo',  'food-store' ),
    );
    return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
  }

  /**
  * Output sections.
  */
  public function output_sections() {
    global $current_section;

    $sections = $this->get_sections();

    if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
      return;
    }

    echo '<ul class="subsubsub">';

    $array_keys = array_keys( $sections );

    foreach ( $sections as $id => $label ) {
      echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
      }

      echo '</ul><br class="clear" />';
  }

  public function osn_admin_field_button( $value ){
    $option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
    $description = WC_Admin_Settings::get_field_description( $value );
            
  ?>
           
  <tr valign="top">
    <th scope="row" class="titledesc"></th>
                
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
      <button class="button-primary osn-send-test-notification">
        <?php echo __( 'Send test notification',  'order-status-notification' ); ?>    
      </button> 
    </td>
  </tr>

  <?php       
}


  /**
   * Get settings array
   *
   * @return array
   */
  public function get_settings( $current_section = '' ) {

    $current_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';

    if ( '' === $current_section ) {
      
      $settings = apply_filters(
        'woocommerce_settings_osn_general',
        
        array(

          array(  
            'title'   => __( 'Notification Settings', 'order-status-notification' ), 
            'type'    => 'title',
            'desc'    => '', 
            'id'      => 'order_status_notification_title' 
          ),

          array(
            'title'   => __( 'Enable', 'order-status-notification' ),
            'desc'    => __( 'Enable Order Status Notification.', 'order-status-notification' ),
            'type'    => 'checkbox',
            'id'      => 'osn_enabled',
            'default' => 'no'                     
          ),

          array(
            'title'   => __( 'Select notification service', 'order-status-notification' ),
            'desc'    => __( 'Select service for notification.', 'order-status-notification' ),
            'type'    => 'radio',
            'options' => array( 'twilio' => 'Twilio', 'plivo' => 'Plivo' ),
            'id'      => 'osn_service',
            'class'   => 'osn_service',
            'autoload' => false,
            'desc_tip' => true,                  
          ),

          array(
            'title'   => __( 'Admin phone number', 'order-status-notification' ),
            'desc'    => __( 'Enter admin phone number with country code. eg: +91765467890 ', 'order-status-notification' ),
            'type'    => 'text',
            'id'      => 'osn_admin_phone',
            'autoload'        => false,
            'desc_tip'        => true,
          ),

          array(
            'title'    => __( 'Send notification to admin on order status', 'order-status-notification' ),
            'desc'     => __( 'Select order status for which admin would get notification', 'order-status-notification' ),
            'id'       => 'osn_admin_notification_status',
            'type'     => 'multiselect',
            'class'    => 'wc-enhanced-select',
            'css'      => 'min-width: 350px;',
            'options'  => wc_get_order_statuses(),
            'default'  => '',
            'autoload' => false,
            'desc_tip' => true,
          ),

          array(
            'title'   => __( 'Admin SMS Text', 'order-status-notification' ),
            'desc'    => __( 'Enter the text that would be send to the admin when a new order would be placed. Available placeholders {ORDER_NUMBER}, {ORDER_STATUS}, {STORE_NAME}, {BILLING_FNAME}, {FULLNAME}, {PHONE}, {PRICE}', 'order-status-notification' ),
            'type'    => 'textarea',
            'css'     => 'min-width: 50%; height: 100px;',
            'id'      => 'osn_admin_notification_text',
            'autoload'        => false,
            'desc_tip'        => true,
          ),

          array(
            'title'    => __( 'Send notification to customers on order status', 'order-status-notification' ),
            'desc'     => __( 'Select order status for which customer would get notification', 'order-status-notification' ),
            'id'       => 'osn_customer_notification_status',
            'type'     => 'multiselect',
            'class'    => 'wc-enhanced-select',
            'css'      => 'min-width: 350px;',
            'options'  => wc_get_order_statuses(),
            'default'  => '',
            'autoload' => false,
            'desc_tip' => true,
          ),

          array(
            'title'   => __( 'Customer SMS Text', 'order-status-notification' ),
            'desc'    => __( 'Enter the text that would be send to the customer when they make a new order. Available placeholders {ORDER_NUMBER}, {ORDER_STATUS}, {STORE_NAME}, {BILLING_FNAME}, {FULLNAME}, {PHONE}, {PRICE}', 'order-status-notification' ),
            'type'    => 'textarea',
            'css'     => 'min-width: 50%; height: 100px;',
            'id'      => 'osn_customer_notification_text',
            'autoload'        => false,
            'desc_tip'        => true,
          ),
        
          array( 
            'type' => 'sectionend', 
            'id' => 'order_status_notification_options'
          ),

          array(
            'title' => __( 'Test Notification', 'order-status-notification' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'general_options',
          ),

          array(
            'title'   => __( 'Test Phone Number', 'order-status-notification' ),
            'desc'    => __( 'Enter phone number where you want to send the test message. eg: +91765467890 ', 'order-status-notification' ),
            'type'    => 'text',
            'id'      => 'osn_test_phone',
            'autoload'  => false,
            'desc_tip'  => true,
          ),

          array(
            'title'   => __( 'Test Message', 'order-status-notification' ),
            'desc'    => __( 'Enter the test message which would be sent to the number', 'order-status-notification' ),
            'type'    => 'textarea',
            'css'     => 'min-width: 50%; height: 100px;',
            'id'      => 'osn_test_message',
            'autoload'        => false,
            'desc_tip'        => true,
          ),

          array(
            'name'    => __( 'Send test message', 'order-status-notification' ),
            'type'    => 'button',
            'title'   => __( 'Send Test', 'order-status-notification' ),
            'id'      => 'send_test_message',
          ),

          array( 
            'type' => 'sectionend', 
            'id' => 'order_status_test_notification'
          ),

        )
      );
    }
    elseif( 'twilio' === $current_section  ) {
      $settings = apply_filters(
        'woocommerce_settings_osn_general',
        
        array(

          array(  
            'title'   => __( 'Twilio Settings', 'order-status-notification' ), 
            'type'    => 'title',
            'desc'    => '', 
            'id'      => 'osn_twilio_title' 
          ),

          array(
            'title'   => __( 'Account SID', 'order-status-notification' ),
            'desc'    => __( 'To view API credentials visit <a href="https://www.twilio.com">https://www.twilio.com</a> ', 'order-status-notification' ),
            'type'    => 'text',
            'id'      => 'osn_twilio_sid',
          ),

          array(
            'title'   => __( 'Account Token', 'order-status-notification' ),
            'desc'    => __( 'To view API credentials visit <a href="https://www.twilio.com">https://www.twilio.com</a> ', 'order-status-notification' ),
            'type'    => 'password',
            'id'      => 'osn_twilio_token',
          ),

          array(
            'title'   => __( 'Twilio Number', 'order-status-notification' ),
            'desc'    => __( 'Country code +10-digit Twilio phone number (i.e +16171241221)', 'order-status-notification' ),
            'type'    => 'text',
            'id'      => 'osn_twilio_number',
            'autoload'        => false,
            'desc_tip'        => true,
          ),
        
        array( 
          'type' => 'sectionend', 
          'id' => 'order_status_notification_options'
        ),

        )
      );
    }
    elseif( 'plivo' === $current_section ) {
      $settings = apply_filters(
        'woocommerce_settings_osn_general',
        
        array(

        array(  
          'title'   => __( 'Plivo Settings', 'order-status-notification' ), 
          'type'    => 'title',
          'desc'    => '', 
          'id'      => 'osn_plivo_title' 
        ),

        array(
            'title'   => __( 'Auth Id', 'order-status-notification' ),
            'desc'    => __( 'To view API credentials visit <a href="https://console.plivo.com/dashboard/">https://console.plivo.com/dashboard/</a>', 'order-status-notification' ),
            'type'    => 'text',
            'id'      => 'osn_plivo_auth',
          ),

          array(
            'title'   => __( 'Auth Token', 'order-status-notification' ),
            'desc'    => __( 'To view API credentials visit <a href="https://console.plivo.com/dashboard/">https://console.plivo.com/dashboard/</a>', 'order-status-notification' ),
            'type'    => 'password',
            'id'      => 'osn_plivo_token',
          ),

          array(
            'title'   => __( 'Source Phone Number', 'order-status-notification' ),
            'desc'    => __( 'Enter source phone number', 'order-status-notification' ),
            'type'    => 'text',
            'id'      => 'osn_plivo_number',
            'autoload'        => false,
            'desc_tip'        => true,
          ),
        
        array( 
          'type' => 'sectionend', 
          'id' => 'order_status_notification_options'
        ),

        )
      );
    }

    return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
  }

  public function osn_add_tabs() {
    $screen = get_current_screen();

    if ( isset( $_GET['page'] ) 
      && isset( $_GET['tab'] )
      && $_GET['page'] == 'wc-settings'
      && $_GET['tab']
      && $_GET['tab'] == 'order_status_notification' ) {

      $screen->add_help_tab(
        array(
          'id'      => 'osn_support_tab',
          'title'   => __( 'Order Notification Variable', 'woocommerce' ),
          'content' =>
            '<h2>' . __( 'Order Notification Variable', 'woocommerce' ) . '</h2>' .
            '<p>' . sprintf(
              __( '{ORDER_NUMBER}, using, or extending WooCommerce, <a href="%s">please read our documentation</a>. You will find all kinds of resources including snippets, tutorials and much more.', 'woocommerce' ),
              'https://docs.woocommerce.com/documentation/plugins/woocommerce/?utm_source=helptab&utm_medium=product&utm_content=docs&utm_campaign=woocommerceplugin'
            ) . '</p>',
        )
      );

    }
    
  }


}
return new WC_Settings_Order_Status_Notification();

endif;