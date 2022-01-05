<?php

/**
 * Renders product SKU ending code and color inputs
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */

global $title;
?>

<div class="wrap">
    <h2><?php echo $title; ?></h2>

    <hr>

    <?php
    // save submitted data
    if ( isset( $_POST[ 'vvic_save_sku_codes' ] ) ):

        $sku_colors = $_POST[ 'vvic_sku_color' ];
        $sku_codes = $_POST[ 'vvic_sku_code' ];

        $sku_codes_complete = array_combine( $sku_colors, $sku_codes );

        $sku_codes_saved = update_option( 'vvic_sku_codes', maybe_serialize( $sku_codes_complete ) );

        if ( $attributes_saved ):
            ?>
            <div class="notice notice-info is-dismissible">
                <p><?php _e( 'SKU codes saved', 'woocommerce' ); ?></p>
            </div>
            <?php
        endif;

    endif;
    ?>

    <p><?php _e( 'Use the inputs below to add SKU ending codes.', 'woocommerce' ); ?></p>

    <form action="" method="post">

        <?php
        // retrieve OCR SKU codes
        $vvic_sku_codes = maybe_unserialize( get_option( 'vvic_sku_codes' ) );

        // if ocr attributes present, display
        if ( $vvic_sku_codes ):
            foreach ( $vvic_sku_codes as $color => $code ):
                ?>

                <!-- attr line cont -->
                <div class="vvic_sku_line">

                    <!-- attr zh -->
                    <div class="vvic_sku_color_cont">
                        <input type="text" id="vvic_sku_color" name="vvic_sku_color[]" placeholder="sku color" value="<?php echo $color; ?>">
                    </div>

                    <!-- attr en -->
                    <div class="vvic_sku_code_cont">
                        <input type="text" id="vvic_sku_code" name="vvic_sku_code[]" placeholder="sku code" value="<?php echo $code; ?>">
                    </div>

                    <!-- add/remove -->
                    <div class="vvic_sku_add_rem">
                        <a class="vvic_sku_add" href="#" title="<?php _e( 'add sku code', 'woocommerce' ); ?>">+</a>
                        <a class="vvic_sku_rem" href="#" title="<?php _e( 'remove sku code', 'woocommerce' ); ?>">-</a>
                    </div>

                </div><!-- .vvic_sku_line ends -->

                <?php
            endforeach;
        endif;

        // add new attribute
        ?>

        <!-- sku line cont -->
        <div class="vvic_sku_line add_new">

            <!-- sku color -->
            <div class="vvic_sku_color_cont">
                <input type="text" id="vvic_sku_color" name="vvic_sku_color[]" placeholder="sku color">
            </div>

            <!-- sku code -->
            <div class="vvic_sku_code_cont">
                <input type="text" id="vvic_sku_code" name="vvic_sku_code[]" placeholder="sku code">
            </div>

            <!-- add/remove -->
            <div class="vvic_sku_add_rem">
                <a class="vvic_sku_add" href="#" title="<?php _e( 'add sku code', 'woocommerce' ); ?>">+</a>
                <a class="vvic_sku_rem" href="#" title="<?php _e( 'remove sku code', 'woocommerce' ); ?>">-</a>
            </div>

        </div><!-- .vvic_sku_line ends -->

        <!-- save attribute map -->
        <div class="vvic_sku_save_cont">
            <button id="vvic_save_sku_codes" type="submit" name="vvic_save_sku_codes" class="button button-primary">
                <?php _e( 'Save SKU Ending Code', 'woocommerce' ); ?>
            </button>
        </div>

    </form>
</div>