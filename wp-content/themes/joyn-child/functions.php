<?php
	
	/*
	*
	*	Joyn Functions - Child Theme
	*	------------------------------------------------
	*	These functions will override the parent theme
	*	functions. We have provided some examples below.
	*
	*
	*/
	
	/* LOAD PARENT THEME STYLES
	================================================== */
	function joyn_child_enqueue_styles() {
	    wp_enqueue_style( 'joyn-parent-style', get_template_directory_uri() . '/style.css' );
	
	}
	add_action( 'wp_enqueue_scripts', 'joyn_child_enqueue_styles' );
	

	/* LOAD THEME LANGUAGE
	================================================== */
	/*
	*	You can uncomment the line below to include your own translations
	*	into your child theme, simply create a "language" folder and add your po/mo files
	*/
	
	// load_theme_textdomain('swiftframework', get_stylesheet_directory_uri().'/language');
	
	
	/* REMOVE PAGE BUILDER ASSETS
	================================================== */
	/*
	*	You can uncomment the line below to remove selected assets from the page builder
	*/
	
	// function spb_remove_assets( $pb_assets ) {
	//     unset($pb_assets['parallax']);
	//     return $pb_assets;
	// }
	// add_filter( 'spb_assets_filter', 'spb_remove_assets' );	


	/* ADD/EDIT PAGE BUILDER TEMPLATES
	================================================== */
	function custom_prebuilt_templates($prebuilt_templates) {
			
		/*
		*	You can uncomment the lines below to add custom templates
		*/
		// $prebuilt_templates["custom"] = array(
		// 	'id' => "custom",
		// 	'name' => 'Custom',
		// 	'code' => 'your-code-here'
		// );

		/*
		*	You can uncomment the lines below to remove default templates
		*/
		// unset($prebuilt_templates['home-1']);
		// unset($prebuilt_templates['home-2']);

		// return templates array
	    return $prebuilt_templates;

	}
	//add_filter( 'spb_prebuilt_templates', 'custom_prebuilt_templates' );
	
	function custom_post_thumb_image($thumb_img_url) {
	    
	    if ($thumb_img_url == "") {
	    	global $post;
	  		ob_start();
	  		ob_end_clean();
	  		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	  		if (!empty($matches) && isset($matches[1][0])) {
	  		$thumb_img_url = $matches[1][0];
	    	}
	    }
	    
	    return $thumb_img_url;
	}
	add_filter( 'sf_post_thumb_image_url', 'custom_post_thumb_image' );
    
    
    /*==========*/
    //CUSTOM FUNCTIONS
    /*==========*/
    
/*1. hook custom javascript*/
    
    function joyn_re_script_enqueue(){
    // get_template_directory_uri geldt voor een parent theme; get_stylesheet_directory_uri() geldt voor een child theme;
    wp_enqueue_script('customjs', get_stylesheet_directory_uri().'/js/customjs.js', array(), '1.0', true);
    wp_enqueue_script('fitvids', get_stylesheet_directory_uri().'/js/jquery.fitvids.js', array(), '1.0', true);
    }
    add_action('wp_enqueue_scripts', 'joyn_re_script_enqueue');
    
//2. backend taal menu
    function jgib_language_menu(){
    register_nav_menu('secondary','Language Menu');
    }
    add_action('init','jgib_language_menu');   


/*3. override van sf_header_wrap in joyn ten behoeve van het taalmenu in de front end*/
	
	if (!function_exists('sf_header_wrap')) {
        function sf_header_wrap($header_layout) {
            global $post, $sf_options;

            $page_classes = sf_page_classes();
            $header_layout = $page_classes['header-layout'];
            $page_header_type = "standard";

            if (is_page() && $post) {
                $page_header_type = sf_get_post_meta($post->ID, 'sf_page_header_type', true);
            } else if (is_singular('post') && $post) {
                $post_header_type = sf_get_post_meta($post->ID, 'sf_page_header_type', true);
                $fw_media_display = sf_get_post_meta($post->ID, 'sf_fw_media_display', true);
                $page_title_style = sf_get_post_meta($post->ID, 'sf_page_title_style', true);
                if ($page_title_style == "fancy" || $fw_media_display == "fw-media-title" || $fw_media_display == "fw-media") {
                    $page_header_type = $post_header_type;
                }
            } else if (is_singular('portfolio') && $post) {
                $port_header_type = sf_get_post_meta($post->ID, 'sf_page_header_type', true);
                $fw_media_display = sf_get_post_meta($post->ID, 'sf_fw_media_display', true);
                $page_title = sf_get_post_meta($post->ID, 'sf_page_title', true);
                $page_title_style = sf_get_post_meta($post->ID, 'sf_page_title_style', true);
                if ($page_title_style == "fancy" || !$page_title) {
                    $page_header_type = $port_header_type;
                }
            }

            $fullwidth_header = $sf_options['fullwidth_header'];
            $enable_mini_header = $sf_options['enable_mini_header'];
            $enable_tb = $sf_options['enable_tb'];
            $tb_left_config = $sf_options['tb_left_config'];
            $tb_right_config = $sf_options['tb_right_config'];
            $tb_left_text = __($sf_options['tb_left_text'], 'swiftframework');
            $tb_right_text = __($sf_options['tb_right_text'], 'swiftframework');
            $enable_sticky_tb = false;
            if ( isset( $sf_options['enable_sticky_topbar'] ) ) {
                $enable_sticky_tb = $sf_options['enable_sticky_topbar'];    
            }
            $header_left_config = $sf_options['header_left_config'];
            $header_right_config = $sf_options['header_right_config'];

            if (($page_header_type == "naked-light" || $page_header_type == "naked-dark") && ($header_layout == "header-vert" || $header_layout == "header-vert-right")) {
                $header_layout = "header-4";
                $enable_tb = false;
            }

            $tb_left_output = $tb_right_output = "";
            if ($tb_left_config == "social") {
            $tb_left_output .= do_shortcode('[social]'). "\n";
            } else if ($tb_left_config == "aux-links") {
            $tb_left_output .= sf_aux_links('tb-menu', TRUE, 'header-1'). "\n";
            } else if ($tb_left_config == "menu") {
            $tb_left_output .= sf_top_bar_menu(). "\n";
            } else if ($tb_left_config == "cart-wishlist") {
            $tb_left_output .= '<div class="aux-item aux-cart-wishlist"><nav class="std-menu cart-wishlist"><ul class="menu">'. "\n";
            $tb_left_output .= sf_get_cart();
            $tb_left_output .= sf_get_wishlist();
            $tb_left_output .= '</ul></nav></div>'. "\n";
            } else {
            $tb_left_output .= '<div class="tb-text">'.do_shortcode($tb_left_text).'</div>'. "\n";
            }

            if ($tb_right_config == "social") {
            $tb_right_output .= do_shortcode('[social]'). "\n";
            } else if ($tb_right_config == "aux-links") {
            $tb_right_output .= sf_aux_links('tb-menu', TRUE, 'header-1'). "\n";
            } else if ($tb_right_config == "menu") {
            $tb_right_output .= sf_top_bar_menu(). "\n";
            } else if ($tb_right_config == "cart-wishlist") {
            $tb_right_output .= '<div class="aux-item aux-cart-wishlist"><nav class="std-menu cart-wishlist"><ul class="menu">'. "\n";
            $tb_right_output .= sf_get_cart();
            $tb_right_output .= sf_get_wishlist();
            $tb_right_output .= '</ul></nav></div>'. "\n";
            } else {
            $tb_right_output .= '<div class="tb-text">'.do_shortcode($tb_right_text).'</div>'. "\n";
            }
            $top_bar_class = "";
            if ($enable_sticky_tb) {
                $top_bar_class = "sticky-top-bar";
            }
        ?>
            <?php if ($enable_tb) { ?>
            <!--// TOP BAR //-->
            <div id="top-bar" class="<?php echo $top_bar_class; ?>">
                <?php if ($fullwidth_header) { ?>
                <div class="container fw-header">
                <?php } else { ?>
                <div class="container">
                <?php } ?>
                        <div class="col-sm-2 tb-left"><?php echo $tb_left_output; ?></div>
                        <div class="col-sm-3"><?php wp_nav_menu(array( 'theme_location'=>'secondary' , 'container' => 'false', 'link_before' => '<span class="menu-item-text">', 'link_after' => '</span>'));  ?></div>
                        <div class="col-sm-7 tb-right"><?php echo $tb_right_output; ?></div>
                </div>
                </div>
               
            <?php } ?>

            <!--// HEADER //-->
            <div class="header-wrap <?php echo $page_classes['header-wrap']; ?> page-header-<?php echo $page_header_type; ?>">

                <div id="header-section" class="<?php echo $header_layout; ?> <?php echo $page_classes['logo']; ?>">
                    <?php if ($enable_mini_header) {
                            echo sf_header($header_layout);
                        } else {
                            echo '<div class="sticky-wrapper">'.sf_header($header_layout).'</div>';
                        }
                    ?>
                </div>

                <?php
                    // Fullscreen Search
                    echo sf_fullscreen_search();
                ?>

                <?php
                    // Fullscreen Search
                    if (isset($header_left_config) && array_key_exists('supersearch', $header_left_config['enabled']) || isset($header_right_config) && array_key_exists('supersearch', $header_right_config['enabled'])) {
                    echo sf_fullscreen_supersearch();
                    }
                ?>

                <?php
                    // Overlay Menu
                    if (isset($header_left_config) && array_key_exists('overlay-menu', $header_left_config['enabled']) || isset($header_right_config) && array_key_exists('overlay-menu', $header_right_config['enabled'])) {
                        echo sf_overlay_menu();
                    }
                ?>

                <?php
                    // Contact Slideout
                    if (isset($header_left_config) && array_key_exists('contact', $header_left_config['enabled']) || isset($header_right_config) && array_key_exists('contact', $header_right_config['enabled'])) {
                        echo sf_contact_slideout();
                    }
                ?>

            </div>

        <?php }
        add_action('sf_container_start', 'sf_header_wrap', 20);
    }



/*4. provide help op de checkout page*/
function jgi_ProvideHelp() {
   ob_start();
   
   $langauge =  pll_current_language( $field = 'slug' );
   switch($langauge)
	{
		case "en":
			
			$message = "Help needed? Call 02/893.25.02 or mail info@janegoodall.be";
			
		break;

		case "nl":
			$message = "Hulp nodig? Telefoneer 02/893.25.02 of mail info@janegoodall.be";
			            
		break;

		case "fr":
			$message = "Besoin d'aide? Appelez 02/893.25.02 ou mailez info@janegoodall.be";
						
		break;

	}
 	echo $message;
	
    return ob_get_clean();   
} 
add_shortcode( 'provide-help_shortcode', 'jgi_ProvideHelp' );



/*5. provide 404 message*/
function jgi_ErrorMessage() {
   ob_start();
   
   $langauge =  pll_current_language( $field = 'slug' );
   switch($langauge)
	{
		case "en":
			$message = "<br />Sorry but we couldn't find the page you are looking for. Please check to make sure you've typed the address correctly. You may also want to search for what you are looking for.";
			
		break;

		case "nl":
			$message = "<br />Sorry. We konden niet de pagina vinden die u zocht. Gelieve te controleren of je het adres correct getypt hebt. Je kan ook de zoekfunctie gebruiken.";
			            
		break;

		case "fr":
			$message = "<br />Désolé, nous ne pouvions pas trouver le page que vous cherchez. Veuillez contrôler si vous avez tapé l'adresse correctement. Vous pouvez aussi utiliser la fonction de recherche.";
						
		break;

	}
 	echo $message;
    	
    return ob_get_clean();   
} 
add_shortcode( 'provide-error_shortcode', 'jgi_ErrorMessage' );

/*6. wijzigingen in functions.js*/
function jgib_adapt_javascriptfunctions()
{
    wp_dequeue_script('sf-functions'); /*ge enqueued op regel 312 van functions.php van joyn theme*/
    wp_register_script('child_theme_sf-functions', get_stylesheet_directory_uri().'/js/functions.js', array('jquery'), NULL, TRUE);
    /*functions.js in de footer laden met FALSE blijkt niet te gaan*/
    wp_enqueue_script('child_theme_sf-functions', get_stylesheet_directory_uri().'/js/functions.js', array('jquery'));
}
add_action('wp_enqueue_scripts', 'jgib_adapt_javascriptfunctions', 1000);

/*7. error messages field validations gravity forms*/
add_filter( 'gform_validation_message_8', 'change_message_into_nl', 10, 2 );
add_filter( 'gform_validation_message_13', 'change_message_into_nl', 10, 2 );
add_filter( 'gform_validation_message_15', 'change_message_into_nl', 10, 2 );
add_filter( 'gform_validation_message_21', 'change_message_into_nl', 10, 2 );
add_filter( 'gform_validation_message_26', 'change_message_into_nl', 10, 2 );
add_filter( 'gform_validation_message_35', 'change_message_into_nl', 10, 2 );
add_filter( 'gform_validation_message_39', 'change_message_into_nl', 10, 2 );

function change_message_into_nl( $message, $form ) {
    return "<div class='validation_error'>Er was een probleem bij het verzenden. Fouten staan hieronder aangeduid.</div>";
}

add_filter( 'gform_validation_message_9', 'change_message_into_fr', 10, 2 );
add_filter( 'gform_validation_message_14', 'change_message_into_fr', 10, 2 );
add_filter( 'gform_validation_message_17', 'change_message_into_fr', 10, 2 );
add_filter( 'gform_validation_message_22', 'change_message_into_fr', 10, 2 );
add_filter( 'gform_validation_message_27', 'change_message_into_fr', 10, 2 );
add_filter( 'gform_validation_message_37', 'change_message_into_fr', 10, 2 );
add_filter( 'gform_validation_message_40', 'change_message_into_fr', 10, 2 );

function change_message_into_fr( $message, $form ) {
    return "<div class='validation_error'>Il y avait un problème à la soumission. Les erreurs sont marquées ci-dessous.</div>";
}


/*8. limit payment methods for direct debts*/
function jgib_LimitPaymentMethods()
{
	if(is_page('become-chimp-guardian') or is_page('word-chimpvoogd') or is_page('parrainez-un-chimpanze') or is_page('donations/donate') or is_page('giften/doe-een-gift') or is_page('dons/faites-un-don')){
	wp_register_script('lpm_script', get_stylesheet_directory_uri() . '/js/filterpayments.js', array('jquery'),'1.1', true);
    wp_enqueue_script('lpm_script');	
	}
}
add_action('wp_enqueue_scripts', 'jgib_LimitPaymentMethods');

/*9. write field validation of gravity form in core log */
add_filter( 'gform_validation', 'log_validation_errors', 50 );
function log_validation_errors( $validation_result ) {
    $form = $validation_result['form'];
    foreach ( $form['fields'] as $field ) {
        if ( $field->failed_validation ) {
            GFCommon::log_error( "form #{$form['id']}: validate() - failed: {$field->label}({$field->id} - {$field->type}) - message: {$field->validation_message}" );
        }
    }
   return $validation_result;
}

/*12. retrieve the fourth last newsitem*/

function jgib_FourthNewsTitle() {
  ob_start();
   get_template_part('fourth-news-title');
   return ob_get_clean();   
} 
add_shortcode( 'fourth-news-title_shortcode', 'jgib_FourthNewsTitle' );

/*
function jgib_FourthNewsExcerpt() {
ob_start();

$language =  pll_current_language( $field = 'slug' );
   
$args = array(
    'posts_per_page' => 1,
    'post_type' => 'post',
    'lang' => $language,
    'offset' => 3
   ); 

$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) :
while ( $the_query->have_posts() ) : $the_query->the_post();
  the_excerpt();
  
endwhile;
endif;

wp_reset_postdata();
return ob_get_clean(); 
} 
add_shortcode( 'fourth-news-excerpt_shortcode', 'jgib_FourthNewsExcerpt' );

function jgib_FourthNewsImage() {
   ob_start();
   get_template_part('fourth-news-image');
   return ob_get_clean();   
} 
add_shortcode('fourth-news-image_shortcode', 'jgib_FourthNewsImage' );
*/

/*13. put icon before page title*/

/*10. provide left footer*/
function jgi_ProvideLeftFooter() {
   ob_start();
   
   $language =  pll_current_language( $field = 'slug' );
   switch($language)
	{
		case "en":
			$message = "<div class='text-left'>Jane Goodall Institute Belgium asbl/vzw<br />
            <i>To empower people of all ages to make a difference for all living things</i></div>";
			break;
			
		case "nl":
			$message = "<div class='text-left'>Jane Goodall Institute Belgium vzw<br />
			            <i>Om mensen van alle leeftijden in staat te stellen een verschil te maken voor alle levende wezens</i></div>";
			break;

		case "fr":
			$message = "<div class='text-left'>Jane Goodall Institute Belgium asbl<br />
			            <i>Encourager les gens de tous âges à agir pour le bénéfice de l'ensemble des êtres vivants</i></div>";
			break;

	}
 	echo $message;
	
    return ob_get_clean();   
} 
add_shortcode( 'leftfooter_shortcode', 'jgi_ProvideLeftFooter' );


/*11. provide buttons in footer*/
function jgib_ProvideButtons() {
   ob_start();
   
   $language =  pll_current_language( $field = 'slug' );
   switch($language)
	{
		case "en":
			$buttons = "<div><a class='footerbutton first' href='http://localhost:8080/jgib2017/chimpanzee-adoption'><span>Adoptions<span></a>
			            <a class='footerbutton' href='http://localhost:8080/jgib2017/donations'><span>Donations</span></a>
			            <a class='footerbutton' href='http://localhost:8080/jgib2017/subscribe-to-newsletter'><span>Newsletter</span></a></div>";
			
		break;

		case "nl":
			$buttons = "<div><a class='footerbutton first' href='http://localhost:8080/jgib2017/chimpansee-adoptie'><span>Adopties<span></a>
			            <a class='footerbutton' href='http://localhost:8080/jgib2017/giften'><span>Giften</span></a>
			            <a class='footerbutton' href='http://localhost:8080/jgib2017/inschrijven-voor-nieuwsbrief'><span>Nieuwsbrief</span></a></div>";
			
		break;

		case "fr":
			$buttons = "<div><a class='footerbutton first' href='http://localhost:8080/jgib2017/adoption-chimpanze'><span>Adoptions<span></a>
			            <a class='footerbutton' href='http://localhost:8080/jgib2017/dons'><span>Dons</span></a>
			            <a class='footerbutton' href='http://localhost:8080/jgib2017/sinscrire-au-newsletter'><span>Newsletter</span></a></div>";
								
		break;

	}
 	echo $buttons;
	
    return ob_get_clean();   
} 
add_shortcode( 'buttons_shortcode', 'jgib_ProvideButtons' );

/*12. provide countries for dropdown of Gravity forms*/
add_filter( 'gform_countries', 'remove_country' );
function remove_country( $countries ){
    return array( 'Albania', 'Andorra', 'Austria', 'Belgium', 'Bosnia and Herzegovina', 'Bulgaria', 'Croatia', 'Czech republic', 'Cyprus', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Iceland', 'Ireland', 'Italy', 'Latvia',
     'Liechtenstein', 'Lithuania', 'Luxemburg', 'Macedonia', 'Malta', 'Moldavia', 'Monaco', 'Netherlands', 'Norway', 'Poland', 'Portugal', 'Romania', 'San Marino', 'Serbia', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'Switzerland', 'Turkey', 'Ukraine', 'United Kingdom', 'Vatican City' );
}


 

?>