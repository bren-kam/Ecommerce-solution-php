head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', 'http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js', '/resources/js_single/?f=jquery.form', function() {
	// Date Picker
	$('#tDate').datepicker({
		minDate: 0,
		dateFormat: 'yy-mm-dd'
	});
	
	// @Fix - Shouldn't have to hide this
	$('#ui-datepicker-div').hide();
	
	// Time Picker
    var tTime = $('#tTime');
	tTime.timepicker({
	  	step: 60
		, show24Hours: false
        , timeFormat: 'g:i a'
	}).timepicker('show');

    // Fix for offset
    tTime.timepicker('hide');

	// The next button
	$('.step, .next, .previous').click( function() {
		selectTab( $(this).attr('id').replace( /[^0-9]/g, '' ) );
	});
	
	// Step 2 - Choosing
	$('a.choose').click( function() {
		var div = $('#d' + $(this).attr('id').replace( /^a/, '' ) ), type = $(this).attr('title').replace( ' Email', '' ).toLowerCase(), h2 = $('#dStep2 h2:first');
		
		$('#dChooseType').fadeOut();
		h2.fadeOut();
		
		setTimeout( function() {
			div.fadeIn();
			h2.text( 'Choose Template' ).fadeIn();
		}, 300 );
		
		$('#hEmailType').val( type );
	});

	// Choose Template function
	$('.choose-template').click( function() {
		$('#hEmailTemplateID').val( $(this).prev().attr('id').replace( 'iTemplateImage', '' ) );
		$('.custom-template').hide();
		$('#dCustom_' + $('#hEmailType').val() ).show();
		selectTab( 3 );
	});
	
	// Make the prices update
	$('#dSelectedProducts').on( 'keyup', '.product-price, .product-box-price', function() {
		var hiddenProduct = $(this).parents('.product:first').find('input:last');
		
		hiddenProduct.val( hiddenProduct.val().replace( /\|[0-9]*/, '|' + parseFloat( $(this).val() ) ) );
	}).on( 'click', '.remove-product', function() {
		$(this).parents('.product').remove();
	});
	
	// Change the text
	$('#sAutoComplete').change( function() {
		var tAutoComplete = $('#tAutoComplete');
		
		tAutoComplete.attr( 'tmpval', tAutoComplete.attr('tmpval').replace( /\s([\w\s]+).../, ' ' + $(this).find('option:selected').text() + '...' ) ).val('').blur();
	});
	
	$('#tAutoComplete').autocomplete({
		minLength: 1,
		source: function( request, response ) {
			// Get the cache type
			var cacheType = $('#sAutoComplete').val();
			
			// Find out if they are already cached so we don't have to do another ajax called
			if ( request['term'] in cache[cacheType] ) {
				response( $.map( cache[cacheType][request['term']], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['name']
					}
				}) );
				
				// If it was cached, return now
				return;
			}
			
			// It was not cached, get data
			$.post( '/products/autocomplete-owned/', { '_nonce' : $('#_autocomplete_owned').val(), 'type' : cacheType, 'term' : request['term'], owned : 1 }, function( autocompleteResponse ) {
				// Assign global cache the response data
				cache[cacheType][request['term']] = autocompleteResponse['suggestions'];
				
				// Return the response data
				response( $.map( autocompleteResponse['suggestions'], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['name']
					}
				}));
			}, 'json' );
		}
	});
	
	// Create the search functionality
	$('#aSearch').click( function() {
		$('#tAddProducts').dataTable().fnDraw();
	});
	
	// Make the list sortable
	$("#dSelectedProducts").sortable( {
		update:function () {
			resortProducts();
		},
		scroll:true,
		placeholder:'product-placeholder'
	});
	
	// Save functionality
	$('.save').click( saveForm );
	
	// AJAX form, saves as well
	$('#fSendEmail').ajaxForm({
		beforeSubmit:  function() {
			if ( !saveForm() )
				return false;
		},  // pre-submit callback
		success:       saveFormSuccess,
		dataType: 'json'
	});
	
	// Send test link
	$('#aSendTest').click( function() {
		if ( $(this).text().search( /\+/ ) > 0 ) {
			// Expand
			$('#dSendTest').show();
			
			$(this).text( 'Send Test [ - ]' );
		} else {
			// Contract
			$('#dSendTest').hide();
			
			$(this).text( 'Send Test [ + ]' );
		}
	});
	
	// Send test funtionality
	$('#bSendTest').click( function() {
		var tTestEmail = $('#tTestEmail'), email = tTestEmail.val();
		
		if ( 0 == email.length || email == tTestEmail.attr('tmpval') || null == email.match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/) ) {
			alert( $(this).attr('error') );
			$('#tTestEmail').focus();
			return false;
		}
		
		// Save the form
		saveForm( testMessage );
	});
	
	// Send email
	$('#aSendEmail').click( function() {
		if ( 0 == $('#hEmailMessageID').val() ) {
			alert( $(this).attr('error') );
			return false;
		}
		
		saveForm( sendScheduleEmail );
	});
	
	// Check all button
	$('#aCheckAll').click( function() {
		$('input.mailing-list').each( function() {
			if ( !$(this).attr( 'disabled' ) )
				$(this).attr( 'checked', true );
		});
	});

	// Uncheck all button
	$('#aUncheckAll').click( function() {
		$('input.mailing-list').attr( 'checked', false );
	});
});

// Cache
cache = { 'offer-box' : {}, 'sku' : {}, 'product' : {}, 'brand' : {} };

/**
 * Resort products
 */
function resortProducts() {
	var hiddenProducts = '';
	
	// Remove other products
	$('input.hidden-product').remove();
	
	$('.product').each( function() {
		var productID = $(this).attr('id').replace( 'dProduct_', '' );
		var price = $('#dProduct_' + productID).find('input:first').val();
		hiddenProducts += '<input type="hidden" name="products[]" class="hidden-product" id="hProduct' + productID + '" value="' + productID + '|' + price + '" />';
	});
	
	$('#fSendEmail').append( hiddenProducts );
}

/**
 * Test Message
 */
function testMessage() {
	$.blockUI({ timeout: 0, message: '<h1><img src="/images/icons/ajax-loading.gif" alt="Sending Test Email..." width="28" height="28" /><br />Sending test email.<br />This may take 1-2 minutes...</h1>' }); 
	
	$.post( '/email-marketing/emails/test/', { _nonce : $('#_test').val(), 'email' : $('#tTestEmail').val(), emid : $('#hEmailMessageID').val() }, function( response ) {
		$.unblockUI();
		ajaxResponse( response );
	}, 'json' );
}

/**
 * Send Schedule Email
 */
function sendScheduleEmail() {
	$.blockUI({ timeout: 0, message: '<h1><img src="/images/icons/ajax-loading.gif" alt="Sending Email..." width="28" height="28" /><br />Sending/scheduling your email.<br />This may take 1-2 minutes...</h1>' }); 
	
	$.post( '/email-marketing/emails/schedule/', { _nonce: $('#_schedule').val(), emid: $('#hEmailMessageID').val() }, function( response ) {
		// Unblock
		$.unblockUI();
		
		// Handle any errors
		if ( !response['success'] ) {
			alert( response['error'] );
			return;
		}
		
		// Redirect
		document.location = '/email-marketing/emails/';
	}, 'json' );
}

/**
 * Saves the form
 *
 * @return bool
 */
function saveForm( saveFormCallback ) {
	if ( !$('#dStep1 .cb:checked:first').length ) {
		alert( 'You must select a Mailing List first' );
		return false;
	}
	
	$.blockUI({ timeout: 0, message: '<h1><img src="/images/icons/ajax-loading.gif" alt="Saving..." width="28" height="28" /><br />Saving...</h1>' }); 
	resortProducts();
	
	if ( 'function' == typeof( saveFormCallback ) )
		globalCallback = saveFormCallback;

    $('#taMessage').val( CKEDITOR.instances.taMessage.getData() );

	$('#fSendEmail').ajaxSubmit({
		success: saveFormSuccess,
		dataType: 'json'
	});
	
	return true;
}

// Callback function after form has been saved
function saveFormSuccess( response ) {
	$.unblockUI();
	
	if ( response['success'] ) {
		$('#hEmailMessageID').val( response['email_message_id'] );
		
		if ( 'function' == typeof( globalCallback ) )
			globalCallback();
		
		globalCallback = null;
	} else {
		alert( response['error'] );
	}
}

// Select one of the tabs
function selectTab( tabID ) {
	var oldTab = $('#tab-top .tab.selected').removeClass('selected'), oldID = oldTab.attr('id').replace( 'h2Step', '' );

	// Reset the second step
	if ( 2 == tabID ) {
		$('#dChooseType').show();
		$('#dStep2 h2:first').text('Choose Email Type');
		$('.email-type').hide();
	} else if ( 3 == tabID ) {
		$('#tAddProducts:not(.dt)').addClass('dt').dataTable({
			aaSorting: [[0,'asc']],
			bAutoWidth: false,
			bProcessing : 1,
			bServerSide : 1,
			iDisplayLength : 20,
			sAjaxSource : '/email-marketing/emails/list-products/',
			sDom : '<"top"lr>t<"bottom"pi>',
			oLanguage: {
					sLengthMenu: 'Rows: <select><option value="20">20</option><option value="50">50</option><option value="100">100</option></select>',
					sInfo: "_START_ - _END_ of _TOTAL_"
			},
			fnDrawCallback : function() {
				// Run Sparrow on new content and add the class last to the last row
				sparrow( $(this).find('tr:last').addClass('last').end() );
			},
			fnServerData: function ( sSource, aoData, fnCallback ) {
				aoData.push({ name : 's', value : $('#tAutoComplete').val() });
				aoData.push({ name : 'sType', value : $('#sAutoComplete').val() });
				
				// Get the data
				$.ajax({
					url: sSource,
					dataType: 'json',
					data: aoData,
					success: fnCallback
				});
			},
		});
	}
	
	$('#dStep' + oldID).fadeOut('fast');
	
	$('#h2Step' + tabID).addClass('selected');
	
	setTimeout( function() {
		$('#dStep' + tabID).fadeIn();
	}, 250 );
}