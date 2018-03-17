<?php
/**
 * Derived from WC 3 
 * @author  RE
 * @date 6 aug 2017
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

$langauge =  pll_current_language( $field = 'slug' );
	
	switch($langauge)
	{
		case "en":
			
			$address = "http://localhost:8080/jgib2017/webshop";
			//$address = get_permalink(wc_get_page_id( 'webshop' ));
			$linkText = "Return to Products";
			//echo $linkText;
		break;

		case "nl":

            $address = "http://localhost:8080/jgib2017/webwinkel";
            //$address = get_permalink(wc_get_page_id( 'webwinkel' ));
			$linkText = "Terug naar produkten";
			//echo $linkText;
		break;

		case "fr":

			$address = "http://localhost:8080/jgib2017/webshop-fr";
			//$address = get_permalink(wc_get_page_id( 'webshop-fr' ));
			$linkText="Retour aux produits";
			//echo $linkText;
		break;

	}


/**
 * @hooked wc_empty_cart_message - 10
 */
do_action( 'woocommerce_cart_is_empty' );

if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
	<p class="return-to-shop">
		<!--
        <a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'webshop' ) ) ); ?>">
            <?php _e( 'Return To webShop', 'woocommerce' ) ?>
        </a>

        -->
        <a class="button wc-backward" href="<?php echo $address; ?>">
            <?php echo $linkText; ?>
        </a>
	</p>
<?php endif; ?>
