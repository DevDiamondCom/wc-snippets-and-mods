<?php

namespace WCSAM\modules\ajax_add_to_cart\frontend;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Frontend - Admin Panel Settings
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
	}

	/**
	 * Added hooked's
	 */
	private function init_hooked()
	{
		// Add Scripts and Styles
		add_action( 'wp_enqueue_scripts', array($this, 'add_scripts'), 99 );

		// Ajax add to cart variable added
		add_action( 'wp_ajax_wcsam_module_add_to_cart_variable', array($this, 'ajax_wcsam_module_add_to_cart_variable') );
		add_action( 'wp_ajax_nopriv_wcsam_module_add_to_cart_variable',  array($this, 'ajax_wcsam_module_add_to_cart_variable') );
	}

	/**
	 * Ajax add to cart variable added
	 */
	public function ajax_wcsam_module_add_to_cart_variable()
	{
		ob_start();

		$product_id         = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$adding_to_cart     = wc_get_product( $product_id );
		$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
		$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
		$missing_attributes = array();
		$variations         = array();
		$attributes         = $adding_to_cart->get_attributes();

		// If no variation ID is set, attempt to get a variation ID from posted attributes.
		if ( empty( $variation_id ) )
			$variation_id = $adding_to_cart->get_matching_variation( wp_unslash( $_POST ) );

		$variation = wc_get_product( $variation_id );

		// Verify all attributes
		foreach ( $attributes as $attribute )
		{
			if ( ! $attribute['is_variation'] )
				continue;

			$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( isset( $_REQUEST[ $taxonomy ] ) )
			{
				// Get value from post data
				if ( $attribute['is_taxonomy'] )
					$value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
				else
					$value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );

				// Get valid value from variation
				$valid_value = isset( $variation->variation_data[ $taxonomy ] ) ? $variation->variation_data[ $taxonomy ] : '';

				// Allow if valid
				if ( '' === $valid_value || $valid_value === $value )
				{
					$variations[ $taxonomy ] = $value;
					continue;
				}

			} else {
				$missing_attributes[] = wc_attribute_label( $attribute['name'] );
			}
		}

		if ( empty( $missing_attributes ) && ! empty( $variation_id ) )
		{
			// Add to cart validation
			$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

			if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) !== false )
			{
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );
				if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' )
					wc_add_to_cart_message( $product_id );

				// Return fragments
				\WC_AJAX::get_refreshed_fragments();

				die();
			}
		}

		wp_send_json( array(
			'error' => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
		));
		die();
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