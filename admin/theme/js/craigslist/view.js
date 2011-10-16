// Used to initialize the table for first time.
var table_initialized = true;

// When the page has loaded
jQuery(function($) {
	// Constantly updated query array
	query_array = new Array();
				
	// Create tmp values
	$('#tAutoComplete').tmpVal( '#929292', '#000000' );
	
	// Create autocomplete
	ac = $('#tAutoComplete').autocomplete( {
		serviceUrl: '/products/autocomplete/',
		width: 300,
		params: {
			_nonce: $('#_ajax_autocomplete_nonce').val(),
			type: 'sku'
		}
	} );
	
	$('#sAutoComplete').change( function() {
		switch( $(this).val() ) {
			case 'category_id':
				ac.setOptions( {
					width: 300,
					params: {
						_nonce: $('#_ajax_autocomplete_nonce').val(),
						type: 'sku'
					},
					onSelect: null
				} );

			default:
				break;
		}

		$('#tAutoComplete').attr( 'tmpval', new_value ).val( new_value ).css( 'color', '#929292' );
	});
	
	// Create the search functionality
	$('#aSearch').click( function() {
		load_craigslist( $('#sAutoComplete').val(), $('#tAutoComplete').val() );
	});

	// Create the crumb changing ability
	$('a.crumb').live( 'click', function() {
		var current_type = $(this).attr('id').replace('bc_', '');
		var current_value = $(this).attr('name');
		var reset_values = false;

		for( i in query_array ) {
			if( !reset_values && ( i == current_type || 'all-craigslist' == current_type ) ) {
				reset_values = true;
				if( 'all-craigslist' == current_type ) {
					$('#tAutoComplete').val( $('#tAutoComplete').attr('tmpval') ).css( 'color', '#929292' );
					load_all_craigslist_templates()
				} else {
					//if( 'all-products' != current_type )
					continue;
				}
			}

			if( reset_values ) {
				delete( query_array[i] );
			}

			var last_value = i;
		}

		if( Object.size( query_array ) != 0 && last_value != 'category' ) {
			load_craigslist( current_type, current_value );
		} else {
			// If you want to display all products, remove the next 3 lines
			$('#dListCraigslist').hide();
			$('#dMsgNoCraigslist').show();
			update_breadcrumb();
		}
	});
	
	$('#aResetSearch').click( function() {
		$('#tAutoComplete').val( $('#tAutoComplete').attr('tmpval') ).css( 'color', '#929292' );
		load_all_craigslist();
	});


	//initialize the table
	TableToolsInit.sSwfPath = "/media/flash/ZeroClipboard.swf";		
	view_craigslist = $('#tViewCraigslist').dataTable({
		//"bJQueryUI" : true,
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/craigslist/list_craigslist/',
		'iDisplayLength' : 100,
		"oLanguage": {
			"sLengthMenu": 'Rows: <select><option value="100">100</option><option value="250">250</option><option value="500">500</option></select>',
			"sInfo": "Records: _START_ - _END_ of _TOTAL_"
		},
		"aaSorting": [[0, 'asc']],
		"sDom" : '<"top"Tlr>t<"bottom"pi>'
	});
	
	// Show the actions (View | Edit | Delete)
	$('#tViewCraigslist tr').live( 'mouseover', function() {
		$(this).find('span.hidden:first').show();
	}).live( 'mouseout', function() {
		$(this).find('span.hidden:first').hide();
	});

	// The delete ad functionality
	$('.delete-craigslist-template').live( 'click', function() {
		var craigslist_template_id = parseInt( $(this).attr('id').replace( 'aViewDelete', '' ) );
	
		// Make sure they want to delete it
		if( confirm( 'Are you sure you want to delete the template? ' + $(this).attr('title').replace( 'Delete ', '' ) + '?' ) ) {
			$.post( '/craigslist/delete/', {
				_nonce: $('#_ajax_delete_craigslist_nonce').val(),
				caid: parseInt( craigslist_template_id )
			}, function( json ) {
				if( true == json ) {
					//$('#trViewCraigslist' + craigslist_template_id).remove();
					var table = view_craigslist;		
					var row = "#sViewCraigslistAdActions" + craigslist_template_id;
					//row = $( row );
					alert( row );
					//table.fnDeleteRow( row );
					
					// Let the tablesorter know you just changed the table
					table.trigger('update');
					
					// Sorting as applied above (NEEDS to be defined like this)
					var sorting = [[0,0],[1,1]];
				
					// Sort on the first column -- ALSO UPDATES PAGER!
					table.trigger("sorton", [sorting]);
				} else {
					alert( 'An error occurred while trying to delete your product. Please refresh the page and try again.');
				}
			}, 'json' );
		}
	});
	
	// The clone ad functionality
	$('.clone-craigslist-template').clone( 'click', function() {
		var craigslist_template_id = parseInt( $(this).attr('id').replace( 'aViewClone', '' ) );
	
		// Make sure they want to delete it
		if( confirm( 'Are you sure you want to delete the template ' + $(this).attr('title').replace( 'Delete ', '' ) + '?' ) ) {
			$.post( '/craigslist/delete/', {
				_nonce: $('#_ajax_delete_craigslist_nonce').val(),
				caid: parseInt( craigslist_template_id )
			}, function( json ) {
				if( true == json ) {
					$('#trViewCraigslist' + craigslist_template_id).remove();
					var table = $('#tViewCraigslist');
					
					// Let the tablesorter know you just changed the table
					table.trigger('update');
				
					// Sorting as applied above (NEEDS to be defined like this)
					var sorting = [[0,0],[1,1]];
				
					// Sort on the first column -- ALSO UPDATES PAGER!
					table.trigger("sorton", [sorting]);
				} else {
					alert( 'An error occurred while trying to delete your product. Please refresh the page and try again.');
				}
			}, 'json' );
		}
	});

	// If they change what's being viewed, refresh the current view
	$('#sVisibility').change( function() {
		load_craigslist( 'visibility', $('#sVisibility').val() );
		//load_all_products()
	} );
	
	// If they change what's being viewed, refresh the current view
	$('#sCraigslistStatus, #sUsers').change( function() {
		load_craigslist( 'user', $('#sCraigslistStatus').val() + '|' + $('#sUsers').val() );
	} );
});

/*
 * Function load all craigslist templates
 * while AllProducts is clicked
 * Reset Search is clicked
 */
function load_all_craigslist() {
	$.ajax( { //ajax request startingd
		url: '/craigslist/get_craigslist/',
		cache: true,
		data : {
			'nonce' : $('#_ajax_get_craigslist_nonce').val(),
			'visibility' : $('#sVisibility option:selected').val()
		},
		type: "POST",//request is a POSt request
		dataType: "json",//expect json as return
		success: function( result ) { //trigger this on success
			if( result['success'] ) {
				if( 0 == result['craigslist_count'] ) {
					$('#dListCraigslist').hide();
					$('#dMsgNoCraigslist').show();
				} else {
					view_craigslist.fnDraw();
					$('#dListCraigslist').show();
					$('#dMsgNoCraigslist').hide();	
				}
			} else {
				//alert( 'An error occurred while fetching your product(s). Please refresh the page and try again.' );
			}
		}
	});
}

function load_craigslist( criteria, searchString ) {
	$.post( { //ajax request startingd
		url: '/craigslist/get_craigslist/',
		cache: true,
		data : {
			'nonce' : $('#_ajax_get_craigslist_nonce').val(),
			'category_id' : urlencode( parseInt( searchString ) )
		},
		dataType: "json",//expect json as return
		success: function( result ) { //trigger this on success
			if( result['success'] ) {
				if( 0 == result['craigslist_count'] ) {
					$('#dListCraigslist').hide();
					$('#dMsgNoCraigslist').show();
				} else {
					view_craigslist.fnDraw();
					$('#dMsgNoCraigslist').hide();
					$('#dListCraigslist').show();
				}
			} else {
				alert( 'An error occurred while fetching your ad(s). Please refresh the page and try again.' );
			}
		}
	});
}

jQuery.fn.stripe = function(className) {
	$(this).find('.' + className + ':even').removeClass('even odd').addClass('odd').end().find('.' + className + ':odd').removeClass('even odd').addClass('even');
}

/**
 * Update breadcrumb for narrowing your search
 */
function update_breadcrumb() {
	var length = Object.size( query_array );
	var place = 1;

	var breadcrumb = ( length > 0 ) ? '<a href="#" id="all-craigslist-templates" class="crumb" title="All Craigslist Templates">All Craigslist Templates</a>' : 'All Craigslist Templates';

	for( i in query_array ) {
		switch( i ) {
			case 'category':
				if( 'brd_undefined' != query_array[i] ) {
					if( typeof( categories[query_array[i]] ) == 'undefined' ) {
						var cat_name = $('#cat_' + query_array[i]).text();
						categories[query_array[i]] = cat_name;
					} else {
						var cat_name = categories[query_array[i]];
					}
					breadcrumb += ( place == length ) ? ' &gt; Category: ' + cat_name : ' &gt; <a href="#" id="bc_' + i + '" class="crumb" name="' + query_array[i] + '" title="' + cat_name + '">Category: ' + cat_name + '</a>';
				}
				break;

			case 'sku':
				breadcrumb += ( place == length ) ? ' &gt; SKU: ' + query_array[i] : ' &gt; <a href="#" id="bc_' + i + '" class="crumb" name="' + query_array[i] + '" title="' + query_array[i] + '">SKU: ' + query_array[i] + '</a>';
				break;

			case 'products':
				breadcrumb += ( place == length ) ? ' &gt; Product Name: ' + query_array[i] : ' &gt; <a href="#" id="bc_' + i + '" class="crumb" name="' + query_array[i] + '" title="' + query_array[i] + '">Product Name: ' + query_array[i] + '</a>';
				break;

			case 'brand':
				breadcrumb += ( place == length ) ? ' &gt; Brand: ' + query_array[i] : ' &gt; <a href="#" id="bc_' + i + '" class="crumb" name="' + query_array[i] + '" title="' + query_array[i] + '">Brand: ' + query_array[i] + '</a>';
				break;
			
			case 'brands':
				breadcrumb += ( place == length ) ? ' &gt; Brands: ' + query_array[i] : ' &gt; <a href="#" id="bc_' + i + '" class="crumb" name="' + query_array[i] + '" title="' + query_array[i] + '">Brands: ' + query_array[i] + '</a>';
				break;

			default:
				break;
		}
		place++;
	}

	$('#pNarrowSearchBreadCrumb').html( breadcrumb );
}

function urlencode (str) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer
    // +      input by: Ratheous
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Joris
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // %          note 1: This reflects PHP 5.3/6.0+ behavior
    // %        note 2: Please be aware that this function expects to encode into UTF-8 encoded strings, as found on
    // %        note 2: pages served as UTF-8
    // *     example 1: urlencode('Kevin van Zonneveld!');
    // *     returns 1: 'Kevin+van+Zonneveld%21'
    // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
    // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'

    str = (str+'').toString();
    
    // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
    // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
                                                                    replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}
