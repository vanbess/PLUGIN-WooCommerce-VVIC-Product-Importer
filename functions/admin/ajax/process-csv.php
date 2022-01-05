<?php

/**
 * AJAX action to process last uploaded CSV file
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
add_action( 'wp_ajax_vvic_process_csv', 'vvic_process_csv' );
add_action( 'wp_ajax_nopriv_vvic_process_csv', 'vvic_process_csv' );

function vvic_process_csv() {

    // *****************
    // PROCESS CSV FILE
    // *****************
    if ( isset( $_POST[ 'target_file' ] ) ):

        check_ajax_referer( 'ocr process csv' );

        // retrieve target file
        $target_file = $_POST[ 'target_file' ];

        // build array for initial processing
        if ( ($handle = fopen( $target_file, "r" )) !== FALSE ):
            $line = 1;
            while ( ($row  = fgetcsv( $handle, 0, ";" )) !== FALSE ):

                // skip empty lines
                if ( $row[ 0 ] === '' ):
                    continue;
                endif;

                // skip lines which contain column keys
                if ( $row[ 0 ] === 'sku_code' ):
                    continue;
                endif;

                if ( $line >= 1 ):

                    // setup product data
                    $sku              = $row[ 0 ];
                    $vvic_url         = $row[ 1 ];
                    $product_title    = $row[ 2 ];
                    $price_usd        = $row[ 3 ];
                    $categories       = $row[ 4 ];
                    $size_chart_image = $row[ 5 ];

                    // product data array for use in AS action to insert product
                    $product_data[ $line ] = [
                            'sku'              => $sku,
                            'vvic_url'         => $vvic_url,
                            'product_title'    => $product_title,
                            'price_usd'        => $price_usd,
                            'categories'       => $categories,
                            'size_chart_image' => $size_chart_image,
                    ];

                endif;
                $line++;
            endwhile;
            fclose( $handle );
        endif;

        // delete all previously added options/data to avoid potential Action Scheduler/processing errors
        $options = [
                '_vvic_product_data',
                '_vvic_process_product_import',
                '_vvic_retrieve_gall_prod_imgs',
                '_vvic_attach_variation_data',
                '_vvic_variations_max_reruns',
                '_vvic_prod_meta_max_reruns',
                '_vvic_last_inserted_products'
        ];

        foreach ( $options as $option ):
            delete_option( $option );
        endforeach;

        // insert data to database for reference and add trigger for import class to run
        add_option( '_vvic_product_data', maybe_serialize( $product_data ) );
        add_option( '_vvic_process_product_import', 'yes' );

        // insert max rerun data to database for rerunning base product meta insertion and variation data attachment actions
        add_option( '_vvic_variations_max_reruns', 10 );
        add_option( '_vvic_prod_meta_max_reruns', 10 );

        // retrieve and return inserted data so that we can debug if necessary
        $processed_data = [
                'inserted_prod_data'   => get_option( '_vvic_product_data' ),
                'import_scheduled'     => get_option( '_vvic_process_product_import' ),
                'max_variation_reruns' => get_option( '_vvic_variations_max_reruns' ),
                'max_meta_reruns'      => get_option( '_vvic_prod_meta_max_reruns' ),
        ];

        // send json response
        wp_send_json( $processed_data );

        wp_die();

    endif;

    // *******************************
    // DELETE LAST PROCESSED CSV FILE
    // *******************************
    if ( isset( $_POST[ 'delete_csv' ] ) ):

        check_ajax_referer( 'delete last process csv' );

        // delete related post meta
        $processed_deleted = delete_option( '_vvic_csv_processed' );
        $uploaded_deleted  = delete_option( '_vvic_last_csv_uploaded' );

        if ( $processed_deleted === true && $uploaded_deleted === true ):
            wp_send_json( 'csv file records deleted' );
        endif;

        wp_die();
    endif;
}
