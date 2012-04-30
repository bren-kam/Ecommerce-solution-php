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
        
        // SSH Connection
        $ssh_connection = ssh2_connect( '199.79.48.137', 22 );
        ssh2_auth_password( $ssh_connection, 'root', 'WIxp2sDfRgLMDTL5' );

        // Copy files
        ssh2_exec( $ssh_connection, "cp -R /gsr/platform/copy/* /home/$username/public_html" . $subdomain2 );

        // Update config & .htaccess file
        $document_root = '\/home\/' . $username . '\/public_html' . $subdomain2;

        ssh2_exec( $ssh_connection, "sed -i 's/\[document_root\]/$document_root/g' /home/$username/public_html/{$subdomain}config.php" );
        ssh2_exec( $ssh_connection, "sed -i 's/\[website_id\]/$website_id/g' /home/$username/public_html/{$subdomain}config.php" );

        // Must use FTP to assign folders under the right user
        ssh2_exec( "mkdir /home/$username/public_html/{$subdomain}custom" );
        ssh2_exec( "mkdir /home/$username/public_html/{$subdomain}custom/theme" );
        ssh2_exec( "mkdir /home/$username/public_html/{$subdomain}custom/cache" );
        ssh2_exec( "mkdir /home/$username/public_html/{$subdomain}custom/cache/css" );
        ssh2_exec( "mkdir /home/$username/public_html/{$subdomain}custom/cache/js" );

        ssh2_exec( "chmod -R 0777 /home/$username/public_html/{$subdomain}custom/cache" );
        ssh2_exec( "chown -R $username:$username /home/$username/public_html/{$subdomain}custom/cache" );

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
