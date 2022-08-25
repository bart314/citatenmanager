<?php
  namespace App\Database;
  require_once 'Connection.php';

  class Quote { 
    
    static function create ($quote, $titel_id) {
      $quote = rtrim($quote);
      if ($quote=="") return 0;
      $txt_ar = Quote::get_text_and_pagenumber($quote);

      $sql = "insert into citaten (titel_id, citaat, pagina) values (:titel_id, :quote, :pagina)";
      try { 
        $db = Connection::getInstance();
        $stmt = $db->dbh->prepare($sql);
        $stmt->bindParam(':titel_id', $titel_id);
        $stmt->bindParam(':quote', $txt_ar['quote']);
        $stmt->bindParam(':pagina', $txt_ar['page']);
        $stmt->execute();
        return $stmt->rowCount();
      } catch (PDOException $e) {
        return $e->getMessage();
      }
    }

    static function read ($cmd_id) {
      $sql = "select * from citaten where id=:id";
      try { 
        $db = Connection::getInstance();
        $stmt = $db->dbh->prepare($sql);
        $stmt->bindParam(':id', $cmd_id);
        $stmt->execute();
        $obj = $stmt->fetch(\PDO::FETCH_OBJ);
        return $obj;
      } catch (\PDOException $e) {
        print $e->getMessage();
      } 
    }


    static function get_text_and_pagenumber($str) {
        /*
         * formaat: quote *ref*
         * Alles na de laatste * wordt genegeerd
         */

        $tmp = explode('*',$str);
        return [
            'quote' => $tmp[0],
            'page' => $tmp[1]
        ];
    }

    static function find_by_term($term) {
      $sql = "select citaten.*, titels.titel, concat(auteurs.voornaam, ' ', auteurs.achternaam) as auteur 
              from citaten join titels on citaten.titel_id=titels.id
              join auteurs on titels.auteur_id=auteurs.id
              where citaat like :term";

      $db = Connection::getInstance();
      $stmt = $db->dbh->prepare($sql);
      try {
        $stmt->bindValue(':term', "%$term%");
        $stmt->execute();
        $rv = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rv;
      } catch (\PDOException $e) {
        print_r ($stmt->errorInfo());
      }
    }

    static function find_by_title($art_id) {
      $sql = "select citaten.* from citaten where titel_id=:id";
      $db = Connection::getInstance();
      $stmt = $db->dbh->prepare($sql);
      try {
        $stmt->bindValue(':id', $art_id);
        $stmt->execute();
        $rv = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rv;
      } catch (\PDOException $e) {
        print_r ($stmt->errorInfo());
      }
    }
      


    static function update($json) {
      $sql = "update quotes set quote=:quote, page=:page where id=:id";


      try {
        $db = Connection::getInstance();
        $stmt = $db->dbh->prepare($sql);
        $stmt->bindParam(':quote', $json['quote']);
        $stmt->bindParam(':page', $json['page']);
        $stmt->bindParam(':id', $json['id']);
        $count = $stmt->execute();
        return $count;
      } catch (PDOException $e) {
        print $e->getMessage();
      }
    }

    function delete($cmd_id) {
    }

  }


