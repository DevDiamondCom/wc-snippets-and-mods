/**
 * WCSAM Module script - Buy One Click
 * @author  DevDiamond <me@devdiamond.com>
 * @version 1.0.0
 */
jQuery( function($)
{
    /**
     * @var object boc_obj - Global object
     */
    var boc = {

        // Scroll Top
        scrollTopSave: 0,

        // Ajax URL
        ajax: boc_obj.ajaxurl,

        // Ajax Actions list
        actions: boc_obj.actions,

        // Form type
        form_type: boc_obj.form_type,

        // Ajax Get Buy One Click form
        ajax_get_boc_form: function( send_data )
        {
            $.ajax({
                type: "POST",
                url: boc.ajax,
                async: false,
                data: send_data,
                success: function (response)
                {
                    // Error data
                    if ( response.status === 'error' )
                    {
                        if ( response.error_type === 'none' )
                            alert( response.data );
                        return;
                    }

                    // console.log(response);

                    $('#boc_order_block').remove();
                    $('.boc-bg').remove();

                    $('body').append(response);

                    $('.boc-bg').fadeIn(200);
                    boc.scrollTopSave = $(document).scrollTop() + 50;
                    $('.boc_order_block_modal').css({'top': boc.scrollTopSave}).fadeIn(200);
                }
            });
        },

        // Ajax Send order data
        ajax_send_order_dara: function( send_data )
        {
            $.ajax({
                type: "POST",
                url: boc.ajax,
                async: false,
                data: send_data,
                success: function (response)
                {
                    $('.boc_form_ajax_loader').slideUp();

                    // Error data
                    if ( response.status === 'error' )
                    {
                       if ( response.error_type === 'field' )
                       {
                           $.each(response.data, function(k,v)
                           {
                               $('[name="'+ v +'"]').next().slideDown();
                           });

                           // Scroll Top
                           if ( ($(document).scrollTop() + 50) > boc.scrollTopSave )
                               $(document.body).animate({ scrollTop: (boc.scrollTopSave - 50) }, 500);
                       }
                       else if ( response.error_type === 'none' )
                           alert( response.data );
                       return;
                    }

                    // Success
                    if ( response.status === 'success' )
                    {
                        $('#boc_order_form').slideUp(300, function()
                        {
                            // Show data
                            $('.boc_result_notice_title').slideDown();

                            switch ( response.action_type )
                            {
                                case 'close_n_sec':
                                    setTimeout(function()
                                    {
                                        $('#boc_order_block').slideUp();
                                    }, response.notice_action );
                                    break;
                                case 'show_message':
                                    $('.boc_result_notice_message').html( response.notice_action ).slideDown();
                                    break;
                                case 'redirect_to':
                                    window.location.href = response.notice_action;
                                    break;
                                case 'none':
                                default:
                                    break;
                            }

                            // Scroll Top
                            if ( ($(document).scrollTop() + 50) > boc.scrollTopSave )
                                $(document.body).animate({ scrollTop: (boc.scrollTopSave - 50) }, 500);

                            // Reloaded
                            if ( boc.form_type === 'cart' )
                            {
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            }
                        });
                        $('.boc-product-this').slideUp(300);
                        $('.finish-price').slideUp(300);
                    }
                }
            });
        }

    };

    $(document).ready(function ()
    {
        // Close Buy one click Modal block
        $(document).on('click', '.boc-bg, .close-boc', function() {
            $('.boc-bg').fadeOut(200);
            $('#boc_order_block').fadeOut(200);
        });

        // Check and Send BOC form data
        $(document).on('click', '#boc_order_form .boc_form_submit', function (e)
        {
            e.preventDefault();

            // Check valid fields
            var novalid_field = 0;

            if ( $('.boc_not_valid_tip').length )
            {
                $('.boc_not_valid_tip').each(function()
                {
                    if ( !$.trim($(this).prev().val()) )
                    {
                        novalid_field++;
                        $(this).slideDown();
                        return;
                    }
                    $(this).slideUp();
                });
            }

            if ( novalid_field )
                return false;

            // Send order data
            var send_data = $(this).closest('#boc_order_form').serializeArray();
            send_data[ send_data.length ] = {
                name:  'action',
                value: boc.actions.add_new_order
            };
            send_data[ send_data.length ] = {
                name:  'form_type',
                value: boc.form_type
            };

            $('.boc_form_ajax_loader').slideDown(function(){ boc.ajax_send_order_dara( send_data ) });
        });

        // Get Buy One Click Form
        $(document).on('click', 'a.boc_button', function (e)
        {
            e.preventDefault();
            var butObj = this;

            var send_data = {
                action:     boc.actions.get_form_block,
                product_id: $(butObj).attr('data-product_id'),
                quantity:   parseInt( $('.boc_button').closest('.product').find('[name="quantity"]').val() ),
                form_type:  boc.form_type,
                attributes: {}
            };

            // Variation data
            if ( boc.form_type !== 'cart' )
            {
                var obj_v_form = $(this).closest('.variations_form');
                var obj_variations = obj_v_form.find('select[name^=attribute]');

                /* Updated code to work with radio button - mantish - WC Variations Radio Buttons - 8 manos */
                if ( ! obj_variations.length )
                    obj_variations = obj_v_form.find('[name^=attribute]:checked');

                /* Backup Code for getting input variable */
                if ( ! obj_variations.length )
                    obj_variations = obj_v_form.find('input[name^=attribute]');

                if ( obj_variations.length )
                {
                    obj_variations.each( function()
                    {
                        var at_name = $(this).attr('name'),
                            at_val  = $(this).val();
                        if ( at_name != '' && at_val != '' )
                            send_data.attributes[ at_name ] = at_val;
                    });
                }
            }

            boc.ajax_get_boc_form( send_data );
        });
    });
});