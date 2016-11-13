<?php

namespace WCSAM\modules\product_filters\widgets;

use WCSAM\modules\product_filters\abstracts\Widgets as Abstract_Widgets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Widget_Reset_Filters
 *
 * @class   Widget_Reset_Filters
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters\widgets
 * @version 1.0.0
 * @extends Abstract_Widgets
 */
class Widget_Reset_Filters extends Abstract_Widgets
{
	/**
	 * Widget_Reset_Filters Constructor.
	 */
	public function __construct()
	{
		$this->w_css_class = 'wcsam_widget wcsam_pf_reset_filters';
		$this->w_desc      = __('Ajax buttons for the reset the search filters and sorting products.', WCSAM_PLUGIN_SLUG);
		$this->w_id        = 'wcsam_ajax_reset_filters';
		$this->w_name      = __('WCSAM - Ajax Reset Filter', WCSAM_PLUGIN_SLUG);
		$this->w_settings  = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __('Reset filters', WCSAM_PLUGIN_SLUG),
				'label' => __('Title:', WCSAM_PLUGIN_SLUG)
			),
			'btn-val'  => array(
				'type'  => 'text',
				'std'   => __('Reset filters', WCSAM_PLUGIN_SLUG),
				'label' => __('Button value:', WCSAM_PLUGIN_SLUG)
			),
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
		$this->add_scripts( array('is_reset_filter' => true) );

		$this->widget_start( $args, $instance );

		echo '<form id="reset_filters_form" method="post" action="' . esc_url( $this->get_form_action() ) . '">
			<input type="submit" value="' . $instance['btn-val'] . '" />
		</form>';

		$this->widget_end( $args );
	}
}
