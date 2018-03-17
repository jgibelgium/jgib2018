<?php
/*
*
*   Meta Box Setup
*   ------------------------------------------------
*   Swift Framework
* 	Copyright Swift Ideas 2017 - http://www.swiftideas.com
*   @since v3.0.0
*   
*
*   swiftframework_create_meta_box()
*	swiftframework_save_meta_box()
*
*/

/**
 * Create meta box function
 */
function swiftframework_create_meta_box( $post, $meta_box ) {
	
	// Check $meta_box is an array
    if ( !is_array($meta_box) ) {
    	return false;
    }

    // Check if meta box has descrptio field, and if so, output it
    if ( isset($meta_box['description']) && $meta_box['description'] != '' ) {
    	echo '<p>'. $meta_box['description'] .'</p>';
    }
    
    // Output nonce field
	wp_nonce_field( basename(__FILE__), 'swiftframework_meta_box_nonce' );
	
	// Start meta table
	if ( $meta_box['post_format'] != "" ) {
		echo '<table class="form-table swiftframework-metabox-table swiftframework-mdl" data-post-format="' . $meta_box['post_format'] .'">';
	} else {
		echo '<table class="form-table swiftframework-metabox-table swiftframework-mdl">';	
	}
 		
 	// Track count
	$count = 0;
	
	// Foreach loop of each meta box field
	foreach( $meta_box['fields'] as $field ) {

		// If field is divider, output and break
		if ( $field['type'] == "divider" ) {
			echo '<tr class="divide"><td colspan="2"><div></div></td></tr>';
			continue;
		}

		// Get field meta
		$meta = get_post_meta( $post->ID, $field['id'], true );

		// Start the row
		if ( isset( $field['required'][0] ) ) {
            $req_parent_id       = $field['required'][0];
            $req_parent_operator = $field['required'][1];
            $req_parent_value    = $field['required'][2];
            $field_visibility = 'hide';
            echo '<tr class="dependency-field" data-parent-id="' . $req_parent_id . '" data-parent-operator="' . $req_parent_operator . '" data-parent-value="' . $req_parent_value . '">';
        } else {
            echo '<tr>';
        }

        // Row title/description
		echo '<th>';
			echo '<label for="'. $field['id'] .'">';
				if ( isset( $field['name'] ) ) {
					echo '<strong>'. $field['name'] .'</strong>';
				}
				if ( isset( $field['desc'] ) ) {
			  		echo '<span>'. $field['desc'] .'</span>';
			  	}
			echo '</label>';
		echo '</th>';
		
		// Switch for the various field types
		switch( $field['type'] ) {

			// Text field
			case 'text': 
				echo '<td><div class="mdl-textfield mdl-js-textfield">';
					echo '<input class="mdl-textfield__input" type="text" id="'. $field['id'] .'" name="swiftframework_meta['. $field['id'] .']" value="'. ($meta ? $meta : $field['std']) .'">';
					echo '<label class="mdl-textfield__label" for="sample3"></label>';
				echo '</div></td>';

				break;	
			
			// Textarea field
			case 'textarea':
				echo '<td><textarea name="swiftframework_meta['. $field['id'] .']" id="'. $field['id'] .'" rows="8" cols="5">'. ($meta ? $meta : $field['std']) .'</textarea></td>';
				break;
			
			// Editor field
			case 'editor' :
				$settings = array(
		            'wpautop' => true,
		            'editor_class' => '',
		            'textarea_name' => 'swiftframework_meta['. $field['id'] .']'
		        );
		        wp_editor($meta, $field['id'], $settings );
				break;

			// Slimline editor field
			case 'slim_editor' :
				$settings = array(
		            'wpautop' => true,
		            'editor_class' => 'slim',
		            'textarea_name' => 'swiftframework_meta['. $field['id'] .']'
		        );
		        echo'<td>';
		        	wp_editor($meta, $field['id'], $settings );
				echo '</td>';
				break;

			// Image field
			case 'image':
				
				$img_src = $field['std'];
				if ( $meta ) {
					$img = wp_get_attachment_image_src( $meta, 'full' );
					if ( is_array($img) ) {
						$img_src = $img[0];
					}
				}
				echo '<td><input class="file-upload" type="hidden" id="' . $field['id'] . '" name="swiftframework_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" />';
		        echo '<img class="swiftframework-meta-thumb" id="swiftframework-meta-thumb-' . $field['id'] . '" src="' . $img_src . '" />';
		        if ( ( $meta ? $meta : $field['std'] ) == '' ) {
		        	$remove = ' style="display:none;"'; $upload = '';
		        } else {
		        	$remove = ''; $upload = ' style="display:none;"';
		        }
		        echo ' <button data-update="Select File" data-choose="Choose a File" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect swiftframework-meta-media-add button-secondary"' . $upload . ' rel-id="' . $field['id'] . '">' . __('Upload', 'swiftframework.io') . '</button>';
		        echo '<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect swiftframework-meta-media-remove" ' . $remove . ' rel-id="' . $field['id'] . '">' . __('Remove Media', 'swiftframework.io') . '</button></td>';

				break;

			// Image field
			case 'multiple-images':
				
		    	$image_ids = swiftframework_meta_get_image_ids( $meta );
				echo '<td><input class="meta-value" type="hidden" id="' . $field['id'] . '" name="swiftframework_meta[' . $field['id'] . ']" value="' . $image_ids . '" />';
				echo '<div class="swiftframework-meta-attached-images">';
				echo '<ul class="images-list">';
				echo ( $image_ids != '' ) ? swiftframework_meta_get_attached_images( explode( ",", $image_ids ) ) : '';
				echo '</ul>';
				echo '</div>'; 
				echo '<button data-update="Select Media" data-choose="Choose images" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect swiftframework-meta-media-images-add button-secondary"' . $upload . ' rel-id="' . $field['id'] . '">' . __('Choose Images', 'swiftframework.io') . '</button>';
				echo '</td>';

				break;
			
			// Color field
 			case 'color':
	            wp_enqueue_style('wp-color-picker');					
	            wp_enqueue_script('wp-color-picker');					
		        echo '<td><input type="text" id="' . $field['id'] . '" name="swiftframework_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" class="swiftframework-meta-colorpicker" style="width: 70px;" data-default-color="' . ($meta ? $meta : $field['std']) . '"/></td>';
		        
				break;
			
			// Media field
			case 'media':
				 
				echo '<td><input type="text" class="file_display_text file-upload" id="' . $field['id'] . '" name="swiftframework_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" />';
		        if( ($meta ? $meta : $field['std']) == '') {$remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; }
		        echo ' <button data-update="Select File" data-choose="Choose a File" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect swiftframework-meta-media-add"' . $upload . ' rel-id="' . $field['id'] . '" data-type="url" data-media-type="' . $field['media_type'] . '">' . __('Add Media', 'swiftframework.io') . '</button>';
		        echo '<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect swiftframework-meta-media-remove" ' . $remove . ' rel-id="' . $field['id'] . '">' . __('Remove Media', 'swiftframework.io') . '</button></td>';
		        
				break;
			
			// Select field
			case 'select':
				$default = $meta ? $meta : $field['std'];
				$default_label = $field['options'][$default];
				echo '<td><div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label getmdl-select">';
				echo '<input class="mdl-textfield__input" value="'.$default_label.'" type="text" id="'. $field['id'] .'" readonly tabIndex="-1" data-val="'.$default.'"/>';
				echo '<label for="'. $field['id'] .'"><i class="mdl-icon-toggle__label material-icons">keyboard_arrow_down</i></label>';
				echo '<label for="'. $field['id'] .'" class="mdl-textfield__label"> </label>';
				echo '<ul class="mdl-menu mdl-menu--bottom-left mdl-js-menu" for="'. $field['id'] .'">';
				foreach( $field['options'] as $key => $option ) {
					echo '<li class="mdl-menu__item" data-val="' . $key . '">'. $option .'</li>';
				}
				echo'</ul>';
				echo '<input type="hidden" class="meta-value" name="swiftframework_meta['. $field['id'] .']" value="' . $default . '" />';
				echo '</div></td>';
				break;

			// Multi-select field
			case 'multi-select':
				echo'<td><select multiple="multiple" name="swiftframework_meta['. $field['id'] .'][]" id="'. $field['id'] .'">';
				foreach( $field['options'] as $key => $option ){
					echo '<option value="' . $key . '"';
					if ( $meta ) {
						echo (is_array($meta) && in_array($key, $meta)) ? ' selected="selected"' : '';
						if( $meta == $key ) echo ' selected="selected"'; 
					} else {
						if( $field['std'] == $key ) echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				break;

			// Button-set Field
			case 'button-set' :
				$default = $meta ? $meta : $field['std'];
				echo '<td colspan="8">';
				    echo '<fieldset class="buttonset '.$field['id'].'" data-current="'.$default.'">';
						foreach( $field['options'] as $key => $option ){
							echo '<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="swiftframework_meta_'. $key .'">';
								echo '<input type="radio" id="swiftframework_meta_'. $key .'" class="mdl-radio__button" name="swiftframework_meta['. $field["id"] .']" value="'. $key .'" ';
			  						if ( $meta ) { 
										if( $meta == $key ) echo ' checked'; 
									} else {
										if( $field['std'] == $key ) echo ' checked';
									}
									echo '>';
								echo '<span class="mdl-radio__label">'.$option.'</span>';
							echo '</label>';
						}
					echo '</fieldset>';
				echo '</td>';
			break;
				
			// Radio field
			case 'radio':
				echo '<td>';
				foreach( $field['options'] as $key => $option ){
					echo '<label class="radio-label"><input type="radio" name="swiftframework_meta['. $field['id'] .']" value="'. $key .'" class="radio"';
					if( $meta ){ 
						if( $meta == $key ) echo ' checked="checked"'; 
					} else {
						if( $field['std'] == $key ) echo ' checked="checked"';
					}
					echo ' /> '. $option .'</label> ';
				}
				echo '</td>';
				break;

			case 'slider':
				$min = $field['js_options']['min'];
				$max = $field['js_options']['max'];
				$step = $field['js_options']['step'];
				echo '<td>';
				echo '<p style="width:300px"><input class="swiftframework-meta-slider mdl-slider mdl-js-slider" id="' . $field['id'] . '" name="swiftframework_meta[' . $field['id'] . ']" type="range" min="' . $min . '" max="' . $max . '" step="' . $step . '" value="' . ($meta ? $meta : $field['std']) . '" tabindex="0"></p>';
				echo '</td>';		        
				break;

			// Checkbox field
			case 'checkbox':

			    echo '<td>';		 
			    $val = '';

                if( $meta ) {
                    if( $meta == 'true' ) $val = ' checked';
                } else {
                    if( $field['std'] == 'true' ) $val = ' checked';
                }

                //echo '<input type="hidden" name="swiftframework_meta['. $field['id'] .']" value="off" />
                //<input type="checkbox" id="'. $field['id'] .'" name="swiftframework_meta['. $field['id'] .']" value="on"'. $val .' /> ';

                echo '<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="'. $field['id'] .'">
					<input type="hidden" name="swiftframework_meta['. $field['id'] .']" value="false" />
					<input class="mdl-checkbox__input" type="checkbox" id="'. $field['id'] .'" name="swiftframework_meta['. $field['id'] .']" value="true" '. $val .' /> 
				</label>';

			    echo '</td>';
			    break;

			// Datepicker field
			case 'datepicker':
				$default = $meta ? $meta : $field['std'];
				echo '<td>';
				echo '<input type="date" id="' . $field['id'] . '" name="swiftframework_meta[' . $field['id'] . ']" value="' . $default . '" class="swiftframework-meta-datepicker" />';
				echo '</td>';
				break;
		}
		
		// Close field row
		echo '</tr>';
	}
 	
 	// close table
	echo '</table>';
}


/**
 * Save meta box function
 */
function swiftframework_save_meta_box( $post_id ) {

	// Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	// Return if not valid
	if ( !isset($_POST['swiftframework_meta']) || !isset($_POST['swiftframework_meta_box_nonce']) || !wp_verify_nonce( $_POST['swiftframework_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return;
	}
	
	// Check user has rights to edit
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
 	
 	// Update post meta for each key=>val
	foreach( $_POST['swiftframework_meta'] as $key => $val ) {
		update_post_meta( $post_id, $key, $val );
	}

}
add_action( 'save_post', 'swiftframework_save_meta_box' );

/**
 * Get meta image ids function
 */
function swiftframework_meta_get_image_ids( $meta_value ) {
    $ids       = explode( ",", $meta_value );
    $return_array = array();
    foreach ( $ids as $id ) {
        if ( wp_get_attachment_image( $id ) ) {
            $return_array[] = $id;
        }
    }
    $ids = implode( ",", $return_array );
    return $ids;
}

/**
 * Get meta attached images function
 */
function swiftframework_meta_get_attached_images( $att_ids = array() ) {
    $output = '';
    $number = 1;
    foreach ( $att_ids as $th_id ) {
        $thumb_src = wp_get_attachment_image_src( $th_id, 'medium' );

        if ( $thumb_src ) {
            $thumb_src = $thumb_src[0];
            $output .= '
			<li class="added">
				<img rel="' . $th_id . '" src="' . $thumb_src . '" />
				<span class="swiftframework-img-order">' . $number . '</span>
				<div class="swiftframework-meta-remove-img"><a title="' . __( "Deselect", "swiftframework.io" ) . '" class="swiftframework-meta-delete-file" href="#">&times;</a>
				</div>
			</li>';
            $number++;
        }
    }
    return $output;
}
