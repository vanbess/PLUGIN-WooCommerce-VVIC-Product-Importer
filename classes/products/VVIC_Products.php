<?php

/**
 * Handles data errors and the rescheduling of querying and inserting and missing VVIC product data
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
class VVIC_Products
{

    /**
     * Traits
     */
    use VVIC_Prod_Data_Import_Status,
        VVIC_Prod_Variations_Import_Status,
        VVIC_Prod_Chart_Cropper,
        VVIC_Prod_Chart_AJAX,
        VVIC_Prod_Chart_OCR,
        VVIC_Prod_JS;

    /**
     * Init
     */
    public static function init()
    {

        // load js
        add_action('admin_footer', [__CLASS__, 'vvic_product_scripts']);

        // register meta box
        add_action('add_meta_boxes', [__CLASS__, 'vvic_meta_box']);

        // register AJAX action for saving chart data
        add_action('wp_ajax_vvic_save_chart_data', [__CLASS__, 'vvic_save_chart_data']);
        add_action('wp_ajax_nopriv_vvic_save_chart_data', [__CLASS__, 'vvic_save_chart_data']);

        // register AJAX action for reading chart header and body data using OCR
        add_action('wp_ajax_vvic_process_chart_ocr', [__CLASS__, 'vvic_process_chart_ocr']);
        add_action('wp_ajax_nopriv_vvic_process_chart_ocr', [__CLASS__, 'vvic_process_chart_ocr']);
    }

    /**
     * Enqueue scripts
     */
    public static function vvic_product_scripts()
    {
        wp_enqueue_script('vvic_chart_js', self::cropper_ocr_js(), ['jquery', 'vvic-cropper-js'], false, true);
        // wp_enqueue_script('vvic-cropper-js', VVIC_URL . 'assets/js/cropper.js', ['jquery'], '1.5.12', true);
    }

    /**
     * Register custom meta box for vvic product
     */
    public static function vvic_meta_box()
    {

        global $post;

        $is_vvic_prod = get_post_meta($post->ID, '_vvic_url', true);

        if ($is_vvic_prod) :
            add_meta_box(
                'vvic_missing_meta',
                __('VVIC Imported Data', 'woocommerce'),
                [__CLASS__, 'vvic_render_meta_box'],
                'product'
            );
        endif;
    }

    /**
     * Renders custom meta box html
     * 
     * @param object $post - Global post object
     */
    public static function vvic_render_meta_box($post)
    {
?>

        <div id="vvic-tabs" data-nonce="<?php echo wp_create_nonce('vvic product ajax'); ?>" data-product-id="<?php echo $post->ID; ?>">

            <!-- tab links -->
            <ul>
                <li><a href="#vvic-tabs-1"><?php _e('Imported Data Status', 'woocommerce'); ?></a></li>
                <li><a href="#vvic-tabs-2"><?php _e('Variations Status', 'woocommerce'); ?></a></li>
                <li><a href="#vvic-tabs-3"><?php _e('Extract chart image data', 'woocommerce'); ?></a></li>
            </ul>

            <!-- imported data -->
            <div id="vvic-tabs-1">
                <h4><?php _e('Imported VVIC meta data status is displayed below.', 'woocommerce'); ?></h4>

                <?php
                self::imported_data_status($post);
                ?>

            </div>

            <!-- variation data -->
            <div id="vvic-tabs-2">
                <h4><?php _e('Variation data status is displayed below', 'woocommerce'); ?></h4>

                <?php
                self::variation_data_status($post);
                ?>
            </div>

            <!-- extract product size chart image data -->
            <div id="vvic-tabs-3">
                <h4 id="vvic_chart_extract_head"><?php _e('Follow the instructions below to extract and save relevant size chart image data', 'woocommerce'); ?></h4>

                <?php
                self::vvic_crop_chart_img($post);
                ?>
            </div>
        </div>

<?php
    }
}

VVIC_Products::init();
