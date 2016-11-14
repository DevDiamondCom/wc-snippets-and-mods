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
				//  Widgets
				//------------------------------------------------------------------
				'widgets' => array(
					'title_args' => array(
						'title'   => __("Widgets", WCSAM_PLUGIN_SLUG),
						'fa-icon' => 'fa-cubes',
					),
					'groups' => array(
						'set_1' => array(
							'group_args' => array(
								'title' => __("General settings Widgets", WCSAM_PLUGIN_SLUG),
								'desc'  => __("General settings for widgets", WCSAM_PLUGIN_SLUG),
							),
							'fields' => array(
								'set_1' => array(
									'field_args' => array(
										'title' => __("Updated container", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Class name or ID name of the updated content", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'update_container_name',
											'desc'    => '<div><strong>Info: </strong>If nothing is specified, the page will be '.
												'updated on the SUBMIT event. If specified a container, it will be updated only '.
												'that part of the page that has specified container. Data is updated on the AJAX</div>'.
												'<br><div><strong>Example: </strong></div><img src="'. PF_ASSETS_URL .'img/set-01.png">',
										),
									),
								),
								'set_2' => array(
									'field_args' => array(
										'title' => __("Loader IMG", WCSAM_PLUGIN_SLUG),
										'desc'  => __("Loader image URL", WCSAM_PLUGIN_SLUG),
									),
									'fields' => array(
										array(
											'type'    => 'text',
											'name'    => 'loader_img_url',
											'default' => WCSAM_ASSETS_URL .'img/ajax-loader-balls_150.gif',
											'desc'    => '<div><strong>Enter your loader or one of the following list</strong></div>'.
												'<div>'. WCSAM_ASSETS_URL .'img/ajax-loader-balls_150.gif</div>'.
												'<div>'. WCSAM_ASSETS_URL .'img/ajax-loader-big.gif</div>'.
												'<div>'. WCSAM_ASSETS_URL .'img/ajax-loader-medium.gif</div>'.
												'<div>'. WCSAM_ASSETS_URL .'img/ajax-loader-small.gif</div>',
										),
									),
								), // END 'set_1'
							),
						), // END 'set_1'
					),
				),
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
						), // END 'set_1'
					),
				), // END Info Tab
			));
		});
	}
}

new Admin_Pages;