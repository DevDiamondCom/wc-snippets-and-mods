<?php

namespace WCSAM\abstracts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Abstract_Querys - Abstract Querys DB Class
 *
 * @class   Abstract_Querys
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\abstracts
 * @version 1.0.0
 */
abstract class Abstract_Querys
{
	/**
	 * Get Term IDs
	 *
	 * @param object|int|string $term      - The term object, ID, or slug whose link will be retrieved.
	 * @param string            $taxonomy  - Taxonomy name (slug) (default: product_cat)
	 *
	 * @return array|false
	 */
	protected function get_taxonomy_ids( $term, $taxonomy = 'product_cat')
	{
		if ( empty( $term ) )
			return false;

		if ( ! is_object($term) )
		{
			if ( is_array( $term ) )
				return wcsam_array_int_above_zero( $term );
			elseif ( preg_match('/(\-?[0-9]+,?)+/', $term) )
				return wcsam_array_int_above_zero( explode(',', trim($term, '-,')) );
			elseif ( is_int( $term ) && $term > 0 )
				return array($term);
			else
				$term = get_term_by( 'slug', $term, $taxonomy );
		}

		if ( ! is_object($term) || is_wp_error( $term ) )
			return false;

		$res_arr = [];
		foreach ( $term as $tVal )
		{
			if ( isset($tVal->term_id) )
				$res_arr[] = $tVal->term_id;
		}

		return $res_arr;
	}

	/**
	 * Get Taxonomy's (Categories)
	 *
	 * @param object|int|string $term      - The term object, ID, or slug whose link will be retrieved.
	 * @param string            $taxonomy  - Taxonomy name (slug) (default: product_cat)
	 *
	 * @return array|false
	 */
	protected function get_taxonomys( $term, $taxonomy = 'product_cat')
	{

		if ( empty( $term ) )
			return false;

		if ( is_array( $term ) )
			$term = wcsam_array_int_above_zero( $term );

		if ( is_object($term) )
			$term = array($term);
		elseif ( is_int( $term ) )
			$term = get_term( $term, $taxonomy );
		if ( is_array( $term ) )
			$term = get_terms(array('taxonomy' => $taxonomy, 'include' => $term));
		elseif ( preg_match('/[0-9,]+/', $term) )
			$term = get_terms(array('taxonomy' => $taxonomy, 'include' => trim($term, ',')));
		else
			$term = get_term_by( 'slug', $term, $taxonomy );

		if ( is_wp_error( $term ) )
			return false;

		return $term;
	}

	/**
	 * Change the query SELECT sql string
	 *
	 * @param  string $select_q  - SELECT sql
	 * @return string            - Correct SELECT sql
	 */
	public static function posts_fields_request( $select_q )
	{
		if ( $select_q === 'wp_posts.*' )
			return 'wp_posts.ID';
		else
			return $select_q;
	}
}