/**
 * WCSAM Module script - Ajax update of quantity in the cart
 * @author  DevDiamond <me@devdiamond.com>
 * @version 1.0.0
 */
jQuery( function($)
{
    $(document).ready(function()
    {
        /**
         * @var object auiqc
         */
        $(document.body).on('blur', 'form .quantity input', function(e)
        {
            e.preventDefault();

            var obj_form_cart = $(e.target).closest('form');

            if ( typeof( obj_form_cart ) === "undefined" )
                return;

            var cache_form = obj_form_cart.html();

            var send_data = {
                auiqc_action : 'quantity',
                auiqc_id     : auiqc.auiqc_id,
                qty_name     : $(this).attr('name'),
                qty_val      : $(this).val()
            };

            $('body').append('<div class="auiqc-bg" style="display: none;"></div>');
            $('.auiqc-bg').css({
                'position': 'fixed',
                'top': 0,
                'left': 0,
                'bottom': 0,
                'right': 0,
                'z-index': 9999,
                'background': "rgba(0, 0, 0, 0.5) url('"+ auiqc.loader_img +"') center center no-repeat"
            }).show();

            obj_form_cart.load( window.location.href + ' form:eq('+ (obj_form_cart.index() - 1) +') > *', send_data, function( html )
            {
                if ( html === 'ERROR' )
                    obj_form_cart.html( cache_form );

                $('.auiqc-bg').remove();
            });
        });
    });
});