<?php
/**
 * Functions
 *
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\product_filters
 * @version 1.0.0
 */

use \WCSAM\modules\product_filters\Product_filters;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if access d directly.

/**
 * Main PF function
 *
 * @return Product_filters
 */
function PF()
{
	return Product_filters::instance();
}
