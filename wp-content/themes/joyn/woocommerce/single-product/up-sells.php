<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
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
	
	global $woocommerce_loop;
	$woocommerce_loop['columns'] = 4;

	if ( $upsells ) : ?>
		
		<div class="upsells up-sells products">
		
            <h4 class="lined-heading"><span><?php _e( 'You may also like&hellip;', 'swiftframework' ) ?></span></h4>

            <?php woocommerce_product_loop_start(); ?>

            <?php foreach ( $upsells as $upsell ) : ?>
            
				<?php
				 	$post_object = get_post( $upsell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					wc_get_template_part( 'content', 'product' ); ?>

			<?php endforeach; ?>

            <?php woocommerce_product_loop_end(); ?>

        </div>

	<?php endif;
	
	wp_reset_postdata();

} else {

    global $product, $woocommerce, $woocommerce_loop;

    $upsells = $product->get_upsells();

    if ( sizeof( $upsells ) == 0 ) {
        return;
    }

    $meta_query = $woocommerce->query->get_meta_query();
    $product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;

    $args = array(
        'post_type'           => 'product',
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => 1,
        'posts_per_page'      => $posts_per_page,
        'orderby'             => $orderby,
        'post__in'            => $upsells,
        'post__not_in'        => array( $productid ),
        'meta_query'          => $meta_query
    );

    $products = new WP_Query( $args );

	//$woocommerce_loop['columns'] 	= $columns;
    $woocommerce_loop['columns'] = 4;

    if ( $upsells ) : ?>

        <div class="upsells products">

            <h4 class="lined-heading"><span><?php _e( 'You may also like&hellip;', 'swiftframework' ) ?></span></h4>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                <?php wc_get_template_part( 'content', 'product' ); ?>

            <?php endwhile; // end of the loop. ?>

            <?php woocommerce_product_loop_end(); ?>

        </div>

    <?php endif;

    wp_reset_postdata();

}