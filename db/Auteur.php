<?php

namespace App\Database;
require_once('Connection.php');

class Auteur {
    static function getAll() {
        $sql = "select * from auteurs order by achternaam";
        try {
            $db = Connection::getInstance();
            $stmt = $db->dbh->prepare($sql);
            $stmt->execute();
            $obj = $stmt->fetchAll(\PDO::FETCH_OBJ);
            return $obj;
          } catch (PDOException $e) {
            print $e->getMessage();
        }
    }
}