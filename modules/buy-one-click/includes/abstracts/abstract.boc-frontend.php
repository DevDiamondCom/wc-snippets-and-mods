<?php

namespace WCSAM\modules\buy_one_click\abstracts;

use WCSAM\modules\buy_one_click\frontend\Frontend as C_Frontend;
use DD_SMSC\SMSC;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Frontend
 *
 * @class   Frontend
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\buy_one_click\abstracts
 * @version 1.0.0
 */
abstract class Frontend
{
	/**
	 * Get product info
	 *
	 * @param int   $product_id - Product ID
	 * @param array $args       - {
	 *      Data
	 *      @type int $quantity   - Quantity
	 *      @type int $attributes - Attributes
	 * }
	 *
	 * @return array|bool  - if success <ARRAY> product info data else <FALSE>
	 */
	protected function product_info( $product_id, $args )
	{
		if ( ! is_int($product_id) || $product_id == 0)
			return false;

		$product = wc_get_product( $product_id );

		if ( ! $product )
			return false;

		// Img
		$imageurl = wp_get_attachment_image_src($product->get_image_id());

		// Quantity
		$quantity = ( (int)$args['quantity'] ? (int)$args['quantity'] : 1 );

		// Attributes
		$attributes_name = array();
		$attributes_val  = array();
		foreach ( $args['attributes'] as $attrKey => $attrVal )
		{
			$attribute_key = urldecode( str_replace( 'attribute_', '', $attrKey ) );

			$term = get_term_by( 'slug', $attrVal, $attribute_key );
			if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name )
			{
				$attributes_name[ wc_attribute_label($term->taxonomy) ] = $term->name;
				$attributes_val[ $attrKey ] = $attrVal;
			}
		}

		// Return result
		return array(
			array(
				'product_id'       => $product_id,
				'product_title'    => $product->get_title(),
				'product_img'      => ( @$imageurl[0] ?: '' ),
				'product_price'    => floatval($product->get_price()),
				'product_quantity' => $quantity,
				'attributes_name'  => $attributes_name,
				'attributes_val'   => $attributes_val,
			)
		);
	}

	/**
	 * Get Cart Info
	 *
	 * @return array
	 */
	protected function cart_info()
	{
		// Cart data
		$cart_data = array();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item )
		{
			$cart_info = array();
			$_product  = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			// Check data
			if ( !$_product || !$_product->exists() || $cart_item['quantity'] < 1 || !apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) )
				continue;

			// Product ID
			$cart_info['product_id'] = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			// Product Title
			$cart_info['product_title'] = $_product->get_title();

			// Img
			$imageurl = wp_get_attachment_image_src($_product->get_image_id());
			$cart_info['product_img'] = ( @$imageurl[0] ?: '' );

			// Product Price
			$cart_info['product_price'] = floatval($_product->get_price());

			// Product Quantity
			$cart_info['product_quantity'] = $cart_item['quantity'];

			// Attributes name
			$attributes_name = array();
			if ( ! empty( $cart_item['data']->variation_id ) && is_array( $cart_item['variation'] ) )
			{
				foreach ( $cart_item['variation'] as $name => $value )
				{
					if ( '' === $value )
						continue;

					$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

					// If this is a term slug, get the term's nice name
					if ( taxonomy_exists( $taxonomy ) )
					{
						$term = get_term_by( 'slug', $value, $taxonomy );
						if ( ! is_wp_error( $term ) && $term && $term->name )
							$value = $term->name;
						$label = wc_attribute_label( $taxonomy );
					}
					// If this is a custom option slug, get the options name
					else
					{
						$value = apply_filters( 'woocommerce_variation_option_name', $value );
						$product_attributes = $cart_item['data']->get_attributes();
						if ( isset( $product_attributes[ str_replace( 'attribute_', '', $name ) ] ) )
							$label = wc_attribute_label( $product_attributes[ str_replace( 'attribute_', '', $name ) ]['name'] );
						else
							$label = $name;
					}

					$attributes_name[ $label ] = $value;
				}
			}
			$cart_info['attributes_name'] = $attributes_name;

			$cart_data[] = $cart_info;
		}

		return $cart_data;
	}

	/**
	 * View Buy One Click form
	 *
	 * @param array $cart_info - {
	 *      form data
	 *
	 *      @type array  $cart_info  - Product data
	 *      @type string $form_title - Form title (name)
	 *      @type string $form_type  - Form type (product, category, cart)
	 * }
	 */
	protected function view_boc_form( $cart_info )
	{
		$product_info_position = BOC()->getP_form_settings('product_info_modal_position');
		$product_info = '';
		if ( BOC()->getP_form_settings('is_product_info_modal') )
		{
			$total_price = 0;
			foreach ( $cart_info['cart_info'] as $pci )
			{
				$total_price += $pci['product_price'] * $pci['product_quantity'];

				$product_info .= '<div class="boc-product-this">';
				if ( $pci['product_img'] )
					$product_info .= '<div class="boc-product-this-img"><img src="' . $pci['product_img'] . '" width="80" height="80"></div>';
				$product_info .= '<div class="boc-product-this-info">';
				$product_info .= '<p class="pti_title"><strong class="name-item">'. $pci['product_title'] .'</strong></p>';
				$product_info .= '<p class="pti_price"><strong>'. apply_filters('wcsam_boc_form_product_price_text', __('Price', WCSAM_PLUGIN_SLUG)) .':</strong> '. wc_price( $pci['product_price'] ) .'</p>';
				$product_info .= '<p class="pti_quantity"><strong>'. apply_filters('wcsam_boc_form_product_quantity_text', __('Quantity', WCSAM_PLUGIN_SLUG)) .':</strong> '. $pci['product_quantity'] .'</p>';
				foreach ( $pci['attributes_name'] as $attrKey => $attrVal )
					$product_info .= '<p class="pti_"><strong>'. apply_filters('wcsam_boc_form_product_attribute_name', $attrKey) .':</strong> '. apply_filters('wcsam_boc_form_product_attribute_val', $attrVal, $attrKey) .'</p>';
				$product_info .= '</div>';
				$product_info .= '</div>';
			}

			$product_info .= '<div class="finish-price">'. apply_filters('wcsam_boc_form_product_total_text', __('Total', WCSAM_PLUGIN_SLUG)) .': <strong>'. wc_price( $total_price ) .'</strong></div>';
		}

		?>
		<div class="boc-bg"></div>
		<div id="boc_order_block" class="boc_order_block_modal">

			<div class="boc_p_title"><?= $cart_info['form_title'] ?></div>

			<?php
				// Product info
				if ( $product_info_position === 'before_fields' )
					echo $product_info;
			?>

			<?= do_action('wcsam_boc_form_before_html') ?>

			<div role="form" class="clear boc_order_form_block">
				<form action="" method="post" id="boc_order_form">
					<div style="display: none;">
						<?php

						$product_id = 0;

						if ( $cart_info['form_type'] !== 'cart' )
						{
							// Attributes
							foreach ( $cart_info['cart_info'][0]['attributes_val'] as $attrKey => $attrVal )
								echo '<input type="hidden" name="attributes['. $attrKey .']" value="'. $attrVal .'">';

							// Product ID
							$product_id = $cart_info['cart_info'][0]['product_id'];
							echo '<input type="hidden" name="product_id" value="'. $product_id .'">';

							// Quantity
							echo '<input type="hidden" name="quantity" value="'. $cart_info['cart_info'][0]['product_quantity'] .'">';
						}

						// Nonce
						wp_nonce_field('wcsam_boc_product_key_'. C_Frontend::NONCE_SLUG . $product_id, 'boc_ids');

						?>
					</div>
					<p>
						<?= do_action('wcsam_boc_form_before_field_html') ?>
					</p>
					<p>
						<?php

						// Full name
						if ( BOC()->getP_form_settings('is_field_full_name') )
						{
							$is_fr = BOC()->getP_form_settings('is_required_field_full_name');

							echo '<span class="boc_field_full_name">';
							echo '<input type="text" name="boc_field_full_name" value="" size="40" '.
								'aria-required="'. ($is_fr ? 'true' : 'false') .'" '.
								'placeholder="'. BOC()->getP_form_settings('field_full_name') .'">';
							if ( $is_fr )
								echo '<span role="alert" class="boc_not_valid_tip">'. __('The field required.', WCSAM_PLUGIN_SLUG) .'</span>';
							echo '</span><br>';
						}

						?>
					</p>
					<p>
						<?php

						// Phone
						if ( BOC()->getP_form_settings('is_field_phone') )
						{
							$is_fr = BOC()->getP_form_settings('is_required_field_phone');

							echo '<span class="boc_field_tel">';
							echo '<input type="tel" name="boc_field_tel" value="" size="40" '.
								'aria-required="'. ($is_fr ? 'true' : 'false') .'" '.
								'placeholder="'. BOC()->getP_form_settings('field_phone') .'">';
							if ( $is_fr )
								echo '<span role="alert" class="boc_not_valid_tip">'. __('The field required.', WCSAM_PLUGIN_SLUG) .'</span>';
							echo '</span><br>';
						}

						?>
					</p>
					<p>
						<?php

						// Email
						if ( BOC()->getP_form_settings('is_field_email') )
						{
							$is_fr = BOC()->getP_form_settings('is_required_field_email');

							echo '<span class="boc_field_email">';
							echo '<input type="tel" name="boc_field_email" value="" size="40" '.
								'aria-required="'. ($is_fr ? 'true' : 'false') .'" '.
								'placeholder="'. BOC()->getP_form_settings('field_email') .'">';
							if ( $is_fr )
								echo '<span role="alert" class="boc_not_valid_tip">'. __('The field required.', WCSAM_PLUGIN_SLUG) .'</span>';
							echo '</span><br>';
						}

						?>
					</p>
					<p>
						<?php

						// Additional info
						if ( BOC()->getP_form_settings('is_field_additional_info') )
						{
							$is_fr = BOC()->getP_form_settings('is_required_field_additional_info');

							echo '<span class="boc_field_additional_info">';
							echo '<textarea name="boc_field_additional_info" '.
								'aria-required="'. ($is_fr ? 'true' : 'false') .'" '.
								'placeholder="'. BOC()->getP_form_settings('field_additional_info') .'"></textarea>';
							if ( $is_fr )
								echo '<span role="alert" class="boc_not_valid_tip">'. __('The field required.', WCSAM_PLUGIN_SLUG) .'</span>';
							echo '</span><br>';
						}

						?>
					</p>
					<p>
						<?= do_action('wcsam_boc_form_after_field_html') ?>
					</p>
					<p>
						<input type="submit" value="<?= BOC()->getP_form_settings('send_btn_name') ?>" class="boc_form_submit">
						<img class="boc_form_ajax_loader" src="<?= WCSAM_ASSETS_URL ?>img/ajax-loader-medium.gif" alt="<?= __('Dispatch...', WCSAM_PLUGIN_SLUG) ?>" style="display: none;">
					</p>
				</form>
				<div class="boc_result_notice_title" title="<?= __('Notice', WCSAM_PLUGIN_SLUG) ?>"><?= esc_html( BOC()->getP_form_settings('success_message_text') ) ?></div>
				<div class="boc_result_notice_message"></div>
			</div>

			<?= do_action('wcsam_boc_form_after_html') ?>

			<?php
			// Product info
			if ( $product_info_position === 'after_fields' )
				echo '<br>'.$product_info;
			?>

			<div class="close-boc"></div>
		</div>
		<?php
	}

	/**
	 * Add new one order in the DB
	 *
	 * @param array $args - {
	 *      order and form data
	 *
	 *      @type int    $product_id - Product ID
	 *      @type array  $attributes - Product attributes (variation product)
	 *      @type int    $quantity   - Product quantity
	 *      @type string $full_name  - Client full name
	 *      @type int    $phone      - Client Phone
	 *      @type string $email      - Client Email
	 *      @type string $desc_info  - Client Desc. info
	 * }
	 */
	protected function add_new_order_in_DB( $args )
	{
		/**
		 * Check and Get Product
		 *
		 * @var $product
		 */
		if ( $args['product_id'] < 1 || !($product = wc_get_product( $args['product_id'] )) )
			wp_send_json(array('status' => 'error', 'error_type' => 'none', 'data' => $this->message_error('No such product')));

		// Quantity
		if ( $args['quantity'] < 1 )
			$args['quantity'] = 1;

		// Create and add new order
		$address = array(
			'first_name' => ( !empty($args['full_name']) ? $args['full_name'] : 'none'),
			'last_name'  => '',
			'company'    => '',
			'email'      => ( !empty($args['email']) ? $args['email'] : ''),
			'phone'      => ( !empty($args['phone']) ? $args['phone'] : ''),
			'address_1'  => ( !empty($args['desc_info']) ? $args['desc_info'] : ''),
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
		);
		$order = wc_create_order();
		$order->add_product($product, $args['quantity'], array('variation' => $args['attributes']));
		$order->set_address($address, 'billing');
		$order->set_address($address, 'shipping');
		$order->update_status('processing');
		//$order->calculate_totals();

		// Send EMAIL
		if ( BOC()->getP_notification('is_email_send') )
		{
			// Message data
			$attributes['p_url']    = '<a href="' . get_the_permalink($product->id) . '" target="_blank">'. $product->get_title() .'</a>';
			$attributes['p_price']  = floatval($product->get_price());
			$attributes['quantity'] = $args['quantity'];
			$attributes['attr']     = array();

			foreach ( $args['attributes'] as $attrKey => $attrVal )
			{
				$attribute_key = urldecode( str_replace( 'attribute_', '', $attrKey ) );

				$term = get_term_by( 'slug', $attrVal, $attribute_key );
				if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name )
					$attributes['attr'][ wc_attribute_label($term->taxonomy) ] = $term->name;
			}

			$this->send_email_notification_about_order(array(
				'full_name' => $args['phone'],
				'email'     => $args['full_name'],
				'phone'     => $args['email'],
				'p_data'    => array( $attributes ),
			));
		}

		// Send SMS
		if ( BOC()->getP_notification('is_sms_send') && !empty($args['phone']) )
		{
			$this->send_sms_notification_about_order(
				array(
					'phone'     => $args['phone'],
					'full_name' => $args['full_name'],
					'email'     => $args['email'],
					'p_price'   => floatval($product->get_price()),
					'p_title'   => $product->get_title(),
					'quantity'  => $args['quantity'],
				),
				'multiple'
			);
		}

		$this->json_success_send();
		die();
	}

	/**
	 * Add orders from cart
	 *
	 * @param array $args - {
	 *      order and form data
	 *
	 *      @type int    $product_id - (empty)
	 *      @type array  $attributes - (empty)
	 *      @type int    $quantity   - (empty)
	 *      @type string $full_name  - Client Full name
	 *      @type int    $phone      - Client Phone
	 *      @type string $email      - Client Email
	 *      @type string $desc_info  - Client Desc. info
	 * }
	 */
	protected function add_orders_from_cart( $args )
	{
		// Get cart info
		$cart_data = $this->cart_info();

		if ( ! $cart_data )
			wp_send_json(array('status' => 'error', 'error_type' => 'none', 'data' => $this->message_error('Cart empty')));

		// Add orders from cart
		$wc_checkout = \WC_Checkout::instance();
		$order_id = $wc_checkout->create_order();
		if ( is_wp_error( $order_id ) )
			wp_send_json(array('status' => 'error', 'error_type' => 'none', 'data' => $this->message_error('ERROR')));

		do_action( 'woocommerce_checkout_order_processed', $order_id, $wc_checkout->posted );

		$order = wc_get_order( $order_id );
		$order->update_status('processing');

		// Empty the Cart
		WC()->cart->empty_cart();

		// Send EMAIL
		if ( BOC()->getP_notification('is_email_send') )
		{
			// Message data
			$p_info = array();
			foreach ( $cart_data as $pVal )
			{
				$attributes['p_url']    = '<a href="' . get_the_permalink($pVal['product_id']) . '" target="_blank">'. $pVal['product_title'] .'</a>';
				$attributes['p_price']  = $pVal['product_price'];
				$attributes['quantity'] = $pVal['product_quantity'];
				$attributes['attr']     = array();

				foreach ( $pVal['attributes_name'] as $attrName => $attrVal )
					$attributes['attr'][ $attrName ] = $attrVal;

				$p_info[] = $attributes;
			}

			$this->send_email_notification_about_order(array(
				'full_name' => $args['phone'],
				'email'     => $args['full_name'],
				'phone'     => $args['email'],
				'p_data'    => $p_info,
			));
		}

		// Send SMS
		if ( BOC()->getP_notification('is_sms_send') && !empty($args['phone']) )
		{
			$this->send_sms_notification_about_order(
				array(
					'phone'     => $args['phone'],
					'full_name' => $args['full_name'],
					'email'     => $args['email'],
				),
				'single'
			);
		}

		$this->json_success_send();
		die();
	}

	/**
	 * Send Success in Json format
	 */
	private function json_success_send()
	{
		// Success message
		switch ( BOC()->getP_form_settings('click_order_btn') )
		{
			case 'close_n_sec':
				$notice_action = (int)BOC()->getP_form_settings('click_order_btn_close_msec');
				break;
			case 'show_message':
				$notice_action = esc_html( BOC()->getP_form_settings('click_order_btn_message') );
				break;
			case 'redirect_to':
				$notice_action = esc_url( BOC()->getP_form_settings('click_order_btn_redirect_url') );
				break;
			case 'none':
			default:
				$notice_action = '';
		}

		wp_send_json(array(
			'status' => 'success',
			'notice_action' => $notice_action,
			'action_type'   => esc_attr( BOC()->getP_form_settings('click_order_btn') ),
		));
	}

	/**
	 * Send SMS Notification about order
	 *
	 * @param array @args - {
	 *      user and message data
	 *
	 *      @type int    @phone     - (Required) Client Phone
	 *      @type string @full_name - Client Full Name
	 *      @type string @email     - Client Email
	 *      @type float  @p_price   - (for single) Product price
	 *      @type string @p_title   - (for single) Product title
	 *      @type int    @quantity  - (for single) Quantity
	 * }
	 * @param string $order_type - (single, multiple) (default: single)
	 */
	protected function send_sms_notification_about_order( $args, $order_type )
	{
		if ( !BOC()->getP_notification('is_sms_send') && !empty($args['phone']) )
			return;

		if ( !is_numeric( $args['phone'] ) || $args['phone'] < 1000000 )
			return;

		$smsc = new SMSC(array(
			'login'    => BOC()->getP_notification('smsc_login'),
			'password' => BOC()->getP_notification('smsc_password'),
			'charset'  => BOC()->getP_notification('sms_encoding', 'utf-8'),
			'is_post'  => BOC()->getP_notification('is_sms_post_method'),
			'is_https' => BOC()->getP_notification('is_sms_https_protocol'),
		));
		if ( $order_type === 'multiple' )
			$smslog = $smsc->send_sms( $args['phone'], $this->sms_compose_multiple(BOC()->getP_notification('sms_template_multiple'), $args) );
		else
			$smslog = $smsc->send_sms( $args['phone'], $this->sms_compose_single(BOC()->getP_notification('sms_template_single'), $args) );
	}

	/**
	 * SMS compose single products
	 *
	 * @param string $message - SMS text (templates)
	 * @param array  $data    - Data replace template
	 *
	 * @return string
	 */
	protected function sms_compose_single($message, $data)
	{
		//Тэги замены
		$template = array(
			'%FULL_NAME%'  => $data['full_name'],
			'%PHONE%'      => $data['phone'],
			'%EMAIL%'      => $data['email'],
			'%P_PRICE%'    => $data['p_price'],
			'%P_NAME%'     => $data['p_title'],
			'%P_QUANTITY%' => $data['quantity'],
		);
		return strtr($message, $template);
	}

	/**
	 * SMS compose multiple products
	 *
	 * @param string $message - SMS text (templates)
	 * @param array  $data    - Data replace template
	 *
	 * @return string
	 */
	protected function sms_compose_multiple($message, $data)
	{
		//Тэги замены
		$template = array(
			'%FULL_NAME%' => $data['full_name'],
			'%PHONE%'     => $data['phone'],
			'%EMAIL%'     => $data['email'],
		);
		return strtr($message, $template);
	}

	/**
	 * Send Email Notification about order
	 *
	 * @param array $args - {
	 *      notification data
	 *
	 *      @type string @full_name  - Client Full Name
	 *      @type string @email      - Client Email
	 *      @type int    @phone      - Client Phonee
	 *      @type string @p_data - {
	 *          Product data
	 *
	 *          @type string @p_url    - Product name (URL)
	 *          @type float  @p_price  - Product Price
	 *          @type int    @quantity - Quantity
	 *          @type array  @attr     - Product Attributes (if product variation)
	 *      }
	 * }
	 */
	protected function send_email_notification_about_order( $args )
	{
		if ( ! BOC()->getP_notification('is_email_send') )
			return;

		// Arbitrary Info
		$arbitrary_info = '';
		if ( BOC()->getP_notification('is_arbitrary_info') )
			$arbitrary_info = BOC()->getP_notification('arbitrary_info');

		$message_data = array(
			'time'      => current_time('mysql'),
			'name_from' => BOC()->getP_notification('name_from_whom'),
			'arb_info'  => $arbitrary_info,
			'phone'     => $args['phone'],
			'full_name' => $args['full_name'],
			'p_data'    => $args['p_data'],
		);

		// Send message to Emails
		$copy_to_email = BOC()->getP_notification('name_from_whom');

		if ( !empty($args['email']) )
			$this->send_email_notification($args['email'], $message_data['name_from'], $this->email_html_template( $message_data ));
		if ( !empty($copy_to_email) )
			$this->send_email_notification($copy_to_email, $message_data['name_from'], $this->email_html_template( $message_data ));
	}

	/**
	 * Send Email Notification
	 *
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 */
	protected function send_email_notification($to, $subject, $message)
	{
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8 \r\n";
		$headers .= "From: " . BOC()->getP_notification('name_from_whom') . " <" . BOC()->getP_notification('email_from_whom') . ">\r\n";

		// Send email
		wp_mail($to, $subject, $message, $headers);
	}

	/**
	 * Email html Template
	 *
	 * @param array $args - {
	 *      @type string @time
	 *      @type string @name_from
	 *      @type int    @phone
	 *      @type string @full_name
	 *      @type string @arb_info
	 *      @type array  @p_data {
	 *          @type string @p_url
	 *          @type string @p_price
	 *          @type string @quantity
	 *          @type array  @attr     - Product attributes
	 *      }
	 * }
	 *
	 * @return string
	 */
	protected function email_html_template( $args )
	{
		$html_message = '<table style="height: 255px; border-color: #5656ff;" border="2" width="579"><tbody>
			<tr><td style="border-color: #5656ff; text-align: center; vertical-align: middle;" colspan="2">' . $args['name_from'] . '</td></tr>
			<tr>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">Телефон</td>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">' . $args['phone'] . '</td>
			</tr>
			<tr>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">ФИО</td>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">' . $args['full_name'] . '</td>
			</tr>
			<tr>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;"> Дата: </td>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">' . $args['time'] . '</td>
			</tr>';

		foreach ( $args['p_data'] as $pVal )
		{
			$html_message .= '
			<tr>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">-------</td>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">-------</td>
			</tr>
			<tr>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">Наименование</td>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">' . $pVal['p_url'] . '</td>
			</tr>
			<tr>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;"> Цена: </td>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">' . $pVal['p_price'] . '</td>
			</tr>
			<tr>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;"> Кол-во: </td>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">' . $pVal['quantity'] . '</td>
			</tr>';

			foreach ( $pVal['attr'] as $attrKey => $attrVal )
			{
				$html_message .= '
					<tr>
						<td style="border-color: #5656ff; text-align: center; vertical-align: middle;"> '. $attrKey .'</td>
						<td style="border-color: #5656ff; text-align: center; vertical-align: middle;"> '. $attrVal .'</td>
					</tr>';
			}
		}

		$html_message .= '
			<tr>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">-------</td>
				<td style="border-color: #5656ff; text-align: center; vertical-align: middle;">-------</td>
			</tr>
			<tr><td style="border-color: #5656ff; text-align: center; vertical-align: middle;" colspan="2">' . $args['arb_info'] . '</td></tr>
			</tbody></table>';

		return $html_message;
	}

	/**
	 * Message error API
	 *
	 * @param string $text - Message|Notice
	 *
	 * @return string
	 */
	protected function message_error( $text )
	{
		return (string) apply_filters('wcsam_boc_form_error_text', $text );
	}
}
