// When the page has loaded
jQuery(function($) {
	// Show the actions (View | Edit | Delete)
	$('.tr-page').live( 'mouseover', function() {
		$('#sViewActions' + $(this).attr('id').replace( 'trPage', '' ) ).show();
	}).live( 'mouseout', function() {
		$('#sViewActions' + $(this).attr('id').replace( 'trPage', '' ) ).hide();
	});
	
	// The delete product functionality
	$('.delete-page').live( 'click', function() {
		var page_id = $(this).attr('id').replace( 'aDeletePage', '' );
 
		// Make sure they want to delete it
		if ( confirm( 'Are you sure you want to delete the page ' + $(this).attr('title').replace( 'Delete ', '' ) + '?' ) ) {
			$.post( '/pages/delete/', { 'pid': page_id, '_nonce' : $('#_nonce').val() }, function( json ) {
				if ( true == json ) {
					$('#trPage' + page_id).remove();
					
					// Update the table
					update_sortable_table( $('#tListPages') );
				} else {
					alert( 'An error occurred while trying to delete your page. Please refresh the page and try again.');
				}
			}, 'json' );
		}
	});
		
	$.tablesorter.defaults.widgets = ['zebra'];
	// Make it a a table
	$("#tListPages").tablesorter( { widthFixed: true, sortList: [[0,1]] } );

	// Enable the eidt note functionality
	$('.edit-note').live( 'click', function(){
		var note_id = $(this).parents('div.dNote:first').attr('id').replace( 'dNote_', '' );
		var dnote = $('#dNote_' + note_id + ' .note');
		var dnote_html = dnote.html();
		if ( dnote.hasClass( 'editing' ) ) return false;
		else
		{
			dnote.html( '<br /><a href="#" class="update-note" >Update</a> | <a href="#" class="cancel-edit" >Cancel</a>' );
			dnote.prepend( '<textarea id="taEditNote_' + note_id + '" rows="3" cols="42">' + dnote_html + '</textarea><div class="hidden cancel-text">' + dnote_html +'</div>' );
			dnote.addClass( 'editing' );
		}
	});
	
	// Enable the update note functionality
	$('.update-note').live( 'click', function(){
		var user_id = parseInt( $("#dCurrentUserId").attr("name") );
		var note_id = $(this).parents('div.dNote:first').attr('id').replace( 'dNote_', '' );
		$.post( '/websites/update_website_note/', {
				nonce: $('#_update_note_nonce').val(), 
				'note_id' : note_id,
				'user_id' : user_id,
				'note_text' : $( '#taEditNote_' + note_id ).val()
				}, 
			function( data ){
				if ( data.success ) {
					$('#dNote_' + note_id + ' .note' ).html( $( '#taEditNote_' + note_id ).val() );
					$('#dNote_' + note_id + ' .note' ).removeClass( 'editing' );
				} else {
					alert ( data.error );
				}
			}, 'json'  );
	} );

	$('.delete-note').live( 'click', function(){
		// Make sure they want to delete it
		if ( confirm( 'Are you sure you want to delete this note?' ) ) {
			var user_id = parseInt( $("#dCurrentUserId").attr("name") );
			var note_id = $(this).parents('div.dNote:first').attr('id').replace( 'dNote_', '' );
			$.post( '/websites/delete_website_note/', {
					nonce: $('#_delete_note_nonce').val(), 
					'note_id' : note_id,
					'user_id' : user_id
					}, 
				function( data ){
					if ( data.success ) {
						$('#dNote_' + note_id ).remove();//html( $( '#taEditNote_' + note_id ).val() );
					} else {
						alert ( data.error );
					}
				}, 'json'  );
		}
	} );
	
	// Enable the cancle editing functionality
	$('.cancel-edit').live( 'click', function(){
		var note_id = $(this).parents('div.dNote:first').attr('id').replace( 'dNote_', '' );		  
		var dnote = $( '#dNote_' + note_id + ' .note' );
		dnote.html( dnote.find('.cancel-text').html() );
		dnote.removeClass( 'editing' );
	});
});

/**
 * Updates a sortable table's look after content is changed
 */
function update_sortable_table( table ) {
	// Let the tablesorter know you just changed the table
	table.trigger('update');

	// Sorting as applied above (NEEDS to be defined like this)
	var sorting = [[0,1],[1,1]];

	// Sort on the first column -- ALSO UPDATES PAGER!
	table.trigger("sorton", [sorting]);
}