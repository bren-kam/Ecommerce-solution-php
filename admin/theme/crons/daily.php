<?php
/**
 * @page Cron Jobs run Daily
 * @package Grey Suit Retail
 */

// Set it as a background job
newrelic_background_job();

/***** CRAIGLIST *****/

// Declare classes
$a = new Analytics();
$c = new Craigslist;

// Determine date range
$date = new DateTime();
$date->sub( new DateInterval('P1D') );

// Update the stats
$a->update_craigslist_stats( $date->format('Y-m-d') );

// Update the tags for analytics of products
$c->update_tags();
?>