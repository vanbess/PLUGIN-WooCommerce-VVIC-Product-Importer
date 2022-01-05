<?php

/**
 * Processes chart header and body OCR via AJAX using Tesseract OCR library and returns OCR results
 *  
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Prod_Chart_OCR
{

    use VVIC_OCR_Run_Body,
        VVIC_OCR_Setting_1,
        VVIC_OCR_Setting_2,
        VVIC_OCR_Setting_3,
        VVIC_OCR_Setting_4,
        VVIC_OCR_Setting_5,
        VVIC_OCR_Setting_6,
        VVIC_OCR_Setting_7,
        VVIC_OCR_Setting_8;

    public static function vvic_process_chart_ocr()
    {

        // *********************
        // PROCESS CHART HEADER
        // *********************
        if (isset($_POST['chart_header'])) :

            $img_blob   = $_POST['chart_header'];
            $product_id = $_POST['product_id'];
            $user_text  = $_POST['user_text'];
            $sku        = get_post_meta($product_id, '_sku', true);
            $file_name  = $sku . '_header.jpg';
            $save_path  = VVIC_PATH . 'classes/products/img-parts/headers/';
            $save_url   = VVIC_URL . 'classes/products/img-parts/headers/';
            $file_path  = $save_path . $file_name;
            $file_url   = $save_url . $file_name;

            $header_saved = file_put_contents($file_path, file_get_contents($img_blob));

            // converted text container array - contains parse text based on Imagick as originally implemented by Tony
            $converted = [];

            if ($header_saved !== false) :

                update_post_meta($product_id, '_vvic_chart_header_img_url', $file_url);

                $im = new Imagick($file_url);

                // do initial image mods
                $im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                $im->setResolution(300, 300);
                $im->contrastImage(30);
                $img_width  = $im->getImageWidth();
                $img_height = $im->getImageHeight();

                // generate parsed text based on different Imagick enhancements and push to $converted
                $converted[] = self::ocr_setting_1($im, $img_width, $img_height, $user_text);
                $converted[] = self::ocr_setting_2($im, $img_width, $img_height, $user_text);
                $converted[] = self::ocr_setting_3($im, $img_width, $img_height, $user_text);
                $converted[] = self::ocr_setting_4($im, $img_width, $img_height, $user_text);
                $converted[] = self::ocr_setting_5($im, $img_width, $img_height, $user_text);
                $converted[] = self::ocr_setting_6($im, $img_width, $img_height, $user_text);
                $converted[] = self::ocr_setting_7($im, $img_width, $img_height, $user_text);
                $converted[] = self::ocr_setting_8($im, $img_width, $img_height, $user_text);

                // filter empty array values
                $converted = array_filter($converted);

            endif;

            wp_send_json([
                'converted' => $converted,
            ]);

            wp_die();

        endif;

        // *******************
        // PROCESS CHART BODY
        // *******************
        if (isset($_POST['chart_body'])) :

            $img_blob   = $_POST['chart_body'];
            $product_id = $_POST['product_id'];
            $user_text  = $_POST['user_text'];
            $sku        = get_post_meta($product_id, '_sku', true);
            $file_name  = $sku . '_body.jpg';
            $save_path  = VVIC_PATH . 'classes/products/img-parts/bodies/';
            $save_url   = VVIC_URL . 'classes/products/img-parts/bodies/';
            $file_path  = $save_path . $file_name;
            $file_url   = $save_url . $file_name;

            $body_saved = file_put_contents($file_path, file_get_contents($img_blob));

            if ($body_saved !== false) :

                update_post_meta($product_id, '_vvic_chart_body_img_url', $file_url);

                $im = new Imagick($file_url);

                $im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                $im->setResolution(300, 300);
                $im->contrastImage(60);
                $im->thresholdimage(0.75 * \Imagick::getQuantum(), 134217727);

                $data = $im->getImageBlob();
                $size = $im->getImageLength();

                $converted = self::process_body($data, $size, $user_text);

            endif;

            wp_send_json([
                'converted' => $converted
            ]);

            wp_die();
        endif;
    }
}
