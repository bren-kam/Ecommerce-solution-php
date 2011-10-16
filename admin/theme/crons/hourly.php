<?php
/**
 * @page Update Scheduled Emails
 * @package Imagine Retailer
 */

$e = new Emails;
$e->update_scheduled_emails();

$t = new Tickets;
$t->clean_uploads();
?>