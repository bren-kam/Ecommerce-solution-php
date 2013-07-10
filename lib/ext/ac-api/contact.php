<?php
/**
 * Active Campaign - Contact - API Library
 *
 * Library based on documentation available on 07/03/2013 from
 * @url http://www.activecampaign.com/api/overview.php
 *
 */

class ActiveCampaignContactAPI {
    const PREFIX = 'contact_';
    const STATUS_SUBSCRIBED = 1;
    const STATUS_UNSUBSCRIBED = 2;
    const NO = 0;
    const YES = 1;

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
    /* Start: Active Campaign Contact API Methods */
    /**********************************************/

    /**
     * Add
     *
     * @return int
     *
     * @param string $email
     * @param string $name
     * @param array $email_list_ids
     * @param int $status [optional]
     * @return bool
     */
    public function sync( $email, $name, $email_list_ids, $status = self::STATUS_SUBSCRIBED ) {
        $params = array(
            'email' => $email
            , 'first_name' => $name
            , 'last_name' => ''
        );

        foreach ( $email_list_ids as $email_list_id ) {
            $email_list_id = (int) $email_list_id;
            $params["status[$email_list_id]"] = $status;
            $params["p[$email_list_id]"] = $email_list_id;
        }

        $this->api( 'sync', $params, ActiveCampaignAPI::REQUEST_TYPE_POST );

        return $this->ac->success();
    }

    /********************************************/
    /* End: Active Campaign Contact API Methods */
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