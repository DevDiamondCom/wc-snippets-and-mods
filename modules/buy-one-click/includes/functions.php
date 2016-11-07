<?php
/**
 * Functions
 *
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\buy_one_click
 * @version 1.0.0
 */

use \WCSAM\modules\buy_one_click\Buy_One_Click;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if access d directly.

/**
 * Main Buy_One_Click function
 *
 * @return Buy_One_Click
 */
function BOC()
{
	return Buy_One_Click::instance();
}