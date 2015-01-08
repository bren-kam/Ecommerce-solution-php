<?php

// require_once 'PHPUnit/Extensions/Database/TestCase.php';
// require_once 'PHPUnit/Extensions/Database/DataSet/QueryDataSet.php';

define( 'ABS_PATH', realpath( $_SERVER['DOCUMENT_ROOT'] ) . '/' );
define( 'LIB_PATH', ABS_PATH . 'lib/' );
date_default_timezone_set('America/Chicago');

// Need registry for Database
require LIB_PATH . 'helpers/registry.php';

// Need to load libraries for stubbing
require LIB_PATH . 'misc/functions.php';

// DB class for helping
require_once LIB_PATH . 'ext/Phactory/autoload.php';

/**
 * Base classe for all tests that needs to connect to Database
 */
abstract class BaseDatabaseTest extends PHPUnit_Extensions_Database_TestCase {
    // Website ID
    const WEBSITE_ID = 1352;

    /**
     * Hold the database variable
     * @var Phactory\Sql\Phactory
     */
    protected $phactory;

    private static $pdo = null;

    /**
     * Initialize DB
     */
    public function __construct() {
        parent::__construct();

        $pdo = Registry::get('pdo_master');

        if ( !$pdo ) {
            try {
                if ( !isset( $_SERVER['WERCKER_MYSQL_HOST'] ) )
                    throw new Exception('Not on Wercker box');

                $pdo = new PDO( "mysql:host=" . $_SERVER['WERCKER_MYSQL_HOST'] . ";dbname=" . $_SERVER['WERCKER_MYSQL_DATABASE'], $_SERVER['WERCKER_MYSQL_USERNAME'], $_SERVER['WERCKER_MYSQL_PASSWORD'] );
                $pdo->exec( file_get_contents('test/db-schema.sql') );
            } catch ( Exception $e) {
                $pdo = new PDO( "mysql:host=127.0.0.1;dbname=test", "root" );
            }

            Registry::set( 'pdo_master', $pdo );
            Registry::set( 'pdo_slave', $pdo );
        }

        $this->phactory = new Phactory\Sql\Phactory( $pdo );
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
if ( !function_exists( 'load_model' ) ) {
    function load_model( $model ) {
        // Form the model name, i.e., AccountListing to account-listing.php
        $model_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $model ) ) . '.php', 1 );

        // Define the paths to search
        $paths = array( LIB_PATH . 'models/' );

        if ( isset( $_SERVER['MODEL_PATH'] ) )
            array_unshift( $paths, ABS_PATH . 'model/' . $_SERVER['MODEL_PATH'] . '/' );

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
}

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

/**
 * Load an exception
 *
 * @var string $exception
 */
function load_exception( $exception ) {
    if ( !stristr( $exception, 'Exception' ) )
        return;

    // Form the model name, i.e., AccountListing to account-listing.php
    $exception_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $exception ) ) . '.php', 1 );

    $full_path = LIB_PATH . 'exceptions/' . $exception_file;

    if ( is_file( $full_path ) )
        require_once $full_path;
}

spl_autoload_register( 'load_exception' );