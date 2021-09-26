<?php

namespace gdhome\Db;
use PDO, PDOException;

class Db {
    private static $instance = NULL;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
      if (!isset(self::$instance)) {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        self::$instance = new PDO('mysql:host='.\gdhome\DB_Info::HOST.';dbname='.\gdhome\DB_Info::DATABASE,\gdhome\DB_Info::USERNAME, \gdhome\DB_Info::PASSWORD, $pdo_options);
        //self::$instance = new PDO('mysql:host=fdb3.awardspace.com;dbname=1171664_whome', '1171664_whome', 'digimon1234', $pdo_options);
      }
      return self::$instance;
    }
  }
