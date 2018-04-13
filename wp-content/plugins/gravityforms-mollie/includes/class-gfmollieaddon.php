<?php
GFForms::include_addon_framework();

class GFMollieAddOn extends GFAddOn 
{
	protected $_version = GF_MOLLIE_ADDON_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'mollieaddon';
	protected $_path = 'gravityforms-mollie/mollieaddon.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms Mollie Add-On';
	protected $_short_title = 'Mollie Add-On';

	private static $_instance = null;
    
    public $webhook_url;    
    public $page_url;
    public $plugin_settings;
    public $donation_id;
    
    private $wpdb;

	public static function get_instance() 
    {
		if ( self::$_instance == null ) {
			self::$_instance = new GFMollieAddOn();
		}

		return self::$_instance;
	}

	public function init() 
    {
        global $wpdb;
        $this->wpdb = $wpdb;

		parent::init();
        
        $this->plugin_settings = $this->get_plugin_settings();              // get global plugin settings
        $this->gfm_hooks   = new GFM_Hooks($this);                          // create Gravity Forms hooks object        

        $site_url = get_home_url();                                        
        if (GFM_TEST_URL !== '') {                                          // test URL is set
            $site_url = str_replace('http://localhost:8080', GFM_TEST_URL,  $site_url);
        }   

        $this->site_url    = $site_url;                                     // set site / webhook URL
        $this->webhook_url = $site_url . '/' .GFM_WEBHOOK. '/';
				                
        add_shortcode('gfm_payments_total', array($this, 'set_payments_total'));  // shortcode for total payments amount 

        if (isset($_GET[GFM_DONATION_ID])) {                                // payment confirmed by Mollie
            $this->process_result();                                        // do post payment processing
        }
	}
    
    public function init_mollie() 
    {
        if (empty($this->plugin_settings) || empty($this->plugin_settings['api-key'])) 
        {
            $this->show_api_message(__METHOD__, 'API key not set');
            return;               
        }
        
        $api_key = $this->plugin_settings['api-key'];     

        try 
        {
            $mollie = new Mollie_API_Client;                                      // create Mollie API object
            
            $this->log_message(__METHOD__, 'Set API Key started   [key = {p1}]', 3, $api_key);              

            $mollie->setApiKey($api_key);                                         // set Mollie API key 
            
            $this->log_message(__METHOD__, 'Set API Key completed [key = {p1}]',  3, $api_key);               
        } 
        catch (Mollie_API_Exception $e) {
            $this->addon->show_api_message(__METHOD__, $e->getMessage());
        }
                
        return $mollie;
    }
    
	public function process_result() 
    {
    	 
        //  for test: check form_id
        //  localhost/janegoodall/gravity-form-donaties/?donation_id=47598e0e94b9c86&form_id=12&entry_id=150
        //  localhost/janegoodall/1-adopteer-chimp-pdj/?donation_id=3959a189967d1c7&form_id=12&entry_id=194
        $url = get_page_link();
  
        $mollie = $this->init_mollie();                                         // initialize Mollie API
        if (!isset($mollie))
            return;
        
        $donation_id = $_GET[GFM_DONATION_ID];
        $form_id     = $_GET[GFM_FORM_ID];
        $entry_id    = $_GET[GFM_ENTRY_ID];
        
        $this->donation_id = $donation_id;                                          // save for email formatting     

        $this->log_message(__METHOD__, 'Completion hook started   [donation_id = {p1} / form_id = {p2} / entry_id = {p3}]', 2, $donation_id, $form_id, $entry_id); 
        
        try 
        {
            // get payment data
            $donation = $this->wpdb->get_row("SELECT * FROM " . GFM_TABLE_DONATIONS . " WHERE donation_id = '" . esc_sql($donation_id) . "'");      
            $payment_id = $donation->payment_id;

            $this->log_message(__METHOD__, 'Get payment started   [payment_id = {p1}]', 3, $payment_id);              

            $payment = $mollie->payments->get($payment_id);                         // get payment status

            $this->log_message(__METHOD__, 'Get payment completed [payment_id = {p1} payment_status = {p2}]', 3, $payment_id, $payment->status);              

            $form = GFAPI::get_form($form_id);
            $form_settings = $this->get_form_settings($form);

            $entry = GFAPI::get_entry($entry_id);

            $res_OK = ($payment->status == 'paid');
            if ($res_OK) {
                $this->send_notifications($form, $entry);                       // send notification emails             
            }
                   
            if (isset($form_settings['notify_plugins']) && $form_settings['notify_plugins'] == '1') 
            {
                $tag = isset($form_settings['tag']) ? $form_settings['tag'] : '';
                $data = $this->get_field_values($entry, $form);

                do_action('gf_mollie_payment_completed', $data, $tag, $res_OK);    		
            }
            
            $page_type = $res_OK ? 'success' : 'failure';
            $page_name = 'page-'. $page_type;
            $page = $form_settings[$page_name];
            if (isset($page)) {                                                 // redirect page specified
                $url = $this->site_url . '/' . $page . '/';    
            } else {
                $url = get_page_link() . '?msg=' . $page_name;                 // TODO: FIX!!! back to current page with message
            }           
            
        } 
        catch (Mollie_API_Exception $e) {
            $this->show_api_message(__METHOD__, $e->getMessage());
        }
        
        wp_redirect($url);
        
        $this->log_message(__METHOD__, 'Completion hook completed [donation_id = {p1} / form_id = {p2} / entry_id = {p3}]', 2, $donation_id, $form_id, $entry_id); 

        exit;
	}
    
    
	public function send_notifications($form, $entry) 
    {
        $notification_ids = array();
               
        foreach($form['notifications'] as $id => $info) {
            array_push($notification_ids, $id);
        }

        GFCommon::send_notifications($notification_ids, $form, $entry);
    }
           
    public function plugin_settings_title() {
        return "Gravity Forms Mollie Add-On Settings";
    }

	public function plugin_settings_fields() 
    {
		return array(
			array(
				'title'  => esc_html__('Mollie Add-On Settings', 'mollieaddon'),
				'fields' => array(
					array(
						'name'              => 'api-key',
						'label'             => esc_html__('API Key', 'mollieaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter Mollie API Key', 'mollieaddon' ),
						'class'             => 'medium',
						//'feedback_callback' => array( $this, 'is_valid_setting' ),
					),
					array(
						'name'    => 'rights-donations',
						'label'   => esc_html__('Rights donations', 'mollieaddon'),
						'type'    => 'select',
						'tooltip' => esc_html__('Select a donation rights level', 'mollieaddon' ),
						'choices' => array(
							array(
								'label' => esc_html__('Administrator', 'mollieaddon'),
								'value' => 'edit_dashboard',
							),
							array(
								'label' => esc_html__('Editor', 'mollieaddon'),
								'value' => 'edit_pages',
							),
							array(
								'label' => esc_html__('Author', 'mollieaddon'),
								'value' => 'edit_posts',
							),
						),
					),
					array(
						'name'    => 'rights-subscriptions',
						'label'   => esc_html__('Rights subscriptions', 'mollieaddon'),
						'type'    => 'select',
						'tooltip' => esc_html__('Select a subscriptions rights level', 'mollieaddon'),
						'choices' => array(
							array(
								'label' => esc_html__('Administrator', 'mollieaddon'),
								'value' => 'edit_dashboard',
							),
							array(
								'label' => esc_html__('Editor', 'mollieaddon'),
								'value' => 'edit_pages',
							),
							array(
								'label' => esc_html__('Author', 'mollieaddon'),
								'value' => 'edit_posts',
							),
						),
					),
					array(
						'name'    => 'loglevel',
						'label'   => esc_html__('Log level', 'mollieaddon'),
						'type'    => 'select',
						'tooltip' => esc_html__('Enter the logging level', 'mollieaddon'),
						'choices' => array(
							array(
								'label' => esc_html__('High', 'mollieaddon'),
								'value' => '3',         // msg loglevel = 1,2,3
							),
							array(
								'label' => esc_html__('Medium', 'mollieaddon'),
								'value' => '2',         // msg loglevel = 1,2
							),
							array(
								'label' => esc_html__('Low', 'mollieaddon'),
								'value' => '1',         // msg loglevel = 1 
							),
							array(
								'label' => esc_html__('No logging', 'mollieaddon'),
								'value' => '0',         // no logging
							),
						),
					),
					array(
						'name'              => 'donation-columns',
						'label'             => esc_html__('Donations table columns', 'mollieaddon'),
						'type'              => 'checkbox',
						'tooltip'           => esc_html__('Columns to display in Donations list', 'mollieaddon' ),
						'choices'           => array( 
                                                array(
                                                    'name'    => 'col_time',
                                                    'label'   => esc_html__('Date/time', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_amount',
                                                    'label'   => esc_html__('Amount', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_payment_id',
                                                    'label'   => esc_html__('Payment ID', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_subscription_id',
                                                    'label'   => esc_html__('Subscription ID', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_payment_method',
                                                    'label'   => esc_html__('Method', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_sub_interval',
                                                    'label'   => esc_html__('Interval', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_donation_id',
                                                    'label'   => esc_html__('Donation ID', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_status',
                                                    'label'   => esc_html__('Status', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_name',
                                                    'label'   => esc_html__('Name', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_email',
                                                    'label'   => esc_html__('Email', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_company',
                                                    'label'   => esc_html__('Company', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_project',
                                                    'label'   => esc_html__('Project', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_address',
                                                    'label'   => esc_html__('Address', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_zipcode',
                                                    'label'   => esc_html__('Zipcode', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_city',
                                                    'label'   => esc_html__('City', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_country',
                                                    'label'   => esc_html__('Country', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_tag',
                                                    'label'   => esc_html__('Tag', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'    => 'col_custom',
                                                    'label'   => esc_html__('Custom', 'mollieaddon'),
                                                    'default-value' => 1,
                                                ),
                                            ),
					),

				)
			),
		);
	}

	public function form_settings_fields( $form ) 
    {
        $pages = get_pages();

        $page_list = array();
        
        foreach ($pages as $page)
        {
            $page_entry = array();
            $page_entry['label'] = esc_html__($page->post_title, 'mollieaddon');
            $page_entry['value'] = $page->post_name;
            
            $page_list[] = $page_entry;           
        }
        
		return array(
			array(
				'title'  => esc_html__('Mollie Form Settings', 'mollieaddon'),
                
				'fields' => array(
					array(
						'name'              => 'minimum-payment-amount',
						'label'             => esc_html__('Minimum payment amount', 'mollieaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter minimum amount for payment', 'mollieaddon' ),
						'class'             => 'small',
                    ),
					array(
						'name'    => 'interval-units',
						'label'   => esc_html__('Donation interval units', 'mollieaddon'),
						'type'    => 'select',
						'tooltip' => esc_html__('Enter interval units for recurring donations', 'mollieaddon'),
                        /* note: values must be Mollie interval string format */
						'choices' => array(
							array(
								'label' => esc_html__('Day', 'mollieaddon'),
								'value' => 'days',       
							),
							array(
								'label' => esc_html__('Week', 'mollieaddon'),
								'value' => 'weeks',        
							),
							array(
								'label' => esc_html__('Month', 'mollieaddon'),
								'value' => 'months',         
							),
						),
					),
					array(
						'name'              => 'tag',
						'label'             => esc_html__('Tag', 'mollieaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter tag for external reference', 'mollieaddon' ),
						'class'             => 'small',
                    ),
					array(
						'name'              => 'payment-description',
						'label'             => esc_html__('Payment description', 'mollieaddon'),
						'type'              => 'text',
						'tooltip'           => esc_html__('Enter payment description template', 'mollieaddon'),                       
						'class'             => 'medium',
					),
					array(
						'name'              => 'page-success',
						'label'             => esc_html__('Page after donation success', 'mollieaddon'),
						'type'              => 'select',
						'tooltip'           => esc_html__('Enter success page', 'mollieaddon'),
						'choices'           => $page_list,
					),
					array(
						'name'              => 'page-failure',
						'label'             => esc_html__('Page after donation failed', 'mollieaddon'),
						'type'              => 'select',
						'tooltip'           => esc_html__('Enter failure page', 'mollieaddon'),
						'choices'           => $page_list,
					),
					array(
						'name'              => 'options',
						'label'             => esc_html__('Options', 'mollieaddon'),
						'type'              => 'checkbox',
						'tooltip'           => esc_html__('Options for this form', 'mollieaddon' ),
						'choices'           => array( 
                                                array(
                                                    'name'    => 'retrieve_methods',
                                                    'label'   => esc_html__('Retrieve payment methods', 'mollieaddon'),
                                                    'tooltip' => esc_html__('Retrieve available payment methods from your Mollie account', 'mollieaddon' ),
                                                    'default-value' => 1,
                                                ),
                                                array(
                                                    'name'  => 'notify_plugins',
                                                    'label' => esc_html__('Notify other plugins', 'mollieaddon'),
                                                    'tooltip' => esc_html__('Notify other plugins after completion of payment', 'mollieaddon' ),
                                                    'default-value' => 1,
                                                ),
                                            ),
					),
				),
			),
		);
	}
    
 	// # HELPERS ------------------------------------------------------------------------------------------------------- 
    public function get_field_id_from_name($form, $field_name) 
    {         
        foreach ($form['fields'] as $field) 
        {
            if (!isset($field['adminLabel']) || !$field['adminLabel']) {
                continue;
            }
            
            if ($field['adminLabel'] == $field_name) {
                return $field['id'];
            }
        }
        
        return '';
    }
    
    public function set_field_value($field_name, $value, $form) 
    { 
        foreach ($form['fields'] as $field) 
        {
            if (!isset($field['adminLabel']) || !$field['adminLabel']) {
                continue;
            }
            
            if ($field['adminLabel'] == $field_name) {
                $f_id   = 'input_' . $field['id'];                
                $_POST[$f_id] = $value;
                break;
            }
        }             

        return;
    }
    
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
    
    public function set_payments_total($atts)
    {
        ob_start();
        
        $mode = $atts[mode];
        
        $sum = $this->wpdb->get_var("SELECT SUM(amount) FROM " . GFM_TABLE_DONATIONS . " WHERE status='paid' AND payment_mode='$mode'");
        echo '&euro;' . number_format($sum, 2, ',', '');

        $output = ob_get_clean();
        return $output;
    }
    
    public function is_mollie_form($form) 
    {
        $cssClass = $form['cssClass'];
        if (strpos($cssClass, GFM_FORM) !== false)              // CSS Class must contain 'gfm-form'
            return true;
        
        return false;
    }
    
    public function log_message($method, $message, $loglevel = 3, $p1 = '', $p2 = '', $p3 = '')       // 3 is lowest level
    {
        $lvl = $this->plugin_settings['loglevel'];
        if ($loglevel <= $lvl)
        {
            $message = str_replace('{p1}', $p1, $message);
            $message = str_replace('{p2}', $p2, $message);
            $message = str_replace('{p3}', $p3, $message);
            
            $msg = '[' . $method . '] ' . $message;
            $this->log_debug($msg);                             // add-on log @ [site]\wp-content\uploads\gravity_forms\logs\mollieaddon_xxx.txt
        }
    }
    
    public function show_api_message($method, $message)
    {
        $msg = 'Mollie API Error - ' . htmlspecialchars($message);
        
        echo '<div class="gfm-api-message">'. $msg . '</div>';                    
        $this->log_message($method, $msg, 1);
    }

}
