jQuery( document ).ready( function ( $ ) {
    
    // **********************
    // ADD DATA MAPPING PAIR
    // **********************
    
    // add attribute map
    $( document ).on( 'click', '.vvic_attr_add', function () {
        var html = $( '.vvic_attr_line.add_new' ).html();
        html = '<div class="vvic_attr_line add_new">' + html + '</div>';
        $( html ).insertBefore( '.vvic_attr_save_cont' );
    } );

    // add sku ending codes map
    $( document ).on( 'click', '.vvic_sku_add', function () {
        var html = $( '.vvic_sku_line.add_new' ).html();
        html = '<div class="vvic_sku_line add_new">' + html + '</div>';
        $( html ).insertBefore( '.vvic_sku_save_cont' );
    } );

    // add chart attribute map
    $( document ).on( 'click', '.vvic_param_add', function () {
        var html = $( '.vvic_chart_param_line.add_new' ).html();
        html = '<div class="vvic_chart_param_line add_new">' + html + '</div>';
        $( html ).insertBefore( '.vvic_chart_param_save_cont' );
    } );

    // add chart replacement map
    $( document ).on( 'click', '.vvic_repl_param_add', function () {
        var html = $( '.vvic_repl_param_line.add_new' ).html();
        html = '<div class="vvic_repl_param_line add_new">' + html + '</div>';
        $( html ).insertBefore( '.vvic_chart_repl_param_save_cont' );
    } );
    
    // *************************
    // REMOVE DATA MAPPING PAIR
    // *************************

    // remove attribute map
    $( document ).on( 'click', '.vvic_attr_rem', function () {
        $( this ).parents( '.vvic_attr_line' ).remove();
    } );

    // remove sku ending codes map
    $( document ).on( 'click', '.vvic_sku_rem', function () {
        $( this ).parents( '.vvic_sku_line' ).remove();
    } );

    // remove chart param map
    $( document ).on( 'click', '.vvic_param_rem', function () {
        $( this ).parents( '.vvic_chart_param_line' ).remove();
    } );

    // remove chart replacement map
    $( document ).on( 'click', '.vvic_repl_param_rem', function () {
        $( this ).parents( '.vvic_repl_param_line' ).remove();
    } );

    // *************
    // PROCESS CSV
    // *************
    $( '#vvic_process_csv' ).on( 'click', function () {

        // vars
        var target_file = $( this ).attr( 'data-target-file' ),
                nonce = $( this ).attr( 'data-nonce' );

        var data = {
            '_ajax_nonce': nonce,
            'action': 'vvic_process_csv',
            'target_file': target_file,
        }
        
//        console.log( data );
        
        $.post( ajaxurl, data, function ( response ) {
            console.log( response );
            if ( response.import_scheduled === 'yes' ) {
                window.alert( 'VVIC Product Import successfully scheduled and will begin processing soon.' );
                location.reload();
            }
        } );

    } );

    // **********************************************
    // DELETE PREVIOUSLY PROCESSED/UPLOADED CSV FILE
    // **********************************************
    $( '#vvic_delete_csv' ).on( 'click', function ( e ) {

        e.preventDefault();

        var del_nonce = $( this ).attr( 'data-nonce' );

        var data = {
            '_ajax_nonce': del_nonce,
            'action': 'vvic_process_csv',
            'delete_csv': true
        }

        $.post( ajaxurl, data, function ( response ) {
            if ( response === 'csv file records deleted') {
                window.alert( 'CSV file records successfully deleted.');
                location.reload();
            }
        } );

    } );
} );

