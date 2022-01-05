<?php

/**
 * Trait which retrieves product data via GET requests
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Retrieve_Product_Data {

    /**
     * Retrieve VVIC product data via GET request
     * 
     * @param string $vvic_id - VVIC Product ID
     */
    public static function retrieve_vvic_data($vvic_id) {

        // setup url for curl request
        $url = "https://api-gw.onebound.cn/vvic/item_get/?key=tel18026387620&secret=20210520&num_iid=" . $vvic_id;

        // init curl request
        $curl = curl_init();

        // set curl options for request
        curl_setopt_array( $curl, array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_FRESH_CONNECT  => true,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
        ) );

        // execute request
        $response = curl_exec( $curl );

        // close curl
        curl_close( $curl );

        // decode response
        $response = json_decode( $response, true );

        // return response if no errors, else return error
        if ( empty( $response[ 'error' ] ) ):
            return $response;
        elseif ( !empty( $response[ 'error' ] ) ):
            return 'data retrieval error';
        endif;

    }

}
