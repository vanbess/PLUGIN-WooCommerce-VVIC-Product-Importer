<?php

/**
 * Displays imported product variations status
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Prod_Variations_Import_Status {

    /**
     * Display variation status flags
     * 
     * @param object $post - Global post object
     */
    public static function variation_data_status($post) {

        $prod_obj = wc_get_product( $post->ID );

        $children = $prod_obj->get_children();

        $child_count = count( $children );
        ?>

        <p><?php _e( 'This product has ' . $child_count . ' children.', 'woocommerce' ); ?></p>

        <table class="widefat fixed striped table-view-list" cellspacing="0">
            <tr>
                <th class="manage-column column-columnname" scope="col"><b><?php _e( 'ID', 'woocommerce' ); ?></b></th>
                <th class="manage-column column-columnname" scope="col"><b><?php _e( 'Thumbnail ID', 'woocommerce' ); ?></b></th>
                <th class="manage-column column-columnname" scope="col"><b><?php _e( 'Price', 'woocommerce' ); ?></b></th>
                <th class="manage-column column-columnname" scope="col"><b><?php _e( 'SKU', 'woocommerce' ); ?></b></th>
            </tr>

            <?php
            foreach ( $children as $child ):

                $price = get_post_meta( $child, '_regular_price', true );
                $thumb = get_post_meta( $child, '_thumbnail_id', true );
                $sku   = get_post_meta( $child, '_sku', true );
                ?>

                <tr>
                    <td class="column-columnname"><?php echo $child; ?></td>
                    <td class="column-columnname"><?php echo $thumb == 0 ? _e( '<span style="color: red"><b>No thumbnail/img</b></span>', 'woocommerce' ) : $thumb; ?></td>
                    <td class="column-columnname"><?php echo $price ? $price : _e( '<span style="color: red"><b>No price</b></span>', 'woocommerce' ); ?></td>
                    <td class="column-columnname"><?php echo $sku ? $sku : _e( '<span style="color: red"><b>No SKU</b></span>', 'woocommerce' ); ?></td>
                </tr>

            <?php endforeach; ?>
        </table>
        <?php
    }

}
