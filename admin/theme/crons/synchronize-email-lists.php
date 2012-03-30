<?php
/**
 * @page Synchronize Email Lists
 * @package Imagine Retailer
 */

// Set it as a background job
newrelic_background_job();

$e = new Emails;
$e->synchronize_email_lists();
?>