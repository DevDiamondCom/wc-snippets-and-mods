<?php

namespace WCSAM\modules\product_filters\abstracts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Widgets
 *
 * @class   Widget_Price_Filter
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters\abstracts
 * @version 1.0.0
 * @extends \WP_Widget
 */
abstract class Widgets extends \WP_Widget
{
	/**
	 * CSS class.
	 *
	 * @var string
	 */
	public $w_css_class;

	/**
	 * Widget description.
	 *
	 * @var string
	 */
	public $w_desc;

	/**
	 * Widget ID.
	 *
	 * @var string
	 */
	public $w_id;

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $w_name;

	/**
	 * Widget Settings.
	 *
	 * @var array
	 */
	public $w_settings;

	/**
	 * Widget Control options.
	 *
	 * @var array
	 */
	public $w_control_options = array();

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct( $this->w_id, $this->w_name, array(
			'classname'   => $this->w_css_class,
			'description' => $this->w_desc,
			'customize_selective_refresh' => true
		), $this->w_control_options );
	}

	/**
	 * Output the html at the start of a widget.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget_start( $args, $instance )
	{
		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) )
			echo $args['before_title'] . $title . $args['after_title'];
	}

	/**
	 * Output the html at the end of a widget.
	 *
	 * @param  array $args
	 */
	public function widget_end( $args )
	{
		echo $args['after_widget'];
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see    \WP_Widget->update
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		if ( empty( $this->w_settings ) )
			return $instance;

		// Loop settings and get values to save.
		foreach ( $this->w_settings as $key => $setting )
		{
			if ( ! isset( $setting['type'] ) )
				continue;

			// Format the value based on settings type.
			switch ( $setting['type'] )
			{
				case 'number':
					$instance[ $key ] = absint( $new_instance[ $key ] );

					if ( isset( $setting['min'] ) && '' !== $setting['min'] )
						$instance[ $key ] = max( $instance[ $key ], $setting['min'] );

					if ( isset( $setting['max'] ) && '' !== $setting['max'] )
						$instance[ $key ] = min( $instance[ $key ], $setting['max'] );
					break;

				case 'textarea':
					$instance[ $key ] = wp_kses( trim( wp_unslash( $new_instance[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
					break;

				case 'checkbox':
					$instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : 1;
					break;

				default:
					$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
					break;
			}
		}

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
		if ( empty( $this->w_settings ) )
			return;

		foreach ( $this->w_settings as $key => $setting )
		{
			$class = @$setting['class'] ?: '';
			$value = @$instance[ $key ] ?: $setting['std'];

			$blockclass = isset( $setting['block_class'] ) ? 'class="' . $setting['block_class'] . '"' : '';

			switch ( $setting['type'] )
			{
				case 'text':
					?>
					<p <?= $blockclass ?>>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
					break;

				case 'number':
					?>
					<p <?= $blockclass ?>>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
					break;

				case 'select':
					?>
					<p <?= $blockclass ?>>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
					break;

				case 'textarea':
					?>
					<p <?= $blockclass ?>>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<textarea class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" cols="20" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
					break;

				case 'checkbox':
					?>
					<p <?= $blockclass ?>>
						<input class="checkbox <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
					break;

				default : return;
			}
		}
		return;
	}

	/**
	 * Get correct current from action
	 *
	 * @return string
	 */
	protected function get_form_action()
	{
		global $wp;

		if ( '' === get_option( 'permalink_structure' ) )
			return remove_query_arg( array( 'page', 'paged' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		else
			return preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
	}

	/**
	 * Check current page type
	 *
	 * @return bool
	 */
	protected function check_page_type()
	{
		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) )
			return false;

		return true;
	}

	/**
	 * Remember current product filters/search
	 *
	 * @return string - hidden filter inputs
	 */
	protected function current_filters_fields()
	{
		$fields = '';

		if ( get_search_query() )
			$fields .= '<input type="hidden" name="s" value="' . get_search_query() . '" />';

		if ( ! empty( $_GET['post_type'] ) )
			$fields .= '<input type="hidden" name="post_type" value="' . esc_attr( $_GET['post_type'] ) . '" />';

		if ( ! empty ( $_GET['product_cat'] ) )
			$fields .= '<input type="hidden" name="product_cat" value="' . esc_attr( $_GET['product_cat'] ) . '" />';

		if ( ! empty( $_GET['product_tag'] ) )
			$fields .= '<input type="hidden" name="product_tag" value="' . esc_attr( $_GET['product_tag'] ) . '" />';

		if ( ! empty( $_GET['orderby'] ) )
			$fields .= '<input type="hidden" name="orderby" value="' . esc_attr( $_GET['orderby'] ) . '" />';

		if ( ! empty( $_GET['min_rating'] ) )
			$fields .= '<input type="hidden" name="min_rating" value="' . esc_attr( $_GET['min_rating'] ) . '" />';

		if ( $_chosen_attributes = \WC_Query::get_layered_nav_chosen_attributes() )
		{
			foreach ( $_chosen_attributes as $attribute => $data )
			{
				$taxonomy_filter = 'filter_' . str_replace( 'pa_', '', $attribute );

				$fields .= '<input type="hidden" name="' . esc_attr( $taxonomy_filter ) . '" value="' . esc_attr( implode( ',', $data['terms'] ) ) . '" />';

				if ( 'or' == $data['query_type'] )
					$fields .= '<input type="hidden" name="' . esc_attr( str_replace( 'pa_', 'query_type_', $attribute ) ) . '" value="or" />';
			}
		}

		return $fields;
	}

	/**
	 * Add styles
	 */
	protected function add_styles()
	{
		// PF Widget styles
		wp_enqueue_style(
			'wcsam-pf-widgets',
			PF_ASSETS_URL . 'css/pf-widgets.css'
		);
	}

	/**
	 * Add scripts
	 *
	 * @param array $args
	 */
	protected function add_scripts( $args = array() )
	{
		// Browser History
		wp_enqueue_script(
			'browser-history',
			WCSAM_ASSETS_URL . 'js/jquery.history.min.js',
			array( 'jquery' ),
			null,
			true
		);

		// PF Widget scripts
		wp_enqueue_script(
			'wcsam-pf-widgets',
			PF_ASSETS_URL . 'js/pf-widgets.js',
			array( 'jquery', 'jquery-ui-slider', 'jquery-touch-punch' ),
			null,
			true
		);
		wp_localize_script( 'wcsam-pf-widgets', 'pf_widgets_data', array(
			'update_container' => esc_html( PF()->getP_widgets('update_container_name') ),
			'loader_img'       => esc_url( PF()->getP_widgets('loader_img_url') ),
		));

		// PF Price Filter
		if ( isset($args['is_price_filter']) && $args['is_price_filter'] )
		{
			wp_localize_script( 'wcsam-pf-widgets', 'pf_price_filter', array(
				'currency_symbol'  => get_woocommerce_currency_symbol(),
				'currency_pos'     => get_option( 'woocommerce_currency_pos' ),
				'min_price'        => isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '',
				'max_price'        => isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '',
			));
		}
	}
}
