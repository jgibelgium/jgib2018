<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
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
    
global $sf_options;
$product_display_type    = $sf_options['product_display_type'];
$product_display_gutters = $sf_options['product_display_gutters'];

$columns = '';
if ( function_exists('wc_get_loop_prop') ) {
$columns = 'columns-' . wc_get_loop_prop( 'columns' );
}
?>
<?php if ( ! $product_display_gutters && ( $product_display_type == "gallery" || $product_display_type == "gallery-bordered" ) ) { ?>
    <ul id="products" class="products product-grid no-gutters product-type-<?php echo $product_display_type; ?> clearfix <?php echo esc_attr( $columns ); ?>">
<?php } else { ?>
    <ul id="products" class="products product-grid gutters row product-type-<?php echo $product_display_type; ?> clearfix <?php echo esc_attr( $columns ); ?>">
<?php } ?>