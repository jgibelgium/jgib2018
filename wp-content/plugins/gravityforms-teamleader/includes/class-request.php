<?php
class GFT_Request 
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
		
		add_action('gform_after_submission', array($this, 'send_form_data'), 10, 2);          // hook form submission from Gravity Forms
        		
    }
        
    public function send_form_data($entry, $form) 
    {
    	$addon = GFTeamleaderAddOn::get_instance();
		if (! $addon->is_teamleader_form($form))
            return;
        
        $fields         = $addon->get_field_values($entry, $form);
        
        $form_settings  = $addon->get_form_settings($form);
        $tag            = $this->gval($form_settings, 'contact_tag');                                      
		
		$this->send_data($fields, $tag);
        
        return;
    }

    public function send_mollie_data($data, $tag, $is_success) 
    {
    	$addon = GFTeamleaderAddOn::get_instance();

        $addon->log_message(__METHOD__, 'Data received from Mollie started', 2);              

        $plugin_settings    = $addon->get_plugin_settings();       
        $ok_only = $this->gval($plugin_settings, 'mollie_success_only');                                              
        if ($is_success || !$ok_only) 
        { 
            // map Mollie names --> Teamleader names
            $fields[GFT_FIELD_FIRSTNAME]    = $this->gval($data, GFM_FIELD_FIRSTNAME);
            $fields[GFT_FIELD_LASTNAME]     = $this->gval($data, GFM_FIELD_LASTNAME);
            $fields[GFT_FIELD_EMAIL]        = $this->gval($data, GFM_FIELD_EMAIL);
            $fields[GFT_FIELD_PHONE]        = $this->gval($data, GFM_FIELD_PHONE);
            $fields[GFT_FIELD_STREET]       = $this->gval($data, GFM_FIELD_ADDRESS);
            $fields[GFT_FIELD_ZIPCODE]      = $this->gval($data, GFM_FIELD_ZIPCODE);
            $fields[GFT_FIELD_CITY]         = $this->gval($data, GFM_FIELD_CITY);
            $fields[GFT_FIELD_COUNTRY]      = $this->gval($data, GFM_FIELD_COUNTRY);
            $fields[GFT_FIELD_OPTIONS]      = $this->gval($data, GFM_FIELD_OPTIONS);
            
            // add custom fields
            $custom_fields = $this->get_custom_fields($data, $plugin_settings);           
            foreach($custom_fields as $key => $value){
                $fields[$key] = $value;
            }
  
            $this->send_data($fields, $tag);            
        }
        
        $addon->log_message(__METHOD__, 'Data received from Mollie completed', 2);              
        
        return;
    }
    
    private function get_custom_fields($data, $plugin_settings) 
    {
        //$custom_fields = array();  
        $custom_fields = [];         
        for ($n = 1; $n <= GFT_NUM_TL_CUSTOM_FIELDS;  $n++) 
        {
            $name = 'custom_' . $n;
            $setting = $this->gval($plugin_settings, $name);
            $set_vals = explode(':', $setting);
            $ml_code = trim($set_vals[0]);
            $tl_id = trim($set_vals[1]);

            if (array_key_exists($ml_code, $data)) {
                $tl_name = GFT_FIELD_CUSTOM . '-' . $tl_id;
                $custom_fields[$tl_name] = $data[$ml_code];                   
            }
        }
        
        return $custom_fields;      
    }

    public function send_data($fields, $tag) 
    {
    	//echo "sending data";
        $addon = GFTeamleaderAddOn::get_instance();
        
        $plugin_settings    = $addon->get_plugin_settings();

        // set fixed fields
        $fname      = $this->gval($fields, GFT_FIELD_FIRSTNAME);                                      
        $lname      = $this->gval($fields, GFT_FIELD_LASTNAME);            
        $email      = $this->gval($fields, GFT_FIELD_EMAIL); 
        $salut      = $this->gval($fields, GFT_FIELD_SALUTATION); 
        $phone      = $this->gval($fields, GFT_FIELD_PHONE);       
        $mobile     = $this->gval($fields, GFT_FIELD_PHONE_MOBILE); 
        $website    = $this->gval($fields, GFT_FIELD_WEBSITE); 
        $country    = $this->gval($fields, GFT_FIELD_COUNTRY);       
        $zipcode    = $this->gval($fields, GFT_FIELD_ZIPCODE);       
        $city       = $this->gval($fields, GFT_FIELD_CITY);       
        $street     = $this->gval($fields, GFT_FIELD_STREET);              
        $street_nr  = $this->gval($fields, GFT_FIELD_STREET_NR); 
        $language   = $this->gval($fields, GFT_FIELD_LANGUAGE); 
        $gender     = $this->gval($fields, GFT_FIELD_GENDER); 
        $dob        = $this->gval($fields, GFT_FIELD_BIRTHDATE); 
        $desc       = $this->gval($fields, GFT_FIELD_DESCRIPTION);
        
        $options    = strtolower($this->gval($fields, GFT_FIELD_OPTIONS)); 
        $newsletter = (strpos($options, 'newsletter') !== false) || (strpos($options, 'nieuwsbrief') !== false); 
             
        $dflt_url = 'https://app.teamleader.eu/api/addContact.php';
        
        $url        = $this->gval($plugin_settings, 'add_contact_url', $dflt_url);                                      
        $api_group  = $this->gval($plugin_settings, 'api_group');                                      
        $api_secret = $this->gval($plugin_settings, 'api_secret');         

        // set fixed fields
        $data = array(  
            "api_group"=>($api_group),
            "api_secret"=>($api_secret),
            
            "forename"=>($fname),
            "surname"=>($lname),
            "email"=>($email),
            "salutation"=>($salut),
            "telephone"=>($phone),
            "gsm"=>($mobile),
            "website"=>($website),
            "country"=>($country),
            "zipcode"=>($zipcode),
            "city"=>($city),
            "street"=>($street),
            "number"=>($street_nr),           
            "language"=>($language),
            "gender"=>($gender),           
            "dob"=>($dob),
            "description"=>($desc),                 
            "newsletter"=>($newsletter),                 
            "add_tag_by_string"=>($tag)
        );
		
		print_r($data);
        
        // add custom fields
        foreach($fields as $name => $value)
        {
            $pos = strpos($name, GFT_FIELD_CUSTOM);
            if ($pos !== false) 
            {
                $id = trim(substr($name, strlen(GFT_FIELD_CUSTOM) + 1));
                $tl_name = 'custom_field_' . $id;
                $data[$tl_name] = $value;
            }
        }
        
        $addon->log_message(__METHOD__, 'Data transfer to Teamleader started', 2);              

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);   
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);        // test only
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        // test only

        $api_output =  curl_exec($ch);
        $json_output = json_decode($api_output);
        $output = $json_output ? $json_output : $api_output;

        curl_close($ch);
        
        $addon->log_message(__METHOD__, 'Data transfer to Teamleader completed - Output = ' . $output, 2);              
	}
    
    public function gval($array, $key, $dflt = '') 
    {
        return isset($array[$key]) ? $array[$key] : $dflt;
    }
}


