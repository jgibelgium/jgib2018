<?php
require_once GFM_PLUGIN_PATH . 'includes/class-gfmollieaddon.php';      // addon class
require_once GFM_PLUGIN_PATH . 'includes/class-request.php';            // payment request handling
require_once GFM_PLUGIN_PATH . 'includes/class-webhook.php';            // webhook handling
require_once GFM_PLUGIN_PATH . 'includes/class-gfhooks.php';            // GF Mollie hooks

$gfm_request = new GFM_Request();                                       // create Mollie request object
$gfm_webhook = new GFM_Webhook();                                       // create Mollie webhook object

if (is_admin()) 
{
    if(!class_exists('WP_List_Table'))
        require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

    require_once GFM_PLUGIN_PATH . 'includes/admin/class-donations-table.php';
    require_once GFM_PLUGIN_PATH . 'includes/admin/class-donors-table.php';
    require_once GFM_PLUGIN_PATH . 'includes/admin/class-subscriptions-table.php';
    require_once GFM_PLUGIN_PATH . 'includes/admin/class-admin.php';

    $gfm_admin = new GFM_Admin();
}

if(!class_exists('Mollie_API_Client')) {
    require_once GFM_PLUGIN_PATH . 'libs/mollie-api-php/src/Mollie/API/Autoloader.php';
}

