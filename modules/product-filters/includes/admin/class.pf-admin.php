<?php

namespace WCSAM\modules\product_filters\admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin - Admin Panel Settings
 *
 * @class   Admin
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters\admin
 * @version 1.0.0
 */
class Admin
{
	/**
	 * Admin constructor.
	 */
	public function __construct()
	{
		$this->init_includes();
	}

	/**
	 * Load Admin Page includes
	 */
	private function init_includes()
	{
		require_once 'class.pf-admin-pages.php';
	}
}

new Admin;