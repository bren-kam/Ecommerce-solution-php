jQuery(function(){
	// Add Address functionality
	$('#aAddLocation').click( function() {
		if ( !validateAddress( $('#tPhone'), $('#tFax'), $('#tEmail'), $('#tWebsite'), $('#tZip') ) )
			return false;

		// Add on to the list
		$('#dContactUsList').append( '<div class="contact" id="dContact' + generateID() + '"><h2><span class="location">' + $('#tLocation').val() + '</span></h2><div class="contact-left"><span class="address">' + $('#tAddress').val() + '</span><br /><span class="city">' + $('#tCity').val() + '</span>, <span class="state">' + $('#sState option:selected').val() + '</span> <span class="zip">' + $('#tZip').val() + '</span></div><div class="contact-right"><span class="phone">' + $('#tPhone').val() + '</span><br /><span class="fax">' + $('#tFax').val() + '</span><br /></div><div style="float:right"><span class="email">' + $('#tEmail').val() + '</span><br /><span class="website">' + $('#tWebsite').val() + '</span></div><br /><br clear="all" /><br /><strong>Store Hours:</strong><br /><span class="store-hours">' + nl2br( $('#taStoreHours').val() ) + '</span><div class="actions"><a href="javascript:;" class="delete-address" title="Delete Address"><img src="/images/icons/x.png" width="15" height="17" alt="Delete Address" /></a><a href="javascript:;" class="edit-address" title="Edit Address"><img src="/images/icons/edit.png" width="15" height="17" alt="Edit Address" /></a></div></div>' );

		// Reset the form
		$('#tLocation, #tAddress, #tCity, #tZip, #tPhone, #tFax, #tEmail, #tWebsite, #taStoreHours').val('');
		$('#sState option:first').attr( 'selected', true );

		// Update the addresses
		updateAddresses();
	});
	
	// Multiple Location Map toggle
	$('#cbMultipleLocationMap').click( function() {
		$.post( '/website/set-pagemeta/', { _nonce: $('#_set_pagemeta').val(), k : 'mlm', v : $(this).is(':checked') ? true : false, apid : $('#hAccountPageId').val() }, ajaxResponse, 'json' );
	});
	
	// Hide All Maps toggle
	$('#cbHideAllMaps').click( function() {
		$.post( '/website/set-pagemeta/', { _nonce: $('#_set_pagemeta').val(), k : 'ham', v : $(this).is(':checked') ? true : false, apid : $('#hAccountPageId').val() }, ajaxResponse, 'json' );
	});

    // Submit a form
    $('#bAddEditProduct').click( function() {
        $('#fAddEditLocation').submit();
    });

    $('#add-location').click( function() {
        $('#fAddEditLocation')[0].reset();

        new Boxy( $('#dAddEditLocation'), {
            title : 'Add Location'
        });
    });

    // Make the click trigger the other one
    $('#bSubmit').click( function() {
        $('#bAddEditLocation').click();
    })
});