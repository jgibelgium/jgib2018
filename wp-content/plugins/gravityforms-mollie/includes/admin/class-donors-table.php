<?php
class GFM_Donors_Table extends WP_List_Table 
{
    public function prepare_items() 
    {
        global $wpdb;
        
        $columns = $this->get_columns();       
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $orderby = (! empty($_GET['orderby'])) ? $_GET['orderby'] : 'customer_name';
        $order   = (! empty($_GET['order']  )) ? $_GET['order']   : 'asc';

        $donors = $wpdb->get_results("SELECT * FROM " . GFM_TABLE_DONORS. " ORDER BY " . $orderby . " "  . $order, ARRAY_A);

        $per_page = 25;
        $current_page = $this->get_pagenum();
        $total_items = count($donors);

        $d = array_slice($donors,(($current_page-1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ) );
        
        $this->items = $d;
    }

    public function get_columns()
    {
        $columns = array();
        $columns['customer_name'] = __('Name', GFM_TXT_DOMAIN);
        $columns['customer_email'] = __('Email address', GFM_TXT_DOMAIN);
        $columns['customer_id'] = __('Customer ID', GFM_TXT_DOMAIN);

        return $columns;
    }

    public function get_sortable_columns()
    {
        $columns = $this->get_columns();
        $sortable_columns = array();
        
        foreach ($columns as $name=>$value) {
            $sortable_columns[$name] = array($name, false); 
        }

        return $sortable_columns; 
    }

    public function column_default($item, $column_name) 
    {
        switch( $column_name ) {
            default:
                return $item[$column_name];
        }
    }

    public function display_tablenav($which) 
    {
        ?>
        <div class="tablenav <?php echo esc_attr($which); ?>">
            <?php $this->pagination($which);?>
            <br class="clear" />
        </div>
        <?php
    }
}