<?php

namespace WCSAM\modules\buy_one_click\frontend;

use WCSAM\modules\buy_one_click\abstracts\Frontend as AbstractFrontend;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Frontend_API
 *
 * @class   Frontend_API
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\buy_one_click\frontend
 * @version 1.0.0
 */
class Frontend_API extends AbstractFrontend
{
	/**
	 * Frontend_API constructor.
	 */
	public function __construct()
	{
		# Ajax Actions
		$unset_methods = array('__construct');
		foreach ( get_class_methods($this) as $m_name )
		{
			if ( in_array( $m_name, $unset_methods ) )
				continue;
			add_action( 'wp_ajax_' . Frontend::AJAX_ACTION_SLUG . $m_name, array($this, $m_name) );
			add_action( 'wp_ajax_nopriv_' . Frontend::AJAX_ACTION_SLUG . $m_name,  array($this, $m_name) );
		}
	}

	/**
	 * Buy One Click form generate
	 */
	public function get_form_block()
	{
		$product_id = (isset($_POST['product_id']) ? absint( $_POST['product_id'] ) : 0);

		$args = array(
			'quantity'   => (isset($_POST['quantity']) ? absint( $_POST['quantity'] ) : 0),
			'attributes' => (isset($_POST['attributes']) ? (array)$_POST['attributes'] : array()),
		);

		switch ( $_POST['form_type'] )
		{
			case 'product':
			case 'category':
				$cart_info['cart_info'] = $this->product_info( $product_id, $args );

				if ( ! $cart_info['cart_info'] )
					wp_send_json(array('status' => 'error', 'error_type' => 'none', 'data' => $this->message_error('ERROR')));

				$cart_info['form_title'] = BOC()->getP_general('boc_button_name_in_'. $_POST['form_type']);
				$cart_info['form_type']  = $_POST['form_type'];

				$this->view_boc_form( $cart_info );
				break;

			case 'cart':
				$cart_info['cart_info'] = $this->cart_info();

				if ( ! $cart_info['cart_info'] )
					wp_send_json(array('status' => 'error', 'error_type' => 'none', 'data' => $this->message_error('ERROR')));

				$cart_info['form_title'] = BOC()->getP_general('boc_button_name_in_cart');
				$cart_info['form_type']  = $_POST['form_type'];

				$this->view_boc_form( $cart_info );
				break;

			default:
				wp_send_json(array('status' => 'error', 'error_type' => 'none', 'data' => $this->message_error('ERROR')));
		}

		wp_die();
	}

	/**
	 * Add new Order in the Orders
	 */
	public function add_new_order()
	{
		$field_data = array(
			'product_id' => ( isset($_POST['product_id'])                ? (int) $_POST['product_id'] : 0),
			'attributes' => ( isset($_POST['attributes'])                ? (array) $_POST['attributes'] : array()),
			'quantity'   => ( isset($_POST['quantity'])                  ? (int) $_POST['quantity'] : 1),
			'full_name'  => ( isset($_POST['boc_field_full_name'])       ? wp_specialchars_decode(esc_html(trim( $_POST['boc_field_full_name'] )), ENT_QUOTES) : ''),
			'phone'      => ( isset($_POST['boc_field_tel'])             ? preg_replace('/(\+|\-|\,|\.)+/', '', trim( $_POST['boc_field_tel'] )) : ''),
			'email'      => ( isset($_POST['boc_field_email'])           ? sanitize_email(trim( $_POST['boc_field_email'] )) : ''),
			'desc_info'  => ( isset($_POST['boc_field_additional_info']) ? wp_specialchars_decode(esc_html(trim( $_POST['boc_field_additional_info'] )), ENT_QUOTES) : ''),
		);

		// Check form NONCE
		if ( ! check_admin_referer('wcsam_boc_product_key_'. Frontend::NONCE_SLUG . $field_data['product_id'], 'boc_ids') )
			wp_send_json(array('status' => 'error', 'error_type' => 'none', 'data' => $this->message_error('ERROR')));

		$error_fields = array();

		// Full name
		if ( BOC()->getP_form_settings('is_field_full_name') &&
			 BOC()->getP_form_settings('is_required_field_full_name') &&
			 empty( $field_data['full_name'] ) )
			$error_fields[] = 'boc_field_full_name';

		// Phone
		if ( BOC()->getP_form_settings('is_field_phone') &&
			 BOC()->getP_form_settings('is_required_field_phone') &&
			( !is_numeric( $field_data['phone'] ) || $field_data['phone'] < 1000000 ))
			$error_fields[] = 'boc_field_tel';

		// Email
		if ( BOC()->getP_form_settings('is_field_email') &&
			 BOC()->getP_form_settings('is_required_field_email') &&
			 empty( $field_data['email'] ) )
			$error_fields[] = 'boc_field_email';

		// Additional info
		if ( BOC()->getP_form_settings('is_field_additional_info') &&
			 BOC()->getP_form_settings('is_required_field_additional_info') &&
			 empty( $field_data['email'] ) )
			$error_fields[] = 'boc_field_additional_info';

		// Check field
		if ( $error_fields )
			wp_send_json(array('status' => 'error', 'error_type' => 'field', 'data' => $error_fields));

		$field_data['phone'] = (int)$field_data['phone'];

		// Form type
		switch ( $_POST['form_type'] )
		{
			case'product':
			case'category':
				$this->add_new_order_in_DB( $field_data );
				break;

			case'cart':
				$this->add_orders_from_cart( $field_data );
				break;

			default:
				wp_send_json(array('status' => 'error', 'error_type' => 'none', 'data' => $this->message_error('Not known type product')));
		}
	}
}

new Frontend_API();