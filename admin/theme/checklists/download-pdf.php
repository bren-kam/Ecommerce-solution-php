<?php
// Have to include special folder
library( 'tcpdf/config/lang/eng' );
library( 'tcpdf/tcpdf' );

$checklists = new Checklists;

// Download the checklist
$c = $checklists->get( $_GET['cid'] );
$items = $checklists->get_checklist_items( $_GET['cid'], true );

// create new PDF document
$pdf = new TCPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false ); 

// set document information
$pdf->SetCreator( PDF_CREATOR );
$pdf->SetAuthor( 'Website Manager' );
$pdf->SetTitle( 'Checklist - ' . $c['title'] );
$pdf->SetSubject( 'Website Manager Checklist - ' . $c['title'] );
$pdf->SetKeywords('checklist, Website Manager');

// set header and footer fonts
$pdf->setPrintHeader( false );
$pdf->setPrintFooter( false );

// set default monospaced font
$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

// set margins
$pdf->SetMargins( 8, 4, 16 );
$pdf->SetHeaderMargin( 0 );
$pdf->SetFooterMargin( 0 );

// set auto page breaks
$pdf->SetAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );

// set image scale factor
$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO ); 

//set some language-dependent strings
$pdf->setLanguageArray( $l ); 

// ---------------------------------------------------------

// add a page
$pdf->AddPage();

// Create title
$pdf->SetFont( 'helvetica', 'B', 30 );
$pdf->Cell( 0, 8, $c['title'], 0, 1 );

$pdf->SetFont( 'helvetica', 'B', 10 );
$pdf->Cell( 0, 2, 'Date Signed Up: ' . date( 'Y-m-d', $c['date_created'] ), 0, 0 );
$pdf->Cell( 0, 2, 'Days Left: ' . $c['days_left'], 0, 1, 'R' );

$pdf->Ln();

foreach ( $items as $section_title => $section_items ) {
	$pdf->SetFont( 'helvetica', 'B', 21 );
	$pdf->Cell( 0, 20, $section_title, 0, 1 );
	
	foreach ( $section_items as $si ) {
		$pdf->SetFont( 'helvetica', 'B', 13 );
		$pdf->Cell( 0, 2, $si['name'], 'B', 0 );
		
		// Do we need to make it red?
		if ( !$si['checked'] )
			$pdf->setTextColor( 255, 0, 0 );
		
		$pdf->Cell( 0, 2, ( $si['checked'] ) ? 'Yes' : 'NO', 'B', 1, 'R' );
		
		// Make the color black
		$pdf->setTextColor( 0, 0, 0 );

		if ( isset( $si['messages'] ) && count( $si['messages'] ) > 0 ) {
			// Set the font size
			$pdf->SetFont( 'helvetica', '', 10 );
			
			foreach ( $si['messages'] as $m ) {
				$pdf->WriteHTML( '<strong>' . $m['contact_name'] . '</strong> - ' . date( 'F jS, Y g:i a', $m['date_created'] ) . ': ' . $m['note'], 0, 0 );
				$pdf->Ln();
			}
		}
		$pdf->Ln();
	}
	
	$pdf->Ln();
}

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output( $c['title'] . '.pdf', 'I' );
?>