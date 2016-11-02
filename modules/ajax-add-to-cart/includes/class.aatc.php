<?php

namespace WCSAM\modules\ajax_add_to_cart;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Ajax_Add_To_Cart - Product filters, prices, colors, etc
 *
 * @class   Ajax_Add_To_Cart
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\ajax_add_to_cart
 * @version 1.0.0
 */
class Ajax_Add_To_Cart
{
	const MODULE_NAME = 'ajax-add-to-cart';

	/**
	 * The single instance of the class.
	 *
	 * @static
	 * @var Ajax_Add_To_Cart
	 */
	protected static $_instance = null;

	/**
	 * Main Ajax_Add_To_Cart Instance.
	 *
	 * @static
	 * @return Ajax_Add_To_Cart - Main instance.
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
	 * Ajax_Add_To_Cart constructor.
	 */
	public function __construct()
	{
		$this->add_defines();
		$this->init_includes();
	}

	private function add_defines()
	{
		define('AATC_ASSETS_URL', WCSAM_MODULES_URL . self::MODULE_NAME .'/assets/');
	}

	/**
	 * Initialization Module files
	 */
	private function init_includes()
	{
		require_once 'functions.php';
		require_once 'frontend/class.aatc-frontend.php';

		if ( is_admin() )
			require_once 'admin/class.aatc-admin.php';
	}
}

Ajax_Add_To_Cart::instance();