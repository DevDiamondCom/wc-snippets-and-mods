<?php
/**
 * Functions
 *
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\ajax_add_to_cart
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if access d directly.

/**
 * Check option - Add selection option to Category Page
 */
if ( wcsam_get_option('ajax_add_to_card', 'general', 'is_variation_category_page', false) )
{
	if ( ! function_exists( 'woocommerce_template_loop_add_to_cart' ) )
	{
		/**
		 * Loop Add to card
		 *
		 * @param array $args
		 */
		function woocommerce_template_loop_add_to_cart( $args = array() )
		{
			global $product;

			if ( $product )
			{
				$defaults = array(
					'quantity' => 1,
					'class'    => implode( ' ', array_filter( array(
						'button',
						'product_type_' . $product->product_type,
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : ''
					) ) )
				);

				$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

				if ($product->product_type == "variable" )
					woocommerce_variable_add_to_cart();
				else
					wc_get_template( 'loop/add-to-cart.php', $args );
			}
		}
	}
}