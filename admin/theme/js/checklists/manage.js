/**
 * Checklists View Page
 */

// When the page has loaded
jQuery( postLoad );

/**
 * postLoad
 *
 * Initial load of the page
 *
 * @param $ (jQuery shortcut)
 */
function postLoad( $ ) {
    // Make the sections sortable
	$('#checklist-sections').sortable({
		items		: '.section',
		cancel		: 'input',
        cursor      : 'move',
		placeholder	: 'section-placeholder',
		forcePlaceholderSize : true
	});

    // Make the section items sortable
    $('.section').sortable({
		items		: '.item',
		cancel		: 'input',
        cursor      : 'move',
		placeholder	: 'item-placeholder',
		forcePlaceholderSize : true
	});

    // Make new sections
    $('#aAddSection').click( function() {
        var a = $(this);

        $.post( '/ajax/checklists/manage/create-section/', { _nonce : $('#_ajax_create_section').val() }, function ( response ) {
            // Make sure there was no error
            if ( !response['result'] ) {
                alert( response['error'] );
                return false;
            }

            a.before('<div class="section"><input type="text" name="sections[' + response['result'] + ']" class="section-title" tmpval="Section title..." /> <a href="javascript:;" class="remove-section hidden" title="Remove Section"><img src="/images/icons/x.png" width="15" height="17" alt="Remove Section" /></a><br /><div class="section-items"><a href="javascript:;" class="add-section-item" id="aAddSectionItem' + response['result'] + '" title="Add Item">Add Item</a><br /><br /></div></div>' ).tmpval();
        }, 'json' );
    });

    // Make new items
    $('.add-section-item').live( 'click', function() {
        // Create new item
        var a = $(this), sectionID = $(this).attr('id').replace( 'aAddSectionItem', '' );

        $.post( '/ajax/checklists/manage/create-item/', { _nonce : $('#_ajax_create_item').val(), sid : sectionID }, function ( response ) {
            // Make sure there was no error
            if ( !response['result'] ) {
                alert( response['error'] );
                return false;
            }

            a.before('<div class="item"><input type="text" name="items[' + sectionID + '][' + response['result'] + '][name]" class="item-name" tmpval="Description..." value="" /> <input type="text" name="items[' + sectionID + '][' + response['result'] + '][assigned_to]" class="tb item-assigned-to" tmpval="Assigned to..." value="" /> <a href="javascript:;" class="remove-item hidden" title="Remove Item"><img src="/images/icons/x.png" width="15" height="17" alt="Remove Item" /></a></div>' ).tmpval();
        }, 'json' );
    });

    // Remove items
    $('.remove-item').live( 'click', function() {
        if ( !confirm( 'Are you sure you want to remove this item? This cannot be undone.' ) )
            return false;

        $(this).parent().remove();
    });

    // Remove Section
    $('.remove-section').live( 'click', function() {
        if ( $(this).parent().find('div.section-items:first .item:first').is('div') ) {
            alert('Please remove all items before deleting a section');
            return;
        }

        if ( !confirm( 'Are you sure you want to remove this section? This cannot be undone.' ) )
            return false;
        
        $(this).parent().remove();
    });
}

// Temporary Values
$.fn.tmpval = function( context ) {
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
}