<?php

global $wpdb;                                                           // used by config.php

require_once GFT_PLUGIN_PATH . 'includes/config.php';                   // constant definitions
require_once GFT_PLUGIN_PATH . 'includes/class-gfteamleaderaddon.php';  // addon class
require_once GFT_PLUGIN_PATH . 'includes/class-request.php';            // data request handling
 
$gft_request = new GFT_Request();                                       // create Mollie request object

add_action('gf_mollie_payment_completed', array($gft_request, 'send_mollie_data'), 10, 3);    
 