<?php
/**
 * @page Cron Jobs run Daily
 * @package Imagine Retailer
 */

// Set it as a background job
newrelic_background_job();

$t = new Tickets;
$t->email_overdue_tickets();

/***** CRAIGSLIT *****/

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