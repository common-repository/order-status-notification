<?php
/**
 * Load assets
 *
 * @package Order Status Notification/Admin
 * @version 1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'OSN_Admin_Assets', false ) ) :

  /**
   * OSN_Admin_Assets Class.
   */
  class OSN_Admin_Assets {

    /**
     * Hook in tabs.
     */
    public function __construct() {
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }

    /**
     * Enqueue styles.
     */
    public function admin_styles() {

      $version      = OSN()->version;

      // Register admin styles.
      wp_register_style( 'osn_toast', OSN()->plugin_url() . '/assets/css/jquery.toast.css', array(), $version );
      wp_register_style( 'osn_admin', OSN()->plugin_url() . '/assets/css/osn-admin.css', array(), $version );

      if ( isset( $_GET['page'] ) 
        && $_GET['page'] == 'wc-settings'
        && isset( $_GET['tab'] )
        && $_GET['tab'] == 'order_status_notification'
      ) {
        wp_enqueue_style( 'osn_toast' );
        wp_enqueue_style( 'osn_admin' );
      }

    }


    /**
     * Enqueue scripts.
     */
    public function admin_scripts() {

      $version      = OSN()->version;

      // Register scripts.
      wp_register_script( 'osn_toast', OSN()->plugin_url() . '/assets/js/jquery.toast.js', array( 'jquery' ), $version );

      wp_register_script( 'osn_admin', OSN()->plugin_url() . '/assets/js/osn-admin.js', array( 'jquery', 'osn_toast' ), $version );

      wp_localize_script(
        'osn_admin',
        'osn_admin_params',
        array(
          'ajax_url'                  => admin_url( 'admin-ajax.php' ),
          'test_notification_nonce'   => wp_create_nonce( 'test-notification' ),
          'error_msg_heading'                 => __( 'Error', 'order-status-notification' ),
          'success_msg_heading'               => __( 'Success', 'order-status-notification' ),
          'success_msg'                       => __( 'Message sent successfully', 'order-status-notification' ),

        )
      );

      if ( isset( $_GET['page'] ) 
        && $_GET['page'] == 'wc-settings'
        && isset( $_GET['tab'] )
        && $_GET['tab'] == 'order_status_notification'
      ) {
        wp_enqueue_script( 'osn_admin' );
        wp_enqueue_script( 'osn_toast' );
      }

    }

  }

endif;

return new OSN_Admin_Assets();
