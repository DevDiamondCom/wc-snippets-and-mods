/**
 * WCSAM Module script - Buy One Click Admin Panel
 * @author  DevDiamond <me@devdiamond.com>
 * @version 1.0.0
 */
jQuery( function( $ )
{
    var bocap = {
        'select_click_order_btn' : function( eVal )
        {
            alert( eVal );
            $('#click_order_btn_close_msec, #click_order_btn_message, #click_order_btn_redirect_url').parent().slideUp();
            switch ( eVal )
            {
                case 'none':
                    break;
                case 'close_n_sec':
                    $('#click_order_btn_close_msec').parent().slideDown();
                    break;
                case 'show_message':
                    $('#click_order_btn_message').parent().slideDown();
                    break;
                case 'redirect_to':
                    $('#click_order_btn_redirect_url').parent().slideDown();
                    break;
            }
        }
    };

    $(document).ready(function()
    {
        bocap.select_click_order_btn( $('#click_order_btn').val() );
        $('#click_order_btn').change(function(){ bocap.select_click_order_btn($(this).val()); });
    });
});
