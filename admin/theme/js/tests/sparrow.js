/**
 * Sparrow : An arbitrary name for common code across all pages on the website
 * @type test
 * @depency jquery
 */

module('Sparrow');

// Add in data to the qunit fixture to test
$('#qunit-fixture').append( '<input type="text" id="test-tmpval" tmpval="Hello World" value="" /> <table id="tDataTable" perPage="100,250,500" ajax="/ajax/test/datatables/"><thead><tr><th>column one</th><th sort="3">column two</th><th>column three</th><th sort="1">column four</th><th sort="2">column five</th></tr></thead><tbody></tbody></table> <a id="aDialog" href="/dialogs/test/" rel="dialog">test</a> <a id="aAJAX" href="/ajax/test/" ajax="1">test</a>' );

// After it has loaded, do the tests
head.js( '/js2/?f=sparrow', function() {
	// Test Temporary Values
	test( "Temporary Values", function() {
		// Set the expentency for number of tests
		expect(9);
		
		// Get the test field
		var tmpval = $('#test-tmpval');
		
		// Make sure the field exists
		ok( tmpval.length, 'Temporary Value Field Exists' );
		
		// Make sure the value now equals temporary value
		equal( tmpval.val(), tmpval.attr('tmpval'), 'Value should be: ' + tmpval.attr('tmpval') );
		
		// Make sure it has the 'tmpval' class
		ok( tmpval.hasClass('tmpval'), "Field should have 'tmpval' class" );
		
		// Focus on the input which should trigger a change
		tmpval.focus();
		
		// It should now be blank
		equal( tmpval.val().length, 0, "Value should be blank" );
		
		// It should not have the 'tmpval' class
		ok( !tmpval.hasClass('tmpval'), "Field should not have 'tmpval' class" );
		
		// Blur it
		tmpval.blur();
		
		// Make sure the value now equals temporary value
		equal( tmpval.val(), tmpval.attr('tmpval'), 'Value should be: ' + tmpval.attr('tmpval') );
		
		// Make sure it has the 'tmpval' class
		ok( tmpval.hasClass('tmpval'), "Field should have 'tmpval' class" );
		
		// Focus it and set the value to the alternate value
		tmpval.focus().val( tmpval.attr('tmpval') ).blur();
		
		// Make sure the value now equals temporary value
		equal( tmpval.val(), tmpval.attr('tmpval'), 'Value should be: ' + tmpval.attr('tmpval') );
		
		// Make sure it has the 'tmpval' class
		ok( tmpval.hasClass('tmpval'), "Field should have 'tmpval' class" );
	});
	
	// Test turning tables into datatables
	test( "Tables > DataTables", function() {
		// Set the expectency for number of tests
		expect(6);
		
		var table = $('#tDataTable');
		
		// Make sure the field exists
		ok( table.length, 'Table Exists' );
		
		// Make sure the class was added
		ok( table.hasClass('dt'), 'DataTables was added successfully' );
		
		// It should have 5 rows
		equal( $('tbody tr', table).length, 5, 'DataTables should have 5 rows' );
		
		// Last tr shuold have a "last" class
		ok( $('tr:last', table).hasClass('last'), 'Last tr should have a "last" class' );
		
		// <div class="top"> should exist
		ok( $('div.top').length, "div.top should exist" );
		
		// Bottom should exist
		ok( $('div.bottom').length, "div.bottom should exist" );
	});
	
	// Test dialog boxes
	test( 'Dialog Boxes', function() {
		// Set the expectency for number of tests
		expect(3);
		
		var dialog = $('#aDialog');
		
		// Anchor should exist
		ok( dialog.length, 'Anchor should exist' );
		
		// It's click should be a function
		equal( typeof( dialog.click ), 'function', "Dialog's click should be a function" );
		
		// Boxy should be defined
		ok( 'undefined' != typeof( Boxy ), 'Boxy should be defined' );
	});
	
	// Test AJAX anchors
	test( "AJAX anchors", function() {
		// Set the expectency for number of tests
		expect(2);
		
		var ajaxAnchor = $('#aAJAX');
		
		// Anchor should exist
		ok( ajaxAnchor.length, 'Anchor should exist' );
		
		// It's click should be a function
		equal( typeof( ajaxAnchor.click ), 'function', "Anchor's click should be a function" );
	});
});