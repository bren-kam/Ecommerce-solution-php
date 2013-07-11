<?php
/**
 * Active Campaign - Message - API Library
 *
 * Library based on documentation available on 07/03/2013 from
 * @url http://www.activecampaign.com/api/overview.php
 *
 */

class ActiveCampaignMessageAPI {
    const PREFIX = 'message_';
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 3;
    const PRIORITY_HIGH = 5;

    /**
     * @var ActiveCampaignApi $ac
     */
    protected $ac;

	/**
	 * Construct class will initiate and run everything
     *
     * @param ActiveCampaignApi $ac
	 */
	public function __construct( ActiveCampaignAPI $ac ) {
        $this->ac = $ac;
	}

    /**********************************************/
    /* Start: Active Campaign Message API Methods */
    /**********************************************/

    /**
     * Add
     *
     * @param string $subject
     * @param string $from_email
     * @param string $from_name
     * @param string $reply_to
     * @param string $html
     * @param string $text
     * @param array $ac_list_ids
     * @return int
     */
    public function add( $subject, $from_email, $from_name, $reply_to, $html, $text, array $ac_list_ids ) {
        $params = array(
            'format' => 'html' // 'html', 'text', 'mime' (both)
            , 'subject' => $subject
            , 'fromemail' => $from_email
            , 'fromname' => $from_name
            , 'reply2' => $reply_to
            , 'priority' => self::PRIORITY_MEDIUM
            , 'charset' => 'utf-8'
            , 'encoding' => 'quoted-printable'
            , 'htmlconstructor' => 'editor'
            , 'html' => $html
            , 'textconstructor' => 'editor'
            , 'text' => $text
        );

        foreach ( $ac_list_ids as $ac_list_id ) {
            $ac_list_id = (int) $ac_list_id;
            $params["p[$ac_list_id]"] = $ac_list_id;
        }

        $result = $this->api( 'add', $params, ActiveCampaignAPI::REQUEST_TYPE_POST );

        return $result->id;
    }

    /**
     * Edit
     *
     * @param int $mc_message_id
     * @param string $subject
     * @param string $from_email
     * @param string $from_name
     * @param string $reply_to
     * @param string $html
     * @param string $text
     * @param array $ac_list_ids
     * @return bool
     */
    public function edit( $mc_message_id, $subject, $from_email, $from_name, $reply_to, $html, $text, array $ac_list_ids ) {
        $params = array(
            'id' => $mc_message_id
            , 'format' => 'html' // 'html', 'text', 'mime' (both)
            , 'subject' => $subject
            , 'fromemail' => $from_email
            , 'fromname' => $from_name
            , 'reply2' => $reply_to
            , 'priority' => self::PRIORITY_MEDIUM
            , 'charset' => 'utf-8'
            , 'encoding' => 'quoted-printable'
            , 'htmlconstructor' => 'editor'
            , 'html' => $html
            , 'textconstructor' => 'editor'
            , 'text' => $text
        );

        foreach ( $ac_list_ids as $ac_list_id ) {
            $ac_list_id = (int) $ac_list_id;
            $params["p[$ac_list_id]"] = $ac_list_id;
        }

        $this->api( 'edit', $params, ActiveCampaignAPI::REQUEST_TYPE_POST );

        return $this->ac->success();
    }

    /**
     * Delete
     *
     * @param int $ac_message_id
     * @return bool
     */
    public function delete( $ac_message_id ) {
        $this->api( 'delete', array(
            'id' => $ac_message_id
        ));

        return $this->ac->success();
    }

    /********************************************/
    /* End: Active Campaign Message API Methods */
    /********************************************/

    /**
     * API
     *
     * @param string $method
     * @param $params [optional]
	 * @param int $request_type
     * @return stdClass object
     */
    protected function api( $method, $params = array(), $request_type = ActiveCampaignAPI::REQUEST_TYPE_GET ) {
        return $this->ac->execute( self::PREFIX . $method, $params, $request_type );
    }
}