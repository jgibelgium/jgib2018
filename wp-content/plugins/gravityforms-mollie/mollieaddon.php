<?php
/*
Plugin Name: Gravity Forms Mollie Add-On
Description: Handle Mollie payments using Gravity Forms. This an adapted version of plugin 'Doneren met Mollie' by Nick Dijkstra
Version: 1.0
Author: Paul de Jong
Text Domain: gravityforms-mollie
Domain Path: /languages/
*/



define('GFM_PLUGIN_PATH', plugin_dir_path(__FILE__));

add_action('gform_loaded', array('GF_Mollie_Bootstrap', 'load' ), 5 );  // load addon only if GF loaded

class GF_Mollie_Bootstrap 
{
    public static function load() 
    {
        // load addon only if GF is activated
        if (! method_exists( 'GFForms', 'include_addon_framework')) {
            return;
        }

        require_once('includes/run.php');         // run add-on
        
        GFAddOn::register('GFMollieAddOn');
    }
}

require_once GFM_PLUGIN_PATH . 'includes/config.php';                   // constant definitions
require_once GFM_PLUGIN_PATH . 'includes/class-install.php';            // database (un)installation

register_activation_hook(__FILE__, 'on_activation');
register_uninstall_hook (__FILE__, 'on_uninstall');

function on_activation() {
    
    $gfm_install = new GFM_Install();
    $gfm_install->upgrade_database();
    $gfm_install->import_default_forms();
}

function on_uninstall() {
    $gfm_install = new GFM_Install();
    $gfm_install->uninstall_database();   
}

function gf_mollie_addon() 
{
    return GFMollieAddOn::get_instance();
}
