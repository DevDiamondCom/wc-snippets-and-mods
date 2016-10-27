<?php
/**
 * WCSAM Core (Main Class)
 *
 * @class    WCSAM
 * @author   DevDiamond <me@devdiamond.com>
 * @package  WC_Snippets_And_Mods
 * @version  1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Load Main WCSAM Class
if ( ! class_exists( 'WCSAM' ) ) :

/**
 * Class WCSAM - Main Class (Core).
 */
final class WCSAM
{
	const OPTIONS_PREFIX = 'WCSAM_';

	/**
	 * Modules list
	 *
	 * @var array
	 */
	public $modules = array();

	/**
	 * The single instance of the class.
	 *
	 * @static
	 * @var WCSAM
	 */
	protected static $_instance = null;

	/**
	 * Main WCSAM Instance.
	 *
	 * Ensures only one instance of WCSAM is loaded or can be loaded.
	 *
	 * @static
	 * @see WCSAM()
	 * @return WCSAM - Main instance.
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
	 * WCSAM Constructor.
	 */
	public function __construct()
	{
		$this->includes();
		$this->init_hooks();
		$this->init_modules();

		do_action('wcsam_loaded');
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks()
	{
		// activation and deactivation hook
		register_activation_hook( WCSAM_PLUGIN_FILE, array( $this, 'plugin_activation' ) );
		register_deactivation_hook( WCSAM_PLUGIN_FILE, array( $this, 'plugin_deactivation' ) );

		// init action
		add_action('init', array( $this, 'init' ), 0 );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes()
	{
		if ( $this->is_request('admin') )
			require_once('admin/class.wcsam-admin.php');
	}

	/**
	 * Modules and Extensions
	 */
	private function init_modules()
	{
		$dir = glob( WCSAM_MODULES_DIR . '*', GLOB_ONLYDIR);
		if ( ! is_array($dir)  )
			return;

		$modules = (array) $this->get_option('wcsam', 'extensions', 'extensions', array());

		foreach ( $dir as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . 'index.php';

			if ( ! file_exists($file) )
				continue;

			$mName = md5($path);
			if ( in_array($mName, $modules) )
			{
				$this->modules[ $mName ] = true;
				include_once $file;
			}
			else
				$this->modules[ $mName ] = false;
		}
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
	 * Init WCSAM when WordPress Initialises.
	 */
	public function init()
	{
		// Set up localisation.
		$this->load_plugin_textdomain();

		// Init action.
		do_action( 'wcsam_init' );
	}

	/**
	 * Load Localisation files.
	 */
	private function load_plugin_textdomain()
	{
		if ( ! is_textdomain_loaded( WCSAM_PLUGIN_SLUG ) )
			load_plugin_textdomain( WCSAM_PLUGIN_SLUG, false, basename( WCSAM_PLUGIN_DIR ) . '/languages' );
	}

	/**
	 * WCSAM activation
	 */
	public function plugin_activation()
	{
		return;
	}

	/**
	 * WCSAM deactivation
	 */
	public function plugin_deactivation()
	{
		return;
	}

	/**
	 * Get option
	 *
	 * @param  string $page_slug    - Page slug
	 * @param  string $tab_slug     - Tab slug
	 * @param  string $option_name  - Option name
	 * @param  bool   $default      - Default option
	 *
	 * @return mixed
	 */
	public function get_option( $page_slug, $tab_slug, $option_name = null, $default = false )
	{
		$option = get_option( self::OPTIONS_PREFIX . $page_slug . '_' . $tab_slug );

		if ( false === $option )
			return $default;

		if ( $option_name === null )
			return $option;
		elseif ( isset( $option[ $option_name ] ) )
			return $option[ $option_name ];
		else
			return $default;
	}
}

endif;

/**
 * Main instance of WCSAM.
 *
 * @return WCSAM
 */
function WCSAM()
{
	return WCSAM::instance();
}
