<?php

/**
 * OCR interpretation of chart images and, writing of data to products and adding manual chart data to products where needed.
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
class VVIC_Charts {

    use VVIC_Chart_Save,
        VVIC_Chart_Review_Lightbox,
        VVIC_Chart_JS,
        VVIC_Chart_CSS,
        VVIC_Render_Chart_Table,
        VVIC_Chart_Parse_Lightbox,
        VVIC_Chart_Render_Prod_Table;

    /**
     * Init
     */
    public static function init() {

        // load scripts
        add_action( 'admin_footer', [ __CLASS__, 'vvic_chart_scripts' ] );

        // register ajax functions for saving updated chart
        add_action( 'wp_ajax_vvic_save_updated_chart', [ __CLASS__, 'vvic_save_updated_chart' ] );
        add_action( 'wp_ajax_nopriv_vvic_save_updated_chart', [ __CLASS__, 'vvic_save_updated_chart' ] );
    }

    /**
     * Scripts
     */
    public static function vvic_chart_scripts() {
        wp_enqueue_style( 'vvic_chart_css', self::css() );
        wp_enqueue_script( 'vvic_chart_js', self::js_ajax(), [ 'jquery' ] );
    }

    /**
     * Query VVIC products
     */
    public static function query_products() {

        $vvic_prods = get_posts( [
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'post_status'    => [ 'publish', 'draft' ],
                'meta_key'       => 'sb_chart_pre_header',
                'fields'         => 'ids',
                'order'          => 'ASC'
            ] );

        if ( !empty( $vvic_prods ) ):
            return $vvic_prods;
        else:
            return 'vvic products not found';
        endif;
    }

}

VVIC_Charts::init();
