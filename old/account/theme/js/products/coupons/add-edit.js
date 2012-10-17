head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', 'jquery.form', function() {
	// Date Picker
	$('input.date').datepicker({
		dateFormat: 'yy-mm-dd'
	})
});