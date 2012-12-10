// On load
jQuery(function(){
    var slug = $('#tSlug');

	// Make it update the slug automatically
	$('#tTitle').keyup( function() {
		if ( 'undefined' == typeof( slug.attr('extra') ) || '' == slug.attr('extra') )
			slug.val( $(this).val().slug() );
	});
	
	// Make the slug not be able to changed once you've changed it
	slug.keyup( function() {
		// Adjust what they're typing in
		var value = $(this).val().replace( ' ', '-').slug();
		
		// Set the extra value and the value
		$(this).attr( 'extra', ( '' == value ) ? '' : 1 ).val( value );
	});
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase();};