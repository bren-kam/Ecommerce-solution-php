<?php

/**
 * Handles all the website information
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Websites extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}

    /**
	 * Get Website
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_website( $website_id ) {
        // Type Juggling
        $website_id = (int) $website_id;

		$website = $this->db->get_row( "SELECT a.`website_id`, a.`company_package_id`, a.`user_id`, a.`os_user_id`, a.`domain`, a.`subdomain`, a.`title`, a.`plan_name`, a.`plan_description`, a.`theme`, a.`logo`, a.`phone`, a.`pages`, a.`products`, a.`product_catalog`, a.`link_brands`, a.`blog`, a.`email_marketing`, a.`mobile_marketing`, a.`shopping_cart`, a.`seo`, a.`room_planner`, a.`craigslist`, a.`social_media`, a.`domain_registration`, a.`additional_email_addresses`, a.`ga_profile_id`, a.`ga_tracking_key`, a.`wordpress_username`, a.`wordpress_password`, a.`mc_list_id`, a.`type`, a.`version`, a.`live`, a.`date_created`, a.`date_updated`, b.`status` AS user_status, c.`name` AS company  FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `companies` AS c ON ( b.`company_id` = c.`company_id` ) WHERE a.`website_id` = $website_id", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website.', __LINE__, __METHOD__ );
			return false;
		}

		return $website;
	}

	/**
	 * Installs a website
	 *
	 * @param int $website_id
	 * @param string $username
	 * @return bool
	 */
	public function install( $website_id, $username ) {
        // Make sure it has enough memory to install
		ini_set('memory_limit', '256M'); 

        if ( empty( $username ) )
            return false;

		// Typecast
		$website_id = (int) $website_id;

        // Get website
        $web = $this->get_website( $website_id );

        // SSH Connection
        $ssh_connection = ssh2_connect( '199.79.48.137', 22 );
        ssh2_auth_password( $ssh_connection, 'root', 'WIxp2sDfRgLMDTL5' );

        ssh2_exec( $ssh_connection, "cp -R /gsr/platform/copy/. /home/$username/public_html" );

        // Update config & .htaccess file
        $document_root = '\/home\/' . $username . '\/public_html';

        ssh2_exec( $ssh_connection, "sed -i 's/\[document_root\]/$document_root/g' /home/$username/public_html/config.php" );
        ssh2_exec( $ssh_connection, "sed -i 's/\[website_id\]/$website_id/g' /home/$username/public_html/config.php" );

        // Must use FTP to assign folders under the right user
        ssh2_exec( $ssh_connection, "mkdir -p /home/$username/public_html/custom/cache/css" );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/theme" );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/cache/js" );

        ssh2_exec( $ssh_connection, "chmod -R 0777 /home/$username/public_html/custom/cache" );
        ssh2_exec( $ssh_connection, "chown -R $username:$username /home/$username/public_html/" );

        // Make sure the public_html directory has the correct group
        ssh2_exec( $ssh_connection, "chown $username:nobody /home/$username/public_html" );

        // Insert pages
        $this->db->query( Pre_Data::pages_sql( $website_id ) );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to insert website pages.', __LINE__, __METHOD__ );
            return false;
        }

        $website_page_id = $this->db->get_var( "SELECT `website_page_id` FROM `website_pages` WHERE `website_id` = $website_id AND `slug` = 'sidebar'" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to get website page id.', __LINE__, __METHOD__ );
            return false;
        }

        // Insert static sidebar elements
        $this->db->query( Pre_Data::attachments_sql( $website_page_id ) );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to insert website sidebar attachments.', __LINE__, __METHOD__ );
            return false;
        }

        // Create default email list
        $this->db->insert( 'email_lists', array( 'website_id' => $website_id, 'name' => 'Default', 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to insert default email list.', __LINE__, __METHOD__ );
            return false;
        }

        $email_list_id = $this->db->insert_id;

        // Create default email autoresponder
        $this->db->insert( 'email_autoresponders', array( 'website_id' => $website_id, 'email_list_id' => $email_list_id, 'name' => 'Default', 'subject' => $web['title'] . ' - Current Offer', 'message' => '<p>Thank you for signing up for the latest tips, trends and special offers. Here is the current offer from our store.<p><br /><br />', 'current_offer' => 1, 'default' => 1, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iisssiis' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to insert email autoresponder.', __LINE__, __METHOD__ );
            return false;
        }

        // Create default email template
        $this->db->insert( 'email_templates', array( 'name' => 'Default', 'template' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>[subject]</title><style type="text/css">body { width: 800px; font-family:Arial, Helvetica, sans-serif; font-size:13px; margin: 15px auto; }p { line-height: 21px; padding-bottom: 7px; }h2{ padding:0; margin:0; }td{ font-size: 13px; padding-right: 10px; }li { padding-top: 7px; }</style></head><body>[message]</body></html>', 'type' => 'default', 'date_created' => dt::date('Y-m-d H:i:s') ), 'ssss' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to insert email template result.', __LINE__, __METHOD__ );
            return false;
        }

        $email_template_id = $this->db->insert_id;

        // Create email template association
        $this->db->insert( 'email_template_associations', array( 'email_template_id' => $email_template_id, 'object_id' => $website_id, 'type' => 'website' ), 'iis' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to insert email template association.', __LINE__, __METHOD__ );
            return false;
        }

        // Create default settings
        $this->db->insert( 'email_settings', array( 'website_id' => $website_id, 'key' => 'timezone', 'value' => 'America/New_York' ), 'iss' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to insert email setting.', __LINE__, __METHOD__ );
            return false;
        }

        // Update website username
        $this->db->update( 'websites', array( 'ftp_username' => base64_encode( security::encrypt( $username, ENCRYPTION_KEY ) ), 'version' => 1 ), array( 'website_id' => $website_id ), 'si', 'i' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update website ftp username', __LINE__, __METHOD__ );
			return false;
		}
			
        return true;
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}
