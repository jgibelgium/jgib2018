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
    	//var debitMethods = ['ideal', 'creditcard', 'mistercash', 'sofort', 'kbc', 'belfius'];	
    	var debitMethods = ['creditcard'];	
        var intvl = jQuery(this).val(); //waarde vd geselecteerde payment frequentie
        jQuery('li.gfm-payment-method select option').each(function(i) 
        {
            var opt = jQuery(this).val();	//naam methode voor eenmalige betaling
            if (intvl > '0')			// subscription payment
            {
                var dm = jQuery.inArray(opt, debitMethods); //Search for a specified value within an array and return its index (or -1 if not found).
                //alert('debit method index: ' + dm);
                var action = dm < 0 ? jQuery(this).hide() : jQuery(this).show();
            }
            else {
                jQuery(this).show();
            }			
        });                            
    });
    
    jQuery('.gform_wrapper ul.gfield_radio li input[value=Other]').attr('placeholder','Other eg. 1000'); 
    jQuery('.gform_wrapper ul.gfield_radio li input[value=Andere]').attr('placeholder','Anders bv. 1000'); 
    jQuery('.gform_wrapper ul.gfield_radio li input[value=Autrement]').attr('placeholder','Autrement ex. 1000'); 
});      