<?php

namespace WCSAM\modules\ajax_add_to_cart\admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Pages - Settings Pages in Admin Panel
 *
 * @class   Admin_Pages
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\ajax_add_to_cart\admin
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
	 * Add new PF menus in the WCSAM
	 */
	private function add_menus()
	{
		add_filter('wcsam_submenu', function($submunu)
		{
			return array_merge($submunu, array(
				'ajax_add_to_card' => array(
					'menu_title' => 'Ajax Add to card',
					'page_title' => 'Ajax Add to card',
					'capability' => 'manage_options',
				),
			));
		});
	}

	/**
	 * Add Tab Pages PF
	 */
	private function add_tab_pages()
	{
		add_filter('wcsam_tabs_ajax_add_to_card', function($tabs)
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
								'title' => __("Variable Products Settings", WCSAM_PLUGIN_SLUG),
								'desc'  => __('These settings for the "Add to card" button to variative products.', WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("Add selection option to Category Page", WCSAM_PLUGIN_SLUG),
										'desc'  => __("This will automatically insert variable selection options on product Category Archive Page", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'switch',
											'name'    => 'is_variation_category_page',
											'default' => false,
										),
									),
								), // END 'set_1'
							),
						), // END 'set_1'
					),
				),
				//------------------------------------------------------------------
				//  Help page tab
				//------------------------------------------------------------------
//				'help' => array(),
			));
		});
	}
}

new Admin_Pages;