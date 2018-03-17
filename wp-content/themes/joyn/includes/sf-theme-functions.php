<?php

	/*
	*
	*	Swift Framework Theme Functions
	*	------------------------------------------------
	*	Swift Framework v3.0
	* 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
	*
	*	sf_run_migration()
	*	sf_theme_opts_name()
	*	sf_theme_activation()
	*	sf_html5_ie_scripts()
	*	sf_admin_bar_menu()
	*	sf_add_portfolio_category_meta()
	*	sf_edit_portfolio_category_meta()
	*	sf_save_portfolio_category_meta()
	*	sf_fullscreen_search()
	*	sf_nextprev_navigation()
	*	sf_joyn_port_post_icon()
	*
	*
	*	OVERRIDES
	*	sf_header_wrap()
	*	sf_get_search()
	*	sf_header_aux()
	*	sf_ajaxsearch()
	*	sf_portfolio_filter()
	*
	*/

	/* CUSTOMIZER COLOUR MIGRATION
	================================================== */
    function sf_run_migration() {
        $GLOBALS['sf_customizer']['design_style_type'] = get_option('design_style_type', 'minimal');
        $GLOBALS['sf_customizer']['accent_color'] = get_option('accent_color', '#fe504f');
        $GLOBALS['sf_customizer']['accent_alt_color'] = get_option('accent_alt_color', '#ffffff');
        $GLOBALS['sf_customizer']['secondary_accent_color'] = get_option('secondary_accent_color', '#222222');
        $GLOBALS['sf_customizer']['secondary_accent_alt_color'] = get_option('secondary_accent_alt_color', '#ffffff');
        $GLOBALS['sf_customizer']['page_bg_color'] = get_option('page_bg_color', '#222222');
        $GLOBALS['sf_customizer']['inner_page_bg_transparent'] = get_option('inner_page_bg_transparent', 'color');
        $GLOBALS['sf_customizer']['inner_page_bg_color'] = get_option('inner_page_bg_color', '#FFFFFF');
        $GLOBALS['sf_customizer']['section_divide_color'] = get_option('section_divide_color', '#e4e4e4');
        $GLOBALS['sf_customizer']['alt_bg_color'] = get_option('alt_bg_color', '#f7f7f7');
        $GLOBALS['sf_customizer']['topbar_bg_color'] = get_option('topbar_bg_color', '#ffffff');
        $GLOBALS['sf_customizer']['topbar_text_color'] = get_option('topbar_text_color', '#222222');
        $GLOBALS['sf_customizer']['topbar_link_color'] = get_option('topbar_link_color', '#666666');
        $GLOBALS['sf_customizer']['topbar_link_hover_color'] = get_option('topbar_link_hover_color', '#fe504f');
        $GLOBALS['sf_customizer']['topbar_divider_color'] = get_option('topbar_divider_color', '#e3e3e3');
        $GLOBALS['sf_customizer']['header_bg_color'] = get_option('header_bg_color', '#ffffff');
        $GLOBALS['sf_customizer']['header_bg_transparent'] = get_option('header_bg_transparent', 'color');
        $GLOBALS['sf_customizer']['header_border_color'] = get_option('header_border_color', '#e4e4e4');
        $GLOBALS['sf_customizer']['header_text_color'] = get_option('header_text_color', '#222');
        $GLOBALS['sf_customizer']['header_link_color'] = get_option('header_link_color', '#222');
        $GLOBALS['sf_customizer']['header_link_hover_color'] = get_option('header_link_hover_color', '#fe504f');
        $GLOBALS['sf_customizer']['header_divider_style'] = get_option('header_divider_style', 'divider');
        $GLOBALS['sf_customizer']['mobile_menu_bg_color'] = get_option('mobile_menu_bg_color', '#222');
        $GLOBALS['sf_customizer']['mobile_menu_divider_color'] = get_option('mobile_menu_divider_color', '#444');
        $GLOBALS['sf_customizer']['mobile_menu_text_color'] = get_option('mobile_menu_text_color', '#e4e4e4');
        $GLOBALS['sf_customizer']['mobile_menu_link_color'] = get_option('mobile_menu_link_color', '#fff');
        $GLOBALS['sf_customizer']['mobile_menu_link_hover_color'] = get_option('mobile_menu_link_hover_color', '#fe504f');
        $GLOBALS['sf_customizer']['nav_hover_style'] = get_option('nav_hover_style', 'standard');
        $GLOBALS['sf_customizer']['nav_bg_color'] = get_option('nav_bg_color', '#fff');
        $GLOBALS['sf_customizer']['nav_text_color'] = get_option('nav_text_color', '#252525');
        $GLOBALS['sf_customizer']['nav_bg_hover_color'] = get_option('nav_bg_hover_color', '#f7f7f7');
        $GLOBALS['sf_customizer']['nav_text_hover_color'] = get_option('nav_text_hover_color', '#fe504f');
        $GLOBALS['sf_customizer']['nav_selected_bg_color'] = get_option('nav_selected_bg_color', '#e3e3e3');
        $GLOBALS['sf_customizer']['nav_selected_text_color'] = get_option('nav_selected_text_color', '#fe504f');
        $GLOBALS['sf_customizer']['nav_pointer_color'] = get_option('nav_pointer_color', '#07c1b6');
        $GLOBALS['sf_customizer']['nav_sm_bg_color'] = get_option('nav_sm_bg_color', '#FFFFFF');
        $GLOBALS['sf_customizer']['nav_sm_text_color'] = get_option('nav_sm_text_color', '#666666');
        $GLOBALS['sf_customizer']['nav_sm_bg_hover_color'] = get_option('nav_sm_bg_hover_color', '#f7f7f7');
        $GLOBALS['sf_customizer']['nav_sm_text_hover_color'] = get_option('nav_sm_text_hover_color', '#000000');
        $GLOBALS['sf_customizer']['nav_sm_selected_text_color'] = get_option('nav_sm_selected_text_color', '#000000');
        $GLOBALS['sf_customizer']['nav_divider'] = get_option('nav_divider', 'solid');
        $GLOBALS['sf_customizer']['nav_divider_color'] = get_option('nav_divider_color', '#f0f0f0');
        $GLOBALS['sf_customizer']['overlay_menu_bg_color'] = get_option('overlay_menu_bg_color', '#fe504f');
        $GLOBALS['sf_customizer']['overlay_menu_link_color'] = get_option('overlay_menu_link_color', '#ffffff');
        $GLOBALS['sf_customizer']['overlay_menu_link_hover_color'] = get_option('overlay_menu_link_hover_color', '#fe504f');
        $GLOBALS['sf_customizer']['overlay_menu_link_hover_bg_color'] = get_option('overlay_menu_link_hover_bg_color', '#ffffff');
        $GLOBALS['sf_customizer']['promo_bar_bg_color'] = get_option('promo_bar_bg_color', '#e4e4e4');
        $GLOBALS['sf_customizer']['promo_bar_text_color'] = get_option('promo_bar_text_color', '#222');
        $GLOBALS['sf_customizer']['breadcrumb_bg_color'] = get_option('breadcrumb_bg_color', '#e4e4e4');
        $GLOBALS['sf_customizer']['breadcrumb_text_color'] = get_option('breadcrumb_text_color', '#666666');
        $GLOBALS['sf_customizer']['breadcrumb_link_color'] = get_option('breadcrumb_link_color', '#999999');
        $GLOBALS['sf_customizer']['page_heading_bg_color'] = get_option('page_heading_bg_color', '#f7f7f7');
        $GLOBALS['sf_customizer']['page_heading_text_color'] = get_option('page_heading_text_color', '#222222');
        $GLOBALS['sf_customizer']['page_heading_text_align'] = get_option('page_heading_text_align', 'left');
        $GLOBALS['sf_customizer']['body_color'] = get_option('body_color', '#222222');
        $GLOBALS['sf_customizer']['body_alt_color'] = get_option('body_alt_color', '#222222');
        $GLOBALS['sf_customizer']['link_color'] = get_option('link_color', '#444444');
        $GLOBALS['sf_customizer']['link_hover_color'] = get_option('link_hover_color', '#999999');
        $GLOBALS['sf_customizer']['h1_color'] = get_option('h1_color', '#222222');
        $GLOBALS['sf_customizer']['h2_color'] = get_option('h2_color', '#222222');
        $GLOBALS['sf_customizer']['h3_color'] = get_option('h3_color', '#222222');
        $GLOBALS['sf_customizer']['h4_color'] = get_option('h4_color', '#222222');
        $GLOBALS['sf_customizer']['h5_color'] = get_option('h5_color', '#222222');
        $GLOBALS['sf_customizer']['h6_color'] = get_option('h6_color', '#222222');
        $GLOBALS['sf_customizer']['overlay_bg_color'] = get_option('overlay_bg_color', '#fe504f');
        $GLOBALS['sf_customizer']['overlay_text_color'] = get_option('overlay_text_color', '#ffffff');
        $GLOBALS['sf_customizer']['article_review_bar_alt_color'] = get_option('article_review_bar_alt_color', '#f7f7f7');
        $GLOBALS['sf_customizer']['article_review_bar_color'] = get_option('article_review_bar_color', '#2e2e36');
        $GLOBALS['sf_customizer']['article_review_bar_text_color'] = get_option('article_review_bar_text_color', '#fff');
        $GLOBALS['sf_customizer']['article_extras_bg_color'] = get_option('article_extras_bg_color', '#f7f7f7');
        $GLOBALS['sf_customizer']['article_np_bg_color'] = get_option('article_np_bg_color', '#444');
        $GLOBALS['sf_customizer']['article_np_text_color'] = get_option('article_np_text_color', '#fff');
        $GLOBALS['sf_customizer']['input_bg_color'] = get_option('input_bg_color', '#f7f7f7');
        $GLOBALS['sf_customizer']['input_text_color'] = get_option('input_text_color', '#222222');
        $GLOBALS['sf_customizer']['icon_container_bg_color'] = get_option('icon_container_bg_color', '#1dc6df');
        $GLOBALS['sf_customizer']['sf_icon_color'] = get_option('sf_icon_color', '#1dc6df');
        $GLOBALS['sf_customizer']['icon_container_hover_bg_color'] = get_option('icon_container_hover_bg_color', '#222');
        $GLOBALS['sf_customizer']['sf_icon_alt_color'] = get_option('sf_icon_alt_color', '#ffffff');
        $GLOBALS['sf_customizer']['boxed_content_color'] = get_option('boxed_content_color', '#07c1b6');
        $GLOBALS['sf_customizer']['share_button_bg'] = get_option('share_button_bg', '#fe504f');
        $GLOBALS['sf_customizer']['share_button_text'] = get_option('share_button_text', '#ffffff');
        $GLOBALS['sf_customizer']['bold_rp_bg'] = get_option('bold_rp_bg', '#e3e3e3');
        $GLOBALS['sf_customizer']['bold_rp_text'] = get_option('bold_rp_text', '#222');
        $GLOBALS['sf_customizer']['bold_rp_hover_bg'] = get_option('bold_rp_hover_bg', '#fe504f');
        $GLOBALS['sf_customizer']['bold_rp_hover_text'] = get_option('bold_rp_hover_text', '#ffffff');
        $GLOBALS['sf_customizer']['tweet_slider_bg'] = get_option('tweet_slider_bg', '#1dc6df');
        $GLOBALS['sf_customizer']['tweet_slider_text'] = get_option('tweet_slider_text', '#ffffff');
        $GLOBALS['sf_customizer']['tweet_slider_link'] = get_option('tweet_slider_link', '#339933');
        $GLOBALS['sf_customizer']['tweet_slider_link_hover'] = get_option('tweet_slider_link_hover', '#ffffff');
        $GLOBALS['sf_customizer']['testimonial_slider_bg'] = get_option('testimonial_slider_bg', '#1dc6df');
        $GLOBALS['sf_customizer']['testimonial_slider_text'] = get_option('testimonial_slider_text', '#ffffff');
        $GLOBALS['sf_customizer']['footer_bg_color'] = get_option('footer_bg_color', '#222222');
        $GLOBALS['sf_customizer']['footer_text_color'] = get_option('footer_text_color', '#cccccc');
        $GLOBALS['sf_customizer']['footer_link_color'] = get_option('footer_link_color', '#ffffff');
        $GLOBALS['sf_customizer']['footer_link_hover_color'] = get_option('footer_link_hover_color', '#cccccc');
        $GLOBALS['sf_customizer']['footer_border_color'] = get_option('footer_border_color', '#333333');
        $GLOBALS['sf_customizer']['copyright_bg_color'] = get_option('copyright_bg_color', '#222222');
        $GLOBALS['sf_customizer']['copyright_text_color'] = get_option('copyright_text_color', '#999999');
        $GLOBALS['sf_customizer']['copyright_link_color'] = get_option('copyright_link_color', '#ffffff');
        $GLOBALS['sf_customizer']['copyright_link_hover_color'] = get_option('copyright_link_hover_color', '#cccccc');
        update_option( 'sf_customizer', $GLOBALS['sf_customizer']);
    }

    if (!isset($GLOBALS['sf_customizer'])) {
        $GLOBALS['sf_customizer'] = get_option('sf_customizer', array());
        if (empty($GLOBALS['sf_customizer'])) {
            sf_run_migration();
        }
    }

	/* THEME OPTIONS NAME
	================================================== */
	if (!function_exists('sf_theme_opts_name')) {
		function sf_theme_opts_name() {
			return 'sf_joyn_options';
		}
	}

	/* THEME ACTIVATION
	================================================== */
	if (!function_exists('sf_theme_activation')) {
		function sf_theme_activation() {
			global $pagenow;
			if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
				// set frontpage to display_posts
				update_option('show_on_front', 'posts');

				// Update sf_theme option for framework plugin
				update_option( 'sf_theme', 'joyn' );

				// provide hook so themes can execute theme specific functions on activation
				do_action('sf_theme_activation');

				// flush rewrite rules
				flush_rewrite_rules();

				// redirect to options page
				//header( 'Location: '.admin_url().'admin.php?page=_sf_options&sf_welcome=true' ) ;
				header( 'Location: '.admin_url().'themes.php?page=install-required-plugins' ) ;
			}
		}
		add_action('after_switch_theme', 'sf_theme_activation');
	}

	/* THEME DEACTIVATION
	================================================== */
	if (!function_exists('sf_theme_deactivation')) {
		function sf_theme_deactivation() {
			// Delete sf_theme_option
			delete_option( 'sf_theme' );
		}
		add_action('switch_theme', 'sf_theme_deactivation');
	}


	/* REQUIRED IE8 COMPATIBILITY SCRIPTS
	================================================== */
	if (!function_exists('sf_html5_ie_scripts')) {
	    function sf_html5_ie_scripts() {
	        $theme_url = get_template_directory_uri();
	        $ie_scripts = '';

	        $ie_scripts .= '<!--[if lt IE 9]>';
	        $ie_scripts .= '<script data-cfasync="false" src="'.$theme_url.'/js/respond.js"></script>';
	        $ie_scripts .= '<script data-cfasync="false" src="'.$theme_url.'/js/html5shiv.js"></script>';
	        $ie_scripts .= '<script data-cfasync="false" src="'.$theme_url.'/js/excanvas.compiled.js"></script>';
	        $ie_scripts .= '<![endif]-->';
	        echo $ie_scripts;
	    }
	    add_action('wp_head', 'sf_html5_ie_scripts');
	}

	/* CUSTOM ADMIN MENU ITEMS
	================================================== */
	if(!function_exists('sf_admin_bar_menu')) {
		function sf_admin_bar_menu() {

			global $wp_admin_bar;

			if ( current_user_can( 'manage_options' ) ) {

				$theme_customizer = array(
					'id' => '1',
					'title' => __('Color Customizer', 'swiftframework'),
					'href' => admin_url('/customize.php'),
					'meta' => array('target' => 'blank')
				);

				$wp_admin_bar->add_menu($theme_customizer);

			}

		}
		add_action('admin_bar_menu', 'sf_admin_bar_menu', 99);
	}

	/* PORTFOLIO CATEGORY META
	================================================== */
	function sf_add_portfolio_category_meta() {
		?>
		<div class="form-field">
			<label for="term_meta[icon]"><?php _e( 'Category Icon', 'swiftframework' ); ?></label>
			<input type="text" name="term_meta[icon]" id="term_meta[icon]" value="">
			<p class="description"><?php _e( 'Enter a Font Awesome or Gizmo class name to display an icon next to the category in the portfolio filter.','swiftframework' ); ?></p>
		</div>
	<?php
	}
	add_action( 'portfolio-category_add_form_fields', 'sf_add_portfolio_category_meta', 10, 2 );

	// Edit term page
	function sf_edit_portfolio_category_meta($term) {
		$t_id = $term->term_id;
		$term_meta = get_option( "portfolio-category_$t_id" );
		?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[icon]"><?php _e( 'Category Icon', 'swiftframework' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[icon]" id="term_meta[icon]" value="<?php echo esc_attr( $term_meta['icon'] ) ? esc_attr( $term_meta['icon'] ) : ''; ?>">
				<p class="description"><?php _e( 'Enter a Font Awesome or Gizmo class name to display an icon next to the category in the portfolio filter.','swiftframework' ); ?></p>
			</td>
		</tr>
	<?php
	}
	add_action( 'portfolio-category_edit_form_fields', 'sf_edit_portfolio_category_meta', 10, 2 );

	// Save extra taxonomy fields callback function.
	function sf_save_portfolio_category_meta( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "portfolio-category_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
			// Save the option array.
			update_option( "portfolio-category_$t_id", $term_meta );
		}
	}
	add_action( 'edited_portfolio-category', 'sf_save_portfolio_category_meta', 10, 2 );
	add_action( 'create_portfolio-category', 'sf_save_portfolio_category_meta', 10, 2 );


	/* HOME PRELOADER
	================================================== */
	if (!function_exists('sf_home_preloader')) {
		function sf_home_preloader() {

			global $sf_options;
			$home_preloader = false;
			if (isset($sf_options['home_preloader'])) {
			$home_preloader = $sf_options['home_preloader'];
			}

			if (!$home_preloader || is_paged() || !(is_home() || is_front_page())) {
				return;
			}

			$logo = $retina_logo = $alt_logo = array();
//			if (isset($sf_options['logo_upload'])) {
//			$logo = $sf_options['logo_upload'];
//			}
//			$logo_alt = get_bloginfo( 'name' );
			{ ?>

				<div id="sf-home-preloader">

					<?php if (isset($logo['url']) && $logo['url'] != "") { ?>
						<div id="preload-logo">
							<img class="standard" src="<?php echo $logo['url']; ?>" alt="<?php echo $logo_alt; ?>" height="<?php echo $logo['height']; ?>" width="<?php echo $logo['width']; ?>" />
						</div>
					<?php } ?>

					<?php echo sf_loading_animation('preloader-loading', 'preloader'); ?>

				</div>

			<?php }
		}
		add_action('sf_before_page_container', 'sf_home_preloader', 4);
	}


	/* FULLSCREEN SEARCH
	================================================== */
	if (!function_exists('sf_fullscreen_search')) {
		function sf_fullscreen_search() {

			global $sf_options;
			$header_search_type = $sf_options['header_search_type'];
			$header_search_pt = $sf_options['header_search_pt'];
			$ajax_url = admin_url('admin-ajax.php');
			$search_title = apply_filters('sf_fs_search_title', __("Search", "swiftframework"));

			// Overlay Search
			if ($header_search_type == "fs-search-on") { ?>

				<div id="fullscreen-search">

					<!--<a href="#" class="fs-overlay-close">
						<i class="ss-delete"></i>
					</a>-->

					<div class="search-wrap" data-ajaxurl="<?php echo $ajax_url; ?>">

						<div class="title"><?php echo $search_title; ?></div>

						<div class="fs-search-bar">
							<form method="get" class="ajax-search-form" action="<?php echo home_url(); ?>/">
								<i class="ss-search"></i>
								<?php if ( $header_search_pt != "any" ) { ?>
									<input type="hidden" name="post_type" value="<?php echo $header_search_pt; ?>" />
								<?php } ?>
								<input id="fs-search-input" type="text" name="s" autocomplete="off">
							</form>
						</div>

						<div class="ajax-loading-wrap">
							<?php echo sf_loading_animation('', 'ajax-loading'); ?>
						</div>

						<div class="ajax-search-results"></div>

					</div>

				</div>

			<?php }
		}
	}

	/* FULLSCREEN SEARCH
	================================================== */
	if (!function_exists('sf_fullscreen_supersearch')) {
		function sf_fullscreen_supersearch() { ?>

			<div id="fullscreen-supersearch">

				<div class="supersearch-wrap">
					<?php echo sf_super_search(); ?>
				</div>

			</div>

		<?php }
	}

	/* NEXT/PREV NAVIGATION
	================================================== */
	if (!function_exists('sf_nextprev_navigation')) {
		function sf_nextprev_navigation() {

			global $sf_options;

			// Pagiantion style
			$pagination_style = $sf_options['pagination_style'];

			// Portfolio category navigation
			$enable_category_navigation = $sf_options['enable_category_navigation'];

			if (!(is_singular('post') || is_singular('portfolio') || is_singular('product')) || $pagination_style != "fs-arrow") {
				return;
			}

			$taxonomy = "category";

			if ( is_singular('portfolio') ) {
				$taxonomy = "portfolio-category";
			} else if ( is_singular('product') ) {
				$taxonomy = "product_cat";
			}

			// Get next/prev post
			$prev_post = get_next_post($enable_category_navigation, '', $taxonomy);
			$next_post = get_previous_post($enable_category_navigation, '', $taxonomy);

			if (!empty( $prev_post )) {

				$postID = $prev_post->ID;
				$prev_permalink = get_permalink($postID);
				$item_subtitle = sf_get_post_meta($postID, 'sf_portfolio_subtitle', true);
				$use_thumb_content = sf_get_post_meta($postID, 'sf_thumbnail_content_main_detail', true);

				$image = $media_image_url = $image_id = "";

				if ($use_thumb_content) {
				$media_image = rwmb_meta('sf_thumbnail_image', 'type=image&size=full', $postID);
				} else {
				$media_image = rwmb_meta('sf_detail_image', 'type=image&size=full', $postID);
				}

				foreach ($media_image as $detail_image) {
					$image_id = $detail_image['ID'];
					$media_image_url = $detail_image['url'];
					break;
				}

				if (!$media_image) {
					$media_image = get_post_thumbnail_id($postID);
					$image_id = $media_image;
					$media_image_url = wp_get_attachment_url( $media_image, 'full' );
				}

				$detail_image = sf_aq_resize($media_image_url, 80, 80, true, false);
				$image_alt = sf_get_post_meta($image_id, '_wp_attachment_image_alt', true);

				if ($detail_image) {
					$image = '<img itemprop="image" src="'.$detail_image[0].'" width="'.$detail_image[1].'" height="'.$detail_image[2].'" alt="'.$image_alt.'" />';
				}

				?>

				<?php if ($image != "") { ?>
				<div id="prev-article-pagination" class="window-arrow-nav prev-item has-img">
				<?php } else { ?>
				<div id="prev-article-pagination" class="window-arrow-nav prev-item">
				<?php } ?>

					<a href="<?php echo $prev_permalink; ?>">
						<div class="nav-transition">
							<div class="overlay-wrap">
								<i class="ss-navigateleft"></i>
								<?php if ($image != "") { ?>
								<figure class="pagination-article-image">
									<?php echo $image; ?>
								</figure>
								<?php } ?>
							</div>
						</div>

						<?php if ($item_subtitle != "") { ?>
						<div class="pagination-article-details has-subtitle">
							<h5><?php echo $prev_post->post_title; ?></h5>
							<p><?php echo $item_subtitle; ?></p>
						<?php } else { ?>
						<div class="pagination-article-details no-subtitle">
							<h5><?php echo $prev_post->post_title; ?></h5>
						<?php } ?>
						</div>
					</a>
				</div>
			<?php }

		 	if (!empty( $next_post )) {

		 		$postID = $next_post->ID;
		 		$next_permalink = get_permalink($postID);
		 		$item_subtitle = sf_get_post_meta($postID, 'sf_portfolio_subtitle', true);
		 		$use_thumb_content = sf_get_post_meta($postID, 'sf_thumbnail_content_main_detail', true);

		 		$image = $media_image_url = $image_id = "";

		 		if ($use_thumb_content) {
		 		$media_image = rwmb_meta('sf_thumbnail_image', 'type=image&size=full', $postID);
		 		} else {
		 		$media_image = rwmb_meta('sf_detail_image', 'type=image&size=full', $postID);
		 		}

		 		foreach ($media_image as $detail_image) {
		 			$image_id = $detail_image['ID'];
		 			$media_image_url = $detail_image['url'];
		 			break;
		 		}

		 		if (!$media_image) {
		 			$media_image = get_post_thumbnail_id($postID);
		 			$image_id = $media_image;
		 			$media_image_url = wp_get_attachment_url( $media_image, 'full' );
		 		}

		 		$detail_image = sf_aq_resize($media_image_url, 80, 80, true, false);
		 		$image_alt = sf_get_post_meta($image_id, '_wp_attachment_image_alt', true);

		 		if ($detail_image) {
		 			$image = '<img itemprop="image" src="'.$detail_image[0].'" width="'.$detail_image[1].'" height="'.$detail_image[2].'" alt="'.$image_alt.'" />';
		 		}

		 		?>

		 		<?php if ($image != "") { ?>
		 		<div id="next-article-pagination" class="window-arrow-nav next-item has-img">
		 		<?php } else { ?>
		 		<div id="next-article-pagination" class="window-arrow-nav next-item">
		 		<?php } ?>

					<a href="<?php echo $next_permalink; ?>">

						<div class="nav-transition">
							<div class="overlay-wrap">
								<i class="ss-navigateright"></i>
								<?php if ($image != "") { ?>
								<figure class="pagination-article-image">
								<?php echo $image; ?>
								</figure>
								<?php } ?>
							</div>
						</div>

						<?php if ($item_subtitle != "") { ?>
						<div class="pagination-article-details has-subtitle">
							<h5><?php echo $next_post->post_title; ?></h5>
							<p><?php echo $item_subtitle; ?></p>
						<?php } else { ?>
						<div class="pagination-article-details no-subtitle">
							<h5><?php echo $next_post->post_title; ?></h5>
						<?php } ?>
						</div>
					</a>
				</div>
		 	<?php }
		}
		add_action('sf_main_container_start', 'sf_nextprev_navigation', 50);
	}


	/* GET THUMB TYPE
	================================================== */
	if (!function_exists('sf_get_thumb_type')) {
		function sf_get_thumb_type() {
			global $sf_options;
			$thumbnail_type = "standard";
			if (isset($sf_options['thumbnail_type'])) {
			$thumbnail_type = $sf_options['thumbnail_type'];
			}

			if ($thumbnail_type != "") {
				return 'thumbnail-'.$thumbnail_type;
			}

		}
	}


	/*
	*
	*	Swift Framework Overrides
	*	------------------------------------------------
	*	Joyn specific functionality
	*
	*/


	/* FILTERS
	================================================== */

	// Post icon
	function sf_joyn_port_post_icon() {
		return "ss-view";
	}
	add_filter('sf_post_icon', "sf_joyn_port_post_icon");
	add_filter('sf_port_post_icon', "sf_joyn_port_post_icon");

	// Add to cart icon
	function sf_joyn_add_to_cart_icon() {
		return '<i class="ss-plus"></i>';
	}
	add_filter('add_to_cart_icon', 'sf_joyn_add_to_cart_icon');

	// Wishlist icon
	function sf_joyn_wishlist_menu_icon() {
		return '<i class="fa-star"></i>';
	}
	add_filter('sf_wishlist_menu_icon', 'sf_joyn_wishlist_menu_icon');


	/*
	*	HEADER WRAP OVERRIDE
	*	------------------------------------------------
	*	@base - /swift-framework/core/sf-header.php
	*
	================================================== */
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
					<div class="col-sm-6 tb-left"><?php echo $tb_left_output; ?></div>
					<div class="col-sm-6 tb-right"><?php echo $tb_right_output; ?></div>
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


	/*
	*	HEADER MENU OVERRIDE
	*	------------------------------------------------
	*	@base - /swift-framework/core/sf-header.php
	*
	================================================== */
	if (!function_exists('sf_main_menu')) {
		function sf_main_menu($id, $layout = "") {

			// VARIABLES
			global $sf_options, $post;

			$show_cart = $sf_options['show_cart'];
			$show_wishlist = $sf_options['show_wishlist'];
			$header_search_type = $sf_options['header_search_type'];
			$vertical_header_text = __($sf_options['vertical_header_text'], 'swiftframework');
			$page_menu = $menu_output = $menu_full_output = $menu_with_search_output = $menu_float_output = $menu_vert_output = "";

			if ($post) {
			$page_menu = sf_get_post_meta($post->ID, 'sf_page_menu', true);
			}
			$main_menu_args = array(
				'echo'            => false,
				'theme_location' => 'main_navigation',
				'walker' => new sf_mega_menu_walker,
				'fallback_cb' => '',
				'menu' => $page_menu
			);


			// MENU OUTPUT
			$menu_output .= '<nav id="'.$id.'" class="std-menu clearfix">'. "\n";

			if(function_exists('wp_nav_menu')) {
				if (has_nav_menu('main_navigation')) {
					$menu_output .= wp_nav_menu( $main_menu_args );
				}
				else {
					$menu_output .= '<div class="no-menu">'.__("Please assign a menu to the Main Menu in Appearance > Menus", "swiftframework").'</div>';
				}
			}
			$menu_output .= '</nav>'. "\n";


			// FULL WIDTH MENU OUTPUT
			if ($layout == "full") {

				$menu_full_output .= '<div class="container">'. "\n";
				$menu_full_output .= '<div class="row">'. "\n";
				$menu_full_output .= '<div class="menu-left">'. "\n";
				$menu_full_output .= $menu_output . "\n";
				$menu_full_output .= '</div>'. "\n";
				$menu_full_output .= '<div class="menu-right">'. "\n";
				$menu_full_output .= '<nav class="std-menu">'. "\n";
				$menu_full_output .= '<ul class="menu">'. "\n";
				$menu_full_output .= sf_get_search($header_search_type);
				if ($show_cart) {
				$menu_full_output .= sf_get_cart();
				}
				if ( class_exists( 'YITH_WCWL_UI' ) && $show_wishlist)  {
				$menu_full_output .= sf_get_wishlist();
				}
				$menu_full_output .= '</ul>'. "\n";
				$menu_full_output .= '</nav>'. "\n";
				$menu_full_output .= '</div>'. "\n";
				$menu_full_output .= '</div>'. "\n";
				$menu_full_output .= '</div>'. "\n";

				$menu_output = $menu_full_output;

			} else if ($layout == "float" || $layout == "float-2") {

				$menu_float_output .= '<div class="float-menu container">'. "\n";
				$menu_float_output .= $menu_output . "\n";
				if ($layout == "float-2") {
					$header_right_output = sf_header_aux('right'). "\n";
					$menu_float_output .= '<div class="header-right">'.$header_right_output.'</div>'. "\n";
				}
				$menu_float_output .= '</div>'. "\n";

				$menu_output = $menu_float_output;

			} else if ($layout == "vertical") {

				$menu_vert_output .= $menu_output . "\n";
				$menu_vert_output .= '<div class="vertical-menu-bottom">'. "\n";
				$menu_vert_output .= sf_header_aux('right');
				$menu_vert_output .= '<div class="copyright">'.do_shortcode(stripslashes($vertical_header_text)).'</div>'. "\n";
				$menu_vert_output .= '</div>'. "\n";

				$menu_output = $menu_vert_output;
			}

			// MENU RETURN
			return $menu_output;
		}
	}


	/*
	*	HEADER SEARCH OVERRIDE
	*	------------------------------------------------
	*	@base - /swift-framework/core/sf-header.php
	*
	================================================== */
	if (!function_exists('sf_get_search')) {
		function sf_get_search($type) {

			if ($type == "search-off") {
				return;
			}

			global $sf_options;
			$header_search_type = $sf_options['header_search_type'];
			$header_search_pt = $sf_options['header_search_pt'];
			$ajax_url = admin_url('admin-ajax.php');

			if ($type == "aux") {
				$type = $header_search_type;
			}

			$search_output = "";

			if ($type == "fs-search-on") {
				$search_output .= '<li class="menu-search parent"><a href="#" class="header-search-link fs-header-search-link"><i class="ss-search"></i></a></li>'. "\n";
			} else {
				$search_output .= '<li class="menu-search parent"><a href="#" class="header-search-link header-search-link-alt"><i class="ss-search"></i></a>'. "\n";
				$search_output .= '<div class="ajax-search-wrap search-wrap" data-ajaxurl="'.$ajax_url.'"><div class="ajax-loading"></div><form method="get" class="ajax-search-form" action="'.home_url().'/">';
				if ($header_search_pt != "any") {
				$search_output .= '<input type="hidden" name="post_type" value="'.$header_search_pt.'" />';
				}
				$search_output .= '<input type="text" placeholder="'.__("Search", "swiftframework").'" name="s" autocomplete="off" /></form><div class="ajax-search-results"></div></div>'. "\n";
				$search_output .= '</li>'. "\n";
			}

			return $search_output;
		}
	}


	/*
	*	HEADER AUX OVERRIDE
	*	------------------------------------------------
	*	@base - /swift-framework/core/sf-header.php
	*
	================================================== */
	if (!function_exists('sf_header_aux')) {
		function sf_header_aux($aux) {

			global $sf_options;

			$config = array();
			$aux_output = "";

			$header_left_text = __($sf_options['header_left_text'], 'swiftframework');
			$header_right_text = __($sf_options['header_right_text'], 'swiftframework');
			$contact_icon = apply_filters('sf_header_contact_icon', '<i class="ss-mail"></i>');
			$header_search_pt = $sf_options['header_search_pt'];
			$ajax_url = admin_url('admin-ajax.php');

			if ($aux == "left") {
				$config = $sf_options['header_left_config'];
			} else if ($aux == "right") {
				$config = $sf_options['header_right_config'];
			}

			if (!empty($config) && isset($config['enabled'])) {

				foreach ($config['enabled'] as $item_id => $item) {

					if ($item_id == "social") {
						$aux_output .= '<div class="aux-item">' . do_shortcode('[social]'). '</div>'. "\n";
					} else if ($item_id == "aux-links") {
						$aux_output .= '<div class="aux-item">' . sf_aux_links('header-menu', TRUE, "header-1") . '</div>'. "\n";
					} else if ($item_id == "cart") {
						$aux_output .= '<div class="aux-item"><nav class="std-menu"><ul class="menu">' . sf_get_cart() . '</ul></nav></div>'. "\n";
					} else if ($item_id == "wishlist") {
						$aux_output .= '<div class="aux-item"><nav class="std-menu"><ul class="menu">' . sf_get_wishlist() . '</ul></nav></div>'. "\n";
					} else if ($item_id == "supersearch") {
						$aux_output .= '<div class="aux-item"><a href="#" class="fs-supersearch-link"><i class="ss-zoomin"></i><span>'.__("Super Search", "swiftframework").'</span></a></div>'. "\n";
					} else if ($item_id == "overlay-menu") {
						$aux_output .= '<div class="aux-item"><a href="#" class="overlay-menu-link"><span>'.__("Menu", "swiftframework").'</span></a></div>'. "\n";
					} else if ($item_id == "contact") {
						$aux_output .= '<div class="aux-item"><a href="#" class="contact-menu-link">'.$contact_icon.'</a></div>'. "\n";
					} else if ($item_id == "search") {
						$aux_output .= '<div class="aux-item"><nav class="std-menu">'. "\n";
						$aux_output .= '<ul class="menu">'. "\n";
						$aux_output .= sf_get_search('aux');
						$aux_output .= '</ul>'. "\n";
						$aux_output .= '</nav></div>'. "\n";
					} else if ($item_id == "text" && $aux == "left") {
						$aux_output .= '<div class="aux-item text">'.do_shortcode($header_left_text).'</div>'. "\n";
					} else if ($item_id == "text" && $aux == "right") {
						$aux_output .= '<div class="aux-item text">'.do_shortcode($header_right_text).'</div>'. "\n";
					}

				}

			}

			return $aux_output;
		}
	}


	/*
	*	AJAX SEARCH OVERRIDE
	*	------------------------------------------------
	*	@base - /swift-framework/core/sf-header.php
	*
	================================================== */
	if (!function_exists('sf_ajaxsearch')) {
		function sf_ajaxsearch() {
			global $sf_options;
			$page_classes = sf_page_classes();
			$header_layout = $page_classes['header-layout'];
			$header_search_type = $sf_options['header_search_type'];
			$header_search_pt = $sf_options['header_search_pt'];
			$search_term = trim($_POST['s']);
			$search_query_args = array(
				's' => $search_term,
				'post_type' => $header_search_pt,
				'post_status' => 'publish',
				'suppress_filters' => false,
				'numberposts' => -1
			);
			$search_query_args = http_build_query($search_query_args);
			$search_results = get_posts( $search_query_args );
			$count = count($search_results);
			$shown_results = 5;

			if ($header_layout == "header-vert" || $header_layout == "header-vert-right") {
				$shown_results = 2;
			}

			if ($header_search_type == "fs-search-on") {
				$shown_results = 20;
			}

			$search_results_ouput = "";

			if (!empty($search_results)) {

				$sorted_posts = $post_type = array();

				foreach ($search_results as $search_result) {
					$sorted_posts[$search_result->post_type][] = $search_result;
				    // Check we don't already have this post type in the post_type array
				    if (empty($post_type[$search_result->post_type])) {
				    	// Add the post type object to the post_type array
				        $post_type[$search_result->post_type] = get_post_type_object($search_result->post_type);
				    }
				}

				$i = 0;
				foreach ($sorted_posts as $key => $type) {
					$search_results_ouput .= '<div class="search-result-pt">';
			        if(isset($post_type[$key]->labels->name)) {
			            $search_results_ouput .= "<h3>".$post_type[$key]->labels->name."</h3>";
			        } else if(isset($key)) {
			            $search_results_ouput .= "<h3>".$key."</h3>";
			        } else {
			            $search_results_ouput .= "<h3>".__("Other", "swiftframework")."</h3>";
			        }

			        foreach ($type as $post) {

			        	$post_title = get_the_title($post->ID);
			        	$post_date = get_the_time(get_option('date_format'), $post->ID);
			        	$post_permalink = get_permalink($post->ID);

			        	$image = get_the_post_thumbnail( $post->ID, 'thumb-square' );

			        	if ($image) {
			        		$search_results_ouput .= '<div class="search-result has-img">';
			        		$search_results_ouput .= '<div class="search-item-img"><a href="'.$post_permalink.'">'.$image.'</div>';
			        	} else {
			        		$search_results_ouput .= '<div class="search-result">';
			        	}

			            $search_results_ouput .= '<div class="search-item-content">';
			            $search_results_ouput .= '<h4><a href="'.$post_permalink.'">'.$post_title.'</a></h4>';

			            if (get_post_type($post) == "product") {
				            $price = sf_get_post_meta( $post->ID, '_regular_price', true);
				            $sale = sf_get_post_meta( $post->ID, '_sale_price', true);
				            if ($sale != "") {
				           		$price = $sale;
				            }
				            if ($price != "") {
				            $search_results_ouput .= '<span>'.get_woocommerce_currency_symbol().$price.'</span>';
			            	}
			            } else {
			            	$search_results_ouput .= '<time>'.$post_date.'</time>';
			            }

			            $search_results_ouput .= '</div>';

			            $search_results_ouput .= '</div>';

			        	$i++;
			        	if ($i == $shown_results) break;
			        }

			       $search_results_ouput .= '</div>';
			        if ($i == $shown_results) break;
			    }

			    if ($count > 1) {
			    	$search_link = get_search_link( $search_term );
			    	
			    	if (strpos($search_link,'?') !== false) {
			    		$search_link .= '&post_type='. $header_search_pt;
			    	} else {
			    		$search_link .= '?post_type='. $header_search_pt;
			    	}
			    	$search_results_ouput .= '<a href="'.$search_link.'" class="all-results">'.sprintf(__("View all %d results", "swiftframework"), $count).'</a>';
			    }

			} else {

				$search_results_ouput .= '<div class="no-search-results">';
				$search_results_ouput .= '<h5>'.__("No results", "swiftframework").'</h5>';
				$search_results_ouput .= '<p>'.__("No search results could be found, please try another query.", "swiftframework").'</p>';
				$search_results_ouput .= '</div>';

			}

			echo $search_results_ouput;
			die();
		}
		add_action('wp_ajax_sf_ajaxsearch', 'sf_ajaxsearch');
		add_action('wp_ajax_nopriv_sf_ajaxsearch', 'sf_ajaxsearch');
	}


	/*
	*	PORTFOLIO FILTER OVERRIDE
	*	------------------------------------------------
	*	@base - /swift-framework/content/sf-portfolio.php
	*
	================================================== */
	if (!function_exists('sf_portfolio_filter')) {
		function sf_portfolio_filter($style = "basic", $post_type = "portfolio", $parent_category = "", $frontend_display = false) {

			$filter_output = $tax_terms = "";
			$show_all_icon = apply_filters('sf_portfolio_show_all_icon', 'ss-gridlines');

			$taxonomy_name = 'category';

			if ( $post_type != "post") {
				$taxonomy_name = $post_type . '-category';
			}

			if ($parent_category == "" || $parent_category == "All") {
				$tax_terms = sf_get_category_list($taxonomy_name, 1, '', true);
			} else {
				$tax_terms = sf_get_category_list($taxonomy_name, 1, $parent_category, true);
			}

		    $filter_output .= '<div class="filter-wrap clearfix">'. "\n";
		    $filter_output .= '<ul class="post-filter-tabs filtering clearfix">'. "\n";
		    $filter_output .= '<li class="all selected"><a data-filter="*" href="#"><i class="'.$show_all_icon.'"></i><span class="item-name">'. __("Show all", "swiftframework").'</span></a></li>'. "\n";
			foreach ($tax_terms as $tax_term) {
				$term = get_term_by('name', $tax_term, 'portfolio-category');
				$term_meta = $term_icon = "";
				if (isset($term->term_id)) {
				$term_meta = get_option( "portfolio-category_$term->term_id" );
				}
				if (isset($term_meta['icon'])) {
					$term_icon = $term_meta['icon'];
				}
				if ($term) {
					$filter_output .= '<li><a href="#" title="View all ' . $term->name . ' items" class="' . $term->slug . '" data-filter=".' . $term->slug . '">';
					if ($term_icon != "") {
						$filter_output .= '<i class="'.$term_icon.'"></i>';
					}
					$filter_output .= '<span class="item-name">' . $term->name . '</span></a></li>'. "\n";
				} else {
					$filter_output .= '<li><a href="#" title="View all ' . $tax_term . ' items" class="' . $tax_term . '" data-filter=".' . $tax_term . '"><span class="item-name">' . $tax_term . '</span></a></li>'. "\n";
				}
			}
		    $filter_output .= '</ul></div>'. "\n";

			return $filter_output;
		}
	}


	/*
	*	PAGE BUILDER TEMPLATES FILTER
	================================================== */
	function sf_joyn_spb_templates($prebuilt_templates) {
		$prebuilt_templates = array();
		return $prebuilt_templates;
	}
	add_filter('spb_prebuilt_templates', 'sf_joyn_spb_templates');
?>
