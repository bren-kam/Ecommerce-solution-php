<?php
/**
 * @page Cron Jobs run Daily
 * @package Imagine Retailer
 */

$t = new Tickets;
$t->email_overdue_tickets();
?>