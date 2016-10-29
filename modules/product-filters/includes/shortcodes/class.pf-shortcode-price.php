<?php

namespace WCSAM\modules\product_filters\shortcodes;

use WCSAM\abstracts\Abstract_Querys;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Shortcodes_Price
 *
 * @class   Shortcodes_Price
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters\shortcodes
 * @version 1.0.0
 */
class Shortcodes_Price extends Abstract_Querys
{
	/**
	 * Is only ID
	 *
	 * @var bool|true
	 */
	private $is_only_ID = true;

	/**
	 * Is echo products
	 *
	 * @var bool
	 */
	private $is_echo = true;

	/**
	 * Shortcodes_Price constructor.
	 */
	public function __construct()
	{
		// Filters on a specified percentage of the price
		add_shortcode('wcsam_product_filter_percent_price', array($this, 'do_shortcode_percent_price'));
		add_filter('wcsam_product_filter_percent_price', array($this, 'do_filter_percent_price'));

		// Filters for a given range of prices
		add_shortcode('wcsam_product_filter_range_price', array($this, 'do_shortcode_range_price'));
		add_filter('wcsam_product_filter_range_price', array($this, 'do_filter_range_price'));

		// Filters on the specified minimum and maximum price
		add_shortcode('wcsam_product_filter_absolute_price', array($this, 'do_shortcode_absolute_price'));
		add_filter('wcsam_product_filter_absolute_price', array($this, 'do_filter_absolute_price'));
	}

	/**
	 * Filters on a specified percentage of the price - ACTION or FILTER
	 *
	 * @param array $attr   - Details are described in method - $this->do_shortcode_percent_price()
	 * @param bool  $is_ids - As a result, return the ID or post data
	 *
	 * @return array
	 */
	public function do_filter_percent_price( $attr, $is_ids = false )
	{
		$this->is_echo    = false;
		$this->is_only_ID = (bool)$is_ids;
		return $this->do_shortcode_percent_price( $attr );
	}

	/**
	 * Filters on a specified percentage of the price - SHORTCODE
	 *
	 * @param  array  $attr  - Details are described in method
	 * @return string
	 */
	public function do_shortcode_percent_price( $attr )
	{
		$attr = (array) apply_filters('wcsam_product_filter_percent_price_attr', $attr);

		/**
		 * @var object|int|string $category  - Category (Required) (The term object, ID, or slug whose link will be retrieved.)
		 * @var double            $price     - Price (Required)
		 * @var int               $per_page  - Per.page (default: 5)
		 * @var double            $percent   - Percent (default: 10)
		 */
		extract( shortcode_atts( array(
			'category'    => '',
			'price'       => 0,
			'per_page'    => 5,
			'percent'     => 10,
		), $attr ) );

		if ( ! ($term_ids = $this->get_taxonomy_ids( $category )))
			return '';

		$per_page = (int)$per_page;
		$price    = (double)$price;
		$percent  = (double)$percent;

		if ( $per_page <= 0 || $percent <= 0 || $price <= 0 )
			return '';

		$min_price = $price - (($price * $percent) / 100);
		$max_price = $price + (($price * $percent) / 100);

		return $this->product_filter( $term_ids, $per_page, $min_price, $max_price );
	}

	/**
	 * Filters for a given range of prices - ACTION or FILTER
	 *
	 * @param array $attr   - Details are described in method - $this->do_shortcode_percent_price()
	 * @param bool  $is_ids - As a result, return the ID or post data
	 *
	 * @return array
	 */
	public function do_filter_range_price( $attr, $is_ids = false )
	{
		$this->is_echo    = false;
		$this->is_only_ID = (bool)$is_ids;
		return $this->do_shortcode_range_price( $attr );
	}

	/**
	 * Filters for a given range of prices - SHORTCODE
	 *
	 * @param  array $attr  - Details are described in method
	 * @return string
	 */
	public function do_shortcode_range_price( $attr )
	{
		$attr = (array) apply_filters('wcsam_product_filter_range_price_attr', $attr);

		/**
		 * @var object|int|string $category  - Category (Required) (The term object, ID, or slug whose link will be retrieved.)
		 * @var double            $price     - Price (Required)
		 * @var int               $per_page  - Per.page (default: 5)
		 * @var double            $range     - Range (default: 100)
		 */
		extract( shortcode_atts( array(
			'category' => '',
			'price'    => 0,
			'per_page' => 5,
			'range'    => 100,
		), $attr ) );

		if ( ! ($term_ids = $this->get_taxonomy_ids( $category )))
			return '';

		$per_page = (int)$per_page;
		$price    = (double)$price;
		$range    = (double)$range;

		if ( $per_page <= 0 || $range <= 0 || $price <= 0 )
			return '';

		if ( $range > $price )
			$min_price = 0;
		else
			$min_price = $price - $range;

		$max_price = $price + $range;

		return $this->product_filter( $term_ids, $per_page, $min_price, $max_price );
	}

	/**
	 * Filters on the specified minimum and maximum price - ACTION or FILTER
	 *
	 * @param array $attr   - Details are described in method - $this->do_shortcode_percent_price()
	 * @param bool  $is_ids - As a result, return the ID or post data
	 *
	 * @return array
	 */
	public function do_filter_absolute_price( $attr, $is_ids = false )
	{
		$this->is_echo    = false;
		$this->is_only_ID = (bool)$is_ids;
		return $this->do_shortcode_absolute_price( $attr );
	}

	/**
	 * Filters on the specified minimum and maximum price - SHORTCODE
	 *
	 * @param  array $attr  - Details are described in method
	 * @return string
	 */
	public function do_shortcode_absolute_price( $attr )
	{
		$attr = (array) apply_filters('wcsam_product_filter_absolute_price_attr', $attr);

		/**
		 * @var object|int|string $category  - Category (Required) (The term object, ID, or slug whose link will be retrieved.)
		 * @var int               $per_page   - Per.page (default: 5)
		 * @var double            $min_price  - Min price (default: 0)
		 * @var double            $max_price  - Max price (default: 9999999999)
		 */
		extract( shortcode_atts( array(
			'category'  => '',
			'per_page'  => 5,
			'min_price' => 0,
			'max_price' => 9999999999,
		), $attr ) );

		if ( ! ($term_ids = $this->get_taxonomy_ids( $category )))
			return '';

		$per_page  = (int)$per_page;
		$min_price = (double)$min_price;
		$max_price = (double)$max_price;

		if ( $per_page <= 0 || $max_price <= 0 || $min_price > $max_price )
			return '';

		if ( $min_price <= 0 )
			$min_price = 0;

		return $this->product_filter( $term_ids, $per_page, $min_price, $max_price );
	}

	/**
	 * Search products for the filters
	 *
	 * @param array  $term_ids   - Category IDs (Taxonomy)
	 * @param int    $per_page   - Per.page
	 * @param double $min_price  - Min price
	 * @param double $max_price  - Max price
	 *
	 * @return string|array
	 */
	private function product_filter( $term_ids, $per_page, $min_price, $max_price )
	{
		global $post;

		if ( isset($post, $post->ID) )
			$post_id = $post->ID;
		else
			$post_id = 0;

		if ( $this->is_only_ID )
			add_filter('posts_fields_request', array( '\WCSAM\abstracts\Abstract_Querys', 'posts_fields_request' ), 11);

		$products = new \WP_Query(array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => '1',
			'orderby'             => 'menu_order title',
			'order'               => 'ASC',
			'posts_per_page'      => $per_page,
			'post__not_in'        => ($post_id ? array($post_id) : array()),
			'meta_query'          => array(
				'set1' => array(
					'key'     => '_price',
					'value'   => array($min_price, $max_price),
					'compare' => 'BETWEEN',
					'type'    => 'DECIMAL',
				),
			),
			'tax_query' => array(
				'set1' => array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => implode( ',', $term_ids ),
					'operator' => 'IN',
				),
			),
			'meta_key' => '',
		));

		if ( ! isset( $products->posts ) )
		{
			if ( $this->is_echo )
				return '';
			else
				return [];
		}

		if ( $this->is_only_ID )
		{
			remove_filter('posts_fields_request', array( '\WCSAM\abstracts\Abstract_Querys', 'posts_fields_request' ), 11);
			$ret_arr = [];
			foreach ( $products->posts as $pVal )
				$ret_arr[] = $pVal->ID;
			unset($products);

			if ( $this->is_echo )
				return do_shortcode('[products ids="'. implode(',', $ret_arr) .'" columns="'. $per_page .'"]');
			else
				return $ret_arr;
		}

		return $products->posts;
	}

	/**
	 * Get filtered price
	 *
	 * wp-content\plugins\woocommerce\includes\widgets\class-wc-widget-price-filter.php
	 *
	 * @return array|null|object|void
	 */
	private function get_filtered_price()
	{
		global $wpdb, $wp_the_query;

		$args       = $wp_the_query->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

		if ( ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) )
		{
			$tax_query[] = array(
				'taxonomy' => $args['taxonomy'],
				'terms'    => array( $args['term'] ),
				'field'    => 'slug',
			);
		}

		foreach ( $meta_query as $key => $query )
		{
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) )
				unset( $meta_query[ $key ] );
		}

		$meta_query = new \WP_Meta_Query( $meta_query );
		$tax_query  = new \WP_Tax_Query( $tax_query );

		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql  = "SELECT min( CAST( price_meta.meta_value AS UNSIGNED ) ) as min_price, max( CAST( price_meta.meta_value AS UNSIGNED ) ) as max_price FROM {$wpdb->posts} ";
		$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " 	WHERE {$wpdb->posts}.post_type = 'product'
					AND {$wpdb->posts}.post_status = 'publish'
					AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
					AND price_meta.meta_value > '' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		return $wpdb->get_row( $sql );
	}
}

new Shortcodes_Price;