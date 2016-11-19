<?php

namespace WCSAM\frontend;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Frontend
 *
 * @class   Frontend
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\frontend
 * @version 1.0.0
 */
class Frontend
{
	/**
	 * Frontend constructor.
	 */
	public function __construct()
	{
		$this->init_hooked();
		$this->init_includes();
	}

	/**
	 * Added hooked's
	 */
	private function init_hooked()
	{
		// Add Scripts and Styles
		add_action( 'wp_enqueue_scripts', array($this, 'add_scripts'), 11 );
	}

	/**
	 * Load Frontend includes
	 */
	private function init_includes()
	{
		//
	}

	/**
	 * Add Scripts
	 */
	public function add_scripts()
	{
		// WCSAM Main Frontend script
		wp_enqueue_script(
			'wcsam-main',
			WCSAM_ASSETS_URL .'js/wcsam-main.js',
			array('jquery'),
			WCSAM_VERSION,
			false
		);
		wp_localize_script( 'wcsam-main', 'wcsam_data', array(
			'currency'           => get_woocommerce_currency(),
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimals'           => wc_get_price_decimals(),
			'price_format'       => get_woocommerce_price_format(),
		));
	}
}

new Frontend;