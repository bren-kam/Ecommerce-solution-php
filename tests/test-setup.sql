-- User Test
INSERT INTO `users` (`user_id`, `company_id`, `email`, `password`, `contact_name`, `store_name`, `work_phone`, `cell_phone`, `billing_first_name`, `billing_last_name`, `billing_address1`, `billing_city`, `billing_state`, `billing_zip`, `products`, `arb_subscription_id`, `role`, `status`, `last_login`, `date_created`, `date_updated`) VALUES
(514, 1, 'test@greysuitretail.com', 'fc02ff8277952c2808d684586325341f', '', '', '', '', '', '', '', '', '', '', 126, '', 7, 1, '2012-08-22 01:44:05', '2012-07-25 17:59:29', '2012-08-22 01:44:09'),
(513, 1, 'test@studio98.com', '32250170a0dca92d53ec9624f336ca24', '', '', '', '', '', '', '', '', '', '', 126, '', 5, 1, '2012-08-22 01:44:05', '2012-07-25 17:59:29', '2012-08-22 01:44:09');

-- Category Test
INSERT INTO `categories` (`category_id`, `parent_category_id`, `name`, `slug`, `sequence`, `date_updated`) VALUES
(562, 560, 'Test Child Child One', 'test-child-child-one', 1, '2012-08-22 01:27:37'),
(561, 559, 'Test Child Two', 'test-child-two', 1, '2012-08-22 01:27:37'),
(560, 559, 'Test Child One', 'test-child-one', 0, '2012-08-22 01:27:37'),
(559, 0, 'Test Parent', 'test-parent', -1, '0000-00-00 00:00:00');
