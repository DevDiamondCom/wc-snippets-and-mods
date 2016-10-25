<?php
/**
 * WOOSM Core (Main Class)
 *
 * @class    WOOSM
 * @author   DevDiamond <me@devdiamond.com>
 * @package  WOOSM
 * @version  1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Load Main WOOSM Class
if ( ! class_exists( 'WOOSM' ) ) :

/**
 * Class WOOSM - Main Class (Core).
 */
final class WOOSM
{
	/**
	 * The single instance of the class.
	 *
	 * @var WOOSM
	 */
	protected static $_instance = null;

	/**
	 * Main WOOSM Instance.
	 *
	 * Ensures only one instance of WOOSM is loaded or can be loaded.
	 *
	 * @static
	 * @see WPTS()
	 * @return WOOSM - Main instance.
	 */
	public static function instance()
	{
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone(){}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup(){}

	/**
	 * WOOSM Constructor.
	 */
	public function __construct()
	{
		$this->includes();
		$this->init_hooks();

		do_action('woosm_loaded');
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks()
	{
		// activation and deactivation hook
		register_activation_hook( WPTS_PLUGIN_FILE, array( $this, 'plugin_activation' ) );
		register_deactivation_hook( WPTS_PLUGIN_FILE, array( $this, 'plugin_deactivation' ) );

		// init action
		add_action('init', array( $this, 'init' ), 0 );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes()
	{
//		if ( $this->is_request('admin') )
//			require_once('admin/class.woosm-admin.php');
	}

	/**
	 * Check the type of request
	 *
	 * @param  string $type  - admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type )
	{
		switch ( $type )
		{
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
		return false;
	}

	/**
	 * Init WOOSM when WordPress Initialises.
	 */
	public function init()
	{
		// Set up localisation.
		$this->load_plugin_textdomain();

		// Init action.
		do_action( 'woosm_init' );
	}

	/**
	 * Load Localisation files.
	 */
	private function load_plugin_textdomain()
	{
		if ( ! is_textdomain_loaded( WOOSM_PLUGIN_SLUG ) )
			load_plugin_textdomain( WOOSM_PLUGIN_SLUG, false, basename( WOOSM_PLUGIN_DIR ) . '/languages' );
	}

	/**
	 * WOOSM activation
	 */
	public function plugin_activation()
	{
		return;
	}

	/**
	 * WOOSM deactivation
	 */
	public function plugin_deactivation()
	{
		return;
	}
}

endif;

/**
 * Main instance of WOOSM.
 *
 * @return WOOSM
 */
function WOOSM()
{
	return WOOSM::instance();
}
