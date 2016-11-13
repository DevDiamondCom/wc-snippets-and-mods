<?php

namespace WCSAM\modules\product_filters\widgets;

use WCSAM\modules\product_filters\abstracts\Widgets as Abstract_Widgets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Widget_Price_Filters
 *
 * @class   Widget_Price_Filters
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters\widgets
 * @version 1.0.0
 * @extends Abstract_Widgets
 */
class Widget_Price_Filters extends Abstract_Widgets
{
	/**
	 * Widget_Price_Filters Constructor.
	 */
	public function __construct()
	{
		$this->w_css_class = 'wcsam_widget wcsam_pf_price_filters';
		$this->w_desc      = __('Ajax filter products by price. The widget displays a handy slider for the filter range in prices.', WCSAM_PLUGIN_SLUG);
		$this->w_id        = 'wcsam_ajax_price_filters';
		$this->w_name      = __('WCSAM - Ajax Price Filter', WCSAM_PLUGIN_SLUG);
		$this->w_settings  = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __('Filter by price', WCSAM_PLUGIN_SLUG),
				'label' => __('Title:', WCSAM_PLUGIN_SLUG)
			),
			'from-text'  => array(
				'type'  => 'text',
				'std'   => __('from', WCSAM_PLUGIN_SLUG),
				'label' => __('"From" text:', WCSAM_PLUGIN_SLUG)
			),
			'to-text'  => array(
				'type'  => 'text',
				'std'   => __('to', WCSAM_PLUGIN_SLUG),
				'label' => __('"To" text:', WCSAM_PLUGIN_SLUG)
			)
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see \WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		if ( !$this->check_page_type() )
			return;

		// Add styles and scripts
		$this->add_styles();
		$this->add_scripts( array('is_price_filter' => true) );

		// Get min and max price filter param
		$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';

		// Find min and max price in current result set
		$prices = $this->get_filtered_price();
		$min    = floor( $prices->min_price );
		$max    = ceil( $prices->max_price );

		if ( $min === $max )
			return;

		/**
		 * Adjust max if the store taxes are not displayed how they are stored.
		 * Min is left alone because the product may not be taxable.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() )
		{
			$tax_classes = array_merge( array( '' ), \WC_Tax::get_tax_classes() );
			$class_max   = $max;

			foreach ( $tax_classes as $tax_class )
			{
				if ( $tax_rates = \WC_Tax::get_rates( $tax_class ) )
					$class_max = $max + \WC_Tax::get_tax_total( \WC_Tax::calc_exclusive_tax( $max, $tax_rates ) );
			}

			$max = $class_max;
		}

		$this->widget_start( $args, $instance );

		echo '<form id="price_slider_form" method="get" action="' . esc_url( $this->get_form_action() ) . '">
			<div class="price_slider_wrapper">
				<div class="price_slider_amount">
					<input type="text" id="min_price" name="min_price" value="' . esc_attr( $min_price ) . '" data-min="' . esc_attr( apply_filters( 'woocommerce_price_filter_widget_min_amount', $min ) ) . '" placeholder="' . esc_attr__('Min price', WCSAM_PLUGIN_SLUG) . '" />
					<input type="text" id="max_price" name="max_price" value="' . esc_attr( $max_price ) . '" data-max="' . esc_attr( apply_filters( 'woocommerce_price_filter_widget_max_amount', $max ) ) . '" placeholder="' . esc_attr__('Max price', WCSAM_PLUGIN_SLUG) . '" />
					<div class="price_label" style="display:none;">
						'. (trim( $instance['from-text'] ) ? '<span class="from-text">'. $instance['from-text'] .':</span>' : '') .'
						'. (trim( $instance['to-text'] ) ? '<span class="to-text">'. $instance['to-text'] .':</span>' : '') .'
						<span class="from"></span><span class="to"></span>
					</div>
					<div class="clear"></div>
				</div>
				<div class="price_slider" style="display:none;"></div>
			</div>
		</form>';

		$this->widget_end( $args );
	}

	/**
	 * Get filtered min price for current products.
	 *
	 * wp-content\plugins\woocommerce\includes\widgets\class-wc-widget-price-filter.php
	 *
	 * @return array|null|object|void
	 */
	protected function get_filtered_price()
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
