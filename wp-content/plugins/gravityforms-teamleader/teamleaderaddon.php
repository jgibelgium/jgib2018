<?php
/*
Plugin Name: Gravity Forms Teamleader Add-On
Description: Send data to Teamleader from Gravity Forms. 
Version: 1.0
Author: Paul de Jong
Text Domain: gravityforms-teamleader
Domain Path: /languages/
*/

define('GFT_PLUGIN_PATH', plugin_dir_path(__FILE__));

add_action('gform_loaded', array('GF_Teamleader_Bootstrap', 'load' ), 5 );  // load addon only after GF loaded

class GF_Teamleader_Bootstrap 
{
    public static function load() 
    {
    	// load addon only if GF is activated
        if (! method_exists( 'GFForms', 'include_addon_framework')) {
            return;
        }
        
        require_once('includes/run.php');         // run add-on
        
        GFAddOn::register('GFTeamleaderAddOn');
    }
}

function gf_teamleader_addon() 
{
    return GFTeamleaderAddOn::get_instance();
}
