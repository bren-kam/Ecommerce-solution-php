<?php
$a = new Analytics;

// Get the date from today
$date = new DateTime();

$a->add_craigslist_stats( $date->format( 'Y-m-d' ) );