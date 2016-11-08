<?php
/**
 * Functions
 *
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\modules\ajax_update_qty_in_the_cart
 * @version 1.0.0
 */

use \WCSAM\modules\ajax_update_qty_in_the_cart\AUIQC;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if access d directly.

/**
 * Main AUIQC function
 *
 * @return AUIQC Class
 */
function AUIQC()
{
	return AUIQC::instance();
}