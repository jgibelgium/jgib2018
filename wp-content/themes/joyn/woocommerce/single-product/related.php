<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( version_compare( WC_VERSION, '2.7', '>=' ) ) {

	global $product, $sf_options, $woocommerce_loop, $sf_carouselID;
	
	if ( $related_products ) :

    $woocommerce_loop['columns'] = 3;

    if ( $sf_carouselID == "" ) {
        $sf_carouselID = 1;
    } else {
        $sf_carouselID ++;
    }

    $product_display_type    = $sf_options['product_display_type'];
    $product_display_gutters = $sf_options['product_display_gutters'];

    $gutter_class = "";

    if ( ! $product_display_gutters && $product_display_type == "gallery" ) {
        $gutter_class = 'no-gutters';
    } else {
        $gutter_class = 'gutters';
    }

    ?>

        <div class="product-carousel spb_content_element">

            <div class="title-wrap clearfix">
                <h2 class="spb-heading"><?php _e( 'Related Products', 'swiftframework' ); ?></h2>

                <div class="carousel-arrows"><a href="#" class="carousel-prev"><i class="ss-navigateleft"></i></a><a
                        href="#" class="carousel-next"><i class="ss-navigateright"></i></a></div>
            </div>

            <div
                class="related products carousel-items <?php echo $gutter_class; ?> product-type-<?php echo $product_display_type; ?>"
                id="carousel-<?php echo $sf_carouselID; ?>" data-columns="<?php echo $woocommerce_loop['columns']; ?>>">

				<?php foreach ( $related_products as $related_product ) : ?>
				
					<?php
					 	$post_object = get_post( $related_product->get_id() );
				
						setup_postdata( $GLOBALS['post'] =& $post_object );
				
						wc_get_template_part( 'content', 'product' ); ?>
				
				<?php endforeach; ?>

            </div>

        </div>

    <?php endif;

    global $sf_include_carousel, $sf_include_isotope;
    $sf_include_carousel = true;
    $sf_include_isotope  = true;

    wp_reset_postdata();

} else {

    global $product, $sf_options, $woocommerce_loop, $sf_carouselID;

    $related = $product->get_related( 12 );

    if ( sizeof( $related ) == 0 ) {
        return;
    }
    
    $product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;

    $args = apply_filters( 'woocommerce_related_products_args', array(
        'post_type'           => 'product',
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => 1,
        'posts_per_page'      => 12,
        'orderby'             => $orderby,
        'post__in'            => $related,
        'post__not_in'        => array( $product_id )
    ) );

    $products = new WP_Query( $args );

	//$woocommerce_loop['columns'] = $columns;
    $woocommerce_loop['columns'] = 3;

    if ( $sf_carouselID == "" ) {
        $sf_carouselID = 1;
    } else {
        $sf_carouselID ++;
    }

    $product_display_type    = $sf_options['product_display_type'];
    $product_display_gutters = $sf_options['product_display_gutters'];

    $gutter_class = "";

    if ( ! $product_display_gutters && $product_display_type == "gallery" ) {
        $gutter_class = 'no-gutters';
    } else {
        $gutter_class = 'gutters';
    }

    if ( $products->have_posts() ) : ?>

        <div class="product-carousel spb_content_element">

            <div class="title-wrap clearfix">
                <h2 class="spb-heading"><?php _e( 'Related Products', 'swiftframework' ); ?></h2>

                <div class="carousel-arrows"><a href="#" class="carousel-prev"><i class="ss-navigateleft"></i></a><a
                        href="#" class="carousel-next"><i class="ss-navigateright"></i></a></div>
            </div>

            <div
                class="related products carousel-items <?php echo $gutter_class; ?> product-type-<?php echo $product_display_type; ?>"
                id="carousel-<?php echo $sf_carouselID; ?>" data-columns="<?php echo $woocommerce_loop['columns']; ?>>">

                <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                    <?php wc_get_template_part( 'content', 'product' ); ?>

                <?php endwhile; // end of the loop. ?>

            </div>

        </div>

    <?php endif;

    global $sf_include_carousel, $sf_include_isotope;
    $sf_include_carousel = true;
    $sf_include_isotope  = true;

    wp_reset_postdata();

}
