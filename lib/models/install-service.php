<?php
class InstallService {
    /**
     * Install a website
     *
     * @throws ModelException
     *
     * @param Account $account
     * @param int $user_id the user that performs the operation
     */
    public function install_website( Account $account, $user_id = -1) {
        if ( empty( $account->server_id ) )
            throw new ModelException( 'No connected server' );

        // Create website industry (furniture)
        $account->add_industries( array( 1 ) );

        // Get Username
        $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );

        // Get Server
        $server = new Server();
        $server->get( $account->server_id );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::server('ip', $server->ip), Config::server('port', $server->ip) );

        ssh2_auth_password( $ssh_connection, Config::server('username', $server->ip), Config::server('password', $server->ip) );

		// Setup as root
        ssh2_exec( $ssh_connection, "sudo su -" );
		
		// Copy files
		ssh2_exec( $ssh_connection, "sudo cp -R /gsr/systems/gsr-site/copy/. /home/$username/public_html" );
		
        // Update config & .htaccess file
        ssh2_exec( $ssh_connection, "sudo sed -i 's/\[website_id\]/" . $account->id . "/g' /home/$username/public_html/index.php" );
        
		ssh2_exec( $ssh_connection, "sudo chown -R $username:$username /home/$username/public_html/" );

        // Make sure the public_html directory has the correct group
        ssh2_exec( $ssh_connection, "sudo chown $username:nobody /home/$username/public_html" );

        // Updated website version
        $account->version = 1;
        $account->user_id_updated = $user_id;
        $account->save();

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
        $email_autoresponder->subject = 'Current Offer';
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

        // Get Server
        $server = new Server();
        $server->get( $account->server_id );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::server('ip', $server->ip), Config::server('port', $server->ip) );
        ssh2_auth_password( $ssh_connection, Config::server('username', $server->ip), Config::server('password', $server->ip) );
		
		// Setup as root
        ssh2_exec( $ssh_connection, "sudo su -" );
		
        // Copy files
        ssh2_exec( $ssh_connection, "sudo cp -R /gsr/systems/gsr-site/copy/. /home/$username/public_html" );

        // Update config & .htaccess file
        ssh2_exec( $ssh_connection, "sudo sed -i 's/\[website_id\]/" . $account->id . "/g' /home/$username/public_html/index.php" );

        // Change files owner
        ssh2_exec( $ssh_connection, "sudo chown -R $username:$username /home/$username/public_html/" );

        // Make sure the public_html directory has the correct group
        ssh2_exec( $ssh_connection, "sudo chown $username:nobody /home/$username/public_html" );

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
                    if($taa->value != ""){
                        $value = $file->copy_file( $account->id, $taa->value, 'websites' );
                    }
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

        // // Copy top brands
        // $account->copy_top_brands_by_account( $template_account->id, $account->id );

        // // Copy products
        // $account_product = new AccountProduct();
        // $account_product->copy_by_account( $template_account->id, $account->id );

        // // Copy product options
        // $account_product_option = new AccountProductOption();
        // $account_product_option->copy_by_account( $template_account->id, $account->id );

        // // Copy related products
        // $account_product_group = new WebsiteProductGroup();
        // $account_product_group->copy_by_account( $template_account->id, $account->id );

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
