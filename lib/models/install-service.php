<?php

class InstallService {
    /**
     * Install a website
     *
     * @param Account $account
     * @param int $user_id the user that performs the operation
     */
    public function install_website( Account $account, $user_id = -1) {
        // Create website industry (furniture)
        $account->add_industries( array( 1 ) );

        // Get Username
        $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::setting('server-ip'), 22 );
        ssh2_auth_password( $ssh_connection, Config::setting('server-username'), Config::setting('server-password') );

        // Copy files
        ssh2_exec( $ssh_connection, "cp -R /gsr/systems/gsr-site/copy/. /home/$username/public_html" );

        // Update config & .htaccess file
        ssh2_exec( $ssh_connection, "sed -i 's/\[website_id\]/" . $account->id . "/g' /home/$username/public_html/index.php" );

        ssh2_exec( $ssh_connection, "chown -R $username:$username /home/$username/public_html/" );

        // Make sure the public_html directory has the correct group
        ssh2_exec( $ssh_connection, "chown $username:nobody /home/$username/public_html" );

        // Updated website version
        $account->version = 1;
        $account->user_id_updated = $user_id;
        $account->save();

        // Insert pages
        $pages = array(
            'about-us' => array(
                'title' => 'About Us'
                , 'content' => '<h2><img class="alignright" title="Family shot" src="http://www.concurringopinions.com/archives/images/family.jpg" alt="" width="189" height="164" style="float:right; padding-left:10px; padding-bottom:10px;" />We\'ll Make Your House...A Home!</h2> <p>ABC Home Furnishings family has been in business for over 30 years in Big Town, Louisiana. We originally started as Waterbed Sleep Shoppe and in 1988 we diversified our product line to carry a wide selection of bedroom, living room, and dining room furniture, in our beautifully decorated 33,000 square foot showroom.</p> <p>We carry some of the most recognized names in furniture and mattresses: Ashley, Berkline, Broyhill, Coaster, and Sealy Mattresses.</p> <p>Our family buyers continue to always search for the best buys and values in the furniture market. We shop during four international shows each year. Making certain to always find products coming from around the world. Today\'s fine furniture is built in The United States, Indonesia, South America, Canada, and China.</p> <p>Count on us for:</p> <ul> <li>Family service</li> <li>Fast and friendly delivery</li> <li>Great customer service</li> <li>Knowledgeable and trained sales people</li> <li>Guaranteed low prices on brand name furniture</li> </ul>'
            )
            , 'contact-us' => array(
                'title' => 'Contact Us'
                , 'content' => '<p>We love to hear from you! Please call, click or come on over.</p>'
            )
            , 'current-offer' => array(
                'title' => 'Yes! Email My Special Coupon Offer Now'
                , 'content' => '<p>Receive Exclusive Tips, Trends, Special Offers and Online Only Sales from ABC Furniture.</p>'
            )
            , 'financing' => array(
                'title' => 'Financing'
                , 'content' => '<p>The <strong>ABC Home Furnishings</strong> credit card gives you the flexibility to pay for your in-store purchases over time while you enjoy your new furniture now.</p> <h3><a href="https://financial.wellsfargo.com/retailprivatelabel/entry.jsp">Apply online for instant pre-approval before you shop!</a></h3> <p><a href="https://financial.wellsfargo.com/retailprivatelabel/entry.jsp" title="Apply Now" target="_blank"><img src="/theme1/wp-content/uploads/2009/11/apply.gif" alt="apply" title="Apply Now" width="146" height="39" /></a></p> <p>As an <strong>ABC Home Furnishings</strong> cardholder, you\'ll enjoy these benefits:</p> <ul> <li>Convenient monthly payments</li> <li>A revolving line of credit for your future furniture needs</li> <li>Special promotional offers where available, including no-interest and reduced rate interest plans</li> <li>No annual fee and no prepayment penalties</li> <li>An easy-to-use online bill payment option</li> </ul> <p>The <strong>ABC Home Furnishings</strong> credit card is provided by Wells Fargo Financial National Bank, a subsidiary of <a title="Wells Fargo Financial" href="http://financial.wellsfargo.com/" target="_blank">Wells Fargo Financial</a>. Wells Fargo Financial is an affiliate of <a title="Wells Fargo Bank, N.A" href="http://www.wellsfargo.com/" target="_blank">Wells Fargo Bank, N.A</a></p>'
            )
            , 'home' => array(
                'title' => 'Home'
                , 'content' => '<p>ABC Home Furnishings is family-owned and family-operated and has served Big Town, USA for over 30 years. <a title="About Us" href="http://furniture.imagineretailer.com/theme1/about-us/">We have built our company by providing beautiful furniture, great service, low prices and hometown relationships from our family to yours.</a></p> <p>ABC always offers simple to get, <a title="Financing" href="http://furniture.imagineretailer.com/theme1/financing/">simple to use financing</a>. Our programs often allow you to make payments while deferring interest, and always provide you benefits when shopping with us.</p> <p>As a ABC Furniture cardholder, you\'ll enjoy benefits such as:<br /> &bull; Convenient monthly payments<br /> &bull; A revolving line of credit for all your purchasing needs<br /> &bull; Special promotional offers where available, including no-interest and reduced rate interest plans<br /> &bull; No annual fee and no prepayment penalties</p> <p>Step inside our beautifully decorated showroom to browse a wide selection of<a title="bedroom furniture" href="http://furniture.imagineretailer.com/theme1/c/furniture/bedrooms/"> bedroom,</a> <a title="living room furniture" href="http://furniture.imagineretailer.com/theme1/c/furniture/living-rooms/">living room,</a> <a title="dining room furniture" href="http://furniture.imagineretailer.com/theme1/c/furniture/dining-rooms/">and dining room furniture,</a> <a title="leather furniture" href="http://furniture.imagineretailer.com/theme1/c/furniture/leather/">leather,</a> <a title="home office furniture" href="http://furniture.imagineretailer.com/theme1/c/furniture/home-office/">home office,</a> <a title="kids furniture" href="http://furniture.imagineretailer.com/theme1/c/furniture/youth/">kids furniture </a>and the area\'s largest selection of brand name mattresses and box spring sets. You\'ll find brands you recognize and trust including <a title="Ashley Furniture" href="http://www.ashleyfurniture.com/">Ashley</a>, <a title="Berkline Furniture" href="http://www.berkline.com/">Berkline</a>, <a title="Broyhill Furniture" href="http://www.broyhillfurniture.com/">Broyhill</a>, <a title="Coaster Furniture" href="http://coastercompany.com/">Coaster</a>, and <a title="Sealy Bedding" href="http://www.sealy.com/">Sealy Mattresses</a>.</p> <p>Make your house a home at ABC Home Furnishings!</p>'
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
     * @param int $user_id the user id that performs the operation
     */
    public function install_package( Account $account, $user_id = -1) {
        // Get company package
        $company_package = new CompanyPackage();
        $company_package->get( $account->company_package_id );

        // Get template account
        $template_account = new Account();
        $template_account->get( $company_package->website_id );

        // Get Unlocked account, used for base LESS
        $unlocked_account = new Account();
        $unlocked_account->get( Account::TEMPLATE_UNLOCKED );

        // Update theme and logo
        $account->theme = $template_account->theme;
        $account->logo = $template_account->logo;

        $account->user_id_updated = $user_id;
        $account->save();

        // Get FTP Username
        $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );
        $template_username = security::decrypt( base64_decode( $template_account->ftp_username ), ENCRYPTION_KEY );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::setting('server-ip'), 22 );
        ssh2_auth_password( $ssh_connection, Config::setting('server-username'), Config::setting('server-password') );

        // Copy files
        ssh2_exec( $ssh_connection, "cp -R /gsr/systems/gsr-site/copy/. /home/$username/public_html" );

        // Update config & .htaccess file
        ssh2_exec( $ssh_connection, "sed -i 's/\[website_id\]/" . $account->id . "/g' /home/$username/public_html/index.php" );

        // Change files owner
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


            try {
                $value = $file->copy_file( $account->id, $taf->file_path, 'websites' );
            } catch(Exception $e) {
                $value = false;
            }

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

        // Copy product options
        $account_product_option = new AccountProductOption();
        $account_product_option->copy_by_account( $template_account->id, $account->id );

        // Copy related products
        $account_product_group = new WebsiteProductGroup();
        $account_product_group->copy_by_account( $template_account->id, $account->id );

        // Reorganize Categories
        $account_category = new AccountCategory();
        $account_category->reorganize_categories( $account->id, new Category() );

        $account->copy_settings_by_account( $template_account->id, $account->id, array(
            'banner-width', 'banner-height', 'banner-speed', 'banner-background-color', 'banner-effect'
            , 'banner-hide-scroller', 'sidebar-image-width', 'less', 'css', 'slideshow-fixed-width'
            , 'slideshow-categories', 'sidebar-left', 'top-categories', 'favicon', 'navigation', 'layout', 'header'
            , 'footer-navigation', 'sm-facebook-link', 'sm-twitter-link', 'sm-google-link', 'sm-pinterest-link'
            , 'sm-linkedin-link', 'sm-youtube-link', 'sm-instagram-link'
        ) );
    }
}
