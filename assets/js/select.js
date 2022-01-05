jQuery( document ).ready( function ( $ ) {
    $( 'input#cb-select-all-1' ).on( 'click', function (  ) {
        if ( $( this ).is( ':checked' ) ) {
            $( 'tbody#the-list' ).find( 'input[type=checkbox]' ).trigger( 'click' );
        } else {
            $( 'tbody#the-list' ).find( 'input[type=checkbox]' ).trigger( 'click' );
        }
    } );
} );

