<?php
class PingdomController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();
    }

    /**
     * Main Status check
     *
     * @return XmlResponse
     */
    protected function main() {
        // Start timer
        $timer = new Timer();

        // Create XML
        $xml = new SimpleXMLElement('<pingdom_http_custom_check/>');
        $xml->addChild( 'status', 'OK' );
        $xml->addChild( 'response_time', round( $timer->stop() * 1000000, 3 )  );

        // Spit out XML
        return new XmlResponse( $xml->asXML() );
    }

    /**
     * Status check for the Master DB
     *
     * @return XmlResponse
     */
    protected function db_master() {
        // Start timer
        $timer = new Timer();

        // Check master
        require '/gsr/systems/db.master.php';

        try {
            new PDO( "mysql:host=$db_host;dbname=$db_name", $db_username, $db_password );
            $message = 'OK';
        } catch( PDOException $e ) {
            $message = 'FAIL';
        }

        // Create XML
        $xml = new SimpleXMLElement('<pingdom_http_custom_check/>');
        $xml->addChild( 'status', $message );
        $xml->addChild( 'response_time', round( $timer->stop() * 1000000, 3 )  );

        // Spit out XML
        return new XmlResponse( $xml->asXML() );
    }

    /**
     * Status check for the Slave DB
     *
     * @return XmlResponse
     */
    protected function db_slave() {
        // Start timer
        $timer = new Timer();

        // Check slave
        require '/gsr/systems/db.slave.php';

        try {
            new PDO( "mysql:host=$db_host;dbname=$db_name", $db_username, $db_password );
            $message = 'OK';
        } catch( PDOException $e ) {
            $message = 'FAIL';
        }

        // Create XML
        $xml = new SimpleXMLElement('<pingdom_http_custom_check/>');
        $xml->addChild( 'status', $message );
        $xml->addChild( 'response_time', round( $timer->stop() * 1000000, 3 )  );

        // Spit out XML
        return new XmlResponse( $xml->asXML() );
    }
}