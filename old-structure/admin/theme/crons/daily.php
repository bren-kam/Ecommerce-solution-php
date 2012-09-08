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
$m = new Mobile_Marketing();
$s = new SiteOnTime();

// Determine date range
$date = new DateTime();
$date->sub( new DateInterval('P1D') );

// Update the stats
$a->update_craigslist_stats( $date->format('Y-m-d') );

// Update the tags for analytics of products
$c->update_tags();

// Synchronize Mobile Subscribers
$m->synchronize_contacts();

// Synchronize Site On Time Products
$s->run();
?>