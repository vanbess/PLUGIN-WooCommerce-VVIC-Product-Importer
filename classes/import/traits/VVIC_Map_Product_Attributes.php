<?php

/**
 * Maps product attributes to imported products and inserts product varition for each
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Map_Product_Attributes {

    /**
     * 1. Map attributes to product if they exist, else insert new attributes and attach
     * 2. If attributes exist and is attached to parent product, insert/create variations for all attribute combinations
     * 
     * @IMPORTANT Some VVIC products do not have props images attached, so image attachment to variation will have to be done manually after import
     * 
     * @todo Attach vvic product props images to each inserted variation during insertion process
     * @todo Update variation SKU and pricing
     * 
     * @uses _vvic_last_inserted_products - List of last inserted products
     * @uses _vvic_props_list - VVIC product property list
     * @uses _vvic_props_list_img - VVIC product property list images
     * @uses vvic_attributes - English => Chinese attribute map as defined in backend
     * @uses vvic_sku_codes - SKU color ending codes as defined in backend
     */
    public static function map_product_attributes($product_id) {

        // retrieve attributes map
        $attribute_map = maybe_unserialize( get_option( 'vvic_attributes' ) );
        
        // retrieve props list
        $props_list = maybe_unserialize(get_post_meta($product_id, '_vvic_props_list', true));

        // setup woo attributes array
        $woo_attributes = [];

        // *****************************************
        // loop to insert/attach product attributes
        // *****************************************
        foreach ( $props_list as $attr_code => $attr ):

            // get attr info
            $attr_info = explode( ":", $attr );

            // get attr name
            $attr_name = trim( $attr_info[ 0 ] );

            // get attr property
            $attr_prop = trim( $attr_info[ 1 ] );

            // *********************************************************************************
            // if attribute name === 颜色 (color) and attribute property !== 图片色 (photo color)
            // *********************************************************************************
            if ( $attr_name === '颜色' && $attr_prop !== '图片色' ):

                // check if attribute property exists in attribute map
                if ( array_key_exists( $attr_prop, $attribute_map ) ):

                    // retrieve english color name
                    $en_color_name = '';

                    if ( isset( $attribute_map[ $attr_prop ] ) ):
                        $en_color_name = $attribute_map[ $attr_prop ];
                    endif;

                    // if en_color_name is not empty
                    if ( $en_color_name ):

                        // add to $woo_attributes
                        $colors[] = $en_color_name;

                        $attrib_name = wc_attribute_taxonomy_name( 'color' );

                        $woo_attributes[ $attrib_name ][ 'name' ]         = $attrib_name;
                        $woo_attributes[ $attrib_name ][ 'value' ]        = $colors;
                        $woo_attributes[ $attrib_name ][ 'position' ]     = 0;
                        $woo_attributes[ $attrib_name ][ 'is_visible' ]   = 1;
                        $woo_attributes[ $attrib_name ][ 'is_variation' ] = 1;
                        $woo_attributes[ $attrib_name ][ 'is_taxonomy' ]  = 1;

                    endif;

                endif;
            endif;
            // end colors check
            // 
            // *****************************
            // if $attr_name === 尺码 (size)
            // *****************************
            if ( $attr_name === '尺码' ):

                // if $attr_prop !== 均码 (one size), push size attributes to array
                if ( $attr_prop !== '均码' ):
                    $sizes[] = $attr_prop;
                endif;

                // add size attributes to $woo_attributes
                $attrib_name = wc_attribute_taxonomy_name( 'size' );

                $woo_attributes[ $attrib_name ][ 'name' ]         = $attrib_name;
                $woo_attributes[ $attrib_name ][ 'value' ]        = $sizes;
                $woo_attributes[ $attrib_name ][ 'position' ]     = 0;
                $woo_attributes[ $attrib_name ][ 'is_visible' ]   = 1;
                $woo_attributes[ $attrib_name ][ 'is_variation' ] = 1;
                $woo_attributes[ $attrib_name ][ 'is_taxonomy' ]  = 1;

            endif;
            // end sizes check

        endforeach;

        // reverse $woo_attributes array so color is first
        ksort( $woo_attributes );

        // if not empty $woo_attributes, set product attributes
        if ( !empty( $woo_attributes ) ):

            // grab color and size attribute arrays
            $pa_color_arr = $woo_attributes[ 'pa_color' ];
            $pa_size_arr  = $woo_attributes[ 'pa_size' ];

            // change product type from simple to variable if attributes present
            wp_remove_object_terms( $product_id, 'simple', 'product_type' );
            wp_set_object_terms( $product_id, 'variable', 'product_type' );

            // update post meta with generated product attributes
            update_post_meta( $product_id, '_product_attributes', $woo_attributes );

            // ****************************
            // if pa_color attribs present
            // ****************************
            if ( $pa_color_arr ):

                // retrieve term name and term vals
                $attrib_name = $pa_color_arr[ 'name' ];
                $color_vals  = $pa_color_arr[ 'value' ];

                // loop through attribute vals and do some magic
                foreach ( $color_vals as $color_val ):

                    // insert non existent attribute vals if vals defined aren't found
                    if ( !term_exists( $color_val, $attrib_name ) ):
                        wp_insert_term( $color_val, $attrib_name );
                    endif;

                    $term_obj = get_term_by( 'name', $color_val, $attrib_name );

                    // get currently set product attributes from parent product
                    $prod_attrs = wp_get_post_terms( $product_id, $attrib_name, [ 'fields' => 'names' ] );

                    // check if attribute name(s) are set for parent product and, if not, set them
                    if ( !in_array( $color_val, $prod_attrs ) ):
                        wp_set_post_terms( $product_id, $color_val, $attrib_name, true );
                    endif;

                endforeach;
            // end $color_vals loop

            endif;

            // ***************************
            // if pa_size attribs present
            // ***************************
            if ( $pa_size_arr ):

                // retrieve term name and term vals
                $attrib_name = $pa_size_arr[ 'name' ];
                $size_vals   = $pa_size_arr[ 'value' ];

                // loop through attribute vals and do some magic
                foreach ( $size_vals as $size_val ):

                    // insert non existent attribute vals if vals defined aren't found
                    if ( !term_exists( $size_val, $attrib_name ) ):
                        wp_insert_term( $size_val, $attrib_name );
                    endif;

                    $term_obj = get_term_by( 'name', $size_val, $attrib_name );

                    // get currently set product attributes from parent product
                    $prod_attrs = wp_get_post_terms( $product_id, $attrib_name, [ 'fields' => 'names' ] );

                    // check if attribute name(s) are set for parent product and, if not, set them
                    if ( !in_array( $size_val, $prod_attrs ) ):
                        wp_set_post_terms( $product_id, $size_val, $attrib_name, true );
                    endif;

                endforeach;

            endif;

            // retrieve product object
            $product = wc_get_product( $product_id );

            // get instance of WC product data store
            $prod_data_store = new WC_Product_Data_Store_CPT();

            // create product variations from all attributes
            $prod_data_store->create_all_product_variations( $product, -1 );

        endif;
        // end $woo_attributes check
    }

}
