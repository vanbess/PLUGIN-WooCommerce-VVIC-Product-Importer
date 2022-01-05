<?php

/**
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait VVIC_Chart_JS
{

    /**
     * JS and AJAX script
     */
    public static function js_ajax()
    {
?>
        <script>
            jQuery(function($) {

                // **************************************
                // SHOW SIZE CHART IMAGE REVIEW LIGHTBOX
                // **************************************
                $('.vvic_review_chart_img').on('click', function() {

                    // retrieve product id
                    var prod_id = $(this).data('prod-id');

                    // show/hide modal et al
                    $('.vvic_review_cont').hide();
                    $("#vvic_chart_review_" + prod_id).show();
                    $(".vvic_review_overlay").show();

                    // retrieve chart img url
                    var chart_img = $('#chart-img-' + prod_id).val();

                    // retrieve header json
                    var header_json = $('#ocr-chart-head-' + prod_id).val();

                    // retrieve body json
                    var body_json = $('#ocr-chart-body-' + prod_id).val();

                    // grab table for inserting chart data
                    var table = $('#vvic_chart_data_table_' + prod_id);

                    // empty table when opening lightbox so that we don't keep appending data to it on each lightbox open
                    $('#vvic_chart_header_' + prod_id).html('');
                    $('.vvic_body_data_row').remove();

                    // parse chart header
                    var header = JSON.parse(header_json);

                    // insert header data into chart table DOM via loop
                    $(header).each(function(index, th_data) {
                        $('#vvic_chart_header_' + prod_id).append('<th class="' + th_data.class + '" colspan="' + th_data.colspan + '" contenteditable="true">' + th_data.value + '</td>');
                    });

                    // parse chart body
                    var body = JSON.parse(body_json)

                    var row_id = '';

                    // insert body data into chart table DOM via loop
                    $(body).each(function(row_index, body_row_data) {

                        // generate body data row id
                        row_id = prod_id + '_' + row_index;

                        // append body data row
                        table.append('<tr class="vvic_body_data_row" id="' + row_id + '"></tr>');

                        // loop to append row data to row
                        $(body_row_data).each(function(index, td_data) {
                            $('#' + row_id).append('<td class="' + td_data.class + '" colspan="' + td_data.colspan + '" contenteditable="true">' + td_data.value + '</td>');
                        });
                    });

                    // insert chart image source
                    $('.vvic_chart_img > img').attr('src', chart_img);
                });

                // close review modal
                $('span.vvic_close').on('click', function() {
                    $(this).parent().hide();
                    $('.vvic_review_overlay').hide();
                });

                // hide overlay + modal
                $('.vvic_review_overlay').on('click', function() {
                    $('.vvic_review_overlay').hide();
                    $('.vvic_review_cont').hide();
                });

                // *******************
                // SAVE UPDATED CHART
                // *******************
                $('button.vvic_update_product_chart').on('click', function(e) {

                    e.preventDefault();

                    // retrieve product id
                    var prod_id = $(this).data('prod-id');

                    // retrieve chart header data
                    var chart_header = $('#vvic_chart_header_' + prod_id + ' th');

                    // setup header data object
                    var header_obj = {};

                    // push chart header date to chart_data object
                    $(chart_header).each(function(index, element) {
                        header_obj[index] = {
                            'class': $(this).attr('class'),
                            'colspan': $(this).attr('colspan'),
                            'value': $(this).text()
                        };
                    });

                    // stup chart body data object
                    const body_obj = {};

                    // loop through data rows and build td data obj data
                    $('.vvic_body_data_row').each(function(r_index, element) {

                        // setup td data object
                        var td_obj = {};

                        // find td in .vvic_body_data_row
                        var td = $(this).find('td');

                        // loop through td and push data to td_obj
                        $(td).each(function(index, element) {

                            var td_class = $(this).attr('class');
                            var td_colspan = $(this).attr('colspan');
                            var td_val = $(this).text();

                            td_obj[index] = {
                                'class': td_class,
                                'colspan': td_colspan,
                                'value': td_val
                            };
                        });

                        // push td data object to body object
                        body_obj[r_index] = td_obj;

                    });

                    // save updated chart data
                    var prod_id = $(this).data('prod-id');
                    var nonce = $(this).data('nonce');

                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'vvic_save_updated_chart',
                        'prod_id': prod_id,
                        'header_data': header_obj,
                        'body_data': body_obj

                    }

                    $.post(ajaxurl, data, function(response) {
                        if (response) {
                            window.alert('<?php _e('Chart data has been updated.', 'woocommerce'); ?>');
                            location.reload();
                        }
                    });

                });

                // **********************************
                // SHOW SIZE CHART OCR PARSING MODAL
                // **********************************
                $('button.vvic_parse_chart_img').on('click', function(e) {

                    e.preventDefault();

                    $('.vvic_converted_header_text_actual').empty();

                    // grab product id
                    var product_id = $(this).data('prod-id');

                    // grab main img src and set it
                    var main_img_src = $(this).data('img-src');
                    $('.vvic_parse_chart_main_' + product_id).attr('src', main_img_src);

                    // setup vars
                    var main_img = document.querySelector('.vvic_parse_chart_main_' + product_id),
                        cropped_header = $('#vvic_cropped_parse_header_' + product_id),
                        cropped_body = $('#vvic_cropped_parse_body_' + product_id),
                        extract_header = $('#vvic_extract_chart_head_' + product_id),
                        extract_body = $('#vvic_extract_chart_body_' + product_id),
                        cropper,
                        header_img_src,
                        body_img_src,
                        nonce;

                    // show lightbox and overlay
                    $('.vvic_chart_parse_overlay, #vvic_chart_parse_' + product_id).show();

                    // init cropper
                    cropper = new Cropper(main_img, {
                        background: false
                    });

                    // extract header on click
                    extract_header.on('click', function(e) {

                        e.preventDefault();

                        // retrieve cropped image src
                        header_img_src = cropper.getCroppedCanvas({
                            width: 1300
                        }).toDataURL();

                        // set header img src
                        cropped_header.attr('src', header_img_src).show();
                        $('#vvic_no_chart_header_' + product_id).hide();

                    });

                    // extract body on click
                    extract_body.on('click', function(e) {

                        e.preventDefault();

                        // retrieve cropped image src
                        body_img_src = cropper.getCroppedCanvas({
                            width: 1300
                        }).toDataURL();

                        // set header img src
                        cropped_body.attr('src', body_img_src).show();
                        $('#vvic_no_chart_body_' + product_id).hide();
                    });

                });

                // *******************
                // parse header image
                // *******************
                $('.vvic-parse-head').on('click', function() {

                    // setup vars
                    var prod_id = $(this).data('product-id');
                    var processing = $(this).data('processing');
                    var complete = $(this).data('complete');
                    var img_blob = $('#vvic_cropped_parse_header_' + prod_id).attr('src');
                    var nonce = $(this).data('nonce');
                    var user_text = $('.user-text-header').val();
                    var button = $(this);
                    var target = $('#vvic_converted_header_text_actual_' + prod_id);

                    // update button text
                    button.text(processing);

                    // send ajax to retrieve converted text
                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'vvic_process_chart_ocr',
                        'product_id': prod_id,
                        'chart_header': img_blob,
                        'user_text': user_text

                    }

                    // get/insert response
                    $.post(ajaxurl, data, function(response) {

                        var converted = response.converted;

                        if (converted !== '') {
                            $(converted).each(function(index, element) {
                                target.append('<input type="checkbox" class="vvic_conv_header" value="' + element + '"><label>' + element + '</label><br><br>');
                            });
                            $('#vvic_converted_header_text_' + prod_id).show();
                        }
                        button.text(complete);
                    });

                });

                // *****************
                // parse body image
                // *****************
                $('.vvic-parse-body').on('click', function() {

                    // setup vars
                    var prod_id = $(this).data('product-id');
                    var processing = $(this).data('processing');
                    var complete = $(this).data('complete');
                    var img_blob = $('#vvic_cropped_parse_body_' + prod_id).attr('src');
                    var nonce = $(this).data('nonce');
                    var user_text = $('.user-text-body').val();
                    var button = $(this);

                    // update button text
                    button.text(processing);

                    // send ajax to retrieve converted text
                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'vvic_process_chart_ocr',
                        'product_id': prod_id,
                        'chart_body': img_blob,
                        'user_text': user_text

                    }

                    // get/insert response
                    $.post(ajaxurl, data, function(response) {
                        button.text(complete);
                        $('#vvic_converted_body_text_actual_' + prod_id).html(response.converted);
                        $('#vvic_converted_body_text_' + prod_id).show();
                        $('#vvic_save_converted_chart_text_' + prod_id).show();
                    });

                });

                // **************************************************
                // save parsed chart header and body data to product
                // **************************************************
                $('.vvic_save_converted_chart_text').on('click', function(e) {

                    e.preventDefault();

                    // vars
                    var prod_id = $(this).data('prod-id');
                    var header_ocr_text = '';
                    var body_ocr_text = $('#vvic_converted_body_text_actual_' + prod_id).html();
                    var nonce = $(this).data('nonce');

                    // find selected header text
                    $('.vvic_conv_header').each(function(index, element) {
                        if ($(this).is(':checked')) {
                            header_ocr_text = $(this).val();
                        }
                    });

                    // replace <br> tags in body text
                    body_ocr_text = body_ocr_text.replace(/<br\s*\/?>/gim, '\n');

                    // check data and save via ajax if found
                    if (header_ocr_text === '' || body_ocr_text === '') {
                        window.alert('<?php _e('Please convert both chart header and body texts before attempting to save.', 'woocommerce'); ?>');
                    } else {

                        // data
                        var data = {
                            '_ajax_nonce': nonce,
                            'action': 'vvic_save_chart_data',
                            'product_id': prod_id,
                            'header_text': header_ocr_text,
                            'body_text': body_ocr_text
                        }

                        // send
                        $.post(ajaxurl, data, function(response) {
                            window.alert(response);
                            location.reload();
                        });
                    }

                });

                // hide lightbox and overlay
                $('span.vvic_close_ocr, .vvic_chart_parse_overlay').on('click', function(e) {
                    e.preventDefault();
                    $('.vvic_chart_parse_cont, .vvic_chart_parse_overlay').hide();
                });

            });
        </script>
<?php
    }
}
