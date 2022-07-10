<?php
  namespace App\Database;
  require 'Config.php';

//  require dirname(__FILE__).'/../.settings/Config.php';

  class Connection {
    public $dbh;
    private static $instance;

    private function __construct() {
        $dsn = 'mysql:host=' . Config::read('db.host').
               ';dbname='    . Config::read('db.basename');

        $user = Config::read('db.user');
        $password = Config::read('db.password');

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
