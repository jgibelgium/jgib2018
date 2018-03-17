<?php
class GFM_Admin 
{
    private $wpdb;
    private $addon;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;        
        $this->addon = GFMollieAddOn::get_instance();

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_gfm_export', array($this, 'export_donations'));

        if (!get_option('permalink_structure'))
            add_action('admin_notices', array($this, 'admin_notice__warning'));
    }
    
    public function add_admin_menu() 
    {
        $rights_donations        = $this->addon->plugin_settings['rights-donations'];
        $rights_subscriptions    = $this->addon->plugin_settings['rights-subscriptions'];
        
        add_menu_page(
            __('Donations', GFM_TXT_DOMAIN),            // page title
            __('GFM Donations', GFM_TXT_DOMAIN),        // menu item
            $rights_donations,                          // donation rights required
            GFM_PAGE_DONATIONS,                         // menu slug
            array($this, 'show_donation_list'),         // page function
            'dashicons-money'
        );

        add_submenu_page(
            GFM_PAGE_DONATIONS,                         // parent menu slug
            __('Subscriptions', GFM_TXT_DOMAIN) . ' | ' . __('GFM Donations', GFM_TXT_DOMAIN),  // page title
            __('GFM Subscriptions', GFM_TXT_DOMAIN),
            $rights_subscriptions,                      // subscription rights required
            GFM_PAGE_SUBSCRIPTIONS,                     // menu item
            array($this, 'show_subscription_list')      // page function
        );
        
        add_submenu_page(
            GFM_PAGE_DONATIONS,                         // parent menu slug
            __('Donors', GFM_TXT_DOMAIN) . ' | ' . __('GFM Donations', GFM_TXT_DOMAIN),         // page title
            __('GFM Donors', GFM_TXT_DOMAIN),
            $rights_subscriptions,                      // subscription rights required
            GFM_PAGE_DONORS,                            // menu item
            array($this, 'show_donor_list')             // page function
        );
        
        // Hidden item for single Donation page
        add_submenu_page(
            null,                                       // parent menu slug
            __('Donation', GFM_TXT_DOMAIN),             // page title
            __('GFM Donation', GFM_TXT_DOMAIN),         
            $rights_subscriptions,                      // subscription rights required
            GFM_PAGE_DONATION,                          // menu item
            array($this, 'show_donation')               // page function
        );
    }
    
    public function show_donation_list() 
    {
        $addon = $this->addon;
        try 
        {
            if (isset($_GET['action']) && $_GET['action'] == 'refund' && isset($_GET['payment']) && check_admin_referer('refund-donation_' . $_GET['payment']))
            {
                $mollie = $addon->init_mollie();                                     // initialize Mollie API
                if (!isset($mollie))
                    return;

                
                $payment_id = $_GET['payment'];
                
                $addon->log_message(__METHOD__, 'Get payment started   [payment_id = {p1}]', 2, $payment_id);              

                $payment = $mollie->payments->get($payment_id);
                
                $addon->log_message(__METHOD__, 'Get payment completed [payment_id = {p1}]', 2, $payment_id);                          
                
                if ($payment->canBeRefunded())
                {
                    $addon->log_message(__METHOD__, 'Refund payment started   [payment_id = {p1}]', 3, $payment_id);                                
                    
                    $refund = $mollie->payments->refund($payment);                         
                    
                    $addon->log_message(__METHOD__, 'Refund payment completed [payment_id = {p1}]', 3, $payment_id);                                
                    
                    $msg = $refund->status == 'refunded' ? 'refund-ok' : 'refund-failed';
                    wp_redirect('?page=' . $_REQUEST['page'] . '&msg=' . $msg);
                }
                else
                    wp_redirect('?page=' . $_REQUEST['page'] . '&msg=refund-nok');
            }
            
        } catch (Mollie_API_Exception $e) {
            $msg = "API call failed: " . htmlspecialchars($e->getMessage());            
            $gfm_msg =  "<div class=\"error notice\"><p>" . $msg . "</p></div>";        // display message          
            $addon->log_message(__METHOD__, $msg, 1);                             // log message
        }

        if (isset($_GET['msg']))
        {
            switch ($_GET['msg'])
            {
                case 'refund-ok':
                    $gfm_msg = '<div class="updated notice"><p>' . esc_html__('The donation is successfully refunded to the donator', GFM_TXT_DOMAIN) . '</p></div>';
                    break;
                case 'refund-failed':
                    $gfm_msg = '<div class="updated notice"><p>' . esc_html__('The donation could not be refunded to the donator', GFM_TXT_DOMAIN) . '</p></div>';
                    break;
                case 'refund-nok':
                    $gfm_msg = '<div class="error notice"><p>' . esc_html__('The donation is not refundable', GFM_TXT_DOMAIN) . '</p></div>';
                    break;
                case 'truncate-ok':
                    $gfm_msg = '<div class="updated notice"><p>' . esc_html__('The donations have been successfully removed from the database', GFM_TXT_DOMAIN) . '</p></div>';
                    break;
            }
        }
        
        $gfmTable = new GFM_Donations_Table();
        $gfmTable->prepare_items();
        ?>

        <div class="wrap">
            <h2><?php esc_html_e('Donations', GFM_TXT_DOMAIN) ?></h2>

            <?php echo isset($gfm_msg) ? $gfm_msg : '';?>

            <form action="admin.php">
                <input type="hidden" name="page" value="<?php echo GFM_PAGE_DONATIONS;?>">

                <input type="text" name="search" placeholder="<?php esc_html_e('Search') ?>">
                <input type="submit" value="<?php esc_html_e('Search') ?>">
            </form>
            
            <?php $subscr = isset($_GET['subscription']) ? '&subscription=' . $_GET['subscription'] : ''; ?>
            <?php $search = isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>
            
            <span style="float: left;">[Period: date(yyyy) / date(yyyy-mm)]</span>
            <a style="float: right;" href="<?php echo admin_url('admin-post.php?action=gfm_export' . $subscr . $search);?>"><?php esc_html_e('Export', GFM_TXT_DOMAIN) ?></a>
            
            <?php $gfmTable->display();?>
        </div>
    <?php
    }
    
    public function show_subscription_list() 
    {
        $addon = $this->addon;
        try 
        {
            if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['subscription']) && check_admin_referer('cancel-subscription_' . $_GET['subscription']))
            {
                $mollie = $addon->init_mollie();                                     // initialize Mollie API
                if (!isset($mollie))
                    return;

                $customer = $this->wpdb->get_row("SELECT * FROM " . GFM_TABLE_DONORS . " WHERE id = '" . esc_sql($_GET['customer']) . "'");
                
                $subscription_id = $_GET['subscription'];
                
                $addon->log_message(__METHOD__, 'Cancel subscription started   [subscription_id = {p1}]', 2, $subscription_id);     
                
                //update db also if subscription has been cancelled outside of GFM
                $subscriptions = $mollie->customers_subscriptions->withParentId($customer->customer_id)->all();               
                $subscription = $subscriptions[0];
                if ($subscription->status !== 'cancelled')  {                                 
                    $subscription = $mollie->customers_subscriptions->withParentId($customer->customer_id)->cancel($subscription_id);
                }
            
                $addon->log_message(__METHOD__, 'Cancel subscription completed [subscription_id = {p1}]', 2, $subscription_id);                                

                if ($subscription->status == 'cancelled')
                {                                      
                    $sql = "UPDATE " . GFM_TABLE_SUBSCRIPTIONS . " SET sub_status = %s WHERE subscription_id = %s";
                    $qry = $this->wpdb->prepare($sql, $subscription->status, $_GET['subscription']);
                    
                    $this->wpdb->query($qry);
                    
                    wp_redirect('?page=' . $_REQUEST['page'] . '&msg=cancel-ok');
                }
                else
                    wp_redirect('?page=' . $_REQUEST['page'] . '&msg=cancel-nok&status=' . $subscription->status);
            }

        } catch (Mollie_API_Exception $e) {
            $msg = "API call failed: " . htmlspecialchars($e->getMessage());            
            $gfm_msg =  "<div class=\"error notice\"><p>" . $msg . "</p></div>";        // display message          
            $addon->log_message(__METHOD__, $msg, 1);                             // log message
        }


        if (isset($_GET['msg']))
        {
            switch ($_GET['msg'])
            {
                case 'cancel-ok':
                    $gfm_msg = '<div class="updated notice"><p>' . esc_html__('The subscription is successfully cancelled', GFM_TXT_DOMAIN) . '</p></div>';
                    break;
                case 'cancel-nok':
                    $gfm_msg = '<div class="error notice"><p>' . esc_html__('The subscription is not cancelled', GFM_TXT_DOMAIN) . '</p></div>';
                    break;
            }
        }

        $gfmTable = new GFM_Subscriptions_Table();
        $gfmTable->prepare_items();
        ?>
        <div class="wrap">
            <h2><?php esc_html_e('Subscriptions', GFM_TXT_DOMAIN) ?></h2>

            <?php
            echo isset($gfm_msg) ? $gfm_msg : '';

            $gfmTable->display();
            ?>
        </div>
        <?php
    }

    public function show_donor_list()
    {
        $gfmTable = new GFM_Donors_Table();
        $gfmTable->prepare_items();
        ?>
        <div class="wrap">
            <h2><?php esc_html_e('Donors', GFM_TXT_DOMAIN) ?></h2>

            <?php
            echo isset($gfm_msg) ? $gfm_msg : '';

            $gfmTable->display();
            ?>
        </div>
        <?php
    }
    
    public function show_donation()
    {
        $donation = $this->wpdb->get_row("SELECT * FROM " . GFM_TABLE_DONATIONS . " WHERE id = '" . esc_sql($_REQUEST['id']) . "'");
        ?>
        <div class="wrap">
            <h2><?php esc_html_e('Donation', GFM_TXT_DOMAIN) ?></h2>

            <table class="widefat fixed striped">
                <thead>
                <tr valign="top">
                    <th id="empty" class="manage-column column-empty" style="width:5px;">&nbsp;</th>
                    <th id="a" class="manage-column column-a" style="width: 200px;">&nbsp;</th>
                    <th id="b" class="manage-column column-b">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Name', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->name);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Email address', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->email);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Company name', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->company);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Phone number', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->phone);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Address', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->address);?><br><?php echo esc_html($donation->zipcode . ' ' . $donation->city);?><br><?php echo esc_html($donation->country);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Project', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->project);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Message', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo nl2br(esc_html($donation->message));?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Amount', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b">&euro; <?php echo number_format($donation->amount, 2, ',', '');?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Payment method', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->payment_method);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Recurring payment', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->customer_id ? __('Yes', GFM_TXT_DOMAIN) : __('No', GFM_TXT_DOMAIN));?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Payment status', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->status);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Donation ID', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->donation_id);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Payment ID', GFM_TXT_DOMAIN);?></strong></th>
                        <td class="column-b"><?php echo esc_html($donation->payment_id);?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function export_donations()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=donations.csv');
        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Date/time', 
            'Name', 
            'Email address', 
            'Phone number', 
            'Address', 
            'Zipcode', 
            'City', 
            'Country', 
            'Company name', 
            'Project', 
            'Message', 
            'Amount', 
            'Status', 
            'Payment method', 
            'Recurring payment', 
            'Interval',
            'Donation ID', 
            'Payment ID',
            'Tag', 
            'Custom'
        ));
        
        $where = '';
        if (isset($_GET['subscription']))
            $where .= ' WHERE subscription_id="' . esc_sql($_GET['subscription']) . '"';

        if (isset($_GET['search'])) {
            $srch = esc_sql($_GET['search']);
            $where .= ($where ? ' AND (' : ' WHERE (');
            
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
            else {
                $where .= 'name LIKE "%' . $srch . '%" OR email LIKE "%' . $srch . '%" OR company LIKE "%' . $srch . '%" OR donation_id LIKE "%' . $srch . '%" OR payment_id LIKE "%' . $srch . '%")';
            }
        }
        
        $qry = 'SELECT d.*, s.sub_interval FROM ' . GFM_TABLE_DONATIONS . ' d LEFT JOIN ' . GFM_TABLE_SUBSCRIPTIONS . ' s ON d.subscription_id = s.subscription_id';
        $donations = $this->wpdb->get_results($qry . $where . " ORDER BY time DESC");
        
        foreach ($donations as $donation)
        {      
            fputcsv($output, array(
                $donation->time,
                $donation->name,
                $donation->email,
                $donation->phone,
                $donation->address,
                $donation->zipcode,
                $donation->city,
                $donation->country,
                $donation->company,
                $donation->project,
                $donation->message,
                $donation->amount,
                $donation->status,
                $donation->payment_method,
                $donation->customer_id ? __('Yes', GFM_TXT_DOMAIN) : __('No', GFM_TXT_DOMAIN),
                $donation->sub_interval,
                $donation->donation_id,
                $donation->payment_id,
                $donation->tag,
                $donation->custom,
            ));
        }
    }

    public function admin_notice__warning() 
    {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('In order for the plugin "Gravity Forms addon for Mollie" to function properly, it is necessary to enable permalinks in your <a href="options-permalink.php">Wordpress settings</a>.', GFM_TXT_DOMAIN); ?></p>
        </div>
        <?php
    }
}