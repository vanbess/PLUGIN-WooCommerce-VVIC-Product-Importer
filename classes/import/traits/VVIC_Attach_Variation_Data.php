<?php

/**
 * Handles retrieval and attachment of variation data
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Attach_Variation_Data {

    use VVIC_Retrieve_Product_Data;

    /**
     * Queries and attaches the following missing variation data:
     * 1. Variation price
     * 2. Variation image
     * 3. Generated/custom variation SKU
     */
    public static function attach_variation_data() {

        // retrieve color sku ending codes
        $sku_ending_codes = maybe_unserialize( get_option( 'vvic_sku_codes' ) );

        // retrieve last inserted product ids
        $product_ids = maybe_unserialize( get_option( '_vvic_last_inserted_products' ) );

        // loop
        foreach ( $product_ids as $product_id ):

            // retrieve product prop img id pairs
            $prop_img_id_pairs = maybe_unserialize( get_post_meta( $product_id, '_vvic_prop_img_id_pairs', true ) );

            // get instance of product object
            $prod_obj = wc_get_product( $product_id );

            if ( is_object( $prod_obj ) ):

                // retrieve parent sku
                $parent_sku = $prod_obj->get_sku();

                // retrieve product price
                $parent_price = get_post_meta( $product_id, '_vvic_price', true );

                // sku errors array
                $sku_errors = [];

                // retrieve children
                $children = $prod_obj->get_children();

                if ( !empty( $children ) ):

                    foreach ( $children as $child ):

                        // retrieve attribs
                        $size              = strtoupper( get_post_meta( $child, 'attribute_pa_size', true ) );
                        $init_color_attrib = get_post_meta( $child, 'attribute_pa_color', true );

                        // generate correct SKU based on available attributes
                        if ( $init_color_attrib ):

                            // retrieve color value
                            $color = ucwords( str_replace( '-', ' ', $init_color_attrib ) );

                            // retrieve sku ending code
                            $sku_ending = $sku_ending_codes[ $color ];

                            // setup unique sku for variation
                            $v_sku = $sku_ending ? $parent_sku . '_' . $sku_ending : $parent_sku . '_sku_ending_code_not_found_';
                            $v_sku = $size ? $v_sku . '_' . $size : $v_sku;

                        else:
                            $v_sku = $size ? $v_sku . '_' . $size : $v_sku;
                        endif;

                        // get variation product instance
                        $v_prod = wc_get_product( $child );

                        // delete any existing variation sku
                        delete_post_meta( $child, '_sku' );

                        // update sku
                        update_post_meta( $child, '_sku', $v_sku );

                        // update price
                        $v_prod->set_regular_price( $parent_price );

                        // attach correct image id to variation
                        if ( $prop_img_id_pairs !== 'prop img id array empty' && is_array( $prop_img_id_pairs ) ):
                            $v_img_id = $prop_img_id_pairs[ $color ];
                            $v_prod->set_image_id( $v_img_id );
                        endif;

                        // save product
                        $v_prod->save();

                    endforeach;

                    // add failed sku variation ids to product for later ref 
                    update_post_meta( $product_id, '_vvic_variation_sku_issues', maybe_serialize( $sku_errors ) );

                endif;

            endif;

        endforeach;
    }

}
