<?php
/**
 * Functions
 *
 * @author   DevDiamond <me@devdiamond.com>
 * @package  WC_Snippets_And_Mods
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if access d directly.

/**
 * Get option from WCSAM Settings
 *
 * @param  string $page_slug    - Page slug
 * @param  string $tab_slug     - Tab slug
 * @param  string $option_name  - Option name
 * @param  bool   $default      - Default option
 *
 * @return mixed
 */
function wcsam_get_option( $page_slug, $tab_slug, $option_name = null, $default = false )
{
	return WCSAM()->get_option( $page_slug, $tab_slug, $option_name, $default );
}

/**
 * TEST function - var_dump()
 *
 * @param mixed $data    - TEST data
 * @param bool  $is_exit - Is exit?
 */
function dd_var_dump( $data, $is_exit = true )
{
	var_dump( $data );
	if ( $is_exit )
		exit();
}

/**
 * TEST function - print_r()
 *
 * @param mixed $data    - TEST data
 * @param bool  $is_exit - Is exit?
 */
function dd_print_r( $data, $is_exit = true )
{
	echo '<pre>';
	print_r( $data );
	echo '</pre>';
	if ( $is_exit )
		exit();
}