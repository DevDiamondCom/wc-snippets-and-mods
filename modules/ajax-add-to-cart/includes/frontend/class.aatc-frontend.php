<?php

namespace WCSAM\modules\ajax_add_to_cart\frontend;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Frontend
 *
 * @class   Frontend
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\ajax_add_to_cart\frontend
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
		add_action( 'wp_enqueue_scripts', array($this, 'add_scripts'), 99 );
	}

	/**
	 * Load Frontend includes
	 */
	private function init_includes()
	{
		require_once 'class.aatc-frontend-api.php';
	}

	/**
	 * Add Scripts
	 */
	public function add_scripts()
	{
		// Ajax Add to card
		wp_enqueue_script(
			'ajax-add-to-cart',
			AATC_ASSETS_URL . 'js/add-to-cart-variation.js',
			array('jquery'),
			'',
			true
		);
	}
}

new Frontend;