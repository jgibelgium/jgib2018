<?php

GFForms::include_addon_framework();

class GFTeamleaderAddOn extends GFAddOn 
{
	protected $_version = GF_TEAMLEADER_ADDON_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'teamleaderaddon';
	protected $_path = 'gravityforms-teamleader/teamleaderaddon.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms Teamleader Add-On';
	protected $_short_title = 'Teamleader Add-On';

	private static $_instance = null;
    private $wpdb;
    
    public $page_url;

    // Fields available in TL Contact API
    public $fieldNames = array(
        GFT_FIELD_FIRSTNAME, GFT_FIELD_LASTNAME, GFT_FIELD_EMAIL, GFT_FIELD_SALUTATION, 
        GFT_FIELD_PHONE, GFT_FIELD_PHONE_MOBILE, GFT_FIELD_WEBSITE, GFT_FIELD_COUNTRY,
        GFT_FIELD_ZIPCODE, GFT_FIELD_CITY, GFT_FIELD_STREET, GFT_FIELD_STREET_NR, 
        GFT_FIELD_LANGUAGE, GFT_FIELD_GENDER, GFT_FIELD_BIRTHDATE, GFT_FIELD_DESCRIPTION, 
        GFT_FIELD_OPTIONS,
    );    
    
	public static function get_instance() 
    {
    	if ( self::$_instance == null ) {
			self::$_instance = new GFTeamleaderAddOn();
		}

		return self::$_instance;
		
	}

	public function init() 
    {
        global $wpdb;
        $this->wpdb = $wpdb;

		parent::init();

        $site_url = get_home_url();                                        
        if (GFT_TEST_URL !== '') {                                          // test URL is set
            $site_url = str_replace('http://localhost:8080', GFT_TEST_URL,  $site_url);
        }   

        $this->site_url    = $site_url;                                     // set site URL
     }
    
    public function plugin_settings_title() {
        return "Gravity Forms Teamleader Add-On Settings";
    }
 
	public function plugin_settings_fields() 
    {
		return array(
			array(
				'title'  => esc_html__('Teamleader Add-On Settings', 'teamleaderaddon'),
				'fields' => array(
					array(
						'name'              => 'add_contact_url',
						'label'             => esc_html__('Add Contact URL', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Teamleader URL for Add Contact API', 'teamleaderaddon' ),
						'class'             => 'medium',
					),                                    
					array(
						'name'              => 'api_group',
						'label'             => esc_html__('API group', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Teamleader API Group', 'teamleaderaddon' ),
						'class'             => 'medium',
					),                                    
					array(
						'name'              => 'api_secret',
						'label'             => esc_html__('API Secret', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Teamleader API Secret', 'teamleaderaddon' ),
						'class'             => 'large',
					),
					array(
						'name'              => 'custom_1',
						'label'             => esc_html__('Custom field 1', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Mollie name and Teamleader ID [name:id]', 'teamleaderaddon' ),
						'class'             => 'medium',
					),
					array(
						'name'              => 'custom_2',
						'label'             => esc_html__('Custom field 2', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Mollie name and Teamleader ID [name:id]', 'teamleaderaddon' ),
						'class'             => 'medium',
					),
					array(
						'name'              => 'custom_3',
						'label'             => esc_html__('Custom field 3', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Mollie name and Teamleader ID [name:id]', 'teamleaderaddon' ),
						'class'             => 'medium',
					),
					array(
						'name'              => 'custom_4',
						'label'             => esc_html__('Custom field 4', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Mollie name and Teamleader ID [name:id]', 'teamleaderaddon' ),
						'class'             => 'medium',
					),
					array(
						'name'              => 'custom_5',
						'label'             => esc_html__('Custom field 5', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Mollie name and Teamleader ID [name:id]', 'teamleaderaddon' ),
						'class'             => 'medium',
					),
					array(
						'name'              => 'options',
						'label'             => esc_html__('Options', 'teamleaderaddon'),
						'type'              => 'checkbox',
						'tooltip'           => esc_html__('Options', 'teamleaderaddon' ),
						'choices'           => array( 
                                                array(
                                                    'name'    => 'mollie_success_only',
                                                    'label'   => esc_html__('Successful Mollie payments only', 'teamleaderaddon'),
                                                    'tooltip' => esc_html__('Accept data from Mollie only if payment was processed succesfully', 'teamleaderaddon' ),
                                                    'default-value' => 1,
                                                ),
                                            ),
					),
					array(
						'name'    => 'loglevel',
						'label'   => esc_html__('Log level', 'teamleaderaddon'),
						'type'    => 'select',
						'tooltip' => esc_html__('Enter the logging level', 'teamleaderaddon'),
						'choices' => array(
							array(
								'label' => esc_html__('High', 'teamleaderaddon'),
								'value' => '3',         // msg loglevel = 1,2,3
							),
							array(
								'label' => esc_html__('Medium', 'teamleaderaddon'),
								'value' => '2',         // msg loglevel = 1,2
							),
							array(
								'label' => esc_html__('Low', 'teamleaderaddon'),
								'value' => '1',         // msg loglevel = 1 
							),
							array(
								'label' => esc_html__('No logging', 'teamleaderaddon'),
								'value' => '0',         // no logging
							),
						),
					),   
				)
			),
		);
	}

	public function form_settings_fields( $form ) 
    {       
		return array(
			array(
				'title'  => esc_html__('Teamleader Form Settings', 'teamleaderaddon'),                
				'fields' => array(                   
					array(
						'name'              => 'contact_tag',
						'label'             => esc_html__('Contact tag', 'teamleaderaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter the tag for the Teamleader contact', 'teamleaderaddon'),
						'class'             => 'small',
					),
				),
			),
		);
	}
    
 	// # HELPERS -------------------------------------------------------------------------------------------------------            
    public function get_field_values($entry, $form) 
    {
    	$field_values = array();
    	        
        foreach ($form['fields'] as $field) 
        {
            if (!isset($field['adminLabel']) || !$field['adminLabel']) {        // field has no name
                continue;
            }

            $f_name = $field['adminLabel'];
            $f_id   = $field['id'];
            $f_type = $field['type'];
            if ($f_type == 'option') {          // Option field [Pricing]
                $f_type = $field['inputType'];
            }
            switch ($f_type) 
            {
                case 'checkbox':    // checkbox / option[checkbox]      
                    $val = '';
                    $id = (string)$f_id;
                    foreach ($entry as $key => $value)
                    {
                        if (strpos($key, $id) !== false && !empty($value))  // id found + value present
                        {
                            $val1 = $this->strip_off_price($value);
                            $val .= ($val1 . ', ');                          
                        }                       
                    }
                    $val = substr($val, 0, strlen($val) - 2);   // strip off last comma 
                    
                    break;
                case 'radio':       // option[radiobuttons]
                    $val = $this->strip_off_price($entry[$f_id]);
                    break;
                case 'date':                        // internal GF format is yyyy/mm/dd
                    $dt = $entry[$f_id];
                    $ts = strtotime($dt);
                    $val = date('d/m/Y', $ts);      // TL format is dd/mm/yyyy     
                    break;
                default:    
                    $val = $entry[$f_id];
            }
            
            if (empty($val)) {
                $val = $field->defaultValue;
            }
            
            $field_values[$f_name] = $val;
        }             

        return $field_values;
    }  

    
    public function strip_off_price($value)
    {
        $val1 = $value;
        
        $pos = strpos($value, '|');  
        if ($pos !== false) {           // strip off price for option field
            $val1 = substr($value, 0, $pos); 
        }
        
        return $val1;       
    }
    
    public function is_teamleader_form($form) 
    {
        $cssClass = $form['cssClass'];
        if (strpos($cssClass, GFT_FORM) !== false)              // CSS Class must contain 'gft-form'
            return true;
        
        return false;
    }
    
    public function log_message($method, $message, $loglevel = 3, $p1 = '', $p2 = '', $p3 = '')       // 3 is lowest level
    {
        $plugin_settings    = $this->get_plugin_settings();
        
        $lvl = $plugin_settings['loglevel'];
        if ($loglevel <= $lvl)
        {
            $message = str_replace('{p1}', $p1, $message);
            $message = str_replace('{p2}', $p2, $message);
            $message = str_replace('{p3}', $p3, $message);
            
            $msg = '[' . $method . '] ' . $message;
            $this->log_debug($msg);                             // add-on log @ [site]\wp-content\uploads\gravity_forms\logs\teamleadereaddon_xxx.txt
        }
    }
}