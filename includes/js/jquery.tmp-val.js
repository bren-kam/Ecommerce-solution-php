jQuery.fn.tmpVal = function() {
	// See if they added color arguments
	if ( arguments.length > 0 ) {
		var temp_color = arguments[0];
		var standard_color = arguments[1];
	} else {
		var temp_color = '';
		var standard_color = '';
	}

	$(this).each( function() {
		var standard_value = ( $(this).attr('title').length > 0 ) ? $(this).attr('title') : $(this).val();
	
		// Set it to that color now
		if ( temp_color.length > 0)
			$(this).css( 'color', temp_color );

		// Make sure it's not already there
		if ( typeof $(this).attr('tmpVal') == 'undefined' )
			$(this).attr( 'tmpVal', standard_value );

		$(this).focus(function() {
			if ( $(this).val() == $(this).attr('tmpVal') )
				$(this).val('');
				
				// If there is color, set it now
				if ( temp_color.length > 0 )
					$(this).css( 'color', standard_color );
		});

		$(this).blur(function() {
			if ( $(this).val().length == 0 || $(this).val() == $(this).attr('tmpVal') ) 
				$(this).val( $(this).attr('tmpVal') );

				// If there is color, set it now
				if ( temp_color.length > 0 && $(this).val() == $(this).attr('tmpVal') )
					$(this).css( 'color', temp_color );
		});
		
		/*
		switch ( $(this).attr('type') ) {
			case 'password':
			case 'text':
				var standard_value = ( $(this).attr('title').length > 0 ) ? $(this).attr('title') : $(this).val();
	
				// Set it to that color now
				if ( temp_color.length > 0)
					$(this).css( 'color', temp_color );
	
				// Make sure it's not already there
				if ( typeof $(this).attr('tmpVal') == 'undefined' )
					$(this).attr( 'tmpVal', standard_value );
	
				$(this).focus(function() {
					if ( $(this).val() == $(this).attr('tmpVal') )
						$(this).val('');
						
						// If there is color, set it now
						if ( temp_color.length > 0 )
							$(this).css( 'color', standard_color );
				});
	
				$(this).blur(function() {
					if ( $(this).val().length == 0 || $(this).val() == $(this).attr('tmpVal') ) 
						$(this).val( $(this).attr('tmpVal') );
	
						// If there is color, set it now
						if ( temp_color.length > 0 && $(this).val() == $(this).attr('tmpVal') )
							$(this).css( 'color', temp_color );
				});
				break;
	
			default: break;
		}*/
	});
}