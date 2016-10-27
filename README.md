# WC Snippets And Mods
WC Snippets and Mods (WС_SAM) - is a WordPress plugin to extend the functionality of E-shop created a wonderful WooCommerce.

**Example of adding a submenu**
```php
/**
 * Add Sub Menu
 * ----------------------------------------------- */
add_filter('wcsam_submenu', function($submunu)
{
    return array_merge($submunu, array(
        'test' => array(
            'menu_title' => 'TEST',           // required
            'page_title' => 'TEST Title',     // optional (default: '')
            'capability' => 'manage_options', // optional (default: 'manage_options')
        ),
        'test2' => array(
            'menu_title' => 'TEST2',          // required
            'page_title' => 'TEST2 Title',    // optional (default: '')
            'capability' => 'manage_options', // optional (default: 'manage_options')
        ),
    ));
});
```

**Example of adding tabs for pages with parameters**
```php
/**
 * General Settings Menu Tabs
 * ----------------------------------------------- */
add_filter('wcsam_tabs_wcsam', function($tabs)
{
    return array_merge($tabs, array(
        //------------------------------------------------------------------
        //  General
        //------------------------------------------------------------------
        'general' => array(
            'title_args' => array(
                'title'   => __("General", WCSAM_PLUGIN_SLUG),  // required
                'fa-icon' => 'fa-gear',                         // optional (default: '')
                'id'      => 'general-settings-id',             // optional (default: '')
                'class'   => 'general-settings-class'           // optional (default: '')
            ),
            'groups' => array(
                'set_1' => array(
                    'group_args' => array(
                        'title' => __("Social Settings block", WCSAM_PLUGIN_SLUG),        // required
                        'desc'  => __("All Social icons param, etc", WCSAM_PLUGIN_SLUG),  // optional (default: '')
                        'id'    => 'social-set-block-id',                                 // optional (default: '')
                        'class' => 'social-set-block-class',                              // optional (default: '')
                    ),
                    'fields' => array(
                        'set_1' => array(
                            'field_args' => array(
                                'title' => __("Is Google Icon?", WCSAM_PLUGIN_SLUG),               // required
                                'desc'  => __("[ON = show, OFF = hide] icon", WCSAM_PLUGIN_SLUG),  // optional (default: '')
                                'id'    => 'social-set-block-id',                                 // optional (default: '')
                                'class' => 'social-set-block-class',                              // optional (default: '')
                            ),
                            'fields' => array(
                                array(
                                    'type'    => 'switch',               // required (switch, text, textarea, select, checkbox, radio, number)
                                    'name'    => 'set_1_set_1',          // required (field name)
                                    'default' => false,                  // optional (values for the {'switch' = true/false, 'checkbox' = array('value' => 'TEXT'), all other = string})
                                    'title'   => 'Title field',          // optional (default: '')
                                    'desc'    => 'OFF/ON button',        // optional (default: '')
                                    'id'      => 'social-switch-id',     // optional (default: '')
                                    'class'   => 'social-switch-class',  // optional (default: '')
                                    'placeholder' => 'placeholder TEXT', // optional (default: '')
                                    'min'     => 0,                      // optional (for number, etc field. default: '')
                                    'max'     => 150,                    // optional (for number, etc field. default: '')
                                    'step'    => 0.01,                   // optional (for number, etc field. default: '')
                                ),
                            ),
                        ), // END 'set_1'
                    ),
                ), // END 'set_1'
            ),
        ),
        //------------------------------------------------------------------
        //  TEST Tabs
        //------------------------------------------------------------------
        // Settings tab for the second, etc.
    ));
});
```

**Example of adding tabs to pages with their own template**
```php
/**
 * General Settings Menu Tabs
 * ----------------------------------------------- */
add_filter('wcsam_tabs_wcsam', function($tabs)
{
    return array_merge($tabs, array(
        //------------------------------------------------------------------
        //  My Settings
        //------------------------------------------------------------------
        'my_settings' => array(
            'title_args' => array(
                'title'   => __("My Settings", WCSAM_PLUGIN_SLUG),  // required
                'fa-icon' => 'fa-faname',                           // optional (default: '')
                'id'      => 'my-settings-id',                      // optional (default: '')
                'class'   => 'my-settings-class'                    // optional (default: '')
            ),
            'template' => array(
                'is_form'        => false,                      // optional (boolean) (default: false)
                'default_fields' => array(                      // optional (array)   (default: null)
                    'field-name-1' => 'Default Value field 1',
                    'field-2-name' => 'Default Value field 2',
                    // etc
                ),
            ),
        ),
        //------------------------------------------------------------------
        //  TEST Tabs
        //------------------------------------------------------------------
        // Settings tab for the second, etc.
    ));
});

/**
 * My Settings template Hook (wcsam_page_tab_{slug})
 * ----------------------------------------------------------------------- */
add_action('wcsam_page_tab_my_settings', function()
{
	// Your Template code
});
```

## License
Copyright (C) 2016 DevDiamond. (email : me@devdiamond.com)

GPLv3 or later - http://www.gnu.org/licenses/gpl-3.0.html