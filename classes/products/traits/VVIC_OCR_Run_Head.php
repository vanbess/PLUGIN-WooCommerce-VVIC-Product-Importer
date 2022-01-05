<?php

/**
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_OCR_Run_Head {

    public static function process_head($data, $size, $user_text) {

        $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR();
        $ocr->imageData( $data, $size );
        $ocr->userWords( $user_text );
        $ocr->lang( 'chi_sim' );
        $ocr->psm( 4 );
        $ocr->config( 'preserve_interword_spaces', 1 );
        $converted = $ocr->run();
        $converted = preg_replace( '/\s+/', ' ', trim( $converted ) );
        
        return $converted;
    }

}
