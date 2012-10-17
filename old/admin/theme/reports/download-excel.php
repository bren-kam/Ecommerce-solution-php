<?php
// Have to include special folder
library( 'PHPExcel/IOFactory' );

$w = new Websites;

// Download the checklist
$websites = $w->report();

$pe = new PHPExcel();

$pe->getProperties()->setCreator('Website Manager')
	->setLastModifiedBy('Website Manager')
	->setTitle('Website Report')
	->setSubject('Website Manager Website Report' )
	->setDescription('All of the data regarding the website report')
	->setKeywords('report Website Manager')
	->setCategory('Website Manager Website Report');

// Setup basic information
$pe->setActiveSheetIndex(0)
	->setCellValue( 'A1', 'Title' )
	->setCellValue( 'B1', 'Company' )
	->setCellValue( 'C1', 'Products' )
	->setCellValue( 'D1', 'Signed Up' )
	->getStyle('A1:D1')->getFont()->setBold( true );

// Start showing each of the items
$row = 3;
foreach ( $websites as $w ) {
	// Make the title of each section bold
	$pe->getActiveSheet()
		->setCellValue( 'A' . $row, $w['title'] )
		->setCellValue( 'B' . $row, $w['company'] )
		->setCellValue( 'C' . $row, $w['products'] )
		->setCellValue( 'D' . $row, $w['date_created'] );
	
	$row++;
}

// Auto Adjust Width
$pe->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$pe->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$pe->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$pe->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$pe->getActiveSheet()->getStyle('A3:D200')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Website Report.xls"' );
header('Pragma: no-cache');
header('Cache-Control: max-age=0');
header('Expires: 0');

$peWriter = PHPExcel_IOFactory::createWriter( $pe, 'Excel5' );
$peWriter->save('php://output');
?>