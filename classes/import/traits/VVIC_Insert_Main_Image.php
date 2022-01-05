<?php

/**
 * Retrieves and inserts/sideloads main product image to provided product id
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Insert_Main_Image {

    /**
     * Sideloads and inserts product main image as retrieved via query data
     * 
     * @param int $product_id - Product ID for which main image should be sideloaded and attached
     */
    public static function sideload_main_image($product_id) {

        // retrieve vvic main product image url
        $main_img_url = 'https:' . get_post_meta( $product_id, '_vvic_main_img', true );

        // ******************************************
        // SIDELOAD MAIN IMAGE AND ATTACH TO PRODUCT
        // ******************************************
        if ( $main_img_url ):

            // download image to temp folder
            $main_img_id = media_sideload_image( $main_img_url, null, null, 'id' );

            // if error during sideload, log, else attach image to product id
            if ( is_wp_error( $main_img_id ) ):
                $error_msg = $main_img_id->get_error_message();
                update_post_meta( $product_id, '_vvic_main_image_retrieval_error', $error_msg );
            elseif ( !is_wp_error( $main_img_id ) ):
                update_post_meta( $product_id, '_thumbnail_id', $main_img_id );
                delete_post_meta( $product_id, '_vvic_main_image_retrieval_error' );
            endif;

        endif;
    }

}
