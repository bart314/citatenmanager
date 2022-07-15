<?php
namespace App\Database;
require_once ('Connection.php');

class Collection {

    static function create(array $args) {
        $sql = "insert into collections(naam) values (:naam)";
        try { 
            $db = Connection::getInstance();
            $stmt = $db->dbh->prepare($sql);
            $stmt->bindParam(':naam', $args['naam']);
            $stmt->execute();
            return $db->dbh->lastInsertId();
          } catch (PDOException $e) {
            print $e->getMessage();
          }
    }

    static function get_all() {
        $sql = "select id, naam as titel, count(citaat_id) as aantal from collections  
                left outer join collections_citaten cc on cc.collection_id=collections.id 
                group by naam,id
                order by naam";
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


    static function get(int $id) { 
        $sql = "select naam,citaat_id,citaat,concat(titel, ', p.',pagina) as pagina from collections
                join collections_citaten on collections_citaten.collection_id=:id
                join citaten on collections_citaten.citaat_id = citaten.id
                join titels on citaten.titel_id=titels.id
                where collections.id=:id";

        try { 
            $db = Connection::getInstance();
            $stmt = $db->dbh->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $obj = $stmt->fetchAll(\PDO::FETCH_OBJ);
            return $obj;
          } catch (PDOException $e) {
            print $e->getMessage();
          }
    }

    static function add_quotes(int $id, array $quotes) {
        try {
            $db = Connection::getInstance();
            $sql = "select count(citaat_id) from collections_citaten
                    where collection_id=:id";
            $stmt = $db->dbh->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $num_rows = $stmt->fetchColumn();
            foreach ($quotes as $q_id) {
                $sql = "insert into collections_citaten values (:id, :c_id)";
                $stmt = $db->dbh->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':c_id', $q_id);
                if ($stmt->execute()) $num_rows++;
            }
            return $num_rows;
        } catch (\PDOException $e) {
            print ($e->getMessage());
        }
    }

}

#print (json_encode(Collection::get(3)));
#print (Collection::add_quotes(3, [40,41,42,43,45]));
