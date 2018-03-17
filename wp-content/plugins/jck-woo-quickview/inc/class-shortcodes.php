<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Iconic_WQV_Shortcodes class
 *
 * @class Iconic_WQV_Shortcodes
 */
class Iconic_WQV_Shortcodes {

    /**
     * Init shortcodes.
     */
    public static function init() {
        $shortcodes = array(
            'quickview_button' => __CLASS__ . '::quickview_button',
        );

        foreach ( $shortcodes as $shortcode => $function ) {
            add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
        }

    }

    /**
     * Quickview button shortcode.
     *
     * @return string
     */
    public static function quickview_button( $atts ) {

        global $jckqv;

        $atts = shortcode_atts( array(
            'product_id' => false,
        ), $atts, 'quickview_button' );

        if( !$atts['product_id'] )
            return;

        ob_start();

        $jckqv->display_button( $atts['product_id'] );

        return ob_get_clean();

    }

}