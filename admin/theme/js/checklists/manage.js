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

    // Make new items
    $('.add-section-item').live( 'click', function() {
        // Create new item
        var sectionID = $(this).attr('id').replace( 'aAddSectionItem', '' );

        $('#dSectionItems' + sectionID).append('<div class="item"><input type="text" name="items[' + sectionID + '][][description]" class="tb item-description" value="" /> <input type="text" name="items[' + sectionID + '][][assigned_to]" class="tb item-assigned-to" value="" /></div>' );
    });
}