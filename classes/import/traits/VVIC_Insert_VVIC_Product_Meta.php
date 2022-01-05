<?php

/**
 * Inserts relevant retrieved VVIC product data as WC product meta
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Insert_VVIC_Product_Meta {

    /**
     * Insert core VVIC product meta for possible future ref
     * 
     * @param array/object $query_data - returned VVIC GET product data
     * @param int $product_id - Product ID which needs to be updated
     */
    public static function insert_vvic_product_meta($query_data, $product_id) {

        // add item_imgs meta
        if ( !get_post_meta( $product_id, '_vvic_item_imgs', true ) ):
            update_post_meta( $product_id, '_vvic_item_imgs', maybe_serialize( $query_data[ 'item' ][ 'item_imgs' ] ) );
        endif;

        // add main img meta
        if ( !get_post_meta( $product_id, '_vvic_main_img', true ) ):
            update_post_meta( $product_id, '_vvic_main_img', $query_data[ 'item' ][ 'pic_url' ] );
        endif;

        // update product price
        if ( !get_post_meta( $product_id, '_regular_price', true ) ):
            update_post_meta( $product_id, '_regular_price', $query_data[ 'item' ][ 'price' ] );
        endif;
        if ( !get_post_meta( $product_id, '_vvic_price', true ) ):
            update_post_meta( $product_id, '_vvic_price', $query_data[ 'item' ][ 'price' ] );
        endif;

        // add props list images
        if ( !get_post_meta( $product_id, '_vvic_props_list_img', true ) || get_post_meta( $product_id, '_vvic_props_list_img', true ) === 'No props images present' ):
            update_post_meta( $product_id, '_vvic_props_list_img', !empty( $query_data[ 'item' ][ 'props_img' ] ) ? maybe_serialize( $query_data[ 'item' ][ 'props_img' ] ) : 'No props images present'  );
        endif;

        // add props list meta
        if ( !get_post_meta( $product_id, '_vvic_props_list', true ) ):
            update_post_meta( $product_id, '_vvic_props_list', !empty( $query_data[ 'item' ][ 'props_list' ] ) ? maybe_serialize( $query_data[ 'item' ][ 'props_list' ] ) : 'No item props returned or found'  );
        endif;

        /**
         * Process/sideload props images, matching each color with 
         * sideloaded id for later reference in VVIC_Attach_Variation_Data
         */
        // retrieve attributes map
        $attribute_map = maybe_unserialize( get_option( 'vvic_attributes' ) );

        // retrieve props list
        $props_list = maybe_unserialize( get_post_meta( $product_id, '_vvic_props_list', true ) );

        // retrieve props image list
        $props_images = maybe_unserialize( get_post_meta( $product_id, '_vvic_props_list_img', true ) );

        // if $props_images not present for some reason, bail early
        if ( !is_array( $props_list ) || $props_images === 'No props images present' ):
            return;
        endif;

        // generate prop image pairs so that we can attach correct image to variation later on
        $prop_img_pairs = [];

        // loop to match up prop with image url and push to $prop_img_pairs
        foreach ( $props_list as $key => $prop ):
            if ( key_exists( $key, $props_images ) ):
                $prop_data = explode( ':', $prop );
                $en_prop   = $attribute_map[ $prop_data[ 1 ] ];
                if ( $en_prop ):
                    $prop_img_pairs[ $en_prop ] = $props_images[ $key ];
                else:
                    $prop_img_pairs[ $prop_data[ 1 ] ] = $props_images[ $key ];
                endif;
            endif;
        endforeach;

        // sideload props images and attach props => img id to product for later ref
        // this is done to prevent sideloading of same image for multiple variations
        $prop_img_id_pairs = [];

        // loop to sideload
        foreach ( $prop_img_pairs as $prop => $img_url ):

            $prop_img_id = media_sideload_image( $img_url, null, null, 'id' );

            if ( !is_wp_error( $prop_img_id ) ):
                $prop_img_id_pairs[ $prop ] = $prop_img_id;
            endif;

        endforeach;

        // attach appropriate image meta for later ref
        if ( !empty( $prop_img_id_pairs ) ):
            update_post_meta( $product_id, '_vvic_prop_img_id_pairs', maybe_serialize( $prop_img_id_pairs ) );
        else:
            update_post_meta( $product_id, '_vvic_prop_img_id_pairs', 'prop img id array empty' );
        endif;
    }

}
