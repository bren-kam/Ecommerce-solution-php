<?php

require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * Base classe for all tests that needs to connect to Database
 */
class BaseDatabaseTest extends PHPUnit_Extensions_Database_TestCase {

    private static $pdo = null;

    /**
     * Retrieve a valid database connection
     * @override
     */
    final public function getConnection() {
        if ( self::$pdo == null ) {
            self::$pdo = new PDO(
                  'mysql:host=' . ActiveRecordBase::DB_HOST
                , ActiveRecordBase::DB_USER, ActiveRecordBase::DB_PASSWORD
            );
        }
        return $this->createDefaultDBConnection(self::$pdo, ActiveRecordBase::DB_NAME);
    }

}
