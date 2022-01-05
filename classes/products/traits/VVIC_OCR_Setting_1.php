<?php

/**
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_OCR_Setting_1 {
    
    use VVIC_OCR_Run_Head;

    public static function ocr_setting_1($im, $img_width, $img_height, $user_text) {
        
        $im2  = clone $im;
        $im2->resizeImage( $img_width, $img_height, Imagick::FILTER_LANCZOS, 1 );
        $data = $im2->getImageBlob();
        $size = $im2->getImageLength();

        $text = self::process_head( $data, $size, $user_text );
        
        unset( $im2 );

        return $text;
    }

}
