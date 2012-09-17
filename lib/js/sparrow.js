/**
 * Sparrow : An arbitrary name for common code across all pages on the website
 *
 * @objective Standardize many of the functions that would require custom javascript and/or css while remaining under 1 kb.
 * @version 1.0.0
 * @depency jquery
 */

// We need a context
var sparrow = function(context) {
	// Temporary Values
	$('input[tmpval],textarea[tmpval]', context).each( function() {
		/**
		 * Sequence of actions:
		 *		1) Set the value to the temporary value (needed for page refreshes
		 *		2) Add the 'tmpval' class which will change it's color
		 * 		3) Set the focus function to empty the value under the right conditions and remove the 'tmpval' class
		 *		4) Set the blur function to fill the value with the temporary value and add the 'tmpval' class
		 */
		$(this).focus( function() {
			// If the value is equal to the temporary value when they focus, empty it
			if( $(this).val() == $(this).attr('tmpval') )
				$(this).val('').removeClass('tmpval');
		}).blur( function() {
			// Set the variables so they don't have to be grabbed twice
			var value = $(this).val(), tmpValue = $(this).attr('tmpval');
			
			// Fill in with the temporary value if it's empty or if it matches the temporary value
			if( 0 == value.length || value == tmpValue ) 
				$(this).val( tmpValue ).addClass('tmpval');
		});
		
		// If there is no value, set it to the correct value
		if( !$(this).val().length )
			$(this).val( $(this).attr('tmpval') ).addClass('tmpval');
	});
	
	// Make datatables
	var tables = $('table[ajax],table.dt', context);
	
	// If there are tables, load datatables plugin and load the content
	if( tables.length )
	head.js( '/resources/js_single/?f=jquery.datatables', function() {
		// Make each table
		tables.addClass('dt').each( function() {
			// Define variables and add on image to th's
			var aPerPage = $(this).attr('perPage').split(','), opts = '', ths = $(this).find('th').append('<img src="/images/trans.gif" width="9" height="8" />'), sorting = new Array(), columns = new Array(), s = '', c = '', a = $(this).attr('ajax');
			
			// Form options
			for( var i in aPerPage ) {
				opts += '<option value="' + aPerPage[i] + '">' + aPerPage[i] + '</option>';
			}
			
			if( ths.length ) {
				// Create sorting array
				for( var i = 0; i < ths.length; i++ ) {
					if( s = $(ths[i]).attr('sort') ) {
						var direction = ( -1 == s.search('desc') ) ? 'asc' : 'desc';
						sorting[s.replace( ' ' + direction, '' ) - 1] = [i, direction];
					}

					if( c = $(ths[i]).attr('column') ) {
						columns.push( { 'sType' : c } );
					} else { 
						columns.push( null );
					}
				}
			} else {
				// If they don't choose anything, do the first one
				sorting = [[0,'asc']];
			}
			
			var settings = {
				bAutoWidth: false,
				iDisplayLength : parseInt( aPerPage[0] ),
				oLanguage: {
						sLengthMenu: '<select>' + opts + '</select>',
						sInfo: "_START_ - _END_ of _TOTAL_"
				},
				aaSorting: sorting,
				aoColumns: columns,
				fnDrawCallback : function() {
					// Run Sparrow on new content and add the class last to the last row
					sparrow( $(this).find('tr:last').addClass('last').end() );
				},
				sDom : '<"top"lfr>t<"bottom"pi>'
			};
			
			// If it's AJAX
			if( a )
				settings.bProcessing = 1, settings.bServerSide = 1, settings.sAjaxSource = a;
			
			// Make the dataTable
			$(this).dataTable(settings);
		});
	});
	
	// Make dialogs
	var dialogs = $('a[rel=dialog]', context);
	
	if( dialogs.length )
	head.js( '/resources/js_single/?f=jquery.boxy', function() {
		// Make dialogs
		dialogs.click( function(e) {
			// Prevent it from going anywhere
			e.preventDefault();
			
			var dialogData = $(this).attr('href').split('#'), content = $('#' + dialogData[1]), settings = {
				title : $(this).attr('title'),
				behaviours : sparrow
			};
			
			// If exists, and they want to cache it use it
			if( content.length && '0' != $(this).attr('cache') ) {
				new Boxy( content, settings );
			} else {
                // Make sure we remove previous content
                if ( content.length )
                    content.remove();

				// If it doesn't exist, add it to the page, load the AJAX ontent, run sparrow, then create it
				$('body').append('<div id="' + dialogData[1] + '" class="dialog" />');
				content = $('#' + dialogData[1]);
				content.load( dialogData[0], function() {
					new Boxy( content, settings ); // Do we want to call Sparrow first, maybe  few milisecnds faster?
				});
			} 
		});
	});
	
	// Make anchors support AJAX calls
	$('a[ajax]', context).click( function( e ) {
		// Prevent the click
		e.preventDefault();
		
		// Should have another way to do confirm boxes from dialogs
		var confirmQuestion = $(this).attr('confirm');
		
		if( confirmQuestion && !confirm( confirmQuestion ) )
			return

		$.get( $(this).attr('href'), ajaxResponse, 'json' );
	}).removeAttr('ajax'); // Prevent it from getting called again

    var RTEs = $('textarea[rte]', context);

	// If there are RTEs
	if ( RTEs.length )
	head.js( '/ckeditor/ckeditor.js', '/ckeditor/adapters/jquery.js', function() {
		RTEs.ckeditor({
			autoGrow_minHeight : 100,
			resize_minHeight: 100,
			height: 100,
			toolbar : [
				['Bold', 'Italic', 'Underline'],
				['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				['NumberedList','BulletedList', 'Table'],
				['Format'],
				['Link','Unlink'],
				['Source']
			]
		});
	});

	// Get any forms that might need to be ajaxed
	var ajaxForms = $('form[ajax]', context);

	// If there are forms, load AJAX form plugin and PHP plugin
	if ( ajaxForms.length )
	head.js( '/resources/js_single/?f=jquery.form', function() {
		// Assign all the forms the AJAX form plugin
		ajaxForms.ajaxForm({
			dataType	: 'json'
            , success	: ajaxResponse
		});
	});
}

// Run Sparrow on some context
$.fn.sparrow = function( context ) {
    sparrow( $(this) );

    return $(this);
}

// After it has loaded
head.ready( function() {
	// Run sparrow
	sparrow( $('body') );
});

// Create a function to handle ajax responses
function ajaxResponse( response ) {
	// Test for success
	if( response['success'] ) {
		// Assign it to PHP function to handle the jquery if it exists
		if( response['refresh'] ) {
			// Refresh the page
			window.location = window.location;
		} else if ( 'object' == typeof( response['jquery'] ) ) {
			head.js( '/resources/js_single/?f=jquery.php', function() {
				// Interpret jQuery
				php( response['jquery'] );
			});
		}
	} else {
		// Handle Errors
		if( response['error'] )
			alert( response['error'] );
	}
}