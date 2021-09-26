<?php
namespace gdhome\Tests\Persistence;

use gdhome\Db\MysqlAdapter;

abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    // only instantiate adapter once per test
    static private $adapter = null;

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    final public function getAdapter()
    {
        if (self::$adapter == null) {
            self::$adapter = new MysqlAdapter( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            //self::$adapter = new MysqlAdapter( 'sqlite:' . __DIR__ . '/../whome.db', $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            //self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
             
       /* $this->db = new PDO('sqlite:' . __DIR__ . '/../whome.db');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->createDefaultDBConnection($this->db, 'testdb');*/
        }

        return self::$adapter;
    }

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                $dbAdapter = $this->getAdapter();
                self::$pdo = $dbAdapter->getConnection();
            }
            $this->conn = $this->createDefaultDBConnection( self::$pdo );
        }

        return $this->conn;
        

    }
    
    final public function cleanTables($tableName){
    	$adapter=$this->getAdapter();
    	$adapter->truncate($tableName);
    
    }
    
  
}