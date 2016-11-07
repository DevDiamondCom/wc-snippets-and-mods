<?php

namespace WCSAM\modules\buy_one_click\shortcodes;

use WCSAM\modules\buy_one_click\frontend\Frontend;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * All BOC Shortcodes
 *
 * @class   Shortcodes
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\buy_one_click\shortcodes
 * @version 1.0.0
 */
class Shortcodes
{
	/**
	 * Shortcodes constructor.
	 */
	public function __construct()
	{
		add_shortcode('wcsam_buy_one_click_in_product', array($this, 'in_product'));
		add_shortcode('wcsam_buy_one_click_in_category', array($this, 'in_category'));
		add_shortcode('wcsam_buy_one_click_in_cart', array($this, 'in_cart'));
	}

	/**
	 * Buy one click in product page
	 */
	public function in_product()
	{
		ob_start();
		Frontend::in_product();
		return ob_get_clean();
	}

	/**
	 * Buy one click in category
	 */
	public function in_category()
	{
		ob_start();
		Frontend::in_category();
		return ob_get_clean();
	}

	/**
	 * Buy one click in cart
	 */
	public function in_cart()
	{
		ob_start();
		Frontend::in_cart();
		return ob_get_clean();
	}
}

new Shortcodes();