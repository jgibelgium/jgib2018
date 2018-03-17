<?php
class GFM_Install
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function upgrade_database () 
    {
        $charset_collate = $this->wpdb->get_charset_collate();
        
        $table_donations     = GFM_TABLE_DONATIONS;
        $table_donors        = GFM_TABLE_DONORS;
        $table_subscriptions = GFM_TABLE_SUBSCRIPTIONS;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sqlDonations = "CREATE TABLE $table_donations (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            amount float(15) NOT NULL,
            payment_id varchar(45) NOT NULL,
            customer_id varchar(45),
            subscription_id varchar(45),
            payment_method varchar(45) NOT NULL,
            payment_mode varchar(45) NOT NULL,
            donation_id varchar(45) NOT NULL,
            status varchar(25) NOT NULL,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(255) NOT NULL,
            company varchar(255) NOT NULL,
            project varchar(255) NOT NULL,
            address varchar(255) NOT NULL,
            zipcode varchar(255) NOT NULL,
            city varchar(255) NOT NULL,
            country varchar(255) NOT NULL,
            tag varchar(45) NOT NULL,
            custom varchar(45) NOT NULL,
            message text NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";
        
        dbDelta($sqlDonations);         // update table

        $sqlDonors = "CREATE TABLE $table_donors (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            customer_id varchar(45) NOT NULL,
            customer_mode varchar(45) NOT NULL,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255) NOT NULL,
            sub_interval varchar(255) NOT NULL,
            sub_amount float(15) NOT NULL,
            sub_description varchar(255) NOT NULL,
            customer_locale varchar(15) NOT NULL,
            secret varchar(45) NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";
        
        dbDelta($sqlDonors);         // update table

        $sqlSubscriptions = "CREATE TABLE $table_subscriptions (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            subscription_id varchar(45) NOT NULL,
            customer_id varchar(45) NOT NULL,
            sub_mode varchar(45) NOT NULL,
            sub_amount float(15) NOT NULL,
            sub_times int(9) NOT NULL,
            sub_interval varchar(45) NOT NULL,
            sub_description varchar(255) NOT NULL,
            sub_method varchar(45) NOT NULL,
            sub_status varchar(25) NOT NULL,
            created_at timestamp NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";
        
        dbDelta($sqlSubscriptions);         // update table
    }
    
    public function import_default_forms()
    {
         if (! method_exists('GFAPI', 'get_forms')) {
             return;
         }

        // check if form 'gfm_default_form' exists
        $form_exists = false;
        $forms = GFAPI::get_forms();
        foreach($forms as $form) {
            if ($form['title'] == 'GFM Default Form') {
                return;
            }
        }
        
        // import default form(s) if not existing
        $path = GFM_PLUGIN_PATH . 'forms/gfm_default_forms.json'; 
        $forms_json = file_get_contents($path);
        $forms = json_decode($forms_json, true);

        unset($forms['version']);
        GFAPI::add_forms($forms);
    }
    
    public function uninstall_database () 
    {
        $table_donations     = GFM_TABLE_DONATIONS;
        $table_donors        = GFM_TABLE_DONORS;
        $table_subscriptions = GFM_TABLE_SUBSCRIPTIONS;

        $this->wpdb->query("DROP TABLE IF EXISTS $table_donations");
        $this->wpdb->query("DROP TABLE IF EXISTS $table_donors");
        $this->wpdb->query("DROP TABLE IF EXISTS $table_subscriptions");
    }
}

