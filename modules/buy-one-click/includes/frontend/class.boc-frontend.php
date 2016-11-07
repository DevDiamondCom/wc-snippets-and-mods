<?php

namespace WCSAM\modules\buy_one_click\frontend;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Frontend
 *
 * @class   Frontend
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\buy_one_click\frontend
 * @version 1.0.0
 */
class Frontend
{
	const AJAX_ACTION_SLUG = 'wcsam_boc_';

	const NONCE_SLUG = 'zs32gva6s87g';

	/**
	 * Frontend constructor.
	 */
	public function __construct()
	{
		add_action('init', array($this, 'init'));
	}

	/**
	 * Init WP
	 */
	public function init()
	{
		$this->init_hooked();
		$this->init_includes();
	}

	/**
	 * Load Frontend includes
	 */
	private function init_includes()
	{
		require_once 'class.boc-frontend-api.php';
	}

	/**
	 * Added hooked's
	 */
	private function init_hooked()
	{
		// In Product page
		if ( BOC()->getP_general('is_boc_button_in_product') )
			add_action(BOC()->getP_general('boc_button_position_in_product'), array($this, 'in_product'));

		// In Category
		if ( BOC()->getP_general('is_boc_button_in_category') )
			add_action(BOC()->getP_general('boc_button_position_in_category'), array($this, 'in_category'));

		// In Cart
		if ( BOC()->getP_general('is_boc_button_in_cart') )
			add_action(BOC()->getP_general('boc_button_position_in_cart'), array($this, 'in_cart'));
	}

	/**
	 * Load Buy one click in product
	 *
	 * @static
	 */
	public static function in_product()
	{
		global $product;

		self::add_scripts('product');
		self::add_styles();

		?>
		<a class="boc_button boc_btn_product" href="#" data-product_id="<?= $product->id ?>" data-product_type="<?= (string)$product->product_type ?>">
			<?= BOC()->getP_general('boc_button_name_in_product') ?></a>
		<?php
	}

	/**
	 * Load Buy one click in category
	 *
	 * @static
	 */
	public static function in_category()
	{
		global $product;

		self::add_scripts('category');
		self::add_styles();

		?>
		<a class="boc_button boc_btn_category" href="#" data-product_id="<?= $product->id ?>" data-product_type="<?= (string)$product->product_type ?>">
			<?= BOC()->getP_general('boc_button_name_in_category') ?></a>
		<?php
	}

	/**
	 * Load Buy one click in cart
	 *
	 * @static
	 */
	public static function in_cart()
	{
		self::add_scripts('cart');
		self::add_styles();

		?>
		<a class="boc_button boc_btn_cart" href="#"><?= BOC()->getP_general('boc_button_name_in_cart') ?></a>
		<?php
	}

	/**
	 * Add Frontend styles
	 *
	 * @static
	 */
	private static function add_styles()
	{
		wp_enqueue_style('boc-button', BOC_ASSETS_URL . 'css/boc-button.css');
		wp_enqueue_style('boc-form', BOC_ASSETS_URL . 'css/boc-form.css');
	}

	/**
	 * Add Frontend Scripts
	 *
	 * @static
	 * @param string $button_type - {product, category, cart}
	 */
	private static function add_scripts( $button_type )
	{
		// Buy One Click Form Script
		wp_enqueue_script(
			'boc-form',
			BOC_ASSETS_URL . 'js/boc-form.js',
			array('jquery'),
			null,
			true
		);
		$boc_form_script = 'var boc_obj = ';
		$boc_form_script .= json_encode(array(
			'ajaxurl'    => admin_url('admin-ajax.php'),
			'loader_img' => WCSAM_ASSETS_URL .'img/ajax-loader-medium.gif',
			'form_type'  => $button_type,
			'actions'    => array(
				'get_form_block' => self::AJAX_ACTION_SLUG . 'get_form_block',
				'add_new_order'  => self::AJAX_ACTION_SLUG . 'add_new_order',
			),
		));
		$boc_form_script .= ';';
		wp_add_inline_script(
			'boc-form',
			$boc_form_script,
			'before'
		);
	}

}

new Frontend();