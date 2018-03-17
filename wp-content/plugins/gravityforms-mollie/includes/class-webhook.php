<?php
class GFM_Webhook 
{
    private $wpdb;
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        add_filter('query_vars', array($this, 'add_query_vars'), 0);
        add_action('parse_request', array($this, 'sniff_requests'), 0);
        add_action('init', array($this, 'add_endpoint'), 0);
    }

    public function add_query_vars($vars)
    {
        $vars[] = '__gfmapi';
        $vars[] = 'sub';
        $vars[] = 'first';
        $vars[] = 'secret';
        return $vars;
    }

    public function add_endpoint()
    {
        add_rewrite_rule('^' . GFM_WEBHOOK . '/first/([0-9]+)/secret/([a-zA-Z0-9]+)/?', 'index.php?__gfmapi=1&first=$matches[1]&secret=$matches[2]', 'top');
        add_rewrite_rule('^' . GFM_WEBHOOK . '/sub/([0-9]+)/?', 'index.php?__gfmapi=1&sub=$matches[1]', 'top');
        add_rewrite_rule('^' . GFM_WEBHOOK . '/?','index.php?__gfmapi=1','top');
        flush_rewrite_rules();
    }
 
    // - One-time payment     : http://localhost/jg_test_wp48/gfm-webhook/                                      $_POST['id'] = 'tr_nnnnnnnnnn' [current payment]
    // - First subscr payment : http://localhost/jg_test_wp48/gfm-webhook/first/[cust_rec_id]/secret/[secret];  $_POST['id'] = 'tr_nnnnnnnnnn' [current payment]
    // - Subscription payment : http://localhost/jg_test_wp48/gfm-webhook/sub/[sub_rec_id];                     $_POST['id'] = 'tr_??????????' [new payment]
    
    public function sniff_requests($query)
    {
        //$_POST['id'] = 'tr_6AyrxqDSmk';       
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($query->query_vars['__gfmapi']))
        {
            $result =  $this->handle_request($query);
            
            echo $result;
            exit;
        }
    }


    private function handle_request($query)
    {       
        $addon = GFMollieAddOn::get_instance();
        
        try 
        { 
            $mollie = $addon->init_mollie();                                     // initialize Mollie API
            if (!isset($mollie))
                return $this->set_error(404, 'Mollie API could not be initialized', $addon);

            if (!$query->query_vars['sub'])                                             // not a subscription donation
            {                
                //****************** One-time Payment or First Payment for Subscription ****************************//
                $type = ($query->query_vars['first'] ? 'first' : 'one-time');
                $addon->log_message(__METHOD__, 'Webhook started   [type = {p1}]', 2, $type);              

                $payment_id = $_POST['id'];
                if (!$payment_id)
                    return $this->set_error(404, 'No payment id', $addon);
                
                $sql = "SELECT * FROM " . GFM_TABLE_DONATIONS . " WHERE payment_id = '" . esc_sql($payment_id) . "'";
                $donation = $this->wpdb->get_row($sql);               
                if (!$donation->id)
                    return $this->set_error(404, 'Donation not found', $addon);

                $addon->log_message(__METHOD__, 'Get payment started   [type = one-time or first / payment_id = {p1}]', 3, $payment_id);              

                $payment = $mollie->payments->get($payment_id);
                
                $addon->log_message(__METHOD__, 'Get payment completed [type = one-time or first / payment_id = {p1}]', 3, $payment_id);                          

                $sql = "UPDATE " . GFM_TABLE_DONATIONS . " SET status = %s, payment_method = %s, payment_mode = %s, customer_id = %s WHERE id = %d";
                $qry = $this->wpdb->prepare($sql, 
                            $payment->status,
                            $payment->method,
                            $payment->mode,
                            $payment->customerId,
                            $donation->id
                        );
                
                $res = $this->wpdb->query($qry);               
                $addon->log_message(__METHOD__, 'Update donation [payment] - {p1}', 3, $res ? 'success' : 'failed');                          

                if (($query->query_vars['first'] && $query->query_vars['secret']) && ($payment->isPaid() && !$payment->isRefunded()))
                {
                    //******************  First Payment for Subscription ****************************//
                    $record_id = $query->query_vars['first'];
                    $secret    = $query->query_vars['secret'];                  
                    $sql = "SELECT * FROM " . GFM_TABLE_DONORS . " WHERE id = '" . esc_sql($record_id) . "' AND secret='" . esc_sql($secret) . "'";
                    $customer = $this->wpdb->get_row($sql);                                       
                    if (!$customer->id)
                        return $this->set_error(404, 'Customer not found', $addon);
                    
                    $sql = "INSERT INTO " . GFM_TABLE_SUBSCRIPTIONS . "(customer_id, created_at ) VALUES (%s, NOW())";
                    $qry = $this->wpdb->prepare($sql, $customer->id);
                    
                    $res = $this->wpdb->query($qry);
                    $addon->log_message(__METHOD__, 'Insert subscription [customer] - {p1}', 3, $res ? 'success' : 'failed');                          

                    $rec_id_sub = $this->wpdb->insert_id;
                    $interval_desc = $customer->sub_interval;                  
                    $webhook_url = $addon->webhook_url . 'sub/' . $rec_id_sub;
                    $startDate = date('Y-m-d', strtotime("+" . $interval_desc, strtotime(date('Y-m-d'))));
                   
                    $addon->log_message(__METHOD__, 'Create subscription started   [customer_id = {p1}]', 2, $customer->customer_id);              
                                        
                    $subscription = $mollie->customers_subscriptions->withParentId($customer->customer_id)->create(array(
                        "amount"      => $customer->sub_amount,
                        "interval"    => $interval_desc,
                        "description" => $customer->sub_description,
                        "webhookUrl"  => $webhook_url,
                        "startDate"   => $startDate,
                    ));
                    
                    $addon->log_message(__METHOD__, 'Create subscription completed [customer_id = {p1}]', 2, $customer->customer_id);              
                    
                    $sql = "UPDATE " . GFM_TABLE_DONATIONS . " SET subscription_id = %s WHERE id = %d";
                    $qry = $this->wpdb->prepare($sql, $subscription->id, $donation->id);
                    
                    $res = $this->wpdb->query($qry);
                    $addon->log_message(__METHOD__, 'Update donation [subscription] - {p1}', 3, $res ? 'success' : 'failed');                          

                    $sql = "UPDATE " . GFM_TABLE_SUBSCRIPTIONS . " SET subscription_id = %s, sub_mode = %s, sub_amount = %s, sub_times = %s, sub_interval = %s, sub_description = %s, sub_method = %s, sub_status = %s WHERE id = %d";
                    $qry = $this->wpdb->prepare($sql, 
                                $subscription->id,
                                $subscription->mode,
                                $subscription->amount,
                                $subscription->times,
                                $subscription->interval,
                                $subscription->description,
                                $subscription->method,
                                $subscription->status,
                                $rec_id_sub
                     );
                    
                    $res = $this->wpdb->query($qry);
                    $addon->log_message(__METHOD__, 'Update subscription [subscription] - {p1}', 3, $res ? 'success' : 'failed');                          

                }
                
                // Report Payment [One-time or First] successfully processed --> Mollie
                $addon->log_message(__METHOD__, 'Webhook completed [type = {p1}]', 2, $type);            
                return 'OK, ' . $payment_id;
            }
            else
            {
                //**************************** Subscription Payment (periodic non-first) ****************************//
                $addon->log_message(__METHOD__, 'Webhook started   [type = subscription]', 2);              
                
                // Get Subscription record
                $rec_id = esc_sql($query->query_vars['sub']);
                $sql = "SELECT * FROM " . GFM_TABLE_SUBSCRIPTIONS . " WHERE id = '" . $rec_id . "'";
                $sub = $this->wpdb->get_row($sql);             
                if (!$sub->id)
                    return $this->set_error(404, 'Subscription not found', $addon);
              
                // Get First Payment record for Subscription
                $sub_id = esc_sql($sub->subscription_id);
                $sql = "SELECT * FROM " . GFM_TABLE_DONATIONS . " WHERE subscription_id = '" . $sub_id . "'";
                $firstDonation = $this->wpdb->get_row($sql);
                if (!$firstDonation->id)
                    return $this->set_error(404, 'First Payment not found', $addon);

                $payment_id = $_POST['id'];                                     // Payment ID of new (periodic) Payment
                if (!$payment_id)
                    return $this->set_error(404, 'No payment id', $addon);
                
                $addon->log_message(__METHOD__, 'Get payment started   [type = subscription / payment_id = {p1}]', 3, $payment_id);
                
                $payment = $mollie->payments->get($payment_id);
                
                $addon->log_message(__METHOD__, 'Get payment completed [type = subscription / payment_id = {p1}]', 3, $payment_id);

                $sql = "SELECT * FROM " . GFM_TABLE_DONATIONS . " WHERE payment_id = '" . esc_sql($payment->id) . "'";
                $donation = $this->wpdb->get_row($sql);
                if (!$donation->id)
                {
                    // Create new Payment
                    $donation_id = uniqid(rand(1, 99));
                    
                    $sql = "INSERT INTO " . GFM_TABLE_DONATIONS .
                           "(`time`, payment_id, customer_id, subscription_id, donation_id, status, amount, name, email, project, company, address, zipcode, city, country, message, phone, tag, custom, payment_method, payment_mode)" .
                           "VALUES ( %s, %s, %s, %s, %s, %s, %f, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )";
                    $qry = $this->wpdb->prepare($sql,
                                //date('Y-m-d H:i:s'),
                                current_time('mysql', false),
                                $payment->id,
                                $payment->customerId,
                                $payment->subscriptionId,
                                $donation_id,
                                $payment->status,
                                $payment->amount,
                                $firstDonation->name,
                                $firstDonation->email,
                                $firstDonation->project,
                                $firstDonation->company,
                                $firstDonation->address,
                                $firstDonation->zipcode,
                                $firstDonation->city,
                                $firstDonation->country,
                                $firstDonation->message,
                                $firstDonation->phone,
                                $firstDonation->tag,
                                $firstDonation->custom,
                                $payment->method,
                                $payment->mode
                            );
                    
                    $res = $this->wpdb->query($qry);
                    $addon->log_message(__METHOD__, 'Insert donation [periodic] - {p1}', 3, $res ? 'success' : 'failed');                          
                }
                else
                {
                    // Update existing payment
                    $sql = "UPDATE " . GFM_TABLE_DONATIONS . " SET status = %s, payment_method = %s, payment_mode = %s WHERE payment_id = %s";
                    $qry = $this->wpdb->prepare($sql,
                                $payment->status,
                                $payment->method,
                                $payment->mode,
                                $payment->id                            
                            );
                    
                    $res = $this->wpdb->query($qry);
                    $addon->log_message(__METHOD__, 'Update donation [periodic] - {p1}', 3, $res ? 'success' : 'failed');                          
               }

                // Report subscription donation successfully processed --> Mollie
                $addon->log_message(__METHOD__, 'Webhook completed [type = subscription]', 2);            
                return 'OK, ' . $payment_id;
            }

        } 
        catch (Mollie_API_Exception $e) 
        {
            $msg = "API call failed: " . $e->getMessage();
            $addon->log_message(__METHOD__, $msg, 1);
          
            status_header(404);
            return $msg;
        }
    }
    
    protected function set_error($code, $msg, $addon)
    {
        $addon->log_message(__METHOD__, $msg, 1);              

        status_header($code);
        return $msg;
    }
}