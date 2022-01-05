<?php

/**
 * AJAX to save cropped chart images and convert them to text using Tesseract OCR
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Prod_Chart_AJAX {

    /**
     * AJAX action to save chart images and process chart images to text
     * 
     * @note - media_sideload_image does not work; assuming this is due to server permissions settings 
     * (sideload error states image could not be moved to uploads directory), 
     * so simply using image links from plugin classes/products/img-parts directory to update product meta
     */
    public static function vvic_save_chart_data() {

        check_ajax_referer( 'vvic product ajax' );

        // **************************
        // Save cropped chart images
        // **************************
        if ( isset( $_POST[ 'header' ] ) ):

            // retrieve header and body image data
            $header_img_data = $_POST[ 'header' ];
            $body_img_data   = $_POST[ 'body' ];

            // retrieve product id and setup up file image save name path et al
            $product_id      = $_POST[ 'product_id' ];
            $sku             = get_post_meta( $product_id, '_sku', true );
            $header_img_name = $sku . '_chart_header.png';
            $body_img_name   = $sku . '_chart_body.png';
            $save_path       = VVIC_PATH . 'classes/products/img-parts/';
            $save_url        = VVIC_URL . 'classes/products/img-parts/';

            // setup body and header save paths
            $body_save_path   = $save_path . $body_img_name;
            $header_save_path = $save_path . $header_img_name;

            // setup body and header saved urls (used for sideloading images later on)
            $body_save_url   = $save_url . $body_img_name;
            $header_save_url = $save_url . $header_img_name;

            // setup array which will contain data returned via json
            $chart_image_data = [];

            // unlink any existing files before putting them in directory to ensure we have only latest crops available
            $unlinked_header = unlink( $header_save_path );
            $unlinked_body   = unlink( $body_save_path );

            $chart_image_data[ 'header_unlinked' ] = $unlinked_header;
            $chart_image_data[ 'body_unlinked' ]   = $unlinked_body;

            // put header and body images in directory
            $header_img = file_put_contents( $header_save_path, file_get_contents( $header_img_data ) );
            $body_img   = file_put_contents( $body_save_path, file_get_contents( $body_img_data ) );

            // push header and body data to array
            // a value of 'false' means that the images failed to upload to provided directory
            // positive integer values mean that the images were uploaded - denotes image size
            $chart_image_data[ 'header' ] = $header_img;
            $chart_image_data[ 'body' ]   = $body_img;

            // delete header and body product meta to ensure update
            delete_post_meta( $product_id, '_vvic_chart_header_img_url' );
            delete_post_meta( $product_id, '_vvic_chart_body_img_url' );

            // update header and body image urls
            $header_img_url_saved = update_post_meta( $product_id, '_vvic_chart_header_img_url', $header_save_url );
            $body_img_url_saved   = update_post_meta( $product_id, '_vvic_chart_body_img_url', $body_save_url );

            // push meta update responses to data array
            $chart_image_data[ 'header_url_saved' ] = $header_img_url_saved;
            $chart_image_data[ 'body_url_saved' ]   = $body_img_url_saved;

            // return json data
            if ( !empty( $chart_image_data ) ):
                $message                       = __( 'Chart header and body images successfully saved to product.', 'woocommerce' );
                $chart_image_data[ 'message' ] = $message;
                wp_send_json_success( $chart_image_data );
            endif;

        endif;

        // *********************************
        // Save converted chart header text
        // *********************************
        if ( isset( $_POST[ 'header_text' ] ) || isset( $_POST[ 'body_text' ] ) ):

            // chart data array which will hold final translated/parsed chart data
            $combined_chart_data = [];

            // vars
            $prod_id     = $_POST[ 'product_id' ];
            $header_text = $_POST[ 'header_text' ];
            $body_text   = $_POST[ 'body_text' ];

            // ********************
            // process header text
            // ********************
            if ( $header_text !== '' ):

                // parse original header data
                $init_header_data = explode( ' ', trim( $header_text ) );

                // retrieve chinese => english chart parameter map
                $zh_en_param_map = maybe_unserialize( get_option( 'vvic_chart_parameter_map' ) );

                // setup term replace pairs array for items which weren't read properly
                $replacement_params = array_filter( maybe_unserialize( get_option( 'vvic_chart_parameter_replacement_map' ) ) );

                // cleaned header params array, i.e. new params array for which 
                // faulty OCR text has been replaced with correct text
                $cleaned_header_params = [];

                // replace faulty OCR text as required and push updated params to 
                // $cleaned_header_params array, else push original param to same array
                foreach ( $init_header_data as $param ):

                    if ( $replacement_params[ $param ] ):
                        $cleaned_header_params[] = $replacement_params[ $param ];
                    else:
                        $cleaned_header_params[] = $param;
                    endif;

                endforeach;

                // create empty english chart parameters array
                $en_header_params = [];

                // push matching english params from chinese => english chart paramater map to $en_header_params
                foreach ( $cleaned_header_params as $param ):
                    $en_header_params[] = $zh_en_param_map[ $param ];
                endforeach;

                // generate final english chart header paramaters array
                $final_header_params = [];

                foreach ( $en_header_params as $param ):
                    $final_header_params[] = [
                            'class'   => 'highlight',
                            'colspan' => '',
                            'value'   => $param ? $param : __( 'not found', 'woocommerce' )
                    ];
                endforeach;

                // push enhanced data to $combined_chart_data
                array_push( $combined_chart_data, $final_header_params );

                // update product chart header meta
                update_post_meta( $prod_id, 'sb_chart_pre_header', maybe_serialize( $final_header_params ) );

            endif;

            // ******************
            // process body text
            // ******************
            if ( $body_text !== '' ):

                // parse initial chart body data lines to array
                if ( strpos( $body_text, 'div' ) !== false ):
                    $body_text          = str_replace( [ '<div>', '</div>' ], '\n', $body_text );
                    $body_text          = str_replace( '\n\n', '\n', $body_text );
                    $init_body_data_arr = array_filter( explode( '\n', trim( $body_text ) ) );
                else:
                    $init_body_data_arr = array_filter( explode( PHP_EOL, trim( $body_text ) ) );
                endif;

                // array to hold line data splits
                $split_body_data = [];

                // split line data
                foreach ( $init_body_data_arr as $line_data ):
                    $line_data         = explode( ' ', $line_data );
                    $split_body_data[] = array_filter($line_data);
                endforeach;
                
                // product chart body data array - used for saving chart body data to product meta
                $chart_body_data = [];

                // loop through line data arrs to build out to required array spec
                foreach ( $split_body_data as $data_line_arr ):

                    // inner body data array
                    $inner_body_data = [];

                    // data counter
                    $data_counter = 0;

                    // loop
                    foreach ( $data_line_arr as $key => $data ):

                        // first col data
                        if ( $data_counter === 0 ):
                            $inner_body_data[] = [
                                    'class'   => 'highlight',
                                    'colspan' => '',
                                    'value'   => $data
                            ];

                        // remainder col data
                        else:
                            $inner_body_data[] = [
                                    'class'   => '',
                                    'colspan' => '',
                                    'value'   => $data
                            ];
                        endif;

                        $data_counter++;
                    endforeach;

                    // push to $combined chart data
                    array_push( $combined_chart_data, $inner_body_data );

                    // push to $chart_body_data
                    $chart_body_data[] = $inner_body_data;

                endforeach;

                // update product chart body meta
                update_post_meta( $prod_id, 'sb_chart_pre_body', maybe_serialize( $chart_body_data ) );

            endif;

            // update product chart combined header and body meta
            update_post_meta( $prod_id, 'sbarray_chart_data', maybe_serialize( $combined_chart_data ) );

            // return data
            if ( get_post_meta( $prod_id, 'sbarray_chart_data', true ) && get_post_meta( $prod_id, 'sb_chart_pre_body', true ) && get_post_meta( $prod_id, 'sb_chart_pre_header', true ) ):
                print __( 'Chart data successfully saved/updated. Navigate to VVIC Imports > Review Charts to review and edit chart data as needed.', 'woocommerce' );
            endif;

        endif;

        // **************************************
        // SAVE FINAL CHART HEADER AND BODY TEXT
        // **************************************
        if ( isset( $_POST[ 'final_header_obj' ] ) && isset( $_POST[ 'final_body_obj' ] ) ):

        endif;

        wp_die();
    }

}
