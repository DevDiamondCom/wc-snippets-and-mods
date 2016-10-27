var wcsam = wcsam || {};

if (typeof $ === 'undefined')
    var $ = jQuery;

// Global variables
wcsam.classEl = '.';

// Init
wcsam.init = function()
{
    // Toggle btn.
    $('.ext-toggle').toggles().on('toggle', function(e, active)
    {
        if (active)
            $(this).next().attr({'checked':true});
        else
            $(this).next().attr({'checked':false});
    });
};

// Start jQuery
jQuery(function() {
    wcsam.init();
});
