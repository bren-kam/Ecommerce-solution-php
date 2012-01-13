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
		placeholder	: 'section-placeholder',
		forcePlaceholderSize : true
	});

    // Make the section items sortable
    $('.section').sortable({
		items		: '.item',
		cancel		: 'input',
		placeholder	: 'item-placeholder',
		forcePlaceholderSize : true
	});

    // Make new sections
    $('#aAddSection').click( function() {
        $(this).before('<div class="section"><input type="text" name="sections[]" class="section-title" tmpval="Section title..." /><br /><div class="section-items"><a href="javascript:;" class="add-section-item" title="Add Item">Add Item</a><br /><br /></div></div>' ).tmpval();
    });

    // Make new items
    $('.add-section-item').live( 'click', function() {
        // Create new item
        var sectionID = ( 'undefined' == typeof( $(this).attr('id') ) ) ? '' : $(this).attr('id').replace( 'aAddSectionItem', '' );

        $(this).before('<div class="item"><input type="text" name="items[' + sectionID + '][][description]" class="tb item-description" tmpval="Description..." value="" /> <input type="text" name="items[' + sectionID + '][][assigned_to]" class="tb item-assigned-to" tmpval="Assigned to..." value="" /></div>' ).tmpval();
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