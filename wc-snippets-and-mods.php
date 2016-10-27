<?php
/**
 * Plugin Name: WC Snippets and Mods
 * Plugin URI: http://devdiamond.com/
 * Description: WC Snippets and Mods (WС_SAM) - is a WordPress plugin to extend the functionality of E-shop created a wonderful WooCommerce.
 * Version: 1.0.0
 * Author: DevDiamond <me@devdiamond.com>
 * Author URI: http://devdiamond.com/
 * License: GPLv3 or later - http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wc_snippets_and_mods
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
if ( ! defined('WCSAM_VERSION') )         define('WCSAM_VERSION',         '1.0.0');
if ( ! defined('WCSAM_PLUGIN_SLUG') )     define('WCSAM_PLUGIN_SLUG',     'wc_snippets_and_mods');
if ( ! defined('WCSAM_PLUGIN_FILE') )     define('WCSAM_PLUGIN_FILE',     __FILE__);
if ( ! defined('WCSAM_PLUGIN_DIR') )      define('WCSAM_PLUGIN_DIR',      plugin_dir_path( WCSAM_PLUGIN_FILE ));
if ( ! defined('WCSAM_PLUGIN_URL') )      define('WCSAM_PLUGIN_URL',      plugin_dir_url( WCSAM_PLUGIN_FILE ));
if ( ! defined('WCSAM_PLUGIN_BASENAME') ) define('WCSAM_PLUGIN_BASENAME', plugin_basename( WCSAM_PLUGIN_FILE ));
if ( ! defined('WCSAM_AJAX_URL') )        define('WCSAM_AJAX_URL',        admin_url( 'admin-ajax.php', 'relative' ));

if ( ! defined('WCSAM_ASSETS_URL') )    define('WCSAM_ASSETS_URL',    WCSAM_PLUGIN_URL . 'assets/');
if ( ! defined('WCSAM_MODULES_DIR') )   define('WCSAM_MODULES_DIR',   WCSAM_PLUGIN_DIR . 'modules/');
if ( ! defined('WCSAM_TEMPLATES_DIR') ) define('WCSAM_TEMPLATES_DIR', WCSAM_PLUGIN_DIR . 'templates/');

# Require Core files
require_once('includes/class.wcsam.php');
require_once('includes/functions.php');

# Global for backwards compatibility.
$GLOBALS['wcsam'] = WCSAM();