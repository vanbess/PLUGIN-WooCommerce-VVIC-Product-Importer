<?php

/**
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_OCR_Run_Body {

    public static function process_body($data, $size, $user_text) {
        
        $ocr       = new \thiagoalessio\TesseractOCR\TesseractOCR();
        $ocr->imageData( $data, $size );
        $ocr->userWords( $user_text ); 
        $ocr->lang( 'eng' );
        $ocr->psm( 4 );
        $ocr->config( 'preserve_interword_spaces', 1 );
        $converted = $ocr->run();
        $converted = preg_replace( '/\n+/', '<br>', trim( $converted ) );

        return $converted;
    }

}
