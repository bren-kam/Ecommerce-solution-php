<?php

class InstallService {
    /**
     * Install a website
     *
     * @param Account $account
     */
    public function install_website( Account $account ) {
        // Create website industry (furniture)
        $account->add_industries( array( 1 ) );

        // Get Username
        $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::setting('server-ip'), 22 );
        ssh2_auth_password( $ssh_connection, Config::setting('server-username'), Config::setting('server-password') );

        // Copy files
        ssh2_exec( $ssh_connection, "cp -R /gsr/platform/copy/. /home/$username/public_html" );

        // Update config & .htaccess file
        $document_root = '\/home\/' . $username . '\/public_html';

        ssh2_exec( $ssh_connection, "sed -i 's/\[document_root\]/$document_root/g' /home/$username/public_html/config.php" );
        ssh2_exec( $ssh_connection, "sed -i 's/\[website_id\]/" . $account->id . "/g' /home/$username/public_html/config.php" );

        // Must use FTP to assign folders under the right user
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom" );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/" . $account->theme );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/cache" );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/cache/css" );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/cache/js" );

        ssh2_exec( $ssh_connection, "chmod -R 0777 /home/$username/public_html/custom/cache" );
        ssh2_exec( $ssh_connection, "chown -R $username:$username /home/$username/public_html/" );

        // Make sure the public_html directory has the correct group
        ssh2_exec( $ssh_connection, "chown $username:nobody /home/$username/public_html" );

        // Updated website version
        $account->version = 1;
        $account->save();

        // Insert pages
        $pages = array(
            'about-us' => array(
                'title' => 'About Us'
                , 'content' => '&lt;h2&gt;&lt;img class=&quot;alignright&quot; title=&quot;Family shot&quot; src=&quot;http://www.concurringopinions.com/archives/images/family.jpg&quot; alt=&quot;&quot; width=&quot;189&quot; height=&quot;164&quot; style=&quot;float:right; padding-left:10px; padding-bottom:10px;&quot; /&gt;We&#039;ll Make Your House�A Home!&lt;/h2&gt; &lt;p&gt;ABC Home Furnishings family has been in business for over 30 years in Big Town, Louisiana. We originally started as Waterbed Sleep Shoppe and in 1988 we diversified our product line to carry a wide selection of bedroom, living room, and dining room furniture, in our beautifully decorated 33,000 square foot showroom.&lt;/p&gt; &lt;p&gt;We carry some of the most recognized names in furniture and mattresses: Ashley, Berkline, Broyhill, Coaster, and Sealy Mattresses.&lt;/p&gt; &lt;p&gt;Our family buyers continue to always search for the best buys and values in the furniture market. We shop during four international shows each year. Making certain to always find products coming from around the world. Today&#039;s fine furniture is built in The United States, Indonesia, South America, Canada, and China.&lt;/p&gt; &lt;p&gt;Count on us for:&lt;/p&gt; &lt;ul&gt; &lt;li&gt;Family service&lt;/li&gt; &lt;li&gt;Fast and friendly delivery&lt;/li&gt; &lt;li&gt;Great customer service&lt;/li&gt; &lt;li&gt;Knowledgeable and trained sales people&lt;/li&gt; &lt;li&gt;Guaranteed low prices on brand name furniture&lt;/li&gt; &lt;/ul&gt;'
            )
            , 'contact-us' => array(
                'title' => 'Contact Us'
                , 'content' => '&lt;p&gt;We love to hear from you! Please call, click or come on over.&lt;/p&gt;'
            )
            , 'current-offer' => array(
                'title' => 'Yes! Email My Special Coupon Offer Now'
                , 'content' => '&lt;p&gt;Receive Exclusive Tips, Trends, Special Offers and Online Only Sales from ABC Furniture.&lt;/p&gt;'
            )
            , 'financing' => array(
                'title' => 'Financing'
                , 'content' => '&lt;p&gt;The &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; credit card gives you the flexibility to pay for your in-store purchases over time while you enjoy your new furniture now.&lt;/p&gt; &lt;h3&gt;&lt;a href=&quot;https://financial.wellsfargo.com/retailprivatelabel/entry.jsp&quot;&gt;Apply online for instant pre-approval before you shop!&lt;/a&gt;&lt;/h3&gt; &lt;p&gt;&lt;a href=&quot;https://financial.wellsfargo.com/retailprivatelabel/entry.jsp&quot; title=&quot;Apply Now&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;/theme1/wp-content/uploads/2009/11/apply.gif&quot; alt=&quot;apply&quot; title=&quot;Apply Now&quot; width=&quot;146&quot; height=&quot;39&quot; /&gt;&lt;/a&gt;&lt;/p&gt; &lt;p&gt;As an &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; cardholder, you&#039;ll enjoy these benefits:&lt;/p&gt; &lt;ul&gt; &lt;li&gt;Convenient monthly payments&lt;/li&gt; &lt;li&gt;A revolving line of credit for your future furniture needs&lt;/li&gt; &lt;li&gt;Special promotional offers where available, including no-interest and reduced rate interest plans&lt;/li&gt; &lt;li&gt;No annual fee and no prepayment penalties&lt;/li&gt; &lt;li&gt;An easy-to-use online bill payment option&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;The &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; credit card is provided by Wells Fargo Financial National Bank, a subsidiary of &lt;a title=&quot;Wells Fargo Financial&quot; href=&quot;http://financial.wellsfargo.com/&quot; target=&quot;_blank&quot;&gt;Wells Fargo Financial&lt;/a&gt;. Wells Fargo Financial is an affiliate of &lt;a title=&quot;Wells Fargo Bank, N.A&quot; href=&quot;http://www.wellsfargo.com/&quot; target=&quot;_blank&quot;&gt;Wells Fargo Bank, N.A&lt;/a&gt;&lt;/p&gt;'
            )
            , 'home' => array(
                'title' => 'Home'
                , 'content' => '&lt;p&gt;ABC Home Furnishings is family-owned and family-operated and has served Big Town, USA for over 30 years. &lt;a title=&quot;About Us&quot; href=&quot;http://furniture.imagineretailer.com/theme1/about-us/&quot;&gt;We have built our company by providing beautiful furniture, great service, low prices and hometown relationships from our family to yours.&lt;/a&gt;&lt;/p&gt; &lt;p&gt;ABC always offers simple to get, &lt;a title=&quot;Financing&quot; href=&quot;http://furniture.imagineretailer.com/theme1/financing/&quot;&gt;simple to use financing&lt;/a&gt;. Our programs often allow you to make payments while deferring interest, and always provide you benefits when shopping with us.&lt;/p&gt; &lt;p&gt;As a ABC Furniture cardholder, you&#039;ll enjoy benefits such as:&lt;br /&gt; &amp;bull; Convenient monthly payments&lt;br /&gt; &amp;bull; A revolving line of credit for all your purchasing needs&lt;br /&gt; &amp;bull; Special promotional offers where available, including no-interest and reduced rate interest plans&lt;br /&gt; &amp;bull; No annual fee and no prepayment penalties&lt;/p&gt; &lt;p&gt;Step inside our beautifully decorated showroom to browse a wide selection of&lt;a title=&quot;bedroom furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/bedrooms/&quot;&gt; bedroom,&lt;/a&gt; &lt;a title=&quot;living room furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/living-rooms/&quot;&gt;living room,&lt;/a&gt; &lt;a title=&quot;dining room furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/dining-rooms/&quot;&gt;and dining room furniture,&lt;/a&gt; &lt;a title=&quot;leather furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/leather/&quot;&gt;leather,&lt;/a&gt; &lt;a title=&quot;home office furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/home-office/&quot;&gt;home office,&lt;/a&gt; &lt;a title=&quot;kids furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/youth/&quot;&gt;kids furniture &lt;/a&gt;and the area&#039;s largest selection of brand name mattresses and box spring sets. You&#039;ll find brands you recognize and trust including &lt;a title=&quot;Ashley Furniture&quot; href=&quot;http://www.ashleyfurniture.com/&quot;&gt;Ashley&lt;/a&gt;, &lt;a title=&quot;Berkline Furniture&quot; href=&quot;http://www.berkline.com/&quot;&gt;Berkline&lt;/a&gt;, &lt;a title=&quot;Broyhill Furniture&quot; href=&quot;http://www.broyhillfurniture.com/&quot;&gt;Broyhill&lt;/a&gt;, &lt;a title=&quot;Coaster Furniture&quot; href=&quot;http://coastercompany.com/&quot;&gt;Coaster&lt;/a&gt;, and &lt;a title=&quot;Sealy Bedding&quot; href=&quot;http://www.sealy.com/&quot;&gt;Sealy Mattresses&lt;/a&gt;.&lt;/p&gt; &lt;p&gt;Make your house a home at ABC Home Furnishings!&lt;/p&gt;'
            )
            , 'sidebar' => array(
                'title' => 'Sidebar'
                , 'content' => ''
            )
            , 'products' => array(
                'title' => 'Products'
                , 'content' => ''
            )
            , 'brands' => array(
                'title' => 'Brands'
                , 'content' => ''
            )
        );

        foreach ( $pages as $slug => $page ) {
            $account_page = new AccountPage();
            $account_page->website_id = $account->id;
            $account_page->slug = $slug;
            $account_page->title = $page['title'];
            $account_page->content = $page['content'];

            try {
                $account_page->create();
            } catch ( ModelException $e ) {
                switch ( $e->getCode() ) {
                    // It's fine if that was the error
                    case ActiveRecordBase::EXCEPTION_DUPLICATE_ENTRY:
                    break;

                    default:
                        // Hopefully this shouldn't happen, but be prepared if it does
                        die( '<strong>Fatal Error (' . $e->getCode() . '):</strong> ' . $e->getMessage() . "<br /><br />\n\n" . $e->getTraceAsString() );
                    break;
                }
            }

            // Need to keep sidebar page
            if ( 'sidebar' == $slug )
                $sidebar_page = $account_page;
        }

        // Insert static sidebar elements
        $attachments = array( 'search', 'video', 'email', 'room-planner' );
        $sequence = 0;

        /**
         * @var AccountPage $sidebar_page
         */
        foreach ( $attachments as $key ) {
            $account_page_attachment = new AccountPageAttachment();
            $account_page_attachment->website_page_id = $sidebar_page->id;
            $account_page_attachment->key = $key;
            $account_page_attachment->value = '';
            $account_page_attachment->sequence = $sequence;
            $account_page_attachment->create();

            $sequence++;
        }

        // Create default email list
        $email_list = new EmailList;
        $email_list->website_id = $account->id;
        $email_list->name = 'Default';
        $email_list->create();

        // Create default email autoresponder
        $email_autoresponder = new EmailAutoresponder;
        $email_autoresponder->website_id = $account->id;
        $email_autoresponder->email_list_id = $email_list->id;
        $email_autoresponder->name = 'Default';
        $email_autoresponder->subject = $account->title . ' - Current Offer';
        $email_autoresponder->message = '<p>Thank you for signing up for the latest tips, trends and special offers. Here is the current offer from our store.<p><br /><br />';
        $email_autoresponder->current_offer = 1;
        $email_autoresponder->default = 1;
        $email_autoresponder->create();

        // Create default email template
        $email_template = new EmailTemplate();
        $email_template->name = 'Default';
        $email_template->template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>[subject]</title><style type="text/css">body { width: 800px; font-family:Arial, Helvetica, sans-serif; font-size:13px; margin: 15px auto; }p { line-height: 21px; padding-bottom: 7px; }h2{ padding:0; margin:0; }td{ font-size: 13px; padding-right: 10px; }li { padding-top: 7px; }</style></head><body>[message]</body></html>';
        $email_template->type = 'default';
        $email_template->create();

        // Create email template association
        $email_template->add_association( $account->id );

        // Create default settings
        $account->set_settings( array( 'timezone' => Config::setting('default-timezone') ) );
    }

    /**
     * Install account package
     *
     * @param Account $account
     */
    public function install_package( Account $account ) {
        // Get company package
        $company_package = new CompanyPackage();
        $company_package->get( $account->company_package_id );

        // Get template account
        $template_account = new Account();
        $template_account->get( $company_package->website_id );

        // Update theme and logo
        $account->theme = $template_account->theme;
        $account->logo = $template_account->logo;
        $account->save();

        // Get FTP Username
        $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );
        $template_username = security::decrypt( base64_decode( $template_account->ftp_username ), ENCRYPTION_KEY );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::setting('server-ip'), 22 );
        ssh2_auth_password( $ssh_connection, Config::setting('server-username'), Config::setting('server-password') );

        // Make The new theme directory
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/" . $template_account->theme );

        // Copy over all the theme files
        ssh2_exec( $ssh_connection, "cp -Rf /home/$template_username/public_html/custom/. /home/$username/public_html/custom" );

		// Copy over config file
        ssh2_exec( $ssh_connection, "yes | cp -rf /home/$template_username/public_html/config.php /home/$username/public_html/config.php" );

		ssh2_exec( $ssh_connection, "sed -i 's/$template_username/$username/g' /home/$username/public_html/config.php" );
		ssh2_exec( $ssh_connection, "sed -i 's/" . $template_account->id . "/" . $account->id . "/g' /home/$username/public_html/config.php" );

        ssh2_exec( $ssh_connection, "chmod -R 0777 /home/$username/public_html/custom/cache" );
        ssh2_exec( $ssh_connection, "chown -R $username:$username /home/$username/public_html/" );

        // Make sure the public_html directory has the correct group
        ssh2_exec( $ssh_connection, "chown $username:nobody /home/$username/public_html" );

        // Copy account pages
        $account_page = new AccountPage();
        $account_page->copy_by_account( $template_account->id, $account->id );

        // Get account pages by slug
        $account_pages = ar::assign_key( $account_page->get_all( $account->id ), 'slug', true );
        $template_account_pages = ar::assign_key( $account_page->get_all( $template_account->id ), 'website_page_id', true );

        // Get attachments
        $account_page_attachment = new AccountPageAttachment();
        $template_account_attachments = $account_page_attachment->get_by_account_page_ids( array_keys( $template_account_pages ) );

        // Delete certain sidebar elements that you can only have one of
        $account_page_attachment->delete_unique_attachments( array_values( $account_pages ) );

        // Declare file which will be used to copy files
        $file = new File();

        /**
         * @var AccountPageAttachment $taa
         */
        if ( is_array( $template_account_attachments )  )
        foreach ( $template_account_attachments as $taa ) {
			// Needs to be a value that we can copy
			if ( 1 != $taa->status )
				continue;

            if ( !in_array( $taa->key, array( 'email', 'search' ) ) ) {
                try {
                    $value = $file->copy_file( $account->id, $taa->value, 'websites' );
                } catch ( HelperException $e ) {
                    continue;
                }

                // Create the link in website files
                $account_file = new AccountFile();
                $account_file->website_id = $account->id;
                $account_file->file_path = $value;
                $account_file->create();
            } else {
                $value = '';
            }

            $new_account_page_attachment = new AccountPageAttachment();
            $new_account_page_attachment->website_page_id = $account_pages[$template_account_pages[$taa->website_page_id]];
            $new_account_page_attachment->key = $taa->key;
            $new_account_page_attachment->value = $value;
            $new_account_page_attachment->extra = $taa->extra;
            $new_account_page_attachment->meta = $taa->meta;
            $new_account_page_attachment->sequence = $taa->sequence;
            $new_account_page_attachment->create();
        }

        // Get account files
        $account_file = new AccountFile();
        $template_account_files = $account_file->get_by_account( $template_account->id );

        /**
         * @var AccountFile $taf
         */
        if ( !empty( $template_account_files ) )
        foreach( $template_account_files as $taf ) {
            // Needs to be a value that we can copy
			if ( !stristr( $taa->value, 'retailcatalog.us' ) )
				continue;

            $value = $file->copy_file( $account->id, $taf->file_path, 'websites' );

            if ( !$value )
                continue;

            // Create the link in website files
            $account_file = new AccountFile();
            $account_file->website_id = $account->id;
            $account_file->file_path = $value;
            $account_file->create();
        }

        // Copy Account industries
        $account->copy_industries_by_account( $template_account->id, $account->id );

        // Copy Account Pagemeta
        $account_pagemeta = new AccountPagemeta();
        $pagemeta_keys = array( 'display-coupon', 'email-coupon', 'hide-all-maps' );

        $template_pagemeta = $account_pagemeta->get_for_pages_by_keys( array_keys( $template_account_pages ), $pagemeta_keys );

        $new_pagemeta = array();

        /**
         * @var AccountPagemeta $tpm
         */
        if ( is_array( $template_pagemeta )  )
        foreach ( $template_pagemeta as $tpm ) {
             $website_page_id = (int) $account_pages[$template_account_pages[$tpm->website_page_id]];

            $new_pagemeta[] = array( 'website_page_id' => $website_page_id, 'key' => $tpm->key, 'value' => $tpm->value );
        }

        if ( 0 != count( $new_pagemeta ) )
            $account_pagemeta->add_bulk( $new_pagemeta );

        // Copy top brands
        $account->copy_top_brands_by_account( $template_account->id, $account->id );

        // Copy products
        $account_product = new AccountProduct();
        $account_product->copy_by_account( $template_account->id, $account->id );

        // Reorganize Categories
        $account_category = new AccountCategory();
        $account_category->reorganize_categories( $account->id, new Category() );

        // Copy Website Settings
		$account->copy_settings_by_account( $template_account->id, $account->id, array( 'banner-width', 'banner-height', 'banner-speed', 'banner-background-color', 'banner-effect', 'banner-hide-scroller', 'sidebar-image-width', 'css' ) );
    }
}
