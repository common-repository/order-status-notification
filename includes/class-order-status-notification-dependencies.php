<?php

/**
 * OrderStatusNotification Dependency
 *
 * @package OrderStatusNotification
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'OSN_Dependencies' ) ) {

  class OSN_Dependencies {

    private static $active_plugins;

    public static function init() {
      self::$active_plugins = (array) get_option( 'active_plugins', array() );
      
      if ( is_multisite() ) {
        self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
      }
    }

    /**
    * Check woocommerce exist
    * @return Boolean
    */
    public static function woocommerce_active_check() {
      if ( !self::$active_plugins ) {
        self::init();
      }
      
      return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
    }

    /**
    * Check if woocommerce active
    * @return Boolean
    */
    public static function is_woocommerce_active() {
      return self::woocommerce_active_check();
    }
        
    /**
    * Check woocommerce exist
    * @return Boolean
    */
    public static function order_status_notification_active_check() {
    
      if ( !self::$active_plugins ) {
        self::init();
      }

      return in_array( 'order-status-notification/order-status-notification.php', self::$active_plugins) || array_key_exists('order-status-notification/order-status-notification.php', self::$active_plugins);
    }

    /**
    * Check if OrderStatusNotification active
    * @return Boolean
    */
    public static function is_order_status_notification_active() {
      return self::order_status_notification_active_check();
    }
  }
}