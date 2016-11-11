<?php

namespace WCSAM\modules\buy_one_click;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Buy_One_Click - Product filters, prices, colors, etc
 *
 * @class   Buy_One_Click
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\buy_one_click
 * @version 1.0.0
 */
class Buy_One_Click
{
	const MODULE_NAME = 'buy-one-click';

	/**
	 * The single instance of the class.
	 *
	 * @static
	 * @var Buy_One_Click
	 */
	protected static $_instance = null;

	/**
	 * Main Ajax_Add_To_Cart Instance.
	 *
	 * @static
	 * @return Buy_One_Click - Main instance.
	 */
	public static function instance()
	{
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone(){}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup(){}

	/**
	 * Buy_One_Click constructor.
	 */
	public function __construct()
	{
		if ( ! is_null( self::$_instance ) )
			return;

		$this->add_defines();
		$this->init_includes();
	}

	/**
	 * Defines
	 */
	private function add_defines()
	{
		define('BOC_ASSETS_URL', WCSAM_MODULES_URL . self::MODULE_NAME .'/assets/');
	}

	/**
	 * Initialization Module files
	 */
	private function init_includes()
	{
		require_once 'abstracts/abstract.boc-frontend.php';
		require_once 'frontend/class.boc-frontend.php';
		require_once 'shortcodes/class.boc-shortcodes.php';

		if ( is_admin() )
			require_once 'admin/class.boc-admin.php';
	}

	/**
	 * Get Param - General Tab
	 *
	 * @param mixed  $default - Return default param
	 * @param string $param   - Param name {
	 *      Params
	 *      @uses [is_boc_button_in_product]  -
	 *      @uses [boc_button_name_in_product]  -
	 *      @uses [boc_button_position_in_product]  -
	 *
	 *      @uses [is_boc_button_in_category]  -
	 *      @uses [boc_button_name_in_category]  -
	 *      @uses [boc_button_position_in_category]  -
	 *
	 *      @uses [is_boc_button_in_cart]  -
	 *      @uses [boc_button_name_in_cart]  -
	 *      @uses [boc_button_position_in_cart]  -
	 * }
	 *
	 * @return mixed
	 */
	public function getP_general( $param = '', $default = false )
	{
		static $get_param_general;

		if ( !isset($get_param_general) )
			$get_param_general = wcsam_get_option('buy_one_click', 'general');

		if ( ! $param )
			return $get_param_general;

		if ( isset( $get_param_general[ $param ] ) )
			return $get_param_general[ $param ];
		else
			return $default;
	}

	/**
	 * Get Param - Form Settings Tab
	 *
	 * @param mixed  $default - Return default param
	 * @param string $param   - Param name {
	 *      Params
	 *      @uses [is_product_info_modal]  -
	 *      @uses [product_info_modal_position]  -
	 *
	 *      @uses [is_field_full_name]  -
	 *      @uses [is_required_field_full_name]  -
	 *      @uses [field_full_name]  -
	 *
	 *      @uses [is_field_phone]  -
	 *      @uses [is_required_field_phone]  -
	 *      @uses [field_phone]  -
	 *
	 *      @uses [is_field_email]  -
	 *      @uses [is_required_field_email]  -
	 *      @uses [field_email]  -
	 *
	 *      @uses [is_field_additional_info]  -
	 *      @uses [is_required_field_additional_info]  -
	 *      @uses [field_additional_info]  -
	 *
	 *      @uses [send_btn_name]  -
	 *
	 *      @uses [success_message_text]  -
	 *
	 *      @uses [click_order_btn]  -
	 *      @uses [click_order_btn_close_msec]  -
	 *      @uses [click_order_btn_message]  -
	 *      @uses [click_order_btn_redirect_url]  -
	 * }
	 *
	 * @return mixed
	 */
	public function getP_form_settings( $param = '', $default = false )
	{
		static $get_param_general;

		if ( !isset($get_param_general) )
			$get_param_general = wcsam_get_option('buy_one_click', 'form_settings');

		if ( ! $param )
			return $get_param_general;

		if ( isset( $get_param_general[ $param ] ) )
			return $get_param_general[ $param ];
		else
			return $default;
	}

	/**
	 * Get Param - Notification Tab
	 *
	 * @param mixed  $default - Return default param
	 * @param string $param   - Param name {
	 *      Params
	 *      @uses [is_email_send]  -
	 *      @uses [name_from_whom]  -
	 *      @uses [email_from_whom]  -
	 *      @uses [copy_to_email]  -
	 *      @uses [info_about_ordering]  -
	 *      @uses [is_arbitrary_info]  -
	 *      @uses [arbitrary_info]  -
	 *
	 *      @uses [is_sms_send]  -
	 *      @uses [smsc_login]  -
	 *      @uses [smsc_password]  -
	 *      @uses [is_sms_post_method]  -
	 *      @uses [is_sms_https_protocol]  -
	 *      @uses [sms_encoding]  -
	 *      @uses [sms_template_single]  -
	 *      @uses [sms_template_multiple]  -
	 * }
	 *
	 * @return mixed
	 */
	public function getP_notification( $param = '', $default = false )
	{
		static $get_param_notification;

		if ( !isset($get_param_notification) )
			$get_param_notification = wcsam_get_option('buy_one_click', 'notification');

		if ( ! $param )
			return $get_param_notification;

		if ( isset( $get_param_notification[ $param ] ) )
			return $get_param_notification[ $param ];
		else
			return $default;
	}
}
