<?php

/**
 * Retrieves and inserts VVIC product size chart image, if present/provided in CSV import file
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Insert_Chart_Image {

    /**
     * Retrieve, download, resize and attach product chart image to product
     * 
     * @param int $product_id - Product ID for which chart image should be sideloaded and attached
     */
    public static function sideload_size_chart_image($product_id) {

        // retrieve sku
        $sku = get_post_meta( $product_id, '_sku', true );

        // setup initial chart image name for saving to directory
        $chart_img_name = $sku . '.jpg';

        // setup final chart image name post resize for saving to directory
        $chart_img_name_resized = $sku . '.jpg';

        // retrieve original chart image url
        $chart_img_url_vvic = get_post_meta( $product_id, '_vvic_size_chart_image', true );

        if ( $chart_img_url_vvic ):

            // setup image save path
            $save_path = VVIC_PATH . 'classes/products/charts/';

            // setup image save url
            $save_url = VVIC_URL . 'classes/products/charts/';

            // download vvic chart image to directory
            file_put_contents( $save_path . $chart_img_name, file_get_contents( $chart_img_url_vvic ) );

            // setup chart image url
            $chart_img_url = $save_url . $chart_img_name;

            // enhance image using Imagick
            $im = new Imagick( $chart_img_url );
            $im->autoLevelImage();
            $im->scaleImage( 1500, 0 );
            $im->despeckleImage();
            $im->enhanceImage();
            $im->adaptiveSharpenImage( 10, 1 );
            
            // write altered images to directory
            $im->writeImage( $save_path . $chart_img_name_resized );

            // update product meta
            $resized_chart_image_src = $save_url . $chart_img_name_resized;
            update_post_meta( $product_id, '_vvic_resized_chart_img', $resized_chart_image_src );
        else:
            return;
        endif;
    }

}
