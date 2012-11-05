/**
 * View Order
 */

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
	$("#sStatus").change( function( e ) {
		$.post(  '/ajax/shopping-cart/orders/update-status/', { _nonce : $('#_nonce').val(), s : $(this).val(), woid : $( "#hOrderID" ).val() }, ajaxResponse, 'json' );
	});
	
	// Expand options
	$(".expand-options").live( 'click', function(){
		var id = $(this).attr( 'id' ).replace( 'aExpandOptions', '' );
		$('#dOptions' + id ).slideDown();
		$('#aExpandOptions' + id ).removeClass('expand-options').addClass('hide-options');
		$('#sExpandOptions' + id ).html( '[ - ]' );
		return false;
	});
	
	// Hide Options
	$(".hide-options").live( 'click', function(){
		var id = $( this ).attr( 'id' ).replace( 'aExpandOptions', '' );
		$( '#dOptions' + id ).slideUp();
		$( '#aExpandOptions' + id ).removeClass('hide-options').addClass('expand-options');
		$( '#sExpandOptions' + id ).html( '[ + ]' );
		return false;
	});	
}