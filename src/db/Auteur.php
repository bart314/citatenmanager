<?php

namespace App\Database;

class Auteur {

    static function create($data) {
        $sql = "insert into auteurs(voornaam, achternaam) values (:voornaam, :achternaam)";

        try {
            $db = Connection::getInstance();
            $stmt = $db->dbh->prepare($sql);
            $stmt->bindParam(':voornaam', $data['voornaam']);
            $stmt->bindParam(':achternaam', $data['achternaam']);
            $stmt->execute();
            return $db->dbh->lastInsertId();
        } catch (PDOException $e) {
            print $e->getMessage();
        }
    }

    static function find($data) {
        $sql = "select id from auteurs where voornaam=:voornaam and achternaam=:achternaam";

        try {
            $db = Connection::getInstance();
            $stmt = $db->dbh->prepare($sql);
            $stmt->bindParam(':voornaam', $data['voornaam']);
            $stmt->bindParam(':achternaam', $data['achternaam']);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            print $e->getMessage();
        }
    }


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