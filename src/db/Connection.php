<?php
  namespace App\Database;

  class Connection {
    public $dbh;
    private static $instance;

    private function __construct() {
        $dsn = 'mysql:host=127.0.0.1;dbname=citaten' ;
        $user = 'citaten';
        $password = 'hoppekee';

        $this->dbh = new \PDO($dsn, $user, $password);
    }


    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }

        return self::$instance;
    }
}
