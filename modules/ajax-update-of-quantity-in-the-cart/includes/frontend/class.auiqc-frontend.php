<?php

namespace WCSAM\modules\ajax_update_qty_in_the_cart\frontend;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Frontend
 *
 * @class   Frontend
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\ajax_update_qty_in_the_cart\frontend
 * @version 1.0.0
 */
class Frontend
{
	const NONCE_SLUG = 'dL5gD48Wd6h86';

	/**
	 * Frontend constructor.
	 */
	public function __construct()
	{
		add_action('wp_loaded', array($this, 'init'));
		add_action('woocommerce_cart_loaded_from_session', array($this, 'action_qty_update'));
	}

	/**
	 * Init WP
	 */
	public function init()
	{
		$this->init_hooks();
	}

	/**
	 * Init hooks for Frontend
	 */
	private function init_hooks()
	{
		add_action('wp_enqueue_scripts', array( $this, 'add_scripts' ));
	}

	/**
	 * Action - Quantity update
	 */
	public function action_qty_update()
	{
		// action qty update (mini API)
		if ( !isset($_POST['auiqc_action']) || !$_POST['auiqc_action'] === 'quantity' )
			return;

		// Check form NONCE
		if ( !isset($_POST['auiqc_id']) || !wp_verify_nonce($_POST['auiqc_id'], 'wcsam_auiqc_'. self::NONCE_SLUG) )
			die('ERROR');

		if ( !isset($_POST['qty_name']) || empty($_POST['qty_name']) ||
			 !isset($_POST['qty_val']) || trim($_POST['qty_val']) === '' )
			die('ERROR');

		$qty_name = esc_html( $_POST['qty_name'] );
		$qty_val  = intval( $_POST['qty_val'] );

		if ( !preg_match('/^cart\[([\w]+)\]\[qty\]$/', $qty_name, $session_id) )
			die('ERROR');

		$qty_name = $session_id[1];

		if ( $qty_val < 1 )
			$qty_val = 1;

		if ( get_current_user_id() )
		{
			$saved_cart = @get_user_meta( get_current_user_id(), '_woocommerce_persistent_cart', true )['cart'];

			if ( empty($saved_cart) || !isset($saved_cart[ $qty_name ]) )
				die('ERROR');
		}

		if ( !WC()->cart->set_quantity( $qty_name, $qty_val ))
			die('ERROR');
	}

	/**
	 * Add Frontend Scripts
	 */
	public function add_scripts()
	{
		if ( ! is_cart() )
			return;

		// AUIQC Script
		wp_enqueue_script(
			'auiqc',
			AUIQC_ASSETS_URL . 'js/auiqc.js',
			array('jquery'),
			null,
			true
		);
		$inline_data = array(
			'auiqc_id'   => wp_create_nonce('wcsam_auiqc_'. self::NONCE_SLUG),
			'loader_img' => WCSAM_ASSETS_URL . 'img/ajax-loader-balls_150.gif',
		);
		wp_add_inline_script(
			'auiqc',
			'var auiqc = ' . json_encode($inline_data) .';',
			'before'
		);
	}

}

new Frontend();