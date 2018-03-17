//
// Filter payment methods for recurring (subscription) payments
// -> only payment methods that may function as 'first payment' for recurring payments are shown
//
// Custom CSS Classes 'gfm-field-payment-interval' and 'gfm-field-payment-method' MUST be configured in GF for the respective fields
//
jQuery(document).ready(function() 
{	
    jQuery('li.gfm-payment-interval select').change(function()
    { 
        var debitMethods = ['ideal', 'creditcard', 'mistercash', 'sofort', 'kbc', 'belfius'];	
        var intvl = jQuery(this).val();
        jQuery('li.gfm-payment-method select option').each(function(i) 
        {
            var opt = jQuery(this).val();			
            if (intvl > '0')			// subscription payment
            {
                var dm = jQuery.inArray(opt, debitMethods);
                var action = dm < 0 ? jQuery(this).hide() : jQuery(this).show();
            }
            else {
                jQuery(this).show();
            }			
        });                            
    });
});         
