<?php

namespace WCSAM;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Helper
 *
 * @class   Helper
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM
 * @version 1.0.0
 */
class Helper
{
	/**
	 * Get Woocommerce Attribute Taxonomies
	 *
	 * @static
	 * @return array
	 */
	public static function attribute_taxonomies()
	{
		// Check woocommerce base
		$_woocommerce = WC();
		if ( ! isset( $_woocommerce ) )
			return array();

		if ( function_exists( 'wc_get_attribute_taxonomies' ) )
			$attribute_taxonomies = wc_get_attribute_taxonomies();
		else
			$attribute_taxonomies = WC()->get_attribute_taxonomies();

		if ( empty( $attribute_taxonomies ) )
			return array();

		$attributes = array();
		foreach ( $attribute_taxonomies as $attribute )
		{
			/* FIX TO WOOCOMMERCE 2.1 */
			if ( function_exists( 'wc_attribute_taxonomy_name' ) )
				$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );
			else
				$taxonomy = WC()->attribute_taxonomy_name( $attribute->attribute_name );

			if ( taxonomy_exists( $taxonomy ) )
				$attributes[ $attribute->attribute_name ] = $attribute->attribute_label;
		}

		return $attributes;
	}

	/**
	 * Get choosen attribute args
	 *
	 * @static
	 */
	public static function get_layered_nav_chosen_attributes()
	{
		if ( WCSAM()->is_wc_older_2_6 )
		{
			global $_chosen_attributes;
			return $_chosen_attributes;
		}

		return \WC_Query::get_layered_nav_chosen_attributes();
	}

	/**
	 * Get product taxonomy
	 *
	 * @static
	 * @return array
	 */
	public static function get_product_taxonomy()
	{
		global $_attributes_array;

		$product_tax = ( !empty( $_attributes_array ) ? $_attributes_array : get_object_taxonomies( 'product' ));

		return array_merge( $product_tax, apply_filters( 'wcsam_product_taxonomy_type', array() ) );
	}
}
