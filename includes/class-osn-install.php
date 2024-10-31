<?php
/**
 * Installation related functions and actions.
 *
 * @package Order_Status_Notification/Classes
 * @version 1.0
 */


defined( 'ABSPATH' ) || exit;

/**
 * OSN_Install Class.
 */
class OSN_Install {

  /**
   * Hook in tabs.
   */
  public static function init() {
    add_filter( 'plugin_action_links_' . OSN_ABSPATH, array( __CLASS__, 'plugin_action_links' ) );

    //Add backend settings
    add_filter( 'woocommerce_get_settings_pages', array( __CLASS__, 'osn_settings_class' ) );
  }


  /**
   * Show action links on the plugin screen.
   *
   * @param mixed $links Plugin Action links.
   *
   * @return array
   */
  public static function plugin_action_links( $links ) {
    $action_links = array(
      'settings' => '<a href="' . admin_url( 'admin.php?page=osn-settings' ) . '" aria-label="' . esc_attr__( 'View Order Status Notification settings', 'order-status-notification' ) . '">' . esc_html__( 'Settings', 'order-status-notification' ) . '</a>',
    );

    return array_merge( $action_links, $links );
  }


  /**
   * Add settings class.
   *
   * @param array $settings Plugin settings.
   *
   * @return array
   */
  public static function osn_settings_class( $settings ) {
    $settings[] = include 'admin/class-wc-settings-order-status-notification.php';
    return $settings;
  }

}

OSN_Install::init();
