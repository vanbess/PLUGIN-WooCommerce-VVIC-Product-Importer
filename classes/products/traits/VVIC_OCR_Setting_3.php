<?php

/**
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_OCR_Setting_3
{

        use VVIC_OCR_Run_Head;

        public static function ocr_setting_3($im, $img_width, $img_height, $user_text)
        {

                $im2 = clone $im;
                $im2->adaptiveSharpenImage(100, 1);
                $im2->paintTransparentImage($im2->getImagePixelColor(0, 0), 0, 1200);
                $im2->resizeImage($img_width * 1.5, $img_height * 1.5, Imagick::FILTER_LANCZOS, 1);

                $data = $im2->getImageBlob();
                $size = $im2->getImageLength();

                $text = self::process_head($data, $size, $user_text);

                unset($im2);

                return $text;
        }
}
