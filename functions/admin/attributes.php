<?php
/**
 * Attribute mapping HTML
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
    if ( isset( $_POST[ 'vvic_attr_save_map' ] ) ):

        $zh_values = $_POST[ 'vvic_attr_zh' ];
        $en_values = $_POST[ 'vvic_attr_en' ];

        $vvic_attributes = array_combine( $zh_values, $en_values );

        $attributes_saved = update_option( 'vvic_attributes', maybe_serialize( $vvic_attributes ) );

        if ( $attributes_saved ):
            ?>
            <div class="notice notice-info is-dismissible">
                <p><?php _e( 'Attributes saved', 'woocommerce' ); ?></p>
            </div>
            <?php
        endif;

    endif;
    ?>

    <p><?php _e( 'Use the inputs below to add and map attributes.', 'woocommerce' ); ?></p>

    <form action="" method="post">

        <?php
        // retrieve OCR attributes
        $vvic_attributes = maybe_unserialize( get_option( 'vvic_attributes' ) );

        // if ocr attributes present, display
        if ( $vvic_attributes ):
            foreach ( $vvic_attributes as $zh => $en ):
                ?>

                <!-- attr line cont -->
                <div class="vvic_attr_line">

                    <!-- attr zh -->
                    <div class="vvic_attr_zh_cont">
                        <input type="text" id="vvic_attr_zh" name="vvic_attr_zh[]" placeholder="chinese attribute" value="<?php echo $zh; ?>">
                    </div>

                    <!-- attr en -->
                    <div class="vvic_attr_zh_cont">
                        <input type="text" id="vvic_attr_en" name="vvic_attr_en[]" placeholder="english attribute" value="<?php echo $en; ?>">
                    </div>

                    <!-- add/remove -->
                    <div class="vvic_attr_add_rem">
                        <a class="vvic_attr_add" href="#" title="<?php _e( 'add attribute', 'woocommerce' ); ?>">+</a>
                        <a class="vvic_attr_rem" href="#" title="<?php _e( 'remove attribute', 'woocommerce' ); ?>">-</a>
                    </div>

                </div><!-- .vvic_attr_line ends -->

                <?php
            endforeach;
        endif;

        // add new attribute
        ?>

        <!-- attr line cont -->
        <div class="vvic_attr_line add_new">

            <!-- attr zh -->
            <div class="vvic_attr_zh_cont">
                <input type="text" id="vvic_attr_zh" name="vvic_attr_zh[]" placeholder="chinese attribute">
            </div>

            <!-- attr en -->
            <div class="vvic_attr_zh_cont">
                <input type="text" id="vvic_attr_en" name="vvic_attr_en[]" placeholder="english attribute">
            </div>

            <!-- add/remove -->
            <div class="vvic_attr_add_rem">
                <a class="vvic_attr_add" href="#" title="<?php _e( 'add attribute', 'woocommerce' ); ?>">+</a>
                <a class="vvic_attr_rem" href="#" title="<?php _e( 'remove attribute', 'woocommerce' ); ?>">-</a>
            </div>

        </div><!-- .vvic_attr_line ends -->

        <!-- save attribute map -->
        <div class="vvic_attr_save_cont">
            <button id="vvic_attr_save_map" type="submit" name="vvic_attr_save_map" class="button button-primary">
                <?php _e( 'Save Attribute Map', 'woocommerce' ); ?>
            </button>
        </div>

    </form>
</div>
