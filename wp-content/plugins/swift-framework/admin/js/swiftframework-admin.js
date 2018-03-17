/*global jQuery,wp */

var SWIFTFRAMEWORK_ADMIN = SWIFTFRAMEWORK_ADMIN || {};

(function( $ ) {
	'use strict';

	SWIFTFRAMEWORK_ADMIN.general = {
		init: function() {
			SWIFTFRAMEWORK_ADMIN.general.instagram();
		},
		load: function() {
		},
		instagram: function() {
			var hash = window.location.hash,
	        token = hash.substring(14),
	        id = token.split('.')[0];
		    
		    //If there's a hash then autofill the token and id
		    if (hash) {
		    	$('#access_token').val(token);
		    	$('#user_id').val(id);
		        $('#sf_instagram_info').append('<div class="notice"><p><span style="color: red;">Important:</span> please make sure you press <b>"Save Changes"</b> to store the authentication information.</p></div>');
		    }
		}
	};

	SWIFTFRAMEWORK_ADMIN.meta = {
		init: function() {
			
			SWIFTFRAMEWORK_ADMIN.meta.select();
			SWIFTFRAMEWORK_ADMIN.meta.color();
			SWIFTFRAMEWORK_ADMIN.meta.media();
			if ( jQuery('.swiftframework-meta-attached-images').length > 0 ) {
				SWIFTFRAMEWORK_ADMIN.meta.multipleMedia();
			}

			// Detect changes for dependencies
	        jQuery( 'body' ).on('change', '.swiftframework-metabox-table select, .swiftframework-metabox-table radio, .swiftframework-metabox-table input[type=radio], .swiftframework-metabox-table input[type=checkbox], .swiftframework-metabox-table input[type=hidden], .spb-buttonset',function() {
	        	SWIFTFRAMEWORK_ADMIN.meta.dependencies();
	        });

	        // Post format specific meta
	        if ( jQuery( '#post-formats-select' ).length > 0 ) {
	        	// Loop through each metabox and add to a cached array
	 			jQuery('.swiftframework-metabox-table').each(function() {
	 				var metabox = {};
	 				var element = jQuery(this);

	 				// Check if is post format meta box, and if so add to array
	 				if ( element.data('post-format') ) {
	 					metabox.element = element;
	 					metabox.format = element.data('post-format');
		 				SWIFTFRAMEWORK_ADMIN.var.postFormatMetaBoxes.push(metabox);
	 				}
	 			});

				jQuery('#post-formats-select input').change(SWIFTFRAMEWORK_ADMIN.meta.checkFormat);
				jQuery('.wp-post-format-ui .post-format-options > a').click(SWIFTFRAMEWORK_ADMIN.meta.checkFormat);
	        }
		},
		load: function() {
			SWIFTFRAMEWORK_ADMIN.meta.checkFormat();

			// Check dependencies
			SWIFTFRAMEWORK_ADMIN.meta.dependencies();
		},
		select: function() {
			jQuery('.getmdl-select input').on('change', function() {
				var $this = jQuery(this),
					selectedValue = $this.data('val');
				$this.parent().find('input.meta-value').val(selectedValue);
			});
		},
		color: function() {
			$('.swiftframework-metabox-table').find('.swiftframework-meta-colorpicker').each( function() {
				$(this).wpColorPicker();
			});
		},
		media: function() {

			// Media remove
			$('.swiftframework-metabox-table').on('click', '.swiftframework-meta-media-remove', function(e) {
				
				e.preventDefault();

				var thisRemove = jQuery(this),
					thisButton = thisRemove.parent().find('.swiftframework-meta-media-add'),
		            thisThumb = thisRemove.parent().find('.swiftframework-meta-thumb'),
		            thisVal = thisButton.parent().find('.file-upload');

	            thisButton.css('display', 'inline-block');
	            thisRemove.css('display', 'none');
	            thisThumb.attr('src', '').css('display', 'none');
	            thisVal.val('');
			});

			// Media select
			var currentField = "";
		    if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
		        $('.swiftframework-metabox-table').on('click', '.swiftframework-meta-media-add', function(e) {
		            
		            e.preventDefault();

		            // Store current button
		            currentField = jQuery(this);

		            /**				 
		            * If an instance of file_frame already exists, then we can open it
		            * rather than creating a new instance.
		            */
		            if ( undefined !== file_frame ) {		
						file_frame.open();					
						return;		
					}

					/**
					 * If we're this far, then an instance does not exist, so we need to
					 * create our own.
					 *
					 * Here, use the wp.media library to define the settings of the Media
					 * Uploader implementation by setting the title and the upload button
					 * text. We're also not allowing the user to select more than one image.
					 */
					var $buttonText = "Use Image";
					if ( currentField.data('media-type') == "video" ) {
						$buttonText = "Use Video";
					} else if ( currentField.data('media-type') == "audio" ) {
						$buttonText = "Use Audio";
					} 
 					file_frame = wp.media.frames.file_frame = wp.media({
						title:    "Insert Media",    // For production, this needs i18n.
						button:   {
							text: $buttonText     // For production, this needs i18n.
						},
						multiple: false
					});

					// Remove all attached 'select' event
					file_frame.off( 'select' );

					/**
					 * Setup an event handler for what to do when an image has been
					 * selected.
					 */
					file_frame.on( 'select', function() {

						image_data = file_frame.state().get( 'selection' ).first().toJSON();
						for ( var image_property in image_data ) {

							/**
							 * Here, you have access to all of the properties
							 * provided by WordPress to the selected image.
							 *
							 * This is generally where you take the data and so whatever
							 * it is that you want to do.
							 *
							 * For purposes of example, we're just going to dump the
							 * properties into the console.
							 */
							//console.log( image_property + ': ' + image_data[ image_property ] );

							// Set meta item thumb
							currentField.parent().find('.swiftframework-meta-thumb').css('display', 'block').attr('src', image_data.url);

							// hide/show appropriate controls
							currentField.parent().find('.swiftframework-meta-media-add').css('display', 'none');
							currentField.parent().find('.swiftframework-meta-media-remove').css('display', 'inline-block');

							// update value
							if ( currentField.data('type') === "url" ) {
								currentField.parent().find('.file-upload').val(image_data.url);
							} else {
								currentField.parent().find('.file-upload').val(image_data.id);
							}
						}

					});

					// Now display the actual file_frame
					file_frame.open();

		        });
			}
		},
		multipleMedia: function() {
			jQuery('.swiftframework-meta-attached-images .images-list').sortable({
	            forcePlaceholderSize: !0,
	            placeholder: "sortable-placeholder",
	            cursor: "move",
	            items: "li",
	            update: function() {
	                var updatedAttachmentIDs = [];
	                var number = 1;
	                jQuery(this).find(".added").each(function() {
	                    var img_id = jQuery(this).find('img').attr("rel");
	                    updatedAttachmentIDs.push( img_id );
	                    jQuery(this).find('.swiftframework-img-order').text(number);
	                    number++;
	                });
	                jQuery(this).parents('td').find('.meta-value').val( updatedAttachmentIDs.join(",") ).trigger("change");
	            }
	        });

			$('.swiftframework-metabox-table').on('click', '.swiftframework-meta-media-images-add', function(e) {
                e.preventDefault();

                var file_frame = "",
                    multiple = true,
                    parentField = jQuery( this ).parents('td').find( '.meta-value' ),
                    attachedImages = jQuery( this ).parents('td').find( '.swiftframework-meta-attached-images .images-list' );

                // If the media frame already exists, reopen it.
                if ( file_frame ) {
                    file_frame.open();
                    return;
                }

                // Create the media frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: jQuery( this ).data( 'uploader_title' ),
                    button: {
                        text: jQuery( this ).data( 'uploader_button_text' ),
                    },
                    multiple: multiple  // Set to true to allow multiple files to be selected
                });

                // When an image is selected, run a callback.
                file_frame.on( 'select', function( ) {
                  
                    // We set multiple to false so only get one image from the uploader
                    var selection = file_frame.state().get( 'selection' );
                    var attachmentIDs = [];
                    var number = 1;

                    // Empty image element, and destroy sortable
                    attachedImages.empty();
                    attachedImages.sortable("destroy");

                    // Loop through selection
                    selection.map( function( attachment ) {
                        attachment = attachment.toJSON();
                        attachmentIDs.push(attachment.id);  
                        attachedImages.append('<li class="added" media_id="'+attachment.id+'"><img src="'+attachment.sizes.medium.url+'" alt="" rel="'+attachment.id+'"><span class="swiftframework-img-order">'+number+'</span><div class="swiftframework-meta-remove-img"><a title="Deselect" class="swiftframework-meta-delete-file" href="#">&times;</a></div></li>');
                        number++;
                    });

                    // Update field
                    parentField.val( attachmentIDs.join(',') );

                    // Init sortable
                    attachedImages.sortable({
                        forcePlaceholderSize: !0,
                        placeholder: "sortable-placeholder",
                        cursor: "move",
                        items: "li",
                        update: function() {
                            var updatedAttachmentIDs = [];
                            var number = 1;
                            jQuery(this).find(".added").each(function() {
                                var img_id = jQuery(this).find('img').attr("rel");
                                updatedAttachmentIDs.push( img_id );
                                jQuery(this).find('.swiftframework-img-order').text(number);
                                number++;
                            });
                            parentField.val( updatedAttachmentIDs.join(",") ).trigger("change");
                        }
                    });
                    
                    // Delete image function
                    jQuery('.swiftframework-meta-delete-file').click(function(e) {
                        e.preventDefault();
    
                        // store image id
                        var imgID = jQuery(this).parents('.added').find('img').attr('rel');

                        // adjust val
                        var field = jQuery(this).parents('td').find('.meta-value');
                        var attachmentIDs = field.val().split(",");
                        attachmentIDs.splice(attachmentIDs.indexOf(imgID), 1);

                        // set new value
                        field.val( attachmentIDs.join(',') );

                        // remove image
                        jQuery(this).parents('.added').remove();

                        return false;
                    });

                    return false;
                });

                // Set already selected images, if there are any
                file_frame.on( 'open', function() {
                    var selection = file_frame.state().get('selection');
                    var attachmentIDs = parentField.val().split(",");
                    jQuery.each( attachmentIDs, function(i, val ) {
                        selection.add(wp.media.attachment(val));
                    });
                });

                // Finally, open the modal
                file_frame.open();
            });
		},
		dependencies: function() {

		    jQuery(".swiftframework-metabox-table .dependency-field").each( function() {

	            var field_operator = jQuery(this).attr('data-parent-operator');
	            var field_value = jQuery(this).attr('data-parent-value');
	            var changed_field_value;
	            var field_values_array;
	            var current_field;
	            var valid_counter = 0;
	            var temp_object;
	            
	            switch ( field_operator ) {
	                case '=':
	                case 'equals':

	                    temp_object = jQuery('.swiftframework-metabox-table  .' + jQuery(this).attr('data-parent-id') );

	                    if ( temp_object.length > 1 ) {

	                        changed_field_value = temp_object.find( 'select' ).val();
	                    
	                    } else {
	                        if ( temp_object.hasClass( 'buttonset') ) {
	                        	if ( temp_object.find('.is-checked input').length > 0 ) {
	                        		changed_field_value = temp_object.find('.is-checked input').val();
	                        	} else {
	                        		changed_field_value = temp_object.data('current');
	                        	}
	                            
	                        } else {
	                            changed_field_value = jQuery( '.swiftframework-metabox-table  .' + jQuery(this).attr('data-parent-id') ).val();
	                        }
	                    }

	                    if ( changed_field_value == field_value ) {
	                        //Show the depency field
	                        jQuery(this).removeClass("hide");
	                    } else {
	                        //Hide the depency field
	                        jQuery(this).addClass("hide");
	                    }

	                    break;
	                case '!=':
	                case 'not':
	                    field_values_array = jQuery(this).attr('data-parent-value').split(',');
	                    current_field = jQuery(this);
	                    valid_counter = 0;

	                    jQuery.each(field_values_array, function(index, value) {
	                        
	                        temp_object = jQuery('.swiftframework-metabox-table  .'+current_field.attr('data-parent-id'));
	                        
	                        if ( temp_object.length > 1 ) {

	                            changed_field_value = temp_object.find( 'select' ).val();
	                    
	                        } else {
	                            if ( temp_object.hasClass( 'buttonset') ) {
	                                         
	                                if( temp_object.attr('checked') == 'checked' ){
	                                    changed_field_value = temp_object.attr('data-value-on');
	                                }else{
	                                    changed_field_value = temp_object.attr('data-value-off');
	                                }

	                            } else {
	                                changed_field_value = jQuery('.swiftframework-metabox-table  .'+jQuery(this).attr('data-parent-id')).val();
	                            }
	                        }

	                        if ( changed_field_value != value.trim() ) {
	                            valid_counter++;
	                        }

	                    });

	                    if ( valid_counter == field_values_array.length ) {
	                        //Show the depency field
	                        jQuery(this).removeClass("hide");
	                    } else {
	                        //Hide the depency field
	                        jQuery(this).addClass("hide");
	                    }
	                    break;
	                case 'or':
	                    field_values_array = jQuery(this).attr('data-parent-value').split(',');
	                    current_field = jQuery(this);
	                    valid_counter = 0;

	                    jQuery.each(field_values_array, function(index, value) {
	                        temp_object = jQuery('.swiftframework-metabox-table  .'+current_field.attr('data-parent-id'));

	                     if ( temp_object.length > 1 ) {

	                        changed_field_value = temp_object.find( 'select' ).val();
	                    
	                    } else {
	                        if ( temp_object.hasClass( 'buttonset') ) {
	                                         
	                            if ( temp_object.attr('checked') == 'checked' ) {
	                                changed_field_value = temp_object.attr('data-value-on');
	                            } else {
	                                changed_field_value = temp_object.attr('data-value-off');
	                            }

	                        } else {
	                            changed_field_value = jQuery('.swiftframework-metabox-table  .'+jQuery(this).attr('data-parent-id')).val();
	                        }
	                    }

	                    if ( changed_field_value != value.trim() ) {
	                    	valid_counter++;
	                    }

	                    });

	                    if ( valid_counter == field_values_array.length ) {
	                        //Hide the depency field
	                        jQuery(this).addClass("hide");
	                    } else {
	                        //Show the depency field
	                        jQuery(this).removeClass("hide");
	                    }
	                break;
		        }
		     });
		},
		checkFormat: function() {
			// Check for the selected post format
			var format = jQuery('#post-formats-select fieldset input:radio:checked').attr('value');
			// Once we have the post format, if it's not undefined we need to adjust the meta box display
			if ( typeof format !== 'undefined' ) {
				jQuery.each(SWIFTFRAMEWORK_ADMIN.var.postFormatMetaBoxes, function(index, metabox) {
	 				if ( metabox.format === format) {
	 					metabox.element.parents('.postbox').first().stop( true, true ).slideDown(600);
	 				} else {
	 					metabox.element.parents('.postbox').first().stop( true, true ).slideUp(600);
	 				}
	 			});				
			}
		}
	};


	/////////////////////////////////////////////
	// GLOBAL VARIABLES
	/////////////////////////////////////////////

	var $document = jQuery(document),
		$window = jQuery(window),
		$body = jQuery('body'),
		file_frame,
		image_data;

	SWIFTFRAMEWORK_ADMIN.var = {};
	SWIFTFRAMEWORK_ADMIN.var.postFormatMetaBoxes = [];

	/////////////////////////////////////////////
	// LOAD + READY FUNCTION
	/////////////////////////////////////////////

	SWIFTFRAMEWORK_ADMIN.onReady = {
		init: function() {
			SWIFTFRAMEWORK_ADMIN.general.init();
			SWIFTFRAMEWORK_ADMIN.meta.init();
		}
	};
	SWIFTFRAMEWORK_ADMIN.onLoad = {
		init: function() {
			SWIFTFRAMEWORK_ADMIN.general.load();
			SWIFTFRAMEWORK_ADMIN.meta.load();
		}
	};

	$document.ready( SWIFTFRAMEWORK_ADMIN.onReady.init );
	$window.on( 'load', SWIFTFRAMEWORK_ADMIN.onLoad.init );

})( jQuery );