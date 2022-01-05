<?php

/**
 * Maps product categories defined in CSV import file to product
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Map_Product_Categories {

    /**
     * Attaches existing category IDs to product if they exist.
     * If supplied category names do not match any existing product categories,
     * new product category is inserted for each non matched category name
     * 
     * @param int $product_id - Product ID for which categories should be mapped
     */
    public static function map_product_categories($product_id) {

        // retrieve previously inserted product ids
//        $product_ids = maybe_unserialize( get_option( '_vvic_last_inserted_products' ) );
        // loop through ids and grab submitted categories
//        foreach ( $product_ids as $product_id ):
        // retrieve categories
        $csv_cats = get_post_meta( $product_id, '_vvic_product_categories', true );

        // setup cat ids array
        $cat_ids = [];

        // only run if imported cats meta present and not empty, else return
        if ( !empty( $csv_cats ) ):

            // strip space after commmas if present
            $csv_cats = str_replace( ', ', ',', $csv_cats );

            // setup imported cats arr
            $csv_cats_arr = explode( ',', $csv_cats );

            // loop through imported cats arr, check if term already exists
            // if true, push cats ids
            foreach ( $csv_cats_arr as $cat_name ):

                // check if product category already exists (returns id)
                $prod_cat = get_terms( [
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false,
                        'fields'     => 'ids',
                        'name'       => $cat_name
                    ] );

                // if not empty, push ids to cat ids array, else insert new term and push cat id to $cat_ids    
                if ( !empty( $prod_cat ) ):
                    $cat_ids[] = $prod_cat[ 0 ];
                else:
                    $cat_insert = wp_insert_term( trim( $cat_name ), 'product_cat' );
                    $cat_ids[]  = $cat_insert[ 'term_id' ];
                endif;

            endforeach;

            // set product categories
            wp_set_post_terms( $product_id, $cat_ids, 'product_cat' );

        endif;

//        endforeach;
    }

}
