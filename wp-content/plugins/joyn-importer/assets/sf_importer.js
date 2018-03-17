
var demo = GetURLParameter('democontent');

	if ('yes' == demo){
			setTimeout(function() {
  						// Do something after 5 seconds
						location.reload();
						
				}, 90000);
	}
					
function GetURLParameter(sParam){
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}
jQuery(document).ready(function(){
		
		jQuery('.filtering li').on('click', 'a', function(e) {
			e.preventDefault();
			jQuery(this).parent().parent().find('li').removeClass('selected');
			jQuery(this).parent().addClass('selected');
			var selector = jQuery(this).data('filter');
			var demos = jQuery('.swift-demo').find('li');

			demos.removeClass('hidden');
			if (selector != "all") {
				demos.each(function() {
					if (!jQuery(this).hasClass(selector)) {
						jQuery(this).addClass('hidden');
					}
				});
			}
		});

		jQuery('.has-items a').click(function(e){
			//jQuery(jQuery(this).attr('data-filter')).hide();
		});

		jQuery('#sf_import_start').click(function(e){
		
			window.location = jQuery(this).attr('data-url');
			
		});
		
		jQuery('.demoimp-button').click(function(e){
							
				data_url_options = '';
				demoid = jQuery(this).attr('data-demoid');
										
				if ( jQuery('#democontent'+demoid).is(':checked') )
					data_url_options += '&democontent=yes';
					
				if ( jQuery('#themeoption'+demoid).is(':checked') )
					data_url_options += '&themeopt=yes';
						
				if ( jQuery('#coloroption'+demoid).is(':checked') )
					data_url_options += '&coloropt=yes';
						
				if ( jQuery('#widgetsoption'+demoid).is(':checked') )
					data_url_options += '&widgetopt=yes';	
						
				if( data_url_options == '' ){
					alert("Please check one of the options.");
				}
				
				else{
					
					jQuery('.sf-modal-notice , .sf-black-overlay').show();
					importElement = jQuery(this);
					jQuery('#sf_import_start').attr('data-url', importElement.attr('data-url')+data_url_options);
							
				}
						
					
		});
				
});
		