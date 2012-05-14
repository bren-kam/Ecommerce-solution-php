// When the page has loaded
jQuery(function($) {
	// This makes it so that clicking on the link selects the whole thing
	$('#tCurrentLink').click( function() {
		$(this).select();
	});
	
    /********** Page Link  **********/
	// Trigger the check to make sure the slug is available
    $('#tTitle').change( function() {
		if ( $(this).attr('tmpval') == $(this).val() || '' == $(this).val().replace(/\s/g, '') ) {
			$('#dSlug, #pSlugError').hide();
			return;
		}

		// Get slugs
		var pageSlug = $(this).val().slug(), sSlug = $('#sSlug');

		// Makes sure it only changes the name when you first write the title
		if ( '' == sSlug.text() ) {
			// Assign the slugs
			sSlug.text( pageSlug );
			$('#tSlug').val( pageSlug );
		}

		// Show the text
		$('#dSlug').show();
	});

	// The "Edit" slug button
	$('#aEditSlug').click( function() {
		// Hide the slug
		$('#sSlug, #aEditSlug').hide();

		// Show the other buttons
		$('#tSlug, #aSaveSlug, #aCancelSlug').show();
	});

	// The "Save" slug button
	$('#aSaveSlug').click( function() {
		var productSlug = $('#tSlug').val().slug();

		// Assign the slugs
		$('#sSlug').text( productSlug );
		$('#tSlug').val( productSlug );

		// Hide the buttons
		$('#tSlug, #aSaveSlug, #aCancelSlug').hide();

		// Show the slug
		$('#sSlug, #aEditSlug').show();
	});

	// The "Cancel" slug link
	$('#aCancelSlug').click( function() {
		// Assign the slugs
		$('#tSlug').val( $('#sSlug').text() );

		// Hide the buttons
		$('#tSlug, #aSaveSlug, #aCancelSlug').hide();

		// Show the slug
		$('#sSlug, #aEditSlug').show();
	});
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }
