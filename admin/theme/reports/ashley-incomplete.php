<?php
// Have to include special folder
library( 'PHPExcel/IOFactory' );

$p = new Products();

// Download the checklist
$products = $p->ashley_incomplete_products();

$pe = new PHPExcel();

$pe->getProperties()->setCreator('Website Manager')
	->setLastModifiedBy('Website Manager')
	->setTitle('Website Report')
	->setSubject('Ashley - Incomplete Products' )
	->setDescription('All of the Ashley Feed products that are incomplete')
	->setKeywords('report ashley incomplete products')
	->setCategory('Website Manager ashley incomplete products');

// Setup basic information
$pe->setActiveSheetIndex(0)
	->setCellValue( 'A1', 'SKU' )
	->setCellValue( 'B1', 'Link' )
	->setCellValue( 'C1', 'Private' )
    ->setCellValue( 'D1', 'Categories' )
    ->setCellValue( 'E1', 'Attributes' )
    ->setCellValue( 'F1', 'Product Images' )
	->getStyle('A1:F1')->getFont()->setBold( true );

// Start showing each of the items
$row = 3;
foreach ( $products as $p ) {
	// Make the title of each section bold
	$pe->getActiveSheet()
		->setCellValue( 'A' . $row, $w['sku'] )
		->setCellValue( 'B' . $row, $w['link'] )
		->setCellValue( 'C' . $row, $w['private'] )
		->setCellValue( 'D' . $row, $w['categories'] )
        ->setCellValue( 'E' . $row, $w['attributes'] )
        ->setCellValue( 'F' . $row, $w['product_images'] );

	$row++;
}

// Auto Adjust Width
$pe->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$pe->getActiveSheet()->getColumnDimension('B')->setWidth(90);
$pe->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$pe->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$pe->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$pe->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$pe->getActiveSheet()->getStyle('A3:D' . ( count( $products ) + 2 ) )->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Ashley - Incomplete Products.xls"' );
header('Pragma: no-cache');
header('Cache-Control: max-age=0');
header('Expires: 0');

$peWriter = PHPExcel_IOFactory::createWriter( $pe, 'Excel5' );
$peWriter->save('php://output');
?>