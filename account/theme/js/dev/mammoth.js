/**
 * Mammoth : An arbitrary name for slightly less common code, largely of which relies on jQuery UI
 *
 * @objective Standardize many of the functions that would generally require custom javascript and/or css while remaining under 5 kb.
 * @version 1.0.0
 * @depency jquery
 * @dependency jquery.ui
 */

// We need a context
var mammoth = function(context) {
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
	head.js( '/js2/?f=jquery.form', function() {
		// Assign all the forms the AJAX form plugin
		ajaxForms.ajaxForm({
			dataType	: 'json',
			success		: ajaxResponse
		});
	});
}