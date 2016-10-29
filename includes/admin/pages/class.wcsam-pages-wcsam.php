<?php

namespace WCSAM\admin\pages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Pages_Wcsam - Default pages data
 *
 * @class   Admin_Pages_Wcsam
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\admin\pages
 * @version 1.0.0
 */
class Admin_Pages_Wcsam extends Admin_Pages
{
	protected static function pages_wcsam()
	{
		self::wcsam_extensions();
		self::wcsam_info();
	}

	private function wcsam_extensions()
	{
		// Add Extensions page tab
		self::$tabs['extensions'] = array(
			'title_args' => array(
				'title'   => __("Extensions", WCSAM_PLUGIN_SLUG),
				'id'      => 'wcsam-extensions',
				'fa-icon' => 'fa-plug',
			),
			'template' => array(
				'is_form' => ! empty(\WCSAM()->modules),
			),
		);
		add_action('wcsam_page_tab_extensions', function(){
			include_once WCSAM_TEMPLATES_DIR . 'menu-pages/wcsam-extensions.php';
		});
	}

	private function wcsam_info()
	{
		// Add Info page tab
		self::$tabs['info'] = array(
			'title_args' => array(
				'title'   => __("Info", WCSAM_PLUGIN_SLUG),
				'id'      => 'wcsam-info',
				'fa-icon' => 'fa-info-circle',
			),
			'template' => array(),
		);
		add_action('wcsam_page_tab_info', function(){
			include_once WCSAM_TEMPLATES_DIR . 'menu-pages/wcsam-info.php';
		});
	}
}