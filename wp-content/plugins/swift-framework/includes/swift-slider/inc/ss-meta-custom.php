<?php

    /*
    *
    *	Swift Slider Meta Boxes
    *	------------------------------------------------
    *	Swift Slider
    * 	Copyright Swift Ideas 2017 - http://www.swiftideas.com
    *
    */

    function swiftslider_metabox() {

    	$prefix = 'ss_';

		/**
		 * Background Meta
		 */
	    $swiftslider_background_meta_box = array(
			'id' => 'ss_background',
			'title' => __('Slide Background', 'swiftframework'),
			'description' => __('Here you can configure the basic slider options.', 'swiftframework'),
			'post_type' => array( 'swift-slider' ),
			'context'  => 'normal',
        	'priority' => 'high',
			'fields' => array(
				// BACKGROUND TYPE
		        array(
		            'name'     => __( 'Background type', 'swift-framework-plugin' ),
		            'id'       => "{$prefix}bg_type",
		            'type'     => 'select',
		            'options'  => array(
		                'color' => 'Color',
		                'image' => 'Image',
		                'video' => 'Video'
		            ),
		            'multiple' => false,
		            'std'      => 'image',
		            'desc'     => __( 'Choose what the background for the slide will be.', 'swift-framework-plugin' )
		        ),
		        // PAGE TITLE BACKGROUND COLOR
		        array(
		            'name' => __( 'Background Color', 'swift-framework-plugin' ),
		            'id'   => $prefix . 'bg_color',
		            'desc' => __( "Set a background color for this slide. If an image is uploaded, this will serve as the overlay color as well.", 'swift-framework-plugin' ),
		            'type' => 'color',
		            'std'  => '',
		        ),
		        // Overlay Opacity Value
		        array(
		            'name'       => __( 'Overlay Opacity', 'swift-framework-plugin' ),
		            'id'         => $prefix . 'bg_opacity',
		            'desc'       => __( 'Set the opacity level of the overlay. This will lighten or darken the image depening on the color selected.', 'swift-framework-plugin' ),
		            'clone'      => false,
		            'type'       => 'slider',
		            'prefix'     => '',
		            'js_options' => array(
		                'min'  => 0,
		                'max'  => 100,
		                'step' => 1,
		            ),
		        ),
		        // BACKGROUND IMAGE
		        array(
		            'name'             => __( 'Background image', 'swift-framework-plugin' ),
		            'desc'             => __( 'The image that will be used as the background image. On video slides, this will be shown as the fallback for browsers/devices where videos are not supported.', 'swift-framework-plugin' ),
		            'id'               => "{$prefix}background_image",
		            'type' 			   => 'image',
		            'max_file_uploads' => 1
		        ),
		        // BACKGROUND IMAGE Size
		        array(
		            'name'     => __( 'Background image size', 'swift-framework-plugin' ),
		            'id'       => "{$prefix}background_size",
		            'type'     => 'select',
		            'options'  => array(
		                'cover'    => __( 'Cover', 'swift-framework-plugin' ),
		                'auto' => __( 'Auto', 'swift-framework-plugin' ),
		            ),
		            'multiple' => false,
		            'std'      => 'cover',
		            'desc'     => __( 'Choose the background image size for the slide background image.', 'swift-framework-plugin' )
		        ),
		        // BACKGROUND IMAGE ALIGNMENT
		        array(
		            'name'     => __( 'Background image vertical align', 'swift-framework-plugin' ),
		            'id'       => "{$prefix}background_valign",
		            'type'     => 'select',
		            'options'  => array(
		                'top'    => __( 'Top', 'swift-framework-plugin' ),
		                'center' => __( 'Center', 'swift-framework-plugin' ),
		                'bottom' => __( 'Bottom', 'swift-framework-plugin' ),
		            ),
		            'multiple' => false,
		            'std'      => 'center',
		            'desc'     => __( 'Choose the vertical align for the slide background image.', 'swift-framework-plugin' )
		        ),
		        // BACKGROUND IMAGE ALIGNMENT
		        array(
		            'name'     => __( 'Background image horizontal align', 'swift-framework-plugin' ),
		            'id'       => "{$prefix}background_halign",
		            'type'     => 'select',
		            'options'  => array(
		                'left'    => __( 'Left', 'swift-framework-plugin' ),
		                'center' => __( 'Center', 'swift-framework-plugin' ),
		                'right' => __( 'Right', 'swift-framework-plugin' ),
		            ),
		            'multiple' => false,
		            'std'      => 'center',
		            'desc'     => __( 'Choose the horizontal align for the slide background image.', 'swift-framework-plugin' )
		        ),
		        // BACKGROUND IMAGE ALIGNMENT
		        array(
		            'name'     => __( 'Mobile Background image horizontal align', 'swift-framework-plugin' ),
		            'id'       => "{$prefix}mobile_background_halign",
		            'type'     => 'select',
		            'options'  => array(
		                'left'    => __( 'Left', 'swift-framework-plugin' ),
		                'center' => __( 'Center', 'swift-framework-plugin' ),
		                'right' => __( 'Right', 'swift-framework-plugin' ),
		            ),
		            'multiple' => false,
		            'std'      => 'center',
		            'desc'     => __( 'Choose the horizontal align for the slide background image on Mobile devices ( landscape / portrait < 768 ).', 'swift-framework-plugin' )
		        ),
		        // SLIDE STYLING
		        array(
		            'name'     => __( 'Slide Styling', 'swift-framework-plugin' ),
		            'id'       => "{$prefix}slide_style",
		            'type'     => 'select',
		            'options'  => array(
		                'light' => __( 'Light', 'swift-framework-plugin' ),
		                'dark'  => __( 'Dark', 'swift-framework-plugin' ),
		            ),
		            'multiple' => false,
		            'std'      => 'light',
		            'desc'     => __( 'Choose light or dark styling for the caption, and controls. NOTE: When you are using a naked header, this will change the logo to be light/dark to match the controls when the slider is visible.', 'swift-framework-plugin' )
		        ),
		        // SLIDE LINK
		        array(
		            'name'     => __( 'Slide link setup', 'swift-framework-plugin' ),
		            'id'       => "{$prefix}slide_link",
		            'type'     => 'select',
		            'options'  => array(
		                'no-link'    => __( 'No Link', 'swift-framework-plugin' ),
		                'link-nw' => __( 'Link (New Window)', 'swift-framework-plugin' ),
		                'link-sw' => __( 'Link (Same Window)', 'swift-framework-plugin' ),
		            ),
		            'multiple' => false,
		            'std'      => 'no-link',
		            'desc'     => __( 'Add a link which makes the whole slide clickable.', 'swift-framework-plugin' )
		        ),
		        // SLIDE LINK URL
		        array(
		            'name'     => __( 'Slide link URL', 'swift-framework-plugin' ),
		            'id'       => "{$prefix}slide_link_url",
		            'type'  => 'text',
		            'val'      => '',
		            'desc'     => __( 'The URL for the slide link. NOTE: make sure to include http://', 'swift-framework-plugin' )
		        ),
			)
		);
		$callback = create_function( '$post,$swiftslider_background_meta_box', 'swiftframework_create_meta_box( $post, $swiftslider_background_meta_box["args"] );' );
		add_meta_box( $swiftslider_background_meta_box['id'], $swiftslider_background_meta_box['title'], $callback, $swiftslider_background_meta_box['post_type'], $swiftslider_background_meta_box['context'], $swiftslider_background_meta_box['priority'], $swiftslider_background_meta_box );
		
		/**
		 * Background Video Meta
		 */
	    $swiftslider_background_video_meta_box = array(
			'id' => 'ss_background_video',
			'title' => __('Slide Background Video', 'swiftframework'),
			'description' => __('Here you can configure the background video options.', 'swiftframework'),
			'post_type' => array( 'swift-slider' ),
			'context'  => 'normal',
        	'priority' => 'low',
			'fields' => array(
				// BACKGROUND VIDEO MP4
	            array(
	                'name'             => __( 'Background Video .mp4', 'swift-framework-plugin' ),
	                'id'               => $prefix . 'background_video_mp4',
	                'desc'             => __( 'Upload/select the video mp4 file for the background.', 'swift-framework-plugin' ),
	                'type' => 'media',
					'media_type' => 'video',
	            ),
	            array(
	                'name'  => __( 'Background Video .mp4 URL', 'swift-framework-plugin' ),
	                'id'    => $prefix . 'background_video_mp4_url',
	                'desc'  => __( 'If hosted elsewhere, provide the video mp4 url for the background.', 'swift-framework-plugin' ),
	                'clone' => false,
	                'type'  => 'text',
	                'std'   => '',
	            ),
	            // BACKGROUND VIDEO WEBM
	            array(
	                'name'             => __( 'Background Video .webm', 'swift-framework-plugin' ),
	                'id'               => $prefix . 'background_video_webm',
	                'desc'             => __( 'Upload/select the video .webm file for the background.', 'swift-framework-plugin' ),
	                'type' => 'media',
					'media_type' => 'video',
	            ),
	            array(
	                'name'  => __( 'Background Video .webm URL', 'swift-framework-plugin' ),
	                'id'    => $prefix . 'background_video_webm_url',
	                'desc'  => __( 'If hosted elsewhere, provide the video .webm url for the background.', 'swift-framework-plugin' ),
	                'clone' => false,
	                'type'  => 'text',
	                'std'   => '',
	            ),
	            // BACKGROUND VIDEO OGG
	            array(
	                'name'             => __( 'Background Video .ogg', 'swift-framework-plugin' ),
	                'id'               => $prefix . 'background_video_ogg',
	                'desc'             => __( 'Upload/select the video ogg file for the background.', 'swift-framework-plugin' ),
	                'type' => 'media',
					'media_type' => 'video',
	            ),
	            array(
	                'name'  => __( 'Background Video .ogg URL', 'swift-framework-plugin' ),
	                'id'    => $prefix . 'background_video_ogg_url',
	                'desc'  => __( 'If hosted elsewhere, provide the video .ogg url for the background.', 'swift-framework-plugin' ),
	                'clone' => false,
	                'type'  => 'text',
	                'std'   => '',
	            ),
	            // VIDEO LOOP
	            array(
	                'name'     => __( 'Video Loop', 'swift-framework-plugin' ),
	                'id'       => "{$prefix}video_loop",
	                'type'     => 'select',
	                'options'  => array(
	                    'loop' => __( 'Loop', 'swift-framework-plugin' ),
	                    ''     => __( 'Single play', 'swift-framework-plugin' ),
	                ),
	                'multiple' => false,
	                'std'      => 'loop',
	                'desc'     => __( 'Choose if you would like the slide background video to play once, or loop.', 'swift-framework-plugin' )
	            ),
	            // VIDEO MUTE
	            array(
	                'name'     => __( 'Video Mute', 'swift-framework-plugin' ),
	                'id'       => "{$prefix}video_mute",
	                'type'     => 'select',
	                'options'  => array(
	                    'muted' => __( 'Mute', 'swift-framework-plugin' ),
	                    ''      => __( 'Volume', 'swift-framework-plugin' ),
	                ),
	                'multiple' => false,
	                'std'      => 'loop',
	                'desc'     => __( 'Choose if you would like the slide background video to be muted, or not.', 'swift-framework-plugin' )
	            ),
	            // VIDEO OVERLAY
	            array(
	                'name'     => __( 'Video Overlay', 'swift-framework-plugin' ),
	                'id'       => "{$prefix}video_overlay",
	                'type'     => 'select',
	                'options'  => array(
	                    'none'         => __( 'None', 'swift-framework-plugin' ),
	                    'lightgrid'    => __( 'Light Grid', 'swift-framework-plugin' ),
	                    'darkgrid'     => __( 'Dark Grid', 'swift-framework-plugin' ),
	                    'lightgridfat' => __( 'Light Grid (Fat)', 'swift-framework-plugin' ),
	                    'darkgridfat'  => __( 'Dark Grid (Fat)', 'swift-framework-plugin' ),
	                    'diaglight'    => __( 'Light Diagonal', 'swift-framework-plugin' ),
	                    'diagdark'     => __( 'Dark Diagonal', 'swift-framework-plugin' ),
	                    'horizlight'   => __( 'Light Horizontal', 'swift-framework-plugin' ),
	                    'horizdark'    => __( 'Dark Horizontal', 'swift-framework-plugin' ),
	                    'vertlight'    => __( 'Light Vertical', 'swift-framework-plugin' ),
	                    'vertdark'     => __( 'Dark Vertical', 'swift-framework-plugin' ),
	                ),
	                'multiple' => false,
	                'std'      => 'none',
	                'desc'     => __( 'Choose if you would like to show an overlay style over the video.', 'swift-framework-plugin' )
	            ),
			)
		);
		$callback = create_function( '$post,$swiftslider_background_video_meta_box', 'swiftframework_create_meta_box( $post, $swiftslider_background_video_meta_box["args"] );' );
		add_meta_box( $swiftslider_background_video_meta_box['id'], $swiftslider_background_video_meta_box['title'], $callback, $swiftslider_background_video_meta_box['post_type'], $swiftslider_background_video_meta_box['context'], $swiftslider_background_video_meta_box['priority'], $swiftslider_background_video_meta_box );


		/**
		 * Slide Content Meta
		 */
	    $swiftslider_slide_content_meta_box = array(
			'id' => 'ss_content',
			'title' => __('Slide Content', 'swiftframework'),
			'description' => __('Here you can configure the slide content video options.', 'swiftframework'),
			'post_type' => array( 'swift-slider' ),
			'context'  => 'normal',
        	'priority' => 'low',
			'fields' => array(
				// CAPTION TITLE
	            array(
	                'name'  => __( 'Caption Title', 'swift-framework-plugin' ),
	                'id'    => $prefix . 'caption_title',
	                'desc'  => __( 'Enter the text to show as the caption title.', 'swift-framework-plugin' ),
	                'clone' => false,
	                'type'  => 'text',
	                'std'   => '',
	            ),
	            // CAPTION TEXT
	            array(
	                'name' => __( 'Caption Text', 'swift-framework-plugin' ),
	                'id'   => $prefix . 'caption_text',
	                'desc' => __( 'Enter the caption text that shows below the title.', 'swift-framework-plugin' ),
	                'type' => 'wysiwyg',
	                'std'  => '',
	            ),
	            // CAPTION HORIZONTAL ALIGN
	            array(
	                'name'     => __( 'Caption Horizontal Align', 'swift-framework-plugin' ),
	                'id'       => "{$prefix}caption_x",
	                'type'     => 'select',
	                'options'  => array(
	                    'left'   => __( 'Left', 'swift-framework-plugin' ),
	                    'center' => __( 'Center', 'swift-framework-plugin' ),
	                    'right'  => __( 'Right', 'swift-framework-plugin' ),
	                ),
	                'multiple' => false,
	                'std'      => 'center',
	                'desc'     => __( 'Choose the horizontal align for the caption.', 'swift-framework-plugin' )
	            ),
	            // CAPTION VERTICAL ALIGN
	            array(
	                'name'     => __( 'Caption Vertical Align', 'swift-framework-plugin' ),
	                'id'       => "{$prefix}caption_y",
	                'type'     => 'select',
	                'options'  => array(
	                    'top'    => __( 'Top', 'swift-framework-plugin' ),
	                    'middle' => __( 'Middle', 'swift-framework-plugin' ),
	                    'bottom' => __( 'Bottom', 'swift-framework-plugin' ),
	                ),
	                'multiple' => false,
	                'std'      => 'middle',
	                'desc'     => __( 'Choose the vertical align for the caption.', 'swift-framework-plugin' )
	            ),
	            // CAPTION TITLE SIZE
	            array(
	                'name'     => __( 'Caption Title Size', 'swift-framework-plugin' ),
	                'id'       => "{$prefix}caption_size",
	                'type'     => 'select',
	                'options'  => array(
	                    'standard' => __( 'Standard', 'swift-framework-plugin' ),
	                    'smaller'  => __( 'Smaller', 'swift-framework-plugin' ),
	                ),
	                'multiple' => false,
	                'std'      => 'standard',
	                'desc'     => __( 'Choose the size for the caption title.', 'swift-framework-plugin' )
	            ),
			)
		);
		$callback = create_function( '$post,$swiftslider_slide_content_meta_box', 'swiftframework_create_meta_box( $post, $swiftslider_slide_content_meta_box["args"] );' );
		add_meta_box( $swiftslider_slide_content_meta_box['id'], $swiftslider_slide_content_meta_box['title'], $callback, $swiftslider_slide_content_meta_box['post_type'], $swiftslider_slide_content_meta_box['context'], $swiftslider_slide_content_meta_box['priority'], $swiftslider_slide_content_meta_box );

	}
	add_action('add_meta_boxes', 'swiftslider_metabox');