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

new Product_filters;