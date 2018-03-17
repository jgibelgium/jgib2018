<?php global $post, $product, $woocommerce; ?>

<?php do_action($this->slug.'-before-meta'); ?>

<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'n/a', 'woocommerce' ); ?></span>.</span>

	<?php endif; ?>

	<?php echo Iconic_WQV_Product::get_categories( $product ); ?>
	<?php echo Iconic_WQV_Product::get_tags( $product ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>

<?php do_action($this->slug.'-after-meta'); ?>