head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', 'http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js', '/resources/js_single/?f=jquery.boxy', function() {
	// Cache
	var cache = { sku : {}, product : {}, brand : {} };
	
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
			if( request['term'] in cache[cacheType] ) {
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
			$.post( '/products/custom-products/autocomplete/', { _nonce : $('#_autocomplete').val(), 'type' : cacheType, 'term' : request['term'] }, function( autocompleteResponse ) {
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
        $('#tViewProducts').dataTable().fnDraw();
    });

    setTimeout( function() {
        $('#tViewProducts').addClass('dt').dataTable({
                aaSorting: [[0,'asc']],
                bAutoWidth: false,
                bProcessing : 1,
                bServerSide : 1,
                iDisplayLength : 20,
                sAjaxSource : '/products/custom-products/list-products/',
                sDom : '<"top"lr>t<"bottom"pi>',
                oLanguage: {
                    sLengthMenu: 'Rows: <select><option value="20">20</option><option value="50">50</option><option value="100">100</option></select>'
                    , sInfo: "_START_ - _END_ of _TOTAL_"
                    , oPaginate: {
                        sNext : ''
                        , sPrevious : ''
                    }
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
                }
            });
    }, 500 );
});

/*
 * Load Products related to specific category slug
 */
function loadProducts() {
	// Define variables
	var categoryID = $('#sCategory').val(), tAutoComplete = $('#tAutoComplete'), autoComplete = tAutoComplete.val(), hCurrentPage = $("#hCurrentPage"), sProductsPerPage = $('#sProductsPerPage'), cbOnlyDiscontinued = ( $('#cbOnlyDiscontinued').is(':checked') ) ? '1' : '0';
    var itemsPerPage = parseInt( ( sProductsPerPage.length ) ? sProductsPerPage.val() : 20 );
	
	// If we're not supposed to refresh, do stuff
	if ( parseInt( $("#doNotRefresh").val() ) > 0 )
		var currentPage = ( !( hCurrentPage.val() ) ) ? 1 : hCurrentPage.val();
	
	// If current page isn't set, set to default
	if( !currentPage )
		currentPage = 1;
	
	// Enable / disable sortability
	if( categoryID.length && !autoComplete.length || autoComplete == tAutoComplete.attr('tmpval') ) {
		$("#dProductList").sortable('enable');
	} else {
		$("#dProductList").sortable('disable');
	}
	
	// Get the products
	$.post( '/products/search/', { cid : categoryID, s : $('#sAutoComplete').val(), v : autoComplete, n : itemsPerPage, p : currentPage, od : cbOnlyDiscontinued }, function( html ) { //trigger this on success
		// Load the content
		$('#dProductList').html( html ).sparrow();
		
		// Fix their images ASAP!
		adjustImageSizes();
		
		// @Fix is this needed? Does it make sense -- setting the value of a droppdown?
		// Purposefully grabbing again -- may not have existed before
		$('#sProductsPerPage').val( itemsPerPage );
		
		// Give it the functions
		$("#sProductsPerPage").change( loadProducts );
		$("#tListRequests_previous").click( previousPage );
		$("#tListRequests_next").click( nextPage );
	}, 'html' );
}