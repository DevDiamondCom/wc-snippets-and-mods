<?php

namespace WCSAM\modules\product_filters\admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Pages - Settings Pages in Admin Panel
 *
 * @class   Admin_Pages
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters\admin
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
				'product_filters' => array(
					'menu_title' => 'Product filters',
					'page_title' => 'Product filters',
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
		add_filter('wcsam_tabs_product_filters', function($tabs)
		{
			return array_merge($tabs, array(
				//------------------------------------------------------------------
				//  Shortcodes
				//------------------------------------------------------------------
				'shortcodes' => array(
					'title_args' => array(
						'title'   => __("Shortcodes and API", WCSAM_PLUGIN_SLUG),
						'fa-icon' => 'fa-code',
//						'fa-icon' => 'fa-file-code-o',
					),
					'groups' => array(
						'set_1' => array(
							'group_args' => array(
								'title' => __("Filter by price", WCSAM_PLUGIN_SLUG),
								'desc'  => __("Shortcode, which filters the items by price. An advantage is that can be used in the text fields and in the code itself template.", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
//								'set_1' => array(
//									'field_args' => array(
//										'title' => __("Is Google Icon?", WCSAM_PLUGIN_SLUG),               // required
//										'desc'  => __("[ON = show, OFF = hide] icon", WCSAM_PLUGIN_SLUG),  // optional (default: '')
//										'id'    => 'social-set-block-id',                                 // optional (default: '')
//										'class' => 'social-set-block-class',                              // optional (default: '')
//									),
//									'fields' => array(
//										array(
//											'type'    => 'switch',               // required (switch, text, textarea, select, checkbox, radio, number)
//											'name'    => 'set_1_set_1',          // required (field name)
//											'default' => false,                  // optional (values for the {'switch' = true/false, 'checkbox' = array('value' => 'TEXT'), all other = string})
//											'title'   => 'Title field',          // optional (default: '')
//											'desc'    => 'OFF/ON button',        // optional (default: '')
//											'id'      => 'social-switch-id',     // optional (default: '')
//											'class'   => 'social-switch-class',  // optional (default: '')
//											'placeholder' => 'placeholder TEXT', // optional (default: '')
//											'min'     => 0,                      // optional (for number, etc field. default: '')
//											'max'     => 150,                    // optional (for number, etc field. default: '')
//											'step'    => 0.01,                   // optional (for number, etc field. default: '')
//										),
//									),
//								), // END 'set_1'
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