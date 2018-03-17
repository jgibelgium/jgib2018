<?php
    /*
    *
    *	Swift Slider Post Type
    *	------------------------------------------------
    *	Swift Slider
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    /* SWIFT SLIDER CATEGORY
    ================================================== */
    function swift_slider_category_register() {

        $args = array(
            "label"             => __( 'Slide Categories', 'swift-framework-plugin' ),
            "singular_label"    => __( 'Slide Category', 'swift-framework-plugin' ),
            'public'            => true,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_nav_menus' => false,
            'exclude_from_search' => true,
            'args'              => array( 'orderby' => 'term_order' ),
            'rewrite'           => false,
            'query_var'         => true
        );
        register_taxonomy( 'swift-slider-category', 'swift-slider', $args );

    }

    add_action( 'init', 'swift_slider_category_register', 0 );
    


    /* SWIFT SLIDER POST TYPE
    ================================================== */
    function swift_slider_register() {

        $labels = array(
            'name'               => __( 'Swift Slider', 'swift-framework-plugin' ),
            'singular_name'      => __( 'Slide', 'swift-framework-plugin' ),
            'add_new'            => __( 'Add New Slide', 'slide', 'swift-framework-plugin' ),
            'add_new_item'       => __( 'Add New Slide', 'swift-framework-plugin' ),
            'edit_item'          => __( 'Edit Slide', 'swift-framework-plugin' ),
            'new_item'           => __( 'New Slide', 'swift-framework-plugin' ),
            'view_item'          => __( 'View Slide', 'swift-framework-plugin' ),
            'search_items'       => __( 'Search Slides', 'swift-framework-plugin' ),
            'not_found'          => __( 'No slides have been added yet', 'swift-framework-plugin' ),
            'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
            'parent_item_colon'  => ''
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'exclude_from_search' => true,
            'menu_icon'           => 'dashicons-format-image',
            'hierarchical'        => false,
            'rewrite'             => false,
            'supports'            => array( 'title', 'thumbnail' ),
            'has_archive'         => true,
            'taxonomies'          => array( 'swift-slider-category' )
        );

        register_post_type( 'swift-slider', $args );
    }

    add_action( 'init', 'swift_slider_register', 0 );


    /* SWIFT SLIDER POST TYPE COLUMNS
    ================================================== */
    function swift_slider_edit_columns( $columns ) {
        $columns = array(
            "cb"                    => "<input type=\"checkbox\" />",
            "thumbnail"             => "",
            "title"                 => __( "Slide", 'swift-framework-plugin' ),
            "swift-slider-category" => __( "Slide Categories", 'swift-framework-plugin' ),
            "bg-type"               => __( "Background Type", 'swift-framework-plugin' ),
            "caption-title"         => __( "Caption Title", 'swift-framework-plugin' ),
        );

        return $columns;
    }

    add_filter( "manage_edit-swift-slider_columns", "swift_slider_edit_columns" );

    /* CUSTOM POST TYPE COLUMNS
    ================================================== */
    function swift_slider_posts_custom_columns( $column ) {
        global $post;
        if ( $post->post_type != 'swift-slider' ) {
            return;
        }
        switch ( $column ) {
            case "thumbnail":
                the_post_thumbnail( 'thumbnail' );
                break;
            case "bg-type":
                echo get_post_meta( $post->ID, 'ss_bg_type', true );
                break;
            case "caption-title":
                echo get_post_meta( $post->ID, 'ss_caption_title', true );
                break;
            case "swift-slider-category":
                echo get_the_term_list( $post->ID, 'swift-slider-category', '', ', ', '' );
                break;
        }
    }
    add_action( "manage_posts_custom_column", "swift_slider_posts_custom_columns" );


?>