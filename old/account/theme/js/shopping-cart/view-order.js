/**
 * Craigslist - Add/Edit Page
 */

// var editorHTML = CKEDITOR.instances.taDescription.getData();

// When the page has loaded
jQuery( postLoad );

/**
 * postLoad
 *
 * Initial load of the page
 *
 * @param $ (jQuery shortcut)
 */
function postLoad( $ ) {
	$("#sStatus").change( function( e ){
		// alert( $( this ).val() );
		var status = $( this ).val();
		var website_order_id = $( "#hOrderID" ).val();
		$.post(  '/ajax/shopping-cart/update-order-status/', 
		   { 
		   'nonce' : $('#_nonce').val(),
		   'status' : status,
		   'website_order_id' : website_order_id
		   },
		   function( result ) {
				if( result['success'] ) {
				} else {
					alert( result['message'] );
				}
			}, 
			'json' );
	});
	
	$(".expand-options").live( 'click', function(){
		var id = ( $( this ).attr( 'id' ) ).replace( 'aExpandOptions', '' );
		$( '#dOptions' + id ).slideDown();
		$( '#aExpandOptions' + id ).removeClass('expand-options').addClass('hide-options');
		$( '#sExpandOptions' + id ).html( '[ - ]' );
		return false;
	});
	
	$(".hide-options").live( 'click', function(){
		var id = ( $( this ).attr( 'id' ) ).replace( 'aExpandOptions', '' );
		$( '#dOptions' + id ).slideUp();
		$( '#aExpandOptions' + id ).removeClass('hide-options').addClass('expand-options');
		$( '#sExpandOptions' + id ).html( '[ + ]' );
		return false;
	});	
}