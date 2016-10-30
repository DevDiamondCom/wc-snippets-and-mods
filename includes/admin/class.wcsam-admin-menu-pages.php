<?php

namespace WCSAM\admin;

use WCSAM\admin\Admin_Menus;
use WCSAM\admin\pages\Admin_Pages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Admin_Menu_Pages - Setup menu pages in WP admin
 *
 * @class   Admin_Menu_Pages
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\admin
 * @version 1.0.0
 */
class Admin_Menu_Pages
{
	/**
	 * Tabs lists for the pages of WCSAM menu (API)
	 *
	 * @var array
	 * @static
	 */
	private static $tabs = array();

	/**
	 * Page is template
	 *
	 * @static
	 * @var bool
	 */
	private static $page_is_template = false;

	/**
	 * Page data group
	 *
	 * @static
	 * @var array
	 */
	private static $page_data_group = array();

	/**
	 * Options page (settings)
	 *
	 * @static
	 * @var array
	 */
	private static $options = array();

	/**
	 * Active Tab
	 *
	 * @static
	 * @var string
	 */
	private static $active_tab;

	/**
	 * Current Page Slug
	 *
	 * @static
	 * @var string
	 */
	private static $page_slug;

	/**
	 * Is Form block
	 *
	 * @static
	 * @var bool
	 */
	private static $is_form = false;

	/**
	 * Admin Menu Pages
	 *
	 * @static
	 */
	public static function admin_menu_pages()
	{
		# Check page slugs
		if ( false === (self::$page_slug = Admin_Menus::check_current_page()) )
			return;

		# Get Tabs list (API)
		if ( Admin_Menus::MAIN_MENU_SLUG === self::$page_slug )
			self::$tabs = Admin_Pages::pages( self::$page_slug );
		else
			self::$tabs = (array) apply_filters('wcsam_tabs_'.self::$page_slug, array());

		if ( ! self::$tabs )
			return;

		if ( isset( $_GET['tab'], self::$tabs[ $_GET['tab'] ] ) )
			self::$active_tab = $_GET['tab'];
		else
			self::$active_tab = key(self::$tabs);

		self::menu_page();
	}

	/**
	 * Menu Page
	 *
	 * @static
	 */
	private static function menu_page()
	{
		echo '<div class="wrap">';

		self::echo_header();
		self::echo_top_menu();
		self::echo_body();

		echo '</div>';
	}

	/**
	 * Echo Header
	 *
	 * @static
	 */
	private static function echo_header()
	{
		?>
		<div class="dd-header">
			<h3><?= get_admin_page_title() ?></h3>
			<div class="dd-header-logo">
				<a href="?page=<?= esc_attr( $_GET['page'] ) ?>&tab=info">
					<img src="<?= WCSAM_PLUGIN_URL . Admin_Menus::ASSETS_ADMIN_IMG . 'logo-30x30.png' ?>" title="<?= esc_attr(__('Info', WCSAM_PLUGIN_SLUG)) ?>" />
				</a>
			</div>
		</div>
		<?
	}

	/**
	 * Echo top menu
	 *
	 * @static
	 */
	private static function echo_top_menu()
	{
		echo '<h2 class="nav-tab-wrapper dd-top-menu">';
		foreach ( self::$tabs as $tKey => $tVal )
		{
			if ( ! isset($tVal['title_args']) )
				continue;

			echo '<a href="?page='. esc_attr( $_GET['page'] ) .'&tab='. $tKey .'" id="'. (@$tVal['title_args']['id'] ?: '') .'" class="nav-tab ';
			if ( $tKey === self::$active_tab )
			{
				if ( isset( $tVal['template'] ) )
				{
					self::$page_is_template = true;
					self::$page_data_group  = (array) @$tVal['template']['default_fields'] ?: array();
					self::$is_form          = (bool) @$tVal['template']['is_form'] ?: false;
				}
				else
				{
					self::$page_data_group = (array) @$tVal['groups'] ?: array();
				}
				echo 'nav-tab-active ';
			}
			echo (@$tVal['title_args']['class'] ?: '') .'">';
			echo (isset($tVal['title_args']['fa-icon']) ? '<i class="fa '. $tVal['title_args']['fa-icon'] .'"></i>' : '');
			echo (@$tVal['title_args']['title'] ?: '') .'</a>';
		}
		echo '</h2>';
	}

	/**
	 * Echo body content
	 *
	 * @static
	 */
	private static function echo_body()
	{
		echo '<div class="dd-settings-block"><br>';

		// TEMPLATE (set)
		if ( self::$page_is_template )
		{
			if ( self::$is_form )
				self::begin_form_data();

			// Action Page Tab (API)
			do_action('wcsam_page_tab_' . self::$active_tab);

			if ( self::$is_form )
				self::end_form_data();
		}
		// GROUP (set) data
		elseif ( self::$page_data_group )
		{
			foreach ( self::$page_data_group as $gVal )
			{
				if ( ! isset( $gVal['group_args'], $gVal['group_args']['title'], $gVal['fields'] ) )
					continue;

				// BEGIN From block
				if ( ! self::$is_form )
					self::begin_form_data();

				?>
				<div id="wcsam_effects_box" class="dd_eb_block">
					<div class="dd_eb_title">
						<h3 id="<?= (@$gVal['group_args']['id'] ?: '') ?>" class="<?= (@$gVal['group_args']['class'] ?: '') ?>"><i class="fa fa-plus-square"></i><?= $gVal['group_args']['title'] ?></h3>
					</div>
					<div class="dd_eb_body" style="display: none;">
						<div class="dd_eb_desc"><?= (@$gVal['group_args']['desc'] ?: '') ?></div>
						<div class="dd_eb_table"><?php self::echo_sets( $gVal['fields'] ); ?></div>
					</div>
				</div>
				<?php
			}
			// END From block
			if ( self::$is_form )
				self::end_form_data();
		}

		echo '</div>';
	}

	/**
	 * Echo table sets
	 *
	 * @static
	 * @param  array $group_fields_data - group fields data
	 */
	private static function echo_sets( &$group_fields_data )
	{
		// BEGIN Table
		echo '<table class="form-table"><tbody>';

		foreach ( $group_fields_data as $fVal )
		{
			if ( ! isset( $fVal['field_args'], $fVal['field_args']['title'], $fVal['fields'] ) )
				continue;
			?>
			<tr valign="top">
				<th>
					<div id="<?= (@$fVal['field_args']['id'] ?: ''); ?>" class="dd_eb_set_header <?= (@$fVal['field_args']['class'] ?: ''); ?>">
						<div class="dd_eb_set_header_title"><?= $fVal['field_args']['title'] ?></div>
						<div class="dd_eb_set_header_desc"><?= (@$fVal['field_args']['desc'] ?: '') ?></div>
					</div>
				</th>
				<td class=""><?php self::echo_fields( $fVal['fields'] ); ?></td>
			</tr>
			<?
		}

		// END Table
		echo '</tbody></table>';
	}

	/**
	 * Echo fields param
	 *
	 * @static
	 * @param array $fields_data - fields data
	 */
	private static function echo_fields( &$fields_data )
	{
		foreach ( $fields_data as $fVal )
		{
			if ( empty($fVal['type']) || empty($fVal['name']) )
				continue;

			// Set Value
			if ( isset(self::$options[ $fVal['name'] ]) )
				$fVal['default'] = self::$options[ $fVal['name'] ];
			elseif ( ! isset($fVal['default']) )
				$fVal['default'] = ($fVal['type'] === 'checkbox' ? array() : '');
			elseif ( $fVal['type'] === 'checkbox' )
				$fVal['default'] = (array) $fVal['default'];
			elseif ( $fVal['type'] === 'switch' )
				$fVal['default'] = (bool) $fVal['default'];
			else
				$fVal['default'] = (string) $fVal['default'];

			// BEGIN set body
			?><div class="dd_eb_set_body"><div class="dd_eb_set_body_title"><?= (@$fVal['title'] ?: '') ?></div><?

			switch ( $fVal['type'] )
			{
				case 'switch':
				{
					?>
					<div class="toggle toggle-light" data-toggle-on="<?= ($fVal['default'] ? 'true' : 'false' ) ?>"
					     data-toggle-height="24" data-toggle-width="62"></div>
					<input class="<?= (@$fVal['class'] ?: '') ?>" id="<?= (@$fVal['id'] ?: '') ?>" type="hidden"
					       name="<?= $fVal['name']; ?>" value="<?= ($fVal['default'] ? 1 : 0 ) ?>">
					<?
					break;
				}
				case 'text':
				{
					?>
					<input class="regular-text <?= (@$fVal['class'] ?: '') ?>" id="<?= (@$fVal['id'] ?: '') ?>"
					       type="text" name="<?= $fVal['name']; ?>" value="<?= $fVal['default'] ?>"
					       placeholder="<?= (@$fVal['placeholder'] ?: '') ?>">
					<?
					break;
				}
				case 'textarea':
				{
					?>
					<textarea class="<?= (@$fVal['class'] ?: '') ?>" id="<?= (@$fVal['id'] ?: '') ?>" name="<?= $fVal['name']; ?>"
					          style="height: 101px; width: 350px;" placeholder="<?= (@$fVal['placeholder'] ?: '') ?>"><?= $fVal['default'] ?></textarea>
					<?
					break;
				}
				case 'select':
				{
					if ( ! isset($fVal['data']) )
						break;

					?>
					<select class="regular-text <?= (@$fVal['class'] ?: '') ?>" id="<?= (@$fVal['id'] ?: '') ?>" name="<?= $fVal['name']; ?>" title="<?= (@$fVal['title'] ?: '') ?>">
					<?php foreach ( $fVal['data'] as $dKey => $dVal ) : ?>
						<option value="<?= $dKey ?>" <?= ($fVal['default'] === $dKey ? 'SELECTED' : '' ) ?>><?= $dVal ?></option>
					<?php endforeach; ?>
					</select>
					<?
					break;
				}
				case 'checkbox':
				{
					if ( ! isset($fVal['data']) )
						break;

					foreach ( $fVal['data'] as $dKey => $dVal )
					{
						?>
						<label><input class="<?= (@$fVal['class'] ?: '') ?>" id="<?= (@$fVal['id'] ?: '') ?>" type="checkbox"
						name="<?= $fVal['name'] ?>[]" value="<?= $dKey ?>" <?= (array_search($dKey, $fVal['default']) !== false ? 'CHECKED' : '' ) ?>>
						<span class="dd_eb_set_body_val"><?= $dVal ?></span></label><br>
						<?
					}
					break;
				}
				case 'radio':
				{
					if ( ! isset($fVal['data']) )
						break;

					if ( $fVal['default'] === '' )
						$fVal['default'] = key($fVal['data']);

					foreach ( $fVal['data'] as $dKey => $dVal )
					{
						?>
						<label><input class="<?= (@$fVal['class'] ?: '') ?>" id="<?= (@$fVal['id'] ?: '') ?>" type="radio"
						name="<?= $fVal['name'] ?>" value="<?= $dKey ?>" <?= ($dKey === $fVal['default'] ? 'CHECKED' : '' ) ?>>
						<span class="dd_eb_set_body_val"><?= $dVal ?></span></label><br>
						<?php
					}
					break;
				}
				case 'number':
				{
					?>
					<input class="<?= (@$fVal['class'] ?: '') ?>" id="<?= (@$fVal['id'] ?: '') ?>" type="number"
					       name="<?= $fVal['name']; ?>" value="<?= $fVal['default'] ?>" min="<?= (@$fVal['min'] ?: '' ) ?>"
					       max="<?= (@$fVal['max'] ?: '' ) ?>" step="<?= (@$fVal['step'] ?: '' ) ?>"
					       placeholder="<?= (@$fVal['placeholder'] ?: '') ?>">
					<?php
					break;
				}
			}

			// END set body
			?><div class="dd_eb_set_body_desc"><?= (@$fVal['desc'] ?: '') ?></div></div><?php
		}
	}

	/**
	 * Begin form data
	 *
	 * @static
	 */
	private static function begin_form_data()
	{
		self::$is_form = true;
		self::$options = wcsam_get_option( self::$page_slug, self::$active_tab, self::$active_tab, array());

		echo '<form action="" method="POST">';
	}

	/**
	 * End form data
	 *
	 * @static
	 */
	private static function end_form_data()
	{
		?>
		<div class="dd-sb-btn"><p>
			<input type="hidden" name="wcsam_action" value="wcsam_update">
			<input type="hidden" name="page_slug" value="<?= self::$page_slug ?>">
			<input type="hidden" name="tab_slug" value="<?= self::$active_tab ?>">
			<?php

			wp_nonce_field('wcsam_action_update_'.self::$page_slug.'_'.self::$active_tab, 'wcsam_set_id');
			$notice_m = __('All settings will be reset to the defaults.\n\nAre you sure you want to reset the SETUP?', WCSAM_PLUGIN_SLUG);

			?><input type="submit" name="bt_save_settings" class="button button-primary" value="<?= __('Save changes', WCSAM_PLUGIN_SLUG) ?>">&nbsp;&nbsp;<?php

			if ( self::$page_data_group )
			{
				?><input type="submit" name="bt_reset_settings" class="button" onclick="return confirm('<?= $notice_m ?>')" value="<?= __('Reset Settings', WCSAM_PLUGIN_SLUG) ?>"><?php
			}
			?>
		</p></div>
		<?php
		echo '</form>';
	}
}
