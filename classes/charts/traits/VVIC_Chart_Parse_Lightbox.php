<?php

/**
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Chart_Parse_Lightbox {

    /**
     * Product size chart parse dialog
     */
    public static function chart_parse_dialogue_box($product_id) {

        // retrieve product SKU
        $sku = get_post_meta( $product_id, '_sku', true );

        // see if previously cropped images exists in uploads folder vvic_images
        $upload_dir_data = wp_upload_dir();
        $base_dir        = $upload_dir_data[ 'basedir' ] . '/vvic_images/';
        $base_url        = $upload_dir_data[ 'baseurl' ] . '/vvic_images/';
        $file_path       = $base_dir . $sku . '/';
        $file_url        = $base_url . $sku . '/';
        $header_url      = $file_url . str_replace( 'JP', 'Jp', $sku ) . ' Parameter.png';
        $body_url        = $file_url . str_replace( 'JP', 'Jp', $sku ) . ' Dress.png';

        // if file exists in upload directory, retrieve url, else retrieve url meta
        if ( file_exists( $file_path ) ):
            $chart_header_url = $header_url;
            $chart_body_url   = $body_url;
        else:
            // retrieve chart header and body urls
            $chart_header_url = get_post_meta( $product_id, '_vvic_chart_header_img_url', true );
            $chart_body_url   = get_post_meta( $product_id, '_vvic_chart_body_img_url', true );
        endif;
        ?>

        <!-- overlay -->
        <div class="vvic_chart_parse_overlay" style="display: none"></div>

        <!-- lightbox -->
        <div class="vvic_chart_parse_cont" id="vvic_chart_parse_<?php echo $product_id; ?>" style="display:none">

            <span class="vvic_close_ocr">x</span>

            <div class="vvic_review_cont_inner">

                <h3><?php _e( 'Parse/Crop Chart for Product ID ' . $product_id, 'woocommerce' ); ?></h3>

                <div class="vvic_parse_chart_data">

                    <!-- chart image -->
                    <div class="vvic_parse_chart_image">

                        <h4><?php _e( 'Size Chart Image', 'woocommerce' ); ?></h4>
                        <hr>

                        <h4><?php _e( '1. Use the crop box below to select chart header and body images.', 'woocommerce' ); ?></h4>
                        <span><?php _e( 'You can select one image at a time. Once selected, click the matching extract button to extract it. Extracted images will appear on the left.', 'woocommerce' ); ?></span><br><br>
                        <span><?php _e( '<u><b>Zoom in or out:</b></u> use mousewheel or similar', 'woocommerce' ); ?></span><br>
                        <span><?php _e( '<u><b>Resize crop:</b></u> click and drag relevant crop box handles', 'woocommerce' ); ?></span><br>
                        <span><?php _e( '<u><b>Create new crop:</b></u> click and drag outside existing crop box', 'woocommerce' ); ?></span><br><br>

                        <img id="vvic_parse_chart_main" class="vvic_parse_chart_main_<?php echo $product_id ?>" src=""/>

                        <!-- crop buttons -->
                        <div class="vvic_parse_chart_crop_btns_cont">
                            <!-- crop head -->
                            <button id="vvic_extract_chart_head_<?php echo $product_id ?>" class="vvic_extract_chart_head button button-primary">
                                <?php _e( 'Extract chart header image', 'woocommerce' ); ?>
                            </button>

                            <!-- crop body -->
                            <button id="vvic_extract_chart_body_<?php echo $product_id ?>" class="vvic_extract_chart_body button button-primary">
                                <?php _e( 'Extract chart body image', 'woocommerce' ); ?>
                            </button>
                        </div>

                    </div>

                    <!-- chart image as uploaded -->
                    <div class="vvic_parse_chart_parse">

                        <h4><?php _e( 'Parse Chart Header and Body images, or crop new images from current size chart image', 'woocommerce' ); ?></h4>

                        <hr>

                        <!-- user defined text for ocr -->
                        <input type="hidden" class="user-text-header" value="<?php echo VVIC_PATH . 'classes/products/charts/user-header.txt' ?>">
                        <input type="hidden" class="user-text-body" value="<?php echo VVIC_PATH . 'classes/products/charts/user-body.txt' ?>">

                        <h4><?php _e( '2. Cropped header and main body images will appear here.', 'woocommerce' ); ?></h4>
                        <span><?php _e( 'Once you\'re satisfied with the crops, click the save button to save and convert to text.', 'woocommerce' ); ?></span><br><br>
                        <span><?php _e( '<b><u>NOTE 1:</u></b> Cropped images will be automatically saved to product once you convert them to text.', 'woocommerce' ); ?></span><br><br>
                        <span><?php _e( '<b><u>NOTE 2:</u></b> If conversion is inaccurate, try to recrop the image and make sure sufficient space is left around it.', 'woocommerce' ); ?></span><br><br>
                        <span><?php _e( '<b><u>NOTE 3:</u></b> Ensure that you remove unnecessary spaces or characters between words before saving to avoid any errors.', 'woocommerce' ); ?></span><br><br>

                        <hr><br>

                        <div class="vvic_cropped_parse_imgs_cont_imgs">

                            <!-- cropped header -->
                            <span><u><i><b><?php _e( 'Cropped chart header image:', 'woocommerce' ); ?></b></i></u></span><br><br>

                            <?php if ( $chart_header_url ): ?>
                                <!-- existing header crop -->
                                <img id="vvic_cropped_parse_header_<?php echo $product_id ?>" class="vvic_ch_img_exists" src="<?php echo $chart_header_url; ?>" alt="<?php _e( 'Cropped chart header image', 'woocommerce' ); ?>"/>
                            <?php else: ?>
                                <img id="vvic_cropped_parse_header_<?php echo $product_id ?>" class="vvic_ch_img_exists" src="" alt="<?php _e( 'Cropped chart header image', 'woocommerce' ); ?>" style="display: none;"/>
                                <span class="vvic_no_chart_image" id="vvic_no_chart_header_<?php echo $product_id ?>"><?php _e( 'No cropped header image present', 'woocommerce' ); ?></span>
                            <?php endif; ?>

                            <!-- convert header crop to text via OCR -->
                            <div class="vvic_convert_to_text">

                                <!-- convert header to text -->
                                <button data-nonce="<?php echo wp_create_nonce( 'vvic process chart OCR' ); ?>" data-processing="<?php _e( 'Processing...', 'woocommerce' ); ?>" data-complete="<?php _e( 'Conversion complete. Click to process again.', 'woocommerce' ); ?>" class="button button-secondary vvic-parse-ocr vvic-parse-head" data-product-id="<?php echo $product_id; ?>">
                                    <?php _e( 'Convert header to text', 'woocommerce' ); ?>
                                </button>

                                <!-- converted header text -->                        
                                <div class="vvic_converted_header_text" id="vvic_converted_header_text_<?php echo $product_id ?>" style="display: none;">

                                    <span><?php _e( 'Converted header text (click on text to edit as needed and remove any unnecessary spaces as needed):', 'woocommerce' ); ?></span><br>

                                    <!-- converted text cont -->
                                    <div class="vvic_converted_header_text_actual" id="vvic_converted_header_text_actual_<?php echo $product_id ?>" contenteditable="true"></div>

                                </div>

                            </div>
                            <br>

                            <!-- cropped main -->
                            <span><u><i><b><?php _e( 'Cropped chart body image:', 'woocommerce' ); ?></b></i></u></span><br><br>

                            <?php if ( $chart_body_url ): ?>
                                <!-- existing body crop -->
                                <img id="vvic_cropped_parse_body_<?php echo $product_id ?>" class="vvic_ch_img_exists" src="<?php echo $chart_body_url; ?>" alt="<?php _e( 'Cropped chart body image', 'woocommerce' ); ?>"/>
                            <?php else: ?>
                                <img id="vvic_cropped_parse_body_<?php echo $product_id ?>" class="vvic_ch_img_exists" src="" alt="<?php _e( 'Cropped chart body image', 'woocommerce' ); ?>" style="display: none;"/>
                                <span class="vvic_no_chart_image" id="vvic_no_chart_body_<?php echo $product_id ?>"><?php _e( 'No cropped body image present', 'woocommerce' ); ?></span>
                            <?php endif; ?>

                            <!-- convert cropped body to text via OCR -->
                            <div class="vvic_convert_to_text">

                                <!-- convert body to text -->
                                <button data-nonce="<?php echo wp_create_nonce( 'vvic process chart OCR' ); ?>" data-processing="<?php _e( 'Processing...', 'woocommerce' ); ?>" data-complete="<?php _e( 'Conversion complete. Click to process again.', 'woocommerce' ); ?>" class="button button-secondary vvic-parse-ocr vvic-parse-body" data-product-id="<?php echo $product_id; ?>">
                                    <?php _e( 'Convert body to text', 'woocommerce' ); ?>
                                </button>

                                <!-- converted body text -->                        
                                <div class="vvic_converted_body_text" id="vvic_converted_body_text_<?php echo $product_id ?>" style="display: none;">

                                    <span><?php _e( 'Converted body text (click on text to edit as needed):', 'woocommerce' ); ?></span><br>

                                    <!-- converted text cont -->
                                    <div class="vvic_converted_body_text_actual" id="vvic_converted_body_text_actual_<?php echo $product_id ?>" contenteditable="true"></div>

                                </div>

                            </div>
                            <br>
                        </div>

                        <!-- save images -->
                        <div class="vvic_save_chart_crops_btn_cont">

                            <!-- save converted text -->
                            <button class="vvic_save_converted_chart_text button button-primary" id="vvic_save_converted_chart_text_<?php echo $product_id ?>" data-nonce="<?php echo wp_create_nonce( 'vvic product ajax' ) ?>" data-prod-id="<?php echo $product_id ?>" class="button button-primary" style="display: none;">
                                <?php _e( 'Save converted chart text to product', 'woocommerce' ); ?>
                            </button>

                        </div>

                    </div>
                </div>
            </div>

        </div>

        <?php
    }

}
