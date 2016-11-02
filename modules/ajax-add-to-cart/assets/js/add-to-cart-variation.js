/**
 * WCSAM Module script - Ajax Add to cart
 * @author  DevDiamond <me@devdiamond.com>
 * @version 1.0.0
 */
jQuery( function( $ )
{
    /**
     * Check mandatory object wc_add_to_cart_params which contains basic information about the product
     * @var object wc_add_to_cart_params
     */
	if ( typeof wc_add_to_cart_params === 'undefined' )
		return false;

    // action:wcsam_module_add_to_cart_variable
    // product_id:77
    // quantity:1
    // variation_id:0
    // variation[attribute_pa_size]:19060
    // variation[attribute_pa_type_color]:pcolor-886009

    // quantity:1
    // add-to-cart:77
    // product_id:77
    // variation_id:0
    // attribute_pa_size:20070
    // attribute_pa_type_color:pcolor-f9f7e1
	
	// Ajax add to cart
	$(document).on('click', '.variations_form .single_add_to_cart_button', function(e)
    {
		e.preventDefault();

		var obj_v_form     = $(this).closest('.variations_form'),
            obj_variations = obj_v_form.find('select[name^=attribute]'),
            var_id         = obj_v_form.find('input[name=variation_id]').val(),
            product_id     = obj_v_form.find('input[name=product_id]').val(),
            quantity       = obj_v_form.find('input[name=quantity]').val();

        var item  = {},
            check = true;

		$('.ajaxerrors').remove();

        /* Updated code to work with radio button - mantish - WC Variations Radio Buttons - 8 manos */
        if ( ! obj_variations.length )
            obj_variations = obj_v_form.find('[name^=attribute]:checked');

        /* Backup Code for getting input variable */
        if ( ! obj_variations.length )
            obj_variations = obj_v_form.find('input[name^=attribute]');

		obj_variations.each( function()
        {
			var $this    = $(this),
				attrName = $this.attr('name'),
				attrVal  = $this.val(),
				index,
				attributeTaxName;

			$this.removeClass('error');
		
			if ( attrVal.length === 0 )
			{
				index = attrName.lastIndexOf( '_' );
				attributeTaxName = attrName.substring( index + 1 );
		
				$this.addClass('required error').before('<div class="ajaxerrors"><p>Please select ' + attributeTaxName + '</p></div>');
		
				check = false;
			}
			else
            {
				item[ attrName ] = attrVal;
			}
		});

		if ( ! check )
			return false;

		// AJAX add to cart request
		var $this_btn = $(this);

		if ( $this_btn.is('.variations_form .single_add_to_cart_button') )
		{
			$this_btn.removeClass('added');
			$this_btn.addClass('loading');

            var data = obj_v_form.serializeArray(),
                dataSend = [];
            data[ data.length ] = {
                'name'  : 'action',
                'value' : 'wcsam_module_add_to_cart_variable'
            };
            $.each(data, function(i, field)
            {
                if ( field.name === 'add-to-cart' )
                    return;
                    // data.splice(i,1);
                dataSend[ dataSend.length ] = {
                    'name'  : field.name,
                    'value' : field.value
                };
            });

            // Trigger event
			$('body').trigger('adding_to_cart', [ $this_btn, dataSend ] );

			// Ajax action
			$.post( wc_add_to_cart_params.ajax_url, dataSend, function( response )
            {
				if ( ! response )
					return;

				var this_page = window.location.toString();

				this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );
				
				if ( response.error && response.product_url )
				{
					window.location = response.product_url;
					return;
				}
				
				if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' )
				{
					window.location = wc_add_to_cart_params.cart_url;
					return;
				}

                $this_btn.removeClass('loading');

                var fragments = response.fragments;
                var cart_hash = response.cart_hash;

                // Block fragments class
                if ( fragments )
                {
                    $.each( fragments, function( key ){
                        $( key ).addClass('updating');
                    });
                }

                // Block widgets and fragments
                $( '.shop_table.cart, .updating, .cart_totals' ).fadeTo( '400', '0.6' ).block({
                    message     : null,
                    overlayCSS  : {
                        opacity : 0.6
                    }
                });

                // Changes button classes
                $this_btn.addClass( 'added' );

                // View cart text
                if ( ! wc_add_to_cart_params.is_cart && $this_btn.parent().find( '.added_to_cart' ).size() === 0 )
                {
                    $this_btn.after( ' <a href="' + wc_add_to_cart_params.cart_url +
                        '" class="added_to_cart wc-forward" title="' +
                        wc_add_to_cart_params.i18n_view_cart + '">' + wc_add_to_cart_params.i18n_view_cart + '</a>' );
                }

                // Replace fragments
                if ( fragments )
                {
                    $.each( fragments, function( key, value ){
                        $( key ).replaceWith( value );
                    });
                }

                // Unblock
                $('.widget_shopping_cart, .updating').stop( true ).css('opacity', '1').unblock();

                // Cart page elements
                $('.shop_table.cart').load( this_page + ' .shop_table.cart:eq(0) > *', function()
                {
                    $( '.shop_table.cart').stop( true ).css( 'opacity', '1' ).unblock();
                    $( document.body ).trigger( 'cart_page_refreshed' );
                });

                $('.cart_totals').load( this_page + ' .cart_totals:eq(0) > *', function()
                {
                    $('.cart_totals').stop( true ).css('opacity', '1').unblock();
                });

                // Trigger event so themes can refresh other areas
                $( document.body ).trigger( 'added_to_cart', [ fragments, cart_hash, $this_btn ] );
			});

			return false;
		}
		else
        {
			return true;
		}
	});
});
