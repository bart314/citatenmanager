<?php

namespace App\Database;

class Title
{
    static function create($data)
    {
        $auteur_id = (int)$data['auteur_id'] ?? 0;
        if ($auteur_id == 0) {
            $tmp = [
                'voornaam' => $data['voornaam'],
                'achternaam' => $data['achternaam']
            ];
            $auteur_id = Auteur::find($tmp);
            if (!$auteur_id) $auteur_id = Auteur::create($tmp);
        }

        $sql = "insert into titels(titel, jaartal, auteur_id) values (:titel, :jaartal, :auteur_id)";
        try {
            $db = Connection::getInstance();
            $stmt = $db->dbh->prepare($sql);
            $stmt->bindParam(':titel', $data['titel']);
            $stmt->bindParam(':jaartal', $data['jaartal']);
            $stmt->bindParam('auteur_id', $auteur_id);
            $stmt->execute();
            $art_id = $db->dbh->lastInsertId();
            if ($data['quotes'] != 0) $tot = Title::insert_quotes($art_id, $data['quotes']);
            return [
                'artikel_id' => (int)$art_id,
                'aantal_quotes' => $tot ?? 0
            ];
        } catch (\PDOException $e) {
            return $e;
        }

    }

    static function getAll()
    {

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
            print_r($stmt->errorInfo());
        }
    }

    static function find($p_id)
    {
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


    static function update($cmd_id, $cmd)
    {

    }

    static function insert_quotes($art_id, $tmp_file)
    {
        $input = file_get_contents($tmp_file['tmp_name']);
        $input_ar = preg_split("#\n\r?#", $input);
        $tot = 0;
        foreach ($input_ar as $line) {
            $tot += Quote::create($line, $art_id);
        }

        return $tot;
    }

    function delete($cmd_id)
    {
    }

}

/**
 * ysql> desc titles;
 * +--------+------------------+------+-----+---------+----------------+
 * | Field  | Type             | Null | Key | Default | Extra          |
 * +--------+------------------+------+-----+---------+----------------+
 * | id     | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
 * | auteur | varchar(128)     | YES  |     | NULL    |                |
 * | titel  | varchar(128)     | YES  |     | NULL    |                |
 * +--------+------------------+------+-----+---------+----------------+
 * 3 rows in set (0,02 sec)
 *
 * mysql>
 **/
