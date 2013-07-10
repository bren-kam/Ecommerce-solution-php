<?php
/**
 * Active Campaign - List -  API Library
 *
 * Library based on documentation available on 07/03/2013 from
 * @url http://www.activecampaign.com/api/overview.php
 *
 */

class ActiveCampaignListAPI {
    const PREFIX = 'list_';

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

    /*******************************************/
    /* Start: Active Campaign List API Methods */
    /*******************************************/

    /**
     * Add
     *
     * @return int
     *
     * @param string $name
     * @param int $analytics_ua
     * @param string $domain
     * @param string $account_name
     * @param string $address
     * @param string $city
     * @param string $state
     * @param int $zip
     * @return int
     */
    public function add( $name, $analytics_ua, $domain, $account_name, $address, $city, $state, $zip ) {
        $result = $this->api( 'add', array(
            'name' => $name
            , 'to_name' => 'Subscriber'
            , 'listid' => format::slug( $name )
            , 'p_use_analytics_read' => 1
            , 'analytics_ua' => $analytics_ua
            , 'p_use_analytics_link' => 1
            , 'analytics_domains[0]' => $domain
            , 'get_unsubscribe_reason' => 1
            , 'send_last_broadcast' => 0
            , 'require_name' => 0
            , 'sender_name' => $account_name
            , 'sender_addr1' => $address
            , 'sender_city' => $city
            , 'sender_state' => $state
            , 'sender_zip' => $zip
            , 'sender_country' => 'USA'
        ), ActiveCampaignAPI::REQUEST_TYPE_POST );

        return $result->id;
    }

    /**
     * List
     *
     * @return stdClass object
     */
    public function list_all() {
        return $this->api( 'list', array(
            'ids' => 'all'
        ) );
    }

    /**
     * Delete Multiple
     *
     * @param array $ac_list_ids
     * @return bool
     */
    public function delete_multiple( array $ac_list_ids ) {
        $this->api( 'delete_list', array(
            'ids' => implode( ',', $ac_list_ids )
        ));

        return $this->ac->success();
    }

    /*****************************************/
    /* End: Active Campaign List API Methods */
    /*****************************************/

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