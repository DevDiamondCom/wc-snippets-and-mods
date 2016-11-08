<?php

namespace WCSAM\modules\ajax_update_qty_in_the_cart\admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Pages - Settings Pages in Admin Panel
 *
 * @class   Admin_Pages
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\ajax_update_qty_in_the_cart\admin
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
				'auiqc' => array(
					'menu_title' => 'Ajax update of quantity in the cart',
					'page_title' => 'Ajax update of quantity in the cart',
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
		add_filter('wcsam_tabs_auiqc', function($tabs)
		{
			return array_merge($tabs, array(
				//------------------------------------------------------------------
				//  Info page tab
				//------------------------------------------------------------------
				'info' => array(
					'title_args' => array(
						'title'   => __("Info", WCSAM_PLUGIN_SLUG),
						'fa-icon' => 'fa-info-circle',
					),
					'groups' => array(
						'set_1' => array(
							// Edit
						), // END 'set_2'
					),
				), // END help Tab
			));
		});
	}
}

new Admin_Pages;