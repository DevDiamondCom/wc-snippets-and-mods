<?php

namespace WCSAM\modules\ajax_update_qty_in_the_cart;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AUIQC - Ajax update of quantity in the cart
 *
 * @class   AUIQC
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\ajax_update_qty_in_the_cart
 * @version 1.0.0
 */
class AUIQC
{
	const MODULE_NAME = 'ajax-update-of-quantity-in-the-cart';

	/**
	 * The single instance of the class.
	 *
	 * @static
	 * @var AUIQC
	 */
	protected static $_instance = null;

	/**
	 * Main Ajax_Add_To_Cart Instance.
	 *
	 * @static
	 * @return AUIQC - Main instance.
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
	 * AUIQC constructor.
	 */
	public function __construct()
	{
		if ( ! is_null( self::$_instance ) )
			return;

		$this->add_defines();
		$this->init_includes();
	}

	private function add_defines()
	{
		define('AUIQC_ASSETS_URL', WCSAM_MODULES_URL . self::MODULE_NAME .'/assets/');
	}

	/**
	 * Initialization Module files
	 */
	private function init_includes()
	{
		require_once 'frontend/class.auiqc-frontend.php';

		if ( is_admin() )
			require_once 'admin/class.auiqc-admin.php';
	}
}
