<?php
class MDB_Customers_Table extends MDB_Items_Table
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
                
            $customers = $mollie->customers->all($offset, $page_length);
            $data = [];            
            foreach ($customers as $customer)
            {
                $data[] = array(
                    'customer_id'       => $customer->id,
                    'customer_name'     => $customer->name,
                    'customer_email'    => $customer->email,
                    'customer_mode'     => $customer->mode,
                    'customer_created'  => isset($customer->createdDatetime) ? date('Y-m-d H:i', strtotime($customer->createdDatetime)) : '',
                );
            }

            if (! empty($_POST['s'])) 
            {
                $data_found = [];
                $search = $_POST['s'];
                foreach ($data as $row) 
                {
                    if   (stripos($row['customer_name'], $search) !== false || 
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
            
            $num_pages = ceil($customers->totalCount / MDB_ITEMS_PER_PAGE);           
            $this->set_pagination_args( array(
                'total_items' => $customers->totalCount,
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
        $orderby = (! empty($_GET['orderby'])) ? $_GET['orderby'] : 'customer_name';
        $order   = (! empty($_GET['order']  )) ? $_GET['order']   : 'asc';
        
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ($order === 'asc') ? $result : -$result;
    }    

    public function get_columns()
    {
        $columns = array(
            'customer_id'      => 'ID',
            'customer_name'    => 'Name',
            'customer_email'   => 'Email',
            'customer_mode'    => 'Mode',
            'customer_created' => 'Created',
        );

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array();

        $sortable_columns['customer_id']    = array('customer_id', false);
        $sortable_columns['customer_name']  = array('customer_name', false);
        $sortable_columns['customer_email'] = array('customer_email', false);

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