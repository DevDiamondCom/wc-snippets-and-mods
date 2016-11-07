<?php

namespace WCSAM\modules\buy_one_click\admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin - Admin Panel Settings
 *
 * @class   Admin
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\buy_one_click\admin
 * @version 1.0.0
 */
class Admin
{
	/**
	 * Admin constructor.
	 */
	public function __construct()
	{
		$this->init_hooked();
		$this->init_includes();
	}

	/**
	 * Added hooked's
	 */
	private function init_hooked()
	{
		// Add Scripts and Styles
		add_action( 'init', array($this, 'add_scripts'), 99 );
		add_action( 'init', array($this, 'add_styles'), 99 );
	}

	/**
	 * Load Admin Page includes
	 */
	private function init_includes()
	{
		require_once 'class.boc-admin-pages.php';
	}

	/**
	 * Add Admin Styles
	 */
	public function add_styles()
	{
		// Buy one click main Admin CSS
		wp_enqueue_style(
			'buy-one-click-admin',
			BOC_ASSETS_URL . 'admin/css/admin-main.css'
		);
	}

	/**
	 * Add Admin Scripts
	 */
	public function add_scripts()
	{
		// Buy one click main Admin JS
		wp_enqueue_script(
			'buy-one-click-admin',
			BOC_ASSETS_URL . 'admin/js/admin-main.js',
			array('jquery')
		);
	}
}

new Admin;