<?php

/**
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Chart_Render_Prod_Table {

    /**
     * Render VVIC product table for chart reviews
     */
    public static function render_product_table() {

        $prod_ids = self::query_products();

        if ( $prod_ids === 'vvic products not found' ):
            ?>
            <div class="notice notice-warning">
                <p><?php _e( 'No VVIC products found', 'woocommerce' ); ?></p>
            </div>
            <?php
            return;
        endif;
        ?>

        <table id="vvic_product_table" class="widefat fixed striped table-view-list">

            <?php
            // loop
            $counter = 0;
            foreach ( $prod_ids as $pid ):

                // setup product data
                $sku              = get_post_meta( $pid, '_sku', true );
                $img_id           = get_post_meta( $pid, '_thumbnail_id', true );
                $img_act          = wp_get_attachment_image_src( $img_id, 'thumbnail' );
                $title            = get_the_title( $pid );
                $permalink        = get_edit_post_link( $pid );
                $has_chart_img    = get_post_meta( $pid, '_vvic_resized_chart_img', true ) === '' ? 'No chart image' : get_post_meta( $pid, '_vvic_resized_chart_img', true );
                $ocr_chart_header = maybe_unserialize( get_post_meta( $pid, 'sb_chart_pre_header', true ) );
                $ocr_chart_body   = maybe_unserialize( get_post_meta( $pid, 'sb_chart_pre_body', true ) );

                // skip items which do not have a chart image attached
                if ( $has_chart_img === 'No chart image' ):
                    continue;
                endif;

                // display header
                if ( $counter === 0 ):
                    ?>
                    <tr>
                        <th><b><?php _e( 'ID', 'woocommerce' ); ?></b></th>
                        <th><b><?php _e( 'Image', 'woocommerce' ); ?></b></th>
                        <th><b><?php _e( 'SKU', 'woocommerce' ); ?></b></th>
                        <th><b><?php _e( 'Title', 'woocommerce' ); ?></b></th>
                        <th><b><?php _e( 'Has chart image?', 'woocommerce' ); ?></b></th>
                        <th><b><?php _e( 'Chart reviewed?', 'woocommerce' ); ?></b></th>
                        <th><b><?php _e( 'Review chart', 'woocommerce' ); ?></b></th>
                    </tr>
                    <?php
                // display body
                endif;
                ?>
                <tr>
                    <td class="td-vvic-id"><a href="<?php echo $permalink; ?>" target="_blank" title="<?php _e( 'Open product edit screen in new tab', 'woocommerce' ); ?>"><u><?php echo $pid; ?></u></a></td>
                    <td class="td-vvic-img"><img src="<?php echo $img_act[ 0 ]; ?>" alt="<?php echo $title; ?>"/></td>
                    <td class="td-vvic-sku"><?php echo $sku; ?></td>
                    <td class="td-vvic-title"><?php echo $title; ?></td>
                    <td class="td-vvic-chart"><?php echo $has_chart_img; ?></td>
                    <td class="td-vvic-chart-reviewed"><?php get_post_meta( $pid, '_vvic_chart_reviewed', true ) === 'Yes' ? _e( 'Yes', 'woocommerce' ) : _e( 'No', 'woocommerce' ); ?></td>
                    <td class="td-vvic-review-chart">

                        <!-- hidden data -->
                        <input type="hidden" id="ocr-chart-head-<?php echo $pid; ?>" value="<?php echo htmlspecialchars( json_encode( $ocr_chart_header ), ENT_QUOTES, 'UTF-8' ); ?>">
                        <input type="hidden" id="ocr-chart-body-<?php echo $pid; ?>" value="<?php echo htmlspecialchars( json_encode( $ocr_chart_body ), ENT_QUOTES, 'UTF-8' ); ?>">
                        <input type="hidden" id="chart-img-<?php echo $pid; ?>" value="<?php echo $has_chart_img ?>">

                        <!-- display review modal -->
                        <button class="vvic_review_chart_img button button-primary" data-prod-id="<?php echo $pid; ?>">
                            <?php _e( 'Review Chart', 'woocommerce' ); ?>
                        </button>

                        <!-- review modal act -->
                        <?php self::chart_dialogue_box( $pid, $chart_img_url ); ?>
                    </td>
                </tr>
                <?php
                $counter++;

            endforeach;
            ?>
        </table>

        <?php
    }

}
