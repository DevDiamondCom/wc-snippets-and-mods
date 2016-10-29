<?php

namespace WCSAM\modules\product_filters\shortcodes;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * All PF Shortcodes
 *
 * @class   Shortcodes
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters\shortcodes
 * @version 1.0.0
 */
class Shortcodes
{
	/**
	 * Shortcodes constructor.
	 */
	public function __construct()
	{
		$this->init_includes();
	}

	/**
	 * Load Shortcodes includes
	 */
	private function init_includes()
	{
		require_once 'class.pf-shortcode-price.php';
	}
}

new Shortcodes;