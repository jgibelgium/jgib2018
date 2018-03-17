<?php
/*
Plugin Name: Mollie Dashoard
Description: Show Mollie payment information
Version: 1.0
Author: Paul de Jong
*/

define('MDB_PLUGIN_PATH', plugin_dir_path(__FILE__));

global $wpdb;                                                     

require_once MDB_PLUGIN_PATH . 'includes/config.php';                   // constant definitions

if (is_admin()) 
{
    if(!class_exists('WP_List_Table'))
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
    /** ABSPATH is the absolute path to the WordPress directory of wp-admin, wp-content en wp-includes. */
    require_once MDB_PLUGIN_PATH . 'includes/class-dashboard.php';
    require_once MDB_PLUGIN_PATH . 'includes/class-items-table.php';
    require_once MDB_PLUGIN_PATH . 'includes/class-payments-table.php';
    require_once MDB_PLUGIN_PATH . 'includes/class-subscriptions-table.php';
    require_once MDB_PLUGIN_PATH . 'includes/class-mandates-table.php';
    require_once MDB_PLUGIN_PATH . 'includes/class-customers-table.php';

    $mdb_main = new MDB_Dashboard();

    if(!class_exists('Mollie_API_Client')) {
        require_once MDB_PLUGIN_PATH . 'libs/mollie-api-php/src/Mollie/API/Autoloader.php';
		/*De klasse Mollie_API_Client staat niet in autoloader.php, wel in client.php*/
    }
}