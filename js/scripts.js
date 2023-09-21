$( document ).ready( ( ) => {

    $( '#calc-form' ).on( 'submit', function( evt ){

        evt.preventDefault();
        $('#results').html('');
        let fd   = new FormData( this );
        let data = Object.fromEntries( fd.entries() );
        $.post( {
            url     : 'action.php',
            data    : data,
            dataType: 'json',
            success : ( data ) => {
                data.forEach( ( item, ind ) => {
                    $('#results').append( `<div class="${ ind % 2 ? `even`: `odd` }">${JSON.stringify( item )}</div>` );
                } );
            },
            error   : ( data ) => {
                $('#results').append( `<div class="error">Упс..</div>` );
            },
        } );

    } );

} );