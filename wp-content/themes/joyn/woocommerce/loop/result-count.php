<?php
/**
 * Result Count
 *
 * Shows text: Showing x - x of x results.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/result-count.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

global $woocommerce, $wp_query, $sf_options;

$products_per_page = $sf_options['products_per_page'];

$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );

if ( is_tax( 'product_cat' ) ) {
    $product_category      = $wp_query->query_vars['product_cat'];
    $product_category_link = get_term_link( $product_category, 'product_cat' );

    if ( $product_category_link != "" ) {
        $shop_page_url = $product_category_link;
    } else {
        $shop_page_url = "";
    }
}

if ( ! woocommerce_products_will_display() ) {
    return;
}
?>
<div class="woocommerce-count-wrap">
    <p class="woocommerce-result-count">
        <?php
        if ( version_compare( WC_VERSION, '3.3', '>=' ) ) {
            if ( $total <= $per_page || -1 === $per_page ) {
                /* translators: %d: total results */
                printf( _n( 'Showing the single result', 'Showing all %d results', $total, 'swiftframework' ), $total );
            } else {
                $first = ( $per_page * $current ) - $per_page + 1;
                $last  = min( $total, $per_page * $current );
                /* translators: 1: first result 2: last result 3: total results */
                printf( _nx( 'Showing the single result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'swiftframework' ), $first, $last, $total );
            }
        } else {
            $paged    = max( 1, $wp_query->get( 'paged' ) );
            $per_page = $wp_query->get( 'posts_per_page' );
            $total    = $wp_query->found_posts;
            $first    = ( $per_page * $paged ) - $per_page + 1;
            $last     = min( $total, $wp_query->get( 'posts_per_page' ) * $paged );

            if ( 1 == $total ) {
                echo __( 'Showing the single product', 'swiftframework' );
            } elseif ( $total <= $per_page ) {
                printf( __( 'Showing all %d products', 'swiftframework' ), $total );
            } else {
                printf( __( 'Showing %1$d-%2$d of %3$d products', 'swiftframework' ), $first, $last, $total );
            }
        }
        ?>
    </p>
    <?php if ( $total > $per_page ) { ?>
        <p class="woocommerce-show-products">
            <span><?php _e( "View", "swiftframework" ); ?> </span>
            <a class="show-products-link"
               href="?show_products=<?php echo $products_per_page; ?>"><?php echo $products_per_page; ?></a>/<a
                class="show-products-link"
                href="?show_products=<?php echo $products_per_page * 2; ?>"><?php echo $products_per_page * 2; ?></a>/<a
                class="show-products-link"
                href="?show_products=<?php echo $total; ?>"><?php _e( "All", "swiftframework" ); ?></a>
        </p>
    <?php } ?>
</div>