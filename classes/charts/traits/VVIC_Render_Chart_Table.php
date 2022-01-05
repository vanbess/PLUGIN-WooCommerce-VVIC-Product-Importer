<?php

/**
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Render_Chart_Table {
    
    /**
     * Render VVIC product table for parsing charts
     */
    public static function render_chart_table() {

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
                        <th><b><?php _e( 'Crop and Parse Chart', 'woocommerce' ); ?></b></th>
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
                    <td class="td-vvic-parse-chart">

                        <!-- display parse chart modal -->
                        <button class="vvic_parse_chart_img button button-primary" data-prod-id="<?php echo $pid; ?>" data-img-src="<?php echo $has_chart_img ?>">
                            <?php _e( 'Parse Size Chart', 'woocommerce' ); ?>
                        </button>

                        <!-- review modal act -->
                        <?php self::chart_parse_dialogue_box( $pid ); ?>
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
