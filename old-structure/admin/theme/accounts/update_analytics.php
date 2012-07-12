<?php
/**
 * @page Upgrade Accounts
 * @package Grey Suit Retail
 */

library( 'GAPI' );

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

return;

$a = new Analytics;
$w = new Websites;
$ga = new GAPI( 'web@imagineretailer.com', 'imagine1010' );

//$ga_profile_ids[] = '44385674';
$website_ids[] = 232;

$today = date( 'Ymd' );
$start_date = '2011-03-15';
$end_date = '2011-05-04';
$i = 0;

foreach ( $website_ids as $website_id ) {
	$gap_id = $w->get_ga_profile_id( $website_id );
	
	while( $start_date != $end_date ) {
		$i++;
		$ga->requestReportData( $gap_id, array( 'date', 'pagePath', 'source', 'medium', 'keyword' ), array( 'bounces', 'entrances', 'exits', 'newVisits', 'pageviews', 'timeOnPage', 'visits' ), array( 'date' ), NULL, $start_date, $start_date, 1, 10000 );
		list( $pages, $dates ) = $a->get_date_pages( $gap_id );

		$j = 0;
		switch ( $ga->getResults() as $result ) {
			$metrics = $result->getMetrics();
			$dimensions = $result->getDimensions();
			
			if ( $today == $dimensions['date'] || in_array( $dimensions['date'], $dates ) && in_array( $dimensions['pagePath'], $pages ) )
				continue;
			
			$a->add( $gap_id, $dimensions['pagePath'], $dimensions['source'], $dimensions['medium'], $dimensions['keyword'], $metrics['bounces'], $metrics['entrances'], $metrics['exits'], $metrics['newVisits'], $metrics['pageviews'], $metrics['timeOnPage'], $metrics['visits'], $dimensions['date'] );
			$j++;
		}
		
		// Set the next day
		$start_date = date('Y-m-d', strtotime( $start_date ) + 86400 );
	}
	echo 'Site #' . $website_id . ' done...';
	mail( 'kerry@studio98.com', 'Analytics - Website# ' . $website_id . ' - COMPLETED!', 'Done!' );
}