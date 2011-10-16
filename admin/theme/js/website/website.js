// On load
jQuery(function(){
	var elSlug = $('.slug:first');
	
	// Make it update the slug automatically
	$('.slug-title:first').keyup( function() {
		if( 'undefined' == typeof( elSlug.attr('extra') ) || '' == elSlug.attr('extra') ){
			elSlug.val( slug( $(this).val() ) );
		}
	});
	
	// Make the slug not be able to changed once you've changed it
	elSlug.keyup( function() {
		// Adjust what they're typing in
		var value = slug( $(this).val().replace( ' ', '-' ) );
		
		// Set the extra value and the value
		$(this).attr( 'extra', ( '' == value ) ? '' : 1 ).val( value );
	});
});

// Turns text into a slug
function slug(string) { return string.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }