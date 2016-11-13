/**
 * WCSAM Module script - Admin Product Filters Widgets
 * @author  DevDiamond <me@devdiamond.com>
 * @version 1.0.0
 */
jQuery( function( $ )
{
    // PFWA Object
    var pfwa = {};

    // PFWA Changes
    pfwa.changeShowType = function( $this, e )
    {
        /**
         * @var object pfwa_data
         */

        var obj_widget        = $($this).parents('.widget-content'),
            obj_widget_inside = $($this).parents('.widget-inside'),
            attr_container    = obj_widget.find('.pfwa_attributes_table_block'),
            hierarchical_cont = obj_widget.find('.pfwa_hierarchical_block');

        var data = {
            action       : 'wcsam_pfwa_select_type',
            widget_id    : $('input[name=widget_id]', obj_widget).val(),
            widget_name  : $('input[name=widget_name]', obj_widget).val(),
            attribute    : $('.pfwa_attribute', obj_widget).val(),
            value        : $('.pfwa_show_type', obj_widget).val(),
            child_term   : $('.pfwa_child_term', obj_widget).val(),
            hierarchical : $('.pfwa_hierarchical', obj_widget).val()
        };

        // Hide container blocks
        hierarchical_cont.stop().slideUp();
        attr_container.stop().slideUp();

        // Create loader BG
        obj_widget_inside.css({'position':'relative'}).append('<div class="pfwa-bg" style="display: none;"></div>');
        $('.pfwa-bg').css({
            'position': 'absolute',
            'top': 0,
            'left': 0,
            'bottom': 0,
            'right': 0,
            'z-index': 9999,
            'background': "rgba(0, 0, 0, 0.5) url('"+ pfwa_data.loader_img +"') center center no-repeat"
        }).show();

        // Get terms list (data)
        $.post(ajaxurl, data, function (response)
        {
            if (data.value == 'list' || data.value == 'select')
            {
                hierarchical_cont.stop().slideDown();
                attr_container.stop().slideUp();
            }
            else
            {
                hierarchical_cont.stop().slideUp();
                attr_container.stop().slideDown();
            }

            attr_container.html(response.content);
            $(document).trigger('pfwa_colorpicker');

            $('.pfwa-bg').remove();
        }, 'json');
    };

    // Init script
    pfwa.init = function()
    {
        // PFWA Changes
        $(document).on('change', '.pfwa_show_type, .pfwa_attribute, .pfwa_child_term', function(e)
        {
            e.preventDefault();
            pfwa.changeShowType( this, e );
            $(document).trigger('pfwa_colorpicker');
        });

        // PFWA color-picker
        $(document).on('pfwa_colorpicker', function()
        {
            $('.pfwa-colorpicker').each(function() {
                $(this).wpColorPicker();
            });
        }).trigger('yith_colorpicker');
        $(document).trigger('pfwa_colorpicker');
    };

    // Start
    pfwa.init();
});