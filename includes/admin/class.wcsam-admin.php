<?php
/**
 * WP Admin Panel
 *
 * @class    Admin
 * @author   DevDiamond <me@devdiamond.com>
 * @package  WC_Snippets_And_Mods/Admin
 * @version  1.0.0
 */

namespace WCSAM\admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin - Admin Panel set.
 */
class Admin
{
	/**
	 * Admin constructor.
	 */
	public function __construct()
	{
		add_action( 'init', array( $this, 'includes_init' ) );
		add_action( 'admin_init', array( $this, 'includes_admin_init' ) );
	}

	/**
	 * Include the necessary classes for the Admin Panel.
	 */
	public function includes_init()
	{
		require_once('class.wcsam-admin-menus.php');
		require_once('class.wcsam-admin-action-settings.php');
		require_once('class.wcsam-admin-menu-pages.php');
		require_once('pages/class.wcsam-pages.php');
	}

	/**
	 * Include the necessary classes for the Admin Panel.
	 */
	public function includes_admin_init()
	{
		//
	}
}

new Admin();
