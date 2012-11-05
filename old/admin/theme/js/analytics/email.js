// When the page has loaded
jQuery(function($) {
	// Make it possible to switch screens
	$('.email-screen').click( function() {
		$('.stat-screen.selected').fadeOut( 'fast' ).removeClass('selected');
		
		var div = $('#d' + $(this).attr('id').replace( /^a/, '' ) );
		setTimeout( function() {
			div.fadeIn().addClass('selected');
			
			// Handle Click Overlay
			if ( 'undefined' == typeof( click_overlay ) && 'dClickOverlay' == div.attr('id') )
			setTimeout( function() {
				var contents = $('#ifClickOverlay').contents();
				
				// Apply the click overlay
				var b = $('body:first', contents);
				$('#ifClickOverlay').animate( { height: b.height() + 50 }, 1000 );
				
				var click_stats = JSON.parse( $('#dClickStats').html() );
				var total_clicks = parseInt( $('#sTotalClicks').text() );
				var cs = new Array();
				
				for ( var i in click_stats ) {
					cs[removeMailChimpQueryString(i)] =  click_stats[i]['clicks'];
				}
				
				$('a', contents).each( function() {
					var href = $(this).attr('href');
					if ( '#' == href )
						return;
					
					var pos = $(this).offset();  
					var width = $(this).width();
					var clicks = ( 'undefined' == typeof( cs[href] ) ) ? 0 : cs[href];
					
					b.append( '<div style="position:absolute;left:' + parseInt( pos['left'] + width ) + 'px;top: ' + parseInt( pos['top'] - 3 ) + 'px;background-color:#fff;padding: 2px 5px; border:2px solid #929292;font-size:18px;color:#FFA900;font-weight:bold">' + Math.round( ( clicks / total_clicks ) * 100 ) + '%</div>' ); //'
				});
				
				click_overlay = true;
			}, 550 );
		}, 250 );
	});
});


function removeMailChimpQueryString( url ) {
	return url.replace( /utm_source=[^&]+&amp;utm_campaign=[^&]+&amp;utm_medium=[^&]+/, '' ).replace( /\?$/, '' );
}