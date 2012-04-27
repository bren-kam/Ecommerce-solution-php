<?php
/**
 * cPanel - API Library
 *
 * Library based on documentation available on 04/26/2012 from
 * @url http://docs.cpanel.net/twiki/bin/view/SoftwareDevelopmentKit/LivePHP#The%20CPANEL%20PHP%20Class
 *
 */
class cPanel_API {
    /**
	 * Constant paths to include files
	 */
	const URL_API = 'http://plugcp.primusconcepts.com/greysuit/';
	const DEBUG = false;

    /**
     * Variables
     */
    private $cpanel = NULL;

	/**
	 * Construct class will initiate and run everything
	 */
	public function __construct() {
	}

    /**
     * Initiate Internal API
     */
    private function _init_internal_api() {
        if ( isset( $this->cpanel ) )
            return;

        require '/usr/local/cpanel/php/cpanel.php';

        $this->cpanel = new CPANEL();
    }
    /**
	
	/******************************/
	/* Start: Trumpia API Methods */
	/******************************/
	
    /**
	 * Add Contact
	 *
     * Adds a new contact to contact list. A ContactID is returned which will be needed to update or
     * delete the contact. There can be only one instance of a tool such as a mobile number on a list;
     * however the same contact or some of the same contact information may exist on a different list.
     *
     * NOTES: If you add the same contact info to different lists they will each be separate records
     * with unique ContactID's. To copy, move, or other contact management, please go online to
     * trumpia.com.
     *
     * @param string $FirstName First name of the contact.
     * @param string $ListName List to add the contact to.
     * @param string $LastName [optional] Last name of the contact.
     * @param string $Email [optional] Email address of the contact.
     * @param int $CountryCode [optional|1]
     *      Mobile phone number's country code. If left blank then it will be assumed to be a
     *      US number.
     * @param string $MobileNumber [optional]
     *      For US, the 10 digit number without the leading 0 or 1. For some international numbers,
     *      the leading 0 or 1 must be omitted.
     * @param string $AIM [optional] AOL Instant Messenger screen name
     * @param string $MSN [optional] MSN Instant Messenger screen name must be an email address.
     * @param bool $SendVerification [optional|FALSE]
     *      TRUE or FALSE. If set to true, this sends a verification message to each tool before they
     *      are added to your distribution list. If the tool is not verified, they will be marked as
     *      not verified and cannot be used. If set to false, the verification step is bypassed and
     *      contacts will be added directly into your distribution list. A verification is omitted
     *      for an international phone number (not US).
     * @param bool $UseCustomMobileVerificationMessage [optional|FALSE]
     *      TRUE or FALSE. Send your custom verification message.
     * @param string $CustomMobileVerificationMessage [optional]
     *      If UseCustomMobileVerificationMessage is TRUE, this message will be sent for the
     *      verification message.
     *      Limit 60 characters.
     *      Default Verification Message : Reply OK to start. Msg&Data rates may apply. Upto 30msg/mo.
	 * @return int
	 */
    public function add_contact( $FirstName, $ListName, $LastName = '', $Email = '', $CountryCode = 1, $MobileNumber = '', $AIM = '', $MSN = '', $SendVerification = FALSE, $UseCustomMobileVerificationMessage = FALSE, $CustomMobileVerificationMessage = '' ) {
        // Format the bool values
        $this->_format_bools( array( &$SendVerification, &$UseCustomMobileVerificationMessage ) );

		// Execute the command
		$response = $this->_execute( 'addcontact', compact( 'FirstName', 'ListName', 'LastName', 'Email', 'CountryCode', 'MobileNumber', 'AIM', 'MSN', 'SendVerification', 'UseCustomMobileVerificationMessage', 'CustomMobileVerificationMessage' ) );

		// Return the contact id if successful
		return ( $this->success() ) ? $response->CONTACTID : false;
	}

    /**
     * Update Contact Data
     *
     * This function allows you to update an existing contact record. The ContactID must be provided
     * and this is the only way to identify which contact to update. Keep in mind that the same contact
     * record, a duplicate, can exist in multiple lists.
     *
     * NOTES: Currently distribution lists that a contact belongs to cannot be updated through the API.
     * Distribution list management must also be done online at trumpia.com.
     *
     * @param int $ContactID Unique ID of contact.
     * @param string $FirstName First name of the contact.
     * @param string $LastName [optional] Last name of the contact.
     * @param string $Email [optional] Email address of the contact.
     * @param int $CountryCode [optional|1]
     *      Mobile phone number's country code. If left blank then it will be assumed to be a
     *      US number.
     * @param string $MobileNumber [optional]
     *      For US, the 10 digit number without the leading 0 or 1. For some international numbers,
     *      the leading 0 or 1 must be omitted.
     * @param string $AIM [optional] AOL Instant Messenger screen name
     * @param string $MSN [optional] MSN Instant Messenger screen name must be an email address.
     * @param bool $SendVerification [optional|FALSE]
     *      TRUE or FALSE. If set to true, this sends a verification message to each tool before they
     *      are added to your distribution list. If the tool is not verified, they will be marked as
     *      not verified and cannot be used. If set to false, the verification step is bypassed and
     *      contacts will be added directly into your distribution list. A verification is omitted
     *      for an international phone number (not US).
     * @param bool $UseCustomMobileVerificationMessage [optional|FALSE]
     *      TRUE or FALSE. Send your custom verification message.
     * @param string $CustomMobileVerificationMessage [optional]
     *      If UseCustomMobileVerificationMessage is TRUE, this message will be sent for the
     *      verification message.
     *      Limit 60 characters.
     *      Default Verification Message : Reply OK to start. Msg&Data rates may apply. Upto 30msg/mo.
     * @return bool
     */
    public function update_contact_data( $ContactID, $FirstName, $LastName = '', $Email = '', $CountryCode = 1, $MobileNumber = '', $AIM = '', $MSN = '', $SendVerification = FALSE, $UseCustomMobileVerificationMessage = FALSE, $CustomMobileVerificationMessage = '' ) {
        // Format the bool values
        $this->_format_bools( array( &$SendVerification, &$UseCustomMobileVerificationMessage ) );

        // Execute the command
		$this->_execute( 'updatecontactdata', compact( 'ContactID', 'FirstName', 'LastName', 'Email', 'CountryCode', 'MobileNumber', 'AIM', 'MSN', 'SendVerification', 'UseCustomMobileVerificationMessage', 'CustomMobileVerificationMessage' ) );

        // Return Success
		return $this->success();
    }

    /**
     * Delete Contact
     *
     * This function deletes the contact from your contact list and any distribution lists it was on.
     *
     * @param int $ContactID Unique ID of contact.
     * @return bool
     */
    public function delete_contact( $ContactID ) {
        // Execute the command
		$this->_execute( 'deletecontact', compact( 'ContactID' ) );

        // Return Success
		return $this->success();
    }

    /**
     * Add Contact To List
     *
     * Adds an existing contact to a list using ContactID. You can copy the contact or use a pointer.
     * Contacts with duplicate tool data cannot be added to the same list.
     *
     * Setting the CreateCopy flag will create a new contact record and return a new ContactID. Adding
     * the same contact to multiple lists allow for single update of the contact's data.
     *
     * @param int $ContactID The contact to add.
     * @param string $ListName The list to add the contact to.
     * @param bool $CreateCopy [optional|FALSE] By default, the same contact record is linked to the
     *      new list. Setting this to TRUE will create a new contact record and return a ContactID.
     * @return bool
     */
    public function add_contact_to_list( $ContactID, $ListName, $CreateCopy = FALSE ) {
        // Format the bool values
        $this->_format_bools( array( &$CreateCopy ) );

        // Execute the command
		$response = $this->_execute( 'addcontacttolist', compact( 'ContactID', 'ListName', 'CreateCopy' ) );

		// Return the success
		return ( $CreateCopy && $this->success() ) ? $response->CONTACTID : $this->success();
    }

    /**
     * Get Contact Data
     *
     * This function returns the contact's information and tools registered along with the tools
     * verification status.
     *
     * @param int $ContactID Unique ID of contact.
     * @return array
     */
    public function get_contact_data( $ContactID ) {
        // Execute the command
		$response = $this->_execute( 'getcontactdata', compact( 'ContactID' ) );

        if ( $this->success() )
        $contact = array(
            'contact_code'                  => $response->CONTACTCODE
            , 'contact_data'                => array(
                'first_name'                    => $response->CONTACTDATA->FIRSTNAME
                , 'last_name'                   => $response->CONTACTDATA->LASTNAME
                , 'tools'                       => array(
                    'email'                        => $response->CONTACTDATA->TOOLS->EMAIL
                    , 'email_verified'             => (string) $response->CONTACTDATA->TOOLS->EMAIL->attributes()->VERIFY
                    , 'mobile_phone'               => $response->CONTACTDATA->TOOLS->MOBILEPHONE
                    , 'mobile_phone_verified'      => (string) $response->CONTACTDATA->TOOLS->MOBILEPHONE->attributes()->VERIFY
                )
            )
        );

		// Return the contact information if successful
		return ( $this->success() ) ? $contact : false;
    }

    /**
     * Get Contact ID
     *
     * This function can be used to find a contact record within a list with an email address, phone
     * number, or screen name. Only one contactID will be returned since a tool is unique within a list.
     *
     * Whenever possible, a contact's ContactID should be stored locally to avoid having to call this
     * function unnecessarily.
     *
     * @param string $ListName List to search in.
     * @param int $ToolType
     *      Email : 1
     *      Mobile Number : 2
     *      AIM : 6
     *      MSN : 7
     * @param string $ToolData
     *      Email address, mobile number, or screen name depending on ToolType selected.
     * @return int
     */
    public function get_contact_id( $ListName, $ToolType, $ToolData ) {
         // Execute the command
		$response = $this->_execute( 'getcontactid', compact( 'ListName', 'ToolType', 'ToolData' ) );

        // Return the contact id if successful
		return ( $this->success() ) ? $response->CONTACTID : false;
    }

    /**
     * Remove Contact
     *
     * This function removes a contact by the actual contact information instead of the ContactID. This
     * function is provided to easily remove subscriptions.
     *
     * NOTE: If you omit a listname and set removeall to true, then all subscriptions associated with
     * the tool will be deleted.
     *
     * @param string $tool Email address, mobile number, or screen name depending on ToolType selected.
     * @param int $tooltype
     *      Email : 1
     *      Mobile Number : 2
     *      AIM : 6
     *      MSN : 7
     * @param string $listname [optional]
     *      Distribution list name - If no name is specified then the tool will be removed from all
     *      lists.
     * @param bool $removeall [optional|false]
     *      By setting this flag to true, all other tools related to the specified tools will be
     *      deleted. In other words the whole subscription will be deleted.
     * @return bool
     */
    public function remove_contact( $tool, $tooltype, $listname, $removeall = FALSE ) {
        // Format the bool values
        $this->_format_bools( array( &$removeall ) );

        // Execute the command
		$this->_execute( 'removecontact', compact( 'tool', 'tooltype', 'listname', 'removeall' ) );

        // Return Success
		return $this->success();
    }

    /**
     * Send To List
     *
     * This function sends a message to all the contacts in the specified list. Email, SMS, IM or any
     * combination can be sent with one function call using the Mode flags. Multiple lists can be
     * selected by passing multiple list names separated by commas. Messages can also be scheduled for
     * a later time using the SendLater flag and LaterTime field.
     *
     * @param bool $EmailMode TRUE or FALSE.
     * @param bool $IMMode TRUE or FALSE.
     * @param bool $SMSMode TRUE or FALSE.
     * @param bool $SBMode TRUE or FALSE.
     * @param string $Description Description of message for your reference.
     * @param string $ListNames name1,name2,name3...
     * @param bool $SendLater TRUE or FALSE.
     * @param string $LaterTime [optional] YYYY-MM-DD HH:MM:SS (ex: 2009-04-22 14:10:32)
     * @param string $EmailSubject [optional] If EmailMode/SBMode is TRUE, Required - Plain text.
     * @param string $EmailMessage [optional]
     *      If EmailMode is TRUE, Required - Basic HTML supported for email contents.
     * @param string $IMMessage [optional]
     *      If IMMode is TRUE, Required - Plain text. Character limit is 500 characters.
     * @param string $SMSMessage [optional]
     *      If SMSMode is TRUE, Required - Plain text. Character limit is (132 - Lengths of
     *      Organization Name) characters.
     *
     *      Characters Allowed: [a-zA-Z0-9 ] and @!"#$%&'()*+,-.?/:;<=>
     * @param string $ChangeOrganizationName [optional]
     *      If SMSMode is TRUE, Optional - This one-time-use organization name will temporarily
     *      override.
     *          - mobile text message structure
     *              <Organization Name>: <your text message content> Reply STOP to cancel
     * @param string $SBMessage [optional] If SBMode is TRUE, Required - Plain text. Smart Blast.
     * @param string $AreaCode [optional] areacode1,areacode2,areacode3...
     * @param string $MailMergeFirstName [optional]
     *      Mail merge replacement for null values if email content contains mail merge variables.
     * @param string $MailMergeLastName [optional] See Mail Merge feature online in compose email.
     * @return bool
     */
    public function send_to_list( $EmailMode, $IMMode, $SMSMode, $SBMode, $Description, $ListNames, $SendLater, $LaterTime = '', $EmailSubject = '', $EmailMessage = '', $IMMessage = '', $SMSMessage = '', $ChangeOrganizationName = '', $SBMessage = '', $AreaCode = '', $MailMergeFirstName = '', $MailMergeLastName = '' ) {
        // Format the bool values
        $this->_format_bools( array( &$EmailMode, &$IMMode, &$SMSMode, &$SBMode, &$SendLater ) );

        // Execute the command
		$this->_execute( 'sendtolist', compact( 'EmailMode', 'IMMode', 'SMSMode', 'SBMode', 'Description', 'ListNames', 'SendLater', 'LaterTime', 'EmailSubject', 'EmailMessage', 'IMMessage', 'SMSMessage', 'ChangeOrganizationName', 'SBMessage', 'AreaCode', 'MailMergeFirstName', 'MailMergeLastName' ) );

        // Return Success
		return $this->success();
    }

    /**
     * Send To Contact
     *
     * This function allows sending a message to a single contact or several contacts using the
     * ContactID numbers.
     *
     * @param bool $EmailMode TRUE or FALSE.
     * @param bool $IMMode TRUE or FALSE.
     * @param bool $SMSMode TRUE or FALSE.
     * @param bool $SBMode TRUE or FALSE.
     * @param string $Description Description of message for your reference.
     * @param string $ContactIDs ContactID1,ContactID2,ContactID3...
     * @param string $EmailSubject [optional] If EmailMode/SBMode is TRUE, Required - Plain text.
     * @param string $EmailMessage [optional]
     *      If EmailMode is TRUE, Required - Basic HTML supported for email contents.
     * @param string $IMMessage [optional]
     *      If IMMode is TRUE, Required - Plain text. Character limit is 500 characters.
     * @param string $SMSMessage [optional]
     *      If SMSMode is TRUE, Required - Plain text. Character limit is (132 - Lengths of
     *      Organization Name) characters.
     *
     *      Characters Allowed: [a-zA-Z0-9 ] and @!"#$%&'()*+,-.?/:;<=>
     * @param string $ChangeOrganizationName [optional]
     *      If SMSMode is TRUE, Optional - This one-time-use organization name will temporarily
     *      override.
     *          - mobile text message structure
     *              <Organization Name>: <your text message content> Reply STOP to cancel
     * @param string $SBMessage [optional] If SBMode is TRUE, Required - Plain text. Smart Blast.
     * @param string $MailMergeFirstName [optional]
     *      Mail merge replacement for null values if email content contains mail merge variables.
     * @param string $MailMergeLastName [optional] See Mail Merge feature online in compose email.
     * @return bool
     */
    public function send_to_contact( $EmailMode, $IMMode, $SMSMode, $SBMode, $Description, $ContactIDs, $EmailSubject = '', $EmailMessage = '', $IMMessage = '', $SMSMessage = '', $ChangeOrganizationName = '', $SBMessage = '', $MailMergeFirstName = '', $MailMergeLastName = '' ) {
        // Format the bool values
        $this->_format_bools( array( &$EmailMode, &$IMMode, &$SMSMode, &$SBMode ) );

        // Execute the command
		$this->_execute( 'sendtocontact', compact( 'EmailMode', 'IMMode', 'SMSMode', 'SBMode', 'Description', 'ContactIDs', 'EmailSubject', 'EmailMessage', 'IMMessage', 'SMSMessage', 'ChangeOrganizationName', 'SBMessage', 'MailMergeFirstName', 'MailMergeLastName' ) );

        // Return Success
		return $this->success();
    }

    /**
     * Check Keyword
     *
     * This function returns whether a keyword is available or not.
     *
     * @param string $Keyword Keyword to check for availability.
     * @return bool
     */
    public function check_keyword( $Keyword ) {
        // Execute the command
		$response = $this->_execute( 'checkkeyword', compact( 'Keyword' ) );

        // Return if it's available
		if ( !$this->success() )
            return false;

        return 'AVAILABLE' == $response->ISAVAILABLE;
    }

    /**
     * Create Keyword
     *
     * @param string $Keyword The keyword to create. Check availability before creating.
     * @param string $ListNames name1,name2,name3...
     * @param bool $UserResponse
     *      TRUE or FALSE. This enables users to add a message or response after the keyword. This
     *      message will be sent to your Trumpia Inbox.
     * @param string $KeywordMessage
     *      The automated message to be sent to someone who texts in the keyword. Limit (132 - Lengths of Organization Name) characters.
     *
     *      Characters Allowed: [a-zA-Z0-9 ] and @!"#$%&'()*+,-.?/:;<=>
     * @param string $NotifyEmail [optional]
     *      Email address to where a notification will be sent when someone signs up via this keyword.
     * @param string $NotifyMobile [optional]
     *      Mobile phones to where a notification will be sent when someone signs up via this keyword.
     * @param bool $UseNotify [optional|FALSE]
     *      TRUE or FALSE. When someone texts in this keyword, send a notification.
     * @param bool $NotifyType1 [optional|FALSE]
     *      TRUE or FALSE. ( If UserResponse is TRUE and UseNotify is TRUE) Notify me when someone
     *      texts in this keyword without an optional message.
     * @param bool $NotifyType2 [optional|FALSE]
     *      TRUE or FALSE. ( If UserResponse is TRUE and UseNotify is TRUE and NotifyType1 is TRUE)
     *      only when the keyword subscriber's mobile number is new to my contact database
     * @param bool $NotifyType3 [optional|FALSE]
     *      TRUE or FALSE. ( If UserResponse is TRUE and UseNotify is TRUE) Notify me when someone
     *      texts in this keyword with an optional message (example: "RADIO Jingle Bells")
     * @param bool $NotifyType4 [optional|FALSE]
     *      TRUE or FALSE. ( If UserResponse is TRUE and UseNotify is TRUE and NotifyType3 is TRUE )
     *      only when the keyword subscriber's mobile number is new to my contact database
     * @param bool $NotifyType5 [optional|FALSE]
     *      TRUE or FALSE. TRUE or FALSE. ( If UserResponse is FALSE and UseNotify is TRUE) only when
     *      the keyword subscriber's mobile number is new to my contact database
     * @param int $SendAutoResponse [optional|2]
     *      1 - Only once per mobile number
     *      2 - Every time
     *      3 - Only once every minute
     *      4 - Only once every hour
     *      5 - Only once every day
     *      6 - Only once every week
     *      7 - Only once every month
     *      8 - Only once every year
     * @param bool $SendAlternate [optional|FALSE]
     *      TRUE or FALSE. ( If SendAutoResponse is not '1') Send an alternate Auto-responder after the
     *      first time. (Send this only if the subscriber already exists in your contact database.)
     * @param string $AlternateMessage [optional]
     *      Limit (132 - Lengths of Organization Name) characters.
     *
     *      Characters Allowed: [a-zA-Z0-9 ] and @!"#$%&'()*+,-.?/:;<=>
     * @param string $ChangeOrganizationName [optional]
     *      This Organization Name will override the default Organization Name in the KeywordMessage
     *      and AlternateMessage responses.
     * @return bool
     */
    public function create_keyword( $Keyword, $ListNames, $UserResponse, $KeywordMessage, $NotifyEmail = '', $NotifyMobile = '', $UseNotify = false, $NotifyType1 = FALSE, $NotifyType2 = FALSE, $NotifyType3 = FALSE, $NotifyType4 = FALSE, $NotifyType5 = FALSE, $SendAutoResponse = 2, $SendAlternate = FALSE, $AlternateMessage = '', $ChangeOrganizationName = '' ) {
        // Format the bool values
        $this->_format_bools( array( &$UserResponse, &$UseNotify, &$NotifyType1, &$NotifyType2, &$NotifyType3, &$NotifyType4, &$NotifyType5, &$SendAlternate ) );

        // Execute the command
		$this->_execute( 'createkeyword', compact( 'Keyword', 'ListNames', 'UserResponse', 'KeywordMessage', 'NotifyEmail', 'NotifyMobile', 'UseNotify', 'NotifyType1', 'NotifyType2', 'NotifyType3', 'NotifyType4', 'NotifyType5', 'SendAutoResponse', 'SendAlternate', 'AlternateMessage', 'ChangeOrganizationName' ) );

        // Return Success
        return $this->success();
    }

    /**
     * Update Keyword
     *
     * @param string $Keyword The keyword to update. This must be keyword setup on your account.
     * @param string $ListNames name1,name2,name3...
     * @param bool $UserResponse
     *      TRUE or FALSE. This enables users to add a message or response after the keyword. This
     *      message will be sent to your Trumpia Inbox.
     * @param string $KeywordMessage
     *      The automated message to be sent to someone who texts in the keyword. Limit (132 - Lengths of Organization Name) characters.
     *
     *      Characters Allowed: [a-zA-Z0-9 ] and @!"#$%&'()*+,-.?/:;<=>
     * @param string $NotifyEmail [optional]
     *      Email address to where a notification will be sent when someone signs up via this keyword.
     * @param string $NotifyMobile [optional]
     *      Mobile phones to where a notification will be sent when someone signs up via this keyword.
     * @param bool $UseNotify [optional|FALSE]
     *      TRUE or FALSE. When someone texts in this keyword, send a notification.
     * @param bool $NotifyType1 [optional|FALSE]
     *      TRUE or FALSE. ( If UserResponse is TRUE and UseNotify is TRUE) Notify me when someone
     *      texts in this keyword without an optional message.
     * @param bool $NotifyType2 [optional|FALSE]
     *      TRUE or FALSE. ( If UserResponse is TRUE and UseNotify is TRUE and NotifyType1 is TRUE)
     *      only when the keyword subscriber's mobile number is new to my contact database
     * @param bool $NotifyType3 [optional|FALSE]
     *      TRUE or FALSE. ( If UserResponse is TRUE and UseNotify is TRUE) Notify me when someone
     *      texts in this keyword with an optional message (example: "RADIO Jingle Bells")
     * @param bool $NotifyType4 [optional|FALSE]
     *      TRUE or FALSE. ( If UserResponse is TRUE and UseNotify is TRUE and NotifyType3 is TRUE )
     *      only when the keyword subscriber's mobile number is new to my contact database
     * @param bool $NotifyType5 [optional|FALSE]
     *      TRUE or FALSE. TRUE or FALSE. ( If UserResponse is FALSE and UseNotify is TRUE) only when
     *      the keyword subscriber's mobile number is new to my contact database
     * @param int $SendAutoResponse [optional|2]
     *      1 - Only once per mobile number
     *      2 - Every time
     *      3 - Only once every minute
     *      4 - Only once every hour
     *      5 - Only once every day
     *      6 - Only once every week
     *      7 - Only once every month
     *      8 - Only once every year
     * @param bool $SendAlternate [optional|FALSE]
     *      TRUE or FALSE. ( If SendAutoResponse is not '1') Send an alternate Auto-responder after the
     *      first time. (Send this only if the subscriber already exists in your contact database.)
     * @param string $AlternateMessage [optional]
     *      Limit (132 - Lengths of Organization Name) characters.
     *
     *      Characters Allowed: [a-zA-Z0-9 ] and @!"#$%&'()*+,-.?/:;<=>
     * @param string $ChangeOrganizationName [optional]
     *      This Organization Name will override the default Organization Name in the KeywordMessage
     *      and AlternateMessage responses.
     * @return bool
     */
    public function update_keyword( $Keyword, $ListNames, $UserResponse, $KeywordMessage, $NotifyEmail = '', $NotifyMobile = '', $UseNotify = false, $NotifyType1 = FALSE, $NotifyType2 = FALSE, $NotifyType3 = FALSE, $NotifyType4 = FALSE, $NotifyType5 = FALSE, $SendAutoResponse = 2, $SendAlternate = FALSE, $AlternateMessage = '', $ChangeOrganizationName = '' ) {
        // Format the bool values
        $this->_format_bools( array( &$UserResponse, &$UseNotify, &$NotifyType1, &$NotifyType2, &$NotifyType3, &$NotifyType4, &$NotifyType5, &$SendAlternate ) );

        // Execute the command
		$this->_execute( 'updatekeyword', compact( 'Keyword', 'ListNames', 'UserResponse', 'KeywordMessage', 'NotifyEmail', 'NotifyMobile', 'UseNotify', 'NotifyType1', 'NotifyType2', 'NotifyType3', 'NotifyType4', 'NotifyType5', 'SendAutoResponse', 'SendAlternate', 'AlternateMessage', 'ChangeOrganizationName' ) );

        // Return Success
        return $this->success();
    }

    /**
     * Delete Keyword
     *
     * This function deletes the specified keyword from your account.
     *
     * @param string $Keyword The mobile keyword you wish to delete.
     * @return bool
     */
    public function delete_keyword( $Keyword ) {
        // Execute the command
		$this->_execute( 'deletekeyword', compact( 'Keyword' ) );

        // Return Success
		return $this->success();
    }

    /**
     * Create List
     *
     * This function creates a list.
     *
     * @param string $ListName
     *      A list name you wish to create. Only alphanumeric characters are allowed. case insensitive.
     *      The maximum length for a list name is 32 characters.
     * @param string $DisplayName
     *      The display name will be the name that your contacts will see when signing up for your
     *      distribution lists. The public will be able to see this name, so make sure it will make
     *      sense to your future contacts.
     * @param int $Frequency
     *      The frequency of messages your subscribers receive each month must be disclosed by mobile
     *      industry regulations.
     * @param string $Description
     *      The description of the nature of messages your subscribers will receive when opted into
     *      this list must be disclosed.
     * @return bool
     */
    public function create_list( $ListName, $DisplayName, $Frequency, $Description ) {
        // Execute the command
		$this->_execute( 'createlist', compact( 'ListName', 'DisplayName', 'Frequency', 'Description' ) );

        // Return Success
		return $this->success();
    }

    /**
     * Rename List
     *
     * This function renames an existing distribution list.
     *
     * @param string $ListName The distribution list to be renamed.
     * @param string $NewListName The new name the distribution list will be changed to.
     * @param string $DisplayName
     *      The display name will be the name that your contacts will see when signing up for your
     *      distribution lists. The public will be able to see this name, so make sure it will make
     *      sense to your future contacts.
     * @param int $Frequency
     *      The frequency of messages your subscribers receive each month must be disclosed by mobile
     *      industry regulations.
     * @param string $Description
     *      The description of the nature of messages your subscribers will receive when opted into
     *      this list must be disclosed.
     * @return bool
     */
    public function rename_list( $ListName, $NewListName, $DisplayName, $Frequency, $Description ) {
        // Execute the command
		$this->_execute( 'renamelist', compact( 'ListName', 'NewListName', 'DisplayName', 'Frequency', 'Description' ) );

        // Return Success
		return $this->success();
    }

    /**
     * Delete List
     *
     * This function deletes a list.
     *
     * @param string $ListName The list name you wish to delete.
     * @param bool $DeleteContact [optional|FALSE]
     *      TRUE or FALSE. Entire contact in entered list will deleted. After doing this, you can't restore data.
     * @return bool
     */
    public function delete_list( $ListName, $DeleteContact = FALSE ) {
        // Format the bool values
        $this->_format_bools( array( &$DeleteContact ) );

        // Execute the command
		$this->_execute( 'deletelist', compact( 'ListName', 'DeleteContact' ) );

        // Return Success
		return $this->success();
    }

	/****************************/
	/* END: Trumpia API Methods */
	/****************************/

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message() {
        return $this->message;
    }

    /**
     * Get private success variable
     *
     * @return string
     */
    public function success() {
        return $this->success;
    }

    /**
     * Get private raw_request variable
     *
     * @return string
     */
    public function raw_request() {
        return $this->raw_request();
    }

    /**
     * Get private request variable
     *
     * @return array Object
     */
    public function request() {
        return $this->request;
    }

    /**
     * Get private raw_response variable
     *
     * @return string
     */
    public function raw_response() {
        return $this->raw_response;
    }

    /**
     * Get private response variable
     *
     * @return stdClass Object
     */
    public function response() {
        return $this->response;
    }

    /**
     * Get private error variable
     *
     * @return string
     */
    public function error() {
        return $this->error;
    }

    /**
     * Format Boolean values
     *
     * @param array $arguments
     * @return void
     */
    private function _format_bools( $arguments ) {
        foreach ( $arguments as &$a ) {
            $a = ( true === $a ) ? 'TRUE' : 'FALSE';
        }
    }

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @param string $method The method being called
	 * @param array $params an array of the parameters to be sent
     * @return stdClass object
	 */
	private function _execute( $method, $params = array() ) {
		if( empty( $this->api_key ) ) {
			$this->error = 'Cannot send request without an API Key.';
			$this->success = false;
            return false;
		}

        // Set the API Key
        $params['APIKEY'] = $this->api_key;

		$this->request = $params;
        $this->raw_request = http_build_query( $this->request );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::URL_API . "$method.php" );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->raw_request );
		curl_setopt( $ch, CURLOPT_POST, 1 );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

        // Decode the response
        $this->response = simplexml_load_string( $this->raw_response );

        curl_close($ch);

        // Set the response
        $this->success = '1' == $this->response->STATUSCODE;

        // Get any helpful debugging information
        if ( $this->success ) {
            $this->message = $this->response->MESSAGE;
            $this->error = NULL;
        } else {
            $this->message = $this->response->ERRORMESSAGE;
            $this->error = $this->response->ERRORCODE;
        }

        // If we're debugging lets give as much info as possible
        if ( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>", self::URL_API, "$method.php</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

		return $this->response;
	}
}