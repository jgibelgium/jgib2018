<?php
class MDB_Subscriptions_Table extends MDB_Items_Table 
{
    public function prepare_items($page = 1, $page_length = MDB_ITEMS_PER_PAGE)  
    {
        $this->items = array();                         // clear data
        
        if ($page > 1)                                  // to break export loop
            return;
        
        try 
        {
            $mollie = new Mollie_API_Client;
            if (get_option('mollie_api_key')) {
                $mollie->setApiKey(get_option('mollie_api_key'));
            } else {
                echo '<div class="error notice"><p>' . esc_html__('Mollie API key not set') . '</p></div>';
                return;
            }

            // Note: no paging done for subscriptions (no 'list subscriptions' API available...)
            // --> all subscriptions are retrieved (assumption is that the number of subscriptions is limited)
            $data = [];
            $offset = 0;
            while (true)
            {
                $customers = $mollie->customers->all($offset, MDB_MAX_ITEMS_REQUEST);
                if (count($customers) == 0)         // done
                    break;
                
                foreach ($customers as $customer)
                {
                    $subscriptions = $mollie->customers_subscriptions->withParentId($customer->id)->all();            
                    foreach ($subscriptions as $subscription)
                    {
                        $data[] = array(
                            'subscription_id'       => $subscription->id,
                            'customer_name'         => $customer->name,
                            'customer_id'           => $customer->id,
                            'subscription_status'   => $subscription->status,
                            'subscription_amount'   => $subscription->amount,
                            'subscription_method'   => $subscription->method,
                            'subscription_interval' => $subscription->interval,
                            'subscription_times'    => $subscription->times,
                            'subscription_created'  => isset($subscription->createdDatetime) ? date('Y-m-d H:i', strtotime($subscription->createdDatetime)) : '',
                        );
                    }
                }
                
                $offset += MDB_MAX_ITEMS_REQUEST;
            }

            if (! empty($_POST['s'])) 
            {
                $data_found = [];
                $search = $_POST['s'];
                foreach ($data as $row) 
                {
                    if   (stripos($row['subscription_id'], $search)  !== false || 
                          stripos($row['customer_name'], $search)    !== false || 
                          stripos($row['customer_id'], $search)      !== false ) {
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
        $orderby = (! empty($_GET['orderby'])) ? $_GET['orderby'] : 'customer_name';
        $order   = (! empty($_GET['order']  )) ? $_GET['order']   : 'asc';
        
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ($order === 'asc') ? $result : -$result;
    }    

    public function get_columns()
    {
        $columns = array(
            'subscription_id'       => 'Subscr ID',
            'customer_name'         => 'Customer name',
            'customer_id'           => 'Customer ID',
            'subscription_status'   => 'Status',
            'subscription_amount'   => 'Amount',
            'subscription_method'   => 'Method',
            'subscription_interval' => 'Interval',
            'subscription_times'    => 'Times',
            'subscription_created'  => 'Created',
        );

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array();

        $sortable_columns['subscription_id']      = array('subscription_id', false);
        $sortable_columns['customer_name']        = array('customer_name', false);
        $sortable_columns['customer_id']          = array('customer_id', false);
        $sortable_columns['subscription_status']  = array('subscription_status', false);
        $sortable_columns['subscription_created'] = array('subscription_created', false);

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

    public function column_subscription_id($item)
    {        
        $prm_view   = '?page=' . MDB_PAGE_SUBSCRIPTION . '&action=view&subscription_id='   . $item['subscription_id'] . '&customer_id=' . $item['customer_id'];
        $prm_cancel = '?page=' . MDB_PAGE_SUBSCRIPTION . '&action=cancel&subscription_id=' . $item['subscription_id'] . '&customer_id=' . $item['customer_id'];

        $actions = array();
        // status = active / cancelled / pending / suspended / completed
            $actions['view']   = sprintf('<a href="%s">View</a>', $prm_view);
        if ($item['subscription_status'] !== 'cancelled') {
            $actions['cancel'] = sprintf('<a href="%s" style="color:#a00;" onclick="return confirm(\'' . 'Are you sure?' . '\')">' . 'cancel' . '</a>', $prm_cancel);
        }

        $ret = sprintf('%1$s %2$s', $item['subscription_id'], $this->row_actions($actions));
        return $ret;
    }
}