<?php
	
	/*
	*
	*	Joyn Functions
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
	*
	*	VARIABLE DEFINITIONS
	*	PLUGIN INCLUDES
	*	THEME UPDATER
	*	THEME SUPPORT
	*	THUMBNAIL SIZES
	*	CONTENT WIDTH
	*	LOAD THEME LANGUAGE
	*	sf_custom_content_functions()
	*	sf_include_framework()
	*	sf_enqueue_styles()
	*	sf_enqueue_scripts()
	*	sf_load_custom_scripts()
	*	sf_admin_scripts()
	*	sf_layerslider_overrides()
	*
	*/
	
	
	/* VARIABLE DEFINITIONS
	================================================== */ 
	define('SF_TEMPLATE_PATH', get_template_directory());
	define('SF_INCLUDES_PATH', SF_TEMPLATE_PATH . '/includes');
	define('SF_FRAMEWORK_PATH', SF_TEMPLATE_PATH . '/swift-framework');
	define('SF_WIDGETS_PATH', SF_INCLUDES_PATH . '/widgets');
	define('SF_LOCAL_PATH', get_template_directory_uri());
	
	
	/* PLUGIN INCLUDES
	================================================== */
	require_once(SF_INCLUDES_PATH . '/plugins/aq_resizer.php');
	include_once(SF_INCLUDES_PATH . '/plugin-includes.php');
	require_once(SF_INCLUDES_PATH . '/theme_update_check.php');
	$JoynUpdateChecker = new ThemeUpdateChecker(
	    'joyn',
	    'https://kernl.us/api/v1/theme-updates/5668e4950a25612471e649fa/'
	);
		

	/* THEME SETUP
	================================================== */
	if (!function_exists('sf_joyn_setup')) {
		function sf_joyn_setup() {
		
			/* SF THEME OPTION CHECK
			================================================== */
			if ( get_option( 'sf_theme' ) == false ) {
				update_option( 'sf_theme', 'joyn' );
			}
			
			/* THEME SUPPORT
			================================================== */	
			add_theme_support( 'structured-post-formats', array('audio', 'gallery', 'image', 'link', 'video') );
			add_theme_support( 'post-formats', array('aside', 'chat', 'quote', 'status') );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
			
			add_theme_support( 'appthemer-crowdfunding', array(
				'campaign-edit'           	=> true,
				'campaign-featured-image' 	=> true,
				'campaign-video'          	=> true,
				'campaign-widget'        	=> true,
				'campaign-category'			=> true,
				'campaign-tag'     			=> true,
				'campaign-type'     		=> true,
				'anonymous-backers'			=> true
			) );
			add_theme_support( 'swiftframework', array(
				'swift-smartscript'			=> false,
				'slideout-menu'				=> false,
				'page-heading-woocommerce'	=> true,
				'pagination-fullscreen'		=> true,
				'bordered-button'			=> true,
				'3drotate-button'			=> true,
				'rounded-button'			=> false,
				'product-inner-heading'		=> false,
				'product-summary-tabs'		=> true,
				'product-layout-opts'		=> false,
				'mobile-shop-filters' 		=> false,
				'mobile-logo-override'		=> true,
				'product-multi-masonry'		=> false,
				'super-search-config'		=> false,
				'advanced-row-styling'		=> false,
				'mobile-shop-filters'		=> false,
				'gizmo-icon-font'			=> true,
				'icon-mind-font'			=> false,
				'menu-new-badge'			=> false,
				'advanced-map-styles'		=> false,
				'hamburger-css' 			=> false,
				'pushnav-menu'				=> false,
				'split-nav-menu'			=> false,
				'max-mega-menu'				=> false,
				'page-heading-woo-description' => false,
				'header-aux-modals'			=> false
			) );
			
			/* THUMBNAIL SIZES
			================================================== */  	
			set_post_thumbnail_size( 220, 150, true);
			add_image_size( 'thumb-square', 250, 250, true);
			add_image_size( 'thumb-image', 600, 450, true);
			add_image_size( 'large-square', 1200, 1200, true);
			add_image_size( 'full-width-image-gallery', 1280, 720, true);
			
			/* CONTENT WIDTH
			================================================== */
			if ( ! isset( $content_width ) ) $content_width = 1140;
			
			/* LOAD THEME LANGUAGE
			================================================== */
			load_theme_textdomain('swiftframework', SF_TEMPLATE_PATH.'/language');
			
		}
		add_action( 'after_setup_theme', 'sf_joyn_setup' );
	}
		
	
	/* THEME FRAMEWORK FUNCTIONS
	================================================== */
	include_once( SF_FRAMEWORK_PATH . '/core/sf-sidebars.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-twitter.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-flickr.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-instagram.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-video.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-posts.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-portfolio.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-portfolio-grid.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-advertgrid.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-infocus.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-comments.php' );
	include_once( SF_FRAMEWORK_PATH . '/widgets/widget-mostloved.php' );
	
	require(SF_INCLUDES_PATH . '/meta-box/meta-box.php');
	include_once(SF_INCLUDES_PATH . '/meta-boxes.php');
	
	if (!function_exists('sf_include_framework')) {
		function sf_include_framework() {
			require_once(SF_INCLUDES_PATH . '/sf-theme-functions.php');
			require_once(SF_INCLUDES_PATH . '/sf-customizer-options.php');
			include_once(SF_INCLUDES_PATH . '/sf-custom-styles.php');
			include_once(SF_INCLUDES_PATH . '/sf-styleswitcher/sf-styleswitcher.php');
			require_once(SF_INCLUDES_PATH . '/overrides/sf-spb-overrides.php');
			require_once(SF_FRAMEWORK_PATH . '/swift-framework.php');			
		}
		add_action('init', 'sf_include_framework', 5);
	}
	
	
	/* THEME OPTIONS FRAMEWORK
	================================================== */ 
	require_once(SF_INCLUDES_PATH . '/sf-colour-scheme.php');
	if (!function_exists('sf_include_theme_options')) {
		function sf_include_theme_options() {
			if (!class_exists( 'ReduxFramework' )) {
			    require_once( SF_INCLUDES_PATH . '/options/framework.php' );
			}
			require_once( SF_INCLUDES_PATH . '/option-extensions/loader.php' );
			require_once( SF_INCLUDES_PATH . '/sf-options.php' );
			global $sf_joyn_options, $sf_options;
			$sf_options = $sf_joyn_options;
		}
		add_action('init', 'sf_include_theme_options', 10);
	}
	
	
	/* THEME OPTIONS VAR RETRIEVAL
	================================================== */
	if (!function_exists('sf_get_theme_opts')) {
		function sf_get_theme_opts() {
			global $sf_joyn_options;
			return $sf_joyn_options;
		}
	}
	
	
	/* LOVE IT INCLUDE
	================================================== */ 
	if (!function_exists('sf_love_it_include')) {
		function sf_love_it_include() {
			global $sf_options;
			$disable_loveit = false;
			if (isset($sf_options['disable_loveit'])) {
			$disable_loveit = $sf_options['disable_loveit'];
			}
			
			if (!$disable_loveit) {
			include_once(SF_INCLUDES_PATH . '/plugins/love-it-pro/love-it-pro.php');
			}
		}
		add_action('init', 'sf_love_it_include', 20);
	}
	
	
	/* LOAD STYLESHEETS
	================================================== */
	if (!function_exists('sf_enqueue_styles')) {
		function sf_enqueue_styles() {  
			
			global $sf_options;
			$enable_responsive = $sf_options['enable_responsive'];		
			$enable_rtl = $sf_options['enable_rtl'];
			
		    wp_register_style('bootstrap', SF_LOCAL_PATH . '/css/bootstrap.min.css', array(), NULL, 'all');
		    wp_register_style('fontawesome', SF_LOCAL_PATH .'/css/font-awesome.min.css', array(), NULL, 'all');
		    wp_register_style('ssgizmo', SF_LOCAL_PATH .'/css/ss-gizmo.css', array(), NULL, 'all');
		    wp_register_style('sf-main', get_stylesheet_directory_uri() . '/style.css', array(), NULL, 'all'); 
		    wp_register_style('sf-rtl', SF_LOCAL_PATH . '/rtl.css', array(), NULL, 'all');
		    wp_register_style('sf-woocommerce', SF_LOCAL_PATH . '/css/sf-woocommerce.css', array(), NULL, 'screen');
		    wp_register_style('sf-responsive', SF_LOCAL_PATH . '/css/responsive.css', array(), NULL, 'screen');
			
		    wp_enqueue_style('bootstrap');  
		    wp_enqueue_style('ssgizmo');
		    wp_enqueue_style('fontawesome'); 
		    wp_enqueue_style('sf-main');  

		    if (sf_woocommerce_activated()) {
		    	wp_enqueue_style('sf-woocommerce'); 
		    }
		    
		    if (is_rtl() || $enable_rtl || isset($_GET['RTL'])) {
		    	wp_enqueue_style('sf-rtl');
		    }
		    
		    if ($enable_responsive) {
		    	wp_enqueue_style('sf-responsive');  
		    }
		
		}		
		add_action('wp_enqueue_scripts', 'sf_enqueue_styles', 99);  
	}
	
	
	/* LOAD FRONTEND SCRIPTS
	================================================== */
	if (!function_exists('sf_enqueue_scripts')) {
		function sf_enqueue_scripts() {
			
			// Variables
			global $sf_options;
		    $enable_rtl = $sf_options['enable_rtl'];
		    $enable_min_scripts = $sf_options['enable_min_scripts'];
			$post_type = get_query_var('post_type');
			$gmaps_api_key 		= get_option('sf_gmaps_api_key');
			
		    // Register Scripts
		    wp_register_script('bootstrap-js', SF_LOCAL_PATH . '/js/bootstrap.min.js', 'jquery', NULL, TRUE);
		    wp_register_script('flexslider', SF_LOCAL_PATH . '/js/jquery.flexslider-min.js', 'jquery', NULL, TRUE);
		    wp_register_script('flexslider-rtl', SF_LOCAL_PATH . '/js/jquery.flexslider-rtl-min.js', 'jquery', NULL, TRUE);
		    wp_register_script('isotope', SF_LOCAL_PATH . '/js/jquery.isotope.min.js', 'jquery', NULL, TRUE);
		    wp_register_script('imagesLoaded', SF_LOCAL_PATH . '/js/imagesloaded.js', 'jquery', NULL, TRUE);
		    wp_register_script('owlcarousel', SF_LOCAL_PATH . '/js/owl.carousel.min.js', 'jquery', NULL, TRUE); 
			wp_register_script('jquery-ui', SF_LOCAL_PATH . '/js/jquery-ui-1.11.4.custom.min.js', 'jquery', NULL, TRUE);
			wp_register_script('ilightbox', SF_LOCAL_PATH . '/js/ilightbox.min.js', 'jquery', NULL, TRUE);
		    wp_register_script('google-maps', '//maps.google.com/maps/api/js?key=' . $gmaps_api_key, 'jquery', NULL, TRUE);
		    wp_register_script('elevatezoom', SF_LOCAL_PATH . '/js/jquery.elevateZoom.min.js', 'jquery', NULL, TRUE);
		    wp_register_script('infinite-scroll',  SF_LOCAL_PATH . '/js/jquery.infinitescroll.min.js', 'jquery', NULL, TRUE);
		    wp_register_script('sf-theme-scripts', SF_LOCAL_PATH . '/js/theme-scripts.js', 'jquery', NULL, TRUE);
		    wp_register_script('sf-theme-scripts-min', SF_LOCAL_PATH . '/js/sf-scripts.min.js', 'jquery', NULL, TRUE);
		    wp_register_script('sf-functions', SF_LOCAL_PATH . '/js/functions.js', 'jquery', NULL, TRUE);
		    wp_register_script('sf-functions-min', SF_LOCAL_PATH . '/js/functions.min.js', 'jquery', NULL, TRUE);
			
			// jQuery
		    wp_enqueue_script('jquery');
		    		   	
		    if ( !is_admin() ) {
		    	
		    	// Theme Scripts
		    	if ($enable_min_scripts) {
		    		wp_enqueue_script('sf-theme-scripts-min');
		    		if ( !is_singular('tribe_events') && $post_type != 'tribe_events' && !is_singular('tribe_venue') && $post_type != 'tribe_venue' ) {
		    			wp_enqueue_script('google-maps');
		    		}
		    		wp_enqueue_script('sf-functions-min');
		    	} else {
		    		wp_enqueue_script('bootstrap-js');
		    		wp_enqueue_script('jquery-ui');
		    		
		    		if ( is_rtl() || $enable_rtl || isset($_GET['RTL']) ) {
		    			wp_enqueue_script('flexslider-rtl');
		    		} else {
		    			wp_enqueue_script('flexslider');
		    		}
		    		
		    		wp_enqueue_script('owlcarousel');
		    		wp_enqueue_script('sf-theme-scripts');
		    		wp_enqueue_script('ilightbox');
		    		
		    		if ( !is_singular('tribe_events') && $post_type != 'tribe_events' && !is_singular('tribe_venue') && $post_type != 'tribe_venue' ) {
		    			wp_enqueue_script('google-maps');
		    		}
		    		
		    		wp_enqueue_script('isotope');
		    		wp_enqueue_script('imagesLoaded');
		    		wp_enqueue_script('infinite-scroll');
		    		
		    		if ( $sf_options['enable_product_zoom'] ) {
		    			wp_enqueue_script('elevatezoom');
		    		}
		    		
		    		wp_enqueue_script('sf-functions');	
		    	}
		    	
		    	// Comments reply
				if ( is_singular() && comments_open() ) {
					wp_enqueue_script('comment-reply');
				}
		    }
		}
		add_action('wp_enqueue_scripts', 'sf_enqueue_scripts');
	}
	
	/* LOAD BACKEND SCRIPTS
	================================================== */
	function sf_admin_scripts() {
	    wp_register_script('admin-functions', get_template_directory_uri() . '/js/sf-admin.js', 'jquery', '1.0', TRUE);
		wp_enqueue_script('admin-functions');
	}
	add_action('admin_enqueue_scripts', 'sf_admin_scripts');
	
	/* CHECK THEME FEATURE SUPPORT
    ================================================== */
    if ( !function_exists( 'sf_theme_supports' ) ) {
        function sf_theme_supports( $feature ) {
        	$supports = get_theme_support( 'swiftframework' );
        	$supports = $supports[0];
    		if ( !isset( $supports[ $feature ] ) || $supports[ $feature ] == "") {
    			return false;
    		} else {
        		return isset( $supports[ $feature ] );
        	}
        }
    }	
?>