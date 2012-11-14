// When the page has loaded
jQuery(function($) {
	if ( $('body').hasClass('apps') )
		$('body').attr( 'app', document.body.className.replace('apps ', '') );
	
	// Set up tabs
	$('.fb-tab').click( function() {
		tab_holder_id = $(this).attr('id').replace( 'aTab', '' ).toLowerCase();	
		
		$('body').removeClass();
		$('body').addClass( tab_holder_id );
		
		if( tab_holder_id == 'apps' )
			$('body').addClass( $('body').attr( 'app' ) );
		
		// Hide other tabs
		$('.fb-tab-wrapper').hide();			
		
		// Show the tab
		$('#' + tab_holder_id).show();
	});
});