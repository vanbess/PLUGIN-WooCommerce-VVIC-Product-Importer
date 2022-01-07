<?php

/**
 * Core CSV Import file
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
global $title;

// retrieve save options
$csv_processed = get_option('_vvic_csv_processed');
$csv_uploaded  = get_option('_vvic_last_csv_uploaded');

// delete_option('_vvic_product_data');
?>

<div class="wrap">

    <h2><?php echo $title; ?></h2>

    <?php
    // process csv file upload
    if (isset($_POST['vvic_upload_csv_file'])) :

        // upload paramaters
        $target_dir  = VVIC_PATH . 'functions/admin/imports/files/csv-files/';
        $target_file = $target_dir . basename($_FILES["vvic_csv_file"]["name"]);

        // move file to directory in question
        if (move_uploaded_file($_FILES['vvic_csv_file']['tmp_name'], $target_file)) :

            // update db
            update_option('_vvic_last_csv_uploaded', $target_file);
    ?>
            <div class="notice notice-info is-dismissible">
                <p><?php _e('The CSV file ' . htmlspecialchars(basename($_FILES['vvic_csv_file']['name'])) . ' has been uploaded successfully.', 'woocommerce'); ?></p>
            </div>
        <?php else : ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e('Sorry, there was an error uploading your CSV. Please try again.', 'woocommerce'); ?></p>
            </div>
        <?php
        endif;

    endif;

    // if csv file already uploaded
    if ($csv_uploaded) :
        $file_name = str_replace(VVIC_PATH . 'functions/admin/imports/files/csv-files/', '', $csv_uploaded);
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('<b>Last file uploaded:</b> ' . $file_name, 'woocommerce'); ?></p>
        </div>
    <?php
    endif;

    // if processing record found
    if ($csv_processed && !empty($csv_processed)) :

        $csv_delete_nonce = wp_create_nonce('delete last process csv');
    ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('<b>The last CSV file processed is:</b> ' . str_replace(VVIC_PATH . 'functions/admin/imports/files/csv-files/', '', $csv_processed) . '. <a id="vvic_delete_csv" href="#" data-nonce="' . $csv_delete_nonce . '"><b><u>CLICK HERE TO DELETE THIS FILE IF YOU\'RE EXPERIENCING PROCESSING ISSUES.</u></b></a>', 'woocommerce'); ?></p>
        </div>
    <?php
    // if no processing record found
    else :
    ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('No CSV processing record found. Upload a file below and submit for processing once upload is complete.', 'woocommerce'); ?></p>
        </div>
    <?php endif; ?>

    <hr>

    <!-- upload csv file -->
    <p><?php _e('Upload CSV file below and click Upload CSV to upload.', 'woocommerce'); ?></p>

    <!-- csv file notes -->
    <div class="vvic_csv_upload_note">

        <span><i><b><u><?php _e('Before uploading your CSV file, please ensure the following requirements are met:', 'woocommerce'); ?></u></b></i></span>

        <ul class="vvic_csv_file_requirements">
            <li><?php _e('Ensure that the file delimiter used is a semi-colon (;), otherwise the import will fail', 'woocommerce'); ?></li>
            <li><?php _e('Ensure that the following data columns are present in your file, in the following order, empty or not:', 'woocommerce'); ?></li>
            <ul class="vvic_csv_file_cols_required">
                <li>sku_code</li>
                <li>vvic_url</li>
                <li>product_title</li>
                <li>price_usd</li>
                <li>categories</li>
                <li>size_chart_image_url</li>
                <li>is_available</li>
            </ul>
            <li><?php _e('Ensure that Chinese => English Attribute Map has been set up', 'woocommerce'); ?></li>
            <li><?php _e('Ensure that SKU Ending Codes map has been set up', 'woocommerce'); ?></li>
            <li><?php _e('Ensure that Chart Parameter Map and Chart Parameter Replacement map has been set up', 'woocommerce'); ?></li>

        </ul>

    </div>

    <!-- upload csv file cont -->
    <div class="vvic_csv_upload_cont">

        <h4><u><?php _e('Select file to upload:', 'woocommerce'); ?></u></h4>

        <!-- upload csv file -->
        <form action="" method="post" enctype="multipart/form-data">

            <input type="file" name="vvic_csv_file"><br>

            <button id="vvic_upload_csv_file" type="submit" name="vvic_upload_csv_file" class="button button-primary">
                <?php _e('Upload CSV', 'woocommerce'); ?>
            </button>

        </form>

    </div>

    <br>

    <!-- process csv file cont -->
    <div class="vvic_csv_process_cont">

        <button id="vvic_process_csv" class="button button-primary" data-target-file="<?php echo $csv_uploaded; ?>" data-nonce="<?php echo wp_create_nonce('ocr process csv') ?>">
            <?php _e('Submit CSV for processing', 'woo'); ?>
        </button>

    </div>

</div>