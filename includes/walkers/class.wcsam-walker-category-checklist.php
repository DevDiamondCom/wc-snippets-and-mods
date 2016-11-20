<?php

namespace WCSAM\walkers;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Walker_Category_Checklist - Display category checklist
 *
 * @see \Walker
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 *
 * @class   Walker_Category_Checklist
 * @author  DevDiamond <me@devdiamond.com>
 * @package WCSAM\walkers
 * @version 1.0.0
 */
class Walker_Category_Checklist extends \Walker
{
	public $tree_type = 'category';
	public $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	public $save_data = array();

	public $input_name    = 'category_checklist';
	public $main_ul_class = 'wcsam-categorychecklist';

	/**
	 * Walker_Category_Checklist constructor.
	 *
	 * @param array $args = {
	 *      // Params
	 *      @type string $input_name    - (optional. default: 'category_checklist') Input field name slug
	 *      @type string $main_ul_class - (optional. default: 'wcsam-categorychecklist') Main UL block class name
	 * }
	 */
	public function __construct( $args = array() )
	{
		// Main UL container
		if ( ! empty($args['main_ul_class']) )
			$this->main_ul_class = (string)$args['main_ul_class'];
		add_filter('wp_list_categories', function( $output, $args )
		{
			return '<ul class="'. $this->main_ul_class .'">' . $output . '</ul>';
		}, 11, 2);

		// Input name param
		if ( ! empty($args['input_name']) )
			$this->input_name = (string)$args['input_name'];
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker:start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 )
	{
		if ( empty( $args['taxonomy'] ) )
			$taxonomy = 'category';
		else
			$taxonomy = $args['taxonomy'];

		$args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'>" .
			'<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'. $this->input_name .'[]" ' .
			(in_array( $category->term_id, $args['selected_cats'] ) ? 'class="wcsam_show_block"' : '') . ' id="in-'.$taxonomy.'-' . $category->term_id . '"' .
			checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
			disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
			esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>' .
			( ! $depth ? '<div class="wcsam-cl-show-hide-btn-close" title="'. __('Show under categories', WCSAM_PLUGIN_SLUG) .'"></div>' : '');
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	public function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}
