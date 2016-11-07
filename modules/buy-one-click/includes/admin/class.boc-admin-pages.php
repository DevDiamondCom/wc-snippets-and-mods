<?php

namespace WCSAM\modules\buy_one_click\admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Pages - Settings Pages in Admin Panel
 *
 * @class   Admin_Pages
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\buy_one_click\admin
 * @version 1.0.0
 */
class Admin_Pages
{
	/**
	 * Admin_Pages constructor.
	 */
	public function __construct()
	{
		$this->add_menus();
		$this->add_tab_pages();
	}

	/**
	 * Add new menus in the WCSAM
	 */
	private function add_menus()
	{
		add_filter('wcsam_submenu', function($submunu)
		{
			return array_merge($submunu, array(
				'buy_one_click' => array(
					'menu_title' => 'Buy One Click',
					'page_title' => 'Buy One Click',
					'capability' => 'manage_options',
				),
			));
		});
	}

	/**
	 * Add Tab Pages
	 */
	private function add_tab_pages()
	{
		add_filter('wcsam_tabs_buy_one_click', function($tabs)
		{
			return array_merge($tabs, array(
				//------------------------------------------------------------------
				//  General Settings
				//------------------------------------------------------------------
				'general' => array(
					'title_args' => array(
						'title'   => __("General Settings", WCSAM_PLUGIN_SLUG),
						'fa-icon' => 'fa-gear',
					),
					'groups' => array(
						'set_1' => array(
							'group_args' => array(
								'title' => __("In Product page", WCSAM_PLUGIN_SLUG),
								'desc'  => __("A button in the Product page", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("Show button?", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Button of the 'Buy one click' in Product page", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_boc_button_in_product',
											'default' => true,
										),
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("Button Name", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Example: Buy one click", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'boc_button_name_in_product',
											'default' => '',
										),
									),
								),
								'set_3' => array(
									'field_args' => array(
										'title' => __("Button position", WCSAM_PLUGIN_SLUG),
										'desc'  => __("The place where the button will be located", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'select',
											'name'    => 'boc_button_position_in_product',
											'default' => 'simple_add_to_cart',
											'data'    => array(
												'woocommerce_simple_add_to_cart'            => __('Above the number of button', WCSAM_PLUGIN_SLUG),
												'woocommerce_product_description_heading'   => __('The tab item description', WCSAM_PLUGIN_SLUG),
												'woocommerce_before_single_product'         => __('Above the image of product', WCSAM_PLUGIN_SLUG),
												'woocommerce_before_single_product_summary' => __('Over the full information about the product', WCSAM_PLUGIN_SLUG),
												'woocommerce_after_single_product_summary'  => __('Under complete information about the product', WCSAM_PLUGIN_SLUG),
											),
										),
									),
								), // END 'set_3'
							),
						),
						'set_2' => array(
							'group_args' => array(
								'title' => __("In Category", WCSAM_PLUGIN_SLUG),
								'desc'  => __("A button in the Category", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("Show button?", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Button of the 'Buy one click' in Category", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_boc_button_in_category',
											'default' => '',
										)
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("Button Name", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Example: Buy one click", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'boc_button_name_in_category',
											'default' => '',
										),
									),
								),
								'set_3' => array(
									'field_args' => array(
										'title' => __("A button in the category", WCSAM_PLUGIN_SLUG),
										'desc'  => __("The place where the button will be located", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'select',
											'name'    => 'boc_button_position_in_category',
											'default' => 'after_shop_loop_item_title',
											'data'    => array(
												'woocommerce_before_shop_loop_item_title' => __('Above product', WCSAM_PLUGIN_SLUG),
												'woocommerce_after_shop_loop_item_title'  => __('Under the title the goods until price', WCSAM_PLUGIN_SLUG),
												'woocommerce_after_shop_loop_item'        => __('Under product', WCSAM_PLUGIN_SLUG),
											),
										),
									),
								), // END 'set_3'
							),
						),
						'set_3' => array(
							'group_args' => array(
								'title' => __("In Cart", WCSAM_PLUGIN_SLUG),
								'desc'  => __("A button in the Cart", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("Show button?", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Button of the 'Buy one click' in Cart", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_boc_button_in_cart',
											'default' => '',
										)
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("Button Name", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Example: Buy one click", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'boc_button_name_in_cart',
											'default' => '',
										),
									),
								),
								'set_3' => array(
									'field_args' => array(
										'title' => __("A button in the category", WCSAM_PLUGIN_SLUG),
										'desc'  => __("The place where the button will be located", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'select',
											'name'    => 'boc_button_position_in_cart',
											'default' => 'cart_actions',
											'data'    => array(
												'woocommerce_cart_contents'  => __('In cart contents', WCSAM_PLUGIN_SLUG),
												'woocommerce_cart_actions'        => __('In cart actions', WCSAM_PLUGIN_SLUG),
												'woocommerce_after_cart_contents' => __('Under cart list', WCSAM_PLUGIN_SLUG),
											),
										),
									),
								), // END 'set_3'
							),
						),
					),
				),
				//------------------------------------------------------------------
				//  Form settings
				//------------------------------------------------------------------
				'form_settings' => array(
					'title_args' => array(
						'title'   => __("Form settings", WCSAM_PLUGIN_SLUG),
						'fa-icon' => 'fa-gear',
					),
					'groups' => array(
						'set_1_1' => array(
							'group_args' => array(
								'title' => __("Fields and buttons", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("Add field Full name?", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_field_full_name',
											'default' => true,
											'title'   => __("Status", WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'    => 'switch',
											'name'    => 'is_required_field_full_name',
											'default' => true,
											'title'   => __("Required field?", WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'  => 'text',
											'name'  => 'field_full_name',
											'title' => __("Placeholder (Example: Your name)", WCSAM_PLUGIN_SLUG),
										),
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("Add field Phone?", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_field_phone',
											'default' => true,
											'title'   => __("Status", WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'    => 'switch',
											'name'    => 'is_required_field_phone',
											'default' => true,
											'title'   => __("Required field?", WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'  => 'text',
											'name'  => 'field_phone',
											'title' => __("Placeholder (Example: Your phone number)", WCSAM_PLUGIN_SLUG),
										),
									),
								),
								'set_3' => array(
									'field_args' => array(
										'title' => __("Add field Email?", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_field_email',
											'default' => false,
											'title'   => __("Status", WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'    => 'switch',
											'name'    => 'is_required_field_email',
											'default' => false,
											'title'   => __("Required field?", WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'  => 'text',
											'name'  => 'field_email',
											'title' => __("Placeholder (Example: Your Email address)", WCSAM_PLUGIN_SLUG),
										),
									),
								),
								'set_4' => array(
									'field_args' => array(
										'title' => __("Add field additional information?", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_field_additional_info',
											'default' => false,
											'title'   => __("Status", WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'    => 'switch',
											'name'    => 'is_required_field_additional_info',
											'default' => false,
											'title'   => __("Required field?", WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'  => 'text',
											'name'  => 'field_additional_info',
											'title' => __("Placeholder (Example: Delivery address)", WCSAM_PLUGIN_SLUG),
										),
									),
								),
								'set_5' => array(
									'field_args' => array(
										'title' => __("Name the buttons in the form of", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Default: To order", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'send_btn_name',
											'default' => 'To order',
										),
									),
								), // END 'set_5'
							),
						),
						'set_1' => array(
							'group_args' => array(
								'title' => __("Information on the order form", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("Show information about a product?", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Display product information in a modal window?", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_product_info_modal',
											'default' => true,
										),
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("Position block about the product", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'select',
											'name'    => 'product_info_modal_position',
											'default' => 'after_fields',
											'data'    => array(
												'before_fields' => 'Before fields',
												'after_fields'  => 'After fields',
											),
										),
									),
								),
							),
						),
						'set_2' => array(
							'group_args' => array(
								'title' => __("Actions and Notifications", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("The message about the successful registration of the order.", WCSAM_PLUGIN_SLUG),
										'desc'  => __('For example: "Thank you for your order!".', WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type' => 'text',
											'name' => 'success_message_text',
											'desc' => __('The message appears in the order form after a user has clicked the order confirmation.', WCSAM_PLUGIN_SLUG),
										),
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("After pressing the button Order", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'select',
											'name'    => 'click_order_btn',
											'id'      => 'click_order_btn',
											'default' => '',
											'data'    => array(
												'none'         => __('To do nothing', WCSAM_PLUGIN_SLUG),
												'close_n_sec'  => __('Close by milliseconds:', WCSAM_PLUGIN_SLUG),
												'show_message' => __('Show message (possible html)', WCSAM_PLUGIN_SLUG),
												'redirect_to'  => __('Make the a redirect to a page', WCSAM_PLUGIN_SLUG),
											),
										),
										array(
											'type'  => 'number',
											'name'  => 'click_order_btn_close_msec',
											'id'    => 'click_order_btn_close_msec',
											'title' => __('Milliseconds', WCSAM_PLUGIN_SLUG),
											'desc'  => __('For example: "1600". The user will see a message and form will be closed after the above specified time.', WCSAM_PLUGIN_SLUG),
											'min'   => '0',
											'step'  => '1',
										),
										array(
											'type'  => 'textarea',
											'name'  => 'click_order_btn_message',
											'id'    => 'click_order_btn_message',
											'title' => __('Message', WCSAM_PLUGIN_SLUG),
										),
										array(
											'type'  => 'text',
											'name'  => 'click_order_btn_redirect_url',
											'id'    => 'click_order_btn_redirect_url',
											'title' => __('Redirect URL', WCSAM_PLUGIN_SLUG),
											'desc'  => __('For example: "http://devdiamond.com". The user will see a message and gets on a given page', WCSAM_PLUGIN_SLUG),
										),
									),
								),
							),
						),// END 'set_2'
					),
				),
				//------------------------------------------------------------------
				//  Notification
				//------------------------------------------------------------------
				'notification' => array(
					'title_args' => array(
						'title'   => __("Notification", WCSAM_PLUGIN_SLUG),
						'fa-icon' => 'fa-gear',
					),
					'groups' => array(
						'set_1' => array(
							'group_args' => array(
								'title' => __("E-mail Settings", WCSAM_PLUGIN_SLUG),
								'desc'  => __("Configuring E-mail Notifications", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1_1' => array(
									'field_args' => array(
										'title' => __("Enable Email notifications?", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_email_send',
											'default' => false,
										),
									),
								),
								'set_1' => array(
									'field_args' => array(
										'title' => __("Name", WCSAM_PLUGIN_SLUG),
										'desc'  => __("From whom", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'        => 'text',
											'name'        => 'name_from_whom',
											'default'     => '',
											'placeholder' => 'DevDiamond',
										),
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("Email", WCSAM_PLUGIN_SLUG),
										'desc'  => __("From whom", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'        => 'text',
											'name'        => 'email_from_whom',
											'default'     => '',
											'placeholder' => 'me@devdiamond.com',
										),
									),
								),
								'set_3' => array(
									'field_args' => array(
										'title' => __("Copy to the email", WCSAM_PLUGIN_SLUG),
										'desc'  => __("This email will be sent a copy of the order of messages.", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'        => 'text',
											'name'        => 'copy_to_email',
											'default'     => '',
											'placeholder' => 'me@devdiamond.com',
											'desc'        => __('Сan specify multiple Email a comma ",". Example: shop1@example.com, shop2@example.com, etc', WCSAM_PLUGIN_SLUG),
										),
									),
								),
								'set_4' => array(
									'field_args' => array(
										'title' => __("Info about ordering", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Send client the order data.", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'info_about_ordering',
											'default' => false,
										),
									),
								),
								'set_5' => array(
									'field_args' => array(
										'title' => __("Arbitrary information", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Sent additional data. You can specify custom text.", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_arbitrary_info',
											'default' => false,
										),
										array(
											'type'        => 'textarea',
											'name'        => 'arbitrary_info',
											'placeholder' => __('Your arbitrary text', WCSAM_PLUGIN_SLUG),
											'title'       => __('Arbitrary information', WCSAM_PLUGIN_SLUG),
											'desc'        => __('Such as contacts or desire. It is possible to specify the HTML tags', WCSAM_PLUGIN_SLUG),
										),
									),
								), // END 'set_5'
							),
						),
						'set_2' => array(
							'group_args' => array(
								'title' => __("SMS Settings", WCSAM_PLUGIN_SLUG),
								'desc'  => __("Configuring SMS Notifications", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("Enable SMS notifications?", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_sms_send',
											'default' => false,
										),
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("Login", WCSAM_PLUGIN_SLUG),
										'desc'  => sprintf(__("Your login from the service %s", WCSAM_PLUGIN_SLUG), 'SMSC' ),
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'smsc_login',
											'default' => '',
										),
									),
								),
								'set_3' => array(
									'field_args' => array(
										'title' => __("Password", WCSAM_PLUGIN_SLUG),
										'desc'  => sprintf(__("Your password from the service %s", WCSAM_PLUGIN_SLUG), 'SMSC' ),
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'smsc_password',
											'default' => '',
										),
									),
								),
								'set_4' => array(
									'field_args' => array(
										'title' => __("Use the POST method", WCSAM_PLUGIN_SLUG),
										'desc'  => "Default 'OFF'",
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_sms_post_method',
											'default' => '',
										),
									),
								),
								'set_5' => array(
									'field_args' => array(
										'title' => __("Use HTTPS protocol", WCSAM_PLUGIN_SLUG),
										'desc'  => "Default 'OFF'",
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'is_sms_https_protocol',
											'default' => '',
										),
									),
								),
								'set_6' => array(
									'field_args' => array(
										'title' => __("Encoding SMS", WCSAM_PLUGIN_SLUG),
										'desc'  => "Default 'UTF-8'",
									),
									'fields' => array(
										array(
											'type'    => 'select',
											'name'    => 'sms_encoding',
											'default' => 'utf-8',
											'data'    => array(
												'utf-8'        => 'UTF-8',
												'koi8-r'       => 'KOI8_R',
												'windows-1251' => 'WINDOWS-1251',
											),
										),
									),
								),
								'set_7' => array(
									'field_args' => array(
										'title' => __("Template SMS messages for single orders", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Orders from category and product pages", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'textarea',
											'name'    => 'sms_template_single',
											'default' => '',
											'desc'    => $this->sms_template_single(),
										),
									),
								),
								'set_8' => array(
									'field_args' => array(
										'title' => __("Template SMS messages for multiple orders", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Orders from cart", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'textarea',
											'name'    => 'sms_template_multiple',
											'default' => '',
											'desc'    => $this->sms_template_multiple(),
										),
									),
								), // END 'set_7'
							),
						), // END 'set_2'
					),
				),
				//------------------------------------------------------------------
				//  Help page tab
				//------------------------------------------------------------------
//				'help' => array(),
			));
		});
	}

	/**
	 * SMS Template Single Products
	 *
	 * @return string
	 */
	private function sms_template_single()
	{
		return '<div class="boc_sms_template">
			<span><strong>%FULL_NAME%</strong> &nbsp;&nbsp;&mdash; Full name</span>
			<span><strong>%PHONE%</strong> &nbsp;&nbsp;&mdash; Phone</span>
			<span><strong>%EMAIL%</strong> &nbsp;&nbsp;&mdash; Email</span>
			<span><strong>%P_PRICE%</strong> &nbsp;&nbsp;&mdash; Product Price</span>
			<span><strong>%P_NAME%</strong> &nbsp;&nbsp;&mdash; Product Name (title)</span>
			<span><strong>%P_QUANTITY%</strong> &nbsp;&nbsp;&mdash; Product Quantity</span>
		</div>';
	}

	/**
	 * SMS Template Multiple Products
	 *
	 * @return string
	 */
	private function sms_template_multiple()
	{
		return '<div class="boc_sms_template">
			<span><strong>%FULL_NAME%</strong> &nbsp;&nbsp;&mdash; Full name</span>
			<span><strong>%PHONE%</strong> &nbsp;&nbsp;&mdash; Phone</span>
			<span><strong>%EMAIL%</strong> &nbsp;&nbsp;&mdash; Email</span>
		</div>';
	}
}

new Admin_Pages;