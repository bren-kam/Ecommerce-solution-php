<?php
/**
 * @page Cron Jobs run Weekly
 * @package Grey Suit Retail
 */

// Set it as a background job
newrelic_background_job();

$a = new Ashley_Feed();
$a->run_all();

$a = new Ashley();
$a->load_packages();
?>