<?php
  namespace App\Database;

  require_once 'Connection.php';
  use App\Database\Connection;

  class Title {
    static function create ($data) {

    }

    static function getAll() {
      $sql = "select titels.titel, titels.id, count(citaten.id) as aantal, 
              concat(auteurs.voornaam, ' ', auteurs.achternaam) as auteur from titels 
              join citaten on titels.id=citaten.titel_id 
              join auteurs  on titels.auteur_id=auteurs.id                                              
              group by titels.id order by titels.titel";
      try {
        $db = Connection::getInstance();
        $stmt = $db->dbh->prepare($sql);
        $stmt->execute();
        $rv = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rv;
      } catch (\PDOException $e) {
        print_r ($stmt->errorInfo());
      }
    }

    static function find($p_id) { 
      $sql = "select * from titels where id=:id";
      try { 
        $db = Connection::getInstance();
        $stmt = $db->dbh->prepare($sql);
        $stmt->bindParam(':id', $p_id);
        $stmt->execute();
        $obj = $stmt->fetch(\PDO::FETCH_OBJ);
        return $obj;
      } catch (\PDOException $e) {
        print $e->getMessage();
      } 
    
    }


    static function update($cmd_id, $cmd) {
    }

    function delete($cmd_id) {
    }

  }

/**
ysql> desc titles;
+--------+------------------+------+-----+---------+----------------+
| Field  | Type             | Null | Key | Default | Extra          |
+--------+------------------+------+-----+---------+----------------+
| id     | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| auteur | varchar(128)     | YES  |     | NULL    |                |
| titel  | varchar(128)     | YES  |     | NULL    |                |
+--------+------------------+------+-----+---------+----------------+
3 rows in set (0,02 sec)

mysql>
**/
