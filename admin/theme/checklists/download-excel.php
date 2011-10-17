<?php
// Have to include special folder
library( 'PHPExcel/IOFactory' );

$checklists = new Checklists;

// Download the checklist
$c = $checklists->get( $_GET['cid'] );
$items = $checklists->get_checklist_items( $_GET['cid'], true );

$pe = new PHPExcel();

$pe->getProperties()->setCreator("Website Manager")
	->setLastModifiedBy("Website Manager")
	->setTitle("Checklist - " . $c['title'])
	->setSubject("Website Manager Checklist - " . $c['title'])
	->setDescription("All of the data regarding the checklist for " . $c['title'])
	->setKeywords("checklist Website Manager")
	->setCategory("Website Manager checklist");

// Setup basic information
$pe->setActiveSheetIndex(0)
	->setCellValue( 'A1', $c['website_title'] )
	->setCellValue( 'C1', 'Date Signed Up: ' . date( 'Y-m-d', $c['date_created'] ) )
	->setTitle( format::limit_chars( $c['title'] . ' Checklist', 27, '...' ) )
	->getStyle('A1')->getFont()->setBold( true );

// Start showing each of the items
$row = 3;
foreach ( $items as $section_title => $section_items ) {
	// Make the title of each section bold
	$pe->getActiveSheet()
		->setCellValue( 'A' . $row, $section_title )
		->getStyle('A' . $row)->getFont()->setBold( true );
	
	$row++;
	
	foreach ( $section_items as $si ) {
		// Add all the checklist items
		$pe->getActiveSheet()
			->setCellValue( 'A' . $row, $si['name'] . ' (' . $si['assigned_to'] . ')' )
			->setCellValue( 'B' . $row, ( $si['checked'] ) ? 'Yes' : '' );
		
		// Determine the messages
		$messages = new PHPExcel_RichText( $pe->getActiveSheet()->getCell('C' . $row) );
		
		// Add all the messages for each checklist item
		if ( isset( $si['messages'] ) && count( $si['messages'] ) > 0 )
		switch ( $si['messages'] as $m ) {
			$contact_name = $messages->createTextRun( $m['contact_name'] );
			$contact_name->getFont()->setBold( true );
			
			$messages->createText( ' ' . date( 'F jS, Y g:i a', $m['date_created'] ) . ': ' . $m['note'] . "\r\n" );
		}
		
		$row++;
	}
	
	$row++;
}

// Auto Adjust Width
$pe->getActiveSheet()->getColumnDimension('A')->setWidth(60);
$pe->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$pe->getActiveSheet()->getColumnDimension('C')->setWidth(60);
$pe->getActiveSheet()->getStyle('A3:C100')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $c['title'] . '.xls' );
header('Pragma: no-cache');
header('Cache-Control: max-age=0');
header('Expires: 0');

$peWriter = PHPExcel_IOFactory::createWriter( $pe, 'Excel5' );
$peWriter->save('php://output');
?>