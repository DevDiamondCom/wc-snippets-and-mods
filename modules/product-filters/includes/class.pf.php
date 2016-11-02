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
		$this->init_includes();
	}

	/**
	 * Initialization Module files
	 */
	private function init_includes()
	{
		if ( is_admin() )
			require_once 'admin/class.pf-admin.php';

		require_once 'shortcodes/class.pf-shortcode.php';
	}
}

Product_filters::instance();