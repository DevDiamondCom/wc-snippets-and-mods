var dds = dds || {};

if (typeof $ === 'undefined')
    var $ = jQuery;

// Global variables
dds.classEl = '.';

dds.SettingsOpenCloseEffect = function( obj_this )
{
    var obj_fa = obj_this.find('h3 > i');
    if ( obj_fa.hasClass('fa-plus-square') )
    {
        obj_fa.removeClass('fa-plus-square').addClass('fa-minus-square');
        obj_this.next().stop().slideDown();
    }
    else
    {
        obj_fa.removeClass('fa-minus-square').addClass('fa-plus-square');
        obj_this.next().stop().slideUp();
    }
};

// Init
dds.init = function()
{
    // Open / Close Effect
    $('.dd_eb_title').click(function(){ dds.SettingsOpenCloseEffect( $(this) ); }).hover(
        function(){ $(this).find('h3 > i.fa-plus-square').css({'color':'#000'});},
        function(){ $(this).find('h3 > i.fa-plus-square').css({'color':'#aaa'});}
    );

    // Toggle btn.
    $('.toggle').toggles().on('toggle', function(e, active)
    {
        if (active)
            $(this).next().val('1');
        else
            $(this).next().val('0');
    });
};

// Start jQuery
jQuery(function() {
    dds.init();
});
