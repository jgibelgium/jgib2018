<?php
class GFM_Hooks 
{
    private $wpdb;
    private $addon;

    public function __construct($addon)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->addon = $addon;

        //note: last parameter is number of arguments passed to function!
        add_action('gform_pre_render', array($this, 'prepare_form'), 10, 1);       
        add_action('gform_pre_submission', array($this, 'modify_entry'), 10, 1);       
        add_action('gform_enqueue_scripts', array($this, 'add_js_css'), 10, 2);
        add_filter('gform_validation', array($this, 'validate_form'), 10, 1);        
        add_filter('gform_disable_notification', array($this, 'disable_notification'), 10, 4);
        add_filter('gform_enable_field_label_visibility_settings', '__return_true');   // add Label visibility option (Field / Appearance tab)
    }

    // prepare form
    public function prepare_form($form) 
    { 
        if (! $this->addon->is_mollie_form($form))
            return $form; 
                
        $form_settings = $this->addon->get_form_settings($form);        
        if (isset($form_settings['retrieve_methods']) && $form_settings['retrieve_methods'] == '1') {      
            $this->set_payment_methods($form);
        }
        
        return $form;
    }
   
    // validate form
    public function validate_form($res) 
    { 
        $form = $res['form'];
        
        if (! $this->addon->is_mollie_form($form))
            return $res; 
                     
        // - Validate payment amount if Checkboxes used with 'use other' GF option [class = 'gfm-field-amount'];
        // - If Dropdown list + Other field used: validation by range of Other (numeric) field [name = 'gfm-field-amount-slct' / 'othr'];
        // 
        $field_id = $this->addon->get_field_id_from_name($form, GFM_FIELD_AMOUNT); 
        if (!empty($field_id))
        {
            $field_input = 'input_' . $field_id;
            $val = rgpost($field_input);
            if ($val == 'gf_other_choice') {
                $val = rgpost($field_input . '_other');
            }

            $form_settings = $this->addon->get_form_settings($form);        
            $min_amount = isset($form_settings['minimum-payment-amount']) ? $form_settings['minimum-payment-amount'] : 0; 

            foreach($form['fields'] as &$field)
            {
                if ($field->id == $field_id) 
                {
                    if ($val < $min_amount) 
                    {
                        $field->failed_validation = true;
                        $field->validation_message = 'Payment amount must be greater than ' . $min_amount;
                        
                        $res['is_valid'] = false;
                        break;
                    }
                }
            }        
        }
        
        $res['form'] = $form;
        return $res;
    }
   
    // load script filter_methods.js
    public function add_js_css($form, $is_ajax) 
    {
        if (! $this->addon->is_mollie_form($form))
            return $form; 
        
        $path = '/wp-content/plugins/gravityforms-mollie/js/filter_methods.js';
        wp_enqueue_script('gfm_methods', $path);
        
        $path = '/wp-content/plugins/gravityforms-mollie/css/custom.css';        
        wp_enqueue_style('gfm_styles', $path);       
    }
    
    public function modify_entry($form)
    {
        if (! $this->addon->is_mollie_form($form))
            return;
        
       $donation_id = uniqid(rand(1, 99));          
       $this->addon->set_field_value(GFM_FIELD_DONATION_ID, $donation_id, $form); 
    }
      
    // disable all notifications for mollie forms
    public function disable_notification($is_disabled, $notification, $form, $entry) 
    {     
        $is_mollie_form = $this->addon->is_mollie_form($form);
        $ret = $is_mollie_form? true : $is_disabled;   // disable only for Mollie forms  
        return $ret;                 
    } 
    
    public function set_payment_methods($form) 
    {
        $mollie = $this->addon->init_mollie();  
        if (!isset($mollie))
            return;
        
        try 
        {
            $methods = $mollie->methods->all();
                        
            foreach ($form['fields'] as &$field) 
            {
                $fieldName = $field['adminLabel'];

                if (isset($fieldName) && $fieldName == GFM_FIELD_PAYMENT_METHOD) 
                {
                    $choices = array();                 // payment-method field found               
                    $sel = true;
                    foreach ($methods as $method) 
                    {
                        $img = $field->type == 'radio' ? '<img style="vertical-align:middle;display:inline-block" src="' . esc_url($method->image->normal) . '">' : '';
                        $choices[] = array('text' => $img . '  ' . $method->description, 'value' => $method->id, 'isSelected' => $sel);
                        $sel = false;
                    }

                    $field->choices = $choices;
                    break;
                }           
            }                        
        } 
        catch (Mollie_API_Exception $e) {
            $this->addon->show_api_message(__METHOD__, $e->getMessage());
        }
    }    
}
