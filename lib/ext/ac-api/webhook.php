<?php
/**
 * Active Campaign - Webhook - API Library
 *
 * Library based on documentation available on 07/03/2013 from
 * @url http://www.activecampaign.com/api/overview.php
 *
 */

class ActiveCampaignWebhookAPI {
    const PREFIX = 'webhook_';

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
    /* Start: Active Campaign Webhook API Methods */
    /**********************************************/

    /**
     * Add
     *
     * @param string $name
     * @param string $url
     * @param array|string $action_array [subscribe, unsubscribe, update, sent, open, click, forward, share, bounce]
     * @param array|string $init_array
     * @return bool
     */
    public function add( $name, $url, $action_array, $init_array ) {
        $params = array(
            'name' => $name
            , 'url' => $url
        );

        // Transfer into array
        if ( is_string( $init_array ) )
            $init_array = array( $init_array );

        $init_count = count( $init_array );

        for ( $i = 0; $i < $init_count; $i++ ) {
            $init = array_pop( $init_array );
            $params["init[$i]"] = $init;
        }

        // Transfer into array
        if ( is_string( $action_array ) )
            $action_array = array( $action_array );

        $action_count = count( $action_array );

        for ( $i = 0; $i < $action_count; $i++ ) {
            $action = array_pop( $action_array );
            $params["action[$i]"] = $action;
        }

        // Add it
        $this->api( 'add', $params, ActiveCampaignAPI::REQUEST_TYPE_POST );

        return $this->ac->success();
    }

    /********************************************/
    /* End: Active Campaign Webhook API Methods */
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