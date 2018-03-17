<?php
class MDB_Mandates_Table extends MDB_Items_Table 
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

            // Note: no paging done for mandates (no 'list mandates' API available...)
            // --> all mandates are retrieved (assumption is that the number of mandates is limited)
            $data = [];
            $offset = 0;
            while (true)
            {
                $customers = $mollie->customers->all($offset,  MDB_MAX_ITEMS_REQUEST);
                if (count($customers) == 0)         // done
                    break;
                
                foreach ($customers as $customer)
                {
                    $mandates = $mollie->customers_mandates->withParentId($customer->id)->all();            
                    foreach ($mandates as $mandate)
                    {
                        $data[] = array(
                            'mandate_id'            => $mandate->id,
                            'customer_name'         => $customer->name,
                            'customer_id'           => $customer->id,
                            'mandate_status'        => $mandate->status,
                            'mandate_method'        => $mandate->method,
                            'mandate_created'       => isset($mandate->createdDatetime) ? date('Y-m-d H:i', strtotime($mandate->createdDatetime)) : '',
                            'mandate_cnsmr_name'    => isset($mandate->details->consumerName)    ? $mandate->details->consumerName : '',
                            'mandate_cnsmr_accnt'   => isset($mandate->details->consumerAccount) ? $mandate->details->consumerAccount : '',
                            'mandate_cnsmr_bic'     => isset($mandate->details->consumerBic)     ? $mandate->details->consumerBic : '',
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
                    if   (stripos($row['mandate_id'], $search)    !== false || 
                          stripos($row['customer_name'], $search) !== false || 
                          stripos($row['customer_id'], $search)   !== false ) {
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
            'mandate_id'          => 'Mandate ID',
            'customer_name'       => 'Customer name',
            'customer_id'         => 'Customer ID',
            'mandate_status'      => 'Status',
            'mandate_method'      => 'Method',
            'mandate_created'     => 'Created',
            'mandate_cnsmr_name'  => 'Consumer Name',
            'mandate_cnsmr_accnt' => 'Consumer Account',
            'mandate_cnsmr_bic'   => 'Consumer BIC',
        );

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array();

        $sortable_columns['mandate_id']     = array('mandate_id', false);
        $sortable_columns['customer_name']  = array('customer_name', false);
        $sortable_columns['customer_id']    = array('customer_id', false);
        $sortable_columns['mandate_status'] = array('mandate_status', false);

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
}