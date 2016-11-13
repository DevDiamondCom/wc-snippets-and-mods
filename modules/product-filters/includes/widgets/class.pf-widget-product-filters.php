<?php

namespace WCSAM\modules\product_filters\widgets;

use WCSAM\modules\product_filters\abstracts\Widgets as Abstract_Widgets;
use WCSAM\Helper;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Widget_Product_Filters
 *
 * @class   Widget_Product_Filters
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters\widgets
 * @version 1.0.0
 * @extends Abstract_Widgets
 */
class Widget_Product_Filters extends Abstract_Widgets
{
	/**
	 * Use to print or not widget
	 */
	public $is_found = false;

	/**
	 * Widget_Product_Filters Constructor.
	 */
	public function __construct()
	{
		$this->w_css_class = 'wcsam_widget wcsam_pf_product_filters';
		$this->w_desc      = __('Filter the list of the properties of products without reloading the page on Ajax.', WCSAM_PLUGIN_SLUG);
		$this->w_id        = 'wcsam_ajax_product_filters';
		$this->w_name      = __('WCSAM - Ajax Product Filter', WCSAM_PLUGIN_SLUG);
		$this->w_settings  = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __('Title:', WCSAM_PLUGIN_SLUG)
			),
			'type' => array(
				'type'    => 'select',
				'std'     => '',
				'label'   => __('Type:', WCSAM_PLUGIN_SLUG),
				'options' => array(
					'list'   => __('List', WCSAM_PLUGIN_SLUG),
					'select' => __('Dropdown', WCSAM_PLUGIN_SLUG),
					'label'  => __('Label', WCSAM_PLUGIN_SLUG),
					'color'  => __('Color', WCSAM_PLUGIN_SLUG),
				),
				'class'   => 'pfwa_show_type',
			),
			'attribute' => array(
				'type'    => 'select',
				'std'     => '',
				'label'   => __('Attribute:', WCSAM_PLUGIN_SLUG),
				'options' => array(),
				'class'   => 'pfwa_attribute',
			),
			'child_term' => array(
				'type'    => 'select',
				'std'     => '',
				'label'   => __('Child term:', WCSAM_PLUGIN_SLUG),
				'options' => array(
					'manual_selection' => __('Manual selection', WCSAM_PLUGIN_SLUG),
					'automatic_search' => __('Automatic search', WCSAM_PLUGIN_SLUG),
				),
				'class'   => 'pfwa_child_term',
			),
			'hierarchical' => array(
				'type'    => 'select',
				'std'     => 'yes',
				'label'   => __('Hierarchical:', WCSAM_PLUGIN_SLUG),
				'options' => array(
					'yes' => __('Yes', WCSAM_PLUGIN_SLUG),
					'no'  => __('No', WCSAM_PLUGIN_SLUG),
				),
				'class'   => 'pfwa_hierarchical',
				'block_class' => 'pfwa_hierarchical_block',
			),
		);
		$this->w_control_options = array( 'width' => 400, 'height' => 350 );

		parent::__construct();

		// Ajax actions
		add_action('wp_ajax_wcsam_pfwa_select_type', array( $this, 'action_select_type') );
	}

	/**
	 * Output widget.
	 *
	 * @see \WP_Widget->widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		if ( !$this->check_page_type() || empty( $instance['attribute'] ) )
			return;

		// Add styles and scripts
		$this->add_styles();
		$this->add_scripts( array('is_product_filter' => true) );

		$_chosen_attributes = Helper::get_layered_nav_chosen_attributes();
		$_attributes_array  = Helper::get_product_taxonomy();

		$filter_term_field = WCSAM()->filter_term_field;
		$current_term      = $_attributes_array && is_tax( $_attributes_array ) ? get_queried_object()->$filter_term_field : '';
		$query_type        = isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
		$display_type      = isset( $instance['type'] ) ? $instance['type'] : 'list';
		$terms_type_list   = ( isset( $instance['display'] ) ) ? $instance['display'] : 'all';

		$child_class  = 'wcsam-pfw-child-terms';
		$parent_class = 'wcsam-pfw-parent-terms';
		$chosen_class = 'wcsam-pfw-chosen';

		/* FIX TO WOOCOMMERCE 2.1 */
		if ( function_exists( 'wc_attribute_taxonomy_name' ) )
			$taxonomy = wc_attribute_taxonomy_name( $instance['attribute'] );
		else
			$taxonomy = \WC()->attribute_taxonomy_name( $instance['attribute'] );

		$taxonomy        = apply_filters( 'wcsam_pf_get_terms_params', $taxonomy, $instance, 'taxonomy_name' );
		$terms_type_list = apply_filters( 'wcsam_pf_get_terms_params', $terms_type_list, $instance, 'terms_type' );

		if ( ! taxonomy_exists( $taxonomy ) )
			return;

		$terms = get_terms( $taxonomy, array( 'hide_empty' => true ) );

		if ( count( $terms ) < 1 )
			return;

		$arg = 'filter_' . sanitize_title( $instance['attribute'] );

		// Current filter
		$current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();
		if ( ! is_array( $current_filter ) )
			$current_filter = array();
		if ( $current_filter )
			$current_filter = array_map( 'esc_attr', $current_filter );

		$this->widget_start( $args, $instance );

		// Display type
		switch ( $display_type )
		{
			// LIST type
			case 'list';
			{
				break;
			}

			// SELECT type (DropDown)
			case 'select';
			{
				break;
			}

			// COLOR type
			case 'color';
			{
				// echo Begin UL
				echo "<ul class='wcsam-pfw-color wcsam-pfw wcsam-pfw-group'>";

				foreach ( $terms as $term )
				{
					if ( ! empty( $instance['colors'][$term->term_id] ) )
					{
						$filter_val = array();
						if ( $instance['child_term'] === 'automatic_search' )
							$filter_val = $this->all_childs_filter_slug( $terms, $term->term_id );
						$filter_val[] = $term->slug;
						$filter_val = implode(',', $filter_val);

						$is_checked = '';
						foreach ( $current_filter as $fVal )
						{
							if ( strpos($filter_val, (string)$fVal) !== false )
							{
								$is_checked = " checked='checked'";
								break;
							}
						}

						$li_style = apply_filters( "{$args['widget_id']}-li_style", 'background-color:' . $instance['colors'][$term->term_id] . ';', $instance );
						$li_id    = md5($arg.$filter_val);

						echo '<li class="wcsam-pfw-checkbox-row">';
						echo '<div class="wcsam-pfw-color-filter '. ($is_checked ? 'wcsam-pfw-active' : '') .'"><input type="checkbox" id="'. $li_id .'" name="'. $arg .'" value="'. $filter_val .'" '. $is_checked .'>'.
							'<label for="'. $li_id .'"><span class="wcsam-pfw-color-block" style="' . $li_style . '"></span><span class="wcsam-pfw-color-name">'. $term->name .'</span></label></div>';
						echo '</li>';
					}
				}

				// echo End UL
				echo "</ul>";

				break;
			}

			// LABEL type
			case 'label';
			{
				// echo Begin UL
				echo "<ul class='wcsam-pfw-label wcsam-pfw wcsam-pfw-group'>";

				foreach ( $terms as $term )
				{
					if ( ! empty( $instance['labels'][$term->term_id] ) )
					{
						$filter_val = array();
						if ( $instance['child_term'] === 'automatic_search' )
							$filter_val = $this->all_childs_filter_slug( $terms, $term->term_id );
						$filter_val[] = $term->slug;
						$filter_val = implode(',', $filter_val);

						$is_checked = '';
						foreach ( $current_filter as $fVal )
						{
							if ( strpos($filter_val, (string)$fVal) !== false )
							{
								$is_checked = " checked='checked'";
								break;
							}
						}

						$li_id = md5($arg.$filter_val);

						echo '<li class="wcsam-pfw-checkbox-row">';
						echo '<div class="wcsam-pfw-label-filter '. ($is_checked ? 'wcsam-pfw-active' : '') .'"><input type="checkbox" id="'. $li_id .'" name="'. $arg .'" value="'. $filter_val .'" '. $is_checked .'>'.
							'<label for="'. $li_id .'"><span class="wcsam-pfw-label-name">'. $term->name .'</span></label></div>';
						echo '</li>';
					}
				}

				// echo End UL
				echo "</ul>";

				break;
			}

			default: do_action( "wcsam_pfw_display_{$display_type}", $args, $instance, $display_type, $terms, $taxonomy, $filter_term_field );
		}

		$this->widget_end( $args );
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see    \WP_Widget->update
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance )
	{
		$instance = parent::update( $new_instance, $old_instance );

		$instance['colors']      = ! empty( $new_instance['colors'] ) ? $new_instance['colors'] : array();
		$instance['multicolor']  = ! empty( $new_instance['multicolor'] ) ? $new_instance['multicolor'] : array();
		$instance['labels']      = ! empty( $new_instance['labels'] ) ? $new_instance['labels'] : array();

		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @see \WP_Widget->form
	 *
	 * @param  array  $instance
	 * @return string
	 */
	public function form( $instance )
	{
		$this->add_admin_styles();
		$this->add_admin_scripts();

		$this->w_settings['attribute']['options'] = $this->dropdown_attributes();

		?>
		<style type="text/css">.pfwa_hierarchical_block{display: <?= (in_array( $instance['type'], array('list','select')) ? 'block' : 'none') ?>}</style>

		<?php

		parent::form( $instance );

		?>

		<div class="pfwa_attributes_table_block">
			<?php

			$show_attr = true;
			switch ( $instance['type'] )
			{
				case 'color':      $values = $instance['colors'];     break;
				case 'label':      $values = $instance['labels'];     break;
				case 'multicolor': $values = $instance['multicolor']; break;
				default: $values = array(); $show_attr = false;
			}

			if ( $show_attr )
			{
				$this->attributes_table(
					array(
						'type'        => $instance['type'],
						'attribute'   => $instance['attribute'],
						'child_term'  => $instance['child_term'],
						'widget_id'   => 'widget-' . $this->id . '-',
						'widget_name' => 'widget-' . $this->id_base . '[' . $this->number . ']',
					),
					$values,
					true
				);
			}

			?>
		</div>
        <input type="hidden" name="widget_id" value="widget-<?= $this->id ?>-" />
        <input type="hidden" name="widget_name" value="widget-<?= $this->id_base ?>[<?= $this->number ?>]" />
		<script type="text/javascript">jQuery(document).trigger('pfwa_colorpicker');</script>
		<?php

		return;
	}


	/**
	 * DropDown attributes
	 *
	 * @return array
	 */
	protected function dropdown_attributes()
	{
		$options = array();
		foreach ( Helper::attribute_taxonomies() as $attrKey => $attrVal )
			$options[ $attrKey ] = $attrVal;

		return $options;
	}

	/**
	 * Action select type
	 */
	public function action_select_type()
	{
		$type     = esc_attr( $_POST['value'] );
		$return   = array( 'message' => '', 'content' => '' );
		$settings = $this->get_settings()[ $this->number ];

		switch ( $type )
		{
			case 'label'      : $values = $settings['labels'];     break;
			case 'color'      : $values = $settings['colors'];     break;
			case 'multicolor' : $values = $settings['multicolor']; break;

			default: $values = '';
		}

		if ( $type )
		{
			$return['content'] = $this->attributes_table(
				array(
					'type'        => $type,
					'attribute'   => esc_attr( $_POST['attribute'] ),
					'child_term'  => esc_attr( $_POST['child_term'] ),
					'widget_id'   => esc_attr( $_POST['widget_id'] ),
					'widget_name' => esc_attr( $_POST['widget_name'] ),
				),
				$values,
				false
			);
		}

		wp_send_json( $return );
	}

	/**
	 * Attributes table
	 *
	 * @param array $args - {
	 *      // params
	 *      @type string $type        - (list, select, label, color)
	 *      @type string $attribute   - Term attribute
	 *      @type string $child_term  - (automatic_search, manual_selection)
	 *      @type string $widget_id   - Widget ID
	 *      @type string $widget_name - Widget Base ID
	 * }
	 * @param array $values
	 * @param bool  $echo
	 *
	 * @return string
	 */
	protected function attributes_table( $args, $values = array(), $echo = true )
	{
		$return = '';
		$terms  = get_terms( 'pa_' . $args['attribute'], array( 'hide_empty' => '0' ) );

		switch ( $args['type'] )
		{
			case 'list':
			case 'select':
				$return = '<input type="hidden" name="' . $args['widget_name'] . '[colors]" value="" /><input type="hidden" name="' . $args['widget_name'] . '[labels]" value="" />';
				break;

			case 'color':
				if ( ! empty( $terms ) )
				{
					$return = sprintf( '<table><tr><th>%s</th><th>%s</th></tr>',
						__('Term', WCSAM_PLUGIN_SLUG),
						__('Color', WCSAM_PLUGIN_SLUG)
					);

					foreach ( $terms as $term )
					{
						if ( $args['child_term'] === 'automatic_search' && $term->parent > 0 )
							continue;

						$return .= sprintf(
							'<tr><td><label for="%1$s%2$s">%3$s</label></td><td><input type="text" id="%1$s%2$s" name="%4$s[colors][%2$s]" value="%5$s" size="3" class="pfwa-colorpicker" /></td></tr>',
							$args['widget_id'],
							$term->term_id,
							$term->name,
							$args['widget_name'],
							( @$values[$term->term_id] ?: '' )
						);
					}

					$return .= '</table>';
				}

				$return .= '<input type="hidden" name="' . $args['widget_name'] . '[labels]" value="" />';
				break;

			case 'label':
				if ( ! empty( $terms ) )
				{
					$return = sprintf( '<table><tr><th>%s</th><th>%s</th></tr>',
						__('Term', WCSAM_PLUGIN_SLUG),
						__('Labels', WCSAM_PLUGIN_SLUG)
					);

					foreach ( $terms as $term )
					{
						if ( $args['child_term'] === 'automatic_search' && $term->parent > 0 )
							continue;

						$return .= sprintf(
							'<tr><td><label for="%1$s%2$s">%3$s</label></td><td><input type="text" id="%1$s%2$s" name="%4$s[labels][%2$s]" value="%5$s" size="3" /></td></tr>',
							$args['widget_id'],
							$term->term_id,
							$term->name,
							$args['widget_name'],
							( @$values[$term->term_id] ?: '' )
						);
					}

					$return .= '</table>';
				}

				$return .= '<input type="hidden" name="' . $args['widget_name'] . '[colors]" value="" />';
				break;

			case 'multicolor':
				if ( ! empty( $terms ) )
				{
					$return = sprintf(
						'<table class="pfwa-multicolor"><tr><th>%s</th><th>%s</th><th>%s</th></tr>',
						__('Term', WCSAM_PLUGIN_SLUG),
						_x('Color 1', 'For multicolor: I.E. white and red T-Shirt', WCSAM_PLUGIN_SLUG),
						_x('Color 2', 'For multicolor: I.E. white and red T-Shirt', WCSAM_PLUGIN_SLUG)
					);

					foreach ( $terms as $term )
					{
						if ( $args['child_term'] === 'automatic_search' && $term->parent > 0 )
							continue;

						$return .= "<tr>";
						$return .= "<td><label for='{$args['widget_id']}{$term->term_id}'>{$term->name}</label></td>";

						for ($x1=0; $x1 < 2; $x1++)
						{
							$return .= sprintf(
								'<td><input type="text" id="%1$s%2$s_%5$s" name="%3$s[multicolor][%2$s][]" value="%4$s" size="3" class="pfwa-colorpicker multicolor" /></td>',
								$args['widget_id'],
								$term->term_id,
								$args['widget_name'],
								( @$values[$term->term_id][ $x1 ] ?: '' ),
								($x1+1)
							);
						}

						$return .= '</tr>';
					}

					$return .= '</table>';
				}

				$return .= '<input type="hidden" name="' . $args['widget_name'] . '[labels]" value="" />';
				break;

			default: $return = '';
		}

		if ( $echo )
			echo $return;

		return $return;
	}

	private function all_childs_filter_slug( &$terms, $term_id )
	{
		if ( !is_array($terms) || ! $terms )
			return array();

		$child_terms = array();

		foreach ( $terms as $term )
		{
			if ( !isset($term->parent) || $term->parent !== (int)$term_id )
				continue;

			$child_terms[] = $term->slug;
			$child_terms = array_merge($child_terms, $this->all_childs_filter_slug( $terms, $term->term_id ));
		}

		return $child_terms;
	}

	/**
	 * Add Admin scripts
	 */
	protected function add_admin_styles()
	{
		// PF Admin Widget scripts
		wp_enqueue_style(
			'wcsam-pf-admin-widgets',
			PF_ASSETS_URL . 'admin/css/pf-admin-widgets.css'
		);
	}

	/**
	 * Add Admin scripts
	 */
	protected function add_admin_scripts()
	{
		// PF Admin Widget scripts
		wp_enqueue_script(
			'wcsam-pf-admin-widgets',
			PF_ASSETS_URL . 'admin/js/pf-admin-widgets.js',
			array( 'jquery' ),
			null,
			true
		);
		wp_localize_script( 'wcsam-pf-admin-widgets', 'pfwa_data', array(
			'loader_img' => WCSAM_ASSETS_URL . 'img/ajax-loader-balls_150.gif',
		));
	}
}
