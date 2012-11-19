head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', 'jquery.form', function() {
	// Date Picker
	$('input.date').datepicker({
		dateFormat: 'yy-mm-dd'
	})
});