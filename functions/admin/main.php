<?php

/**
 * Function for creating admin page
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
function vvic_import_csv_page() {

    // add main menu page
    add_menu_page( __( 'Import VVIC Products', 'woocommerce' ), __( 'VVIC Imports', 'woocommerce' ), 'manage_options', 'vvic-product-import', 'vvic_import_csv', 'dashicons-database-import', 6 );

    // add attributes mapping page
    add_submenu_page( 'vvic-product-import', __( 'Attribute Map', 'woocommerce' ), __( 'Attribute Map', 'woocommerce' ), 'manage_options', 'vvic-attribute-map', 'vvic_map_attributes_render' );

    // add product codes page
    add_submenu_page( 'vvic-product-import', __( 'SKU Ending Codes', 'woocommerce' ), __( 'SKU Ending Codes', 'woocommerce' ), 'manage_options', 'vvic-sku-ending-codes', 'vvic_product_codes_render' );

    // add chart paramater map
    add_submenu_page( 'vvic-product-import', __( 'Size Chart Parameter Map', 'woocommerce' ), __( 'Chart Parameter Map', 'woocommerce' ), 'manage_options', 'vvic-chart-paramater-map', 'vvic_chart_parameter_map_render' );

    // add chart parse page
    add_submenu_page( 'vvic-product-import', __( 'Crop/Parse Charts', 'woocommerce' ), __( 'Crop/Parse Charts', 'woocommerce' ), 'manage_options', 'vvic-charts-crop', 'vvic_charts_crop_render' );

    // add chart review page
    add_submenu_page( 'vvic-product-import', __( 'Review Charts', 'woocommerce' ), __( 'Review Charts', 'woocommerce' ), 'manage_options', 'vvic-chart-reviews', 'vvic_chart_reviews_render' );
}

add_action( 'admin_menu', 'vvic_import_csv_page' );

/**
 * Add OCR CSV import page
 * 
 * @global string $title - page title
 */
function vvic_import_csv() {
    include VVIC_PATH . 'functions/admin/imports/csv-import.php';
}

/**
 * Attribute mapping page
 * 
 * @global string $title - page title
 */
function vvic_map_attributes_render() {
    include VVIC_PATH . 'functions/admin/attributes.php';
}

/**
 * Product codes page
 * 
 * @global string $title - page title
 */
function vvic_product_codes_render() {
    include VVIC_PATH . 'functions/admin/sku-ending-codes.php';
}

/**
 * Chart parameter map
 */
function vvic_chart_parameter_map_render() {
    include VVIC_PATH . 'functions/admin/chart-parameter-map.php';
}

/**
 * Chart review page
 * 
 * @global string $title - page title
 */
function vvic_chart_reviews_render() {
    global $title;
    ?>
    <div class="wrap">
        <h2><?php echo $title; ?></h2>

        <?php
        VVIC_Charts::render_product_table();
        ?>

    </div>
    <?php
}

/**
 * Chart crop and parse page
 * 
 * @global string $title - page title
 */
function vvic_charts_crop_render() {
    global $title;
    ?>
    <div class="wrap">
        <h2><?php echo $title; ?></h2>

        <?php
        VVIC_Charts::render_chart_table();
        ?>

    </div>
    <?php
}
