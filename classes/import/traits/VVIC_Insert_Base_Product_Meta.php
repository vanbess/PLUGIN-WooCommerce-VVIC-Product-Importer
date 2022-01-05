<?php
/**
 * Inserts basic product meta during product import from parsed CSV import file
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Insert_Base_Product_Meta {
    
    /**
     * Insert base product metadata as retrieved from parsed CSV import file data,
     * then insert WC default product meta
     * 
     * @param int $product_id - Product ID to which metadata should be attached
     * @param array $product - Product data array as parsed from CSV import file
     * 
     * @see VVIC_Import
     */
    public static function insert_base_product_meta($product_id, $product) {
        
        // insert product meta parsed from CSV import file
        update_post_meta( $product_id, '_sku', $product[ 'sku' ] ? $product[ 'sku' ] : ''  );
        update_post_meta( $product_id, '_vvic_url', $product[ 'vvic_url' ] ? $product[ 'vvic_url' ] : ''  );
        update_post_meta( $product_id, '_regular_price', $product[ 'price_usd' ] ? $product[ 'price_usd' ] : '0.00'  );
        update_post_meta( $product_id, '_vvic_product_categories', $product[ 'categories' ] ? $product[ 'categories' ] : ''  );
        update_post_meta( $product_id, '_vvic_size_chart_image', $product[ 'size_chart_image' ] ? $product[ 'size_chart_image' ] : ''  );

        // insert default product meta
        update_post_meta( $product_id, '_tax_status', 'taxable' );
        update_post_meta( $product_id, '_manage_stock', 'no' );
        update_post_meta( $product_id, '_backorders', 'no' );
        update_post_meta( $product_id, '_sold_individually', 'no' );
        update_post_meta( $product_id, '_virtual', 'no' );
        update_post_meta( $product_id, '_downloadable', 'no' );
        update_post_meta( $product_id, '_stock', null );
        update_post_meta( $product_id, '_stock_status', 'instock' );
        update_post_meta( $product_id, '_product_version', get_option( 'woocommerce_version' ) );
        update_post_meta( $product_id, '_no_shipping_required', 'no' );
        
    }
    
}
