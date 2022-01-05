<?php

/**
 * Displays status of imported VVIC Data
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Prod_Data_Import_Status {

    /**
     * Display imported data status flags
     * 
     * @param object $post - Global post object
     */
    public static function imported_data_status($post) {

        // retrieve relative post meta
        $chart_img       = get_post_meta( $post->ID, '_vvic_size_chart_image', true );
        $gallery_imgs    = maybe_unserialize( get_post_meta( $post->ID, '_vvic_item_imgs', true ) );
        $main_img        = get_post_meta( $post->ID, '_vvic_main_img', true );
        $props_list      = maybe_unserialize( get_post_meta( $post->ID, '_vvic_props_list', true ) );
        $props_list_imgs = maybe_unserialize( get_post_meta( $post->ID, '_vvic_props_list_img', true ) );
        ?>

        <!-- chart image -->
        <div class="vvic_retrieval_data">
            <?php if ( $chart_img ): ?>
                <p><span class="vvic_check">&#10003;</span> <?php _e( "Chart image present: $chart_img", 'woocommerce' ); ?></p>
            <?php else: ?>
                <p><span class="vvic_x">&#10007;</span> <?php _e( 'Chart image not present.', 'woocommerce' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- gallery images -->
        <div class="vvic_retrieval_data">
            <?php if ( $gallery_imgs ): ?>
                <p><span class="vvic_check">&#10003;</span> <?php _e( "Gallery image links present:", 'woocommerce' ); ?></p>
                <ul>
                    <?php foreach ( $gallery_imgs as $img_arr ): ?>
                        <li><?php echo $img_arr[ 'url' ]; ?></li>
                    <?php endforeach; ?>
                </ul>

            <?php else: ?>
                <p><span class="vvic_x">&#10007;</span> <?php _e( 'Gallery image links not present.', 'woocommerce' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- main image -->
        <div class="vvic_retrieval_data">
            <?php if ( $main_img ): ?>
                <p><span class="vvic_check">&#10003;</span> <?php _e( "Main image meta present: $main_img", 'woocommerce' ); ?></p>
            <?php else: ?>
                <p><span class="vvic_x">&#10007;</span> <?php _e( 'Main image meta not present.', 'woocommerce' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- props list -->
        <div class="vvic_retrieval_data">
            <?php if ( $props_list ): ?>
                <p><span class="vvic_check">&#10003;</span> <?php _e( "Product properties meta present:", 'woocommerce' ); ?></p>
                <ul>
                    <?php foreach ( $props_list as $prop ): ?>
                        <li><?php echo $prop; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><span class="vvic_x">&#10007;</span> <?php _e( 'Product properties meta not present.', 'woocommerce' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- props list images -->
        <div class="vvic_retrieval_data">
            <?php if ( $props_list_imgs !== 'No props images present' ): ?>
                <p><span class="vvic_check">&#10003;</span> <?php _e( "Product property images meta present:", 'woocommerce' ); ?></p>
                <ul>
                    <?php foreach ( $props_list_imgs as $key => $url ): ?>
                        <li><?php echo $url; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><span class="vvic_x">&#10007;</span> <?php _e( '<b>Product property images meta not present. <u>REASON:</u> ' . $props_list_imgs . '. Variation images have to be attached manually.</b>', 'woocommerce' ); ?></p>
            <?php endif; ?>
        </div>

        <?php
    }

}
