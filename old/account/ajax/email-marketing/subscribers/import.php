<?php
/**
 * @page Import Subscribers
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'import-subscribers' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

// Get the file extension
$file_extension = strtolower( f::extension( $_FILES["Filedata"]['name'] ) );

// See what we're dealing with
switch ( $file_extension ) {
	case 'xls':
		// Load excel reader
		library('Excel_Reader/Excel_Reader');
		$er = new Excel_Reader();
		// Set the basics and then read in the rows
		$er->setOutputEncoding('ASCII');
		$er->read( $_FILES['Filedata']['tmp_name'] );
		
		$rows = $er->sheets[0]['cells'];
		$index = 1;
	break;
	
	case 'csv':
		// Make sure it's opened properly
		$ajax->ok( $handle = fopen( $_FILES['Filedata']['tmp_name'], "r"), _('An error occurred while trying to read your file.') );
		
		// Loop through the rows
		while( $row = fgetcsv( $handle ) ) {
			$rows[] = $row;
		}
		// Close the file
		fclose( $handle );
		$index = 0;
	break;
	
	default:
		// Display an error
		$ajax->ok( false, _('Only CSV and Excel file types are accepted. File type: ') . $extension );
	break;
}

// If there was something to import
$ajax->ok( is_array( $rows ), _('There were no emails to import') );

// Loop through emails
foreach ( $rows as $r ) {
	// Determine the column being used for name or email
	if ( !isset( $email_column ) || !isset( $name_column ) )
	if ( stristr( $r[0 + $index], 'name' ) && stristr( $r[1 + $index], 'email' ) ) {
		$email_column = 1 + $index;
		$name_column =  0 + $index;
		continue;
	} else {
		$email_column = 0 + $index;
		$name_column = 1 + $index;
		
		if ( stristr( $r[0 + $index], 'email' ) && stristr( $r[1 + $index], 'name' ) )
			continue;
	}
	
	// If there is an invalid email, skip it
	if ( empty( $r[$email_column] ) || 0 == preg_match( "/^([a-zA-Z0-9_\\-\\.]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.)|(([a-zA-Z0-9\\-]+\\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\\]?)\$/", $r[$email_column] ) )
		continue;
	
	// Create emails
	$emails[] = array( 'email' => $r[$email_column], 'name' => ( isset( $r[$name_column] ) ? $r[$name_column] : '' ) );
}

// Instantiate email marketing class
$e = new Email_Marketing();

// Instantiate website id!
global $user;
$user['website']['website_id'] = (int)$_POST['wid'];

// Temporarily import the emails
$e->import_emails( $emails );

// Set variables
$last_ten_emails = array_slice( $emails, 0, 10 );
$email_html = '';

// Create HTML
foreach ( $last_ten_emails as $e ) {
	$email_html .= '<tr><td>' . $e['email'] . '</td><td>' . $e['name'] . '</td></tr>';
}

// Assign it to the table
jQuery('#tUploadedSubcribers')->append( $email_html );

// Hide the main view
jQuery('#dDefault')->hide();

// Show the next table
jQuery('#dUploadedSubscribers')->show();

// Add the response
$ajax->add_response( 'jquery', jQuery::getResponse() );

// Send response
$ajax->respond();