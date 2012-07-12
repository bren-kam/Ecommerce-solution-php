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
	$('.remove-zip').live( 'click', function(){
		var blah = $( this ).attr( 'id' );
		blah = blah.replace( 'aRemoveZip', '' );
		$( '#trZip' + blah ).detach();
	});
	
	$('#aNewZipCode').live( 'click', function() {
		var zip = $("#tNewZipCode").val();
		if ( zip ) {
			var html = '<tr id="trZip' + zip + '"><td>' + zip + '</td><td><a href="#" id="aRemoveZip' + zip + '" class="remove-zip"><img src="/images/icons/x.png" width="15" height="17" alt="Delete"/></a><input type="hidden" name="hZip' + zip + '" value="' + zip + '"/></td></tr>';
			$("#trAddZipCode").before( html );
			$("#tNewZipCode").val( '' );
		}
	});
}