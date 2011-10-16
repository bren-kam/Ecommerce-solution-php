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
	$( '#aAddNewZip' ).click( function() {
		var zip = $( '#tAddNewZip' ).val();
		if( ( zip.length < 5 ) || ( zip == 'New Zip...' ) ) {
			alert( "Please enter a valid zip code." );
			return false;
		} else {
			var html = '<span id="sZip' + zip + '"><input type="text" class="tb" maxlength="5" name="tZip[]" value="' + zip + '"/> <a href="#" id="aDeleteZip' + zip + '" class="delete-zip"><img width="15" height="17" alt="Delete" src="/images/icons/x.png" /></a><br/></span>';
			$('#tAddNewZip').before( html );
			$('#tAddNewZip').val( '' );
		}
	});
	
	$( '.delete-zip' ).live( 'click', function() {
		var zip = $( this ).attr( 'id' );
		zip = zip.replace( 'aDeleteZip', '' );
		$( '#sZip' + zip ).remove();
	});
}