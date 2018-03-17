<?php
class MDB_Dashboard
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        
        $this->wpdb = $wpdb; 
        
        add_action('admin_menu', array($this, 'add_dashboard_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_mdb_export', array($this, 'export_data'));
    }
    
    public function add_dashboard_menu() 
    {
        add_menu_page(
            'Mollie Payments',                          // page title
            'MDB Payments',                             // menu item
            'edit_dashboard',                           // rights required
            MDB_PAGE_PAYMENTS,                          // menu slug
            array($this, 'show_payment_list')           // page function
        );
        
        add_submenu_page(
            MDB_PAGE_PAYMENTS,                          // parent menu slug
            'Mollie Subscriptions',                     // page title
            'MDB Subscriptions',                        // menu item
            'edit_dashboard',                           // rights required
            MDB_PAGE_SUBSCRIPTIONS,                     // menu slug
            array($this, 'show_subscription_list')      // page function
        );
        
        add_submenu_page(
            MDB_PAGE_PAYMENTS,                          // parent menu slug
            'Mollie Mandates',                          // page title
            'MDB Mandates',                             // menu item
            'edit_dashboard',                           // mandate rights required
            MDB_PAGE_MANDATES,                          // menu slug
            array($this, 'show_mandate_list')           // page function
        );
        
        add_submenu_page( 
            MDB_PAGE_PAYMENTS,                          // parent menu slug
            'Mollie Customers',                         // page title
            'MDB Customers',                            // menu item
            'edit_dashboard',                           // mandate rights required
            MDB_PAGE_CUSTOMERS,                         // menu slug
            array($this, 'show_customer_list')          // page function
        );
        
        add_submenu_page( 
            MDB_PAGE_PAYMENTS,                          // parent menu slug
            'Mollie Settings',                          // page title
            'MDB Settings',                             // menu item
            'edit_dashboard',                           // mandate rights required
            MDB_PAGE_SETTINGS,                          // menu slug
            array($this, 'show_settings')               // page function
        );
        
        add_submenu_page(
            null,                                       // no parent (detail page)
            'Payment',                                  // page title
            'MDB Payment',                              // menu item     
            'edit_dashboard',                           // subscription rights required
            MDB_PAGE_PAYMENT,                           // menu slug
            array($this, 'handle_payment')              // page function
        );
        
        add_submenu_page(
            null,                                       // no parent (detail page)
            'Subscription',                             // page title
            'MDB Subscription',                         // menu item     
            'edit_dashboard',                           // subscription rights required
            MDB_PAGE_SUBSCRIPTION,                      // menu slug
            array($this, 'handle_subscription')         // page function
        );
    }
    
    public function show_payment_list() 
    {        
        $payments_tbl = new MDB_Payments_Table();
        
        $page = isset($_GET['paged']) ? $_GET['paged'] : 1;
        $payments_tbl->prepare_items($page);
        
        $msg = '';                  
        if (isset($_GET['msg']))
        {
            switch ($_GET['msg'])
            {
                case 'refund-ok':
                    $msg = '<div class="updated notice"><p>' . esc_html__('The payment has been succesfully refunded') . '</p></div>';
                    break;
                case 'refund-failed':
                    $msg = '<div class="error notice"><p>' . esc_html__('The payment could not be refunded') . '</p></div>';
                    break;
                case 'refund-not-allowed':
                    $msg = '<div class="updated notice"><p>' . esc_html__('Refund not allowed for this payment') . '</p></div>';
                    break;
            }
        }
        ?>

        <div class="wrap">
            <h2><?php esc_html_e('Mollie Payments') ?></h2>
            
            <form method="post" style="float: left;">
                <input type="hidden" name="page" value="<?php esc_html_e(MDB_PAGE_PAYMENTS)?>" />
                <?php $payments_tbl->search_box('Search', 'payment_id'); ?>
            </form>
            
            <a href="<?php echo admin_url('admin-post.php?action=mdb_export&type=payments');?>" style="float: right;"><?php esc_html_e('Export')?></a>
            
            <?php
            echo isset($msg) ? $msg : '';                                 
            $payments_tbl->display();
            ?>
        </div>
    <?php
    } 
    
    public function show_subscription_list() 
    {        
        $subscr_tbl = new MDB_Subscriptions_Table();        
        $subscr_tbl->prepare_items();
        
        $msg = '';       
        if (isset($_GET['msg']))
        {
            switch ($_GET['msg'])
            {
                case 'cancel-ok':
                    $msg = '<div class="updated notice"><p>' . esc_html__('The subscription has been cancelled') . '</p></div>';
                    break;
                case 'cancel-nok':
                    $msg = '<div class="error notice"><p>' . esc_html__('The subscription could not be cancelled') . '</p></div>';
                    break;
            }
        }       
        ?>

        <div class="wrap">
            <h2><?php esc_html_e('Mollie Subscriptions') ?></h2>
            
            <form method="post" style="float: left;">
                <input type="hidden" name="page" value="<?php esc_html_e(MDB_PAGE_SUBSCRIPTIONS)?>" />
                <?php $subscr_tbl->search_box('Search', 'customer_name'); ?>
            </form>
            
            <a href="<?php echo admin_url('admin-post.php?action=mdb_export&type=subscriptions');?>" style="float: right;"><?php esc_html_e('Export')?></a>
            
            <?php
            echo isset($msg) ? $msg : '';                                 
            $subscr_tbl->display();
            ?>
        </div>
    <?php
    } 
    
    public function show_mandate_list() 
    {       
        $mandate_tbl = new MDB_Mandates_Table();       
        $mandate_tbl->prepare_items();
        ?>

        <div class="wrap">
            <h2><?php esc_html_e('Mollie Mandates') ?></h2>
            
            <form method="post" style="float: left;">
                <input type="hidden" name="page" value="<?php esc_html_e(MDB_PAGE_MANDATES)?>" />
                <?php $mandate_tbl->search_box('Search', 'customer_name'); ?>
            </form>
            
            <a href="<?php echo admin_url('admin-post.php?action=mdb_export&type=mandates');?>" style="float: right;"><?php esc_html_e('Export')?></a>
            
            <?php
            echo isset($msg) ? $msg : '';                                 
            $mandate_tbl->display();
            ?>
        </div>

    <?php
    } 
    
    public function show_customer_list() 
    {       
        $customer_tbl = new MDB_Customers_Table();
        
        $page = isset($_GET['paged']) ? $_GET['paged'] : 1;
        $customer_tbl->prepare_items($page);
        ?>

        <div class="wrap">
            <h2><?php esc_html_e('Mollie Customers') ?></h2>
            
            <form method="post" style="float: left;">
                <input type="hidden" name="page" value="<?php esc_html_e(MDB_PAGE_CUSTOMERS)?>" />
                <?php $customer_tbl->search_box('Search', 'customer_name'); ?>
            </form>
            
            <a href="<?php echo admin_url('admin-post.php?action=mdb_export&type=customers');?>" style="float: right;"><?php esc_html_e('Export')?></a>
            
            <?php
            echo isset($msg) ? $msg : '';                                 
            $customer_tbl->display();
            ?>
        </div>

    <?php
    } 
   
    public function handle_payment()
    {
        $msg = '';      
        try 
        {
            if (isset($_GET['action']))
            {
                if (isset($_GET['payment_id']))
                {
                    $mollie = new Mollie_API_Client;
                    if (get_option('mollie_api_key')) {
                        $mollie->setApiKey(get_option('mollie_api_key'));
                    } else {
                        echo '<div class="error notice"><p>' . esc_html__('Mollie API key not set') . '</p></div>';
                        return;
                    }
                    
                    $payment_id = $_GET['payment_id'];
                    $payment = $mollie->payments->get($payment_id);                      // get payment

                    if ($_GET['action'] == 'view')
                    {                        
                        $metadata = '';
                        if (isset($payment->metadata)) {
                            foreach ($payment->metadata as $name => $value) {
                                $metadata .= '<p>' . $name . ': ' . $value . '</p>'; 
                            }
                        }
                        
                        $details = '';
                        if (isset($payment->details)) {
                            foreach ($payment->details as $name => $value) {
                                $details .= '<p>' . $name . ': ' . $value . '</p>'; 
                            }
                        }
                        
                        $links = '';
                        if (isset($payment->links)) {
                            foreach ($payment->links as $name => $value) {
                                $links .= '<p>' . $name . ': ' . $value . '</p>'; 
                            }
                        }
                    }

                    if ($_GET['action'] == 'refund')
                    {
                        if ($payment->canBeRefunded()) {
                            $refund = $mollie->payments->refund($payment);                          
                            $msg = ($refund->status == 'refunded') ? '&msg=refund-ok' : '&msg=refund-failed';
                        }
                        else {
                            $msg = '&msg=refund-not-allowed';
                        }
                        
                        wp_redirect('?page=' . MDB_PAGE_PAYMENTS . $msg);                      
                    }
                }
            }
        } 
        catch (Mollie_API_Exception $e) {
            $msg = "API call failed: " . htmlspecialchars($e->getMessage());            
        }
        ?>

        <div class="wrap">
            <h2><?php esc_html_e('Mollie Payment') ?></h2>
            
            <?php esc_html_e($msg) ?>

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
                        <th class="column-a" scope="row"><strong><?php esc_html_e('ID');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->id);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Amount');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->amount);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Amount Refunded');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->amountRefunded);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Amount Remaining');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->amountRemaining);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Method');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->method);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Status');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->status);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Mode');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->mode);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Customer ID');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->customerId);?></td>
                    </tr>                   
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Subscription ID');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->subscriptionId);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Mandate ID');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->mandateId);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Profile ID');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->profileId);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Created');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->createdDatetime);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Paid');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->paidDatetime);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Cancelled');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->cancelledDatetime);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Expired');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->expiredDatetime);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Expiry Period');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->expiryPeriod);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Recurring Type');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->recurringType);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Metadata');?></strong></th>
                        <td class="column-b"><?php echo $metadata;?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Details');?></strong></th>
                        <td class="column-b"><?php echo $details;?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Links');?></strong></th>
                        <td class="column-b"><?php echo $links;?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Description');?></strong></th>
                        <td class="column-b"><?php echo esc_html($payment->description);?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }       
   
    public function handle_subscription()
    {
        $msg = '';      
        try 
        {
            if (isset($_GET['action']))
            {
                if (isset($_GET['subscription_id']) && isset($_GET['customer_id']))
                {
                    $mollie = new Mollie_API_Client;
                    if (get_option('mollie_api_key')) {
                        $mollie->setApiKey(get_option('mollie_api_key'));
                    } else {
                        echo '<div class="error notice"><p>' . esc_html__('Mollie API key not set') . '</p></div>';
                        return;
                    }

                    $subscription_id = $_GET['subscription_id'];
                    $customer_id     = $_GET['customer_id'];                

                    if ($_GET['action'] == 'view')
                    {
                        $customer     = $mollie->customers->get($customer_id);                      // get customer
                        
                        $subscriptions = $mollie->customers_subscriptions->withParentId($customer->id)->all();
                        $subscription = $subscriptions[0];

                        // following fails for status = cancelled (?)
                        //$subscription = $mollie->customers_subscriptions
                        //                       ->withParentId($customer_id)->get($subscription_id); // get subscription 
                    }

                    if ($_GET['action'] == 'cancel')
                    {
                        $subscription = $mollie->customers_subscriptions
                                               ->withParentId($customer_id)->cancel($subscription_id); // cancel subscription
                        
                        $page = '?page=' . MDB_PAGE_SUBSCRIPTIONS;
                        if ($subscription->status == 'cancelled') {                            
                            wp_redirect($page . '&msg=cancel-ok');
                        } else {
                            wp_redirect($page . '&msg=cancel-nok');
                        }
                    }
                }
            }
        } 
        catch (Mollie_API_Exception $e) {
            $msg = "API call failed: " . htmlspecialchars($e->getMessage());            
        }
        ?>

        <div class="wrap">
            <h2><?php esc_html_e('Mollie Subscription') ?></h2>
            
            <?php esc_html_e($msg) ?>

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
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Subscription ID');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->id);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Customer Name');?></strong></th>
                        <td class="column-b"><?php echo esc_html($customer->name);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Customer ID');?></strong></th>
                        <td class="column-b"><?php echo esc_html($customer->id);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Status');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->status);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Amount');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->amount);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Method');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->method);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Interval');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->interval);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Times');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->times);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Mode');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->mode);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Created');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->createdDatetime);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Started');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->startDate);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Cancelled');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->cancelledDatetime);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Webhook URL');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->links->webhookUrl);?></td>
                    </tr>
                    <tr>
                        <th class="column-empty"></th>
                        <th class="column-a" scope="row"><strong><?php esc_html_e('Description');?></strong></th>
                        <td class="column-b"><?php echo esc_html($subscription->description);?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
        
    public function register_settings()
    {
        register_setting(MDB_GROUP_SETTINGS, 'mollie_api_key');    
    }
    
    public function show_settings()
    {
    ?>
        <div class="wrap">
            <h2><?php esc_html_e('Mollie Settings') ?></h2>
            <form method="post" action="options.php">
                <?php
                    settings_fields(MDB_GROUP_SETTINGS);
                    do_settings_sections(MDB_GROUP_SETTINGS);
                ?>
                <table class="form-table">
                     <tr valign="top">
                     <th scope="row">Mollie API key</th>
                        <td><input type="text" name="mollie_api_key" size="50" value="<?php echo esc_attr(get_option('mollie_api_key')); ?>" /></td>
                     </tr>
                </table>                
                <?php submit_button();?>          
            </form>
        </div>
    <?php        
    }
    
    public function export_data()
    {
        if (isset($_GET['type'])) 
        {
            switch ($_GET['type']) {
                case 'payments':
                    $tbl = new MDB_Payments_Table();                   
                    break;
                case 'subscriptions':
                    $tbl = new MDB_Subscriptions_Table();                   
                    break;
                case 'mandates':
                    $tbl = new MDB_Mandates_Table();                   
                    break;
                case 'customers':
                    $tbl = new MDB_Customers_Table();                   
                    break;
            }
            
            $tbl->export();
        }
    }
}

