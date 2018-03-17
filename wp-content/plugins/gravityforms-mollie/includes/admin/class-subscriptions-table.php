<?php
class GFM_Subscriptions_Table extends WP_List_Table 
{
    public function prepare_items() 
    {
        global $wpdb;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $orderby = (! empty($_GET['orderby'])) ? $_GET['orderby'] : 'created_at';
        $order   = (! empty($_GET['order']  )) ? $_GET['order']   : 'asc';

        $qry =  "SELECT s.*, d.customer_name FROM " . GFM_TABLE_SUBSCRIPTIONS . " s JOIN " . GFM_TABLE_DONORS . " d ON s.customer_id = d.id ";
        $qry .= "ORDER BY " .  $orderby . " " . $order;
         
        $subscriptions = $wpdb->get_results($qry, ARRAY_A);

        $per_page = 25;
        $current_page = $this->get_pagenum();
        $total_items = count($subscriptions);

        $d = array_slice($subscriptions,(($current_page-1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
        $this->items = $d;
    }
    
    public function get_columns()
    {
        $columns = array();
        $columns['created_at']      = __('Date/time', GFM_TXT_DOMAIN);
        $columns['customer_name']   = __('Name', GFM_TXT_DOMAIN);
        $columns['sub_amount']      = __('Amount', GFM_TXT_DOMAIN);
        $columns['sub_interval']    = __('Interval', GFM_TXT_DOMAIN);
        $columns['sub_status']      = __('Status', GFM_TXT_DOMAIN);
        $columns['subscription_id'] = __('Subscription ID', GFM_TXT_DOMAIN);

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

    public function column_subscription_id($item)
    {
        if ($item['sub_status'] != 'active')
            return $item['subscription_id'];

        $url_view = '?page=' . GFM_PAGE_DONATIONS . '&subscription=' . $item['subscription_id'];
        $url_cancel = wp_nonce_url('?page=' . GFM_PAGE_SUBSCRIPTIONS . '&action=cancel&subscription=' . $item['subscription_id'] . '&customer=' . $item['customer_id'], 'cancel-subscription_' . $item['subscription_id']);
        
        $actions = array(
            'view'    => sprintf('<a href="%s">' . esc_html__('View', GFM_TXT_DOMAIN) . '</a>', $url_view),
            'cancel'  => sprintf('<a href="%s" style="color:#a00;" onclick="return confirm(\'' . __('Are you sure?', GFM_TXT_DOMAIN) . '\')">' . esc_html__('Cancel', GFM_TXT_DOMAIN) . '</a>', $url_cancel),
        );

        //Return the title contents
        return sprintf('%1$s %2$s',
            $item['subscription_id'],
            $this->row_actions($actions)
        );
    }

    public function column_customer_name($item)
    {
        global $wpdb;
        
        $customer = $wpdb->get_row("SELECT * FROM " . GFM_TABLE_DONORS . " WHERE id = '" . esc_sql($item['customer_id']) . "'");
        
        return $customer->customer_name;
    }

    public function column_default($item, $column_name) 
    {
        switch( $column_name ) 
        {
            case 'created_at':
                return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item[ $column_name ]));
                break;
            case 'sub_amount':
                return '&euro; ' . number_format($item['sub_amount'], 2, ',', '');
                break;
            case 'sub_interval':
                return $this->getInterval($item['sub_interval']);
                break;
            case 'sub_status':
                return $this->getStatus($item['sub_status']);
                break;
            default:
                return $item[ $column_name ];
        }
    }

    public function getInterval($interval) 
    {
        switch ($interval) {
            case '1 month':
                $return = __('Monthly', GFM_TXT_DOMAIN);
                break;
            case '3 months':
                $return = __('Quarterly', GFM_TXT_DOMAIN);
                break;
            case '12 months':
                $return = __('Annually', GFM_TXT_DOMAIN);
                break;
            default:
                $return = $interval;
        }

        return $return;
    }

    public function getStatus($status) 
    {
        switch ($status) {
            case 'pending':
                $return = __('Pending', GFM_TXT_DOMAIN);
                break;
            case 'active':
                $return = __('Active', GFM_TXT_DOMAIN);
                break;
            case 'cancelled':
                $return = __('Cancelled', GFM_TXT_DOMAIN);
                break;
            case 'suspended':
                $return = __('Suspended', GFM_TXT_DOMAIN);
                break;
            case 'completed':
                $return = __('Completed', GFM_TXT_DOMAIN);
                break;
        }

        return $return;
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