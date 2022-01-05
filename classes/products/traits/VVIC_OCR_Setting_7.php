<?php

/**
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_OCR_Setting_7 {

    use VVIC_OCR_Run_Head;

    public static function ocr_setting_7($im, $img_width, $img_height, $user_text) {

        $im2 = clone $im;
        $im2->modulateImage( 100, 0, 100 );
        $im2->thresholdimage( 0.65 * \Imagick::getQuantum(), 134217727 );
        $im2->negateImage( 'true' );
        $im2->resizeImage( $img_width * 1.25, $img_height * 1.25, Imagick::FILTER_LANCZOS, 1 );

        $data = $im2->getImageBlob();
        $size = $im2->getImageLength();

        $text = self::process_head( $data, $size, $user_text );

        unset( $im2 );

        return $text;
    }

}
