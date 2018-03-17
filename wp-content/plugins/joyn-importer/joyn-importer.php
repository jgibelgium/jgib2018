<?php
/*
Plugin Name: Joyn Demo Content
Description: Replicate any of the Joyn example sites in just a few clicks!
Author: Swift Ideas
Author URI: http://www.swiftideas.net
Version: 1.0
Text Domain: joyn-importer
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


// Updater
require_once('wp-updates-plugin.php');
new WPUpdatesPluginUpdater_677( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));


//Message displayed when Joyn theme is not active
function admin_notice_message(){    		
		echo '<div class="updated"><p>Joyn theme must be installed to use this plugin. The import functionalities are disabled.</p></div>';		
}

function swift_importer_menu_page(){
    add_menu_page( 'Joyn Demo Content', 'Joyn Demos', 'manage_options', 'admin.php?import=swiftdemo', '', plugin_dir_url(__FILE__).'/assets/images/logo.png');
}

add_action( 'admin_menu', 'swift_importer_menu_page' );

if ( ! defined( 'WP_LOAD_IMPORTERS' ) )
	return;

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( ! class_exists( 'WP_Importer' ) ) {
	$class_Swift_Importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_Swift_Importer ) )
		require $class_Swift_Importer;
}

// include Widget data class
if (!class_exists( 'Swift_Widget_Data' )) {
	require dirname( __FILE__ ) . '/class-widget-data.php';
}
// include WXR file parsers
require dirname( __FILE__ ) . '/parsers.php';

/**
 * WordPress Importer class for managing the import process of a WXR file
 *
 * @package WordPress
 * @subpackage Importer
 */
if ( class_exists( 'WP_Importer' ) ) {
class Swift_Import extends WP_Importer {
	var $max_wxr_version = 1.2; // max. supported WXR version

	var $id; // WXR attachment ID

	// information to import from WXR file
	var $version;
	var $authors = array();
	var $posts = array();
	var $terms = array();
	var $categories = array();
	var $tags = array();
	var $base_url = '';
	// mappings from old information to new
	var $processed_authors = array();
	var $author_mapping = array();
	var $processed_terms = array();
	var $processed_posts = array();
	var $post_orphans = array();
	var $processed_menu_items = array();
	var $menu_item_orphans = array();
	var $missing_menu_items = array();
	var $fetch_attachments = false;
	var $url_remap = array();
	var $featured_images = array();
	var $firsttime = true;
	var $import_menu_itens = true;
	var $demofiles = array();
	
	function Swift_Import() { /* nothing */  }
	
	function initialize_data() {
		
		$plugin_path = dirname(__FILE__);
		
		// Demos
		$demofiles['id-0']           = 'demo';
		$demofiles['title-0']        = 'Main Demo';
		$demofiles['filter-0']       = 'multi-purpose';
		$demofiles['previewlink-0']  = 'http://joyn.swiftideas.com/';
		$demofiles['content-0']      = $plugin_path .'/demofiles/joyn-demo-content.xml.gz';
		$demofiles['colors-0']       = $plugin_path .'/demofiles/joyn-demo-colors.json';
		$demofiles['themeoptions-0'] = $plugin_path .'/demofiles/joyn-demo-options.json';
		$demofiles['widgets-0']      = $plugin_path .'/demofiles/joyn-demo-widgets.json'; 

		return $demofiles;
	}
	
	/**
	 * Registered callback function for the WordPress Importer
	 *
	 * Manages the three separate stages of the WXR import process
	 */
	function dispatch() {
		
		$this->header();
		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
		switch ( $step ) {
			case 0:
				$this->greet();
				break;
			case 1: 
				$this->import();
				break;
		}

		$this->footer();
	}

	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import() {
		global $plugin_path, $demoid;
		$demofiles_data = $this->initialize_data();
		$plugin_path = dirname(__FILE__);
		$import_theme_options = false;
		$import_color_options = false;
		$import_demo_content = false;
		$import_demo_widgets = false;
				
		//check if it's' to import the Demo Content
		if ( isset($_GET['demoid']) ){
			$demoid = $_GET['demoid'];
		}	 
		
		//check if it's' to import the Demo Content
		if ( isset($_GET['democontent']) && $_GET['democontent'] == 'yes' ){
			$import_demo_content = true;
		}			
		
		//check if it's' to import the Theme options
		if ( isset($_GET['themeopt']) && $_GET['themeopt'] == 'yes' ){
			$import_theme_options = true;
		}
		
		//check if it's' to import the Color options	
		if ( isset($_GET['coloropt']) && $_GET['coloropt'] == 'yes' ){
			$import_color_options = true;
		}	
		
		//check if it's' to import the Widget options	
		if ( isset($_GET['widgetopt']) && $_GET['widgetopt'] == 'yes' ){
			$import_demo_widgets = true;
		}
			
		//add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );
		$demo_id_text = 'content-'.$demoid;  
		$file = $demofiles_data[$demo_id_text];
		?>
		
		<div class="note-wrap">
			<div class="spinnermessage">
				<h3>The demo content is importing, please wait.</h3> 
				<p>Note: the page may refresh at intervals, this is normal.</p>
			</div>
			<div class="importspinner">  
				<div class="bounce1"></div> 
				<div class="bounce2"></div> 
				<div class="bounce3"></div> 
			</div>
			<div class="import-success-msg"><h3>The demo content has imported successfully, enjoy!</h3><a href="<?php echo get_bloginfo('url'); ?>">Visit site</a></div>
		</div>

		<?php	
		//Import the demo content if the option is enabled
		if( $import_demo_content ){
			$this->import_start( $file );
			//$this->get_author_mapping();
			//wp_suspend_cache_invalidation( true );
			$this->process_categories();
			$this->process_tags();
			$this->process_terms();
			$this->process_posts();
			//wp_suspend_cache_invalidation( false );
			$this->assign_menus_to_locations($demoid);
			// update incorrect/missing information in the DB
			$this->backfill_parents();
			$this->backfill_attachment_urls();
			$this->remap_featured_images();
			//$this->import_end();
		}
			
		//Import Theme Options if the option was checked
		if ( $import_theme_options ){
			$this->import_theme_options($demoid);
		}
		
		//Import Color Options if the option was checked		
		if( $import_color_options ){			
			$this->import_colors($demoid);
		}
		
		//Import Widgets if the option was checked				
		if ( $import_demo_widgets){			
			$widget_data = new Swift_Widget_Data();
			$demofiles_data = $this->initialize_data();
			$widget_file = $demofiles_data['widgets-'.$demoid];
			$widget_data->ajax_import_widget_data($widget_file);
		}
			
		// Use a static front page
		$static_frontpage = get_page_by_title( 'Home' );
		update_option( 'page_on_front', $static_frontpage->ID );
		update_option( 'show_on_front', 'page' );
		?>
		<script>
				jQuery('.importspinner, .spinnermessage').hide();
				jQuery('.import-success-msg').show();
		</script>
		
		<?php
	}
	
	
	
	
	/**
	 * Get Menu Item Id with special meta data
	 * 
	 */
	
	function get_menu_item_special_data($demoid){
		
		  global $wpdb;
		  			
		  if($demoid == 6){
		  		$item_name = 'GET STARTED';
		  }
		  
		  if($demoid == 21){
		    	$item_name = 'PROJECT PLANNER';	
		  }
			  
		 
		  $item_type = 'custom';

    	  $query = 'SELECT ID FROM '.$wpdb->posts.', '.$wpdb->term_relationships.', '.$wpdb->postmeta.' ';
     	  $query .= 'WHERE ID = object_id AND ID = post_id ';
          $query .= 'AND post_title = "'.$item_name.'" ';
          $query .= 'AND post_status = "publish" ';
          $query .= 'AND post_type = "nav_menu_item" ';
          $query .= 'AND meta_key = "_menu_item_object" ';
          
		  return  $wpdb->get_var( $query );
			
	}
	
	
	
	/**
	 * Assign the menus to the locations
	 * 
	 */
	
	function assign_menus_to_locations($demoid){
		
		$term = get_term_by('name', 'Main Menu', 'nav_menu');
		
		if ($term == null || 0 == $term->term_id) {
			$term = get_term_by('name', 'Main', 'nav_menu');	
		}
		
		$menu_id =  $term->term_id;
				
	 	$new_theme_navs = get_theme_mod( 'nav_menu_locations' );
	 	$new_theme_locations = get_registered_nav_menus();
				   
	 	foreach ($new_theme_locations as $location => $description ) {
			
			// We are setting same nav menus for each theme location 
			if ($demoid == 18 && $location == 'footer_menu'){
				$term_magazine = get_term_by('name', 'Footer Menu', 'nav_menu');	
				$new_theme_navs[$location] = $term_magazine->term_id;	
			} else {
				$new_theme_navs[$location] = $menu_id;	
			}
		
       	}

       	set_theme_mod( 'nav_menu_locations', $new_theme_navs );
					
	}
		
	/**
	 * Import the theme options from a json file
	 *
	 * @param string $file Path to the json file for importing
	 */
	function import_theme_options($demoid){
		
		$demofiles_data = $this->initialize_data();
		$file = $demofiles_data['themeoptions-'.$demoid];
		$import = file_get_contents( $file );		
		$imported_options = array();
		
		if ( !empty( $import ) ) {
                $imported_options = json_decode( htmlspecialchars_decode( $import ), true );
         }
				
		$plugin_options['REDUX_imported'] = 1;
        foreach($imported_options as $key => $value) {
				$plugin_options[$key] = $value;
        }
			
		if(!empty($imported_options) && is_array($imported_options) && isset($imported_options['redux-backup']) && $imported_options['redux-backup'] == '1' ) {
             $plugin_options['REDUX_imported'] = 1;	 
             foreach($imported_options as $key => $value) {
					  $plugin_options[$key] = $value;
             }

             /**
               * action 'redux/options/{opt_name}/import'
               * @param  &array [&$plugin_options, redux_options]
               */
					 
               do_action_ref_array( "redux/options/sf_joyn_options/import", array(&$plugin_options, $imported_options));

               $plugin_options['REDUX_COMPILER'] = time();
               unset( $plugin_options['defaults'], $plugin_options['compiler'], $plugin_options['import'], $plugin_options['import_code'] );
			   $this->set_theme_options( $plugin_options );
        }
		
		
		update_option( 'sf_joyn_options', $plugin_options );    
		
	}
	
	function set_theme_options( $value = '' ) { 	
		 	 	
            $value['REDUX_last_saved'] = time();
            if( !empty($value) && isset($args) ) {
                $options = $value;
                if ( $args['database'] === 'transient' ) {
                    set_transient( 'sf_joyn_options-transient', $value, time() );
                } else if ( $args['database'] === 'theme_mods' ) {
                    set_theme_mod( $args['opt_name'] . '-mods', $value );
                } else if ( $args['database'] === 'theme_mods_expanded' ) {
                    foreach ( $value as $k=>$v ) {
                        set_theme_mod( $k, $v );
                    }
                } else {
					                    
					   update_option( 'sf_joyn_options', $value  );
                }

                $options = $value;

                /**
                 * action 'redux-saved-{opt_name}'
                 * @deprecated
                 * @param mixed $value set/saved option value
                 */
				 
				 // To work for all the themes this must be replaced redux-saved-{opt_name} and assign a specific value for opt_name 
		
                do_action( "redux-saved-sf_joyn_options", $value ); // REMOVE
				
                /**
                 * action 'redux/options/{opt_name}/saved'
                 * @param mixed $value set/saved option value
                 */
		
				 // To work for all the themes this must be replaced redux-saved-{opt_name} and assign a specific value for opt_name 
                do_action( "redux/options/sf_joyn_options/saved", $value );

            }
        } 
	
	
	/**
	 * Parses the WXR file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import_colors($demoid) {
			
		global $plugin_path;
		$plugin_path = dirname(__FILE__);
		
		$demofiles_data = $this->initialize_data();
		$file = $demofiles_data['colors-'.$demoid];
		$import = file_get_contents( $file );							
		
		if ( !empty( $import ) ) {
	        $imported_options = json_decode( htmlspecialchars_decode( $import ), true );
        }
		$sf_customizer_options = array();
		if( !empty( $imported_options ) && is_array( $imported_options ) )  {
               
                foreach($imported_options as $key => $value) {	
				
					$sf_customizer_options[$key] = $value;
					
                }
		}
		
		update_option('sf_customizer', $sf_customizer_options);
		
		
	}
	
	/**
	 * Parses the WXR file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import_start( $file ) {
	   
	   		$import_data = $this->parse( $file );

			if ( is_wp_error( $import_data ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo esc_html( $import_data->get_error_message() ) . '</p>';
				$this->footer();
				die();
			}
		
			$this->version = $import_data['version'];
			$this->get_authors_from_import( $import_data );
			$this->posts = $import_data['posts'];
			$this->terms = $import_data['terms'];
			$this->categories = $import_data['categories'];
			$this->tags = $import_data['tags'];
			$this->base_url = esc_url( $import_data['base_url'] );

			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );
			//do_action( 'import_start' );
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	function import_end() {
		wp_import_cleanup( $this->id );

		wp_cache_flush();
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		//do_action( 'import_end' );
	}

	/**
	 * Retrieve authors from parsed WXR data
	 *
	 * Uses the provided author information from WXR 1.1 files
	 * or extracts info from each post for WXR 1.0 files
	 *
	 * @param array $import_data Data returned by a WXR parser
	 */
	function get_authors_from_import( $import_data ) {
		if ( ! empty( $import_data['authors'] ) ) {
			$this->authors = $import_data['authors'];
		// no author information, grab it from the posts
		} else {
			foreach ( $import_data['posts'] as $post ) {
				$login = sanitize_user( $post['post_author'], true );
				if ( empty( $login ) ) {
					//printf( __( 'Failed to import author %s. Their posts will be attributed to the current user.', 'wordpress-importer' ), esc_html( $post['post_author'] ) );
					echo '<br />';
					continue;
				}

				if ( ! isset($this->authors[$login]) )
					$this->authors[$login] = array(
						'author_login' => $login,
						'author_display_name' => $post['post_author']
					);
			}
		}
	}

	/**
	 * Display pre-import options, author importing/mapping and option to
	 * fetch attachments
	 */
	function import_options() {
		?>
		
		<form action="<?php echo admin_url( 'admin.php?import=swiftdemo&amp;step=2' ); ?>" method="post">
			<?php wp_nonce_field( 'import-wordpress' ); ?>
			<input type="hidden" name="import_id" value="<?php echo $this->id; ?>" />
			<p class="submit"><input type="submit" class="button" value="<?php esc_attr_e( 'Submit', 'wordpress-importer' ); ?>" /></p>
		</form>
		
		<a href="<?php echo admin_url('admin.php?import=swiftdemo&amp;step=0');?>"> Back to Demo Options </a>
		
		<?php
	}

	/**
	 * Display import options for an individual author. That is, either create
	 * a new user based on import info or map to an existing user
	 *
	 * @param int $n Index for each author in the form
	 * @param array $author Author information, e.g. login, display name, email
	 */
	function author_select( $n, $author ) {
		_e( 'Import author:', 'wordpress-importer' );
		echo ' <strong>' . esc_html( $author['author_display_name'] );
		if ( $this->version != '1.0' ) echo ' (' . esc_html( $author['author_login'] ) . ')';
		echo '</strong><br />';

		if ( $this->version != '1.0' )
			echo '<div style="margin-left:18px">';

		$create_users = $this->allow_create_users();
		if ( $create_users ) {
			if ( $this->version != '1.0' ) {
				_e( 'or create new user with login name:', 'wordpress-importer' );
				$value = '';
			} else {
				_e( 'as a new user:', 'wordpress-importer' );
				$value = esc_attr( sanitize_user( $author['author_login'], true ) );
			}

			echo ' <input type="text" name="user_new['.$n.']" value="'. $value .'" /><br />';
		}

		if ( ! $create_users && $this->version == '1.0' )
			_e( 'assign posts to an existing user:', 'wordpress-importer' );
		else
			_e( 'or assign posts to an existing user:', 'wordpress-importer' );
		wp_dropdown_users( array( 'name' => "user_map[$n]", 'multi' => true, 'show_option_all' => __( '- Select -', 'wordpress-importer' ) ) );
		echo '<input type="hidden" name="imported_authors['.$n.']" value="' . esc_attr( $author['author_login'] ) . '" />';

		if ( $this->version != '1.0' )
			echo '</div>';
	}

	/**
	 * Map old author logins to local user IDs based on decisions made
	 * in import options form. Can map to an existing user, create a new user
	 * or falls back to the current user in case of error with either of the previous
	 */
	function get_author_mapping() {
		if ( ! isset( $_POST['imported_authors'] ) )
			return;

		$create_users = $this->allow_create_users();

		foreach ( (array) $_POST['imported_authors'] as $i => $old_login ) {
			// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
			$santized_old_login = sanitize_user( $old_login, true );
			$old_id = isset( $this->authors[$old_login]['author_id'] ) ? intval($this->authors[$old_login]['author_id']) : false;

			if ( ! empty( $_POST['user_map'][$i] ) ) {
				$user = get_userdata( intval($_POST['user_map'][$i]) );
				if ( isset( $user->ID ) ) {
					if ( $old_id )
						$this->processed_authors[$old_id] = $user->ID;
					$this->author_mapping[$santized_old_login] = $user->ID;
				}
			} else if ( $create_users ) {
				if ( ! empty($_POST['user_new'][$i]) ) {
					$user_id = wp_create_user( $_POST['user_new'][$i], wp_generate_password() );
				} else if ( $this->version != '1.0' ) {
					$user_data = array(
						'user_login' => $old_login,
						'user_pass' => wp_generate_password(),
						'user_email' => isset( $this->authors[$old_login]['author_email'] ) ? $this->authors[$old_login]['author_email'] : '',
						'display_name' => $this->authors[$old_login]['author_display_name'],
						'first_name' => isset( $this->authors[$old_login]['author_first_name'] ) ? $this->authors[$old_login]['author_first_name'] : '',
						'last_name' => isset( $this->authors[$old_login]['author_last_name'] ) ? $this->authors[$old_login]['author_last_name'] : '',
					);
					$user_id = wp_insert_user( $user_data );
				}

				if ( ! is_wp_error( $user_id ) ) {
					if ( $old_id )
						$this->processed_authors[$old_id] = $user_id;
					$this->author_mapping[$santized_old_login] = $user_id;
				} else {
					printf( __( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'wordpress-importer' ), esc_html($this->authors[$old_login]['author_display_name']) );
					if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
						echo ' ' . $user_id->get_error_message();
					echo '<br />';
				}
			}

			// failsafe: if the user_id was invalid, default to the current user
			if ( ! isset( $this->author_mapping[$santized_old_login] ) ) {
				if ( $old_id )
					$this->processed_authors[$old_id] = (int) get_current_user_id();
				$this->author_mapping[$santized_old_login] = (int) get_current_user_id();
			}
		}
	}

	/**
	 * Create new categories based on import information
	 *
	 * Doesn't create a new category if its slug already exists
	 */
	function process_categories() {
		$this->categories = apply_filters( 'wp_import_categories', $this->categories );

		if ( empty( $this->categories ) )
			return;

		foreach ( $this->categories as $cat ) {
			// if the category already exists leave it alone
			$term_id = term_exists( $cat['category_nicename'], 'category' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($cat['term_id']) )
					$this->processed_terms[intval($cat['term_id'])] = (int) $term_id;
				continue;
			}

			$category_parent = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
			$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
			$catarr = array(
				'category_nicename' => $cat['category_nicename'],
				'category_parent' => $category_parent,
				'cat_name' => $cat['cat_name'],
				'category_description' => $category_description
			);

			$id = wp_insert_category( $catarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($cat['term_id']) )
					$this->processed_terms[intval($cat['term_id'])] = $id;
			} else {
				//printf( __( 'Failed to import category %s', 'wordpress-importer' ), esc_html($cat['category_nicename']) );
				if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
					echo ': ' . $id->get_error_message();
				echo '<br />';
				continue;
			}
		}

		unset( $this->categories );
	}

	/**
	 * Create new post tags based on import information
	 *
	 * Doesn't create a tag if its slug already exists
	 */
	function process_tags() {
		$this->tags = apply_filters( 'wp_import_tags', $this->tags );

		if ( empty( $this->tags ) )
			return;

		foreach ( $this->tags as $tag ) {
			// if the tag already exists leave it alone
			$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($tag['term_id']) )
					$this->processed_terms[intval($tag['term_id'])] = (int) $term_id;
				continue;
			}

			$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
			$tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

			$id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($tag['term_id']) )
					$this->processed_terms[intval($tag['term_id'])] = $id['term_id'];
			} else {
				//printf( __( 'Failed to import post tag %s', 'wordpress-importer' ), esc_html($tag['tag_name']) );
				if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
					echo ': ' . $id->get_error_message();
				echo '<br />';
				continue;
			}
		}

		unset( $this->tags );
	}

	/**
	 * Create new terms based on import information
	 *
	 * Doesn't create a term its slug already exists
	 */
	function process_terms() {
		$this->terms = apply_filters( 'wp_import_terms', $this->terms );

		if ( empty( $this->terms ) )
			return;

		foreach ( $this->terms as $term ) {
			// if the term already exists in the correct taxonomy leave it alone
			$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($term['term_id']) )
					$this->processed_terms[intval($term['term_id'])] = (int) $term_id;
				continue;
			}

			if ( empty( $term['term_parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
				if ( is_array( $parent ) ) $parent = $parent['term_id'];
			}
			$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
			$termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent) );

			$id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($term['term_id']) )
					$this->processed_terms[intval($term['term_id'])] = $id['term_id'];
			} else {
				//printf( __( 'Failed to import %s %s', 'wordpress-importer' ), esc_html($term['term_taxonomy']), esc_html($term['term_name']) );
				if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
					echo ': ' . $id->get_error_message();
				echo '<br />';
				continue;
			}
		}

		unset( $this->terms );
	}

	/**
	 * Create new posts based on import information
	 *
	 * Posts marked as having a parent which doesn't exist will become top level items.
	 * Doesn't create a new post if: the post type doesn't exist, the given post ID
	 * is already noted as imported or a post with the same title and date already exists.
	 * Note that new/updated terms, comments and meta are imported for the last of the above.
	 */
	function process_posts() {
		$this->posts = apply_filters( 'wp_import_posts', $this->posts );

		foreach ( $this->posts as $post ) {
			$post = apply_filters( 'wp_import_post_data_raw', $post );

			/*
			if ( ! post_type_exists( $post['post_type'] ) ) {
				printf( __( 'Failed to import &#8220;%s&#8221;: Invalid post type %s', 'wordpress-importer' ),
					esc_html($post['post_title']), esc_html($post['post_type']) );
				echo '<br />';
				do_action( 'wp_import_post_exists', $post );
				continue;
			}*/

			if ( isset( $this->processed_posts[$post['post_id']] ) && ! empty( $post['post_id'] ) )
				continue;

			if ( $post['status'] == 'auto-draft' )
				continue;

			if ( 'nav_menu_item' == $post['post_type'] ) {
									
				$this->process_menu_item( $post );
				if ( $this->firsttime )
					$this->firsttime = false;
				continue;
			}

			$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				//printf( __('%s &#8220;%s&#8221; already exists.', 'wordpress-importer'), $post_type_object->labels->singular_name, esc_html($post['post_title']) );
				//echo '<br />';
				$comment_post_ID = $post_id = $post_exists;
			} else {
				$post_parent = (int) $post['post_parent'];
				if ( $post_parent ) {
					// if we already know the parent, map it to the new local ID
					if ( isset( $this->processed_posts[$post_parent] ) ) {
						$post_parent = $this->processed_posts[$post_parent];
					// otherwise record the parent for later
					} else {
						$this->post_orphans[intval($post['post_id'])] = $post_parent;
						$post_parent = 0;
					}
				}

				// map the post author
				$author = sanitize_user( $post['post_author'], true );
				if ( isset( $this->author_mapping[$author] ) )
					$author = $this->author_mapping[$author];
				else
					$author = (int) get_current_user_id();

				$postdata = array(
					'import_id' => $post['post_id'], 'post_author' => $author, 'post_date' => $post['post_date'],
					'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
					'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
					'post_status' => $post['status'], 'post_name' => $post['post_name'],
					'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
					'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
					'post_type' => $post['post_type'], 'post_password' => $post['post_password']
				);

				$original_post_ID = $post['post_id'];
				$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

				if ( 'attachment' == $postdata['post_type'] ) {
					$remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];

					// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
					// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
					$postdata['upload_date'] = $post['post_date'];
					if ( isset( $post['postmeta'] ) ) {
						foreach( $post['postmeta'] as $meta ) {
							if ( $meta['key'] == '_wp_attached_file' ) {
								if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) )
									$postdata['upload_date'] = $matches[0];
								break;
							}
						}
					}

					$comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );
				} else {
					$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
					do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
				}

				if ( is_wp_error( $post_id ) ) {
					//printf( __( 'Failed to import %s &#8220;%s&#8221;', 'wordpress-importer' ),
					//		$post_type_object->labels->singular_name, esc_html($post['post_title']) );
					if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
						echo ': ' . $post_id->get_error_message();
					echo '<br />';
					continue;
				}

				if ( $post['is_sticky'] == 1 )
					stick_post( $post_id );
			}

			// map pre-import ID to local ID
			$this->processed_posts[intval($post['post_id'])] = (int) $post_id;

			if ( ! isset( $post['terms'] ) )
				$post['terms'] = array();

			$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

			// add categories, tags and other terms
			if ( ! empty( $post['terms'] ) ) {
				$terms_to_set = array();
				foreach ( $post['terms'] as $term ) {
					// back compat with WXR 1.0 map 'tag' to 'post_tag'
					$taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
					$term_exists = term_exists( $term['slug'], $taxonomy );
					$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
					if ( ! $term_id ) {
						$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];
							do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
						} else {
							//printf( __( 'Failed to import %s %s', 'wordpress-importer' ), esc_html($taxonomy), esc_html($term['name']) );
							if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
								echo ': ' . $t->get_error_message();
							echo '<br />';
							do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
							continue;
						}
					}
					$terms_to_set[$taxonomy][] = intval( $term_id );
				}

				foreach ( $terms_to_set as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
				}
				unset( $post['terms'], $terms_to_set );
			}

			if ( ! isset( $post['comments'] ) )
				$post['comments'] = array();

			$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

			// add/update comments
			if ( ! empty( $post['comments'] ) ) {
				$num_comments = 0;
				$inserted_comments = array();
				foreach ( $post['comments'] as $comment ) {
					$comment_id	= $comment['comment_id'];
					$newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
					$newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
					$newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
					$newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
					$newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
					$newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
					$newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
					$newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
					$newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
					$newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
					$newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
					$newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
					if ( isset( $this->processed_authors[$comment['comment_user_id']] ) )
						$newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment['comment_user_id']];
				}
				ksort( $newcomments );

				foreach ( $newcomments as $key => $comment ) {
					// if this is a new post we can skip the comment_exists() check
					if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
						if ( isset( $inserted_comments[$comment['comment_parent']] ) )
							$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
						$comment = wp_filter_comment( $comment );
						$inserted_comments[$key] = wp_insert_comment( $comment );
						do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

						foreach( $comment['commentmeta'] as $meta ) {
							$value = maybe_unserialize( $meta['value'] );
							add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
						}

						$num_comments++;
					}
				}
				unset( $newcomments, $inserted_comments, $post['comments'] );
			}

			if ( ! isset( $post['postmeta'] ) )
				$post['postmeta'] = array();

			$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

			// add/update post meta
			if ( ! empty( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					$key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
					$value = false;

					if ( '_edit_last' == $key ) {
						if ( isset( $this->processed_authors[intval($meta['value'])] ) )
							$value = $this->processed_authors[intval($meta['value'])];
						else
							$key = false;
					}

					if ( $key ) {
						// export gets meta straight from the DB so could have a serialized string
						if ( ! $value )
							$value = maybe_unserialize( $meta['value'] );

						add_post_meta( $post_id, $key, $value );
						do_action( 'import_post_meta', $post_id, $key, $value );

						// if the post has a featured image, take note of this in case of remap
						if ( '_thumbnail_id' == $key )
							$this->featured_images[$post_id] = (int) $value;
					}
				}
			}
		}

		unset( $this->posts );
	}

	/**
	 * Attempt to create a new menu item from import data
	 *
	 * Fails for draft, orphaned menu items and those without an associated nav_menu
	 * or an invalid nav_menu term. If the post type or term object which the menu item
	 * represents doesn't exist then the menu item will not be imported (waits until the
	 * end of the import to retry again before discarding).
	 *
	 * @param array $item Menu item details from WXR file
	 */
	function process_menu_item( $item ) {
		
		global $demoid;
		// skip draft, orphaned menu items
		if ( 'draft' == $item['status'] )
			return;
			
		$menu_slug = false;
		if ( isset($item['terms']) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					
					break;
				}
			}
		}
				
		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			return;
		}
				
		//Return if menu already has menu itens
		$menu_terms = wp_get_nav_menu_items($menu_slug);
			
		if (count($menu_terms) > 0 && $this->firsttime){
			$this->import_menu_itens = false;
		}
			
		if ( !$this->import_menu_itens ){
			return;
		}
			
		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}	
		
		foreach ( $item['postmeta'] as $meta )
			$$meta['key'] = $meta['value'];

		if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
		} else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$this->missing_menu_items[] = $item;
			return;
		}
		
		

	
		if ( isset( $this->processed_menu_items[intval($_menu_item_menu_item_parent)] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[intval($_menu_item_menu_item_parent)];
		} else if ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) )
			$_menu_item_classes = implode( ' ', $_menu_item_classes );

		
		
		$args = array(
			'menu-item-object-id' => $_menu_item_object_id,
			'menu-item-object' => $_menu_item_object,
			'menu-item-parent-id' => $_menu_item_menu_item_parent,
			'menu-item-position' => intval( $item['menu_order'] ),
			'menu-item-type' => $_menu_item_type,
			'menu-item-title' => $item['post_title'],
			'menu-item-url' => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title' => $item['post_excerpt'],
			'menu-item-target' => $_menu_item_target,
			'menu-item-classes' => $_menu_item_classes,
			'menu-item-xfn' => $_menu_item_xfn,
			'menu-item-status' => $item['status']
		);
		
		
				
		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		
		if ( $id && ! is_wp_error( $id ) )
			$this->processed_menu_items[intval($item['post_id'])] = (int) $id;
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array $post Attachment post details from WXR
	 * @param string $url URL to fetch attachment from
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	function process_attachment( $post, $url ) {
		
		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
		if ( preg_match( '|^/[\w\W]+$|', $url ) )
			$url = rtrim( $this->base_url, '/' ) . $url;

		$upload = $this->fetch_remote_file( $url, $post );
		if ( is_wp_error( $upload ) )
			return $upload;

		if ( $info = wp_check_filetype( $upload['file'] ) )
			$post['post_mime_type'] = $info['type'];
		else
			return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'wordpress-importer') );

		$post['guid'] = $upload['url'];

		// as per wp-admin/includes/upload.php
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		/*
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts = pathinfo( $url );
			$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

			$parts_new = pathinfo( $upload['url'] );
			$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
		}*/

		return $post_id;
	}

	/**
	 * Attempt to download a remote file attachment
	 *
	 * @param string $url URL of item to fetch
	 * @param array $post Attachment details
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	function fetch_remote_file( $url, $post ) {
		// extract the file name and extension from the url
		$file_name = basename( $url );

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] )
			return new WP_Error( 'upload_dir_error', $upload['error'] );

		// fetch the remote url and write it to the placeholder file
		$headers = wp_get_http( $url, $upload['file'] );

		// request failed
		if ( ! $headers ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', __('Remote server did not respond', 'wordpress-importer') );
		}

		// make sure the fetch was successful
		if ( $headers['response'] != '200' ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', sprintf( __('Remote server returned error response %1$d %2$s', 'wordpress-importer'), esc_html($headers['response']), get_status_header_desc($headers['response']) ) );
		}

		$filesize = filesize( $upload['file'] );

		if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'wordpress-importer') );
		}

		if ( 0 == $filesize ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'wordpress-importer') );
		}

		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', sprintf(__('Remote file is too large, limit is %s', 'wordpress-importer'), size_format($max_size) ) );
		}

		// keep track of the old and new urls so we can substitute them later
		$this->url_remap[$url] = $upload['url'];
		$this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?
		// keep track of the destination if the remote url is redirected somewhere else
		if ( isset($headers['x-final-location']) && $headers['x-final-location'] != $url )
			$this->url_remap[$headers['x-final-location']] = $upload['url'];

		return $upload;
	}

	/**
	 * Attempt to associate posts and menu items with previously missing parents
	 *
	 * An imported post's parent may not have been imported when it was first created
	 * so try again. Similarly for child menu items and menu items which were missing
	 * the object (e.g. post) they represent in the menu
	 */
	function backfill_parents() {
		global $wpdb;

		// find parents for post orphans
		foreach ( $this->post_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = false;
			if ( isset( $this->processed_posts[$child_id] ) )
				$local_child_id = $this->processed_posts[$child_id];
			if ( isset( $this->processed_posts[$parent_id] ) )
				$local_parent_id = $this->processed_posts[$parent_id];

			if ( $local_child_id && $local_parent_id )
				$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
		}

		// all other posts/terms are imported, retry menu items with missing associated object
		$missing_menu_items = $this->missing_menu_items;
		foreach ( $missing_menu_items as $item )
			$this->process_menu_item( $item );

		// find parents for menu item orphans
		foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = 0;
			if ( isset( $this->processed_menu_items[$child_id] ) )
				$local_child_id = $this->processed_menu_items[$child_id];
			if ( isset( $this->processed_menu_items[$parent_id] ) )
				$local_parent_id = $this->processed_menu_items[$parent_id];

			if ( $local_child_id && $local_parent_id )
				update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
		}
	}

	/**
	 * Use stored mapping information to update old attachment URLs
	 */
	function backfill_attachment_urls() {
		global $wpdb;
		// make sure we do the longest urls first, in case one is a substring of another
		uksort( $this->url_remap, array(&$this, 'cmpr_strlen') );

		foreach ( $this->url_remap as $from_url => $to_url ) {
			// remap urls in post_content
			$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url) );
			// remap enclosure urls
			$result = $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url) );
		}
	}

	/**
	 * Update _thumbnail_id meta to new, imported attachment IDs
	 */
	function remap_featured_images() {
		// cycle through posts that have a featured image
		foreach ( $this->featured_images as $post_id => $value ) {
			if ( isset( $this->processed_posts[$value] ) ) {
				$new_id = $this->processed_posts[$value];
				// only update if there's a difference
				if ( $new_id != $value )
					update_post_meta( $post_id, '_thumbnail_id', $new_id );
			}
		}
	}

	/**
	 * Parse a WXR file
	 *
	 * @param string $file Path to WXR file for parsing
	 * @return array Information gathered from the WXR file
	 */
	function parse( $file ) {
		$parser = new Swift_WXR_Parser();
		return $parser->parse( $file );
	}

	// Display import page title
	function header() {
		echo '<div class="wrap">';
		screen_icon();
		//Welcome message
		echo '<h2 style="margin-left:20px;">' . __( 'Joyn Demo Content Importer', 'wordpress-importer' ) . '</h2>';

		$updates = get_plugin_updates();
		$basename = plugin_basename(__FILE__);
		if ( isset( $updates[$basename] ) ) {
			$update = $updates[$basename];
			echo '<div class="error"><p><strong>';
			printf( __( 'A new version of this importer is available. Please update to version %s to ensure compatibility with newer export files.', 'wordpress-importer' ), $update->update->new_version );
			echo '</strong></p></div>';
		}
	}

	// Close div.wrap
	function footer() {
		echo '</div>';
	}

	/**
	 * Display introductory text and file upload form
	 */
	function greet() {
				
		global $plugin_path;
		
		$demofiles_data = $this->initialize_data();
		$plugin_path = dirname(__FILE__);
		$demourl = admin_url('admin.php?import=swiftdemo&amp;step=1');
		
		?>

		<div class="note-wrap clearfix">
			<h3>Please Read!</h3>
			<p>This demo content importer has been built to make the import process as easy for you as possible. We've done what we can to ensure as little difficulty as possible. We have also gone the extra mile to add in the extra things are sorted for you, such as setting the home page, menu, and widgets - things that aren't possible with the standard WordPress Importer!</p>
			<h4>Steps to take before using this plugin.</h4>
			<ol>
				<li>The import process will work best on a clean install. You can use a plugin such as WordPress Reset to clear your data for you.</li>
				<li>Ensure all plugins are installed beforehand, e.g. WooCommerce, BuddyPress, bbPress - any plugins that you add content to.</li>
				<li>Once you start the process, please leave it running and uninteruppted - the page will refresh and show you a completed message once the process is done.</li>
			</ol>
		</div>

<!--		<div class="filter-wrap clearfix">
			<ul class="post-filter-tabs filtering clearfix" style="opacity: 1;">
				<li class="selected"><a href="#" title="View all All items" class="All" data-filter="all"><span class="item-name">All</span></a></li>
				<li><a href="#" title="View all BuddyPress items" class="buddypress" data-filter="buddypress"><span class="item-name">BuddyPress</span></a></li>
				<li>
					<a href="#" title="View all Business items" class="business" data-filter="business"><span class="item-name">Business</span></a>
				</li>
				<li>
					<a href="#" title="View all Crowdfunding items" class="crowdfunding" data-filter="crowdfunding"><span class="item-name">Crowdfunding</span></a></li>
				<li>
					<a href="#" title="View all Magazine items" class="magazine" data-filter="magazine"><span class="item-name">Magazine</span></a>
				</li>
				<li>
					<a href="#" title="View all Musician items" class="musician" data-filter="musician"><span class="item-name">Musician</span></a>
					</li>
				<li>
					<a href="#" title="View all Non-profit items" class="non-profit" data-filter="non-profit"><span class="item-name">Non-profit</span></a>
				</li>
				<li>
					<a href="#" title="View all One-Page items" class="one-page" data-filter="one-page"><span class="item-name">One-Page</span></a>
				</li>
				<li>
					<a href="#" title="View all Personal items" class="personal" data-filter="personal"><span class="item-name">Personal</span></a>
				</li>
				<li>
					<a href="#" title="View all Portfolio items" class="portfolio" data-filter="portfolio"><span class="item-name">Portfolio</span></a>
				</li>
				<li>
					<a href="#" title="View all Restaurant items" class="restaurant" data-filter="restaurant"><span class="item-name">Restaurant</span></a>
				</li>
				<li>
					<a href="#" title="View all Shops items" class="shops" data-filter="shop"><span class="item-name">Shop</span></a>
				</li>
				<li>
					<a href="#" title="View all Wedding items" class="wedding" data-filter="wedding"><span class="item-name">Wedding</span></a>
				</li>
				<li>
					<a href="#" title="View all Directory items" class="directory" data-filter="directory"><span class="item-name">Directory</span></a>
				</li>  
				<li>
					<a href="#" title="View all Photography items" class="photography" data-filter="photography"><span class="item-name">Photography</span></a>
				</li>

		</ul>
	</div>-->
	
	<div>
	
	<?php  	
					
		if (function_exists('sf_joyn_setup')) {
			$html_output = '<div><ul class="swift-demo">';
			
			for ( $i=0; $i<=0; $i++){
			
				$html_output .= '<li class="'.$demofiles_data["filter-$i"].'"><a href="'.$demofiles_data["previewlink-$i"].'" target="_blank" class="product '.$demofiles_data["id-$i"].'"></a>';
				$html_output .= '<div class="item-wrap">';
				$html_output .= '<h3>'.$demofiles_data["title-$i"].'</h3>';
				$html_output .= '<div class="importoptions"><span>'.__("Select what you'd like to import:", 'swift-importer').'</span><div class="dinput">';
				$html_output .= '<input type="checkbox" name="democontent'.$i.'" id="democontent'.$i.'">Demo Content</input></div><div class="dinput">';
				$html_output .= '<input type="checkbox" name="widgetsoption'.$i.'" id="widgetsoption'.$i.'">Widgets</input></div><div class="dinput">';
				$html_output .= '<input type="checkbox" name="themeoption'.$i.'" id="themeoption'.$i.'">Theme Options</input></div><div class="dinput">';
				$html_output .= '<input type="checkbox" name="coloroption'.$i.'" id="coloroption'.$i.'">Color Options</input></div></div>';
				$html_output .= '<div data-demoid="'.$i.'" data-url="'.$demourl.'&amp;demoid='.$i.'" class="demoimp-button">Import</div>';
				$html_output .= '</div></li>';
				
			}
			$html_output .= '</ul></div>';	
		}	
		echo $html_output;
		?>
		
	</div>
	<div class="sf-modal-notice">
		<div class="spinnermessage">
			<h3>Start Import</h3> 
			<p>Please press the button below to start the import process.</p>
			<p><span>Note</span>: the page may refresh at intervals, this is normal.</p>
			<div id="sf_import_start">Start Importing</div>
		</div>
	</div>
	<div  class="sf-black-overlay"></div>

	<?php	}

	/**
	 * Decide if the given meta key maps to information we will want to import
	 *
	 * @param string $key The meta key to check
	 * @return string|bool The key if we do want to import, false if not
	 */
	function is_valid_meta_key( $key ) {
		// skip attachment metadata since we'll regenerate it from scratch
		// skip _edit_lock as not relevant for import
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) )
			return false;
		return $key;
	}

	/**
	 * Decide whether or not the importer is allowed to create users.
	 * Default is true, can be filtered via import_allow_create_users
	 *
	 * @return bool True if creating users is allowed
	 */
	function allow_create_users() {
		return apply_filters( 'import_allow_create_users', true );
	}

	/**
	 * Decide whether or not the importer should attempt to download attachment files.
	 * Default is true, can be filtered via import_allow_fetch_attachments. The choice
	 * made at the import options screen must also be true, false here hides that checkbox.
	 *
	 * @return bool True if downloading attachments is allowed
	 */
	function allow_fetch_attachments() {
		return apply_filters( 'import_allow_fetch_attachments', true );
	}

	/**
	 * Decide what the maximum file size for downloaded attachments is.
	 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
	 *
	 * @return int Maximum attachment file size to import
	 */
	function max_attachment_size() {
		return apply_filters( 'import_attachment_size_limit', 0 );
	}

	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
	 * @return int 60
	 */
	 function bump_request_timeout( $val ) { 
			return 300;
	}

	// return the difference in length between two strings
	function cmpr_strlen( $a, $b ) {
		return strlen($b) - strlen($a);
	}
} 

} // class_exists( 'Swift_Importer' )

function swift_importer_init() {
		
	if (!function_exists('sf_joyn_setup')) {	
		add_action('admin_notices', 'admin_notice_message');   
	}
	
	//unregister_setting( 'sf_joyn_options_group', 'sf_joyn_options');
	load_plugin_textdomain( 'wordpress-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	/**
	 * WordPress Importer object for registering the import callback
	 * @global Swift_Import $Swift_Import
	 */
	$GLOBALS['Swift_Import'] = new Swift_Import();
		
	register_importer( 'swiftdemo', 'Joyn Demo Content', __("Import demo content for Joyn by Swift Ideas. 1 click and you're ready to go!  <strong>By Swift Ideas</strong>", 'wordpress-importer'), array( $GLOBALS['Swift_Import'], 'dispatch' ) );
	
define( "WIDGET_DATA_MIN_PHP_VER", '5.3.0' );

register_activation_hook( __FILE__, 'swift_widget_data_activation' );

function swift_widget_data_activation() {
	if ( version_compare( phpversion(), WIDGET_DATA_MIN_PHP_VER, '<' ) ) {
		die( sprintf( "The minimum PHP version required for this plugin is %s", WIDGET_DATA_MIN_PHP_VER ) );
	}
}

}


add_action( 'admin_init', 'swift_importer_init' ); 
add_action( 'admin_enqueue_scripts', 'swift_importer_scripts');

if (!function_exists('swift_importer_scripts')){
	function swift_importer_scripts(){		
			wp_enqueue_style( 'widget_data', plugins_url( '/assets/sf_importer.css', __FILE__ ) );
			wp_enqueue_script( 'widget_data', plugins_url( '/assets/sf_importer.js', __FILE__ ), array( 'jquery' ) );
		}
} 
