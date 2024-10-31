<?php
/**
 * Order_Status_Notification setup
 *
 * @package Order_Status_Notification
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Order_Status_Notification Class.
 *
 * @class Order_Status_Notification
 */
final class Order_Status_Notification {

  /**
   * Order_Status_Notification version.
   *
   * @var string
   */
  public $version = '1.0';

  /**
   * The single instance of the class.
   *
   * @var Order_Status_Notification
   * @since 1.0
   */
  protected static $_instance = null;

  /**
   * Main Order_Status_Notification Instance.
   *
   * Ensures only one instance of Order_Status_Notification is loaded or can be loaded.
   *
   * @since 1.0
   * @static
   * @return Order_Status_Notification - Main instance.
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }


  /**
   * Order_Status_Notification Constructor.
   */
  public function __construct() {
    if ( OSN_Dependencies::is_woocommerce_active() ) {
      $this->define_constants();
      $this->includes();
      do_action( 'order_status_notification_loaded' );
    } else {
      add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
    }
  }

  /**
   * Define OSN Constants.
   */
  private function define_constants() {
    $this->define( 'OSN_ABSPATH', dirname( OSN_PLUGIN_FILE ) . '/' );
    $this->define( 'OSN_PLUGIN_BASENAME', plugin_basename( OSN_PLUGIN_FILE ) );
    $this->define( 'OSN_VERSION', $this->version );
  }

  /**
   * Define constant if not already set.
   *
   * @param string      $name  Constant name.
   * @param string|bool $value Constant value.
   */
  private function define( $name, $value ) {
    if ( ! defined( $name ) ) {
      define( $name, $value );
    }
  }

  /**
   * When WP has loaded all plugins, trigger the `osn_loaded` hook.
   *
   * This ensures `osn_loaded` is called only after all other plugins
   * are loaded, to avoid issues caused by plugin directory naming changing
   *
   * @since 1.0
   */
  public function on_plugins_loaded() {
    do_action( 'osn_loaded' );
  }

  /**
   * What type of request is this?
   *
   * @param  string $type admin, ajax, cron or frontend.
   * @return bool
   */
  private function is_request( $type ) {
    switch ( $type ) {
      case 'admin':
        return is_admin();
      case 'ajax':
        return defined( 'DOING_AJAX' );
      case 'frontend':
        return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
    }
  }

  /**
   * Include required core files used in admin and on the frontend.
   */
  public function includes() {
    /**
     * Core classes.
     */
    include_once OSN_ABSPATH . 'includes/admin/class-osn-service.php';
    include_once OSN_ABSPATH . 'includes/osn-core-functions.php';
    include_once OSN_ABSPATH . 'includes/class-osn-install.php';

    include_once OSN_ABSPATH . 'includes/class-osn-actions.php';

    if ( $this->is_request( 'admin' ) ) {
      include_once OSN_ABSPATH . 'includes/admin/class-osn-admin.php';
    }
  }

  /**
   * Include required frontend files.
   */
  public function frontend_includes() {
    include_once OSN_ABSPATH . 'includes/class-osn-frontend.php';
    include_once OSN_ABSPATH . 'includes/class-osn-frontend-scripts.php';
  }

  /**
   * Init Order_Status_Notification when WordPress Initialises.
   */
  public function init() {
    
    // Set up localisation.
    $this->load_plugin_textdomain();
  }

  /**
   * Load Localisation files.
   *
   * Note: the first-loaded translation file overrides any following ones if the same translation is present.
   */
  public function load_plugin_textdomain() {
    load_plugin_textdomain( 
      'order-status-notification', 
      false, 
      dirname( plugin_basename( OSN_PLUGIN_FILE ) ). '/languages/' 
    );
  }
  
  /**
   * Get the plugin url.
   *
   * @return string
   */
  public function plugin_url() {
    return untrailingslashit( plugins_url( '/', OSN_PLUGIN_FILE ) );
  }

  /**
   * Get the plugin path.
   *
   * @return string
   */
  public function plugin_path() {
    return untrailingslashit( plugin_dir_path( OSN_PLUGIN_FILE ) );
  }

  /**
   * Display admin notice
     */
  public function admin_notices() {

    echo '<div class="error"><p>';
    _e( 'Order Status Notification requires <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> plugins to be active!', 'order-status-notification' );
    echo '</p></div>';
  }
}