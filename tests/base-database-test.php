<?php

require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'PHPUnit/Extensions/Database/DataSet/QueryDataSet.php';

define('LIB_PATH', realpath( $_SERVER['DOCUMENT_ROOT'] . '../' ) . '/lib/' );

// Need registry for Database
require LIB_PATH . 'helpers/registry.php';

// DB class for helping
require LIB_PATH . 'test/db.php';

/**
 * Base classe for all tests that needs to connect to Database
 */
abstract class BaseDatabaseTest extends PHPUnit_Extensions_Database_TestCase {
    /**
     * Hold the database variable
     * @var DB
     */
    protected $db;

    private static $pdo = null;

    /**
     * Initialize DB
     */
    public function __construct() {
        $this->db = new DB();
    }

    /**
     * Retrieve a valid database connection
     * @override
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection() {
        if ( self::$pdo == null ) {
            self::$pdo = new PDO(
                  'mysql:host=' . ActiveRecordBase::DB_HOST
                , ActiveRecordBase::DB_USER, ActiveRecordBase::DB_PASSWORD
            );
        }

        return $this->createDefaultDBConnection( self::$pdo, ActiveRecordBase::DB_NAME );
    }

    public function getDataSet() {
        return new PHPUnit_Extensions_Database_DataSet_QueryDataSet( $this->getConnection() );
    }
    
}

/**
 * Load a model
 *
 * @var string $model
 */
function load_model( $model ) {
    // Form the model name, i.e., AccountListing to account-listing.php
    $model_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $model ) ) . '.php', 1 );

    // Define the paths to search
    $paths = array( MODEL_PATH, LIB_PATH . 'models/' );

    // Loop through each path and see if it exists
    foreach ( $paths as $path ) {
        $full_path = $path . $model_file;

        if ( is_file( $full_path ) ) {
            require_once $full_path;
            break;
        }
    }
}
spl_autoload_register( 'load_model' );

/**
 * Load a model
 *
 * @var string $model
 */
function load_response( $response ) {
    if ( !stristr( $response, 'Response' ) )
        return;

    // Form the model name, i.e., AccountListing to account-listing.php
    $response_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $response ) ) . '.php', 1 );

    $full_path = LIB_PATH . 'responses/' . $response_file;

    if ( is_file( $full_path ) )
        require_once $full_path;
}

spl_autoload_register( 'load_response' );