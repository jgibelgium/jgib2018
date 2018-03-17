<?php
class GFM_Donations_Table extends WP_List_Table 
{
    public function prepare_items() 
    {
        global $wpdb;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $where = $this->set_filter($columns);
        
        $orderby = (! empty($_GET['orderby'])) ? $_GET['orderby'] : 'time';
        $order   = (! empty($_GET['order']  )) ? $_GET['order']   : 'asc';
        
        $qry = 'SELECT d.*, s.sub_interval FROM ' . GFM_TABLE_DONATIONS . ' d LEFT JOIN ' . GFM_TABLE_SUBSCRIPTIONS . ' s ON d.subscription_id = s.subscription_id';
        $donations = $wpdb->get_results($qry . $where . " ORDER BY " .  $orderby . " " . $order, ARRAY_A);

        $per_page = 25;
        $current_page = $this->get_pagenum();
        $total_items = count($donations);

        $d = array_slice($donations,(($current_page-1) * $per_page), $per_page);

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page )
        ) );
        
        $this->items = $d;
    }
    
    public function get_columns()
    {
        $addon = GFMollieAddOn::get_instance();
        $settings = $addon->plugin_settings;
        
        $sections = $addon->plugin_settings_fields();
        $defs = $sections[0]['fields'][4]['choices'];       // 'donation-columns' definitions
        
        $columns = array();
        foreach ($settings as $name=>$value)
        {
            if (strpos($name, 'col_') !== false)            // find column name settings
            {
                if ($value == 1) {                          // column included
                    $col_name = substr($name, 4);           // strip off 'col_'
                    foreach($defs as $def) {                // find label for setting
                        if ($def['name'] == $name){
                            $columns[$col_name] = $def['label'];
                            break;
                        }
                    }
                }               
            }
        }

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

    public function column_donation_id($item)
    {        
        $url_refund = wp_nonce_url('?page=' . GFM_PAGE_DONATIONS . '&action=refund&payment=' . $item['payment_id'], 'refund-donation_' . $item['payment_id']);
        $url_view = '?page=' . GFM_PAGE_DONATION . '&id=' . $item['id'];

        $actions = array();
        $actions['view'] = sprintf('<a href="%s">' . esc_html__('View', GFM_TXT_DOMAIN) . '</a>', $url_view);

        if ($item['status'] == 'paid' && $item['amount'] > 0.30)
            $actions['refund'] = sprintf('<a href="%s" style="color:#a00;" onclick="return confirm(\'' . __('Are you sure?', GFM_TXT_DOMAIN) . '\')">' . esc_html__('Refund', GFM_TXT_DOMAIN) . '</a>', $url_refund);

        //Return the title contents
        return sprintf('%1$s %2$s',
            $item['donation_id'],
            $this->row_actions($actions)
        );
    }

    public function column_default($item, $column_name) 
    {
        switch($column_name) 
        {
            case 'amount':
                $img = $item['payment_method'] ? '<img valign="top" src="https://www.mollie.com/images/payscreen/methods/' . $item['payment_method'] . '.png" width="18"> ' : '';
                $nmb = number_format($item[$column_name], 2, ',', '');
                $typ = isset($item['customer_id']) && $item['customer_id'] ? '<small>(recurring)</small>' : '';                
                return $img . '&euro; ' . $nmb . ' ' . $typ;
            case 'time':
                return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item[$column_name]));
            case 'status':
                return $this->statusName($item[$column_name]) . ($item['payment_mode'] == 'test' ? ' <small>(' . $item['payment_mode'] . ')</small>' : '');
            default:
                return $item[$column_name];            
        }
    }
    
    public function set_filter($columns)
    {        
        $where = '';
        
        if (isset($_GET['subscription']))
            $where .= ' WHERE subscription_id="' . esc_sql($_GET['subscription']) . '"';

        if (isset($_GET['search'])) 
        {
            $where .= ($where ? ' AND (' : ' WHERE (');
            
            $srch = esc_sql($_GET['search']);
            if (substr($srch, 0, 4) == 'date')      // date(yyyy) OR date(yyyy-mm)
            {
                $year = substr($srch, 5, 4);
                $where .= ' YEAR(time)=' . $year;
                if (strlen($srch) == 13) {
                    $month = substr($srch, 10, 2);
                    $where .= ' AND MONTH(time)=' . $month;
                }
                
                $where .= ')';
            }
            else
            {                        
                foreach ($columns as $col=>$hdr) {
                    $where .= ($col . ' LIKE "%'  . $srch . '%" OR ');
                }

                $where = substr($where, 0, strlen($where) - 3) . ')';
            }
        }
        
        return $where;       
    }
    
    public function statusName($status) 
    {
        switch( $status ) 
        {
            case 'open':
                return __('Open', GFM_TXT_DOMAIN);
            case 'cancelled':
                return __('Cancelled', GFM_TXT_DOMAIN);
            case 'pending':
                return __('Pending', GFM_TXT_DOMAIN);
            case 'expired':
                return __('Expired', GFM_TXT_DOMAIN);
            case 'paid':
                return __('Paid', GFM_TXT_DOMAIN);
            case 'paidout':
                return __('Paid out', GFM_TXT_DOMAIN);
            case 'refunded':
                return __('Refunded', GFM_TXT_DOMAIN);
            case 'charged_back':
                return __('Charged back', GFM_TXT_DOMAIN);
            default:
                return $status;
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