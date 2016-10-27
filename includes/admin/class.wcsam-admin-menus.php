<?php
/**
 * Setup menus in WP admin
 *
 * @class    Admin_Menus
 * @author   DevDiamond <me@devdiamond.com>
 * @package  WC_Snippets_And_Mods/Admin
 * @version  1.0.0
 */

namespace WCSAM\admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Menus - Create WCSAM Menus
 */
class Admin_Menus
{
	const ASSETS_FRONT_CSS = 'assets/css/';
	const ASSETS_ADMIN_CSS = 'assets/admin/css/';

	const ASSETS_FRONT_JS  = 'assets/js/';
	const ASSETS_ADMIN_JS  = 'assets/admin/js/';

	const ASSETS_ADMIN_IMG = 'assets/admin/img/';

	const MAIN_MENU_SLUG       = 'wcsam';
	const MAIN_MENU_CAPABILITY = 'manage_options';

	/**
	 * Sub menu list for WCSAM (API)
	 *
	 * @var array
	 */
	public static $submenu = array();

	/**
	 * Admin_Menus constructor.
	 */
	public function __construct()
	{
		// Add menu
		add_action('admin_menu', array( $this, 'admin_menu' ), 9);

		// Get Sub Menu list (API)
		self::$submenu = (array) apply_filters('wcsam_submenu', array());

//		add_action( 'admin_head', array( $this, '' ) );
//		add_action( 'admin_bar_menu', array( $this, '' ) );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu()
	{
		// Main menu
		add_menu_page(
			__('General Settings', WCSAM_PLUGIN_SLUG),
			__('WС_SAM', WCSAM_PLUGIN_SLUG),
			self::MAIN_MENU_CAPABILITY,
			self::MAIN_MENU_SLUG,
			null,
			'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAp9JREFUeNqklM9LVFEUx78zTj9mstRUUCeHpE1Qi/4EoV1QK4t+EGYZFk5YUVFG0Q8TQoiIdtGmDFokFtaqQCRa5TJoFWSR5aCOyui8+Xn63rn39e6MigNd+Lx3z33nnnfuOeceXwSCMsYASZKbaykGyjDWRy6ZuUP6yzWo5mHiJ8ptHzlNriWNsBG4x1cFGbR0cuSXecNnHXk7GSdBd4FfQglltRNYmANevgK26E9LliMLZA+ZKvVQ/a2SbDDGkKUvnR3A/SdAJk05D4wMU0EQsvblzd7C8JeEIOdOMqSZAbjzSMvr1vO8j4GtNTS8yp5Sg47rmTLG/Zj4AfQcB9IpfeToMWB2VgcxU2zUWSkp52gsqBS3NQI/f+uzPx8CqquB+AzwbhSoM8bCno4KU9RUA6CSQvqIVJOL7SJTkyInD4gEKe+qE/n8UeTDW5GWkMgmrl0+pXXa94nUUI5oegsJ5mNALdQrYx3yb6TTlLlx/JO39v6NyPWoJ6cckRP79V5j9LbfLgGHBZc1wVFZXVwkCS8mar7EtWzW00k5VopVLM2Re90jn2kTmfgmcmSvSIjyjkoed0Rk6JlIOKCP3H1Y6xxq1XuMd+eVLbuwb5Bb/Km/nqURi+ukzKh718OkTANPXwC1lKkDS0eVzRXywE6KooEsqr81kWbSWCFytUvHy2G8ug6KNPj0tybPszlS5dqxPWwhX0jhFigvIhFg7CvvsLkXsUmgdTcwHy+qt3myk/wpLWyfXZeqsL+zsLtZzDkmIcEbe/YoCzu+rEUFVhNyJmQht5Owu1QNv+akTRscHQM2e1653cado7TbqBtVi6IqKLSvu1b7gumNg5aOMjBtmsQyD2MrNNd8UPdBNS6Qh//bsftNS0uuZUyNvwIMAJy1Xn0pD6IaAAAAAElFTkSuQmCC',
			'56.1'
		);

		// Main sub menu
		self::$submenu[ self::MAIN_MENU_SLUG ] = array(
			'page_title' => __('General Settings', WCSAM_PLUGIN_SLUG),
			'menu_title' => __('General Settings', WCSAM_PLUGIN_SLUG),
			'capability' => self::MAIN_MENU_CAPABILITY,
		);

		// Add all sub menu
		$this->sub_menu();

		// Add Style and Script
		if ( self::check_current_page() !== false )
		{
			$this->add_styles();
			$this->add_scripts();
		}
	}

	/**
	 * Add Sub Menu list
	 */
	private function sub_menu()
	{
		// Main sub menu
		add_submenu_page(
			self::MAIN_MENU_SLUG,
			self::$submenu[ self::MAIN_MENU_SLUG ]['page_title'],
			self::$submenu[ self::MAIN_MENU_SLUG ]['menu_title'],
			self::$submenu[ self::MAIN_MENU_SLUG ]['capability'],
			self::MAIN_MENU_SLUG,
			array( 'WCSAM\admin\Admin_Menu_Pages', 'admin_menu_pages' )
		);

		// Other all sub menu
		foreach ( self::$submenu as $sKey => $sVal )
		{
			if ( ! preg_match('/[\w-]+/', $sKey ) || ! trim($sVal['page_title']) || $sKey === self::MAIN_MENU_SLUG )
				continue;

			if ( ! trim($sVal['capability']) )
				self::$submenu[ $sKey ]['capability'] = $sVal['capability'] = 'manage_options';
			if ( ! current_user_can( $sVal['capability'] ) )
				continue;

			if ( ! trim($sVal['menu_title']) )
				$sVal['menu_title'] = $sVal['page_title'];

			add_submenu_page(
				self::MAIN_MENU_SLUG,
				$sVal['page_title'],
				$sVal['menu_title'],
				$sVal['capability'],
				self::MAIN_MENU_SLUG.'-'.$sKey,
				array( 'WCSAM\admin\Admin_Menu_Pages', 'admin_menu_pages' )
			);
		}
	}

	/**
	 * Check Current Page
	 *
	 * @static
	 * @return bool|string - false or page_slug
	 */
	public static function check_current_page()
	{
		if ( ! isset($_GET['page']) )
			return false;

		if ( $_GET['page'] !== self::MAIN_MENU_SLUG )
			$page_slug = str_replace(self::MAIN_MENU_SLUG.'-', '', $_GET['page']);
		else
			$page_slug = $_GET['page'];

		if ( ! isset( self::$submenu[ $page_slug ] ) )
			return false;

		if ( ! current_user_can( self::$submenu[ $page_slug ]['capability'] ) )
			return false;

		return $page_slug;
	}

	/**
	 * Add Scripts
	 */
	private function add_scripts()
	{
		// Toggle JS
		wp_enqueue_script(
			'toggle-min',
			WCSAM_PLUGIN_URL . self::ASSETS_FRONT_JS . 'toggles.min.js',
			array('jquery')
		);

		// DD JS Script
		wp_enqueue_script(
			'dd-script',
			WPTS_PLUGIN_URL . self::ASSETS_ADMIN_JS . 'dd-script.js',
			array('jquery')
		);

		// Main JS
		wp_enqueue_script(
			'wcsam-main',
			WCSAM_PLUGIN_URL . self::ASSETS_ADMIN_JS . 'admin-main.js',
			array('jquery')
		);
	}

	/**
	 * Add Styles
	 */
	private function add_styles()
	{
		// FontAwesome Styles
		wp_enqueue_style(
			'fontawesome',
			WCSAM_PLUGIN_URL . self::ASSETS_FRONT_CSS . 'font-awesome.min.css'
		);

		// DD Style
		wp_enqueue_style(
			'dd-style',
			WPTS_PLUGIN_URL . self::ASSETS_ADMIN_CSS . 'dd-style.css'
		);

		// Main CSS
		wp_enqueue_style(
			'wcsam-main',
			WCSAM_PLUGIN_URL . self::ASSETS_ADMIN_CSS . 'admin-main.css'
		);
	}
}

new Admin_Menus();
