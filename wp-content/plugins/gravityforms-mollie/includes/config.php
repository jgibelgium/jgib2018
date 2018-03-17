<?php

global $wpdb;                                                          

// General
define('GF_MOLLIE_ADDON_VERSION',       '1.0');
define('GFM_WEBHOOK',                   'gfm-webhook');
define('GFM_DONATION_ID',               'donation_id');
define('GFM_FORM_ID',                   'form_id');
define('GFM_ENTRY_ID',                  'entry_id');
define('GFM_TXT_DOMAIN',                'default');
define('GFM_FORM',                      'gfm-form');

// Database
define('GFM_TABLE_DONATIONS',           $wpdb->prefix . 'gfm_donations');
define('GFM_TABLE_DONORS',              $wpdb->prefix . 'gfm_donors');
define('GFM_TABLE_SUBSCRIPTIONS',       $wpdb->prefix . 'gfm_subscriptions');

// Admin pages
define('GFM_PAGE_DONATIONS',            'gfm-page-donations');
define('GFM_PAGE_DONORS',               'gfm-page-donors');
define('GFM_PAGE_SUBSCRIPTIONS',        'gfm-page-subscriptions');
define('GFM_PAGE_DONATION',             'gfm-page-donation');
define('GFM_PAGE_EXPORT',               'gfm-page-export');

// Field names ($field->adminLabel)
define('GFM_FIELD_FIRSTNAME',           'gfm-field-firstname');
define('GFM_FIELD_LASTNAME',            'gfm-field-lastname');
define('GFM_FIELD_EMAIL',               'gfm-field-email');
define('GFM_FIELD_PHONE',               'gfm-field-phone');
define('GFM_FIELD_ADDRESS',             'gfm-field-address');
define('GFM_FIELD_ZIPCODE',             'gfm-field-zipcode');
define('GFM_FIELD_CITY',                'gfm-field-city');
define('GFM_FIELD_COUNTRY',             'gfm-field-country');
define('GFM_FIELD_PROJECT',             'gfm-field-project');
define('GFM_FIELD_COMPANY',             'gfm-field-company');
define('GFM_FIELD_MESSAGE',             'gfm-field-message');
define('GFM_FIELD_AMOUNT',              'gfm-field-amount');
define('GFM_FIELD_AMOUNT_SELECTED',     'gfm-field-amount-selected');
define('GFM_FIELD_AMOUNT_OTHER',        'gfm-field-amount-other');
define('GFM_FIELD_PAYMENT_INTERVAL',    'gfm-field-payment-interval');
define('GFM_FIELD_PAYMENT_METHOD',      'gfm-field-payment-method');
define('GFM_FIELD_AUTHORIZATION',       'gfm-field-authorization');
define('GFM_FIELD_DONATION_ID',         'gfm-field-donation-id');
define('GFM_FIELD_OPTIONS',             'gfm-field-options');

// Field types ($field->cssclass)
define('GFM_TYPE_CUSTOM',               'gfm-type-custom');

// Test URLs
define('GFM_TEST_URL', '');                                   // uncomment for production session
//define('GFM_TEST_URL', 'http://d968d58f.ngrok.io');           // uncomment plus adapt for ngrok test session
//define('GFM_TEST_URL', 'http://google.com');                  // uncomment for non-ngrok test session    
