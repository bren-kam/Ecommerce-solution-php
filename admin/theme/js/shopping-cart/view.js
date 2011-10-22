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
	$("#cbSameShipping").click( function( e ){
		var checked = $( this ).attr( "checked" );
		var fields = Array( 'FirstName', 'LastName', 'Address', 'Address2', 'City', 'Zip' );
		if ( checked ) {
			for ( field in fields ) {
				var value = $( "#tBilling" + fields[field] ).val();
				$("#tShipping" + fields[field]).val( value );
			}
			$("#sShippingState").val( $("#sBillingState").val() );
		} else {
			for ( field in fields ) {
				$("#tShipping" + fields[field]).val( '' );
			}
			$("#sShippingState").val( '---' );
		}
	});
}