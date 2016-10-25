<?php
/**
 * Plugin Name: Woo Snippets and Mods
 * Plugin URI: http://devdiamond.com/
 * Description: Plugin for expansion wonderful tool (plugin) WooCommerce.
 * Version: 1.0.0
 * Author: DevDiamond <me@devdiamond.com>
 * Author URI: http://devdiamond.com/
 * License: GPLv3 or later - http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: woosm
 * Domain Path: /languages/
 *
 * Copyright (C) 2016 DevDiamond. (email : me@devdiamond.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if access d directly.

# Defines
if ( ! defined('WOOSM_VERSION') )         define('WOOSM_VERSION',         '1.0.0');
if ( ! defined('WOOSM_AUTHOR_SLUG') )     define('WOOSM_AUTHOR_SLUG',     'DD');
if ( ! defined('WOOSM_PLUGIN_SLUG') )     define('WOOSM_PLUGIN_SLUG',     'woosm');
if ( ! defined('WOOSM_PLUGIN_FILE') )     define('WOOSM_PLUGIN_FILE',     __FILE__);
if ( ! defined('WOOSM_PLUGIN_DIR') )      define('WOOSM_PLUGIN_DIR',      plugin_dir_path( WOOSM_PLUGIN_FILE ));
if ( ! defined('WOOSM_PLUGIN_URL') )      define('WOOSM_PLUGIN_URL',      plugin_dir_url( WOOSM_PLUGIN_FILE ));
if ( ! defined('WOOSM_PLUGIN_BASENAME') ) define('WOOSM_PLUGIN_BASENAME', plugin_basename( WOOSM_PLUGIN_FILE ));
if ( ! defined('WOOSM_AJAX_URL') )        define('WOOSM_AJAX_URL',        admin_url( 'admin-ajax.php', 'relative' ));

# Require Core files
require_once('includes/class.woosm.php');
require_once('includes/functions.php');

# Global for backwards compatibility.
$GLOBALS['woosm'] = WOOSM();