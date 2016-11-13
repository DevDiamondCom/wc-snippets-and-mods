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
	 * Max price
	 *
	 * @var int
	 */
	private $max_price = 9999999999;

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
		 * @var object|int|string $category        - Category (Required) (The term object, ID, or slug whose link will be retrieved.)
		 * @var double            $price           - Price (Required)
		 * @var int               $per_page        - Per.page (default: 5)
		 * @var string            $other_products  - Other product filter (default: 'none') {none, rand, price_min, price_max}
		 * @var double            $percent         - Percent (default: 10)
		 */
		extract( shortcode_atts( array(
			'category'       => '',
			'price'          => 0,
			'per_page'       => 5,
			'other_products' => 'none',
			'percent'        => 10,
		), $attr ) );

		$per_page = (int)$per_page;
		$price    = (double)$price;
		$percent  = (double)$percent;

		if ( $per_page <= 0 || $percent <= 0 || $price <= 0 )
			return '';

		if ( ! ($term_ids = $this->get_taxonomy_ids( $category )))
			return '';

		$min_price = $price - (($price * $percent) / 100);
		$max_price = $price + (($price * $percent) / 100);

		$other_products = $this->correct_other_products_name( $other_products, $min_price, $max_price );

		return $this->product_filter( $term_ids, $per_page, $min_price, $max_price, $other_products );
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
		 * @var object|int|string $category        - Category (Required) (The term object, ID, or slug whose link will be retrieved.)
		 * @var double            $price           - Price (Required)
		 * @var int               $per_page        - Per.page (default: 5)
		 * @var string            $other_products  - Other product filter (default: 'none') {none, rand, price_min, price_max}
		 * @var double            $range           - Range (default: 100)
		 */
		extract( shortcode_atts( array(
			'category'       => '',
			'price'          => 0,
			'per_page'       => 5,
			'other_products' => 'none',
			'range'          => 100,
		), $attr ) );

		$per_page = (int)$per_page;
		$price    = (double)$price;
		$range    = (double)$range;

		if ( $per_page <= 0 || $range <= 0 || $price <= 0 )
			return '';

		if ( ! ($term_ids = $this->get_taxonomy_ids( $category )))
			return '';

		if ( $range > $price )
			$min_price = 0;
		else
			$min_price = $price - $range;

		$max_price = $price + $range;

		$other_products = $this->correct_other_products_name( $other_products, $min_price, $max_price );

		return $this->product_filter( $term_ids, $per_page, $min_price, $max_price, $other_products );
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
		 * @var object|int|string $category        - Category (Required) (The term object, ID, or slug whose link will be retrieved.)
		 * @var int               $per_page        - Per.page (default: 5)
		 * @var string            $other_products  - Other product filter (default: 'none') {none, rand, price_min, price_max}
		 * @var double            $min_price       - Min price (default: 0)
		 * @var double            $max_price       - Max price (default: $this->max_price)
		 */
		extract( shortcode_atts( array(
			'category'        => '',
			'per_page'        => 5,
			'other_products'  => 'none',
			'min_price'       => 0,
			'max_price'       => $this->max_price,
		), $attr ) );

		$per_page  = (int)$per_page;
		$min_price = (double)$min_price;
		$max_price = (double)$max_price;

		if ( $per_page <= 0 || $max_price <= 0 || $min_price > $max_price )
			return '';

		if ( ! ($term_ids = $this->get_taxonomy_ids( $category )))
			return '';

		if ( $min_price <= 0 )
			$min_price = 0;

		$other_products = $this->correct_other_products_name( $other_products, $min_price, $max_price );

		return $this->product_filter( $term_ids, $per_page, $min_price, $max_price, $other_products );
	}

	/**
	 * Search products for the filters
	 *
	 * @param array  $term_ids        - Category IDs (Taxonomy)
	 * @param int    $per_page        - Per.page
	 * @param double $min_price       - Min price
	 * @param double $max_price       - Max price
	 * @param double $other_products  - Other product filter name
	 *
	 * @return string|array
	 */
	private function product_filter( $term_ids, $per_page, $min_price, $max_price, $other_products )
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
				'price_filter' => array(
					'key'          => '_price',
					'value'        => array($min_price, $max_price),
					'compare'      => 'BETWEEN',
					'type'         => 'DECIMAL',
					'price_filter' => true,
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

		if ( $products->post_count < $per_page && $other_products !== 'none' )
		{
			$post_ids = ($post_id ? array($post_id) : array());
			if ( empty( $products->posts ) )
				$products->posts = array();

			foreach ( $products->posts as $pVal )
				$post_ids[] = $pVal->ID;

			$arr_q2 = array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => '1',
				'orderby'             => ($other_products === 'rand' ? 'rand' : 'menu_order title'),
				'order'               => 'ASC',
				'posts_per_page'      => ($per_page - $products->post_count),
				'post__not_in'        => $post_ids,
				'tax_query' => array(
					'set1' => array(
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => implode( ',', $term_ids ),
						'operator' => 'IN',
					),
				),
				'meta_key' => '',
			);
			if ( $other_products === 'price_min' || $other_products === 'price_max' )
			{
				$arr_q2['meta_query'] = array(
					'price_filter' => array(
						'key'          => '_price',
						'value'        => ($other_products === 'price_min' ? array(0, $min_price) : array($max_price, $this->max_price)),
						'compare'      => 'BETWEEN',
						'type'         => 'DECIMAL',
						'price_filter' => true,
					),
				);
			}
			$wp_q2 = new \WP_Query( $arr_q2 );

			if ( ! empty( $wp_q2->posts ) )
				$products->posts = array_merge($products->posts, $wp_q2->posts);
		}

		if ( empty( $products->posts ) )
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
	 * Corrected other product filter name
	 *
	 * @param  string $other_products - Other product filter name
	 * @param  double $min_price      - Min price
	 * @param  double $max_price      - Max price
	 *
	 * @return string
	 */
	private function correct_other_products_name( $other_products, $min_price, $max_price )
	{
		if ( ! in_array( $other_products, array('none', 'rand', 'price_min', 'price_max') ) )
			return 'none';
		elseif ( $other_products === 'price_min' && $min_price == 0 )
			return 'none';
		elseif ( $other_products === 'price_max' && $max_price >= $this->max_price )
			return 'none';
		else
			return $other_products;
	}
}

new Shortcodes_Price;