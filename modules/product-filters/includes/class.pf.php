<?php

namespace WCSAM\modules\product_filters;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Product_filters - Product filters, prices, colors, etc
 *
 * @class   Product_filters
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters
 * @version 1.0.0
 */
class Product_filters
{
	const MODULE_NAME = 'product-filters';

	/**
	 * The single instance of the class.
	 *
	 * @static
	 * @var Product_filters
	 */
	protected static $_instance = null;

	/**
	 * Main Ajax_Add_To_Cart Instance.
	 *
	 * @static
	 * @return Product_filters - Main instance.
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
	 * Product_filters constructor.
	 */
	public function __construct()
	{
		if ( ! is_null( self::$_instance ) )
			return;

		$this->add_defines();
		$this->init_hooks();
		$this->init_includes();
	}

	/**
	 * Defines
	 */
	private function add_defines()
	{
		define('PF_ASSETS_URL', WCSAM_MODULES_URL . self::MODULE_NAME .'/assets/');
	}

	/**
	 * Initialization hooks
	 */
	private function init_hooks()
	{
		add_action( 'widgets_init', array($this, 'register_widgets') );
	}

	/**
	 * Initialization Module files
	 */
	private function init_includes()
	{
		require_once 'abstracts/abstract.pf-widgets.php';
		require_once 'widgets/class.pf-widget-product-filters.php';
		require_once 'widgets/class.pf-widget-price-filters.php';
		require_once 'widgets/class.pf-widget-reset-filters.php';
		require_once 'shortcodes/class.pf-shortcode.php';

		if ( is_admin() )
			require_once 'admin/class.pf-admin.php';
	}

	/**
	 * Register widgets
	 */
	public function register_widgets()
	{
		register_widget( 'WCSAM\modules\product_filters\widgets\Widget_Product_Filters' );
		register_widget( 'WCSAM\modules\product_filters\widgets\Widget_Price_Filters' );
		register_widget( 'WCSAM\modules\product_filters\widgets\Widget_Reset_Filters' );
	}

	/**
	 * Get Param - Notification Tab
	 *
	 * @param mixed  $default - Return default param
	 * @param string $param   - Param name {
	 *      Params
	 *      @uses [update_container_name]  - Updated container name (#category_main or .category_main etc)
	 *      @uses [loader_img_url]         - Loader image URL
	 * }
	 *
	 * @return mixed
	 */
	public function getP_widgets( $param = '', $default = false )
	{
		static $get_param_w;

		if ( !isset($get_param_w) )
			$get_param_w = wcsam_get_option('product_filters', 'widgets');

		if ( ! $param )
			return $get_param_w;

		if ( isset( $get_param_w[ $param ] ) )
			return $get_param_w[ $param ];
		else
			return $default;
	}
}