/**
 * WCSAM Module script - Product Filters Widgets
 * @author  DevDiamond <me@devdiamond.com>
 * @version 1.0.0
 */
jQuery( function( $ )
{
    /**
     * pf_widgets_data is required to continue, ensure the object exists
     *
     *  @var object pf_widgets_data
     */
    if ( typeof pf_widgets_data === 'undefined' )
        return false;

    // PFW Object
    var pfw = {};

    // PFW Load BG Class name
    pfw.bgClassName = 'wcsam-pf-bg';
    pfw.bgClass     = '.' + pfw.bgClassName;

    // Create PFW BG
    pfw.createBG = function()
    {
        $('body').append('<div class="'+ pfw.bgClassName +'" style="display: none;"></div>');
        $( pfw.bgClass ).css({
            'position': 'fixed',
            'top': 0,
            'left': 0,
            'bottom': 0,
            'right': 0,
            'z-index': 9999,
            'background': "rgba(0, 0, 0, 0.5) url('"+ pf_widgets_data.loader_img +"') center center no-repeat"
        }).show();
    };

    // Ajax Load page
    pfw.loadPage = function( url )
    {
        $( pf_widgets_data.update_container ).load( url + ' ' + pf_widgets_data.update_container + ' > *', function( html )
        {
            $( pfw.bgClass ).remove();
        });
    };

    // get URL str parse
    pfw.strToObject = function( str, sep1, sep2 )
    {
        var vars = {}, hash;
        var hashes = str.split( sep1 );
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split( sep2 );
            vars[hash[0]] = hash[1];
        }
        return vars;
    };

    // Get URL vars (object)
    pfw.getUrlVars = function()
    {
        if ( window.location.href.indexOf('?') !== -1 )
            return pfw.strToObject( window.location.href.slice(window.location.href.indexOf('?') + 1), '&', '=' );
        else
            return {};
    };

    // Get URL
    pfw.getCleanUrl = function()
    {
        var $url = '';

        if ( window.location.href.indexOf('?') !== -1 )
            $url = window.location.href.slice(0, window.location.href.indexOf('?'));
        else
            $url = window.location.href;

        return $url;
    };

    // New Get URL
    pfw.newGetUrl = function( form_serialize_data )
    {
        var get_url_data = $.extend(
            pfw.getUrlVars(),
            pfw.strToObject( form_serialize_data, '&', '=' )
        );

        return pfw.getCleanUrl() + '?' + $.param( get_url_data );
    };

    // Price Slider
    pfw.price_slider = function()
    {
        /**
         * pf_price_filter is required to continue, ensure the object exists
         *
         *  @var object pf_price_filter
         */
        if ( typeof pf_price_filter === 'undefined' )
            return false;

        // Get markup ready for slider
        $( 'input#min_price, input#max_price' ).hide();
        $( '.price_slider, .price_label' ).show();

        // Price slider uses jquery ui
        var min_price         = $( '.price_slider_amount #min_price' ).data( 'min' ),
            max_price         = $( '.price_slider_amount #max_price' ).data( 'max' ),
            current_min_price = parseInt( min_price, 10 ),
            current_max_price = parseInt( max_price, 10 );

        if ( pf_price_filter.min_price )
            current_min_price = parseInt( pf_price_filter.min_price, 10 );
        if ( pf_price_filter.max_price )
            current_max_price = parseInt( pf_price_filter.max_price, 10 );

        // New prise from Slide
        $( document.body ).bind( 'price_slider_create price_slider_slide', function( event, min, max )
        {
            var max_p_str = '', min_p_str = '';

            switch ( pf_price_filter.currency_pos )
            {
                case 'left':
                    min_p_str = pf_price_filter.currency_symbol + min;
                    max_p_str = pf_price_filter.currency_symbol + max;
                    break;
                case 'left_space':
                    min_p_str = pf_price_filter.currency_symbol + ' ' + min;
                    max_p_str = pf_price_filter.currency_symbol + ' ' + max;
                    break;
                case 'right':
                    min_p_str = min + pf_price_filter.currency_symbol;
                    max_p_str = max + pf_price_filter.currency_symbol;
                    break;
                case 'right_space':
                    min_p_str = min + ' ' + pf_price_filter.currency_symbol;
                    max_p_str = max + ' ' + pf_price_filter.currency_symbol;
                    break;
            }

            if ( min_p_str !== '' && max_p_str !== '' )
            {
                $( '.price_slider_amount span.from' ).html( min_p_str );
                $( '.price_slider_amount span.to' ).html( max_p_str );
            }

            $( document.body ).trigger( 'price_slider_updated', [ min, max ] );
        });

        // Slider
        $('.price_slider').slider({
            range: true,
            animate: true,
            min: min_price,
            max: max_price,
            values: [ current_min_price, current_max_price ],
            create: function()
            {
                $('.price_slider_amount #min_price').val( current_min_price );
                $('.price_slider_amount #max_price').val( current_max_price );

                $(document.body).trigger('price_slider_create', [ current_min_price, current_max_price ] );
            },
            slide: function( event, ui )
            {
                $( 'input#min_price' ).val( ui.values[0] );
                $( 'input#max_price' ).val( ui.values[1] );

                $(document.body).trigger( 'price_slider_slide', [ ui.values[0], ui.values[1] ] );
            },
            change: function( event, ui )
            {
                $(document.body).trigger( 'price_slider_change', [ ui.values[0], ui.values[1] ] );
            }
        });

        // Slide Change
        $(document.body).on('price_slider_change', function (e, minP, maxP)
        {
            e.preventDefault();

            if ( minP === maxP || minP > maxP || (current_min_price === minP && current_max_price === maxP) )
                return;

            pfw.createBG();

            // Update Ajax or Submit
            if ( $.trim(pf_widgets_data.update_container) )
                History.pushState(null, document.title, pfw.newGetUrl( $('#price_slider_form').serialize() ));
            else
                $('#price_slider_form').submit();
        });
    };

    // Product Label and Color Type Filters
    pfw.product_filters = function()
    {
        // Product Filters Change
        $(document.body).on('change', '.wcsam-pfw-checkbox-row input[type="checkbox"]', function (e)
        {
            e.preventDefault();

            var filters_data = {};

            $('.wcsam-pfw-checkbox-row input[type="checkbox"]:checked').each(function()
            {
                var f_name = $(this).attr('name'),
                    f_val  = $(this).val();

                if ( typeof( filters_data[ f_name ] ) ==="undefined" )
                    filters_data[ f_name ] = f_val;
                else
                    filters_data[ f_name ] = filters_data[ f_name ] + ',' + f_val;
            });

            var url = pfw.getCleanUrl() + '?' + $.param( $.extend( pfw.getUrlVars(), filters_data ) );

            pfw.createBG();

            // Update Ajax or Submit
            if ( $.trim(pf_widgets_data.update_container) )
                History.pushState(null, document.title, url);
            else
                window.location.href = url;
        });
    };

    // Reset Filters
    pfw.reset_filters = function()
    {
        $('#reset_filters_form').submit(function()
        {
            pfw.createBG();

            window.location.href = $(this).attr('action');

            // Stop submit
            return false;
        });
    };

    // Init script
    pfw.init = function()
    {
        pfw.price_slider();
        pfw.product_filters();
        pfw.reset_filters();
    };

    // StateChange actions
    if ( $.trim(pf_widgets_data.update_container) )
    {
        History.Adapter.bind(window, 'statechange', function(e)
        {
            var State = History.getState();
            pfw.loadPage(State.url);
        });
    }

    // Start
    pfw.init();
});