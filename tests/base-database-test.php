<?php

require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * Base classe for all tests that needs to connect to Database
 */
abstract class BaseDatabaseTest extends PHPUnit_Extensions_Database_TestCase {

    private static $pdo = null;

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

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet() {
        // How does this work?
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/guestbook-seed.xml');
    }

}
