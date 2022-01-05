<?php

/**
 *  Functionality to crop/extract chart header and body images
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Prod_Chart_Cropper {

    /**
     * Renders HTML et al to crop/extract product chart header and body images, 
     * then save them to product for later reference/OCR processing
     * 
     * @param object $post - Global post object
     */
    public static function vvic_crop_chart_img($post) {

        // frab product id
        $product_id    = $post->ID;
        
        // grab chart image url
        $chart_img_url = get_post_meta( $product_id, '_vvic_resized_chart_img', true );

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

        <!-- chart image cont -->
        <div class="vvic_chart_img_crop_cont">

            <!-- main img cont -->
            <div class="vvic_chart_img_cont">

                <h4><?php _e( '1. Use the crop box below to select chart header and body images.', 'woocommerce' ); ?></h4>
                <span><?php _e( 'You can select one image at a time. Once selected, click the matching extract button to extract it. Extracted images will appear on the left.', 'woocommerce' ); ?></span><br><br>
                <span><?php _e( '<u><b>Zoom in or out:</b></u> use mousewheel or similar', 'woocommerce' ); ?></span><br>
                <span><?php _e( '<u><b>Resize crop:</b></u> click and drag relevant crop box handles', 'woocommerce' ); ?></span><br>
                <span><?php _e( '<u><b>Create new crop:</b></u> click and drag outside existing crop box', 'woocommerce' ); ?></span><br><br>

                <img id="vvic_chart_main" src="<?php echo $chart_img_url; ?>" alt="alt"/>

                <!-- crop buttons -->
                <div class="vvic_chart_crop_btns_cont">
                    <!-- crop head -->
                    <button id="vvic_extract_chart_head" class="button button-primary">
                        <?php _e( 'Extract chart header image', 'woocommerce' ); ?>
                    </button>

                    <!-- crop body -->
                    <button id="vvic_extract_chart_body" class="button button-primary">
                        <?php _e( 'Extract chart body image', 'woocommerce' ); ?>
                    </button>
                </div>
            </div>

            <!-- cropped images cont -->
            <div class="vvic_cropped_imgs_cont">

                <!-- user defined text for ocr -->
                <input type="hidden" id="user-text-header" value="<?php echo VVIC_PATH . 'classes/products/charts/user-header.txt' ?>">
                <input type="hidden" id="user-text-body" value="<?php echo VVIC_PATH . 'classes/products/charts/user-body.txt' ?>">

                <h4><?php _e( '2. Cropped header and main body images will appear here.', 'woocommerce' ); ?></h4>
                <span><?php _e( 'Once you\'re satisfied with the crops, click the save button to save and convert to text.', 'woocommerce' ); ?></span><br><br>
                <span><?php _e( '<b><u>NOTE 1:</u></b> Cropped images will be automatically saved to product once you convert them to text.', 'woocommerce' ); ?></span><br><br>
                <span><?php _e( '<b><u>NOTE 2:</u></b> If conversion is inaccurate, try to recrop the image and make sure sufficient space is left around it.', 'woocommerce' ); ?></span><br><br>
                <span><?php _e( '<b><u>NOTE 3:</u></b> Ensure that you remove unnecessary spaces or characters between words before saving to avoid any errors.', 'woocommerce' ); ?></span><br><br>

                <hr><br>

                <div class="vvic_cropped_imgs_cont_imgs">

                    <!-- cropped header -->
                    <span><u><i><b><?php _e( 'Cropped chart header image:', 'woocommerce' ); ?></b></i></u></span><br><br>

                    <?php if ( $chart_header_url ): ?>
                        <!-- existing header crop -->
                        <img id="vvic_cropped_header" class="vvic_ch_img_exists" src="<?php echo $chart_header_url; ?>" alt="<?php _e( 'Cropped chart header image', 'woocommerce' ); ?>"/>

                    <?php else: ?>

                        <!-- empty header crop -->
                        <img id="vvic_cropped_header" class="vvic_ch_img_small" src="https://dev.nordace.com/wp-content/uploads/woocommerce-placeholder-247x296.png" alt="<?php _e( 'Cropped chart header image', 'woocommerce' ); ?>"/>
                    <?php endif; ?>

                    <!-- convert header crop to text via OCR -->
                    <div class="vvic_convert_to_text">

                        <!-- convert header to text -->
                        <button id="vvic_chart_header_to_text" data-nonce="<?php echo wp_create_nonce( 'vvic process chart OCR' ); ?>" data-processing="<?php _e( 'Processing...', 'woocommerce' ); ?>" data-complete="<?php _e( 'Conversion complete. Click to process again.', 'woocommerce' ); ?>" class="button button-secondary" data-product-id="<?php echo $product_id; ?>">
                            <?php _e( 'Convert header to text', 'woocommerce' ); ?>
                        </button>

                        <!-- converted header text -->                        
                        <div class="vvic_converted_header_text" style="display: none;">

                            <span><?php _e( 'Select most relevant/accurate text from the list below (faulty values will be replaced using replacement values defined in Chart Replacement Map under settings):', 'woocommerce' ); ?></span><br>

                            <!-- converted text cont -->
                            <div class="vvic_converted_header_text_actual"></div>

                        </div>

                    </div>
                    <br>

                    <!-- cropped main -->
                    <span><u><i><b><?php _e( 'Cropped chart body image:', 'woocommerce' ); ?></b></i></u></span><br><br>

                    <?php if ( $chart_body_url ): ?>
                        <!-- existing body crop -->
                        <img id="vvic_cropped_body" class="vvic_ch_img_exists" src="<?php echo $chart_body_url; ?>" alt="<?php _e( 'Cropped chart body image', 'woocommerce' ); ?>"/>
                    <?php else: ?>
                        <!-- empty body crop -->
                        <img id="vvic_cropped_body" class="vvic_ch_img_small" src="https://dev.nordace.com/wp-content/uploads/woocommerce-placeholder-247x296.png" alt="<?php _e( 'Cropped chart body image', 'woocommerce' ); ?>"/>
                    <?php endif; ?>

                    <!-- convert cropped body to text via OCR -->
                    <div class="vvic_convert_to_text">

                        <!-- convert body to text -->
                        <button id="vvic_chart_body_to_text" data-nonce="<?php echo wp_create_nonce( 'vvic process chart OCR' ); ?>" data-processing="<?php _e( 'Processing...', 'woocommerce' ); ?>" data-complete="<?php _e( 'Conversion complete. Click to process again.', 'woocommerce' ); ?>" class="button button-secondary" data-product-id="<?php echo $product_id; ?>">
                            <?php _e( 'Convert body to text', 'woocommerce' ); ?>
                        </button>

                        <!-- converted body text -->                        
                        <div class="vvic_converted_body_text" style="display: none;">

                            <span><?php _e( 'Converted body text (click on text to edit as needed):', 'woocommerce' ); ?></span><br>

                            <!-- converted text cont -->
                            <div class="vvic_converted_body_text_actual" contenteditable="true"></div>

                        </div>

                    </div>


                    <br>

                </div>

                <!-- save images -->
                <div class="vvic_save_chart_crops_btn_cont">

                    <input type="hidden" id="vvic_cropped_header_input" name="vvic_cropped_header_input">
                    <input type="hidden" id="vvic_cropped_body_input" name="vvic_cropped_body_input">

                    <!-- save converted text -->
                    <button id="vvic_save_converted_chart_text" class="button button-primary" style="display: none;" disabled="true" data-updated="<?php _e( 'Chart data updated as appropriate', 'woocommerce' ); ?>">
                        <?php _e( 'Save converted chart text to product', 'woocommerce' ); ?>
                    </button>

                </div>

            </div>

            <?php
        }

    }
    