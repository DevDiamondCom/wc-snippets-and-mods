<?php

namespace WCSAM\admin\pages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Pages - Default pages data
 *
 * @class   Admin_Pages
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\admin\pages
 * @version 1.0.0
 */
class Admin_Pages
{
	/**
	 * Tab pages param
	 *
	 * @static
	 * @var array
	 */
	protected static $tabs = array();

	/**
	 * Admin_Default_Pages constructor.
	 */
	public function __construct()
	{
		require_once 'class.wcsam-pages-wcsam.php';
	}

	/**
	 * Default page tabs
	 *
	 * @static
	 * @return array|false
	 */
	public static function pages( $page = '', $tab = '' )
	{
		$page = (string)$page;
		$tab  = (string)$tab;

		if ( ! empty($page) && ! empty($tab) )
		{
			$method = $page.'_'.$tab;
			if ( method_exists( __CLASS__, $method ) )
			{
				self::$method();
				return self::$tabs[ $tab ];
			}
			else
				return false;
		}

		if ( $page === 'wcsam' || $page === '' )
			Admin_Pages_Wcsam::pages_wcsam();

		return self::$tabs;
	}
}

new Admin_Pages();