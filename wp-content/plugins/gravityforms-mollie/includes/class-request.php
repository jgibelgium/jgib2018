<?php
class GFM_Request 
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        add_action('gform_after_submission', array($this, 'send_payment'), 10, 2);          // hook form submission
    }
    
	public function send_payment($entry, $form) 
    {
        $addon = GFMollieAddOn::get_instance();
        if (! $addon->is_mollie_form($form))
            return;
        
        try 
        {
            $form_settings = $addon->get_form_settings($form);
            $fields = $addon->get_field_values($entry, $form);
            
            $form_id = $form['id'];
            $entry_id = $entry['id'];          
                                                                                // Field Validations
            $interval   = $this->gval($fields, GFM_FIELD_PAYMENT_INTERVAL);     // Required                                      
            $method     = $this->gval($fields, GFM_FIELD_PAYMENT_METHOD);       // Required                                
            $amount     = $this->gval($fields, GFM_FIELD_AMOUNT);               // Required                                     
            $am_slct    = $this->gval($fields, GFM_FIELD_AMOUNT_SELECTED);      // Required                                     
            $am_othr    = $this->gval($fields, GFM_FIELD_AMOUNT_OTHER);         // Min amount 
            
            $fname      = $this->gval($fields, GFM_FIELD_FIRSTNAME);            // Required                                    
            $lname      = $this->gval($fields, GFM_FIELD_LASTNAME);             // Required                                  
            $email      = $this->gval($fields, GFM_FIELD_EMAIL);                // Required                              
            $phone      = $this->gval($fields, GFM_FIELD_PHONE);                                      
            $address    = $this->gval($fields, GFM_FIELD_ADDRESS);                                      
            $zipcode    = $this->gval($fields, GFM_FIELD_ZIPCODE);                                      
            $city       = $this->gval($fields, GFM_FIELD_CITY);                                      
            $country    = $this->gval($fields, GFM_FIELD_COUNTRY);                                      
            $project    = $this->gval($fields, GFM_FIELD_PROJECT);                                      
            $company    = $this->gval($fields, GFM_FIELD_COMPANY);                                      
            $message    = $this->gval($fields, GFM_FIELD_MESSAGE);                                      
            $auth       = $this->gval($fields, GFM_FIELD_AUTHORIZATION);                                      
            $donation_id = $this->gval($fields, GFM_FIELD_DONATION_ID);
            
            // Radio buttons used -> amount = AMOUNT [AM_SLCT / AM_OTHR = null; GF 'Other value' option used]
            // Dropdown list used -> amount = AM_SLCT or AM_OTHR [AMOUNT = null; separate field AM_OTHR used]
            if (!empty($am_slct)) {
                $amount = (!empty($am_othr) && $am_othr > '0.00' ) ? $am_othr : $am_slct;
            }
            
            if (empty($donation_id)) {                   // hidden field GFM_FIELD_DONATION_ID not set 
                $donation_id = uniqid(rand(1, 99));          
            }
                        
            $name = $fname . ' ' . $lname;        
            
            $interval = (empty($interval) ? '0' : $interval);
            $interval_units = $this->gval($form_settings, 'interval-units', 'months');                                                            
            $interval_desc = ($interval == '0' ? 'one-time' : $interval . ' ' . $interval_units);
            $interval_text = $this->get_option_text($form, GFM_FIELD_PAYMENT_INTERVAL, $interval);
                    
            $payment_desc_template = $this->gval($form_settings, 'payment-description', '{tag} {name} {amount} {email} {interval}');  
            
            $tag = $this->gval($form_settings, 'tag');
            
            // get values of custom type fields
            $custom_fields = $this->get_custom_fields($form, $fields);    
            $custom = implode(', ', $custom_fields);
            
            $description = str_replace(
                array('{id}', '{tag}', '{name}', '{amount}', '{email}', '{interval}', '{custom}'),
                array($donation_id,  $tag, $name, $amount, $email, $interval_text, $custom),
                $payment_desc_template
            );
            
            $redirect_url = get_page_link() . '?' . GFM_DONATION_ID . '=' . $donation_id . '&' . GFM_FORM_ID . '=' . $form_id . '&' . GFM_ENTRY_ID . '=' . $entry_id;
			//$addon->show_api_message(__METHOD__, '$redirect_url: {p1}', 3, $redirect_url);
            
            $mollie = $addon->init_mollie();                                     // initialize Mollie API
            if (!isset($mollie))
                return;
            
            if ($interval == '0')                       // Donatie interval = Eenmalig
            {
                $webhook_url = $addon->webhook_url;
                
               // create payment in Mollie
                $addon->log_message(__METHOD__, 'Create payment started   [type = one-time]', 2);              
                
                $payment = $mollie->payments->create(array(
                    "amount"        => $amount,
                    "description"   => $description,
                    "redirectUrl"   => $redirect_url,
                    "webhookUrl"    => $webhook_url,
                    "method"        => $method,
                    "metadata"      => array(
                        "name"          => $name,
                        "email"         => $email,
                        "donation_id"   => $donation_id,
                    )
                ));
                
                $addon->log_message(__METHOD__, 'Create payment completed [type = one-time / payment_id = {p1}]', 2, $payment->id);
			}
            else                                    // Donation interval = N periods
            {
                // create customer in Mollie              
                $addon->log_message(__METHOD__, 'Create customer started  ', 2);              

                $secret = uniqid();
                $customer = $mollie->customers->create(array(
                    "name"  => $name,
                    "email" => $email,
                ));
                
                $addon->log_message(__METHOD__, 'Create customer completed [customer_id = {p1}]', 2, $customer->id); 

                // insert customer -> donors table
                $sql = "INSERT INTO " . GFM_TABLE_DONORS . "
                        (customer_id, customer_mode, customer_name, customer_email, sub_interval, sub_amount, sub_description, customer_locale, secret)
                        VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s )";
                
                $qry = $this->wpdb->prepare($sql,                     
                    $customer->id,
                    $customer->mode,
                    $customer->name,
                    $customer->email,
                    $interval_desc,
                    $amount,
                    $description,
                    $customer->locale,
                    $secret
                );
                
                $res = $this->wpdb->query($qry);
                $record_id = $this->wpdb->insert_id;
                
                $addon->log_message(__METHOD__, 'Insert customer [request] - {p1}', 3, $res ? 'success' : 'failed');                                        
                
                // create First payment in Mollie
                $webhook_url = $addon->webhook_url . 'first/' . $record_id  . '/secret/' . $secret;
                
                $addon->log_message(__METHOD__, 'Create payment started   [type = first]', 2);              

                $payment = $mollie->payments->create(array(
                    "customerId"    => $customer->id,
                    "recurringType" => 'first',
                    "amount"        => $amount,
                    "description"   => $description,
                    "redirectUrl"   => $redirect_url,
                    "webhookUrl"    => $webhook_url,
                    "method"        => $method,
                    "metadata"      => array(
                        "name"          => $name,
                        "email"         => $email,
                        "donation_id"   => $donation_id,
                    )
                ));
                
                $addon->log_message(__METHOD__, 'Create payment completed [type = first / payment_id = {p1}]', 2, $payment->id);
            }//end else
            
            // insert donation -> donations table
            
            $tpl = "INSERT INTO " . GFM_TABLE_DONATIONS . 
                   "(`time`, payment_id, customer_id, donation_id, status, amount, name, email, project, company, address, zipcode, city, country, message, phone, tag, custom, payment_method, payment_mode)" .
                    "VALUES ( %s, %s, %s, %s, %s, %f, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )";
            
           $qry = $this->wpdb->prepare($tpl,  
                        //date('Y-m-d H:i:s'),
                        current_time('mysql', false),
                        $payment->id,
                        (isset($customer) ? $customer->id : null),
                        $donation_id,
                        'open',
                        $amount,
                        isset($name)    ? $name : null,
                        isset($email)   ? $email : null,
                        isset($project) ? $project : null,
                        isset($company) ? $company : null,
                        isset($address) ? $address : null,
                        isset($zipcode) ? $zipcode : null,
                        isset($city)    ? $city : null,
                        isset($country) ? $country : null,
                        isset($message) ? $message : null,
                        isset($phone)   ? $phone : null,
                        isset($tag)     ? $tag : null,
                        isset($custom)  ? $custom : null,
                        $payment->method,
                        $payment->mode
                    );
           
            $res = $this->wpdb->query($qry);
            $addon->log_message(__METHOD__, 'Insert donation [request] - {p1}', 3, $res ? 'success' : 'failed');                          
			 
			  
            // redirect to Mollie payment page
            $mollie_page_url = $payment->getPaymentUrl();
		    $addon->log_message(__METHOD__, '$mollie_page_url: {p1}', 3, $mollie_page_url);
			wp_redirect($mollie_page_url); 
		    exit;// wp_redirect() does not exit automatically, and should almost always be followed by a call to exit
        } 
        catch (Mollie_API_Exception $e) {
            $addon->show_api_message(__METHOD__, $e->getMessage());
			//$addon->show_api_message(__METHOD__, "Ron is in API exception");
        }       
	}

    public function get_custom_fields($form, $fields)
    {
        $custom_fields = [];  
        //$custom_fields = array();             
        foreach ($form['fields'] as $fld) {
            if (strpos($fld->cssClass, GFM_TYPE_CUSTOM) !== false) {
                $val = $this->gval($fields, $fld->adminLabel);
                if (! empty($val))
                    $custom_fields[] = $val;
				    //$custom_fields = $val;
            }
        }
        
        return $custom_fields;
    }
    
    public function get_option_text($form, $field_name, $value)
    {
        $text = '';
        foreach ($form['fields'] as $field) 
        {
            if ($field['adminLabel'] == $field_name) 
            {
                foreach($field['choices'] as $option)
                {
                    $txt = $option['text'];
                    $val = $option['value'];
                    if ($val == $value) {
                        $text = $txt;
                    }
                }
            }
            if (!empty($text))
                break;
        }      
        return $text;
    }
    
    public function gval($array, $key, $dflt = '') 
    {
        return isset($array[$key]) ? $array[$key] : $dflt;
    }

}


