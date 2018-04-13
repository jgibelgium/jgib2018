<?php
class MDB_Payments_Table extends MDB_Items_Table
{
    public function prepare_items($page = 1, $page_length = MDB_ITEMS_PER_PAGE) 
    {
        $this->items = array();                         // clear data
        
        try 
        {
            $mollie = new Mollie_API_Client;
            if (get_option('mollie_api_key')) {
                $mollie->setApiKey(get_option('mollie_api_key'));
            } else {
                echo '<div class="error notice"><p>' . esc_html__('Mollie API key not set') . '</p></div>';
                return;
            }
            
            $offset = ($page - 1) * $page_length;
            
            $payments = $mollie->payments->all($offset, $page_length);
            $data = [];            
            foreach ($payments as $payment)
            {
                if ($payment->method == 'creditcard') {
                    $cust_name = isset($payment->details->cardHolder)   ? $payment->details->cardHolder : '';
                } else {
                    $cust_name = isset($payment->details->consumerName) ? $payment->details->consumerName : '';                    
                }
                
                $data = array(
                    'payment_id'                => $payment->id,
                    'payment_amount'            => $payment->amount,
                    'payment_method'            => $payment->method,
                    'payment_cust_name'         => $cust_name,
                    'payment_status'            => $payment->status,
                    'payment_createdDatetime'   => isset($payment->createdDatetime) ? date('Y-m-d H:i', strtotime($payment->createdDatetime)) : '',
                    'payment_paidDatetime'      => isset($payment->paidDatetime)    ? date('Y-m-d H:i', strtotime($payment->paidDatetime)) : '',
                    'payment_mode'              => $payment->mode,
                    /*
                    'payment_customerId'        => $payment->customerId,
                    'payment_subscriptionId'    => $payment->subscriptionId,
                    'payment_mandateId'         => $payment->mandateId,
                    'payment_profileId'         => $payment->profileId,
                    'payment_cancelledDatetime' => $payment->cancelledDatetime,
                    'payment_expiredDatetime'   => $payment->xpiredDatetime,
                    'payment_expiryPeriod'      => $payment->expiryPeriod,
                    //'payment_metadata'          => $payment->metadata,
                    'payment_description'       => $payment->description,
                    'payment_recurringType'     => $payment->recurringType,
                    'payment_amountRefunded'    => $payment->amountRefunded,
                    'payment_amountRemaining'   => $payment->amountRemaining,
                    'payment_webhookUrl'        => $payment->links->webhookUrl,
                    'payment_redirectUrl'       => $payment->links->redirectUrl,
                    'payment_paymentUrl'        => $payment->links->paymentUrl,
                     */
                );
            }

            if (! empty($_POST['s'])) 
            {
                $data_found = array();
                $search = $_POST['s'];
                foreach ($data as $row) 
                {
                    if   (stripos($row['payment_id'], $search)          !== false || 
                          stripos($row['payment_amount'], $search)      !== false ||
                          stripos($row['payment_method'], $search)      !== false ||
                          stripos($row['payment_cust_name'], $search)   !== false ||
                          stripos($row['payment_status'], $search)      !== false ) {
                        $data_found[] = $row;
                    }
                }
                $data = $data_found;           
            }

            $columns = $this->get_columns();       
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);
            
            usort($data, array(&$this, 'usort_reorder'));           // sort rows
            
            $num_pages = ceil($payments->totalCount / MDB_ITEMS_PER_PAGE);           
            $this->set_pagination_args( array(
                'total_items' => $payments->totalCount,
                'per_page'    => MDB_ITEMS_PER_PAGE,
                'total_pages' => $num_pages
            ) );
                       
            $this->items = $data;
        } 
        catch (Mollie_API_Exception $e) 
        {
            $msg = "API call failed: " . htmlspecialchars($e->getMessage());            
        }
    }
    
    // compare two row values (for sorting)
    public function usort_reorder($a, $b) 
    {
        $orderby = (! empty($_GET['orderby'])) ? $_GET['orderby'] : 'payment_id';
        $order   = (! empty($_GET['order']  )) ? $_GET['order']   : 'asc';
        
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ($order === 'asc') ? $result : -$result;
    }    

    public function get_columns()
    {
        $columns = array(
            'payment_id'                => 'ID',
            'payment_amount'            => 'Amount',
            'payment_method'            => 'Method',
            'payment_cust_name'         => 'Customer Name',
            'payment_status'            => 'Status',
            'payment_createdDatetime'   => 'Created',
            'payment_paidDatetime'      => 'Paid',           
            'payment_mode'              => 'Mode',
            /*
            'payment_customerId'        => 'Customer ID',
            'payment_subscriptionId'    => 'Subscription ID',
            'payment_mandateId'         => 'Mandate ID',
            'payment_profileId'         => 'Profile ID',
            'payment_cancelledDatetime' => 'Cancelled',
            'payment_expiredDatetime'   => 'Expired',
            'payment_expiryPeriod'      => 'Expiry Period',
            //'payment_metadata'          => 'Metadata',
            'payment_description'       => 'Description',
            'payment_recurringType'     => 'Recurring Type',
            'payment_amountRefunded'    => 'Amount Refunded',
            'payment_amountRemaining'   => 'Amount Remaining',
            'payment_webhookUrl'        => 'Webhook URL',
            'payment_redirectUrl'       => 'Redirect URL',
            'payment_paymentUrl'        => 'Payment URL',
            */
        );

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array();

        $sortable_columns['payment_id']              = array('payment_id', false);        
        $sortable_columns['payment_amount']          = array('payment_amount', false);        
        $sortable_columns['payment_method']          = array('payment_method', false);
        $sortable_columns['payment_cust_name']       = array('payment_cust_name', false);
        $sortable_columns['payment_status']          = array('payment_status', false);
        $sortable_columns['payment_createdDatetime'] = array('payment_createdDatetime', false);
        $sortable_columns['payment_paidDatetime']    = array('payment_paidDatetime', false);

        return $sortable_columns; 
    }

    public function column_default($item, $column_name) 
    {
        switch($column_name) 
        {
            default:
                return $item[$column_name];
        }
    }

    public function column_payment_id($item)
    {        
        $prm_view   = '?page=' . MDB_PAGE_PAYMENT . '&action=view&payment_id='   . $item['payment_id'];
        $prm_refund = '?page=' . MDB_PAGE_PAYMENT . '&action=refund&payment_id=' . $item['payment_id'];

        $actions = array();
        // status = open / cancelled / expired / failed / pending / paid / paidout / refunded / charged_back
        $actions['view']   = sprintf('<a href="%s">View</a>', $prm_view);
        if ($item['payment_status'] !== 'cancelled' &&
            $item['payment_status'] !== 'failed') {         // more states?
            $actions['refund'] = sprintf('<a href="%s" style="color:#a00;" onclick="return confirm(\'' . 'Are you sure?' . '\')">' . 'Refund' . '</a>', $prm_refund);
        }

        $ret = sprintf('%1$s %2$s', $item['payment_id'], $this->row_actions($actions));
        return $ret;
    }
}