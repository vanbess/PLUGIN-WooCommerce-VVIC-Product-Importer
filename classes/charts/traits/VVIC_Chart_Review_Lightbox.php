<?php

/**
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Chart_Review_Lightbox {

    /**
     * Product chart review dialog
     */
    public static function chart_dialogue_box($product_id) {
        ?>

        <!-- overlay -->
        <div class="vvic_review_overlay" style="display: none"></div>

        <!-- lightbox -->
        <div class="vvic_review_cont" id="vvic_chart_review_<?php echo $product_id; ?>" style="display:none">

            <span class="vvic_close">x</span>

            <div class="vvic_review_cont_inner">

                <h3><?php _e( 'Review chart for Product ID ' . $product_id, 'woocommerce' ); ?></h3>

                <div class="vvic_chart_data">

                    <!-- parsed chart -->
                    <div class="vvic_parsed_chart">

                        <h4><?php _e( 'Parsed chart', 'woocommerce' ); ?></h4>
                        <hr>

                        <p><?php _e( 'Below is a rendered version of size chart data as parsed via OCR. You can edit the data as needed by clicking on each cell. Once you\'re done editing, be sure to hit the update button.', 'woocommerce' ); ?></p>

                        <table id="vvic_chart_data_table_<?php echo $product_id; ?>" class="vvic_chart_data_table">

                            <!-- header -->
                            <tr id="vvic_chart_header_<?php echo $product_id; ?>"></tr>

                        </table>
                    </div>

                    <!-- chart image as uploaded -->
                    <div class="vvic_chart_img">
                        <h4><?php _e( 'Chart image', 'woocommerce' ); ?></h4>
                        <hr>
                        <img src="<?php echo $chart_img_url; ?>" alt="alt"/>
                    </div>
                </div>

                <div class="vvic_review_update_cont">
                    <button class="vvic_update_product_chart button button-primary" data-nonce="<?php echo wp_create_nonce( 'save updated chart data' ); ?>" data-prod-id="<?php echo $product_id; ?>">
                        <?php _e( 'Update Chart/Product', 'woocommerce' ); ?>
                    </button>
                </div>

            </div>

        </div>

        <?php
    }

}
