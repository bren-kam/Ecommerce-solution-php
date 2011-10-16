/**
 * Test: Mammoth
 * @type test
 * @depency jquery.u
 */

module('Mammoth');

// Add in data to the qunit fixture to test
$('#qunit-fixture').append( '<form action="/ajax/test/" method="post" ajax="1" id="fAJAXTest"><input type="text" name="a" value="b" /></form>' );

// After it has loaded, do the tests
head.js( '/js2/?f=mammoth', function() {
	// Test turning forms into AJAX tests
	test( "AJAX Forms", function() {
		// Set the expectency for number of tests
		expect(4);
		
		var form = $('#fAJAXTest');
		
		// Make sure the field exists
		ok( form.length, 'Form Exists' );
		
		// Check to see if it added the submit function
		ok( 'function' == typeof( form.submit ), "AJAX Form Added" );
		
		// NOTE: Assuming AJAX Form works as requested
		
		// Make sure that it can translate the returned object into jQuery
		php({"a":[],"q":[{"s":"#fAJAXTest","m":["replaceWith"],"a":[["<h1 id='h1'>Hello World!<\/h1>"]]}]}); // Should replace the ajax form with "Hello World"
		
		// Make sure form no longer exists
		ok( !$('#fAJAXTest').length, 'Form no longer exists' );
		
		// Make sure the h1 does exist
		ok( $('#h1').length, 'H1 Exists' );
	});
});