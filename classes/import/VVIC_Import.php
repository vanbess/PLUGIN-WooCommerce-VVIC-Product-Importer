<?php

/**
 * Handles import process
 *
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
class VVIC_Import
{

    /**
     * Traits which handle entire import process
     */
    use VVIC_Retrieve_Product_Data,
        VVIC_Insert_Base_Product_Meta,
        VVIC_Insert_Chart_Image,
        VVIC_Insert_VVIC_Product_Meta,
        VVIC_Insert_Main_Image,
        VVIC_Insert_Gallery_Images,
        VVIC_Map_Product_Categories,
        VVIC_Map_Product_Attributes,
        VVIC_Attach_Variation_Data;

    /**
     * @var array - Array of product ids for which data retrieval failed, i.e. data retrieval needs to be rerun
     */
    static $rerun_ids = [];

    /**
     * @var array $inserted_product_ids - Array of inserted product ids used for later ref
     */
    static $inserted_product_ids = [];

    /**
     * @var array - Holds product ids for which gallery images failed to download or gallery images don't exist
     */
    static $gallery_img_fails = [];

    /**
     * @var array - product ids for which props images somehow could not be retrieved
     */
    static $props_imgs_fails = [];

    /**
     * @var array - list of CSV skus which failed to import
     */
    static $failed_product_skus = [];

    /**
     * Init class
     */
    public static function init()
    {

        // schedule product import
        add_action('admin_head', [__CLASS__, 'schedule_import']);

        // function to run during initial product import
        add_action('vvic_import_products', [__CLASS__, 'import_products']);

        // function to rerun data retrieval on product ids which failed initial vvic data retrieval
        add_action('vvic_retrieve_failed_data', [__CLASS__, 'rerun_data_import_failed']);

        // function to run during gallery images import
        add_action('vvic_import_gallery_imgs', [__CLASS__, 'sideload_gallery_images']);

        // function to import variation data
        add_action('vvic_insert_variation_meta', [__CLASS__, 'attach_variation_data']);

        // check for missing product data
        self::check_product_data();

        // rerun gallery image imports for products without gallery images
        add_action('vvic_reimport_gallery_imgs', [__CLASS__, 'rerun_gallery_import']);
    }

    /**
     * Schedule product import actions through Action Scheduler
     */
    public static function schedule_import()
    {

        // check reruns
        $variation_reruns = get_option('_vvic_variations_max_reruns');
        $prod_meta_reruns = get_option('_vvic_prod_meta_max_reruns');

        // perform initial product import
        if (false === as_next_scheduled_action('vvic_import_products') && get_option('_vvic_process_product_import') === 'yes') :
            as_schedule_single_action(strtotime('now'), 'vvic_import_products');
            update_option('_vvic_process_product_import', 'no');
            update_option('_vvic_retrieve_gall_prod_imgs', 'yes');
        endif;

        // perform gallery images import 
        if (false === as_next_scheduled_action('vvic_import_gallery_imgs') && get_option('_vvic_retrieve_gall_prod_imgs') === 'yes') :
            as_schedule_single_action(strtotime('now'), 'vvic_import_gallery_imgs');
            update_option('_vvic_retrieve_gall_prod_imgs', 'no');
            update_option('_vvic_attach_variation_data', 'yes');
        endif;

        // attach missing variation data
        if (false === as_next_scheduled_action('vvic_insert_variation_meta') && get_option('_vvic_attach_variation_data') === 'yes' && $variation_reruns > 0) :
            as_schedule_single_action(strtotime('now'), 'vvic_insert_variation_meta');
            update_option('_vvic_attach_variation_data', 'no');

            // update rerun count
            $variation_reruns--;
            update_option('_vvic_variations_max_reruns', $variation_reruns);

        endif;

        // perform data retrieval rerun on product ids for which initial and subsequent data retrieval failed
        if (false === as_next_scheduled_action('vvic_retrieve_failed_data') && !empty(self::$rerun_ids) || !empty(self::$props_imgs_fails) && $prod_meta_reruns > 0) :
            as_schedule_single_action(strtotime('now'), 'vvic_retrieve_failed_data');

            // empty $rerun_ids array to avoid infinite reruns of this action
            self::$rerun_ids        = [];
            self::$props_imgs_fails = [];

            // update rerun count
            $prod_meta_reruns--;
            update_option('_vvic_prod_meta_max_reruns', $prod_meta_reruns);

            // schedule variation meta action
            update_option('_vvic_attach_variation_data', 'yes');

        endif;

        // perform gallery images import for products which failed to attach gallery images
        if (false === as_next_scheduled_action('vvic_reimport_gallery_imgs') && !empty(self::$gallery_img_fails)) :
            as_schedule_single_action(strtotime('now'), 'vvic_reimport_gallery_imgs');

            // empty $gallery_img_fails array to avoid infinite reruns of this action
            self::$gallery_img_fails = [];

        endif;
    }

    /**
     * VVIC product import function
     */
    public static function import_products()
    {

        // change max execution time
        ini_set('max_execution_time', 600);

        // retrieve product data
        $product_data = maybe_unserialize(get_option('_vvic_product_data'));

        // retrieve currently marked CSV file
        $current_csv = get_option('_vvic_last_csv_uploaded');
        $current_csv = str_replace(VVIC_PATH . 'functions/admin/imports/csv-files/', '', $current_csv);

        // include required wordpress file to handle upload/sideload
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // loop through product data and insert initial basic version of product
        foreach ($product_data as $product) :

            // skip products which has been flagged as not available at time of import
            $available = $product['is_available'];

            if ($available == 'N') :
                continue;
            endif;

            // insert product
            $product_id = wp_insert_post([
                'post_type'   => 'product',
                'post_status' => 'draft',
                'post_title'  => $product['product_title'] ? $product['product_title'] : $product['sku'] ? $product['sku'] : 'OCR Draft Product',
            ]);

            // if product inserted, continue processing
            if (!is_wp_error($product_id)) :

                // add product id to inserted product ids array
                self::$inserted_product_ids[] = $product_id;

                // insert base product meta
                self::insert_base_product_meta($product_id, $product);

                // strip vvic id from vvic product url
                $vvic_id = str_replace('https://www.vvic.com/item/', '', $product['vvic_url']);

                // add vvic_id to product meta for possible future reference if needed 
                update_post_meta($product_id, '_vvic_prod_id', $vvic_id);

                // query and return vvic data (error returned if query fails for some reason)
                $query_data = self::retrieve_vvic_data($vvic_id);

                // if no data retrieval error, insert relevant VVIC product data
                if ($query_data !== 'data retrieval error') :

                    // insert product meta based on returned query data (handles query failure as well)
                    self::insert_vvic_product_meta($query_data, $product_id);

                    // sideload main image
                    self::sideload_main_image($product_id);

                    // sideload chart image
                    self::sideload_size_chart_image($product_id);

                    // map product categories
                    self::map_product_categories($product_id);

                    // map attributes
                    self::map_product_attributes($product_id);

                // flag product for data retrieval rerun on query error
                elseif ($query_data === 'data retrieval error') :
                    self::$rerun_ids[] = $product_id;
                endif;

            // if product not inserted for some reason, log error and add product SKU to $failed_product_skus array
            elseif (is_wp_error($product_id)) :
                self::$failed_product_skus = $product['sku'];
            endif;

        endforeach;

        // If product ids array is not empty, insert associated meta data into db
        if (!empty(self::$inserted_product_ids)) :

            // insert product ids
            update_option('_vvic_last_inserted_products', maybe_serialize(self::$inserted_product_ids));

            // insert processed csv for future reference
            update_option('_vvic_csv_processed', $current_csv);

        endif;

        // If failed product ids present, update relevant option for use in resubmission action
        if (!empty(self::$failed_product_skus)) :
            update_option('_vvic_failed_product_imports', maybe_serialize(self::$failed_product_skus));
        endif;
    }

    /**
     * Checks for imported products for relevant data and reschedules either 
     * base product data retrieval or product gallery images retrieval
     * 
     * @static $gallery_image_fails
     * @static $rerun_ids
     */
    public static function check_product_data()
    {

        // retrieve existing vvic draft product ids
        $products = [];

        $vvic_products = get_posts([
            'post_type'   => 'product',
            'numberposts' => -1,
            'post_status' => 'draft',
            'meta_key'    => '_vvic_url',
            'fields'      => 'ids',
        ]);

        foreach ($vvic_products as $id) :
            $products[] = $id;
        endforeach;

        // loop
        if (!empty($products)) :
            foreach ($products as $product) :

                // retrieve main, gallery image ids, vvic gallery image links and vvic props image links
                $main_img          = get_post_meta($product, '_thumbnail_id', true);
                $gallery_ids       = get_post_meta($product, '_product_image_gallery', true);
                $gallery_img_links = get_post_meta($product, '_vvic_item_imgs', true);
                $props_img_links   = get_post_meta($product, '_vvic_props_list_img', true);

                // if main image does not exist, add product id to $rerun_ids array and continue
                if (!$main_img || empty($main_img)) :
                    self::$rerun_ids[] = $product;
                    continue;
                endif;

                // if gallery ids don't exist and vvic gallery image links meta present, add product id to $gallery_img_fails array and continue
                if (!$gallery_ids || empty($gallery_ids) && $gallery_img_links) :
                    self::$gallery_img_fails[] = $product;
                endif;

                // if gallery image links NOT present [vvic props images], push product ids to $props_images_failed array
                if ($props_img_links === 'No props images present' || !$props_img_links) :
                    self::$props_imgs_fails[] = $product;
                endif;

            endforeach;
        else :
            if (function_exists('as_unschedule_action') && true === as_next_scheduled_action('vvic_retrieve_failed_data')) :
                as_unschedule_action('vvic_retrieve_failed_data');
            endif;
        endif;
    }

    /**
     * Function to rerun vvic product data query and insert props images 
     * for any products which do not have them attach, or failed to attach them
     */
    public static function rerun_props_image_import()
    {

        // retrieve props images failed product ids
        $products = self::$props_imgs_fails;

        // if not empty, requery vvic product data for each and attach props list images
        if (!empty($products)) :

            foreach ($products as $product_id) :

                $vvic_id = get_post_meta($product_id, '_vvic_prod_id', true);

                $query_data = self::retrieve_vvic_data($vvic_id);

                if ($query_data !== 'data retrieval error') :

                    // add props list images
                    update_post_meta($product_id, '_vvic_props_list_img', !empty($query_data['item']['props_img']) ? maybe_serialize($query_data['item']['props_img']) : 'No props images present');

                endif;

            endforeach;

        endif;
    }

    /**
     * Function to retrieve vvic data on product ids for which data retrieval failed on initial and subsequent runs
     */
    public static function rerun_data_import_failed()
    {

        // retrieve previously imported product ids
        $products = self::$rerun_ids;

        // include required wordpress file to handle upload/sideload
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // loop through ids and rerun data retrieval process for products with _vvic_rerun_data_retrieval meta key
        if (!empty($products)) :
            foreach ($products as $product_id) :

                // retrieve vvic product id
                $vvic_id = get_post_meta($product_id, '_vvic_prod_id', true);

                // query and return vvic data (error returned if query fails for some reason)
                $query_data = self::retrieve_vvic_data($vvic_id);

                // if no data retrieval error, insert relevant VVIC product data
                if ($query_data !== 'data retrieval error') :

                    // insert product meta based on returned query data (handles query failure as well)
                    self::insert_vvic_product_meta($query_data, $product_id);

                    // sideload main image
                    self::sideload_main_image($product_id);

                    // sideload chart image
                    self::sideload_size_chart_image($product_id);

                    // map product categories
                    self::map_product_categories($product_id);

                    // map attributes
                    self::map_product_attributes($product_id);

                endif;

            endforeach;
        endif;
    }

    /**
     * Rerun gallery imports for products which failed to import
     * 
     * @uses $gallery_image_fails List of product ids which do not have gallery images attached
     */
    public static function rerun_gallery_import()
    {

        // change max execution time
        ini_set('max_execution_time', 600);

        // retrieve rerun product ids
        $gall_rerun_ids = self::$gallery_img_fails;

        // include required wordpress file to handle upload/sideload
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        if (!empty($gall_rerun_ids)) :

            foreach ($gall_rerun_ids as $product_id) :

                // setup gallery image ids array for attaching img ids to product after sideload
                $gall_img_ids = [];

                // retrieve item gallery image links
                $item_img_links = maybe_unserialize(get_post_meta($product_id, '_vvic_item_imgs', true));

                // loop through image links, sideload each and push ids to $gall_img_ids
                if (!empty($item_img_links)) :
                    foreach ($item_img_links as $img_link_arr) :

                        $gall_img_src = 'https:' . $img_link_arr['url'];

                        $gall_img_id = media_sideload_image($gall_img_src, null, null, 'id');

                        // log any errors and add failed pids to array
                        if (is_wp_error($gall_img_id)) :
                            $error_msg = $gall_img_id->get_error_message();
                            update_post_meta($product_id, '_vvic_gallery_imgs_retrieval_error', $error_msg);
                        // else push gallery img id to gall img ids array
                        elseif (!is_wp_error($gall_img_id)) :
                            $gall_img_ids[] = $gall_img_id;
                        endif;

                    endforeach;
                endif;

                // attach gallery image ids to product
                if (!empty($gall_img_ids)) :
                    update_post_meta($product_id, '_product_image_gallery', implode(',', $gall_img_ids));
                    delete_post_meta($product_id, '_vvic_gallery_imgs_retrieval_error');
                endif;

            endforeach;

        endif;
    }
}

// execute
VVIC_Import::init();
