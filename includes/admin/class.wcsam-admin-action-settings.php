<?php
/**
 * Actions Settings WCSAM
 *
 * Manipulation of settings WCSAM
 *
 * @class    Admin_Action_Settings
 * @author   DevDiamond <me@devdiamond.com>
 * @package  WC_Snippets_And_Mods/Admin
 * @version  1.0.0
 */

namespace WCSAM\admin;

use WCSAM;
use WCSAM\admin\Admin_Menus;
use WCSAM\admin\pages\Admin_Pages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Action_Settings - Actions Settings WCSAM
 */
class Admin_Action_Settings
{
	/**
	 * Admin_Action_Settings constructor.
	 */
	public function __construct()
	{
		// check POST data from wcsam
		if ( ! isset($_POST['wcsam_action']) || $_POST['wcsam_action'] !== 'wcsam_update' ||
			 empty( $_POST['page_slug'] ) || empty( $_POST['tab_slug'] ) )
			return;

		// check user can
		if ( $_POST['page_slug'] === Admin_Menus::MAIN_MENU_SLUG )
		{
			if ( ! current_user_can( Admin_Menus::MAIN_MENU_CAPABILITY ) )
				return;
		}
		else
		{
			if ( ! isset(Admin_Menus::$submenu[ $_POST['page_slug'] ], Admin_Menus::$submenu[ $_POST['page_slug'] ]['capability']) )
				return;
			elseif ( ! current_user_can( Admin_Menus::$submenu[ $_POST['page_slug'] ]['capability'] ) )
				return;
		}

		// check NONCE data from wcsam
		if ( ! check_admin_referer('wcsam_action_update_'. $_POST['page_slug'] .'_'. $_POST['tab_slug'] , 'wcsam_set_id') )
			return;

		// Update data
		if ( isset($_POST['bt_save_settings']) )
			$this->update_settings_post($_POST['page_slug'], $_POST['tab_slug']);
		// Reset data
		elseif ( isset($_POST['bt_reset_settings']) )
			$this->reset_settings_post($_POST['page_slug'], $_POST['tab_slug']);
	}

	/**
	 * Refresh the data from the post request
	 *
	 * @param string $page_slug  - Page slug
	 * @param string $tab_slug   - Tab slug
	 */
	private function update_settings_post( $page_slug, $tab_slug )
	{
		unset($_POST['page_slug'], $_POST['tab_slug']);
		unset($_POST['wcsam_action'], $_POST['wcsam_set_id'], $_POST['_wp_http_referer']);
		unset($_POST['bt_save_settings'], $_POST['bt_reset_settings']);

		self::_update_option( $page_slug, $tab_slug, $_POST, false );
	}

	/**
	 * Reset data
	 *
	 * @param string $page_slug  - Page slug
	 * @param string $tab_slug   - Tab slug
	 */
	private function reset_settings_post( $page_slug, $tab_slug )
	{
		// get page data
		$default_data = Admin_Pages::pages( $page_slug, $tab_slug );
		if ( $default_data === false )
		{
			$default_data = (array) apply_filters('wcsam_tabs_'.$page_slug, array());
			$default_data = @$default_data[ $tab_slug ] ?: array();
		}
		if ( isset($default_data['template']) )
			$default_data = (array) @$default_data['template']['default_fields'] ?: array();
		elseif ( isset($default_data['groups']) )
			$default_data = $this->default_fields_data( $default_data['groups'] );

		self::_update_option( $page_slug, $tab_slug, $default_data, false );
	}

	/**
	 * Update option
	 *
	 * @static
	 * @param  string       $page_slug  - Page slug
	 * @param  string       $tab_slug   - Tab slug
	 * @param  array|string $options    - Update option data
	 * @param  bool|true    $is_merge   - Is merge data
	 */
	private static function _update_option( $page_slug, $tab_slug, $options, $is_merge = true )
	{
		if ( $is_merge === true )
		{
			$option = get_option( WCSAM::OPTIONS_PREFIX . $page_slug . '_' . $tab_slug );
			$option = ( false === $option ) ? array() : (array) $option;
			$option = array_merge( $option, (array) $options );
		}
		elseif ( $is_merge === false )
			$option = (array) $options;
		else
			return;

		update_option( WCSAM::OPTIONS_PREFIX . $page_slug . '_' . $tab_slug, $option );
	}

	/**
	 * Default fields data
	 *
	 * @static
	 * @return array
	 */
	private function default_fields_data( &$data )
	{
		$data = array();
		foreach ( $data as $tVal )
		{
			if ( ! isset($tVal['fields']) )
				continue;

			foreach ( $tVal['fields'] as $gVal )
			{
				if ( ! isset($gVal['fields']) )
					continue;

				foreach ( $gVal['fields'] as $fVal )
				{
					if ( ! isset( $fVal['type'], $fVal['name'] ) )
						continue;

					if ( ! isset($fVal['default']) )
						$fVal['default'] = ($fVal['type'] === 'checkbox' ? array() : '');
					elseif ( $fVal['type'] === 'checkbox' )
						$fVal['default'] = (array) $fVal['default'];
					elseif ( $fVal['type'] === 'switch' )
						$fVal['default'] = (bool) $fVal['default'];
					else
						$fVal['default'] = (string) $fVal['default'];

					$data[ $fVal['name'] ] = $fVal['default'];
				}
			}
		}

		return $data;
	}
}

new Admin_Action_Settings;