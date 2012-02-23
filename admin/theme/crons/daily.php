<?php
/**
 * @page Cron Jobs run Daily
 * @package Imagine Retailer
 */

// Set it as a background job
newrelic_background_job();

$t = new Tickets;
$t->email_overdue_tickets();
?>