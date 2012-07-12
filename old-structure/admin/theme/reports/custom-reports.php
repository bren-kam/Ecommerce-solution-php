<?php
/**
 * @page Reports
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user || $user['role'] < 7 || ( $user['role'] < 8 && '1' != $user['company_id'] ) )
	login();

$xls = false;
$custom_reports = array(
    'all-accounts' => _('All Accounts')
);

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'custom-report' ) ) {
    $xls = true;
    $r = new Reports();
    $report = $r->custom_report( $_POST['sCustomReport'] );

    if ( count( $report ) > 0 ) {
        $nice_name = 'Custom Report - ' . $custom_reports[$_POST['sCustomReport']];
        // Have to include special folder
        library( 'PHPExcel/IOFactory' );

        $pe = new PHPExcel();

        $pe->getProperties()->setCreator('Website Manager')
            ->setLastModifiedBy('Website Manager')
            ->setTitle( $nice_name )
            ->setSubject('Website Manager / ' . $nice_name )
            ->setDescription('All of the data regarding the report')
            ->setKeywords('custom report Website Manager')
            ->setCategory('Website Manager Custom Report');

        // Get the columns
        $columns = array_keys( $report[0] );

        // Setup basic information
        $sheet = $pe->setActiveSheetIndex(0);
        $letter = $last_letter = 'A';

        foreach ( $columns as $name ) {
            // Set the sheet cell value
            $sheet->setCellValue( $letter . '1', $name );

            // Set the last column
            $last_letter = $letter;

            $letter++;
        }

        $sheet->getStyle('A1:' . $last_letter . '1')->getFont()->setBold( true );

        // Start showing each of the items
        $row_number = 3;

        foreach ( $report as $row ) {
            $letter = 'A';

            foreach ( $row as $key => $value ) {
                $sheet->setCellValue( $letter . $row_number, $value );
                $letter++;
            }

            $row_number++;
        }

        $sheet->getStyle('A3:' . $last_letter . $row_number)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $nice_name . '.xls"' );
        header('Pragma: no-cache');
        header('Cache-Control: max-age=0');
        header('Expires: 0');

        $peWriter = PHPExcel_IOFactory::createWriter( $pe, 'Excel5' );
        $peWriter->save('php://output');
        exit;
    }
}

css( 'form', 'reports/list' );
javascript( 'jquery', 'jquery.tmp-val', 'reports/list' );

$selected = 'custom-reports';
$title = _('Custom Reports') . ' | ' . TITLE;
get_header();
?>

<div id="content">
    <h1><?php echo _('Custom Reports'); ?></h1>
    <br clear="all" /><br />
    <?php get_sidebar( 'reports/' ); ?>
    <div id="subcontent">
        <form name="fCustomReport" action="" method="post">
            <table>
                <tr>
                    <td><label for="sCustomReport">Custom Report:</label></td>
                    <td>
                        <select name="sCustomReport" id="sCustomReport">
                            <option value="">-- <?php echo _('Select a Report'); ?> --</option>
                            <option value="all-accounts">All Accounts</option>
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo _('Download Report'); ?>" /></td>
                </tr>
            </table>
            <?php nonce::field('custom-report'); ?>
        </form>
    </div>
    <br /><br />
</div>

<?php get_footer(); ?>