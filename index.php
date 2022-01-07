<?php

/**
 * Plugin Name: SBWC VVIC Product Importer
 * Description: Import VVIC products | Review Imported Data | Review Imported Chart | Edit | Publish!
 * Version: 1.0.1
 * Author: WC Bessinger
 */
!defined( 'ABSPATH' ) ? exit() : '';

add_action( 'plugins_loaded', 'vvic_import_init' );

function vvic_import_init() {

    // constants
    define( 'VVIC_PATH', plugin_dir_path( __FILE__ ) );
    define( 'VVIC_URL', plugin_dir_url( __FILE__ ) );

    // tesseract
    require_once VVIC_PATH . 'assets/tesseract/vendor/autoload.php';

    // include ajax function which processes last uploaded CSV
    include VVIC_PATH . 'functions/admin/ajax/process-csv.php';

    // admin page which contains all sub pages
    include VVIC_PATH . 'functions/admin/main.php';

    // js and css
    add_action( 'admin_enqueue_scripts', 'vvic_scripts' );

    function vvic_scripts() {
        wp_enqueue_script( 'vvic-admin', VVIC_URL . 'assets/js/admin.js', [ 'jquery' ], '1.0.1', true );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_style( 'vvic-admin', VVIC_URL . 'assets/css/admin.css' );
        wp_enqueue_style( 'vvic-jqui', VVIC_URL . 'assets/css/jquery.ui.min.css' );
        wp_enqueue_style( 'vvic-cropper-css', VVIC_URL . 'assets/css/cropper.css' );
        wp_enqueue_style( 'vvic-product-meta-css', VVIC_URL . 'assets/css/product.css' );
        wp_enqueue_script( 'vvic-cropper-js', VVIC_URL . 'assets/js/cropper.js', [ 'jquery' ], '1.15.2', true );
        wp_enqueue_script( 'vvic-cb-select', VVIC_URL . 'assets/js/select.js', [ 'jquery' ], false, true );
    }

    // chart review/parsing functionality
    include VVIC_PATH . 'classes/charts/traits/VVIC_Chart_Save.php';
    include VVIC_PATH . 'classes/charts/traits/VVIC_Chart_JS.php';
    include VVIC_PATH . 'classes/charts/traits/VVIC_Chart_CSS.php';
    include VVIC_PATH . 'classes/charts/traits/VVIC_Chart_Review_Lightbox.php';
    include VVIC_PATH . 'classes/charts/traits/VVIC_Chart_Render_Prod_Table.php';
    include VVIC_PATH . 'classes/charts/traits/VVIC_Chart_Parse_Lightbox.php';
    include VVIC_PATH . 'classes/charts/traits/VVIC_Render_Chart_Table.php';
    include VVIC_PATH . 'classes/charts/VVIC_Charts.php';

    // import class and traits
    include VVIC_PATH . 'classes/import/traits/VVIC_Insert_Base_Product_Meta.php';
    include VVIC_PATH . 'classes/import/traits/VVIC_Insert_Chart_Image.php';
    include VVIC_PATH . 'classes/import/traits/VVIC_Insert_Gallery_Images.php';
    include VVIC_PATH . 'classes/import/traits/VVIC_Insert_Main_Image.php';
    include VVIC_PATH . 'classes/import/traits/VVIC_Insert_VVIC_Product_Meta.php';
    include VVIC_PATH . 'classes/import/traits/VVIC_Retrieve_Product_Data.php';
    include VVIC_PATH . 'classes/import/traits/VVIC_Map_Product_Categories.php';
    include VVIC_PATH . 'classes/import/traits/VVIC_Map_Product_Attributes.php';
    include VVIC_PATH . 'classes/import/traits/VVIC_Attach_Variation_Data.php';
    include VVIC_PATH . 'classes/import/VVIC_Import.php';

    // product meta box
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Run_Body.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Run_Head.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Setting_1.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Setting_2.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Setting_3.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Setting_4.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Setting_5.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Setting_6.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Setting_7.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_OCR_Setting_8.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_Prod_JS.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_Prod_Chart_Cropper.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_Prod_Data_Import_Status.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_Prod_Variations_Import_Status.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_Prod_Chart_AJAX.php';
    include VVIC_PATH . 'classes/products/traits/VVIC_Prod_Chart_OCR.php';
    include VVIC_PATH . 'classes/products/VVIC_Products.php';
}
