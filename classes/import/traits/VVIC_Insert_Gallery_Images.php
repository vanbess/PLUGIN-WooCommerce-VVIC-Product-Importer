<?php

/**
 * Retrieves and inserts/sideloads VVIC product gallery images
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Insert_Gallery_Images {

    /**
     * Insert/sideload and attach product gallery images to product
     * 
     * @uses _product_image_gallery - Product image gallery: skips iteration if gallery image ids already present/attached
     * @uses _vvic_item_imgs - List of VVIC product gallery image links
     */
    public static function sideload_gallery_images() {
        
        // change max execution time
        ini_set('max_execution_time', 600);

        // retrieve product ids
        $product_ids = maybe_unserialize( get_option( '_vvic_last_inserted_products' ) );

        // include required wordpress file to handle upload/sideload
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // loop to retrieve and attach gallery images to each product
        foreach ( $product_ids as $product_id ):

            // check if gallery images present for product; if true, 
            // skip retrieval so that we get all gallery images loaded 
            // over time (AS action tends to time out on first run, 
            // so multiple runs needed to retrieve all images)
            if ( get_post_meta( $product_id, '_product_image_gallery', true ) ):
                continue;
            endif;

            // setup gallery image ids array for attaching img ids to product after sideload
            $gall_img_ids = [];

            // item image links
            $item_img_links = maybe_unserialize( get_post_meta( $product_id, '_vvic_item_imgs', true ) );

            if ( $item_img_links ):
                foreach ( $item_img_links as $img_link_arr ):

                    $gall_img_src = 'https:' . $img_link_arr[ 'url' ];

                    $gall_img_id = media_sideload_image( $gall_img_src, null, null, 'id' );

                    // log any errors and add failed pids to array
                    if ( is_wp_error( $gall_img_id ) ):
                        $error_msg          = $gall_img_id->get_error_message();
                        update_post_meta( $product_id, '_vvic_gallery_imgs_retrieval_error', $error_msg );
                    // else push gallery img id to gall img ids array
                    elseif ( !is_wp_error( $gall_img_id ) ):
                        $gall_img_ids[] = $gall_img_id;
                    endif;

                endforeach;
            endif;
            
            // attach gallery image ids to product
            if ( !empty( $gall_img_ids ) ):
                update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gall_img_ids ) );
                delete_post_meta( $product_id, '_vvic_gallery_imgs_retrieval_error' );
            endif;

        endforeach;

    }

}
