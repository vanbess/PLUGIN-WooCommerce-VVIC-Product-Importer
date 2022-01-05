//var $ = jQuery;

jQuery( document ).ready( function ( $ ) {



// *****************
// jQuery Tabs init
// *****************
    $( function () {
        $( "#vvic-tabs" ).tabs();
    } );

// **********************************
// Cropper init and image extraction
// **********************************
// vars
    var main_img = document.querySelector( '#vvic_chart_main' ),
            cropped_header = $( '#vvic_cropped_header' ),
            cropped_header_input = $( '#vvic_cropped_header_input' ),
            cropped_body = $( '#vvic_cropped_body' ),
            cropped_body_input = $( '#vvic_cropped_body_input' ),
            extract_header = $( '#vvic_extract_chart_head' ),
            extract_body = $( '#vvic_extract_chart_body' ),
            save_convert = $( '#vvic_save_convert_chart_crops' ),
            nonce = $( '#vvic-tabs' ).data( 'nonce' ),
            product_id = $( '#vvic-tabs' ).data( 'product-id' ),
            convert_header = $( '#vvic_chart_header_to_text' ),
            convert_body = $( '#vvic_chart_body_to_text' ),
            cropper,
            header_img_src,
            body_img_src;

// init cropper
    cropper = new Cropper( main_img, {
        minContainerWidth: 700,
        minContainerHeight: 500,
//        viewMode: 2,
//        modal: false
    } );

// extract header on click
    extract_header.on( 'click', function ( e ) {

        e.preventDefault();

        // retrieve cropped image src
        header_img_src = cropper.getCroppedCanvas( {
            width: 1300
        } ).toDataURL();

        // set header img src
        cropped_header.attr( 'src', header_img_src ).removeClass( 'vvic_ch_img_small' );
        cropped_header_input.val( header_img_src );

    } );

// extract body on click
    extract_body.on( 'click', function ( e ) {

        e.preventDefault();

        // retrieve cropped image src
        body_img_src = cropper.getCroppedCanvas( {
            width: 1300
        } ).toDataURL();

        // set header img src
        cropped_body.attr( 'src', body_img_src ).removeClass( 'vvic_ch_img_small' );
        cropped_body_input.val( body_img_src );
    } );

// ****************************************************
// Save/Sideload chart header and body images via AJAX
// ****************************************************
    save_convert.on( 'click', function ( e ) {

        e.preventDefault();

        // data
        var data = {
            '_ajax_nonce': nonce,
            'action': 'vvic_save_chart_data',
            'product_id': product_id,
            'header': cropped_header_input.val(),
            'body': cropped_body_input.val(),
        }

        // send
        $.post( ajaxurl, data, function ( response ) {
            if ( response.success === true ) {
                window.alert( response.data.message );
            }
        } );

    } );

// ***********************************************************
// Convert header and body images to text using Tesseract OCR
// ***********************************************************

// header
    convert_header.on( 'click', function ( e ) {

        e.preventDefault();

        // vars
        var header_img = cropped_header.attr( 'src' );
        var target = $( '.vvic_converted_header_text_actual' );
        var proc_txt = $( this ).data( 'processing' );
        var compl_txt = $( this ).data( 'complete' );
        var nonce = $( this ).data( 'nonce' );
        var product_id = $( this ).data( 'product-id' );
        var user_text = $( '#user-text-header' ).val();

        // set button text
        $( this ).text( proc_txt );

        var data = {
            '_ajax_nonce': nonce,
            'action': 'vvic_process_chart_ocr',
            'chart_header': header_img,
            'product_id': product_id,
            'user_text': user_text
        }

        $.post( ajaxurl, data, function ( response ) {
//            console.log( response );

            if ( response.converted !== '' ) {
                $( '.vvic_converted_header_text, #vvic_save_converted_chart_text' ).show();
                target.show();
                target.text( response.converted );
                $( '#vvic_save_converted_chart_text' ).attr( 'disabled', false );
            }

            convert_header.text( compl_txt );
        } );


    } );

// body
    convert_body.on( 'click', function ( e ) {

        e.preventDefault();

        // vars
        var body_img = cropped_body.attr( 'src' );
        var target = $( '.vvic_converted_body_text_actual' );
        var proc_txt = $( this ).data( 'processing' );
        var compl_txt = $( this ).data( 'complete' );
        var nonce = $( this ).data( 'nonce' );
        var product_id = $( this ).data( 'product-id' );
        var user_text = $( '#user-text-body' ).val();

        // set button text
        $( this ).text( proc_txt );

        var data = {
            '_ajax_nonce': nonce,
            'action': 'vvic_process_chart_ocr',
            'chart_body': body_img,
            'product_id': product_id,
            'user_text': user_text
        }

        $.post( ajaxurl, data, function ( response ) {
            
            console.log( response );

            if ( response.converted !== '' ) {
                $( '.vvic_converted_body_text, #vvic_save_converted_chart_text' ).show();
                target.show();
                target.html( response.converted );
                $( '#vvic_save_converted_chart_text' ).attr( 'disabled', false );
            }

            convert_body.text( compl_txt );
        } );

    } );

// **********************************************************
// Save converted chart header and body text to product meta
// **********************************************************
// save converted header
    $( 'button#vvic_save_converted_chart_text' ).on( 'click', function ( e ) {

        e.preventDefault();

        // updated text
        var updated = $( this ).data( 'updated' );

        // retrieve converted header text
        var header_text = $( '.vvic_converted_header_text_actual' ).text();

        // retrieve converted body text
        var body_text = $( '.vvic_converted_body_text_actual' ).html();
        
        body_text = body_text.replace(/<br\s*\/?>/gim, '\n');
        
//        console.log( body_text );

        if ( header_text === '' || body_text === '' ) {
            window.alert( 'Please convert both chart header and body texts before attempting to save.' );
        } else {

            // data
            var data = {
                '_ajax_nonce': nonce,
                'action': 'vvic_save_chart_data',
                'product_id': product_id,
                'header_text': header_text,
                'body_text': body_text
            }

            // send
            $.post( ajaxurl, data, function ( response ) {
//                console.log( response );
                window.alert( response );
            } );
        }
    } );

} );