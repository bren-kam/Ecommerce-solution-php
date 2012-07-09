<?php
/**
 * Method to handle a Ticket
 */
final class Ticket extends ActiveRecordBase {
    /**
     * Constants
     */
    const TECHNICAL_USER_ID = 493;

    /**
     * Hold the Ticket ID
     * @var int
     */
    private $_ticket_id;

    /**
     * Translations for certain characters
     * @var array
     */
    private $_translations = array(
        '’' => "'"
        , '‘' => "'"
        , '”' => '"'
        , '“' => '"'
    );

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'tickets' );
    }

    /**
	 * Create Ticket
	 *
     * @param User $user
	 * @param string $summary
	 * @param string $message
	 * @return int
	 */
	public function create( $user, $summary, $message ) {
        $message = $this->_clean_message( $message );
        $b = fn::browser();

        $this->insert( array( 'user_id' => $user->id, 'assigned_to_user_id' => self::TECHNICAL_USER_ID, 'website_id' => 0, 'summary' => $summary, 'message' => $message, 'browser_name' => $this->b['name'], 'browser_version' => $this->b['version'], 'browser_platform' => $b['platform'], 'browser_user_agent' => $b['user_agent'], 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiisssssss' );

		// Get the assigned to user
		$assigned_to_user = new User();
        $assigned_to_user->get( 493 );

		// Needs to be moved to the controller, right?
		return fn::mail( $assigned_to_user['email'], 'New ' . $user->website->title . ' Ticket - ' . $summary, "Name: " . $user['contact_name'] . "\nEmail: " . $user['email'] . "\nSummary: $summary\n\n" . $message . "\n\nhttp://admin." . DOMAIN . "/tickets/ticket/?tid=" . $this->get_insert_id() );
	}

    /**
     * Clean a ticket message
     *
     * @param string $message
     * @return string
     */
    private function _clean_message( $message ) {
        // Lets remove any characters that might be causing a problem
        $message = str_replace( array_keys( $this->_translations ), array_values( $this->_translations ), $message );
        $message = nl2br( format::links_to_anchors( format::htmlentities( stripslashes( $message ), array('&') ), true , true ) );

        return $message;
    }
}
