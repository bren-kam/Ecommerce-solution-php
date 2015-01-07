<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 09/12/14
 * Time: 17:01
 */

class YEXT {

    const OFFER_ID = 1756;
    public static $SUBSCRIPTION_ID = 680224;

    public static $customer_id = 639457;
    public static $url = 'https://api.yext.com/v1/';
    public static $base_service = 'customers/639457/';
    public static $api_key = 'PmHIbdjdLcUViKfge8LU';

    public $last_response_code;

    /**
     * @var Account $account
     */
    public $account;

    public function __construct( Account $account ) {
        $this->account = $account;
    }

    /**
     * Get
     *
     * @param $service
     * @param $data
     * @return stdObject
     */
    public function get( $service, $data = [] ) {
        return $this->_request( 'get', $service, $data );
    }

    /**
     * Post
     *
     * @param $service
     * @param $data
     * @return stdObject
     */
    public function post( $service, $data = [] ) {
        return $this->_request( 'post', $service, $data );
    }

    /**
     * Put
     *
     * @param $service
     * @param $data
     * @return stdObject
     */
    public function put( $service, $data = [] ) {
        return $this->_request( 'put', $service, $data );
    }

    /**
     * Delete
     *
     * @param $service
     * @param $data
     * @return stdObject
     */
    public function delete( $service, $data = [] ) {
        return $this->_request( 'delete', $service, $data );
    }

    /**
     * Report
     *
     * @param  string[] $metrics
     * @param  string[] $dimensions
     * @param  string $date_start
     * @param  string $date_end
     * @return array[]
     */
    public function report( $metrics, $dimensions, $date_start, $date_end ) {
        $base_service = self::$base_service;
        self::$base_service = '';
        $try_count = 0;
        $report = null;

        try {
            $report_key_response = $this->_request( 'get', 'powerlistings/startReport', [
                'format' => 'json'
                , 'customerId' => self::$customer_id
                , 'metrics' => implode(',', $metrics)
                , 'dimensions' => implode(',', $dimensions)
                , 'dateStart' => $date_start
                , 'dateEnd' => $date_end
            ]);

            if ( !isset( $report_key_response->key ) ) {
                throw new Exception( 'Could not get Report Key' );
            }

            $report_key = $report_key_response->key;

            while ( true ) {
                $response = $this->_request( 'get', 'powerlistings/getReport', [
                    'format' => 'json'
                    , 'key' => $report_key
                ]);

                if ( $response->status == 'done' ) {
                    $report = $response->data;
                    break;
                }

                if ( $response->status == 'failed') {
                    throw new Exception( 'Report Failed' );
                }

                if ( $try_count > 30 ) {
                    throw new Exception( 'Get Report Time Out' );
                }

                $try_count++;
                sleep( 1 );
            }
        } catch ( Exception $e ) {
            /* Do nothing */
        }

        self::$base_service = $base_service;
        return $report;
    }

    /**
     * Request
     *
     * @param $method
     * @param $service
     * @param $data
     * @return stdObject
     */
    private function _request( $method, $service, $data ) {

        $parameters = [ 'api_key' => self::$api_key ];
        if ( $method == 'get') {
            $parameters = array_merge( $parameters, $data );
            $request = [];
        } else {
            $request = $data;
        }
        $url = self::$url . self::$base_service . $service . '?' . http_build_query( $parameters );
        $json_request = json_encode($request);

        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url );
        if ( $method == 'get' ) {
            // do nothing
        } else if ( $method == 'post' ) {
            curl_setopt( $curl, CURLOPT_POST, true );
            if ( $json_request ) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $json_request);
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json;charset=UTF-8']);
            }
        } else {
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, strtoupper( $method ) );
            if ( $json_request ) {
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $json_request );
                curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json;charset=UTF-8' ] );
            }
        }
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );

        $json_response = curl_exec( $curl );
        $response = json_decode( $json_response );

        $this->last_response_code  = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

        $api_log = new ApiExtLog();
        $api_log->website_id = $this->account->id;
        $api_log->api = 'YEXT';
        $api_log->method = $method . ' ' . $service;
        $api_log->url = $url;
        $api_log->request = json_encode( $request );
        $api_log->raw_request = $json_request;
        $api_log->response = json_encode( $response );
        $api_log->raw_response = $this->last_response_code . '|' . $json_response;
        $api_log->create();

        return $response;
    }

}
