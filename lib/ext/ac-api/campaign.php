<?php
/**
 * Active Campaign - Campaign - API Library
 *
 * Library based on documentation available on 07/03/2013 from
 * @url http://www.activecampaign.com/api/overview.php
 *
 */

class ActiveCampaignCampaignAPI {
    const PREFIX = 'campaign_';
    const STATUS_DRAFT = 0;
    const STATUS_SCHEDULED = 1;
    const VISIBILITY_PUBLIC = 1;
    const VISIBILITY_PRIVATE = 0;

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

    /***********************************************/
    /* Start: Active Campaign Campaign API Methods */
    /***********************************************/


    /**
     * Create
     *
     * @param int $ac_message_id
     * @param string $subject
     * @param string $date yyyy-mm-dd hh:mm:ss
     * @param array $ac_list_ids
     * @param int $status [optional]
     * @return int
     */
    public function create( $ac_message_id, $subject, $date, array $ac_list_ids, $status = self::STATUS_DRAFT ) {
        $params = array(
            'type' => 'single' // 'single', 'recurring', 'split', 'responder', 'reminder', 'special', 'activerss', 'text'
            , 'filterid' => 0 // 0 for no segment
            , 'bounceid' => -1 // -1 = use all available bounce accounts, 0 = don't use bounce management, or ID of a bounce account
            , 'name' => $subject
            , 'sdate' => $date
            , 'status' => $status
            , 'visibility' => self::VISIBILITY_PUBLIC
            , 'tracklinks' => 'all'
            , 'tracklinksanalytics' => 1
            , 'trackreads' => 1
            , 'trackreadsanalytics' => 1
            , 'trackreplies' => 1
            , 'analytics_campaign_name' => $subject
            , 'embed_images' => 1
            , 'htmlunsub' => 1
            , 'textunsub' => 1
            , "m[$ac_message_id]" => 100 // Send this message ID
        );

        foreach ( $ac_list_ids as $ac_list_id ) {
            $ac_list_id = (int) $ac_list_id;
            $params["p[$ac_list_id]"] = $ac_list_id;
        }

        $result = $this->api( 'create', $params, ActiveCampaignAPI::REQUEST_TYPE_POST );

        return $result->id;
    }

    /*********************************************/
    /* End: Active Campaign Campaign API Methods */
    /*********************************************/

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